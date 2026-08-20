<?php
/**
 * PlantTFDB - Browse Database
 * @package PlantTFDB
 */

require_once 'config/database.php';

$page_title = 'Browse Database';

$browse_by = $_GET['by'] ?? 'family';
$selected_id = $_GET['id'] ?? '';

// Récupérer les familles avec comptage
$families = $pdo->query("
    SELECT 
        f.family_id,
        f.family_code,
        f.family_name,
        f.description,
        COUNT(tf.tf_id) as tf_count
    FROM tf_families f
    LEFT JOIN transcription_factors tf ON f.family_id = tf.family_id
    GROUP BY f.family_id
    ORDER BY f.family_code
")->fetchAll();

// Récupérer les espèces avec comptage
$species = $pdo->query("
    SELECT 
        s.species_id,
        s.scientific_name,
        s.common_name,
        COUNT(tf.tf_id) as tf_count
    FROM species s
    LEFT JOIN transcription_factors tf ON s.species_id = tf.species_id
    GROUP BY s.species_id
    ORDER BY s.scientific_name
")->fetchAll();

// Si un filtre est sélectionné, récupérer les TF
$filtered_tf = [];
if ($selected_id) {
    if ($browse_by == 'family') {
        $stmt = $pdo->prepare("
            SELECT 
                tf.*,
                s.scientific_name,
                s.common_name
            FROM transcription_factors tf
            JOIN species s ON tf.species_id = s.species_id
            WHERE tf.family_id = ?
            ORDER BY tf.gene_name
        ");
    } else {
        $stmt = $pdo->prepare("
            SELECT 
                tf.*,
                f.family_code,
                f.family_name
            FROM transcription_factors tf
            JOIN tf_families f ON tf.family_id = f.family_id
            WHERE tf.species_id = ?
            ORDER BY tf.gene_name
        ");
    }
    $stmt->execute([$selected_id]);
    $filtered_tf = $stmt->fetchAll();
}

include 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-folder-open"></i> Browse Database</h1>
        <p>Explore transcription factors by family or species</p>
    </div>
    
    <!-- Browse Tabs -->
    <div class="browse-tabs">
        <a href="?by=family" class="tab <?php echo ($browse_by == 'family') ? 'active' : ''; ?>">
            <i class="fas fa-layer-group"></i> By TF Family
        </a>
        <a href="?by=species" class="tab <?php echo ($browse_by == 'species') ? 'active' : ''; ?>">
            <i class="fas fa-seedling"></i> By Species
        </a>
    </div>
    
    <div class="browse-container">
        <!-- Sidebar List -->
        <div class="browse-sidebar">
            <?php if ($browse_by == 'family'): ?>
                <h3>TF Families</h3>
                <ul class="category-list">
                    <?php foreach ($families as $family): ?>
                    <li>
                        <a href="?by=family&id=<?php echo $family['family_id']; ?>"
                           class="<?php echo ($selected_id == $family['family_id']) ? 'active' : ''; ?>">
                            <?php echo getFamilyBadge($family['family_code']); ?>
                            <span class="family-name"><?php echo h($family['family_name']); ?></span>
                            <span class="count"><?php echo formatNumber($family['tf_count']); ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <h3>Plant Species</h3>
                <ul class="category-list">
                    <?php foreach ($species as $sp): ?>
                    <li>
                        <a href="?by=species&id=<?php echo $sp['species_id']; ?>"
                           class="<?php echo ($selected_id == $sp['species_id']) ? 'active' : ''; ?>">
                            <i class="fas fa-seedling"></i>
                            <span class="species-name">
                                <em><?php echo h($sp['scientific_name']); ?></em>
                                <?php if ($sp['common_name']): ?>
                                <small>(<?php echo h($sp['common_name']); ?>)</small>
                                <?php endif; ?>
                            </span>
                            <span class="count"><?php echo formatNumber($sp['tf_count']); ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <!-- Results Panel -->
        <div class="browse-content">
            <?php if ($selected_id && count($filtered_tf) > 0): ?>
                <div class="results-header">
                    <h2>Transcription Factors</h2>
                    <p class="results-count"><?php echo formatNumber(count($filtered_tf)); ?> TF found</p>
                </div>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Gene Name</th>
                                <th>Gene ID</th>
                                <?php if ($browse_by == 'family'): ?>
                                <th>Species</th>
                                <?php else: ?>
                                <th>Family</th>
                                <?php endif; ?>
                                <th>Chromosome</th>
                                <th>Length</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($filtered_tf as $tf): ?>
                            <tr>
                                <td><strong><?php echo h($tf['gene_name'] ?: '-'); ?></strong></td>
                                <td><code><?php echo h($tf['gene_id']); ?></code></td>
                                <?php if ($browse_by == 'family'): ?>
                                <td><em><?php echo h($tf['scientific_name']); ?></em></td>
                                <?php else: ?>
                                <td><?php echo getFamilyBadge($tf['family_code']); ?></td>
                                <?php endif; ?>
                                <td><?php echo h($tf['chromosome'] ?: '-'); ?></td>
                                <td><?php echo $tf['protein_length'] ? formatNumber($tf['protein_length']) . ' AA' : '-'; ?></td>
                                <td>
                                    <a href="tf_detail.php?id=<?php echo $tf['tf_id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($selected_id): ?>
                <div class="no-results">
                    <i class="fas fa-inbox"></i>
                    <h3>No Transcription Factors Found</h3>
                    <p>This category doesn't contain any TF yet</p>
                </div>
            <?php else: ?>
                <div class="browse-placeholder">
                    <i class="fas fa-hand-pointer"></i>
                    <h3>Select a Category</h3>
                    <p>Choose a <?php echo ($browse_by == 'family') ? 'TF family' : 'species'; ?> from the list to view transcription factors</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
