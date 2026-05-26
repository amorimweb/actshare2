<?php
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$user = getAuthUser();
if (!$user) jsonOk(['user' => null]);

jsonOk(['user' => [
    'id'    => $user['id'],
    'email' => $user['email'],
    'role'  => $user['role'],
    'nome'  => $user['nome'],
]]);
