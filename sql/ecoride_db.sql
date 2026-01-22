-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 22 jan. 2026 à 12:15
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `avis_id` int(11) NOT NULL,
  `passager_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `trajet_id` int(11) NOT NULL,
  `commentaire` text DEFAULT NULL,
  `note` int(11) DEFAULT NULL CHECK (`note` >= 1 and `note` <= 5),
  `est_valide` tinyint(1) DEFAULT 0,
  `etoiles` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`avis_id`, `passager_id`, `utilisateur_id`, `trajet_id`, `commentaire`, `note`, `est_valide`, `etoiles`) VALUES
(25, 6, 1, 0, 'Super voyage !', 5, 1, 5),
(26, 5, 2, 0, 'Bonne conduite.', 4, 1, 4),
(27, 4, 3, 0, 'Ponctuel et sympathique.', 4, 1, 4),
(28, 3, 4, 0, 'Conduite très prudente.', 4, 1, 3),
(29, 2, 5, 0, 'Bon voyage.', 4, 1, 4),
(30, 1, 6, 0, 'Je recommande !', 5, 1, 3),
(34, 8, 3, 3, 'super sympa', 4, 0, 4),
(35, 1, 8, 6, 'Voiture miteuse', 1, 0, 1),
(37, 4, 8, 2, 'très désagréable', 2, 0, 2),
(38, 2, 8, 18, 'Correct', 3, 1, 3);

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `reservation_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `trajet_id` int(11) NOT NULL,
  `date_reservation` datetime NOT NULL,
  `statut` enum('attente','en_cours','termine') DEFAULT 'attente',
  `commission_credit` decimal(10,2) DEFAULT 2.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`reservation_id`, `utilisateur_id`, `trajet_id`, `date_reservation`, `statut`, `commission_credit`) VALUES
(17, 8, 6, '2026-01-16 12:20:50', 'attente', 2.00);

-- --------------------------------------------------------

--
-- Structure de la table `trajet`
--

