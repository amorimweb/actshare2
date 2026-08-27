<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);

$db = getDB();

if ($method === 'PUT') {
    $stmt = $db->prepare('SELECT id FROM cupons WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        jsonError('Cupom não encontrado.', 404);
    }

    $body = json_decode(file_get_contents('php://input'), true) ?? [];

    $codigo     = strtoupper(trim($body['codigo'] ?? ''));
    $tipo       = trim($body['tipo'] ?? 'porcentagem');
    $valor      = floatval($body['valor'] ?? 0);
    $validade   = trim($body['validade'] ?? '');
    $limite_uso = isset($body['limite_uso']) && $body['limite_uso'] !== '' ? intval($body['limite_uso']) : null;

    if (empty($codigo) || strlen($codigo) > 10) {
        jsonError('Código do cupom inválido (máximo 10 caracteres).', 400);
    }
    if (!in_array($tipo, ['fixo', 'porcentagem'])) {
        jsonError('Tipo de desconto inválido.', 400);
    }
    if ($valor <= 0) {
        jsonError('Valor do desconto deve ser maior que zero.', 400);
    }
    if (empty($validade)) {
        jsonError('Data de validade é obrigatória.', 400);
    }

    // Verifica unicidade do código (ignorando o próprio cupom)
    $stmt = $db->prepare('SELECT id FROM cupons WHERE codigo = ? AND id != ? LIMIT 1');
    $stmt->execute([$codigo, $id]);
    if ($stmt->fetch()) {
        jsonError('Já existe outro cupom cadastrado com este código.', 400);
    }

    $stmt = $db->prepare('
        UPDATE cupons SET codigo = ?, tipo = ?, valor = ?, validade = ?, limite_uso = ?
        WHERE id = ?
    ');
    $stmt->execute([$codigo, $tipo, $valor, $validade, $limite_uso, $id]);

    jsonOk(['success' => true]);
}

if ($method === 'DELETE') {
    $stmt = $db->prepare('SELECT id FROM cupons WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        jsonError('Cupom não encontrado.', 404);
    }
    
    $stmt = $db->prepare('DELETE FROM cupons WHERE id = ?');
    $stmt->execute([$id]);
    
    jsonOk(['success' => true]);
}

methodNotAllowed();
