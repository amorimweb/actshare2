<?php
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

try {
    $db = getDB();
    $db->query('SELECT 1');

    $cursos     = (int) $db->query('SELECT COUNT(*) FROM cursos')->fetchColumn();
    $categorias = (int) $db->query('SELECT COUNT(*) FROM categorias')->fetchColumn();
    $primeiros  = $db->query('SELECT id, titulo FROM cursos ORDER BY id LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);

    jsonOk([
        'php'        => PHP_VERSION,
        'db'         => DB_NAME,
        'cursos'     => $cursos,
        'categorias' => $categorias,
        'primeiros_cursos' => $primeiros,
    ]);
} catch (Throwable $e) {
    jsonError('Falha: ' . $e->getMessage(), 500);
}
