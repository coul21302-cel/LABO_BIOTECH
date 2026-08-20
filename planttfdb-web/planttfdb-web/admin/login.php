<?php
/**
 * PlantTFDB - Admin Login
 * @package PlantTFDB Admin
 */

require_once '../config/database.php';
require_once 'includes/auth.php';

// Si déjà connecté, rediriger vers dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$timeout_message = '';

if (isset($_GET['timeout'])) {
    $timeout_message = 'Your session has expired. Please login again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        if (authenticateUser($username, $password)) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - PlantTFDB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2d7a3e 0%, #1e5a8e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header i {
            font-size: 3rem;
            color: #2d7a3e;
            margin-bottom: 1rem;
        }
        
        .login-header h1 {
            font-size: 1.8rem;
            color: #212121;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #757575;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .alert-danger {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }
        
        .alert-warning {
            background: #fff3e0;
            color: #e65100;
            border-left: 4px solid #e65100;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #212121;
            font-weight: 500;
        }
        
        .form-group label i {
            margin-right: 0.5rem;
            color: #2d7a3e;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #2d7a3e;
            box-shadow: 0 0 0 3px rgba(45,122,62,0.1);
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #2d7a3e, #1e5a8e);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e0e0e0;
        }
        
        .login-footer a {
            color: #2d7a3e;
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .default-credentials {
            background: #e8f5e9;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #2e7d32;
        }
        
        .default-credentials strong {
            display: block;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-lock-open"></i>
            <h1>Admin Panel</h1>
            <p>PlantTFDB Management System</p>
        </div>
        
        <?php if ($timeout_message): ?>
        <div class="alert alert-warning">
            <i class="fas fa-clock"></i> <?php echo $timeout_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo h($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Username
                </label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       class="form-control" 
                       placeholder="Enter your username"
                       required 
                       autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-key"></i> Password
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control" 
                       placeholder="Enter your password"
                       required>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login to Admin Panel
            </button>
        </form>
        
        <div class="default-credentials">
            <strong><i class="fas fa-info-circle"></i> Default Credentials:</strong>
            Username: <code>admin</code> | Password: <code>admin123</code><br>
            <small style="color: #666; margin-top: 0.5rem; display: block;">
                ⚠️ Change these credentials in production!
            </small>
        </div>
        
        <div class="login-footer">
            <a href="../index.php">
                <i class="fas fa-arrow-left"></i> Back to Public Site
            </a>
        </div>
    </div>
</body>
</html>
