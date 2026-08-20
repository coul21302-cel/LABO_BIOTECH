-- =====================================================
-- Laboratory Management System (LMS)
-- Base de données pour Laboratoire de Biotechnologie
-- VERSION CORRIGÉE
-- =====================================================

-- Suppression des tables existantes
DROP TABLE IF EXISTS messages_contact;
DROP TABLE IF EXISTS galerie;
DROP TABLE IF EXISTS partenaires;
DROP TABLE IF EXISTS actualites;
DROP TABLE IF EXISTS publications;
DROP TABLE IF EXISTS projet_membres;
DROP TABLE IF EXISTS projets;
DROP TABLE IF EXISTS membres;
DROP TABLE IF EXISTS utilisateurs;

-- =====================================================
-- Table : utilisateurs (Admin)
-- =====================================================
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : membres (Équipe du laboratoire)
-- =====================================================
CREATE TABLE membres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NULL,
    telephone VARCHAR(20) NULL,
    categorie ENUM('Professeur', 'Chercheur', 'Doctorant', 'Étudiant') NOT NULL,
    specialite VARCHAR(200) NULL,
    biographie TEXT NULL,
    domaine_recherche TEXT NULL,
    photo VARCHAR(255) NULL,
    ordre_affichage INT DEFAULT 0,
    actif TINYINT(1) DEFAULT 1,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : projets
-- =====================================================
CREATE TABLE projets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    objectifs TEXT NULL,
    statut ENUM('En cours', 'Terminé', 'Planifié') DEFAULT 'En cours',
    date_debut DATE NULL,
    date_fin DATE NULL,
    budget DECIMAL(15,2) NULL,
    financement VARCHAR(255) NULL,
    image VARCHAR(255) NULL,
    ordre_affichage INT DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : projet_membres (Relation N:N)
