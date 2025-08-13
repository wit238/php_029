<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Basic</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">
    <style>
    

    </style>

</head>
<body>
    <a href="Index.php">Back to Menu</a>
    <br>
    <h1>Welcome to PHP Basics</h1>
    <p>This page demonstrates the basic syntax of PHP.</p>

    <hr>
    <H1 style="color: red;">Basic PHP Syntax</H1>

    <pre>       &lt;?php 
                echo "Hello, World!";
        ? &gt;</pre>

    <h2>Result</h3>


   <div style="color: blue;">
       <?php
        echo "Hello, World!.<br>";
       
        print "<span style=color:red;>Witthawat Chankimha </span>";    // This is a comment
     
        ?>
   </div>

    <hr>
    <h1 style="color: red;">PHP Variables</h1>
    <pre>       &lt;?php 
                $greeting = "Hello, World!";
                echo $greeting;
        ? &gt;</pre>
    <h2>Result</h2>

    <div style="color: blue;"> 
        <?php
        $greeting = "<span style=color:blue;>  Hello, World! </span>";
        echo $greeting;
        ?>
    </div>
    <hr>

    <h1 style="color: red;" >แบบฝึกหัด</h1>
    <?php
    $age = 20;
    echo "<span style=color:blue;>I am $age years old.</span><br>";
    ?>
    <hr>

    <H1 style="color: red;">Calculate with Variables</H1>
    <?php
    $num1 = 5;
    $num2 = 4;
    echo "<span style=color:blue;>The sum of $num1 and $num2 is " . ($num1 + $num2) . ".</span><br>";
    ?>
    <hr>

    <h1 style="color: red;">คำนวนพื้นที่สามเหลี่ยม</h1>
    <?php
    $base = 10;
    $height = 5;    
    $area = 0.5 * $base * $height;
    echo "<span style=color:blue;>พื้นที่ของ3 เหลี่ยมคือ  $area ตารางหน่วย</span><br>";
    ?>
    <hr>
    <h1 style="color: red;">คำนวนอายุจากปีเกิด</h1>
    <?php
    $birthYear = 2000;
    $currentYear = date("Y");
    $age = $currentYear - $birthYear;
    echo "<span style=color:blue;>อายุของคุณคือ $age ปี</span><br>";
    ?>
    <hr>


     <h1 style="color: red;">If-Else</h1>
     <!-- เกณฑ์ผ่านการสอบ ต้องได้คะแนนมากว่า 60 คะแนน -->
    <?php
    $score = 75; // เปลี่ยนค่า score เพื่อทดสอบ  
    echo "<span style=color:blue;>คะแนนของคุณคือ $score</span><br>";
    if ($score >= 60) {
        echo "<span style=color:blue;>คุณผ่านการสอบ</span><br>";
    } else {
        echo "<span style=color:red;>คุณไม่ผ่านการสอบ</span><br>";
    }
    ?>
    <h1 style="color: red;">Boolean Variable</h1>

    <?php
    $isStudent = true;
    echo "<span style=color:blue;>คุณเป็นนักศึกษาใช่หรือไม่ </span><br>";

    if (!$isStudent ) {
        echo "<span style =color:blue;>ใช่ คุณเป็นนักศึกษา</span><br>";
    } else {
        echo "<span style=color:red;>ไม่ คุณไม่เป็นนักศึกษา</span><br>";  
    }
    ?>
    <hr>

    <h1 style="color: red;">Loop</h1>
    <h2>For Loop</h2>
    <h3>แสดงตัวเลข 1-10</h3>
    <H1>แสดงตัวเลขที่บวกกัน</H1>
    <?php


    $sum = 0;
    for ($i = 5; $i <= 9; $i++) {
        echo "  $i  ";
        $sum += $i;

        if ($i < 9) {
            echo " + ";
        } else {
            echo " =";
        }
     }
    echo " $sum";
    ?>

    <hr>
    <h2>While Loop</h2>
    <h3>สูตรคูณ แม่ 2</h3>

    <?php
    $i = 1;
    while ($i <= 12) {
        echo "2 x $i = " . (2 * $i) . "<br>";
        $i++;
    }
    ?>

    <hr>

    <H1>ตารางสูตรคูณ แม่ 5 แบบตาราง</H1>

    <table class=" table table-bordered table-striped w-auto mx-auto text-center">
        <thead class="table-dark text-center">
            <tr>
                <th>ลำดับ</th>
                <th>สูตร</th>
                <th>ผลลัพธ์</th>
            </tr>
        </thead>
        <tbody> 

        <?php
        for ($i = 1; $i <= 12; $i++) {
            echo "<tr>";
            echo "<td>$i</td>";
            echo "<td>5 x $i</td>";
            echo "<td>" . (5 * $i) . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <hr>
    <h2>สร้างตัวแปรอาเรย์ แบบที่ 1: Indexed Array</h2>
    <h5>PHP จะกำหนด index เป็นตัวเลขอัตโนมัติ โดยเริ่มจาก 0</h5>
    
    <?php
    $fruits =["Apple", "Banana", "Cherry"];
    
    ?>
     <h3>แสดงรายการผลไม้ โดยใช้ index</h3>
    <div style="color:blue; background-color: lightgray; padding: 10px;">
        <?php
    echo "ผลไม้ที่ 1: " . $fruits[0] . "<br>";
    echo "ผลไม้ที่ 2: " . $fruits[1] . "<br>";
    echo "ผลไม้ที่ 3: " . $fruits[2] . "<br>";
    
    ?>
</div>
<br>

<div style="color:red; background-color: lightgray; padding: 10px;">
<?php
echo "รายการผลไม้ :<br>";
echo "ผลไม้ที่ 1: " . $fruits[0] . "<br>";
echo "ผลไม้ที่ 3: " . $fruits[2] . "<br>";


?>
</div>

    <br>
    <h4>แสดงรายการผลไม้ โดยใช้ print readable</h4>
    <div style="color:blue; background-color: lightgray; padding: 10px;">
        <?php
            echo "รายการผลไม้: <br>";
            print_r($fruits); // แสดงผลอาเรย์ทั้งหมด  print readable
            echo "<br>";

        ?>
    </div>
<h3>แสดงจำนวนสมาชิก</h3>
<div style="color:red; background-color: lightgray; padding: 10px;">
    <?php
            echo "จำนวนสมาชิกในอาเรย์: " . count($fruits) . "ชนิด<br>";

    ?>
</div>
    <h4>แสดงรายการผลไม้ โดยใช้ implode เพื่อแปลงอาเรย์เป็นสตริง</h4>
    <div style="color:blue; background-color: lightgray; padding: 10px;">
        <?php
            // แสดงรายการผลไม้และจำนวนสมาชิกในอาเรย์
            // ใช้ implode เพื่อแปลงอาเรย์เป็นสตริง และแสดงผลลัพธ์
            echo "รายการผลไม้: " . implode(", ", $fruits) . "<br>"; // ผลลัพธ์: Apple, Banana, Cherry
            echo "<br>";
        ?>
    </div>

       <h4>แสดงรายการผลไม้ ใช้คำสั่ง foreach เพื่อวนลูป</h4>
    <div style="color:blue; background-color: lightgray; padding: 10px;">
        <?php
            // ใช้คำสั่ง foreach เพื่อวนลูปค่าใน array ทีละตัว โดยในแต่ละรอบ ตัวแปร $fruit จะเก็บค่าผลไม้ 1 ชนิด
        foreach ($fruits as $fruit){
            if($fruit == end($fruits)){
            echo "$fruit. <br>";
          }else{
            echo "$fruit, ";
            }
        }
        ?>
        
            </div>
        
                    <hr>
                <h2>สร้างตัวแปรอาเรย์ แบบที่ 2: Associative Array</h2>
                <h6>เป็น array ซ้อนกันหลายชุด (Multidimensional array)</h6>
                <h6>แต่ละชุดเป็น associative array ที่ระบุชื่อ key ชัดเจน เช่น "name" และ "price"</h6>
                <h6>ใช้สำหรับเก็บข้อมูลที่มีความสัมพันธ์กัน key => value เช่น รายการสินค้า</h6>
        
        
                <?php
                    // สร้างอาเรย์ของผลไม้ที่มีชื่อและราคา
                    $products = [
                        ["name" => "Apple", "price" => 30],
                        ["name" => "Banana", "price" => 20],
                        ["name" => "Cherry", "price" => 15]
                    ];
                ?>
                 <h4>แสดงรายการผลไม้ ใช้คำสั่ง key value</h4>
    <div style="color:blue; background-color: lightgray; padding: 10px;">
        <?php
            // แสดงผลลัพธ์ของการเข้าถึงข้อมูลในอาเรย์
            echo $products[0]["name"] . "<br>";  // Apple
            echo $products[2]["price"] . "<br>"; // 15
        ?>
    </div>
      <h4>แสดงรายการสินค้า ใช้คำสั่ง foreach เพื่อวนลูป</h4>
    <div style="color:blue; background-color: lightgray; padding: 10px;">
        <?php
        $totel_price = 0; // ตัวแปรสำหรับเก็บผลรวมราคา
       foreach ($products as $product) {
            echo "ชื่อสินค้า: " . $product["name"] . ", ราคา: " . $product["price"] . " บาท<br>";
            $totel_price += $product["price"];
            echo "รวมราคา: " . $totel_price . " บาท<br>";
        }
        ?>
    </div>  
    <hr>

        
            <hr>
            <a href="Index.php">Back to Menu</a>
        </body>
        </html>
        ?>
           

   
            
          