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
    
    if (isset($_REQUEST['view_id'])) {
        try {
            $id = $_REQUEST['view_id'];
            $select_stmt = $conn->prepare('SELECT * FROM orders WHERE id = :id');
            $select_stmt->bindParam(':id', $id);
            $select_stmt->execute();
            $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
    
            $order_detail = $conn->prepare('SELECT * FROM oder_detail WHERE date = :date');
            $order_detail->bindParam(':date', $row['date']);
            $order_detail->execute();
            $orderDT = $order_detail->fetch(PDO::FETCH_ASSOC);
            extract($orderDT);
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
                <div class="container">

                    
                    

                    <h2>ข้อมูลรายการสั่งซื้อ เลขที่ใบเสร็จ <?php echo $row['id'];?></h2>
                    <div class="table-wrapper">
                        <table class="fl-table">
                            
                            <thead>
                            <tr>
                                <th>ชื่อลูกค้า</th>
                                <td><?php echo $row['cusname'];?></td>
                                <th>วันที่</th>
                                <td><?php echo $row['date'];?></td>
                            </tr>
                            <tr>
                                <th>รูปภาพ</th>
                                <th>ชื่อสินค้า</th>
                                <th>จำนวน</th>
                                <th>ราคาต่อชิ้น (บาท)</th>
                                <th>ราคารวม (บาท)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $id = $_REQUEST['view_id'];
                            $select_stmt = $conn->prepare('SELECT * FROM orders WHERE id = :id');
                            $select_stmt->bindParam(':id', $id);
                            $select_stmt->execute();

                            
                            $order_detail = $conn->prepare('SELECT * FROM order_detail WHERE date = :date');
                            $order_detail->bindParam(':date', $row['date']);
                            $order_detail->execute();
                               

                            while ($row = $order_detail->fetch(PDO::FETCH_ASSOC)) { ?>
                            <tr>
                                <td style="text-align: center"><img src="upload/<?php echo $row['image']; ?>" width="100px" height="100px" alt=""></td>
                                <td><?php echo $row['name'];?></td>
                                <td><?php echo $row['qty'];?></td>
                                <td style="text-align: right"><?php echo number_format(($row['price']/$row['qty']),2);?></td>
                                <td style="text-align: right"><?php echo number_format($row['price'],2);?></td>

                            </tr>
                            <?php
                            }
                            ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            <?php
                                if (isset($_REQUEST['view_id'])) {
                                    try {
                                        $id = $_REQUEST['view_id'];
                                        $select_stmt = $conn->prepare('SELECT * FROM orders WHERE id = :id');
                                        $select_stmt->bindParam(':id', $id);
                                        $select_stmt->execute();
                                        $row = $select_stmt->fetch(PDO::FETCH_ASSOC);
                                        extract($row);

                                    } catch (PDOException $e) {
                                        $e->getMessage();
                                    }
                                }
                                
                                echo("<td style='text-align: right'><b>ราคารวม :</b></td>");
                                echo("<td class='text-right'>" .number_format($row['total']*100/107,2)."</td>");
                                echo("</tr>");
                                echo("<tr>");
                                echo("<td colspan='4' class='text-right'><b>VAT 7% :</b></td>");
                                echo("<td class='text-right'>" .number_format(($row['total'] - ($row['total']*100/107)),2)."</td>");
                                echo("</tr>");
                                echo("<tr>");
                                echo("<td colspan='4' class='text-right'><b>ราคารวมสุทธิ :</b></td>");
                                echo("<td class='text-right'><b>" .number_format($row['total'],2)."</b></td>");
                                echo("</tr>");
                                ?>
                            </tr>
                            <tbody>
                        </table>
                        <div>
                            <br>
                            <a href="employee_order.php" class="btn btn-secondary" style="float: right;">ย้อนกลับ</a>
                            <a class="btn btn-success" href="" data-toggle="modal" data-target="#DTModal" style="float: right;">ใบเสร็จ</a>
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
        <!-- Buy Modal-->
        <div class="modal fade" id="DTModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document" style="max-width: 52%;" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark" id="exampleModalLabel" >ใบเสร็จ</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <div class="invoice">
		<div class="company-address">
			<hb class="text-dark">ร้านขายสินค้า POS</hb>
			<br />
			42/1 ซอย14 ถนนลงหาดบางแสน
			<br />
			ตำบลแสนสุข อำเภอเมืองชลบุรี
			<br />
            จังหวัดชลบุรี 20130
            <br>
            อีเมล : pointofsale@buu.ac.th
		</div>
	
		<div class="invoice-details">
            เลขที่ใบเสร็จ : <?php echo $row['id'];?>
            <br>
			วันที่ : <?php
            $order_detail = $conn->prepare('SELECT * FROM order_detail WHERE date = :date');
            $order_detail->bindParam(':date', $row['date']);
            $order_detail->execute();
            $roworder = $order_detail->fetch(PDO::FETCH_ASSOC);

            date_default_timezone_set("Asia/Bangkok");
             echo date("Y-m-d", strtotime($roworder['date']));
             echo "<br>";
             echo "เวลา : ".date("h:i:s a", strtotime($roworder['date']));
             echo "<br>";
             echo "พนักงานขาย : ".$roworder['empname'];
             ?>
		</div>
		
		<div class="customer-address">
			ชื่อลูกค้า : <?php 
            $cus = $conn->prepare('SELECT * FROM orders WHERE date = :date');
            $cus->bindParam(':date', $row['date']);
            $cus->execute();
            $cus = $cus->fetch(PDO::FETCH_ASSOC);

            echo $cus['cusname'];
			echo "<br>";
            echo "เบอร์โทรศัพท์ : ".$cus['phone'];
            echo "<br>";
            echo "ที่อยู่ : ".$cus['address']; 
            echo "<br>";
            echo "อีเมล : ".$cus['email'];
            ?>
			
		</div>
		
		<div class="clear-fix"></div>
			<table border='1' cellspacing='0'>
				<tr>
					<th width=250>ชื่อสินค้า</th>
					<th width=80>จำนวน</th>
					<th width=100>ราคาต่อชิ้น (บาท)</th>
					<th width=100>ราคารวม (บาท)</th>
				</tr>

			<?php	
            $order_detail = $conn->prepare('SELECT * FROM order_detail WHERE date = :date');
            $order_detail->bindParam(':date', $row['date']);
            $order_detail->execute();
            $total_price = 0;		
			foreach($order_detail as $item) {
                $item_price = ($item["price"] / $item['qty']);
                $name = $item['name'];
                $amount = $item['qty'];
                $price = $item["price"];
                $total_price = $total_price + $price;
                echo("<tr>");
                echo("<td style='font-size: 14px'>$name</td>");
                echo("<td class='text-center'>$amount</td>");
                echo("<td class='text-right'>".number_format($item_price,2)."</td>");
                echo("<td class='text-right'>".number_format($price,2)."</td>");
                echo("</tr>");
            }
            

            echo("<tr>");
            echo("</tr>");
            echo("<tr>");
            echo("<td colspan='3' class='text-right'><b>ราคารวม :</b></td>");
            echo("<td class='text-right'>" .number_format($total_price*100/107,2)."</td>");
            echo("</tr>");
            echo("<tr>");
            echo("<td colspan='3' class='text-right'><b>VAT 7% :</b></td>");
            echo("<td class='text-right'>" .number_format(($total_price - ($total_price*100/107)),2)."</td>");
            echo("</tr>");
            echo("<tr>");
            echo("<td colspan='3' class='text-right'><b>ราคารวมสุทธิ :</b></td>");
            echo("<td class='text-right'><b>" .number_format($total_price,2)."</b></td>");
            echo("</tr>");
			?>
			</table>
		</div>                       
                    </div>
                    <div class="modal-footer">
                        <a href=""  class="btn btn-success">Print</a>
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
<style>
    body {		
			font-family: Verdana;
		}
		
		div.invoice {
		border:1px solid #ccc;
		padding:10px;
		width:570pt;
		}

		div.company-address {
			border:1px solid #ccc;
			float:left;
			width:200pt;
		}
		
		div.invoice-details {
			border:1px solid #ccc;
			float:right;
			width:200pt;
		}
		
		div.customer-address {
			border:1px solid #ccc;
			float:right;
			margin-bottom:20px;
			margin-top:100px;
			width:200pt;
		}
		
		div.clear-fix {
			clear:both;
			float:none;
		}
		
		table {
			width:100%;
		}
		
		th {
			text-align: left;
		}
		
		td {
		}
		
		.text-left {
			text-align:left;
		}
		
		.text-center {
			text-align:center;
		}
		
		.text-right {
			text-align:right;
		}
    *{
    box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
}
body{
    font-family: Helvetica;
    -webkit-font-smoothing: antialiased;
    background: rgba( 71, 147, 227, 1);
}
h2{
    text-align: center;
    font-size: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: black;
    padding: 30px 0;
}

/* Table Styles */

.table-wrapper{
    margin: 5px 70px 70px;
    box-shadow: 0px 35px 50px rgba( 0, 0, 0, 0.2 );
}

.fl-table {
    border-radius: 5px;
    font-size: 16px;
    font-weight: normal;
    border: none;
    border-collapse: collapse;
    width: 100%;
    max-width: 100%;
    white-space: nowrap;
    background-color: white;
}

.fl-table td, .fl-table th {
    text-align: center;
    padding: 8px;
}

.fl-table td {
    border-right: 1px solid #f8f8f8;
    font-size: 16px;
}

.fl-table thead th {
    color: #ffffff;
    background: #324960;
}


.fl-table thead th:nth-child(odd) {
    color: #ffffff;
    background: #324960;
}

.fl-table tr:nth-child(even) {
    background: #ffffff;
}

/* Responsive */

@media (max-width: 767px) {
    .fl-table {
        display: block;
        width: 100%;
    }
    .table-wrapper:before{
        content: "Scroll horizontally >";
        display: block;
        text-align: right;
        font-size: 11px;
        color: white;
        padding: 0 0 10px;
    }
    .fl-table thead, .fl-table tbody, .fl-table thead th {
        display: block;
    }
    .fl-table thead th:last-child{
        border-bottom: none;
    }
    .fl-table thead {
        float: left;
    }
    .fl-table tbody {
        width: auto;
        position: relative;
        overflow-x: auto;
    }
    .fl-table td, .fl-table th {
        padding: 20px .625em .625em .625em;
        height: 60px;
        vertical-align: middle;
        box-sizing: border-box;
        overflow-x: hidden;
        overflow-y: auto;
        width: 120px;
        font-size: 13px;
        text-overflow: ellipsis;
    }
    .fl-table thead th {
        text-align: left;
        border-bottom: 1px solid #f7f7f9;
    }
    .fl-table tbody tr {
        display: table-cell;
    }
    .fl-table tbody tr:nth-child(odd) {
        background: none;
    }
    .fl-table tr:nth-child(even) {
        background: transparent;
    }
    .fl-table tr td:nth-child(odd) {
        background: #F8F8F8;
        border-right: 1px solid #E6E4E4;
    }
    .fl-table tr td:nth-child(even) {
        border-right: 1px solid #E6E4E4;
    }
    .fl-table tbody td {
        display: block;
        text-align: center;
    }
}
</style>
</html>