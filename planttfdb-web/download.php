<?php
/**
 * PlantTFDB - Download Sequences
 * @package PlantTFDB
 */

require_once 'config/database.php';

// Gestion du téléchargement direct
if (isset($_GET['id']) && isset($_GET['type'])) {
    $tf_id = intval($_GET['id']);
    $type = $_GET['type'];
    
    $stmt = $pdo->prepare("
        SELECT tf.*, s.scientific_name, f.family_code
        FROM transcription_factors tf
        JOIN species s ON tf.species_id = s.species_id
        JOIN tf_families f ON tf.family_id = f.family_id
        WHERE tf.tf_id = ?
    ");
    $stmt->execute([$tf_id]);
    $tf = $stmt->fetch();
    
    if ($tf) {
        $gene_name = $tf['gene_name'] ?: $tf['gene_id'];
        
        if ($type == 'protein' && $tf['protein_sequence']) {
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="' . $gene_name . '_protein.fasta"');
            echo formatFasta(
                $gene_name . ' | ' . $tf['gene_id'] . ' | ' . $tf['scientific_name'] . ' | ' . $tf['family_code'] . ' | Protein',
                $tf['protein_sequence']
            );
            exit;
        }
        elseif ($type == 'cds' && $tf['cds_sequence']) {
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="' . $gene_name . '_cds.fasta"');
            echo formatFasta(
                $gene_name . ' | ' . $tf['gene_id'] . ' | ' . $tf['scientific_name'] . ' | ' . $tf['family_code'] . ' | CDS',
                $tf['cds_sequence']
            );
            exit;
        }
        elseif ($type == 'json') {
            // Récupérer toutes les données du TF
            $data = [
                'gene_info' => $tf,
                'domains' => $pdo->prepare("SELECT * FROM protein_domains WHERE tf_id = ?")->execute([$tf_id]) ? $pdo->fetchAll() : [],
                'go_terms' => $pdo->prepare("SELECT * FROM gene_ontology WHERE tf_id = ?")->execute([$tf_id]) ? $pdo->fetchAll() : [],
                'xrefs' => $pdo->prepare("SELECT * FROM cross_references WHERE tf_id = ?")->execute([$tf_id]) ? $pdo->fetchAll() : [],
            ];
            
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $gene_name . '_data.json"');
            echo json_encode($data, JSON_PRETTY_PRINT);
            exit;
        }
    }
}

