<?php
require_once '../db_config.php';
$current_page = 'galerie';
$page_title = 'Galerie Photos';

$categorie_filter = isset($_GET['cat']) ? $_GET['cat'] : '';

$sql = "SELECT * FROM galerie WHERE 1=1";
$params = [];

if ($categorie_filter) {
    $sql .= " AND categorie = ?";
    $params[] = $categorie_filter;
}

$sql .= " ORDER BY ordre_affichage, date_ajout DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$images = $stmt->fetchAll();

include '../header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Galerie Photos</h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Accueil</a> / Galerie
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="gallery-filters">
            <a href="<?php echo SITE_URL; ?>/pages/galerie.php" class="filter-btn <?php echo !$categorie_filter ? 'active' : ''; ?>">
                Toutes
            </a>
            <a href="?cat=Activité" class="filter-btn <?php echo $categorie_filter == 'Activité' ? 'active' : ''; ?>">
                Activités
            </a>
            <a href="?cat=Équipement" class="filter-btn <?php echo $categorie_filter == 'Équipement' ? 'active' : ''; ?>">
                Équipements
            </a>
            <a href="?cat=Expérience" class="filter-btn <?php echo $categorie_filter == 'Expérience' ? 'active' : ''; ?>">
                Expériences
            </a>
            <a href="?cat=Événement" class="filter-btn <?php echo $categorie_filter == 'Événement' ? 'active' : ''; ?>">
                Événements
            </a>
        </div>
        
        <?php if (count($images) > 0): ?>
        <div class="gallery-grid">
            <?php foreach ($images as $img): ?>
            <div class="gallery-item">
                <img src="<?php echo UPLOAD_URL . h($img['nom_fichier']); ?>" 
                     alt="<?php echo h($img['titre']); ?>"
                     class="gallery-image">
                <div class="gallery-overlay">
                    <h3><?php echo h($img['titre']); ?></h3>
                    <?php if (!empty($img['description'])): ?>
                    <p><?php echo h($img['description']); ?></p>
                    <?php endif; ?>
                    <span class="badge badge-primary"><?php echo h($img['categorie']); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-center text-muted">Aucune image dans cette catégorie.</p>
        <?php endif; ?>
    </div>
</section>

<style>
.gallery-filters {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 3rem;
}

.filter-btn {
    padding: 0.7rem 1.5rem;
    border-radius: 25px;
    background: #fff;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    transition: var(--transition);
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--primary-color);
    color: #fff;
}
</style>

<?php include '../footer.php'; ?>
