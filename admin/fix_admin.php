<?php
require_once '../db_config.php';

$email = 'admin@labo-biotech.com';
$password = 'admin123';

// Le serveur web génère lui-même le hash BCRYPT natif
$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE email = ?");
    $stmt->execute([$hash, $email]);
    
    echo "<h2>SUCCÈS : Mot de passe réinitialisé !</h2>";
    echo "<p>Identifiants à utiliser :</p>";
    echo "<ul>";
    echo "<li><b>Email :</b> " . htmlspecialchars($email) . "</li>";
    echo "<li><b>Mot de passe :</b> " . htmlspecialchars($password) . "</li>";
    echo "</ul>";
    echo "<a href='login.php'>Aller à la page de connexion</a>";
} catch (PDOException $e) {
    echo "<h2>Erreur SQL :</h2> " . $e->getMessage();
}
?>