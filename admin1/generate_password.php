<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de Mot de Passe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2d7a3e;
            border-bottom: 3px solid #2d7a3e;
            padding-bottom: 10px;
        }
        .info-box {
            background: #e8f5e9;
            border-left: 4px solid #2d7a3e;
            padding: 15px;
            margin: 20px 0;
        }
        .code-box {
            background: #f4f4f4;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            overflow-x: auto;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn {
            background: #2d7a3e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #1e5a8e;
        }
        .hash-display {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Générateur de Hash de Mot de Passe</h1>
        
        <div class="info-box">
            <strong>ℹ️ Information :</strong><br>
            Ce script génère un hash sécurisé pour le mot de passe : <strong>admin123</strong>
        </div>

        <?php
        /**
         * Générateur de mot de passe hashé pour l'admin
         */
        
        // Le mot de passe à hasher
        $password = 'admin123';
        
        // Générer le hash
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        echo '<div class="success">';
        echo '<h2>✅ Hash Généré avec Succès !</h2>';
        echo '</div>';
        
        echo '<h3>📋 Mot de passe :</h3>';
        echo '<div class="code-box">' . htmlspecialchars($password) . '</div>';
        
        echo '<h3>🔑 Hash généré :</h3>';
        echo '<div class="hash-display">' . htmlspecialchars($hash) . '</div>';
        
        echo '<h3>💾 Requête SQL à exécuter :</h3>';
        echo '<div class="info-box">';
        echo 'Copiez cette requête et exécutez-la dans <strong>phpMyAdmin</strong> :<br>';
        echo '(Base de données : <code>labo_biotech</code> → Onglet <code>SQL</code>)';
        echo '</div>';
        
        echo '<div class="code-box">';
        echo "UPDATE utilisateurs <br>";
        echo "SET mot_de_passe = '" . $hash . "'<br>";
        echo "WHERE email = 'admin@labo-biotech.com';";
        echo '</div>';
        
        echo '<h3>🧪 Test de vérification :</h3>';
        if (password_verify($password, $hash)) {
            echo '<div class="success">✅ Le hash fonctionne correctement ! La vérification réussit.</div>';
        } else {
            echo '<div style="background:#f8d7da;color:#721c24;padding:15px;border-radius:5px;">❌ Erreur de vérification !</div>';
        }
        ?>
        
        <h3>📝 Instructions :</h3>
        <ol>
            <li>Copiez la requête SQL ci-dessus</li>
            <li>Ouvrez phpMyAdmin : <code>http://localhost/phpmyadmin</code></li>
            <li>Sélectionnez la base de données : <strong>labo_biotech</strong></li>
            <li>Cliquez sur l'onglet <strong>SQL</strong></li>
            <li>Collez la requête et cliquez sur <strong>Exécuter</strong></li>
            <li>Retournez à la page de login : <code>http://localhost/labo-biotech/admin/login.php</code></li>
            <li>Connectez-vous avec :
                <ul>
                    <li><strong>Email :</strong> admin@labo-biotech.com</li>
                    <li><strong>Mot de passe :</strong> admin123</li>
                </ul>
            </li>
        </ol>
        
        <div class="info-box">
            <strong>⚠️ Sécurité :</strong><br>
            Supprimez ce fichier après utilisation pour des raisons de sécurité !
        </div>
        
        <form method="get" action="">
            <button type="submit" class="btn">🔄 Régénérer un nouveau hash</button>
        </form>
    </div>
</body>
</html>
