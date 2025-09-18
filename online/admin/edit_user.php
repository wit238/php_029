<?php
session_start();
require_once '../config.php'; // เชื่อมต่อฐานข้อมูล
require_once 'auth_admin.php'; // ตรวจสอบการเข้าสู่ระบบผู้ดูแลระบบ

$user = null; // Initialize $user to null

// Fetch user data for editing
if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'member'");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // User not found or not a member, redirect
            header("Location: users.php");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        // Log error for debugging
        error_log("Error fetching user for edit: " . $e->getMessage());
    }
} else {
    // No ID provided, redirect to users list
    header("Location: users.php");
    exit;
}

// Handle form submission for updating user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Basic validation
    if (empty($username) || empty($email)) {
        $errors[] = "กรุณากรอกชื่อผู้ใช้และอีเมล";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    // Check if username or email already exists for another user
    try {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
        $stmt->execute([$username, $email, $user['user_id']]);
        if ($stmt->fetch()) {
            $errors[] = "มีคนใช้ชื่อผู้ใช้หรืออีเมลนี้แล้ว";
        }
    } catch (PDOException $e) {
        $errors[] = "ข้อผิดพลาดฐานข้อมูลในการตรวจสอบความซ้ำซ้อน: " . $e->getMessage();
        error_log("Error checking username/email uniqueness: " . $e->getMessage());
    }

    // Password validation
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = "รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร";
        }
        if ($password !== $confirm_password) {
            $errors[] = "รหัสผ่านไม่ตรงกัน";
        }
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE users SET username = ?, full_name = ?, email = ?";
            $params = [$username, $full_name, $email];

            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql .= ", password = ?";
                $params[] = $hashed_password;
            }

            $sql .= " WHERE user_id = ?";
            $params[] = $user['user_id'];

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $_SESSION['success_message'] = "อัปเดตข้อมูลผู้ใช้สำเร็จ!";
            header("Location: users.php");
            exit;

        } catch (PDOException $e) {
            $error = "ข้อผิดพลาดฐานข้อมูลในการอัปเดต: " . $e->getMessage();
            error_log("Error updating user: " . $e->getMessage());
        }
    } else {
        $error = implode("<br>", $errors);
    }

    // Basic validation
    if (empty($username) || empty($email)) {
        $errors[] = "กรุณากรอกชื่อผู้ใช้และอีเมล";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    // If no basic validation errors, proceed with update
    if (empty($errors)) {
        try {
            $sql = "UPDATE users SET username = ?, full_name = ?, email = ? WHERE user_id = ?";
            $params = [$username, $full_name, $email, $user['user_id']];

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            $_SESSION['success_message'] = "อัปเดตข้อมูลผู้ใช้สำเร็จ!";
            header("Location: users.php");
            exit;

        } catch (PDOException $e) {
            $error = "ข้อผิดพลาดฐานข้อมูลในการอัปเดต: " . $e->getMessage();
            error_log("Error updating user: " . $e->getMessage());
        }
    } else {
        $error = implode("<br>", $errors);
    }

    // Update $user array to reflect current form values if there are errors
    $user['username'] = $username;
    $user['full_name'] = $full_name;
    $user['email'] = $email;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8f9fa;
        }

        .content-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-top: 50px;
            margin-bottom: 50px; /* Added for better spacing */
        }

        h2 {
            color: #2c3e50;
            font-weight: 600;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 30px;
        }

        .btn {
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .btn-sm i {
            margin-right: 5px;
        }
    </style>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="container mt-4">
    <div class="content-container">
        <h2><i class="bi bi-pencil-square"></i> แก้ไขข้อมูลสมาชิก</h2>
        <a href="users.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> กลับหน้ารายชื่อสมาชิก</a>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($user): ?>
        <form method="post" class="row g-3">
            <div class="col-md-6 mb-3">
                <label class="form-label">ชื่อผู้ใช้</label> 
                <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($user['username']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">ชื่อ-นามสกุล</label>
                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <div class="col-md-6 mb-3 position-relative">
                <label class="form-label">รหัสผ่านใหม่ <small class="text-muted">(ถ้าไม่ต้องการเปลี่ยน ให้เว้นว่าง)</small></label>
                <input type="password" name="password" id="password" class="form-control">
                <i class="bi bi-eye-slash" id="togglePassword" style="cursor: pointer; position: absolute; right: 20px; top: 40px;"></i>
            </div>
            <div class="col-md-6 mb-3 position-relative">
                <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                <i class="bi bi-eye-slash" id="toggleConfirmPassword" style="cursor: pointer; position: absolute; right: 20px; top: 40px;"></i>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> บันทึกการแก้ไข</button>
                <a href="del_user_sweet.php?id=<?= $user['user_id'] ?>" class="btn btn-danger" onclick="confirmDelete(event, this.href)">
                    <i class="bi bi-trash"></i> ลบผู้ใช้
                </a>
            </div>
        </form>
        <?php else: ?>
            <div class="alert alert-warning">ไม่พบข้อมูลผู้ใช้</div>
        <?php endif; ?>
    </div>

    <script>
    function confirmDelete(event, url) {
        event.preventDefault(); // Prevent the link from redirecting immediately
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบผู้ใช้นี้ใช่ไหม? การกระทำนี้ไม่สามารถย้อนกลับได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url; // Redirect to the delete script if confirmed
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('bi-eye');
        });

        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmPassword = document.querySelector('#confirm_password');

        toggleConfirmPassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            // toggle the eye slash icon
            this.classList.toggle('bi-eye');
        });
    });
    </script>
</body>
</html>