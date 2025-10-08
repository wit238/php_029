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

function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending':
            return 'bg-warning text-dark';
        case 'processing':
            return 'bg-info text-dark';
        case 'shipped':
            return 'bg-primary';
        case 'delivered':
        case 'completed':
            return 'bg-success';
        case 'cancelled':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการสั่งซื้อ - FindYourMeal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F8F9FA;
            font-family: 'Kanit', 'Poppins', sans-serif;
        }
        .accordion-button:not(.collapsed) {
            color: #fff;
            background-color: #E67E22;
        }
        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(230, 126, 34, 0.25);
        }
        .accordion-item {
            border-radius: 15px !important;
            border: 1px solid #dee2e6;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .footer { background-color: #343a40; color: #f8f9fa; padding: 2rem 0; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <?php require_once 'navbar.php'; ?>

    <div class="container my-5 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2"><i class="bi bi-receipt-cutoff me-2"></i>ประวัติการสั่งซื้อ</h1>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>กลับหน้าแรก</a>
        </div>

        <?php if (empty($orders)): ?>
            <div class="text-center p-5 bg-white rounded-3 shadow-sm">
                <i class="bi bi-bag-x" style="font-size: 5rem; color: #6c757d;"></i>
                <h3 class="mt-4">คุณยังไม่เคยสั่งซื้อ</h3>
                <p class="text-muted">ไปหาเมนูอร่อยๆ แล้วสั่งกันเลย</p>
                <a href="index.php" class="btn btn-primary mt-3">เริ่มค้นหาเมนู</a>
            </div>
        <?php else: ?>
            <div class="accordion" id="ordersAccordion">
                <?php foreach ($orders as $index => $order): ?>
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading<?= $order['order_id'] ?>">
                            <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $order['order_id'] ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $order['order_id'] ?>">
                                <div class="d-flex w-100 justify-content-between align-items-center pe-3">
                                    <span class="fw-bold">Order #<?= $order['order_id'] ?></span>
                                    <span class="text-muted d-none d-md-block"><i class="bi bi-calendar-event me-2"></i><?= date("d/m/Y", strtotime($order['order_date'])) ?></span>
                                    <span class="fw-bold">฿<?= number_format($order['total_amount'], 2) ?></span>
                                    <span class="badge <?= getStatusBadgeClass($order['status']) ?>"><?= ucfirst($order['status']) ?></span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?= $order['order_id'] ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $order['order_id'] ?>" data-bs-parent="#ordersAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-lg-7">
                                        <h6 class="mb-3">รายการ</h6>
                                        <ul class="list-group list-group-flush">
                                        <?php foreach (getOrderItems($conn, $order['order_id']) as $item): ?>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span><?= htmlspecialchars($item['product_name']) ?></span>
                                                <span class="text-muted"><?= $item['quantity'] ?> x ฿<?= number_format($item['price'], 2) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <div class="col-lg-5 mt-4 mt-lg-0">
                                        <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>
                                        <?php if ($shipping): ?>
                                            <h6 class="mb-3">ข้อมูลการจัดส่ง</h6>
                                            <p class="mb-1 small text-muted"><strong>ที่อยู่:</strong> <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?> <?= $shipping['postal_code'] ?></p>
                                            <p class="mb-1 small text-muted"><strong>โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?></p>
                                        <?php else: ?>
                                            <div class="text-muted">ไม่มีข้อมูลการจัดส่ง</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> FindYourMeal - 664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>