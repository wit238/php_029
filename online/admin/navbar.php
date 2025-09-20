<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth_admin.php'; // Ensure user is an admin
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><i class="bi bi-shield-lock-fill"></i> Admin Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users.php"><i class="bi bi-people-fill"></i> Members</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="categories.php"><i class="bi bi-tags-fill"></i> Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php"><i class="bi bi-box-seam-fill"></i> Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-receipt-cutoff"></i> Orders</a>
                </li>
            </ul>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
                </span>
                <a href="../logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>