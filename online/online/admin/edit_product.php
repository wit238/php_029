<?php
session_start();
require_once '../config.php';
require_once 'auth_admin.php';

$product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = null;

if (!$product_id) {
    $_SESSION['error'] = "รหัสสินค้าไม่ถูกต้อง";
    header("Location: products.php");
    exit;
}

// --- Fetch existing product data ---
try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $_SESSION['error'] = "ไม่พบสินค้าที่คุณต้องการแก้ไข";
        header("Location: products.php");
        exit;
    }

    // Fetch categories for the dropdown
    $stmt_categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['error'] = "Database Error: " . $e->getMessage();
    header("Location: products.php");
    exit;
}


// --- Handle Form Submission for Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $current_image_url = $product['image_url'];

    // Basic Validation
    if (empty($name) || $price === false || $price <= 0 || $stock === false || $stock < 0 || $category_id === false) {
        $_SESSION['error'] = "ข้อมูลสินค้าไม่ถูกต้อง กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        $new_image_url = $current_image_url;

        // New Image Upload Handling
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../img/';
            $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
            $unique_filename = uniqid('prod_', true) . '.' . $file_extension;
            $target_file = $upload_dir . $unique_filename;
            
            $check = getimagesize($_FILES['product_image']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                    // Delete old image if it exists
                    if ($current_image_url && file_exists('../' . $current_image_url)) {
                        unlink('../' . $current_image_url);
                    }
                    $new_image_url = 'img/' . $unique_filename; // Set new image path
                } else {
                    $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพใหม่";
                }
            } else {
                $_SESSION['error'] = "ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ";
            }
        }

        // Update database if no upload error
        if (!isset($_SESSION['error'])) {
            try {
                $sql = "UPDATE products SET 
                            product_name = ?, 
                            description = ?, 
                            price = ?, 
                            stock = ?, 
                            category_id = ?, 
                            image_url = ? 
                        WHERE product_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$name, $description, $price, $stock, $category_id, $new_image_url, $product_id]);
                
                $_SESSION['success'] = "อัปเดตข้อมูลสินค้า '$name' สำเร็จ!";
                header("Location: products.php");
                exit;

            } catch (PDOException $e) {
                $_SESSION['error'] = "Database Error: " . $e->getMessage();
            }
        }
    }
    // Redirect back to the edit page if there was an error
    header("Location: edit_product.php?id=" . $product_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสินค้า - <?= htmlspecialchars($product['product_name'] ?? 'N/A') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(to right, #ece9e6, #ffffff);
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transition: all 0.3s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.5rem;
            font-weight: 600;
            font-size: 1.2rem;
        }
        .btn-icon { margin-right: 0.5rem; }
        .form-label { font-weight: 500; }
        .current-img-preview {
            width: 100%;
            max-width: 200px;
            height: auto;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 2px solid #dee2e6;
            padding: 5px;
            margin-bottom: 1rem;
        }
        .drop-zone {
            border: 2px dashed #ccc;
            border-radius: 0.5rem;
            padding: 25px;
            text-align: center;
            font-weight: 500;
            color: #aaa;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .drop-zone--over {
            border-style: solid;
            background-color: #f0f8ff;
            color: #333;
        }
        .drop-zone__input {
            display: none;
        }
        #charCount {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="bi bi-pencil-square btn-icon"></i>แก้ไขสินค้า</h1>
            <a href="products.php" class="btn btn-outline-secondary"><i class="bi bi-chevron-left btn-icon"></i>กลับไปหน้ารายการสินค้า</a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด!', text: '<?= addslashes($_SESSION['error']) ?>' });
        </script>
        <?php unset($_SESSION['error']); endif; ?>

        <div class="card">
            <div class="card-header">
                แก้ไขข้อมูลสำหรับ: <?= htmlspecialchars($product['product_name'] ?? 'N/A') ?>
            </div>
            <div class="card-body p-4">
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="product_name" class="form-label">ชื่อสินค้า</label>
                                <input type="text" id="product_name" name="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">รายละเอียด</label>
                                <textarea id="description" name="description" class="form-control" rows="5"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                                <div id="charCount" class="text-end">0 ตัวอักษร</div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">ราคา (บาท)</label>
                                    <input type="number" id="price" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($product['price'] ?? '0.00') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="form-label">จำนวน</label>
                                    <input type="number" id="stock" name="stock" class="form-control" value="<?= htmlspecialchars($product['stock'] ?? '0') ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">หมวดหมู่</label>
                                <select id="category_id" name="category_id" class="form-select" required>
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    <?php 
                                    $selected_cat_id = $product['category_id'] ?? 0;
                                    foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['category_id'] ?>" <?= ($selected_cat_id == $cat['category_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['category_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3 text-center">
                                <label class="form-label">รูปภาพปัจจุบัน</label>
                                <div>
                                    <img src="../<?= !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'img/placeholder.png' ?>" alt="Current Image" class="current-img-preview" id="imagePreview">
                                </div>
                                <div class="drop-zone mt-2">
                                    <span class="drop-zone__prompt"><i class="bi bi-cloud-arrow-up-fill"></i><br>ลากไฟล์มาวางที่นี่ หรือ คลิกเพื่อเลือกไฟล์</span>
                                    <input type="file" name="product_image" class="drop-zone__input" accept="image/*">
                                </div>
                                <small class="form-text text-muted">หากไม่ต้องการเปลี่ยน ให้เว้นว่างไว้</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-end">
                        <button type="submit" name="update_product" class="btn btn-primary btn-lg"><i class="bi bi-save-fill btn-icon"></i>บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character Counter
        const descriptionTextarea = document.getElementById('description');
        const charCount = document.getElementById('charCount');
        
        function updateCharCount() {
            const count = descriptionTextarea.value.length;
            charCount.textContent = `${count} ตัวอักษร`;
        }
        descriptionTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count

        // Drag and Drop Image Upload
        const dropZoneElement = document.querySelector(".drop-zone");
        const inputElement = document.querySelector(".drop-zone__input");

        dropZoneElement.addEventListener("click", e => {
            inputElement.click();
        });

        inputElement.addEventListener("change", e => {
            if (inputElement.files.length) {
                updateThumbnail(dropZoneElement, inputElement.files[0]);
            }
        });

        dropZoneElement.addEventListener("dragover", e => {
            e.preventDefault();
            dropZoneElement.classList.add("drop-zone--over");
        });

        ["dragleave", "dragend"].forEach(type => {
            dropZoneElement.addEventListener(type, e => {
                dropZoneElement.classList.remove("drop-zone--over");
            });
        });

        dropZoneElement.addEventListener("drop", e => {
            e.preventDefault();
            if (e.dataTransfer.files.length) {
                inputElement.files = e.dataTransfer.files;
                updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
            }
            dropZoneElement.classList.remove("drop-zone--over");
        });

        function updateThumbnail(dropZoneElement, file) {
            const imagePreview = document.getElementById('imagePreview');
            const reader = new FileReader();
            reader.onload = function(){
                imagePreview.src = reader.result;
            };
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>
</html>
