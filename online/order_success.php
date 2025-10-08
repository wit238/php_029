<?php
session_start();
require_once 'config.php';

// User must be logged in and have just placed an order
if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_order_id'])) {
    header('Location: index.php');
    exit;
}

$last_order_id = $_SESSION['last_order_id'];

// Unset the session variable so the page can't be reloaded with the same order ID
unset($_SESSION['last_order_id']);

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สั่งซื้อสำเร็จ - FindYourMeal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F8F9FA;
            font-family: 'Kanit', 'Poppins', sans-serif;
        }
        .success-card {
            background-color: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            max-width: 600px;
            width: 100%;
        }
        .btn-primary {
            background-color: #E67E22;
            border-color: #E67E22;
        }
        .btn-primary:hover {
            background-color: #D35400;
            border-color: #D35400;
        }
        .footer { background-color: #343a40; color: #f8f9fa; padding: 2rem 0; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php require_once 'navbar.php'; ?>

    <div class="container d-flex align-items-center justify-content-center flex-grow-1 my-5">
        <div class="text-center success-card">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
            <h1 class="display-5 mt-3">สั่งซื้อสำเร็จ!</h1>
            <p class="lead text-muted">ขอบคุณที่สั่งเมนูกับ FindYourMeal</p>
            <hr class="my-4">
            <p>หมายเลขคำสั่งซื้อของคุณคือ:</p>
            <h3 class="fw-bold" style="color: #E67E22;">#<?= htmlspecialchars($last_order_id) ?></h3>
            <p class="mt-4 text-muted">เราจะดำเนินการจัดส่งให้คุณโดยเร็วที่สุด</p>
            <div class="mt-4">
                <a href="index.php" class="btn btn-outline-secondary">กลับไปหน้าแรก</a>
                <a href="order.php" class="btn btn-primary">ดูประวัติการสั่งซื้อ</a>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> FindYourMeal - 664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>