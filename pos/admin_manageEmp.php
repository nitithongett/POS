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
    
    if (isset($_REQUEST['delete_id'])) {
        $id = $_REQUEST['delete_id'];

        $select_stmt = $conn->prepare('SELECT * FROM users WHERE id = :id');
        $select_stmt->bindParam(':id', $id);
        $select_stmt->execute();
        $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
        unlink("upload/".$row['image']);
        
        $delete_stmt = $conn->prepare('DELETE FROM users WHERE id = :id');
        $delete_stmt->bindParam(':id', $id);
        $delete_stmt->execute();

        header("location: admin_manageEmp.php");
    }

    if (isset($_POST['adduser'])) {
        $username = $_POST['username'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $birthday = $_POST['birthday'];
        $password = MD5($_POST['password']);
        $c_password = MD5($_POST['c_password']);
        $urole = $_POST['urole'];
        $image_file = $_FILES['file']['name'];
        $type = $_FILES['file']['type'];
        $size = $_FILES['file']['size'];
        $temp = $_FILES['file']['tmp_name'];

        $path = "upload/" . $image_file;  //set upload folder path

        if ($type == "image/jpg" || $type == "image/jpeg" || $type == "image/png" || $type == "image/gif") {
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


        
        if (empty($username)) {
            $_SESSION['error'] = 'กรุณากรอก username';

        } else if (empty($firstname)) {
            $_SESSION['error'] = 'กรุณากรอกชื่อ';

        } else if (empty($lastname)) {
            $_SESSION['error'] = 'กรุณากรอกนามสกุล';

        } else if (empty($address)) {
            $_SESSION['error'] = 'กรุณากรอกที่อยู่';

        } else if (empty($phone)) {
            $_SESSION['error'] = 'กรุณากรอกเบอร์โทรศัพท์';

        } else if (strlen($_POST['phone']) != 10) {
            $_SESSION['error'] = 'เบอร์โทรศัพท์ต้องมีความยาว 10 ตัวอักษร';

        } else if (empty($email)) {
            $_SESSION['error'] = 'กรุณากรอกอีเมล';

        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';

        } else if (empty($birthday)) {
            $_SESSION['error'] = 'กรุณากรอกวันเกิด';

        } else if (empty($password)) {
            $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';

        } else if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
            $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร';

        } else if (empty($c_password)) {
            $_SESSION['error'] = 'กรุณายืนยันรหัสผ่านอีกครั้ง';

        } else if ($password != $c_password) {
            $_SESSION['error'] = 'รหัสผ่านไม่ตรงกัน';

        } else if (empty($image_file)) {
            $_SESSION['error'] = 'กรุณาอัพโหลดรูปภาพ';

        } else if (preg_match("/^[0-9]{10}$/", $phone)) {
            try {

                $check_username = $conn->prepare("SELECT username FROM users WHERE username = :username");
                $check_username->bindParam(":username", $username);
                $check_username->execute();
                $row = $check_username->fetch(PDO::FETCH_ASSOC);

                if ($row['username'] == $username) {
                    $_SESSION['error'] = 'มี username นี้อยู่ในระบบแล้ว';
        
                } else if (!isset($_SESSION['error'])) {
                    $stmt = $conn->prepare("INSERT INTO users(username, firstname, lastname, address, phone, email, birthday, password , urole, image) 
                    VALUES(:username, :firstname, :lastname, :address, :phone, :email, :birthday, :password, :urole, :image)");
                    $stmt->bindParam(":username", $username);
                    $stmt->bindParam(":firstname", $firstname);
                    $stmt->bindParam(":lastname", $lastname);
                    $stmt->bindParam(":address", $address);
                    $stmt->bindParam(":phone", $phone);
                    $stmt->bindParam(":email", $email);
                    $stmt->bindParam(":birthday", $birthday);
                    $stmt->bindParam(":password", $password);
                    $stmt->bindParam(":urole", $urole);
                    $stmt->bindParam(":image", $image_file);
                    $stmt->execute();
                    $_SESSION['success'] = "เพิ่มข้อมูลเรียบร้อยแล้ว";
        

                } else {
                    $_SESSION['error'] = "มีบางอย่างผิดพลาด";
        
                }

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }  else {
            $_SESSION['error'] = "เบอร์โทรศัพท์ไม่ถูกต้อง";
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
                                <h4 class="modal-title">เพิ่มข้อมูลพนักงาน</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body">
                                <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" name="username" aria-describebdy="username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">ชื่อ</label>
                                        <input type="text" class="form-control" name="firstname" aria-describebdy="firstname">
                                    </div>
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">นามสกุล</label>
                                        <input type="text" class="form-control" name="lastname" aria-describebdy="lastname">
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label">ที่อยู่</label>
                                        <input type="text" class="form-control" name="address" aria-describebdy="address">
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                        <input type="text" class="form-control" name="phone" aria-describebdy="phone">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">อีเมล</label>
                                        <input type="text" class="form-control" name="email" aria-describebdy="email">
                                    </div>
                                    <div class="mb-3">
                                        <label for="birthday" class="form-label">วันเกิด</label>
                                        <input type="date" class="form-control" name="birthday" aria-describebdy="birthday">
                                    </div>
                                    <div class="mb-3">
                                        <label for="urole" class="form-label">ระดับสมาชิก</label>
                                        <br>
                                        <select name="urole">
                                            <option value="Employee">พนักงาน</option>
                                            <option value="Admin">แอดมิน</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="file" class="form-label">รูปภาพ</label>
                                        <div>
                                            <input type="file" name="file" class="form-control">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">รหัสผ่าน</label>
                                        <input type="password" class="form-control" name="password" aria-describebdy="password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="c_password" class="form-label">ยืนยันรหัสผ่าน</label>
                                        <input type="password" class="form-control" name="c_password" aria-describebdy="c_password">
                                    </div>
                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <input type="submit" name="adduser" class="btn btn-success" value="เพิ่มข้อมูล">
                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">ปิด</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="container-fluid">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Employees</li>
                        <li class="breadcrumb-item active" aria-current="page">Overview</li>
                    </ol>
                    </nav>
                    <h1 class="h2">รายชื่อพนักงาน
                        <a href="" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">เพิ่มข้อมูล <i data-feather="plus"></i></a>
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
                                <h5 class="card-header">Employees List</h5>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col" style="text-align: center"></th>
                                                    <th scope="col" style="text-align: center">ชื่อผู้ใช้งาน</th>
                                                    <th scope="col" style="text-align: center">ชื่อ</th>
                                                    <th scope="col" style="text-align: center">นามสกุล</th>
                                                    <th scope="col" style="text-align: center">ที่อยู่</th>
                                                    <th scope="col" style="text-align: center">เบอร์โทรศัพท์</th>
                                                    <th scope="col" style="text-align: center">อีเมล</th>
                                                    <th scope="col" style="text-align: center">วันเกิด</th>
                                                    <th scope="col" style="text-align: center">ระดับสมาชิก</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php


                                                if (isset($_POST['search'])) {

                                                    $srh = $_POST['srh'];
                                                    $check_data = $conn->prepare("SELECT * FROM users WHERE username = '$srh' OR firstname = '$srh' OR lastname = '$srh' OR address = '$srh'
                                                    OR phone = '$srh' OR email = '$srh' OR birthday = '$srh' OR urole = '$srh' OR id = '$srh'");
                                                    $check_data->execute();

                                                } else {                                   
                                                    $check_data = $conn->prepare("SELECT * FROM users");
                                                    $check_data->execute();
                                                }

                                                while ($row = $check_data->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                    <form action="" method="POST">
                                                        <tr>
                                                            <td style="text-align: center"><img src="upload/<?php echo $row['image']; ?>" width="100px" height="100px" alt=""></td>
                                                            <td style="text-align: center"><?php echo $row['username']; ?></td>
                                                            <td style="text-align: center"><?php echo $row['firstname']; ?></td>
                                                            <td style="text-align: center"><?php echo $row['lastname']; ?></td>
                                                            <td style="text-align: center"><?php echo $row['address']; ?></td>
                                                            <td style="text-align: center"><?php echo $row['phone']; ?></td>
                                                            <td style="text-align: center"><?php echo $row['email']; ?></td>
                                                            <td style="text-align: center"><?php echo $row['birthday']; ?></td>
                                                            <td style="text-align: center"><?php if ($row['urole'] == "Admin") {
                                                                echo "ผู้ดูแลระบบ";
                                                            } else {
                                                                echo "พนักงาน";
                                                            } ?></td>
                                                            <td><a href="admin_edit_Emp.php?update_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Edit</a></td>
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