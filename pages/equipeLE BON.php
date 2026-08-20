<?php
require_once '../db_config.php';

$page_title = 'Notre Équipe';
$current_page = 'equipe';

// Récupérer tous les membres par catégorie
$categories = ['Professeur', 'Chercheur', 'Doctorant', 'Étudiant'];
$membres_par_categorie = [];

foreach ($categories as $cat) {
    $stmt = $pdo->prepare("SELECT * FROM membres WHERE categorie = ? ORDER BY ordre_affichage, nom");
    $stmt->execute([$cat]);
    $membres_par_categorie[$cat] = $stmt->fetchAll();
}

include '../header.php';
?>

<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-users"></i> Notre Équipe</h1>
        <p>Découvrez les chercheurs et étudiants qui font avancer la science</p>
    </div>
</div>

<div class="container equipe-page">
    <div class="intro-section">
        <p>
            Notre laboratoire réunit une équipe pluridisciplinaire de chercheurs, 
            doctorants et étudiants passionnés par la biotechnologie végétale. 
            Ensemble, nous travaillons sur des projets innovants pour l'amélioration 
            des cultures et la sécurité alimentaire.
        </p>
    </div>
    
    <?php foreach ($categories as $categorie): ?>
        <?php if (!empty($membres_par_categorie[$categorie])): ?>
        <div class="team-category">
            <h2 class="category-title">
                <i class="<?php echo getCategoryIcon($categorie); ?>"></i>
                <?php echo $categorie; ?>s
                <span class="count">(<?php echo count($membres_par_categorie[$categorie]); ?>)</span>
            </h2>
            
            <div class="membres-grid">
                <?php foreach ($membres_par_categorie[$categorie] as $membre): 
                    // Compter les publications du membre
                    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM publications WHERE membre_id = ?");
                    $stmt->execute([$membre['id']]);
                    $pub_count = $stmt->fetch()['total'];
                ?>
                <div class="membre-card">
                    <div class="membre-photo">
                        <img src="<?php echo UPLOAD_URL . h($membre['photo']); ?>" 
                             alt="<?php echo h($membre['prenom'] . ' ' . $membre['nom']); ?>"
                             onerror="this.src='<?php echo UPLOAD_URL; ?>default-avatar.png'">
                        <div class="photo-overlay">
                            <a href="../membre_profil.php?id=<?php echo $membre['id']; ?>" class="view-profile">
                                <i class="fas fa-eye"></i> Voir le profil
                            </a>
                        </div>
                    </div>
                    
                    <div class="membre-info">
                        <h3>
                            <a href="../membre_profil.php?id=<?php echo $membre['id']; ?>">
                                <?php echo h($membre['prenom'] . ' ' . $membre['nom']); ?>
                            </a>
                        </h3>
                        
                        <?php if ($membre['specialite']): ?>
                        <p class="specialite">
                            <i class="fas fa-microscope"></i> <?php echo h($membre['specialite']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if ($membre['biographie']): ?>
                        <p class="bio"><?php echo h(truncate($membre['biographie'], 120)); ?></p>
                        <?php endif; ?>
                        
                        <div class="membre-stats">
                            <?php if ($pub_count > 0): ?>
                            <span class="stat-item" title="Publications">
                                <i class="fas fa-book"></i> <?php echo $pub_count; ?> publication<?php echo $pub_count > 1 ? 's' : ''; ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php if ($membre['google_scholar_id']): ?>
                            <a href="https://scholar.google.com/citations?user=<?php echo h($membre['google_scholar_id']); ?>" 
                               target="_blank" class="stat-item scholar-link" title="Google Scholar">
                                <i class="fas fa-graduation-cap"></i> Scholar
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="membre-actions">
                            <a href="../membre_profil.php?id=<?php echo $membre['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-user"></i> Voir le profil complet
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<style>
.equipe-page {
    padding: 3rem 0;
}

.intro-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 3rem;
    text-align: center;
}

.intro-section p {
    font-size: 1.1rem;
    line-height: 1.8;
    margin: 0;
    max-width: 800px;
    margin: 0 auto;
}

.team-category {
    margin-bottom: 4rem;
}

.category-title {
    color: var(--primary-color);
    font-size: 2rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid var(--primary-color);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.category-title .count {
    color: #999;
    font-size: 1.2rem;
    font-weight: normal;
}

.membres-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
}

.membre-card {
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.membre-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.membre-photo {
    position: relative;
    overflow: hidden;
    height: 300px;
}

.membre-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.membre-card:hover .membre-photo img {
    transform: scale(1.1);
}

.photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.membre-card:hover .photo-overlay {
    opacity: 1;
}

.view-profile {
    color: white;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: 600;
    padding: 1rem 2rem;
    border: 2px solid white;
    border-radius: 50px;
    transition: all 0.3s;
}

.view-profile:hover {
    background: white;
    color: var(--primary-color);
}

.membre-info {
    padding: 1.5rem;
}

.membre-info h3 {
    margin-bottom: 0.5rem;
    font-size: 1.3rem;
}

.membre-info h3 a {
    color: var(--dark-text);
    text-decoration: none;
    transition: color 0.3s;
}

.membre-info h3 a:hover {
    color: var(--primary-color);
}

.specialite {
    color: var(--secondary-color);
    font-weight: 500;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.bio {
    color: #666;
    line-height: 1.6;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.membre-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin: 1rem 0;
    padding: 1rem 0;
    border-top: 1px solid #e0e0e0;
    border-bottom: 1px solid #e0e0e0;
}

.stat-item {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    color: #777;
    font-size: 0.9rem;
    text-decoration: none;
}

.stat-item i {
    color: var(--secondary-color);
}

.scholar-link {
    color: #4285F4;
    font-weight: 600;
}

.scholar-link:hover {
    color: #1976D2;
}

.membre-actions {
    margin-top: 1rem;
}

.membre-actions .btn {
    width: 100%;
    justify-content: center;
}

@media (max-width: 768px) {
    .membres-grid {
        grid-template-columns: 1fr;
    }
    
    .membre-photo {
        height: 250px;
    }
}
</style>

<?php
function getCategoryIcon($categorie) {
    $icons = [
        'Professeur' => 'fas fa-user-tie',
        'Chercheur' => 'fas fa-user-graduate',
        'Doctorant' => 'fas fa-user-clock',
        'Étudiant' => 'fas fa-user'
    ];
    return $icons[$categorie] ?? 'fas fa-user';
}

include '../footer.php';
?>
