<?php
/**
 * PlantTFDB - Admin Dashboard
 * @package PlantTFDB Admin
 */

require_once '../config/database.php';
require_once 'includes/auth.php';
require_once 'includes/admin_functions.php';

requireAuth();

$page_title = 'Dashboard';
$stats = getDashboardStats($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - PlantTFDB Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-body">
    
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-logo">
            <i class="fas fa-dna"></i>
            <span>PlantTFDB Admin</span>
        </div>
        
        <nav class="admin-nav">
            <a href="dashboard.php" class="active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="tf_manage.php">
                <i class="fas fa-dna"></i> Manage TF
            </a>
            <a href="tf_add.php">
                <i class="fas fa-plus-circle"></i> Add TF
            </a>
            <a href="families_manage.php">
                <i class="fas fa-layer-group"></i> TF Families
            </a>
            <a href="species_manage.php">
                <i class="fas fa-seedling"></i> Species
            </a>
            <a href="import_csv.php">
                <i class="fas fa-file-upload"></i> Import Data
            </a>
            <hr>
            <a href="../index.php" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Public Site
            </a>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
        
        <div class="admin-user">
            <i class="fas fa-user-circle"></i>
            <span><?php echo h(getLoggedInUser()); ?></span>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <h1><i class="fas fa-home"></i> Dashboard</h1>
            <div class="admin-actions">
                <span class="welcome-text">Welcome, <?php echo h(getLoggedInUser()); ?>!</span>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #2d7a3e, #4caf50);">
                    <i class="fas fa-dna"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo formatNumber($stats['total_tf']); ?></h3>
                    <p>Total Transcription Factors</p>
                    <a href="tf_manage.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #1976d2, #42a5f5);">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo formatNumber($stats['total_species']); ?></h3>
                    <p>Plant Species</p>
                    <a href="species_manage.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f57c00, #ffa726);">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo formatNumber($stats['total_families']); ?></h3>
                    <p>TF Families</p>
                    <a href="families_manage.php" class="stat-link">Manage <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #7b1fa2, #ab47bc);">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo formatNumber($stats['tf_this_week']); ?></h3>
                    <p>Added This Week</p>
                    <span class="stat-badge">New</span>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="tf_add.php" class="action-btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    <span>Add New TF</span>
                </a>
                <a href="import_csv.php" class="action-btn btn-secondary">
                    <i class="fas fa-file-upload"></i>
                    <span>Import CSV/FASTA</span>
                </a>
                <a href="families_manage.php" class="action-btn btn-success">
                    <i class="fas fa-layer-group"></i>
                    <span>Manage Families</span>
                </a>
                <a href="species_manage.php" class="action-btn btn-info">
                    <i class="fas fa-seedling"></i>
                    <span>Manage Species</span>
                </a>
            </div>
        </div>
        
        <!-- Data Overview -->
        <div class="overview-grid">
            <div class="overview-card">
                <h3><i class="fas fa-chart-pie"></i> Data Completeness</h3>
                <div class="progress-item">
                    <div class="progress-label">
                        <span>TF with Protein Sequence</span>
                        <strong><?php echo formatNumber($stats['tf_with_protein']); ?> / <?php echo formatNumber($stats['total_tf']); ?></strong>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?php echo ($stats['total_tf'] > 0) ? ($stats['tf_with_protein'] / $stats['total_tf'] * 100) : 0; ?>%"></div>
                    </div>
                </div>
                <div class="progress-item">
                    <div class="progress-label">
                        <span>TF with CDS Sequence</span>
                        <strong><?php echo formatNumber($stats['tf_with_cds']); ?> / <?php echo formatNumber($stats['total_tf']); ?></strong>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?php echo ($stats['total_tf'] > 0) ? ($stats['tf_with_cds'] / $stats['total_tf'] * 100) : 0; ?>%"></div>
                    </div>
                </div>
            </div>
            
            <div class="overview-card">
                <h3><i class="fas fa-clock"></i> Recently Added</h3>
                <div class="recent-list">
                    <?php if (count($stats['recent_tf']) > 0): ?>
                        <?php foreach ($stats['recent_tf'] as $tf): ?>
                        <div class="recent-item">
                            <div class="recent-icon">
                                <i class="fas fa-dna"></i>
                            </div>
                            <div class="recent-info">
                                <strong><?php echo h($tf['gene_name'] ?: $tf['gene_id']); ?></strong>
                                <small><em><?php echo h($tf['scientific_name']); ?></em></small>
                            </div>
                            <div class="recent-date">
                                <?php echo date('M d, Y', strtotime($tf['date_added'])); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No recent additions</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- System Info -->
        <div class="system-info">
            <h3><i class="fas fa-info-circle"></i> System Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Database Version:</span>
                    <span class="info-value"><?php echo VERSION; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">PHP Version:</span>
                    <span class="info-value"><?php echo phpversion(); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">MySQL Version:</span>
                    <span class="info-value"><?php echo $pdo->query('SELECT VERSION()')->fetchColumn(); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Last Login:</span>
                    <span class="info-value"><?php echo date('Y-m-d H:i:s', $_SESSION['login_time']); ?></span>
                </div>
            </div>
        </div>
    </main>
    
</body>
</html>
