-- Lote B do Diagnóstico ActShare EAD: cadastro de curso/conteúdo
-- Múltiplas caixas de descrição do curso (com toggle de visibilidade individual),
-- hierarquia de pré-requisitos entre cursos e materiais de aula para download.

ALTER TABLE cursos
  ADD COLUMN IF NOT EXISTS video_url_explicativo VARCHAR(500) DEFAULT NULL AFTER descricao,
  ADD COLUMN IF NOT EXISTS diferencial TEXT DEFAULT NULL AFTER video_url_explicativo,
  ADD COLUMN IF NOT EXISTS conteudo_programatico TEXT DEFAULT NULL AFTER diferencial,
  ADD COLUMN IF NOT EXISTS publico_alvo TEXT DEFAULT NULL AFTER conteudo_programatico,
  ADD COLUMN IF NOT EXISTS condicoes TEXT DEFAULT NULL AFTER publico_alvo,
  ADD COLUMN IF NOT EXISTS vis_nome TINYINT(1) NOT NULL DEFAULT 1 AFTER condicoes,
  ADD COLUMN IF NOT EXISTS vis_breve_descricao TINYINT(1) NOT NULL DEFAULT 1 AFTER vis_nome,
  ADD COLUMN IF NOT EXISTS vis_carga_horaria TINYINT(1) NOT NULL DEFAULT 1 AFTER vis_breve_descricao,
  ADD COLUMN IF NOT EXISTS vis_valor TINYINT(1) NOT NULL DEFAULT 1 AFTER vis_carga_horaria,
  ADD COLUMN IF NOT EXISTS vis_descricao TINYINT(1) NOT NULL DEFAULT 1 AFTER vis_valor,
  ADD COLUMN IF NOT EXISTS vis_video TINYINT(1) NOT NULL DEFAULT 1 AFTER vis_descricao,
  ADD COLUMN IF NOT EXISTS vis_diferencial TINYINT(1) NOT NULL DEFAULT 1 AFTER vis_video,
  ADD COLUMN IF NOT EXISTS vis_conteudo TINYINT(1) NOT NULL DEFAULT 1 AFTER vis_diferencial,
  ADD COLUMN IF NOT EXISTS vis_publico_alvo TINYINT(1) NOT NULL DEFAULT 1 AFTER vis_conteudo,
  ADD COLUMN IF NOT EXISTS vis_condicoes TINYINT(1) NOT NULL DEFAULT 1 AFTER vis_publico_alvo;

CREATE TABLE IF NOT EXISTS curso_prerequisitos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  curso_id INT UNSIGNED NOT NULL,
  prerequisito_curso_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_curso_prereq (curso_id, prerequisito_curso_id),
  CONSTRAINT fk_prereq_curso FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
  CONSTRAINT fk_prereq_prereq FOREIGN KEY (prerequisito_curso_id) REFERENCES cursos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS aula_materiais (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  aula_id INT UNSIGNED NOT NULL,
  nome_arquivo VARCHAR(255) NOT NULL,
  caminho VARCHAR(500) NOT NULL,
  tamanho_bytes INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_material_aula FOREIGN KEY (aula_id) REFERENCES aulas(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (categorias.parent_id já foi adicionado no Lote A — ver histórico do chat/commit)
ALTER TABLE categorias
  ADD COLUMN IF NOT EXISTS parent_id INT UNSIGNED DEFAULT NULL AFTER slug;

-- Só cria a FK se ainda não existir (MySQL não suporta "ADD CONSTRAINT IF NOT EXISTS")
SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'categorias' AND CONSTRAINT_NAME = 'fk_categoria_parent'
);
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE categorias ADD CONSTRAINT fk_categoria_parent FOREIGN KEY (parent_id) REFERENCES categorias(id) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Lote C: Banco de Perguntas — imagem de apoio por pergunta
ALTER TABLE perguntas ADD COLUMN IF NOT EXISTS imagem_url VARCHAR(500) DEFAULT NULL AFTER texto;

-- Lote C: tempo de prova controlado pelo servidor
ALTER TABLE quiz_resposta ADD COLUMN IF NOT EXISTS iniciado_em DATETIME DEFAULT NULL AFTER aula_id;

-- Lote E: integração real ASAAS (opcional, cai no modo simulado sem chave)
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS asaas_customer_id VARCHAR(50) DEFAULT NULL AFTER documento;

CREATE TABLE IF NOT EXISTS configuracoes (
  chave VARCHAR(80) NOT NULL,
  valor VARCHAR(255) NOT NULL,
  PRIMARY KEY (chave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lote F: Combos (produto composto de 2+ cursos)
CREATE TABLE IF NOT EXISTS combos (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  titulo VARCHAR(250) NOT NULL,
  descricao TEXT DEFAULT NULL,
  thumb_url VARCHAR(500) DEFAULT NULL,
  preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  prazo_validade_dias INT UNSIGNED DEFAULT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  publico TINYINT(1) NOT NULL DEFAULT 0,
  disponivel_loja TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS combo_itens (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  combo_id INT UNSIGNED NOT NULL,
  curso_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_combo_curso (combo_id, curso_id),
  CONSTRAINT fk_comboitem_combo FOREIGN KEY (combo_id) REFERENCES combos(id) ON DELETE CASCADE,
  CONSTRAINT fk_comboitem_curso FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE itens_pedido ADD COLUMN IF NOT EXISTS combo_id INT UNSIGNED DEFAULT NULL AFTER curso_id;
ALTER TABLE itens_pedido MODIFY COLUMN curso_id INT UNSIGNED DEFAULT NULL;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'itens_pedido' AND CONSTRAINT_NAME = 'fk_item_combo'
);
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE itens_pedido ADD CONSTRAINT fk_item_combo FOREIGN KEY (combo_id) REFERENCES combos(id) ON DELETE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Valores padrão das regras de desconto (a tela Admin > Cupons > "Regras de
-- Desconto" já funciona sem isso, pois includes/configuracoes.php tem
-- fallback em PHP — isso só faz a tabela já nascer com os valores visíveis).
INSERT INTO configuracoes (chave, valor) VALUES
  ('cupom_indicacao_percentual', '10'),
  ('cupom_indicacao_validade_dias', '30'),
  ('desconto_fidelidade_percentual', '10'),
  ('desconto_progressivo_faixa1_min', '2'),
  ('desconto_progressivo_faixa1_max', '5'),
  ('desconto_progressivo_faixa1_percentual', '5'),
  ('desconto_progressivo_faixa2_min', '6'),
  ('desconto_progressivo_faixa2_max', '10'),
  ('desconto_progressivo_faixa2_percentual', '10'),
  ('desconto_progressivo_faixa3_min', '11'),
  ('desconto_progressivo_faixa3_percentual', '15')
ON DUPLICATE KEY UPDATE valor = valor;

-- Todo curso precisa de um "codigo" (o certificado usa [CODIGO]-[ID] como
-- código de autenticidade da validação pública — sem ele a validação nunca
-- encontra o certificado). Cursos cadastrados antes desta correção ficaram
-- com codigo NULL; preenche com um valor único e estável baseado no id.
-- Cursos novos já ganham um código automático em api/cursos/index.php.
UPDATE cursos SET codigo = CONCAT('C', LPAD(id, 6, '0')) WHERE codigo IS NULL OR codigo = '';
