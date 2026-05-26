<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/jwt.php';
require_once __DIR__ . '/../../includes/auth.php';

$user = requireAuth();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare('SELECT id, nome, email, role, created_at FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->execute([$user['id']]);
    $dbUser = $stmt->fetch();
    
    if (!$dbUser) {
        jsonError('Usuário não encontrado.', 404);
    }
    
    jsonOk($dbUser);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $nome  = trim($body['nome'] ?? '');
    $email = trim($body['email'] ?? '');

    if (!$nome || !$email) {
        jsonError('Nome e E-mail são campos obrigatórios.', 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonError('E-mail inválido.', 400);
    }

    // Verifica se e-mail já existe em outro usuário
    $stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ? LIMIT 1');
    $stmt->execute([$email, $user['id']]);
    if ($stmt->fetch()) {
        jsonError('Este e-mail já está sendo utilizado por outro usuário.', 400);
    }

    // Atualiza
    $stmt = $db->prepare('UPDATE usuarios SET nome = ?, email = ? WHERE id = ?');
    $stmt->execute([$nome, $email, $user['id']]);

    // Busca dados atualizados para atualizar o token/cookie
    $stmt = $db->prepare('SELECT id, nome, email, role FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->execute([$user['id']]);
    $updatedUser = $stmt->fetch();

    $newToken = signToken($updatedUser);
    setAuthCookie($newToken);

    jsonOk([
        'success' => true,
        'user' => [
            'id'    => $updatedUser['id'],
            'email' => $updatedUser['email'],
            'role'  => $updatedUser['role'],
            'nome'  => $updatedUser['nome'],
        ]
    ]);
} else {
    methodNotAllowed();
}
