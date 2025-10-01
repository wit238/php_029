<?php
session_start();
require_once '../config.php';
require_once 'auth_admin.php';

// --- Handle Delete User ---
if (isset($_GET['delete'])) {
    $user_id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    
    // ป้องกันการลบตัวเอง
    if ($user_id && $user_id != $_SESSION['user_id']) {
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
            $stmt->execute([$user_id]);
            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "ลบสมาชิกสำเร็จ!";
            } else {
                $_SESSION['error'] = "ไม่พบสมาชิกที่ต้องการลบ หรือไม่ได้รับอนุญาตให้ลบ";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
        }
    } else if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "คุณไม่สามารถลบบัญชีของตัวเองได้";
    }
    
    header("Location: users.php");
    exit;
}

// --- Fetch User Data ---
try {
    $stmt = $conn->query("SELECT user_id, username, full_name, email, created_at FROM users WHERE role = 'member' ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Database Error: " . $e->getMessage();
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(to right, #fdfbfb, #ebedee);
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.2rem;
        }
        .btn-icon { margin-right: 0.5rem; }
        .table thead th {
            font-weight: 600;
        }
        .dataTables_wrapper .row {
            align-items: center;
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

        <?php if (isset($_SESSION['error'])): ?>
        <script> Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด!', text: '<?= addslashes($_SESSION['error']) ?>' }); </script>
        <?php unset($_SESSION['error']); endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
        <script> Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: '<?= addslashes($_SESSION['success']) ?>', timer: 1500, showConfirmButton: false }); </script>
        <?php unset($_SESSION['success']); endif; ?>

        <div class="card">
            <div class="card-header"><i class="bi bi-list-ul btn-icon"></i>รายชื่อสมาชิกทั้งหมด</div>
            <div class="card-body p-4">
                <?php if (empty($users)): ?>
                    <div class="alert alert-info text-center">ยังไม่มีสมาชิกในระบบ</div>
                <?php else: ?>
                    <table id="usersTable" class="table table-hover table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ชื่อผู้ใช้</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>อีเมล</th>
                                <th>วันที่สมัคร</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['full_name'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td class="text-center">
                                        <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i> แก้ไข</a>
                                        <a href="users.php?delete=<?= $user['user_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(event, this.href)"><i class="bi bi-trash"></i> ลบ</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.8/i18n/th.json"
                },
                "order": [[ 3, "desc" ]] // Default sort by created_at date descending
            });
        });

        function confirmDelete(event, url) {
            event.preventDefault();
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณต้องการลบสมาชิกคนนี้ใช่ไหม? การกระทำนี้ไม่สามารถย้อนกลับได้!",
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
</body>
</html>