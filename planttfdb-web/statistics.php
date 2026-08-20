<?php
/**
 * PlantTFDB - Statistics Page
 * @package PlantTFDB
 */

require_once 'config/database.php';

$page_title = 'Statistics';

// Statistiques globales
$global_stats = $pdo->query("
    SELECT 
        COUNT(*) as total_tf,
        COUNT(DISTINCT species_id) as total_species,
        COUNT(DISTINCT family_id) as total_families,
        AVG(protein_length) as avg_length
    FROM transcription_factors
")->fetch();

// Distribution par famille
$family_stats = $pdo->query("
    SELECT 
        f.family_code,
        f.family_name,
        COUNT(tf.tf_id) as tf_count
    FROM tf_families f
    LEFT JOIN transcription_factors tf ON f.family_id = tf.family_id
    GROUP BY f.family_id
    HAVING tf_count > 0
    ORDER BY tf_count DESC
")->fetchAll();

// Distribution par espèce
$species_stats = $pdo->query("
    SELECT 
        s.scientific_name,
        s.common_name,
        COUNT(tf.tf_id) as tf_count
    FROM species s
    LEFT JOIN transcription_factors tf ON s.species_id = s.species_id
    GROUP BY s.species_id
    HAVING tf_count > 0
    ORDER BY tf_count DESC
")->fetchAll();

// Distribution par chromosome
$chromosome_stats = $pdo->query("
    SELECT 
        chromosome,
        COUNT(*) as tf_count
    FROM transcription_factors
    WHERE chromosome IS NOT NULL
    GROUP BY chromosome
    ORDER BY CAST(chromosome AS UNSIGNED), chromosome
")->fetchAll();

// Distribution de taille des protéines
$size_stats = $pdo->query("
    SELECT 
        CASE 
            WHEN protein_length < 100 THEN '< 100 AA'
            WHEN protein_length < 200 THEN '100-200 AA'
            WHEN protein_length < 300 THEN '200-300 AA'
            WHEN protein_length < 500 THEN '300-500 AA'
            ELSE '> 500 AA'
        END as size_range,
        COUNT(*) as count
    FROM transcription_factors
    WHERE protein_length IS NOT NULL
    GROUP BY size_range
    ORDER BY MIN(protein_length)
")->fetchAll();

include 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-chart-bar"></i> Database Statistics</h1>
        <p>Comprehensive statistics and distribution charts</p>
    </div>
    
    <!-- Global Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card large">
            <div class="stat-icon">
                <i class="fas fa-dna"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo formatNumber($global_stats['total_tf']); ?></h3>
                <p>Total Transcription Factors</p>
            </div>
        </div>
        
        <div class="stat-card large">
            <div class="stat-icon">
                <i class="fas fa-seedling"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo formatNumber($global_stats['total_species']); ?></h3>
                <p>Plant Species</p>
            </div>
        </div>
        
        <div class="stat-card large">
            <div class="stat-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo formatNumber($global_stats['total_families']); ?></h3>
                <p>TF Families</p>
            </div>
        </div>
        
        <div class="stat-card large">
            <div class="stat-icon">
                <i class="fas fa-ruler"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo formatNumber($global_stats['avg_length'], 0); ?></h3>
                <p>Average Protein Length (AA)</p>
            </div>
        </div>
    </div>
    
    <!-- Distribution Charts -->
    <div class="charts-grid">
        <!-- TF Family Distribution -->
        <div class="chart-card">
            <h2><i class="fas fa-layer-group"></i> TF Family Distribution</h2>
            <div class="chart-container">
                <?php 
                $max_family = max(array_column($family_stats, 'tf_count'));
                foreach ($family_stats as $family): 
                    $percentage = ($family['tf_count'] / $max_family) * 100;
                ?>
                <div class="chart-bar-item">
                    <div class="chart-label">
                        <?php echo getFamilyBadge($family['family_code']); ?>
                        <span class="chart-name"><?php echo h($family['family_name']); ?></span>
                    </div>
                    <div class="chart-bar-container">
                        <div class="chart-bar" style="width: <?php echo $percentage; ?>%"></div>
                        <span class="chart-value"><?php echo formatNumber($family['tf_count']); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Species Distribution -->
        <div class="chart-card">
            <h2><i class="fas fa-seedling"></i> Species Distribution</h2>
            <div class="chart-container">
                <?php 
                $max_species = max(array_column($species_stats, 'tf_count'));
                foreach ($species_stats as $sp): 
                    $percentage = ($sp['tf_count'] / $max_species) * 100;
                ?>
                <div class="chart-bar-item">
                    <div class="chart-label">
                        <i class="fas fa-seedling text-success"></i>
                        <span class="chart-name">
                            <em><?php echo h($sp['scientific_name']); ?></em>
                            <?php if ($sp['common_name']): ?>
                            <small>(<?php echo h($sp['common_name']); ?>)</small>
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="chart-bar-container">
                        <div class="chart-bar chart-bar-success" style="width: <?php echo $percentage; ?>%"></div>
                        <span class="chart-value"><?php echo formatNumber($sp['tf_count']); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Additional Charts -->
    <div class="charts-grid">
        <!-- Chromosome Distribution -->
        <?php if (count($chromosome_stats) > 0): ?>
        <div class="chart-card">
            <h2><i class="fas fa-map-marked-alt"></i> Chromosome Distribution</h2>
            <div class="chart-container">
                <?php 
                $max_chr = max(array_column($chromosome_stats, 'tf_count'));
                foreach ($chromosome_stats as $chr): 
                    $percentage = ($chr['tf_count'] / $max_chr) * 100;
                ?>
                <div class="chart-bar-item">
                    <div class="chart-label">
                        <span class="chart-name">Chr <?php echo h($chr['chromosome']); ?></span>
                    </div>
                    <div class="chart-bar-container">
                        <div class="chart-bar chart-bar-info" style="width: <?php echo $percentage; %>%"></div>
                        <span class="chart-value"><?php echo formatNumber($chr['tf_count']); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Protein Size Distribution -->
        <div class="chart-card">
            <h2><i class="fas fa-ruler-horizontal"></i> Protein Size Distribution</h2>
            <div class="chart-container">
                <?php 
                $max_size = max(array_column($size_stats, 'count'));
                foreach ($size_stats as $size): 
                    $percentage = ($size['count'] / $max_size) * 100;
                ?>
                <div class="chart-bar-item">
                    <div class="chart-label">
                        <span class="chart-name"><?php echo h($size['size_range']); ?></span>
                    </div>
                    <div class="chart-bar-container">
                        <div class="chart-bar chart-bar-warning" style="width: <?php echo $percentage; ?>%"></div>
                        <span class="chart-value"><?php echo formatNumber($size['count']); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Summary Table -->
    <div class="summary-section">
        <h2><i class="fas fa-table"></i> Detailed Statistics</h2>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($family_stats as $family): ?>
                    <tr>
                        <td><?php echo getFamilyBadge($family['family_code']); ?> <?php echo h($family['family_name']); ?></td>
                        <td><?php echo formatNumber($family['tf_count']); ?></td>
                        <td>
                            <div class="progress-small">
                                <div class="progress-bar" style="width: <?php echo ($family['tf_count'] / $global_stats['total_tf']) * 100; ?>%"></div>
                            </div>
                            <?php echo formatNumber(($family['tf_count'] / $global_stats['total_tf']) * 100, 1); ?>%
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
