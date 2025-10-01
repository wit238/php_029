<?php
session_start();
require_once '../config.php';
require_once 'auth_admin.php';

// --- Handle Add Product ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $image_url = null;

    // Basic Validation
    if (empty($name) || $price === false || $price <= 0 || $stock === false || $stock < 0 || $category_id === false) {
        $_SESSION['error'] = "ข้อมูลสินค้าไม่ถูกต้อง กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        // Image Upload Handling
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../img/';
            // Create a unique filename to prevent overwriting
            $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $unique_filename = uniqid('prod_', true) . '.' . $file_extension;
            $target_file = $upload_dir . $unique_filename;
            
            // Check if file is a valid image
            $check = getimagesize($_FILES['product_image']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                    $image_url = 'img/' . $unique_filename; // Store relative path for web
                } else {
                    $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
                }
            } else {
                $_SESSION['error'] = "ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ";
            }
        }

        // Insert into database if no upload error
        if (!isset($_SESSION['error'])) {
            try {
                $stmt = $conn->prepare(
                    "INSERT INTO products (product_name, description, price, stock, category_id, image_url) 
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$name, $description, $price, $stock, $category_id, $image_url]);
                $_SESSION['success'] = "เพิ่มสินค้า '$name' สำเร็จ!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Database Error: " . $e->getMessage();
            }
        }
    }
    header("Location: products.php");
    exit;
}

// --- Handle Delete Product ---
if (isset($_GET['delete'])) {
    $product_id = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($product_id) {
        try {
            // First, get the image_url to delete the file
            $stmt = $conn->prepare("SELECT image_url FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $image_path = $stmt->fetchColumn();

            // Delete from database
            $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);

            // If deletion was successful, delete the image file
            if ($stmt->rowCount() > 0) {
                if ($image_path && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);
                }
                $_SESSION['success'] = "ลบสินค้าสำเร็จ!";
            } else {
                $_SESSION['error'] = "ไม่พบสินค้าที่ต้องการลบ";
            }

        } catch (PDOException $e) {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
        }
    }
    header("Location: products.php");
    exit;
}

// --- Fetch Data for Display ---
try {
    // Fetch all products with category names
    $stmt_products = $conn->query(
        "SELECT p.*, c.category_name 
         FROM products p 
         LEFT JOIN categories c ON p.category_id = c.category_id 
         ORDER BY p.created_at DESC"
    );
    $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all categories for the form dropdown
    $stmt_categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Database Error: " . $e->getMessage();
    $products = [];
    $categories = [];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f4f7f6;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
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
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
        }
        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }
        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>
    <?php require_once 'navbar.php'; ?>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="bi bi-box-seam-fill btn-icon"></i>จัดการสินค้า</h1>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left btn-icon"></i>กลับหน้าหลัก</a>
        </div>

        <!-- Display SweetAlert2 Notifications -->
        <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด!', text: '<?= addslashes($_SESSION['error']) ?>' });
        </script>
        <?php unset($_SESSION['error']); endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
        <script>
            Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: '<?= addslashes($_SESSION['success']) ?>', timer: 1500, showConfirmButton: false });
        </script>
        <?php unset($_SESSION['success']); endif; ?>

        <div class="row">
            <!-- Add Product Form -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header"><i class="bi bi-plus-circle-fill btn-icon"></i>เพิ่มสินค้าใหม่</div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="product_name" class="form-label">ชื่อสินค้า</label>
                                <input type="text" id="product_name" name="product_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">รายละเอียด</label>
                                <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">ราคา (บาท)</label>
                                    <input type="number" id="price" step="0.01" name="price" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="form-label">จำนวน</label>
                                    <input type="number" id="stock" name="stock" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">หมวดหมู่</label>
                                <select id="category_id" name="category_id" class="form-select" required>
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="product_image" class="form-label">รูปภาพสินค้า</label>
                                <input class="form-control" type="file" id="product_image" name="product_image" accept="image/*">
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="add_product" class="btn btn-primary"><i class="bi bi-plus-lg btn-icon"></i>เพิ่มสินค้า</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Product List -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header"><i class="bi bi-list-ul btn-icon"></i>รายการสินค้า</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>รูปภาพ</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>หมวดหมู่</th>
                                        <th>ราคา</th>
                                        <th>คงเหลือ</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($products)): ?>
                                        <tr><td colspan="6" class="text-center text-muted">ยังไม่มีสินค้าในระบบ</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($products as $p): ?>
                                        <tr>
                                            <td>
                                                <img src="../<?= !empty($p['image_url']) ? htmlspecialchars($p['image_url']) : 'img/placeholder.png' ?>" alt="<?= htmlspecialchars($p['product_name']) ?>" class="product-img">
                                            </td>
                                            <td><?= htmlspecialchars($p['product_name']) ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($p['category_name'] ?? 'N/A') ?></span></td>
                                            <td><?= number_format($p['price'], 2) ?></td>
                                            <td><?= $p['stock'] ?></td>
                                            <td class="text-center">
                                                <a href="edit_product.php?id=<?= $p['product_id'] ?>" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i></a>
                                                <a href="products.php?delete=<?= $p['product_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(event, this.href)"><i class="bi bi-trash"></i></a>
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
        </div>
    </div>

    <script>
    function confirmDelete(event, url) {
        event.preventDefault();
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณต้องการลบสินค้านี้ใช่ไหม? การกระทำนี้ไม่สามารถย้อนกลับได้!",
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