<?php
require_once '../db_config.php';
$current_page = 'recherche';
$page_title = 'Recherche';

// Récupérer les projets par statut
$stmt = $pdo->query("SELECT * FROM projets ORDER BY statut, ordre_affichage");
$projets = $stmt->fetchAll();

// Grouper par statut
$projets_en_cours = [];
$projets_termines = [];
$projets_planifies = [];

foreach ($projets as $projet) {
    if ($projet['statut'] == 'En cours') {
        $projets_en_cours[] = $projet;
    } elseif ($projet['statut'] == 'Terminé') {
        $projets_termines[] = $projet;
    } else {
        $projets_planifies[] = $projet;
    }
}

include '../header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Recherche</h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Accueil</a> / Recherche
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Axes de recherche -->
        <div class="section-content mb-5">
            <h2 class="mb-3"><i class="fas fa-atom"></i> Nos Axes de Recherche</h2>
            <div class="research-axes">
                <div class="axis-card">
                    <div class="axis-icon"><i class="fas fa-dna"></i></div>
                    <h3>Amélioration Génétique</h3>
                    <p>Sélection assistée par marqueurs moléculaires, cartographie génétique, identification de QTLs pour des caractères d'intérêt agronomique.</p>
                </div>
                
                <div class="axis-card">
                    <div class="axis-icon"><i class="fas fa-flask"></i></div>
                    <h3>Culture In Vitro</h3>
                    <p>Micropropagation, embryogenèse somatique, conservation de germoplasme, transformation génétique des plantes.</p>
                </div>
                
                <div class="axis-card">
                    <div class="axis-icon"><i class="fas fa-bacteria"></i></div>
                    <h3>Microbiologie Végétale</h3>
                    <p>Symbioses plantes-microorganismes, biostimulants, biocontrôle, caractérisation de souches PGPR et rhizobiennes.</p>
                </div>
                
                <div class="axis-card">
                    <div class="axis-icon"><i class="fas fa-leaf"></i></div>
                    <h3>Physiologie du Stress</h3>
                    <p>Réponses des plantes aux stress abiotiques (sécheresse, salinité, chaleur) et biotiques (maladies, ravageurs).</p>
                </div>
            </div>
        </div>
        
        <!-- Projets en cours -->
        <?php if (count($projets_en_cours) > 0): ?>
        <div class="section-content mb-5" id="projets">
            <h2 class="mb-3">
                <i class="fas fa-project-diagram"></i> 
                Projets en Cours (<?php echo count($projets_en_cours); ?>)
            </h2>
            
            <?php foreach ($projets_en_cours as $projet): ?>
            <div class="project-card">
                <div class="project-header">
                    <h3 class="project-title"><?php echo h($projet['titre']); ?></h3>
                    <span class="badge badge-success"><?php echo h($projet['statut']); ?></span>
                </div>
                
                <?php if ($projet['date_debut']): ?>
                <p class="project-dates">
                    <i class="fas fa-calendar"></i> 
                    Début: <?php echo formatDate($projet['date_debut']); ?>
                    <?php if ($projet['date_fin']): ?>
                    - Fin prévue: <?php echo formatDate($projet['date_fin']); ?>
                    <?php endif; ?>
                </p>
                <?php endif; ?>
                
                <p class="project-description"><?php echo nl2br(h($projet['description'])); ?></p>
                
                <?php if (!empty($projet['objectifs'])): ?>
                <div class="project-objectives">
                    <strong><i class="fas fa-bullseye"></i> Objectifs:</strong>
                    <p><?php echo nl2br(h($projet['objectifs'])); ?></p>
                </div>
                <?php endif; ?>
                
                <?php
                // Récupérer les membres du projet
                $stmt = $pdo->prepare("
                    SELECT m.*, pm.role 
                    FROM membres m 
                    JOIN projet_membres pm ON m.id = pm.membre_id 
                    WHERE pm.projet_id = ?
                    ORDER BY pm.id
                ");
                $stmt->execute([$projet['id']]);
                $membres_projet = $stmt->fetchAll();
                
                if (count($membres_projet) > 0):
                ?>
                <div class="project-team">
                    <h4><i class="fas fa-users"></i> Équipe:</h4>
                    <div class="team-tags">
                        <?php foreach ($membres_projet as $membre): ?>
                        <span class="team-tag">
                            <?php echo h($membre['prenom'] . ' ' . $membre['nom']); ?>
                            <?php if ($membre['role']): ?>
                            (<?php echo h($membre['role']); ?>)
                            <?php endif; ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php
                // Récupérer les publications associées
                $stmt = $pdo->prepare("SELECT * FROM publications WHERE projet_id = ? ORDER BY annee DESC");
                $stmt->execute([$projet['id']]);
                $publications = $stmt->fetchAll();
                
                if (count($publications) > 0):
                ?>
                <div class="project-publications">
                    <h4><i class="fas fa-book"></i> Publications:</h4>
                    <ul>
                        <?php foreach ($publications as $pub): ?>
                        <li>
                            <?php echo h($pub['auteurs']); ?> (<?php echo h($pub['annee']); ?>). 
                            <em><?php echo h($pub['titre']); ?></em>
                            <?php if ($pub['revue']): ?>
                            . <?php echo h($pub['revue']); ?>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Projets terminés -->
        <?php if (count($projets_termines) > 0): ?>
        <div class="section-content">
            <h2 class="mb-3">
                <i class="fas fa-check-circle"></i> 
                Projets Terminés (<?php echo count($projets_termines); ?>)
            </h2>
            
            <?php foreach ($projets_termines as $projet): ?>
            <div class="project-card project-completed">
                <div class="project-header">
                    <h3 class="project-title"><?php echo h($projet['titre']); ?></h3>
                    <span class="badge badge-info"><?php echo h($projet['statut']); ?></span>
                </div>
                
                <p class="project-dates">
                    <i class="fas fa-calendar"></i> 
                    <?php echo formatDate($projet['date_debut']); ?> - 
                    <?php echo formatDate($projet['date_fin']); ?>
                </p>
                
                <p class="project-description"><?php echo h(truncate($projet['description'], 250)); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.research-axes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.axis-card {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.axis-card:hover {
    transform: translateY(-5px);
}

.axis-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: var(--light-blue);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--secondary-color);
}

.axis-card h3 {
    font-size: 1.3rem;
    margin-bottom: 1rem;
    color: var(--dark-text);
}

.project-description {
    line-height: 1.8;
    margin: 1rem 0;
}

.project-objectives {
    margin-top: 1.5rem;
    padding: 1rem;
    background: var(--light-green);
    border-radius: 5px;
}

.project-objectives strong {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--primary-color);
}

.project-publications {
    margin-top: 1.5rem;
}

.project-publications h4 {
    margin-bottom: 1rem;
    color: var(--primary-color);
}

.project-publications ul {
    list-style: none;
    padding-left: 0;
}

.project-publications li {
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border-color);
    line-height: 1.6;
}

.project-completed {
    opacity: 0.85;
    border-left: 4px solid var(--secondary-color);
}
</style>

<?php include '../footer.php'; ?>
