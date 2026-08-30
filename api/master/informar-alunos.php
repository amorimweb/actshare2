<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/email_templates.php';

$gestor = requireMasterOrAdmin();
$cursoId = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$cursoId) {
    jsonError('ID do curso inválido.', 400);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    methodNotAllowed();
}

$db = getDB();
$context = getGestorContext($gestor, $db);
$orgId = $context['org_id'];

if (!$orgId) {
    jsonOk(['enviados' => 0, 'total' => 0]);
}

$stmt = $db->prepare('
    SELECT u.email, u.nome, c.titulo AS curso_titulo
    FROM membros_organizacao mo
    JOIN usuarios u ON mo.usuario_id = u.id
    JOIN matriculas m ON u.id = m.aluno_id
    JOIN cursos c ON c.id = m.curso_id
    WHERE mo.organizacao_id = ? AND m.curso_id = ?
');
$stmt->execute([$orgId, $cursoId]);
$alunos = $stmt->fetchAll();

$total = count($alunos);
$enviados = 0;

foreach ($alunos as $aluno) {
    $ok = enviarEmailTemplate($db, 'curso_disponivel', $aluno['email'], [
        'nome' => $aluno['nome'],
        'curso' => $aluno['curso_titulo'],
    ]);
    if ($ok) $enviados++;
}

jsonOk(['enviados' => $enviados, 'total' => $total]);
