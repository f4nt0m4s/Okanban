-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 18 nov. 2022 à 11:31
-- Version du serveur :  5.7.31
-- Version de PHP : 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `okanban`
--

-- --------------------------------------------------------

--
-- Structure de la table `card`
--

DROP TABLE IF EXISTS `card`;
CREATE TABLE IF NOT EXISTS `card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `kanban_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kanban_id` (`kanban_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `kanban`
--

DROP TABLE IF EXISTS `kanban`;
CREATE TABLE IF NOT EXISTS `kanban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `visibility` int(11) NOT NULL,
  `creator_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_creator_id` (`creator_id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `kanbanuser`
--

DROP TABLE IF EXISTS `kanbanuser`;
CREATE TABLE IF NOT EXISTS `kanbanuser` (
  `kanban_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`kanban_id`,`user_id`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `task`
--

DROP TABLE IF EXISTS `task`;
CREATE TABLE IF NOT EXISTS `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `limit_date` timestamp NULL DEFAULT NULL,
  `card_id` int(11) NOT NULL,
  `creator_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_card_id` (`card_id`),
  KEY `fk_creator_id` (`creator_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `taskuser`
--

DROP TABLE IF EXISTS `taskuser`;
CREATE TABLE IF NOT EXISTS `taskuser` (
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`task_id`,`user_id`),
  KEY `fk_id_user` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Les contraintes
--

SET foreign_key_checks = 1;
ALTER TABLE `kanbanuser`
	ADD CONSTRAINT `fk_kanban_id`
	FOREIGN KEY (`kanban_id`)
	REFERENCES `kanban`(`id`)
	ON DELETE NO ACTION
	ON UPDATE CASCADE;
ALTER TABLE `kanbanuser`
	ADD CONSTRAINT `fk_user_id`
	FOREIGN KEY (`user_id`)
	REFERENCES `user`(`id`)
	ON DELETE NO ACTION
	ON UPDATE CASCADE;
ALTER TABLE `taskuser`
	ADD CONSTRAINT `fk_task_id`
	FOREIGN KEY (`task_id`)
	REFERENCES `task`(`id`)
	ON DELETE NO ACTION
	ON UPDATE CASCADE;
ALTER TABLE `taskuser`
	ADD CONSTRAINT `fk_id_user`
	FOREIGN KEY (`user_id`)
	REFERENCES `user`(`id`)
	ON DELETE NO ACTION
	ON UPDATE CASCADE;
ALTER TABLE `task`
	ADD CONSTRAINT `fk_card_id`
	FOREIGN KEY (`card_id`)
	REFERENCES `card`(`id`)
	ON DELETE NO ACTION
	ON UPDATE CASCADE;
ALTER TABLE `task`
	ADD CONSTRAINT `fk_creator_id`
	FOREIGN KEY (`creator_id`)
	REFERENCES `user`(`id`)
	ON DELETE NO ACTION
	ON UPDATE CASCADE;
ALTER TABLE `card`
	ADD CONSTRAINT `fk_kanban_id`
	FOREIGN KEY (`kanban_id`)
	REFERENCES `kanban`(`id`)
	ON DELETE NO ACTION
	ON UPDATE CASCADE;
ALTER TABLE `kanban`
	ADD CONSTRAINT `fk_creator_id`
	FOREIGN KEY (`creator_id`)
	REFERENCES `user`(`id`)
	ON DELETE NO ACTION
	ON UPDATE CASCADE;