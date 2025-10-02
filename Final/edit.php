<?php
require_once 'configs.php';

$student_id = '';
$Fname = '';
$Lname = '';
$email = '';
$Tel = '';
$age = '';
$error = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['id'];
    $Fname = $_POST['Fname'];
    $Lname = $_POST['Lname'];
    $email = trim($_POST['email']);
    $Tel = $_POST['Tel'];
    $age = trim($_POST['age']);

 
    if (empty($Fname) || empty($Lname) || empty($email) || empty($Tel) || empty($age)) {
        $error[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "รูปแบบอีเมลไม่ถูกต้อง";
    }

    if (empty($error)) {
        try {
            $sql = "UPDATE tb_664230029 SET Fname = ?, Lname = ?, email = ?, Tel = ?, age = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$Fname, $Lname, $email, $Tel, $age, $student_id]);

            header("Location: index.php?update=success");
            exit();
        } catch (PDOException $e) {
            $error[] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $e->getMessage();
        }
    }
} 

else if (isset($_GET['id'])) {
    $student_id = $_GET['id'];
    try {
        $sql = "SELECT * FROM tb_664230029 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $Fname = $student['Fname'];
            $Lname = $student['Lname'];
            $email = $student['email'];
            $Tel = $student['Tel'];
            $age = $student['age'];
        } else {
            header("Location: index.php"); 
            exit();
        }
    } catch (PDOException $e) {
        die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>แก้ไขข้อมูลนักศึกษา</title>
    <style>
        .container {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">แก้ไขข้อมูลนักศึกษา (ID: <?= htmlspecialchars($student_id) ?>)</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($error as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="edit.php" method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($student_id) ?>">
            
            <div class="mb-3">
                <label for="Fname" class="form-label">ชื่อ</label>
                <input type="text" name="Fname" class="form-control" id="Fname" value="<?= htmlspecialchars($Fname) ?>" required>
            </div>
            <div class="mb-3">
                <label for="Lname" class="form-label">นามสกุล</label>
                <input type="text" name="Lname" class="form-control" id="Lname" value="<?= htmlspecialchars($Lname) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">อีเมล</label>
                <input type="email" name="email" class="form-control" id="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="mb-3">
                <label for="Tel" class="form-label">เบอร์โทร</label>
                <input type="tel" name="Tel" class="form-control" id="Tel" value="<?= htmlspecialchars($Tel) ?>" required>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">อายุ</label>
                <input type="number" name="age" class="form-control" id="age" value="<?= htmlspecialchars($age) ?>"required>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
