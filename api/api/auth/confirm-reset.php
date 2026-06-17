<?php
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$body     = json_decode(file_get_contents('php://input'), true) ?? [];
$token    = trim($body['token'] ?? '');
$password = $body['password'] ?? '';

if (!$token || !$password) jsonError('Token e senha são obrigatórios.', 400);

$db   = getDB();
$stmt = $db->prepare(
    'SELECT s.usuario_id FROM sessoes s WHERE s.token = ? AND s.expires_at > NOW() LIMIT 1'
);
$stmt->execute([$token]);
$session = $stmt->fetch();

if (!$session) jsonError('Token inválido ou expirado.', 400);

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
$db->prepare('UPDATE usuarios SET senha_hash = ? WHERE id = ?')
   ->execute([$hash, $session['usuario_id']]);
$db->prepare('DELETE FROM sessoes WHERE token = ?')
   ->execute([$token]);

jsonOk(['success' => true]);
