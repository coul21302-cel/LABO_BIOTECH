<?php
require_once '../db_config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_nom'] = $user['nom'] . ' ' . $user['prenom'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_role'] = $user['role'];
            
            // Mettre à jour la dernière connexion
            $stmt = $pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Administration</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-login">
        <div class="login-card">
            <div class="login-header">
                <img src="<?php echo SITE_URL; ?>/uploads/images/logo2.png" alt="Logo LBV" class="login-logo">
                <h1>Administration</h1>
                <p>Laboratoire Campus de Biotechnologie Végétale</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" class="form-control" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
            
            <div class="login-footer">
                <a href="<?php echo SITE_URL; ?>">
                    <i class="fas fa-arrow-left"></i> Retour au site
                </a>
            </div>
            
            <div class="login-info">
                <small>Par défaut: admin@labo-biotech.com / admin123</small>
            </div>
        </div>
    </div>
    
    <style>
    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .login-logo {
        width: 80px;
        height: 80px;
        object-fit: contain;
    }
    
    .login-header h1 {
        font-size: 2rem;
        color: var(--dark-text);
        margin-bottom: 0.5rem;
    }
    
    .login-header p {
        color: var(--gray-text);
    }
    
    .login-form {
        margin: 2rem 0;
    }
    
    .btn-block {
        width: 100%;
    }
    
    .login-footer {
        text-align: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }
    
    .login-info {
        text-align: center;
        margin-top: 1rem;
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 5px;
        color: var(--gray-text);
    }
    </style>
</body>
</html>
