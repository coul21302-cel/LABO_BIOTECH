<?php
require_once '../db_config.php';
requireLogin();

$success = '';
$error = '';

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM membres WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Membre supprimé avec succès.';
    } catch (PDOException $e) {
        $error = 'Erreur lors de la suppression.';
    }
}

// Ajout/Modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $categorie = $_POST['categorie'];
    $specialite = trim($_POST['specialite']);
    $biographie = trim($_POST['biographie']);
    $domaine_recherche = trim($_POST['domaine_recherche']);
    $researchgate = trim($_POST['researchgate'] ?? '');
    $google_scholar = trim($_POST['google_scholar'] ?? '');
    $linkedin = trim($_POST['linkedin'] ?? '');
    $orcid = trim($_POST['orcid'] ?? '');
    $site_web = trim($_POST['site_web'] ?? '');
    
    // Upload photo
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Création automatique du dossier s'il n'existe pas
            if (!file_exists(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0777, true);
            }

            $new_filename = generateFileName($filename);
            $upload_path = UPLOAD_DIR . $new_filename;
            
            // Vérification des droits d'écriture
            if (is_writable(UPLOAD_DIR)) {
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    $photo = $new_filename;
                } else {
                    $error = "Échec du transfert du fichier vers le serveur.";
                }
            } else {
                $error = "Le répertoire de destination n'est pas accessible en écriture.";
            }
        } else {
            $error = "Format d'image non autorisé (utilisez JPG, JPEG, PNG ou GIF).";
        }
    }
    
    try {
        if ($id) {
            // Mise à jour
            $sql = "UPDATE membres SET nom=?, prenom=?, email=?, categorie=?, specialite=?, biographie=?, domaine_recherche=?, 
                    researchgate=?, google_scholar=?, linkedin=?, orcid=?, site_web=?";
            $params = [$nom, $prenom, $email, $categorie, $specialite, $biographie, $domaine_recherche,
                      $researchgate, $google_scholar, $linkedin, $orcid, $site_web];
            
            if ($photo) {
                $sql .= ", photo=?";
                $params[] = $photo;
            }
            
            $sql .= " WHERE id=?";
            $params[] = $id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $success = 'Membre modifié avec succès.';
        } else {
            // Ajout
            if (!$photo) $photo = 'default-avatar.png';
            
            $stmt = $pdo->prepare("INSERT INTO membres (nom, prenom, email, categorie, specialite, biographie, domaine_recherche, 
                                   researchgate, google_scholar, linkedin, orcid, site_web, photo) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $categorie, $specialite, $biographie, $domaine_recherche,
                           $researchgate, $google_scholar, $linkedin, $orcid, $site_web, $photo]);
            $success = 'Membre ajouté avec succès.';
        }
    } catch (PDOException $e) {
        $error = 'Erreur SQL : ' . $e->getMessage();
    }
}

// Récupérer tous les membres
$membres = $pdo->query("SELECT * FROM membres ORDER BY categorie, nom")->fetchAll();

