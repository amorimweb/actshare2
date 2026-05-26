<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$gestor = requireMasterOrAdmin();
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

$nome     = trim($body['nome']     ?? '');
$email    = trim($body['email']    ?? '');
$password = $body['password'] ?? '';

if (!$nome || !$email || !$password) jsonError('nome, email e password são obrigatórios.', 400);

$db = getDB();

// Verifica e-mail duplicado
$stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if ($stmt->fetch()) jsonError('Este e-mail já está cadastrado.', 400);

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
$stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, ?)');
$stmt->execute([$nome, $email, $hash, 'aluno']);
$novoId = (int)$db->lastInsertId();

// Obtém ou cria organização do gestor
$stmt = $db->prepare('SELECT id FROM organizacoes WHERE gestor_id = ? AND ativo = 1 LIMIT 1');
$stmt->execute([$gestor['id']]);
$org = $stmt->fetch();

if (!$org) {
    $stmt = $db->prepare('INSERT INTO organizacoes (gestor_id, ativo) VALUES (?, 1)');
    $stmt->execute([$gestor['id']]);
    $orgId = (int)$db->lastInsertId();
} else {
    $orgId = $org['id'];
}

$stmt = $db->prepare('INSERT INTO membros_organizacao (organizacao_id, usuario_id) VALUES (?, ?)');
$stmt->execute([$orgId, $novoId]);

$stmt = $db->prepare('SELECT id, nome, email, role FROM usuarios WHERE id = ?');
$stmt->execute([$novoId]);
jsonOk($stmt->fetch(), 201);
