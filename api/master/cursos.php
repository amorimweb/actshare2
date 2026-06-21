<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$gestor = requireMasterOrAdmin();
$db = getDB();
$context = getGestorContext($gestor, $db);
$mainGestorId = $context['id'];

// Retorna todas as matrículas B2B compradas pelo gestor (onde vagas_totais > 0)
// E junta com cursos para pegar título, thumb_url, etc.
$stmt = $db->prepare('
    SELECT m.id AS matricula_id, m.curso_id, m.vagas_totais, m.vagas_usadas, m.participante, m.created_at AS data_compra,
           c.titulo AS curso_titulo, c.thumb_url, c.carga_horaria_horas, c.prazo_conclusao_dias
    FROM matriculas m
    JOIN cursos c ON m.curso_id = c.id
    WHERE m.aluno_id = ? AND m.vagas_totais IS NOT NULL AND m.vagas_totais > 0
    ORDER BY m.created_at DESC
');
$stmt->execute([$mainGestorId]);
$cursos = $stmt->fetchAll();

jsonOk($cursos);
