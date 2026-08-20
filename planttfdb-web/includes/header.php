<?php
/**
 * PlantTFDB - Header Template
 * @package PlantTFDB
 */

if (!isset($page_title)) {
    $page_title = SITE_NAME;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo SITE_DESCRIPTION; ?>">
    <title><?php echo h($page_title); ?> - <?php echo SITE_NAME; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo SITE_URL; ?>">
                        <i class="fas fa-dna"></i>
                        <span><?php echo SITE_NAME; ?></span>
                    </a>
                    <p class="tagline"><?php echo SITE_DESCRIPTION; ?></p>
                </div>
                
                <nav class="main-nav">
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>
                            <i class="fas fa-home"></i> Home
                        </a></li>
                        <li><a href="<?php echo SITE_URL; ?>/search.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'search.php') ? 'class="active"' : ''; ?>>
                            <i class="fas fa-search"></i> Search
                        </a></li>
                        <li><a href="<?php echo SITE_URL; ?>/browse.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'browse.php') ? 'class="active"' : ''; ?>>
                            <i class="fas fa-folder-open"></i> Browse
                        </a></li>
                        <li><a href="<?php echo SITE_URL; ?>/statistics.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'statistics.php') ? 'class="active"' : ''; ?>>
                            <i class="fas fa-chart-bar"></i> Statistics
                        </a></li>
                        <li><a href="<?php echo SITE_URL; ?>/download.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'download.php') ? 'class="active"' : ''; ?>>
                            <i class="fas fa-download"></i> Download
                        </a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