// Gestion du téléchargement en masse
if (isset($_POST['download_bulk'])) {
    $family_id = $_POST['family_id'] ?? '';
    $species_id = $_POST['species_id'] ?? '';
    $seq_type = $_POST['seq_type'] ?? 'protein';
    
    $sql = "SELECT tf.*, s.scientific_name, f.family_code
            FROM transcription_factors tf
            JOIN species s ON tf.species_id = s.species_id
            JOIN tf_families f ON tf.family_id = f.family_id
            WHERE 1=1";
    
    $params = [];
    if ($family_id) {
        $sql .= " AND tf.family_id = ?";
        $params[] = $family_id;
    }
    if ($species_id) {
        $sql .= " AND tf.species_id = ?";
        $params[] = $species_id;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tfs = $stmt->fetchAll();
    
    if (count($tfs) > 0) {
        $filename = 'planttfdb_bulk_' . date('Ymd') . '.fasta';
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        foreach ($tfs as $tf) {
            $gene_name = $tf['gene_name'] ?: $tf['gene_id'];
            $sequence = ($seq_type == 'protein') ? $tf['protein_sequence'] : $tf['cds_sequence'];
            
            if ($sequence) {
                echo formatFasta(
                    $gene_name . ' | ' . $tf['gene_id'] . ' | ' . $tf['scientific_name'] . ' | ' . $tf['family_code'],
                    $sequence
                );
                echo "\n";
            }
        }
        exit;
    }
}

$page_title = 'Download Data';

// Récupérer les filtres pour le téléchargement en masse
$species_list = $pdo->query("SELECT species_id, scientific_name, common_name FROM species ORDER BY scientific_name")->fetchAll();
$family_list = $pdo->query("SELECT family_id, family_code, family_name FROM tf_families ORDER BY family_code")->fetchAll();

include 'includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><i class="fas fa-download"></i> Download Data</h1>
        <p>Export sequences and data in various formats</p>
    </div>
    
    <!-- Download Options -->
    <div class="download-sections">
        <!-- Bulk Download -->
        <div class="download-card">
            <h2><i class="fas fa-database"></i> Bulk Sequence Download</h2>
            <p>Download multiple sequences in FASTA format</p>
            
            <form method="POST" action="download.php" class="download-form">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="species_id"><i class="fas fa-seedling"></i> Filter by Species</label>
                        <select id="species_id" name="species_id" class="form-control">
                            <option value="">All Species</option>
                            <?php foreach ($species_list as $species): ?>
                            <option value="<?php echo $species['species_id']; ?>">
                                <?php echo h($species['scientific_name']); ?>
                                <?php if ($species['common_name']): ?>
                                    (<?php echo h($species['common_name']); ?>)
                                <?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="family_id"><i class="fas fa-layer-group"></i> Filter by Family</label>
                        <select id="family_id" name="family_id" class="form-control">
                            <option value="">All Families</option>
                            <?php foreach ($family_list as $family): ?>
                            <option value="<?php echo $family['family_id']; ?>">
                                <?php echo h($family['family_code']); ?> - <?php echo h($family['family_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="seq_type"><i class="fas fa-file-code"></i> Sequence Type</label>
                        <select id="seq_type" name="seq_type" class="form-control">
                            <option value="protein">Protein Sequences</option>
                            <option value="cds">CDS (Nucleotide) Sequences</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" name="download_bulk" class="btn btn-primary btn-large">
                    <i class="fas fa-download"></i> Download FASTA File
                </button>
            </form>
        </div>
        
        <!-- Database Export -->
        <div class="download-card">
            <h2><i class="fas fa-file-export"></i> Database Export</h2>
            <p>Export complete database or specific tables</p>
            
            <div class="export-options">
                <div class="export-option">
                    <i class="fas fa-table"></i>
                    <h4>CSV Format</h4>
                    <p>Export TF data as CSV spreadsheet</p>
                    <a href="export.php?format=csv" class="btn btn-outline">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                </div>
                
                <div class="export-option">
                    <i class="fas fa-code"></i>
                    <h4>JSON Format</h4>
                    <p>Export TF data as JSON</p>
                    <a href="export.php?format=json" class="btn btn-outline">
                        <i class="fas fa-file-code"></i> Export JSON
                    </a>
                </div>
                
                <div class="export-option">
                    <i class="fas fa-file-alt"></i>
                    <h4>Tab-Delimited</h4>
                    <p>Export as tab-separated values</p>
                    <a href="export.php?format=tsv" class="btn btn-outline">
                        <i class="fas fa-file-alt"></i> Export TSV
                    </a>
                </div>
            </div>
        </div>
        
        <!-- API Access -->
        <div class="download-card">
            <h2><i class="fas fa-code"></i> Programmatic Access</h2>
            <p>Access data via REST API</p>
            
            <div class="api-docs">
                <h4>Example API Endpoints:</h4>
                <pre class="code-block">
# Get all TF
GET <?php echo SITE_URL; ?>/api/transcription_factors

# Get TF by ID
GET <?php echo SITE_URL; ?>/api/transcription_factors/1

# Search TF
GET <?php echo SITE_URL; ?>/api/search?q=MADS

# Get TF by family
GET <?php echo SITE_URL; ?>/api/families/MADS/transcription_factors
                </pre>
                
                <a href="api_documentation.php" class="btn btn-outline">
                    <i class="fas fa-book"></i> View Full API Documentation
                </a>
            </div>
        </div>
        
        <!-- Download Statistics -->
        <div class="download-card">
            <h2><i class="fas fa-chart-bar"></i> Available Data</h2>
            <div class="data-stats">
                <?php
                $stats = $pdo->query("
                    SELECT 
                        COUNT(*) as total_tf,
                        COUNT(CASE WHEN protein_sequence IS NOT NULL THEN 1 END) as with_protein,
                        COUNT(CASE WHEN cds_sequence IS NOT NULL THEN 1 END) as with_cds,
                        COUNT(DISTINCT species_id) as total_species,
                        COUNT(DISTINCT family_id) as total_families
                    FROM transcription_factors
                ")->fetch();
                ?>
                <div class="stat-item">
                    <i class="fas fa-dna"></i>
                    <span class="stat-value"><?php echo formatNumber($stats['total_tf']); ?></span>
                    <span class="stat-label">Total TF</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-atom"></i>
                    <span class="stat-value"><?php echo formatNumber($stats['with_protein']); ?></span>
                    <span class="stat-label">Protein Sequences</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-dna"></i>
                    <span class="stat-value"><?php echo formatNumber($stats['with_cds']); ?></span>
                    <span class="stat-label">CDS Sequences</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-seedling"></i>
                    <span class="stat-value"><?php echo formatNumber($stats['total_species']); ?></span>
                    <span class="stat-label">Species</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Citation -->
    <div class="citation-box">
        <h3><i class="fas fa-quote-left"></i> How to Cite</h3>
        <p>If you use data from PlantTFDB in your research, please cite:</p>
        <div class="citation-text">
            PlantTFDB: Plant Transcription Factor Database. 
            Available at: <?php echo SITE_URL; ?>. 
            Accessed: <?php echo date('F d, Y'); ?>.
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
