-- Lote V15 do novo ciclo de ajustes do cliente (EscopoV14_02/V14_04/V15_01)

-- Garante que os textos acentuados deste arquivo sejam interpretados como
-- UTF-8 nesta sessão, independente da configuração padrão do cliente mysql
-- que rodar o import.
SET NAMES utf8mb4;

-- Corrige perguntas antigas com curso_id/modulo_id nulos (o filtro do Banco de
-- Questões dependia dessas colunas estarem preenchidas; o código agora resolve
-- sempre pela aula, mas isso já deixa o dado coerente também).
UPDATE perguntas p
JOIN aulas a ON a.id = p.aula_id
JOIN modulos m ON m.id = a.modulo_id
SET p.curso_id = m.curso_id, p.modulo_id = m.id
WHERE p.curso_id IS NULL OR p.modulo_id IS NULL;

-- "Incluído por": rastreia quem cadastrou cada usuário (gestor ou aluno),
-- pedido na coluna da lista de Gestores do painel do Gestor.
ALTER TABLE usuarios
  ADD COLUMN IF NOT EXISTS criado_por_id INT UNSIGNED DEFAULT NULL AFTER role;

SET @fk_exists = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND CONSTRAINT_NAME = 'fk_usuario_criado_por'
);
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE usuarios ADD CONSTRAINT fk_usuario_criado_por FOREIGN KEY (criado_por_id) REFERENCES usuarios(id) ON DELETE SET NULL',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Módulo Comercial (Admin > Pedidos / Clientes)
ALTER TABLE pedidos
  ADD COLUMN IF NOT EXISTS forma_pagamento VARCHAR(20) DEFAULT NULL AFTER situacao;

-- "Observação" interna do Admin na ficha do Cliente (Cliente = usuários que
-- pagaram um pedido ou lideram uma organização — não é uma tabela nova, é o
-- mesmo usuario/organizacao já existentes, só ganha esse campo extra).
ALTER TABLE usuarios
  ADD COLUMN IF NOT EXISTS observacao_admin TEXT DEFAULT NULL AFTER pais;

-- Exame Exemplar Global (QM/AU/TL): cada tipo é um produto próprio vinculado
-- a um treinamento, com preço configurável pelo Admin — substitui o antigo
-- toggle único "Com Prova Final (+R$150)".
CREATE TABLE IF NOT EXISTS exames_curso (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  curso_id INT UNSIGNED NOT NULL,
  tipo ENUM('QM', 'AU', 'TL') NOT NULL,
  preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_curso_tipo_exame (curso_id, tipo),
  CONSTRAINT fk_exame_curso FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE itens_pedido
  ADD COLUMN IF NOT EXISTS exames_selecionados VARCHAR(20) DEFAULT NULL AFTER com_prova;

ALTER TABLE matriculas
  ADD COLUMN IF NOT EXISTS exames_selecionados VARCHAR(20) DEFAULT NULL AFTER com_prova;

-- Corrige a acentuação corrompida das 5 perguntas padrão da Pesquisa de
-- Satisfação (ficaram com mojibake desde a migration original que as criou).
UPDATE pesquisa_perguntas SET texto = 'Como você avalia a clareza e organização do conteúdo do curso?' WHERE id = 1;
UPDATE pesquisa_perguntas SET texto = 'Como você avalia o domínio técnico e a didática do instrutor?' WHERE id = 2;
UPDATE pesquisa_perguntas SET texto = 'Como você avalia a qualidade do material de apoio disponibilizado?' WHERE id = 3;
UPDATE pesquisa_perguntas SET texto = 'Como você avalia a experiência de navegação e usabilidade da plataforma?' WHERE id = 4;
UPDATE pesquisa_perguntas SET texto = 'Qual é o seu nível de satisfação geral com o curso realizado?' WHERE id = 5;
