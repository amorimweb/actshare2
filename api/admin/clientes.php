<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$db = getDB();

// "Cliente" é quem paga: todo usuário que já fez pelo menos um pedido, ou que
// é o gestor titular de uma organização (contrato B2B), mesmo sem pedido
// ainda registrado (ex.: cadastro manual do Admin).
$stmt = $db->query('
    SELECT u.id, u.nome, u.email, u.documento, u.tipo_pessoa, u.razao_social,
           u.inscricao_estadual, u.telefone, u.cidade, u.estado, u.observacao_admin,
           u.created_at,
           o.id AS organizacao_id, o.certificado_acesso,
           (SELECT COUNT(*) FROM pedidos WHERE usuario_id = u.id) AS total_pedidos
    FROM usuarios u
    LEFT JOIN organizacoes o ON o.gestor_id = u.id
    WHERE u.id IN (SELECT DISTINCT usuario_id FROM pedidos)
       OR u.id IN (SELECT DISTINCT gestor_id FROM organizacoes)
    ORDER BY u.nome
');
$clientes = $stmt->fetchAll();

jsonOk($clientes);
