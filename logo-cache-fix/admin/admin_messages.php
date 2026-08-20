<?php
require_once '../db_config.php';
requireLogin();

$success = '';
$error = '';

// Marquer comme lu/non lu
if (isset($_GET['toggle_read'])) {
    $id = (int)$_GET['toggle_read'];
    try {
        $stmt = $pdo->prepare("UPDATE messages_contact SET lu = NOT lu WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Statut du message modifié.';
    } catch (PDOException $e) {
        $error = 'Erreur lors de la modification.';
    }
}

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM messages_contact WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Message supprimé avec succès.';
    } catch (PDOException $e) {
        $error = 'Erreur lors de la suppression.';
    }
}

// Récupérer tous les messages
$messages = $pdo->query("SELECT * FROM messages_contact ORDER BY date_envoi DESC")->fetchAll();

// Message sélectionné pour affichage détaillé
$view_message = null;
if (isset($_GET['view'])) {
    $stmt = $pdo->prepare("SELECT * FROM messages_contact WHERE id = ?");
    $stmt->execute([$_GET['view']]);
    $view_message = $stmt->fetch();
    
    // Marquer comme lu automatiquement quand on l'ouvre
    if ($view_message && !$view_message['lu']) {
        $stmt = $pdo->prepare("UPDATE messages_contact SET lu = 1 WHERE id = ?");
        $stmt->execute([$_GET['view']]);
        $view_message['lu'] = 1;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Messages</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-dashboard">
        <aside class="admin-sidebar">
            <h2><img src="<?php echo SITE_URL; ?>/uploads/images/logo.png?v=<?php echo time(); ?>" alt="Logo" class="admin-logo"> LBV Admin</h2>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Tableau de bord</a></li>
                    <li><a href="admin_equipe.php"><i class="fas fa-users"></i> Équipe</a></li>
                    <li><a href="admin_alumni.php"><i class="fas fa-user-graduate"></i> Alumni</a></li>
                    <li><a href="admin_projets.php"><i class="fas fa-project-diagram"></i> Projets</a></li>
                    <li><a href="admin_publications.php"><i class="fas fa-book"></i> Publications</a></li>
                    <li><a href="admin_actualites.php"><i class="fas fa-newspaper"></i> Actualités</a></li>
                    <li><a href="admin_galerie.php"><i class="fas fa-images"></i> Galerie</a></li>
                    <li><a href="admin_partenaires.php"><i class="fas fa-handshake"></i> Partenaires</a></li>
                    <li><a href="admin_messages.php" class="active"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <h1><i class="fas fa-envelope"></i> Messages de Contact</h1>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo h($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <?php if ($view_message): ?>
            <!-- Affichage détaillé du message -->
            <div class="message-detail-box">
                <div class="message-detail-header">
                    <h2><?php echo h($view_message['sujet'] ?: 'Sans sujet'); ?></h2>
                    <a href="admin_messages.php" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-times"></i> Fermer
                    </a>
                </div>
                
                <div class="message-detail-meta">
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <strong>De:</strong> <?php echo h($view_message['nom']); ?>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-envelope"></i>
                        <strong>Email:</strong> <a href="mailto:<?php echo h($view_message['email']); ?>"><?php echo h($view_message['email']); ?></a>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <strong>Date:</strong> <?php echo formatDate($view_message['date_envoi'], 'd/m/Y H:i'); ?>
                    </div>
                    <?php if ($view_message['adresse_ip']): ?>
                    <div class="meta-item">
                        <i class="fas fa-network-wired"></i>
                        <strong>IP:</strong> <?php echo h($view_message['adresse_ip']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="message-detail-content">
                    <h3>Message:</h3>
                    <p><?php echo nl2br(h($view_message['message'])); ?></p>
                </div>
                
                <div class="message-detail-actions">
                    <a href="mailto:<?php echo h($view_message['email']); ?>?subject=Re: <?php echo urlencode($view_message['sujet']); ?>" class="btn btn-primary">
                        <i class="fas fa-reply"></i> Répondre par email
                    </a>
                    <a href="?toggle_read=<?php echo $view_message['id']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-<?php echo $view_message['lu'] ? 'envelope' : 'envelope-open'; ?>"></i>
                        Marquer comme <?php echo $view_message['lu'] ? 'non lu' : 'lu'; ?>
                    </a>
                    <a href="?delete=<?php echo $view_message['id']; ?>" class="btn btn-delete" data-confirm-delete>
                        <i class="fas fa-trash"></i> Supprimer
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Liste des messages -->
            <div class="admin-table">
                <h3>Tous les messages (<?php echo count($messages); ?>)</h3>
                
                <?php
                $non_lus = array_filter($messages, function($m) { return !$m['lu']; });
                if (count($non_lus) > 0):
                ?>
                <p style="color: var(--primary-color); font-weight: bold;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo count($non_lus); ?> message(s) non lu(s)
                </p>
                <?php endif; ?>
                
                <table>
                    <thead>
                        <tr>
                            <th>Statut</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Sujet</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $m): ?>
                        <tr style="<?php echo !$m['lu'] ? 'font-weight:bold; background:#f0f9ff;' : ''; ?>">
                            <td>
                                <?php if ($m['lu']): ?>
                                <i class="fas fa-envelope-open" style="color: #999;" title="Lu"></i>
                                <?php else: ?>
                                <i class="fas fa-envelope" style="color: var(--primary-color);" title="Non lu"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo h($m['nom']); ?></td>
                            <td><?php echo h($m['email']); ?></td>
                            <td><?php echo h($m['sujet'] ?: '-'); ?></td>
                            <td><?php echo h(truncate($m['message'], 50)); ?></td>
                            <td><?php echo formatDate($m['date_envoi'], 'd/m/Y H:i'); ?></td>
                            <td class="action-buttons">
                                <a href="?view=<?php echo $m['id']; ?>" class="btn btn-sm btn-primary" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?toggle_read=<?php echo $m['id']; ?>" class="btn btn-sm btn-edit" title="Marquer">
                                    <i class="fas fa-<?php echo $m['lu'] ? 'envelope' : 'envelope-open'; ?>"></i>
                                </a>
                                <a href="?delete=<?php echo $m['id']; ?>" class="btn btn-sm btn-delete" data-confirm-delete title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <style>
    .message-detail-box {
        background: #fff;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .message-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }
    
    .message-detail-header h2 {
        margin: 0;
        color: var(--primary-color);
    }
    
    .message-detail-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1rem;
        background: var(--light-gray);
        border-radius: 5px;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .meta-item i {
        color: var(--primary-color);
    }
    
    .message-detail-content {
        padding: 1.5rem;
        background: var(--light-gray);
        border-radius: 5px;
        margin-bottom: 1.5rem;
    }
    
    .message-detail-content h3 {
        margin-bottom: 1rem;
        color: var(--dark-text);
    }
    
    .message-detail-content p {
        line-height: 1.8;
        white-space: pre-wrap;
    }
    
    .message-detail-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    </style>
    
    <script src="<?php echo SITE_URL; ?>/js/script.js"></script>
</body>
</html>
