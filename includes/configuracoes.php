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
        'desconto_progressivo_faixa3_max'        => 20,
        'desconto_progressivo_faixa3_percentual' => 10,
        'desconto_progressivo_faixa4_min'        => 21,
        'desconto_progressivo_faixa4_max'        => 30,
        'desconto_progressivo_faixa4_percentual' => 15,
        'desconto_progressivo_faixa5_min'        => 31,
        'desconto_progressivo_faixa5_max'        => 40,
        'desconto_progressivo_faixa5_percentual' => 20,
        'desconto_progressivo_faixa6_min'        => 41,
        'desconto_progressivo_faixa6_max'        => 70,
        'desconto_progressivo_faixa6_percentual' => 25,
        'desconto_progressivo_faixa7_min'        => 71,
        'desconto_progressivo_faixa7_max'        => 100,
        'desconto_progressivo_faixa7_percentual' => 30,
        'desconto_progressivo_faixa8_min'        => 101,
        'desconto_progressivo_faixa8_percentual' => 40,
    ];

    $stmt = $db->query('SELECT chave, valor FROM configuracoes');
    foreach ($stmt->fetchAll() as $row) {
        $padroes[$row['chave']] = is_numeric($row['valor']) ? $row['valor'] + 0 : $row['valor'];
    }
    return $padroes;
}

function getDescontoProgressivoPercentual(array $config, int $vagas): float {
    // Percorre da faixa mais alta pra mais baixa; a última faixa (8) não tem
    // "max" — é "acima de X".
    for ($faixa = 8; $faixa >= 1; $faixa--) {
        $min = $config["desconto_progressivo_faixa{$faixa}_min"] ?? null;
        if ($min === null) continue;
        $max = $config["desconto_progressivo_faixa{$faixa}_max"] ?? null;
        if ($vagas >= $min && ($max === null || $vagas <= $max)) {
            return (float)$config["desconto_progressivo_faixa{$faixa}_percentual"];
        }
    }
    return 0.0;
}

/** Lista as 8 faixas configuradas, em ordem, pra exibir no Admin. */
function getFaixasDescontoProgressivo(array $config): array {
    $faixas = [];
    for ($i = 1; $i <= 8; $i++) {
        $faixas[] = [
            'faixa'       => $i,
            'min'         => (int)($config["desconto_progressivo_faixa{$i}_min"] ?? 0),
            'max'         => isset($config["desconto_progressivo_faixa{$i}_max"]) ? (int)$config["desconto_progressivo_faixa{$i}_max"] : null,
            'percentual'  => (float)($config["desconto_progressivo_faixa{$i}_percentual"] ?? 0),
        ];
    }
    return $faixas;
}
