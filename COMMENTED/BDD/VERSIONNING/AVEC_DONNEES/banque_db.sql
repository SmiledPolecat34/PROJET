-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 12 oct. 2024 à 16:02
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `beneficiaires`
--

INSERT INTO `beneficiaires` (`beneficiaireId`, `clientId`, `beneficiaireCompteId`, `nom`, `prenom`) VALUES
(3, 11, 6, 'Versayo', 'Franklin Entreprise'),
(5, 19, 6, 'Versayo', 'Franklin'),
(6, 19, 9, 'qscd', 'qsfdbg');

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`clientId`, `nom`, `prenom`, `telephone`, `email`, `mdp`, `dateCreation`) VALUES
(10, 'VERSAYO', 'FRANKLIN', '0784983989', 'Versayo.franklin@gmail.com', '$2y$10$aZACRmVVLulaBlPptpKQQupy7YUOc5VZM7m/aOBQQhxkR.4qyraki', '2024-10-12 12:18:12'),
(11, 'test', 'testeur', '0972095822', 'test1@gmail.com', '$2y$10$kdMpbhcKMcBic/YnoTjQvueZxTAQkdFFzSPyGKMVlWlT0aua8dL6S', '2024-10-12 12:19:05'),
(12, 'test2', 'Fae', '0427590829', 'waw@koosa.com', '$2y$10$uVChTZULDVhNx4udSWyZAeMPcfzNlmeFyF0O.NrusPPvUGPbOgMvG', '2024-10-12 12:47:45'),
(13, 'Versayo', 'karine', '0962952749', 'a@a.com', '$2y$10$vLB2JFRPtUFg1YqebWBBCu06I/jFzkFzOE379YYxX8hHP4hA0vyf6', '2024-10-12 15:18:26'),
(15, 'VERSAYO', 'Tai', '0784983989', 'b@b.com', '$2y$10$zUJdrKihbUZgbJGfbXJdyex/HmeQDU1mZ143ASpFQp.V4sD.kM.ae', '2024-10-12 15:24:54'),
(16, 'VERSAYO', 'FRANKLIN', '0784983989', 'svdsf@gmail.com', '$2y$10$SWmQ4jGsOIBzhyNkCBQXH.mHtWtoinZF7EhEvuhXh8Wn2kCNEFfF.', '2024-10-12 15:25:48'),
(17, 'VERSAYO', 'FRANKLIN', '0784983989', 'sssf@gmail.com', '$2y$10$TGQIuKwhaTA71G54PfT9SOtDk4QGw2NUvLQlN/D37V.cJQ6.CyKYC', '2024-10-12 15:26:08'),
(18, 'Versayo', 'Franklin', '0762199414', 'zefgtffr@a.com', '$2y$10$4SR23THmZEDYoBKw/UHuC.p1VXU9D7cVNdYkrMMNS1.xvSVDXvKQK', '2024-10-12 15:27:11'),
(19, 'VERSAYO', 'FRANKLIN', '0784983989', 'Versayo.fsqdfbgranklin@gmail.com', '$2y$10$TtTsGAM1vRTL.9WxGuKgRewABTFH/kzdlSN9smWA64yi6xJeTlI0C', '2024-10-12 15:27:56');

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comptebancaire`
--

INSERT INTO `comptebancaire` (`compteId`, `numeroCompte`, `solde`, `typeDeCompte`, `dateOuverture`, `clientId`) VALUES
(6, 'FR7458523146713578891796289', '1386.00', 'Entreprise', '2024-10-12 10:18:23', 10),
(7, 'FR5112189800411031341382490', '370.00', 'Epargne', '2024-10-12 10:19:11', 11),
(8, 'FR0905734176493497199845900', '200.00', 'Courant', '2024-10-12 10:21:13', 10),
(9, 'FR9004169933918844186553519', '200.00', 'Epargne', '2024-10-12 10:24:52', 10),
(10, 'FR9948935577699013996936783', '300.00', 'Epargne', '2024-10-12 10:25:01', 10),
(11, 'FR9423740026837007718818136', '300.00', 'Courant', '2024-10-12 10:47:51', 12),
(12, 'FR9033493157540617492286580', '503.00', 'Epargne', '2024-10-12 13:27:20', 18),
(13, 'FR3217534449611882698450758', '242.00', 'Courant', '2024-10-12 13:28:01', 19);

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `transferts`
--

INSERT INTO `transferts` (`transfertId`, `emetteurId`, `destinataireId`, `montant`, `motif`, `dateTransfert`, `emetteurCompteId`, `beneficiaireCompteId`) VALUES
(1, 10, 11, '100.00', 'Test1', '2024-10-12 12:35:53', 6, 7),
(2, 10, 11, '100.00', 'Test2', '2024-10-12 12:37:58', 6, 7),
(3, 12, 10, '156.00', 'Waw', '2024-10-12 12:49:02', 11, 6),
(8, 11, 10, '130.00', 'Test7', '2024-10-12 15:36:57', 7, 6);

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
