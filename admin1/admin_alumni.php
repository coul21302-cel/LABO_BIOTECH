<?php
require_once '../db_config.php';
requireLogin();

$success = '';
$error = '';

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM alumni WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Alumni supprimé avec succès.';
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
    $statut_labo = $_POST['statut_labo'];
    $periode_debut = $_POST['periode_debut'] ?: null;
    $periode_fin = $_POST['periode_fin'] ?: null;
    $poste_actuel = trim($_POST['poste_actuel']);
    $organisation_actuelle = trim($_POST['organisation_actuelle']);
    $ville_actuelle = trim($_POST['ville_actuelle']);
    $pays_actuel = trim($_POST['pays_actuel']);
    $these_titre = trim($_POST['these_titre']);
    $domaine_specialisation = trim($_POST['domaine_specialisation']);
    $realisations = trim($_POST['realisations']);
    $parcours_professionnel = trim($_POST['parcours_professionnel']);
    $linkedin = trim($_POST['linkedin']);
    $researchgate = trim($_POST['researchgate']);
    $google_scholar = trim($_POST['google_scholar']);
    $site_web_perso = trim($_POST['site_web_perso']);
    $testimonial = trim($_POST['testimonial']);
    $afficher_contact = isset($_POST['afficher_contact']) ? 1 : 0;
    $actif = isset($_POST['actif']) ? 1 : 0;
    
    // Upload photo
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['photo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = generateFileName($filename);
            $upload_path = UPLOAD_DIR . $new_filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                $photo = $new_filename;
            }
        }
    }
    
    try {
        if ($id) {
            // Mise à jour
            $sql = "UPDATE alumni SET nom=?, prenom=?, email=?, statut_labo=?, periode_debut=?, periode_fin=?, 
                    poste_actuel=?, organisation_actuelle=?, ville_actuelle=?, pays_actuel=?, 
                    these_titre=?, domaine_specialisation=?, realisations=?, parcours_professionnel=?,
                    linkedin=?, researchgate=?, google_scholar=?, site_web_perso=?, 
                    testimonial=?, afficher_contact=?, actif=?";
            $params = [$nom, $prenom, $email, $statut_labo, $periode_debut, $periode_fin,
                      $poste_actuel, $organisation_actuelle, $ville_actuelle, $pays_actuel,
                      $these_titre, $domaine_specialisation, $realisations, $parcours_professionnel,
                      $linkedin, $researchgate, $google_scholar, $site_web_perso,
                      $testimonial, $afficher_contact, $actif];
            
            if ($photo) {
                $sql .= ", photo=?";
                $params[] = $photo;
            }
            
            $sql .= " WHERE id=?";
            $params[] = $id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $success = 'Alumni modifié avec succès.';
        } else {
            // Ajout
            $stmt = $pdo->prepare("INSERT INTO alumni (nom, prenom, email, photo, statut_labo, periode_debut, periode_fin, 
                poste_actuel, organisation_actuelle, ville_actuelle, pays_actuel, these_titre, domaine_specialisation, 
                realisations, parcours_professionnel, linkedin, researchgate, google_scholar, site_web_perso, 
                testimonial, afficher_contact, actif) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $photo, $statut_labo, $periode_debut, $periode_fin,
                           $poste_actuel, $organisation_actuelle, $ville_actuelle, $pays_actuel,
                           $these_titre, $domaine_specialisation, $realisations, $parcours_professionnel,
                           $linkedin, $researchgate, $google_scholar, $site_web_perso,
                           $testimonial, $afficher_contact, $actif]);
            $success = 'Alumni ajouté avec succès.';
        }
    } catch (PDOException $e) {
        $error = 'Erreur: ' . $e->getMessage();
    }
}

// Récupérer tous les alumni
$alumni = $pdo->query("SELECT * FROM alumni ORDER BY periode_fin DESC, nom")->fetchAll();

