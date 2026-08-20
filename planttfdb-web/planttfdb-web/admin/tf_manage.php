<?php
require_once '../config/database.php';
require_once 'includes/auth.php';
require_once 'includes/admin_functions.php';
requireAuth();

$page_title = 'Manage Transcription Factors';
$success = '';
$error = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $tf_id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM transcription_factors WHERE tf_id = ?");
        $stmt->execute([$tf_id]);
        $success = 'Transcription factor deleted successfully';
        logAdminAction($pdo, 'DELETE_TF', "TF ID: $tf_id");
    } catch (Exception $e) {
        $error = 'Error deleting transcription factor';
    }
}

// Get all TF
$sql = "SELECT tf.*, s.scientific_name, f.family_code
        FROM transcription_factors tf
        JOIN species s ON tf.species_id = s.species_id
        JOIN tf_families f ON tf.family_id = f.family_id
        ORDER BY tf.date_added DESC";
$tfs = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?> - PlantTFDB Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/admin.css">
</head>
<body class="admin-body">
    <?php include 'includes/sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header">
            <h1><i class="fas fa-dna"></i> Manage Transcription Factors</h1>
            <div class="admin-actions">
                <a href="tf_add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New TF
                </a>
            </div>
        </div>
        
        <?php if ($success): ?>
        <div class="alert-success"><?php echo h($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert-error"><?php echo h($error); ?></div>
        <?php endif; ?>
        
        <div class="data-table-admin">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gene Name</th>
                        <th>Gene ID</th>
                        <th>Species</th>
                        <th>Family</th>
                        <th>Length (AA)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tfs as $tf): ?>
                    <tr>
                        <td><?php echo $tf['tf_id']; ?></td>
                        <td><strong><?php echo h($tf['gene_name'] ?: '-'); ?></strong></td>
                        <td><code><?php echo h($tf['gene_id']); ?></code></td>
                        <td><em><?php echo h($tf['scientific_name']); ?></em></td>
                        <td><?php echo getFamilyBadge($tf['family_code']); ?></td>
                        <td><?php echo $tf['protein_length'] ? formatNumber($tf['protein_length']) : '-'; ?></td>
                        <td>
                            <a href="tf_edit.php?id=<?php echo $tf['tf_id']; ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?delete=<?php echo $tf['tf_id']; ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Delete this TF?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
