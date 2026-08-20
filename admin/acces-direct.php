<?php
// ACCÈS DIRECT SANS LOGIN - TEMPORAIRE
// À SUPPRIMER après avoir réglé le problème de mot de passe !

session_start();

// Créer une session admin automatiquement
$_SESSION['admin_id'] = 1;
$_SESSION['admin_nom'] = 'Admin Système';
$_SESSION['admin_email'] = 'admin@labo-biotech.com';
$_SESSION['admin_role'] = 'super_admin';

// Rediriger vers le dashboard
header('Location: dashboard.php');
exit;
?>
