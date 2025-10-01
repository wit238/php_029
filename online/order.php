    <?php
session_start();
require 'config.php';
require 'functions.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งซื้อ - The Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #2e2ebeff; /* Dark theme from cart.php */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
        }
        .footer {
            background-color: #343a40;
            color: #f8f9fa;
            padding: 2rem 0;
        }
        .order-card {
            border-color: rgba(255,255,255,0.1) !important;
            border-radius: 0.75rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
        }
        .order-header {
            background-color: #1a1a1a;
            border-bottom: 1px solid #444;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            padding: 1rem 1.5rem;
        }
        .order-item {
            border-bottom: 1px solid #495057;
            padding: 0.85rem 0;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .total-amount {
            font-size: 1.2rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <?php require_once 'navbar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container py-5 text-light">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);"><i class="bi bi-receipt-cutoff me-2"></i>ประวัติการสั่งซื้อ</h1>
                <a href="index.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-1"></i>กลับหน้าหลัก</a>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>ทำรายการสั่งซื้อเรียบร้อยแล้ว!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (empty($orders)): ?>
                <div class="card bg-dark text-center p-5">
                    <div class="card-body">
                        <i class="bi bi-bag-x" style="font-size: 4rem; color: #6c757d;"></i>
                        <h3 class="card-title mt-3">คุณยังไม่เคยสั่งซื้อสินค้า</h3>
                        <p class="card-text text-white-50">ดูเหมือนว่าคุณยังไม่ได้ทำการสั่งซื้อใดๆ</p>
                        <a href="index.php" class="btn btn-primary mt-3">เลือกซื้อสินค้า</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="card order-card bg-dark mb-4">
                        <div class="order-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="h5">รหัสคำสั่งซื้อ: #<?= $order['order_id'] ?></strong>
                                <div class="small text-white-50">วันที่: <?= date("d/m/Y H:i", strtotime($order['order_date'])) ?></div>
                            </div>
                            <span class="badge bg-secondary fs-6"><?= ucfirst($order['status']) ?></span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-lg-7">
                                    <h5 class="mb-3 border-bottom border-secondary pb-2">รายการสินค้า</h5>
                                    <?php foreach (getOrderItems($conn, $order['order_id']) as $item): ?>
                                        <div class="d-flex justify-content-between order-item">
                                            <span><?= htmlspecialchars($item['product_name']) ?></span>
                                            <span class="text-white-50"><?= $item['quantity'] ?> x <?= number_format($item['price'], 2) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="d-flex justify-content-between mt-3 pt-3 border-top border-secondary">
                                        <span class="total-amount">รวมทั้งสิ้น</span>
                                        <span class="total-amount">฿<?= number_format($order['total_amount'], 2) ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-5 mt-4 mt-lg-0 ps-lg-4 border-start border-secondary">
                                    <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>
                                    <?php if ($shipping): ?>
                                        <h5 class="mb-3 border-bottom border-secondary pb-2">ข้อมูลการจัดส่ง</h5>
                                        <p class="mb-1"><strong class="text-white-50">ที่อยู่:</strong> <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?> <?= $shipping['postal_code'] ?></p>
                                        <p class="mb-1"><strong class="text-white-50">เบอร์โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?></p>
                                        <p class="mb-0"><strong class="text-white-50">สถานะ:</strong> <span class="badge bg-info text-dark"><?= ucfirst($shipping['shipping_status']) ?></span></p>
                                    <?php else: ?>
                                        <div class="text-white-50">ไม่มีข้อมูลการจัดส่ง</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container text-center">
           
            <p>664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>