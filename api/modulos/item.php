<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

if ($method === 'PUT') {
    requireAdmin();
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $set    = [];
    $params = [];
    foreach (['titulo', 'ordem'] as $f) {
        if (array_key_exists($f, $body)) { $set[] = "$f = ?"; $params[] = $body[$f]; }
    }
    if (!$set) jsonError('Nenhum campo para atualizar.', 400);
    $params[] = $id;
    getDB()->prepare('UPDATE modulos SET ' . implode(', ', $set) . ' WHERE id = ?')->execute($params);

    $stmt = getDB()->prepare('SELECT * FROM modulos WHERE id = ?');
    $stmt->execute([$id]);
    jsonOk($stmt->fetch());
}

if ($method === 'DELETE') {
    requireAdmin();
    $db   = getDB();
    $stmt = $db->prepare('SELECT id FROM modulos WHERE id = ?');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Módulo não encontrado.', 404);
    $db->prepare('DELETE FROM modulos WHERE id = ?')->execute([$id]);
    jsonOk(['success' => true]);
}

methodNotAllowed();
