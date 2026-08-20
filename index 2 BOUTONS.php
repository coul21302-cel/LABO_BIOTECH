<?php
require_once 'db_config.php';
$current_page = 'accueil';
$page_title = 'Accueil';

// Récupérer les dernières actualités
$stmt = $pdo->query("SELECT * FROM actualites WHERE COALESCE(publie, 1) = 1 ORDER BY date_publication DESC LIMIT 3");
$actualites = $stmt->fetchAll();

// Récupérer les dernières publications
$stmt = $pdo->query("SELECT * FROM publications ORDER BY annee DESC, id DESC LIMIT 4");
$publications = $stmt->fetchAll();

// Récupérer les axes de recherche (projets en cours)
$stmt = $pdo->query("SELECT * FROM projets WHERE statut = 'En cours' ORDER BY ordre_affichage LIMIT 3");
$projets = $stmt->fetchAll();

include 'header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="container">
            <h1 class="hero-title animate-fade-in">Laboratoire Campus de Biotechnologie Végétale</h1>
            <p class="hero-subtitle animate-fade-in-delay">Excellence en recherche pour une agriculture durable</p>
            <div class="hero-buttons animate-fade-in-delay-2">
                <a href="<?php echo SITE_URL; ?>/pages/recherche.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-microscope"></i> Nos Recherches
                </a>
                <a href="<?php echo SITE_URL; ?>/pages/publications.php" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-book"></i> Publications
                </a>
            </div>
        </div>
    </div>
    <div class="hero-scroll">
        <i class="fas fa-chevron-down"></i>
    </div>
</section>

<!-- Présentation -->
<section class="section section-about">
    <div class="container">
        <div class="section-header text-center">
            <h2>Bienvenue au Laboratoire</h2>
            <p class="section-subtitle">Un centre d'excellence en biotechnologie végétale</p>
        </div>
        
        <div class="about-grid">
            <div class="about-card">
                <div class="about-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h3>Notre Mission</h3>
                <p>Développer des solutions biotechnologiques innovantes pour améliorer la productivité et la résilience des cultures tropicales face aux défis climatiques et alimentaires.</p>
            </div>
            
            <div class="about-card">
                <div class="about-icon">
                    <i class="fas fa-atom"></i>
                </div>
                <h3>Nos Expertises</h3>
                <p>Amélioration génétique, biologie moléculaire, culture in vitro, microbiologie végétale et biotechnologies pour le développement durable.</p>
            </div>
            
            <div class="about-card">
                <div class="about-icon">
                    <i class="fas fa-globe-africa"></i>
                </div>
                <h3>Notre Impact</h3>
                <p>Contribution à la sécurité alimentaire en Afrique de l'Ouest à travers des recherches de pointe et des partenariats stratégiques internationaux.</p>
            </div>
        </div>
    </div>
</section>

<!-- Axes de Recherche -->
<section class="section section-research bg-light">
    <div class="container">
        <div class="section-header text-center">
            <h2>Axes de Recherche</h2>
            <p class="section-subtitle">Nos projets de recherche innovants</p>
        </div>
        
        <?php if (count($projets) > 0): ?>
        <div class="research-grid">
            <?php foreach ($projets as $projet): ?>
            <div class="research-card">
                <div class="research-icon">
                    <i class="fas fa-flask"></i>
                </div>
                <h3><?php echo h($projet['titre']); ?></h3>
                <p><?php echo h(truncate($projet['description'], 150)); ?></p>
                <span class="badge badge-success"><?php echo h($projet['statut']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/pages/recherche.php" class="btn btn-primary">
                Voir tous les projets <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">Aucun projet en cours pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Publications Récentes -->
<section class="section section-publications">
    <div class="container">
        <div class="section-header text-center">
            <h2>Publications Récentes</h2>
            <p class="section-subtitle">Nos dernières contributions scientifiques</p>
        </div>
        
        <?php if (count($publications) > 0): ?>
        <div class="publications-grid">
            <?php foreach ($publications as $pub): ?>
            <div class="publication-card">
                <div class="publication-year">
                    <i class="fas fa-calendar-alt"></i> <?php echo h($pub['annee']); ?>
                </div>
                <h4><?php echo h($pub['titre']); ?></h4>
                <p class="publication-authors"><i class="fas fa-user"></i> <?php echo h($pub['auteurs']); ?></p>
                <?php if (!empty($pub['revue'])): ?>
                <p class="publication-journal"><i class="fas fa-book"></i> <?php echo h($pub['revue']); ?></p>
                <?php endif; ?>
                <div class="publication-meta">
                    <span class="badge badge-info"><?php echo h($pub['type_publication']); ?></span>
                    <?php if (!empty($pub['doi'])): ?>
                    <a href="https://doi.org/<?php echo h($pub['doi']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> DOI
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/pages/publications.php" class="btn btn-primary">
                Toutes les publications <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">Aucune publication disponible.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Actualités -->
<section class="section section-news bg-light">
    <div class="container">
        <div class="section-header text-center">
            <h2>Actualités</h2>
            <p class="section-subtitle">Événements et annonces du laboratoire</p>
        </div>
        
        <?php if (count($actualites) > 0): ?>
        <div class="news-grid">
            <?php foreach ($actualites as $actu): ?>
            <div class="news-card">
                <?php if (!empty($actu['image'])): ?>
                <div class="news-image" style="background-image: url('<?php echo UPLOAD_URL . h($actu['image']); ?>')"></div>
                <?php else: ?>
                <div class="news-image news-image-default">
                    <i class="fas fa-newspaper"></i>
                </div>
                <?php endif; ?>
                <div class="news-content">
                    <div class="news-meta">
                        <span class="badge badge-<?php echo $actu['type'] == 'Séminaire' ? 'primary' : ($actu['type'] == 'Conférence' ? 'success' : 'info'); ?>">
                            <?php echo h($actu['type']); ?>
                        </span>
                        <span class="news-date">
                            <i class="fas fa-clock"></i> <?php echo formatDate($actu['date_publication']); ?>
                        </span>
                    </div>
                    <h3><?php echo h($actu['titre']); ?></h3>
                    <p><?php echo h(truncate($actu['contenu'], 120)); ?></p>
                    <?php if (!empty($actu['date_evenement'])): ?>
                    <p class="news-event-date">
                        <i class="fas fa-calendar"></i> <?php echo formatDate($actu['date_evenement']); ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/pages/actualites.php" class="btn btn-primary">
                Toutes les actualités <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">Aucune actualité pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Chiffres Clés -->
<section class="section section-stats">
    <div class="container">
        <div class="stats-grid">
            <?php
            // Compter les membres
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM membres WHERE actif = 1");
            $total_membres = $stmt->fetch()['total'];
            
            // Compter les projets
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM projets");
            $total_projets = $stmt->fetch()['total'];
            
            // Compter les publications
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM publications");
            $total_publications = $stmt->fetch()['total'];
            
            // Compter les partenaires
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM partenaires WHERE actif = 1");
            $total_partenaires = $stmt->fetch()['total'];
            ?>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $total_membres; ?></div>
                <div class="stat-label">Membres de l'équipe</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="stat-number"><?php echo $total_projets; ?></div>
                <div class="stat-label">Projets de recherche</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="stat-number"><?php echo $total_publications; ?></div>
                <div class="stat-label">Publications scientifiques</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="stat-number"><?php echo $total_partenaires; ?></div>
                <div class="stat-label">Partenaires internationaux</div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
