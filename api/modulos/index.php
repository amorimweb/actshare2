<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

requireAdmin();
$body = json_decode(file_get_contents('php://input'), true) ?? [];
if (empty($body['curso_id']) || empty($body['titulo'])) jsonError('curso_id e titulo são obrigatórios.', 400);

$db   = getDB();
$stmt = $db->prepare('INSERT INTO modulos (curso_id, titulo, ordem) VALUES (?, ?, ?)');
$stmt->execute([$body['curso_id'], $body['titulo'], $body['ordem'] ?? 0]);

$id   = $db->lastInsertId();
$stmt = $db->prepare('SELECT * FROM modulos WHERE id = ?');
$stmt->execute([$id]);
jsonOk($stmt->fetch(), 201);
