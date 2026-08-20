<?php
/**
 * Configuration de la base de données
 * Laboratory Management System (LMS)
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'labo-biotech');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Options PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// Connexion à la base de données
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Configuration de l'application
define('SITE_NAME', 'Laboratoire Campus de Biotechnologie Végétale');
define('SITE_URL', 'http://localhost/labo-biotech');
define('ADMIN_EMAIL', 'admin@labo-biotech.com');

// Chemins
define('UPLOAD_DIR', __DIR__ . '/uploads/images/');
define('UPLOAD_URL', SITE_URL . '/uploads/images/');

// Créer le dossier uploads s'il n'existe pas
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonction helper pour sécuriser les sorties HTML
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Fonction pour rediriger si non connecté
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

// Fonction pour générer un nom de fichier unique
function generateFileName($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

// Fonction pour formater les dates
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

// Fonction pour tronquer un texte
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}
?>
