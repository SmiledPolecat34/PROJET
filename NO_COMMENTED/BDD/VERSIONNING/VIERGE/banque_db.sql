-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 12 oct. 2024 à 16:01
-- Version du serveur : 11.1.2-MariaDB
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `banque_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `beneficiaires`
--

DROP TABLE IF EXISTS `beneficiaires`;
CREATE TABLE IF NOT EXISTS `beneficiaires` (
  `beneficiaireId` int(11) NOT NULL AUTO_INCREMENT,
  `clientId` int(11) NOT NULL,
  `beneficiaireCompteId` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  PRIMARY KEY (`beneficiaireId`),
  KEY `clientId` (`clientId`),
  KEY `beneficiaireCompteId` (`beneficiaireCompteId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `clientId` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `telephone` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `mdp` varchar(150) NOT NULL,
  `dateCreation` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`clientId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comptebancaire`
--

DROP TABLE IF EXISTS `comptebancaire`;
CREATE TABLE IF NOT EXISTS `comptebancaire` (
  `compteId` int(11) NOT NULL AUTO_INCREMENT,
  `numeroCompte` varchar(27) NOT NULL,
  `solde` decimal(10,2) DEFAULT 0.00,
  `typeDeCompte` enum('Courant','Epargne','Entreprise') NOT NULL,
  `dateOuverture` timestamp NULL DEFAULT current_timestamp(),
  `clientId` int(11) DEFAULT NULL,
  PRIMARY KEY (`compteId`),
  UNIQUE KEY `numeroCompte` (`numeroCompte`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transferts`
--

DROP TABLE IF EXISTS `transferts`;
CREATE TABLE IF NOT EXISTS `transferts` (
  `transfertId` int(11) NOT NULL AUTO_INCREMENT,
  `emetteurId` int(11) NOT NULL,
  `destinataireId` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `motif` varchar(255) NOT NULL,
  `dateTransfert` datetime NOT NULL,
  `emetteurCompteId` int(11) NOT NULL,
  `beneficiaireCompteId` int(11) NOT NULL,
  PRIMARY KEY (`transfertId`),
  KEY `emetteurId` (`emetteurId`),
  KEY `destinataireId` (`destinataireId`),
  KEY `emetteurCompteId` (`emetteurCompteId`),
  KEY `beneficiaireCompteId` (`beneficiaireCompteId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `beneficiaires`
--
ALTER TABLE `beneficiaires`
  ADD CONSTRAINT `beneficiaires_ibfk_3` FOREIGN KEY (`beneficiaireCompteId`) REFERENCES `comptebancaire` (`compteId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `comptebancaire`
--
ALTER TABLE `comptebancaire`
  ADD CONSTRAINT `comptebancaire_ibfk_1` FOREIGN KEY (`clientId`) REFERENCES `client` (`clientId`);

--
-- Contraintes pour la table `transferts`
--
ALTER TABLE `transferts`
  ADD CONSTRAINT `transferts_ibfk_1` FOREIGN KEY (`emetteurId`) REFERENCES `client` (`clientId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transferts_ibfk_2` FOREIGN KEY (`destinataireId`) REFERENCES `client` (`clientId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transferts_ibfk_3` FOREIGN KEY (`emetteurCompteId`) REFERENCES `comptebancaire` (`compteId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transferts_ibfk_4` FOREIGN KEY (`beneficiaireCompteId`) REFERENCES `comptebancaire` (`compteId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
