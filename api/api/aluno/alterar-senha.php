<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$user = requireAuth();
$body = json_decode(file_get_contents('php://input'), true) ?? [];

$senhaAtual = $body['senha_atual'] ?? '';
$novaSenha  = $body['nova_senha'] ?? '';
$confirmar  = $body['confirmar_senha'] ?? '';

if (!$senhaAtual || !$novaSenha || !$confirmar) {
    jsonError('Todos os campos são obrigatórios.', 400);
}

if (strlen($novaSenha) < 6) {
    jsonError('A nova senha deve ter pelo menos 6 caracteres.', 400);
}

if ($novaSenha !== $confirmar) {
    jsonError('A nova senha e a confirmação não coincidem.', 400);
}

$db = getDB();
$stmt = $db->prepare('SELECT senha_hash FROM usuarios WHERE id = ? LIMIT 1');
$stmt->execute([$user['id']]);
$dbUser = $stmt->fetch();

if (!$dbUser || !password_verify($senhaAtual, $dbUser['senha_hash'])) {
    jsonError('A senha atual está incorreta.', 400);
}

$novaHash = password_hash($novaSenha, PASSWORD_DEFAULT);
$stmt = $db->prepare('UPDATE usuarios SET senha_hash = ? WHERE id = ?');
$stmt->execute([$novaHash, $user['id']]);

jsonOk(['success' => true, 'message' => 'Senha alterada com sucesso!']);
