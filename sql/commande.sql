-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `commande`;
CREATE TABLE `commande` (
  `token` varchar(255) NOT NULL,
  `id` varchar(255) NOT NULL,
  `nom_client` varchar(25) NOT NULL,
  `prenom_client` varchar(25) NOT NULL,
  `mail_client` varchar(111) NOT NULL,
  `etat` varchar(50) NOT NULL,
  `livraison` datetime NOT NULL,
  `date_paiement` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `commande` (`token`, `id`, `nom_client`, `prenom_client`, `mail_client`, `etat`, `livraison`, `date_paiement`) VALUES
('63aa6f4a404b0db1784c2a718f1910bf2e102f8a55a81d20f8d44b6a4217742d',	'03947142-e4dc-11e7-95e8-35de601141a2',	'Jik',	'Jerk',	'Jork',	'paid',	'2014-10-10 23:10:00',	'2018-02-04 00:00:00'),
('03846b3f5222dced417e8dac9babc5acaf779647e588cc8a843a47a6a918a698',	'edafd0fe-f243-11e7-af81-a9a6d11c7a25',	'dumas',	'yann',	'yanndumlas@gmail.fr',	'non traité',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
('c78f61c11631c5ba27f3e340222a3b53adc83232986ba672c0ffd218331424ce',	'de890ab8-f244-11e7-981c-3fd1980d3f74',	'dumas',	'yann',	'yanndumlas@gmail.fr',	'non traité',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
('03a9228a4af96d7c1f25c4bfd38da6b111462c4b9eb62b50245be46e651db2b3',	'1d116792-f247-11e7-94d1-8397c79ab41e',	'dumas',	'yann',	'yanndumlas@gmail.fr',	'non traité',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
('9734a2f22b0a02802133679571b1ff98600ba6e9142269adcad080f919db4e59',	'7b971866-f247-11e7-ad04-a333684559d7',	'dumas',	'yann',	'yanndumlas@gmail.fr',	'non traité',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00'),
('5a377e484d61d59506b27703ba57bf3f10a36dd2fc780e6e31c4a1d89a85977c',	'918ba36a-05c7-11e8-99d1-0242ac170004',	'',	'',	'',	'non traité',	'2018-01-30 14:12:13',	'0000-00-00 00:00:00'),
('e0d412e72196ba7aff0dd9d7f3a61f7e38771640f4a361d5172b1dde6f363a9b',	'7c8a5ace-099b-11e8-9b3b-0242ac170005',	'Rambo',	'John',	'mail@mail.Com',	'paid',	'2018-10-28 00:00:00',	'2018-02-04 00:00:00');

-- 2018-02-04 13:03:26
