<?php
//connect to database แบบ PDO

$host = "localhost";
$username = "root";
$password = "";
$dbname = "db6646_029";

$dns = "mysql:host=$host;dbname=$dbname;charset=utf8";

    try {

        $conn = new PDO($dns, $username);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "PDO : Connected successfully";

    } catch (PDOException $e) {
        echo "PDO : Connection failed: " . $e->getMessage();
    }




?>