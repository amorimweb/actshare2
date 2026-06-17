<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$user = requireAuth();
$id   = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

$alunoId = $user['id'];
if (in_array($user['role'], ['admin', 'gestor']) && isset($_GET['aluno_id'])) {
    $alunoId = (int)$_GET['aluno_id'];
}

// Busca informações do aluno
$stmtUser = $db->prepare('SELECT nome, email FROM usuarios WHERE id = ? LIMIT 1');
$stmtUser->execute([$alunoId]);
$alunoInfo = $stmtUser->fetch();
$alunoNome = $alunoInfo ? $alunoInfo['nome'] : 'Aluno';
$alunoEmail = $alunoInfo ? $alunoInfo['email'] : '';

// Verifica matrícula
$stmt = $db->prepare('SELECT * FROM matriculas WHERE aluno_id = ? AND curso_id = ? LIMIT 1');
$stmt->execute([$alunoId, $id]);
$matricula = $stmt->fetch();
if (!$matricula) jsonError('Você não está matriculado neste curso.', 403);

// Verifica restrição de liberação de certificado pela organização (para alunos normais)
$bloqueadoEmpresa = false;
if ($user['role'] === 'aluno') {
    $stmtOrg = $db->prepare('
        SELECT o.certificado_acesso
        FROM membros_organizacao mo
        JOIN organizacoes o ON mo.organizacao_id = o.id
        JOIN matriculas m_gestor ON o.gestor_id = m_gestor.aluno_id
        WHERE mo.usuario_id = ? AND m_gestor.curso_id = ? AND m_gestor.vagas_totais > 0
        LIMIT 1
    ');
    $stmtOrg->execute([$alunoId, $id]);
    $orgSetting = $stmtOrg->fetchColumn();
    if ($orgSetting === 'empresa') {
        $bloqueadoEmpresa = true;
    }
}

// Curso com módulos e aulas, incluindo dados do instrutor
$stmt = $db->prepare('
    SELECT c.*, i.nome AS instrutor_nome, i.qualificacao1 AS instrutor_qualificacao, i.avatar_url AS instrutor_avatar
    FROM cursos c
    LEFT JOIN instrutores i ON c.instrutor_id = i.id
    WHERE c.id = ?
');
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

// Verifica se o aluno tem um exame aprovado neste curso
$stmtExam = $db->prepare('
    SELECT COUNT(*) 
    FROM quiz_resposta qr
    JOIN aulas a ON qr.aula_id = a.id
    JOIN modulos m ON a.modulo_id = m.id
    WHERE qr.matricula_id = ? AND a.e_prova = 1 AND qr.aprovado = 1
');
$stmtExam->execute([$matricula['id']]);
$hasApprovedExam = ((int)$stmtExam->fetchColumn()) > 0;

jsonOk([
    'curso'             => array_merge($curso, ['modulos' => $modulos]),
    'matricula'         => $matricula,
    'progresso'         => $progresso,
    'has_approved_exam' => $hasApprovedExam,
    'aluno_nome'        => $alunoNome,
    'aluno_email'       => $alunoEmail,
    'bloqueado_empresa' => $bloqueadoEmpresa
]);
