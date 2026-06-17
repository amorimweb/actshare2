<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

requireAdmin();
$body = json_decode(file_get_contents('php://input'), true) ?? [];
if (empty($body['modulo_id']) || empty($body['titulo'])) jsonError('modulo_id e titulo são obrigatórios.', 400);

$db   = getDB();
$stmt = $db->prepare('
    INSERT INTO aulas (modulo_id, titulo, ordem, video_url, duracao_min, descricao, tipo, e_prova, quizz_qtd_perguntas, exemplar_global, nota_corte_tipo, nota_corte_valor, tempo_limite_minutos, bloquear_proctoring)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
');
$stmt->execute([
    $body['modulo_id'],
    $body['titulo'],
    $body['ordem']               ?? 0,
    $body['url']                 ?? $body['video_url'] ?? null,
    $body['duracao_min']         ?? 0,
    $body['descricao']           ?? null,
    $body['tipo']                ?? 'video',
    $body['e_prova']             ?? 0,
    $body['quizz_qtd_perguntas'] ?? 1,
    $body['exemplar_global']     ?? 0,
    $body['nota_corte_tipo']     ?? 'percentual',
    $body['nota_corte_valor']    ?? 70,
    $body['tempo_limite_minutos']?? 0,
    $body['bloquear_proctoring'] ?? 0,
]);

$id   = $db->lastInsertId();
$stmt = $db->prepare('SELECT * FROM aulas WHERE id = ?');
$stmt->execute([$id]);
jsonOk($stmt->fetch(), 201);
