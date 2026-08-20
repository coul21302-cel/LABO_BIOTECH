<?php
require_once '../db_config.php';
$current_page = 'partenaires';
$page_title = 'Nos Partenaires';

// Récupérer tous les partenaires actifs
$stmt = $pdo->query("SELECT * FROM partenaires WHERE actif = 1 ORDER BY ordre_affichage, nom");
$tous_partenaires = $stmt->fetchAll();

// Séparer en nationaux et internationaux
$partenaires_nationaux = [];
$partenaires_internationaux = [];

foreach ($tous_partenaires as $part) {
    // Si le pays est "Sénégal" ou vide, c'est un partenaire national
    if (empty($part['pays']) || strtolower($part['pays']) == 'sénégal' || strtolower($part['pays']) == 'senegal') {
        $partenaires_nationaux[] = $part;
    } else {
        $partenaires_internationaux[] = $part;
    }
}

include '../header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Nos Partenaires</h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Accueil</a> / Partenaires
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section-header text-center">
            <p class="section-subtitle">Nos collaborations stratégiques pour la recherche et l'innovation</p>
        </div>
        
        <!-- PARTENAIRES NATIONAUX -->
        <?php if (count($partenaires_nationaux) > 0): ?>
        <div class="partenaires-section">
            <h2 class="partenaires-title">
                <i class="fas fa-flag"></i> Partenaires Nationaux
                <span class="count-badge"><?php echo count($partenaires_nationaux); ?></span>
            </h2>
            
            <div class="partners-grid">
                <?php foreach ($partenaires_nationaux as $part): ?>
                <div class="partner-card">
                    <div class="partner-logo">
                        <?php if (!empty($part['logo'])): ?>
                        <img src="<?php echo UPLOAD_URL . h($part['logo']); ?>" alt="<?php echo h($part['nom']); ?>">
                        <?php else: ?>
                        <i class="fas fa-university"></i>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo h($part['nom']); ?></h3>
                    <p class="partner-type">
                        <span class="badge badge-success">
                            <i class="fas fa-flag"></i> <?php echo h($part['type']); ?>
                        </span>
                    </p>
                    <?php if (!empty($part['ville'])): ?>
                    <p class="partner-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo h($part['ville']); ?>, Sénégal
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($part['description'])): ?>
                    <p class="partner-description"><?php echo h($part['description']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($part['site_web'])): ?>
                    <a href="<?php echo h($part['site_web']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Site web
                    </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- PARTENAIRES INTERNATIONAUX -->
        <?php if (count($partenaires_internationaux) > 0): ?>
        <div class="partenaires-section">
            <h2 class="partenaires-title">
                <i class="fas fa-globe-africa"></i> Partenaires Internationaux
                <span class="count-badge"><?php echo count($partenaires_internationaux); ?></span>
            </h2>
            
            <div class="partners-grid">
                <?php foreach ($partenaires_internationaux as $part): ?>
                <div class="partner-card international">
                    <div class="partner-logo">
                        <?php if (!empty($part['logo'])): ?>
                        <img src="<?php echo UPLOAD_URL . h($part['logo']); ?>" alt="<?php echo h($part['nom']); ?>">
                        <?php else: ?>
                        <i class="fas fa-globe"></i>
                        <?php endif; ?>
                    </div>
                    <h3><?php echo h($part['nom']); ?></h3>
                    <p class="partner-type">
                        <span class="badge badge-info">
                            <i class="fas fa-globe"></i> <?php echo h($part['type']); ?>
                        </span>
                    </p>
                    <?php if (!empty($part['ville']) || !empty($part['pays'])): ?>
                    <p class="partner-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo h($part['ville']); ?><?php echo $part['ville'] && $part['pays'] ? ', ' : ''; ?><?php echo h($part['pays']); ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($part['description'])): ?>
                    <p class="partner-description"><?php echo h($part['description']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($part['site_web'])): ?>
                    <a href="<?php echo h($part['site_web']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Site web
                    </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (count($partenaires_nationaux) == 0 && count($partenaires_internationaux) == 0): ?>
        <p class="text-center text-muted">Aucun partenaire enregistré.</p>
        <?php endif; ?>
    </div>
</section>

<style>
.partenaires-section {
    margin-bottom: 4rem;
}

.partenaires-title {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid var(--primary-color);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.count-badge {
    background: var(--primary-color);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 1rem;
    font-weight: 500;
}

.partner-card {
    border: 2px solid var(--border-color);
    transition: all 0.3s ease;
}

.partner-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 8px 20px rgba(45, 122, 62, 0.2);
}

.partner-card.international:hover {
    border-color: var(--secondary-color);
    box-shadow: 0 8px 20px rgba(30, 90, 142, 0.2);
}

.partner-type {
    margin: 1rem 0;
}

.partner-location {
    color: var(--gray-text);
    margin-bottom: 1rem;
    font-weight: 500;
}

.partner-description {
    color: var(--gray-text);
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.partner-logo {
    position: relative;
    overflow: hidden;
}

.partner-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.partner-card:hover .partner-logo img {
    transform: scale(1.05);
}

.badge-success {
    background: #4caf50;
    color: white;
}

.badge-info {
    background: #2196f3;
    color: white;
}
</style>

<?php include '../footer.php'; ?>
