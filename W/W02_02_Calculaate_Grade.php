<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <title>Grade</title>
</head>
<body>
  <div class="container mt-5">
      
        <h1 class="text-center text-danger">PHP check Grade A-E from Score</h1>
        <hr>
        <form method="POST" class="mt-4 mb-5 text-center mt-5">
            <div class="form-group mb-3 text-center">
                <label for="number" class="form-label"><p>กรุณากรอกคะแนนเพื่อทำการตรวจสอบว่าได้เกรดอะไร</p></label>
                <input type="number" name="score" id="score" value="<?php echo isset($_POST['score']) ? $_POST['score'] : ''; ?>"
                 class="form-control w-50 mx-auto" required placeholder="Enter a score " require>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Check</button>
            <button type="button" class="btn btn-secondary mt-2" onclick="clearGrade()">reset</button>
        </form>

            <div id ="grade" class="text-center">
                <?php
                $get_score = $_post['score'] ?? null; // รับค่าตัวด้านขวา รับค่าตัวแปรที่ส่งมาจากฟอร์ม  null ถ้าไม่มีการส่งค่าเข้ามา
                if (isset($_POST['score'])) {
                    $score = $_POST['score'];
                    if ($score >= 80 && $score <= 100) {
                        echo "<h3 class = 'text-success text-center'>$get_score คุณได้เกรด A</h3>";
                    } elseif ($score >= 75 && $score < 80) {
                        echo "<h3 class = 'text-success text-center'>$get_score คุณได้เกรด B+</h3>";
                    } elseif ($score >= 70 && $score < 75) {
                        echo "<h3 class = 'text-success text-center'>$get_score คุณได้เกรด B</h3>";
                    } elseif ($score >= 65 && $score < 70) {
                        echo "<h3 class = 'text-success text-center'>$get_score คุณได้เกรด C+</h3>";
                    } elseif ($score >= 60 && $score < 65) {
                        echo "<h3 class = 'text-success text-center'>$get_score คุณได้เกรด C</h3>";
                    } elseif ($score >= 55 && $score < 60) {
                        echo "<h3 class = 'text-success text-center'>$get_score คุณได้เกรด D+</h3>";
                    } elseif ($score >= 50 && $score < 55) {
                        echo "<h3 class = 'text-success text-center'>$get_score คุณได้เกรด D</h3>";
                    } else {
                        echo "<h3 class = 'text-danger text-center'>$get_score คุณได้เกรด F</h3>";
                    }
                }
                
                ?>
            </div>
            <br>


            <a href="Index.php">Back to Menu</a>   
            <hr>
            
    <script>
        // ฟังก์ชันสำหรับล้างผลลัพธ์เกรดและช่องกรอกคะแนน
        function clearGrade() {
            document.getElementById('grade').innerHTML = '';
            document.getElementById('score').value = '';
        }  
    </script>
</body>
</html>