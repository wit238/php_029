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
    <title><?= htmlspecialchars($product['product_name']) ?> - FindYourMeal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F8F9FA;
            font-family: 'Kanit', 'Poppins', sans-serif;
        }
        .product-image {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        .details-card {
            background-color: white;
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }
        .product-category {
            font-weight: 500;
            color: #E67E22;
        }
        .product-title {
            font-weight: 700;
        }
        .product-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #E67E22;
        }
        .star-rating { color: #ffc107; }
        .quantity-input-group {
            max-width: 150px;
        }
        .btn-primary {
            background-color: #E67E22;
            border-color: #E67E22;
        }
        .btn-primary:hover {
            background-color: #D35400;
            border-color: #D35400;
        }
        .review-item {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .review-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .footer { background-color: #343a40; color: #f8f9fa; padding: 2rem 0; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <?php require_once 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="container my-5 flex-grow-1">
        <div class="details-card">
            <div class="row g-5">
                <div class="col-lg-6">
                    <?php
                        $placeholder_image = 'https://via.placeholder.com/600x400.png?text=Food+Image';
                        $product_image = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : $placeholder_image;
                    ?>
                    <img src="<?= $product_image ?>" class="product-image" alt="<?= htmlspecialchars($product['product_name']) ?>" onerror="this.onerror=null; this.src='<?= $placeholder_image ?>';">
                </div>
                <div class="col-lg-6 d-flex flex-column">
                    <div>
                        <p class="product-category mb-2"><?= htmlspecialchars($product['category_name']) ?></p>
                        <h1 class="product-title display-5 mb-3"><?= htmlspecialchars($product['product_name']) ?></h1>
                        <div class="d-flex align-items-center mb-3">
                            <div class="star-rating">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                            </div>
                            <span class="text-muted ms-2">4.5 (142 รีวิว)</span>
                        </div>
                        <p class="lead text-muted mb-4"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        <p class="product-price mb-4">฿<?= number_format($product['price'], 2) ?></p>
                    </div>
                    
                    <div class="mt-auto">
                    <?php if ($isLoggedIn): ?>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            <div class="d-flex align-items-center">
                                <div class="input-group quantity-input-group me-3">
                                    <button class="btn btn-outline-secondary" type="button" id="button-minus">-</button>
                                    <input type="number" name="quantity" id="quantity" class="form-control text-center" value="1" min="1" max="<?= (int)$product['stock'] ?>" required>
                                    <button class="btn btn-outline-secondary" type="button" id="button-plus">+</button>
                                </div>
                                <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg flex-grow-1"><i class="bi bi-cart-plus-fill me-2"></i>เพิ่มลงตะกร้า</button>
                            </div>
                            <p class="text-muted mt-2"><small>มีในสต็อก: <?= htmlspecialchars($product['stock']) ?> ชิ้น</small></p>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-secondary mt-3"><i class="bi bi-info-circle-fill"></i> กรุณา <a href="login.php" class="alert-link">เข้าสู่ระบบ</a> เพื่อสั่งซื้อ</div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">รีวิวล่าสุด</h3>
                <div class="review-item">
                    <div class="d-flex align-items-center mb-2">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236c757d'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="Reviewer">
                        <h5 class="mb-0">นักชิมมืออาชีพ</h5>
                        <div class="star-rating ms-auto">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                    <p class="mb-1">รสชาติเข้มข้น จัดจ้าน ถึงเครื่องมากๆ ครับ ใครชอบอาหารใต้ต้องไม่พลาดเมนูนี้เลย</p>
                    <small class="text-muted">รีวิวเมื่อ 2 วันที่แล้ว</small>
                </div>
                <div class="review-item">
                    <div class="d-flex align-items-center mb-2">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236c757d'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="Reviewer">
                        <h5 class="mb-0">คุณพลอย</h5>
                        <div class="star-rating ms-auto">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                        </div>
                    </div>
                    <p class="mb-1">อร่อยมากค่ะ แต่แอบเผ็ดไปนิดนึงสำหรับคนทานเผ็ดไม่เก่ง บรรยากาศร้านดีมากค่ะ</p>
                    <small class="text-muted">รีวิวเมื่อ 1 สัปดาห์ที่แล้ว</small>
                </div>
                 <div class="review-item">
                    <div class="d-flex align-items-center mb-2">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%236c757d'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;" alt="Reviewer">
                        <h5 class="mb-0">John Doe</h5>
                        <div class="star-rating ms-auto">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star"></i>
                        </div>
                    </div>
                    <p class="mb-1">A must-try dish! The ingredients were fresh and the taste was perfectly balanced. Highly recommended.</p>
                    <small class="text-muted">รีวิวเมื่อ 3 สัปดาห์ที่แล้ว</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> FindYourMeal - 664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const minusButton = document.getElementById('button-minus');
        const plusButton = document.getElementById('button-plus');
        const quantityInput = document.getElementById('quantity');

        if (minusButton) {
            minusButton.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > quantityInput.min) {
                    quantityInput.value = currentValue - 1;
                }
            });
        }

        if (plusButton) {
            plusButton.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                const maxValue = parseInt(quantityInput.max);
                if (!isNaN(maxValue) && maxValue > 0) {
                    if (currentValue < maxValue) {
                        quantityInput.value = currentValue + 1;
                    }
                } else {
                    quantityInput.value = currentValue + 1;
                }
            });
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>