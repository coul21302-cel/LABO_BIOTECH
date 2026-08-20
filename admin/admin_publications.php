<?php
require_once '../db_config.php';
requireLogin();

$success = '';
$error = '';

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM publications WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Publication supprimée avec succès.';
    } catch (PDOException $e) {
        $error = 'Erreur lors de la suppression.';
    }
}

// Ajout/Modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $titre = trim($_POST['titre']);
    $auteurs = trim($_POST['auteurs']);
    $annee = $_POST['annee'];
    $revue = trim($_POST['revue']);
    $volume = trim($_POST['volume']);
    $pages = trim($_POST['pages']);
    $doi = trim($_POST['doi']);
    $lien_pdf = trim($_POST['lien_pdf']);
    $resume = trim($_POST['resume']);
    $type_publication = $_POST['type_publication'];
    $membre_id = $_POST['membre_id'] ?: null;
    $projet_id = $_POST['projet_id'] ?: null;
    
    try {
        if ($id) {
            // Mise à jour
            $stmt = $pdo->prepare("UPDATE publications SET titre=?, auteurs=?, annee=?, revue=?, volume=?, pages=?, doi=?, lien_pdf=?, resume=?, type_publication=?, membre_id=?, projet_id=? WHERE id=?");
            $stmt->execute([$titre, $auteurs, $annee, $revue, $volume, $pages, $doi, $lien_pdf, $resume, $type_publication, $membre_id, $projet_id, $id]);
            $success = 'Publication modifiée avec succès.';
        } else {
            // Ajout
            $stmt = $pdo->prepare("INSERT INTO publications (titre, auteurs, annee, revue, volume, pages, doi, lien_pdf, resume, type_publication, membre_id, projet_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titre, $auteurs, $annee, $revue, $volume, $pages, $doi, $lien_pdf, $resume, $type_publication, $membre_id, $projet_id]);
            $success = 'Publication ajoutée avec succès.';
        }
    } catch (PDOException $e) {
        $error = 'Erreur: ' . $e->getMessage();
    }
}

// Récupérer toutes les publications
$publications = $pdo->query("SELECT p.*, m.nom, m.prenom FROM publications p LEFT JOIN membres m ON p.membre_id = m.id ORDER BY p.annee DESC")->fetchAll();

// Publication à éditer
$edit_pub = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM publications WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_pub = $stmt->fetch();
}

