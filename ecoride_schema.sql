-- =========================================================
-- EcoRide - Script SQL MySQL (XAMPP / MySQL 8+)
-- Version figée (conforme à ta demande)
--
-- Objectifs :
-- 1) Garder la table CONFIGURATION avec 2 champs :
--      - id_configuration (PK auto_increment)
--      - id_utilisateur   (FK -> utilisateur.id_utilisateur)
-- 2) Supprimer la table parametre_configuration (et toute redondance)
-- 3) Avoir la table PARAMETRE comme sur ta capture :
--      - parametre_id
--      - id_configuration (FK -> configuration.id_configuration)
--      - propriete
--      - valeur
-- 4) Avoir EXACTEMENT le même jeu de données que ta capture pour :
--      - utilisateur (2 lignes : Michel, Sophie)
--      - configuration (3 lignes : (1,1) (2,1) (3,2))
--      - parametre (5 lignes, ids/valeurs identiques)
-- 5) Remplir aussi les autres tables avec quelques exemples.
-- 6) La table ROLE doit contenir 4 valeurs prédéfinies (énoncé) :
--      UTILISATEUR, CHAUFFEUR, EMPLOYE, ADMINISTRATEUR
-- =========================================================

DROP DATABASE IF EXISTS ecoride;
CREATE DATABASE ecoride CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ecoride;

SET sql_mode = 'STRICT_ALL_TABLES';

-- =========================================================
-- 1) TABLES (entités)
-- =========================================================

CREATE TABLE utilisateur (
  id_utilisateur INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom            VARCHAR(50),
  prenom         VARCHAR(50),
  email          VARCHAR(50),
  password       VARCHAR(255),
  telephone      VARCHAR(50),
  adresse        VARCHAR(100),
  date_naissance VARCHAR(50),
  photo          VARCHAR(50),
  pseudo         VARCHAR(50)
) ENGINE=InnoDB;

