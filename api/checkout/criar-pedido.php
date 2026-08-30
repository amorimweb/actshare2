<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/matriculas.php';
require_once __DIR__ . '/../../includes/configuracoes.php';
require_once __DIR__ . '/../../includes/asaas.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$user = requireAuth();
$body = json_decode(file_get_contents('php://input'), true) ?? [];

$itens = $body['itens'] ?? []; // [ { curso_id, vagas, com_prova } ]
$cupomCodigo = trim($body['cupom_codigo'] ?? '');
$formaPagamento = trim($body['forma_pagamento'] ?? 'pix'); // pix, boleto, cartao

if (empty($itens)) {
    jsonError('O carrinho não pode estar vazio.', 400);
}

$db = getDB();
$config = getConfiguracoes($db);

$stmtTipo = $db->prepare('SELECT tipo_pessoa FROM usuarios WHERE id = ? LIMIT 1');
$stmtTipo->execute([$user['id']]);
$compradorEhPF = $stmtTipo->fetchColumn() !== 'juridica'; // padrão PF quando não informado

$totalBruto = 0.00;
$totalDesconto = 0.00;
$totalVagasCursosPF = 0;  // soma de vagas de todos os cursos do pedido (regra PF: desconto pelo total, não por curso)
$totalBrutoCursosPF = 0.0; // soma dos subtotais desses mesmos cursos, base sobre a qual o % de desconto PF incide
$itensProcessados = [];

// 1. Processar cada item do carrinho (curso avulso ou combo)
foreach ($itens as $item) {
    $comboId   = (int)($item['combo_id']   ?? 0);
    $cursoId   = (int)($item['curso_id']   ?? 0);
    $vagas     = (int)($item['vagas']      ?? 1);
    $examesTipos = array_filter(array_map('trim', explode(',', strtoupper($item['exames_selecionados'] ?? ''))));

    if ($vagas <= 0) $vagas = 1;

    if ($comboId) {
        $stmt = $db->prepare('SELECT id, preco FROM combos WHERE id = ? AND ativo = 1 LIMIT 1');
        $stmt->execute([$comboId]);
        $combo = $stmt->fetch();
        if (!$combo) jsonError('Um ou mais combos selecionados não estão disponíveis.', 404);

        // Todos os cursos do combo precisam ter os pré-requisitos cumpridos
        $stmtCursos = $db->prepare('SELECT curso_id FROM combo_itens WHERE combo_id = ?');
        $stmtCursos->execute([$comboId]);
        foreach ($stmtCursos->fetchAll() as $ci) {
            $pendentes = cursosPrerequisitosPendentes($db, $user['id'], (int)$ci['curso_id']);
            if ($pendentes) {
                $nomes = implode(', ', array_column($pendentes, 'titulo'));
                jsonError("Para comprar este combo você precisa concluir antes: $nomes.", 400);
            }
        }

        $precoUnitario = (float)$combo['preco'];
        $subtotalItem = $precoUnitario * $vagas;
        $totalBruto += $subtotalItem;

        $itensProcessados[] = [
            'combo_id'       => $comboId,
            'curso_id'       => null,
            'vagas'          => $vagas,
            'preco_unitario' => $precoUnitario,
            'com_prova'      => 0,
            'exames'         => '',
        ];
        continue;
    }

    // Busca dados do curso
    $stmt = $db->prepare('SELECT id, preco FROM cursos WHERE id = ? AND ativo = 1 LIMIT 1');
    $stmt->execute([$cursoId]);
    $curso = $stmt->fetch();

    if (!$curso) {
        jsonError('Um ou mais cursos selecionados não estão disponíveis.', 404);
    }

    $pendentes = cursosPrerequisitosPendentes($db, $user['id'], $cursoId);
    if ($pendentes) {
        $nomes = implode(', ', array_column($pendentes, 'titulo'));
        jsonError("Para comprar este curso você precisa concluir antes: $nomes.", 400);
    }

    $precoUnitario = (float)$curso['preco'];
    $examesValidos = [];
    if ($examesTipos) {
        $placeholders = implode(',', array_fill(0, count($examesTipos), '?'));
        $stmtEx = $db->prepare("SELECT tipo, preco FROM exames_curso WHERE curso_id = ? AND ativo = 1 AND tipo IN ($placeholders)");
        $stmtEx->execute(array_merge([$cursoId], $examesTipos));
        foreach ($stmtEx->fetchAll() as $ex) {
            $examesValidos[] = $ex['tipo'];
            $precoUnitario += (float)$ex['preco'];
        }
    }
    $comProva = $examesValidos ? 1 : 0;
    $subtotalItem = $precoUnitario * $vagas;
    $totalBruto += $subtotalItem;

    if ($compradorEhPF) {
        // PF: desconto progressivo calculado sobre o TOTAL de unidades do
        // pedido (todos os cursos somados) — aplicado depois do loop.
        $totalVagasCursosPF += $vagas;
        $totalBrutoCursosPF += $subtotalItem;
    } else {
        // PJ: desconto progressivo por vagas do MESMO curso (regra já existente).
        $descontoProgPercentual = getDescontoProgressivoPercentual($config, $vagas);
        if ($descontoProgPercentual > 0) {
            $totalDesconto += $subtotalItem * ($descontoProgPercentual / 100);
        }
    }

    $itensProcessados[] = [
        'combo_id'       => null,
        'curso_id'       => $cursoId,
        'vagas'          => $vagas,
        'preco_unitario' => $precoUnitario,
        'com_prova'      => $comProva,
        'exames'         => implode(',', $examesValidos),
    ];
}

