<?php
session_start();
require_once 'config.php';

// --- Data Fetching ---
$stmt_cats = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$all_categories = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);

// --- Fetch Featured Products ---
$stmt_featured = $conn->query("SELECT * FROM products WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 5");
$featured_products = $stmt_featured->fetchAll(PDO::FETCH_ASSOC);

// --- Filtering Logic ---
$selected_category_id = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
$search_term = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);

$sql = "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id";
$params = [];
$where_clauses = [];
$current_category_name = 'สินค้าทั้งหมด';

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
    $where_clauses[] = "p.product_name LIKE ?";
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

// Image list for the scroller
$scroller_images = glob('img/prod_*.{jpg,png,webp,gif}', GLOB_BRACE);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - The Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #2e2ebeff;
            font-family: 'Kanit', sans-serif;
        }
        .product-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            background-color: #343a40;
        }
        .product-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
        }
        .product-img-container {
            position: relative;
        }
        .product-img {
            width: 100%;
            height: auto;
            aspect-ratio: 4 / 3;
            object-fit: contain;
            background-color: #FFFFFF;
        }
        .card-title { font-weight: 500; }
        .card-subtitle { color: #adb5bd; }
        .product-price { font-size: 1.25rem; font-weight: 500; }
        .btn-outline-light { border-radius: 20px; }
        .footer { background-color: #343a40; color: #f8f9fa; padding: 2rem 0; }
        .filter-sidebar .list-group-item {
            background-color: #343a40;
            color: #f8f9fa;
            border-color: rgba(255,255,255,0.1);
        }
        .filter-sidebar .list-group-item.active {
            background-color: #0d6efd;
            border-color: #0d6efd;
            font-weight: 500;
        }
        .filter-sidebar .list-group-item-action:hover {
            background-color: #495057;
        }
        hr.styled {
            border: 0;
            height: 1px;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
        }
        .star-rating {
            color: #ffc107;
        }

        /* Scroller Styles */
        .scroller-section {
            text-align: center;
            color: white;
        }
        .scroller-section h1 {
            font-weight: 700;
            text-shadow: 0 3px 6px rgba(0,0,0,0.5);
        }
        .scroller {
            max-width: 1200px;
            margin: 2rem auto 0;
            overflow: hidden;
            -webkit-mask-image: linear-gradient(to right, transparent, #000 10%, #000 90%, transparent);
            mask-image: linear-gradient(to right, transparent, #000 10%, #000 90%, transparent);
        }
        .scroller__inner {
            display: flex;
            gap: 1.5rem; /* Adjusted gap */
            padding-block: 1rem;
            animation: scroll 60s linear infinite; /* Adjusted speed */
        }
        @keyframes scroll {
            to {
                transform: translateX(calc(-100% - 1.5rem)); /* Adjusted gap */
            }
        }
        .scroller__inner img {
            height: 180px; /* Increased size */
            width: auto;
            aspect-ratio: 1 / 1;
            object-fit: contain;
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        /* Featured Carousel */
        .featured-carousel .carousel-item img {
            height: 400px;
            object-fit: contain;
            width: 100%;
            background-color: #FFFFFF;
        }
        .featured-carousel .carousel-caption {
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <?php require_once 'navbar.php'; ?>

    <div class="container-fluid mt-4 flex-grow-1">
        <div class="row">

            <!-- Sidebar -->
            <aside class="col-md-3">
                <div class="sticky-top" style="top: 1.5rem; z-index: 1;">
                    <div class="card filter-sidebar bg-dark text-light">
                        <div class="card-header fw-bold"><i class="bi bi-filter me-2"></i>ตัวกรองสินค้า</div>
                        <div class="card-body">
                            <form action="index.php" method="get">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control bg-dark text-light" placeholder="ค้นหาชื่อสินค้า..." name="search" value="<?= htmlspecialchars($search_term ?? '') ?>">
                                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item fw-bold">หมวดหมู่</li>
                            <a href="index.php" class="list-group-item list-group-item-action <?= !$selected_category_id ? 'active' : '' ?>">สินค้าทั้งหมด</a>
                            <?php foreach ($all_categories as $category): ?>
                                <a href="index.php?category=<?= $category['category_id'] ?>" class="list-group-item list-group-item-action <?= ($selected_category_id == $category['category_id']) ? 'active' : '' ?>"><?= htmlspecialchars($category['category_name']) ?></a>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="col-md-9">
                
                <!-- Animated Scroller Section -->
                <div class="card bg-dark text-light mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="scroller-section">
                            <h1 class="display-4">The Shop Collection</h1>
                            <p class="lead">ค้นพบสไตล์ที่เป็นคุณกับคอลเลคชั่นล่าสุดของเรา</p>
                            <div class="scroller">
                                <div class="scroller__inner">
                                    <?php 
                                    // Duplicate images for seamless loop
                                    $images_to_scroll = !empty($scroller_images) ? array_merge($scroller_images, $scroller_images) : [];
                                    foreach ($images_to_scroll as $image_path): ?>
                                        <img src="<?= htmlspecialchars($image_path) ?>" alt="Product Image">
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Products Section -->
                <?php if (!empty($featured_products)): ?>
                <div class="mb-4">
                    <h2 class="text-light mb-3"><i class="bi bi-star-fill text-warning me-2"></i>สินค้าแนะนำ</h2>
                    <div id="featuredCarousel" class="carousel slide featured-carousel" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach ($featured_products as $index => $product): ?>
                                <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>" aria-label="Slide <?= $index + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner rounded-3">
                            <?php foreach ($featured_products as $index => $product): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= htmlspecialchars($product['image_url']) ?>" class="d-block w-100" alt="<?= htmlspecialchars($product['product_name']) ?>">
                                    <div class="carousel-caption d-none d-md-block p-3">
                                        <h5><?= htmlspecialchars($product['product_name']) ?></h5>
                                        <p>ราคา ฿<?= number_format($product['price'], 2) ?></p>
                                        <a href="product_detail.php?id=<?=$product['product_id'] ?>" class="btn btn-primary btn-sm">ดูรายละเอียด</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
                <?php endif; ?>

                <hr class="styled my-4">

                <h2 class="mb-4 text-light" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);">กำลังเลือกชม: <span class="text-white   "><?= htmlspecialchars($current_category_name) ?></span></h2>
                <div class="row">
                    <?php if (empty($products)): ?>
                        <div class="col-12">
                            <div class="alert alert-warning text-center bg-dark text-light border-warning">
                                <i class="bi bi-exclamation-triangle-fill h4"></i>
                                <p class="h5 mt-2">ไม่พบสินค้าที่ตรงกับเงื่อนไข</p>
                                <a href="index.php" class="btn btn-primary mt-2">กลับไปหน้าสินค้าทั้งหมด</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card product-card h-100">
                                    <div class="product-img-container">
                                        <img src="<?= !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'img/placeholder.png' ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($product['product_name']) ?>">
                                        <?php 
                                        // Check if the product was created in the last 24 hours
                                        if (isset($product['created_at']) && strtotime($product['created_at']) > strtotime('-24 hours')): 
                                        ?>
                                            <span class="badge bg-primary position-absolute top-0 end-0 m-2 fs-6">New</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-light"><?= htmlspecialchars($product['product_name']) ?></h5>
                                        <h6 class="card-subtitle mb-2 text-white-50"><?= htmlspecialchars($product['category_name']) ?></h6>
                                        <div class="star-rating mb-2">
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-fill"></i>
                                            <i class="bi bi-star-half"></i>
                                            <span class="text-white-50 ms-1">(<?= rand(50, 250) ?>)</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3">
                                            <p class="product-price text-light mb-0">฿<?= number_format($product['price'], 2) ?></p>
                                            <a href="product_detail.php?id=<?=$product['product_id'] ?>" class="btn btn-outline-light btn-sm">ดูรายละเอียด</a>
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
    <footer class="footer mt-auto">
        <div class="container text-center">
            <p>&copy; 664230029 Witthawat CH. 66/46</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>