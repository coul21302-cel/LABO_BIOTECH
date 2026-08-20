    </main>
    
    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-dna"></i> <?php echo SITE_NAME; ?></h3>
                    <p>Plant Transcription Factor Database - A comprehensive resource for plant TF research.</p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>/search.php">Search Database</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/browse.php">Browse by Family</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/statistics.php">Statistics</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/download.php">Download Data</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Database Info</h4>
                    <p><strong>Version:</strong> <?php echo VERSION; ?></p>
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT 
                            COUNT(*) as total_tf,
                            COUNT(DISTINCT species_id) as total_species,
                            COUNT(DISTINCT family_id) as total_families
                        FROM transcription_factors");
                        $stats = $stmt->fetch();
                        ?>
                        <p><strong>Total TF:</strong> <?php echo formatNumber($stats['total_tf']); ?></p>
                        <p><strong>Species:</strong> <?php echo formatNumber($stats['total_species']); ?></p>
                        <p><strong>Families:</strong> <?php echo formatNumber($stats['total_families']); ?></p>
                    <?php } catch(Exception $e) { } ?>
                </div>
                
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p><i class="fas fa-envelope"></i> contact@planttfdb.org</p>
                    <p><i class="fas fa-map-marker-alt"></i> Dakar, Sénégal</p>
                    <div class="social-links">
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="GitHub"><i class="fab fa-github"></i></a>
                        <a href="#" title="ResearchGate"><i class="fab fa-researchgate"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                <p>Powered by science and open data</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?php echo SITE_URL; ?>/assets/js/app.js"></script>
</body>
</html>
