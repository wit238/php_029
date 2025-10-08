<?php
session_start();
require '../config.php';
require '../functions.php';

// ตรวจสอบสิทธิ์ admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// จัดการ request แบบ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // อัปเดตสถานะ
    if (isset($_POST['update_status'])) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$_POST['status'], $_POST['order_id']]);
        header("Location: orders.php");
        exit;
    }
    // อัปเดตสถานะการจัดส่ง
    if (isset($_POST['update_shipping'])) {
        $stmt = $conn->prepare("UPDATE shipping SET shipping_status = ? WHERE shipping_id = ?");
        $stmt->execute([$_POST['shipping_status'], $_POST['shipping_id']]);
        header("Location: orders.php");
        exit;
    }
    // ลบคำสั่งซื้อ
    if (isset($_POST['delete_order'])) {
        $order_id_to_delete = $_POST['order_id'];
        try {
            $conn->beginTransaction();
            // 1. ลบจาก order_items
            $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$order_id_to_delete]);
            // 2. ลบจาก shipping
            $stmt = $conn->prepare("DELETE FROM shipping WHERE order_id = ?");
            $stmt->execute([$order_id_to_delete]);
            // 3. ลบจาก orders
            $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
            $stmt->execute([$order_id_to_delete]);
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Failed to delete order #$order_id_to_delete: " . $e->getMessage());
        }
        header("Location: orders.php");
        exit;
    }
}


// ดึงคำสั่งซื้อทั้งหมด
$stmt = $conn->query("\n    SELECT o.*, u.username, u.full_name\n    FROM orders o\n    LEFT JOIN users u ON o.user_id = u.user_id\n    ORDER BY o.order_date DESC\n");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getStatusBadge($status) {
    switch ($status) {
        case 'pending': return 'bg-warning text-dark';
        case 'processing': return 'bg-info text-dark';
        case 'shipped': return 'bg-primary';
        case 'completed': return 'bg-success';
        case 'cancelled': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการคำสั่งซื้อ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="bi bi-receipt-cutoff me-2"></i>จัดการคำสั่งซื้อ</h1>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> กลับหน้าหลัก</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>ลูกค้า</th>
                                <th>วันที่สั่งซื้อ</th>
                                <th class="text-end">ยอดรวม</th>
                                <th class="text-center">สถานะ</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">ยังไม่มีคำสั่งซื้อ</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?= $order['order_id'] ?></strong></td>
                                        <td><?= htmlspecialchars($order['full_name'] ?? $order['username']) ?></td>
                                        <td><?= date("d M Y, H:i", strtotime($order['order_date'])) ?></td>
                                        <td class="text-end"><?= number_format($order['total_amount'], 2) ?> ฿</td>
                                        <td class="text-center">
                                            <span class="badge <?= getStatusBadge($order['status']) ?>"><?= ucfirst($order['status']) ?></span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#orderModal<?= $order['order_id'] ?>">
                                                <i class="bi bi-eye"></i> ดูรายละเอียด
                                            </button>
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

    <!-- Modals -->
    <?php foreach ($orders as $order): ?>
    <?php $shipping = getShippingInfo($conn, $order['order_id']); ?>
    <?php $items = getOrderItems($conn, $order['order_id']); ?>
    <div class="modal fade" id="orderModal<?= $order['order_id'] ?>" tabindex="-1" aria-labelledby="orderModalLabel<?= $order['order_id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel<?= $order['order_id'] ?>">
                        รายละเอียดคำสั่งซื้อ #<?= $order['order_id'] ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="bi bi-person-circle me-2"></i>ข้อมูลลูกค้า</h5>
                            <p><strong>ชื่อ:</strong> <?= htmlspecialchars($order['full_name'] ?? $order['username']) ?></p>
                            
                            <?php if ($shipping): ?>
                                <h5><i class="bi bi-truck me-2"></i>ข้อมูลการจัดส่ง</h5>
                                <p>
                                    <strong>ที่อยู่:</strong> <?= htmlspecialchars($shipping['address']) ?>, <?= htmlspecialchars($shipping['city']) ?> <?= $shipping['postal_code'] ?><br>
                                    <strong>เบอร์โทร:</strong> <?= htmlspecialchars($shipping['phone']) ?><br>
                                    <strong>สถานะ:</strong> <?= htmlspecialchars($shipping['shipping_status']) ?>
                                </p>
                            <?php else: ?>
                                <p class="text-muted">ไม่มีข้อมูลการจัดส่ง</p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-box-seam me-2"></i>รายการสินค้า</h5>
                            <ul class="list-group mb-3">
                                <?php foreach ($items as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($item['product_name']) ?> (x<?= $item['quantity'] ?>)
                                        <span><?= number_format($item['price'] * $item['quantity'], 2) ?> ฿</span>
                                    </li>
                                <?php endforeach; ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center active">
                                    <strong>ยอดรวม</strong>
                                    <strong><?= number_format($order['total_amount'], 2) ?> ฿</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <h5><i class="bi bi-pencil-square me-2"></i>อัปเดตสถานะ</h5>
                    <div class="row">
                        <div class="col-md-6">
                             <form method="post">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <div class="input-group">
                                    <select name="status" class="form-select">
                                        <?php
                                        $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
                                        foreach ($statuses as $status) {
                                            $selected = ($order['status'] === $status) ? 'selected' : '';
                                            echo "<option value=\"$status\" $selected>" . ucfirst($status) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary">อัปเดต</button>
                                </div>
                            </form>
                        </div>
                        <?php if ($shipping): ?>
                        <div class="col-md-6">
                            <form method="post">
                                <input type="hidden" name="shipping_id" value="<?= $shipping['shipping_id'] ?>">
                                <div class="input-group">
                                    <select name="shipping_status" class="form-select">
                                        <?php
                                        $s_statuses = ['not_shipped', 'shipped', 'delivered'];
                                        foreach ($s_statuses as $s) {
                                            $selected = ($shipping['shipping_status'] === $s) ? 'selected' : '';
                                            echo "<option value=\"$s\" $selected>" . ucfirst($s) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" name="update_shipping" class="btn btn-success">อัปเดต</button>
                                </div>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <form method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบคำสั่งซื้อนี้? การกระทำนี้ไม่สามารถย้อนกลับได้');">
                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                        <button type="submit" name="delete_order" class="btn btn-danger"><i class="bi bi-trash-fill me-2"></i>ลบคำสั่งซื้อ</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>