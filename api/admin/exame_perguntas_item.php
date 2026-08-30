<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$id = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $stmt = $db->prepare('SELECT id FROM exame_perguntas WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Pergunta não encontrada.', 404);

    $db->prepare('DELETE FROM exame_perguntas WHERE id = ?')->execute([$id]);
    jsonOk(['success' => true]);
}

methodNotAllowed();
