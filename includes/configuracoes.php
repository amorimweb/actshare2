<?php
/**
 * Configurações comerciais editáveis pelo admin (tabela chave/valor
 * `configuracoes`) — cupom de indicação, desconto por fidelidade e faixas
 * de desconto progressivo, hoje usados em checkout/criar-pedido.php e
 * checkout/simular-pagamento.php em vez de percentuais fixos no código.
 */
function getConfiguracoes(PDO $db): array {
    $padroes = [
        'cupom_indicacao_percentual'             => 10,
        'cupom_indicacao_validade_dias'          => 30,
        'desconto_fidelidade_percentual'         => 10,
        'desconto_progressivo_faixa1_min'        => 2,
        'desconto_progressivo_faixa1_max'        => 5,
        'desconto_progressivo_faixa1_percentual' => 5,
        'desconto_progressivo_faixa2_min'        => 6,
        'desconto_progressivo_faixa2_max'        => 10,
        'desconto_progressivo_faixa2_percentual' => 10,
        'desconto_progressivo_faixa3_min'        => 11,
        'desconto_progressivo_faixa3_percentual' => 15,
    ];

    $stmt = $db->query('SELECT chave, valor FROM configuracoes');
    foreach ($stmt->fetchAll() as $row) {
        $padroes[$row['chave']] = is_numeric($row['valor']) ? $row['valor'] + 0 : $row['valor'];
    }
    return $padroes;
}

function getDescontoProgressivoPercentual(array $config, int $vagas): float {
    if ($vagas >= $config['desconto_progressivo_faixa3_min']) {
        return (float)$config['desconto_progressivo_faixa3_percentual'];
    }
    if ($vagas >= $config['desconto_progressivo_faixa2_min'] && $vagas <= $config['desconto_progressivo_faixa2_max']) {
        return (float)$config['desconto_progressivo_faixa2_percentual'];
    }
    if ($vagas >= $config['desconto_progressivo_faixa1_min'] && $vagas <= $config['desconto_progressivo_faixa1_max']) {
        return (float)$config['desconto_progressivo_faixa1_percentual'];
    }
    return 0.0;
}
