<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$chave = trim($GLOBALS['_ROUTE']['chave'] ?? '');
if (!$chave) jsonError('Chave inválida.', 400);
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') methodNotAllowed();

$db = getDB();
$stmt = $db->prepare('SELECT chave FROM email_templates WHERE chave = ? LIMIT 1');
$stmt->execute([$chave]);
if (!$stmt->fetch()) jsonError('Template não encontrado.', 404);

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$assunto = trim($body['assunto'] ?? '');
$corpo = trim($body['corpo'] ?? '');
$ativo = !empty($body['ativo']) ? 1 : 0;

if (!$assunto || !$corpo) jsonError('Assunto e corpo são obrigatórios.', 400);

$stmt = $db->prepare('UPDATE email_templates SET assunto = ?, corpo = ?, ativo = ? WHERE chave = ?');
$stmt->execute([$assunto, $corpo, $ativo, $chave]);

jsonOk(['success' => true]);
