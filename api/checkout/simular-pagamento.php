<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/pedidos.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$user = requireAuth();
$body = json_decode(file_get_contents('php://input'), true) ?? [];

$pedidoId = (int)($body['pedido_id'] ?? 0);

if (!$pedidoId) {
    jsonError('pedido_id é obrigatório.', 400);
}

$db = getDB();

// Verifica se o pedido pertence ao usuário ou se é admin
$stmt = $db->prepare('SELECT id FROM pedidos WHERE id = ? AND (usuario_id = ? OR ? = "admin") LIMIT 1');
$stmt->execute([$pedidoId, $user['id'], $user['role']]);
if (!$stmt->fetch()) {
    jsonError('Pedido não encontrado.', 404);
}

try {
    $resultado = liberarMatriculasDoPedido($db, $pedidoId);

    if ($resultado['already_paid']) {
        jsonOk(['success' => true, 'message' => 'Este pedido já está pago e as matrículas já foram liberadas.']);
    }

    jsonOk([
        'success'      => true,
        'message'      => 'Pagamento simulado com sucesso! Matrículas liberadas.',
        'updated_user' => $resultado['updated_user'],
    ]);
} catch (Exception $e) {
    jsonError($e->getMessage(), 500);
}
