-- ============================================================
-- ActShare EAD — Dados de exemplo (seed)
-- Execute no phpMyAdmin após importar o schema.sql
-- ============================================================

SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- Instrutores
-- ------------------------------------------------------------
INSERT INTO `instrutores` (`nome`, `qualificacao1`, `qualificacao2`, `avatar_url`) VALUES
('Dr. Carlos Mendes',    'Doutor em Gestão Empresarial',        'MBA em Liderança e Inovação',        NULL),
('Ana Paula Rocha',      'Especialista em Compliance e LGPD',   'Advogada com 15 anos de experiência', NULL),
('Rafael Sousa',         'Engenheiro de Software Sênior',       'Certificado AWS e Google Cloud',      NULL),
('Mariana Figueiredo',   'Mestre em Psicologia Organizacional', 'Coach Executivo Certificado',         NULL),
('João Augusto Lima',    'Especialista em Segurança do Trabalho','Pós-graduado em Gestão de Riscos',   NULL);

-- ------------------------------------------------------------
-- Categorias (já inseridas no schema, apenas garante IDs)
-- ------------------------------------------------------------
-- 1 = Gestão | 2 = Compliance | 3 = Tecnologia | 4 = Soft Skills | 5 = Jurídico

-- ------------------------------------------------------------
-- Cursos
-- ------------------------------------------------------------
INSERT INTO `cursos` (`titulo`, `descricao`, `thumb_url`, `ativo`, `publico`, `categoria_id`, `instrutor_id`, `preco`, `carga_horaria_horas`) VALUES
(
  'Gestão de Projetos na Prática',
  'Aprenda a planejar, executar e controlar projetos com metodologias ágeis e tradicionais. Domine ferramentas como Kanban, Scrum e PMBOK aplicados ao dia a dia corporativo.',
  'https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=600&q=80',
  1, 1, 1, 1, 0.00, 20
),
(
  'LGPD na Prática: Proteja sua Empresa',
  'Entenda todos os aspectos da Lei Geral de Proteção de Dados. Do conceito à implementação, capacite sua equipe para evitar multas e garantir conformidade.',
  'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&q=80',
  1, 1, 2, 2, 0.00, 8
),
(
  'Python para Automação de Processos',
  'Do zero ao automatizador: aprenda Python focado em automação de tarefas repetitivas, relatórios automáticos e integração de sistemas.',
  'https://images.unsplash.com/photo-1526379095098-d400fd0bf935?w=600&q=80',
  1, 1, 3, 3, 0.00, 24
),
(
  'Liderança e Comunicação Assertiva',
  'Desenvolva habilidades de liderança, comunicação e gestão de pessoas. Aprenda a engajar equipes, dar feedbacks eficazes e resolver conflitos.',
  'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&q=80',
  1, 1, 4, 4, 0.00, 12
),
(
  'Compliance e Ética nos Negócios',
  'Estruture um programa de compliance robusto. Políticas anticorrupção, código de conduta, canal de denúncias e gestão de riscos.',
  'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&q=80',
  1, 1, 2, 2, 0.00, 16
),
(
  'Excel Avançado para Gestores',
  'Domine as funções avançadas do Excel: tabelas dinâmicas, Power Query, dashboards e automação com macros VBA voltados para tomada de decisão.',
  'https://images.unsplash.com/photo-1543286386-2e659306cd6c?w=600&q=80',
  1, 1, 1, 1, 0.00, 10
),
(
  'Segurança do Trabalho — NR Essenciais',
  'Normas Regulamentadoras obrigatórias: NR-1, NR-6, NR-17 e NR-35. Capacite sua equipe e mantenha sua empresa em conformidade legal.',
  'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&q=80',
  1, 1, 5, 5, 0.00, 8
),
(
  'Inteligência Artificial no Trabalho',
  'Entenda como usar ferramentas de IA (ChatGPT, Copilot, Gemini) para aumentar sua produtividade, criar conteúdos e automatizar tarefas do dia a dia.',
  'https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=600&q=80',
  1, 1, 3, 3, 0.00, 6
);

-- ------------------------------------------------------------
-- Módulos dos cursos
-- ------------------------------------------------------------

-- Curso 1: Gestão de Projetos
INSERT INTO `modulos` (`curso_id`, `titulo`, `ordem`) VALUES
(1, 'Fundamentos de Gerenciamento de Projetos', 1),
(1, 'Metodologias Ágeis — Scrum e Kanban',       2),
(1, 'Planejamento e Escopo',                      3),
(1, 'Gestão de Riscos e Qualidade',               4);

-- Curso 2: LGPD
INSERT INTO `modulos` (`curso_id`, `titulo`, `ordem`) VALUES
(2, 'Introdução à LGPD',                    1),
(2, 'Direitos dos Titulares',               2),
(2, 'Obrigações das Empresas',              3),
(2, 'Implementação Prática',                4);

-- Curso 3: Python
INSERT INTO `modulos` (`curso_id`, `titulo`, `ordem`) VALUES
(3, 'Python do Zero',                        1),
(3, 'Manipulação de Arquivos e Planilhas',   2),
(3, 'Automação Web com Selenium',            3),
(3, 'Integração com APIs REST',              4);

