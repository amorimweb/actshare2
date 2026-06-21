<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$gestor = requireMasterOrAdmin();
$cursoId = (int)($GLOBALS['_ROUTE']['id'] ?? 0);

if (!$cursoId) {
    jsonError('ID do curso inválido.', 400);
}

$db = getDB();
$context = getGestorContext($gestor, $db);
$mainGestorId = $context['id'];

// Busca a matrícula do gestor para este curso
$stmt = $db->prepare('
    SELECT m.id AS matricula_id, m.curso_id, m.vagas_totais, m.vagas_usadas, m.participante, m.created_at AS data_compra,
           c.titulo AS curso_titulo, c.thumb_url, c.carga_horaria_horas, c.prazo_conclusao_dias
    FROM matriculas m
    JOIN cursos c ON m.curso_id = c.id
    WHERE m.aluno_id = ? AND m.curso_id = ? AND m.vagas_totais IS NOT NULL AND m.vagas_totais > 0
    LIMIT 1
');
$stmt->execute([$mainGestorId, $cursoId]);
$compra = $stmt->fetch();

if (!$compra) {
    jsonError('Você não possui vagas compradas para este curso ou a matrícula B2B não foi localizada.', 404);
}

jsonOk($compra);
