<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

if ($method === 'GET') {
    $stmt = $db->prepare('SELECT * FROM instrutores WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $instrutor = $stmt->fetch();
    if (!$instrutor) jsonError('Instrutor não encontrado.', 404);
    jsonOk($instrutor);
}

if ($method === 'PUT') {
    requireAdmin();
    $stmt = $db->prepare('SELECT id FROM instrutores WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Instrutor não encontrado.', 404);

    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    if (empty($body['nome'])) jsonError('Nome é obrigatório.', 400);

    $stmt = $db->prepare('
        UPDATE instrutores
        SET nome = ?, qualificacao1 = ?, qualificacao2 = ?, avatar_url = ?, assinatura_url = ?, descricao = ?
        WHERE id = ?
    ');
    $stmt->execute([
        $body['nome'],
        $body['qualificacao1']  ?? null,
        $body['qualificacao2']  ?? null,
        $body['avatar_url']     ?? null,
        $body['assinatura_url'] ?? null,
        $body['descricao']      ?? null,
        $id,
    ]);

    jsonOk(['success' => true]);
}

if ($method === 'DELETE') {
    requireAdmin();
    $stmt = $db->prepare('SELECT id FROM instrutores WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) jsonError('Instrutor não encontrado.', 404);

    // Cursos que usam este instrutor voltam a ficar "sem instrutor"
    $db->prepare('UPDATE cursos SET instrutor_id = NULL WHERE instrutor_id = ?')->execute([$id]);
    $db->prepare('DELETE FROM instrutores WHERE id = ?')->execute([$id]);

    jsonOk(['success' => true]);
}

methodNotAllowed();