-- Curso 4: Liderança
INSERT INTO `modulos` (`curso_id`, `titulo`, `ordem`) VALUES
(4, 'Autoconhecimento e Estilos de Liderança', 1),
(4, 'Comunicação Assertiva',                   2),
(4, 'Gestão de Equipes e Conflitos',           3);

-- Curso 5: Compliance
INSERT INTO `modulos` (`curso_id`, `titulo`, `ordem`) VALUES
(5, 'Fundamentos de Compliance',        1),
(5, 'Lei Anticorrupção',                2),
(5, 'Programa de Integridade',          3),
(5, 'Gestão de Riscos Corporativos',    4);

-- ------------------------------------------------------------
-- Aulas dos módulos
-- ------------------------------------------------------------

-- Módulo 1 (Gestão de Projetos — Fundamentos)
INSERT INTO `aulas` (`modulo_id`, `titulo`, `tipo`, `video_url`, `ordem`, `duracao_min`) VALUES
(1, 'O que é um projeto?',                        'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 1, 12),
(1, 'Ciclo de vida de um projeto',                'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 2, 18),
(1, 'O papel do gerente de projetos',             'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 3, 15),
(1, 'Quiz — Fundamentos',                         'quiz',  NULL,                                           4,  0);

-- Módulo 2 (Gestão de Projetos — Ágil)
INSERT INTO `aulas` (`modulo_id`, `titulo`, `tipo`, `video_url`, `ordem`, `duracao_min`) VALUES
(2, 'Manifesto Ágil e seus princípios',           'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 1, 20),
(2, 'Scrum: papéis, eventos e artefatos',         'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 2, 25),
(2, 'Kanban na prática',                          'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 3, 18),
(2, 'Quiz — Metodologias Ágeis',                  'quiz',  NULL,                                           4,  0);

-- Módulo 5 (LGPD — Introdução)
INSERT INTO `aulas` (`modulo_id`, `titulo`, `tipo`, `video_url`, `ordem`, `duracao_min`) VALUES
(5, 'O que é a LGPD e por que ela existe?',       'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 1, 10),
(5, 'Conceitos fundamentais: dado, tratamento…',  'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 2, 14),
(5, 'Bases legais para tratamento de dados',      'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 3, 16),
(5, 'Quiz — Introdução à LGPD',                   'quiz',  NULL,                                           4,  0);

-- Módulo 6 (LGPD — Direitos)
INSERT INTO `aulas` (`modulo_id`, `titulo`, `tipo`, `video_url`, `ordem`, `duracao_min`) VALUES
(6, 'Direitos dos titulares de dados',            'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 1, 12),
(6, 'Como responder às solicitações dos titulares','video','https://www.youtube.com/watch?v=dQw4w9WgXcQ', 2, 15),
(6, 'Quiz — Direitos dos Titulares',              'quiz',  NULL,                                           3,  0);

-- Módulo 9 (Python — Do Zero)
INSERT INTO `aulas` (`modulo_id`, `titulo`, `tipo`, `video_url`, `ordem`, `duracao_min`) VALUES
(9,  'Instalação e configuração do ambiente',     'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 1, 15),
(9,  'Variáveis, tipos e operadores',             'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 2, 20),
(9,  'Condicionais e loops',                      'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 3, 22),
(9,  'Funções e módulos',                         'video', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 4, 25),
(9,  'Quiz — Python Básico',                      'quiz',  NULL,                                           5,  0);

-- ------------------------------------------------------------
-- Quiz: perguntas e opções para a aula de "Fundamentos" (aula id=4)
-- ------------------------------------------------------------
INSERT INTO `perguntas` (`aula_id`, `texto`) VALUES
(4, 'O que define um projeto?'),
(4, 'Qual é o principal papel do gerente de projetos?'),
(4, 'Qual fase NÃO faz parte do ciclo de vida de um projeto?');

-- Pergunta 1
INSERT INTO `opcoes` (`pergunta_id`, `texto`, `correta`) VALUES
(1, 'Um trabalho repetitivo e contínuo',                              0),
(1, 'Um esforço temporário para criar um produto ou resultado único', 1),
(1, 'Qualquer tarefa do dia a dia da empresa',                        0),
(1, 'Uma reunião de planejamento',                                    0);

-- Pergunta 2
INSERT INTO `opcoes` (`pergunta_id`, `texto`, `correta`) VALUES
(2, 'Escrever o código do sistema',                    0),
(2, 'Garantir que o projeto seja entregue no prazo',   0),
(2, 'Integrar pessoas, processos e tecnologia',        1),
(2, 'Contratar os membros da equipe',                  0);

-- Pergunta 3
INSERT INTO `opcoes` (`pergunta_id`, `texto`, `correta`) VALUES
(3, 'Iniciação',    0),
(3, 'Planejamento', 0),
(3, 'Produção',     1),
(3, 'Encerramento', 0);

-- ------------------------------------------------------------
-- Usuários de exemplo
-- ------------------------------------------------------------
-- Senha de todos: password
INSERT INTO `usuarios` (`nome`, `email`, `senha_hash`, `role`) VALUES
('Robson Amorim',  'robson@actshare.com.br',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Maria Silva',    'maria@exemplo.com',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluno'),
('João Santos',    'joao@exemplo.com',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluno'),
('Empresa ABC',    'gestor@empresaabc.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gestor');
