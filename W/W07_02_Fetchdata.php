<?php
require_once 'W07_01_connectDB.php';
$sql = "SELECT * FROM products";
$result = $conn->query($sql); // ใช้ query() เพื่อรันคำสั่ง sql และรับผลลัพธ์
 
if ($result->rowCount() > 0) {
    // output data of each row
    // echo "<h2>พบข้อมูลในตาราง Product</h2>";
    //$data = $result->fetchAll(PDO::FETCH_NUM);
    //$data = $result->fetchAll(PDO::FETCH_ASSOC); // ดึงข้อมูลทั้งหมดในรูปแบบ associative array
    //$data = $result->fetchAll(PDO::FETCH_BOTH); // ดึงข้อมูลทั้งหมดในรูปแบบ associative array และ numeric array
    //print_r($data); // แสดงข้อมูลที่ดึงมา

    // ใช้ prepared statement เพื่อป้องกัน SQL injection

    // ใช้ execute() เพื่อรันคำสั่ง sql

    // ใช้ fetchAll() เพื่อดึงข้อมูลทั้งหมดในครั้งเดียว

    // ใช้ print_r() เพื่อแสดงข้อมูลที่ดึงมาในรูปแบบ array

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_NUM); 

    // echo "<br>";
    // echo "<pre>";
    //     print_r($data);
    // echo "</pre>";

  // echo "=======================================================================";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC); 

    // echo "<br>";
    // echo "<pre>";
    //     print_r($data);
    // echo "</pre>";
    // echo "=======================================================================";

  
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_BOTH); 

    // echo "<br>";
    // echo "<pre>";
    //     print_r($data);
    // echo "</pre>";
    // แสดงผลข้อมูลที่ดึงมาด้วย JSON

        header('Content-Type: application/json'); // ระบุ Content-Type เป็น JSON
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); // แปลงข้อมูลใน $arr เป็น JSON และแสดงผล

} else {
    echo "<h2>ไม่พบข้อมูลในตาราง Product</h2>";
}
?>