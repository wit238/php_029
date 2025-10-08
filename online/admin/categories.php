<?php
session_start();
require_once '../config.php';
require_once 'auth_admin.php';

// Handle Add Category
if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        try {
            $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->execute([$category_name]);
            $_SESSION['success'] = "เพิ่มหมวดหมู่สำเร็จ!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "ข้อผิดพลาดในการเพิ่มหมวดหมู่: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "กรุณากรอกชื่อหมวดหมู่";
    }
    header("Location: categories.php");
    exit;
}

// Handle Update Category
if (isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $new_name = trim($_POST['new_name']);
    if (!empty($new_name) && !empty($category_id)) {
        try {
            $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
            $stmt->execute([$new_name, $category_id]);
            $_SESSION['success'] = "อัปเดตหมวดหมู่สำเร็จ!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "ข้อผิดพลาดในการอัปเดตหมวดหมู่: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "กรุณากรอกชื่อหมวดหมู่ใหม่";
    }
    header("Location: categories.php");
    exit;
}

// Handle Delete Category
// if (isset($_GET['delete'])) {
//     $category_id = $_GET['delete'];
//     if (!empty($category_id)) {
//         try {
//             $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
//             $stmt->execute([$category_id]);
//             $_SESSION['success'] = "ลบหมวดหมู่สำเร็จ!";
//         } catch (PDOException $e) {
//             $_SESSION['error'] = "ข้อผิดพลาดในการลบหมวดหมู่: " . $e->getMessage();
//         }
//     }
//     header("Location: categories.php");
//     exit;
// }

if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
// ตรวจสอบวำ่ หมวดหมนู่ ยี้ ังถูกใชอ้ยหู่ รอื ไม่
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $productCount = $stmt->fetchColumn();
if ($productCount > 0) {
// ถำ้มสี นิ คำ้อยใู่ นหมวดหมนู่ ี้
$_SESSION['error'] = "ไม่สามารถลบหมวดหมู่ได้ เนื่องจากมีสินค้าภายในหมวดหมู่นี้";
} else {
// ถำ้ไมม่ สี นิ คำ้ ใหล้ บได ้
$stmt = $conn->prepare("DELETE FROM  categories WHERE category_id = ?");
$stmt->execute([$category_id]);
$_SESSION['success'] = "ลบหมวดหมู่เรียบร้อยแล้ว";
}
header("Location: categories.php");
exit;
}

// Fetch all categories
try {
    $stmt = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "ข้อผิดพลาดในการดึงข้อมูลหมวดหมู่: " . $e->getMessage();
    $categories = []; // Ensure $categories is an array even on error
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการหมวดหมู่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-color: #86b7fe;
        }
        .btn {
            transition: all 0.3s ease-in-out;
        }
        .btn:hover {
            transform: translateY(-2px);
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
            <h1 class="h3"><i class="bi bi-tags-fill btn-icon"></i>จัดการหมวดหมู่สินค้า</h1>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left btn-icon"></i>กลับหน้าหลัก</a>
        </div>

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

        <?php if (isset($_SESSION['success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '<?= addslashes($_SESSION['success']) ?>',
                timer: 1500,
                showConfirmButton: false
            });
        </script>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-plus-circle-fill btn-icon"></i>เพิ่มหมวดหมู่ใหม่
            </div>
            <div class="card-body">
                <form method="post" action="categories.php">
                    <div class="input-group">
                        <input type="text" name="category_name" class="form-control" placeholder="เช่น อาหารไทย, ของหวาน" required>
                        <button type="submit" name="add_category" class="btn btn-primary"><i class="bi bi-plus-lg btn-icon"></i>เพิ่ม</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-list-ul btn-icon"></i>รายการหมวดหมู่ทั้งหมด
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">ชื่อหมวดหมู่</th>
                                <th scope="col" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">ยังไม่มีหมวดหมู่สินค้า</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($categories as $index => $cat): ?>
                                <tr>
                                    <th scope="row"><?= $index + 1 ?></th>
                                    <td><?= htmlspecialchars($cat['category_name']) ?></td>
                                    <td class="text-center">
                                        <form method="post" action="categories.php" class="d-inline-flex align-items-center">
                                            <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                                            <input type="text" name="new_name" class="form-control form-control-sm me-2" placeholder="เปลี่ยนชื่อ" required>
                                            <button type="submit" name="update_category" class="btn btn-sm btn-outline-warning me-2" style="width: 80px;"><i class="bi bi-pencil-square"></i> แก้ไข</button>
                                            <a href="categories.php?delete=<?= $cat['category_id'] ?>" class="btn btn-sm btn-outline-danger" style="width: 80px;" onclick="confirmDelete(event, this.href)"><i class="bi bi-trash"></i> ลบ</a>
                                        </form>
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
            text: "การลบหมวดหมู่จะสำเร็จก็ต่อเมื่อไม่มีสินค้าในหมวดหมู่นี้",
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