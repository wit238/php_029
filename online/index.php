<?php
session_start(); // เริ่มต้น session เพื่อจัดการการเข้าสู่ระบบ
require_once 'config.php';

// ดึงข้อมูลสินค้าจากฐานข้อมูล
$stmt = $conn->query("SELECT p.*, c.category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$IsLogin  = isset($_SESSION['user_id']); // ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือไม่

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

</head>
<style>
     body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
        background-image: url('img/002.gif');
        background-size: cover;
        background-repeat: no-repeat;
    }
    .container {
        max-width: 1500px;
        /* margin-top: 100px; */
        background-color: rgba(255, 255, 255, 0.8);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
     } 
  
</style>
<body>
<div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>รายการสินค้า</h1>
        <div>

            <?php
                if ($IsLogin): ?>
                    <span class="me-3">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?> (<?=$_SESSION['role'] ?>)</span>
                    <a href="profile.php" class="btn btn-info">ข้อมูลส่วนตัว</a>
                    <a href="cart.php" class="btn btn-warning">ดูตะกร้า</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-success">เข้าสู่ระบบ</a>
                        <a href="register.php" class="btn btn-primary">สมัครสมาชิก</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- รายการสินค้าที่แสดง -->
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($product['category_name']) ?></h6>
                                    <p class="card-text"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                                    <p><strong>ราคา:</strong> <?= number_format($product['price'], 2) ?> บาท</p>
                                    <?php if ($IsLogin): ?>
                                        
                                        <form action="cart.php" method="post" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?= $product['product_id']?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-success">เพิ่มในตะกร้า</button>
                                        </form>
                                        <?php else: ?>
                                            <small class="text-muted">เข้าสู่ระบบเพื่อสั่งซื้อ</small>
                                            <?php endif; ?>
                                            <a href="product_detail.php?id=<?=$product['product_id'] ?>"
                                            class="btn btn-sm btn-outline-primary floatend">ดูรายละเอียด</a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <a href="logout.php" class="btn btn-danger ">ออกจากระบบ</a>
                            </div>
                            
                            
                            
                            
                        </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</html>