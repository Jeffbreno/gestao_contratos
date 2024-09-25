-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 25/09/2024 às 01:17
-- Versão do servidor: 8.3.0
-- Versão do PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gestao_contratos_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_banco`
--

DROP TABLE IF EXISTS `tb_banco`;
CREATE TABLE IF NOT EXISTS `tb_banco` (
  `codigo` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dt_cadastro` datetime DEFAULT NULL,
  `dt_alteracao` datetime DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_contrato`
--

DROP TABLE IF EXISTS `tb_contrato`;
CREATE TABLE IF NOT EXISTS `tb_contrato` (
  `codigo` int NOT NULL AUTO_INCREMENT,
  `prazo` int NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_inclusao` date NOT NULL,
  `convenio_servico` int DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  KEY `convenio_servico` (`convenio_servico`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_convenio`
--

DROP TABLE IF EXISTS `tb_convenio`;
CREATE TABLE IF NOT EXISTS `tb_convenio` (
  `codigo` int NOT NULL AUTO_INCREMENT,
  `convenio` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verba` decimal(10,2) NOT NULL,
  `banco` int DEFAULT NULL,
  PRIMARY KEY (`codigo`),
  KEY `banco` (`banco`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_convenio_servico`
--

DROP TABLE IF EXISTS `tb_convenio_servico`;
CREATE TABLE IF NOT EXISTS `tb_convenio_servico` (
  `codigo` int NOT NULL AUTO_INCREMENT,
  `convenio` int DEFAULT NULL,
  `servico` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`codigo`),
  KEY `convenio` (`convenio`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_usuario`
--

DROP TABLE IF EXISTS `tb_usuario`;
CREATE TABLE IF NOT EXISTS `tb_usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dt_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  `dt_alteracao` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_alteracao` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_usuario`
--

INSERT INTO `tb_usuario` (`id`, `nome`, `login`, `email`, `senha`, `dt_cadastro`, `dt_alteracao`, `id_alteracao`) VALUES
(1, 'Jefferson Breno de A. Lopes', 'master', 'jeffbreno@gmail.com', '$2y$10$uy4JBDJWT.CDJrUODKQQuunoYFCyPvhlroGL5vIsmn5VAR3Vca7Ge', '2023-08-22 11:48:33', '2024-09-18 10:49:48', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
