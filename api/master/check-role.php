<?php
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$user = getAuthUser();

jsonOk([
    'isMaster' => $user && $user['role'] === 'gestor',
    'isAdmin'  => $user && $user['role'] === 'admin',
]);
