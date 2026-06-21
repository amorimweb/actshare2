<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$gestor = requireMasterOrAdmin();
$cursoId = (int)($GLOBALS['_ROUTE']['id'] ?? 0);

if (!$cursoId) {
    jsonError('ID do curso inválido.', 400);
}

$db = getDB();
$context = getGestorContext($gestor, $db);
$mainGestorId = $context['id'];

// 1. Busca contrato B2B
$stmt = $db->prepare('SELECT id, vagas_totais, vagas_usadas, participante, com_prova FROM matriculas WHERE aluno_id = ? AND curso_id = ? AND vagas_totais > 0 LIMIT 1');
$stmt->execute([$mainGestorId, $cursoId]);
$contract = $stmt->fetch();

if (!$contract) {
    jsonError('Você não possui contrato B2B para este curso.', 404);
}

// 2. Verifica se já está cadastrado como participante
if ($context['is_subgestor']) {
    $stmt = $db->prepare('SELECT id FROM matriculas WHERE aluno_id = ? AND curso_id = ? AND vagas_totais IS NULL LIMIT 1');
    $stmt->execute([$gestor['id'], $cursoId]);
    if ($stmt->fetch()) {
        jsonError('Você já está registrado como participante deste curso.', 400);
    }
} else {
    if ($contract['participante'] == 1) {
        jsonError('Você já está registrado como participante deste curso.', 400);
    }
}

// 3. Verifica se há vagas disponíveis
if ($contract['vagas_usadas'] >= $contract['vagas_totais']) {
    jsonError('Limite de vagas deste curso foi atingido.', 400);
}

// 4. Registra gestor como participante
$db->beginTransaction();
try {
    if ($context['is_subgestor']) {
        // Busca prazo padrão do curso
        $stmtCurso = $db->prepare('SELECT prazo_conclusao_dias FROM cursos WHERE id = ? LIMIT 1');
        $stmtCurso->execute([$cursoId]);
        $curso = $stmtCurso->fetch();
        $prazoDias = $curso ? (int)$curso['prazo_conclusao_dias'] : 180;
        if (!$prazoDias) $prazoDias = 180;

        $dataFimAcesso = date('Y-m-d H:i:s', time() + ($prazoDias * 24 * 3600));

        // Cria matrícula do sub-gestor
        $stmtMat = $db->prepare('
            INSERT INTO matriculas (aluno_id, curso_id, data_fim_acesso, vagas_usadas, vagas_totais, com_prova)
            VALUES (?, ?, ?, 1, NULL, ?)
        ');
        $stmtMat->execute([$gestor['id'], $cursoId, $dataFimAcesso, (int)$contract['com_prova']]);

        // Incrementa vagas_usadas no contrato do gestor
        $stmtInc = $db->prepare('UPDATE matriculas SET vagas_usadas = vagas_usadas + 1 WHERE id = ?');
        $stmtInc->execute([$contract['id']]);
    } else {
        $stmt = $db->prepare('UPDATE matriculas SET participante = 1, vagas_usadas = vagas_usadas + 1 WHERE id = ?');
        $stmt->execute([$contract['id']]);
    }
    $db->commit();
    
    jsonOk(['success' => true, 'message' => 'Você foi cadastrado como participante com sucesso!']);
} catch (Exception $e) {
    $db->rollBack();
    jsonError($e->getMessage(), 500);
}
