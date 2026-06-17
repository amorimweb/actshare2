SET NAMES utf8mb4;

INSERT IGNORE INTO categorias (nome, slug) VALUES
('Interpretação das Normas', 'interpretacao-das-normas'),
('Auditor Interno', 'auditor-interno'),
('Auditor Líder', 'auditor-lider'),
('Automotivo', 'automotivo'),
('Segurança da Informação', 'seguranca-da-informacao');

INSERT INTO instrutores (nome, qualificacao1, qualificacao2)
SELECT 'Equipe Técnica ActShare', 'Especialistas em sistemas de gestão', 'Normas ISO, IATF e auditorias'
WHERE NOT EXISTS (
  SELECT 1 FROM instrutores WHERE nome = 'Equipe Técnica ActShare'
);

SET @instrutor_id := (SELECT id FROM instrutores WHERE nome = 'Equipe Técnica ActShare' ORDER BY id LIMIT 1);
SET @cat_interpretacao := (SELECT id FROM categorias WHERE slug = 'interpretacao-das-normas' LIMIT 1);
SET @cat_auditor_interno := (SELECT id FROM categorias WHERE slug = 'auditor-interno' LIMIT 1);
SET @cat_auditor_lider := (SELECT id FROM categorias WHERE slug = 'auditor-lider' LIMIT 1);
SET @cat_automotivo := (SELECT id FROM categorias WHERE slug = 'automotivo' LIMIT 1);
SET @cat_seguranca := (SELECT id FROM categorias WHERE slug = 'seguranca-da-informacao' LIMIT 1);

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 14001:2026 - Interpretação da Norma',
       'Interpretação aplicada dos requisitos da ISO 14001:2026 para sistemas de gestão ambiental, com foco em implementação, evidências e conformidade.',
       'https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?w=900&q=80',
       1, 1, @cat_interpretacao, @instrutor_id, 297.00, 16, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 14001:2026 - Interpretação da Norma');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 9001:2015 - Interpretação da Norma',
       'Curso para compreender os requisitos da ISO 9001:2015 e sua aplicação em processos, indicadores, riscos, oportunidades e melhoria contínua.',
       'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=900&q=80',
       1, 1, @cat_interpretacao, @instrutor_id, 297.00, 16, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 9001:2015 - Interpretação da Norma');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 45001:2018 - Interpretação da Norma',
       'Estudo dos requisitos de saúde e segurança ocupacional da ISO 45001:2018, incluindo contexto, liderança, planejamento, operação e avaliação.',
       'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=900&q=80',
       1, 1, @cat_interpretacao, @instrutor_id, 297.00, 16, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 45001:2018 - Interpretação da Norma');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO/IEC 17025 - Interpretação da Norma',
       'Treinamento sobre os requisitos gerais para competência de laboratórios de ensaio e calibração conforme ISO/IEC 17025.',
       'https://images.unsplash.com/photo-1581093458791-9d09f535c4a3?w=900&q=80',
       1, 1, @cat_interpretacao, @instrutor_id, 297.00, 16, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO/IEC 17025 - Interpretação da Norma');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 14001:2026 - Auditor Interno 1ª e 2ª Parte',
       'Formação de auditor interno para auditorias de primeira e segunda parte em sistemas de gestão ambiental baseados na ISO 14001:2026.',
       'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=900&q=80',
       1, 1, @cat_auditor_interno, @instrutor_id, 397.00, 24, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 14001:2026 - Auditor Interno 1ª e 2ª Parte');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 9001:2015 - Auditor Interno 1ª e 2ª Parte',
       'Capacitação para planejar, executar, relatar e acompanhar auditorias internas e de fornecedores conforme ISO 9001:2015.',
       'https://images.unsplash.com/photo-1552664730-d307ca884978?w=900&q=80',
       1, 1, @cat_auditor_interno, @instrutor_id, 397.00, 24, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 9001:2015 - Auditor Interno 1ª e 2ª Parte');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 45001:2018 - Auditor Interno 1ª e 2ª Parte',
       'Curso de auditor interno para sistemas de gestão de saúde e segurança ocupacional, com práticas de auditoria e relatórios.',
       'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=900&q=80',
       1, 1, @cat_auditor_interno, @instrutor_id, 397.00, 24, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 45001:2018 - Auditor Interno 1ª e 2ª Parte');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 14001:2026 - Auditor Líder Exemplar Global',
       'Preparação para atuação como auditor líder em sistemas de gestão ambiental, alinhada às competências exigidas por organismos internacionais.',
       'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=900&q=80',
       1, 1, @cat_auditor_lider, @instrutor_id, 699.00, 40, 180
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 14001:2026 - Auditor Líder Exemplar Global');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 9001:2015 - Auditor Líder Exemplar Global',
       'Formação avançada de auditor líder para sistemas de gestão da qualidade, com abordagem de planejamento, condução e fechamento de auditorias.',
       'https://images.unsplash.com/photo-1560264280-88b68371db39?w=900&q=80',
       1, 1, @cat_auditor_lider, @instrutor_id, 699.00, 40, 180
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 9001:2015 - Auditor Líder Exemplar Global');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 45001:2018 - Auditor Líder Exemplar Global',
       'Capacitação para liderar auditorias de saúde e segurança ocupacional conforme ISO 45001:2018 e boas práticas internacionais.',
       'https://images.unsplash.com/photo-1521791136064-7986c2920216?w=900&q=80',
       1, 1, @cat_auditor_lider, @instrutor_id, 699.00, 40, 180
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 45001:2018 - Auditor Líder Exemplar Global');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'IATF 16949 - Interpretação da Norma',
       'Interpretação dos requisitos da IATF 16949 para cadeia automotiva, incluindo requisitos específicos de clientes e gestão de processos.',
       'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=900&q=80',
       1, 1, @cat_automotivo, @instrutor_id, 497.00, 24, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'IATF 16949 - Interpretação da Norma');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'APQP 3ª Edição',
       'Planejamento avançado da qualidade do produto com foco em desenvolvimento, validação, riscos, documentação e entregáveis do APQP.',
       'https://images.unsplash.com/photo-1581090700227-1e37b190418e?w=900&q=80',
       1, 1, @cat_automotivo, @instrutor_id, 297.00, 12, 90
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'APQP 3ª Edição');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'FMEA AIAG & VDA',
       'Aplicação prática do FMEA AIAG & VDA para análise de riscos, priorização de ações e integração com desenvolvimento de produto e processo.',
       'https://images.unsplash.com/photo-1581092334651-ddf26d9a09d0?w=900&q=80',
       1, 1, @cat_automotivo, @instrutor_id, 297.00, 16, 90
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'FMEA AIAG & VDA');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'MAS - Measurement System Analysis',
       'Treinamento de análise de sistemas de medição para avaliação de repetibilidade, reprodutibilidade, tendência, linearidade e estabilidade.',
       'https://images.unsplash.com/photo-1581091215367-59ab6b3625f4?w=900&q=80',
       1, 1, @cat_automotivo, @instrutor_id, 247.00, 12, 90
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'MAS - Measurement System Analysis');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'CEP - Controle Estatístico do Processo',
       'Fundamentos e aplicação do CEP para monitoramento, estabilidade, capacidade de processo e tomada de decisão baseada em dados.',
       'https://images.unsplash.com/photo-1543286386-2e659306cd6c?w=900&q=80',
       1, 1, @cat_automotivo, @instrutor_id, 247.00, 12, 90
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'CEP - Controle Estatístico do Processo');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'PPAP - Processo de Aprovação de Peça de Produção',
       'Curso sobre requisitos, níveis de submissão, documentação e critérios para aprovação de peças de produção no setor automotivo.',
       'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=900&q=80',
       1, 1, @cat_automotivo, @instrutor_id, 247.00, 12, 90
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'PPAP - Processo de Aprovação de Peça de Produção');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'CQI-09 - Tratamento Térmico',
       'Requisitos especiais CQI-09 para avaliação de processos de tratamento térmico na cadeia automotiva.',
       'https://images.unsplash.com/photo-1516937941344-00b4e0337589?w=900&q=80',
       1, 1, @cat_automotivo, @instrutor_id, 297.00, 12, 90
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'CQI-09 - Tratamento Térmico');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'CQI-11 - Tratamento Superficial',
       'Treinamento sobre requisitos CQI-11 para avaliação e controle de processos de tratamento superficial.',
       'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=900&q=80',
       1, 1, @cat_automotivo, @instrutor_id, 297.00, 12, 90
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'CQI-11 - Tratamento Superficial');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'CQI-14 - Gestão de Garantia Automotiva',
       'Aplicação dos requisitos CQI-14 para gestão de garantia, análise de falhas, retorno de campo e melhoria de processos automotivos.',
       'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=900&q=80',
       1, 1, @cat_automotivo, @instrutor_id, 297.00, 12, 90
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'CQI-14 - Gestão de Garantia Automotiva');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 27001 - Interpretação da Norma',
       'Interpretação dos requisitos da ISO 27001 para implantação, manutenção e melhoria de sistemas de gestão de segurança da informação.',
       'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=900&q=80',
       1, 1, @cat_seguranca, @instrutor_id, 397.00, 20, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 27001 - Interpretação da Norma');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'ISO 27701 - Interpretação da Norma',
       'Curso de interpretação da ISO 27701 para gestão de informações de privacidade e integração com sistemas de segurança da informação.',
       'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=900&q=80',
       1, 1, @cat_seguranca, @instrutor_id, 397.00, 20, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'ISO 27701 - Interpretação da Norma');

INSERT INTO cursos (titulo, descricao, thumb_url, ativo, publico, categoria_id, instrutor_id, preco, carga_horaria_horas, prazo_conclusao_dias)
SELECT 'TISAX VDA ISA - Interpretação',
       'Treinamento sobre TISAX e catálogo VDA ISA para avaliação de segurança da informação na cadeia automotiva.',
       'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=900&q=80',
       1, 1, @cat_seguranca, @instrutor_id, 397.00, 20, 120
WHERE NOT EXISTS (SELECT 1 FROM cursos WHERE titulo = 'TISAX VDA ISA - Interpretação');
