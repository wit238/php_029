<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$product_id = $_GET['id'];

$stmt = $conn->prepare("SELECT p.*, c.category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    // Optional: Handle product not found
    header("Location: index.php");
    exit;
}

$isLoggedIn  = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['product_name']) ?> - The Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #2e2ebeff;
            font-family: 'Kanit', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            /* Removed vertical centering to allow content to flow */
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .navbar-brand {
            font-weight: 500;
        }

        .product-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .product-details-container {
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            margin: 2rem 0; /* Added margin for spacing */
        }

        .product-title {
            font-weight: 600;
        }

        .product-category {
            font-size: 1rem;
        }

        .product-price {
            font-size: 2.5rem;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .quantity-input {
            width: 80px;
            text-align: center;
        }

        .btn-add-to-cart {
            padding: 0.75rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 500;
            border-radius: 50px;
        }

        .footer {
            background-color: #343a40;
            color: #f8f9fa;
            padding: 2rem 0;
        }

        .star-rating {
            color: #ffc107;
        }

        .review-item {
            border-bottom: 1px solid #495057;
            padding: 1.5rem 0;
        }
        .review-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

    <?php require_once 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="product-details-container bg-dark text-light">
                <a href="index.php" class="btn btn-outline-light btn-sm mb-4"><i class="bi bi-arrow-left"></i> กลับไปหน้าร้าน</a>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <?php
                            // Define the placeholder image path
                            $placeholder_image = 'img/placeholder.png';
                            // Use the image_url from the database if available, otherwise use the placeholder
                            $product_image = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : $placeholder_image;
                        ?>
                        <img src="<?= $product_image ?>"
                             class="product-image"
                             alt="<?= htmlspecialchars($product['product_name']) ?>"
                             onerror="this.onerror=null; this.src='<?= $placeholder_image ?>';">
                    </div>
                    <div class="col-md-6">
                        <h1 class="product-title mt-4 mt-md-0"><?= htmlspecialchars($product['product_name']) ?></h1>
                        <p class="product-category text-white-50">หมวดหมู่: <?= htmlspecialchars($product['category_name']) ?></p>
                        <div class="star-rating mb-2">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                            <span class="text-white-50 ms-1">4.5 (142 รีวิว)</span>
                        </div>
                        <p class="lead mt-3"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        <p class="product-price">฿<?= number_format($product['price'], 2) ?></p>
                        
                        <?php if ($isLoggedIn): ?>
                            <form action="cart.php" method="post" class="mt-3">
                                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                <div class="d-flex align-items-center">
                                    <label for="quantity" class="me-3">จำนวน:</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control quantity-input me-3 bg-dark text-light" value="1" min="1" max="<?= (int)$product['stock'] ?>" required>
                                    <button type="submit" name="add_to_cart" class="btn btn-light btn-add-to-cart"><i class="bi bi-cart-plus-fill"></i> เพิ่มลงตะกร้า</button>
                                </div>
                                <p class="text-white-50 mt-2">มีสินค้าทั้งหมด: <?= htmlspecialchars($product['stock']) ?> ชิ้น</p>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-light mt-3"><i class="bi bi-info-circle-fill"></i> กรุณา <a href="login.php" class="alert-link">เข้าสู่ระบบ</a> เพื่อสั่งซื้อสินค้า</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Fake Reviews Section -->
                <hr class="my-4 border-secondary">
                <div class="reviews-section">
                    <h3 class="mb-4">รีวิวจากลูกค้า</h3>
                    
                    <div class="review-item d-flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-circle fs-2 text-white-50"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mt-0 mb-1">สมชาย ใจดี</h5>
                            <div class="star-rating mb-2">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            </div>
                            <p>สินค้าคุณภาพดีมากครับ จัดส่งรวดเร็ว แพ็คมาอย่างดี ไม่มีตำหนิเลย ประทับใจมากครับ</p>
                            <small class="text-white-50">รีวิวเมื่อ 2 วันที่แล้ว</small>
                        </div>
                    </div>

                    <div class="review-item d-flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-circle fs-2 text-white-50"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mt-0 mb-1">ลูกค้า The Shop</h5>
                            <div class="star-rating mb-2">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                            </div>
                            <p>โดยรวมแล้วดีครับ แต่คิดว่าขนาดเล็กไปนิดหน่อยเมื่อเทียบกับในรูป แต่คุณภาพวัสดุดีครับ</p>
                            <small class="text-white-50">รีวิวเมื่อ 1 สัปดาห์ที่แล้ว</small>
                        </div>
                    </div>

                    <div class="review-item d-flex">
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-circle fs-2 text-white-50"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mt-0 mb-1">ผู้ใช้งาน</h5>
                            <div class="star-rating mb-2">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i>
                            </div>
                            <p>สวยงามตามท้องเรื่อง</p>
                            <small class="text-white-50">รีวิวเมื่อ 3 สัปดาห์ที่แล้ว</small>
                        </div>
                    </div>

                </div>
                <!-- End Fake Reviews Section -->

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p>&copy; 664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
