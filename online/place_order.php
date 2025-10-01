<?php
session_start();
require_once 'config.php';

// 1. Security Checks: User must be logged in, cart not empty, and must be a POST request.
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// 2. Sanitize and Gather Shipping Info from POST
$full_name = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
$province = filter_input(INPUT_POST, 'province', FILTER_SANITIZE_STRING); // Kept for potential future use, but not inserted
$postal_code = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);

// Basic validation for shipping info
if (empty($full_name) || empty($address) || empty($city) || empty($postal_code) || empty($phone)) {
    $_SESSION['error'] = 'กรุณากรอกข้อมูลที่อยู่สำหรับจัดส่งให้ครบถ้วน';
    header('Location: checkout.php');
    exit;
}

$conn->beginTransaction();

try {
    // 3. Recalculate total price and check stock on the server-side
    $total_price = 0;
    $cart_products = [];
    
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id IN ($placeholders) FOR UPDATE"); // Lock rows for update
    $stmt->execute($product_ids);
    $products_from_db_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $products_from_db = [];
    foreach ($products_from_db_raw as $product) {
        $products_from_db[$product['product_id']] = $product;
    }

    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        if (!isset($products_from_db[$product_id])) {
            throw new Exception("ไม่พบสินค้า ID: $product_id ในระบบ");
        }
        $product = $products_from_db[$product_id];
        
        // Check stock availability - CORRECTED to use `stock`
        if ($product['stock'] < $quantity) {
            throw new Exception("สินค้า '" . htmlspecialchars($product['product_name']) . "' มีไม่เพียงพอในสต็อก");
        }

        $total_price += $product['price'] * $quantity;
        $cart_products[$product_id] = $product; // Store full product data
    }

    // 4. Insert into 'orders' table - CORRECTED to match schema
    $stmt_order = $conn->prepare(
        "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)"
    );
    $stmt_order->execute([
        $_SESSION['user_id'],
        $total_price,
        'pending' // Initial order status
    ]);

    $order_id = $conn->lastInsertId();

    // 5. Insert into 'order_items' and Update product stock
    $stmt_order_item = $conn->prepare(
        "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)"
    );
    $stmt_update_stock = $conn->prepare(
        "UPDATE products SET stock = stock - ? WHERE product_id = ?" // CORRECTED to use `stock`
    );

    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $product = $cart_products[$product_id];
        // Insert order item
        $stmt_order_item->execute([
            $order_id,
            $product_id,
            $quantity,
            $product['price']
        ]);
        // Update stock
        $stmt_update_stock->execute([$quantity, $product_id]);
    }

    // 6. (NEW) Insert into 'shipping' table
    $stmt_shipping = $conn->prepare(
        "INSERT INTO shipping (order_id, address, city, postal_code, phone) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt_shipping->execute([
        $order_id,
        $address,
        $city,
        $postal_code,
        $phone
    ]);

    // 7. If all successful, commit the transaction
    $conn->commit();

    // 8. Clear the cart and redirect to a success page
    unset($_SESSION['cart']);
    $_SESSION['last_order_id'] = $order_id;
    header('Location: order_success.php?success=true'); // Added success flag
    exit;

} catch (Exception $e) {
    // If any error occurs, roll back the transaction
    $conn->rollBack();
    
    // Set error message and redirect back to checkout
    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการสั่งซื้อ: ' . $e->getMessage();
    header('Location: checkout.php');
    exit;
}