// Membre à éditer
$edit_membre = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM membres WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_membre = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de l'équipe - Administration</title>
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
                    <li><a href="admin_equipe.php" class="active"><i class="fas fa-users"></i> Équipe</a></li>
                    <li><a href="admin_projets.php"><i class="fas fa-project-diagram"></i> Projets</a></li>
                    <li><a href="admin_publications.php"><i class="fas fa-book"></i> Publications</a></li>
                    <li><a href="admin_actualites.php"><i class="fas fa-newspaper"></i> Actualités</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-header">
                <h1><i class="fas fa-users"></i> Gestion de l'équipe</h1>
            </div>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo h($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <!-- Formulaire -->
            <div class="admin-form-section">
                <h2><?php echo $edit_membre ? 'Modifier' : 'Ajouter'; ?> un membre</h2>
                <form method="POST" enctype="multipart/form-data" class="admin-form">
                    <input type="hidden" name="id" value="<?php echo $edit_membre['id'] ?? ''; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" 
                                   value="<?php echo h($edit_membre['nom'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control" 
                                   value="<?php echo h($edit_membre['prenom'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo h($edit_membre['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Catégorie *</label>
                            <select name="categorie" class="form-control" required>
                                <option value="">Sélectionner...</option>
                                <option value="Professeur" <?php echo ($edit_membre['categorie'] ?? '') == 'Professeur' ? 'selected' : ''; ?>>Professeur</option>
                                <option value="Chercheur" <?php echo ($edit_membre['categorie'] ?? '') == 'Chercheur' ? 'selected' : ''; ?>>Chercheur</option>
                                <option value="Doctorant" <?php echo ($edit_membre['categorie'] ?? '') == 'Doctorant' ? 'selected' : ''; ?>>Doctorant</option>
                                <option value="Étudiant" <?php echo ($edit_membre['categorie'] ?? '') == 'Étudiant' ? 'selected' : ''; ?>>Étudiant</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Spécialité</label>
                        <input type="text" name="specialite" class="form-control" 
                               value="<?php echo h($edit_membre['specialite'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Biographie</label>
                        <textarea name="biographie" class="form-control" rows="4"><?php echo h($edit_membre['biographie'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Domaine de recherche</label>
                        <textarea name="domaine_recherche" class="form-control" rows="3"><?php echo h($edit_membre['domaine_recherche'] ?? ''); ?></textarea>
                    </div>
                    
                    <h3 class="form-section-title">Réseaux Professionnels</h3>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fab fa-researchgate"></i> ResearchGate
                        </label>
                        <input type="url" name="researchgate" class="form-control" 
                               value="<?php echo h($edit_membre['researchgate'] ?? ''); ?>"
                               placeholder="https://www.researchgate.net/profile/...">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-graduation-cap"></i> Google Scholar
                        </label>
                        <input type="url" name="google_scholar" class="form-control" 
                               value="<?php echo h($edit_membre['google_scholar'] ?? ''); ?>"
                               placeholder="https://scholar.google.com/citations?user=...">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fab fa-linkedin"></i> LinkedIn
                        </label>
                        <input type="url" name="linkedin" class="form-control" 
                               value="<?php echo h($edit_membre['linkedin'] ?? ''); ?>"
                               placeholder="https://www.linkedin.com/in/...">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fab fa-orcid"></i> ORCID
                        </label>
                        <input type="url" name="orcid" class="form-control" 
                               value="<?php echo h($edit_membre['orcid'] ?? ''); ?>"
                               placeholder="https://orcid.org/0000-0000-0000-0000">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-globe"></i> Site web personnel
                        </label>
                        <input type="url" name="site_web" class="form-control" 
                               value="<?php echo h($edit_membre['site_web'] ?? ''); ?>"
                               placeholder="https://...">
                    </div>
                    
                    <h3 class="form-section-title">Photo</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <?php if ($edit_membre && $edit_membre['photo']): ?>
                        <small>Photo actuelle: <?php echo h($edit_membre['photo']); ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <?php if ($edit_membre): ?>
                        <a href="admin_equipe.php" class="btn btn-outline-primary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Liste des membres -->
            <div class="admin-table">
                <h3>Liste des membres (<?php echo count($membres); ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Email</th>
                            <th>Spécialité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($membres as $membre): ?>
                        <tr>
                            <td>
                                <img src="<?php echo UPLOAD_URL . h($membre['photo']); ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%;" 
                                     onerror="this.src='<?php echo UPLOAD_URL; ?>default-avatar.png'">
                            </td>
                            <td><strong><?php echo h($membre['prenom'] . ' ' . $membre['nom']); ?></strong></td>
                            <td><span class="badge badge-info"><?php echo h($membre['categorie']); ?></span></td>
                            <td><?php echo h($membre['email']); ?></td>
                            <td><?php echo h(truncate($membre['specialite'], 40)); ?></td>
                            <td class="action-buttons">
                                <a href="?edit=<?php echo $membre['id']; ?>" class="btn btn-sm btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $membre['id']; ?>" 
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