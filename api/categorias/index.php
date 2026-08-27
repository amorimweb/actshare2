<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = getDB()->query('SELECT * FROM categorias ORDER BY nome');
    jsonOk($stmt->fetchAll());
}

if ($method === 'POST') {
    requireAdmin();
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    if (empty($body['nome'])) jsonError('Nome é obrigatório.', 400);

    $parentId = isset($body['parent_id']) && $body['parent_id'] !== '' ? (int)$body['parent_id'] : null;

    $db   = getDB();
    $stmt = $db->prepare('INSERT INTO categorias (nome, slug, parent_id) VALUES (?, ?, ?)');
    $stmt->execute([$body['nome'], $body['slug'] ?? null, $parentId]);

    $id   = $db->lastInsertId();
    $stmt = $db->prepare('SELECT * FROM categorias WHERE id = ?');
    $stmt->execute([$id]);
    jsonOk($stmt->fetch(), 201);
}

methodNotAllowed();
