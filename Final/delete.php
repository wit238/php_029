<?php
require_once 'configs.php';

// ตรวจสอบว่ามีการส่ง id มาหรือไม่
if (isset($_GET['id'])) {
    $key = $_GET['id'];

    try {
        // เตรียมคำสั่ง SQL สำหรับลบข้อมูล
        $sql = "DELETE FROM tb_664230029 WHERE `key` = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$key]);

        // เมื่อลบสำเร็จ ให้ redirect กลับไปที่หน้าแรก
        header("Location: index.php?delete=success");
        exit();

    } catch (PDOException $e) {
        // กรณีเกิดข้อผิดพลาด
        echo "Error: " . $e->getMessage();
    }

} else {
    // ถ้าไม่มี id ส่งมา ให้กลับไปหน้าแรก
    header("Location: index.php");
    exit();
}
?>
