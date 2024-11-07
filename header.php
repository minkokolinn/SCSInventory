<?php
error_reporting(0);
include_once 'utility.php';
include_once 'dbConfig/connect.php';
include_once 'op/UserOP.php';
if (Util::checkAuth()) {
  $userid = $_COOKIE['auth'];
  $userObj = Util::getUserWithID($userid);
  if ($userObj->active == TRUE) {
  } else {
    echo "<script>alert('Your account had been banned! Contact admin to gain account permission!')</script>";
    echo "<script>location='logout.php'</script>";
  }
} else {
  echo "<script>alert('Invalid action! Please Login first')</script>";
  echo "<script>location='index.php'</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($_POST['btnSearchReport']) ? "Stock Balance Report From " . $_POST['tfFromDate'] . " To " . $_POST['tfToDate'] : "SCS Inventory System"; ?></title>

  <link rel="icon" type="image/png" href="images/SCS_logo.png">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <!-- <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
  <!-- JQuery -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

  <!-- For Date Picker -->
  <link
      href=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css"
      rel="stylesheet"
    />
    <script src=
"https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js">
    </script>
    <script src=
"https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js">
    </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Preloader -->
    <!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="images/SCS_logo.png" alt="AdminLTELogo" height="auto" width="200">
  </div> -->

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="#" class="nav-link">Inventory Management</a>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">

        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="logout.php" role="button">
            <i class="fas fa-sign-out-alt"></i>
            &nbsp;&nbsp;&nbsp;Logout
          </a>
        </li>

      </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="" class="brand-link d-flex justify-content-center">
        <img src="images/SCS_logo.png" alt="Secure Channel Login" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="font-weight-bold">&nbsp;</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <?php
            $userProfile = "";
            if ($userObj->userProfile == "") {
              $userProfile = "profileImg/img_avatar.png";
            } else {
              $userProfile = $userObj->userProfile;
            }
            ?>
            <img src="<?php echo $userProfile; ?>" class="img-circle elevation-2" alt="User Image">
          </div>
          <div class="info">
            <a href="#" class="d-block"><?php echo $userObj->userName; ?></a>
          </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
          <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-sidebar">
                <i class="fas fa-search fa-fw"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
            <li class="nav-item menu-open">
              <a href="home.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>
                  Dashboard
                </p>
              </a>
            </li>
            <?php if (!empty($userObj->administration)) { ?>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-tools"></i>
                  <p>
                    Administration
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="accountmng.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Account Management</p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php } ?>

            <?php if (!empty($userObj->general)) { ?>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-bars"></i>
                  <p>
                    General
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="brandmng.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Brand (Hardware)</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="hardwarelist.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Hardware</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="softwarelist.php" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Software</p>
                    </a>
                  </li>
                  <!-- <li class="nav-item">
                  <a href="" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>License</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Service</p>
                  </a>  
                </li> -->
                </ul>
              </li>
            <?php } ?>
            <?php if (!empty($userObj->person)) { ?>
              <li class="nav-item">
                <a href="suppliermng.php" class="nav-link">
                  <i class="nav-icon fas fa-chalkboard-teacher"></i>
                  <p>
                    Supplier
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="customermng.php" class="nav-link">
                  <i class="nav-icon fas fa-person-booth"></i>
                  <p>
                    Customer
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="personadd.php" class="nav-link">
                  <i class="nav-icon fas fa-user-plus"></i>
                  <p>
                    Add Person
                  </p>
                </a>
              </li>
            <?php } ?>
            <?php if (!empty($userObj->stockin)) { ?>
              <li class="nav-item">
                <a href="silist.php" class="nav-link">
                  <i class="nav-icon fa fa-shopping-cart"></i>
                  <p>
                    Stock In
                  </p>
                </a>
              </li>
            <?php } ?>
            <?php if (!empty($userObj->stockout)) { ?>
              <li class="nav-item">
                <a href="solist.php" class="nav-link">
                  <i class="nav-icon fas fa-clipboard-check"></i>
                  <p>
                    Stock Out
                  </p>
                </a>
              </li>
            <?php } ?>
            <?php if (!empty($userObj->stockbalance)) { ?>
              <li class="nav-item">
                <a href="stockbalance.php" class="nav-link">
                  <i class="nav-icon fas fa-signal"></i>
                  <p>
                    Stock Balance
                  </p>
                </a>
              </li>
            <?php } ?>
            <li class="nav-item">
              <a href="stockreport.php" class="nav-link">
                <i class="nav-icon fas fa-file-powerpoint"></i>
                <p>
                  Stock Report
                </p>
              </a>
            </li>
            <!-- <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fab fa-sellcast"></i>
                <p>
                  Sale
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="saleinvoice.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Sale Invoice</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="salelist.php" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Sale List</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fab fa-sellsy"></i>
                <p>
                  Stock (Hardware)
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Stock In</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Stock Out</p>
                  </a>
                </li>
              </ul>
            </li> -->
          </ul>
        </nav>
        <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
    </aside>