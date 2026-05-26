<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    requireAdmin();
    $stmt = getDB()->query('SELECT id, nome, email, role, ativo, created_at FROM usuarios ORDER BY created_at DESC');
    jsonOk($stmt->fetchAll());
}

if ($method === 'PATCH') {
    requireAdmin();
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $userId = (int)($body['userId'] ?? 0);
    $role   = $body['role'] ?? '';
    if (!$userId || !$role) jsonError('userId e role são obrigatórios.', 400);

    $allowed = ['admin', 'gestor', 'aluno', 'instrutor'];
    if (!in_array($role, $allowed)) jsonError('Role inválida.', 400);

    $db   = getDB();
    $stmt = $db->prepare('UPDATE usuarios SET role = ? WHERE id = ?');
    $stmt->execute([$role, $userId]);

    $stmt = $db->prepare('SELECT id, nome, email, role, ativo FROM usuarios WHERE id = ?');
    $stmt->execute([$userId]);
    jsonOk($stmt->fetch());
}

methodNotAllowed();
