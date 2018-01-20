-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `paiement`;
CREATE TABLE `paiement` (
  `id` varchar(255) NOT NULL,
  `commande_id` varchar(255) NOT NULL,
  `carte_bancaire` varchar(255) NOT NULL,
  `date_expiration` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `paiement` (`id`, `commande_id`, `carte_bancaire`, `date_expiration`) VALUES
('fffa6350-fad6-11e7-bdd1-cf48a0e351f0',	'03947142-e4dc-11e7-95e8-35de601141a2',	'1234-5678-1234-5678',	'11-18');

-- 2018-01-18 11:26:12
