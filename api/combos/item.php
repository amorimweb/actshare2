<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

if ($method === 'GET') {
    $stmt = $db->prepare('SELECT * FROM combos WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $combo = $stmt->fetch();
    if (!$combo) jsonError('Combo não encontrado.', 404);

    $stmtItens = $db->prepare('
        SELECT ci.curso_id, c.titulo, c.preco, c.thumb_url, c.carga_horaria_horas
        FROM combo_itens ci JOIN cursos c ON ci.curso_id = c.id
        WHERE ci.combo_id = ?
    ');
    $stmtItens->execute([$id]);
    $combo['cursos'] = $stmtItens->fetchAll();

    jsonOk($combo);
}

if ($method === 'PUT') {
    requireAdmin();
    $stmt = $db->prepare('SELECT id FROM combos WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Combo não encontrado.', 404);

    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    if (empty($body['titulo'])) jsonError('Título é obrigatório.', 400);

    $db->beginTransaction();
    try {
        $stmt = $db->prepare('
            UPDATE combos SET titulo=?, descricao=?, thumb_url=?, preco=?, prazo_validade_dias=?, ativo=?, publico=?, disponivel_loja=?
            WHERE id=?
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
            $id,
        ]);

        if (array_key_exists('curso_ids', $body)) {
            $cursoIds = array_values(array_unique(array_map('intval', $body['curso_ids'] ?? [])));
            if (count($cursoIds) < 2) throw new Exception('Um combo precisa ter pelo menos 2 cursos.');
            $db->prepare('DELETE FROM combo_itens WHERE combo_id = ?')->execute([$id]);
            $stmtItem = $db->prepare('INSERT INTO combo_itens (combo_id, curso_id) VALUES (?, ?)');
            foreach ($cursoIds as $cid) $stmtItem->execute([$id, $cid]);
        }

        $db->commit();
        jsonOk(['success' => true]);
    } catch (Exception $e) {
        $db->rollBack();
        jsonError($e->getMessage(), 400);
    }
}

if ($method === 'DELETE') {
    requireAdmin();
    $stmt = $db->prepare('SELECT id FROM combos WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Combo não encontrado.', 404);

    $db->prepare('DELETE FROM combos WHERE id = ?')->execute([$id]);
    jsonOk(['success' => true]);
}

methodNotAllowed();
