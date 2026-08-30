<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$matriculaId = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$matriculaId) jsonError('ID de matrícula inválido.', 400);
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') methodNotAllowed();

$db = getDB();
$body = json_decode(file_get_contents('php://input'), true) ?? [];

// Único campo editável aqui é a data de término (prazo de acesso) da matrícula
// — pedido explícito do cliente: só o Admin pode alterar essa data.
if (!array_key_exists('data_fim_acesso', $body)) {
    jsonError('Informe data_fim_acesso.', 400);
}

$dataFim = trim($body['data_fim_acesso']) === '' ? null : $body['data_fim_acesso'];

$stmt = $db->prepare('SELECT id FROM matriculas WHERE id = ? LIMIT 1');
$stmt->execute([$matriculaId]);
if (!$stmt->fetch()) {
    jsonError('Matrícula não encontrada.', 404);
}

$stmt = $db->prepare('UPDATE matriculas SET data_fim_acesso = ? WHERE id = ?');
$stmt->execute([$dataFim, $matriculaId]);

jsonOk(['success' => true]);
