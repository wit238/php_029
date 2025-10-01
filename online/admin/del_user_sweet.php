
<?php
session_start();
require_once '../config.php'; // เชื่อมต่อฐานข้อมูล
require_once 'auth_admin.php'; // ตรวจสอบการเข้าสู่ระบบผู้ดูแลระบบ

// ตั้งค่าการรายงานข้อผิดพลาดสำหรับการดีบัก
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // ป้องกันลบตัวเอง
    if ($user_id != $_SESSION['user_id']) {
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
            $stmt->execute([$user_id]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User not found or not a member.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Cannot delete your own account.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request or user_id not provided.']);
}
exit;
?>