CREATE TABLE configuration (
  id_configuration INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur   INT UNSIGNED NOT NULL,
  CONSTRAINT fk_configuration_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE parametre (
  parametre_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_configuration INT UNSIGNED NOT NULL,
  propriete        VARCHAR(50),
  valeur           VARCHAR(50),
  CONSTRAINT fk_parametre_configuration
    FOREIGN KEY (id_configuration) REFERENCES configuration(id_configuration)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE voiture (
  id_voiture INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  modele VARCHAR(50),
  immatriculation VARCHAR(50),
  energie VARCHAR(50),
  couleur VARCHAR(50),
  date_premiere_immatriculation VARCHAR(50)
) ENGINE=InnoDB;

CREATE TABLE marque (
  id_marque INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(50)
) ENGINE=InnoDB;

CREATE TABLE role (
  id_role INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE covoiturage (
  id_covoiturage INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date_depart VARCHAR(50),
  heure_depart VARCHAR(50),
  lieu_depart VARCHAR(50),
  date_arrivee VARCHAR(50),
  heure_arrivee VARCHAR(50),
  lieu_arrivee VARCHAR(50),
  statut VARCHAR(50),
  nb_place INT,
  prix_personne INT
) ENGINE=InnoDB;

CREATE TABLE avis (
  id_avis INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_covoiturage INT UNSIGNED NOT NULL,
  commentaire TEXT,
  note TINYINT UNSIGNED NULL,
  statut VARCHAR(20) NOT NULL,
  CONSTRAINT fk_avis_covoiturage
    FOREIGN KEY (id_covoiturage)
    REFERENCES covoiturage(id_covoiturage)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 2) TABLES D’ASSOCIATION
-- =========================================================

CREATE TABLE gere (
  id_utilisateur INT UNSIGNED,
  id_voiture     INT UNSIGNED,
  PRIMARY KEY(id_utilisateur, id_voiture),
  CONSTRAINT fk_gere_utilisateur FOREIGN KEY (id_utilisateur)
    REFERENCES utilisateur(id_utilisateur)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_gere_voiture FOREIGN KEY (id_voiture)
    REFERENCES voiture(id_voiture)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE utilise (
  id_voiture     INT UNSIGNED,
  id_covoiturage INT UNSIGNED,
  PRIMARY KEY(id_voiture, id_covoiturage),
  CONSTRAINT fk_utilise_voiture FOREIGN KEY (id_voiture)
    REFERENCES voiture(id_voiture)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_utilise_covoiturage FOREIGN KEY (id_covoiturage)
    REFERENCES covoiturage(id_covoiturage)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE detient (
  id_voiture INT UNSIGNED,
  id_marque  INT UNSIGNED,
  PRIMARY KEY(id_voiture, id_marque),
  CONSTRAINT fk_detient_voiture FOREIGN KEY (id_voiture)
    REFERENCES voiture(id_voiture)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_detient_marque FOREIGN KEY (id_marque)
    REFERENCES marque(id_marque)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE possede (
  id_utilisateur INT UNSIGNED,
  id_role        INT UNSIGNED,
  PRIMARY KEY(id_utilisateur, id_role),
  CONSTRAINT fk_possede_utilisateur FOREIGN KEY (id_utilisateur)
    REFERENCES utilisateur(id_utilisateur)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_possede_role FOREIGN KEY (id_role)
    REFERENCES role(id_role)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE depose (
  id_utilisateur INT UNSIGNED,
  id_avis        INT UNSIGNED,
  PRIMARY KEY(id_utilisateur, id_avis),
  CONSTRAINT fk_depose_utilisateur FOREIGN KEY (id_utilisateur)
    REFERENCES utilisateur(id_utilisateur)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_depose_avis FOREIGN KEY (id_avis)
    REFERENCES avis(id_avis)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE participe (
  id_utilisateur INT UNSIGNED,
  id_covoiturage INT UNSIGNED,
  PRIMARY KEY(id_utilisateur, id_covoiturage),
  CONSTRAINT fk_participe_utilisateur FOREIGN KEY (id_utilisateur)
    REFERENCES utilisateur(id_utilisateur)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_participe_covoiturage FOREIGN KEY (id_covoiturage)
    REFERENCES covoiturage(id_covoiturage)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE credit_transaction (
    id_credit_transaction INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT UNSIGNED NOT NULL,
    id_covoiturage INT UNSIGNED NULL,
    montant INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    date_transaction DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_credit_tx_user (id_utilisateur),
    INDEX idx_credit_tx_covoit (id_covoiturage),

    CONSTRAINT fk_credit_transaction_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE,

    CONSTRAINT fk_credit_transaction_covoiturage
        FOREIGN KEY (id_covoiturage)
        REFERENCES covoiturage(id_covoiturage)
        ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- 3) DONNÉES
-- =========================================================

-- 3.1 Rôles
INSERT INTO role (libelle) VALUES
  ('UTILISATEUR'),
  ('CHAUFFEUR'),
  ('EMPLOYE'),
  ('ADMINISTRATEUR');

-- 3.2 Utilisateur : on garde uniquement Admin (id=1)
INSERT INTO utilisateur (id_utilisateur, nom, prenom, email, password, telephone, adresse, date_naissance, photo, pseudo) VALUES
  (1, 'Admin', 'admin', 'admin@ecoride.fr', '$2y$10$b6QXVFisdGxvSyBvlVnPLOrv.2nGr2AnBiovca0vqFRBcnLgwkduu', NULL, NULL, NULL, NULL, 'Admin');

-- 3.3 Configurations / Paramètres : supprimés (car ils pointaient vers Michel/Sophie)
-- (Si tu veux une config pour Admin, dis-moi et je te la recrée proprement.)

-- 3.5 Marques (exemples)
INSERT INTO marque (libelle) VALUES
  ('Renault'),
  ('Tesla'),
  ('Peugeot');

-- 3.6 Voitures (exemples)
INSERT INTO voiture (modele, immatriculation, energie, couleur, date_premiere_immatriculation) VALUES
  ('Zoé',  'AA-123-AA', 'electrique', 'blanc',  '2021-05-12'),
  ('Model 3','BB-456-BB','electrique', 'rouge', '2022-03-03');

-- 3.7 Liens voiture <-> marque
INSERT INTO detient (id_voiture, id_marque) VALUES
  (1, 1),
  (2, 2);

-- 3.8 Liens utilisateur <-> voiture : supprimés (Michel n’existe plus)
-- INSERT INTO gere (...)

-- 3.9 Covoiturages (exemples)
INSERT INTO covoiturage (date_depart, heure_depart, lieu_depart, date_arrivee, heure_arrivee, lieu_arrivee, statut, nb_place, prix_personne) VALUES
  ('2026-02-01', '08:30', 'Lyon',  '2026-02-01', '12:00', 'Paris', 'PLANIFIE', 3, 12),
  ('2026-02-05', '18:15', 'Paris', '2026-02-05', '20:45', 'Lille', 'PLANIFIE', 2, 8);

-- 3.10 Liens voiture <-> covoiturage
INSERT INTO utilise (id_voiture, id_covoiturage) VALUES
  (2, 1),
  (1, 2);

-- 3.11 Participation : supprimée (Sophie n’existe plus)
-- INSERT INTO participe (...)

-- 3.14 Attribution des rôles : Admin = UTILISATEUR + ADMINISTRATEUR
INSERT INTO possede (id_utilisateur, id_role) VALUES
  (1, 1), -- UTILISATEUR
  (1, 4); -- ADMINISTRATEUR

-- =========================================================
-- FIN
-- =========================================================
