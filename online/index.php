<?php
session_start();
require_once 'config.php';

// --- Data Fetching ---
$stmt_cats = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$all_categories = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);

// --- Filtering Logic ---
$selected_category_id = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
$search_term = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);

$sql = "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id";
$params = [];
$where_clauses = [];
$current_category_name = 'ทุกเมนู';

if ($selected_category_id) {
    $where_clauses[] = "p.category_id = ?";
    $params[] = $selected_category_id;
    foreach ($all_categories as $cat) {
        if ($cat['category_id'] == $selected_category_id) {
            $current_category_name = $cat['category_name'];
            break;
        }
    }
}

if ($search_term) {
    $where_clauses[] = "(p.product_name LIKE ? OR p.description LIKE ?)";
    $params[] = '%' . $search_term . '%';
    $params[] = '%' . $search_term . '%';
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " GROUP BY p.product_id ORDER BY p.created_at DESC";

$stmt_prods = $conn->prepare($sql);
$stmt_prods->execute($params);
$products = $stmt_prods->fetchAll(PDO::FETCH_ASSOC);

$isLoggedIn  = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindYourMeal - ค้นหารสชาติที่ใช่สำหรับคุณ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #F8F9FA;
            font-family: 'Kanit', 'Poppins', sans-serif;
        }
        .hero-section {
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.pexels.com/photos/1640777/pexels-photo-1640777.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1');
            background-size: cover;
            background-position: center;
            padding: 8rem 0;
            color: white;
            text-align: center;
        }
        .hero-section h1 { font-weight: 700; text-shadow: 0 3px 6px rgba(0,0,0,0.5); }
        .hero-section p { font-size: 1.25rem; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        .search-bar {
            max-width: 600px;
            margin: 2rem auto 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .search-bar .form-control {
            padding: 0.8rem 1.2rem;
            font-size: 1.1rem;
            border-radius: 50px 0 0 50px;
        }
        .search-bar .btn {
            border-radius: 0 50px 50px 0;
            padding: 0.8rem 1.5rem;
        }

        .food-card {
            border: none;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
        }
        .food-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        }
        .food-card-img {
            width: 100%;
            aspect-ratio: 16 / 10;
            object-fit: cover;
        }
        .card-title { font-weight: 600; }
        .card-subtitle { color: #6c757d; }
        .food-price { font-size: 1.2rem; font-weight: 600; color: #E67E22; }
        .star-rating { color: #ffc107; }

        .sidebar .list-group-item {
            border-radius: 8px !important;
            margin-bottom: 5px;
            transition: all 0.2s ease;
        }
        .sidebar .list-group-item.active {
            background-color: #E67E22;
            border-color: #E67E22;
            color: white;
        }
        .sidebar .list-group-item:not(.active):hover {
            background-color: #e9ecef;
        }
        .footer { background-color: #343a40; color: #f8f9fa; padding: 2rem 0; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <?php require_once 'navbar.php'; ?>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <h1 class="display-4">ค้นพบรสชาติใหม่ที่ใช่สำหรับคุณ</h1>
            <p class="lead">รีวิวและให้คะแนนร้านอาหารและเมนูโปรดของคุณ</p>
            <form action="index.php" method="get" class="search-bar">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="ค้นหาชื่อร้านอาหาร, เมนู, หรือประเภทอาหาร..." name="search" value="<?= htmlspecialchars($search_term ?? '') ?>">
                    <button class="btn btn-primary" type="submit" style="background-color: #E67E22; border-color: #E67E22;"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </header>

    <div class="container mt-5 flex-grow-1">
        <div class="row">

            <!-- Sidebar -->
            <aside class="col-lg-3">
                <div class="sticky-top" style="top: 1.5rem;">
                    <h4 class="mb-3"><i class="bi bi-compass me-2"></i>สำรวจ</h4>
                    <div class="sidebar">
                        <div class="list-group">
                            <a href="index.php" class="list-group-item list-group-item-action <?= !$selected_category_id ? 'active' : '' ?>">ทุกประเภท</a>
                            <?php foreach ($all_categories as $category): ?>
                                <a href="index.php?category=<?= $category['category_id'] ?>" class="list-group-item list-group-item-action <?= ($selected_category_id == $category['category_id']) ? 'active' : '' ?>"><?= htmlspecialchars($category['category_name']) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="col-lg-9">
                <h2 class="mb-4">กำลังแสดง: <span class="fw-bold" style="color: #E67E22;"><?= htmlspecialchars($current_category_name) ?></span></h2>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php if (empty($products)): ?>
                        <div class="col-12">
                            <div class="alert alert-warning text-center">
                                <i class="bi bi-exclamation-triangle-fill h4"></i>
                                <p class="h5 mt-2">ไม่พบเมนูที่ตรงกับเงื่อนไข</p>
                                <a href="index.php" class="btn btn-dark mt-2">กลับไปหน้าแรก</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col">
                                <div class="card food-card h-100">
                                    <img src="<?= !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'https://via.placeholder.com/400x250.png?text=Food+Image' ?>" class="food-card-img" alt="<?= htmlspecialchars($product['product_name']) ?>">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                                        <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($product['category_name']) ?></h6>
                                        <div class="star-rating mb-2">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-half"></i>
                                            <span class="text-muted ms-1">(<?= rand(10, 200) ?>)</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                                            <p class="food-price mb-0">฿<?= number_format($product['price'], 2) ?></p>
                                            <a href="product_detail.php?id=<?=$product['product_id'] ?>" class="btn btn-sm btn-outline-dark">ดูรายละเอียด / สั่งซื้อ</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>

        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> FindYourMeal - 664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
