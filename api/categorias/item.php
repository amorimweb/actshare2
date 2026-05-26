<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

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
