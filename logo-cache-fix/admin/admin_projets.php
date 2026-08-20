<?php
require_once '../db_config.php';
requireLogin();

$success = '';
$error = '';

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM projets WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Projet supprimé avec succès.';
    } catch (PDOException $e) {
        $error = 'Erreur lors de la suppression.';
    }
}

// Ajout/Modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $objectifs = trim($_POST['objectifs']);
    $statut = $_POST['statut'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $budget = $_POST['budget'];
    $financement = trim($_POST['financement']);
    
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
            $sql = "UPDATE projets SET titre=?, description=?, objectifs=?, statut=?, date_debut=?, date_fin=?, budget=?, financement=?";
            $params = [$titre, $description, $objectifs, $statut, $date_debut, $date_fin, $budget, $financement];
            
            if ($image) {
                $sql .= ", image=?";
                $params[] = $image;
            }
            
            $sql .= " WHERE id=?";
            $params[] = $id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $success = 'Projet modifié avec succès.';
        } else {
            // Ajout
            $stmt = $pdo->prepare("INSERT INTO projets (titre, description, objectifs, statut, date_debut, date_fin, budget, financement, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titre, $description, $objectifs, $statut, $date_debut, $date_fin, $budget, $financement, $image]);
            $success = 'Projet ajouté avec succès.';
        }
    } catch (PDOException $e) {
        $error = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
    }
}

// Récupérer tous les projets
$projets = $pdo->query("SELECT * FROM projets ORDER BY statut, titre")->fetchAll();

// Projet à éditer
$edit_projet = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM projets WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_projet = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Projets - Administration</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-dashboard">
        <aside class="admin-sidebar">
            <h2><img src="<?php echo SITE_URL; ?>/uploads/images/logo.png?v=<?php echo time(); ?>" alt="Logo" class="admin-logo"> LBV Admin</h2>
            <nav class="admin-nav">
                <ul>
                    <li><a href="dashboard.php"><i class="fas fa-home"></i> Tableau de bord</a></li>
                    <li><a href="admin_equipe.php"><i class="fas fa-users"></i> Équipe</a></li>
                    <li><a href="admin_alumni.php"><i class="fas fa-user-graduate"></i> Alumni</a></li>
                    <li><a href="admin_projets.php" class="active"><i class="fas fa-project-diagram"></i> Projets</a></li>
                    <li><a href="admin_publications.php"><i class="fas fa-book"></i> Publications</a></li>
                    <li><a href="admin_actualites.php"><i class="fas fa-newspaper"></i> Actualités</a></li>
                    <li><a href="admin_galerie.php"><i class="fas fa-images"></i> Galerie</a></li>
                    <li><a href="admin_partenaires.php"><i class="fas fa-handshake"></i> Partenaires</a></li>
                    <li><a href="admin_messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1><i class="fas fa-project-diagram"></i> Gestion des Projets</h1>
            </div>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo h($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <!-- Formulaire -->
            <div class="admin-form-section">
                <h2><?php echo $edit_projet ? 'Modifier' : 'Ajouter'; ?> un projet</h2>
                <form method="POST" enctype="multipart/form-data" class="admin-form">
                    <input type="hidden" name="id" value="<?php echo $edit_projet['id'] ?? ''; ?>">
                    
                    <div class="form-group">
                        <label class="form-label">Titre du projet *</label>
                        <input type="text" name="titre" class="form-control" 
                               value="<?php echo h($edit_projet['titre'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="4" required><?php echo h($edit_projet['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Objectifs</label>
                        <textarea name="objectifs" class="form-control" rows="3"><?php echo h($edit_projet['objectifs'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Statut *</label>
                            <select name="statut" class="form-control" required>
                                <option value="">Sélectionner...</option>
                                <option value="En cours" <?php echo ($edit_projet['statut'] ?? '') == 'En cours' ? 'selected' : ''; ?>>En cours</option>
                                <option value="Terminé" <?php echo ($edit_projet['statut'] ?? '') == 'Terminé' ? 'selected' : ''; ?>>Terminé</option>
                                <option value="Planifié" <?php echo ($edit_projet['statut'] ?? '') == 'Planifié' ? 'selected' : ''; ?>>Planifié</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Budget (FCFA)</label>
                            <input type="number" name="budget" class="form-control" step="0.01"
                                   value="<?php echo h($edit_projet['budget'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Date de début</label>
                            <input type="date" name="date_debut" class="form-control" 
                                   value="<?php echo h($edit_projet['date_debut'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Date de fin</label>
                            <input type="date" name="date_fin" class="form-control" 
                                   value="<?php echo h($edit_projet['date_fin'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Source de financement</label>
                        <input type="text" name="financement" class="form-control" 
                               value="<?php echo h($edit_projet['financement'] ?? ''); ?>"
                               placeholder="Ex: Horizon Europe, IRD, WAAPP...">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Image du projet</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <?php if ($edit_projet && $edit_projet['image']): ?>
                        <small>Image actuelle: <?php echo h($edit_projet['image']); ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <?php if ($edit_projet): ?>
                        <a href="admin_projets.php" class="btn btn-outline-primary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Liste des projets -->
            <div class="admin-table">
                <h3>Liste des projets (<?php echo count($projets); ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Statut</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Budget</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projets as $projet): ?>
                        <tr>
                            <td><strong><?php echo h($projet['titre']); ?></strong></td>
                            <td>
                                <span class="badge badge-<?php echo $projet['statut'] == 'En cours' ? 'success' : ($projet['statut'] == 'Terminé' ? 'info' : 'warning'); ?>">
                                    <?php echo h($projet['statut']); ?>
                                </span>
                            </td>
                            <td><?php echo $projet['date_debut'] ? formatDate($projet['date_debut']) : '-'; ?></td>
                            <td><?php echo $projet['date_fin'] ? formatDate($projet['date_fin']) : '-'; ?></td>
                            <td><?php echo $projet['budget'] ? number_format($projet['budget'], 0, ',', ' ') . ' FCFA' : '-'; ?></td>
                            <td class="action-buttons">
                                <a href="?edit=<?php echo $projet['id']; ?>" class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $projet['id']; ?>" 
                                   class="btn btn-sm btn-delete" 
                                   data-confirm-delete>
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
    .admin-form-section {
        background: #fff;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .admin-form-section h2 {
        margin-bottom: 1.5rem;
        color: var(--primary-color);
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    </style>
    
    <script src="<?php echo SITE_URL; ?>/js/script.js"></script>
</body>
</html>
