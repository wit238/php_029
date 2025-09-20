<?php
session_start(); // เริ่มต้น session เพื่อจัดการการเข้าสู่ระบบ
require_once 'config.php';

// ดึงข้อมูลสินค้าจากฐานข้อมูล 6 ชิ้นล่าสุด และจัดกลุ่มเพื่อป้องกันการซ้ำกัน
$stmt = $conn->query("SELECT p.*, c.category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
GROUP BY p.product_id
ORDER BY p.created_at DESC
LIMIT 6");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isLoggedIn  = isset($_SESSION['user_id']); // ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือไม่

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - ร้านค้าออนไลน์</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #e4e6e7ff;
            font-family: 'Kanit', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .navbar-brand {
            font-weight: 500;
        }

        .product-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden; /* Important for the zoom effect */
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .product-img {
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease; /* Add transition for smooth zoom */
        }

        .product-card:hover .product-img {
            transform: scale(1.05); /* Zoom in on hover */
        }

        .product-img-placeholder {
            height: 200px;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-weight: 500;
            color: #212529;
        }

        .card-subtitle {
            color: #6c757d;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: 500;
            color: #343a40;
        }

        .btn-outline-dark {
            border-radius: 20px;
        }

        .btn-dark {
            border-radius: 20px;
        }

        .footer {
            background-color: #343a40;
            color: #f8f9fa;
            padding: 2rem 0;
        }
    </style>
</head>
<body>

    <?php require_once 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container py-5">
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card product-card h-100">
                            <?php
                                // กำหนด path ของรูปภาพสำรอง
                                $placeholder_image = 'img/placeholder.png';
                                // ใช้ path จากฐานข้อมูลโดยตรง ถ้ามี, ถ้าไม่มีก็ใช้ placeholder
                                $product_image = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : $placeholder_image;
                            ?>
                            <img src="<?= $product_image ?>"
                                 class="card-img-top product-img"
                                 alt="<?= htmlspecialchars($product['product_name']) ?>"
                                 onerror="this.onerror=null; this.src='<?= $placeholder_image ?>';">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($product['category_name']) ?></h6>
                                <p class="card-text text-muted small flex-grow-1"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="product-price mb-0">฿<?= number_format($product['price'], 2) ?></p>
                                    <a href="product_detail.php?id=<?=$product['product_id'] ?>" class="btn btn-outline-dark btn-sm">ดูรายละเอียด</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p>&copy; 664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>