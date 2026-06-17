-- ============================================================
-- ActShare EAD — Migração para Recursos do Administrador
-- ============================================================

-- 1. Tabela cursos
ALTER TABLE `cursos` 
  ADD COLUMN `nome_certificado` VARCHAR(250) DEFAULT NULL AFTER `titulo`,
  ADD COLUMN `codigo` VARCHAR(10) DEFAULT NULL AFTER `nome_certificado`,
  ADD COLUMN `prazo_acesso_dias` INT UNSIGNED DEFAULT NULL AFTER `carga_horaria_horas`,
  ADD COLUMN `disponivel_loja` TINYINT(1) NOT NULL DEFAULT 1 AFTER `ativo`,
  ADD COLUMN `certificado_template_url` VARCHAR(500) DEFAULT NULL AFTER `disponivel_loja`,
  ADD COLUMN `certificado_config` TEXT DEFAULT NULL AFTER `certificado_template_url`,
  ADD COLUMN `certificado_liberacao` ENUM('ambos', 'empresa', 'aluno') DEFAULT 'ambos' AFTER `certificado_config`,
  ADD COLUMN `exibir_instrutor` TINYINT(1) NOT NULL DEFAULT 0 AFTER `instrutor_id`;

-- 2. Tabela instrutores
ALTER TABLE `instrutores`
  ADD COLUMN `descricao` TEXT DEFAULT NULL AFTER `qualificacao2`;

-- 3. Tabela perguntas
ALTER TABLE `perguntas`
  ADD COLUMN `curso_id` INT UNSIGNED DEFAULT NULL AFTER `aula_id`,
  ADD COLUMN `modulo_id` INT UNSIGNED DEFAULT NULL AFTER `curso_id`,
  ADD CONSTRAINT `fk_pergunta_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pergunta_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE;

-- 4. Tabela aulas (Configurações de Prova/Avaliação)
ALTER TABLE `aulas`
  ADD COLUMN `exemplar_global` TINYINT(1) NOT NULL DEFAULT 0 AFTER `e_prova`,
  ADD COLUMN `nota_corte_tipo` ENUM('questoes', 'percentual') NOT NULL DEFAULT 'percentual' AFTER `exemplar_global`,
  ADD COLUMN `nota_corte_valor` INT UNSIGNED NOT NULL DEFAULT 70 AFTER `nota_corte_tipo`,
  ADD COLUMN `tempo_limite_minutos` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `nota_corte_valor`,
  ADD COLUMN `bloquear_proctoring` TINYINT(1) NOT NULL DEFAULT 0 AFTER `tempo_limite_minutos`;

-- 5. Tabela de Logs e Tentativas de Avaliação
CREATE TABLE IF NOT EXISTS `avaliacao_tentativas` (
  `id`              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `matricula_id`    INT UNSIGNED    NOT NULL,
  `aula_id`         INT UNSIGNED    NOT NULL,
  `pedido_id`       INT UNSIGNED    DEFAULT NULL,
  `total_questoes`  INT UNSIGNED    NOT NULL,
  `acertos`         INT UNSIGNED    NOT NULL,
  `erros`           INT UNSIGNED    NOT NULL,
  `nao_respondidas` INT UNSIGNED    NOT NULL,
  `nota`            INT UNSIGNED    NOT NULL, -- Porcentagem de acerto
  `resultado`       ENUM('aprovado', 'reprovado') NOT NULL,
  `respostas_json`  JSON            NOT NULL, -- Cópia detalhada de perguntas, alternativas e o que o aluno marcou
  `codigo_certificado` VARCHAR(50)  DEFAULT NULL,
  `created_at`      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_avt_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_avt_aula`      FOREIGN KEY (`aula_id`)      REFERENCES `aulas`      (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_avt_pedido`    FOREIGN KEY (`pedido_id`)    REFERENCES `pedidos`    (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Tabelas de Pesquisa de Satisfação
CREATE TABLE IF NOT EXISTS `pesquisa_perguntas` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `texto`      VARCHAR(500)    NOT NULL,
  `created_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pesquisa_respostas` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `matricula_id` INT UNSIGNED    NOT NULL,
  `pergunta_id`  INT UNSIGNED    NOT NULL,
  `nota`         TINYINT UNSIGNED NOT NULL,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mat_perg` (`matricula_id`, `pergunta_id`),
  CONSTRAINT `fk_pesq_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pesq_pergunta`  FOREIGN KEY (`pergunta_id`)  REFERENCES `pesquisa_perguntas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Tabela de Certificados Emitidos Manualmente
CREATE TABLE IF NOT EXISTS `certificados_manuais` (
  `id`                   INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `cliente_nome`         VARCHAR(250)    NOT NULL,
  `curso_nome`           VARCHAR(250)    NOT NULL,
  `carga_horaria`        INT UNSIGNED    NOT NULL,
  `data_conclusao`       DATE            NOT NULL,
  `tipo_texto`           ENUM('participacao', 'aprovacao') NOT NULL DEFAULT 'participacao',
  `instrutor_nome`       VARCHAR(250)    NOT NULL,
  `assinatura_url`       VARCHAR(500)    NOT NULL,
  `codigo_autenticidade` VARCHAR(50)     NOT NULL,
  `created_at`           DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cert_autenticidade` (`codigo_autenticidade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Seed das perguntas padrão da Pesquisa de Satisfação
INSERT INTO `pesquisa_perguntas` (`texto`) VALUES
('Como você avalia a clareza e organização do conteúdo do curso?'),
('Como você avalia o domínio técnico e a didática do instrutor?'),
('Como você avalia a qualidade do material de apoio disponibilizado?'),
('Como você avalia a experiência de navegação e usabilidade da plataforma?'),
('Qual é o seu nível de satisfação geral com o curso realizado?');
