<?php
/**
 * PlantTFDB - Database Configuration
 * Professional Edition
 * 
 * @package PlantTFDB
 * @version 1.0.0
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'planttfdb');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuration du site
define('SITE_NAME', 'PlantTFDB');
define('SITE_DESCRIPTION', 'Plant Transcription Factor Database');
define('SITE_URL', 'http://localhost/planttfdb-web');
define('VERSION', '1.0.0');

// Pagination
define('RESULTS_PER_PAGE', 20);

// Connexion PDO avec gestion d'erreurs professionnelle
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
} catch (PDOException $e) {
    // En production, logger l'erreur au lieu de l'afficher
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed. Please contact the administrator.");
}

/**
 * Fonction de sécurisation HTML
 * 
 * @param string $string
 * @return string
 */
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction de formatage des nombres
 * 
 * @param mixed $number
 * @param int $decimals
 * @return string
 */
function formatNumber($number, $decimals = 0) {
    return number_format($number, $decimals, '.', ',');
}

/**
 * Fonction de troncature de texte
 * 
 * @param string $text
 * @param int $length
 * @return string
 */
function truncate($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Génère une séquence FASTA formatée
 * 
 * @param string $header
 * @param string $sequence
 * @param int $lineLength
 * @return string
 */
function formatFasta($header, $sequence, $lineLength = 60) {
    $output = ">$header\n";
    $output .= chunk_split($sequence, $lineLength, "\n");
    return trim($output);
}

/**
 * Retourne le badge coloré pour une famille de TF
 * 
 * @param string $familyCode
 * @return string
 */
function getFamilyBadge($familyCode) {
    $colors = [
        'MADS' => '#9c27b0',
        'MYB' => '#2196f3',
        'bHLH' => '#4caf50',
        'WRKY' => '#ff9800',
        'NAC' => '#f44336',
        'bZIP' => '#00bcd4',
        'AP2' => '#ff5722',
        'C2H2' => '#795548'
    ];
    
    $color = $colors[$familyCode] ?? '#607d8b';
    return "<span class='family-badge' style='background: $color;'>" . h($familyCode) . "</span>";
}

/**
 * Retourne l'icône pour une catégorie GO
 * 
 * @param string $category
 * @return string
 */
function getGOIcon($category) {
    $icons = [
        'BP' => '<i class="fas fa-project-diagram" title="Biological Process"></i>',
        'MF' => '<i class="fas fa-tools" title="Molecular Function"></i>',
        'CC' => '<i class="fas fa-cube" title="Cellular Component"></i>'
    ];
    
    return $icons[$category] ?? '';
}

?>
