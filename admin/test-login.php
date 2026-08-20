<?php
// PAGE DE TEST - À PLACER DANS : labo-biotech/admin/test-login.php
// Accès : http://localhost/labo-biotech/admin/test-login.php

require_once '../db_config.php';

echo "<h1>Test de connexion Admin</h1>";
echo "<hr>";

// 1. Vérifier la connexion à la base de données
echo "<h2>1. Connexion à la base de données</h2>";
try {
    $test = $pdo->query("SELECT 1");
    echo "✅ <strong style='color:green;'>CONNEXION BDD OK</strong><br><br>";
} catch (Exception $e) {
    echo "❌ <strong style='color:red;'>ERREUR BDD : " . $e->getMessage() . "</strong><br><br>";
    exit;
}

// 2. Vérifier si la table utilisateurs existe
echo "<h2>2. Table utilisateurs</h2>";
try {
    $count = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
    echo "✅ <strong style='color:green;'>Table existe, {$count} utilisateur(s)</strong><br><br>";
} catch (Exception $e) {
    echo "❌ <strong style='color:red;'>ERREUR : " . $e->getMessage() . "</strong><br><br>";
    exit;
}

// 3. Afficher l'utilisateur admin
echo "<h2>3. Utilisateur admin</h2>";
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->execute(['admin@labo-biotech.com']);
$user = $stmt->fetch();

if ($user) {
    echo "✅ <strong style='color:green;'>Utilisateur trouvé</strong><br>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Champ</th><th>Valeur</th></tr>";
    echo "<tr><td>ID</td><td>{$user['id']}</td></tr>";
    echo "<tr><td>Email</td><td>{$user['email']}</td></tr>";
    echo "<tr><td>Nom</td><td>{$user['nom']}</td></tr>";
    echo "<tr><td>Prénom</td><td>{$user['prenom']}</td></tr>";
    echo "<tr><td>Rôle</td><td>{$user['role']}</td></tr>";
    echo "<tr><td>Hash (début)</td><td>" . substr($user['mot_de_passe'], 0, 30) . "...</td></tr>";
    echo "</table><br>";
} else {
    echo "❌ <strong style='color:red;'>Utilisateur NON trouvé</strong><br><br>";
    exit;
}

// 4. Tester le mot de passe
echo "<h2>4. Test du mot de passe</h2>";
$password_test = 'admin123';
$hash_actuel = $user['mot_de_passe'];

echo "<p>Mot de passe testé : <strong>$password_test</strong></p>";
echo "<p>Hash dans la BDD : <code>$hash_actuel</code></p>";

if (password_verify($password_test, $hash_actuel)) {
    echo "✅ <strong style='color:green;'>LE MOT DE PASSE EST CORRECT !</strong><br>";
    echo "<p>Vous pouvez vous connecter avec :<br>";
    echo "Email : <strong>admin@labo-biotech.com</strong><br>";
    echo "Password : <strong>admin123</strong></p>";
} else {
    echo "❌ <strong style='color:red;'>LE MOT DE PASSE NE CORRESPOND PAS !</strong><br><br>";
    
    // Générer un nouveau hash
    $nouveau_hash = password_hash($password_test, PASSWORD_DEFAULT);
    echo "<h3>Solution :</h3>";
    echo "<p>Exécutez cette requête dans phpMyAdmin :</p>";
    echo "<textarea rows='5' cols='80' style='font-family:monospace;'>UPDATE utilisateurs 
SET mot_de_passe = '$nouveau_hash'
WHERE email = 'admin@labo-biotech.com';</textarea>";
    
    echo "<p><strong>Puis rafraîchissez cette page pour vérifier.</strong></p>";
}

echo "<hr>";
echo "<h2>5. Actions</h2>";
echo "<p><a href='login.php'>→ Aller à la page de connexion</a></p>";
echo "<p><a href='test-login.php'>→ Rafraîchir cette page</a></p>";

// ATTENTION : Supprimez ce fichier après utilisation pour des raisons de sécurité !
?>
