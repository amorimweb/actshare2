-- Lote V15_02 do novo ciclo de ajustes do cliente (correção "item 28 em diante")

-- Garante que os textos acentuados deste arquivo (explicação de exames,
-- templates de e-mail) sejam interpretados como UTF-8 nesta sessão,
-- independente da configuração padrão do cliente mysql que rodar o import.
SET NAMES utf8mb4;

-- "Avaliação" vira um 4º tipo de seleção, ao lado de QM/AU/TL — mesmo modelo
-- de produto (exames_curso), só um tipo novo no enum.
ALTER TABLE exames_curso
  MODIFY COLUMN tipo ENUM('AVALIACAO', 'QM', 'AU', 'TL') NOT NULL;

-- Preço base do curso guardado separado no carrinho, pra permitir religar/
-- desligar Avaliação/Exame direto no card do carrinho recalculando o preço.
-- (armazenado só no localStorage do navegador — não precisa de coluna nova.)

-- Mais faixas de desconto progressivo (o cliente pediu 8 faixas ao todo).
INSERT INTO configuracoes (chave, valor) VALUES
  ('desconto_progressivo_faixa4_min', '21'),
  ('desconto_progressivo_faixa4_max', '30'),
  ('desconto_progressivo_faixa4_percentual', '15'),
  ('desconto_progressivo_faixa5_min', '31'),
  ('desconto_progressivo_faixa5_max', '40'),
  ('desconto_progressivo_faixa5_percentual', '20'),
  ('desconto_progressivo_faixa6_min', '41'),
  ('desconto_progressivo_faixa6_max', '70'),
  ('desconto_progressivo_faixa6_percentual', '25'),
  ('desconto_progressivo_faixa7_min', '71'),
  ('desconto_progressivo_faixa7_max', '100'),
  ('desconto_progressivo_faixa7_percentual', '30'),
  ('desconto_progressivo_faixa8_min', '101'),
  ('desconto_progressivo_faixa8_percentual', '40')
ON DUPLICATE KEY UPDATE valor = valor;

-- Ajusta as faixas 1-3 já existentes para bater com os novos números do cliente
INSERT INTO configuracoes (chave, valor) VALUES ('desconto_progressivo_faixa3_max', '20')
  ON DUPLICATE KEY UPDATE valor = '20';
UPDATE configuracoes SET valor = '2'  WHERE chave = 'desconto_progressivo_faixa1_min';
UPDATE configuracoes SET valor = '5'  WHERE chave = 'desconto_progressivo_faixa1_max';
UPDATE configuracoes SET valor = '5'  WHERE chave = 'desconto_progressivo_faixa1_percentual';
UPDATE configuracoes SET valor = '6'  WHERE chave = 'desconto_progressivo_faixa2_min';
UPDATE configuracoes SET valor = '10' WHERE chave = 'desconto_progressivo_faixa2_max';
UPDATE configuracoes SET valor = '7.5' WHERE chave = 'desconto_progressivo_faixa2_percentual';
UPDATE configuracoes SET valor = '11' WHERE chave = 'desconto_progressivo_faixa3_min';
UPDATE configuracoes SET valor = '20' WHERE chave = 'desconto_progressivo_faixa3_max';
UPDATE configuracoes SET valor = '10' WHERE chave = 'desconto_progressivo_faixa3_percentual';

-- Combos: hierarquia opcional (pré-requisito entre produtos do mesmo combo)
ALTER TABLE combo_itens
  ADD COLUMN IF NOT EXISTS ordem INT UNSIGNED NOT NULL DEFAULT 0;

-- Combos: pode incluir Avaliações/Exames além de cursos
ALTER TABLE combo_itens
  ADD COLUMN IF NOT EXISTS exame_id INT UNSIGNED DEFAULT NULL AFTER curso_id,
  MODIFY COLUMN curso_id INT UNSIGNED DEFAULT NULL;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'combo_itens' AND CONSTRAINT_NAME = 'fk_comboitem_exame'
);
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE combo_itens ADD CONSTRAINT fk_comboitem_exame FOREIGN KEY (exame_id) REFERENCES exames_curso(id) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Certificado: número sequencial de autenticidade por curso (código do curso
-- + número sequencial), além do código aleatório já existente
ALTER TABLE matriculas
  ADD COLUMN IF NOT EXISTS certificado_numero_sequencial INT UNSIGNED DEFAULT NULL;

-- configuracoes.valor precisa caber um texto explicativo (Avaliação/Exames),
-- não só números/percentuais curtos.
ALTER TABLE configuracoes MODIFY COLUMN valor TEXT NOT NULL;

INSERT INTO configuracoes (chave, valor) VALUES
  ('texto_explicacao_exames', 'Ao concluir um treinamento, você pode optar por uma avaliação simples de fixação de conteúdo (Avaliação) ou por um dos Exames Exemplar Global — certificações reconhecidas internacionalmente, disponíveis em três modalidades: QM (Quality Management), AU (Auditor) e TL (Team Leader). Cada modalidade tem seu próprio processo de avaliação e emite um certificado com validade internacional.')
