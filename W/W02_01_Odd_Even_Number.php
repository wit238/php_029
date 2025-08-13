<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Odd_Even_Number</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <style>

    </style>
</head>
<body>
    <div class="container mt-5">
      
        <h1 class="text-center text-danger">Odd or Even Number Checker</h1>
        <hr>
        <form method="POST" class="mt-4 mb-5 text-center mt-5">
            <div class="form-group mb-3 text-center">
                <label for="number" class="form-label"><p>กรุณากรอกตัวเลขเพื่อทำการตรวจสอบว่าเป็นเลขคู่เลขคี่</p></label>
                <input type="number" name="number" id="number" class="form-control w-50 mx-auto" required placeholder="Enter a number ">
            </div>
            <button type="submit" class="btn btn-primary mt-2">Check</button>
        </form>

            <?php
            $get_number = $_post['number'] ?? null; // รับค่าตัวด้านขวา รับค่าตัวแปรที่ส่งมาจากฟอร์ม  null ถ้าไม่มีการส่งค่าเข้ามา
            if (isset($_POST['number'])) {
                $number = $_POST['number'];
                if ($number % 2 == 0) {
                    echo "<h3 class = 'text-success text-center'>$get_number เป็นเลขคู่</h3>";
                } else {
                    echo "<h3 class = 'text-danger text-center'>$get_number เป็นเลขคี่</h3>";
                }
            } 
            ?>
            <br>
            <a href="Index.php">Back to Menu</a>   
            <hr>
            </div>
          






</body>
</html>