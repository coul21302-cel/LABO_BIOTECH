<?php
require_once '../db_config.php';
$current_page = 'laboratoire';
$page_title = 'Le Laboratoire';
include '../header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Le Laboratoire</h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Accueil</a> / Le Laboratoire
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <!-- Historique -->
        <div class="section-content mb-5">
            <h2 class="mb-3"><i class="fas fa-history"></i> Historique</h2>
            <div class="content-box">
                <p>Le Laboratoire de Biotechnologie Végétale (LBV) a été fondé en <strong>2005</strong> dans le cadre d'un partenariat stratégique entre l'Université Cheikh Anta Diop de Dakar et plusieurs institutions internationales de recherche.</p>
                
                <p>Depuis sa création, le laboratoire s'est imposé comme un <strong>centre d'excellence régional</strong> en biotechnologie végétale, contribuant significativement à la recherche appliquée pour l'amélioration des cultures tropicales.</p>
                
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-year">2005</div>
                        <div class="timeline-content">
                            <h4>Création du laboratoire</h4>
                            <p>Inauguration officielle avec les premières installations de culture in vitro et de biologie moléculaire.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-year">2010</div>
                        <div class="timeline-content">
                            <h4>Extension des infrastructures</h4>
                            <p>Acquisition d'équipements de génomique et création d'une serre expérimentale.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-year">2015</div>
                        <div class="timeline-content">
                            <h4>Certification ISO</h4>
                            <p>Obtention de la certification ISO 9001 pour la qualité des processus de recherche.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-year">2020</div>
                        <div class="timeline-content">
                            <h4>Réseau régional</h4>
                            <p>Création du réseau ouest-africain de biotechnologie végétale (ROBV).</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mission -->
        <div class="section-content mb-5">
            <h2 class="mb-3"><i class="fas fa-bullseye"></i> Notre Mission</h2>
            <div class="mission-grid">
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-dna"></i>
                    </div>
                    <h3>Recherche d'Excellence</h3>
                    <p>Mener des recherches de pointe en biotechnologie végétale pour développer des variétés améliorées adaptées aux conditions locales.</p>
                </div>
                
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Formation</h3>
                    <p>Former la prochaine génération de chercheurs et techniciens en biotechnologie végétale à travers des programmes de Master et Doctorat.</p>
                </div>
                
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Partenariats</h3>
                    <p>Développer des collaborations avec des institutions académiques et des organismes de développement pour maximiser l'impact de nos recherches.</p>
                </div>
                
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3>Innovation Durable</h3>
                    <p>Contribuer à la sécurité alimentaire et au développement agricole durable en Afrique de l'Ouest.</p>
                </div>
            </div>
        </div>
        
        <!-- Infrastructures -->
        <div class="section-content">
            <h2 class="mb-3"><i class="fas fa-building"></i> Infrastructures</h2>
            <div class="infrastructure-grid">
                <div class="infra-card">
                    <i class="fas fa-flask"></i>
                    <h4>Laboratoire de culture in vitro</h4>
                    <p>Salle stérile équipée de 8 postes de culture sous flux laminaire, autoclave, et système de contrôle climatique.</p>
                </div>
                
                <div class="infra-card">
                    <i class="fas fa-microscope"></i>
                    <h4>Laboratoire de biologie moléculaire</h4>
                    <p>Équipements d'extraction d'ADN/ARN, PCR (thermocycleurs standards et en temps réel), électrophorèse, et séquenceur Illumina.</p>
                </div>
                
                <div class="infra-card">
                    <i class="fas fa-seedling"></i>
                    <h4>Serre expérimentale</h4>
                    <p>Serre climatisée de 500 m² pour l'évaluation phénotypique des plantes transgéniques et mutantes.</p>
                </div>
                
                <div class="infra-card">
                    <i class="fas fa-bacteria"></i>
                    <h4>Laboratoire de microbiologie</h4>
                    <p>Installation dédiée à l'isolement et la caractérisation des microorganismes d'intérêt agricole.</p>
                </div>
                
                <div class="infra-card">
                    <i class="fas fa-temperature-low"></i>
                    <h4>Chambre froide et congélateurs</h4>
                    <p>Systèmes de conservation à -20°C, -80°C et azote liquide pour la préservation du matériel biologique.</p>
                </div>
                
                <div class="infra-card">
                    <i class="fas fa-desktop"></i>
                    <h4>Salle de bioinformatique</h4>
                    <p>8 postes informatiques hautes performances avec logiciels d'analyse de données génomiques.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.content-box {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.content-box p {
    margin-bottom: 1.5rem;
    line-height: 1.8;
    font-size: 1.05rem;
}

.timeline {
    position: relative;
    padding: 2rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 80px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--primary-color);
}

.timeline-item {
    display: flex;
    margin-bottom: 2rem;
    position: relative;
}

.timeline-year {
    width: 80px;
    flex-shrink: 0;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-color);
}

.timeline-content {
    flex: 1;
    background: var(--light-green);
    padding: 1.5rem;
    border-radius: 10px;
    margin-left: 2rem;
    position: relative;
}

.timeline-content::before {
    content: '';
    position: absolute;
    left: -10px;
    top: 1.5rem;
    width: 10px;
    height: 10px;
    background: var(--primary-color);
    border-radius: 50%;
}

.timeline-content h4 {
    margin-bottom: 0.5rem;
    color: var(--dark-text);
}

.mission-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.mission-card {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.mission-card:hover {
    transform: translateY(-5px);
}

.mission-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: var(--light-green);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: var(--primary-color);
}

.mission-card h3 {
    font-size: 1.3rem;
    margin-bottom: 1rem;
}

.infrastructure-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.infra-card {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid var(--primary-color);
}

.infra-card i {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.infra-card h4 {
    margin-bottom: 1rem;
    color: var(--dark-text);
}

.infra-card p {
    color: var(--gray-text);
    font-size: 0.95rem;
}

.mb-3 { margin-bottom: 1.5rem; }
.mb-5 { margin-bottom: 3rem; }
</style>

<?php include '../footer.php'; ?>
