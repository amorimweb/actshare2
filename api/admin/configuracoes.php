<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/configuracoes.php';

requireAdmin();
$method = $_SERVER['REQUEST_METHOD'];
$db = getDB();

if ($method === 'GET') {
    jsonOk(getConfiguracoes($db));
}

if ($method === 'PUT') {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    $permitidas = array_keys(getConfiguracoes($db));

    $stmt = $db->prepare('INSERT INTO configuracoes (chave, valor) VALUES (?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor)');
    foreach ($body as $chave => $valor) {
        if (!in_array($chave, $permitidas, true)) continue;
        $stmt->execute([$chave, (string)$valor]);
    }

    jsonOk(getConfiguracoes($db));
}

methodNotAllowed();
