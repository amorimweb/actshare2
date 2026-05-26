<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$user = requireAuth();
$body = json_decode(file_get_contents('php://input'), true) ?? [];

$matriculaId = (int)($body['matricula_id'] ?? 0);
$aulaId      = (int)($body['aula_id']      ?? 0);
if (!$matriculaId || !$aulaId) jsonError('matricula_id e aula_id são obrigatórios.', 400);

$concluida    = $body['concluida']    ?? false;
$tempoParada  = $body['tempo_parada'] ?? 0;

$db = getDB();

// Verifica que a matrícula pertence ao usuário
$stmt = $db->prepare('SELECT id FROM matriculas WHERE id = ? AND aluno_id = ? LIMIT 1');
$stmt->execute([$matriculaId, $user['id']]);
if (!$stmt->fetch()) jsonError('Matrícula não encontrada.', 403);

$dataConclusao = $concluida ? date('Y-m-d H:i:s') : null;

$stmt = $db->prepare('
    INSERT INTO progresso_aula (matricula_id, aula_id, concluida, tempo_parada, data_conclusao)
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        concluida      = VALUES(concluida),
        tempo_parada   = VALUES(tempo_parada),
        data_conclusao = COALESCE(data_conclusao, VALUES(data_conclusao)),
        updated_at     = NOW()
');
$stmt->execute([$matriculaId, $aulaId, $concluida ? 1 : 0, $tempoParada, $dataConclusao]);

jsonOk(['success' => true]);
