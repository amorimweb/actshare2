<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/configuracoes.php';

// Endpoint público (sem login) — o carrinho/checkout precisa das faixas
// mesmo para visitante não autenticado, só para mostrar o desconto estimado.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$db = getDB();
$config = getConfiguracoes($db);
jsonOk(getFaixasDescontoProgressivo($config));
