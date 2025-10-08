<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userNav = null;
$default_profile_image = 'img/default_avatar.png'; // New default image

if ($isLoggedIn) {
    try {
        $stmtNav = $conn->prepare("SELECT username, full_name, profile_image FROM users WHERE user_id = ?");
        $stmtNav->execute([$_SESSION['user_id']]);
        $userNav = $stmtNav->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Navbar user fetch failed: " . $e->getMessage());
        $userNav = ['username' => 'User', 'full_name' => 'ผู้ใช้งาน', 'profile_image' => null];
    }
}
?>
<style>
    .navbar-profile-img {
        width: 32px;
        height: 32px;
        object-fit: cover;
    }
    .navbar-brand {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        color: #E67E22 !important;
    }
</style>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm" style="z-index: 1030;">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-egg-fried me-2"></i>FindYourMeal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if ($isLoggedIn && $userNav): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= htmlspecialchars($userNav['profile_image'] ?? 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'%236c757d\'%3E%3Cpath d=\'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z\'/%3E%3C/svg%3E') ?>" alt="<?= htmlspecialchars($userNav['full_name']) ?>" class="rounded-circle navbar-profile-img me-2">
                            <?= htmlspecialchars($userNav['full_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle me-2"></i>โปรไฟล์ของฉัน</a></li>
                            <li><a class="dropdown-item" href="order.php"><i class="bi bi-receipt me-2"></i>ประวัติการสั่งซื้อ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-outline-secondary btn-sm">เข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="register.php" class="btn btn-primary btn-sm" style="background-color: #E67E22; border-color: #E67E22;">สมัครสมาชิก</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>