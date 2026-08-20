<aside class="admin-sidebar">
    <div class="admin-logo">
        <i class="fas fa-dna"></i>
        <span>PlantTFDB Admin</span>
    </div>
    <nav class="admin-nav">
        <a href="dashboard.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'class="active"' : ''; ?>>
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="tf_manage.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'tf_manage.php') ? 'class="active"' : ''; ?>>
            <i class="fas fa-dna"></i> Manage TF
        </a>
        <a href="tf_add.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'tf_add.php') ? 'class="active"' : ''; ?>>
            <i class="fas fa-plus-circle"></i> Add TF
        </a>
        <a href="families_manage.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'families_manage.php') ? 'class="active"' : ''; ?>>
            <i class="fas fa-layer-group"></i> TF Families
        </a>
        <a href="species_manage.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'species_manage.php') ? 'class="active"' : ''; ?>>
            <i class="fas fa-seedling"></i> Species
        </a>
        <a href="import_csv.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'import_csv.php') ? 'class="active"' : ''; ?>>
            <i class="fas fa-file-upload"></i> Import Data
        </a>
        <hr>
        <a href="../index.php" target="_blank">
            <i class="fas fa-external-link-alt"></i> View Public Site
        </a>
        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
    <div class="admin-user">
        <i class="fas fa-user-circle"></i>
        <span><?php echo h(getLoggedInUser()); ?></span>
    </div>
</aside>
