<?php
session_start();
require 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

if (isset($_GET['update']) && $_GET['update'] === 'success') {
    $success = "บันทึกข้อมูลเรียบร้อยแล้ว";
}
if (isset($_GET['upload']) && $_GET['upload'] === 'success') {
    $success = "อัปเดตรูปโปรไฟล์สำเร็จ";
}

// Fetch user data first
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle Profile Image Upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5 MB

        if (in_array($_FILES['profile_image']['type'], $allowed_types) && $_FILES['profile_image']['size'] <= $max_size) {
            $upload_dir = 'img/profiles/';
            $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $new_filename = 'profile_' . $user_id . '_' . uniqid() . '.' . $file_extension;
            $new_filepath = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $new_filepath)) {
                // Delete old image if it's not the default one
                $default_image = 'img/book.png';
                if ($user['profile_image'] && $user['profile_image'] != $default_image && file_exists($user['profile_image'])) {
                    unlink($user['profile_image']);
                }

                // Update database
                $stmt_img = $conn->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
                $stmt_img->execute([$new_filepath, $user_id]);
                header("Location: profile.php?upload=success");
                exit;
            } else {
                $errors[] = "ขออภัย, ไม่สามารถอัปโหลดไฟล์ได้";
            }
        } else {
            $errors[] = "ไฟล์ไม่ถูกต้อง (ต้องเป็น JPG, PNG, GIF, WEBP และขนาดไม่เกิน 5MB)";
        }
    }

    // Handle other form data
    if (isset($_POST['update_details'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($full_name) || empty($email)) {
            $errors[] = "กรุณากรอกชื่อ-นามสกุลและอีเมล";
        }

        $stmt_email = $conn->prepare("SELECT * FROM users WHERE email = ? AND user_id != ?");
        $stmt_email->execute([$email, $user_id]);
        if ($stmt_email->rowCount() > 0) {
            $errors[] = "อีเมลนี้ถูกใช้งานแล้ว";
        }

        $update_password_sql = '';
        if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
            if (!password_verify($current_password, $user['password'])) {
                $errors[] = "รหัสผ่านเดิมไม่ถูกต้อง";
            } elseif (strlen($new_password) < 6) {
                $errors[] = "รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร";
            } elseif ($new_password !== $confirm_password) {
                $errors[] = "รหัสผ่านใหม่และการยืนยันไม่ตรงกัน";
            } else {
                $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $update_password_sql = ", password = '" . $new_hashed . "'";
            }
        }

        if (empty($errors)) {
            $sql = "UPDATE users SET full_name = ?, email = ? {$update_password_sql} WHERE user_id = ?";
            $stmt_update = $conn->prepare($sql);
            $stmt_update->execute([$full_name, $email, $user_id]);
            $success = "บันทึกข้อมูลเรียบร้อยแล้ว";
        }
    }

    // Re-fetch user data to show all updates
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ของคุณ - The Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #2e2ebeff; display: flex; flex-direction: column; min-height: 100vh; color: #f8f9fa; }
        .main-content { flex: 1; }
        .footer { background-color: #343a40; color: #f8f9fa; padding: 2rem 0; }
        .profile-card { background-color: #212529; border-radius: 0.75rem; box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2); padding: 2rem; }
        .form-control { background-color: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.2); color: #f8f9fa; }
        .form-control:focus { background-color: rgba(255,255,255,0.1); border-color: #80bdff; color: #f8f9fa; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
        .form-control::placeholder { color: #ced4da; }
        .form-label { color: #ced4da; }
        hr { border-color: rgba(255,255,255,0.2); }
        .btn-primary { background-color: #0d6efd; border-color: #0d6efd; }
        .alert-success { background-color: #198754; color: white; border: none; }
        .alert-danger { background-color: #dc3545; color: white; border: none; }
        .profile-image-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 1.5rem;
            border: 4px solid #495057;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .profile-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <?php require_once 'navbar.php'; ?>

    <div class="main-content">
        <div class="container py-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h2" style="text-shadow: 0 2px 4px rgba(0,0,0,0.5);"><i class="bi bi-person-circle me-3"></i>โปรไฟล์ของคุณ</h1>
                <a href="index.php" class="btn btn-outline-light"><i class="bi bi-arrow-left me-1"></i>กลับหน้าหลัก</a>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
            <?php elseif (!empty($success)): ?>
            <div class="alert alert-success mb-4"><i class="bi bi-check-circle-fill me-2"></i><?= $success ?></div>
            <?php endif; ?>

            <div class="profile-card">
                <div class="row">
                    <div class="col-lg-4 text-center">
                        <div class="profile-image-container">
                            <img src="<?= htmlspecialchars($user['profile_image']) ?>?v=<?= time() ?>" alt="Profile Image">
                        </div>
                        <form method="post" enctype="multipart/form-data">
                            <label for="profile_image" class="form-label">เปลี่ยนรูปโปรไฟล์</label>
                            <input type="file" name="profile_image" id="profile_image" class="form-control mb-2">
                            <button type="submit" class="btn btn-secondary btn-sm">อัปโหลดรูป</button>
                        </form>
                    </div>
                    <div class="col-lg-8">
                        <h4 class="mb-4">แก้ไขข้อมูลส่วนตัว</h4>
                        <form method="post" class="row g-3">
                            <input type="hidden" name="update_details" value="1">
                            <div class="col-md-6">
                                <label for="full_name" class="form-label">ชื่อ-นามสกุล</label>
                                <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($user['full_name']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">อีเมล</label>
                                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']) ?>">
                            </div>
                            <div class="col-12">
                                <hr class="my-4">
                                <h5 class="mb-3"><i class="bi bi-key-fill me-2"></i>เปลี่ยนรหัสผ่าน (ไม่จำเป็น)</h5>
                            </div>
                            <div class="col-md-4">
                                <label for="current_password" class="form-label">รหัสผ่านเดิม</label>
                                <input type="password" name="current_password" id="current_password" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="new_password" class="form-label">รหัสผ่านใหม่ (≥ 6 ตัวอักษร)</label>
                                <input type="password" name="new_password" id="new_password" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                            </div>
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>บันทึกการเปลี่ยนแปลง</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container text-center">
            <p class="mb-0">664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>