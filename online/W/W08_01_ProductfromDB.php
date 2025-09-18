<?php
require_once 'W07_01_ConnectDB.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loop for Show Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">

    <style>
       
    </style>
</head>

<body>

    <?php
    $sql = "SELECT * FROM products";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    ?>


    <div class="container mt-5">
        <h1>Product List</h1>

        <form action="" method="post" class="mb-3">
            <div class="d-flex ">
                <input type="number" name="price" placeholder="Enter Price" class="form-control mb-2" required>
                
                <button type="submit" class="btn btn-primary ">Filter</button>
            </div>


        </form>
        <table id="productTable" class=" table table-striped table-bordered">
            <thead>
                <tr>

                    <th>Number ลำดับสินค้า</th>
                    <th>ID รหัสสินค้า</th>
                    <th>Name ชื่อสินค้า</th>
                    <th>Category หมวดหมู่</th>
                    <th>Price ราคาสินค้า</th>
                    <th>Quantity จำนวนสินค้า</th>
                </tr>
            </thead>


            <tbody>
                <?php
                if (isset($_POST['price']) && !empty($_POST['price'])) {
                    $filterPrice = $_POST['price'];
                    $filteredProduct = array_filter($products, function ($product) use ($filterPrice) {
                        return $product['price'] == $filterPrice;
                    });

                    // คือค่า array ใหม่ โดยรีเซต index ใหม่
                    $filteredProduct = array_values($filteredProduct);

                } else { 

                    $filteredProduct = $data;
                }
                ;

                foreach ($filteredProduct as $index => $product) {
                 
                    echo "<tr>";
                    echo "<td>" . $index + 1 . "</td>";
                    echo "<td>" . $product['product_id'] . "</td>";
                    echo "<td>" . $product['product_name'] . "</td>";
                    echo "<td>" . $product['category'] . "</td>";
                    echo "<td>" . $product['price'] . " บาท" . "</td>";
                    echo "<td>" . $product['stock_quantity'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

     <hr>
            <a href="Index.php">Back to Menu</a>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('#');
    </script>
</body>

</html>




