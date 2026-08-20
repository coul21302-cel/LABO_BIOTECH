<?php
/**
 * PlantTFDB - Admin Functions
 * @package PlantTFDB Admin
 */

/**
 * Générer un nom de fichier unique pour les uploads
 * 
 * @param string $original_filename
 * @return string
 */
function generateUniqueFilename($original_filename) {
    $extension = pathinfo($original_filename, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Parser un fichier FASTA
 * 
 * @param string $content
 * @return array
 */
function parseFasta($content) {
    $sequences = [];
    $lines = explode("\n", $content);
    $current_header = '';
    $current_sequence = '';
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        if ($line[0] === '>') {
            if ($current_header !== '') {
                $sequences[] = [
                    'header' => $current_header,
                    'sequence' => $current_sequence
                ];
            }
            $current_header = substr($line, 1);
            $current_sequence = '';
        } else {
            $current_sequence .= $line;
        }
    }
    
    // Ajouter la dernière séquence
    if ($current_header !== '') {
        $sequences[] = [
            'header' => $current_header,
            'sequence' => $current_sequence
        ];
    }
    
    return $sequences;
}

/**
 * Parser un fichier CSV
 * 
 * @param string $filepath
 * @param string $delimiter
 * @return array
 */
function parseCSV($filepath, $delimiter = ',') {
    $data = [];
    $headers = [];
    
    if (($handle = fopen($filepath, 'r')) !== FALSE) {
        // Première ligne = headers
        $headers = fgetcsv($handle, 0, $delimiter);
        
        while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }
        fclose($handle);
    }
    
    return $data;
}

/**
 * Valider les données d'un TF
 * 
 * @param array $data
 * @return array Tableau d'erreurs (vide si valide)
 */
function validateTFData($data) {
    $errors = [];
    
    if (empty($data['gene_id'])) {
        $errors[] = 'Gene ID is required';
    }
    
    if (empty($data['species_id'])) {
        $errors[] = 'Species is required';
    }
    
    if (empty($data['family_id'])) {
        $errors[] = 'TF Family is required';
    }
    
    if (!empty($data['protein_sequence'])) {
        // Vérifier que c'est bien une séquence protéique (lettres A-Z seulement)
        if (!preg_match('/^[ACDEFGHIKLMNPQRSTVWY]+$/i', str_replace(["\n", "\r", " "], '', $data['protein_sequence']))) {
            $errors[] = 'Invalid protein sequence format';
        }
    }
    
    if (!empty($data['cds_sequence'])) {
        // Vérifier que c'est bien de l'ADN (ATGC seulement)
        if (!preg_match('/^[ATGC]+$/i', str_replace(["\n", "\r", " "], '', $data['cds_sequence']))) {
            $errors[] = 'Invalid CDS sequence format (must contain only A, T, G, C)';
        }
    }
    
    return $errors;
}

/**
 * Logger une action admin
 * 
 * @param PDO $pdo
 * @param string $action
 * @param string $details
 */
function logAdminAction($pdo, $action, $details = '') {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs (username, action, details, ip_address, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $_SESSION['admin_username'] ?? 'unknown',
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? ''
        ]);
    } catch (Exception $e) {
        // Si la table admin_logs n'existe pas, on ignore silencieusement
        error_log("Admin log failed: " . $e->getMessage());
    }
}

/**
 * Obtenir les statistiques du dashboard
 * 
 * @param PDO $pdo
 * @return array
 */
function getDashboardStats($pdo) {
    $stats = [];
    
    // Total TF
    $stats['total_tf'] = $pdo->query("SELECT COUNT(*) FROM transcription_factors")->fetchColumn();
    
    // Total Species
    $stats['total_species'] = $pdo->query("SELECT COUNT(*) FROM species")->fetchColumn();
    
    // Total Families
    $stats['total_families'] = $pdo->query("SELECT COUNT(*) FROM tf_families")->fetchColumn();
    
    // TF avec séquences
    $stats['tf_with_protein'] = $pdo->query("SELECT COUNT(*) FROM transcription_factors WHERE protein_sequence IS NOT NULL")->fetchColumn();
    $stats['tf_with_cds'] = $pdo->query("SELECT COUNT(*) FROM transcription_factors WHERE cds_sequence IS NOT NULL")->fetchColumn();
    
    // TF ajoutés cette semaine
    $stats['tf_this_week'] = $pdo->query("
        SELECT COUNT(*) FROM transcription_factors 
        WHERE date_added >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ")->fetchColumn();
    
    // Derniers TF ajoutés
    $stats['recent_tf'] = $pdo->query("
        SELECT tf.gene_name, tf.gene_id, s.scientific_name, tf.date_added
        FROM transcription_factors tf
        JOIN species s ON tf.species_id = s.species_id
        ORDER BY tf.date_added DESC
        LIMIT 5
    ")->fetchAll();
    
    return $stats;
}

/**
 * Calculer les propriétés d'une protéine
 * 
 * @param string $sequence
 * @return array
 */
function calculateProteinProperties($sequence) {
    $clean_seq = str_replace(["\n", "\r", " ", "\t"], '', strtoupper($sequence));
    
    $properties = [
        'length' => strlen($clean_seq),
        'molecular_weight' => null,
        'isoelectric_point' => null
    ];
    
    // Poids moléculaire approximatif (poids moyen des AA = 110 Da)
    $properties['molecular_weight'] = round((strlen($clean_seq) * 110) / 1000, 2); // en kDa
    
    // PI approximatif (nécessiterait un calcul plus complexe)
    // Pour l'instant, on laisse null
    
    return $properties;
}

/**
 * Générer un template CSV pour l'import
 * 
 * @return string
 */
function generateCSVTemplate() {
    $headers = [
        'gene_id',
        'gene_name',
        'species_scientific_name',
        'family_code',
        'chromosome',
        'start_position',
        'end_position',
        'strand',
        'protein_sequence',
        'cds_sequence',
        'description'
    ];
    
    return implode(',', $headers) . "\n";
}
?>
