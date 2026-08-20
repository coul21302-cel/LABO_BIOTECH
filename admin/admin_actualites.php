<?php
require_once '../db_config.php';
requireLogin();

$success = '';
$error = '';

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM actualites WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Actualité supprimée avec succès.';
    } catch (PDOException $e) {
        $error = 'Erreur lors de la suppression.';
    }
}

// Ajout/Modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $titre = trim($_POST['titre']);
    $contenu = trim($_POST['contenu']);
    $type = $_POST['type'];
    $date_evenement = $_POST['date_evenement'] ?: null;
    $lieu = trim($_POST['lieu']);
    $publie = isset($_POST['publie']) ? 1 : 0;
    
    // Upload image
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = generateFileName($filename);
            $upload_path = UPLOAD_DIR . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image = $new_filename;
            }
        }
    }
    
    try {
        if ($id) {
            // Mise à jour
            $sql = "UPDATE actualites SET titre=?, contenu=?, type=?, date_evenement=?, lieu=?, publie=?";
            $params = [$titre, $contenu, $type, $date_evenement, $lieu, $publie];
            
            if ($image) {
                $sql .= ", image=?";
                $params[] = $image;
            }
            
            $sql .= " WHERE id=?";
            $params[] = $id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $success = 'Actualité modifiée avec succès.';
        } else {
            // Ajout
            $stmt = $pdo->prepare("INSERT INTO actualites (titre, contenu, type, date_evenement, lieu, image, publie) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titre, $contenu, $type, $date_evenement, $lieu, $image, $publie]);
            $success = 'Actualité ajoutée avec succès.';
        }
    } catch (PDOException $e) {
        $error = 'Erreur: ' . $e->getMessage();
    }
}

// Récupérer toutes les actualités
$actualites = $pdo->query("SELECT * FROM actualites ORDER BY date_publication DESC")->fetchAll();

// Actualité à éditer
$edit_actu = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM actualites WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_actu = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Actualités</title>
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
                    <li><a href="admin_publications.php"><i class="fas fa-book"></i> Publications</a></li>
                    <li><a href="admin_actualites.php" class="active"><i class="fas fa-newspaper"></i> Actualités</a></li>
                    <li><a href="admin_galerie.php"><i class="fas fa-images"></i> Galerie</a></li>
                    <li><a href="admin_partenaires.php"><i class="fas fa-handshake"></i> Partenaires</a></li>
                    <li><a href="admin_messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <h1><i class="fas fa-newspaper"></i> Gestion des Actualités</h1>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo h($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <div class="admin-form-section">
                <h2><?php echo $edit_actu ? 'Modifier' : 'Ajouter'; ?> une actualité</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $edit_actu['id'] ?? ''; ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="titre" class="form-control" 
                               value="<?php echo h($edit_actu['titre'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Contenu *</label>
                        <textarea name="contenu" class="form-control" rows="6" required><?php echo h($edit_actu['contenu'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Type *</label>
                            <select name="type" class="form-control" required>
                                <option value="">Sélectionner...</option>
                                <option value="Séminaire" <?php echo ($edit_actu['type'] ?? '') == 'Séminaire' ? 'selected' : ''; ?>>Séminaire</option>
                                <option value="Conférence" <?php echo ($edit_actu['type'] ?? '') == 'Conférence' ? 'selected' : ''; ?>>Conférence</option>
                                <option value="Événement" <?php echo ($edit_actu['type'] ?? '') == 'Événement' ? 'selected' : ''; ?>>Événement</option>
                                <option value="Annonce" <?php echo ($edit_actu['type'] ?? '') == 'Annonce' ? 'selected' : ''; ?>>Annonce</option>
                                <option value="Autre" <?php echo ($edit_actu['type'] ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Date de l'événement</label>
                            <input type="date" name="date_evenement" class="form-control" 
                                   value="<?php echo h($edit_actu['date_evenement'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Lieu</label>
                        <input type="text" name="lieu" class="form-control" 
                               value="<?php echo h($edit_actu['lieu'] ?? ''); ?>" placeholder="Ex: Amphithéâtre de l'UCAD">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <?php if ($edit_actu && $edit_actu['image']): ?>
                        <small>Image actuelle: <?php echo h($edit_actu['image']); ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="publie" value="1" <?php echo ($edit_actu['publie'] ?? 1) ? 'checked' : ''; ?>>
                            Publier cette actualité
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <?php if ($edit_actu): ?>
                        <a href="admin_actualites.php" class="btn btn-outline-primary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="admin-table">
                <h3>Liste des actualités (<?php echo count($actualites); ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Publié</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actualites as $a): ?>
                        <tr>
                            <td><strong><?php echo h($a['titre']); ?></strong></td>
                            <td><span class="badge badge-info"><?php echo h($a['type']); ?></span></td>
                            <td><?php echo formatDate($a['date_publication']); ?></td>
                            <td><?php echo $a['publie'] ? '<span style="color:green;">✓</span>' : '<span style="color:red;">✗</span>'; ?></td>
                            <td class="action-buttons">
                                <a href="?edit=<?php echo $a['id']; ?>" class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $a['id']; ?>" class="btn btn-sm btn-delete" data-confirm-delete>
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
    .checkbox-label {display: flex; align-items: center; gap: 0.5rem; font-weight: 500;}
    .checkbox-label input {width: auto; margin: 0;}
    </style>
    
    <script src="<?php echo SITE_URL; ?>/js/script.js"></script>
</body>
</html>
