-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 05 mars 2026 à 14:56
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bibliotheque_projet_semaine_6`
--

-- --------------------------------------------------------

--
-- Structure de la table `lecteurs`
--

DROP TABLE IF EXISTS `lecteurs`;
CREATE TABLE IF NOT EXISTS `lecteurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `lecteurs`
--

INSERT INTO `lecteurs` (`id`, `nom`, `prenom`, `email`) VALUES
(1, 'ATCHA', 'Alexandre', 'alexandreatcha440@gmail.com'),
(2, 'Colette', 'ATCHA', 'colette@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `liste_lecture`
--

DROP TABLE IF EXISTS `liste_lecture`;
CREATE TABLE IF NOT EXISTS `liste_lecture` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_livre` int NOT NULL,
  `id_lecteur` int NOT NULL,
  `date_emprunt` date NOT NULL,
  `date_retour` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `liste_lecture`
--

INSERT INTO `liste_lecture` (`id`, `id_livre`, `id_lecteur`, `date_emprunt`, `date_retour`) VALUES
(1, 22, 1, '2025-11-28', '0000-00-00'),
(2, 38, 1, '2025-11-28', '0000-00-00'),
(3, 22, 2, '2025-11-28', '0000-00-00'),
(4, 24, 2, '2025-11-28', '0000-00-00'),
(5, 40, 2, '2025-11-28', '2025-11-28'),
(6, 23, 1, '2025-11-29', '0000-00-00'),
(7, 59, 2, '2025-11-30', '0000-00-00'),
(8, 47, 1, '2025-11-30', '0000-00-00'),
(9, 50, 1, '2025-12-02', '0000-00-00'),
(10, 44, 1, '2025-12-02', '0000-00-00'),
(11, 60, 1, '2026-01-30', '0000-00-00'),
(12, 43, 1, '2026-02-13', '0000-00-00');

-- --------------------------------------------------------

--
-- Structure de la table `livres`
--

DROP TABLE IF EXISTS `livres`;
CREATE TABLE IF NOT EXISTS `livres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) NOT NULL,
  `auteur` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `maison_edition` varchar(100) NOT NULL,
  `nombre_exemplaire` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `livres`
--

