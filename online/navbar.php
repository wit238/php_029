<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-gem"></i> The Shop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="ms-auto">
                <?php if ($isLoggedIn): ?>
                    <span class="navbar-text me-3">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                    <a href="profile.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-person-circle"></i> ข้อมูลส่วนตัว</a>
                    <a href="cart.php" class="btn btn-dark btn-sm"><i class="bi bi-cart-fill"></i> ดูตะกร้า</a>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-dark btn-sm">เข้าสู่ระบบ</a>
                    <a href="register.php" class="btn btn-dark btn-sm">สมัครสมาชิก</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
