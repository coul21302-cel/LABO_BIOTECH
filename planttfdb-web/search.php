<?php
/**
 * PlantTFDB - Advanced Search
 * @package PlantTFDB
 */

require_once 'config/database.php';

$page_title = 'Search Database';

// Initialiser les variables de recherche
$query = $_GET['q'] ?? '';
$species_filter = $_GET['species'] ?? '';
$family_filter = $_GET['family'] ?? '';
$chromosome_filter = $_GET['chromosome'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * RESULTS_PER_PAGE;

// Récupérer les options pour les filtres
$species_list = $pdo->query("SELECT species_id, scientific_name, common_name FROM species ORDER BY scientific_name")->fetchAll();
$family_list = $pdo->query("SELECT family_id, family_code, family_name FROM tf_families ORDER BY family_code")->fetchAll();

// Construire la requête de recherche
$sql = "SELECT SQL_CALC_FOUND_ROWS
    tf.tf_id,
    tf.gene_id,
    tf.gene_name,
    tf.chromosome,
    tf.protein_length,
    tf.description,
    s.scientific_name,
    s.common_name,
    f.family_code,
    f.family_name
FROM transcription_factors tf
JOIN species s ON tf.species_id = s.species_id
JOIN tf_families f ON tf.family_id = f.family_id
WHERE 1=1";

$params = [];

// Filtre par texte
if (!empty($query)) {
    $sql .= " AND (tf.gene_name LIKE ? OR tf.gene_id LIKE ? OR s.scientific_name LIKE ? OR s.common_name LIKE ? OR tf.description LIKE ?)";
    $search_term = "%$query%";
    $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term, $search_term]);
}

// Filtre par espèce
if (!empty($species_filter)) {
    $sql .= " AND tf.species_id = ?";
    $params[] = $species_filter;
}

// Filtre par famille
if (!empty($family_filter)) {
    $sql .= " AND tf.family_id = ?";
    $params[] = $family_filter;
}

// Filtre par chromosome
if (!empty($chromosome_filter)) {
    $sql .= " AND tf.chromosome = ?";
    $params[] = $chromosome_filter;
}

$sql .= " ORDER BY tf.gene_name LIMIT " . RESULTS_PER_PAGE . " OFFSET $offset";

// Exécuter la recherche
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

// Compter le nombre total de résultats
$total_results = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$total_pages = ceil($total_results / RESULTS_PER_PAGE);

include 'includes/header.php';
?>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-search"></i> Search Database</h1>
        <p>Search for transcription factors by name, ID, species, or family</p>
    </div>
    
    <!-- Search Form -->
    <div class="search-panel">
        <form method="GET" action="search.php" class="search-advanced-form">
            <div class="form-row">
                <div class="form-group col-12">
                    <label for="query"><i class="fas fa-search"></i> Search Query</label>
                    <input type="text" 
                           id="query" 
                           name="q" 
                           class="form-control" 
                           placeholder="Gene name, ID, species, or keyword..."
                           value="<?php echo h($query); ?>">
                    <small class="form-text">Examples: OsMADS1, LOC_Os03g11614, MADS, Oryza</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="species"><i class="fas fa-seedling"></i> Species</label>
                    <select id="species" name="species" class="form-control">
                        <option value="">All Species</option>
                        <?php foreach ($species_list as $species): ?>
                        <option value="<?php echo $species['species_id']; ?>" 
                                <?php echo ($species_filter == $species['species_id']) ? 'selected' : ''; ?>>
                            <?php echo h($species['scientific_name']); ?>
                            <?php if ($species['common_name']): ?>
                                (<?php echo h($species['common_name']); ?>)
                            <?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group col-md-4">
                    <label for="family"><i class="fas fa-layer-group"></i> TF Family</label>
                    <select id="family" name="family" class="form-control">
                        <option value="">All Families</option>
                        <?php foreach ($family_list as $family): ?>
                        <option value="<?php echo $family['family_id']; ?>"
                                <?php echo ($family_filter == $family['family_id']) ? 'selected' : ''; ?>>
                            <?php echo h($family['family_code']); ?> - <?php echo h($family['family_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group col-md-4">
                    <label for="chromosome"><i class="fas fa-map-marker-alt"></i> Chromosome</label>
                    <input type="text" 
                           id="chromosome" 
                           name="chromosome" 
                           class="form-control" 
                           placeholder="e.g., 1, 2, 3..."
                           value="<?php echo h($chromosome_filter); ?>">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="search.php" class="btn btn-outline">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
    
    <!-- Results Section -->
    <?php if (!empty($query) || !empty($species_filter) || !empty($family_filter) || !empty($chromosome_filter)): ?>
    <div class="results-section">
        <div class="results-header">
            <h2>Search Results</h2>
            <p class="results-count">
                Found <strong><?php echo formatNumber($total_results); ?></strong> transcription factor(s)
                <?php if ($total_pages > 1): ?>
                    (Page <?php echo $page; ?> of <?php echo $total_pages; ?>)
                <?php endif; ?>
            </p>
        </div>
        
        <?php if (count($results) > 0): ?>
        <div class="results-list">
            <?php foreach ($results as $tf): ?>
            <div class="result-card">
                <div class="result-header">
                    <h3>
                        <a href="tf_detail.php?id=<?php echo $tf['tf_id']; ?>">
                            <?php echo h($tf['gene_name'] ?: $tf['gene_id']); ?>
                        </a>
                    </h3>
                    <?php echo getFamilyBadge($tf['family_code']); ?>
                </div>
                
                <div class="result-info">
                    <div class="info-item">
                        <i class="fas fa-tag"></i>
                        <strong>Gene ID:</strong> <code><?php echo h($tf['gene_id']); ?></code>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-seedling"></i>
                        <strong>Species:</strong> 
                        <em><?php echo h($tf['scientific_name']); ?></em>
                        <?php if ($tf['common_name']): ?>
                            (<?php echo h($tf['common_name']); ?>)
                        <?php endif; ?>
                    </div>
                    <?php if ($tf['chromosome']): ?>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <strong>Location:</strong> Chromosome <?php echo h($tf['chromosome']); ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($tf['protein_length']): ?>
                    <div class="info-item">
                        <i class="fas fa-ruler"></i>
                        <strong>Length:</strong> <?php echo formatNumber($tf['protein_length']); ?> AA
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($tf['description']): ?>
                <p class="result-description">
                    <?php echo h(truncate($tf['description'], 200)); ?>
                </p>
                <?php endif; ?>
                
                <div class="result-actions">
                    <a href="tf_detail.php?id=<?php echo $tf['tf_id']; ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                    <a href="download.php?id=<?php echo $tf['tf_id']; ?>&type=protein" class="btn btn-sm btn-outline">
                        <i class="fas fa-download"></i> Download Sequence
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" class="page-link">
                <i class="fas fa-chevron-left"></i> Previous
            </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
               class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" class="page-link">
                Next <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <h3>No Results Found</h3>
            <p>Try adjusting your search criteria or browse the database</p>
            <a href="browse.php" class="btn btn-primary">Browse Database</a>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
