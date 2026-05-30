<?php
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

try {
    $db   = getDB();
    $stmt = $db->query('SELECT 1 AS ok');
    $row  = $stmt->fetch();
    jsonOk(['ok' => true, 'db' => DB_NAME]);
} catch (Throwable $e) {
    jsonError('Falha na conexão: ' . $e->getMessage(), 500);
}
