-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : sam. 28 fév. 2026 à 09:01
-- Version du serveur : 8.0.44
-- Version de PHP : 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `Macopres`
--

-- --------------------------------------------------------

--
-- Structure de la table `employe`
--

CREATE TABLE `employe` (
  `idEmploye` int NOT NULL,
  `matricule` varchar(50) DEFAULT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `dateNaissance` date DEFAULT NULL,
  `sexe` enum('H','F') NOT NULL,
  `tel` int DEFAULT NULL,
  `adresse` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `departement` enum('ad','c','m','f') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `code` enum('30','35','40','45','ap','sa') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `dateEmbauche` date DEFAULT NULL,
  `photo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'photo_defaut.jpeg',
  `dateCreation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `employe`
--

INSERT INTO `employe` (`idEmploye`, `matricule`, `nom`, `prenom`, `dateNaissance`, `sexe`, `tel`, `adresse`, `departement`, `code`, `dateEmbauche`, `photo`, `dateCreation`) VALUES
(5, 'MCPRS25/26-C5', 'ndiaye', 'ibrahima', '2026-02-04', 'H', 778889900, 'dakar', 'c', '45', '2026-02-11', 'photo_defaut.jpeg', '2026-02-21 10:53:35'),
(6, 'MCPRS25/26-M6', 'fall', 'ibou', '2026-02-05', 'H', 778888888, 'dakar', 'm', '45', '2026-02-25', 'photo_defaut.jpeg', '2026-02-21 10:55:15'),
(7, 'MCPRS25/26-AD7', 'faty', 'moussa', '2026-02-04', 'H', 776665544, 'dakar', 'ad', 'sa', '2026-02-12', 'photo_defaut.jpeg', '2026-02-21 10:55:54'),
(12, 'MCPRS25/26-AD12', 'ba', 'ndeye nogaye', '2004-11-27', 'F', 773245450, 'Golf cité aliou sow n°609', 'ad', 'sa', '2024-12-30', 'photo_defaut.jpeg', '2026-02-25 06:07:30'),
(13, 'MCPRS25/26-F13', 'ba', 'pape moussa ', '2006-10-16', 'H', 773150365, 'Golf cité aliou sow n°609', 'f', '30', '2026-02-06', 'photo_defaut.jpeg', '2026-02-25 10:07:29'),
(18, 'MCPRS25/26-C18', 'thiam', 'ibrahima', '2026-02-05', 'H', 776668877, 'thies', 'c', '30', '2026-02-25', 'photo_defaut.jpeg', '2026-02-25 13:04:44'),
(21, 'MCPRS25/26-C21', 'ndiaye', 'diouma', '2026-02-10', 'F', 770000000, 'Golf cité aliou sow n°609', 'c', '35', '2026-02-14', 'photo_defaut.jpeg', '2026-02-27 21:34:35');

-- --------------------------------------------------------

--
-- Structure de la table `pointage`
--

CREATE TABLE `pointage` (
  `idPointage` int NOT NULL,
  `idEmploye` int NOT NULL,
  `datePointage` datetime NOT NULL,
  `heureArrive` time NOT NULL,
  `heureDepart` time NOT NULL,
  `montantEntrant` int NOT NULL,
  `montantSortie` int NOT NULL,
  `solde` int NOT NULL,
  `retard` tinyint(1) NOT NULL DEFAULT '0',
  `tempsRetard` varchar(50) DEFAULT NULL,
  `dateEnregistrement` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idUser` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `idRole` int NOT NULL,
  `nomRole` varchar(50) NOT NULL,
  `description` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`idRole`, `nomRole`, `description`) VALUES
(1, 'PDG', 'Il est le directeur général et a toutes les autorisations.'),
(2, 'Assistant(e)', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `saison`
--

CREATE TABLE `saison` (
  `idSaison` int NOT NULL,
  `libelle` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `idUser` int NOT NULL,
  `prenom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nom` varchar(50) NOT NULL,
  `tel` int NOT NULL,
  `login` varchar(50) NOT NULL,
  `motDePasse` varchar(200) NOT NULL,
  `photoDeProfil` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `statut` enum('1','0') NOT NULL DEFAULT '1',
  `idRole` int DEFAULT NULL,
  `dateCreation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`idUser`, `prenom`, `nom`, `tel`, `login`, `motDePasse`, `photoDeProfil`, `statut`, `idRole`, `dateCreation`) VALUES
(1, 'mass', 'ba', 776594218, 'pdg', '$2y$10$brkATCkJWECs2vY9EbvE.O9c.vfnafpxhOnexUjCeocrP73Tyw/NK', 'photo_pdg.jpeg', '1', 1, '2026-02-22 14:35:14'),
(6, 'ndeye nogaye', 'ba', 773245450, 'ndeye nogaye', '$2y$10$okKG.CMvJlZT8i4ndMAnh.hRvDsMPHohEY/iIkg9ZtqAT3atFwb4K', NULL, '1', 2, '2026-02-25 06:07:31');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `employe`
--
ALTER TABLE `employe`
  ADD PRIMARY KEY (`idEmploye`);

--
-- Index pour la table `pointage`
--
ALTER TABLE `pointage`
  ADD PRIMARY KEY (`idPointage`),
  ADD KEY `fk_pointage_employe` (`idEmploye`),
  ADD KEY `fk_pointage_user` (`idUser`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`idRole`);

--
-- Index pour la table `saison`
--
ALTER TABLE `saison`
  ADD PRIMARY KEY (`idSaison`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`idUser`),
  ADD KEY `fk_user_role` (`idRole`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `employe`
--
ALTER TABLE `employe`
  MODIFY `idEmploye` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `pointage`
--
ALTER TABLE `pointage`
  MODIFY `idPointage` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `idRole` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `saison`
--
ALTER TABLE `saison`
  MODIFY `idSaison` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `idUser` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `pointage`
--
ALTER TABLE `pointage`
  ADD CONSTRAINT `fk_pointage_employe` FOREIGN KEY (`idEmploye`) REFERENCES `employe` (`idEmploye`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_pointage_user` FOREIGN KEY (`idUser`) REFERENCES `user` (`idUser`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_role` FOREIGN KEY (`idRole`) REFERENCES `role` (`idRole`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
