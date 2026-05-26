<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/jwt.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$email    = trim($body['email'] ?? '');
$password = $body['password'] ?? '';

if (!$email || !$password) jsonError('E-mail e senha são obrigatórios.', 400);

$db   = getDB();
$stmt = $db->prepare('SELECT id, email, role, nome, senha_hash FROM usuarios WHERE email = ? AND ativo = 1 LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['senha_hash'])) {
    jsonError('E-mail ou senha incorretos.', 401);
}

$token = signToken($user);
setAuthCookie($token);

jsonOk(['user' => [
    'id'    => $user['id'],
    'email' => $user['email'],
    'role'  => $user['role'],
    'nome'  => $user['nome'],
]]);
