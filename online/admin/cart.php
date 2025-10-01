<?php
session_start();
require 'config.php';
// ตรวจสอบกำรล็อกอิน
if (!isset($_SESSION['________'])) { // TODO: ใส่ session ของ user
header("Location: ________.php"); // TODO: หน้ำ login
exit;
}
$user_id = $_SESSION['________']; // TODO: ก ำหนด user_id
// -----------------------------
// เพมิ่ สนิ คำ้เขำ้ตะกรำ้
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['________'])) { // TODO: product_id
$product_id = $_POST['________']; // TODO: product_id
$quantity = max(1, intval($_POST['quantity'] ?? 1));
// ตรวจสอบวำ่ สนิ คำ้อยใู่ นตะกรำ้แลว้หรอื ยัง
$stmt = $pdo->prepare("SELECT * FROM ________ WHERE user_id = ? AND product_id = ?");
// TODO: ใสช่ อื่ ตำรำงตะกรำ้
$stmt->execute([$user_id, $product_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if ($item) {
// ถ ้ำมีแล้ว ให้เพิ่มจ ำนวน
$stmt = $pdo->prepare("UPDATE ________ SET quantity = quantity + ? WHERE ________ = ?");
// TODO: ชอื่ ตำรำง, primary key ของตะกร ้ำ
$stmt->execute([$quantity, $item['________']]);
} else {
// ถ ้ำยังไม่มี ให้เพิ่มใหม่
$stmt = $pdo->prepare("INSERT INTO ________ (user_id, product_id, quantity) VALUES (?, ?, ?)");
$stmt->execute([$user_id, $product_id, $quantity]);
}
header("Location: ________.php"); // TODO: กลับมำที่ cart
exit;
}
// -----------------------------
// ลบสนิ คำ้ออกจำกตะกรำ้
// -----------------------------
if (isset($_GET['remove'])) {
$cart_id = $_GET['remove'];
$stmt = $pdo->prepare("DELETE FROM ________ WHERE ________ = ? AND user_id = ?");
// TODO: ชอื่ ตำรำงตะกรำ้, primary key
$stmt->execute([$cart_id, $user_id]);
header("Location: ________.php"); // TODO: กลับมำที่ cart
exit;
}
// -----------------------------
// ดงึรำยกำรสนิ คำ้ในตะกรำ้
// -----------------------------
$stmt = $pdo->prepare("SELECT cart.________, cart.________, products.________, products.________
FROM cart
JOIN products ON cart.________ = products.________
WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
// -----------------------------
// ค ำนวณรำคำรวม
// -----------------------------
$total = 0;
foreach ($items as $item) {
$total += $item['________'] * $item['________']; // TODO: quantity * price
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ตะกรำ้สนิ คำ้</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h2>ตะกรำ้สนิ คำ้</h2>
<a href="________.php" class="btn btn-secondary mb-3">← กลับไปเลอื กสนิ คำ้</a> <!-- TODO: หน้ำ index -->
<?php if (count($items) === 0): ?>
<div class="alert alert-warning">________</div> <!-- TODO: ข ้อควำมกรณีตะกร ้ำว่ำง -->
<?php else: ?>
<table class="table table-bordered">
<thead>
<tr>
<th>ชอื่ สนิ คำ้</th>
<th>จ ำนวน</th>
<th>รำคำต่อหน่วย</th>
<th>รำคำรวม</th>
<th>จัดกำร</th>
</tr>
</thead>
<tbody>
<?php foreach ($items as $item): ?>
<tr>
<td><?= htmlspecialchars($item['________']) ?></td> <!-- TODO: product_name -->
<td><?= $item['________'] ?></td> <!-- TODO: quantity -->
<td><?= number_format($item['________'], 2) ?></td> <!-- TODO: price -->
<td><?= number_format($item['________'] * $item['________'], 2) ?></td> <!-- TODO: price *
quantity -->
<td>
<a href="cart.php?remove=<?= $item['________'] ?>"
class="btn btn-sm btn-danger"
onclick="return confirm('คณุ ตอ้ งกำรลบสนิ คำ้นอี้ อกจำกตะกรำ้หรอื ไม?' ่ )">ลบ</a>
<!-- TODO: cart_id -->
</td>
</tr>
<?php endforeach; ?>
<tr>
<td colspan="3" class="text-end"><strong>รวมทั้งหมด:</strong></td>
<td colspan="2"><strong><?= number_format($total, 2) ?> บำท</strong></td>
</tr>
</tbody>
</table>
<a href="________.php" class="btn btn-success">สั่งซอื้ สนิ คำ้</a> <!-- TODO: checkout -->
<?php endif; ?>
</body>
</html>