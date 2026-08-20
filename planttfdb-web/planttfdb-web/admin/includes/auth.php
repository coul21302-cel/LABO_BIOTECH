<?php
/**
 * PlantTFDB - Authentication System
 * @package PlantTFDB Admin
 */

session_start();

/**
 * Vérifier si l'utilisateur est connecté
 * Redirige vers login si non authentifié
 */
function requireAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
    
    // Renouveler la session toutes les 30 minutes
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();
        session_destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Authentifier un utilisateur
 * 
 * @param string $username
 * @param string $password
 * @return bool
 */
function authenticateUser($username, $password) {
    // En production, vérifier contre la base de données
    // Pour cet exemple, identifiants en dur (À CHANGER EN PRODUCTION!)
    
    $valid_users = [
        'admin' => password_hash('admin123', PASSWORD_DEFAULT),
        'curator' => password_hash('curator123', PASSWORD_DEFAULT)
    ];
    
    if (isset($valid_users[$username]) && password_verify($password, $valid_users[$username])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['last_activity'] = time();
        $_SESSION['login_time'] = time();
        return true;
    }
    
    return false;
}

/**
 * Déconnecter l'utilisateur
 */
function logoutUser() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

/**
 * Obtenir le nom d'utilisateur connecté
 * 
 * @return string
 */
function getLoggedInUser() {
    return $_SESSION['admin_username'] ?? 'Unknown';
}

/**
 * Vérifier les permissions (extensible)
 * 
 * @param string $permission
 * @return bool
 */
function hasPermission($permission) {
    // Pour l'instant, tous les admins ont toutes les permissions
    // En production, implémenter un système de rôles
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>
