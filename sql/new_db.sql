-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `carte`;
CREATE TABLE `carte` (
  `id` varchar(300) NOT NULL,
  `nom` varchar(25) NOT NULL,
  `password` varchar(250) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `cumul` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `carte` (`id`, `nom`, `password`, `mail`, `created_at`, `updated_at`, `cumul`) VALUES
('4aad23ee-f56c-11e7-8ef9-196a985cd0d9',	'thomas',	'test',	'richard@thomas.fr',	'2018-01-09 18:38:31',	'2018-01-09 18:38:31',	0),
('20726e60-f876-11e7-9cb7-59e2645a98a5',	'yann',	'$2y$10$o/GOwNJfXZ49qqlb8ReOSuzKW3BXqvxJ3uaMcHeONj22Cg9/ldmp.',	'yanndumas54230@gmail.com',	'2018-01-13 15:26:29',	'2018-01-13 15:26:29',	0);

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE `categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(64) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `commande`;
CREATE TABLE `commande` (
  `token` varchar(255) NOT NULL,
  `id` varchar(255) NOT NULL,
  `nom_client` varchar(25) NOT NULL,
  `prenom_client` varchar(25) NOT NULL,
  `mail_client` varchar(111) NOT NULL,
  `etat` varchar(50) NOT NULL,
  `livraison` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sand_id` int(10) NOT NULL,
  `quantite` int(4) NOT NULL,
  `commande_id` varchar(255) NOT NULL,
  `taille_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `sand2cat`;
CREATE TABLE `sand2cat` (
  `sand_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `sand2com`;
CREATE TABLE `sand2com` (
  `sand_id` varchar(300) NOT NULL,
  `com_id` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `sandwich`;
CREATE TABLE `sandwich` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `type_pain` text NOT NULL,
  `img` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `taille_sandwich`;
CREATE TABLE `taille_sandwich` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(64) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `tarif`;
CREATE TABLE `tarif` (
  `taille_id` int(11) NOT NULL,
  `sand_id` int(11) NOT NULL,
  `prix` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2018-01-16 13:11:01
