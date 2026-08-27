<?php
/**
 * Integração real com a API do ASAAS (cobranças PIX/boleto/cartão).
 *
 * Sem ASAAS_API_KEY configurada no .env, asaasConfigured() retorna false e
 * o checkout (api/checkout/criar-pedido.php) mantém o comportamento
 * simulado atual — nada muda pra quem ainda não tem a chave. Assim que a
 * chave for configurada, as novas cobranças passam a ser reais automaticamente.
 *
 * Confirmação de pagamento real chega via webhook do ASAAS em
 * api/checkout/asaas-webhook.php, que libera as matrículas (mesma lógica
 * usada pelo botão de simulação, em includes/pedidos.php).
 */

function asaasConfigured(): bool {
    return defined('ASAAS_API_KEY') && ASAAS_API_KEY !== '';
}

function asaasBaseUrl(): string {
    return ASAAS_ENV === 'production'
        ? 'https://api.asaas.com/v3'
        : 'https://sandbox.asaas.com/api/v3';
}

function asaasRequest(string $method, string $path, ?array $body = null): array {
    $ch = curl_init(asaasBaseUrl() . $path);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'access_token: ' . ASAAS_API_KEY,
        ],
        CURLOPT_TIMEOUT => 15,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }
    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        throw new Exception('Falha ao comunicar com o ASAAS: ' . $error);
    }
    $data = json_decode($response, true) ?? [];
    if ($status >= 400) {
        $msg = $data['errors'][0]['description'] ?? 'Erro desconhecido do ASAAS.';
        throw new Exception('ASAAS: ' . $msg);
    }
    return $data;
}

/** Busca ou cria o cliente ASAAS correspondente ao usuário do sistema. */
function asaasGetOrCreateCustomer(PDO $db, array $usuario): string {
    $stmt = $db->prepare('SELECT asaas_customer_id FROM usuarios WHERE id = ? LIMIT 1');
    $stmt->execute([$usuario['id']]);
    $existente = $stmt->fetchColumn();
    if ($existente) return $existente;

    $payload = [
        'name'  => $usuario['nome'],
        'email' => $usuario['email'],
    ];
    if (!empty($usuario['documento'])) $payload['cpfCnpj'] = preg_replace('/\D/', '', $usuario['documento']);

    $customer = asaasRequest('POST', '/customers', $payload);
    $db->prepare('UPDATE usuarios SET asaas_customer_id = ? WHERE id = ?')->execute([$customer['id'], $usuario['id']]);
    return $customer['id'];
}

/**
 * Cria uma cobrança real no ASAAS para o pedido. $formaPagamento é
 * 'pix' | 'boleto' | 'cartao' (ASAAS usa PIX/BOLETO/CREDIT_CARD).
 */
function asaasCriarCobranca(PDO $db, array $usuario, int $pedidoId, float $valor, string $formaPagamento): array {
    $customerId = asaasGetOrCreateCustomer($db, $usuario);

    $billingType = match ($formaPagamento) {
        'pix'    => 'PIX',
        'boleto' => 'BOLETO',
        'cartao' => 'CREDIT_CARD',
        default  => 'UNDEFINED',
    };

    $cobranca = asaasRequest('POST', '/payments', [
        'customer'    => $customerId,
        'billingType' => $billingType,
        'value'       => round($valor, 2),
        'dueDate'     => date('Y-m-d', strtotime('+2 days')),
        'externalReference' => (string)$pedidoId,
    ]);

    $resultado = [
        'asaas_id'  => $cobranca['id'],
        'invoice_url' => $cobranca['invoiceUrl'] ?? null,
    ];

    if ($billingType === 'PIX') {
        $qr = asaasRequest('GET', '/payments/' . $cobranca['id'] . '/pixQrCode');
        $resultado['pix_code'] = $qr['payload'] ?? null;
        $resultado['pix_qr']   = isset($qr['encodedImage']) ? 'data:image/png;base64,' . $qr['encodedImage'] : null;
    } elseif ($billingType === 'BOLETO') {
        $resultado['boleto_barcode'] = $cobranca['identificationField'] ?? null;
        $resultado['boleto_pdf']     = $cobranca['bankSlipUrl'] ?? null;
    }

    $db->prepare('UPDATE pedidos SET asaas_id = ? WHERE id = ?')->execute([$cobranca['id'], $pedidoId]);

    return $resultado;
}
