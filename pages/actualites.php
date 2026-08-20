<?php
require_once '../db_config.php';
$current_page = 'actualites';
$page_title = 'Actualités';

$stmt = $pdo->query("SELECT * FROM actualites WHERE publie = 1 ORDER BY date_publication DESC");
$actualites = $stmt->fetchAll();

include '../header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Actualités</h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Accueil</a> / Actualités
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (count($actualites) > 0): ?>
        <div class="actualites-list">
            <?php foreach ($actualites as $actu): ?>
            <article class="actualite-card">
                <?php if (!empty($actu['image'])): ?>
                <div class="actualite-image" style="background-image: url('<?php echo UPLOAD_URL . h($actu['image']); ?>')"></div>
                <?php endif; ?>
                
                <div class="actualite-content">
                    <div class="actualite-meta">
                        <span class="badge badge-<?php echo $actu['type'] == 'Séminaire' ? 'primary' : ($actu['type'] == 'Conférence' ? 'success' : 'info'); ?>">
                            <?php echo h($actu['type']); ?>
                        </span>
                        <span class="actualite-date">
                            <i class="fas fa-clock"></i> <?php echo formatDate($actu['date_publication'], 'd/m/Y H:i'); ?>
                        </span>
                    </div>
                    
                    <h2 class="actualite-title"><?php echo h($actu['titre']); ?></h2>
                    
                    <?php if (!empty($actu['date_evenement'])): ?>
                    <p class="evenement-date">
                        <i class="fas fa-calendar-alt"></i> 
                        <strong>Date de l'événement:</strong> <?php echo formatDate($actu['date_evenement'], 'd/m/Y'); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($actu['lieu'])): ?>
                    <p class="evenement-lieu">
                        <i class="fas fa-map-marker-alt"></i> 
                        <strong>Lieu:</strong> <?php echo h($actu['lieu']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <div class="actualite-texte">
                        <?php echo nl2br(h($actu['contenu'])); ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">Aucune actualité pour le moment.</p>
        <?php endif; ?>
    </div>
</section>

<style>
.actualites-list {
    max-width: 900px;
    margin: 0 auto;
}

.actualite-card {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.actualite-image {
    height: 300px;
    background-size: cover;
    background-position: center;
}

.actualite-content {
    padding: 2rem;
}

.actualite-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.actualite-date {
    color: var(--gray-text);
    font-size: 0.9rem;
}

.actualite-title {
    font-size: 1.8rem;
    color: var(--dark-text);
    margin-bottom: 1.5rem;
}

.evenement-date,
.evenement-lieu {
    margin-bottom: 1rem;
    color: var(--gray-text);
}

.actualite-texte {
    line-height: 1.8;
    font-size: 1.05rem;
}
</style>

<?php include '../footer.php'; ?>