// 1b. PF: aplica o desconto progressivo uma única vez, sobre o total de
// unidades/cursos do pedido inteiro (em vez de por curso, como no PJ).
if ($compradorEhPF && $totalVagasCursosPF > 0) {
    $descontoProgPercentualPF = getDescontoProgressivoPercentual($config, $totalVagasCursosPF);
    if ($descontoProgPercentualPF > 0) {
        $totalDesconto += $totalBrutoCursosPF * ($descontoProgPercentualPF / 100);
    }
}

// 2. Desconto por Fidelidade (se ex-aluno com curso concluído)
$stmt = $db->prepare('SELECT COUNT(id) FROM matriculas WHERE aluno_id = ? AND concluido = 1');
$stmt->execute([$user['id']]);
$concluidos = (int)$stmt->fetchColumn();

if ($concluidos > 0) {
    // Desconto adicional sobre o saldo acumulado (% configurável pelo admin)
    $totalDesconto += ($totalBruto - $totalDesconto) * ((float)$config['desconto_fidelidade_percentual'] / 100);
}

// 3. Processar cupom se fornecido
$cupomId = null;
$cupomIndicacaoId = null;

if ($cupomCodigo) {
    // Cupom padrão
    $stmt = $db->prepare('SELECT id, tipo, valor, validade, limite_uso, usos FROM cupons WHERE codigo = ? LIMIT 1');
    $stmt->execute([$cupomCodigo]);
    $cupom = $stmt->fetch();
    
    if ($cupom && strtotime($cupom['validade']) >= time() && ($cupom['limite_uso'] === null || $cupom['usos'] < $cupom['limite_uso'])) {
        $cupomId = (int)$cupom['id'];
        $saldoBruto = $totalBruto - $totalDesconto;
        
        if ($cupom['tipo'] === 'porcentagem') {
            $totalDesconto += $saldoBruto * ((float)$cupom['valor'] / 100);
        } else {
            $totalDesconto += (float)$cupom['valor'];
        }
    } else {
        // Cupom de indicação B2C
        $stmt = $db->prepare('SELECT id, percentual, validade, utilizado FROM cupons_indicacao WHERE codigo = ? LIMIT 1');
        $stmt->execute([$cupomCodigo]);
        $ref = $stmt->fetch();
        
        if ($ref && strtotime($ref['validade']) >= time() && $ref['utilizado'] == 0) {
            $cupomIndicacaoId = (int)$ref['id'];
            $saldoBruto = $totalBruto - $totalDesconto;
            $totalDesconto += $saldoBruto * ((float)$ref['percentual'] / 100);
        }
    }
}

