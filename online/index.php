<?php
session_start(); // เริ่มต้น session เพื่อจัดการการเข้าสู่ระบบ
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>WECOME</h1>
        <p>ยินดีต้อนรับสู่หน้าหลักของเว็บไซต์</p>
        <p>ผู้ใช้ : <?= htmlentities($_SESSION['username'])?></p>
        <p>บทบาท : <?= htmlentities($_SESSION['role'])?></p>
        
        <p><a href="logout.php" class="btn btn-secondary">ออกจากระบบ</a></p>
        
    </div>
    

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</html>