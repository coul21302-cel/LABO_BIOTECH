<?php
require_once '../db_config.php';

$page_title = 'Notre Équipe';
$current_page = 'equipe';

// Récupérer tous les membres par catégorie
$categories = ['Professeur', 'Chercheur', 'Doctorant', 'Étudiant'];
$membres_par_categorie = [];

foreach ($categories as $cat) {
    $stmt = $pdo->prepare("SELECT * FROM membres WHERE categorie = ? AND actif = 1 ORDER BY ordre_affichage, nom");
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
                    </div>
                    
                    <div class="membre-info">
                        <h3><?php echo h($membre['prenom'] . ' ' . $membre['nom']); ?></h3>
                        
                        <?php if ($membre['specialite']): ?>
                        <p class="specialite">
                            <i class="fas fa-microscope"></i> <?php echo h($membre['specialite']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if ($membre['biographie']): ?>
                        <p class="bio"><?php echo h(truncate($membre['biographie'], 120)); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($membre['email']): ?>
                        <p class="membre-email">
                            <i class="fas fa-envelope"></i> 
                            <a href="mailto:<?php echo h($membre['email']); ?>"><?php echo h($membre['email']); ?></a>
                        </p>
                        <?php endif; ?>
                        
                        <div class="membre-stats">
                            <?php if ($pub_count > 0): ?>
                            <span class="stat-item" title="Publications">
                                <i class="fas fa-book"></i> <?php echo $pub_count; ?> publication<?php echo $pub_count > 1 ? 's' : ''; ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Réseaux sociaux -->
                        <?php if (!empty($membre['researchgate']) || !empty($membre['google_scholar']) || !empty($membre['linkedin']) || !empty($membre['orcid'])): ?>
                        <div class="membre-social">
                            <?php if (!empty($membre['researchgate'])): ?>
                            <a href="<?php echo h($membre['researchgate']); ?>" target="_blank" title="ResearchGate" class="social-link">
                                <i class="fab fa-researchgate"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['google_scholar'])): ?>
                            <a href="<?php echo h($membre['google_scholar']); ?>" target="_blank" title="Google Scholar" class="social-link">
                                <i class="fas fa-graduation-cap"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['linkedin'])): ?>
                            <a href="<?php echo h($membre['linkedin']); ?>" target="_blank" title="LinkedIn" class="social-link">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['orcid'])): ?>
                            <a href="<?php echo h($membre['orcid']); ?>" target="_blank" title="ORCID" class="social-link">
                                <i class="fab fa-orcid"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($membre['site_web'])): ?>
                            <a href="<?php echo h($membre['site_web']); ?>" target="_blank" title="Site web" class="social-link">
                                <i class="fas fa-globe"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
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
    padding: 2.5rem;
    border-radius: 15px;
    margin-bottom: 3rem;
    text-align: center;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
}

.intro-section p {
    font-size: 1.2rem;
    line-height: 1.8;
    margin: 0;
    max-width: 900px;
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
    background: linear-gradient(135deg, var(--light-green), var(--light-blue));
}

.membre-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.membre-card:hover .membre-photo img {
    transform: scale(1.05);
}

.membre-info {
    padding: 1.5rem;
}

.membre-info h3 {
    margin-bottom: 0.5rem;
    font-size: 1.3rem;
    color: var(--dark-text);
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

.membre-email {
    margin: 0.75rem 0;
    font-size: 0.9rem;
}

.membre-email a {
    color: var(--secondary-color);
    text-decoration: none;
}

.membre-email a:hover {
    text-decoration: underline;
}

.membre-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin: 1rem 0;
    padding: 1rem 0;
    border-top: 1px solid #e0e0e0;
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

.membre-social {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e0e0e0;
    justify-content: center;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--light-gray);
    color: var(--gray-text);
    font-size: 1.1rem;
    transition: all 0.3s;
}

.social-link:hover {
    transform: scale(1.15);
}

.social-link .fa-researchgate {
    color: #00D0AF;
}

.social-link:hover .fa-researchgate {
    background: #00D0AF;
    color: white;
}

.social-link .fa-linkedin {
    color: #0077B5;
}

.social-link:hover .fa-linkedin {
    background: #0077B5;
    color: white;
}

.social-link .fa-graduation-cap {
    color: #4285F4;
}

.social-link:hover .fa-graduation-cap {
    background: #4285F4;
    color: white;
}

.social-link .fa-orcid {
    color: #A6CE39;
}

.social-link:hover .fa-orcid {
    background: #A6CE39;
    color: white;
}

.social-link .fa-globe {
    color: var(--primary-color);
}

.social-link:hover .fa-globe {
    background: var(--primary-color);
    color: white;
}

@media (max-width: 768px) {
    .intro-section {
        padding: 1.5rem;
    }
    
    .intro-section p {
        font-size: 1rem;
    }
    
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
