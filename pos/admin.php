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

date_default_timezone_set("Asia/Bangkok");
$year = date("Y");

$jan = "01";
$order_jan = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$jan' AND year(date) = '$year' ");
$order_jan->execute();
$totaljan = 0;
while($row = $order_jan->fetch(PDO::FETCH_ASSOC)) {
    $totaljan = $totaljan + $row['total'];
}

$feb = "02";
$order_feb = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$feb' AND year(date) = '$year' ");
$order_feb->execute();
$totalfeb = 0;
while($row = $order_feb->fetch(PDO::FETCH_ASSOC)) {
    $totalfeb = $totalfeb + $row['total'];
}

$mar = "03";
$order_mar = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$mar' AND year(date) = '$year' ");
$order_mar->execute();
$totalmar = 0;
while($row = $order_mar->fetch(PDO::FETCH_ASSOC)) {
    $totalmar = $totalmar + $row['total'];
}

$apr = "04";
$order_apr = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$apr' AND year(date) = '$year' ");
$order_apr->execute();
$totalapr = 0;
while($row = $order_apr->fetch(PDO::FETCH_ASSOC)) {
    $totalapr = $totalapr + $row['total'];
}

$may = "05";
$order_may = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$may' AND year(date) = '$year' ");
$order_may->execute();
$totalmay = 0;
while($row = $order_may->fetch(PDO::FETCH_ASSOC)) {
    $totalmay = $totalmay + $row['total'];
}

$jun = "06";
$order_jun = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$jun' AND year(date) = '$year' ");
$order_jun->execute();
$totaljun = 0;
while($row = $order_jun->fetch(PDO::FETCH_ASSOC)) {
    $totaljun = $totaljun + $row['total'];
}

$jul = "07";
$order_jul = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$jul' AND year(date) = '$year' ");
$order_jul->execute();
$totaljul = 0;
while($row = $order_jul->fetch(PDO::FETCH_ASSOC)) {
    $totaljul = $totaljul + $row['total'];
}

$aug = "08";
$order_aug = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$aug' AND year(date) = '$year' ");
$order_aug->execute();
$totalaug = 0;
while($row = $order_aug->fetch(PDO::FETCH_ASSOC)) {
    $totalaug = $totalaug + $row['total'];
}

$sep = "09";
$order_sep = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$sep' AND year(date) = '$year' ");
$order_sep->execute();
$totalsep = 0;
while($row = $order_sep->fetch(PDO::FETCH_ASSOC)) {
    $totalsep = $totalsep + $row['total'];
}

$oct = "10";
$order_oct = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$oct' AND year(date) = '$year' ");
$order_oct->execute();
$totaloct = 0;
while($row = $order_oct->fetch(PDO::FETCH_ASSOC)) {
    $totaloct = $totaloct + $row['total'];
}

$nov = "11";
$order_nov = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$nov' AND year(date) = '$year' ");
$order_nov->execute();
$totalnov = 0;
while($row = $order_nov->fetch(PDO::FETCH_ASSOC)) {
    $totalnov = $totalnov + $row['total'];
}

$dec = "12";
$order_dec = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$dec' AND year(date) = '$year' ");
$order_dec->execute();
$totaldec = 0;
while($row = $order_dec->fetch(PDO::FETCH_ASSOC)) {
    $totaldec = $totaldec + $row['total'];
}