INSERT INTO `livres` (`id`, `titre`, `auteur`, `description`, `maison_edition`, `nombre_exemplaire`) VALUES
(41, 'Le Petit Prince', 'Antoine de Saint-Exupéry', 'Le Petit Prince est un roman français. Livre à succès, il est l\'oeuvre la plus connue d\'Antoine de Saint-Exupéry. Publié en anglais le 6 avril 1943 à New York par l\'éditeur Hitchcock. Paru en plus de six cents langues et dialectes différent.Le Petit Prince est l\'ouvrage la plus traduit au monde après la Bible. Le narrateur est un aviateur qui, à la suite d\'une panne moteur, doit se poser en catastrophe dans le  désert du sahara et tente seul de réparer son avion.', 'Reynal & Hitchcock', 15),
(42, 'L\'Etranger', 'Albert Camus', 'L\'Etranger est le premier roman publié d\'Albert Camus, paru en 1942. Les premières esquisses datent de 1938, mais le roman ne prend vraiment forme que dans les premiers mois de 1940 et sera travaillé par Camus jusqu\'en 1941. Il prend place dans la tétralogie que Camus nommera Cycle de l\'absurde qui décrit les fondements de la philosophie camusienne : l\'absurde. Cette tétralogie comprend également l\'essai. Le Mythe de Sisyphe ainsi que les pièces de théâtre Caligula et Le Malentendu', 'Frémeaux & Associés', 7),
(43, '1984', 'George Orwell', 'Novlangue, police de la pensée, Big Brother...Soixante-dix ans après la publication du roman d\'anticipation de George Orwell, les concepts clés de 1984 sont devenus des références essentielles pour comprendre les ressorts totalitaires de la société actuelle. Dans un monde où la télésurveillance s\'est généralisée, où la numérisation a donné un élan sans précédant au pouvoir et à l\'arbitraire des administration. Où le passé tend à se dissoudre dans l\'éternel présent de l\'innovation, le cef Orwell est à redécouvrir dans une nouvelle traduction et une édition critique.', 'Les éditions de rue Dorion', 15),
(44, 'Les Misérables', 'Victor Hugo', 'La foule des malheureux contre qui s\'acharnent la mailice du sort et la malignité de l\'homme, Hugo a su la peindre sous des aspects aussi atroces que grandiose dans des tableaux dont la plupart sont désormais classiques. C\'est Jean Valjean, ce rude gaillard qui peut, d\'un coup de reins, soulever une charrette embourbée, cet acrobate aux évasions spectaculaires, cet ancien forçat qui peut-être un saint...C\'est Gavroche, le petit Parusuen gouailleur. Ce sont Cosette, la fillette martyre, Javert, le policier inflexible, des malheureux et des coquins : Les Misérables', 'Gallimard', 11),
(45, 'Harry Potter à l\'école des sociers', 'J.K. Rowling', 'Le début de ma saga Harry Potter. Voler en balai, jeter des sorts, combattre les trolls : Harry révèle de grands talents. Sorti à Londrs le 26 juin 1997, il est initialement tiré à 500 examplaires, puis connaît au fil des mois un succès grandissant. En 2001, le volume est adapté au cinéma. Il trouve son importance puisqu\'il sert de base introductve aux tomes de la série qu\'à la pièce de théâtre Harry et l\'Enfant maudit', 'Gallimard', 26),
(46, 'Le Petit Voyageur', 'S. M\'Baye', 'Un court roman de voyage et de reflexion. L\'auteur emmène le lecteur à travers des paysages et rencontrs qui interrogent l\'identité et le sens du dépacement.', 'Editions Soleil', 5),
(47, 'Initiation au JavaScript', 'M. Koffi', 'Guide pratique pou débuter en JavaScript. Comprend des exercices et des exemples pour construire des applications web interactives.', 'TechPress', 8),
(48, 'Histoire de l\'Afrique', 'A. Touré', 'Un panorama historique des grandes époques africaines. Le livre propose une synthèse accessible pour les étudiants et curieux.', 'Anthologie', 3),
(49, 'Design web Moderne', 'L. Saga', 'Principes et pratique du design web comtemporain. Traite du responsive, de l\'accessibilité et des bonnes pratiques UX/UI.', 'WebLivre', 9),
(50, 'Algorithmes et structures', 'P. N\'Diaye', 'Cours d\'algorithmes pour informaticiens. Des exercices corrigés et des illustration complètent chaque chapitre.', 'Université Press', 2),
(51, 'Economie pour tous', 'F. Akpo', 'Concepts économiques expliqués de manière simple et terre à terre. Idéal pour les débutants en économie, pour les chercheurs passionnés et por les citoyens curieux. Ce livre offre des exercices de tout type et des corrigés.', 'EcoEditions', 7),
(52, 'Photographie créative', 'R. Dossou', 'Techniques et astuces pour photographes amateur. Beaucoup d\'exemples pratiques et de projets à réaliser grâce à ce livre. Vous soyez un photograpes amateurs capables de faire des photos claires et attrayantes, des shotting, des photos de tout genre: photos d\'identité, photo professionnelle etc; ce livre est pour vous. Il vous permet de franchir un étape dans les photos.', 'ImagePress', 2),
(53, 'Programmation PHP avancée', 'C. Mensah', 'Approfondissement des concepts PHP php moderne. Sécurité, PDO, patterns et deploiement en production.', 'DevBooks', 5),
(54, 'Gestion de projet Agile', 'D. Kouassi', 'Méthodes agiles pour gérer efficacement les projets. Exemples concrets en entreprise et sur des cas réels.', 'ProManage', 6),
(55, 'Cuisine du Monde', 'S. Adjovi', 'Recettes et techniques culinaires internationales. Des plats simples aux recettes plus élaborées, illustrés par étape. ', 'Gastronome', 3),
(56, 'Ces différences qui nous rassembles', 'Daniel Henkel', 'Dans cet ouvrage, avec franchise, humilité, humour et, je pense, bon sens, je m\'éfforce de traiter sans faux-semblants les grands sujets qui concerne chacun, des thèmes au fil desquels une vérité s\'impose: quelque part sur le chemin, nous avons abandonné l\'essentiel, le contact humain.', 'Plon', 19),
(57, 'Archictecture des systèmes', 'B. Kouman', 'Principes d\'architecture pour systèmes distribués. Etudes de cas et bonne pratique pour concevoir des architectures robustes.', 'UniPress', 11),
(58, 'Poèmes de la ville', 'L. Sissoko', 'Recueil de poèmes inspirés par la vie urbaine. Rythmes, images et émotions se mêlent pour peindre la cité.', 'Rivages', 8),
(59, 'Blockchain expliquée', 'N. Houngbédji', 'Concepts et usages de la blockchain. Applications, smart-contrats et impact sur l\'économie numérique.', 'TechPress', 12),
(60, 'Apologie de Socrate', 'Platon', 'L\'Appologie de Socrate est un récit du procès de Socrate à Athènes en 399 av. Jésus-Christ où il est accusé de corrompre la jeunesse et d\'impiété. Le livre, qui est une défence de Socrate par lui même, dénonce les dangers de la mauvaise foi et de l\'ignorance et défend la philosophie comme la seule quête digne d\'être vécue. Le texte, bien qu\'une plaidoirie, est considéré comme un texte fondateur de la philosophie.', 'FLAMMARION', 22),
(61, 'Entrepreneuriat local', 'K. Adé', 'Creer et développer une PME locale. Conseils pratique, étude de marché et gestion financière.', 'BusinssBooks', 7),
(62, 'Energies Renouvelables', 'P. Zannou ', 'Techniques et enjeux des éneergies propres. Analyses et solutions adaptées aux contexte locaux.', 'GreenEditions', 4);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