// Alumni à éditer
$edit_alumni = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM alumni WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_alumni = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Alumni</title>
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
                    <li><a href="admin_alumni.php" class="active"><i class="fas fa-user-graduate"></i> Alumni</a></li>
                    <li><a href="admin_projets.php"><i class="fas fa-project-diagram"></i> Projets</a></li>
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
            <h1><i class="fas fa-user-graduate"></i> Gestion des Alumni</h1>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo h($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
            <?php endif; ?>
            
            <div class="admin-form-section">
                <h2><?php echo $edit_alumni ? 'Modifier' : 'Ajouter'; ?> un alumni</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $edit_alumni['id'] ?? ''; ?>">
                    
                    <h3>Informations Personnelles</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Prénom *</label>
                            <input type="text" name="prenom" class="form-control" 
                                   value="<?php echo h($edit_alumni['prenom'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nom *</label>
                            <input type="text" name="nom" class="form-control" 
                                   value="<?php echo h($edit_alumni['nom'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?php echo h($edit_alumni['email'] ?? ''); ?>">
                        <small>Sera affiché uniquement si autorisé ci-dessous</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Photo</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <?php if ($edit_alumni && $edit_alumni['photo']): ?>
                        <small>Photo actuelle: <?php echo h($edit_alumni['photo']); ?></small>
                        <?php endif; ?>
                    </div>
                    
                    <h3>Période au Laboratoire</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Statut au labo *</label>
                            <select name="statut_labo" class="form-control" required>
                                <option value="">Sélectionner...</option>
                                <option value="Stagiaire" <?php echo ($edit_alumni['statut_labo'] ?? '') == 'Stagiaire' ? 'selected' : ''; ?>>Stagiaire</option>
                                <option value="Doctorant" <?php echo ($edit_alumni['statut_labo'] ?? '') == 'Doctorant' ? 'selected' : ''; ?>>Doctorant</option>
                                <option value="Post-doc" <?php echo ($edit_alumni['statut_labo'] ?? '') == 'Post-doc' ? 'selected' : ''; ?>>Post-doc</option>
                                <option value="Chercheur" <?php echo ($edit_alumni['statut_labo'] ?? '') == 'Chercheur' ? 'selected' : ''; ?>>Chercheur</option>
                                <option value="Technicien" <?php echo ($edit_alumni['statut_labo'] ?? '') == 'Technicien' ? 'selected' : ''; ?>>Technicien</option>
                                <option value="Autre" <?php echo ($edit_alumni['statut_labo'] ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Date début</label>
                            <input type="date" name="periode_debut" class="form-control" 
                                   value="<?php echo h($edit_alumni['periode_debut'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Date fin</label>
                            <input type="date" name="periode_fin" class="form-control" 
                                   value="<?php echo h($edit_alumni['periode_fin'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <h3>Situation Actuelle</h3>
                    <div class="form-group">
                        <label class="form-label">Poste actuel</label>
                        <input type="text" name="poste_actuel" class="form-control" 
                               value="<?php echo h($edit_alumni['poste_actuel'] ?? ''); ?>"
                               placeholder="Ex: Chercheur Senior, Enseignant-Chercheur">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Organisation actuelle</label>
                        <input type="text" name="organisation_actuelle" class="form-control" 
                               value="<?php echo h($edit_alumni['organisation_actuelle'] ?? ''); ?>"
                               placeholder="Ex: IRD, ISRA, AfricaRice">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Ville</label>
                            <input type="text" name="ville_actuelle" class="form-control" 
                                   value="<?php echo h($edit_alumni['ville_actuelle'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pays</label>
                            <input type="text" name="pays_actuel" class="form-control" 
                                   value="<?php echo h($edit_alumni['pays_actuel'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <h3>Travaux et Réalisations</h3>
                    <div class="form-group">
                        <label class="form-label">Titre de thèse (si applicable)</label>
                        <textarea name="these_titre" class="form-control" rows="2"><?php echo h($edit_alumni['these_titre'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Domaine de spécialisation</label>
                        <input type="text" name="domaine_specialisation" class="form-control" 
                               value="<?php echo h($edit_alumni['domaine_specialisation'] ?? ''); ?>"
                               placeholder="Ex: Génomique végétale, Biologie moléculaire">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Réalisations au labo</label>
                        <textarea name="realisations" class="form-control" rows="3"><?php echo h($edit_alumni['realisations'] ?? ''); ?></textarea>
                        <small>Publications, brevets, prix obtenus pendant la période au labo</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Parcours professionnel</label>
                        <textarea name="parcours_professionnel" class="form-control" rows="3"><?php echo h($edit_alumni['parcours_professionnel'] ?? ''); ?></textarea>
                        <small>Évolution de carrière après le labo</small>
                    </div>
                    
                    <h3>Réseaux Professionnels</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">LinkedIn</label>
                            <input type="url" name="linkedin" class="form-control" 
                                   value="<?php echo h($edit_alumni['linkedin'] ?? ''); ?>"
                                   placeholder="https://www.linkedin.com/in/...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">ResearchGate</label>
                            <input type="url" name="researchgate" class="form-control" 
                                   value="<?php echo h($edit_alumni['researchgate'] ?? ''); ?>"
                                   placeholder="https://www.researchgate.net/profile/...">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Google Scholar</label>
                            <input type="url" name="google_scholar" class="form-control" 
                                   value="<?php echo h($edit_alumni['google_scholar'] ?? ''); ?>"
                                   placeholder="https://scholar.google.com/citations?user=...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Site web personnel</label>
                            <input type="url" name="site_web_perso" class="form-control" 
                                   value="<?php echo h($edit_alumni['site_web_perso'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <h3>Témoignage</h3>
                    <div class="form-group">
                        <label class="form-label">Témoignage sur l'expérience au labo</label>
                        <textarea name="testimonial" class="form-control" rows="4"><?php echo h($edit_alumni['testimonial'] ?? ''); ?></textarea>
                    </div>
                    
                    <h3>Visibilité</h3>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="afficher_contact" value="1" <?php echo ($edit_alumni['afficher_contact'] ?? 0) ? 'checked' : ''; ?>>
                            Afficher l'email sur la page publique
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="actif" value="1" <?php echo ($edit_alumni['actif'] ?? 1) ? 'checked' : ''; ?>>
                            Publier sur la page Alumni
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <?php if ($edit_alumni): ?>
                        <a href="admin_alumni.php" class="btn btn-outline-primary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="admin-table">
                <h3>Liste des alumni (<?php echo count($alumni); ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Nom</th>
                            <th>Statut au labo</th>
                            <th>Période</th>
                            <th>Poste actuel</th>
                            <th>Publié</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alumni as $a): ?>
                        <tr>
                            <td>
                                <?php if ($a['photo']): ?>
                                <img src="<?php echo UPLOAD_URL . h($a['photo']); ?>" style="width:50px;height:50px;border-radius:50%;object-fit:cover;">
                                <?php else: ?>
                                <i class="fas fa-user-circle" style="font-size:50px;color:#ccc;"></i>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo h($a['prenom'] . ' ' . $a['nom']); ?></strong></td>
                            <td><span class="badge badge-info"><?php echo h($a['statut_labo']); ?></span></td>
                            <td><?php echo $a['periode_debut'] ? date('Y', strtotime($a['periode_debut'])) : '?'; ?> - <?php echo $a['periode_fin'] ? date('Y', strtotime($a['periode_fin'])) : '?'; ?></td>
                            <td><?php echo h($a['poste_actuel'] ?: '-'); ?></td>
                            <td><?php echo $a['actif'] ? '<span style="color:green;">✓</span>' : '<span style="color:red;">✗</span>'; ?></td>
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
    .admin-form-section h3 {margin-top: 2rem; margin-bottom: 1rem; color: var(--secondary-color); font-size: 1.2rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.5rem;}
    .form-row {display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;}
    @media (max-width: 768px) {.form-row {grid-template-columns: 1fr;}}
    .form-actions {display: flex; gap: 1rem; margin-top: 1.5rem;}
    .checkbox-label {display: flex; align-items: center; gap: 0.5rem; font-weight: 500;}
    .checkbox-label input {width: auto; margin: 0;}
    </style>
    
    <script src="<?php echo SITE_URL; ?>/js/script.js"></script>
</body>
</html>
