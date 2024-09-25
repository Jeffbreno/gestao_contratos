-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 25/09/2024 às 15:54
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
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_banco`
--

INSERT INTO `tb_banco` (`codigo`, `nome`, `dt_cadastro`, `dt_alteracao`) VALUES
(4, 'SICOOB PJ', '2024-09-24 14:33:22', '2024-09-24 15:32:18'),
(8, 'SICRED PJ', '2024-09-24 14:35:10', '2024-09-24 15:32:10'),
(9, 'Nubanck PJ', '2024-09-24 14:37:53', '2024-09-24 15:21:40');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_contrato`
--

INSERT INTO `tb_contrato` (`codigo`, `prazo`, `valor`, `data_inclusao`, `convenio_servico`) VALUES
(1, 15, 4500.00, '2023-01-05', 3),
(2, 25, 2000.00, '2024-10-01', 1),
(3, 236, 5000000.00, '2024-09-25', 4),
(4, 500, 100000.00, '2024-08-10', 4);

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
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_convenio`
--

INSERT INTO `tb_convenio` (`codigo`, `convenio`, `verba`, `banco`) VALUES
(1, 'Alguma coisa', 100.00, 8),
(12, 'HUMANA SAÚDE', 2305.55, 9),
(11, 'Outra coisa', 10.00, 4),
(13, 'UNIMED', 500000.00, 9);

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_convenio_servico`
--

INSERT INTO `tb_convenio_servico` (`codigo`, `convenio`, `servico`) VALUES
(1, 11, 'TESTE'),
(3, 1, 'Mais um teste'),
(4, 12, 'Transfusão de sangue');

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