$totalLiquido = max(0.00, $totalBruto - $totalDesconto);

// 4. Salvar pedido no banco
$db->beginTransaction();
try {
    $stmt = $db->prepare('
        INSERT INTO pedidos (usuario_id, total_bruto, desconto, total_liquido, cupom_id, situacao, asaas_id, forma_pagamento)
        VALUES (?, ?, ?, ?, ?, "pendente", "simulado", ?)
    ');
    $stmt->execute([$user['id'], $totalBruto, $totalDesconto, $totalLiquido, $cupomId, $formaPagamento]);
    $pedidoId = (int)$db->lastInsertId();
    
    $stmtItem = $db->prepare('
        INSERT INTO itens_pedido (pedido_id, curso_id, combo_id, vagas, preco_unitario, com_prova, exames_selecionados)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ');
    foreach ($itensProcessados as $it) {
        $stmtItem->execute([$pedidoId, $it['curso_id'], $it['combo_id'], $it['vagas'], $it['preco_unitario'], $it['com_prova'], $it['exames'] ?: null]);
    }
    
    // Se usou cupom comum, incrementa usos
    if ($cupomId) {
        $db->exec("UPDATE cupons SET usos = usos + 1 WHERE id = $cupomId");
    }
    // Se usou cupom de indicação, marca como usado
    if ($cupomIndicacaoId) {
        $db->exec("UPDATE cupons_indicacao SET utilizado = 1 WHERE id = $cupomIndicacaoId");
    }
    
    $db->commit();

    $responsePayload = [
        'pedido_id'      => $pedidoId,
        'total_liquido'  => $totalLiquido,
        'forma_pagamento'=> $formaPagamento,
    ];

    // Com ASAAS_API_KEY configurada no .env, gera uma cobrança real; a
    // confirmação de pagamento chega depois via webhook (asaas-webhook.php),
    // que libera as matrículas. Sem chave, mantém a simulação de sempre
    // (o botão "simular pagamento" libera na hora).
    if (asaasConfigured()) {
        try {
            $cobranca = asaasCriarCobranca($db, $user, $pedidoId, $totalLiquido, $formaPagamento);
            $responsePayload = array_merge($responsePayload, $cobranca);
            $responsePayload['modo'] = 'real';
        } catch (Exception $e) {
            // Se o ASAAS falhar, não trava a compra — cai pro fluxo simulado
            // e loga o motivo pra investigação manual do admin.
            error_log('Falha ao criar cobrança ASAAS para pedido ' . $pedidoId . ': ' . $e->getMessage());
            $responsePayload['modo'] = 'simulado_fallback';
        }
    } else {
        $responsePayload['modo'] = 'simulado';
    }

    if (!isset($responsePayload['pix_code']) && $formaPagamento === 'pix') {
        $responsePayload['pix_code'] = '00020101021226830014br.gov.bcb.pix2561pix.example.com/qr/v2/cob-simulada-actshare-' . $pedidoId;
        $responsePayload['pix_qr'] = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($responsePayload['pix_code']);
    } elseif (!isset($responsePayload['boleto_barcode']) && $formaPagamento === 'boleto') {
        $responsePayload['boleto_barcode'] = '34191.79001 01043.513184 91020.150008 7 90000000000000';
        $responsePayload['boleto_pdf'] = '#';
    } elseif ($formaPagamento === 'cartao' && !isset($responsePayload['invoice_url'])) {
        // Cartão de Crédito sem ASAAS -> Simula sucesso imediato
        $responsePayload['cartao_sucesso'] = true;
    }

    jsonOk($responsePayload);

} catch (Exception $e) {
    $db->rollBack();
    jsonError($e->getMessage(), 500);
}
