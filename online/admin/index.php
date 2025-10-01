
<?php

session_start();
require_once '../config.php'; // เชื่อมต่อฐานข้อมูล
require_once 'auth_admin.php'; // ตรวจสอบการเข้าสู่ระบบผู้ดูแลระบบ 

?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แผงควบคุมผู้ดูแลระบบ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Kanit', sans-serif;
        background-color: #f4f7f6;
    }
    .menu-card {
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none;
        color: #34495e;
        display: block;
        height: 100%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        background-color: #343a40;
        color: #fff;
    }
    .menu-card i {
        font-size: 3rem;
        margin-bottom: 15px;
    }
    .menu-card h5 {
        font-weight: 500;
        margin: 0;
    }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="p-4 mb-4 bg-light rounded-3">
        <div class="container-fluid py-3">
            <h1 class="display-5 fw-bold">Dashboard</h1>
            <p class="col-md-8 fs-4">ยินดีต้อนรับ, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! เลือกเมนูเพื่อเริ่มการจัดการระบบหลังบ้าน</p>
        </div>
    </div>
    
    <div class="row gy-4">
        <div class="col-md-6 col-lg-3">
            <a href="users.php" class="menu-card">
                <i class="bi bi-people-fill"></i>
                <h5>จัดการสมาชิก</h5>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="categories.php" class="menu-card">
                <i class="bi bi-tags-fill"></i>
                <h5>จัดการหมวดหมู่</h5>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="products.php" class="menu-card">
                <i class="bi bi-box-seam-fill"></i>
                <h5>จัดการสินค้า</h5>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="orders.php" class="menu-card">
                <i class="bi bi-receipt-cutoff"></i>
                <h5>จัดการคำสั่งซื้อ</h5>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
