<?php
    include_once "dbConfig/connect.php";
    include_once "op/UserOP.php";
    include_once "utility.php";

    if (Util::checkAuth()) {
        header("location:home.php");
    }


    $user=new UserOP();

    if (isset($_POST['btnLogin'])) {
        if($user->authenticate($_POST['tfEmail'],$_POST['tfPassword'])){
            echo "<script>location='home.php'</script>";
        };
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SCS Inventory Login</title>
    <link rel="icon" type="image/png" href="images/SCS_logo.png">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Akshar:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <link rel="stylesheet" href="css/index_css.css">
</head>
<script>
    function changeType(e) {
        const passwordField = document.querySelector("#password");
        console.log(passwordField.type);
        if (passwordField.type == "password") {
            passwordField.type = "text";
            e.target.className = "fas fa-eye-slash";
        } else {
            passwordField.type = "password";
            e.target.className = "fas fa-eye";
        }
    }
</script>

<body>
    <div id="main_content" class="container-fluid">
        <main class="row" id="full_height">
            <aside class="col-12 col-md-5 col-lg-4 text-center bg-primary" style="padding-top: 10%;">
                <img src="images/SCS_logo.png" alt="" class="w-50 mb-4">
                <h3 class="text-center" style="font-family: 'Akshar', sans-serif; font-size:25px; font-weight: normal;">Inventory Management System</h3>
            </aside>
            <section class="col-12 col-md-7 col-lg-8 px-5" style="padding-top: 6%;">
                <div>
                    <section class="content-header">
                        <div class="container-fluid">
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <h1 style="font-family: 'Akshar', sans-serif;">User Login Form</h1>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="content">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12 col-lg-9">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Fill in all required fields</small></h3>
                                        </div>
                                        <form id="quickForm" method="post">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Username</label>
                                                    <input type="email" name="tfEmail" class="form-control" id="exampleInputEmail1" placeholder="Enter Username" autocomplete="off" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Password</label>
                                                    <div class="form-inline">
                                                        <input type="password" name="tfPassword" class="form-control col-11 mr-1" id="password" placeholder="Enter Password" autocomplete="off" required>
                                                        <i class="fas fa-eye" id="togglePassword" onclick="changeType(event)"></i>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-0">
                                                    <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="terms" class="custom-control-input" id="exampleCheck1" checked>
                                                        <label class="custom-control-label" for="exampleCheck1">I agree to the <a href="#" target="_blank">terms of service</a>.</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer d-flex justify-content-end">
                                                <button type="reset" class="btn btn-secondary mr-3">Reset</button>
                                                <button type="submit" class="btn btn-primary" name="btnLogin">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </main>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
</body>

</html>