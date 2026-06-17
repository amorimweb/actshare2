SET NAMES utf8mb4;

-- Execute uma vez no phpMyAdmin depois de fazer backup.
-- Corrige textos salvos com mojibake, por exemplo: Gestao quebrada, Protecao quebrada, Automacao quebrada.

UPDATE categorias
SET nome = CONVERT(BINARY CONVERT(nome USING latin1) USING utf8mb4)
WHERE nome LIKE '%Ã%' OR nome LIKE '%Â%';

UPDATE instrutores
SET nome = CASE WHEN nome LIKE '%Ã%' OR nome LIKE '%Â%' THEN CONVERT(BINARY CONVERT(nome USING latin1) USING utf8mb4) ELSE nome END,
    qualificacao1 = CASE WHEN qualificacao1 LIKE '%Ã%' OR qualificacao1 LIKE '%Â%' THEN CONVERT(BINARY CONVERT(qualificacao1 USING latin1) USING utf8mb4) ELSE qualificacao1 END,
    qualificacao2 = CASE WHEN qualificacao2 LIKE '%Ã%' OR qualificacao2 LIKE '%Â%' THEN CONVERT(BINARY CONVERT(qualificacao2 USING latin1) USING utf8mb4) ELSE qualificacao2 END
WHERE nome LIKE '%Ã%' OR nome LIKE '%Â%'
   OR qualificacao1 LIKE '%Ã%' OR qualificacao1 LIKE '%Â%'
   OR qualificacao2 LIKE '%Ã%' OR qualificacao2 LIKE '%Â%';

UPDATE cursos
SET titulo = CASE WHEN titulo LIKE '%Ã%' OR titulo LIKE '%Â%' THEN CONVERT(BINARY CONVERT(titulo USING latin1) USING utf8mb4) ELSE titulo END,
    descricao = CASE WHEN descricao LIKE '%Ã%' OR descricao LIKE '%Â%' THEN CONVERT(BINARY CONVERT(descricao USING latin1) USING utf8mb4) ELSE descricao END
WHERE titulo LIKE '%Ã%' OR titulo LIKE '%Â%'
   OR descricao LIKE '%Ã%' OR descricao LIKE '%Â%';

UPDATE modulos
SET titulo = CONVERT(BINARY CONVERT(titulo USING latin1) USING utf8mb4)
WHERE titulo LIKE '%Ã%' OR titulo LIKE '%Â%';

UPDATE aulas
SET titulo = CASE WHEN titulo LIKE '%Ã%' OR titulo LIKE '%Â%' THEN CONVERT(BINARY CONVERT(titulo USING latin1) USING utf8mb4) ELSE titulo END,
    descricao = CASE WHEN descricao LIKE '%Ã%' OR descricao LIKE '%Â%' THEN CONVERT(BINARY CONVERT(descricao USING latin1) USING utf8mb4) ELSE descricao END
WHERE titulo LIKE '%Ã%' OR titulo LIKE '%Â%'
   OR descricao LIKE '%Ã%' OR descricao LIKE '%Â%';

UPDATE perguntas
SET texto = CASE WHEN texto LIKE '%Ã%' OR texto LIKE '%Â%' THEN CONVERT(BINARY CONVERT(texto USING latin1) USING utf8mb4) ELSE texto END,
    justificativa = CASE WHEN justificativa LIKE '%Ã%' OR justificativa LIKE '%Â%' THEN CONVERT(BINARY CONVERT(justificativa USING latin1) USING utf8mb4) ELSE justificativa END
WHERE texto LIKE '%Ã%' OR texto LIKE '%Â%'
   OR justificativa LIKE '%Ã%' OR justificativa LIKE '%Â%';

UPDATE opcoes
SET texto = CONVERT(BINARY CONVERT(texto USING latin1) USING utf8mb4)
WHERE texto LIKE '%Ã%' OR texto LIKE '%Â%';

UPDATE usuarios
SET nome = CASE WHEN nome LIKE '%Ã%' OR nome LIKE '%Â%' THEN CONVERT(BINARY CONVERT(nome USING latin1) USING utf8mb4) ELSE nome END,
    razao_social = CASE WHEN razao_social LIKE '%Ã%' OR razao_social LIKE '%Â%' THEN CONVERT(BINARY CONVERT(razao_social USING latin1) USING utf8mb4) ELSE razao_social END,
    endereco = CASE WHEN endereco LIKE '%Ã%' OR endereco LIKE '%Â%' THEN CONVERT(BINARY CONVERT(endereco USING latin1) USING utf8mb4) ELSE endereco END,
    complemento = CASE WHEN complemento LIKE '%Ã%' OR complemento LIKE '%Â%' THEN CONVERT(BINARY CONVERT(complemento USING latin1) USING utf8mb4) ELSE complemento END,
    bairro = CASE WHEN bairro LIKE '%Ã%' OR bairro LIKE '%Â%' THEN CONVERT(BINARY CONVERT(bairro USING latin1) USING utf8mb4) ELSE bairro END,
    cidade = CASE WHEN cidade LIKE '%Ã%' OR cidade LIKE '%Â%' THEN CONVERT(BINARY CONVERT(cidade USING latin1) USING utf8mb4) ELSE cidade END
WHERE nome LIKE '%Ã%' OR nome LIKE '%Â%'
   OR razao_social LIKE '%Ã%' OR razao_social LIKE '%Â%'
   OR endereco LIKE '%Ã%' OR endereco LIKE '%Â%'
   OR complemento LIKE '%Ã%' OR complemento LIKE '%Â%'
   OR bairro LIKE '%Ã%' OR bairro LIKE '%Â%'
   OR cidade LIKE '%Ã%' OR cidade LIKE '%Â%';
