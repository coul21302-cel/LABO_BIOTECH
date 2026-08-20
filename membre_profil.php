<?php
require_once 'db_config.php';

// Récupérer l'ID du membre
$membre_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$membre_id) {
    header('Location: pages/equipe.php');
    exit;
}

// Récupérer les informations du membre
$stmt = $pdo->prepare("SELECT * FROM membres WHERE id = ?");
$stmt->execute([$membre_id]);
$membre = $stmt->fetch();

if (!$membre) {
    header('Location: pages/equipe.php');
    exit;
}

// Récupérer les publications du membre
$stmt = $pdo->prepare("
    SELECT * FROM publications 
    WHERE membre_id = ? 
    ORDER BY annee DESC, id DESC
");
$stmt->execute([$membre_id]);
$publications = $stmt->fetchAll();

// Récupérer les projets du membre
$stmt = $pdo->prepare("
    SELECT p.*, pm.role 
    FROM projets p
    INNER JOIN projet_membres pm ON p.id = pm.projet_id
    WHERE pm.membre_id = ?
    ORDER BY p.statut, p.date_debut DESC
");
$stmt->execute([$membre_id]);
$projets = $stmt->fetchAll();

$page_title = $membre['prenom'] . ' ' . $membre['nom'];
$current_page = 'equipe';

include 'header.php';
?>

<div class="page-header">
    <div class="container">
        <nav class="breadcrumb">
            <a href="index.php">Accueil</a>
            <span>/</span>
            <a href="pages/equipe.php">Équipe</a>
            <span>/</span>
            <span><?php echo h($membre['prenom'] . ' ' . $membre['nom']); ?></span>
        </nav>
        <h1><?php echo h($membre['prenom'] . ' ' . $membre['nom']); ?></h1>
    </div>
</div>

<div class="container membre-detail">
    <!-- Profil du membre -->
    <div class="membre-profile">
        <div class="profile-header">
            <div class="profile-photo">
                <img src="<?php echo UPLOAD_URL . h($membre['photo']); ?>" 
                     alt="<?php echo h($membre['prenom'] . ' ' . $membre['nom']); ?>"
                     onerror="this.src='<?php echo UPLOAD_URL; ?>default-avatar.png'">
            </div>
            <div class="profile-info">
                <div class="profile-badge">
                    <span class="badge badge-<?php echo getCategoryColor($membre['categorie']); ?>">
                        <?php echo h($membre['categorie']); ?>
                    </span>
                </div>
                <h2><?php echo h($membre['prenom'] . ' ' . $membre['nom']); ?></h2>
                <?php if ($membre['specialite']): ?>
                <p class="specialite">
                    <i class="fas fa-microscope"></i> <?php echo h($membre['specialite']); ?>
                </p>
                <?php endif; ?>
                
                <div class="contact-info">
                    <?php if ($membre['email']): ?>
                    <a href="mailto:<?php echo h($membre['email']); ?>" class="contact-link">
                        <i class="fas fa-envelope"></i> <?php echo h($membre['email']); ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($membre['telephone']): ?>
                    <a href="tel:<?php echo h($membre['telephone']); ?>" class="contact-link">
                        <i class="fas fa-phone"></i> <?php echo h($membre['telephone']); ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($membre['google_scholar_id']): ?>
                    <a href="https://scholar.google.com/citations?user=<?php echo h($membre['google_scholar_id']); ?>" 
                       target="_blank" class="contact-link scholar-link">
                        <i class="fas fa-graduation-cap"></i> Google Scholar
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Biographie -->
        <?php if ($membre['biographie']): ?>
        <div class="profile-section">
            <h3><i class="fas fa-user"></i> Biographie</h3>
            <p class="text-content"><?php echo nl2br(h($membre['biographie'])); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Domaine de recherche -->
        <?php if ($membre['domaine_recherche']): ?>
        <div class="profile-section">
            <h3><i class="fas fa-flask"></i> Domaines de recherche</h3>
            <p class="text-content"><?php echo nl2br(h($membre['domaine_recherche'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Projets du membre -->
    <?php if (!empty($projets)): ?>
    <div class="membre-projects">
        <h3><i class="fas fa-project-diagram"></i> Projets (<?php echo count($projets); ?>)</h3>
        <div class="projects-grid">
            <?php foreach ($projets as $projet): ?>
            <div class="project-card">
                <div class="project-header">
                    <h4><?php echo h($projet['titre']); ?></h4>
                    <span class="badge badge-<?php echo $projet['statut'] == 'En cours' ? 'success' : 'secondary'; ?>">
                        <?php echo h($projet['statut']); ?>
                    </span>
                </div>
                <?php if ($projet['role']): ?>
                <p class="project-role">
                    <i class="fas fa-user-tag"></i> <strong><?php echo h($projet['role']); ?></strong>
                </p>
                <?php endif; ?>
                <p class="project-description"><?php echo h(truncate($projet['description'], 150)); ?></p>
                <a href="pages/recherche.php#projet-<?php echo $projet['id']; ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-right"></i> Voir le projet
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Publications du membre -->
    <div class="membre-publications">
        <h3>
            <i class="fas fa-book"></i> 
            Publications (<?php echo count($publications); ?>)
        </h3>
        
        <?php if (empty($publications)): ?>
        <div class="no-publications">
            <i class="fas fa-book-open"></i>
            <p>Aucune publication enregistrée pour le moment.</p>
        </div>
        <?php else: ?>
        
        <!-- Statistiques -->
        <div class="pub-stats">
            <?php 
            $annees = array_unique(array_column($publications, 'annee'));
            rsort($annees);
            ?>
            <div class="stat-item">
                <i class="fas fa-book"></i>
                <div>
                    <strong><?php echo count($publications); ?></strong>
                    <span>Publication(s)</span>
                </div>
            </div>
            <div class="stat-item">
                <i class="fas fa-calendar"></i>
                <div>
                    <strong><?php echo min($annees) . ' - ' . max($annees); ?></strong>
                    <span>Période</span>
                </div>
            </div>
        </div>
        
        <!-- Liste des publications par année -->
        <?php
        $pubs_par_annee = [];
        foreach ($publications as $pub) {
            $pubs_par_annee[$pub['annee']][] = $pub;
        }
        krsort($pubs_par_annee);
        ?>
        
        <div class="publications-list">
            <?php foreach ($pubs_par_annee as $annee => $pubs): ?>
            <div class="year-group">
                <h4 class="year-title">
                    <i class="fas fa-calendar-alt"></i> <?php echo $annee; ?>
                    <span class="count">(<?php echo count($pubs); ?>)</span>
                </h4>
                
                <?php foreach ($pubs as $pub): ?>
                <div class="publication-item">
                    <div class="pub-header">
                        <h5><?php echo h($pub['titre']); ?></h5>
                        <?php if ($pub['type_publication']): ?>
                        <span class="pub-type"><?php echo h($pub['type_publication']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="pub-authors">
                        <i class="fas fa-users"></i> <?php echo h($pub['auteurs']); ?>
                    </p>
                    
                    <div class="pub-details">
                        <?php if ($pub['revue']): ?>
                        <span class="detail-item">
                            <i class="fas fa-newspaper"></i> <?php echo h($pub['revue']); ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if ($pub['volume']): ?>
                        <span class="detail-item">Vol. <?php echo h($pub['volume']); ?></span>
                        <?php endif; ?>
                        
                        <?php if ($pub['pages']): ?>
                        <span class="detail-item">pp. <?php echo h($pub['pages']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($pub['resume']): ?>
                    <div class="pub-resume">
                        <p><?php echo h(truncate($pub['resume'], 200)); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="pub-actions">
                        <?php if ($pub['doi']): ?>
                        <a href="https://doi.org/<?php echo h($pub['doi']); ?>" 
                           target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-link"></i> DOI
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($pub['lien_pdf']): ?>
                        <a href="<?php echo h($pub['lien_pdf']); ?>" 
                           target="_blank" class="btn btn-sm btn-danger">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Bouton retour -->
    <div class="back-button">
        <a href="pages/equipe.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left"></i> Retour à l'équipe
        </a>
    </div>
</div>

<style>
.membre-detail {
    padding: 3rem 0;
}

.membre-profile {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 2rem;
    margin-bottom: 3rem;
}

.profile-header {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #f0f0f0;
}

.profile-photo img {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.profile-info h2 {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.specialite {
    font-size: 1.1rem;
    color: var(--secondary-color);
    margin: 1rem 0;
    font-weight: 500;
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    margin-top: 1.5rem;
}

.contact-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #555;
    text-decoration: none;
    font-size: 0.95rem;
    transition: color 0.3s;
}

.contact-link:hover {
    color: var(--primary-color);
}

.scholar-link {
    color: #4285F4;
    font-weight: 600;
}

.scholar-link:hover {
    color: #1976D2;
}

.profile-section {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e0e0e0;
}

.profile-section h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.text-content {
    line-height: 1.8;
    color: #555;
}

/* Projets */
.membre-projects {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 2rem;
    margin-bottom: 3rem;
}

.membre-projects h3 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.project-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    border-left: 4px solid var(--secondary-color);
    transition: transform 0.3s, box-shadow 0.3s;
}

.project-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.project-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.project-header h4 {
    color: var(--dark-text);
    font-size: 1.1rem;
    flex: 1;
}

.project-role {
    color: var(--secondary-color);
    margin-bottom: 1rem;
}

.project-description {
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.6;
}

/* Publications */
.membre-publications {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 2rem;
    margin-bottom: 3rem;
}

.membre-publications h3 {
    color: var(--primary-color);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.8rem;
}

.no-publications {
    text-align: center;
    padding: 3rem;
    color: #999;
}

.no-publications i {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: #ddd;
}

.pub-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: white;
}

.stat-item i {
    font-size: 2.5rem;
    opacity: 0.8;
}

.stat-item div strong {
    display: block;
    font-size: 1.8rem;
    font-weight: 700;
}

.stat-item div span {
    font-size: 0.9rem;
    opacity: 0.9;
}

.publications-list {
    margin-top: 2rem;
}

.year-group {
    margin-bottom: 3rem;
}

.year-title {
    color: var(--primary-color);
    font-size: 1.5rem;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 3px solid var(--primary-color);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.year-title .count {
    color: #999;
    font-size: 1rem;
    font-weight: normal;
}

.publication-item {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid var(--secondary-color);
    transition: transform 0.3s, box-shadow 0.3s;
}

.publication-item:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.pub-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.pub-header h5 {
    color: var(--dark-text);
    font-size: 1.15rem;
    line-height: 1.4;
    flex: 1;
}

.pub-type {
    background: var(--primary-color);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    white-space: nowrap;
}

.pub-authors {
    color: #666;
    margin-bottom: 0.8rem;
    font-style: italic;
}

.pub-details {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

.detail-item {
    color: #777;
    font-size: 0.9rem;
}

.pub-resume {
    background: white;
    padding: 1rem;
    border-radius: 5px;
    margin: 1rem 0;
}

.pub-resume p {
    color: #555;
    line-height: 1.6;
    margin: 0;
}

.pub-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.back-button {
    text-align: center;
    margin: 2rem 0;
}

@media (max-width: 768px) {
    .profile-header {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .profile-photo {
        display: flex;
        justify-content: center;
    }
    
    .contact-info {
        align-items: center;
    }
    
    .projects-grid {
        grid-template-columns: 1fr;
    }
    
    .pub-stats {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
function getCategoryColor($categorie) {
    $colors = [
        'Professeur' => 'danger',
        'Chercheur' => 'primary',
        'Doctorant' => 'warning',
        'Étudiant' => 'info'
    ];
    return $colors[$categorie] ?? 'secondary';
}

include 'footer.php';
?>
