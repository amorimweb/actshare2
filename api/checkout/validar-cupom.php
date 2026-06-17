<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

requireAuth();

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$codigo = trim($body['codigo'] ?? '');
$total = isset($body['total']) ? (float)$body['total'] : null;

if (!$codigo) {
    jsonError('Código do cupom é obrigatório.', 400);
}

$db = getDB();

// 1. Busca nos cupons padrão
$stmt = $db->prepare('SELECT * FROM cupons WHERE codigo = ? LIMIT 1');
$stmt->execute([$codigo]);
$cupom = $stmt->fetch();

if ($cupom) {
    // Valida expiração
    if (strtotime($cupom['validade']) < time()) {
        jsonError('Este cupom de desconto expirou.', 400);
    }
    // Valida limite de usos
    if ($cupom['limite_uso'] !== null && $cupom['usos'] >= $cupom['limite_uso']) {
        jsonError('Este cupom atingiu o limite máximo de utilizações.', 400);
    }
    
    // Valida se o desconto fixo excede o total do carrinho
    if ($total !== null && $cupom['tipo'] === 'fixo' && (float)$cupom['valor'] > $total) {
        jsonError('O valor do cupom excede o valor total do carrinho.', 400);
    }
    
    jsonOk([
        'success' => true,
        'cupom' => [
            'id'     => (int)$cupom['id'],
            'codigo' => $cupom['codigo'],
            'tipo'   => $cupom['tipo'], // 'fixo' ou 'porcentagem'
            'valor'  => (float)$cupom['valor']
        ]
    ]);
}

// 2. Busca nos cupons de indicação B2C
$stmt = $db->prepare('SELECT * FROM cupons_indicacao WHERE codigo = ? LIMIT 1');
$stmt->execute([$codigo]);
$ref = $stmt->fetch();

if ($ref) {
    // Valida expiração
    if (strtotime($ref['validade']) < time()) {
        jsonError('Este cupom de indicação expirou.', 400);
    }
    // Valida utilização
    if ($ref['utilizado'] == 1) {
        jsonError('Este cupom de indicação já foi utilizado.', 400);
    }
    
    jsonOk([
        'success' => true,
        'cupom' => [
            'id'     => (int)$ref['id'],
            'codigo' => $ref['codigo'],
            'tipo'   => 'porcentagem',
            'valor'  => (float)$ref['percentual'],
            'is_indicacao' => true
        ]
    ]);
}

jsonError('Cupom inválido ou inexistente.', 404);
