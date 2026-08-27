<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    $where = [];
    if (($_GET['ativo'] ?? '') === 'true')   $where[] = 'ativo = 1';
    if (($_GET['publico'] ?? '') === 'true') $where[] = 'publico = 1';
    $sql = 'SELECT * FROM combos' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC';
    $combos = $db->query($sql)->fetchAll();

    $stmtItens = $db->prepare('
        SELECT ci.curso_id, c.titulo, c.preco, c.thumb_url
        FROM combo_itens ci JOIN cursos c ON ci.curso_id = c.id
        WHERE ci.combo_id = ?
    ');
    foreach ($combos as &$combo) {
        $stmtItens->execute([$combo['id']]);
        $combo['cursos'] = $stmtItens->fetchAll();
    }
    jsonOk($combos);
}

if ($method === 'POST') {
    requireAdmin();
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    if (empty($body['titulo'])) jsonError('Título é obrigatório.', 400);
    $cursoIds = array_values(array_unique(array_map('intval', $body['curso_ids'] ?? [])));
    if (count($cursoIds) < 2) jsonError('Um combo precisa ter pelo menos 2 cursos.', 400);

    $db->beginTransaction();
    try {
        $stmt = $db->prepare('
            INSERT INTO combos (titulo, descricao, thumb_url, preco, prazo_validade_dias, ativo, publico, disponivel_loja)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $body['titulo'],
            $body['descricao'] ?? null,
            $body['thumb_url'] ?? null,
            $body['preco'] ?? 0,
            $body['prazo_validade_dias'] ?? null,
            $body['ativo'] ?? 1,
            $body['publico'] ?? 0,
            $body['disponivel_loja'] ?? 1,
        ]);
        $comboId = (int)$db->lastInsertId();

        $stmtItem = $db->prepare('INSERT INTO combo_itens (combo_id, curso_id) VALUES (?, ?)');
        foreach ($cursoIds as $cid) $stmtItem->execute([$comboId, $cid]);

        $db->commit();
        jsonOk(['success' => true, 'id' => $comboId], 201);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 400);
    }
}

methodNotAllowed();
