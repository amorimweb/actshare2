<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

if ($method === 'PUT') {
    requireAdmin();
    $db   = getDB();
    $stmt = $db->prepare('SELECT id FROM categorias WHERE id = ?');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Categoria não encontrada.', 404);

    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $nome = trim($body['nome'] ?? '');
    $slug = trim($body['slug'] ?? '');
    $parentId = isset($body['parent_id']) && $body['parent_id'] !== '' ? (int)$body['parent_id'] : null;
    if (empty($nome)) jsonError('Nome da categoria é obrigatório.', 400);
    if (empty($slug)) $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $nome), '-'));
    if ($parentId === $id) jsonError('Uma categoria não pode ser subcategoria dela mesma.', 400);

    $stmtDup = $db->prepare('SELECT id FROM categorias WHERE slug = ? AND id != ?');
    $stmtDup->execute([$slug, $id]);
    if ($stmtDup->fetch()) jsonError('Já existe outra categoria com este slug.', 400);

    $db->prepare('UPDATE categorias SET nome = ?, slug = ?, parent_id = ? WHERE id = ?')
       ->execute([$nome, $slug, $parentId, $id]);
    jsonOk(['success' => true]);
}

if ($method === 'DELETE') {
    requireAdmin();
    $db   = getDB();
    $stmt = $db->prepare('SELECT id FROM categorias WHERE id = ?');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Categoria não encontrada.', 404);

    $db->prepare('DELETE FROM categorias WHERE id = ?')->execute([$id]);
    jsonOk(['success' => true]);
}

methodNotAllowed();
