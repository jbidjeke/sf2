-- phpMyAdmin SQL Dump
-- version 4.1.14.8
-- http://www.phpmyadmin.net
--
-- Client :  db652072668.db.1and1.com
-- Généré le :  Sam 07 Janvier 2017 à 20:58
-- Version du serveur :  5.5.52-0+deb7u1-log
-- Version de PHP :  5.4.45-0+deb7u6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `db652072668`
--

-- --------------------------------------------------------

--
-- Structure de la table `Category`
--

CREATE TABLE IF NOT EXISTS `Category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

--
-- Contenu de la table `Category`
--

INSERT INTO `Category` (`id`, `name`) VALUES
(2, 'Voitures'),
(3, 'Motos'),
(4, 'Nautisme'),
(5, 'Ventes immobilieres'),
(6, 'Locations'),
(7, 'Colocation'),
(8, 'Locations '),
(9, 'Bureaux et Commerces'),
(10, 'Maisons'),
(11, 'Loisirs'),
(12, 'Materiel Professionnel'),
(13, 'Emploi Services'),
(14, 'Media'),
(15, 'Covoiturage');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
