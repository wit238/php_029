<?php
session_start();
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE (username = ? OR email = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username_or_email, $username_or_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin'){
            header("Location: admin/index.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
$isLoggedIn  = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - FindYourMeal</title>
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
        }
        .image-section {
            flex: 1;
            background-image: url('https://images.pexels.com/photos/2741448/pexels-photo-2741448.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1');
            background-size: cover;
            background-position: center;
        }
        .login-form-container {
            width: 100%;
            max-width: 450px;
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
        <div class="form-section">
            <div class="login-form-container">
                <div class="text-center mb-5">
                    <a class="navbar-brand fs-3" href="index.php"><i class="bi bi-egg-fried me-2"></i>FindYourMeal</a>
                    <h1 class="h3 mt-4 mb-3 fw-normal">ยินดีต้อนรับกลับมา!</h1>
                    <p class="text-muted">เข้าสู่ระบบเพื่อค้นหารสชาติที่ใช่สำหรับคุณ</p>
                </div>

                <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
                    <div class="alert alert-success">สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ</div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif;?>

                <form method="post">
                    <div class="form-floating mb-3">
                        <input type="text" name="username_or_email" id="username_or_email" class="form-control" placeholder="Username or Email" required>
                        <label for="username_or_email">ชื่อผู้ใช้หรืออีเมล</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                        <label for="password">รหัสผ่าน</label>
                    </div>
                    
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">เข้าสู่ระบบ</button>
                    </div>
                </form>
                <div class="text-center">
                    <p class="text-muted">ยังไม่มีบัญชี? <a href="register.php" style="color: #E67E22;">สมัครสมาชิกที่นี่</a></p>
                </div>
            </div>
        </div>
        <div class="image-section d-none d-md-flex">
            <!-- Background image is set in CSS -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>