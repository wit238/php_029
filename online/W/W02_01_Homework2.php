<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CalculateMoney</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5 ">
        <h1 class="text-center ">PHP Calculate Money</h1>
        <hr>
        <p class="text-center">กรุณากรอกข้อมูลเพื่อทำการคำนวณยอดเงิน</p>

        <form action="" method="post" class="text-center">
            <div class="row justify-content-center mb-3">
                <div class="form-group col-md-5">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price"
                        value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>"
                        class="form-control w-100 mx-auto" placeholder="Enter a Price" required>
                </div>
                <div class="form-group col-md-5">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" id="amount"
                        value="<?php echo isset($_POST['amount']) ? $_POST['amount'] : ''; ?>"
                        class="form-control w-100 mx-auto" placeholder="Enter a Amount" required>
                </div>
            </div>

            <div>
                <div>
                    <label class="form-lable d-block" for=""> membership </label>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="member" id="member" value="1"
                        <?php
                        echo (isset($_POST['member']) && $_POST['member'] == '1') ? 'checked' : '';
                        ?>
                        >
                        <label for="member"> Member (10% Discount) </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="member" id="member" value="0"
                        <?php
                        echo (isset($_POST['member']) && $_POST['member'] == '0') ? 'checked' : '';
                        ?>  
                        >
                        <label for="member"> Not a Member </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-danger btn-lg mt-3 mb-3 ">Calculate</button>
            <button type="button" class="btn btn-secondary btn-lg mt-3 mb-3 " onclick="clearGrade()">Reset All</button>

        </form>
        <div class="row justify-content-center mt-4">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white text-center fw-bold fs-5">
                            Show Result
                        </div>
                 
        <div id="calculate" class="card-body">
            <?php
            if (isset($_POST['price']) && isset($_POST['amount'])) {
                $price = $_POST['price'];
                $amount = $_POST['amount'];

                if (is_numeric($price) && is_numeric($amount)) {
                    $price = floatval($price);
                    $amount = floatval($amount);
                    $total = $price * $amount; // คำนวณยอดรวม
                    $discount = $total * 0.1;
                    $total_paid = $total;

                    if (isset($_POST['member'])&&$_POST['member'] == '1') {
                        $total_paid =$total-$discount; // หักส่วนลด 10% สำหรับสมาชิก
                        echo "<ul class='list-group list-group-flush'>";
                        echo "<li class='list-group-item'>Price of product: <strong>" . number_format($price, 2) . "</strong></li>";
                        echo "<li class='list-group-item'>Amount of product: <strong>" . number_format($amount, 2) . "</strong></li>";
                        echo "<li class='list-group-item'>Total: <strong>" . number_format($total, 2) . "</strong></li>";
                        echo "<li class='list-group-item text-success'>Member Discount (10%): <strong>" . number_format($discount, 2) . "</strong></li>";  
                        echo "<li class='list-group-item text-danger'>Total Paid: <strong>" . number_format($total_paid, 2) . "</strong></li>";
                        echo "</ul>";
                    }else {
                        echo "<ul class='list-group list-group-flush'>";
                        echo "<li class='list-group-item'>Price of product: <strong>" . number_format($price, 2) . "</strong></li>";
                        echo "<li class='list-group-item'>Amount of product: <strong>" . number_format($amount, 2) . "</strong></li>";
                        echo "<li class='list-group-item'>Total: <strong>" . number_format($total, 2) . "</strong></li>";
                        echo "<li class='list-group-item text-danger'>Total Paid: <strong>" . number_format($total_paid, 2) . "</strong></li>";
                        echo "</ul>";
                    }
                        
                       


                } else {
                    echo "<div class='alert alert-danger text-center'>Please input value for price and Amount.</div>";


                }


            } else {
                echo "<div class='alert alert-secondary text-center'>Please input Price and Amount.</div>";
            }
            ?>

        </div>
            </div>
              </div>
                 </div>
                  

        <hr>
    </div>
   <button style="background-color:rgb(188, 246, 246); border: none; border-radius: 5px; padding: 8px; margin: 8px; "
	type="button" onclick="window.location.href='W01_Introphp.php'">
	<a href="index.php" style="color:rgb(128, 147, 255); text-decoration: none; font-size: 16px; font-weight: bold; ">Back to Home</a></button>
            <hr>
        </div>
    <script>
        // ฟังก์ชันสำหรับล้างผลลัพธ์เกรดและช่องกรอกคะแนน
        function clearGrade() {
            document.getElementById('price').innerHTML = '';
            document.getElementById(member1).checked = false;
            document.getElementById(member2).checked = true;
            document.getElementById('price').value = '';
            document.getElementById('amount').value = '';
        }  
    </script>
</body>

</html>
