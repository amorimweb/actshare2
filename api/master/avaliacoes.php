<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$gestor = requireMasterOrAdmin();

$alunoId = (int)($GLOBALS['_ROUTE']['aluno_id'] ?? 0);
$cursoId = (int)($GLOBALS['_ROUTE']['curso_id'] ?? 0);

if (!$alunoId || !$cursoId) {
    jsonError('IDs de aluno e curso inválidos.', 400);
}

$db = getDB();
$context = getGestorContext($gestor, $db);
$mainGestorId = $context['id'];
$orgId = $context['org_id'];

if (!$orgId) {
    jsonError('Organização não encontrada.', 404);
}
$org = ['id' => $orgId];

// 2. Garante que o aluno pertence à organização do gestor (ou se é o próprio gestor/sub-gestor)
if ($alunoId !== (int)$gestor['id'] && $alunoId !== $mainGestorId) {
    $stmt = $db->prepare('SELECT id FROM membros_organizacao WHERE organizacao_id = ? AND usuario_id = ? LIMIT 1');
    $stmt->execute([$org['id'], $alunoId]);
    if (!$stmt->fetch()) {
        jsonError('Aluno não pertence à sua organização.', 403);
    }
}

// 3. Busca matrícula do aluno
$stmt = $db->prepare('SELECT id FROM matriculas WHERE aluno_id = ? AND curso_id = ? LIMIT 1');
$stmt->execute([$alunoId, $cursoId]);
$mat = $stmt->fetch();

if (!$mat) {
    jsonError('Matrícula não encontrada.', 404);
}
$matriculaId = $mat['id'];

// 4. Busca tentativas detalhadas na tabela `avaliacao_tentativas`
$stmt = $db->prepare('
    SELECT id, total_questoes, acertos, erros, nota, resultado, respostas_json, created_at
    FROM avaliacao_tentativas
    WHERE matricula_id = ?
    ORDER BY created_at DESC
');
$stmt->execute([$matriculaId]);
$tentativas = $stmt->fetchAll();

// Decodifica JSON de respostas
foreach ($tentativas as &$t) {
    if (!empty($t['respostas_json'])) {
        $t['respostas'] = json_decode($t['respostas_json'], true);
    } else {
        $t['respostas'] = [];
    }
    unset($t['respostas_json']);
}

// 5. Busca resumo de quizzes da tabela `quiz_resposta` (caso existam quizzes comuns sem tentativas detalhadas)
$stmt = $db->prepare('
    SELECT qr.id, qr.nota, qr.aprovado, qr.acertos, qr.total_perguntas, qr.tentativas_restantes, qr.updated_at,
           a.titulo AS aula_titulo, a.e_prova
    FROM quiz_resposta qr
    JOIN aulas a ON qr.aula_id = a.id
    WHERE qr.matricula_id = ?
');
$stmt->execute([$matriculaId]);
$quizzes = $stmt->fetchAll();

jsonOk([
    'matricula_id' => $matriculaId,
    'tentativas'   => $tentativas,
    'quizzes'      => $quizzes
]);
