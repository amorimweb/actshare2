<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/response.php';
require_once __DIR__ . '/../../includes/auth.php';

requireAdmin();

try {
    $db = getDB();
    $db->beginTransaction();

    echo "Iniciando seeding do ambiente de teste do Gestor...\n";

    // 1. Limpa registros anteriores para permitir execução repetida
    $emailsToClean = [
        'gestor.teste@actshare.com.br',
        'subgestor.teste@actshare.com.br',
        'aluno1@empresa.com',
        'aluno2@empresa.com'
    ];
    $placeholders = implode(',', array_fill(0, count($emailsToClean), '?'));
    $db->prepare("DELETE FROM usuarios WHERE email IN ($placeholders)")->execute($emailsToClean);

    // 2. Cria o Gestor Principal
    $senhaHash = password_hash('Teste123', PASSWORD_BCRYPT);
    $stmt = $db->prepare('
        INSERT INTO usuarios (nome, email, senha_hash, role, tipo_pessoa, documento, razao_social, cep, endereco, numero, complemento, bairro, cidade, estado)
        VALUES (?, ?, ?, "gestor", "juridica", "73.307.380/0001-85", "Sigrid Rut Sand Corp", "01001-000", "Praça da Sé", "100", "Sala 401", "Sé", "São Paulo", "SP")
    ');
    $stmt->execute(['Sigrid Rut Sand (Gestor 1)', 'gestor.teste@actshare.com.br', $senhaHash]);
    $gestorId = (int)$db->lastInsertId();

    // 3. Cria a Organização do Gestor
    $stmt = $db->prepare('INSERT INTO organizacoes (gestor_id, ativo, certificado_acesso) VALUES (?, 1, "ambos")');
    $stmt->execute([$gestorId]);
    $orgId = (int)$db->lastInsertId();

    // 4. Cria o Sub-Gestor
    $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, "gestor")');
    $stmt->execute(['Lucas Oliveira (Sub-Gestor)', 'subgestor.teste@actshare.com.br', $senhaHash]);
    $subGestorId = (int)$db->lastInsertId();
    
    $stmt = $db->prepare('INSERT INTO membros_organizacao (organizacao_id, usuario_id) VALUES (?, ?)');
    $stmt->execute([$orgId, $subGestorId]);

    // 5. Cria 2 Alunos (Funcionários)
    $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, "aluno")');
    $stmt->execute(['José da Silva', 'aluno1@empresa.com', $senhaHash]);
    $aluno1Id = (int)$db->lastInsertId();

    $stmt = $db->prepare('INSERT INTO usuarios (nome, email, senha_hash, role) VALUES (?, ?, ?, "aluno")');
    $stmt->execute(['Márcia Ribeiro', 'aluno2@empresa.com', $senhaHash]);
    $aluno2Id = (int)$db->lastInsertId();

    $stmt = $db->prepare('INSERT INTO membros_organizacao (organizacao_id, usuario_id) VALUES (?, ?)');
    $stmt->execute([$orgId, $aluno1Id]);
    $stmt->execute([$orgId, $aluno2Id]);

    // 6. Busca cursos disponíveis no banco para atrelar licenças
    $stmt = $db->query('SELECT id FROM cursos LIMIT 2');
    $cursos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($cursos) < 2) {
        // Fallback: se não tiver cursos cadastrados, cria dois cursos de teste
        $stmt = $db->prepare('INSERT INTO cursos (titulo, descricao, carga_horaria_horas, prazo_conclusao_dias, preco, ativo, publico) VALUES (?, ?, ?, ?, ?, 1, 1)');
        $stmt->execute(['ISO 14001:2026 - Interpretação da Norma', 'Treinamento completo de ISO 14001.', 40, 180, 299.90]);
        $curso1Id = (int)$db->lastInsertId();

        $stmt->execute(['ISO 9001:2015 - Sistema de Gestão da Qualidade', 'Requisitos da Qualidade.', 32, 180, 199.90]);
        $curso2Id = (int)$db->lastInsertId();
    } else {
        $curso1Id = (int)$cursos[0];
        $curso2Id = (int)$cursos[1];
    }

    // 7. Compra B2B para o Gestor (Matrículas B2B)
    $prazoAcesso = date('Y-m-d H:i:s', time() + (180 * 24 * 3600));

    // Curso 1: 5 vagas contratadas, 2 utilizadas
    $stmt = $db->prepare('
        INSERT INTO matriculas (aluno_id, curso_id, vagas_totais, vagas_usadas, participante, com_prova, data_fim_acesso)
        VALUES (?, ?, 5, 2, 0, 1, ?)
    ');
    $stmt->execute([$gestorId, $curso1Id, $prazoAcesso]);

    // Curso 2: 3 vagas contratadas, 0 utilizadas
    $stmt = $db->prepare('
        INSERT INTO matriculas (aluno_id, curso_id, vagas_totais, vagas_usadas, participante, com_prova, data_fim_acesso)
        VALUES (?, ?, 3, 0, 0, 0, ?)
    ');
    $stmt->execute([$gestorId, $curso2Id, $prazoAcesso]);

    // 8. Aloca os 2 Alunos no Curso 1 (Matrículas dos Alunos)
    // Aluno 1: Progresso 50%, concluído 0
    $stmt = $db->prepare('
        INSERT INTO matriculas (aluno_id, curso_id, progresso_total, concluido, com_prova, data_fim_acesso)
        VALUES (?, ?, 50, 0, 1, ?)
    ');
    $stmt->execute([$aluno1Id, $curso1Id, $prazoAcesso]);
    $mat1Id = (int)$db->lastInsertId();

    // Aluno 2: Progresso 0%, concluído 0
    $stmt = $db->prepare('
        INSERT INTO matriculas (aluno_id, curso_id, progresso_total, concluido, com_prova, data_fim_acesso)
        VALUES (?, ?, 0, 0, 1, ?)
    ');
    $stmt->execute([$aluno2Id, $curso1Id, $prazoAcesso]);

    // 9. Cria uma aula com e_prova = 1 vinculada ao Curso 1 (para podermos simular exames)
    // Verifica primeiro se há módulos
    $stmt = $db->prepare('SELECT id FROM modulos WHERE curso_id = ? LIMIT 1');
    $stmt->execute([$curso1Id]);
    $moduloId = $stmt->fetchColumn();

    if (!$moduloId) {
        $stmt = $db->prepare('INSERT INTO modulos (curso_id, titulo, ordem) VALUES (?, "Módulo Geral", 1)');
        $stmt->execute([$curso1Id]);
        $moduloId = (int)$db->lastInsertId();
    }

    $stmt = $db->prepare('INSERT INTO aulas (modulo_id, titulo, tipo, e_prova, ordem) VALUES (?, "Exame de Qualificação", "quiz", 1, 10)');
    $stmt->execute([$moduloId]);
    $aulaProvaId = (int)$db->lastInsertId();

    // 10. Insere tentativa de exame e resultados na tabela `avaliacao_tentativas` para o Aluno 1
    $gabaritoMock = [
        [
            'texto_pergunta' => 'Qual o principal foco da norma ISO 14001:2015?',
            'opcao_escolhida_id' => 101,
            'opcao_correta_id' => 101,
            'texto_correta' => 'Sistema de Gestão Ambiental',
            'acertou' => true,
            'justificativa' => 'A norma ISO 14001 é o padrão internacional para implementação de um SGA eficaz.'
        ],
        [
            'texto_pergunta' => 'Auditorias internas devem ser realizadas em quais intervalos?',
            'opcao_escolhida_id' => 202,
            'opcao_correta_id' => 201,
            'texto_correta' => 'Intervalos planejados e periódicos',
            'acertou' => false,
            'justificativa' => 'Conforme requisito 9.2, auditorias devem ser feitas em intervalos planejados.'
        ]
    ];
    $gabaritoJson = json_encode($gabaritoMock, JSON_UNESCAPED_UNICODE);

    // Tentativa 1 (Reprovado)
    $stmt = $db->prepare('
        INSERT INTO avaliacao_tentativas (matricula_id, aula_id, total_questoes, acertos, erros, nao_respondidas, nota, resultado, respostas_json, created_at)
        VALUES (?, ?, 2, 1, 1, 0, 50, "reprovado", ?, SUBDATE(NOW(), INTERVAL 1 HOUR))
    ');
    $stmt->execute([$mat1Id, $aulaProvaId, $gabaritoJson]);

    // Registra no quiz_resposta (resumo de tentativas)
    $stmt = $db->prepare('
        INSERT INTO quiz_resposta (matricula_id, aula_id, nota, aprovado, acertos, total_perguntas, tentativas_restantes)
        VALUES (?, ?, 50, 0, 1, 2, 4)
    ');
    $stmt->execute([$mat1Id, $aulaProvaId]);

    $db->commit();
    echo "Ambiente de teste do Gestor semeado com sucesso!\n";

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "Erro ao semear banco: " . $e->getMessage() . "\n";
}
