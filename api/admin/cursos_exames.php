<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$cursoId = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$cursoId) jsonError('ID do curso inválido.', 400);

$db = getDB();
$tipos = ['QM', 'AU', 'TL'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare('SELECT id, tipo, preco, ativo FROM exames_curso WHERE curso_id = ?');
    $stmt->execute([$cursoId]);
    $existentes = [];
    foreach ($stmt->fetchAll() as $row) {
        $existentes[$row['tipo']] = $row;
    }
    $out = [];
    foreach ($tipos as $tipo) {
        $out[] = $existentes[$tipo] ?? ['id' => null, 'tipo' => $tipo, 'preco' => 0, 'ativo' => 0];
    }
    jsonOk($out);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $exames = $body['exames'] ?? [];

    $stmt = $db->prepare('
        INSERT INTO exames_curso (curso_id, tipo, preco, ativo)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE preco = VALUES(preco), ativo = VALUES(ativo)
    ');
    foreach ($exames as $ex) {
        $tipo = strtoupper(trim($ex['tipo'] ?? ''));
        if (!in_array($tipo, $tipos)) continue;
        $preco = (float)($ex['preco'] ?? 0);
        $ativo = !empty($ex['ativo']) ? 1 : 0;
        $stmt->execute([$cursoId, $tipo, $preco, $ativo]);
    }

    jsonOk(['success' => true]);
}

methodNotAllowed();
