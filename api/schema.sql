-- ============================================================
-- ActShare EAD — Schema do banco de dados
-- Compatível com MySQL 8.0+ e MariaDB 10.4+
-- Execute este arquivo no phpMyAdmin para criar as tabelas
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- Usuários
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `nome`        VARCHAR(150)    NOT NULL,
  `email`       VARCHAR(200)    NOT NULL,
  `senha_hash`  VARCHAR(255)    NOT NULL,
  `role`        ENUM('admin','gestor','aluno','instrutor') NOT NULL DEFAULT 'aluno',
  `ativo`       TINYINT(1)      NOT NULL DEFAULT 1,
  `documento`   VARCHAR(30)     DEFAULT NULL,
  `telefone`    VARCHAR(30)     DEFAULT NULL,
  `tipo_pessoa` ENUM('fisica','juridica') DEFAULT NULL,
  `data_nascimento` DATE        DEFAULT NULL,
  `razao_social` VARCHAR(200)   DEFAULT NULL,
  `inscricao_estadual` VARCHAR(50) DEFAULT NULL,
  `cep`         VARCHAR(20)     DEFAULT NULL,
  `endereco`    VARCHAR(200)    DEFAULT NULL,
  `numero`      VARCHAR(30)     DEFAULT NULL,
  `complemento` VARCHAR(100)    DEFAULT NULL,
  `bairro`      VARCHAR(100)    DEFAULT NULL,
  `cidade`      VARCHAR(100)    DEFAULT NULL,
  `estado`      VARCHAR(2)      DEFAULT NULL,
  `pais`        VARCHAR(80)     DEFAULT 'Brasil',
  `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuário admin padrão — senha: admin123 (troque após o primeiro acesso)
INSERT INTO `usuarios` (`nome`, `email`, `senha_hash`, `role`) VALUES
('Administrador', 'admin@actshare.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ------------------------------------------------------------
-- Sessões (reset de senha)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessoes` (
  `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `usuario_id`  INT UNSIGNED    NOT NULL,
  `token`       VARCHAR(100)    NOT NULL,
  `expires_at`  DATETIME        NOT NULL,
  `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario` (`usuario_id`),
  UNIQUE KEY `uq_token`   (`token`),
  CONSTRAINT `fk_sessao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Categorias
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `nome`       VARCHAR(100)  NOT NULL,
  `slug`       VARCHAR(120)  DEFAULT NULL,
  `icone`      VARCHAR(50)   DEFAULT NULL,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorias` (`nome`, `slug`) VALUES
('Gestão',          'gestao'),
('Compliance',      'compliance'),
('Tecnologia',      'tecnologia'),
('Soft Skills',     'soft-skills'),
('Jurídico',        'juridico');

-- ------------------------------------------------------------
-- Instrutores
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `instrutores` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `nome`           VARCHAR(150)  NOT NULL,
  `qualificacao1`  VARCHAR(200)  DEFAULT NULL,
  `qualificacao2`  VARCHAR(200)  DEFAULT NULL,
  `avatar_url`     VARCHAR(500)  DEFAULT NULL,
  `assinatura_url` VARCHAR(500)  DEFAULT NULL,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Cursos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cursos` (
  `id`                   INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `titulo`               VARCHAR(250)     NOT NULL,
  `descricao`            TEXT             DEFAULT NULL,
  `thumb_url`            VARCHAR(500)     DEFAULT NULL,
  `ativo`                TINYINT(1)       NOT NULL DEFAULT 1,
  `publico`              TINYINT(1)       NOT NULL DEFAULT 0,
  `categoria_id`         INT UNSIGNED     DEFAULT NULL,
  `instrutor_id`         INT UNSIGNED     DEFAULT NULL,
  `preco`                DECIMAL(10,2)    NOT NULL DEFAULT 0.00,
  `carga_horaria_horas`  INT UNSIGNED     NOT NULL DEFAULT 0,
  `prazo_conclusao_dias` INT UNSIGNED     DEFAULT NULL,
  `created_at`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_curso_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_curso_instrutor` FOREIGN KEY (`instrutor_id`) REFERENCES `instrutores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Módulos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `modulos` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `curso_id`   INT UNSIGNED  NOT NULL,
  `titulo`     VARCHAR(200)  NOT NULL,
  `ordem`      INT UNSIGNED  NOT NULL DEFAULT 0,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_modulo_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Aulas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `aulas` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `modulo_id`   INT UNSIGNED  NOT NULL,
  `titulo`      VARCHAR(250)  NOT NULL,
  `descricao`   TEXT          DEFAULT NULL,
  `tipo`        ENUM('video','texto','quiz','pdf') NOT NULL DEFAULT 'video',
  `video_url`   VARCHAR(500)  DEFAULT NULL,
  `publica`     TINYINT(1)    NOT NULL DEFAULT 0,
  `ordem`       INT UNSIGNED  NOT NULL DEFAULT 0,
  `duracao_min` INT UNSIGNED  NOT NULL DEFAULT 0,
  `quizz_qtd_perguntas` INT UNSIGNED NOT NULL DEFAULT 1,
  `e_prova`     TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_aula_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Matrículas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `matriculas` (
  `id`               INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `aluno_id`         INT UNSIGNED    NOT NULL,
  `curso_id`         INT UNSIGNED    NOT NULL,
  `data_inicio`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_fim_acesso`  DATETIME        DEFAULT NULL,
  `progresso_total`  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `concluido`        TINYINT(1)      NOT NULL DEFAULT 0,
  `data_conclusao`   DATETIME        DEFAULT NULL,
  `vagas_usadas`     INT UNSIGNED    NOT NULL DEFAULT 1,
  `vagas_totais`     INT UNSIGNED    DEFAULT NULL,
  `participante`     TINYINT(1)      NOT NULL DEFAULT 0,
  `com_prova`        TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_aluno_curso` (`aluno_id`, `curso_id`),
  CONSTRAINT `fk_matricula_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_matricula_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Progresso por aula
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `progresso_aula` (
  `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `matricula_id`   INT UNSIGNED  NOT NULL,
  `aula_id`        INT UNSIGNED  NOT NULL,
  `concluida`      TINYINT(1)    NOT NULL DEFAULT 0,
  `tempo_parada`   INT UNSIGNED  NOT NULL DEFAULT 0,
  `data_conclusao` DATETIME      DEFAULT NULL,
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_matricula_aula` (`matricula_id`, `aula_id`),
  CONSTRAINT `fk_prog_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prog_aula`      FOREIGN KEY (`aula_id`)      REFERENCES `aulas`      (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Organizações (para gestores/masters)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `organizacoes` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `gestor_id`  INT UNSIGNED  NOT NULL,
  `ativo`      TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_org_gestor` FOREIGN KEY (`gestor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Membros de organização
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `membros_organizacao` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `organizacao_id`   INT UNSIGNED  NOT NULL,
  `usuario_id`       INT UNSIGNED  NOT NULL,
  `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_org_usuario` (`organizacao_id`, `usuario_id`),
  CONSTRAINT `fk_membro_org`     FOREIGN KEY (`organizacao_id`) REFERENCES `organizacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_membro_usuario` FOREIGN KEY (`usuario_id`)     REFERENCES `usuarios`      (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Perguntas de quiz
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `perguntas` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `aula_id`    INT UNSIGNED  NOT NULL,
  `texto`      TEXT          NOT NULL,
  `justificativa` TEXT        DEFAULT NULL,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pergunta_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Opções de resposta
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `opcoes` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `pergunta_id` INT UNSIGNED  NOT NULL,
  `texto`       VARCHAR(500)  NOT NULL,
  `correta`     TINYINT(1)    NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_opcao_pergunta` FOREIGN KEY (`pergunta_id`) REFERENCES `perguntas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Respostas de quiz
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `quiz_resposta` (
  `id`               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `matricula_id`     INT UNSIGNED  NOT NULL,
  `aula_id`          INT UNSIGNED  NOT NULL,
  `nota`             TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `aprovado`         TINYINT(1)    NOT NULL DEFAULT 0,
  `acertos`          INT UNSIGNED  NOT NULL DEFAULT 0,
  `total_perguntas`  INT UNSIGNED  NOT NULL DEFAULT 0,
  `tentativas_restantes` INT UNSIGNED NOT NULL DEFAULT 5,
  `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_quiz_matricula_aula` (`matricula_id`, `aula_id`),
  CONSTRAINT `fk_quiz_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_quiz_aula`      FOREIGN KEY (`aula_id`)      REFERENCES `aulas`      (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Cupons de Desconto
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cupons` (
  `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `codigo`      VARCHAR(50)     NOT NULL,
  `tipo`        ENUM('fixo','porcentagem') NOT NULL DEFAULT 'porcentagem',
  `valor`       DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `validade`    DATETIME        NOT NULL,
  `limite_uso`  INT UNSIGNED    DEFAULT NULL,
  `usos`        INT UNSIGNED    NOT NULL DEFAULT 0,
  `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Pedidos
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `usuario_id`    INT UNSIGNED    NOT NULL,
  `total_bruto`   DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `desconto`      DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `total_liquido` DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `cupom_id`      INT UNSIGNED    DEFAULT NULL,
  `situacao`      ENUM('pendente','pago','cancelado') NOT NULL DEFAULT 'pendente',
  `asaas_id`      VARCHAR(100)    DEFAULT NULL,
  `created_at`    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pedido_cupom` FOREIGN KEY (`cupom_id`) REFERENCES `cupons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Itens de Pedido
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `itens_pedido` (
  `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `pedido_id`      INT UNSIGNED    NOT NULL,
  `curso_id`       INT UNSIGNED    NOT NULL,
  `vagas`          INT UNSIGNED    NOT NULL DEFAULT 1,
  `preco_unitario` DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  `com_prova`      TINYINT(1)      NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_item_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_curso`  FOREIGN KEY (`curso_id`)  REFERENCES `cursos`  (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Cupons de Indicação (B2C)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cupons_indicacao` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `indicador_id` INT UNSIGNED    NOT NULL,
  `codigo`       VARCHAR(50)     NOT NULL,
  `percentual`   INT UNSIGNED    NOT NULL DEFAULT 10,
  `validade`     DATETIME        NOT NULL,
  `utilizado`    TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ind_codigo` (`codigo`),
  CONSTRAINT `fk_indicador_usuario` FOREIGN KEY (`indicador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Perguntas Sorteadas (Trava por matrícula/aula)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `quiz_perguntas_sorteadas` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `matricula_id` INT UNSIGNED    NOT NULL,
  `aula_id`      INT UNSIGNED    NOT NULL,
  `pergunta_id`  INT UNSIGNED    NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mat_aula_perg` (`matricula_id`, `aula_id`, `pergunta_id`),
  CONSTRAINT `fk_qps_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qps_pergunta` FOREIGN KEY (`pergunta_id`) REFERENCES `perguntas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Logs de Proctoring (Anti-Cheat)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `proctoring_logs` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `matricula_id` INT UNSIGNED    NOT NULL,
  `aula_id`      INT UNSIGNED    NOT NULL,
  `tipo_evento`  VARCHAR(50)     NOT NULL,
  `detalhes`     TEXT            DEFAULT NULL,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_proctoring_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_proctoring_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
