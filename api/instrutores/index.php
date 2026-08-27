<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = getDB()->query('SELECT * FROM instrutores ORDER BY nome');
    jsonOk($stmt->fetchAll());
}

if ($method === 'POST') {
    requireAdmin();
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    if (empty($body['nome'])) jsonError('Nome é obrigatório.', 400);

    $db   = getDB();
    $stmt = $db->prepare('INSERT INTO instrutores (nome, qualificacao1, qualificacao2, avatar_url, assinatura_url, descricao) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$body['nome'], $body['qualificacao1'] ?? null, $body['qualificacao2'] ?? null, $body['avatar_url'] ?? null, $body['assinatura_url'] ?? null, $body['descricao'] ?? null]);

    $id   = $db->lastInsertId();
    $stmt = $db->prepare('SELECT * FROM instrutores WHERE id = ?');
    $stmt->execute([$id]);
    jsonOk($stmt->fetch(), 201);
}

methodNotAllowed();
