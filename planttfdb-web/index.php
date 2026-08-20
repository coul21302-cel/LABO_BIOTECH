<?php
/**
 * PlantTFDB - Home Page
 * @package PlantTFDB
 */

require_once 'config/database.php';

$page_title = 'Home';

// Récupérer les statistiques globales
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_tf,
        COUNT(DISTINCT species_id) as total_species,
        COUNT(DISTINCT family_id) as total_families
    FROM transcription_factors
");
$global_stats = $stmt->fetch();

// Récupérer les derniers TF ajoutés
$stmt = $pdo->query("
    SELECT 
        tf.tf_id,
        tf.gene_name,
        tf.gene_id,
        s.scientific_name as species,
        f.family_code,
        tf.date_added
    FROM transcription_factors tf
    JOIN species s ON tf.species_id = s.species_id
    JOIN tf_families f ON tf.family_id = f.family_id
    ORDER BY tf.date_added DESC
    LIMIT 5
");
$recent_tf = $stmt->fetchAll();

// Récupérer la distribution par famille
$stmt = $pdo->query("
    SELECT 
        f.family_code,
        f.family_name,
        COUNT(tf.tf_id) as tf_count
    FROM tf_families f
    LEFT JOIN transcription_factors tf ON f.family_id = tf.family_id
    GROUP BY f.family_id
    ORDER BY tf_count DESC
    LIMIT 10
");
$family_distribution = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1><i class="fas fa-dna"></i> Plant Transcription Factor Database</h1>
            <p class="hero-subtitle">
                A comprehensive resource for plant transcription factor research
            </p>
            
            <!-- Quick Search -->
            <div class="hero-search">
                <form action="search.php" method="GET" class="search-form">
                    <div class="search-input-group">
                        <input type="text" 
                               name="q" 
                               placeholder="Search by gene name, ID, or species..." 
                               class="search-input"
                               required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </form>
                <p class="search-example">
                    <strong>Examples:</strong> OsMADS1, LOC_Os03g11614, Oryza sativa
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-dna"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo formatNumber($global_stats['total_tf']); ?></h3>
                    <p>Transcription Factors</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo formatNumber($global_stats['total_species']); ?></h3>
                    <p>Plant Species</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo formatNumber($global_stats['total_families']); ?></h3>
                    <p>TF Families</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-content">
                    <h3><?php echo VERSION; ?></h3>
                    <p>Database Version</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="container">
        <h2 class="section-title">Database Features</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>Advanced Search</h3>
                <p>Search by gene name, ID, species, family, or functional annotations</p>
                <a href="search.php" class="btn btn-outline">Search Now</a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3>Browse by Category</h3>
                <p>Explore TF organized by species, family, or functional domains</p>
                <a href="browse.php" class="btn btn-outline">Browse Database</a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-download"></i>
                </div>
                <h3>Download Sequences</h3>
                <p>Export protein and nucleotide sequences in FASTA format</p>
                <a href="download.php" class="btn btn-outline">Download Data</a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>Statistics & Analysis</h3>
                <p>View comprehensive statistics and distribution charts</p>
                <a href="statistics.php" class="btn btn-outline">View Statistics</a>
            </div>
        </div>
    </div>
</section>

<!-- Recent Additions -->
<section class="recent-section">
    <div class="container">
        <h2 class="section-title">Recently Added Transcription Factors</h2>
        
        <?php if (count($recent_tf) > 0): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Gene Name</th>
                        <th>Gene ID</th>
                        <th>Species</th>
                        <th>Family</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_tf as $tf): ?>
                    <tr>
                        <td><strong><?php echo h($tf['gene_name']); ?></strong></td>
                        <td><code><?php echo h($tf['gene_id']); ?></code></td>
                        <td><em><?php echo h($tf['species']); ?></em></td>
                        <td><?php echo getFamilyBadge($tf['family_code']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($tf['date_added'])); ?></td>
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
        <?php else: ?>
        <p class="text-center text-muted">No transcription factors in database yet.</p>
        <?php endif; ?>
    </div>
</section>

<!-- Family Distribution -->
<section class="distribution-section">
    <div class="container">
        <h2 class="section-title">TF Family Distribution</h2>
        
        <div class="distribution-grid">
            <?php foreach ($family_distribution as $family): ?>
            <div class="distribution-item">
                <div class="distribution-bar">
                    <div class="bar-fill" style="width: <?php echo ($family['tf_count'] > 0) ? min(($family['tf_count'] / max(array_column($family_distribution, 'tf_count'))) * 100, 100) : 0; ?>%"></div>
                </div>
                <div class="distribution-info">
                    <span class="family-name"><?php echo h($family['family_code']); ?></span>
                    <span class="tf-count"><?php echo formatNumber($family['tf_count']); ?> TF</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Explore the Database</h2>
            <p>Start discovering plant transcription factors and their regulatory networks</p>
            <div class="cta-buttons">
                <a href="search.php" class="btn btn-large btn-primary">
                    <i class="fas fa-search"></i> Start Searching
                </a>
                <a href="browse.php" class="btn btn-large btn-outline-light">
                    <i class="fas fa-folder-open"></i> Browse All TF
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
