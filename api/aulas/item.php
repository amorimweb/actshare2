<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

if ($method === 'PUT') {
    requireAdmin();
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $map    = [
        'titulo'               => 'titulo', 
        'ordem'                => 'ordem', 
        'url'                  => 'video_url', 
        'video_url'            => 'video_url', 
        'duracao_min'          => 'duracao_min', 
        'descricao'            => 'descricao', 
        'tipo'                 => 'tipo',
        'e_prova'              => 'e_prova',
        'quizz_qtd_perguntas'  => 'quizz_qtd_perguntas',
        'exemplar_global'      => 'exemplar_global',
        'nota_corte_tipo'      => 'nota_corte_tipo',
        'nota_corte_valor'     => 'nota_corte_valor',
        'tempo_limite_minutos' => 'tempo_limite_minutos',
        'bloquear_proctoring'  => 'bloquear_proctoring'
    ];
    $set    = [];
    $params = [];
    foreach ($map as $bodyKey => $col) {
        if (array_key_exists($bodyKey, $body)) { $set[$col] = "$col = ?"; $params[] = $body[$bodyKey]; }
    }
    if (!$set) jsonError('Nenhum campo para atualizar.', 400);
    $params[] = $id;
    getDB()->prepare('UPDATE aulas SET ' . implode(', ', $set) . ' WHERE id = ?')->execute($params);

    $stmt = getDB()->prepare('SELECT * FROM aulas WHERE id = ?');
    $stmt->execute([$id]);
    jsonOk($stmt->fetch());
}

if ($method === 'DELETE') {
    requireAdmin();
    $db   = getDB();
    $stmt = $db->prepare('SELECT id FROM aulas WHERE id = ?');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Aula não encontrada.', 404);
    $db->prepare('DELETE FROM aulas WHERE id = ?')->execute([$id]);
    jsonOk(['success' => true]);
}

methodNotAllowed();
