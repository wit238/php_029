<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// We need the database connection for the navbar now
require_once 'config.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userNav = null;
if ($isLoggedIn) {
    try {
        $stmtNav = $conn->prepare("SELECT username, full_name, profile_image FROM users WHERE user_id = ?");
        $stmtNav->execute([$_SESSION['user_id']]);
        $userNav = $stmtNav->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Gracefully handle DB error for navbar, don't crash the page
        error_log("Navbar user fetch failed: " . $e->getMessage());
        // Provide default values so the navbar doesn't break
        $userNav = ['username' => 'User', 'profile_image' => 'img/book.png'];
    }
}
?>
<style>
    .navbar-profile-img {
        width: 32px;
        height: 32px;
        object-fit: cover;
    }
</style>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-gem me-2"></i>The Shop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if ($isLoggedIn && $userNav): ?>
                    <li class="nav-item">
                        <a href="cart.php" class="nav-link me-2"><i class="bi bi-cart-fill"></i> ตะกร้าสินค้า</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?= htmlspecialchars($userNav['profile_image']) ?>?v=<?= time() ?>" alt="<?= htmlspecialchars($userNav['full_name']) ?>" class="rounded-circle navbar-profile-img me-2">
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
                        <a href="login.php" class="btn btn-outline-dark btn-sm">เข้าสู่ระบบ</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="register.php" class="btn btn-dark btn-sm">สมัครสมาชิก</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>