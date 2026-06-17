<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

if ($method === 'DELETE') {
    $stmt = $db->prepare('SELECT id FROM cupons WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        jsonError('Cupom não encontrado.', 404);
    }
    
    $stmt = $db->prepare('DELETE FROM cupons WHERE id = ?');
    $stmt->execute([$id]);
    
    jsonOk(['success' => true]);
}

methodNotAllowed();
