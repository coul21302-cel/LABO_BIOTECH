<?php
require_once '../db_config.php';
requireLogin();

// Statistiques
$stats = [];
$stats['membres'] = $pdo->query("SELECT COUNT(*) FROM membres WHERE actif = 1")->fetchColumn();
$stats['projets'] = $pdo->query("SELECT COUNT(*) FROM projets")->fetchColumn();
$stats['publications'] = $pdo->query("SELECT COUNT(*) FROM publications")->fetchColumn();
$stats['actualites'] = $pdo->query("SELECT COUNT(*) FROM actualites WHERE publie = 1")->fetchColumn();
$stats['messages'] = $pdo->query("SELECT COUNT(*) FROM messages_contact WHERE lu = 0")->fetchColumn();

// Dernières actualités
$dernieres_actualites = $pdo->query("SELECT * FROM actualites ORDER BY date_publication DESC LIMIT 5")->fetchAll();

// Derniers messages
$derniers_messages = $pdo->query("SELECT * FROM messages_contact ORDER BY date_envoi DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Administration</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-dashboard">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <h2><i class="fas fa-flask"></i> LBV Admin</h2>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Tableau de bord</a></li>
                    <li><a href="admin_equipe.php"><i class="fas fa-users"></i> Équipe</a></li>
                    <li><a href="admin_projets.php"><i class="fas fa-project-diagram"></i> Projets</a></li>
                    <li><a href="admin_publications.php"><i class="fas fa-book"></i> Publications</a></li>
                    <li><a href="admin_actualites.php"><i class="fas fa-newspaper"></i> Actualités</a></li>
                    <li><a href="admin_galerie.php"><i class="fas fa-images"></i> Galerie</a></li>
                    <li><a href="admin_partenaires.php"><i class="fas fa-handshake"></i> Partenaires</a></li>
                    <li><a href="admin_messages.php"><i class="fas fa-envelope"></i> Messages 
                        <?php if ($stats['messages'] > 0): ?>
                        <span class="badge badge-warning"><?php echo $stats['messages']; ?></span>
                        <?php endif; ?>
                    </a></li>
                    <li><a href="<?php echo SITE_URL; ?>" target="_blank"><i class="fas fa-external-link-alt"></i> Voir le site</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>Tableau de bord</h1>
                <p>Bienvenue, <strong><?php echo h($_SESSION['admin_nom']); ?></strong></p>
            </div>
            
            <!-- Statistiques -->
            <div class="dashboard-stats">
                <div class="stat-box">
                    <div class="stat-box-icon" style="background: #4caf50;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?php echo $stats['membres']; ?></div>
                        <div class="stat-label">Membres</div>
                    </div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-box-icon" style="background: #2196f3;">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?php echo $stats['projets']; ?></div>
                        <div class="stat-label">Projets</div>
                    </div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-box-icon" style="background: #ff9800;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?php echo $stats['publications']; ?></div>
                        <div class="stat-label">Publications</div>
                    </div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-box-icon" style="background: #9c27b0;">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div>
                        <div class="stat-number"><?php echo $stats['actualites']; ?></div>
                        <div class="stat-label">Actualités</div>
                    </div>
                </div>
            </div>
            
            <!-- Contenu récent -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
                <!-- Dernières actualités -->
                <div class="admin-table">
                    <h3 style="padding: 1rem; background: var(--light-gray); margin: 0;">Dernières actualités</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dernieres_actualites as $actu): ?>
                            <tr>
                                <td><?php echo h(truncate($actu['titre'], 40)); ?></td>
                                <td><?php echo formatDate($actu['date_publication']); ?></td>
                                <td><span class="badge badge-info"><?php echo h($actu['type']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Derniers messages -->
                <div class="admin-table">
                    <h3 style="padding: 1rem; background: var(--light-gray); margin: 0;">Derniers messages</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($derniers_messages as $msg): ?>
                            <tr style="<?php echo !$msg['lu'] ? 'font-weight: bold;' : ''; ?>">
                                <td><?php echo h($msg['nom']); ?></td>
                                <td><?php echo h($msg['email']); ?></td>
                                <td><?php echo formatDate($msg['date_envoi']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
