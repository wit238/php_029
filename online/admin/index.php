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

    .content-container {
        background-color: #ffffff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-top: 50px;
        margin-bottom: 50px;
    }

    .header-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 2px solid #e0e0e0;
        margin-bottom: 20px;
    }

    h2 {
        color: #2c3e50;
        font-weight: 600;
        margin: 0;
    }

    .welcome-message {
        font-size: 1.1rem;
        color: #555;
    }

    .menu-card {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none;
        color: #34495e;
        display: block;
        height: 100%;
    }

    .menu-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        background-color: #3498db;
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

    .logout-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

</style>
</head>
<body>

<div class="container content-container">
    <div class="header-wrapper">
        <h2><i class="bi bi-shield-lock-fill"></i> แผงควบคุมผู้ดูแลระบบ</h2>
        <a href="../logout.php" class="btn btn-outline-danger logout-btn"><i class="bi bi-box-arrow-right"></i> ออกจากระบบ</a>
    </div>
    
    <p class="welcome-message mb-4">ยินดีต้อนรับ, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
    
    <div class="row gy-4">
        <div class="col-md-6 col-lg-3">
            <a href="users.php" class="menu-card">
                <i class="bi bi-people-fill"></i>
                <h5>จัดการสมาชิก</h5>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="#" class="menu-card"> <!-- Placeholder for categories.php -->
                <i class="bi bi-tags-fill"></i>
                <h5>จัดการหมวดหมู่</h5>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="#" class="menu-card"> <!-- Placeholder for products.php -->
                <i class="bi bi-box-seam-fill"></i>
                <h5>จัดการสินค้า</h5>
            </a>
        </div>
        <div class="col-md-6 col-lg-3">
            <a href="#" class="menu-card"> <!-- Placeholder for orders.php -->
                <i class="bi bi-receipt-cutoff"></i>
                <h5>จัดการคำสั่งซื้อ</h5>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>