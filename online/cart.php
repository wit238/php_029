<?php
session_start();
require_once 'config.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

//ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// --- CART ACTIONS HANDLER ---

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if ($product_id && $quantity > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
        header('Location: cart.php');
        exit;
    }
}

// Handle Remove from Cart
if (isset($_GET['remove'])) {
    $product_id_to_remove = filter_input(INPUT_GET, 'remove', FILTER_VALIDATE_INT);
    if ($product_id_to_remove && isset($_SESSION['cart'][$product_id_to_remove])) {
        unset($_SESSION['cart'][$product_id_to_remove]);
        header('Location: cart.php');
        exit;
    }
}

// Handle Update Quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $product_id = filter_var($product_id, FILTER_VALIDATE_INT);
        $quantity = filter_var($quantity, FILTER_VALIDATE_INT);

        if ($product_id && $quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } elseif ($product_id && $quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header('Location: cart.php');
    exit;
}

// --- DATA FETCHING FOR DISPLAY ---
$cart_items = [];
$total_price = 0;
if (!empty($_SESSION['cart'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['cart']), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders)");
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
            'image_url' => $product['image_url'] ?? 'img/placeholder.png',
            'stock' => $product['stock']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า - The Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .cart-item-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        .quantity-input {
            width: 80px;
        }
        .footer {
            background-color: #343a40;
            color: #f8f9fa;
            padding: 2rem 0;
            margin-top: auto;
        }
        .card {
            border-color: rgba(255,255,255,0.1) !important;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php require_once 'navbar.php'; ?>

    <div class="main-content container my-5">
        <h1 class="mb-4 text-light" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);">ตะกร้าสินค้าของคุณ</h1>

        <?php if (empty($cart_items)): ?>
            <div class="card bg-dark text-light text-center p-5">
                <div class="card-body">
                    <h2 class="card-title">ตะกร้าสินค้าของคุณว่างเปล่า</h2>
                    <p class="card-text text-white-50">ดูเหมือนว่าคุณยังไม่ได้เพิ่มสินค้าใดๆ ลงในตะกร้า</p>
                    <a href="index.php" class="btn btn-primary mt-3"><i class="bi bi-arrow-left me-2"></i>กลับไปเลือกซื้อสินค้า</a>
                </div>
            </div>
        <?php else: ?>
            <form action="cart.php" method="post">
                <div class="card bg-dark text-light p-4">
                    <div class="table-responsive">
                        <table class="table align-middle table-dark table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">สินค้า</th>
                                    <th scope="col">ราคา</th>
                                    <th scope="col" class="text-center">จำนวน</th>
                                    <th scope="col" class="text-end">ราคารวม</th>
                                    <th scope="col" class="text-center">ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-img me-3">
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($item['name']) ?></h6>
                                                <small class="text-white-50">คงเหลือ: <?= $item['stock'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>฿<?= number_format($item['price'], 2) ?></td>
                                    <td class="text-center">
                                        <input type="number" name="quantities[<?= $item['id'] ?>]" class="form-control form-control-sm quantity-input mx-auto bg-dark text-light" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>">
                                    </td>
                                    <td class="text-end">฿<?= number_format($item['subtotal'], 2) ?></td>
                                    <td class="text-center">
                                        <a href="cart.php?remove=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(event, this.href)"><i class="bi bi-trash-fill"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="index.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-2"></i>เลือกซื้อสินค้าต่อ</a>
                        <button type="submit" name="update_cart" class="btn btn-info"><i class="bi bi-arrow-clockwise me-2"></i>อัปเดตตะกร้า</button>
                    </div>
                </div>
            </form>

            <div class="row mt-4 justify-content-end">
                <div class="col-md-6 col-lg-5 col-xl-4">
                    <div class="card bg-dark text-light">
                        <div class="card-body">
                            <h5 class="card-title mb-3">สรุปคำสั่งซื้อ</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-light">
                                    ราคาสินค้า
                                    <span>฿<?= number_format($total_price, 2) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent text-light">
                                    ค่าจัดส่ง
                                    <span>ฟรี</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold border-top pt-3 bg-transparent text-light border-secondary">
                                    ยอดรวมทั้งสิ้น
                                    <span>฿<?= number_format($total_price, 2) ?></span>
                                </li>
                            </ul>
                            <div class="d-grid mt-4">
                                <a href="checkout.php" class="btn btn-primary btn-lg"><i class="bi bi-credit-card-fill me-2"></i>ดำเนินการชำระเงิน</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p>&copy; 664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmDelete(event, url) {
        event.preventDefault();
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการนำสินค้านี้ออกจากตะกร้าใช่ไหม?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
    </script>
</body>
</html>