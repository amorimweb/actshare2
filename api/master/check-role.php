<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$user = getAuthUser();
$acesso = null;

if ($user && $user['role'] === 'gestor') {
    $db = getDB();
    $context = getGestorContext($user, $db);
    $acesso = $context['certificado_acesso'];
}

jsonOk([
    'isMaster' => $user && $user['role'] === 'gestor',
    'isAdmin'  => $user && $user['role'] === 'admin',
    'certificado_acesso' => $acesso,
]);
