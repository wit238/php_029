<?php
// Connect to database แบบ Mysqli
// $host = "localhost";
// $username = "root";
// $password = "";
// $dbname = "db68s_products";

    // $conn = new mysqli($host, $username, $password, $dbname);
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }else {
    //     echo "Connected successfully";
    // }



//connect to database แบบ PDO

$host = "localhost";
$username = "root";
$password = "";
$dbname = "db68s_products";

$dns = "mysql:host=$host;dbname=$dbname;charset=utf8";

try {
   // $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn = new PDO($dns, $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "PDO :     Connected successfully";
} catch (PDOException $e) {
   // echo "PDO : Connection failed: " . $e->getMessage();
}

?>
