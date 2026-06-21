-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/06/2026 às 14:21
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `actshare`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `aulas`
--

CREATE TABLE `aulas` (
  `id` int(10) UNSIGNED NOT NULL,
  `modulo_id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('video','texto','quiz','pdf') NOT NULL DEFAULT 'video',
  `video_url` varchar(500) DEFAULT NULL,
  `publica` tinyint(1) NOT NULL DEFAULT 0,
  `ordem` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `duracao_min` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `quizz_qtd_perguntas` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `e_prova` tinyint(1) NOT NULL DEFAULT 0,
  `exemplar_global` tinyint(1) NOT NULL DEFAULT 0,
  `nota_corte_tipo` enum('questoes','percentual') NOT NULL DEFAULT 'percentual',
  `nota_corte_valor` int(10) UNSIGNED NOT NULL DEFAULT 70,
  `tempo_limite_minutos` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `bloquear_proctoring` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `aulas`
--

INSERT INTO `aulas` (`id`, `modulo_id`, `titulo`, `descricao`, `tipo`, `video_url`, `publica`, `ordem`, `duracao_min`, `quizz_qtd_perguntas`, `e_prova`, `exemplar_global`, `nota_corte_tipo`, `nota_corte_valor`, `tempo_limite_minutos`, `bloquear_proctoring`, `created_at`) VALUES
(1, 1, 'O que é um projeto?', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 1, 12, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(2, 1, 'Ciclo de vida de um projeto', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 2, 18, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(3, 1, 'O papel do gerente de projetos', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 3, 15, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(4, 1, 'Quiz — Fundamentos', NULL, 'quiz', NULL, 0, 4, 0, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(5, 2, 'Manifesto Ágil e seus princípios', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 1, 20, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(6, 2, 'Scrum: papéis, eventos e artefatos', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 2, 25, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(7, 2, 'Kanban na prática', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 3, 18, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(8, 2, 'Quiz — Metodologias Ágeis', NULL, 'quiz', NULL, 0, 4, 0, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(9, 5, 'O que é a LGPD e por que ela existe?', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 1, 10, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(10, 5, 'Conceitos fundamentais: dado, tratamento…', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 2, 14, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(11, 5, 'Bases legais para tratamento de dados', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 3, 16, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(12, 5, 'Quiz — Introdução à LGPD', NULL, 'quiz', NULL, 0, 4, 0, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(13, 6, 'Direitos dos titulares de dados', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 1, 12, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(14, 6, 'Como responder às solicitações dos titulares', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 2, 15, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(15, 6, 'Quiz — Direitos dos Titulares', NULL, 'quiz', NULL, 0, 3, 0, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(16, 9, 'Instalação e configuração do ambiente', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 1, 15, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(17, 9, 'Variáveis, tipos e operadores', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 2, 20, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(18, 9, 'Condicionais e loops', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 3, 22, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(19, 9, 'Funções e módulos', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 4, 25, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(20, 9, 'Quiz — Python Básico', NULL, 'quiz', NULL, 0, 5, 0, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-05 22:35:34'),
(21, 20, 'Conceitos Iniciais da ISO 27001', NULL, 'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 0, 1, 20, 1, 0, 0, 'percentual', 70, 0, 0, '2026-05-27 10:41:57'),
(22, 20, 'Quiz de Fixação — Auditoria ISO 27001', NULL, 'quiz', NULL, 0, 2, 0, 2, 0, 0, 'percentual', 70, 0, 0, '2026-05-27 10:41:57'),
(23, 20, 'Avaliação Final de Certificação (Exame Monitorado)', NULL, 'quiz', NULL, 0, 3, 0, 3, 1, 0, 'percentual', 70, 0, 0, '2026-05-27 10:41:57'),
(24, 1, 'Exame de Qualificação', NULL, 'quiz', NULL, 0, 10, 0, 1, 1, 0, 'percentual', 70, 0, 0, '2026-06-17 01:50:32'),
(25, 1, 'Exame de Qualificação', NULL, 'quiz', NULL, 0, 10, 0, 1, 1, 0, 'percentual', 70, 0, 0, '2026-06-17 23:10:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacao_tentativas`
--

CREATE TABLE `avaliacao_tentativas` (
  `id` int(10) UNSIGNED NOT NULL,
  `matricula_id` int(10) UNSIGNED NOT NULL,
  `aula_id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED DEFAULT NULL,
  `total_questoes` int(10) UNSIGNED NOT NULL,
  `acertos` int(10) UNSIGNED NOT NULL,
  `erros` int(10) UNSIGNED NOT NULL,
  `nao_respondidas` int(10) UNSIGNED NOT NULL,
  `nota` int(10) UNSIGNED NOT NULL,
  `resultado` enum('aprovado','reprovado') NOT NULL,
  `respostas_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`respostas_json`)),
  `codigo_certificado` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `avaliacao_tentativas`
--

