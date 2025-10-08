<?php
session_start();
require_once '../config.php';
require_once 'auth_admin.php';

// Fetch all member users
try {
    $stmt = $conn->prepare("SELECT user_id, username, full_name, email, created_at FROM users WHERE role = 'member' ORDER BY user_id DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "ข้อผิดพลาดในการดึงข้อมูลผู้ใช้: " . $e->getMessage();
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1140px;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        .card-header {
            background-color: #fff;
            border-bottom: none;
            padding: 1.5rem;
            font-weight: 600;
            color: #343a40;
        }
        .btn-icon {
            margin-right: 0.5rem;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f7ff;
        }
        .action-buttons .btn {
            width: 85px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <?php require_once 'navbar.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="bi bi-people-fill btn-icon"></i>จัดการสมาชิก</h1>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left btn-icon"></i>กลับหน้าหลัก</a>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '<?= addslashes($_SESSION['success_message']) ?>',
                timer: 1500,
                showConfirmButton: false
            });
        </script>
        <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '<?= addslashes($_SESSION['error']) ?>',
            });
        </script>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ul btn-icon"></i>รายชื่อสมาชิกทั้งหมด
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>ชื่อผู้ใช้</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>อีเมล</th>
                                <th>วันที่สมัคร</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">ยังไม่มีสมาชิก</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['user_id']) ?></td>
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                        <td class="text-center action-buttons">
                                            <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-outline-warning me-2">
                                                <i class="bi bi-pencil-square btn-icon"></i>แก้ไข
                                            </a>
                                            <a href="del_user_sweet.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(event, this.href)">
                                                <i class="bi bi-trash btn-icon"></i>ลบ
                                            </a>
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

    <script>
    function confirmDelete(event, url) {
        event.preventDefault();
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบผู้ใช้นี้ใช่ไหม? การกระทำนี้ไม่สามารถย้อนกลับได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, ลบเลย!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>