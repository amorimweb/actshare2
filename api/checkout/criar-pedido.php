<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') methodNotAllowed();

$user = requireAuth();
$body = json_decode(file_get_contents('php://input'), true) ?? [];

$itens = $body['itens'] ?? []; // [ { curso_id, vagas, com_prova } ]
$cupomCodigo = trim($body['cupom_codigo'] ?? '');
$formaPagamento = trim($body['forma_pagamento'] ?? 'pix'); // pix, boleto, cartao

if (empty($itens)) {
    jsonError('O carrinho não pode estar vazio.', 400);
}

$db = getDB();

$totalBruto = 0.00;
$totalDesconto = 0.00;
$itensProcessados = [];

// 1. Processar cada item do carrinho
foreach ($itens as $item) {
    $cursoId   = (int)($item['curso_id']   ?? 0);
    $vagas     = (int)($item['vagas']      ?? 1);
    $comProva  = (int)($item['com_prova']  ?? 0);
    
    if ($vagas <= 0) $vagas = 1;
    
    // Busca dados do curso
    $stmt = $db->prepare('SELECT id, preco FROM cursos WHERE id = ? AND ativo = 1 LIMIT 1');
    $stmt->execute([$cursoId]);
    $curso = $stmt->fetch();
    
    if (!$curso) {
        jsonError('Um ou mais cursos selecionados não estão disponíveis.', 404);
    }
    
    $precoUnitario = (float)$curso['preco'];
    if ($comProva === 1) {
        $precoUnitario += 150.00;
    }
    $subtotalItem = $precoUnitario * $vagas;
    $totalBruto += $subtotalItem;
    
    // Aplica Desconto Progressivo por vagas (B2B)
    $descontoProgPercentual = 0;
    if ($vagas >= 2 && $vagas <= 5) {
        $descontoProgPercentual = 5;
    } elseif ($vagas >= 6 && $vagas <= 10) {
        $descontoProgPercentual = 10;
    } elseif ($vagas > 10) {
        $descontoProgPercentual = 15;
    }
    
    if ($descontoProgPercentual > 0) {
        $totalDesconto += $subtotalItem * ($descontoProgPercentual / 100);
    }
    
    $itensProcessados[] = [
        'curso_id'       => $cursoId,
        'vagas'          => $vagas,
        'preco_unitario' => $precoUnitario,
        'com_prova'      => $comProva
    ];
}

// 2. Desconto por Fidelidade (se ex-aluno com curso concluído)
$stmt = $db->prepare('SELECT COUNT(id) FROM matriculas WHERE aluno_id = ? AND concluido = 1');
$stmt->execute([$user['id']]);
$concluidos = (int)$stmt->fetchColumn();

if ($concluidos > 0) {
    // 10% de desconto adicional sobre o saldo acumulado
    $totalDesconto += ($totalBruto - $totalDesconto) * 0.10;
}

// 3. Processar cupom se fornecido
$cupomId = null;
$cupomIndicacaoId = null;

if ($cupomCodigo) {
    // Cupom padrão
    $stmt = $db->prepare('SELECT id, tipo, valor, validade, limite_uso, usos FROM cupons WHERE codigo = ? LIMIT 1');
    $stmt->execute([$cupomCodigo]);
    $cupom = $stmt->fetch();
    
    if ($cupom && strtotime($cupom['validade']) >= time() && ($cupom['limite_uso'] === null || $cupom['usos'] < $cupom['limite_uso'])) {
        $cupomId = (int)$cupom['id'];
        $saldoBruto = $totalBruto - $totalDesconto;
        
        if ($cupom['tipo'] === 'porcentagem') {
            $totalDesconto += $saldoBruto * ((float)$cupom['valor'] / 100);
        } else {
            $totalDesconto += (float)$cupom['valor'];
        }
    } else {
        // Cupom de indicação B2C
        $stmt = $db->prepare('SELECT id, percentual, validade, utilizado FROM cupons_indicacao WHERE codigo = ? LIMIT 1');
        $stmt->execute([$cupomCodigo]);
        $ref = $stmt->fetch();
        
        if ($ref && strtotime($ref['validade']) >= time() && $ref['utilizado'] == 0) {
            $cupomIndicacaoId = (int)$ref['id'];
            $saldoBruto = $totalBruto - $totalDesconto;
            $totalDesconto += $saldoBruto * ((float)$ref['percentual'] / 100);
        }
    }
}

$totalLiquido = max(0.00, $totalBruto - $totalDesconto);

// 4. Salvar pedido no banco
$db->beginTransaction();
try {
    $stmt = $db->prepare('
        INSERT INTO pedidos (usuario_id, total_bruto, desconto, total_liquido, cupom_id, situacao, asaas_id)
        VALUES (?, ?, ?, ?, ?, "pendente", "simulado")
    ');
    $stmt->execute([$user['id'], $totalBruto, $totalDesconto, $totalLiquido, $cupomId]);
    $pedidoId = (int)$db->lastInsertId();
    
    $stmtItem = $db->prepare('
        INSERT INTO itens_pedido (pedido_id, curso_id, vagas, preco_unitario, com_prova)
        VALUES (?, ?, ?, ?, ?)
    ');
    foreach ($itensProcessados as $it) {
        $stmtItem->execute([$pedidoId, $it['curso_id'], $it['vagas'], $it['preco_unitario'], $it['com_prova']]);
    }
    
    // Se usou cupom comum, incrementa usos
    if ($cupomId) {
        $db->exec("UPDATE cupons SET usos = usos + 1 WHERE id = $cupomId");
    }
    // Se usou cupom de indicação, marca como usado
    if ($cupomIndicacaoId) {
        $db->exec("UPDATE cupons_indicacao SET utilizado = 1 WHERE id = $cupomIndicacaoId");
    }
    
    $db->commit();
    
    // 5. Retorna dados simulados do pagamento
    $responsePayload = [
        'pedido_id'      => $pedidoId,
        'total_liquido'  => $totalLiquido,
        'forma_pagamento'=> $formaPagamento,
    ];
    
    if ($formaPagamento === 'pix') {
        $responsePayload['pix_code'] = '00020101021226830014br.gov.bcb.pix2561pix.example.com/qr/v2/cob-simulada-actshare-' . $pedidoId;
        $responsePayload['pix_qr'] = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($responsePayload['pix_code']);
    } elseif ($formaPagamento === 'boleto') {
        $responsePayload['boleto_barcode'] = '34191.79001 01043.513184 91020.150008 7 90000000000000';
        $responsePayload['boleto_pdf'] = '#';
    } else {
        // Cartão de Crédito -> Simula sucesso imediato
        $responsePayload['cartao_sucesso'] = true;
    }
    
    jsonOk($responsePayload);
    
} catch (Exception $e) {
    $db->rollBack();
    jsonError($e->getMessage(), 500);
}
