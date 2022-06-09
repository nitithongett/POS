<?php 

    session_start();
    require_once 'config/db2.php';
    $db_handle = new DBcontroller();

    if (!isset($_SESSION['employee_login'])) {
        header('location: index.php');
    }

    $firstname = $_SESSION['firstname'];
    $lastname = $_SESSION['lastname'];
    
    $userdata = $conn->prepare("SELECT * FROM users WHERE firstname = '$firstname' AND lastname = '$lastname'");
    $userdata->execute();
    $rowuserdata = $userdata->fetch(PDO::FETCH_ASSOC);

    $cus_id = 0;
    $datacusname = "";
    if(isset($_GET['cus_id'])) {
        $cus_id = $_GET["cus_id"];
        $cusdata = $conn->prepare("SELECT * FROM customers WHERE id = '$cus_id'");
        $cusdata->execute();
        $rowcusdata = $cusdata->fetch(PDO::FETCH_ASSOC);
        $datacusname = $rowcusdata['firstname']." ".$rowcusdata['lastname'];
        
    }
 
    if ($rowuserdata['urole'] != 'Employee') {
        unset($_SESSION['user_login']);
        unset($_SESSION['admin_login']);
        header('location: index.php');
    }




    if(!empty($_GET["action"])) {
        switch($_GET["action"]) {
            case "add";
                $productById = $db_handle->runQuery("SELECT * FROM products WHERE id ='" . $_GET["id"]. "'");
                if(!empty($_POST["quantity"])) {
                    if($_POST['quantity'] > $productById[0]['amount']){
                        $_SESSION['amount'] = "จำนวนสินค้าคงเหลือไม่เพียงพอ";
                        break;
                    } else {
                        $itemArray = array($productById[0]["id"]=>(array('name'=>$productById[0]["name"],
                                                                        'id'=>$productById[0]["id"],
                                                                        'quantity'=>$_POST["quantity"],
                                                                        'price'=>$productById[0]["price"],
                                                                        'image'=>$productById[0]["image"])));
                    }

                }

                if(!empty($_SESSION["cart_item"])) {
                    if(in_array($productById[0]["id"],array_keys($_SESSION["cart_item"]))) {
                        foreach($_SESSION["cart_item"] as $k => $v) {
                            if($productById[0]["id"] == $k) {
                                if(empty($_SESSION["cart_item"][$k]["quantity"])) {
                                    $_SESSION["cart_item"][$k]["quantity"] = 0;
                                }
                                $_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
                                if($_SESSION["cart_item"][$k]["quantity"] > $productById[0]['amount']){
                                    $_SESSION['amount'] = "จำนวนสินค้าคงเหลือไม่เพียงพอ";
                                    $_SESSION["cart_item"][$k]["quantity"] -= $_POST["quantity"];
                                    break;
                                }
                            }
                        }
                    } else {
                        $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
                    }
                } else {
                    $_SESSION["cart_item"] = $itemArray;
                }
            break;
            case "sale";
                $ctitem = array($_SESSION["cart_item"]);
                $count = 0;
                $empname = $rowuserdata['firstname']." ".$rowuserdata['lastname'];
                $cus_id = $_GET['id'];
                $cusdata = $conn->prepare("SELECT * FROM customers WHERE id = '$cus_id'");
                $cusdata->execute();
                $rowcusdata = $cusdata->fetch(PDO::FETCH_ASSOC);
                $cusname = $rowcusdata['firstname']." ".$rowcusdata['lastname'];
                $cusphone = $rowcusdata['phone'];
                $cusadd = $rowcusdata['address'];
                $cusemail = $rowcusdata['email'];

                $totalPrice = 0;
                $description = "";

                
                foreach ($ctitem as $ct) {
                    $count+= count($ct);
                }
                
                if ($count == 1){

                    $ctitem = array_values($_SESSION["cart_item"]);
                    $item = ($ctitem[0]);
                    $name = $item['name'];
                    $qty = $item['quantity'];
                    $price = $item['price'];
                    $image = $item['image'];
                    $ttprice = $qty * $price;
                    $totalPrice = $totalPrice + $ttprice;
                    $description = $item['name']." (".$item['quantity'].")";

                    $data_product = $conn->prepare("SELECT * FROM products WHERE name = '$name'");
                    $data_product->execute();
                    $datapd = $data_product->fetch(PDO::FETCH_ASSOC);
                    $datapd['amount'] = $datapd['amount'] - $qty;
                    if($datapd['amount'] == 0){
                        $datapd['status'] = "ไม่พร้อมขาย";
                    }

                    $up_stmt = $conn->prepare("UPDATE products SET amount = :amount, status = :status WHERE name = :name");
                    $up_stmt->bindParam(":amount", $datapd['amount']);
                    $up_stmt->bindParam(":status", $datapd['status']);
                    $up_stmt->bindParam(":name", $name);
                    $up_stmt->execute();

                    $stmt = $conn->prepare("INSERT INTO order_detail(name, qty, price, cusname, empname, image) 
                    VALUES(:name, :qty, :price, :cusname, :empname, :image)");
                    $stmt->bindParam(":name", $name);
                    $stmt->bindParam(":qty", $qty);
                    $stmt->bindParam(":price", $ttprice);
                    $stmt->bindParam(":cusname", $cusname);
                    $stmt->bindParam(":empname", $empname);
                    $stmt->bindParam(":image", $image);
                    $stmt->execute();

                    $stmt2 = $conn->prepare("INSERT INTO orders(cusname, description, total, empname,phone , address, email) VALUES(:cusname, :description, :total, :empname,:phone,:address,:email)");
                    $stmt2->bindParam(":cusname", $cusname);
                    $stmt2->bindParam(":total", $totalPrice);
                    $stmt2->bindParam(":description", $description);
                    $stmt2->bindParam(":empname", $empname);
                    $stmt2->bindParam(":phone", $cusphone);
                    $stmt2->bindParam(":address", $cusadd);
                    $stmt2->bindParam(":email", $cusemail);
                    $stmt2->execute();
                    unset($_SESSION["cart_item"]);
                    $_SESSION['success'] = 'ทำรายการเสร็จเรียบร้อย';     

                } elseif ($count > 1 && $count != 0) {
                    for($i = 0; $i < $count; $i++){

                        $item = ($ctitem[0][$i]);
                        $name = $item['name'];
                        $qty = $item['quantity'];
                        $price = $item['price'];
                        $image = $item['image'];
                        $ttprice = $qty * $price;
                        $totalPrice = $totalPrice + $ttprice;
        
                        if($i == 0){
                            $description = $item['name']." (".$item['quantity'].")";
                        } else {
                            $description = $description.", ".$item['name']." (".$item['quantity'].")";
                        }
                        
                        $data_product = $conn->prepare("SELECT * FROM products WHERE name = '$name'");
                        $data_product->execute();
                        $datapd = $data_product->fetch(PDO::FETCH_ASSOC);
                        $datapd['amount'] = $datapd['amount'] - $qty;
                        if($datapd['amount'] == 0){
                            $datapd['status'] = "ไม่พร้อมขาย";
                        }
        
                        $up_stmt = $conn->prepare("UPDATE products SET amount = :amount, status = :status WHERE name = :name");
                        $up_stmt->bindParam(":amount", $datapd['amount']);
                        $up_stmt->bindParam(":status", $datapd['status']);
                        $up_stmt->bindParam(":name", $name);
                        $up_stmt->execute();
        
                        $stmt = $conn->prepare("INSERT INTO order_detail(name, qty, price, cusname, empname, image) 
                        VALUES(:name, :qty, :price, :cusname, :empname, :image)");
                        $stmt->bindParam(":name", $name);
                        $stmt->bindParam(":qty", $qty);
                        $stmt->bindParam(":price", $ttprice);
                        $stmt->bindParam(":cusname", $cusname);
                        $stmt->bindParam(":empname", $empname);
                        $stmt->bindParam(":image", $image);
                        $stmt->execute();
        
                    }    
                        
                    $stmt2 = $conn->prepare("INSERT INTO orders(cusname, description, total, empname,phone , address, email) VALUES(:cusname, :description, :total, :empname,:phone,:address,:email)");
                    $stmt2->bindParam(":cusname", $cusname);
                    $stmt2->bindParam(":total", $totalPrice);
                    $stmt2->bindParam(":description", $description);
                    $stmt2->bindParam(":empname", $empname);
                    $stmt2->bindParam(":phone", $cusphone);
                    $stmt2->bindParam(":address", $cusadd);
                    $stmt2->bindParam(":email", $cusemail);
                    $stmt2->execute();
                    unset($_SESSION["cart_item"]);
                    $_SESSION['success'] = 'ทำรายการเสร็จเรียบร้อย';               
                }
            break;
            case "remove";
                if(!empty($_SESSION["cart_item"])) {
                    $ctitem = array($_SESSION["cart_item"]);
                    $count = 0;
                    foreach ($ctitem as $ct) {
                        $count+= count($ct);
                    }
                    $id = $_GET['id'];
                    if($count == 1){
                        unset($_SESSION["cart_item"]);
                    } else {
                        $productById = $db_handle->runQuery("SELECT * FROM products WHERE id = $id");
                        $pdname = $productById[0]['name'];
                        $pdid = $productById[0]['id'];
                        for($i = 0; $i < $count; $i++) {
                            if ($_SESSION["cart_item"][$i]['id'] == $pdid){
                                unset($_SESSION["cart_item"][$i]);
                                break;
                            }
                        }
                    }     

                    if(empty($_SESSION["cart_item"])) {
                        unset($_SESSION["cart_item"]);
                    }
                    
                }
            break;
            case "empty";
                unset($_SESSION["cart_item"]);
            break;
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

                <div class="container-fluid shadow p-2">
                    <center><h2>รายการสั่งซื้อสินค้า</h2></center>
                </div>
                <?php if (isset($_SESSION['amount'])) { ?>
                                        <div class="alert alert-danger" role="alert">
                                            <?php
                                            echo $_SESSION['amount'];
                                            unset($_SESSION['amount']);
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
                <div class="d-flex">
                    <div style="height:600px;overflow-y: scroll;" class="shadow-sm col-6 p-3">
                        <div><center><h4>รายการสินค้า</h4></center>
                            <form action="" method="POST">
                                <div class="input-group mb-3">
                                    <input type="text" name="srh" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="basic-addon1" autofocus>
                                    <input type="submit" name="search" class="btn btn-info" value="Search">
                                    <a href="" class="btn btn-secondary">View All</a>
                                </div>
                            </form>


                            <div onlick="" class="js-products d-flex" style="flex-wrap: wrap;height:90%;">
                            <?php
                                if (isset($_POST['search'])) {

                                $srh = $_POST['srh'];

                                $product_array = $db_handle->runQuery("SELECT * FROM products WHERE price = '$srh' OR product_num = '$srh' AND status ='พร้อมขาย' ");
                                } else {
                                $product_array = $db_handle->runQuery("SELECT * FROM products WHERE status ='พร้อมขาย' ");

                                                        
                                }                               

                                if (!empty($product_array)) {
                                    foreach ($product_array as $key => $value) { ?>
                                        <form action="employee_create_order.php?action=add&id=<?php echo $product_array[$key]["id"];?>" method="POST">
                                            <div class="card m-2 border-0" style="min-width: 250px;max-width: 250px;">
                                                <div class="product-image">
                                                    <img src="upload/<?php echo $product_array[$key]["image"]; ?>" alt="" class="w-100 rounded border">
                                                </div>                                      
                                                <div class="p-4" >
                                                    <div class="text-muted"><?php echo $product_array[$key]["name"]; ?></div>
                                                    <div class="" style="font-size:20px"><b><?php echo number_format($product_array[$key]["price"],2)." ฿"; ?></b></div>
                                                    <div class="cart-action">
                                                        <input type="text" class="product-quantity" name="quantity" value="1" size="2">
                                                        <input type="submit" value="เพิ่มสินค้า" class="btnAddAction btn-success">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    <?php
                                    }
                                }
                                ?>
                            </div>                                

    

                        </div>
                    </div>
                    <div style="height:600px;overflow-y: scroll;" class="col-6 bg-light p-4 pt-2">
                        <div><center><h4>ตะกร้าสินค้า <a href="employee_create_order.php?action=empty" id="btnEmpty">Empty</a></h4>
                    </center>
                        </div>
                        <div>
                        <form action="" method="GET">
                                <label style="color: black" for="cus_id">ลูกค้า</label>
                                <select name="cus_id">
                                <option value="">--เลือกลูกค้า--</option>
                                <?php 
                                $data_customer = $conn->prepare("SELECT * FROM customers ORDER BY firstname asc");
                                $data_customer->execute();
                                while ($rowcus = $data_customer->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option value="<?php echo $rowcus['id']; ?>"><?php  echo $rowcus['firstname']." ".$rowcus['lastname'];?></option>
                                <?php  } ?>                
                                </select>
                                <button class="btn-xs btn-info" type="submit">+</button>
                                <h99 style="color: red;font-size: 14px;">**กรุณากดปุ่ม + เพื่อเลือกลูกค้า**</h99>
                            </form>   
                        </div>
                        <?php
                            $data_customer = $conn->prepare("SELECT * FROM customers");
                            $data_customer->execute();

                            if(isset($_SESSION["cart_item"])) {
                                $total_quantity = 0;
                                $total_price = 0;
                            
                        ?>
                            <table class="table table-striped">
                                
                                <tr>
                                    <th></th>
                                    <th style="text-align: center">ชื่อสินค้า</th>
                                    <th style="text-align: center">จำนวนสินค้า (ชิ้น)</th>
                                    <th style="text-align: center">ราคาต่อชิ้น (บาท)</th>
                                    <th style="text-align: center">ราคารวม (บาท)</th>
                                    <th style="text-align: center"></th>
                                </tr>
                            
                                <?php

                                   foreach($_SESSION["cart_item"] as $item) {
                                       $item_price = $item["quantity"] * $item["price"];
                                    

                                ?>

                                <tr>
                                    <td style="width:110px"><img src="upload/<?php echo $item["image"]; ?>" alt="" class="w-100 rounded border"></td>
                                    <td style="text-align: center"><?php echo $item["name"]; ?></td>
                                    <td style="text-align: center"><?php echo $item["quantity"]; ?></td>
                                    <td style="text-align: center"><?php echo number_format($item["price"],2); ?></td>
                                    <td style="text-align: center"><?php echo number_format($item_price,2); ?></td>
                                    <td style="text-align: center"><a href="employee_create_order.php?action=remove&id=<?php echo $item["id"];?>" class="btnRemoveAction" alt="Remove Item"><i data-feather="trash"></a></td>
                                </tr>
                                
                                <?php
                                    $total_quantity += $item["quantity"];
                                    $total_price += ($item["price"] * $item["quantity"]);

                                   }
                                ?>

                                <tr>
                                    <td colspan="2" style="text-align: right">รวม :</td>
                                    <td style="text-align: right"><?php echo $total_quantity; ?> ชิ้น</td>
                                    <td colspan="2" style="text-align: right"><?php echo number_format($total_price,2)." ฿"; ?></td>
                                    <td>
                                        <div>
                                        <a class="btn btn-success" href="" data-toggle="modal" data-target="#buyModal">
                                        ยืนยัน
                                        </a>
                                        </div>      
                                    </td>
                                </tr>
                                



                            </table>
                            
                            <?php
                                    
                                   } else {
                            ?>
                                <div class="no-records">ยังไม่มีรายการสินค้า</div>
                            <?php 
                            } 
                            ?>
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
        <div class="modal fade" id="buyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

            <div class="modal-dialog" role="document" style="max-width: 52%;" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark" id="exampleModalLabel">ยืนยันรายการสั่งซิ้อสินค้า</h5>
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
                                วันที่ : <?php
                                date_default_timezone_set("Asia/Bangkok");
                                echo date("Y-m-d");
                                echo "<br>";
                                echo "เวลา : ".date("h:i:s a"); ?>
                                <br>
                                พนักงานขาย : <?php echo $rowuserdata['firstname']." ".$rowuserdata['lastname'];?>
                            </div>

                        
                            <div class="customer-address">
                                ชื่อลูกค้า : <?php 
                                if ($cus_id == 0){
                                    echo ("<h2>กรุณาเลือกลูกค้า</h2>");
                                } else {
                                    $cusid_data = $conn->prepare('SELECT * FROM customers WHERE id = :cusid');
                                    $cusid_data->bindParam(':cusid', $cus_id);
                                    $cusid_data->execute();
                                    $cusdata = $cusid_data->fetch(PDO::FETCH_ASSOC);
                                    echo $cusdata['firstname']." ".$cusdata['lastname'];
                                    echo "<br>";
                                    echo "เบอร์โทรศัพท์ : ".$cusdata['phone'];
                                    echo "<br>";
                                    echo "ที่อยู่ : ".$cusdata['address']; 
                                    echo "<br>";
                                    echo "อีเมล : ".$cusdata['email'];
                                }

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
                                

                            
                                $total_price = 0;                
                                foreach($_SESSION["cart_item"] as $item) {
                                        $item_price = ($item["quantity"] * $item["price"]);
                                        $name = $item['name'];
                                        $amount = $item['quantity'];
                                        $price = $item["price"];
                                        $total_price = $total_price + $item_price;
                                        echo("<tr>");
                                        echo("<td style='font-size: 14px'>$name</td>");
                                        echo("<td class='text-center'>$amount</td>");
                                        echo("<td class='text-right'>".number_format($price,2)."</td>");
                                        echo("<td class='text-right'>".number_format($item_price,2)."</td>");
                                        echo("</tr>");
                                        
                                }


                                echo("<tr>");
                                echo("</tr>");
                                echo("<tr>");
                                echo("<td colspan='3' class='text-right' style='font-size: 14px;'><b>ราคารวม :</b></td>");
                                echo("<td class='text-right'>" .number_format($total_price*100/107,2)."</td>");
                                echo("</tr>");
                                echo("<tr>");
                                echo("<td colspan='3' class='text-right' style='font-size: 14px;'><b>VAT 7% :</b></td>");
                                echo("<td class='text-right'>" .number_format(($total_price - ($total_price*100/107)),2)."</td>");
                                echo("</tr>");
                                echo("<tr>");
                                echo("<td colspan='3' class='text-right' style='font-size: 20px;'><b>ราคารวมสุทธิ :</b></td>");
                                echo("<td class='text-right'><b>" .number_format($total_price,2)."</b></td>");
                                echo("</tr>");
                                echo("<tr>");
                                ?>
                                </table>
                            </div>                       
                        </div>
                        <div class="modal-footer">
                            <a href='employee_create_order.php?action=sale&id=<?php echo $cus_id; ?>'  class='btn btn-success' id='btnSale'>Buy</a>
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        </div>
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
</style>
</html>