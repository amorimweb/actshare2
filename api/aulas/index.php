<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

requireAdmin();
$body = json_decode(file_get_contents('php://input'), true) ?? [];
if (empty($body['modulo_id']) || empty($body['titulo'])) jsonError('modulo_id e titulo são obrigatórios.', 400);

$db   = getDB();
$stmt = $db->prepare('
    INSERT INTO aulas (modulo_id, titulo, ordem, video_url, duracao_min, descricao, tipo)
    VALUES (?, ?, ?, ?, ?, ?, ?)
');
$stmt->execute([
    $body['modulo_id'],
    $body['titulo'],
    $body['ordem']       ?? 0,
    $body['url']         ?? $body['video_url'] ?? null,
    $body['duracao_min'] ?? 0,
    $body['descricao']   ?? null,
    $body['tipo']        ?? 'video',
]);

$id   = $db->lastInsertId();
$stmt = $db->prepare('SELECT * FROM aulas WHERE id = ?');
$stmt->execute([$id]);
jsonOk($stmt->fetch(), 201);
