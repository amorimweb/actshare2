<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$user = getAuthUser();
$acesso = null;

if ($user && $user['role'] === 'gestor') {
    $db = getDB();
    $stmt = $db->prepare('SELECT certificado_acesso FROM organizacoes WHERE gestor_id = ? AND ativo = 1 LIMIT 1');
    $stmt->execute([$user['id']]);
    $res = $stmt->fetch();
    $acesso = $res ? $res['certificado_acesso'] : null;
}

jsonOk([
    'isMaster' => $user && $user['role'] === 'gestor',
    'isAdmin'  => $user && $user['role'] === 'admin',
    'certificado_acesso' => $acesso,
]);
