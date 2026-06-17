<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$user = requireAuth();
$id   = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

// Verifica matrícula
$stmt = $db->prepare('SELECT * FROM matriculas WHERE aluno_id = ? AND curso_id = ? LIMIT 1');
$stmt->execute([$user['id'], $id]);
$matricula = $stmt->fetch();
if (!$matricula) jsonError('Você não está matriculado neste curso.', 403);

// Curso com módulos e aulas
$stmt = $db->prepare('SELECT * FROM cursos WHERE id = ?');
$stmt->execute([$id]);
$curso = $stmt->fetch();
if (!$curso) jsonError('Curso não encontrado.', 404);

$stmt = $db->prepare('SELECT * FROM modulos WHERE curso_id = ? ORDER BY ordem');
$stmt->execute([$id]);
$modulos = $stmt->fetchAll();

foreach ($modulos as &$mod) {
    $stmt2 = $db->prepare('SELECT * FROM aulas WHERE modulo_id = ? ORDER BY ordem');
    $stmt2->execute([$mod['id']]);
    $mod['aulas'] = $stmt2->fetchAll();
}

// Progresso do aluno neste curso
$stmt = $db->prepare('SELECT * FROM progresso_aula WHERE matricula_id = ?');
$stmt->execute([$matricula['id']]);
$progresso = $stmt->fetchAll();

jsonOk([
    'curso'     => array_merge($curso, ['modulos' => $modulos]),
    'matricula' => $matricula,
    'progresso' => $progresso,
]);
