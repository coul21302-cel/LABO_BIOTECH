# PlantTFDB - Plant Transcription Factor Database
## Interface Web Professionnelle v1.0.0

---

## 📋 Table des Matières
- [Description](#description)
- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Structure du Projet](#structure-du-projet)
- [Utilisation](#utilisation)
- [Technologies](#technologies)
- [Support](#support)

---

## 📖 Description

PlantTFDB est une base de données complète et professionnelle dédiée aux **facteurs de transcription des plantes**.

Cette interface web moderne offre:
- ✅ Recherche avancée multi-critères
- ✅ Navigation par espèce et famille
- ✅ Visualisation détaillée de chaque TF
- ✅ Téléchargement de séquences FASTA
- ✅ Statistiques et graphiques interactifs
- ✅ Design responsive et moderne

---

## ✨ Fonctionnalités

### 🔍 Recherche
- Recherche par nom de gène, ID, espèce
- Filtres par famille de TF
- Filtres par chromosome
- Pagination des résultats
- Tri et export

### 📊 Visualisation
- Fiche détaillée pour chaque TF
- Domaines protéiques
- Annotations Gene Ontology
- Profils d'expression
- Sites de liaison ADN
- Références croisées (UniProt, GenBank, etc.)

### 💾 Téléchargement
- Export FASTA (protéines et CDS)
- Export JSON complet
- Téléchargement en masse
- Export CSV et TSV

### 📈 Statistiques
- Distribution par famille
- Distribution par espèce
- Distribution par chromosome
- Graphiques interactifs

---

## ⚙️ Prérequis

- **PHP** : 7.4 ou supérieur
- **MySQL** : 5.7 ou supérieur (ou MariaDB 10.2+)
- **Serveur Web** : Apache ou Nginx
- **Extensions PHP** :
  - PDO
  - pdo_mysql
  - mbstring

---

## 📥 Installation

### 1. Télécharger le Projet

Extraire l'archive `planttfdb-web.zip` dans votre dossier web :

```bash
# Pour XAMPP (Windows)
C:\xampp\htdocs\planttfdb-web\

# Pour MAMP (Mac)
/Applications/MAMP/htdocs/planttfdb-web/

# Pour Linux
/var/www/html/planttfdb-web/
```

### 2. Créer la Base de Données

1. Ouvrir **phpMyAdmin** : `http://localhost/phpmyadmin`
2. Créer une nouvelle base : `planttfdb`
3. Importer le fichier SQL : `planttfdb-PROFESSIONNEL-FINAL.sql`

### 3. Configuration

Éditer le fichier `config/database.php` :

```php
define('DB_HOST', 'localhost');      // Serveur MySQL
define('DB_NAME', 'planttfdb');      // Nom de la base
define('DB_USER', 'root');           // Utilisateur MySQL
define('DB_PASS', '');               // Mot de passe MySQL
define('SITE_URL', 'http://localhost/planttfdb-web');
```

### 4. Accès

Ouvrir dans le navigateur :
```
http://localhost/planttfdb-web
```

---

## 📁 Structure du Projet

```
planttfdb-web/
├── config/
│   └── database.php          # Configuration BDD
├── includes/
│   ├── header.php            # En-tête
│   ├── footer.php            # Pied de page
│   └── functions.php         # Fonctions utilitaires
├── assets/
│   ├── css/
│   │   └── style.css         # Styles CSS
│   └── js/
│       └── app.js            # JavaScript
├── index.php                 # Page d'accueil
├── search.php                # Recherche avancée
├── tf_detail.php             # Détail d'un TF
├── browse.php                # Navigation
├── statistics.php            # Statistiques
├── download.php              # Téléchargement
└── README.md                 # Ce fichier
```

---

## 🎯 Utilisation

### Rechercher un TF

1. Page d'accueil → Barre de recherche
2. Ou Menu → **Search**
3. Entrer nom/ID/espèce
4. Appliquer filtres (optionnel)
5. Cliquer sur un résultat pour voir détails

### Naviguer par Catégorie

1. Menu → **Browse**
2. Choisir : Par Famille ou Par Espèce
3. Sélectionner une catégorie
4. Explorer les TF

### Télécharger des Séquences

1. Menu → **Download**
2. Sélectionner filtres
3. Choisir format (FASTA, JSON, CSV)
4. Télécharger

### Voir les Statistiques

1. Menu → **Statistics**
2. Explorer les graphiques
3. Analyser les distributions

---

## 🛠️ Technologies Utilisées

### Backend
- **PHP 8+** : Logique serveur
- **MySQL** : Base de données
- **PDO** : Accès sécurisé à la BDD

### Frontend
- **HTML5** : Structure
- **CSS3** : Design moderne
- **JavaScript (Vanilla)** : Interactions
- **Font Awesome 6** : Icônes
- **Google Fonts (Inter)** : Typographie

### Architecture
- **MVC Pattern** : Organisation du code
- **Responsive Design** : Compatible mobile
- **Security First** : Prepared statements, escaping
- **Performance** : Pagination, indexation

---

## 🎨 Personnalisation

### Modifier les Couleurs

Éditer `assets/css/style.css` :

```css
:root {
    --primary-color: #2d7a3e;    /* Vert principal */
    --primary-dark: #1e5a8e;     /* Bleu foncé */
    --primary-light: #4caf50;    /* Vert clair */
    /* ... */
}
```

### Ajouter un Logo

1. Placer votre logo dans `assets/images/logo.png`
2. Éditer `includes/header.php`

### Modifier le Footer

Éditer `includes/footer.php` pour changer les informations de contact.

---

## 🔒 Sécurité

- ✅ Préparation des requêtes SQL (protection injection SQL)
- ✅ Échappement HTML (protection XSS)
- ✅ Validation des entrées
- ✅ Gestion sécurisée des sessions
- ✅ Pas de mot de passe en clair

---

## 📊 Performance

- ✅ Indexation optimale de la BDD
- ✅ Pagination des résultats
- ✅ Requêtes optimisées avec JOIN
- ✅ Cache navigateur
- ✅ CSS/JS minifiables

---

## 🐛 Dépannage

### Erreur "Database connection failed"

- Vérifier les identifiants dans `config/database.php`
- Vérifier que MySQL est démarré
- Vérifier que la base `planttfdb` existe

### Page blanche

- Activer l'affichage des erreurs PHP
- Vérifier les permissions des fichiers
- Consulter les logs Apache/PHP

### CSS ne charge pas

- Vérifier le chemin dans `SITE_URL`
- Vider le cache navigateur (Ctrl+F5)

---

## 📝 To-Do / Améliorations Futures

- [ ] API REST complète
- [ ] Export Excel
- [ ] Graphiques interactifs (Chart.js)
- [ ] Comparaison de TF
- [ ] Analyse BLAST
- [ ] Interface admin pour gestion
- [ ] Multi-langues
- [ ] Mode sombre

---

## 👨‍💻 Développeur

**PlantTFDB Professional Edition**

Pour toute question ou suggestion :
- 📧 Email : contact@planttfdb.org
- 🌐 Web : http://planttfdb.org

---

## 📜 License

© 2026 PlantTFDB. Tous droits réservés.

Ce projet est distribué pour usage académique et de recherche.

---

## 🙏 Remerciements

Merci d'utiliser PlantTFDB !

Si vous utilisez cette base de données dans vos recherches,
merci de citer :

```
PlantTFDB: Plant Transcription Factor Database. 
Available at: http://localhost/planttfdb-web
Accessed: [Date]
```

---

**Bon travail avec PlantTFDB ! 🌱🧬**