INSERT INTO `avaliacao_tentativas` (`id`, `matricula_id`, `aula_id`, `pedido_id`, `total_questoes`, `acertos`, `erros`, `nao_respondidas`, `nota`, `resultado`, `respostas_json`, `codigo_certificado`, `created_at`) VALUES
(2, 14, 25, NULL, 2, 1, 1, 0, 50, 'reprovado', '[{\"texto_pergunta\":\"Qual o principal foco da norma ISO 14001:2015?\",\"opcao_escolhida_id\":101,\"opcao_correta_id\":101,\"texto_correta\":\"Sistema de Gestão Ambiental\",\"acertou\":true,\"justificativa\":\"A norma ISO 14001 é o padrão internacional para implementação de um SGA eficaz.\"},{\"texto_pergunta\":\"Auditorias internas devem ser realizadas em quais intervalos?\",\"opcao_escolhida_id\":202,\"opcao_correta_id\":201,\"texto_correta\":\"Intervalos planejados e periódicos\",\"acertou\":false,\"justificativa\":\"Conforme requisito 9.2, auditorias devem ser feitas em intervalos planejados.\"}]', NULL, '2026-06-17 22:10:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `slug`, `icone`, `created_at`) VALUES
(1, 'Gestão', 'gestao', NULL, '2026-05-06 01:14:24'),
(2, 'Compliance', 'compliance', NULL, '2026-05-06 01:14:24'),
(3, 'Tecnologia', 'tecnologia', NULL, '2026-05-06 01:14:24'),
(4, 'Soft Skills', 'soft-skills', NULL, '2026-05-06 01:14:24'),
(5, 'Jurídico', 'juridico', NULL, '2026-05-06 01:14:24'),
(6, 'Interpretação das Normas', 'interpretacao-das-normas', NULL, '2026-05-30 12:08:35'),
(7, 'Auditor Interno', 'auditor-interno', NULL, '2026-05-30 12:08:35'),
(8, 'Auditor Líder', 'auditor-lider', NULL, '2026-05-30 12:08:35'),
(9, 'Automotivo', 'automotivo', NULL, '2026-05-30 12:08:35'),
(10, 'Segurança da Informação', 'seguranca-da-informacao', NULL, '2026-05-30 12:08:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `certificados_manuais`
--

CREATE TABLE `certificados_manuais` (
  `id` int(10) UNSIGNED NOT NULL,
  `cliente_nome` varchar(250) NOT NULL,
  `curso_nome` varchar(250) NOT NULL,
  `carga_horaria` int(10) UNSIGNED NOT NULL,
  `data_conclusao` date NOT NULL,
  `tipo_texto` enum('participacao','aprovacao') NOT NULL DEFAULT 'participacao',
  `instrutor_nome` varchar(250) NOT NULL,
  `assinatura_url` varchar(500) NOT NULL,
  `codigo_autenticidade` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupons`
--

CREATE TABLE `cupons` (
  `id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `tipo` enum('fixo','porcentagem') NOT NULL DEFAULT 'porcentagem',
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `validade` datetime NOT NULL,
  `limite_uso` int(10) UNSIGNED DEFAULT NULL,
  `usos` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupons_indicacao`
--

CREATE TABLE `cupons_indicacao` (
  `id` int(10) UNSIGNED NOT NULL,
  `indicador_id` int(10) UNSIGNED NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `percentual` int(10) UNSIGNED NOT NULL DEFAULT 10,
  `validade` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `cupons_indicacao`
--

INSERT INTO `cupons_indicacao` (`id`, `indicador_id`, `codigo`, `percentual`, `validade`, `utilizado`, `created_at`) VALUES
(1, 6, 'REF-JT571X', 10, '2026-06-26 15:58:19', 0, '2026-05-27 10:58:19'),
(2, 7, 'REF-C4F3IE', 10, '2026-06-26 16:47:58', 0, '2026-05-27 11:47:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cursos`
--

CREATE TABLE `cursos` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `nome_certificado` varchar(250) DEFAULT NULL,
  `codigo` varchar(10) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `thumb_url` varchar(500) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `disponivel_loja` tinyint(1) NOT NULL DEFAULT 1,
  `certificado_template_url` varchar(500) DEFAULT NULL,
  `certificado_config` text DEFAULT NULL,
  `certificado_liberacao` enum('ambos','empresa','aluno') DEFAULT 'ambos',
  `publico` tinyint(1) NOT NULL DEFAULT 0,
  `categoria_id` int(10) UNSIGNED DEFAULT NULL,
  `instrutor_id` int(10) UNSIGNED DEFAULT NULL,
  `exibir_instrutor` tinyint(1) NOT NULL DEFAULT 0,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  `carga_horaria_horas` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `prazo_acesso_dias` int(10) UNSIGNED DEFAULT NULL,
  `prazo_conclusao_dias` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `cursos`
--

INSERT INTO `cursos` (`id`, `titulo`, `nome_certificado`, `codigo`, `descricao`, `thumb_url`, `ativo`, `disponivel_loja`, `certificado_template_url`, `certificado_config`, `certificado_liberacao`, `publico`, `categoria_id`, `instrutor_id`, `exibir_instrutor`, `preco`, `carga_horaria_horas`, `prazo_acesso_dias`, `prazo_conclusao_dias`, `created_at`, `updated_at`) VALUES
(1, 'Gestão de Projetos na Prática', NULL, NULL, 'Aprenda a planejar, executar e controlar projetos com metodologias ágeis e tradicionais. Domine ferramentas como Kanban, Scrum e PMBOK aplicados ao dia a dia corporativo.', 'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=600&q=80', 1, 1, NULL, NULL, 'ambos', 1, 1, 1, 0, 0.00, 20, NULL, NULL, '2026-05-05 22:35:34', '2026-05-05 22:35:34'),
(2, 'LGPD na Prática: Proteja sua Empresa', NULL, NULL, 'Entenda todos os aspectos da Lei Geral de Proteção de Dados. Do conceito à implementação, capacite sua equipe para evitar multas e garantir conformidade.', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&q=80', 1, 1, NULL, NULL, 'ambos', 1, 2, 2, 0, 0.00, 8, NULL, NULL, '2026-05-05 22:35:34', '2026-05-05 22:35:34'),
(3, 'Python para Automação de Processos', NULL, NULL, 'Do zero ao automatizador: aprenda Python focado em automação de tarefas repetitivas, relatórios automáticos e integração de sistemas.', 'https://images.unsplash.com/photo-1526379095098-d400fd0bf935?w=600&q=80', 1, 1, NULL, NULL, 'ambos', 1, 3, 3, 0, 0.00, 24, NULL, NULL, '2026-05-05 22:35:34', '2026-05-05 22:35:34'),
(4, 'Liderança e Comunicação Assertiva', NULL, NULL, 'Desenvolva habilidades de liderança, comunicação e gestão de pessoas. Aprenda a engajar equipes, dar feedbacks eficazes e resolver conflitos.', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&q=80', 1, 1, NULL, NULL, 'ambos', 1, 4, 4, 0, 0.00, 12, NULL, NULL, '2026-05-05 22:35:34', '2026-05-05 22:35:34'),
(5, 'Compliance e Ética nos Negócios', NULL, NULL, 'Estruture um programa de compliance robusto. Políticas anticorrupção, código de conduta, canal de denúncias e gestão de riscos.', 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&q=80', 1, 1, NULL, NULL, 'ambos', 1, 2, 2, 0, 0.00, 16, NULL, NULL, '2026-05-05 22:35:34', '2026-05-05 22:35:34'),
(6, 'Excel Avançado para Gestores', NULL, NULL, 'Domine as funções avançadas do Excel: tabelas dinâmicas, Power Query, dashboards e automação com macros VBA voltados para tomada de decisão.', 'https://images.unsplash.com/photo-1543286386-2e659306cd6c?w=600&q=80', 1, 1, NULL, NULL, 'ambos', 1, 1, 1, 0, 0.00, 10, NULL, NULL, '2026-05-05 22:35:34', '2026-05-05 22:35:34'),
(7, 'Segurança do Trabalho — NR Essenciais', NULL, NULL, 'Normas Regulamentadoras obrigatórias: NR-1, NR-6, NR-17 e NR-35. Capacite sua equipe e mantenha sua empresa em conformidade legal.', 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&q=80', 1, 1, NULL, NULL, 'ambos', 1, 5, 5, 0, 0.00, 8, NULL, NULL, '2026-05-05 22:35:34', '2026-05-05 22:35:34'),
(8, 'Inteligência Artificial no Trabalho', NULL, NULL, 'Entenda como usar ferramentas de IA (ChatGPT, Copilot, Gemini) para aumentar sua produtividade, criar conteúdos e automatizar tarefas do dia a dia.', 'https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=600&q=80', 1, 1, NULL, NULL, 'ambos', 1, 3, 3, 0, 0.00, 6, NULL, NULL, '2026-05-05 22:35:34', '2026-05-05 22:35:34'),
(9, 'Certificação Auditor Líder ISO 27001 (Exemplar Global)', NULL, NULL, 'Curso preparatório completo voltado para a auditoria de sistemas de gestão de segurança da informação (SGSI) com exames simulados e prova de certificação monitorada de acordo com as regras de conformidade internacional.', 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600&q=80', 1, 1, NULL, NULL, 'ambos', 1, 2, 2, 0, 499.00, 40, NULL, 180, '2026-05-27 10:41:57', '2026-05-27 10:41:57'),
(10, 'ISO 14001:2026 - Interpretação da Norma', NULL, NULL, 'Interpretação aplicada dos requisitos da ISO 14001:2026 para sistemas de gestão ambiental, com foco em implementação, evidências e conformidade.', 'https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 6, 6, 0, 297.00, 16, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(11, 'ISO 9001:2015 - Interpretação da Norma', NULL, NULL, 'Curso para compreender os requisitos da ISO 9001:2015 e sua aplicação em processos, indicadores, riscos, oportunidades e melhoria contínua.', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 6, 6, 0, 297.00, 16, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(12, 'ISO 45001:2018 - Interpretação da Norma', NULL, NULL, 'Estudo dos requisitos de saúde e segurança ocupacional da ISO 45001:2018, incluindo contexto, liderança, planejamento, operação e avaliação.', 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 6, 6, 0, 297.00, 16, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(13, 'ISO/IEC 17025 - Interpretação da Norma', NULL, NULL, 'Treinamento sobre os requisitos gerais para competência de laboratórios de ensaio e calibração conforme ISO/IEC 17025.', 'https://images.unsplash.com/photo-1581093458791-9d09f535c4a3?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 6, 6, 0, 297.00, 16, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(14, 'ISO 14001:2026 - Auditor Interno 1ª e 2ª Parte', NULL, NULL, 'Formação de auditor interno para auditorias de primeira e segunda parte em sistemas de gestão ambiental baseados na ISO 14001:2026.', 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 7, 6, 0, 397.00, 24, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(15, 'ISO 9001:2015 - Auditor Interno 1ª e 2ª Parte', NULL, NULL, 'Capacitação para planejar, executar, relatar e acompanhar auditorias internas e de fornecedores conforme ISO 9001:2015.', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 7, 6, 0, 397.00, 24, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(16, 'ISO 45001:2018 - Auditor Interno 1ª e 2ª Parte', NULL, NULL, 'Curso de auditor interno para sistemas de gestão de saúde e segurança ocupacional, com práticas de auditoria e relatórios.', 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 7, 6, 0, 397.00, 24, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(17, 'ISO 14001:2026 - Auditor Líder Exemplar Global', NULL, NULL, 'Preparação para atuação como auditor líder em sistemas de gestão ambiental, alinhada às competências exigidas por organismos internacionais.', 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 8, 6, 0, 699.00, 40, NULL, 180, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(18, 'ISO 9001:2015 - Auditor Líder Exemplar Global', NULL, NULL, 'Formação avançada de auditor líder para sistemas de gestão da qualidade, com abordagem de planejamento, condução e fechamento de auditorias.', 'https://images.unsplash.com/photo-1560264280-88b68371db39?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 8, 6, 0, 699.00, 40, NULL, 180, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(19, 'ISO 45001:2018 - Auditor Líder Exemplar Global', NULL, NULL, 'Capacitação para liderar auditorias de saúde e segurança ocupacional conforme ISO 45001:2018 e boas práticas internacionais.', 'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 8, 6, 0, 699.00, 40, NULL, 180, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(20, 'IATF 16949 - Interpretação da Norma', NULL, NULL, 'Interpretação dos requisitos da IATF 16949 para cadeia automotiva, incluindo requisitos específicos de clientes e gestão de processos.', 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 9, 6, 0, 497.00, 24, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(21, 'APQP 3ª Edição', NULL, NULL, 'Planejamento avançado da qualidade do produto com foco em desenvolvimento, validação, riscos, documentação e entregáveis do APQP.', 'https://images.unsplash.com/photo-1581090700227-1e37b190418e?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 9, 6, 0, 297.00, 12, NULL, 90, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(22, 'FMEA AIAG & VDA', NULL, NULL, 'Aplicação prática do FMEA AIAG & VDA para análise de riscos, priorização de ações e integração com desenvolvimento de produto e processo.', 'https://images.unsplash.com/photo-1581092334651-ddf26d9a09d0?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 9, 6, 0, 297.00, 16, NULL, 90, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(23, 'MAS - Measurement System Analysis', NULL, NULL, 'Treinamento de análise de sistemas de medição para avaliação de repetibilidade, reprodutibilidade, tendência, linearidade e estabilidade.', 'https://images.unsplash.com/photo-1581091215367-59ab6b3625f4?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 9, 6, 0, 247.00, 12, NULL, 90, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(24, 'CEP - Controle Estatístico do Processo', NULL, NULL, 'Fundamentos e aplicação do CEP para monitoramento, estabilidade, capacidade de processo e tomada de decisão baseada em dados.', 'https://images.unsplash.com/photo-1543286386-2e659306cd6c?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 9, 6, 0, 247.00, 12, NULL, 90, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(25, 'PPAP - Processo de Aprovação de Peça de Produção', NULL, NULL, 'Curso sobre requisitos, níveis de submissão, documentação e critérios para aprovação de peças de produção no setor automotivo.', 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 9, 6, 0, 247.00, 12, NULL, 90, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(26, 'CQI-09 - Tratamento Térmico', NULL, NULL, 'Requisitos especiais CQI-09 para avaliação de processos de tratamento térmico na cadeia automotiva.', 'https://images.unsplash.com/photo-1516937941344-00b4e0337589?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 9, 6, 0, 297.00, 12, NULL, 90, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(27, 'CQI-11 - Tratamento Superficial', NULL, NULL, 'Treinamento sobre requisitos CQI-11 para avaliação e controle de processos de tratamento superficial.', 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 9, 6, 0, 297.00, 12, NULL, 90, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(28, 'CQI-14 - Gestão de Garantia Automotiva', NULL, NULL, 'Aplicação dos requisitos CQI-14 para gestão de garantia, análise de falhas, retorno de campo e melhoria de processos automotivos.', 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 9, 6, 0, 297.00, 12, NULL, 90, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(29, 'ISO 27001 - Interpretação da Norma', NULL, NULL, 'Interpretação dos requisitos da ISO 27001 para implantação, manutenção e melhoria de sistemas de gestão de segurança da informação.', 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 10, 6, 0, 397.00, 20, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(30, 'ISO 27701 - Interpretação da Norma', NULL, NULL, 'Curso de interpretação da ISO 27701 para gestão de informações de privacidade e integração com sistemas de segurança da informação.', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 10, 6, 0, 397.00, 20, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35'),
(31, 'TISAX VDA ISA - Interpretação', NULL, NULL, 'Treinamento sobre TISAX e catálogo VDA ISA para avaliação de segurança da informação na cadeia automotiva.', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=900&q=80', 1, 1, NULL, NULL, 'ambos', 1, 10, 6, 0, 397.00, 20, NULL, 120, '2026-05-30 12:08:35', '2026-05-30 12:08:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `instrutores`
--

CREATE TABLE `instrutores` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `qualificacao1` varchar(200) DEFAULT NULL,
  `qualificacao2` varchar(200) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `assinatura_url` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `instrutores`
--

INSERT INTO `instrutores` (`id`, `nome`, `qualificacao1`, `qualificacao2`, `descricao`, `avatar_url`, `assinatura_url`, `created_at`) VALUES
(1, 'Dr. Carlos Mendes', 'Doutor em Gestão Empresarial', 'MBA em Liderança e Inovação', NULL, NULL, NULL, '2026-05-05 22:35:34'),
(2, 'Ana Paula Rocha', 'Especialista em Compliance e LGPD', 'Advogada com 15 anos de experiência', NULL, NULL, NULL, '2026-05-05 22:35:34'),
(3, 'Rafael Sousa', 'Engenheiro de Software Sênior', 'Certificado AWS e Google Cloud', NULL, NULL, NULL, '2026-05-05 22:35:34'),
(4, 'Mariana Figueiredo', 'Mestre em Psicologia Organizacional', 'Coach Executivo Certificado', NULL, NULL, NULL, '2026-05-05 22:35:34'),
(5, 'João Augusto Lima', 'Especialista em Segurança do Trabalho', 'Pós-graduado em Gestão de Riscos', NULL, NULL, NULL, '2026-05-05 22:35:34'),
(6, 'Equipe Técnica ActShare', 'Especialistas em sistemas de gestão', 'Normas ISO, IATF e auditorias', NULL, NULL, NULL, '2026-05-30 12:08:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(10) UNSIGNED NOT NULL,
  `pedido_id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(10) UNSIGNED NOT NULL,
  `vagas` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `preco_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `com_prova` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`id`, `pedido_id`, `curso_id`, `vagas`, `preco_unitario`, `com_prova`) VALUES
(1, 1, 9, 1, 649.00, 1),
(2, 2, 9, 2, 649.00, 1),
(3, 3, 9, 1, 649.00, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(10) UNSIGNED NOT NULL,
  `aluno_id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(10) UNSIGNED NOT NULL,
  `data_inicio` datetime NOT NULL DEFAULT current_timestamp(),
  `data_fim_acesso` datetime DEFAULT NULL,
  `progresso_total` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `concluido` tinyint(1) NOT NULL DEFAULT 0,
  `data_conclusao` datetime DEFAULT NULL,
  `vagas_usadas` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `vagas_totais` int(10) UNSIGNED DEFAULT NULL,
  `participante` tinyint(1) NOT NULL DEFAULT 0,
  `com_prova` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `matriculas`
--

INSERT INTO `matriculas` (`id`, `aluno_id`, `curso_id`, `data_inicio`, `data_fim_acesso`, `progresso_total`, `concluido`, `data_conclusao`, `vagas_usadas`, `vagas_totais`, `participante`, `com_prova`, `created_at`) VALUES
(1, 6, 9, '2026-05-27 10:58:19', '2026-11-23 14:58:19', 0, 0, NULL, 1, NULL, 0, 1, '2026-05-27 10:58:19'),
(2, 7, 9, '2026-05-27 11:47:58', '2026-11-23 15:47:58', 0, 0, NULL, 1, NULL, 0, 1, '2026-05-27 11:47:58'),
(3, 7, 1, '2026-05-27 12:12:20', NULL, 0, 0, NULL, 1, NULL, 0, 0, '2026-05-27 12:12:20'),
(4, 1, 1, '2026-05-30 12:32:54', NULL, 0, 0, NULL, 1, NULL, 0, 0, '2026-05-30 12:32:54'),
(5, 9, 1, '2026-05-30 12:39:41', NULL, 25, 0, NULL, 1, NULL, 0, 0, '2026-05-30 12:39:41'),
(6, 9, 2, '2026-05-30 12:39:41', NULL, 60, 0, NULL, 1, NULL, 0, 0, '2026-05-30 12:39:41'),
(7, 9, 9, '2026-05-30 12:39:41', NULL, 10, 0, NULL, 1, NULL, 0, 0, '2026-05-30 12:39:41'),
(12, 17, 1, '2026-06-17 23:10:18', '2026-12-15 03:10:18', 0, 0, NULL, 2, 5, 0, 1, '2026-06-17 23:10:18'),
(13, 17, 6, '2026-06-17 23:10:18', '2026-12-15 03:10:18', 0, 0, NULL, 0, 3, 0, 0, '2026-06-17 23:10:18'),
(14, 19, 1, '2026-06-17 23:10:18', '2026-12-15 03:10:18', 50, 0, NULL, 1, NULL, 0, 1, '2026-06-17 23:10:18'),
(15, 20, 1, '2026-06-17 23:10:18', '2026-12-15 03:10:18', 0, 0, NULL, 1, NULL, 0, 1, '2026-06-17 23:10:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `membros_organizacao`
--

CREATE TABLE `membros_organizacao` (
  `id` int(10) UNSIGNED NOT NULL,
  `organizacao_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `membros_organizacao`
--

INSERT INTO `membros_organizacao` (`id`, `organizacao_id`, `usuario_id`, `created_at`) VALUES
(4, 2, 18, '2026-06-17 23:10:18'),
(5, 2, 19, '2026-06-17 23:10:18'),
(6, 2, 20, '2026-06-17 23:10:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modulos`
--

CREATE TABLE `modulos` (
  `id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `ordem` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `modulos`
--

INSERT INTO `modulos` (`id`, `curso_id`, `titulo`, `ordem`, `created_at`) VALUES
(1, 1, 'Fundamentos de Gerenciamento de Projetos', 1, '2026-05-05 22:35:34'),
(2, 1, 'Metodologias Ágeis — Scrum e Kanban', 2, '2026-05-05 22:35:34'),
(3, 1, 'Planejamento e Escopo', 3, '2026-05-05 22:35:34'),
(4, 1, 'Gestão de Riscos e Qualidade', 4, '2026-05-05 22:35:34'),
(5, 2, 'Introdução à LGPD', 1, '2026-05-05 22:35:34'),
(6, 2, 'Direitos dos Titulares', 2, '2026-05-05 22:35:34'),
(7, 2, 'Obrigações das Empresas', 3, '2026-05-05 22:35:34'),
(8, 2, 'Implementação Prática', 4, '2026-05-05 22:35:34'),
(9, 3, 'Python do Zero', 1, '2026-05-05 22:35:34'),
(10, 3, 'Manipulação de Arquivos e Planilhas', 2, '2026-05-05 22:35:34'),
(11, 3, 'Automação Web com Selenium', 3, '2026-05-05 22:35:34'),
(12, 3, 'Integração com APIs REST', 4, '2026-05-05 22:35:34'),
(13, 4, 'Autoconhecimento e Estilos de Liderança', 1, '2026-05-05 22:35:34'),
(14, 4, 'Comunicação Assertiva', 2, '2026-05-05 22:35:34'),
(15, 4, 'Gestão de Equipes e Conflitos', 3, '2026-05-05 22:35:34'),
(16, 5, 'Fundamentos de Compliance', 1, '2026-05-05 22:35:34'),
(17, 5, 'Lei Anticorrupção', 2, '2026-05-05 22:35:34'),
(18, 5, 'Programa de Integridade', 3, '2026-05-05 22:35:34'),
(19, 5, 'Gestão de Riscos Corporativos', 4, '2026-05-05 22:35:34'),
(20, 9, 'Diretrizes de Auditoria e Conformidade', 1, '2026-05-27 10:41:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `opcoes`
--

CREATE TABLE `opcoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `pergunta_id` int(10) UNSIGNED NOT NULL,
  `texto` varchar(500) NOT NULL,
  `correta` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `opcoes`
--

INSERT INTO `opcoes` (`id`, `pergunta_id`, `texto`, `correta`) VALUES
(1, 1, 'Um trabalho repetitivo e contínuo', 0),
(2, 1, 'Um esforço temporário para criar um produto ou resultado único', 1),
(3, 1, 'Qualquer tarefa do dia a dia da empresa', 0),
(4, 1, 'Uma reunião de planejamento', 0),
(5, 2, 'Escrever o código do sistema', 0),
(6, 2, 'Garantir que o projeto seja entregue no prazo', 0),
(7, 2, 'Integrar pessoas, processos e tecnologia', 1),
(8, 2, 'Contratar os membros da equipe', 0),
(9, 3, 'Iniciação', 0),
(10, 3, 'Planejamento', 0),
(11, 3, 'Produção', 1),
(12, 3, 'Encerramento', 0),
(13, 4, 'O funcionário que executa os backups diários da segurança.', 0),
(14, 4, 'A pessoa responsável por gerenciar e liderar a equipe de auditoria.', 1),
(15, 4, 'O diretor executivo da empresa parceira externa.', 0),
(16, 4, 'Nenhuma das anteriores.', 0),
(17, 5, '1 a 2 dias dependendo do porte da organização.', 1),
(18, 5, '6 meses ininterruptos de análise profunda.', 0),
(19, 5, 'Apenas 10 minutos de conferência rápida.', 0),
(20, 5, 'Todas as alternativas anteriores estão corretas.', 0),
(21, 6, 'Uma medida ou prática adotada para mitigar e gerenciar riscos de segurança.', 1),
(22, 6, 'Uma fechadura eletrônica instalada na porta principal de TI.', 0),
(23, 6, 'Um software antivírus instalado nos notebooks da administração.', 0),
(24, 6, 'Todas as alternativas anteriores estão corretas.', 0),
(25, 7, 'Em intervalos planejados e periódicos definidos pela organização.', 1),
(26, 7, 'Somente após a ocorrência de incidentes ou vazamentos de dados graves.', 0),
(27, 7, 'Uma vez a cada 10 anos para revalidação do SGSI.', 0),
(28, 7, 'Nenhuma das alternativas anteriores.', 0),
(29, 8, 'A ausência total ou falha sistemática de aplicação de um requisito obrigatório da norma.', 1),
(30, 8, 'Um erro de digitação simples em um cabeçalho do manual de segurança.', 0),
(31, 8, 'Deixar a tela do computador desbloqueada em uma única ausência rápida.', 0),
(32, 8, 'Nenhuma das anteriores.', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `organizacoes`
--

CREATE TABLE `organizacoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `gestor_id` int(10) UNSIGNED NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `certificado_acesso` enum('empresa','aluno','ambos') DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `organizacoes`
--

INSERT INTO `organizacoes` (`id`, `gestor_id`, `ativo`, `certificado_acesso`, `created_at`) VALUES
(2, 17, 1, 'ambos', '2026-06-17 23:10:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `total_bruto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `desconto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_liquido` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cupom_id` int(10) UNSIGNED DEFAULT NULL,
  `situacao` enum('pendente','pago','cancelado') NOT NULL DEFAULT 'pendente',
  `asaas_id` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `total_bruto`, `desconto`, `total_liquido`, `cupom_id`, `situacao`, `asaas_id`, `created_at`) VALUES
(1, 6, 649.00, 0.00, 649.00, NULL, 'pago', 'simulado', '2026-05-27 10:58:14'),
(2, 7, 1298.00, 64.90, 1233.10, NULL, 'pendente', 'simulado', '2026-05-27 11:42:37'),
(3, 7, 649.00, 0.00, 649.00, NULL, 'pago', 'simulado', '2026-05-27 11:47:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perguntas`
--

CREATE TABLE `perguntas` (
  `id` int(10) UNSIGNED NOT NULL,
  `aula_id` int(10) UNSIGNED NOT NULL,
  `curso_id` int(10) UNSIGNED DEFAULT NULL,
  `modulo_id` int(10) UNSIGNED DEFAULT NULL,
  `texto` text NOT NULL,
  `justificativa` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perguntas`
--

INSERT INTO `perguntas` (`id`, `aula_id`, `curso_id`, `modulo_id`, `texto`, `justificativa`, `created_at`) VALUES
(1, 4, NULL, NULL, 'O que define um projeto?', NULL, '2026-05-05 22:35:34'),
(2, 4, NULL, NULL, 'Qual é o principal papel do gerente de projetos?', NULL, '2026-05-05 22:35:34'),
(3, 4, NULL, NULL, 'Qual fase NÃO faz parte do ciclo de vida de um projeto?', NULL, '2026-05-05 22:35:34'),
(4, 22, NULL, NULL, 'Qual das seguintes alternativas descreve melhor o principal papel do Auditor Líder?', 'O auditor líder é responsável por planejar, coordenar e dirigir a equipe auditora, além de consolidar o relatório final.', '2026-05-27 10:41:57'),
(5, 22, NULL, NULL, 'Qual a duração recomendada padrão de uma auditoria de Fase 1?', 'A auditoria de fase 1 é focada em revisão documental do sistema e costuma levar de 1 a 2 dias.', '2026-05-27 10:41:57'),
(6, 23, NULL, NULL, 'O que constitui um controle de segurança da informação segundo a ISO 27001?', 'Controles de segurança da informação são práticas, políticas ou mecanismos técnicos ou físicos para gerenciar e reduzir riscos a níveis aceitáveis.', '2026-05-27 10:41:57'),
(7, 23, NULL, NULL, 'Qual é a frequência de realização correta de auditorias internas de conformidade em uma organização certificada?', 'A norma exige que auditorias internas ocorram em intervalos planejados para verificar a conformidade do SGSI.', '2026-05-27 10:41:57'),
(8, 23, NULL, NULL, 'Qual das opções abaixo caracteriza uma \"Não Conformidade Maior\" em auditorias ISO?', 'A Não Conformidade Maior é configurada quando há ausência total ou falha sistemática na aplicação de um requisito mandatório da norma.', '2026-05-27 10:41:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pesquisa_perguntas`
--

CREATE TABLE `pesquisa_perguntas` (
  `id` int(10) UNSIGNED NOT NULL,
  `texto` varchar(500) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pesquisa_perguntas`
--

INSERT INTO `pesquisa_perguntas` (`id`, `texto`, `created_at`) VALUES
(1, 'Como voc├¬ avalia a clareza e organiza├º├úo do conte├║do do curso?', '2026-06-16 22:40:37'),
(2, 'Como voc├¬ avalia o dom├¡nio t├®cnico e a did├ítica do instrutor?', '2026-06-16 22:40:37'),
(3, 'Como voc├¬ avalia a qualidade do material de apoio disponibilizado?', '2026-06-16 22:40:37'),
(4, 'Como voc├¬ avalia a experi├¬ncia de navega├º├úo e usabilidade da plataforma?', '2026-06-16 22:40:37'),
(5, 'Qual ├® o seu n├¡vel de satisfa├º├úo geral com o curso realizado?', '2026-06-16 22:40:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pesquisa_respostas`
--

CREATE TABLE `pesquisa_respostas` (
  `id` int(10) UNSIGNED NOT NULL,
  `matricula_id` int(10) UNSIGNED NOT NULL,
  `pergunta_id` int(10) UNSIGNED NOT NULL,
  `nota` tinyint(3) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `proctoring_logs`
--

CREATE TABLE `proctoring_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `matricula_id` int(10) UNSIGNED NOT NULL,
  `aula_id` int(10) UNSIGNED NOT NULL,
  `tipo_evento` varchar(50) NOT NULL,
  `detalhes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `progresso_aula`
--

CREATE TABLE `progresso_aula` (
  `id` int(10) UNSIGNED NOT NULL,
  `matricula_id` int(10) UNSIGNED NOT NULL,
  `aula_id` int(10) UNSIGNED NOT NULL,
  `concluida` tinyint(1) NOT NULL DEFAULT 0,
  `tempo_parada` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data_conclusao` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `quiz_perguntas_sorteadas`
--

CREATE TABLE `quiz_perguntas_sorteadas` (
  `id` int(10) UNSIGNED NOT NULL,
  `matricula_id` int(10) UNSIGNED NOT NULL,
  `aula_id` int(10) UNSIGNED NOT NULL,
  `pergunta_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `quiz_perguntas_sorteadas`
--

INSERT INTO `quiz_perguntas_sorteadas` (`id`, `matricula_id`, `aula_id`, `pergunta_id`) VALUES
(1, 1, 22, 4),
(2, 1, 22, 5),
(3, 2, 22, 4),
(4, 2, 22, 5),
(7, 2, 23, 6),
(5, 2, 23, 7),
(6, 2, 23, 8);

-- --------------------------------------------------------

--
-- Estrutura para tabela `quiz_resposta`
--

CREATE TABLE `quiz_resposta` (
  `id` int(10) UNSIGNED NOT NULL,
  `matricula_id` int(10) UNSIGNED NOT NULL,
  `aula_id` int(10) UNSIGNED NOT NULL,
  `nota` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `aprovado` tinyint(1) NOT NULL DEFAULT 0,
  `acertos` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_perguntas` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `tentativas_restantes` int(10) UNSIGNED NOT NULL DEFAULT 5,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `quiz_resposta`
--

INSERT INTO `quiz_resposta` (`id`, `matricula_id`, `aula_id`, `nota`, `aprovado`, `acertos`, `total_perguntas`, `tentativas_restantes`, `updated_at`) VALUES
(1, 1, 22, 50, 0, 1, 2, 4, '2026-05-27 10:59:13'),
(2, 2, 22, 50, 0, 1, 2, 4, '2026-05-27 11:48:50'),
(4, 14, 25, 50, 0, 1, 2, 4, '2026-06-17 23:10:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessoes`
--

CREATE TABLE `sessoes` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `sessoes`
--

INSERT INTO `sessoes` (`id`, `usuario_id`, `token`, `expires_at`, `created_at`) VALUES
(1, 2, '02f8da04408b8b86a57415081cf28e1919ce9726da4104dc54302fc5bd4d84ce', '2026-05-27 16:39:41', '2026-05-27 10:39:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `role` enum('admin','gestor','aluno','instrutor') NOT NULL DEFAULT 'aluno',
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `documento` varchar(30) DEFAULT NULL,
  `telefone` varchar(30) DEFAULT NULL,
  `tipo_pessoa` enum('fisica','juridica') DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `razao_social` varchar(200) DEFAULT NULL,
  `inscricao_estadual` varchar(50) DEFAULT NULL,
  `cep` varchar(20) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `numero` varchar(30) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `pais` varchar(80) DEFAULT 'Brasil',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha_hash`, `role`, `ativo`, `documento`, `telefone`, `tipo_pessoa`, `data_nascimento`, `razao_social`, `inscricao_estadual`, `cep`, `endereco`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `pais`, `created_at`) VALUES
(1, 'Administrador', 'admin@actshare.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-05-06 01:14:24'),
(2, 'Robson Amorim', 'robson@actshare.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-05-05 22:35:34'),
(3, 'Maria Silva', 'maria@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluno', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-05-05 22:35:34'),
(4, 'João Santos', 'joao@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluno', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-05-05 22:35:34'),
(5, 'Empresa ABC', 'gestor@empresaabc.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gestor', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-05-05 22:35:34'),
(6, 'ROBSON', 'robson.dev9@gmail.com', '$2y$10$Kzpp9Mq/IWtSdhdQTOwd/ecMgXUXFRhE9cHFpNRG4sxTXQ996mOR6', 'aluno', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-05-27 10:39:58'),
(7, 'Teste', 'teste@teste.com', '$2y$10$XSm6KCuLKiAU.HNdVIqZOO0Ip0nhUyRp0Neo9g7MyyMdrqCFr0e4W', 'aluno', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-05-27 11:28:07'),
(8, 'Admin Teste', 'admin.teste@actshare.com.br', '$2y$10$.3R366hoqABhvictbrSFU.W/hYYmh2RHsDLdtZWIGUs5RUkluaRuC', 'admin', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-05-30 12:39:41'),
(9, 'Aluno Teste', 'aluno.teste@actshare.com.br', '$2y$10$.3R366hoqABhvictbrSFU.W/hYYmh2RHsDLdtZWIGUs5RUkluaRuC', 'aluno', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-05-30 12:39:41'),
(10, 'Cliente Teste Compra', 'cliente.teste+767869149@actshare.com.br', '$2y$10$Y/Tp9tW6okHfPbgPaDRcbe8r9flf1fAZjGhHNJ6amv37lYr8gciK6', 'aluno', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-30 12:49:57'),
(11, 'Cliente Teste Compra', 'cliente.teste+890556165@actshare.com.br', '$2y$10$h3XSdB1MsMhJ0TrLm0xI1OZ9vSbsSc6X2QkgQ.0C3TS1uydKmBzkG', 'aluno', 1, '123', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-30 12:50:11'),
(12, 'Cliente Completo Teste', 'cliente.completo+589210141@actshare.com.br', '$2y$10$j65gIgk1uol2dZatF8RVruX5OCUKGbUaXYczERb6FoV/BohZJ1w5K', 'aluno', 1, '123.456.789-00', '11988887777', 'fisica', '1990-01-01', NULL, NULL, '01001000', 'Praca da Se', '100', NULL, 'Se', 'Sao Paulo', 'SP', 'Brasil', '2026-05-30 12:52:18'),
(17, 'Sigrid Rut Sand (Gestor 1)', 'gestor.teste@actshare.com.br', '$2y$10$Ku9LJOreDx2oiCmgo87omu7tQ8ATBvY/TJGHcbcRVYW01Li3fbD3m', 'gestor', 1, '73.307.380/0001-85', NULL, 'juridica', NULL, 'Sigrid Rut Sand Corp', NULL, '01001-000', 'Praça da Sé', '100', 'Sala 401', 'Sé', 'São Paulo', 'SP', 'Brasil', '2026-06-17 23:10:18'),
(18, 'Lucas Oliveira (Sub-Gestor)', 'subgestor.teste@actshare.com.br', '$2y$10$Ku9LJOreDx2oiCmgo87omu7tQ8ATBvY/TJGHcbcRVYW01Li3fbD3m', 'gestor', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-06-17 23:10:18'),
(19, 'José da Silva', 'aluno1@empresa.com', '$2y$10$Ku9LJOreDx2oiCmgo87omu7tQ8ATBvY/TJGHcbcRVYW01Li3fbD3m', 'aluno', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-06-17 23:10:18'),
(20, 'Márcia Ribeiro', 'aluno2@empresa.com', '$2y$10$Ku9LJOreDx2oiCmgo87omu7tQ8ATBvY/TJGHcbcRVYW01Li3fbD3m', 'aluno', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Brasil', '2026-06-17 23:10:18');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `aulas`
--
ALTER TABLE `aulas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_aula_modulo` (`modulo_id`);

--
-- Índices de tabela `avaliacao_tentativas`
--
ALTER TABLE `avaliacao_tentativas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_avt_matricula` (`matricula_id`),
  ADD KEY `fk_avt_aula` (`aula_id`),
  ADD KEY `fk_avt_pedido` (`pedido_id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_slug` (`slug`);

--
-- Índices de tabela `certificados_manuais`
--
ALTER TABLE `certificados_manuais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cert_autenticidade` (`codigo_autenticidade`);

--
-- Índices de tabela `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_codigo` (`codigo`);

--
-- Índices de tabela `cupons_indicacao`
--
ALTER TABLE `cupons_indicacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ind_codigo` (`codigo`),
  ADD KEY `fk_indicador_usuario` (`indicador_id`);

--
-- Índices de tabela `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_curso_categoria` (`categoria_id`),
  ADD KEY `fk_curso_instrutor` (`instrutor_id`);

--
-- Índices de tabela `instrutores`
--
ALTER TABLE `instrutores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_pedido` (`pedido_id`),
  ADD KEY `fk_item_curso` (`curso_id`);

--
-- Índices de tabela `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_aluno_curso` (`aluno_id`,`curso_id`),
  ADD KEY `fk_matricula_curso` (`curso_id`);

--
-- Índices de tabela `membros_organizacao`
--
ALTER TABLE `membros_organizacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_org_usuario` (`organizacao_id`,`usuario_id`),
  ADD KEY `fk_membro_usuario` (`usuario_id`);

--
-- Índices de tabela `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_modulo_curso` (`curso_id`);

--
-- Índices de tabela `opcoes`
--
ALTER TABLE `opcoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_opcao_pergunta` (`pergunta_id`);

--
-- Índices de tabela `organizacoes`
--
ALTER TABLE `organizacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_org_gestor` (`gestor_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_usuario` (`usuario_id`),
  ADD KEY `fk_pedido_cupom` (`cupom_id`);

--
-- Índices de tabela `perguntas`
--
ALTER TABLE `perguntas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pergunta_aula` (`aula_id`),
  ADD KEY `fk_pergunta_curso` (`curso_id`),
  ADD KEY `fk_pergunta_modulo` (`modulo_id`);

--
-- Índices de tabela `pesquisa_perguntas`
--
ALTER TABLE `pesquisa_perguntas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pesquisa_respostas`
--
ALTER TABLE `pesquisa_respostas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mat_perg` (`matricula_id`,`pergunta_id`),
  ADD KEY `fk_pesq_pergunta` (`pergunta_id`);

--
-- Índices de tabela `proctoring_logs`
--
ALTER TABLE `proctoring_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_proctoring_matricula` (`matricula_id`),
  ADD KEY `fk_proctoring_aula` (`aula_id`);

--
-- Índices de tabela `progresso_aula`
--
ALTER TABLE `progresso_aula`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_matricula_aula` (`matricula_id`,`aula_id`),
  ADD KEY `fk_prog_aula` (`aula_id`);

--
-- Índices de tabela `quiz_perguntas_sorteadas`
--
ALTER TABLE `quiz_perguntas_sorteadas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mat_aula_perg` (`matricula_id`,`aula_id`,`pergunta_id`),
  ADD KEY `fk_qps_pergunta` (`pergunta_id`);

--
-- Índices de tabela `quiz_resposta`
--
ALTER TABLE `quiz_resposta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_quiz_matricula_aula` (`matricula_id`,`aula_id`),
  ADD KEY `fk_quiz_aula` (`aula_id`);

--
-- Índices de tabela `sessoes`
--
ALTER TABLE `sessoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuario` (`usuario_id`),
  ADD UNIQUE KEY `uq_token` (`token`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `aulas`
--
ALTER TABLE `aulas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `avaliacao_tentativas`
--
ALTER TABLE `avaliacao_tentativas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `certificados_manuais`
--
ALTER TABLE `certificados_manuais`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cupons`
--
ALTER TABLE `cupons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cupons_indicacao`
--
ALTER TABLE `cupons_indicacao`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `instrutores`
--
ALTER TABLE `instrutores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `membros_organizacao`
--
ALTER TABLE `membros_organizacao`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `opcoes`
--
ALTER TABLE `opcoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `organizacoes`
--
ALTER TABLE `organizacoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `perguntas`
--
ALTER TABLE `perguntas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `pesquisa_perguntas`
--
ALTER TABLE `pesquisa_perguntas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `pesquisa_respostas`
--
ALTER TABLE `pesquisa_respostas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `proctoring_logs`
--
ALTER TABLE `proctoring_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `progresso_aula`
--
ALTER TABLE `progresso_aula`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `quiz_perguntas_sorteadas`
--
ALTER TABLE `quiz_perguntas_sorteadas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `quiz_resposta`
--
ALTER TABLE `quiz_resposta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `sessoes`
--
ALTER TABLE `sessoes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `aulas`
--
ALTER TABLE `aulas`
  ADD CONSTRAINT `fk_aula_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `avaliacao_tentativas`
--
ALTER TABLE `avaliacao_tentativas`
  ADD CONSTRAINT `fk_avt_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_avt_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_avt_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `cupons_indicacao`
--
ALTER TABLE `cupons_indicacao`
  ADD CONSTRAINT `fk_indicador_usuario` FOREIGN KEY (`indicador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `cursos`
--
ALTER TABLE `cursos`
  ADD CONSTRAINT `fk_curso_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_curso_instrutor` FOREIGN KEY (`instrutor_id`) REFERENCES `instrutores` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `fk_item_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_item_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `fk_matricula_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_matricula_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `membros_organizacao`
--
ALTER TABLE `membros_organizacao`
  ADD CONSTRAINT `fk_membro_org` FOREIGN KEY (`organizacao_id`) REFERENCES `organizacoes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_membro_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `modulos`
--
ALTER TABLE `modulos`
  ADD CONSTRAINT `fk_modulo_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `opcoes`
--
ALTER TABLE `opcoes`
  ADD CONSTRAINT `fk_opcao_pergunta` FOREIGN KEY (`pergunta_id`) REFERENCES `perguntas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `organizacoes`
--
ALTER TABLE `organizacoes`
  ADD CONSTRAINT `fk_org_gestor` FOREIGN KEY (`gestor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedido_cupom` FOREIGN KEY (`cupom_id`) REFERENCES `cupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `perguntas`
--
ALTER TABLE `perguntas`
  ADD CONSTRAINT `fk_pergunta_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pergunta_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pergunta_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pesquisa_respostas`
--
ALTER TABLE `pesquisa_respostas`
  ADD CONSTRAINT `fk_pesq_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pesq_pergunta` FOREIGN KEY (`pergunta_id`) REFERENCES `pesquisa_perguntas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `proctoring_logs`
--
ALTER TABLE `proctoring_logs`
  ADD CONSTRAINT `fk_proctoring_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_proctoring_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `progresso_aula`
--
ALTER TABLE `progresso_aula`
  ADD CONSTRAINT `fk_prog_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_prog_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `quiz_perguntas_sorteadas`
--
ALTER TABLE `quiz_perguntas_sorteadas`
  ADD CONSTRAINT `fk_qps_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_qps_pergunta` FOREIGN KEY (`pergunta_id`) REFERENCES `perguntas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `quiz_resposta`
--
ALTER TABLE `quiz_resposta`
  ADD CONSTRAINT `fk_quiz_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_quiz_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `sessoes`
--
ALTER TABLE `sessoes`
  ADD CONSTRAINT `fk_sessao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
