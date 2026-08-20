<?php
/**
 * Script pour récupérer les publications Google Scholar
 * Utilise l'API Serpapi ou scraping simple
 */

require_once '../db_config.php';

/**
 * Fonction pour récupérer les publications depuis Google Scholar
 * @param string $author_name Nom de l'auteur
 * @param string $scholar_id ID Google Scholar (optionnel)
 * @return array Publications trouvées
 */
function getGoogleScholarPublications($author_name, $scholar_id = null) {
    $publications = [];
    
    if ($scholar_id) {
        // Si on a l'ID Google Scholar, on peut utiliser l'URL directe
        $url = "https://scholar.google.com/citations?user=" . $scholar_id . "&hl=fr";
    } else {
        // Sinon, on fait une recherche par nom
        $search_query = urlencode($author_name);
        $url = "https://scholar.google.com/scholar?q=" . $search_query . "&hl=fr";
    }
    
    // Configuration pour simuler un navigateur
    $options = [
        'http' => [
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
        ]
    ];
    $context = stream_context_create($options);
    
    // Récupérer le contenu
    $html = @file_get_contents($url, false, $context);
    
    if ($html) {
        // Parser le HTML pour extraire les publications
        // Note: Google Scholar peut bloquer le scraping, il est recommandé d'utiliser une API
        $publications = parseScholarHTML($html);
    }
    
    return $publications;
}

/**
 * Parser le HTML de Google Scholar
 */
function parseScholarHTML($html) {
    $publications = [];
    
    // Utiliser DOMDocument pour parser
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);
    
    // Chercher les éléments de publication
    $items = $xpath->query("//div[@class='gs_ri']");
    
    foreach ($items as $item) {
        $pub = [];
        
        // Titre
        $title = $xpath->query(".//h3[@class='gs_rt']/a", $item);
        if ($title->length > 0) {
            $pub['titre'] = trim($title->item(0)->textContent);
        }
        
        // Auteurs et info
        $info = $xpath->query(".//div[@class='gs_a']", $item);
        if ($info->length > 0) {
            $pub['auteurs'] = trim($info->item(0)->textContent);
        }
        
        // Résumé
        $abstract = $xpath->query(".//div[@class='gs_rs']", $item);
        if ($abstract->length > 0) {
            $pub['resume'] = trim($abstract->item(0)->textContent);
        }
        
        $publications[] = $pub;
    }
    
    return $publications;
}

/**
 * Importer les publications dans la base de données
 */
function importPublications($membre_id, $publications) {
    global $pdo;
    
    $imported = 0;
    
    foreach ($publications as $pub) {
        // Vérifier si la publication existe déjà
        $stmt = $pdo->prepare("SELECT id FROM publications WHERE titre = ? AND membre_id = ?");
        $stmt->execute([$pub['titre'], $membre_id]);
        
        if (!$stmt->fetch()) {
            // Extraire l'année depuis les auteurs (format: "Auteur - Revue, Année")
            $annee = extractYear($pub['auteurs']);
            
            // Insérer la nouvelle publication
            $stmt = $pdo->prepare("
                INSERT INTO publications (titre, auteurs, annee, resume, membre_id, date_ajout)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $pub['titre'],
                $pub['auteurs'],
                $annee ?: date('Y'),
                $pub['resume'] ?? '',
                $membre_id
            ]);
            
            $imported++;
        }
    }
    
    return $imported;
}

/**
 * Extraire l'année depuis le texte
 */
function extractYear($text) {
    if (preg_match('/\b(19|20)\d{2}\b/', $text, $matches)) {
        return $matches[0];
    }
    return null;
}

// Si appelé directement
if (isset($_GET['membre_id'])) {
    $membre_id = (int)$_GET['membre_id'];
    
    // Récupérer les infos du membre
    $stmt = $pdo->prepare("SELECT * FROM membres WHERE id = ?");
    $stmt->execute([$membre_id]);
    $membre = $stmt->fetch();
    
    if ($membre) {
        $author_name = $membre['prenom'] . ' ' . $membre['nom'];
        
        // Récupérer les publications
        $publications = getGoogleScholarPublications($author_name);
        
        if (!empty($publications)) {
            $imported = importPublications($membre_id, $publications);
            echo json_encode([
                'success' => true,
                'message' => "$imported publications importées pour " . $author_name,
                'count' => $imported
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => "Aucune publication trouvée pour " . $author_name
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Membre non trouvé"
        ]);
    }
}
?>
