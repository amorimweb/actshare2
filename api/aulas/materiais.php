<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

$method = $_SERVER['REQUEST_METHOD'];
$aulaId = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$aulaId) jsonError('ID de aula inválido.', 400);

$db = getDB();

if ($method === 'GET') {
    requireAuth();
    $stmt = $db->prepare('SELECT id, aula_id, nome_arquivo, tamanho_bytes, created_at FROM aula_materiais WHERE aula_id = ? ORDER BY created_at');
    $stmt->execute([$aulaId]);
    jsonOk($stmt->fetchAll());
}

if ($method === 'POST') {
    requireAdmin();

    if (empty($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
        jsonError('Envie um arquivo válido.', 400);
    }

    $stmt = $db->prepare('SELECT id FROM aulas WHERE id = ? LIMIT 1');
    $stmt->execute([$aulaId]);
    if (!$stmt->fetch()) jsonError('Aula não encontrada.', 404);

    $arquivo = $_FILES['arquivo'];
    $maxBytes = 5 * 1024 * 1024;
    if ($arquivo['size'] > $maxBytes) {
        jsonError('Arquivo maior que 5MB.', 400);
    }

    $dir = __DIR__ . '/../../assets/uploads/materiais';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $nomeOriginal = $arquivo['name'];
    $ext = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
    $extsPermitidas = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'jpg', 'jpeg', 'png', 'txt', 'csv'];
    if ($ext && !in_array($ext, $extsPermitidas)) {
        jsonError('Tipo de arquivo não permitido.', 400);
    }

    $nomeArmazenado = bin2hex(random_bytes(16)) . ($ext ? '.' . $ext : '');
    $destino = $dir . '/' . $nomeArmazenado;

    if (!move_uploaded_file($arquivo['tmp_name'], $destino)) {
        jsonError('Falha ao salvar o arquivo.', 500);
    }

    $stmt = $db->prepare('
        INSERT INTO aula_materiais (aula_id, nome_arquivo, caminho, tamanho_bytes)
        VALUES (?, ?, ?, ?)
    ');
    $stmt->execute([$aulaId, $nomeOriginal, 'materiais/' . $nomeArmazenado, $arquivo['size']]);

    $id = $db->lastInsertId();
    $stmt = $db->prepare('SELECT id, aula_id, nome_arquivo, tamanho_bytes, created_at FROM aula_materiais WHERE id = ?');
    $stmt->execute([$id]);
    jsonOk($stmt->fetch(), 201);
}

methodNotAllowed();
