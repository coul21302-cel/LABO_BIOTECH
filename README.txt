================================================================================
  LABORATORY MANAGEMENT SYSTEM (LMS)
  Système de Gestion de Laboratoire Scientifique
  Version 1.0
================================================================================

📋 DESCRIPTION
--------------
Système web complet de gestion pour le Laboratoire de Biotechnologie Végétale
Développé en PHP/MySQL avec interface responsive et sécurisée.

📦 CONTENU DU PACKAGE
--------------------
labo-biotech/
├── index.php                 # Page d'accueil
├── db_config.php            # Configuration base de données
├── header.php               # En-tête du site
├── footer.php               # Pied de page
├── schema.sql               # Structure de la base de données
├── css/
│   └── style.css            # Feuille de styles
├── js/
│   └── script.js            # Fonctions JavaScript
├── uploads/images/          # Dossier des images uploadées
├── pages/                   # Pages publiques
│   ├── laboratoire.php
│   ├── equipe.php
│   ├── recherche.php
│   ├── publications.php
│   ├── actualites.php
│   ├── galerie.php
│   ├── partenaires.php
│   └── contact.php
└── admin/                   # Interface d'administration
    ├── login.php
    ├── dashboard.php
    ├── admin_equipe.php
    ├── admin_projets.php
    ├── admin_publications.php
    ├── admin_actualites.php
    └── logout.php

🔧 PRÉREQUIS
------------
- XAMPP (ou WAMP/MAMP/LAMP)
- PHP 8.0 ou supérieur
- MySQL 5.7 ou MariaDB 10.3+
- Apache avec mod_rewrite activé

📥 INSTALLATION
---------------

1. EXTRAIRE LE PROJET
   - Extraire le fichier labo-biotech.zip
   - Copier le dossier "labo-biotech" dans :
     → Windows: C:/xampp/htdocs/
     → Linux: /opt/lampp/htdocs/
     → Mac: /Applications/XAMPP/htdocs/

2. CRÉER LA BASE DE DONNÉES
   a) Démarrer XAMPP (Apache + MySQL)
   b) Ouvrir phpMyAdmin: http://localhost/phpmyadmin
   c) Créer une nouvelle base de données nommée: labo_biotech
   d) Sélectionner la base "labo_biotech"
   e) Aller dans l'onglet "Importer"
   f) Choisir le fichier "schema.sql" depuis le dossier du projet
   g) Cliquer sur "Exécuter"

3. CONFIGURER LA CONNEXION (si nécessaire)
   Ouvrir le fichier: db_config.php
   Modifier si besoin :
   
   define('DB_HOST', 'localhost');     // Hôte MySQL
   define('DB_NAME', 'labo_biotech');  // Nom de la base
   define('DB_USER', 'root');          // Utilisateur MySQL
   define('DB_PASS', '');              // Mot de passe (vide par défaut)

4. PERMISSIONS DU DOSSIER UPLOADS
   Sur Linux/Mac, exécuter :
   chmod -R 777 uploads/images/

🚀 ACCÈS AU SYSTÈME
-------------------

▶ SITE PUBLIC
URL: http://localhost/labo-biotech/

Pages disponibles:
- Accueil
- Le Laboratoire
- Équipe
- Recherche
- Publications
- Actualités
- Galerie
- Contact

▶ ESPACE ADMINISTRATEUR
URL: http://localhost/labo-biotech/admin/login.php

Identifiants par défaut:
📧 Email    : admin@labo-biotech.com
🔑 Mot de passe : admin123

⚠️ IMPORTANT : Changer ces identifiants après la première connexion !

Fonctionnalités admin:
- Gestion de l'équipe (CRUD complet)
- Gestion des projets de recherche
- Gestion des publications scientifiques
- Gestion des actualités
- Gestion de la galerie photos
- Gestion des partenaires
- Consultation des messages de contact
- Tableau de bord avec statistiques

🔐 SÉCURITÉ
-----------
✓ Mots de passe hashés avec password_hash()
✓ Requêtes préparées PDO (protection SQL injection)
✓ Validation des données utilisateur
✓ Sessions PHP sécurisées
✓ Protection CSRF (à implémenter en production)
✓ Upload de fichiers sécurisé

📊 BASE DE DONNÉES
------------------
Tables principales:
- utilisateurs      : Comptes administrateurs
- membres           : Membres de l'équipe
- projets           : Projets de recherche
- projet_membres    : Relation projets ↔ membres
- publications      : Publications scientifiques
- actualites        : Actualités et événements
- galerie           : Photos du laboratoire
- partenaires       : Partenaires institutionnels
- messages_contact  : Messages du formulaire de contact

🎨 DESIGN
---------
- Palette de couleurs: Vert (#2d7a3e), Bleu (#1e5a8e), Blanc
- Design responsive (mobile, tablette, desktop)
- Interface moderne et épurée
- Icônes Font Awesome 6.4
- Animations CSS3

🛠️ TECHNOLOGIES UTILISÉES
---------------------------
Frontend:
- HTML5
- CSS3 (Grid, Flexbox, Animations)
- JavaScript (Vanilla)

Backend:
- PHP 8+
- PDO (PHP Data Objects)
- MySQL / MariaDB

🐛 DÉPANNAGE
------------

ERREUR: "Cannot connect to database"
→ Vérifier que MySQL est démarré dans XAMPP
→ Vérifier les identifiants dans db_config.php

ERREUR: Page blanche / erreur 500
→ Activer l'affichage des erreurs PHP
→ Vérifier les logs Apache

ERREUR: "Permission denied" sur uploads/
→ Exécuter : chmod -R 777 uploads/images/

Images ne s'affichent pas
→ Vérifier que le dossier uploads/images/ existe
→ Créer un fichier default-avatar.png dans uploads/images/

📝 PERSONNALISATION
-------------------

Modifier les informations du laboratoire:
1. Ouvrir db_config.php
2. Modifier la constante SITE_NAME
3. Modifier les informations de contact dans footer.php

Changer les couleurs:
1. Ouvrir css/style.css
2. Modifier les variables CSS dans :root

Ajouter un administrateur:
1. Se connecter à phpMyAdmin
2. Table "utilisateurs" → Insérer
3. Utiliser password_hash() pour le mot de passe en PHP

📞 SUPPORT
----------
Pour toute question technique ou fonctionnelle,
consulter la documentation complète du projet.

📄 LICENCE
----------
Ce projet est développé pour un usage éducatif et académique.
Tous droits réservés © 2024

================================================================================
  Développé avec ❤️ pour le Laboratoire de Biotechnologie Végétale
================================================================================
