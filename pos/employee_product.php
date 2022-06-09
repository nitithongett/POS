<?php 

    session_start();
    require_once 'config/db.php';
    if (!isset($_SESSION['employee_login'])) {
        header('location: index.php');
    }

    $firstname = $_SESSION['firstname'];
    $lastname = $_SESSION['lastname'];
    
    $userdata = $conn->prepare("SELECT * FROM users WHERE firstname = '$firstname' AND lastname = '$lastname'");
    $userdata->execute();
    $rowuserdata = $userdata->fetch(PDO::FETCH_ASSOC);

    if ($rowuserdata['urole'] != 'Employee') {
        unset($_SESSION['user_login']);
        unset($_SESSION['admin_login']);
        header('location: index.php');
    }
    
    if (isset($_REQUEST['delete_id'])) {
        $id = $_REQUEST['delete_id'];

        $select_stmt = $conn->prepare('SELECT * FROM products WHERE id = :id');
        $select_stmt->bindParam(':id', $id);
        $select_stmt->execute();
        $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
        unlink("upload/".$row['image']);
        
        $delete_stmt = $conn->prepare('DELETE FROM products WHERE id = :id');
        $delete_stmt->bindParam(':id', $id);
        $delete_stmt->execute();

        header("location: employee_product.php");
    }
    
    if (isset($_POST['addproduct'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $type = $_POST['type'];
        $price = $_POST['price'];
        $amount = 0;
        $status = "ไม่พร้อมขาย";

        $image_file = $_FILES['file']['name'];
        $itype = $_FILES['file']['type'];
        $size = $_FILES['file']['size'];
        $temp = $_FILES['file']['tmp_name'];
        $product_num = rand(0,9999)."1111".rand(0,99999);
        $path = "upload/" . $image_file;  //set upload folder path

        if ($itype == "image/jpg" || $itype == "image/jpeg" || $itype == "image/png" || $itype == "image/gif") {
            if (!file_exists($path)) {  // check file not exist in your upload folder path
                if ($size < 5000000) {  // check file size
                    move_uploaded_file($temp, 'upload/'.$image_file); //move upload file temperary to upload folder
                } else {
                    $errorMsg = "Your file too large.";
                }
            } else {
                $errorMsg = "File already exists.";
            }
        } else {
            $errorMsg = "Upload JPG, JPEG, PNG and GIF file formate.";
        }

        if (empty($name)) {
            $_SESSION['error'] = 'กรุณากรอกชื่อสินค้า';
        } else if(empty($description)) {
            $_SESSION['error'] = 'กรุณากรอกคำอธิบาย';
        } else if(empty($type)) {
            $_SESSION['error'] = 'กรุณากรอกประเภทสินค้า';
        } else if(empty($price)) {
            $_SESSION['error'] = 'กรุณากรอกราคา';
        } else if(empty($image_file)){
            $_SESSION['error'] = 'กรุณาอัปโหลดรูปภาพ';
        } else if($price){
            if(is_numeric($price)){
                try {
                    $stmt = $conn->prepare("INSERT INTO products(name, description, price, amount, status, image, type, product_num) 
                    VALUES(:name, :description, :price, :amount, :status, :image, :type ,:product_num)");
                    $stmt->bindParam(":name", $name);
                    $stmt->bindParam(":description", $description);
                    $stmt->bindParam(":price", $price);
                    $stmt->bindParam(":amount", $amount);
                    $stmt->bindParam(":status", $status);
                    $stmt->bindParam(":type", $type);
                    $stmt->bindParam(":product_num", $product_num);
                    $stmt->bindParam(":image", $image_file);
                    $stmt->execute();
                    $_SESSION['success'] = "เพิ่มข้อมูลเรียบร้อยแล้ว"; 
            } catch (PDOException $e) {
                    echo $e->getMessage();
            }
            } else {
                $_SESSION['error'] = 'กรุณากรอกราคาเป็นตัวเลข เช่น 100 หรือ 100.5';
            }
        }
    }

    if (isset($_POST['addstock'])) {
        $product_num = $_POST['id'];
        $amount = $_POST['amount'];
        
        if (empty($product_num)) {
            $_SESSION['error'] = 'กรุณากรอกรหัสสินค้า';
        } else if(empty($amount)) {
            $_SESSION['error'] = 'กรุณากรอกจำนวนสินค้า';
        } else if($amount){
            if (is_numeric($amount)){
                $select_stmt = $conn->prepare('SELECT * FROM products WHERE product_num = :product_num');
                $select_stmt->bindParam(':product_num', $product_num);
                $select_stmt->execute();
                $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
                
                $name = $row['name'];
                $emp_name = $rowuserdata['firstname']." ".$rowuserdata['lastname'];
                $stmt = $conn->prepare("INSERT INTO stockpd(name, amount, emp_name) 
                VALUES(:name, :amount, :emp_name)");
                $stmt->bindParam(":name", $name);
                $stmt->bindParam(":amount", $amount);
                $stmt->bindParam(":emp_name", $emp_name);
                $stmt->execute();
            
                $amount = ($_POST['amount'] + $row['amount']);
                $status = "พร้อมขาย";
            
                $up_stmt = $conn->prepare("UPDATE products SET amount = :amount, status = :status WHERE product_num = :product_num");
                $up_stmt->bindParam(":amount", $amount);
                $up_stmt->bindParam(":status", $status);
                $up_stmt->bindParam(":product_num", $product_num);
                $up_stmt->execute();
                $_SESSION['success'] = 'เพิ่มจำนวนสต็อกสินค้าเรียบร้อยแล้ว';
            } else {
                $_SESSION['error'] = 'กรุณากรอกจำนวนสินค้าเป็นตัวเลข เช่น 5';
            }

        }


    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="employee_create_order.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">POS <sup>Employee</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">


            <!-- Divider -->
            <hr class="sidebar-divider">


            <!-- Nav Item - Order -->
            <li class="nav-item">
                <a class="nav-link" href="employee_create_order.php">
                    <i class="fas fa-fw fa-dollar-sign"></i>
                    <span>ทำรายการสั่งซื้อ</span></a>
            </li>              

            <!-- Nav Item - Employess and Customer -->
            <li class="nav-item">
                <a class="nav-link" href="employee_manageCus.php">
                    <i class="fas fa-fw fa-user"></i>
                    <span>ข้อมูลลูกค้า</span></a>
            </li> 

            <li class="nav-item">
                <a class="nav-link" href="employee_product.php">
                    <i class="fas fa-fw fa-box"></i>
                    <span>ข้อมูลสินค้า</span></a>
            </li> 

            <li class="nav-item">
                <a class="nav-link" href="employee_order.php">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>ข้อมูลรายการขายสินค้า</span></a>
            </li> 

            <!-- Divider -->
            <hr class="sidebar-divider">


            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form method="POST" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="">
                        <div>
                            <input type="text" name="srh" class="form-control bg-light border-0 small" placeholder="Search for...">.
                            <input type="submit" name="search" class="btn btn-primary" value="Search">
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>


                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->

                        <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <tbody>                                
                                    <tr>
                                        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $rowuserdata['firstname']; ?> <?php echo $rowuserdata['lastname']; ?></span>
                                    </tr>                                
                                </tbody>
                                <img class="img-profile rounded-circle" src="upload/<?php echo $rowuserdata['image']; ?>">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="index.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->
                <!-- The Modal เพิ่มข้อมูล -->
                <div class="modal" id="addModal">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">เพิ่มข้อมูลสินค้า</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body">
                                <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">ชื่อสินค้า</label>
                                        <input type="text" class="form-control" name="name" aria-describebdy="name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">คำอธิบาย</label>
                                        <input type="text" class="form-control" name="description" aria-describebdy="description">
                                    </div>
                                    <div class="mb-3">
                                        <label for="type" class="form-label">ประเภท</label>
                                        <input type="text" class="form-control" name="type" aria-describebdy="type">
                                    </div>
                                    <div class="mb-3">
                                        <label for="price" class="form-label">ราคา</label>
                                        <input type="text" class="form-control" name="price" aria-describebdy="price">
                                    </div>
                                    <div>
                                        <label for="file" class="form-label">รูปภาพ</label>
                                        <div>
                                            <input type="file" name="file" class="form-control">
                                        </div>
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <input type="submit" name="addproduct" class="btn btn-success" value="เพิ่มข้อมูล">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ปิด</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- The Modal เพิ่ม Stock -->
                <div class="modal" id="addStock">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header">
                                <h4 class="modal-title">เพิ่ม Stock สินค้า</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body">
                                <form action="" method="POST">
                                    <div class="mb-3">
                                        <label for="id" class="form-label">รหัสสินค้า</label>
                                        <input type="text" class="form-control" name="id" aria-describebdy="id">
                                    </div>
                                    <div class="mb-3">
                                        <label for="amout" class="form-label">จำนวน</label>
                                        <input type="text" class="form-control" name="amount" aria-describebdy="amount">
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <input type="submit" name="addstock" class="btn btn-success" value="เพิ่มข้อมูล">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ปิด</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="container-fluid">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">Products</li>
                            <li class="breadcrumb-item active" aria-current="page">Overview</li>
                        </ol>
                    </nav>
                    <h1 class="h2">รายชื่อสินค้า
                        <a href="" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">เพิ่มข้อมูล <i data-feather="plus"></i></a>
                        <a href="" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStock">เพิ่ม Stock <i data-feather="plus"></i></a>
                    </h1>
                    <?php if (isset($_SESSION['error'])) { ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php
                                            echo $_SESSION['error'];
                                            unset($_SESSION['error']);
                                            ?>
                                        </div>
                    <?php } ?>
                    <?php if (isset($_SESSION['success'])) { ?>
                                        <div class="alert alert-success" role="alert">
                                            <?php
                                            echo $_SESSION['success'];
                                            unset($_SESSION['success']);
                                            ?>
                                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-12 col-xl-20 mb-4 mb-lg-0">
                            <div class="card">
                                <h5 class="card-header">Products List</h5>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col" style="text-align: center">รหัสสินค้า</th>
                                                    <th scope="col" style="text-align: center"></th>
                                                    <th scope="col" style="text-align: center">ชื่อสินค้า</th>
                                                    <th scope="col" style="text-align: center">คำอธิบาย</th>
                                                    <th scope="col" style="text-align: center">ประเภท</th>
                                                    <th scope="col" style="text-align: center">ราคา (บาท)</th>
                                                    <th scope="col" style="text-align: center">สินค้าคงเหลือ (ชิ้น)</th>
                                                    <th scope="col" style="text-align: center">สถานะ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    if (isset($_POST['search'])) {

                                                        $srh = $_POST['srh'];
                                                        $check_data = $conn->prepare("SELECT * FROM products WHERE name = '$srh' OR description = '$srh' 
                                                        OR type = '$srh' OR price = '$srh' OR status = '$srh' OR product_num = '$srh' OR id = '$srh'");
                                                        $check_data->execute();
                                                    } else {
                                                        $check_data = $conn->prepare("SELECT * FROM products");
                                                        $check_data->execute();
                                                    }

                                                while ($row = $check_data->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                    <form action="" method="POST">
                                                        <tr>
                                                            <th scope="row"><?php echo $row['product_num']; ?></th>
                                                            <td style="text-align: center"><img src="upload/<?php echo $row['image']; ?>" width="100px" height="100px" alt=""></td>
                                                            <td style="text-align: center"><?php echo $row['name']; ?></td>
                                                            <td style="text-align: center"><?php echo iconv_substr($row['description'],0,50,'UTF-8')."..."; ?></td>
                                                            <td style="text-align: center"><?php echo $row['type']; ?></td>
                                                            <td style="text-align: center"><?php echo number_format($row['price'],2); ?></td>
                                                            <td style="text-align: center"><?php if($row['amount'] == 0){
                                                                echo "หมด";
                                                            } else {
                                                                echo $row['amount'];
                                                            }?></td>
                                                            <td style="text-align: center;color:<?php 
                                                                if ($row['status'] == "พร้อมขาย") {
                                                                    echo "green";
                                                                } else {
                                                                    echo "red";
                                                                }
                                                            ?>"><?php echo $row['status']; ?></td>
                                                            <td><a href="employee_edit_product.php?update_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a></td>
                                                            <td><a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Delete</a></td>
                                                        </tr>
                                                    </form>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="" class="btn btn-block btn-light">View All</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; Your Website 2021</span>
                        </div>
                    </div>
                </footer>
                <!-- End of Footer -->

            </div>
            <!-- End of Content Wrapper -->

        </div>
        <!-- End of Page Wrapper -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>

        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="index.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>      

        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="js/sb-admin-2.min.js"></script>

        <!-- Page level plugins -->
        <script src="vendor/chart.js/Chart.min.js"></script>

        <!-- Page level custom scripts -->
        <script src="js/demo/chart-area-demo.js"></script>
        <script src="js/demo/chart-pie-demo.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script>
            feather.replace()
        </script>
</body>

</html>