// Listes pour les sélecteurs
$membres = $pdo->query("SELECT id, nom, prenom FROM membres WHERE actif = 1 ORDER BY nom")->fetchAll();
$projets = $pdo->query("SELECT id, titre FROM projets ORDER BY titre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Publications</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-dashboard">
        <aside class="admin-sidebar">
            <h2><img src="<?php echo SITE_URL; ?>/uploads/images/logo2.png?v=<?php echo time(); ?>" alt="Logo" class="admin-logo"> LCBV Admin</h2>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Tableau de bord</a></li>
                    <li><a href="admin_equipe.php"><i class="fas fa-users"></i> Équipe</a></li>
                    <li><a href="admin_alumni.php"><i class="fas fa-user-graduate"></i> Alumni</a></li>
                    <li><a href="admin_projets.php"><i class="fas fa-project-diagram"></i> Projets</a></li>
                    <li><a href="admin_publications.php" class="active"><i class="fas fa-book"></i> Publications</a></li>
                    <li><a href="admin_actualites.php"><i class="fas fa-newspaper"></i> Actualités</a></li>
                    <li><a href="admin_galerie.php"><i class="fas fa-images"></i> Galerie</a></li>
                    <li><a href="admin_partenaires.php"><i class="fas fa-handshake"></i> Partenaires</a></li>
                    <li><a href="admin_messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <h1><i class="fas fa-book"></i> Gestion des Publications</h1>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo h($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <div class="admin-form-section">
                <h2><?php echo $edit_pub ? 'Modifier' : 'Ajouter'; ?> une publication</h2>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $edit_pub['id'] ?? ''; ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Titre *</label>
                        <textarea name="titre" class="form-control" rows="2" required><?php echo h($edit_pub['titre'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Auteurs *</label>
                        <input type="text" name="auteurs" class="form-control" 
                               value="<?php echo h($edit_pub['auteurs'] ?? ''); ?>" 
                               placeholder="Nom1 A., Nom2 B., Nom3 C." required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Année *</label>
                            <input type="number" name="annee" class="form-control" min="1900" max="2100"
                                   value="<?php echo h($edit_pub['annee'] ?? date('Y')); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Type *</label>
                            <select name="type_publication" class="form-control" required>
                                <option value="">Sélectionner...</option>
                                <option value="Article" <?php echo ($edit_pub['type_publication'] ?? '') == 'Article' ? 'selected' : ''; ?>>Article</option>
                                <option value="Conférence" <?php echo ($edit_pub['type_publication'] ?? '') == 'Conférence' ? 'selected' : ''; ?>>Conférence</option>
                                <option value="Chapitre" <?php echo ($edit_pub['type_publication'] ?? '') == 'Chapitre' ? 'selected' : ''; ?>>Chapitre</option>
                                <option value="Thèse" <?php echo ($edit_pub['type_publication'] ?? '') == 'Thèse' ? 'selected' : ''; ?>>Thèse</option>
                                <option value="Autre" <?php echo ($edit_pub['type_publication'] ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Revue/Journal</label>
                            <input type="text" name="revue" class="form-control" 
                                   value="<?php echo h($edit_pub['revue'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Volume</label>
                            <input type="text" name="volume" class="form-control" 
                                   value="<?php echo h($edit_pub['volume'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Pages</label>
                            <input type="text" name="pages" class="form-control" 
                                   value="<?php echo h($edit_pub['pages'] ?? ''); ?>" placeholder="ex: 123-145">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">DOI</label>
                            <input type="text" name="doi" class="form-control" 
                                   value="<?php echo h($edit_pub['doi'] ?? ''); ?>" placeholder="10.xxxx/xxxxx">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Lien PDF</label>
                        <input type="url" name="lien_pdf" class="form-control" 
                               value="<?php echo h($edit_pub['lien_pdf'] ?? ''); ?>" placeholder="https://...">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Résumé</label>
                        <textarea name="resume" class="form-control" rows="4"><?php echo h($edit_pub['resume'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Auteur principal (membre du labo)</label>
                            <select name="membre_id" class="form-control">
                                <option value="">Aucun</option>
                                <?php foreach ($membres as $membre): ?>
                                <option value="<?php echo $membre['id']; ?>" <?php echo ($edit_pub['membre_id'] ?? '') == $membre['id'] ? 'selected' : ''; ?>>
                                    <?php echo h($membre['prenom'] . ' ' . $membre['nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Projet associé</label>
                            <select name="projet_id" class="form-control">
                                <option value="">Aucun</option>
                                <?php foreach ($projets as $projet): ?>
                                <option value="<?php echo $projet['id']; ?>" <?php echo ($edit_pub['projet_id'] ?? '') == $projet['id'] ? 'selected' : ''; ?>>
                                    <?php echo h($projet['titre']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <?php if ($edit_pub): ?>
                        <a href="admin_publications.php" class="btn btn-outline-primary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="admin-table">
                <h3>Liste des publications (<?php echo count($publications); ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteurs</th>
                            <th>Année</th>
                            <th>Type</th>
                            <th>Revue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($publications as $pub): ?>
                        <tr>
                            <td><strong><?php echo h(truncate($pub['titre'], 60)); ?></strong></td>
                            <td><?php echo h(truncate($pub['auteurs'], 40)); ?></td>
                            <td><?php echo h($pub['annee']); ?></td>
                            <td><span class="badge badge-info"><?php echo h($pub['type_publication']); ?></span></td>
                            <td><?php echo h($pub['revue']); ?></td>
                            <td class="action-buttons">
                                <a href="?edit=<?php echo $pub['id']; ?>" class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $pub['id']; ?>" class="btn btn-sm btn-delete" data-confirm-delete>
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    
    <style>
    .admin-form-section {background: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem;}
    .admin-form-section h2 {margin-bottom: 1.5rem; color: var(--primary-color);}
    .form-row {display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;}
    @media (max-width: 768px) {.form-row {grid-template-columns: 1fr;}}
    .form-actions {display: flex; gap: 1rem; margin-top: 1.5rem;}
    </style>
    
    <script src="<?php echo SITE_URL; ?>/js/script.js"></script>
</body>
</html>
