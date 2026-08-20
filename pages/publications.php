<?php
require_once '../db_config.php';
$current_page = 'publications';
$page_title = 'Publications';

// Filtres
$annee_filter = isset($_GET['annee']) ? $_GET['annee'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';

// Construction de la requête
$sql = "SELECT p.*, m.prenom, m.nom as nom_membre 
        FROM publications p 
        LEFT JOIN membres m ON p.membre_id = m.id 
        WHERE 1=1";
$params = [];

if ($annee_filter) {
    $sql .= " AND p.annee = ?";
    $params[] = $annee_filter;
}

if ($type_filter) {
    $sql .= " AND p.type_publication = ?";
    $params[] = $type_filter;
}

$sql .= " ORDER BY p.annee DESC, p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$publications = $stmt->fetchAll();

// Récupérer les années disponibles
$annees = $pdo->query("SELECT DISTINCT annee FROM publications ORDER BY annee DESC")->fetchAll(PDO::FETCH_COLUMN);

include '../header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Publications Scientifiques</h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Accueil</a> / Publications
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Filtres -->
        <div class="filters-box">
            <form method="GET" class="filters-form">
                <div class="filter-group">
                    <label><i class="fas fa-calendar"></i> Année:</label>
                    <select name="annee" class="form-control">
                        <option value="">Toutes les années</option>
                        <?php foreach ($annees as $annee): ?>
                        <option value="<?php echo $annee; ?>" <?php echo $annee_filter == $annee ? 'selected' : ''; ?>>
                            <?php echo $annee; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label><i class="fas fa-tag"></i> Type:</label>
                    <select name="type" class="form-control">
                        <option value="">Tous les types</option>
                        <option value="Article" <?php echo $type_filter == 'Article' ? 'selected' : ''; ?>>Article</option>
                        <option value="Conférence" <?php echo $type_filter == 'Conférence' ? 'selected' : ''; ?>>Conférence</option>
                        <option value="Chapitre" <?php echo $type_filter == 'Chapitre' ? 'selected' : ''; ?>>Chapitre</option>
                        <option value="Thèse" <?php echo $type_filter == 'Thèse' ? 'selected' : ''; ?>>Thèse</option>
                        <option value="Autre" <?php echo $type_filter == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                
                <?php if ($annee_filter || $type_filter): ?>
                <a href="<?php echo SITE_URL; ?>/pages/publications.php" class="btn btn-outline-primary">
                    <i class="fas fa-times"></i> Réinitialiser
                </a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Résultats -->
        <div class="publications-count">
            <strong><?php echo count($publications); ?></strong> publication<?php echo count($publications) > 1 ? 's' : ''; ?> trouvée<?php echo count($publications) > 1 ? 's' : ''; ?>
        </div>
        
        <?php if (count($publications) > 0): ?>
        <div class="publications-list">
            <?php 
            $current_year = '';
            foreach ($publications as $pub): 
                if ($pub['annee'] != $current_year):
                    if ($current_year != '') echo '</div>';
                    $current_year = $pub['annee'];
            ?>
            <div class="year-group">
                <h2 class="year-title"><?php echo h($current_year); ?></h2>
            <?php endif; ?>
            
            <div class="publication-item">
                <div class="pub-header">
                    <span class="badge badge-info"><?php echo h($pub['type_publication']); ?></span>
                    <span class="pub-year"><?php echo h($pub['annee']); ?></span>
                </div>
                
                <h3 class="pub-title"><?php echo h($pub['titre']); ?></h3>
                
                <p class="pub-authors">
                    <i class="fas fa-users"></i> <?php echo h($pub['auteurs']); ?>
                </p>
                
                <?php if (!empty($pub['revue'])): ?>
                <p class="pub-journal">
                    <i class="fas fa-book"></i> <em><?php echo h($pub['revue']); ?></em>
                    <?php if ($pub['volume']): ?>
                    , Vol. <?php echo h($pub['volume']); ?>
                    <?php endif; ?>
                    <?php if ($pub['pages']): ?>
                    , pp. <?php echo h($pub['pages']); ?>
                    <?php endif; ?>
                </p>
                <?php endif; ?>
                
                <?php if (!empty($pub['resume'])): ?>
                <div class="pub-abstract">
                    <strong>Résumé:</strong>
                    <p><?php echo h($pub['resume']); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="pub-actions">
                    <?php if (!empty($pub['doi'])): ?>
                    <a href="https://doi.org/<?php echo h($pub['doi']); ?>" target="_blank" class="btn btn-sm btn-primary">
                        <i class="fas fa-external-link-alt"></i> DOI
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($pub['lien_pdf'])): ?>
                    <a href="<?php echo h($pub['lien_pdf']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <p>Aucune publication trouvée avec ces critères.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.filters-box {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.filters-form {
    display: flex;
    gap: 1rem;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.publications-count {
    margin-bottom: 2rem;
    font-size: 1.1rem;
    color: var(--gray-text);
}

.year-group {
    margin-bottom: 3rem;
}

.year-title {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-color);
}

.publication-item {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.pub-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.pub-year {
    font-weight: 600;
    color: var(--gray-text);
}

.pub-title {
    font-size: 1.3rem;
    color: var(--dark-text);
    margin-bottom: 1rem;
    line-height: 1.4;
}

.pub-authors,
.pub-journal {
    margin-bottom: 0.8rem;
    color: var(--gray-text);
}

.pub-abstract {
    margin: 1.5rem 0;
    padding: 1rem;
    background: var(--light-gray);
    border-radius: 5px;
}

.pub-abstract strong {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.pub-abstract p {
    font-size: 0.95rem;
    line-height: 1.6;
}

.pub-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.no-results {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--gray-text);
}

.no-results i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}
</style>

<?php include '../footer.php'; ?>
