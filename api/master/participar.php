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

// 1. Busca contrato B2B
$stmt = $db->prepare('SELECT id, vagas_totais, vagas_usadas, participante FROM matriculas WHERE aluno_id = ? AND curso_id = ? AND vagas_totais > 0 LIMIT 1');
$stmt->execute([$gestor['id'], $cursoId]);
$contract = $stmt->fetch();

if (!$contract) {
    jsonError('Você não possui contrato B2B para este curso.', 404);
}

// 2. Verifica se já está cadastrado como participante
if ($contract['participante'] == 1) {
    jsonError('Você já está registrado como participante deste curso.', 400);
}

// 3. Verifica se há vagas disponíveis
if ($contract['vagas_usadas'] >= $contract['vagas_totais']) {
    jsonError('Limite de vagas deste curso foi atingido.', 400);
}

// 4. Registra gestor como participante
$db->beginTransaction();
try {
    $stmt = $db->prepare('UPDATE matriculas SET participante = 1, vagas_usadas = vagas_usadas + 1 WHERE id = ?');
    $stmt->execute([$contract['id']]);
    $db->commit();
    
    jsonOk(['success' => true, 'message' => 'Você foi cadastrado como participante com sucesso!']);
} catch (Exception $e) {
    $db->rollBack();
    jsonError($e->getMessage(), 500);
}
