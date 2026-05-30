-- ============================================================
-- ActShare EAD — Cursos, Quizzes e Provas de Demonstração
-- Execute no phpMyAdmin ou terminal para inserir dados de testes
-- ============================================================

SET NAMES utf8mb4;

-- 1. Insere o curso (Pago: R$ 499,00)
INSERT INTO `cursos` (`titulo`, `descricao`, `thumb_url`, `ativo`, `publico`, `categoria_id`, `instrutor_id`, `preco`, `carga_horaria_horas`, `prazo_conclusao_dias`) VALUES
('Certificação Auditor Líder ISO 27001 (Exemplar Global)', 'Curso preparatório completo voltado para a auditoria de sistemas de gestão de segurança da informação (SGSI) com exames simulados e prova de certificação monitorada de acordo com as regras de conformidade internacional.', 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600&q=80', 1, 1, 2, 2, 499.00, 40, 180);

SET @curso_id = LAST_INSERT_ID();

-- 2. Insere o módulo
INSERT INTO `modulos` (`curso_id`, `titulo`, `ordem`) VALUES
(@curso_id, 'Diretrizes de Auditoria e Conformidade', 1);

SET @modulo_id = LAST_INSERT_ID();

-- 3. Insere a aula de vídeo
INSERT INTO `aulas` (`modulo_id`, `titulo`, `tipo`, `video_url`, `ordem`, `duracao_min`) VALUES
(@modulo_id, 'Conceitos Iniciais da ISO 27001', 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 1, 20);

-- 4. Insere a aula de Quiz de Fixação (e_prova = 0)
INSERT INTO `aulas` (`modulo_id`, `titulo`, `tipo`, `video_url`, `ordem`, `duracao_min`, `quizz_qtd_perguntas`, `e_prova`) VALUES
(@modulo_id, 'Quiz de Fixação — Auditoria ISO 27001', 'quiz', NULL, 2, 0, 2, 0);

SET @aula_quiz_id = LAST_INSERT_ID();

-- 5. Insere a aula de Prova Final (e_prova = 1)
INSERT INTO `aulas` (`modulo_id`, `titulo`, `tipo`, `video_url`, `ordem`, `duracao_min`, `quizz_qtd_perguntas`, `e_prova`) VALUES
(@modulo_id, 'Avaliação Final de Certificação (Exame Monitorado)', 'quiz', NULL, 3, 0, 3, 1);

SET @aula_prova_id = LAST_INSERT_ID();

-- 6. Insere perguntas para o Quiz de Fixação
INSERT INTO `perguntas` (`aula_id`, `texto`, `justificativa`) VALUES
(@aula_quiz_id, 'Qual das seguintes alternativas descreve melhor o principal papel do Auditor Líder?', 'O auditor líder é responsável por planejar, coordenar e dirigir a equipe auditora, além de consolidar o relatório final.'),
(@aula_quiz_id, 'Qual a duração recomendada padrão de uma auditoria de Fase 1?', 'A auditoria de fase 1 é focada em revisão documental do sistema e costuma levar de 1 a 2 dias.');

-- Pega IDs das perguntas do quiz
-- Como inserimos 2 registros, podemos pegar o id do primeiro e calcular o próximo
SET @perg_quiz1_id = LAST_INSERT_ID();
SET @perg_quiz2_id = @perg_quiz1_id + 1;

-- Opções para pergunta 1 do quiz
INSERT INTO `opcoes` (`pergunta_id`, `texto`, `correta`) VALUES
(@perg_quiz1_id, 'O funcionário que executa os backups diários da segurança.', 0),
(@perg_quiz1_id, 'A pessoa responsável por gerenciar e liderar a equipe de auditoria.', 1),
(@perg_quiz1_id, 'O diretor executivo da empresa parceira externa.', 0),
(@perg_quiz1_id, 'Nenhuma das anteriores.', 0);

-- Opções para pergunta 2 do quiz
INSERT INTO `opcoes` (`pergunta_id`, `texto`, `correta`) VALUES
(@perg_quiz2_id, '1 a 2 dias dependendo do porte da organização.', 1),
(@perg_quiz2_id, '6 meses ininterruptos de análise profunda.', 0),
(@perg_quiz2_id, 'Apenas 10 minutos de conferência rápida.', 0),
(@perg_quiz2_id, 'Todas as alternativas anteriores estão corretas.', 0);


-- 7. Insere perguntas para a Prova Final (Exame Monitorado)
INSERT INTO `perguntas` (`aula_id`, `texto`, `justificativa`) VALUES
(@aula_prova_id, 'O que constitui um controle de segurança da informação segundo a ISO 27001?', 'Controles de segurança da informação são práticas, políticas ou mecanismos técnicos ou físicos para gerenciar e reduzir riscos a níveis aceitáveis.'),
(@aula_prova_id, 'Qual é a frequência de realização correta de auditorias internas de conformidade em uma organização certificada?', 'A norma exige que auditorias internas ocorram em intervalos planejados para verificar a conformidade do SGSI.'),
(@aula_prova_id, 'Qual das opções abaixo caracteriza uma "Não Conformidade Maior" em auditorias ISO?', 'A Não Conformidade Maior é configurada quando há ausência total ou falha sistemática na aplicação de um requisito mandatório da norma.');

-- Pega IDs das perguntas da prova
SET @perg_prova1_id = LAST_INSERT_ID();
SET @perg_prova2_id = @perg_prova1_id + 1;
SET @perg_prova3_id = @perg_prova1_id + 2;

-- Opções para pergunta 1 da prova
INSERT INTO `opcoes` (`pergunta_id`, `texto`, `correta`) VALUES
(@perg_prova1_id, 'Uma medida ou prática adotada para mitigar e gerenciar riscos de segurança.', 1),
(@perg_prova1_id, 'Uma fechadura eletrônica instalada na porta principal de TI.', 0),
(@perg_prova1_id, 'Um software antivírus instalado nos notebooks da administração.', 0),
(@perg_prova1_id, 'Todas as alternativas anteriores estão corretas.', 0);

-- Opções para pergunta 2 da prova
INSERT INTO `opcoes` (`pergunta_id`, `texto`, `correta`) VALUES
(@perg_prova2_id, 'Em intervalos planejados e periódicos definidos pela organização.', 1),
(@perg_prova2_id, 'Somente após a ocorrência de incidentes ou vazamentos de dados graves.', 0),
(@perg_prova2_id, 'Uma vez a cada 10 anos para revalidação do SGSI.', 0),
(@perg_prova2_id, 'Nenhuma das alternativas anteriores.', 0);

-- Opções para pergunta 3 da prova
INSERT INTO `opcoes` (`pergunta_id`, `texto`, `correta`) VALUES
(@perg_prova3_id, 'A ausência total ou falha sistemática de aplicação de um requisito obrigatório da norma.', 1),
(@perg_prova3_id, 'Um erro de digitação simples em um cabeçalho do manual de segurança.', 0),
(@perg_prova3_id, 'Deixar a tela do computador desbloqueada em uma única ausência rápida.', 0),
(@perg_prova3_id, 'Nenhuma das anteriores.', 0);
