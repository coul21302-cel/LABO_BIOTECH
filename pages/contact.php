<?php
require_once '../db_config.php';
$current_page = 'contact';
$page_title = 'Contact';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($nom) || empty($email) || empty($message)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, sujet, message, adresse_ip) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $email, $sujet, $message, $_SERVER['REMOTE_ADDR']]);
            $success = 'Votre message a été envoyé avec succès. Nous vous répondrons dans les meilleurs délais.';
            
            // Réinitialiser les champs
            $nom = $email = $sujet = $message = '';
        } catch (PDOException $e) {
            $error = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
        }
    }
}

include '../header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Contactez-nous</h1>
        <div class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Accueil</a> / Contact
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info-section">
                <h2><i class="fas fa-info-circle"></i> Informations</h2>
                
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Adresse</h3>
                        <p>Laboratoire de Biotechnologie Végétale<br>
                        Université Cheikh Anta Diop<br>
                        Dakar, Sénégal</p>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h3>Téléphone</h3>
                        <p>+221 33 XXX XX XX<br>
                        +221 77 XXX XX XX</p>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p>contact@labo-biotech.sn<br>
                        info@labo-biotech.sn</p>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h3>Horaires</h3>
                        <p>Lundi - Vendredi: 8h00 - 17h00<br>
                        Samedi: 8h00 - 12h00</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form-section">
                <h2><i class="fas fa-paper-plane"></i> Envoyez-nous un message</h2>
                
                <?php if ($success): ?>
                <div class="alert alert-success"><?php echo h($success); ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo h($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" class="contact-form" data-validate>
                    <div class="form-group">
                        <label for="nom" class="form-label">Nom complet <span class="required">*</span></label>
                        <input type="text" id="nom" name="nom" class="form-control" 
                               value="<?php echo h($nom ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo h($email ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sujet" class="form-label">Sujet</label>
                        <input type="text" id="sujet" name="sujet" class="form-control" 
                               value="<?php echo h($sujet ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Message <span class="required">*</span></label>
                        <textarea id="message" name="message" class="form-control" rows="6" required><?php echo h($message ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Envoyer le message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 3rem;
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
}

.contact-info-section h2,
.contact-form-section h2 {
    font-size: 1.8rem;
    color: var(--primary-color);
    margin-bottom: 2rem;
}

.info-card {
    display: flex;
    gap: 1.5rem;
    background: #fff;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.info-icon {
    width: 60px;
    height: 60px;
    flex-shrink: 0;
    border-radius: 10px;
    background: var(--light-green);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: var(--primary-color);
}

.info-content h3 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    color: var(--dark-text);
}

.info-content p {
    color: var(--gray-text);
    line-height: 1.8;
}

.contact-form-section {
    background: #fff;
    padding: 2.5rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.required {
    color: #f44336;
}
</style>

<?php include '../footer.php'; ?>
