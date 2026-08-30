<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$id = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $texto = trim($body['texto'] ?? '');
    if (!$texto) jsonError('O texto da pergunta é obrigatório.', 400);

    $stmt = $db->prepare('SELECT id FROM pesquisa_perguntas WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Pergunta não encontrada.', 404);

    $stmt = $db->prepare('UPDATE pesquisa_perguntas SET texto = ? WHERE id = ?');
    $stmt->execute([$texto, $id]);
    jsonOk(['success' => true]);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $stmt = $db->prepare('SELECT id FROM pesquisa_perguntas WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Pergunta não encontrada.', 404);

    // Apaga também as respostas já dadas para essa pergunta (FK on delete cascade já cobre isso)
    $stmt = $db->prepare('DELETE FROM pesquisa_perguntas WHERE id = ?');
    $stmt->execute([$id]);
    jsonOk(['success' => true]);
}

methodNotAllowed();
