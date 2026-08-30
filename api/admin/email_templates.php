<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->query('SELECT * FROM email_templates ORDER BY nome');
    jsonOk($stmt->fetchAll());
}

methodNotAllowed();
