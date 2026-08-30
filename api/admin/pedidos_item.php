<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();
$id = (int)($GLOBALS['_ROUTE']['id'] ?? 0);
if (!$id) jsonError('ID inválido.', 400);
if ($_SERVER['REQUEST_METHOD'] !== 'GET') methodNotAllowed();

$db = getDB();

$stmt = $db->prepare('
    SELECT p.*, u.nome AS usuario_nome, u.email AS usuario_email, u.razao_social, u.tipo_pessoa,
           c.codigo AS cupom_codigo
    FROM pedidos p
    JOIN usuarios u ON u.id = p.usuario_id
    LEFT JOIN cupons c ON c.id = p.cupom_id
    WHERE p.id = ?
');
$stmt->execute([$id]);
$pedido = $stmt->fetch();
if (!$pedido) jsonError('Pedido não encontrado.', 404);

$pedido['nome_cliente'] = ($pedido['tipo_pessoa'] === 'juridica' && $pedido['razao_social']) ? $pedido['razao_social'] : $pedido['usuario_nome'];

$stmt = $db->prepare('
    SELECT ip.*, c.titulo AS curso_titulo, c.prazo_acesso_dias, cb.titulo AS combo_titulo
    FROM itens_pedido ip
    LEFT JOIN cursos c ON c.id = ip.curso_id
    LEFT JOIN combos cb ON cb.id = ip.combo_id
    WHERE ip.pedido_id = ?
');
$stmt->execute([$id]);
$itens = $stmt->fetchAll();

// Desconto/percentual proporcional de cada item em relação ao bruto do pedido
// (o desconto é calculado no nível do pedido, não por item, no schema atual).
$percentualDesconto = $pedido['total_bruto'] > 0 ? ($pedido['desconto'] / $pedido['total_bruto']) : 0;
foreach ($itens as &$item) {
    $item['produto'] = $item['curso_titulo'] ?? $item['combo_titulo'];
    $totalItem = (float)$item['preco_unitario'] * (int)$item['vagas'];
    $descontoItem = round($totalItem * $percentualDesconto, 2);
    $item['preco_total'] = $totalItem;
    $item['percentual_desconto'] = round($percentualDesconto * 100, 1);
    $item['liquido_pago'] = round($totalItem - $descontoItem, 2);
}

$pedido['itens'] = $itens;
jsonOk($pedido);
