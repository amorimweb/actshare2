<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    $stmt = $db->query('SELECT * FROM cupons ORDER BY created_at DESC');
    jsonOk($stmt->fetchAll());
}

if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    
    $codigo = strtoupper(trim($body['codigo'] ?? ''));
    $tipo = trim($body['tipo'] ?? 'porcentagem');
    $valor = floatval($body['valor'] ?? 0);
    $validade = trim($body['validade'] ?? '');
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

    // Verifica unicidade do código
    $stmt = $db->prepare('SELECT id FROM cupons WHERE codigo = ? LIMIT 1');
    $stmt->execute([$codigo]);
    if ($stmt->fetch()) {
        jsonError('Já existe um cupom cadastrado com este código.', 400);
    }

    $stmt = $db->prepare('
        INSERT INTO cupons (codigo, tipo, valor, validade, limite_uso, usos)
        VALUES (?, ?, ?, ?, ?, 0)
    ');
    $stmt->execute([$codigo, $tipo, $valor, $validade, $limite_uso]);
    
    jsonOk(['success' => true, 'id' => $db->lastInsertId()], 201);
}

methodNotAllowed();
