<?php
require_once 'configs.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">

    <style>
       
    </style>
</head>

<body>

    <?php
    $sql = "SELECT * FROM tb_664230029";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    ?>


    <div class="container mt-5">
        
        <form action="" method="post" class="mb-3">
        <div class="d-flex justify-content-between mt-4">      
            <h1>รายการนักศึกษา</h1>
             <a href="register.php" class="btn btn-success ">+ เพิ่มนักศึกษา</a>
          
        </div>


        </form>
        <table id="productTable" class=" table table-striped table-bordered">
            <thead>
                <tr>

                    <th>#</th>
                    <th>student ID</th>
                    <th>Name</th>
                    <th>Last name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Created At</th>

                </tr>
            </thead>


            <tbody>

                <?php
                if (isset($_POST['id']) && !empty($_POST['id'])) {
                    $filterid = $_POST['id'];
                    $filteredid = array_filter($ids, function ($id) use ($filterid) {
                        return $id['id'] == $filterid;
                    });

                 
                    $filteredid = array_values($filteredid);

                } else { 

                    $filteredid = $data;
                }
                ;

                foreach ($filteredid as $index => $id) {
                 
                    echo "<tr>";
                    echo "<td>" . $index + 1 . "</td>";
                    echo "<td>" . $id['id'] . "</td>";
                    echo "<td>" . $$id['Fname'] . "</td>";
                    echo "<td>" . $$id['Lname'] . "</td>";
                    echo "<td>" . $$id['Email'] ."</td>";
                    echo "<td>" . $$id['Tel'] . "</td>";
              
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script>
        let table = new DataTable('#productTable');
    </script>
</body>

</html>