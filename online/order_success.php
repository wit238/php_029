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
    <title>การสั่งซื้อสำเร็จ - The Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(to bottom right, #212529, #343a40);
            color: #f8f9fa;
        }
        .success-card {
            background: rgba(40, 40, 40, 0.65);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 3rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php require_once 'navbar.php'; ?>

    <div class="container d-flex align-items-center justify-content-center flex-grow-1">
        <div class="text-center success-card">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
            <h1 class="display-5 mt-3">การสั่งซื้อสำเร็จ!</h1>
            <p class="lead text-white-50">ขอบคุณที่สั่งซื้อสินค้ากับ The Shop</p>
            <hr class="my-4 border-light border-opacity-25">
            <p>หมายเลขคำสั่งซื้อของคุณคือ:</p>
            <h3 class="fw-bold">#<?= htmlspecialchars($last_order_id) ?></h3>
            <p class="mt-4 text-white-50">เราจะดำเนินการจัดส่งสินค้าให้คุณโดยเร็วที่สุด</p>
            <a href="index.php" class="btn btn-primary btn-lg mt-3">กลับไปหน้าแรก</a>
        </div>
    </div>

    <footer class="footer mt-auto">
        <div class="container text-center">
            <p>&copy; 664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
