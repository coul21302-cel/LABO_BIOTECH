<?php
require_once '../db_config.php';
$current_page = 'alumni';
$page_title = 'Nos Alumni';

// Récupérer tous les alumni actifs
$stmt = $pdo->query("SELECT * FROM alumni WHERE actif = 1 ORDER BY periode_fin DESC, nom");
$tous_alumni = $stmt->fetchAll();

// Statistiques
$total_alumni = count($tous_alumni);
$pays_uniques = array_unique(array_filter(array_column($tous_alumni, 'pays_actuel')));
$nb_pays = count($pays_uniques);

include '../header.php';
?>

<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-graduate"></i> Nos Alumni</h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Accueil</a> / Alumni
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section-header text-center">
            <p class="section-subtitle">
                Nos anciens membres poursuivent des carrières brillantes dans le monde entier
            </p>
            <div class="alumni-stats">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <strong><?php echo $total_alumni; ?></strong>
                    <span>Alumni</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-globe-africa"></i>
                    <strong><?php echo $nb_pays; ?></strong>
                    <span>Pays</span>
                </div>
            </div>
        </div>
        
        <?php if (count($tous_alumni) > 0): ?>
        
        <!-- Filtres -->
        <div class="alumni-filters">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-globe"></i> Tous
            </button>
            <button class="filter-btn" data-filter="Doctorant">
                <i class="fas fa-graduation-cap"></i> Doctorants
            </button>
            <button class="filter-btn" data-filter="Post-doc">
                <i class="fas fa-flask"></i> Post-docs
            </button>
            <button class="filter-btn" data-filter="Chercheur">
                <i class="fas fa-microscope"></i> Chercheurs
            </button>
        </div>
        
        <!-- Grille des alumni -->
        <div class="alumni-grid">
            <?php foreach ($tous_alumni as $alumnus): ?>
            <div class="alumni-card" data-status="<?php echo h($alumnus['statut_labo']); ?>">
                <div class="alumni-photo">
                    <?php if ($alumnus['photo']): ?>
                    <img src="<?php echo UPLOAD_URL . h($alumnus['photo']); ?>" alt="<?php echo h($alumnus['prenom'] . ' ' . $alumnus['nom']); ?>">
                    <?php else: ?>
                    <div class="alumni-photo-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                    <?php endif; ?>
                    <div class="alumni-badge">
                        <?php echo h($alumnus['statut_labo']); ?>
                    </div>
                </div>
                
                <div class="alumni-info">
                    <h3><?php echo h($alumnus['prenom'] . ' ' . $alumnus['nom']); ?></h3>
                    
                    <?php if ($alumnus['periode_debut'] || $alumnus['periode_fin']): ?>
                    <p class="alumni-period">
                        <i class="fas fa-calendar"></i>
                        <?php 
                        echo $alumnus['periode_debut'] ? date('Y', strtotime($alumnus['periode_debut'])) : '?';
                        echo ' - ';
                        echo $alumnus['periode_fin'] ? date('Y', strtotime($alumnus['periode_fin'])) : '?';
                        ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if ($alumnus['domaine_specialisation']): ?>
                    <p class="alumni-specialization">
                        <i class="fas fa-atom"></i>
                        <?php echo h($alumnus['domaine_specialisation']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <div class="alumni-current">
                        <?php if ($alumnus['poste_actuel']): ?>
                        <p class="current-position">
                            <i class="fas fa-briefcase"></i>
                            <strong><?php echo h($alumnus['poste_actuel']); ?></strong>
                        </p>
                        <?php endif; ?>
                        
                        <?php if ($alumnus['organisation_actuelle']): ?>
                        <p class="current-org">
                            <i class="fas fa-building"></i>
                            <?php echo h($alumnus['organisation_actuelle']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if ($alumnus['ville_actuelle'] || $alumnus['pays_actuel']): ?>
                        <p class="current-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php 
                            echo h($alumnus['ville_actuelle']); 
                            if ($alumnus['ville_actuelle'] && $alumnus['pays_actuel']) echo ', ';
                            echo h($alumnus['pays_actuel']); 
                            ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($alumnus['these_titre']): ?>
                    <div class="alumni-thesis">
                        <p><strong>Thèse :</strong></p>
                        <p class="thesis-title"><?php echo h($alumnus['these_titre']); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($alumnus['testimonial']): ?>
                    <div class="alumni-testimonial">
                        <i class="fas fa-quote-left"></i>
                        <p><?php echo h(truncate($alumnus['testimonial'], 150)); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Réseaux sociaux -->
                    <div class="alumni-social">
                        <?php if ($alumnus['afficher_contact'] && $alumnus['email']): ?>
                        <a href="mailto:<?php echo h($alumnus['email']); ?>" title="Email">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($alumnus['linkedin']): ?>
                        <a href="<?php echo h($alumnus['linkedin']); ?>" target="_blank" title="LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($alumnus['researchgate']): ?>
                        <a href="<?php echo h($alumnus['researchgate']); ?>" target="_blank" title="ResearchGate">
                            <i class="fab fa-researchgate"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($alumnus['google_scholar']): ?>
                        <a href="<?php echo h($alumnus['google_scholar']); ?>" target="_blank" title="Google Scholar">
                            <i class="fas fa-graduation-cap"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($alumnus['site_web_perso']): ?>
                        <a href="<?php echo h($alumnus['site_web_perso']); ?>" target="_blank" title="Site web">
                            <i class="fas fa-globe"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        <p class="text-center text-muted">Aucun alumni enregistré pour le moment.</p>
        <?php endif; ?>
        
        <!-- Section Rejoindre le réseau -->
        <div class="alumni-network-cta">
            <div class="cta-content">
                <i class="fas fa-network-wired"></i>
                <h2>Rejoignez notre réseau Alumni</h2>
                <p>Vous êtes un ancien membre du laboratoire ? Partagez votre parcours avec nous !</p>
                <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> Nous contacter
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.alumni-stats {
    display: flex;
    justify-content: center;
    gap: 3rem;
    margin: 2rem 0;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.stat-item i {
    font-size: 2.5rem;
    color: var(--primary-color);
}

.stat-item strong {
    font-size: 2rem;
    color: var(--dark-text);
}

.stat-item span {
    color: var(--gray-text);
    font-size: 0.9rem;
}

.alumni-filters {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin: 2rem 0;
    flex-wrap: wrap;
}

.filter-btn {
    background: var(--white);
    border: 2px solid var(--border-color);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-btn:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.filter-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.alumni-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.alumni-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s;
}

.alumni-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.alumni-photo {
    position: relative;
    height: 250px;
    background: linear-gradient(135deg, var(--light-green), var(--light-blue));
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.alumni-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.alumni-photo-placeholder {
    font-size: 5rem;
    color: white;
    opacity: 0.5;
}

.alumni-badge {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    background: var(--primary-color);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.alumni-info {
    padding: 1.5rem;
}

.alumni-info h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    font-size: 1.3rem;
}

.alumni-period,
.alumni-specialization,
.current-position,
.current-org,
.current-location {
    font-size: 0.9rem;
    color: var(--gray-text);
    margin: 0.5rem 0;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.alumni-period i,
.alumni-specialization i,
.current-position i,
.current-org i,
.current-location i {
    color: var(--primary-color);
    margin-top: 0.2rem;
}

.current-position strong {
    color: var(--dark-text);
}

.alumni-current {
    background: var(--light-gray);
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
}

.alumni-thesis {
    margin: 1rem 0;
    padding: 1rem;
    background: var(--light-blue);
    border-left: 4px solid var(--secondary-color);
    border-radius: 5px;
}

.thesis-title {
    font-style: italic;
    color: var(--dark-text);
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.alumni-testimonial {
    margin: 1rem 0;
    padding: 1rem;
    background: var(--light-green);
    border-radius: 8px;
    position: relative;
}

.alumni-testimonial i {
    color: var(--primary-color);
    opacity: 0.3;
    font-size: 1.5rem;
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
}

.alumni-testimonial p {
    font-size: 0.9rem;
    line-height: 1.6;
    color: var(--dark-text);
    margin-left: 1.5rem;
}

.alumni-social {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.alumni-social a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: var(--light-gray);
    color: var(--gray-text);
    transition: all 0.3s;
}

.alumni-social a:hover {
    background: var(--primary-color);
    color: white;
    transform: scale(1.1);
}

.alumni-network-cta {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 3rem;
    border-radius: 15px;
    text-align: center;
    margin-top: 3rem;
}

.cta-content i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.8;
}

.cta-content h2 {
    color: white;
    margin-bottom: 1rem;
}

.cta-content p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.cta-content .btn {
    background: white;
    color: var(--primary-color);
}

.cta-content .btn:hover {
    background: var(--light-gray);
}

@media (max-width: 768px) {
    .alumni-grid {
        grid-template-columns: 1fr;
    }
    
    .alumni-stats {
        gap: 1.5rem;
    }
    
    .filter-btn {
        font-size: 0.9rem;
        padding: 0.6rem 1.2rem;
    }
}
</style>

<script>
// Filtrage des alumni
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const alumniCards = document.querySelectorAll('.alumni-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Activer le bouton cliqué
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Filtrer les cartes
            alumniCards.forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php include '../footer.php'; ?>
