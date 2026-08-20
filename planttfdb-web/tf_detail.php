<?php
/**
 * PlantTFDB - TF Detail Page
 * @package PlantTFDB
 */

require_once 'config/database.php';

$tf_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tf_id <= 0) {
    header('Location: search.php');
    exit;
}

// Récupérer les informations du TF
$stmt = $pdo->prepare("
    SELECT 
        tf.*,
        s.scientific_name,
        s.common_name,
        s.taxonomy_id,
        f.family_code,
        f.family_name,
        f.description as family_description
    FROM transcription_factors tf
    JOIN species s ON tf.species_id = s.species_id
    JOIN tf_families f ON tf.family_id = f.family_id
    WHERE tf.tf_id = ?
");
$stmt->execute([$tf_id]);
$tf = $stmt->fetch();

if (!$tf) {
    header('Location: search.php');
    exit;
}

$page_title = $tf['gene_name'] ?: $tf['gene_id'];

// Récupérer les domaines protéiques
$domains = $pdo->prepare("SELECT * FROM protein_domains WHERE tf_id = ? ORDER BY start_position");
$domains->execute([$tf_id]);
$domains = $domains->fetchAll();

// Récupérer les annotations GO
$go_terms = $pdo->prepare("SELECT * FROM gene_ontology WHERE tf_id = ? ORDER BY go_category, go_term");
$go_terms->execute([$tf_id]);
$go_terms = $go_terms->fetchAll();

// Regrouper GO par catégorie
$go_by_category = [
    'BP' => [],
    'MF' => [],
    'CC' => []
];
foreach ($go_terms as $go) {
    $go_by_category[$go['go_category']][] = $go;
}

// Récupérer les références croisées
$xrefs = $pdo->prepare("SELECT * FROM cross_references WHERE tf_id = ? ORDER BY database_name");
$xrefs->execute([$tf_id]);
$xrefs = $xrefs->fetchAll();

// Récupérer les données d'expression
$expression = $pdo->prepare("SELECT * FROM expression_data WHERE tf_id = ? ORDER BY expression_level DESC");
$expression->execute([$tf_id]);
$expression = $expression->fetchAll();

// Récupérer les publications
$publications = $pdo->prepare("SELECT * FROM publications WHERE tf_id = ? ORDER BY year DESC");
$publications->execute([$tf_id]);
$publications = $publications->fetchAll();

// Récupérer les sites de liaison
$binding_sites = $pdo->prepare("SELECT * FROM binding_sites WHERE tf_id = ?");
$binding_sites->execute([$tf_id]);
$binding_sites = $binding_sites->fetchAll();

include 'includes/header.php';
?>

<div class="container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <a href="index.php">Home</a>
        <span>/</span>
        <a href="search.php">Search</a>
        <span>/</span>
        <span><?php echo h($page_title); ?></span>
    </nav>
    
    <!-- TF Header -->
    <div class="tf-header">
        <div class="tf-title-section">
            <h1>
                <i class="fas fa-dna"></i>
                <?php echo h($tf['gene_name'] ?: 'Unnamed TF'); ?>
            </h1>
            <?php echo getFamilyBadge($tf['family_code']); ?>
        </div>
        
        <div class="tf-actions">
            <a href="download.php?id=<?php echo $tf_id; ?>&type=protein" class="btn btn-primary">
                <i class="fas fa-download"></i> Download Sequence
            </a>
            <button onclick="window.print()" class="btn btn-outline">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
    
    <!-- Quick Info Bar -->
    <div class="quick-info">
        <div class="info-item">
            <i class="fas fa-tag"></i>
            <div>
                <strong>Gene ID</strong>
                <span><code><?php echo h($tf['gene_id']); ?></code></span>
            </div>
        </div>
        <div class="info-item">
            <i class="fas fa-seedling"></i>
            <div>
                <strong>Species</strong>
                <span><em><?php echo h($tf['scientific_name']); ?></em></span>
            </div>
        </div>
        <div class="info-item">
            <i class="fas fa-layer-group"></i>
            <div>
                <strong>Family</strong>
                <span><?php echo h($tf['family_name']); ?></span>
            </div>
        </div>
        <?php if ($tf['chromosome']): ?>
        <div class="info-item">
            <i class="fas fa-map-marker-alt"></i>
            <div>
                <strong>Chromosome</strong>
                <span><?php echo h($tf['chromosome']); ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Main Content Grid -->
    <div class="detail-grid">
        <!-- Left Column -->
        <div class="detail-main">
            <!-- Description -->
            <?php if ($tf['description']): ?>
            <section class="detail-section">
                <h2><i class="fas fa-info-circle"></i> Description</h2>
                <p><?php echo nl2br(h($tf['description'])); ?></p>
            </section>
            <?php endif; ?>
            
            <?php if ($tf['function_summary']): ?>
            <section class="detail-section">
                <h2><i class="fas fa-clipboard-list"></i> Function Summary</h2>
                <p><?php echo nl2br(h($tf['function_summary'])); ?></p>
            </section>
            <?php endif; ?>
            
            <!-- Genomic Information -->
            <section class="detail-section">
                <h2><i class="fas fa-map-marked-alt"></i> Genomic Information</h2>
                <table class="info-table">
                    <tr>
                        <th>Chromosome</th>
                        <td><?php echo h($tf['chromosome'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Start Position</th>
                        <td><?php echo $tf['start_position'] ? formatNumber($tf['start_position']) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>End Position</th>
                        <td><?php echo $tf['end_position'] ? formatNumber($tf['end_position']) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Strand</th>
                        <td><?php echo h($tf['strand'] ?: 'N/A'); ?></td>
                    </tr>
                    <?php if ($tf['start_position'] && $tf['end_position']): ?>
                    <tr>
                        <th>Length (bp)</th>
                        <td><?php echo formatNumber($tf['end_position'] - $tf['start_position'] + 1); ?> bp</td>
                    </tr>
                    <?php endif; ?>
                </table>
            </section>
            
            <!-- Protein Information -->
            <section class="detail-section">
                <h2><i class="fas fa-atom"></i> Protein Information</h2>
                <table class="info-table">
                    <tr>
                        <th>Protein Length</th>
                        <td><?php echo $tf['protein_length'] ? formatNumber($tf['protein_length']) . ' AA' : 'N/A'; ?></td>
                    </tr>
                    <?php if ($tf['molecular_weight']): ?>
                    <tr>
                        <th>Molecular Weight</th>
                        <td><?php echo formatNumber($tf['molecular_weight'], 2); ?> kDa</td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($tf['isoelectric_point']): ?>
                    <tr>
                        <th>Isoelectric Point</th>
                        <td><?php echo formatNumber($tf['isoelectric_point'], 2); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </section>
            
            <!-- Protein Domains -->
            <?php if (count($domains) > 0): ?>
            <section class="detail-section">
                <h2><i class="fas fa-puzzle-piece"></i> Protein Domains</h2>
                <div class="domains-list">
                    <?php foreach ($domains as $domain): ?>
                    <div class="domain-card">
                        <h4><?php echo h($domain['domain_name']); ?></h4>
                        <?php if ($domain['domain_type']): ?>
                        <p class="domain-type"><strong>Type:</strong> <?php echo h($domain['domain_type']); ?></p>
                        <?php endif; ?>
                        <p class="domain-position">
                            <i class="fas fa-ruler"></i> Position: <?php echo $domain['start_position']; ?>-<?php echo $domain['end_position']; ?>
                            (<?php echo ($domain['end_position'] - $domain['start_position'] + 1); ?> AA)
                        </p>
                        <?php if ($domain['pfam_id']): ?>
                        <p class="domain-id">
                            <strong>Pfam:</strong> 
                            <a href="https://pfam.xfam.org/family/<?php echo h($domain['pfam_id']); ?>" target="_blank">
                                <?php echo h($domain['pfam_id']); ?> <i class="fas fa-external-link-alt"></i>
                            </a>
                        </p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
            
            <!-- Gene Ontology -->
            <?php if (count($go_terms) > 0): ?>
            <section class="detail-section">
                <h2><i class="fas fa-tags"></i> Gene Ontology Annotations</h2>
                
                <?php foreach (['BP' => 'Biological Process', 'MF' => 'Molecular Function', 'CC' => 'Cellular Component'] as $cat => $cat_name): ?>
                    <?php if (count($go_by_category[$cat]) > 0): ?>
                    <div class="go-category">
                        <h4><?php echo getGOIcon($cat); ?> <?php echo $cat_name; ?></h4>
                        <ul class="go-list">
                            <?php foreach ($go_by_category[$cat] as $go): ?>
                            <li>
                                <a href="https://amigo.geneontology.org/amigo/term/<?php echo h($go['go_term']); ?>" target="_blank">
                                    <code><?php echo h($go['go_term']); ?></code>
                                </a>
                                <?php echo h($go['go_name']); ?>
                                <?php if ($go['evidence_code']): ?>
                                <span class="evidence-badge"><?php echo h($go['evidence_code']); ?></span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>
            <?php endif; ?>
            
            <!-- Expression Data -->
            <?php if (count($expression) > 0): ?>
            <section class="detail-section">
                <h2><i class="fas fa-chart-line"></i> Expression Data</h2>
                <div class="expression-chart">
                    <?php 
                    $max_expr = max(array_column($expression, 'expression_level'));
                    foreach ($expression as $expr): 
                    $percentage = ($expr['expression_level'] / $max_expr) * 100;
                    ?>
                    <div class="expression-item">
                        <div class="expr-label">
                            <strong><?php echo h($expr['tissue']); ?></strong>
                            <?php if ($expr['development_stage']): ?>
                            <span class="stage">(<?php echo h($expr['development_stage']); ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="expr-bar-container">
                            <div class="expr-bar" style="width: <?php echo $percentage; ?>%"></div>
                            <span class="expr-value"><?php echo formatNumber($expr['expression_level'], 2); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($expression[0]['data_source']): ?>
                <p class="data-source"><small><strong>Source:</strong> <?php echo h($expression[0]['data_source']); ?></small></p>
                <?php endif; ?>
            </section>
            <?php endif; ?>
            
            <!-- Binding Sites -->
            <?php if (count($binding_sites) > 0): ?>
            <section class="detail-section">
                <h2><i class="fas fa-dna"></i> DNA Binding Sites</h2>
                <?php foreach ($binding_sites as $site): ?>
                <div class="binding-site-card">
                    <h4><?php echo h($site['motif_name']); ?></h4>
                    <p class="motif-sequence"><code><?php echo h($site['motif_sequence']); ?></code></p>
                    <?php if ($site['transfac_id'] || $site['jaspar_id']): ?>
                    <p class="motif-refs">
                        <?php if ($site['transfac_id']): ?>
                        <strong>TRANSFAC:</strong> <?php echo h($site['transfac_id']); ?>
                        <?php endif; ?>
                        <?php if ($site['jaspar_id']): ?>
                        <strong>JASPAR:</strong> <?php echo h($site['jaspar_id']); ?>
                        <?php endif; ?>
                    </p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </section>
            <?php endif; ?>
            
            <!-- Sequences -->
            <?php if ($tf['protein_sequence'] || $tf['cds_sequence']): ?>
            <section class="detail-section">
                <h2><i class="fas fa-file-code"></i> Sequences</h2>
                
                <?php if ($tf['protein_sequence']): ?>
                <div class="sequence-container">
                    <div class="sequence-header">
                        <h4>Protein Sequence (<?php echo strlen($tf['protein_sequence']); ?> AA)</h4>
                        <button class="btn btn-sm btn-outline" onclick="copySequence('protein-seq')">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                    <pre class="sequence" id="protein-seq"><?php echo chunk_split($tf['protein_sequence'], 60, "\n"); ?></pre>
                </div>
                <?php endif; ?>
                
                <?php if ($tf['cds_sequence']): ?>
                <div class="sequence-container">
                    <div class="sequence-header">
                        <h4>CDS Sequence (<?php echo strlen($tf['cds_sequence']); ?> bp)</h4>
                        <button class="btn btn-sm btn-outline" onclick="copySequence('cds-seq')">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                    <pre class="sequence" id="cds-seq"><?php echo chunk_split($tf['cds_sequence'], 60, "\n"); ?></pre>
                </div>
                <?php endif; ?>
            </section>
            <?php endif; ?>
        </div>
        
        <!-- Right Sidebar -->
        <div class="detail-sidebar">
            <!-- Cross References -->
            <?php if (count($xrefs) > 0): ?>
            <div class="sidebar-section">
                <h3><i class="fas fa-link"></i> External Links</h3>
                <ul class="xref-list">
                    <?php foreach ($xrefs as $xref): ?>
                    <li>
                        <strong><?php echo h($xref['database_name']); ?>:</strong>
                        <?php if ($xref['url']): ?>
                        <a href="<?php echo h($xref['url']); ?>" target="_blank">
                            <?php echo h($xref['accession']); ?> <i class="fas fa-external-link-alt"></i>
                        </a>
                        <?php else: ?>
                        <?php echo h($xref['accession']); ?>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Publications -->
            <?php if (count($publications) > 0): ?>
            <div class="sidebar-section">
                <h3><i class="fas fa-book"></i> Publications</h3>
                <ul class="pub-list">
                    <?php foreach ($publications as $pub): ?>
                    <li>
                        <?php if ($pub['title']): ?>
                        <strong><?php echo h($pub['title']); ?></strong><br>
                        <?php endif; ?>
                        <?php if ($pub['authors']): ?>
                        <small><?php echo h(truncate($pub['authors'], 50)); ?></small><br>
                        <?php endif; ?>
                        <?php if ($pub['journal'] && $pub['year']): ?>
                        <small><em><?php echo h($pub['journal']); ?></em>, <?php echo $pub['year']; ?></small><br>
                        <?php endif; ?>
                        <?php if ($pub['pubmed_id']): ?>
                        <a href="https://pubmed.ncbi.nlm.nih.gov/<?php echo h($pub['pubmed_id']); ?>/" target="_blank" class="pubmed-link">
                            <i class="fas fa-external-link-alt"></i> PubMed
                        </a>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <!-- Download Options -->
            <div class="sidebar-section">
                <h3><i class="fas fa-download"></i> Download</h3>
                <div class="download-options">
                    <?php if ($tf['protein_sequence']): ?>
                    <a href="download.php?id=<?php echo $tf_id; ?>&type=protein" class="download-btn">
                        <i class="fas fa-file-code"></i> Protein (FASTA)
                    </a>
                    <?php endif; ?>
                    <?php if ($tf['cds_sequence']): ?>
                    <a href="download.php?id=<?php echo $tf_id; ?>&type=cds" class="download-btn">
                        <i class="fas fa-file-code"></i> CDS (FASTA)
                    </a>
                    <?php endif; ?>
                    <a href="download.php?id=<?php echo $tf_id; ?>&type=json" class="download-btn">
                        <i class="fas fa-file-download"></i> JSON Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copySequence(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;
    navigator.clipboard.writeText(text).then(() => {
        alert('Sequence copied to clipboard!');
    });
}
</script>

<?php include 'includes/footer.php'; ?>
