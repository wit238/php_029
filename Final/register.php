<?php
require_once 'configs.php';

$error = []; // ตัวแปรสำหรับเก็บ error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $std_id = trim($_POST['std_id']);
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $mail = trim($_POST['mail']);
    $Tel = $_POST['Tel'];
    $age = $_POST['age'];

    // ตรวจสอบว่ากรอกข้อมูลมาครบหรือไม่ (emtry)
    if (empty($std_id) || empty($f_name) || empty($l_name) || empty($mail) || empty($Tel) || empty($age) ) {
        $error[] = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        // ตรวจสอบว่าอีเมลถูกต้องหรือไม่ (filter_var)
        $error[] = "อีเมลไม่ถูกต้อง";

    } else {
        // ตรวจสอบว่าชื่อผู้ใช้หรืออีเมลถูกใช้ไปแล้วหรือไม่
        $sql = "SELECT * FROM tb_664230029 WHERE std_id = ? OR mail = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$std_id, $mail]);

        if ($stmt->rowCount() > 0) {
            $error[] = "รหัสนักศึกษาหรืออีเมลนี้ถูกใช้ไปแล้ว"; 
        }
    }

    if (empty($error)) { // ถ้าไม่มีข้อผิดพลาดใดๆ

        $sql = "INSERT INTO tb_664230029(std_id, f_name, l_name, mail, Tel , age) VALUES (?, ?, ?, ?, ? ,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$std_id, $f_name, $l_name, $mail, $Tel, $age]);

        // ถ้าบันทึกสำเร็จ ให้เปลี่ยนเส้นทางไปหน้า index
        header("Location: index.php?register=success");
        
        exit(); // หยุดการทำงานของสคริปต์หลังจากเปลี่ยนเส้นทาง
    }


}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>เพิ่มนักศึกษา</title>
    <style>

        .container {
            background-color: #ffffffcc;
            border-radius: 10px;
            padding: 30px;
            max-width: 600px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #002fffff;
        }
         h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #0000ffff;
        }
        a.btn-link {
            color: #1cf38bff;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">เพิ่มนักศึกษา</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($error as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>


        <form action="" method="post">
            <div class="mb-3">
                <label for="std_id" class="form-label">รหัสนักศึกษา</label>
                <input type="number" name="std_id" class="form-control" id="std_id" placeholder="เช่น 664230029"
                    value="<?= isset($_POST['std_id']) ? htmlspecialchars($_POST['std_id']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="f_name" class="form-label">ชื่อ</label>
                <input type="text" name="f_name" class="form-control" id="f_name" placeholder="ชื่อ"
                    value="<?= isset($_POST['f_name']) ? htmlspecialchars($_POST['f_name']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="l_name" class="form-label">นามสกุล</label>
                <input type="text" name="l_name" class="form-control" id="l_name" placeholder="นามสกุล"
                    value="<?= isset($_POST['l_name']) ? htmlspecialchars($_POST['l_name']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="mail" class="form-label">อีเมล</label>
                <input type="email" name="mail" class="form-control" id="mail" placeholder="example@email.com"
                    value="<?= isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="Tel" class="form-label">เบอร์โทร</label>
                <input type="tel" name="Tel" class="form-control" id="Tel" placeholder="08XXXXXXXX"
                    value="<?= isset($_POST['Tel']) ? htmlspecialchars($_POST['Tel']) : '' ?>" required>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">อายุ</label>
                <input type="age" name="age" class="form-control" id="age" placeholder="อายุ"
                    value="<?= isset($_POST['age']) ? htmlspecialchars($_POST['age']) : '' ?>" required>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <a href="index.php" class="btn btn-secondary">ดูรายการ</a>
                <button type="submit" class="btn btn-primary">เพิ่มข้อมูล</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>