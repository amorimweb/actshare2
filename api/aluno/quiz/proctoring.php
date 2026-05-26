<?php
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$user = requireAuth();
$body = json_decode(file_get_contents('php://input'), true) ?? [];

$matriculaId = (int)($body['matricula_id'] ?? 0);
$aulaId      = (int)($body['aula_id'] ?? 0);
$tipoEvento  = trim($body['tipo_evento'] ?? '');
$detalhes    = trim($body['detalhes'] ?? '');

if (!$matriculaId || !$aulaId || !$tipoEvento) {
    jsonError('Dados incompletos para registro do log.', 400);
}

$db = getDB();

// Verifica se a matrícula pertence ao usuário logado
$stmt = $db->prepare('SELECT id FROM matriculas WHERE id = ? AND aluno_id = ? LIMIT 1');
$stmt->execute([$matriculaId, $user['id']]);
if (!$stmt->fetch()) {
    jsonError('Matrícula inválida ou não autorizada.', 403);
}

try {
    $stmt = $db->prepare('
        INSERT INTO proctoring_logs (matricula_id, aula_id, tipo_evento, detalhes)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([$matriculaId, $aulaId, $tipoEvento, $detalhes]);
    
    jsonOk(['success' => true, 'message' => 'Log de proctoring registrado com sucesso.']);
} catch (Exception $e) {
    jsonError($e->getMessage(), 500);
}
