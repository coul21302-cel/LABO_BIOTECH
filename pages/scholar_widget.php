<!-- 
    Widget à intégrer dans la page de détail d'un membre (equipe.php ou membre_detail.php)
-->

<?php if ($membre['google_scholar_id']): ?>
<div class="scholar-widget">
    <h3>
        <i class="fas fa-graduation-cap"></i> 
        Profil Google Scholar
    </h3>
    
    <div class="scholar-profile">
        <a href="https://scholar.google.com/citations?user=<?php echo h($membre['google_scholar_id']); ?>" 
           target="_blank" 
           class="btn btn-scholar">
            <i class="fab fa-google"></i> 
            Voir le profil complet sur Google Scholar
            <i class="fas fa-external-link-alt ml-2"></i>
        </a>
    </div>
    
    <!-- Iframe d'aperçu du profil Google Scholar -->
    <div class="scholar-preview mt-3">
        <iframe 
            src="https://scholar.google.com/citations?user=<?php echo h($membre['google_scholar_id']); ?>&hl=fr&view_op=list_works"
            width="100%" 
            height="500px" 
            frameborder="0"
            style="border: 1px solid #ddd; border-radius: 8px;">
        </iframe>
    </div>
</div>
<?php endif; ?>

<style>
.scholar-widget {
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-top: 2rem;
}

.scholar-widget h3 {
    color: #333;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.scholar-widget h3 i {
    color: #4285F4;
}

.scholar-profile {
    text-align: center;
    margin-bottom: 1.5rem;
}

.btn-scholar {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #4285F4 0%, #34A853 100%);
    color: white;
    padding: 1rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 15px rgba(66, 133, 244, 0.3);
}

.btn-scholar:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(66, 133, 244, 0.4);
    color: white;
}

.btn-scholar i.fab {
    font-size: 1.2em;
}

.scholar-preview {
    border-radius: 8px;
    overflow: hidden;
}

@media (max-width: 768px) {
    .scholar-preview iframe {
        height: 400px;
    }
}
</style>
