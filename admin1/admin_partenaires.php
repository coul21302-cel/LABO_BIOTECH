<?php
require_once '../db_config.php';
requireLogin();

$success = '';
$error = '';

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM partenaires WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Partenaire supprimé avec succès.';
    } catch (PDOException $e) {
        $error = 'Erreur lors de la suppression.';
    }
}

// Ajout/Modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $nom = trim($_POST['nom']);
    $type = $_POST['type'];
    $pays = trim($_POST['pays']);
    $ville = trim($_POST['ville']);
    $site_web = trim($_POST['site_web']);
    $description = trim($_POST['description']);
    $actif = isset($_POST['actif']) ? 1 : 0;
    
    // Upload logo
    $logo = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['logo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = generateFileName($filename);
            $upload_path = UPLOAD_DIR . $new_filename;
            
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                $logo = $new_filename;
            }
        }
    }
    
    try {
        if ($id) {
            // Mise à jour
            $sql = "UPDATE partenaires SET nom=?, type=?, pays=?, ville=?, site_web=?, description=?, actif=?";
            $params = [$nom, $type, $pays, $ville, $site_web, $description, $actif];
            
            if ($logo) {
                $sql .= ", logo=?";
                $params[] = $logo;
            }
            
            $sql .= " WHERE id=?";
            $params[] = $id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $success = 'Partenaire modifié avec succès.';
        } else {
            // Ajout
            $stmt = $pdo->prepare("INSERT INTO partenaires (nom, type, pays, ville, site_web, logo, description, actif) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $type, $pays, $ville, $site_web, $logo, $description, $actif]);
            $success = 'Partenaire ajouté avec succès.';
        }
    } catch (PDOException $e) {
        $error = 'Erreur: ' . $e->getMessage();
    }
}

// Récupérer tous les partenaires
$partenaires = $pdo->query("SELECT * FROM partenaires ORDER BY nom")->fetchAll();

// Partenaire à éditer
$edit_part = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM partenaires WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_part = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Partenaires</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-dashboard">
        <aside class="admin-sidebar">
            <h2><img src="<?php echo SITE_URL; ?>/uploads/images/logo.png" alt="Logo" class="admin-logo"> LBV Admin</h2>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Tableau de bord</a></li>
                    <li><a href="admin_equipe.php"><i class="fas fa-users"></i> Équipe</a></li>
                    <li><a href="admin_projets.php"><i class="fas fa-project-diagram"></i> Projets</a></li>
                    <li><a href="admin_publications.php"><i class="fas fa-book"></i> Publications</a></li>
                    <li><a href="admin_actualites.php"><i class="fas fa-newspaper"></i> Actualités</a></li>
                    <li><a href="admin_galerie.php"><i class="fas fa-images"></i> Galerie</a></li>
                    <li><a href="admin_partenaires.php" class="active"><i class="fas fa-handshake"></i> Partenaires</a></li>
                    <li><a href="admin_messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <h1><i class="fas fa-handshake"></i> Gestion des Partenaires</h1>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo h($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <div class="admin-form-section">
                <h2><?php echo $edit_part ? 'Modifier' : 'Ajouter'; ?> un partenaire</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $edit_part['id'] ?? ''; ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Nom du partenaire *</label>
                        <input type="text" name="nom" class="form-control" 
                               value="<?php echo h($edit_part['nom'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Type *</label>
                        <select name="type" class="form-control" required>
                            <option value="">Sélectionner...</option>
                            <option value="Université" <?php echo ($edit_part['type'] ?? '') == 'Université' ? 'selected' : ''; ?>>Université</option>
                            <option value="Centre de recherche" <?php echo ($edit_part['type'] ?? '') == 'Centre de recherche' ? 'selected' : ''; ?>>Centre de recherche</option>
                            <option value="Laboratoire" <?php echo ($edit_part['type'] ?? '') == 'Laboratoire' ? 'selected' : ''; ?>>Laboratoire</option>
                            <option value="Entreprise" <?php echo ($edit_part['type'] ?? '') == 'Entreprise' ? 'selected' : ''; ?>>Entreprise</option>
                            <option value="Autre" <?php echo ($edit_part['type'] ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Ville</label>
                            <input type="text" name="ville" class="form-control" 
                                   value="<?php echo h($edit_part['ville'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Pays</label>
                            <input type="text" name="pays" class="form-control" 
                                   value="<?php echo h($edit_part['pays'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Site web</label>
                        <input type="url" name="site_web" class="form-control" 
                               value="<?php echo h($edit_part['site_web'] ?? ''); ?>" placeholder="https://...">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo h($edit_part['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <?php if ($edit_part && $edit_part['logo']): ?>
                        <small>Logo actuel: <?php echo h($edit_part['logo']); ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="actif" value="1" <?php echo ($edit_part['actif'] ?? 1) ? 'checked' : ''; ?>>
                            Partenaire actif (visible sur le site)
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <?php if ($edit_part): ?>
                        <a href="admin_partenaires.php" class="btn btn-outline-primary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="admin-table">
                <h3>Liste des partenaires (<?php echo count($partenaires); ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Localisation</th>
                            <th>Site web</th>
                            <th>Actif</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partenaires as $p): ?>
                        <tr>
                            <td><strong><?php echo h($p['nom']); ?></strong></td>
                            <td><span class="badge badge-info"><?php echo h($p['type']); ?></span></td>
                            <td><?php echo h($p['ville']); ?><?php echo $p['ville'] && $p['pays'] ? ', ' : ''; ?><?php echo h($p['pays']); ?></td>
                            <td><?php echo $p['site_web'] ? '<a href="'.h($p['site_web']).'" target="_blank">Visiter</a>' : '-'; ?></td>
                            <td><?php echo $p['actif'] ? '<span style="color:green;">✓</span>' : '<span style="color:red;">✗</span>'; ?></td>
                            <td class="action-buttons">
                                <a href="?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-sm btn-delete" data-confirm-delete>
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
