<?php
session_start();
require_once '../config.php'; // เชื่อมต่อฐานข้อมูล
require_once 'auth_admin.php'; // ตรวจสอบการเข้าสู่ระบบผู้ดูแลระบบ 

// ลบสมาชิก
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    // ป้องกันลบตัวเอง
    if ($user_id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
        $stmt->execute([$user_id]);
    }
    header("Location: users.php");
    exit;
}

// ดึงข้อมูลสมาชิก (เฉพาะสมาชิกทั่วไป)
$stmt = $conn->query("SELECT * FROM users WHERE role = 'member' ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>จัดการสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f7f6;
        }

        .content-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-top: 50px;
        }

        h2 {
            color: #2c3e50;
            font-weight: 600;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 30px;
        }

        .table thead th {
            background-color: #f8f9fa;
            color: #34495e;
            font-weight: 500;
            border-bottom-width: 2px;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
            transform: scale(1.015);
            transition: background-color 0.2s ease-in-out, transform 0.2s ease-in-out;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .table td, .table th {
            vertical-align: middle;
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

        #usersTable_wrapper .dataTables_filter input {
            border-radius: 20px;
            padding-left: 15px;
        }

    </style>
</head>

<body>
    <div class="container content-container">
        <h2><i class="bi bi-people-fill"></i> จัดการสมาชิก</h2>
        <a href="index.php" class="btn btn-secondary mb-4"><i class="bi bi-arrow-left-circle"></i> กลับหน้าผู้ดูแล</a>
        <?php if (count($users) === 0): ?>
            <div class="alert alert-info"><i class="bi bi-info-circle-fill"></i> ยังไม่มีสมาชิกในระบบ</div>
        <?php else: ?>
            <table id="usersTable" class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ชื่อผู้ใช้</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>อีเมล</th>
                        <th>วันที่สมัคร</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= $user['created_at'] ?></td>
                            <td>
                                <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i>แก้ไข</a>
                                <a href="users.php?delete=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('คุณต้องการลบสมาชิกนี้หรือไม่?')"><i class="bi bi-trash3-fill"></i>ลบ</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- DataTables JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.8/i18n/th.json"
                }
            });
        });
    </script>
</body>

</html>
