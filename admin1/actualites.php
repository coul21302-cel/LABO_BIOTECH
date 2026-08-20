<?php include('includes/db.php'); include('includes/header.php'); ?>
<?php include('sidebar.php'); ?>

<main class="container">
    <h1 class="page-title">Actualités du Laboratoire</h1>

    <div class="news-list">
        <?php
        $news_query = $pdo->query("SELECT * FROM actualites ORDER BY date_publication DESC");
        while($news = $news_query->fetch()) {
            $date = date('d/m/Y', strtotime($news['date_publication']));
            ?>
            <article class="news-card">
                <div class="news-date"><?php echo $date; ?></div>
                <div class="news-content">
                    <span class="badge-cat"><?php echo $news['categorie']; ?></span>
                    <h3><?php echo $news['titre']; ?></h3>
                    <p><?php echo nl2br($news['contenu']); ?></p>
                </div>
            </article>
            <?php
        }
        ?>
    </div>
</main>

<style>
.news-list { display: flex; flex-direction: column; gap: 20px; margin-top: 30px; }
.news-card { 
    display: flex; gap: 20px; background: white; padding: 20px; 
    border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
}
.news-date { 
    min-width: 100px; font-weight: bold; color: #27ae60; 
    border-right: 2px solid #eee; display: flex; align-items: center; 
}
.badge-cat { 
    background: #e8f5e9; color: #2e7d32; padding: 2px 8px; 
    border-radius: 5px; font-size: 0.75rem; font-weight: bold; 
}
.news-content h3 { margin: 10px 0; color: #2c3e50; }

/* Adaptation Mobile */
@media (max-width: 600px) {
    .news-card { flex-direction: column; }
    .news-date { border-right: none; border-bottom: 2px solid #eee; padding-bottom: 10px; min-width: auto; }
}
</style>

<?php include('includes/footer.php'); ?>