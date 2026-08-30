<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$db = getDB();

$stmt = $db->query('
    SELECT p.id, p.total_bruto, p.desconto, p.total_liquido, p.situacao, p.forma_pagamento,
           p.asaas_id, p.created_at,
           u.id AS usuario_id, u.nome AS usuario_nome, u.razao_social, u.tipo_pessoa,
           c.codigo AS cupom_codigo
    FROM pedidos p
    JOIN usuarios u ON u.id = p.usuario_id
    LEFT JOIN cupons c ON c.id = p.cupom_id
    ORDER BY p.created_at DESC, p.id DESC
');
$pedidos = $stmt->fetchAll();

foreach ($pedidos as &$p) {
    $p['nome_cliente'] = ($p['tipo_pessoa'] === 'juridica' && $p['razao_social']) ? $p['razao_social'] : $p['usuario_nome'];
    unset($p['usuario_nome'], $p['razao_social'], $p['tipo_pessoa']);
}

jsonOk($pedidos);
