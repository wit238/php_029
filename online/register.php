<?php
require_once 'config.php';

$error = []; // ตัวแปรสำหรับเก็บ error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ตรวจสอบว่ากรอกข้อมูลมาครบหรือไม่ (emtry)
    if (empty($username) || empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // ตรวจสอบว่าอีเมลถูกต้องหรือไม่ (filter_var)
        $error[] = "อีเมลไม่ถูกต้อง";

    } elseif (strlen($password) < 6) {
        $error[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร";
    } elseif ($password !== $confirm_password) {
        // ตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
        $error[] = "รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน";

    } else {
        // ตรวจสอบว่าชื่อผู้ใช้หรืออีเมลถูกใช้ไปแล้วหรือไม่
        $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $error[] = "ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว";
        }
    }

    if (empty($error)) { // ถ้าไม่มีข้อผิดพลาดใดๆ

        // นำข้อมูลไปบันทึกในฐานข้อมูล
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(username,full_name,email,password,role) VALUES (?, ?, ?, ?, 'member')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $fullname, $email, $hashedPassword]);

        // ถ้าบันทึกสำเร็จ ให้เปลี่ยนเส้นทางไปหน้า login
        header("Location: login.php?register=success");
        
        exit(); // หยุดการทำงานของสคริปต์หลังจากเปลี่ยนเส้นทาง
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - FindYourMeal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            background-color: #F8F9FA;
            font-family: 'Kanit', 'Poppins', sans-serif;
        }
        .main-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }
        .form-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow-y: auto;
        }
        .image-section {
            flex: 1;
            background-image: url('https://images.pexels.com/photos/1267320/pexels-photo-1267320.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1');
            background-size: cover;
            background-position: center;
        }
        .register-form-container {
            width: 100%;
            max-width: 550px;
        }
        .form-control:focus {
            border-color: #E67E22;
            box-shadow: 0 0 0 0.25rem rgba(230, 126, 34, 0.25);
        }
        .btn-primary {
            background-color: #E67E22;
            border-color: #E67E22;
        }
        .btn-primary:hover {
            background-color: #D35400;
            border-color: #D35400;
        }
        @media (max-width: 768px) {
            .image-section {
                display: none;
            }
            .form-section {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="image-section d-none d-md-flex">
            <!-- Background image is set in CSS -->
        </div>
        <div class="form-section">
            <div class="register-form-container">
                <div class="text-center mb-5">
                    <a class="navbar-brand fs-3" href="index.php"><i class="bi bi-egg-fried me-2"></i>FindYourMeal</a>
                    <h1 class="h3 mt-4 mb-3 fw-normal">เข้าร่วมชุมชนนักชิม</h1>
                    <p class="text-muted">สร้างบัญชีเพื่อเริ่มรีวิวและค้นหาสุดยอดเมนู</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($error as $e): ?>
                            <div><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($e) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                                <label for="username">ชื่อผู้ใช้</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Full Name" required value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
                                <label for="fullname">ชื่อ-นามสกุล</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        <label for="email">อีเมล</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <label for="password">รหัสผ่าน</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                                <label for="confirm_password">ยืนยันรหัสผ่าน</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">สมัครสมาชิก</button>
                    </div>
                </form>
                <div class="text-center">
                    <p class="text-muted">มีบัญชีอยู่แล้ว? <a href="login.php" style="color: #E67E22;">เข้าสู่ระบบที่นี่</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
