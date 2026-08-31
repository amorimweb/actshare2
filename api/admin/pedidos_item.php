<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/pedidos.php';

requireAdmin();
$id = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare('
        SELECT p.*, u.nome AS usuario_nome, u.email AS usuario_email, u.razao_social, u.tipo_pessoa,
               c.codigo AS cupom_codigo
        FROM pedidos p
        JOIN usuarios u ON u.id = p.usuario_id
        LEFT JOIN cupons c ON c.id = p.cupom_id
        WHERE p.id = ?
    ');
    $stmt->execute([$id]);
    $pedido = $stmt->fetch();
    if (!$pedido) jsonError('Pedido não encontrado.', 404);

    $pedido['nome_cliente'] = ($pedido['tipo_pessoa'] === 'juridica' && $pedido['razao_social']) ? $pedido['razao_social'] : $pedido['usuario_nome'];

    $stmt = $db->prepare('
        SELECT ip.*, c.titulo AS curso_titulo, c.prazo_acesso_dias, cb.titulo AS combo_titulo
        FROM itens_pedido ip
        LEFT JOIN cursos c ON c.id = ip.curso_id
        LEFT JOIN combos cb ON cb.id = ip.combo_id
        WHERE ip.pedido_id = ?
    ');
    $stmt->execute([$id]);
    $itens = $stmt->fetchAll();

    // Desconto/percentual proporcional de cada item em relação ao bruto do pedido
    // (o desconto é calculado no nível do pedido, não por item, no schema atual).
    $percentualDesconto = $pedido['total_bruto'] > 0 ? ($pedido['desconto'] / $pedido['total_bruto']) : 0;
    foreach ($itens as &$item) {
        $item['produto'] = $item['curso_titulo'] ?? $item['combo_titulo'];
        $totalItem = (float)$item['preco_unitario'] * (int)$item['vagas'];
        $descontoItem = round($totalItem * $percentualDesconto, 2);
        $item['preco_total'] = $totalItem;
        $item['percentual_desconto'] = round($percentualDesconto * 100, 1);
        $item['liquido_pago'] = round($totalItem - $descontoItem, 2);
    }

    $pedido['itens'] = $itens;
    jsonOk($pedido);
}

// PUT — edição pelo Admin. Regra do cliente: só o STS "Pago" (confirmação
// automática via Asaas) trava a edição do pedido; qualquer outro status
// (Aguardando Pgto, Baixa Manual, Cancelado) permanece editável, inclusive
// depois de já estar em Baixa Manual.
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $stmt = $db->prepare('SELECT * FROM pedidos WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $pedido = $stmt->fetch();
    if (!$pedido) jsonError('Pedido não encontrado.', 404);

    if ($pedido['situacao'] === 'pago') {
        jsonError('Este pedido foi pago via Asaas e não pode mais ser editado.', 403);
    }

    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    $situacoesPermitidas = ['pendente', 'baixa_manual', 'cancelado'];
    $novaSituacao = $body['situacao'] ?? $pedido['situacao'];
    if (!in_array($novaSituacao, $situacoesPermitidas, true)) {
        jsonError('Status de pagamento inválido.', 400);
    }

    $observacaoAdmin = array_key_exists('observacao_admin', $body) ? trim((string)$body['observacao_admin']) : $pedido['observacao_admin'];
    $itensBody = is_array($body['itens'] ?? null) ? $body['itens'] : [];

    $db->beginTransaction();
    try {
        $totalBruto = 0;
        $prazosParaPropagar = []; // aplicados só DEPOIS de liberar matrículas — ver abaixo
        foreach ($itensBody as $it) {
            $itemId = (int)($it['id'] ?? 0);
            if (!$itemId) continue;

            $cursoId  = !empty($it['curso_id'])  ? (int)$it['curso_id']  : null;
            $comboId  = !empty($it['combo_id'])  ? (int)$it['combo_id']  : null;
            $vagas    = max(1, (int)($it['vagas'] ?? 1));
            $preco    = max(0, (float)($it['preco_unitario'] ?? 0));
            $exames   = trim((string)($it['exames_selecionados'] ?? '')) ?: null;

            $stmtUp = $db->prepare('
                UPDATE itens_pedido
                SET curso_id = ?, combo_id = ?, vagas = ?, preco_unitario = ?, exames_selecionados = ?
                WHERE id = ? AND pedido_id = ?
            ');
            $stmtUp->execute([$cursoId, $comboId, $vagas, $preco, $exames, $itemId, $id]);

            $totalBruto += $preco * $vagas;

            // Prazo de acesso: não é campo do item_pedido, é aplicado direto
            // nas matrículas (item 6.b.ii). Não pode rodar aqui ainda: se o
            // pedido está transicionando pra Baixa Manual agora mesmo, a
            // matrícula desse curso pode nem existir até liberarMatriculas-
            // DoPedido() rodar (mais abaixo) — e ela recalcularia o prazo
            // padrão do curso por cima de qualquer override feito antes.
            if (!empty($it['prazo_acesso_dias']) && $cursoId) {
                $dias = (int)$it['prazo_acesso_dias'];
                if ($dias > 0) {
                    $prazosParaPropagar[] = ['curso_id' => $cursoId, 'data_fim' => date('Y-m-d H:i:s', time() + ($dias * 24 * 3600))];
                }
            }
        }

        if ($itensBody) {
            $totalLiquido = max(0, $totalBruto - (float)$pedido['desconto']);
            $db->prepare('UPDATE pedidos SET total_bruto = ?, total_liquido = ? WHERE id = ?')
               ->execute([$totalBruto, $totalLiquido, $id]);
        }

        $db->prepare('UPDATE pedidos SET observacao_admin = ? WHERE id = ?')
           ->execute([$observacaoAdmin, $id]);

        // Baixa Manual = Admin confirmando o pagamento fora do fluxo Asaas —
        // libera as matrículas igual a um pagamento automático (mas sem
        // travar o pedido). liberarMatriculasDoPedido() é quem grava o
        // situacao='baixa_manual' (lendo os itens já atualizados acima pra
        // criar as matrículas certas) — só dispara ao TRANSICIONAR pra esse
        // status; se já estava em baixa_manual, só atualiza situacao direto.
        $transicionandoParaBaixaManual = $novaSituacao === 'baixa_manual' && $pedido['situacao'] !== 'baixa_manual';
        if (!$transicionandoParaBaixaManual) {
            $db->prepare('UPDATE pedidos SET situacao = ? WHERE id = ?')->execute([$novaSituacao, $id]);
        }

        $db->commit();

        if ($transicionandoParaBaixaManual) {
            liberarMatriculasDoPedido($db, $id, 'baixa_manual');
        }

        // Só agora, com a matrícula garantidamente já criada/liberada, é que
        // um prazo de acesso customizado pode sobrescrever o padrão do curso.
        foreach ($prazosParaPropagar as $p) {
            propagarPrazoAcessoDoItem($db, (int)$pedido['usuario_id'], $p['curso_id'], $p['data_fim']);
        }

        jsonOk(['success' => true]);
    } catch (Exception $e) {
        if ($db->inTransaction()) $db->rollBack();
        jsonError($e->getMessage(), 400);
    }
}

methodNotAllowed();