ON DUPLICATE KEY UPDATE valor = valor;

-- Templates de e-mail configuráveis pelo Admin
CREATE TABLE IF NOT EXISTS email_templates (
  chave VARCHAR(60) NOT NULL,
  nome VARCHAR(150) NOT NULL,
  assunto VARCHAR(200) NOT NULL,
  corpo TEXT NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (chave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Módulo EXAMES: cadastro completo do produto Avaliação/Exame Exemplar
-- Global (nº de questões, nota de corte, tempo limite, prazo próprio) +
-- banco de questões independente do curso, sorteado na hora do exame.
ALTER TABLE exames_curso
  ADD COLUMN IF NOT EXISTS nome VARCHAR(150) DEFAULT NULL AFTER tipo,
  ADD COLUMN IF NOT EXISTS prazo_dias INT UNSIGNED NOT NULL DEFAULT 180 AFTER preco,
  ADD COLUMN IF NOT EXISTS numero_questoes INT UNSIGNED NOT NULL DEFAULT 10 AFTER prazo_dias,
  ADD COLUMN IF NOT EXISTS nota_corte_tipo ENUM('questoes','percentual') NOT NULL DEFAULT 'percentual' AFTER numero_questoes,
  ADD COLUMN IF NOT EXISTS nota_corte_valor INT UNSIGNED NOT NULL DEFAULT 70 AFTER nota_corte_tipo,
  ADD COLUMN IF NOT EXISTS tempo_limite_minutos INT UNSIGNED NOT NULL DEFAULT 60 AFTER nota_corte_valor;

CREATE TABLE IF NOT EXISTS exame_perguntas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  exame_curso_id INT UNSIGNED NOT NULL,
  texto TEXT NOT NULL,
  justificativa TEXT DEFAULT NULL,
  imagem_url VARCHAR(500) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_exameperg_exame FOREIGN KEY (exame_curso_id) REFERENCES exames_curso(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS exame_opcoes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  pergunta_id INT UNSIGNED NOT NULL,
  texto VARCHAR(500) NOT NULL,
  correta TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  CONSTRAINT fk_exameopc_pergunta FOREIGN KEY (pergunta_id) REFERENCES exame_perguntas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tentativas de exame: uma linha por tentativa (sem refazer, exceto retake
-- de até 1 ano em caso de reprovação no Exame Exemplar Global)
CREATE TABLE IF NOT EXISTS exame_tentativas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  matricula_id INT UNSIGNED NOT NULL,
  exame_curso_id INT UNSIGNED NOT NULL,
  iniciado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  finalizado_em DATETIME DEFAULT NULL,
  total_questoes INT UNSIGNED DEFAULT NULL,
  acertos INT UNSIGNED DEFAULT NULL,
  erros INT UNSIGNED DEFAULT NULL,
  nao_respondidas INT UNSIGNED DEFAULT NULL,
  resultado ENUM('aprovado','reprovado') DEFAULT NULL,
  respostas_json JSON DEFAULT NULL,
  prazo_retake_ate DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_exametent_matricula FOREIGN KEY (matricula_id) REFERENCES matriculas(id) ON DELETE CASCADE,
  CONSTRAINT fk_exametent_exame FOREIGN KEY (exame_curso_id) REFERENCES exames_curso(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO email_templates (chave, nome, assunto, corpo) VALUES
  ('conta_criada', 'Confirmação de criação de conta',
   'Bem-vindo(a) à ActShare!',
   'Olá, {nome}!\n\nSua conta na plataforma ActShare foi criada com sucesso.\n\nAcesse quando quiser em {link_site}.\n\nEquipe ActShare'),
  ('pedido_recebido', 'Pedido recebido',
   'Recebemos seu pedido #{pedido_id}',
   'Olá, {nome}!\n\nRecebemos seu pedido #{pedido_id} no valor de R$ {total}.\n\nAssim que o pagamento for confirmado, seu(s) treinamento(s) serão liberados automaticamente.\n\nEquipe ActShare'),
  ('aguardando_pagamento', 'Aguardando pagamento',
   'Seu pedido #{pedido_id} está aguardando pagamento',
   'Olá, {nome}!\n\nSeu pedido #{pedido_id} ainda está aguardando a confirmação do pagamento.\n\nEquipe ActShare'),
  ('pagamento_confirmado', 'Pagamento confirmado',
   'Pagamento do pedido #{pedido_id} confirmado!',
   'Olá, {nome}!\n\nSeu pagamento foi confirmado e seu(s) treinamento(s) já estão disponíveis na sua Área do Aluno.\n\nEquipe ActShare'),
  ('curso_disponivel', 'Curso disponível para o aluno',
   'Seu treinamento {curso} já está disponível',
   'Olá, {nome}!\n\nO treinamento "{curso}" já está disponível na plataforma ActShare.\nAcesse sua Área do Aluno para começar.\n\nEquipe ActShare')
ON DUPLICATE KEY UPDATE chave = chave;
