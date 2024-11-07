<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'op/UserOP.php';
include_once 'utility.php';

if (empty($userObj->administration) || $userObj->administration!="rw") {
    Util::alert("Access Denied!");
    Util::gogo("home.php");
}

if (isset($_REQUEST['uidtoupd'])) {
    $uidtoupd = $_REQUEST['uidtoupd'];
    $user = Util::getUserWithID($uidtoupd);
} else {
    // header("location:accountmng.php");
    Util::gogo("accountmng.php");
}

include_once 'header.php';

if (isset($_REQUEST['btnUpdate'])) {
    $updateUserObj = new UserOP();

    $active="";
    $administration = "";
    $general = "";
    $person = "";
    $stockin = "";
    $stockout = "";
    $stockbalance = "";


    // Administration
    if (isset($_POST['chkAdministrationR'])) {
        $administration = "r";
    }
    if (isset($_POST['chkAdministrationW'])) {
        $administration = "rw";
    }

    // General
    if (isset($_POST['chkGeneralR'])) {
        $general = "r";
    }
    if (isset($_POST['chkGeneralW'])) {
        $general = "rw";
    }

    // Person
    if (isset($_POST['chkPersonR'])) {
        $person = "r";
    }
    if (isset($_POST['chkPersonW'])) {
        $person = "rw";
    }

    // StockIn
    if (isset($_POST['chkStockInR'])) {
        $stockin = "r";
    }
    if (isset($_POST['chkStockInW'])) {
        $stockin = "rw";
    }

    // StockOut
    if (isset($_POST['chkStockOutR'])) {
        $stockout = "r";
    }
    if (isset($_POST['chkStockOutW'])) {
        $stockout = "rw";
    }

    // StockBalance
    if (isset($_POST['chkStockBalanceR'])) {
        $stockbalance = "r";
    }
    if (isset($_POST['chkStockBalanceW'])) {
        $stockbalance = "rw";
    }

    if ($_POST['tfPass'] == "") {
        $updateUserObj->__construct1(
            $_POST['tfName'],
            $_POST['tfEmail'],
            "",
            $_POST['tfPhone'],
            "",
            $active,
            $administration,
            $general,
            $person,
            $stockin,
            $stockout,
            $stockbalance
        );
    } else {
        $updateUserObj->__construct1(
            $_POST['tfName'],
            $_POST['tfEmail'],
            password_hash($_POST['tfPass'], PASSWORD_DEFAULT),
            $_POST['tfPhone'],
            "",
            $active,
            $administration,
            $general,
            $person,
            $stockin,
            $stockout,
            $stockbalance
        );
    }

    //run the update 
    $updateRst = $updateUserObj->updateUserWithID($uidtoupd);
    if ($updateRst == "success") {
        echo "<script>alert('Updated successfully')</script>";
        echo "<script>location='accountmng.php'</script>";
    } else if ($updateRst == "already") {
        echo "<script>alert('Account already Existed')</script>";
    } else {
        echo "<script>alert('Update failed')</script>";
    }
}

?>

<script>
  $(document).ready(function() {
    $('#chkAdministrationW').on('change', function() {
      if ($(this).is(':checked')) {
        $('#chkAdministrationR').prop('checked', true);
        $('#chkAdministrationR').prop('disabled', true);
      } else {
        $('#chkAdministrationR').prop('checked', false);
        $('#chkAdministrationR').prop('disabled', false);
      }
    });

    $('#chkGeneralW').on('change', function() {
      if ($(this).is(':checked')) {
        $('#chkGeneralR').prop('checked', true);
        $('#chkGeneralR').prop('disabled', true);
      } else {
        $('#chkGeneralR').prop('checked', false);
        $('#chkGeneralR').prop('disabled', false);
      }
    });

    $('#chkPersonW').on('change', function() {
      if ($(this).is(':checked')) {
        $('#chkPersonR').prop('checked', true);
        $('#chkPersonR').prop('disabled', true);
      } else {
        $('#chkPersonR').prop('checked', false);
        $('#chkPersonR').prop('disabled', false);
      }
    });

    $('#chkStockInW').on('change', function() {
      if ($(this).is(':checked')) {
        $('#chkStockInR').prop('checked', true);
        $('#chkStockInR').prop('disabled', true);
      } else {
        $('#chkStockInR').prop('checked', false);
        $('#chkStockInR').prop('disabled', false);
      }
    });

    $('#chkStockOutW').on('change', function() {
      if ($(this).is(':checked')) {
        $('#chkStockOutR').prop('checked', true);
        $('#chkStockOutR').prop('disabled', true);
      } else {
        $('#chkStockOutR').prop('checked', false);
        $('#chkStockOutR').prop('disabled', false);
      }
    });

    $('#chkStockBalanceW').on('change', function() {
      if ($(this).is(':checked')) {
        $('#chkStockBalanceR').prop('checked', true);
        $('#chkStockBalanceR').prop('disabled', true);
      } else {
        $('#chkStockBalanceR').prop('checked', false);
        $('#chkStockBalanceR').prop('disabled', false);
      }
    });
  });