$dataPoints1 = array(
	array("label"=> "Jan", "y"=> $totaljan),
	array("label"=> "Feb", "y"=> $totalfeb),
	array("label"=> "Mar", "y"=> $totalmar),
	array("label"=> "Apr", "y"=> $totalapr),
	array("label"=> "May", "y"=> $totalmay),
	array("label"=> "Jun", "y"=> $totaljun),
	array("label"=> "Jul", "y"=> $totaljul),
	array("label"=> "Aug", "y"=> $totalaug),
	array("label"=> "Sep", "y"=> $totalsep),
	array("label"=> "Oct", "y"=> $totaloct),
	array("label"=> "Nov", "y"=> $totalnov),
	array("label"=> "Dec", "y"=> $totaldec),
);

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

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text font-weight-bold text-primary text-uppercase mb-1">
                                                ยอดการขาย (Monthly) <br><?php
                                            date_default_timezone_set("Asia/Bangkok");
                                            $mydate=getdate(date("U"));
                                            $date = "$mydate[month] $mydate[year]";
                                            echo $date; ?></div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php 
                                            date_default_timezone_set("Asia/Bangkok");
                                            $month = date("m");
                                            $year = date("Y");
                                            $check_data = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$month' AND year(date) = '$year' ");
                                            $check_data->execute();
                                            $d_count = $check_data->rowCount();
                                            $total = 0;
                                            while($row = $check_data->fetch(PDO::FETCH_ASSOC)) {
                                                $total = $total + $row['total'];
                                                
                                            }                                                                               
                                            echo number_format($total,2)." บาท" ; 
                                            ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text font-weight-bold text-success text-uppercase mb-1">ยอดการขายวันนี้ <br><?php
                                            date_default_timezone_set("Asia/Bangkok");
                                            $mydate=getdate(date("U"));
                                            $date = "$mydate[mday] $mydate[month] $mydate[year]";
                                            echo $date; ?> </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php 
                                            date_default_timezone_set("Asia/Bangkok");
                                            $datetd = date("Y-m-d");
                                            $check_data = $conn->prepare("SELECT date,total FROM orders WHERE date(date) = '$datetd' ");
                                            $check_data->execute();
                                            $d_count = $check_data->rowCount();                                           
                                            $total = 0;
                                            while($row = $check_data->fetch(PDO::FETCH_ASSOC)) {
                                                $total = $total + $row['total'];
                                                
                                            }                                                                               
                                            echo number_format($total,2)." บาท" ;                                   
                                            ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text font-weight-bold text-info text-uppercase mb-1">จำนวนลูกค้า
                                            </div>
                                            <br>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php
                                            
                                            $check_data = $conn->prepare("SELECT * FROM customers ");
                                            $check_data->execute();
                                            $cus_count = $check_data->rowCount();
                                            echo $cus_count.' คน';
                                            ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text font-weight-bold text-warning text-uppercase mb-1">จำนวนพนักงาน</div>
                                            <br>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php
                                            
                                            $check_data = $conn->prepare("SELECT * FROM users WHERE urole = 'Employee'");
                                            $check_data->execute();
                                            $emp_count = $check_data->rowCount();
                                            echo $emp_count.' คน';
                                            ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div  style="height:400px;overflow-y: scroll;" class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">รายการสั่งซื้อวันนี้</h6>
                                    <form method="POST" class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search" action="">
                                        <div>
                                            <input type="text" name="srh" class="form-control bg-light border-0 small" placeholder="Search for...">.
                                            <input type="submit" name="search" class="btn btn-primary" value="Search">
                                        </div>
                                    </form>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col" style="text-align: center">เลขที่ใบเสร็จ</th>
                                                    <th scope="col" style="text-align: center">วันที่สั่งซื้อ</th>
                                                    <th scope="col" style="text-align: center">ชื่อลูกค้า</th>
                                                    <th scope="col" style="text-align: center">ราคา (บาท)</th>
                                                    <th scope="col" style="text-align: center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    if (isset($_POST['search'])) {

                                                        $srh = $_POST['srh'];
                                                        date_default_timezone_set("Asia/Bangkok");
                                                        $datetd = date("Y-m-d");                                                       
                                                        $check_data = $conn->prepare("SELECT * FROM orders WHERE orders_num = '$srh' OR cusname = '$srh' OR total = '$srh' 
                                                        AND date(date) = '$datetd' ORDER BY date DESC");
                                                        $check_data->execute();
                                                    } else {
                                                        date_default_timezone_set("Asia/Bangkok");
                                                        $datetd = date("Y-m-d");
                                                        $check_data = $conn->prepare("SELECT * FROM orders WHERE date(date) = '$datetd' ORDER BY date DESC");
                                                        $check_data->execute();

                                                        
                                                    }


                                                while ($row = $check_data->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                    <form action="" method="POST">
                                                        <tr>
                                                            <td style="text-align: center"><?php echo $row['id'];?></td>
                                                            <td style="text-align: center"><?php echo $row['date'];?></td>
                                                            <td style="text-align: center"><?php echo $row['cusname'];?></td>                               
                                                            <td style="text-align: center"><?php echo number_format($row['total'],2);?></td>
                                                            <td><a href="admin_order_detail.php?view_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">View</a></td>
                                                        </tr>
                                                    </form>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                        <a href="" class="btn btn-block btn-light">View All</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart -->
                        <div class="col-xl-4 col-lg-4">
                            <div style="height:400px;overflow-y: scroll;" class="card shadow mb-4 ">
                                <!-- Card Header - Dropdown -->
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">รายการสินค้าหมดสต็อก</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                <div class="table-responsive">
                                        <table class="table" style="height:200px;overflow-y: scroll;" class="shadow-sm col-2 p-3">
                                            <thead>
                                                <tr>
                                                    <th scope="col" style="text-align: center"></th>
                                                    <th scope="col" style="text-align: center">รหัสสินค้า</th>
                                                    <th scope="col" style="text-align: center">สถานะ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                        $check_data = $conn->prepare("SELECT * FROM products WHERE status = 'ไม่พร้อมขาย'");
                                                        $check_data->execute();

                                                while ($row = $check_data->fetch(PDO::FETCH_ASSOC)) {
                                                ?>
                                                    <tr>
                                                        <td style="width:90px"><a href=""><img src="upload/<?php echo $row["image"]; ?>" alt="" class="w-100 rounded border"></a></td>
                                                        <td style="text-align: center"><?php echo $row['product_num'];?></td>
                                                        <td style="text-align: center;color: red">สินค้าหมด</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">

                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">จำนวนรายการขายสินค้า</h6>
                                </div>
                                <div class="card-body">
                                    <h4 style="font-size: 18px;" class="text-warning">จำนวนการขายทั้งหมด 
                                            <span class="float-right">All Time</span></h4>
                                    <div>
                                        <h4 style="font-size:14px;color :black;">-<?php
                                            $check_data = $conn->prepare("SELECT date,total FROM orders ");
                                            $check_data->execute();
                                            $d_count = $check_data->rowCount(); 
                                            $total = 0;
                                            while($row = $check_data->fetch(PDO::FETCH_ASSOC)) {
                                                $total = $total + $row['total'];
                                                
                                            }
                                            $total = number_format($total,2);                                                                                                                           
                                            echo " ".$d_count." รายการ" ;
                                            echo ("<span class='float-right'>$total บาท</span>");                                                       
                                            ?>
                                        </h4>
                                    </div>                                    
                                    <h4 style="font-size: 18px;" class="text-info">จำนวนการขายเดือนนี้ 
                                            <span class="float-right"><?php
                                            date_default_timezone_set("Asia/Bangkok");
                                            $mydate=getdate(date("U"));
                                            $date = "$mydate[month] $mydate[year]";
                                            echo $date; ?></span></h4>
                                    <div>
                                        <h4 style="font-size:14px;color :black;">-<?php
                                            date_default_timezone_set("Asia/Bangkok");
                                            $month = date("m");
                                            $year = date("Y");
                                            $check_data = $conn->prepare("SELECT date,total FROM orders WHERE month(date) = '$month' AND year(date) = '$year' ");
                                            $check_data->execute();
                                            $d_count = $check_data->rowCount(); 
                                            $total = 0;
                                            while($row = $check_data->fetch(PDO::FETCH_ASSOC)) {
                                                $total = $total + $row['total'];
                                                
                                            }
                                            $total = number_format($total,2);                                                                                                           
                                            echo " ".$d_count." รายการ" ;
                                            echo ("<span class='float-right'>$total บาท</span>");                                                    
                                            ?>
                                        </h4>
                                    </div>
                                    <h4 style="font-size: 18px;" class="text-success">จำนวนการขายวันนี้ 
                                            <span class="float-right"><?php
                                            date_default_timezone_set("Asia/Bangkok");
                                            $mydate=getdate(date("U"));
                                            $date = "$mydate[mday] $mydate[month] $mydate[year]";
                                            echo $date; ?></span></h4>
                                    <div>
                                    <h4 style="font-size:14px;color :black;">-<?php 
                                            date_default_timezone_set("Asia/Bangkok");
                                            $datetd = date("Y-m-d");
                                            $check_data = $conn->prepare("SELECT date,total FROM orders WHERE date(date) = '$datetd' ");
                                            $check_data->execute();
                                            $d_count = $check_data->rowCount();
                                            $total = 0;
                                            while($row = $check_data->fetch(PDO::FETCH_ASSOC)) {
                                                $total = $total + $row['total'];
                                                
                                            }
                                            $total = number_format($total,2);
                                            echo " ".$d_count." รายการ";
                                            echo ("<span class='float-right'>$total บาท</span>");                                          
                                        ?>
                                        </h4>                                       

                                    </div>
                                </div>
                            </div>

                            <!-- Color System -->
                            <div class="row">
        <div class="col-lg-6 mb-4">
            <a href="admin_manageEmp.php">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body" style="text-align: center;">
                        ข้อมูลพนักงาน    <i class="fas fa-user-tie fa-2x text-gray-250 col-auto"></i>                  
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-6 mb-4">
            <a href="admin_manageCus.php">
                <div class="card bg-success text-white shadow">
                    <div class="card-body" style="text-align: center;">
                        ข้อมูลลูกค้า   <i class="fas fa-users fa-2x text-gray-250 col-auto"></i>                  
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-6 mb-4">
            <a href="admin_product.php">
                <div class="card bg-info text-white shadow">
                    <div class="card-body" style="text-align: center;">
                        ข้อมูลสินค้า   <i class="fas fa-box fa-2x text-gray-250 col-auto"></i>                  
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-6 mb-4">
            <a href="admin_stock.php">
                <div class="card bg-warning text-black shadow">
                    <div class="card-body" style="text-align: center;">
                        ข้อมูลสต็อกสินค้า   <i class="fas fa-box-open fa-2x text-gray-250 col-auto"></i>                  
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-6 mb-4">
            <a href="admin_order.php">
                <div class="card bg-light text-black shadow">
                    <div class="card-body" style="text-align: center;">
                        ข้อมูลการขายสินค้า   <i class="fas fa-clipboard-list fa-2x text-black-250 col-auto"></i>                  
                    </div>
                </div>
            </a>
        </div>
    </div>

</div>
<div class="col-lg-6 mb-4">

    <!-- Illustrations -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ยอดการขายต่อเดือน ปี <?php echo " ".date("Y");?></h6>
        </div>
        <div class="card-body">
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
         </div>
    </div>


        
</div>

                    
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

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

</body>
<script>
window.onload = function () {
 
 var chart = new CanvasJS.Chart("chartContainer", {
     title: {
         text: ""
     },
     theme: "light2",
     animationEnabled: true,
     toolTip:{
         shared: true,
         reversed: true
     },
     axisY: {
         title: "",
         suffix: " บาท"
     },
     legend: {
         cursor: "pointer",
         itemclick: toggleDataSeries
     },
     data: [
         {
             type: "stackedColumn",
             name: "รายได้",            
             yValueFormatString: "#,##0 บาท",
             dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
         }
     ]
 });
  
 chart.render();
  
 function toggleDataSeries(e) {
     if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
         e.dataSeries.visible = false;
     } else {
         e.dataSeries.visible = true;
     }
     e.chart.render();
 }
  
 }
</script>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</html>