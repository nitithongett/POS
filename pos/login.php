<?php

    session_start();
    require_once 'config/db.php';
    
    if  (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);

        if (empty($username)) {
            $_SESSION['error'] = 'กรุณากรอก username';
            header("location: index.php");
        } else if (empty($password)) {
            $_SESSION['error'] = 'กรุณากรอก password';
            header("location: index.php");
        } else {
            try {
                $check_data = $conn->prepare("SELECT * FROM users WHERE username = :username");
                $check_data->bindParam(":username", $username);
                $check_data->execute();
                $row = $check_data->fetch(PDO::FETCH_ASSOC);

                if ($check_data->rowCount() > 0) {

                    if ($username == $row['username']) {
                        if ($password == $row['password']) {
                            if ($row['urole'] == 'Admin') {
                                $_SESSION['admin_login'] = $row['id'];
                                $_SESSION["firstname"] = $row["firstname"];
                                $_SESSION["lastname"] = $row["lastname"];
                                header("location: admin.php");
                            } else {
                                $_SESSION['employee_login'] = $row['id'];
                                $_SESSION["firstname"] = $row["firstname"];
                                $_SESSION["lastname"] = $row["lastname"];
                                header("location: employee_create_order.php");
                            }
                        } else {
                            $_SESSION['error'] = 'password ไม่ถูกต้อง';
                            header("location: index.php");
                        }
                    } else {
                        $_SESSION['error'] = 'username ไม่ถูกต้อง ';
                        header("location: index.php");
                    }
                } else {
                    $_SESSION['error'] = 'ไม่มีข้อมูลในระบบ';
                    header("location: index.php");
                }

            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    } else {

    }
?>