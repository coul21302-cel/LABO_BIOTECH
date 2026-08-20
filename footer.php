    </main>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>
                        <img src="<?php echo SITE_URL; ?>/uploads/images/logo2.png?v=<?php echo time(); ?>" alt="Logo" class="footer-logo">
                        <?php echo SITE_NAME; ?>
                    </h3>
                    <p>Centre d'excellence en recherche biotechnologique pour le développement durable de l'agriculture tropicale.</p>
                    <div class="social-links">
                        <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        <a href="#" title="ResearchGate"><i class="fab fa-researchgate"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/pages/laboratoire.php">Le Laboratoire</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/equipe.php">Notre Équipe</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/recherche.php">Axes de Recherche</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/publications.php">Publications</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Recherche</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/pages/recherche.php#projets">Projets en cours</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/actualites.php">Actualités</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/partenaires.php">Nos Partenaires</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/galerie.php">Galerie Photos</a></li>
                    </ul>
                </div>
                
                <div class="footer-section footer-location">
                    <h4>Localisation</h4>
                    <div class="location-info">
                        <p><strong>Université Cheikh Anta Diop (UCAD)</strong></p>
                        <p>Faculté des Sciences et Techniques</p>
                        <p>Département de Biologie Végétale</p>
                        <p class="address-line">
                            <i class="fas fa-map-marker-alt"></i> 
                            BP 5005, Dakar-Fann, Sénégal
                        </p>
                        <p><i class="fas fa-phone"></i> +221 33 825 05 92</p>
                        <p><i class="fas fa-envelope"></i> lbv@ucad.edu.sn</p>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/pages/contact.php" class="btn btn-sm btn-outline-light mt-2">
                        <i class="fas fa-paper-plane"></i> Nous contacter
                    </a>
                </div>
            </div>
            
            <!-- Google Maps -->
            <div class="footer-map">
                <h4><i class="fas fa-map-marked-alt"></i> Où nous trouver ?</h4>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.9886637954614!2d-17.471652784669494!3d14.693775289743542!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xec173131c2f6907%3A0x44aa296d7598c54c!2sUniversit%C3%A9%20Cheikh%20Anta%20Diop%20de%20Dakar!5e0!3m2!1sfr!2ssn!4v1234567890123!5m2!1sfr!2ssn"
                        width="100%" 
                        height="300" 
                        style="border:0; border-radius: 10px;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tous droits réservés.</p>
                <p>
                    <div class="dev-credit-card">
    <div class="dev-card-header">
        <div class="dev-avatar">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="dev-title">
            <h4>OUSMANE BARRY</h4>
            <p>Bioinformaticien , Développeur</p>
        </div>
    </div>
    
    <div class="dev-card-body">
        <p class="dev-description">
            <i class="fas fa-lightbulb"></i> 
            Vous avez un projet web ? Je crée des sites professionnels adaptés à vos besoins.
        </p>
        
    
    </div>
    
    <div class="dev-card-footer">
        <a href="mailto:ob4365355@gmail.com" class="dev-contact-btn">
            <i class="fas fa-envelope"></i> ob4365355@gmail.com
        </a>
        <a href="tel:+221781201858" class="dev-contact-btn">
            <i class="fas fa-phone-alt"></i> +221 77 120 18 58
        </a>
        <a href="https://wa.me/221781201858" target="_blank" class="dev-contact-btn whatsapp-btn">
            <i class="fab fa-whatsapp"></i> WhatsApp Business
        </a>
    </div>
</div>
                    <a href="<?php echo SITE_URL; ?>/admin/login.php" class="admin-link">
                        <i class="fas fa-lock"></i> Espace Administrateur
                    </a>
                </p>
            </div>
        </div>
    </footer>
    
    <script src="<?php echo SITE_URL; ?>/js/script.js"></script>
</body>
</html>
