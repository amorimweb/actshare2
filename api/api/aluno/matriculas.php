<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$user = requireAuth();
$db   = getDB();

$stmt = $db->prepare('
    SELECT m.*, c.titulo AS curso_titulo, c.thumb_url, c.carga_horaria_horas
    FROM matriculas m
    JOIN cursos c ON m.curso_id = c.id
    WHERE m.aluno_id = ?
    ORDER BY m.created_at DESC
');
$stmt->execute([$user['id']]);
jsonOk($stmt->fetchAll());
