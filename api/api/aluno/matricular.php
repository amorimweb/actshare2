<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$user  = requireAuth();
$body  = json_decode(file_get_contents('php://input'), true) ?? [];
$cursoId = (int)($body['cursoId'] ?? 0);
if (!$cursoId) jsonError('cursoId é obrigatório.', 400);

$db   = getDB();
$stmt = $db->prepare('SELECT id FROM matriculas WHERE aluno_id = ? AND curso_id = ? LIMIT 1');
$stmt->execute([$user['id'], $cursoId]);
if ($stmt->fetch()) jsonOk(['alreadyEnrolled' => true]);

$stmt = $db->prepare('INSERT INTO matriculas (aluno_id, curso_id, progresso_total, concluido) VALUES (?, ?, 0, 0)');
$stmt->execute([$user['id'], $cursoId]);

$id   = $db->lastInsertId();
$stmt = $db->prepare('SELECT * FROM matriculas WHERE id = ?');
$stmt->execute([$id]);
jsonOk($stmt->fetch(), 201);