</script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><a href="accountmng.php" class="mr-3"><i class="fas fa-angle-left"></i></a> Update User</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="accountmng.php">Administration</a></li>
                        <li class="breadcrumb-item active">Update User</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- /.row -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Name</label>
                                        <input type="text" class="form-control" name="tfName" id="inputAddress" value="<?php echo $user->userName; ?>" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">Email</label>
                                        <input type="email" class="form-control" name="tfEmail" id="inputPassword4" value="<?php echo $user->userEmail; ?>" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">New Password (Optional / only if you need to change password)</label>
                                        <input type="password" class="form-control" name="tfPass" id="inputAddress" placeholder="Enter a new Password" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Phone</label>
                                        <input type="number" class="form-control" name="tfPhone" id="inputAddress" value="<?php echo $user->userPhone; ?>" autocomplete="off" required>
                                    </div>
                                    <!-- <div class="form-group col-md-6">
                                        <label for="inputPassword4" class="form-text">Profile</label>
                                        <input type="file" id="inputPassword4" name="tfProfile" autocomplete="off">
                                    </div> -->
                                </div>
                                <div class="form-group">
                                    <label for="" class="form-text">Access Control</label>
                                    <div class="form-row mb-3">
                                        <div class="form-check col-md-5 col-5"></div>
                                        <div class="form-check col-md-3 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkActive' type='checkbox' id='activebox' " . ($user->active == 1 ? 'checked' : '') . " disabled>";
                                            ?>
                                            <label class="form-check-label" for="activechk">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="form-text">Permission</label>
                                    <!-- Administration -->
                                    <div class="form-row mb-3">
                                        <div class="form-check col-md-3"></div>
                                        <div class="form-check col-md-3 col-6">
                                            <label class="form-check-label">
                                                Administration
                                            </label>
                                        </div>
                                        <div class="form-check col-md-2 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkAdministrationR' type='checkbox' id='chkAdministrationR' " . ($user->administration == "r" || $user->administration == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkAdministrationR">
                                                Read
                                            </label>
                                        </div>
                                        <div class="form-check col-md-3 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkAdministrationW' type='checkbox' id='chkAdministrationW' " . ($user->administration == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkAdministrationW">
                                                Write
                                            </label>
                                        </div>
                                    </div>
                                    <!-- General -->
                                    <div class="form-row mb-3">
                                        <div class="form-check col-md-3"></div>
                                        <div class="form-check col-md-3 col-6">
                                            <label class="form-check-label">
                                                General
                                            </label>
                                        </div>
                                        <div class="form-check col-md-2 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkGeneralR' type='checkbox' id='chkGeneralR' " . ($user->general == "r" || $user->general == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkGeneralR">
                                                Read
                                            </label>
                                        </div>
                                        <div class="form-check col-md-3 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkGeneralW' type='checkbox' id='chkGeneralW' " . ($user->general == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkGeneralW">
                                                Write
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Person -->
                                    <div class="form-row mb-3">
                                        <div class="form-check col-md-3"></div>
                                        <div class="form-check col-md-3 col-6">
                                            <label class="form-check-label">
                                                Person
                                            </label>
                                        </div>
                                        <div class="form-check col-md-2 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkPersonR' type='checkbox' id='chkPersonR' " . ($user->person == "r" || $user->person == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkPersonR">
                                                Read
                                            </label>
                                        </div>
                                        <div class="form-check col-md-3 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkPersonW' type='checkbox' id='chkPersonW' " . ($user->person == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkPersonW">
                                                Write
                                            </label>
                                        </div>
                                    </div>
                                    <!-- StockIn -->
                                    <div class="form-row mb-3">
                                        <div class="form-check col-md-3"></div>
                                        <div class="form-check col-md-3 col-6">
                                            <label class="form-check-label">
                                                Stock In
                                            </label>
                                        </div>
                                        <div class="form-check col-md-2 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkStockInR' type='checkbox' id='chkStockInR' " . ($user->stockin == "r" || $user->stockin == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkStockInR">
                                                Read
                                            </label>
                                        </div>
                                        <div class="form-check col-md-3 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkStockInW' type='checkbox' id='chkStockInW' " . ($user->stockin == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkStockInW">
                                                Write
                                            </label>
                                        </div>
                                    </div>
                                    <!-- StockOut -->
                                    <div class="form-row mb-3">
                                        <div class="form-check col-md-3"></div>
                                        <div class="form-check col-md-3 col-6">
                                            <label class="form-check-label">
                                                Stock Out
                                            </label>
                                        </div>
                                        <div class="form-check col-md-2 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkStockOutR' type='checkbox' id='chkStockOutR' " . ($user->stockout == "r" || $user->stockout == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkStockOutR">
                                                Read
                                            </label>
                                        </div>
                                        <div class="form-check col-md-3 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkStockOutW' type='checkbox' id='chkStockOutW' " . ($user->stockout == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkStockOutW">
                                                Write
                                            </label>
                                        </div>
                                    </div>
                                    <!-- StockBalance -->
                                    <div class="form-row mb-3">
                                        <div class="form-check col-md-3"></div>
                                        <div class="form-check col-md-3 col-6">
                                            <label class="form-check-label">
                                                Stock Balance
                                            </label>
                                        </div>
                                        <div class="form-check col-md-2 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkStockBalanceR' type='checkbox' id='chkStockBalanceR' " . ($user->stockbalance == "r" || $user->stockbalance == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkStockBalanceR">
                                                Read
                                            </label>
                                        </div>
                                        <div class="form-check col-md-3 col-3">
                                            <?php
                                            echo "<input class='form-check-input' name='chkStockBalanceW' type='checkbox' id='chkStockBalanceW' " . ($user->stockbalance == "rw" ? "checked" : "") . " >";
                                            ?>
                                            <label class="form-check-label" for="chkStockBalanceW">
                                                Write
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-end mx-1">
                                    <button type="submit" class="btn btn-primary" name="btnUpdate">Update</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include_once 'footer.php';
?>