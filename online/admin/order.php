<?php
session_start();
require_once '../config.php';
require_once 'auth_admin.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
    $new_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $allowed_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

    if ($order_id && $new_status && in_array($new_status, $allowed_statuses)) {
        try {
            $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
            $stmt->execute([$new_status, $order_id]);
            $_SESSION['success_message'] = "อัปเดตสถานะของ Order #$order_id เป็น '$new_status' เรียบร้อยแล้ว";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอัปเดตสถานะ: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "ข้อมูลไม่ถูกต้องสำหรับการอัปเดตสถานะ";
    }
    // Redirect to the same page to prevent form resubmission
    header('Location: order.php');
    exit;
}


// Fetch all orders with user information
try {
    $stmt = $conn->prepare(
        "SELECT o.*, u.full_name 
         FROM orders o
         JOIN users u ON o.user_id = u.user_id
         ORDER BY o.order_date DESC"
    );
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูลคำสั่งซื้อ: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคำสั่งซื้อ - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-box-seam"></i> จัดการคำสั่งซื้อ</h2>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">ลูกค้า</th>
                            <th scope="col">ที่อยู่จัดส่ง</th>
                            <th scope="col">ยอดรวม</th>
                            <th scope="col">วันที่สั่งซื้อ</th>
                            <th scope="col">สถานะ</th>
                            <th scope="col" style="width: 220px;">อัปเดตสถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">ยังไม่มีคำสั่งซื้อ</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <th scope="row"><?= htmlspecialchars($order['order_id']) ?></th>
                                    <td><?= htmlspecialchars($order['full_name']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($order['shipping_name']) ?><br>
                                        <small class="text-muted"><?= htmlspecialchars($order['shipping_address'] . ', ' . $order['shipping_city'] . ', ' . $order['shipping_province'] . ' ' . $order['shipping_zip']) ?></small><br>
                                        <small class="text-muted"><i class="bi bi-telephone-fill"></i> <?= htmlspecialchars($order['shipping_phone']) ?></small>
                                    </td>
                                    <td><?= number_format($order['total_amount'], 2) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php 
                                                switch ($order['order_status']) {
                                                    case 'Pending': echo 'bg-warning text-dark'; break;
                                                    case 'Processing': echo 'bg-info text-dark'; break;
                                                    case 'Shipped': echo 'bg-primary'; break;
                                                    case 'Delivered': echo 'bg-success'; break;
                                                    case 'Cancelled': echo 'bg-danger'; break;
                                                    default: echo 'bg-secondary';
                                                }
                                            ?>">
                                            <?= htmlspecialchars($order['order_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form action="order.php" method="POST" class="d-flex">
                                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                            <select name="status" class="form-select form-select-sm me-2">
                                                <option value="Pending" <?= $order['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="Processing" <?= $order['order_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                                <option value="Shipped" <?= $order['order_status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                                <option value="Delivered" <?= $order['order_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                                <option value="Cancelled" <?= $order['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-dark btn-sm">อัปเดต</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
