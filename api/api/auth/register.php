<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/jwt.php';
require_once __DIR__ . '/../../includes/auth.php';

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

$optionalFields = [
    'documento',
    'telefone',
    'tipo_pessoa',
    'data_nascimento',
    'razao_social',
    'inscricao_estadual',
    'cep',
    'endereco',
    'numero',
    'complemento',
    'bairro',
    'cidade',
    'estado',
    'pais',
];

$values = [];
foreach ($optionalFields as $field) {
    $value = trim((string)($body[$field] ?? ''));
    $values[$field] = $value !== '' ? $value : null;
}

$columns = array_merge(['nome', 'email', 'senha_hash', 'role'], $optionalFields);
$placeholders = implode(', ', array_fill(0, count($columns), '?'));
$stmt = $db->prepare('INSERT INTO usuarios (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')');
$stmt->execute(array_merge([$nome, $email, $hash, 'aluno'], array_values($values)));

$id = (int)$db->lastInsertId();
$stmt = $db->prepare('SELECT id, email, role, nome, documento, telefone, tipo_pessoa, data_nascimento, razao_social, inscricao_estadual, cep, endereco, numero, complemento, bairro, cidade, estado, pais FROM usuarios WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

$token = signToken($user);
setAuthCookie($token);

jsonOk(['success' => true, 'user' => $user], 201);
