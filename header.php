<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? h($page_title) . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <meta name="description" content="Laboratoire de Biotechnologie Végétale - Recherche et Innovation">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="<?php echo SITE_URL; ?>">
                        <img src="<?php echo SITE_URL; ?>/uploads/images/logo2.png?v=<?php echo time(); ?>" alt="Logo LBV" class="logo-image">
                        <span class="logo-text">
                            <strong>LCBV</strong>
                            <small>Laboratoire Campus de Biotechnologie Végétale</small>
                        </span>
                    </a>
                </div>
                
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <nav class="main-nav" id="mainNav">
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>" <?php echo (!isset($current_page) || $current_page == 'accueil') ? 'class="active"' : ''; ?>>Accueil</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/laboratoire.php" <?php echo (isset($current_page) && $current_page == 'laboratoire') ? 'class="active"' : ''; ?>>Laboratoire</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/equipe.php" <?php echo (isset($current_page) && $current_page == 'equipe') ? 'class="active"' : ''; ?>>Équipe</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/alumni.php" <?php echo (isset($current_page) && $current_page == 'alumni') ? 'class="active"' : ''; ?>>Alumni</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/recherche.php" <?php echo (isset($current_page) && $current_page == 'recherche') ? 'class="active"' : ''; ?>>Recherche</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/publications.php" <?php echo (isset($current_page) && $current_page == 'publications') ? 'class="active"' : ''; ?>>Publications</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/actualites.php" <?php echo (isset($current_page) && $current_page == 'actualites') ? 'class="active"' : ''; ?>>Actualités</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/galerie.php" <?php echo (isset($current_page) && $current_page == 'galerie') ? 'class="active"' : ''; ?>>Galerie</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/contact.php" <?php echo (isset($current_page) && $current_page == 'contact') ? 'class="active"' : ''; ?>>Contact</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="main-content">
