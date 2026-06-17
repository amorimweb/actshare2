-- ============================================================
-- ActShare - REPLICA COMPLETA DO BANCO LOCAL
-- ATENCAO: execute este SQL dentro do banco remoto correto.
-- Ele apaga TODAS as tabelas do banco selecionado e recria a copia local.
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET GROUP_CONCAT_MAX_LEN = 1000000;

SET @tables := (
  SELECT GROUP_CONCAT(CONCAT('`', TABLE_NAME, '`') SEPARATOR ',')
  FROM INFORMATION_SCHEMA.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_TYPE = 'BASE TABLE'
);

SET @drop_sql := IF(@tables IS NULL, 'SELECT "Nenhuma tabela para apagar"', CONCAT('DROP TABLE ', @tables));
PREPARE stmt FROM @drop_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

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
INSERT INTO `aulas` VALUES (1,1,'O que Ã© um projeto?',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,12,1,0,'2026-05-05 22:35:34'),(2,1,'Ciclo de vida de um projeto',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,2,18,1,0,'2026-05-05 22:35:34'),(3,1,'O papel do gerente de projetos',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,3,15,1,0,'2026-05-05 22:35:34'),(4,1,'Quiz â€” Fundamentos',NULL,'quiz',NULL,0,4,0,1,0,'2026-05-05 22:35:34'),(5,2,'Manifesto Ãgil e seus princÃ­pios',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,20,1,0,'2026-05-05 22:35:34'),(6,2,'Scrum: papÃ©is, eventos e artefatos',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,2,25,1,0,'2026-05-05 22:35:34'),(7,2,'Kanban na prÃ¡tica',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,3,18,1,0,'2026-05-05 22:35:34'),(8,2,'Quiz â€” Metodologias Ãgeis',NULL,'quiz',NULL,0,4,0,1,0,'2026-05-05 22:35:34'),(9,5,'O que Ã© a LGPD e por que ela existe?',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,10,1,0,'2026-05-05 22:35:34'),(10,5,'Conceitos fundamentais: dado, tratamentoâ€¦',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,2,14,1,0,'2026-05-05 22:35:34'),(11,5,'Bases legais para tratamento de dados',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,3,16,1,0,'2026-05-05 22:35:34'),(12,5,'Quiz â€” IntroduÃ§Ã£o Ã  LGPD',NULL,'quiz',NULL,0,4,0,1,0,'2026-05-05 22:35:34'),(13,6,'Direitos dos titulares de dados',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,12,1,0,'2026-05-05 22:35:34'),(14,6,'Como responder Ã s solicitaÃ§Ãµes dos titulares',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,2,15,1,0,'2026-05-05 22:35:34'),(15,6,'Quiz â€” Direitos dos Titulares',NULL,'quiz',NULL,0,3,0,1,0,'2026-05-05 22:35:34'),(16,9,'InstalaÃ§Ã£o e configuraÃ§Ã£o do ambiente',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,15,1,0,'2026-05-05 22:35:34'),(17,9,'VariÃ¡veis, tipos e operadores',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,2,20,1,0,'2026-05-05 22:35:34'),(18,9,'Condicionais e loops',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,3,22,1,0,'2026-05-05 22:35:34'),(19,9,'FunÃ§Ãµes e mÃ³dulos',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,4,25,1,0,'2026-05-05 22:35:34'),(20,9,'Quiz â€” Python BÃ¡sico',NULL,'quiz',NULL,0,5,0,1,0,'2026-05-05 22:35:34'),(21,20,'Conceitos Iniciais da ISO 27001',NULL,'video','https://www.youtube.com/watch?v=dQw4w9WgXcQ',0,1,20,1,0,'2026-05-27 10:41:57'),(22,20,'Quiz de FixaÃ§Ã£o â€” Auditoria ISO 27001',NULL,'quiz',NULL,0,2,0,2,0,'2026-05-27 10:41:57'),(23,20,'AvaliaÃ§Ã£o Final de CertificaÃ§Ã£o (Exame Monitorado)',NULL,'quiz',NULL,0,3,0,3,1,'2026-05-27 10:41:57');
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
INSERT INTO `categorias` VALUES (1,'GestÃ£o','gestao',NULL,'2026-05-06 01:14:24'),(2,'Compliance','compliance',NULL,'2026-05-06 01:14:24'),(3,'Tecnologia','tecnologia',NULL,'2026-05-06 01:14:24'),(4,'Soft Skills','soft-skills',NULL,'2026-05-06 01:14:24'),(5,'JurÃ­dico','juridico',NULL,'2026-05-06 01:14:24'),(6,'InterpretaÃ§Ã£o das Normas','interpretacao-das-normas',NULL,'2026-05-30 12:08:35'),(7,'Auditor Interno','auditor-interno',NULL,'2026-05-30 12:08:35'),(8,'Auditor LÃ­der','auditor-lider',NULL,'2026-05-30 12:08:35'),(9,'Automotivo','automotivo',NULL,'2026-05-30 12:08:35'),(10,'SeguranÃ§a da InformaÃ§Ã£o','seguranca-da-informacao',NULL,'2026-05-30 12:08:35');
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
INSERT INTO `cursos` VALUES (1,'GestÃ£o de Projetos na PrÃ¡tica','Aprenda a planejar, executar e controlar projetos com metodologias Ã¡geis e tradicionais. Domine ferramentas como Kanban, Scrum e PMBOK aplicados ao dia a dia corporativo.','https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=600&q=80',1,1,1,1,0.00,20,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(2,'LGPD na PrÃ¡tica: Proteja sua Empresa','Entenda todos os aspectos da Lei Geral de ProteÃ§Ã£o de Dados. Do conceito Ã  implementaÃ§Ã£o, capacite sua equipe para evitar multas e garantir conformidade.','https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600&q=80',1,1,2,2,0.00,8,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(3,'Python para AutomaÃ§Ã£o de Processos','Do zero ao automatizador: aprenda Python focado em automaÃ§Ã£o de tarefas repetitivas, relatÃ³rios automÃ¡ticos e integraÃ§Ã£o de sistemas.','https://images.unsplash.com/photo-1526379095098-d400fd0bf935?w=600&q=80',1,1,3,3,0.00,24,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(4,'LideranÃ§a e ComunicaÃ§Ã£o Assertiva','Desenvolva habilidades de lideranÃ§a, comunicaÃ§Ã£o e gestÃ£o de pessoas. Aprenda a engajar equipes, dar feedbacks eficazes e resolver conflitos.','https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&q=80',1,1,4,4,0.00,12,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(5,'Compliance e Ã‰tica nos NegÃ³cios','Estruture um programa de compliance robusto. PolÃ­ticas anticorrupÃ§Ã£o, cÃ³digo de conduta, canal de denÃºncias e gestÃ£o de riscos.','https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=600&q=80',1,1,2,2,0.00,16,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(6,'Excel AvanÃ§ado para Gestores','Domine as funÃ§Ãµes avanÃ§adas do Excel: tabelas dinÃ¢micas, Power Query, dashboards e automaÃ§Ã£o com macros VBA voltados para tomada de decisÃ£o.','https://images.unsplash.com/photo-1543286386-2e659306cd6c?w=600&q=80',1,1,1,1,0.00,10,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(7,'SeguranÃ§a do Trabalho â€” NR Essenciais','Normas Regulamentadoras obrigatÃ³rias: NR-1, NR-6, NR-17 e NR-35. Capacite sua equipe e mantenha sua empresa em conformidade legal.','https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&q=80',1,1,5,5,0.00,8,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(8,'InteligÃªncia Artificial no Trabalho','Entenda como usar ferramentas de IA (ChatGPT, Copilot, Gemini) para aumentar sua produtividade, criar conteÃºdos e automatizar tarefas do dia a dia.','https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=600&q=80',1,1,3,3,0.00,6,NULL,'2026-05-05 22:35:34','2026-05-05 22:35:34'),(9,'CertificaÃ§Ã£o Auditor LÃ­der ISO 27001 (Exemplar Global)','Curso preparatÃ³rio completo voltado para a auditoria de sistemas de gestÃ£o de seguranÃ§a da informaÃ§Ã£o (SGSI) com exames simulados e prova de certificaÃ§Ã£o monitorada de acordo com as regras de conformidade internacional.','https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600&q=80',1,1,2,2,499.00,40,180,'2026-05-27 10:41:57','2026-05-27 10:41:57'),(10,'ISO 14001:2026 - InterpretaÃ§Ã£o da Norma','InterpretaÃ§Ã£o aplicada dos requisitos da ISO 14001:2026 para sistemas de gestÃ£o ambiental, com foco em implementaÃ§Ã£o, evidÃªncias e conformidade.','https://images.unsplash.com/photo-1497436072909-60f360e1d4b1?w=900&q=80',1,1,6,6,297.00,16,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(11,'ISO 9001:2015 - InterpretaÃ§Ã£o da Norma','Curso para compreender os requisitos da ISO 9001:2015 e sua aplicaÃ§Ã£o em processos, indicadores, riscos, oportunidades e melhoria contÃ­nua.','https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=900&q=80',1,1,6,6,297.00,16,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(12,'ISO 45001:2018 - InterpretaÃ§Ã£o da Norma','Estudo dos requisitos de saÃºde e seguranÃ§a ocupacional da ISO 45001:2018, incluindo contexto, lideranÃ§a, planejamento, operaÃ§Ã£o e avaliaÃ§Ã£o.','https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=900&q=80',1,1,6,6,297.00,16,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(13,'ISO/IEC 17025 - InterpretaÃ§Ã£o da Norma','Treinamento sobre os requisitos gerais para competÃªncia de laboratÃ³rios de ensaio e calibraÃ§Ã£o conforme ISO/IEC 17025.','https://images.unsplash.com/photo-1581093458791-9d09f535c4a3?w=900&q=80',1,1,6,6,297.00,16,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(14,'ISO 14001:2026 - Auditor Interno 1Âª e 2Âª Parte','FormaÃ§Ã£o de auditor interno para auditorias de primeira e segunda parte em sistemas de gestÃ£o ambiental baseados na ISO 14001:2026.','https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=900&q=80',1,1,7,6,397.00,24,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(15,'ISO 9001:2015 - Auditor Interno 1Âª e 2Âª Parte','CapacitaÃ§Ã£o para planejar, executar, relatar e acompanhar auditorias internas e de fornecedores conforme ISO 9001:2015.','https://images.unsplash.com/photo-1552664730-d307ca884978?w=900&q=80',1,1,7,6,397.00,24,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(16,'ISO 45001:2018 - Auditor Interno 1Âª e 2Âª Parte','Curso de auditor interno para sistemas de gestÃ£o de saÃºde e seguranÃ§a ocupacional, com prÃ¡ticas de auditoria e relatÃ³rios.','https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=900&q=80',1,1,7,6,397.00,24,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(17,'ISO 14001:2026 - Auditor LÃ­der Exemplar Global','PreparaÃ§Ã£o para atuaÃ§Ã£o como auditor lÃ­der em sistemas de gestÃ£o ambiental, alinhada Ã s competÃªncias exigidas por organismos internacionais.','https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=900&q=80',1,1,8,6,699.00,40,180,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(18,'ISO 9001:2015 - Auditor LÃ­der Exemplar Global','FormaÃ§Ã£o avanÃ§ada de auditor lÃ­der para sistemas de gestÃ£o da qualidade, com abordagem de planejamento, conduÃ§Ã£o e fechamento de auditorias.','https://images.unsplash.com/photo-1560264280-88b68371db39?w=900&q=80',1,1,8,6,699.00,40,180,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(19,'ISO 45001:2018 - Auditor LÃ­der Exemplar Global','CapacitaÃ§Ã£o para liderar auditorias de saÃºde e seguranÃ§a ocupacional conforme ISO 45001:2018 e boas prÃ¡ticas internacionais.','https://images.unsplash.com/photo-1521791136064-7986c2920216?w=900&q=80',1,1,8,6,699.00,40,180,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(20,'IATF 16949 - InterpretaÃ§Ã£o da Norma','InterpretaÃ§Ã£o dos requisitos da IATF 16949 para cadeia automotiva, incluindo requisitos especÃ­ficos de clientes e gestÃ£o de processos.','https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?w=900&q=80',1,1,9,6,497.00,24,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(21,'APQP 3Âª EdiÃ§Ã£o','Planejamento avanÃ§ado da qualidade do produto com foco em desenvolvimento, validaÃ§Ã£o, riscos, documentaÃ§Ã£o e entregÃ¡veis do APQP.','https://images.unsplash.com/photo-1581090700227-1e37b190418e?w=900&q=80',1,1,9,6,297.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(22,'FMEA AIAG & VDA','AplicaÃ§Ã£o prÃ¡tica do FMEA AIAG & VDA para anÃ¡lise de riscos, priorizaÃ§Ã£o de aÃ§Ãµes e integraÃ§Ã£o com desenvolvimento de produto e processo.','https://images.unsplash.com/photo-1581092334651-ddf26d9a09d0?w=900&q=80',1,1,9,6,297.00,16,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(23,'MAS - Measurement System Analysis','Treinamento de anÃ¡lise de sistemas de mediÃ§Ã£o para avaliaÃ§Ã£o de repetibilidade, reprodutibilidade, tendÃªncia, linearidade e estabilidade.','https://images.unsplash.com/photo-1581091215367-59ab6b3625f4?w=900&q=80',1,1,9,6,247.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(24,'CEP - Controle EstatÃ­stico do Processo','Fundamentos e aplicaÃ§Ã£o do CEP para monitoramento, estabilidade, capacidade de processo e tomada de decisÃ£o baseada em dados.','https://images.unsplash.com/photo-1543286386-2e659306cd6c?w=900&q=80',1,1,9,6,247.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(25,'PPAP - Processo de AprovaÃ§Ã£o de PeÃ§a de ProduÃ§Ã£o','Curso sobre requisitos, nÃ­veis de submissÃ£o, documentaÃ§Ã£o e critÃ©rios para aprovaÃ§Ã£o de peÃ§as de produÃ§Ã£o no setor automotivo.','https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=900&q=80',1,1,9,6,247.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(26,'CQI-09 - Tratamento TÃ©rmico','Requisitos especiais CQI-09 para avaliaÃ§Ã£o de processos de tratamento tÃ©rmico na cadeia automotiva.','https://images.unsplash.com/photo-1516937941344-00b4e0337589?w=900&q=80',1,1,9,6,297.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(27,'CQI-11 - Tratamento Superficial','Treinamento sobre requisitos CQI-11 para avaliaÃ§Ã£o e controle de processos de tratamento superficial.','https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=900&q=80',1,1,9,6,297.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(28,'CQI-14 - GestÃ£o de Garantia Automotiva','AplicaÃ§Ã£o dos requisitos CQI-14 para gestÃ£o de garantia, anÃ¡lise de falhas, retorno de campo e melhoria de processos automotivos.','https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=900&q=80',1,1,9,6,297.00,12,90,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(29,'ISO 27001 - InterpretaÃ§Ã£o da Norma','InterpretaÃ§Ã£o dos requisitos da ISO 27001 para implantaÃ§Ã£o, manutenÃ§Ã£o e melhoria de sistemas de gestÃ£o de seguranÃ§a da informaÃ§Ã£o.','https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=900&q=80',1,1,10,6,397.00,20,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(30,'ISO 27701 - InterpretaÃ§Ã£o da Norma','Curso de interpretaÃ§Ã£o da ISO 27701 para gestÃ£o de informaÃ§Ãµes de privacidade e integraÃ§Ã£o com sistemas de seguranÃ§a da informaÃ§Ã£o.','https://images.unsplash.com/photo-1563986768609-322da13575f3?w=900&q=80',1,1,10,6,397.00,20,120,'2026-05-30 12:08:35','2026-05-30 12:08:35'),(31,'TISAX VDA ISA - InterpretaÃ§Ã£o','Treinamento sobre TISAX e catÃ¡logo VDA ISA para avaliaÃ§Ã£o de seguranÃ§a da informaÃ§Ã£o na cadeia automotiva.','https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=900&q=80',1,1,10,6,397.00,20,120,'2026-05-30 12:08:35','2026-05-30 12:08:35');
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
INSERT INTO `instrutores` VALUES (1,'Dr. Carlos Mendes','Doutor em GestÃ£o Empresarial','MBA em LideranÃ§a e InovaÃ§Ã£o',NULL,NULL,'2026-05-05 22:35:34'),(2,'Ana Paula Rocha','Especialista em Compliance e LGPD','Advogada com 15 anos de experiÃªncia',NULL,NULL,'2026-05-05 22:35:34'),(3,'Rafael Sousa','Engenheiro de Software SÃªnior','Certificado AWS e Google Cloud',NULL,NULL,'2026-05-05 22:35:34'),(4,'Mariana Figueiredo','Mestre em Psicologia Organizacional','Coach Executivo Certificado',NULL,NULL,'2026-05-05 22:35:34'),(5,'JoÃ£o Augusto Lima','Especialista em SeguranÃ§a do Trabalho','PÃ³s-graduado em GestÃ£o de Riscos',NULL,NULL,'2026-05-05 22:35:34'),(6,'Equipe TÃ©cnica ActShare','Especialistas em sistemas de gestÃ£o','Normas ISO, IATF e auditorias',NULL,NULL,'2026-05-30 12:08:35');
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
INSERT INTO `modulos` VALUES (1,1,'Fundamentos de Gerenciamento de Projetos',1,'2026-05-05 22:35:34'),(2,1,'Metodologias Ãgeis â€” Scrum e Kanban',2,'2026-05-05 22:35:34'),(3,1,'Planejamento e Escopo',3,'2026-05-05 22:35:34'),(4,1,'GestÃ£o de Riscos e Qualidade',4,'2026-05-05 22:35:34'),(5,2,'IntroduÃ§Ã£o Ã  LGPD',1,'2026-05-05 22:35:34'),(6,2,'Direitos dos Titulares',2,'2026-05-05 22:35:34'),(7,2,'ObrigaÃ§Ãµes das Empresas',3,'2026-05-05 22:35:34'),(8,2,'ImplementaÃ§Ã£o PrÃ¡tica',4,'2026-05-05 22:35:34'),(9,3,'Python do Zero',1,'2026-05-05 22:35:34'),(10,3,'ManipulaÃ§Ã£o de Arquivos e Planilhas',2,'2026-05-05 22:35:34'),(11,3,'AutomaÃ§Ã£o Web com Selenium',3,'2026-05-05 22:35:34'),(12,3,'IntegraÃ§Ã£o com APIs REST',4,'2026-05-05 22:35:34'),(13,4,'Autoconhecimento e Estilos de LideranÃ§a',1,'2026-05-05 22:35:34'),(14,4,'ComunicaÃ§Ã£o Assertiva',2,'2026-05-05 22:35:34'),(15,4,'GestÃ£o de Equipes e Conflitos',3,'2026-05-05 22:35:34'),(16,5,'Fundamentos de Compliance',1,'2026-05-05 22:35:34'),(17,5,'Lei AnticorrupÃ§Ã£o',2,'2026-05-05 22:35:34'),(18,5,'Programa de Integridade',3,'2026-05-05 22:35:34'),(19,5,'GestÃ£o de Riscos Corporativos',4,'2026-05-05 22:35:34'),(20,9,'Diretrizes de Auditoria e Conformidade',1,'2026-05-27 10:41:57');
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
INSERT INTO `opcoes` VALUES (1,1,'Um trabalho repetitivo e contÃ­nuo',0),(2,1,'Um esforÃ§o temporÃ¡rio para criar um produto ou resultado Ãºnico',1),(3,1,'Qualquer tarefa do dia a dia da empresa',0),(4,1,'Uma reuniÃ£o de planejamento',0),(5,2,'Escrever o cÃ³digo do sistema',0),(6,2,'Garantir que o projeto seja entregue no prazo',0),(7,2,'Integrar pessoas, processos e tecnologia',1),(8,2,'Contratar os membros da equipe',0),(9,3,'IniciaÃ§Ã£o',0),(10,3,'Planejamento',0),(11,3,'ProduÃ§Ã£o',1),(12,3,'Encerramento',0),(13,4,'O funcionÃ¡rio que executa os backups diÃ¡rios da seguranÃ§a.',0),(14,4,'A pessoa responsÃ¡vel por gerenciar e liderar a equipe de auditoria.',1),(15,4,'O diretor executivo da empresa parceira externa.',0),(16,4,'Nenhuma das anteriores.',0),(17,5,'1 a 2 dias dependendo do porte da organizaÃ§Ã£o.',1),(18,5,'6 meses ininterruptos de anÃ¡lise profunda.',0),(19,5,'Apenas 10 minutos de conferÃªncia rÃ¡pida.',0),(20,5,'Todas as alternativas anteriores estÃ£o corretas.',0),(21,6,'Uma medida ou prÃ¡tica adotada para mitigar e gerenciar riscos de seguranÃ§a.',1),(22,6,'Uma fechadura eletrÃ´nica instalada na porta principal de TI.',0),(23,6,'Um software antivÃ­rus instalado nos notebooks da administraÃ§Ã£o.',0),(24,6,'Todas as alternativas anteriores estÃ£o corretas.',0),(25,7,'Em intervalos planejados e periÃ³dicos definidos pela organizaÃ§Ã£o.',1),(26,7,'Somente apÃ³s a ocorrÃªncia de incidentes ou vazamentos de dados graves.',0),(27,7,'Uma vez a cada 10 anos para revalidaÃ§Ã£o do SGSI.',0),(28,7,'Nenhuma das alternativas anteriores.',0),(29,8,'A ausÃªncia total ou falha sistemÃ¡tica de aplicaÃ§Ã£o de um requisito obrigatÃ³rio da norma.',1),(30,8,'Um erro de digitaÃ§Ã£o simples em um cabeÃ§alho do manual de seguranÃ§a.',0),(31,8,'Deixar a tela do computador desbloqueada em uma Ãºnica ausÃªncia rÃ¡pida.',0),(32,8,'Nenhuma das anteriores.',0);
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
INSERT INTO `perguntas` VALUES (1,4,'O que define um projeto?',NULL,'2026-05-05 22:35:34'),(2,4,'Qual Ã© o principal papel do gerente de projetos?',NULL,'2026-05-05 22:35:34'),(3,4,'Qual fase NÃƒO faz parte do ciclo de vida de um projeto?',NULL,'2026-05-05 22:35:34'),(4,22,'Qual das seguintes alternativas descreve melhor o principal papel do Auditor LÃ­der?','O auditor lÃ­der Ã© responsÃ¡vel por planejar, coordenar e dirigir a equipe auditora, alÃ©m de consolidar o relatÃ³rio final.','2026-05-27 10:41:57'),(5,22,'Qual a duraÃ§Ã£o recomendada padrÃ£o de uma auditoria de Fase 1?','A auditoria de fase 1 Ã© focada em revisÃ£o documental do sistema e costuma levar de 1 a 2 dias.','2026-05-27 10:41:57'),(6,23,'O que constitui um controle de seguranÃ§a da informaÃ§Ã£o segundo a ISO 27001?','Controles de seguranÃ§a da informaÃ§Ã£o sÃ£o prÃ¡ticas, polÃ­ticas ou mecanismos tÃ©cnicos ou fÃ­sicos para gerenciar e reduzir riscos a nÃ­veis aceitÃ¡veis.','2026-05-27 10:41:57'),(7,23,'Qual Ã© a frequÃªncia de realizaÃ§Ã£o correta de auditorias internas de conformidade em uma organizaÃ§Ã£o certificada?','A norma exige que auditorias internas ocorram em intervalos planejados para verificar a conformidade do SGSI.','2026-05-27 10:41:57'),(8,23,'Qual das opÃ§Ãµes abaixo caracteriza uma \"NÃ£o Conformidade Maior\" em auditorias ISO?','A NÃ£o Conformidade Maior Ã© configurada quando hÃ¡ ausÃªncia total ou falha sistemÃ¡tica na aplicaÃ§Ã£o de um requisito mandatÃ³rio da norma.','2026-05-27 10:41:57');
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
INSERT INTO `usuarios` VALUES (1,'Administrador','admin@actshare.com.br','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-06 01:14:24'),(2,'Robson Amorim','robson@actshare.com.br','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-05 22:35:34'),(3,'Maria Silva','maria@exemplo.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-05 22:35:34'),(4,'JoÃ£o Santos','joao@exemplo.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-05 22:35:34'),(5,'Empresa ABC','gestor@empresaabc.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','gestor',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-05 22:35:34'),(6,'ROBSON','robson.dev9@gmail.com','$2y$10$Kzpp9Mq/IWtSdhdQTOwd/ecMgXUXFRhE9cHFpNRG4sxTXQ996mOR6','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-27 10:39:58'),(7,'Teste','teste@teste.com','$2y$10$XSm6KCuLKiAU.HNdVIqZOO0Ip0nhUyRp0Neo9g7MyyMdrqCFr0e4W','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-27 11:28:07'),(8,'Admin Teste','admin.teste@actshare.com.br','$2y$10$.3R366hoqABhvictbrSFU.W/hYYmh2RHsDLdtZWIGUs5RUkluaRuC','admin',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-30 12:39:41'),(9,'Aluno Teste','aluno.teste@actshare.com.br','$2y$10$.3R366hoqABhvictbrSFU.W/hYYmh2RHsDLdtZWIGUs5RUkluaRuC','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Brasil','2026-05-30 12:39:41'),(10,'Cliente Teste Compra','cliente.teste+767869149@actshare.com.br','$2y$10$Y/Tp9tW6okHfPbgPaDRcbe8r9flf1fAZjGhHNJ6amv37lYr8gciK6','aluno',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-30 12:49:57'),(11,'Cliente Teste Compra','cliente.teste+890556165@actshare.com.br','$2y$10$h3XSdB1MsMhJ0TrLm0xI1OZ9vSbsSc6X2QkgQ.0C3TS1uydKmBzkG','aluno',1,'123',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-05-30 12:50:11'),(12,'Cliente Completo Teste','cliente.completo+589210141@actshare.com.br','$2y$10$j65gIgk1uol2dZatF8RVruX5OCUKGbUaXYczERb6FoV/BohZJ1w5K','aluno',1,'123.456.789-00','11988887777','fisica','1990-01-01',NULL,NULL,'01001000','Praca da Se','100',NULL,'Se','Sao Paulo','SP','Brasil','2026-05-30 12:52:18');
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
