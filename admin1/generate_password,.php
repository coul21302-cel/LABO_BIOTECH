<?php
/**
 * Générateur de mot de passe hashé
 * Utilisez ce fichier pour générer le hash correct
 */

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Mot de passe : " . $password . "<br>";
echo "Hash généré : " . $hash . "<br><br>";

echo "Copiez ce hash et exécutez cette requête SQL dans phpMyAdmin :<br><br>";
echo "<code style='background:#f4f4f4; padding:10px; display:block;'>";
echo "UPDATE utilisateurs SET mot_de_passe = '" . $hash . "' WHERE email = 'admin@labo-biotech.com';";
echo "</code>";
?>
