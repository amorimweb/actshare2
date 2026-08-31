<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->query('
        SELECT p.id, p.total_bruto, p.desconto, p.total_liquido, p.situacao, p.forma_pagamento,
               p.asaas_id, p.created_at,
               u.id AS usuario_id, u.nome AS usuario_nome, u.razao_social, u.tipo_pessoa,
               c.codigo AS cupom_codigo
        FROM pedidos p
        JOIN usuarios u ON u.id = p.usuario_id
        LEFT JOIN cupons c ON c.id = p.cupom_id
        ORDER BY p.created_at DESC, p.id DESC
    ');
    $pedidos = $stmt->fetchAll();

    foreach ($pedidos as &$p) {
        $p['nome_cliente'] = ($p['tipo_pessoa'] === 'juridica' && $p['razao_social']) ? $p['razao_social'] : $p['usuario_nome'];
        unset($p['usuario_nome'], $p['razao_social'], $p['tipo_pessoa']);
    }

    jsonOk($pedidos);
}

// POST — pedido manual pelo Admin (item 7 do escopo): atribui produtos já
// cadastrados a um cliente já cadastrado. Nasce como "Aguardando Pgto" —
// o Admin confirma o pagamento depois pela edição do pedido (item 6).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    $usuarioId = (int)($body['usuario_id'] ?? 0);
    if (!$usuarioId) jsonError('Selecione um cliente.', 400);

    $stmt = $db->prepare('SELECT id FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->execute([$usuarioId]);
    if (!$stmt->fetch()) jsonError('Cliente não encontrado.', 404);

    $itensBody = is_array($body['itens'] ?? null) ? $body['itens'] : [];
    if (!$itensBody) jsonError('Adicione pelo menos um produto ao pedido.', 400);

    $db->beginTransaction();
    try {
        $totalBruto = 0;
        $itensValidos = [];
        foreach ($itensBody as $it) {
            $cursoId = !empty($it['curso_id']) ? (int)$it['curso_id'] : null;
            $comboId = !empty($it['combo_id']) ? (int)$it['combo_id'] : null;
            if (!$cursoId && !$comboId) continue;

            $vagas  = max(1, (int)($it['vagas'] ?? 1));
            $preco  = max(0, (float)($it['preco_unitario'] ?? 0));
            $exames = $cursoId ? (trim((string)($it['exames_selecionados'] ?? '')) ?: null) : null;

            $totalBruto += $preco * $vagas;
            $itensValidos[] = [$cursoId, $comboId, $vagas, $preco, $exames];
        }
        if (!$itensValidos) throw new Exception('Nenhum produto válido informado.');

        $stmtPed = $db->prepare('
            INSERT INTO pedidos (usuario_id, total_bruto, desconto, total_liquido, situacao)
            VALUES (?, ?, 0, ?, "pendente")
        ');
        $stmtPed->execute([$usuarioId, $totalBruto, $totalBruto]);
        $pedidoId = (int)$db->lastInsertId();

        $stmtItem = $db->prepare('
            INSERT INTO itens_pedido (pedido_id, curso_id, combo_id, vagas, preco_unitario, exames_selecionados)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        foreach ($itensValidos as [$cursoId, $comboId, $vagas, $preco, $exames]) {
            $stmtItem->execute([$pedidoId, $cursoId, $comboId, $vagas, $preco, $exames]);
        }

        $db->commit();
        jsonOk(['success' => true, 'id' => $pedidoId], 201);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 400);
    }
}

methodNotAllowed();
