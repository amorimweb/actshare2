-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: actshare
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aulas`
--

DROP TABLE IF EXISTS `aulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aulas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `modulo_id` int(10) unsigned NOT NULL,
  `titulo` varchar(250) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('video','texto','quiz','pdf') NOT NULL DEFAULT 'video',
  `video_url` varchar(500) DEFAULT NULL,
  `publica` tinyint(1) NOT NULL DEFAULT 0,
  `ordem` int(10) unsigned NOT NULL DEFAULT 0,
  `duracao_min` int(10) unsigned NOT NULL DEFAULT 0,
  `quizz_qtd_perguntas` int(10) unsigned NOT NULL DEFAULT 1,
  `e_prova` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_aula_modulo` (`modulo_id`),
  CONSTRAINT `fk_aula_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aulas`
--

LOCK TABLES `aulas` WRITE;
/*!40000 ALTER TABLE `aulas` DISABLE KEYS */;
INSERT INTO `aulas` VALUES (1,1,'O que é um projeto?',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,12,1,0,'2026-05-05 22:35:34'),(2,1,'Ciclo de vida de um projeto',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,2,18,1,0,'2026-05-05 22:35:34'),(3,1,'O papel do gerente de projetos',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,3,15,1,0,'2026-05-05 22:35:34'),(4,1,'Quiz — Fundamentos',NULL,'quiz',NULL,0,4,0,1,0,'2026-05-05 22:35:34'),(5,2,'Manifesto Ágil e seus princípios',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,20,1,0,'2026-05-05 22:35:34'),(6,2,'Scrum: papéis, eventos e artefatos',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,2,25,1,0,'2026-05-05 22:35:34'),(7,2,'Kanban na prática',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,3,18,1,0,'2026-05-05 22:35:34'),(8,2,'Quiz — Metodologias Ágeis',NULL,'quiz',NULL,0,4,0,1,0,'2026-05-05 22:35:34'),(9,5,'O que é a LGPD e por que ela existe?',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,10,1,0,'2026-05-05 22:35:34'),(10,5,'Conceitos fundamentais: dado, tratamento…',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,2,14,1,0,'2026-05-05 22:35:34'),(11,5,'Bases legais para tratamento de dados',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,3,16,1,0,'2026-05-05 22:35:34'),(12,5,'Quiz — Introdução à LGPD',NULL,'quiz',NULL,0,4,0,1,0,'2026-05-05 22:35:34'),(13,6,'Direitos dos titulares de dados',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,12,1,0,'2026-05-05 22:35:34'),(14,6,'Como responder às solicitações dos titulares',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,2,15,1,0,'2026-05-05 22:35:34'),(15,6,'Quiz — Direitos dos Titulares',NULL,'quiz',NULL,0,3,0,1,0,'2026-05-05 22:35:34'),(16,9,'Instalação e configuração do ambiente',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,15,1,0,'2026-05-05 22:35:34'),(17,9,'Variáveis, tipos e operadores',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,2,20,1,0,'2026-05-05 22:35:34'),(18,9,'Condicionais e loops',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,3,22,1,0,'2026-05-05 22:35:34'),(19,9,'Funções e módulos',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,4,25,1,0,'2026-05-05 22:35:34'),(20,9,'Quiz — Python Básico',NULL,'quiz',NULL,0,5,0,1,0,'2026-05-05 22:35:34'),(21,20,'Conceitos Iniciais da ISO 27001',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,20,1,0,'2026-05-27 10:41:57'),(22,20,'Quiz de Fixação — Auditoria ISO 27001',NULL,'quiz',NULL,0,2,0,2,0,'2026-05-27 10:41:57'),(23,20,'Avaliação Final de Certificação (Exame Monitorado)',NULL,'quiz',NULL,0,3,0,3,1,'2026-05-27 10:41:57');
/*!40000 ALTER TABLE `aulas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `slug` varchar(120) DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Gestão','gestao',NULL,'2026-05-06 01:14:24'),(2,'Compliance','compliance',NULL,'2026-05-06 01:14:24'),(3,'Tecnologia','tecnologia',NULL,'2026-05-06 01:14:24'),(4,'Soft Skills','soft-skills',NULL,'2026-05-06 01:14:24'),(5,'Jurídico','juridico',NULL,'2026-05-06 01:14:24'),(6,'Interpretação das Normas','interpretacao-das-normas',NULL,'2026-05-30 12:08:35'),(7,'Auditor Interno','auditor-interno',NULL,'2026-05-30 12:08:35'),(8,'Auditor Líder','auditor-lider',NULL,'2026-05-30 12:08:35'),(9,'Automotivo','automotivo',NULL,'2026-05-30 12:08:35'),(10,'Segurança da Informação','seguranca-da-informacao',NULL,'2026-05-30 12:08:35');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cupons`
--

