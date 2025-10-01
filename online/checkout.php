<?php
session_start();
require_once 'config.php';

// 1. Security: Redirect if not logged in or cart is empty
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

// 2. Data Fetching: Get full product details for items in the cart
$cart_items = [];
$total_price = 0;
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $conn->prepare("SELECT product_id, product_name, price, image_url FROM products WHERE product_id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $products_in_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products_in_cart as $product) {
        $product_id = $product['product_id'];
        $quantity = $_SESSION['cart'][$product_id];
        $subtotal = $product['price'] * $quantity;
        $total_price += $subtotal;

        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['product_name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'image_url' => $product['image_url'] ?? 'img/functions.png'
        ];
    }
}

//Data Fetching: Get user's saved address to pre-fill the form
$stmt_user = $conn->prepare("SELECT full_name, address, city, province, zip, phone FROM users WHERE user_id = ?");
$stmt_user->execute([$_SESSION['user_id']]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน - The Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #2e2ebeff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
        }
        .summary-item-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.375rem;
        }
        .footer {
            background-color: #343a40;
            color: #f8f9fa;
            padding: 2rem 0;
        }
        .card {
            border-color: rgba(255,255,255,0.1) !important;
        }
        .list-group-item-dark {
            background-color: transparent !important;
            border-color: rgba(255,255,255,0.15) !important;
        }
        .form-control {
            background-color: #212529;
            color: #fff;
            border-color: #495057;
        }
        .form-control:focus {
            background-color: #343a40;
            color: #fff;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
    </style>
</head>
<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container my-5 main-content">
        <div class="text-center mb-5">
            <h1 class="text-light" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);">ดำเนินการชำระเงิน</h1>
            <p class="lead text-white-50">กรุณาตรวจสอบข้อมูลการสั่งซื้อและที่อยู่สำหรับจัดส่งให้ถูกต้อง</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>เกิดข้อผิดพลาด!</strong> <?= htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                <div class="row g-5">
                    <!-- Shipping Information Form -->
                    <div class="col-md-7 col-lg-8">
                        <div class="card bg-dark text-light">
                            <div class="card-header bg-transparent border-secondary">
                                <h4 class="mb-0">ข้อมูลสำหรับจัดส่ง</h4>
                            </div>
                            <div class="card-body">
                                <form action="place_order.php" method="POST" class="needs-validation" novalidate>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="fullname" class="form-label">ชื่อ-นามสกุล</label>
                                            <input type="text" class="form-control" id="fullname" name="fullname" value="" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="address" class="form-label">ที่อยู่</label>
                                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="city" class="form-label">อำเภอ/เขต</label>
                                            <input type="text" class="form-control" id="city" name="city" value="" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="province" class="form-label">จังหวัด</label>
                                            <input type="text" class="form-control" id="province" name="province" value="" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="zip" class="form-label">รหัสไปรษณีย์</label>
                                            <input type="text" class="form-control" id="zip" name="zip" value="" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="" required>
                                        </div>
                                    </div>
                                    <hr class="my-4 border-secondary">
                                    <h4 class="mb-3">การชำระเงิน</h4>
                                    <div class="my-3">
                                        <div class="form-check">
                                            <input id="cod" name="paymentMethod" type="radio" class="form-check-input" checked required>
                                            <label class="form-check-label" for="cod">ชำระเงินปลายทาง (Cash on Delivery)</label>
                                        </div>
                                    </div>
                                    <hr class="my-4 border-secondary">

                                    <button class="w-100 btn btn-primary btn-lg" type="submit" >ยืนยันการสั่งซื้อ</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-md-5 col-lg-4 order-md-last">
                        <div class="card bg-dark text-light">
                            <div class="card-header bg-transparent border-secondary d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">สรุปรายการสั่งซื้อ</h4>
                                <span class="badge bg-primary rounded-pill"><?= !empty($cart_items) ? count($cart_items) : 0 ?></span>
                            </div>
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($cart_items)): ?>
                                    <?php foreach ($cart_items as $item): ?>
                                    <li class="list-group-item list-group-item-dark d-flex justify-content-between lh-sm">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($item['image_url']) ?>" class="summary-item-img me-3" alt="<?= htmlspecialchars($item['name']) ?>">
                                            <div>
                                                <h6 class="my-0 text-light"><?= htmlspecialchars($item['name']) ?></h6>
                                                <small class="text-white-50">จำนวน: <?= $item['quantity'] ?></small>
                                            </div>
                                        </div>
                                        <span class="text-light">฿<?= number_format($item['subtotal'], 2) ?></span>
                                    </li>
                                    <?php endforeach; ?>
                                    <li class="list-group-item list-group-item-dark d-flex justify-content-between fs-5 fw-bold border-top-0 pt-3">
                                        <span>ยอดรวมทั้งสิ้น</span>
                                        <strong>฿<?= number_format($total_price, 2) ?></strong>
                                    </li>
                                <?php else: ?>
                                    <li class="list-group-item list-group-item-dark">
                                        <p class="text-white-50 text-center mb-0">ไม่มีสินค้าในตะกร้า</p>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container text-center">
            <p>&copy; 664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bootstrap form validation
        (() => {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>
