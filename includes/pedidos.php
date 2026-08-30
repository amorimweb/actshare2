<?php
require_once __DIR__ . '/configuracoes.php';

/**
 * Marca um pedido como pago e libera as matrículas correspondentes aos itens
 * do carrinho (fluxo B2C individual ou B2B por vagas), gera o cupom de
 * indicação do comprador e promove a role para 'gestor' quando aplicável.
 *
 * Extraído de api/checkout/simular-pagamento.php para ser reutilizado tanto
 * pelo botão de simulação (sem credenciais reais do ASAAS) quanto pelo
 * webhook de confirmação de pagamento real (api/checkout/asaas-webhook.php).
 */
function liberarMatriculasDoPedido(PDO $db, int $pedidoId): array {
    $stmt = $db->prepare('SELECT * FROM pedidos WHERE id = ? LIMIT 1');
    $stmt->execute([$pedidoId]);
    $pedido = $stmt->fetch();
    if (!$pedido) throw new Exception('Pedido não encontrado.');

    if ($pedido['situacao'] === 'pago') {
        return ['already_paid' => true];
    }

    $config = getConfiguracoes($db);

    $db->beginTransaction();
    try {
        $db->prepare('UPDATE pedidos SET situacao = "pago" WHERE id = ?')->execute([$pedidoId]);

        // PJ sempre vira Gestor ao comprar, mesmo comprando 1 única vaga
        // (PF só vira Gestor quando compra 2+ vagas do mesmo produto — ver
        // abaixo, no loop por item).
        $stmt = $db->prepare('SELECT tipo_pessoa FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([$pedido['usuario_id']]);
        $compradorTipoPessoa = $stmt->fetchColumn();
        if ($compradorTipoPessoa === 'juridica') {
            $db->prepare('UPDATE usuarios SET role = "gestor" WHERE id = ? AND role = "aluno"')
               ->execute([$pedido['usuario_id']]);
        }

        $stmt = $db->prepare('SELECT * FROM itens_pedido WHERE pedido_id = ?');
        $stmt->execute([$pedidoId]);
        $itens = $stmt->fetchAll();

        // Expande combos em uma matrícula por curso contido (Lote F). Todo
        // produto do combo herda a MESMA validade do combo (prazo_validade_dias),
        // em vez do prazo individual de cada curso — pedido explícito do cliente.
        $itensExpandidos = [];
        foreach ($itens as $item) {
            if (!empty($item['combo_id'])) {
                $stmtCombo = $db->prepare('SELECT prazo_validade_dias FROM combos WHERE id = ? LIMIT 1');
                $stmtCombo->execute([$item['combo_id']]);
                $comboPrazoDias = (int)($stmtCombo->fetchColumn() ?: 0) ?: null;

                $stmtItensCombo = $db->prepare('SELECT curso_id FROM combo_itens WHERE combo_id = ? ORDER BY ordem');
                $stmtItensCombo->execute([$item['combo_id']]);
                foreach ($stmtItensCombo->fetchAll() as $ci) {
                    $itensExpandidos[] = [
                        'curso_id' => (int)$ci['curso_id'], 'vagas' => $item['vagas'], 'com_prova' => $item['com_prova'],
                        'exames_selecionados' => $item['exames_selecionados'] ?? null, 'prazo_dias_override' => $comboPrazoDias,
                    ];
                }
            } else {
                $itensExpandidos[] = $item;
            }
        }

        foreach ($itensExpandidos as $item) {
            $cursoId = (int)$item['curso_id'];
            $vagas   = (int)$item['vagas'];

            if (!empty($item['prazo_dias_override'])) {
                $prazoDias = (int)$item['prazo_dias_override'];
            } else {
                $stmtC = $db->prepare('SELECT prazo_conclusao_dias FROM cursos WHERE id = ? LIMIT 1');
                $stmtC->execute([$cursoId]);
                $curso = $stmtC->fetch();
                $prazoDias = $curso ? (int)$curso['prazo_conclusao_dias'] : 180;
                if (!$prazoDias) $prazoDias = 180;
            }

            $dataFimAcesso = date('Y-m-d H:i:s', time() + ($prazoDias * 24 * 3600));
            $comProva = (int)$item['com_prova'];
            $examesSel = $item['exames_selecionados'] ?? null;

            if ($vagas === 1) {
                $stmtM = $db->prepare('
                    INSERT INTO matriculas (aluno_id, curso_id, data_fim_acesso, vagas_usadas, vagas_totais, participante, com_prova, exames_selecionados)
                    VALUES (?, ?, ?, 1, NULL, 0, ?, ?)
                    ON DUPLICATE KEY UPDATE data_fim_acesso = VALUES(data_fim_acesso), com_prova = VALUES(com_prova), exames_selecionados = VALUES(exames_selecionados)
                ');
                $stmtM->execute([$pedido['usuario_id'], $cursoId, $dataFimAcesso, $comProva, $examesSel]);

                // Cupom de indicação B2C — % e prazo configuráveis pelo admin
                $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $codigoRef = 'REF-' . substr(str_shuffle($caracteres), 0, 6);
                $validadeCupom = date('Y-m-d H:i:s', time() + ((int)$config['cupom_indicacao_validade_dias'] * 24 * 3600));

                $stmtR = $db->prepare('
                    INSERT INTO cupons_indicacao (indicador_id, codigo, percentual, validade, utilizado)
                    VALUES (?, ?, ?, ?, 0)
                ');
                $stmtR->execute([$pedido['usuario_id'], $codigoRef, (int)$config['cupom_indicacao_percentual'], $validadeCupom]);
            } else {
                $stmtM = $db->prepare('
                    INSERT INTO matriculas (aluno_id, curso_id, data_fim_acesso, vagas_usadas, vagas_totais, participante, com_prova, exames_selecionados)
                    VALUES (?, ?, ?, 0, ?, 0, ?, ?)
                    ON DUPLICATE KEY UPDATE vagas_totais = VALUES(vagas_totais), data_fim_acesso = VALUES(data_fim_acesso), com_prova = VALUES(com_prova), exames_selecionados = VALUES(exames_selecionados)
                ');
                $stmtM->execute([$pedido['usuario_id'], $cursoId, $dataFimAcesso, $vagas, $comProva, $examesSel]);

                $db->prepare('UPDATE usuarios SET role = "gestor" WHERE id = ? AND role = "aluno"')
                   ->execute([$pedido['usuario_id']]);
            }
        }

        $db->commit();

        $stmt = $db->prepare('SELECT id, nome, email, role FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([$pedido['usuario_id']]);
        $updatedUser = $stmt->fetch();

        require_once __DIR__ . '/email_templates.php';
        enviarEmailTemplate($db, 'pagamento_confirmado', $updatedUser['email'], [
            'nome' => $updatedUser['nome'],
            'pedido_id' => $pedidoId,
        ]);

        return ['already_paid' => false, 'updated_user' => $updatedUser];
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}
