<?php
// Webhook do ASAAS: chamado por eles quando o status de uma cobrança muda.
// Configurar a URL desta rota no painel ASAAS (Configurações > Webhooks),
// com o mesmo token em ASAAS_WEBHOOK_TOKEN no .env para validar a origem.
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/pedidos.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

if (ASAAS_WEBHOOK_TOKEN !== '') {
    $tokenRecebido = $_SERVER['HTTP_ASAAS_ACCESS_TOKEN'] ?? '';
    if (!hash_equals(ASAAS_WEBHOOK_TOKEN, $tokenRecebido)) {
        jsonError('Token de webhook inválido.', 401);
    }
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$evento = $body['event'] ?? '';
$payment = $body['payment'] ?? [];

$eventosConfirmados = ['PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED'];

if (!in_array($evento, $eventosConfirmados, true)) {
    // Outros eventos (PAYMENT_OVERDUE, PAYMENT_DELETED etc.) só são registrados.
    jsonOk(['success' => true, 'ignored' => true]);
}

$pedidoId = (int)($payment['externalReference'] ?? 0);
if (!$pedidoId) {
    jsonError('externalReference (pedido_id) ausente no payload.', 400);
}

$db = getDB();
try {
    liberarMatriculasDoPedido($db, $pedidoId);
    jsonOk(['success' => true]);
} catch (Exception $e) {
    jsonError($e->getMessage(), 500);
}
