-- phpMyAdmin SQL Dump
-- version 3.5.6
-- http://www.phpmyadmin.net
--
-- Data de Criação: 19-Dez-2017 às 11:29
-- Versão do servidor: 5.1.54-rel12.6-log
-- versão do PHP: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de Dados: `botrecruit`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `area`
--

CREATE TABLE IF NOT EXISTS `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(255) NOT NULL,
  `img_link` varchar(255) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Extraindo dados da tabela `area`
--

INSERT INTO `area` (`id`, `descricao`, `img_link`, `titulo`) VALUES
(1, 'Área de Tecnologia da Informação', 'https://cdn.gomix.com/86c850a3-f340-43ab-907f-89c5ede9e8a7%2F16522536_1394457680613018_1405850424_n.jpg', 'Tecnologia da Informação'),
(2, 'Área de Produção', 'https://cdn.gomix.com/86c850a3-f340-43ab-907f-89c5ede9e8a7%2F16468768_1394457677279685_381343019_n.jpg', 'Produção'),
(3, 'Área de Contabilidade', 'https://cdn.gomix.com/86c850a3-f340-43ab-907f-89c5ede9e8a7%2F16521994_1394457673946352_1875328274_n.jpg', 'Contabilidade');

-- --------------------------------------------------------

--
-- Estrutura da tabela `entrevista`
--

CREATE TABLE IF NOT EXISTS `entrevista` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) DEFAULT NULL,
  `fk_usuario` int(11) DEFAULT NULL,
  `facebook_id` varchar(20) NOT NULL,
  `fk_vaga` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fk_usuario` (`fk_usuario`),
  KEY `idx_fk_vaga` (`fk_vaga`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1349 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `entrevista_has_resposta`
--

CREATE TABLE IF NOT EXISTS `entrevista_has_resposta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resposta` text,
  `fk_pergunta` int(11) NOT NULL,
  `fk_entrevista` int(11) NOT NULL,
  `datahora` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fk_pergunta` (`fk_pergunta`),
  KEY `fk_entrevista` (`fk_entrevista`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=189 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `pergunta`
--

CREATE TABLE IF NOT EXISTS `pergunta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Extraindo dados da tabela `pergunta`
--

INSERT INTO `pergunta` (`id`, `descricao`) VALUES
(1, 'Qual sua formação profissional?'),
(2, 'Possui experiência na área? '),
(3, 'O que você mais gosta de fazer em seu tempo livre?'),
(4, 'Fale um pouco sobre você. Seus Conhecimentos e Habilidades.'),
(5, 'Possui LinkedIn? Se sim informe o link para podermos te conhecer melhor profissionalmente.'),
(6, 'Muito bom! Agora Nos envie seu currículo!');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `email` varchar(80) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `link_curriculo` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `facebook_img` varchar(255) DEFAULT NULL,
  `preent` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `facebook_id` (`facebook_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=68 ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `vaga`
--

CREATE TABLE IF NOT EXISTS `vaga` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `salario` float NOT NULL,
  `descricao` text NOT NULL,
  `img_link` varchar(255) NOT NULL,
  `fk_area` int(11) NOT NULL,
  `link_web` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fk_area` (`fk_area`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Extraindo dados da tabela `vaga`
--

INSERT INTO `vaga` (`id`, `nome`, `salario`, `descricao`, `img_link`, `fk_area`, `link_web`) VALUES
(1, 'Engenheiro de Software', 2500, 'Desenvolvimento de sistemas web utilizando principalmente linguagem php', 'https://cdn.gomix.com/86c850a3-f340-43ab-907f-89c5ede9e8a7%2Ficone_engenheiro_de_software%20(1).jpg', 1, 'http://www.codeside.com.br/botrecruit/webviews/engenheiro.html'),
(3, 'Contador', 2000, 'Realizar análises críticas dos indicadores operacionais estratégicos.', 'https://cdn.gomix.com/86c850a3-f340-43ab-907f-89c5ede9e8a7%2F16467223_1394458170612969_440477516_n.jpg', 3, 'http://www.codeside.com.br/botrecruit/webviews/contador.html'),
(4, 'Auxiliar de Produção', 1200, 'Separar materiais, realizar montagem de peças, limpar máquinas.', 'https://cdn.gomix.com/86c850a3-f340-43ab-907f-89c5ede9e8a7%2F16522672_1394457757279677_502827694_n.jpg', 2, 'http://www.codeside.com.br/botrecruit/webviews/auxiliar.html');

-- --------------------------------------------------------

--
-- Estrutura da tabela `vaga_has_pergunta`
--

CREATE TABLE IF NOT EXISTS `vaga_has_pergunta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_vaga` int(11) NOT NULL,
  `fk_pergunta` int(11) NOT NULL,
  `indice` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fk_vaga` (`fk_vaga`),
  KEY `idx_fk_pergunta` (`fk_pergunta`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Extraindo dados da tabela `vaga_has_pergunta`
--

INSERT INTO `vaga_has_pergunta` (`id`, `fk_vaga`, `fk_pergunta`, `indice`) VALUES
(1, 1, 1, 5),
(2, 1, 2, 2),
(3, 1, 3, 3),
(4, 1, 4, 4),
(5, 1, 5, 1),
(6, 1, 6, 6),
(19, 3, 1, 5),
(20, 3, 2, 2),
(21, 3, 3, 3),
(22, 3, 4, 4),
(23, 3, 5, 1),
(24, 3, 6, 6),
(25, 4, 1, 5),
(26, 4, 2, 2),
(27, 4, 3, 3),
(28, 4, 4, 4),
(29, 4, 5, 1),
(30, 4, 6, 6);

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `entrevista`
--
ALTER TABLE `entrevista`
  ADD CONSTRAINT `entrevista_ibfk_1` FOREIGN KEY (`fk_usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `entrevista_ibfk_2` FOREIGN KEY (`fk_vaga`) REFERENCES `vaga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `entrevista_has_resposta`
--
ALTER TABLE `entrevista_has_resposta`
  ADD CONSTRAINT `entrevista_has_resposta_ibfk_1` FOREIGN KEY (`fk_pergunta`) REFERENCES `pergunta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `entrevista_has_resposta_ibfk_2` FOREIGN KEY (`fk_entrevista`) REFERENCES `entrevista` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `vaga`
--
ALTER TABLE `vaga`
  ADD CONSTRAINT `vaga_ibfk_1` FOREIGN KEY (`fk_area`) REFERENCES `area` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `vaga_has_pergunta`
--
ALTER TABLE `vaga_has_pergunta`
  ADD CONSTRAINT `vaga_has_pergunta_ibfk_1` FOREIGN KEY (`fk_vaga`) REFERENCES `vaga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vaga_has_pergunta_ibfk_2` FOREIGN KEY (`fk_pergunta`) REFERENCES `pergunta` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
