<?php
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$body     = json_decode(file_get_contents('php://input'), true) ?? [];
$email    = trim($body['email'] ?? '');
$password = $body['password'] ?? '';
$nome     = trim($body['nome'] ?? '');

if (!$email || !$password) jsonError('E-mail e senha são obrigatórios.', 400);

$db   = getDB();
$stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if ($stmt->fetch()) jsonError('Este e-mail já está cadastrado. Faça login ou recupere sua senha.', 400);

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
$nome = $nome ?: explode('@', $email)[0];

$stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)');
$stmt->execute([$nome, $email, $hash, 'aluno']);

jsonOk(['success' => true], 201);
