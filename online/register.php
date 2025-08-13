<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' ) {
    // Get form data
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; 


    // นำข้อมูลไปบันทึกในฐานข้อมูล
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users(username,full_name,email,password,role) VALUES (?, ?, ?, ?, 'admin')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([ $username, $fullname, $email, $hashedPassword]);
    
    
} 
    ?>

<!DOCTYPE html>
<html lang="en">
    <head>  
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

</head>
<body>
   <div class="container mt-5">
    <h2 class="mb-4">สมัครสมาชิก</h2>
    <form method="post">
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="username" class="form-label">ชื่อผู้ใช้</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="ชื่อผู้ใช้">
            </div>
            <div class="col-md-6">
                <label for="fullname" class="form-label">ชื่อ-นามสกุล</label>
                <input type="text" class="form-control" id="fullname"  name="fullname" placeholder="ชื่อ-นามสกุล">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" class="form-control" id="email" placeholder="อีเมล" name="email">
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input type="password" class="form-control" id="password" placeholder="รหัสผ่าน" name="password">
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-6">
                <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน</label>
                <input type="password" class="form-control" id="confirm_password" placeholder="ยืนยันรหัสผ่าน" name="confirm_password">
            </div>
        </div>


        <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
        <a href="login.php" class= "btn btn-link">เข้าสู่ระบบ</a>

    </form>
</div>
        </form>

   </div>





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
    crossorigin="anonymous"></script>
</body>
</html>