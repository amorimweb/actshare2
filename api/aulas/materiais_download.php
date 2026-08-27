<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$id = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();
$stmt = $db->prepare('
    SELECT am.*, a.publica, m.curso_id
    FROM aula_materiais am
    JOIN aulas a ON am.aula_id = a.id
    JOIN modulos m ON a.modulo_id = m.id
    WHERE am.id = ?
    LIMIT 1
');
$stmt->execute([$id]);
$material = $stmt->fetch();
if (!$material) jsonError('Material não encontrado.', 404);

// Controle de acesso: aula pública libera pra qualquer um; caso contrário
// precisa estar autenticado como admin ou matriculado no curso da aula.
if ((int)$material['publica'] !== 1) {
    $user = requireAuth();
    if ($user['role'] !== 'admin') {
        $stmtMat = $db->prepare('SELECT id FROM matriculas WHERE aluno_id = ? AND curso_id = ? LIMIT 1');
        $stmtMat->execute([$user['id'], $material['curso_id']]);
        if (!$stmtMat->fetch()) jsonError('Você não tem acesso a este material.', 403);
    }
}

$caminhoAbsoluto = __DIR__ . '/../../assets/uploads/' . $material['caminho'];
if (!is_file($caminhoAbsoluto)) jsonError('Arquivo não encontrado no servidor.', 404);

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($material['nome_arquivo']) . '"');
header('Content-Length: ' . filesize($caminhoAbsoluto));
readfile($caminhoAbsoluto);
exit;
