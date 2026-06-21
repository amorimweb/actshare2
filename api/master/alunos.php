<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$user = requireMasterOrAdmin();
$db   = getDB();
$context = getGestorContext($user, $db);
$mainGestorId = $context['id'];
$orgId = $context['org_id'];

if (!$orgId) jsonOk([]);
$org = ['id' => $orgId];

// Busca membros da organização
$stmt = $db->prepare('
    SELECT u.id, u.nome, u.email, u.role, u.created_at
    FROM membros_organizacao mo
    JOIN usuarios u ON mo.usuario_id = u.id
    WHERE mo.organizacao_id = ?
    ORDER BY u.nome
');
$stmt->execute([$org['id']]);
$alunos = $stmt->fetchAll();

// Para cada aluno, busca matrículas com progresso
foreach ($alunos as &$aluno) {
    $stmt2 = $db->prepare('
        SELECT m.*, c.titulo AS curso_titulo,
               (SELECT qr.aprovado FROM quiz_resposta qr JOIN aulas a ON qr.aula_id = a.id WHERE qr.matricula_id = m.id AND a.e_prova = 1 LIMIT 1) AS exam_aprovado
        FROM matriculas m
        JOIN cursos c ON m.curso_id = c.id
        WHERE m.aluno_id = ?
    ');
    $stmt2->execute([$aluno['id']]);
    $aluno['matriculas'] = $stmt2->fetchAll();
}

jsonOk($alunos);