-- =====================================================
CREATE TABLE projet_membres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projet_id INT NOT NULL,
    membre_id INT NOT NULL,
    role VARCHAR(100) NULL COMMENT 'Chef de projet, Collaborateur, etc.',
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projet_id) REFERENCES projets(id) ON DELETE CASCADE,
    FOREIGN KEY (membre_id) REFERENCES membres(id) ON DELETE CASCADE,
    UNIQUE KEY unique_projet_membre (projet_id, membre_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : publications
-- =====================================================
CREATE TABLE publications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(500) NOT NULL,
    auteurs TEXT NOT NULL,
    annee YEAR NOT NULL,
    revue VARCHAR(255) NULL,
    volume VARCHAR(50) NULL,
    pages VARCHAR(50) NULL,
    doi VARCHAR(255) NULL,
    lien_pdf VARCHAR(500) NULL,
    resume TEXT NULL,
    type_publication ENUM('Article', 'Conférence', 'Chapitre', 'Thèse', 'Autre') DEFAULT 'Article',
    membre_id INT NULL COMMENT 'Auteur principal du labo',
    projet_id INT NULL COMMENT 'Projet associé',
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (membre_id) REFERENCES membres(id) ON DELETE SET NULL,
    FOREIGN KEY (projet_id) REFERENCES projets(id) ON DELETE SET NULL,
    INDEX idx_annee (annee),
    INDEX idx_type (type_publication)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : actualites
-- CORRECTION : publie en TINYINT(1) au lieu de BOOLEAN
-- =====================================================
CREATE TABLE actualites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    type ENUM('Séminaire', 'Conférence', 'Événement', 'Annonce', 'Autre') DEFAULT 'Annonce',
    date_evenement DATE NULL,
    lieu VARCHAR(255) NULL,
    image VARCHAR(255) NULL,
    publie TINYINT(1) NOT NULL DEFAULT 1,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : galerie
-- =====================================================
CREATE TABLE galerie (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin VARCHAR(500) NOT NULL,
    categorie ENUM('Activité', 'Équipement', 'Expérience', 'Événement', 'Autre') DEFAULT 'Autre',
    ordre_affichage INT DEFAULT 0,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : partenaires
-- =====================================================
CREATE TABLE partenaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    type ENUM('Université', 'Centre de recherche', 'Laboratoire', 'Entreprise', 'Autre') NOT NULL,
    pays VARCHAR(100) NULL,
    ville VARCHAR(100) NULL,
    site_web VARCHAR(255) NULL,
    logo VARCHAR(255) NULL,
    description TEXT NULL,
    actif TINYINT(1) DEFAULT 1,
    ordre_affichage INT DEFAULT 0,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table : messages_contact
-- =====================================================
CREATE TABLE messages_contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    sujet VARCHAR(255) NULL,
    message TEXT NOT NULL,
    lu TINYINT(1) DEFAULT 0,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    adresse_ip VARCHAR(45) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DONNÉES DE TEST
-- =====================================================

-- Utilisateur admin par défaut
-- Email: admin@labo-biotech.com
-- Mot de passe : admin123
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'Système', 'admin@labo-biotech.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- Membres de l'équipe
INSERT INTO membres (nom, prenom, email, categorie, specialite, biographie, domaine_recherche, photo, ordre_affichage, actif) VALUES
('Diop', 'Amadou', 'adiop@labo-biotech.sn', 'Professeur', 'Biotechnologie végétale', 'Professeur titulaire avec 20 ans d\'expérience en amélioration génétique des plantes tropicales.', 'Amélioration génétique des céréales, résistance aux stress abiotiques', 'default-avatar.png', 1, 1),
('Ndiaye', 'Fatou', 'fndiaye@labo-biotech.sn', 'Professeur', 'Biologie moléculaire', 'Spécialiste en biologie moléculaire et génomique fonctionnelle des plantes.', 'Génomique des plantes, marqueurs moléculaires', 'default-avatar.png', 2, 1),
('Seck', 'Moussa', 'mseck@labo-biotech.sn', 'Chercheur', 'Microbiologie végétale', 'Chercheur en interactions plantes-microorganismes et biostimulants.', 'Rhizosphère, symbioses végétales', 'default-avatar.png', 3, 1),
('Fall', 'Aïssatou', 'afall@labo-biotech.sn', 'Chercheur', 'Culture in vitro', 'Experte en multiplication végétative et conservation in vitro.', 'Micropropagation, cryoconservation', 'default-avatar.png', 4, 1),
('Kane', 'Ibrahima', 'ikane@labo-biotech.sn', 'Doctorant', 'Physiologie végétale', 'Doctorant travaillant sur la tolérance à la sécheresse du mil.', 'Stress hydrique, métabolisme végétal', 'default-avatar.png', 5, 1),
('Thiam', 'Mariama', 'mthiam@labo-biotech.sn', 'Doctorant', 'Génétique moléculaire', 'Thèse sur les gènes de résistance aux maladies du riz.', 'Résistance aux pathogènes, QTL mapping', 'default-avatar.png', 6, 1);

-- Projets
INSERT INTO projets (titre, description, objectifs, statut, date_debut, date_fin, ordre_affichage) VALUES
('Amélioration de la tolérance à la sécheresse du mil', 
 'Projet visant à identifier et caractériser les gènes impliqués dans la tolérance à la sécheresse chez le mil (Pennisetum glaucum).',
 'Identifier les QTLs de tolérance à la sécheresse, développer des marqueurs moléculaires, créer des variétés améliorées',
 'En cours', '2023-01-15', NULL, 1),
('Micropropagation des espèces forestières menacées',
 'Développement de protocoles de culture in vitro pour la conservation d\'espèces forestières endémiques.',
 'Établir des protocoles de micropropagation, constituer une banque de germoplasme in vitro',
 'En cours', '2022-06-01', NULL, 2),
('Biostimulants à base de microorganismes rhizosphériques',
 'Isolement et caractérisation de bactéries et champignons promoteurs de croissance végétale.',
 'Constituer une collection de souches, tester leur efficacité, développer des formulations',
 'Terminé', '2021-03-01', '2023-12-31', 3);

-- Association projets-membres
INSERT INTO projet_membres (projet_id, membre_id, role) VALUES
(1, 1, 'Chef de projet'),
(1, 5, 'Doctorant'),
(2, 4, 'Responsable scientifique'),
(3, 3, 'Chef de projet'),
(3, 2, 'Collaborateur');

-- Publications
INSERT INTO publications (titre, auteurs, annee, revue, doi, type_publication, membre_id, projet_id) VALUES
('Genetic diversity and drought tolerance in pearl millet (Pennisetum glaucum) landraces from Senegal',
 'Diop A., Kane I., Ndiaye F.', 2023, 'African Journal of Biotechnology', '10.5897/AJB2023.001', 'Article', 1, 1),
('In vitro propagation of Cordyla pinnata: an endangered tree species',
 'Fall A., Seck M.', 2023, 'Plant Cell Tissue and Organ Culture', '10.1007/s11240-023-001', 'Article', 4, 2),
('Plant growth-promoting rhizobacteria from Senegalese soils enhance millet growth under drought stress',
 'Seck M., Diop A., Ndiaye F.', 2022, 'Applied Soil Ecology', '10.1016/j.apsoil.2022.001', 'Article', 3, 3);

-- Actualités (CORRECTION : Utilisation de 1 au lieu de TRUE)
INSERT INTO actualites (titre, contenu, type, date_evenement, publie) VALUES
('Séminaire : Biotechnologie et sécurité alimentaire en Afrique',
 'Le laboratoire organise un séminaire international sur le thème "Biotechnologie végétale et sécurité alimentaire en Afrique de l\'Ouest". Intervenants de 5 pays africains.',
 'Séminaire', '2024-04-15', 1),
('Nouveau projet financé par l\'Union Européenne',
 'Le laboratoire vient de recevoir un financement Horizon Europe pour un projet sur l\'adaptation des cultures aux changements climatiques.',
 'Annonce', NULL, 1),
('Conférence internationale AFBG 2024',
 'Participation du laboratoire à la conférence African Biotechnology and Genomics à Nairobi (Kenya).',
 'Conférence', '2024-06-20', 1);

-- Partenaires (CORRECTION : Utilisation de 1 au lieu de TRUE)
INSERT INTO partenaires (nom, type, pays, site_web, actif, ordre_affichage) VALUES
('Université Cheikh Anta Diop de Dakar', 'Université', 'Sénégal', 'https://www.ucad.sn', 1, 1),
('IRD - Institut de Recherche pour le Développement', 'Centre de recherche', 'France', 'https://www.ird.fr', 1, 2),
('CIRAD - Centre de coopération internationale en recherche agronomique', 'Centre de recherche', 'France', 'https://www.cirad.fr', 1, 3),
('ICRISAT - International Crops Research Institute', 'Centre de recherche', 'Inde', 'https://www.icrisat.org', 1, 4);

-- =====================================================
-- FIN DU SCHÉMA - VERSION CORRIGÉE
-- =====================================================
