<?php

session_start();
require_once 'config/db.php';
if (!isset($_SESSION['admin_login'])) {
    header('location: index.php');
}

$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];

$userdata = $conn->prepare("SELECT * FROM users WHERE firstname = '$firstname' AND lastname = '$lastname'");
$userdata->execute();
$rowuserdata = $userdata->fetch(PDO::FETCH_ASSOC);

if ($rowuserdata['urole'] != 'Admin') {
    unset($_SESSION['user_login']);
    unset($_SESSION['admin_login']);
    header('location: index.php');
}

if (isset($_REQUEST['update_id'])) {
    try {
        $id = $_REQUEST['update_id'];
        $select_stmt = $conn->prepare('SELECT * FROM products WHERE id = :id');
        $select_stmt->bindParam(':id', $id);
        $select_stmt->execute();
        $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $e->getMessage();
    }
}

if (isset($_REQUEST['btn_update'])) {
    try {
        $name = $_REQUEST['name'];
        $description = $_REQUEST['description'];
        $type = $_REQUEST['type'];
        $price = $_REQUEST['price'];
        $amount = $_REQUEST['amount'];
        $status = $_REQUEST['status'];
        $image_file = $_FILES['file']['name'];
        $itype = $_FILES['file']['type'];
        $size = $_FILES['file']['size'];
        $temp = $_FILES['file']['tmp_name'];

        $path = "upload/" . $image_file;  //set upload folder path
        $direc = "upload/";

        if ($image_file) {
            if ($itype == "image/jpg" || $itype == "image/jpeg" || $itype == "image/png" || $itype == "image/gif") {
                if (!file_exists($path)) {  // check file not exist in your upload folder path
                    if ($size < 5000000) {  // check file size
                        unlink($direc.$row['image']);
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
        } else {
            $image_file = $row['image'];
        }

        $update_stmt = $conn->prepare("UPDATE products SET name = :name, description = :description, type = :type, price = :price, amount = :amount, status = :status, image = :fimage WHERE id = :id");
        $update_stmt->bindParam(':name', $name);
        $update_stmt->bindParam(':description', $description);
        $update_stmt->bindParam(':type', $type);
        $update_stmt->bindParam(':price', $price);
        $update_stmt->bindParam(':amount', $amount);
        $update_stmt->bindParam(':status', $status);
        $update_stmt->bindParam(':fimage', $image_file);
        $update_stmt->bindParam(':id', $id);
        $update_stmt->execute();

        header("location: admin_product.php");
    } catch (PDOException $e) {
        $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home Page</title>
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
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">POS <sup>Admin</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="admin.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
            ข้อมูล
            </div>

            <!-- Nav Item - Employess and Customer -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-user"></i>
                    <span>ข้อมูลพนักงานและลูกค้า</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">จัดการข้อมูล</h6>
                        <a class="collapse-item" href="admin_manageEmp.php">พนักงาน</a>
                        <a class="collapse-item" href="admin_manageCus.php">ลูกค้า</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Product -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-box"></i>
                    <span>ข้อมูลสินค้า</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">จัดการข้อมูลสินค้า</h6>
                        <a class="collapse-item" href="admin_product.php">ข้อมูลสินค้า</a>
                        <a class="collapse-item" href="admin_stock.php">ข้อมูลสต็อกสินค้า</a>
                        <a class="collapse-item" href="admin_order.php">ข้อมูลการขาย</a>
                    </div>
                </div>
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

                <div class="container">
                    <br>
                    <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
                        <?php {
                            $id = $_REQUEST['update_id'];
                            $select_stmt = $conn->prepare('SELECT * FROM products WHERE id = :id');
                            $select_stmt->bindParam(':id', $id);
                            $select_stmt->execute();
                            $row = $select_stmt->fetch(PDO::FETCH_ASSOC);

                        ?>
                            <h2 style="text-align:center">แก้ไขข้อมูลสินค้า</h2>
                            <div class="mb-3">
                                <label for="product_num" class="form-label">รหัสสินค้า</label>
                                <input type="text" class="form-control" name="product_num" aria-describebdy="product_num" value="<?php echo $row['product_num']; ?>" placeholder="<?php echo $row['product_num']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">ชื่อสินค้า</label>
                                <input type="text" class="form-control" name="name" aria-describebdy="name" value="<?php echo $row['name']; ?>" placeholder="<?php echo $row['name']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">คำอธิบาย</label>
                                <input type="text" class="form-control" name="description" aria-describebdy="description" value="<?php echo $row['description']; ?>" placeholder="<?php echo $row['description']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">ประเภท</label>
                                <input type="text" class="form-control" name="type" aria-describebdy="type" value="<?php echo $row['type']; ?>" placeholder="<?php echo $row['type']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">ราคา</label>
                                <input type="text" class="form-control" name="price" aria-describebdy="price" value="<?php echo $row['price']; ?>" placeholder="<?php echo $row['price']; ?>">
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">จำนวนที่เหลือ</label>
                                <input type="text" class="form-control" name="amount" aria-describebdy="amount" value="<?php echo $row['amount']; ?>" placeholder="<?php echo $row['amount']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">สถานะ</label>
                                <input type="text" class="form-control" name="status" aria-describebdy="status" value="<?php echo $row['status']; ?>" placeholder="<?php echo $row['status']; ?>" readonly>
                            </div>
                            <div>
                                <label for="file" class="form-label">รูปภาพ</label>
                                <div>
                                    <input type="file" name="file" class="form-control" value="<?php echo $row['image']; ?>">
                                    <br>
                                    <p><img src="upload/<?php echo $row['image'] ?>" height="100px" width="100px" alt=""></p>
                                </div>
                            </div>  
                            <br>                           
                            <div>
                                <input type="submit" name="btn_update" class="btn btn-success" value="แก้ไขข้อมูล">
                                <a href="admin_product.php" class="btn btn-danger">ยกเลิก</a>
                            </div>
                        <?php } ?>

                    </form>
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