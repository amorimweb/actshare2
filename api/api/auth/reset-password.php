<?php
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$body  = json_decode(file_get_contents('php://input'), true) ?? [];
$email = trim($body['email'] ?? '');

if (!$email) jsonError('E-mail é obrigatório.', 400);

$db   = getDB();
$stmt = $db->prepare('SELECT id FROM usuarios WHERE email = ? AND ativo = 1 LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

$response = ['success' => true];

// Retorna sucesso mesmo se e-mail não existe (segurança)
if ($user) {
    $token     = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + 3600);

    $stmt = $db->prepare(
        'INSERT INTO sessoes (usuario_id, token, expires_at) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)'
    );
    $stmt->execute([$user['id'], $token, $expiresAt]);

    $link = SITE_URL . '/nova-senha?token=' . $token;
    error_log("[reset-password] Link: $link");

    if (str_contains(SITE_URL, 'localhost')) {
        $response['debug_link'] = $link;
    }
}

jsonOk($response);
