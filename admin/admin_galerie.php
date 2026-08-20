<?php
require_once '../db_config.php';
requireLogin();

$success = '';
$error = '';

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // Récupérer le fichier avant suppression
        $stmt = $pdo->prepare("SELECT nom_fichier FROM galerie WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetch();
        
        // Supprimer l'image du serveur
        if ($file && file_exists(UPLOAD_DIR . $file['nom_fichier'])) {
            unlink(UPLOAD_DIR . $file['nom_fichier']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM galerie WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Image supprimée avec succès.';
    } catch (PDOException $e) {
        $error = 'Erreur lors de la suppression.';
    }
}

// Ajout/Modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $categorie = $_POST['categorie'];
    
    // Upload image obligatoire pour ajout
    $image_uploaded = false;
    $nom_fichier = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = generateFileName($filename);
            $upload_path = UPLOAD_DIR . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $nom_fichier = $new_filename;
                $image_uploaded = true;
            }
        } else {
            $error = 'Format d\'image non autorisé. Utilisez JPG, PNG ou GIF.';
        }
    }
    
    if (!$error) {
        try {
            if ($id) {
                // Mise à jour
                $sql = "UPDATE galerie SET titre=?, description=?, categorie=?";
                $params = [$titre, $description, $categorie];
                
                if ($nom_fichier) {
                    $sql .= ", nom_fichier=?, chemin=?";
                    $params[] = $nom_fichier;
                    $params[] = UPLOAD_URL . $nom_fichier;
                }
                
                $sql .= " WHERE id=?";
                $params[] = $id;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $success = 'Image modifiée avec succès.';
            } else {
                // Ajout - image obligatoire
                if (!$nom_fichier) {
                    $error = 'Veuillez sélectionner une image.';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO galerie (titre, description, nom_fichier, chemin, categorie) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$titre, $description, $nom_fichier, UPLOAD_URL . $nom_fichier, $categorie]);
                    $success = 'Image ajoutée avec succès.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Erreur: ' . $e->getMessage();
        }
    }
}

// Récupérer toutes les images
$images = $pdo->query("SELECT * FROM galerie ORDER BY date_ajout DESC")->fetchAll();

// Image à éditer
$edit_image = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM galerie WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_image = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de la Galerie</title>
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
                    <li><a href="admin_actualites.php"><i class="fas fa-newspaper"></i> Actualités</a></li>
                    <li><a href="admin_galerie.php" class="active"><i class="fas fa-images"></i> Galerie</a></li>
                    <li><a href="admin_partenaires.php"><i class="fas fa-handshake"></i> Partenaires</a></li>
                    <li><a href="admin_messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <h1><i class="fas fa-images"></i> Gestion de la Galerie Photos</h1>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo h($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <div class="admin-form-section">
                <h2><?php echo $edit_image ? 'Modifier' : 'Ajouter'; ?> une image</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $edit_image['id'] ?? ''; ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Titre *</label>
                        <input type="text" name="titre" class="form-control" 
                               value="<?php echo h($edit_image['titre'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo h($edit_image['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Catégorie *</label>
                        <select name="categorie" class="form-control" required>
                            <option value="">Sélectionner...</option>
                            <option value="Activité" <?php echo ($edit_image['categorie'] ?? '') == 'Activité' ? 'selected' : ''; ?>>Activité</option>
                            <option value="Équipement" <?php echo ($edit_image['categorie'] ?? '') == 'Équipement' ? 'selected' : ''; ?>>Équipement</option>
                            <option value="Expérience" <?php echo ($edit_image['categorie'] ?? '') == 'Expérience' ? 'selected' : ''; ?>>Expérience</option>
                            <option value="Événement" <?php echo ($edit_image['categorie'] ?? '') == 'Événement' ? 'selected' : ''; ?>>Événement</option>
                            <option value="Autre" <?php echo ($edit_image['categorie'] ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Image <?php echo !$edit_image ? '*' : ''; ?></label>
                        <input type="file" name="image" class="form-control" accept="image/*" <?php echo !$edit_image ? 'required' : ''; ?>>
                        <?php if ($edit_image): ?>
                        <small>Image actuelle: <?php echo h($edit_image['nom_fichier']); ?></small>
                        <div style="margin-top: 1rem;">
                            <img src="<?php echo UPLOAD_URL . h($edit_image['nom_fichier']); ?>" style="max-width: 200px; border-radius: 5px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <?php if ($edit_image): ?>
                        <a href="admin_galerie.php" class="btn btn-outline-primary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="admin-table">
                <h3>Galerie (<?php echo count($images); ?> images)</h3>
                <div class="gallery-admin-grid">
                    <?php foreach ($images as $img): ?>
                    <div class="gallery-admin-item">
                        <img src="<?php echo UPLOAD_URL . h($img['nom_fichier']); ?>" alt="<?php echo h($img['titre']); ?>">
                        <div class="gallery-admin-info">
                            <strong><?php echo h($img['titre']); ?></strong>
                            <span class="badge badge-info"><?php echo h($img['categorie']); ?></span>
                            <div class="action-buttons" style="margin-top: 0.5rem;">
                                <a href="?edit=<?php echo $img['id']; ?>" class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $img['id']; ?>" class="btn btn-sm btn-delete" data-confirm-delete>
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
    
    <style>
    .admin-form-section {background: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 2rem;}
    .admin-form-section h2 {margin-bottom: 1.5rem; color: var(--primary-color);}
    .form-actions {display: flex; gap: 1rem; margin-top: 1.5rem;}
    .gallery-admin-grid {display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;}
    .gallery-admin-item {background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);}
    .gallery-admin-item img {width: 100%; height: 200px; object-fit: cover;}
    .gallery-admin-info {padding: 1rem;}
    </style>
    
    <script src="<?php echo SITE_URL; ?>/js/script.js"></script>
</body>
</html>