DROP TABLE IF EXISTS `cupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cupons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `tipo` enum('fixo','porcentagem') NOT NULL DEFAULT 'porcentagem',
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `validade` datetime NOT NULL,
  `limite_uso` int(10) unsigned DEFAULT NULL,
  `usos` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cupons`
--

LOCK TABLES `cupons` WRITE;
/*!40000 ALTER TABLE `cupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `cupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cupons_indicacao`
--

DROP TABLE IF EXISTS `cupons_indicacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cupons_indicacao` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `indicador_id` int(10) unsigned NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `percentual` int(10) unsigned NOT NULL DEFAULT 10,
  `validade` datetime NOT NULL,
  `utilizado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ind_codigo` (`codigo`),
  KEY `fk_indicador_usuario` (`indicador_id`),
  CONSTRAINT `fk_indicador_usuario` FOREIGN KEY (`indicador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cupons_indicacao`
--

LOCK TABLES `cupons_indicacao` WRITE;
/*!40000 ALTER TABLE `cupons_indicacao` DISABLE KEYS */;
INSERT INTO `cupons_indicacao` VALUES (1,6,'REF-JT571X',10,'2026-06-26 15:58:19',0,'2026-05-27 10:58:19'),(2,7,'REF-C4F3IE',10,'2026-06-26 16:47:58',0,'2026-05-27 11:47:58');
/*!40000 ALTER TABLE `cupons_indicacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cursos`
--

DROP TABLE IF EXISTS `cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cursos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(250) NOT NULL,
  `descricao` text DEFAULT NULL,
  `thumb_url` varchar(500) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `publico` tinyint(1) NOT NULL DEFAULT 0,
  `categoria_id` int(10) unsigned DEFAULT NULL,
  `instrutor_id` int(10) unsigned DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL DEFAULT 0.00,
  `carga_horaria_horas` int(10) unsigned NOT NULL DEFAULT 0,
  `prazo_conclusao_dias` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_curso_categoria` (`categoria_id`),
  KEY `fk_curso_instrutor` (`instrutor_id`),
  CONSTRAINT `fk_curso_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_curso_instrutor` FOREIGN KEY (`instrutor_id`) REFERENCES `instrutores` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos`
--

LOCK TABLES `cursos` WRITE;
/*!40000 ALTER TABLE `cursos` DISABLE KEYS */;
INSERT INTO `cursos` VALUES (1,'Gestão de Projetos na Prática','Aprenda a planejar, executar e controlar projetos com metodologias ágeis e tradicionais. Domine ferramentas como Kanban, Scrum e PMBOK aplicados ao dia a dia corporativo.','https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=600&q=80',1,1,1,1,0.00,20,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(2,'LGPD na Prática: Proteja sua Empresa','Entenda todos os aspectos da Lei Geral de Proteção de Dados. Do conceito à implementação, capacite sua equipe para evitar multas e garantir conformidade.','https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&q=80',1,1,2,2,0.00,8,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(3,'Python para Automação de Processos','Do zero ao automatizador: aprenda Python focado em automação de tarefas repetitivas, relatórios automáticos e integração de sistemas.','https://images.unsplash.com/photo-1526379095098-d400fd0bf935?w=600&q=80',1,1,3,3,0.00,24,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(4,'Liderança e Comunicação Assertiva','Desenvolva habilidades de liderança, comunicação e gestão de pessoas. Aprenda a engajar equipes, dar feedbacks eficazes e resolver conflitos.','https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&q=80',1,1,4,4,0.00,12,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(5,'Compliance e Ética nos Negócios','Estruture um programa de compliance robusto. Políticas anticorrupção, código de conduta, canal de denúncias e gestão de riscos.','https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&q=80',1,1,2,2,0.00,16,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(6,'Excel Avançado para Gestores','Domine as funções avançadas do Excel: tabelas dinâmicas, Power Query, dashboards e automação com macros VBA voltados para tomada de decisão.','https://images.unsplash.com/photo-1543286386-2e659306cd6c?w=600&q=80',1,1,1,1,0.00,10,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(7,'Segurança do Trabalho — NR Essenciais','Normas Regulamentadoras obrigatórias: NR-1, NR-6, NR-17 e NR-35. Capacite sua equipe e mantenha sua empresa em conformidade legal.','https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&q=80',1,1,5,5,0.00,8,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(8,'Inteligência Artificial no Trabalho','Entenda como usar ferramentas de IA (ChatGPT, Copilot, Gemini) para aumentar sua produtividade, criar conteúdos e automatizar tarefas do dia a dia.','https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=600&q=80',1,1,3,3,0.00,6,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(9,'Certificação Auditor Líder ISO 27001 (Exemplar Global)','Curso preparatório completo voltado para a auditoria de sistemas de gestão de segurança da informação (SGSI) com exames simulados e prova de certificação monitorada de acordo com as regras de conformidade internacional.','https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600&q=80',1,1,2,2,499.00,40,180,'2026-05-27 10:41:57','2026-05-27 10:41:57'),(10,'ISO 14001:2026 - Interpretação da Norma','Interpretação aplicada dos requisitos da ISO 14001:2026 para sistemas de gestão ambiental, com foco em implementação, evidências e conformidade.','https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?w=900&q=80',1,1,6,6,297.00,16,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(11,'ISO 9001:2015 - Interpretação da Norma','Curso para compreender os requisitos da ISO 9001:2015 e sua aplicação em processos, indicadores, riscos, oportunidades e melhoria contínua.','https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=900&q=80',1,1,6,6,297.00,16,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(12,'ISO 45001:2018 - Interpretação da Norma','Estudo dos requisitos de saúde e segurança ocupacional da ISO 45001:2018, incluindo contexto, liderança, planejamento, operação e avaliação.','https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=900&q=80',1,1,6,6,297.00,16,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(13,'ISO/IEC 17025 - Interpretação da Norma','Treinamento sobre os requisitos gerais para competência de laboratórios de ensaio e calibração conforme ISO/IEC 17025.','https://images.unsplash.com/photo-1581093458791-9d09f535c4a3?w=900&q=80',1,1,6,6,297.00,16,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(14,'ISO 14001:2026 - Auditor Interno 1ª e 2ª Parte','Formação de auditor interno para auditorias de primeira e segunda parte em sistemas de gestão ambiental baseados na ISO 14001:2026.','https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=900&q=80',1,1,7,6,397.00,24,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(15,'ISO 9001:2015 - Auditor Interno 1ª e 2ª Parte','Capacitação para planejar, executar, relatar e acompanhar auditorias internas e de fornecedores conforme ISO 9001:2015.','https://images.unsplash.com/photo-1552664730-d307ca884978?w=900&q=80',1,1,7,6,397.00,24,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(16,'ISO 45001:2018 - Auditor Interno 1ª e 2ª Parte','Curso de auditor interno para sistemas de gestão de saúde e segurança ocupacional, com práticas de auditoria e relatórios.','https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=900&q=80',1,1,7,6,397.00,24,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(17,'ISO 14001:2026 - Auditor Líder Exemplar Global','Preparação para atuação como auditor líder em sistemas de gestão ambiental, alinhada às competências exigidas por organismos internacionais.','https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=900&q=80',1,1,8,6,699.00,40,180,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(18,'ISO 9001:2015 - Auditor Líder Exemplar Global','Formação avançada de auditor líder para sistemas de gestão da qualidade, com abordagem de planejamento, condução e fechamento de auditorias.','https://images.unsplash.com/photo-1560264280-88b68371db39?w=900&q=80',1,1,8,6,699.00,40,180,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(19,'ISO 45001:2018 - Auditor Líder Exemplar Global','Capacitação para liderar auditorias de saúde e segurança ocupacional conforme ISO 45001:2018 e boas práticas internacionais.','https://images.unsplash.com/photo-1521791136064-7986c2920216?w=900&q=80',1,1,8,6,699.00,40,180,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(20,'IATF 16949 - Interpretação da Norma','Interpretação dos requisitos da IATF 16949 para cadeia automotiva, incluindo requisitos específicos de clientes e gestão de processos.','https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=900&q=80',1,1,9,6,497.00,24,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(21,'APQP 3ª Edição','Planejamento avançado da qualidade do produto com foco em desenvolvimento, validação, riscos, documentação e entregáveis do APQP.','https://images.unsplash.com/photo-1581090700227-1e37b190418e?w=900&q=80',1,1,9,6,297.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(22,'FMEA AIAG & VDA','Aplicação prática do FMEA AIAG & VDA para análise de riscos, priorização de ações e integração com desenvolvimento de produto e processo.','https://images.unsplash.com/photo-1581092334651-ddf26d9a09d0?w=900&q=80',1,1,9,6,297.00,16,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(23,'MAS - Measurement System Analysis','Treinamento de análise de sistemas de medição para avaliação de repetibilidade, reprodutibilidade, tendência, linearidade e estabilidade.','https://images.unsplash.com/photo-1581091215367-59ab6b3625f4?w=900&q=80',1,1,9,6,247.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(24,'CEP - Controle Estatístico do Processo','Fundamentos e aplicação do CEP para monitoramento, estabilidade, capacidade de processo e tomada de decisão baseada em dados.','https://images.unsplash.com/photo-1543286386-2e659306cd6c?w=900&q=80',1,1,9,6,247.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(25,'PPAP - Processo de Aprovação de Peça de Produção','Curso sobre requisitos, níveis de submissão, documentação e critérios para aprovação de peças de produção no setor automotivo.','https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=900&q=80',1,1,9,6,247.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(26,'CQI-09 - Tratamento Térmico','Requisitos especiais CQI-09 para avaliação de processos de tratamento térmico na cadeia automotiva.','https://images.unsplash.com/photo-1516937941344-00b4e0337589?w=900&q=80',1,1,9,6,297.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(27,'CQI-11 - Tratamento Superficial','Treinamento sobre requisitos CQI-11 para avaliação e controle de processos de tratamento superficial.','https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=900&q=80',1,1,9,6,297.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(28,'CQI-14 - Gestão de Garantia Automotiva','Aplicação dos requisitos CQI-14 para gestão de garantia, análise de falhas, retorno de campo e melhoria de processos automotivos.','https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=900&q=80',1,1,9,6,297.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(29,'ISO 27001 - Interpretação da Norma','Interpretação dos requisitos da ISO 27001 para implantação, manutenção e melhoria de sistemas de gestão de segurança da informação.','https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=900&q=80',1,1,10,6,397.00,20,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(30,'ISO 27701 - Interpretação da Norma','Curso de interpretação da ISO 27701 para gestão de informações de privacidade e integração com sistemas de segurança da informação.','https://images.unsplash.com/photo-1563986768609-322da13575f3?w=900&q=80',1,1,10,6,397.00,20,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(31,'TISAX VDA ISA - Interpretação','Treinamento sobre TISAX e catálogo VDA ISA para avaliação de segurança da informação na cadeia automotiva.','https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=900&q=80',1,1,10,6,397.00,20,120,'2026-05-30 12:08:35','2026-05-30 12:08:35');
/*!40000 ALTER TABLE `cursos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instrutores`
--

DROP TABLE IF EXISTS `instrutores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instrutores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `qualificacao1` varchar(200) DEFAULT NULL,
  `qualificacao2` varchar(200) DEFAULT NULL,
  `avatar_url` varchar(500) DEFAULT NULL,
  `assinatura_url` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instrutores`
--

LOCK TABLES `instrutores` WRITE;
/*!40000 ALTER TABLE `instrutores` DISABLE KEYS */;
INSERT INTO `instrutores` VALUES (1,'Dr. Carlos Mendes','Doutor em Gestão Empresarial','MBA em Liderança e Inovação',NULL,NULL,'2026-05-05 22:35:34'),(2,'Ana Paula Rocha','Especialista em Compliance e LGPD','Advogada com 15 anos de experiência',NULL,NULL,'2026-05-05 22:35:34'),(3,'Rafael Sousa','Engenheiro de Software Sênior','Certificado AWS e Google Cloud',NULL,NULL,'2026-05-05 22:35:34'),(4,'Mariana Figueiredo','Mestre em Psicologia Organizacional','Coach Executivo Certificado',NULL,NULL,'2026-05-05 22:35:34'),(5,'João Augusto Lima','Especialista em Segurança do Trabalho','Pós-graduado em Gestão de Riscos',NULL,NULL,'2026-05-05 22:35:34'),(6,'Equipe Técnica ActShare','Especialistas em sistemas de gestão','Normas ISO, IATF e auditorias',NULL,NULL,'2026-05-30 12:08:35');
/*!40000 ALTER TABLE `instrutores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itens_pedido`
--

DROP TABLE IF EXISTS `itens_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itens_pedido` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` int(10) unsigned NOT NULL,
  `curso_id` int(10) unsigned NOT NULL,
  `vagas` int(10) unsigned NOT NULL DEFAULT 1,
  `preco_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  `com_prova` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_item_pedido` (`pedido_id`),
  KEY `fk_item_curso` (`curso_id`),
  CONSTRAINT `fk_item_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itens_pedido`
--

LOCK TABLES `itens_pedido` WRITE;
/*!40000 ALTER TABLE `itens_pedido` DISABLE KEYS */;
INSERT INTO `itens_pedido` VALUES (1,1,9,1,649.00,1),(2,2,9,2,649.00,1),(3,3,9,1,649.00,1);
/*!40000 ALTER TABLE `itens_pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `matriculas`
--

DROP TABLE IF EXISTS `matriculas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `matriculas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aluno_id` int(10) unsigned NOT NULL,
  `curso_id` int(10) unsigned NOT NULL,
  `data_inicio` datetime NOT NULL DEFAULT current_timestamp(),
  `data_fim_acesso` datetime DEFAULT NULL,
  `progresso_total` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `concluido` tinyint(1) NOT NULL DEFAULT 0,
  `data_conclusao` datetime DEFAULT NULL,
  `vagas_usadas` int(10) unsigned NOT NULL DEFAULT 1,
  `vagas_totais` int(10) unsigned DEFAULT NULL,
  `participante` tinyint(1) NOT NULL DEFAULT 0,
  `com_prova` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_aluno_curso` (`aluno_id`,`curso_id`),
  KEY `fk_matricula_curso` (`curso_id`),
  CONSTRAINT `fk_matricula_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_matricula_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matriculas`
--

LOCK TABLES `matriculas` WRITE;
/*!40000 ALTER TABLE `matriculas` DISABLE KEYS */;
INSERT INTO `matriculas` VALUES (1,6,9,'2026-05-27 10:58:19','2026-11-23 14:58:19',0,0,NULL,1,NULL,0,1,'2026-05-27 10:58:19'),(2,7,9,'2026-05-27 11:47:58','2026-11-23 15:47:58',0,0,NULL,1,NULL,0,1,'2026-05-27 11:47:58'),(3,7,1,'2026-05-27 12:12:20',NULL,0,0,NULL,1,NULL,0,0,'2026-05-27 12:12:20'),(4,1,1,'2026-05-30 12:32:54',NULL,0,0,NULL,1,NULL,0,0,'2026-05-30 12:32:54'),(5,9,1,'2026-05-30 12:39:41',NULL,25,0,NULL,1,NULL,0,0,'2026-05-30 12:39:41'),(6,9,2,'2026-05-30 12:39:41',NULL,60,0,NULL,1,NULL,0,0,'2026-05-30 12:39:41'),(7,9,9,'2026-05-30 12:39:41',NULL,10,0,NULL,1,NULL,0,0,'2026-05-30 12:39:41');
/*!40000 ALTER TABLE `matriculas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `membros_organizacao`
--

DROP TABLE IF EXISTS `membros_organizacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `membros_organizacao` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `organizacao_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_org_usuario` (`organizacao_id`,`usuario_id`),
  KEY `fk_membro_usuario` (`usuario_id`),
  CONSTRAINT `fk_membro_org` FOREIGN KEY (`organizacao_id`) REFERENCES `organizacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_membro_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `membros_organizacao`
--

LOCK TABLES `membros_organizacao` WRITE;
/*!40000 ALTER TABLE `membros_organizacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `membros_organizacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modulos`
--

DROP TABLE IF EXISTS `modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `modulos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `curso_id` int(10) unsigned NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `ordem` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_modulo_curso` (`curso_id`),
  CONSTRAINT `fk_modulo_curso` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modulos`
--

LOCK TABLES `modulos` WRITE;
/*!40000 ALTER TABLE `modulos` DISABLE KEYS */;
INSERT INTO `modulos` VALUES (1,1,'Fundamentos de Gerenciamento de Projetos',1,'2026-05-05 22:35:34'),(2,1,'Metodologias Ágeis — Scrum e Kanban',2,'2026-05-05 22:35:34'),(3,1,'Planejamento e Escopo',3,'2026-05-05 22:35:34'),(4,1,'Gestão de Riscos e Qualidade',4,'2026-05-05 22:35:34'),(5,2,'Introdução à LGPD',1,'2026-05-05 22:35:34'),(6,2,'Direitos dos Titulares',2,'2026-05-05 22:35:34'),(7,2,'Obrigações das Empresas',3,'2026-05-05 22:35:34'),(8,2,'Implementação Prática',4,'2026-05-05 22:35:34'),(9,3,'Python do Zero',1,'2026-05-05 22:35:34'),(10,3,'Manipulação de Arquivos e Planilhas',2,'2026-05-05 22:35:34'),(11,3,'Automação Web com Selenium',3,'2026-05-05 22:35:34'),(12,3,'Integração com APIs REST',4,'2026-05-05 22:35:34'),(13,4,'Autoconhecimento e Estilos de Liderança',1,'2026-05-05 22:35:34'),(14,4,'Comunicação Assertiva',2,'2026-05-05 22:35:34'),(15,4,'Gestão de Equipes e Conflitos',3,'2026-05-05 22:35:34'),(16,5,'Fundamentos de Compliance',1,'2026-05-05 22:35:34'),(17,5,'Lei Anticorrupção',2,'2026-05-05 22:35:34'),(18,5,'Programa de Integridade',3,'2026-05-05 22:35:34'),(19,5,'Gestão de Riscos Corporativos',4,'2026-05-05 22:35:34'),(20,9,'Diretrizes de Auditoria e Conformidade',1,'2026-05-27 10:41:57');
/*!40000 ALTER TABLE `modulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opcoes`
--

DROP TABLE IF EXISTS `opcoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opcoes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pergunta_id` int(10) unsigned NOT NULL,
  `texto` varchar(500) NOT NULL,
  `correta` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_opcao_pergunta` (`pergunta_id`),
  CONSTRAINT `fk_opcao_pergunta` FOREIGN KEY (`pergunta_id`) REFERENCES `perguntas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opcoes`
--

LOCK TABLES `opcoes` WRITE;
/*!40000 ALTER TABLE `opcoes` DISABLE KEYS */;
INSERT INTO `opcoes` VALUES (1,1,'Um trabalho repetitivo e contínuo',0),(2,1,'Um esforço temporário para criar um produto ou resultado único',1),(3,1,'Qualquer tarefa do dia a dia da empresa',0),(4,1,'Uma reunião de planejamento',0),(5,2,'Escrever o código do sistema',0),(6,2,'Garantir que o projeto seja entregue no prazo',0),(7,2,'Integrar pessoas, processos e tecnologia',1),(8,2,'Contratar os membros da equipe',0),(9,3,'Iniciação',0),(10,3,'Planejamento',0),(11,3,'Produção',1),(12,3,'Encerramento',0),(13,4,'O funcionário que executa os backups diários da segurança.',0),(14,4,'A pessoa responsável por gerenciar e liderar a equipe de auditoria.',1),(15,4,'O diretor executivo da empresa parceira externa.',0),(16,4,'Nenhuma das anteriores.',0),(17,5,'1 a 2 dias dependendo do porte da organização.',1),(18,5,'6 meses ininterruptos de análise profunda.',0),(19,5,'Apenas 10 minutos de conferência rápida.',0),(20,5,'Todas as alternativas anteriores estão corretas.',0),(21,6,'Uma medida ou prática adotada para mitigar e gerenciar riscos de segurança.',1),(22,6,'Uma fechadura eletrônica instalada na porta principal de TI.',0),(23,6,'Um software antivírus instalado nos notebooks da administração.',0),(24,6,'Todas as alternativas anteriores estão corretas.',0),(25,7,'Em intervalos planejados e periódicos definidos pela organização.',1),(26,7,'Somente após a ocorrência de incidentes ou vazamentos de dados graves.',0),(27,7,'Uma vez a cada 10 anos para revalidação do SGSI.',0),(28,7,'Nenhuma das alternativas anteriores.',0),(29,8,'A ausência total ou falha sistemática de aplicação de um requisito obrigatório da norma.',1),(30,8,'Um erro de digitação simples em um cabeçalho do manual de segurança.',0),(31,8,'Deixar a tela do computador desbloqueada em uma única ausência rápida.',0),(32,8,'Nenhuma das anteriores.',0);
/*!40000 ALTER TABLE `opcoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `organizacoes`
--

DROP TABLE IF EXISTS `organizacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `organizacoes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gestor_id` int(10) unsigned NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_org_gestor` (`gestor_id`),
  CONSTRAINT `fk_org_gestor` FOREIGN KEY (`gestor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `organizacoes`
--

LOCK TABLES `organizacoes` WRITE;
/*!40000 ALTER TABLE `organizacoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `organizacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedidos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL,
  `total_bruto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `desconto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_liquido` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cupom_id` int(10) unsigned DEFAULT NULL,
  `situacao` enum('pendente','pago','cancelado') NOT NULL DEFAULT 'pendente',
  `asaas_id` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pedido_usuario` (`usuario_id`),
  KEY `fk_pedido_cupom` (`cupom_id`),
  CONSTRAINT `fk_pedido_cupom` FOREIGN KEY (`cupom_id`) REFERENCES `cupons` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pedido_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (1,6,649.00,0.00,649.00,NULL,'pago','simulado','2026-05-27 10:58:14'),(2,7,1298.00,64.90,1233.10,NULL,'pendente','simulado','2026-05-27 11:42:37'),(3,7,649.00,0.00,649.00,NULL,'pago','simulado','2026-05-27 11:47:53');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perguntas`
--

DROP TABLE IF EXISTS `perguntas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perguntas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aula_id` int(10) unsigned NOT NULL,
  `texto` text NOT NULL,
  `justificativa` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pergunta_aula` (`aula_id`),
  CONSTRAINT `fk_pergunta_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perguntas`
--

LOCK TABLES `perguntas` WRITE;
/*!40000 ALTER TABLE `perguntas` DISABLE KEYS */;
INSERT INTO `perguntas` VALUES (1,4,'O que define um projeto?',NULL,'2026-05-05 22:35:34'),(2,4,'Qual é o principal papel do gerente de projetos?',NULL,'2026-05-05 22:35:34'),(3,4,'Qual fase NÃO faz parte do ciclo de vida de um projeto?',NULL,'2026-05-05 22:35:34'),(4,22,'Qual das seguintes alternativas descreve melhor o principal papel do Auditor Líder?','O auditor líder é responsável por planejar, coordenar e dirigir a equipe auditora, além de consolidar o relatório final.','2026-05-27 10:41:57'),(5,22,'Qual a duração recomendada padrão de uma auditoria de Fase 1?','A auditoria de fase 1 é focada em revisão documental do sistema e costuma levar de 1 a 2 dias.','2026-05-27 10:41:57'),(6,23,'O que constitui um controle de segurança da informação segundo a ISO 27001?','Controles de segurança da informação são práticas, políticas ou mecanismos técnicos ou físicos para gerenciar e reduzir riscos a níveis aceitáveis.','2026-05-27 10:41:57'),(7,23,'Qual é a frequência de realização correta de auditorias internas de conformidade em uma organização certificada?','A norma exige que auditorias internas ocorram em intervalos planejados para verificar a conformidade do SGSI.','2026-05-27 10:41:57'),(8,23,'Qual das opções abaixo caracteriza uma \"Não Conformidade Maior\" em auditorias ISO?','A Não Conformidade Maior é configurada quando há ausência total ou falha sistemática na aplicação de um requisito mandatório da norma.','2026-05-27 10:41:57');
/*!40000 ALTER TABLE `perguntas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proctoring_logs`
--

DROP TABLE IF EXISTS `proctoring_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `proctoring_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `matricula_id` int(10) unsigned NOT NULL,
  `aula_id` int(10) unsigned NOT NULL,
  `tipo_evento` varchar(50) NOT NULL,
  `detalhes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_proctoring_matricula` (`matricula_id`),
  KEY `fk_proctoring_aula` (`aula_id`),
  CONSTRAINT `fk_proctoring_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_proctoring_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proctoring_logs`
--

LOCK TABLES `proctoring_logs` WRITE;
/*!40000 ALTER TABLE `proctoring_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `proctoring_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `progresso_aula`
--

DROP TABLE IF EXISTS `progresso_aula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `progresso_aula` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `matricula_id` int(10) unsigned NOT NULL,
  `aula_id` int(10) unsigned NOT NULL,
  `concluida` tinyint(1) NOT NULL DEFAULT 0,
  `tempo_parada` int(10) unsigned NOT NULL DEFAULT 0,
  `data_conclusao` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_matricula_aula` (`matricula_id`,`aula_id`),
  KEY `fk_prog_aula` (`aula_id`),
  CONSTRAINT `fk_prog_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prog_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `progresso_aula`
--

LOCK TABLES `progresso_aula` WRITE;
/*!40000 ALTER TABLE `progresso_aula` DISABLE KEYS */;
/*!40000 ALTER TABLE `progresso_aula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_perguntas_sorteadas`
--

DROP TABLE IF EXISTS `quiz_perguntas_sorteadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_perguntas_sorteadas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `matricula_id` int(10) unsigned NOT NULL,
  `aula_id` int(10) unsigned NOT NULL,
  `pergunta_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mat_aula_perg` (`matricula_id`,`aula_id`,`pergunta_id`),
  KEY `fk_qps_pergunta` (`pergunta_id`),
  CONSTRAINT `fk_qps_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_qps_pergunta` FOREIGN KEY (`pergunta_id`) REFERENCES `perguntas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_perguntas_sorteadas`
--

LOCK TABLES `quiz_perguntas_sorteadas` WRITE;
/*!40000 ALTER TABLE `quiz_perguntas_sorteadas` DISABLE KEYS */;
INSERT INTO `quiz_perguntas_sorteadas` VALUES (1,1,22,4),(2,1,22,5),(3,2,22,4),(4,2,22,5),(7,2,23,6),(5,2,23,7),(6,2,23,8);
/*!40000 ALTER TABLE `quiz_perguntas_sorteadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_resposta`
--

DROP TABLE IF EXISTS `quiz_resposta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quiz_resposta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `matricula_id` int(10) unsigned NOT NULL,
  `aula_id` int(10) unsigned NOT NULL,
  `nota` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `aprovado` tinyint(1) NOT NULL DEFAULT 0,
  `acertos` int(10) unsigned NOT NULL DEFAULT 0,
  `total_perguntas` int(10) unsigned NOT NULL DEFAULT 0,
  `tentativas_restantes` int(10) unsigned NOT NULL DEFAULT 5,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_quiz_matricula_aula` (`matricula_id`,`aula_id`),
  KEY `fk_quiz_aula` (`aula_id`),
  CONSTRAINT `fk_quiz_aula` FOREIGN KEY (`aula_id`) REFERENCES `aulas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_quiz_matricula` FOREIGN KEY (`matricula_id`) REFERENCES `matriculas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_resposta`
--

LOCK TABLES `quiz_resposta` WRITE;
/*!40000 ALTER TABLE `quiz_resposta` DISABLE KEYS */;
INSERT INTO `quiz_resposta` VALUES (1,1,22,50,0,1,2,4,'2026-05-27 10:59:13'),(2,2,22,50,0,1,2,4,'2026-05-27 11:48:50');
/*!40000 ALTER TABLE `quiz_resposta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessoes`
--

DROP TABLE IF EXISTS `sessoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessoes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario` (`usuario_id`),
  UNIQUE KEY `uq_token` (`token`),
  CONSTRAINT `fk_sessao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessoes`
--

LOCK TABLES `sessoes` WRITE;
/*!40000 ALTER TABLE `sessoes` DISABLE KEYS */;
INSERT INTO `sessoes` VALUES (1,2,'02f8da04408b8b86a57415081cf28e1919ce9726da4104dc54302fc5bd4d84ce','2026-05-27 16:39:41','2026-05-27 10:39:41');
/*!40000 ALTER TABLE `sessoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','admin@actshare.com.br','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-06 01:14:24'),(2,'Robson Amorim','robson@actshare.com.br','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-05 22:35:34'),(3,'Maria Silva','maria@exemplo.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-05 22:35:34'),(4,'João Santos','joao@exemplo.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-05 22:35:34'),(5,'Empresa ABC','gestor@empresaabc.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','gestor',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-05 22:35:34'),(6,'ROBSON','robson.dev9@gmail.com','$2y$10$Kzpp9Mq/IWtSdhdQTOwd/ecMgXUXFRhE9cHFpNRG4sxTXQ996mOR6','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-27 10:39:58'),(7,'Teste','teste@teste.com','$2y$10$XSm6KCuLKiAU.HNdVIqZOO0Ip0nhUyRp0Neo9g7MyyMdrqCFr0e4W','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-27 11:28:07'),(8,'Admin Teste','admin.teste@actshare.com.br','$2y$10$.3R366hoqABhvictbrSFU.W/hYYmh2RHsDLdtZWIGUs5RUkluaRuC','admin',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-30 12:39:41'),(9,'Aluno Teste','aluno.teste@actshare.com.br','$2y$10$.3R366hoqABhvictbrSFU.W/hYYmh2RHsDLdtZWIGUs5RUkluaRuC','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-30 12:39:41'),(10,'Cliente Teste Compra','cliente.teste+767869149@actshare.com.br','$2y$10$Y/Tp9tW6okHfPbgPaDRcbe8r9flf1fAZjGhHNJ6amv37lYr8gciK6','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-30 12:49:57'),(11,'Cliente Teste Compra','cliente.teste+890556165@actshare.com.br','$2y$10$h3XSdB1MsMhJ0TrLm0xI1OZ9vSbsSc6X2QkgQ.0C3TS1uydKmBzkG','aluno',1,'123',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-30 12:50:11'),(12,'Cliente Completo Teste','cliente.completo+589210141@actshare.com.br','$2y$10$j65gIgk1uol2dZatF8RVruX5OCUKGbUaXYczERb6FoV/BohZJ1w5K','aluno',1,'123.456.789-00','11988887777','fisica','1990-01-01',NULL,NULL,'01001000','Praca da Se','100',NULL,'Se','Sao Paulo','SP','Brasil','2026-05-30 12:52:18');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'actshare'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-30 12:59:17