CREATE TABLE `trajet` (
  `trajet_id` int(11) NOT NULL,
  `ville_depart` varchar(100) NOT NULL,
  `ville_arrivee` varchar(100) NOT NULL,
  `date_depart` date NOT NULL,
  `heure_depart` time NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `nb_place` int(11) NOT NULL,
  `chauffeur_id` int(11) NOT NULL,
  `heure_arrivee` time NOT NULL,
  `voiture_id` int(11) NOT NULL,
  `statut` enum('attente','en_cours','termine') DEFAULT 'attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `trajet`
--

INSERT INTO `trajet` (`trajet_id`, `ville_depart`, `ville_arrivee`, `date_depart`, `heure_depart`, `prix`, `nb_place`, `chauffeur_id`, `heure_arrivee`, `voiture_id`, `statut`) VALUES
(1, 'Paris', 'Lyon', '2025-10-25', '08:00:00', 15.00, 1, 1, '13:00:00', 6, 'attente'),
(2, 'Paris', 'Bordeaux', '2025-10-26', '08:30:00', 55.00, 2, 2, '14:30:00', 5, 'attente'),
(3, 'Lyon', 'Paris', '2025-10-27', '13:00:00', 20.00, 2, 3, '18:00:00', 4, 'attente'),
(4, 'Paris', 'Lyon', '2025-10-28', '09:15:00', 15.00, 3, 4, '14:15:00', 3, 'attente'),
(5, 'Lyon', 'Paris', '2025-10-29', '04:00:00', 10.00, 3, 5, '09:00:00', 2, 'attente'),
(6, 'Lille', 'Marseille', '2025-10-30', '05:00:00', 70.00, 3, 6, '14:00:00', 1, 'attente'),
(18, 'test', 'test', '2026-01-12', '00:00:00', 12.00, 0, 8, '01:00:00', 10, 'termine');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `utilisateur_id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `sexe` enum('H','F','Autre') DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `credit` int(11) DEFAULT 20,
  `role` enum('utilisateur','employe','admin') DEFAULT 'utilisateur',
  `est_suspendu` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`utilisateur_id`, `nom`, `prenom`, `sexe`, `email`, `password`, `photo_profil`, `credit`, `role`, `est_suspendu`) VALUES
(1, 'P.', 'Jean', 'H', 'jean.p@exemple.com', 'hash_password_1', NULL, 20, 'utilisateur', 0),
(2, 'S.', 'Julie', 'F', 'julie.s@exemple.com', 'hash_password_2', NULL, 20, 'utilisateur', 0),
(3, 'L.', 'Max', 'H', 'max.l@exemple.com', 'hash_password_3', NULL, 20, 'utilisateur', 0),
(4, 'N.', 'Marie', 'F', 'marie.n@exemple.com', 'hash_password_4', NULL, 20, 'utilisateur', 0),
(5, 'H.', 'Teo', 'Autre', 'teo.h@exemple.com', 'hash_password_5', NULL, 20, 'utilisateur', 0),
(6, 'D.', 'Baptiste', 'H', 'baptiste.d@exemple.com', 'hash_password_6', NULL, 20, 'utilisateur', 0),
(8, 'Test', 'Test', 'H', 'test@mail.com', '$2y$10$qp1d5IF/xkRV0/94TdZ84eNgu1OI7zXySGmBtRqCc1ZjwSDSrU93K', 'profil_8_1768399180.jpg', 987, 'utilisateur', 0),
(9, 'TestAdmin', 'Admin', 'H', 'admin@mail.com', '$2y$10$.Hdvq/tPSHt3ptQNexM.5en1mACC9V7Z6uHTiaLXGEupmzm16lDly', NULL, 20, 'admin', 0),
(10, 'TestEmploye', 'Employe', 'H', 'employe@mail.com', '$2y$10$yrhUsueibtee93UYN/gsO.8nIHakEq2RN/ZgCNv9gwde132hwIlSm', NULL, 20, 'employe', 0);

-- --------------------------------------------------------

--
-- Structure de la table `voiture`
--

CREATE TABLE `voiture` (
  `voiture_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `marque` varchar(50) NOT NULL,
  `modele` varchar(50) NOT NULL,
  `immatriculation` varchar(20) NOT NULL,
  `date_immatriculation` date DEFAULT NULL,
  `couleur` varchar(30) DEFAULT NULL,
  `est_electrique` tinyint(1) DEFAULT 0,
  `pref_fumeur` tinyint(1) DEFAULT 0,
  `pref_animal` tinyint(1) DEFAULT 0,
  `places_disponibles` int(11) NOT NULL DEFAULT 3,
  `categorie` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `voiture`
--

INSERT INTO `voiture` (`voiture_id`, `utilisateur_id`, `marque`, `modele`, `immatriculation`, `date_immatriculation`, `couleur`, `est_electrique`, `pref_fumeur`, `pref_animal`, `places_disponibles`, `categorie`) VALUES
(1, 1, '', 'Tesla Model 3', 'PH-456-FE', NULL, 'Blanc', 1, 0, 0, 3, 'Pas de nourriture'),
(2, 2, '', 'Lexus LX 570', 'ZI-864-LG', NULL, 'Grise', 0, 0, 0, 3, 'Pas de nourriture'),
(3, 3, '', 'Tesla Cybertruck', 'JK-546-GE', NULL, 'Vert', 1, 0, 0, 3, 'Pas de nourriture'),
(4, 4, '', 'Mercedes-Benz AMG G 65', 'YE-974-WP', NULL, 'Noire', 0, 0, 0, 3, 'Pas de nourriture'),
(5, 5, '', 'Tesla Model 3', 'RH-862-ZH', NULL, 'Noire', 1, 0, 0, 3, 'Pas de nourriture'),
(6, 6, '', 'Renault Clio 5', 'GH-646-EZ', NULL, 'Rouge', 0, 0, 0, 3, 'Pas de nourriture'),
(17, 8, '', 'Tesla Model 3', 'ER-785-DF', '2021-06-19', '', 1, 0, 1, 3, 'Pas de nourritures');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`avis_id`),
  ADD KEY `passager_id` (`passager_id`),
  ADD KEY `chauffeur_id` (`utilisateur_id`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `trajet_id` (`trajet_id`);

--
-- Index pour la table `trajet`
--
ALTER TABLE `trajet`
  ADD PRIMARY KEY (`trajet_id`),
  ADD KEY `chauffeur_id` (`chauffeur_id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`utilisateur_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD PRIMARY KEY (`voiture_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `avis_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `trajet`
--
ALTER TABLE `trajet`
  MODIFY `trajet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `utilisateur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `voiture`
--
ALTER TABLE `voiture`
  MODIFY `voiture_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`trajet_id`) REFERENCES `trajet` (`trajet_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `trajet`
--
ALTER TABLE `trajet`
  ADD CONSTRAINT `trajet_ibfk_1` FOREIGN KEY (`chauffeur_id`) REFERENCES `utilisateur` (`utilisateur_id`);

--
-- Contraintes pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD CONSTRAINT `voiture_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`utilisateur_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
