<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$matriculaId = (int)($GLOBALS['_ROUTE']['matricula_id'] ?? 0);
if (!$matriculaId) jsonError('Matrícula inválida.', 400);
if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$db = getDB();

$stmt = $db->prepare('
    SELECT qr.id, qr.nota, qr.aprovado, qr.acertos, qr.total_perguntas, qr.updated_at,
           a.titulo AS aula_titulo, a.e_prova
    FROM quiz_resposta qr
    JOIN aulas a ON qr.aula_id = a.id
    WHERE qr.matricula_id = ?
');
$stmt->execute([$matriculaId]);
$quizzes = $stmt->fetchAll();

$stmt = $db->prepare('
    SELECT et.id, et.iniciado_em, et.finalizado_em, et.total_questoes, et.acertos, et.erros,
           et.nao_respondidas, et.resultado, et.prazo_retake_ate, ec.tipo AS exame_tipo, ec.nome AS exame_nome
    FROM exame_tentativas et
    JOIN exames_curso ec ON ec.id = et.exame_curso_id
    WHERE et.matricula_id = ?
    ORDER BY et.iniciado_em DESC
');
$stmt->execute([$matriculaId]);
$exames = $stmt->fetchAll();

jsonOk(['quizzes' => $quizzes, 'exames' => $exames]);
