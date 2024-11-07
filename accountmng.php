<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'op/UserOP.php';
include_once 'utility.php';

if (empty($userObj->administration)) {
  Util::alert("Access Denied!");
  Util::gogo("home.php");
}

if (isset($_POST['btnCreate'])) {
  $active = false;
  $administration = "";
  $general = "";
  $person = "";
  $stockin = "";
  $stockout = "";
  $stockbalance = "";

  if (isset($_POST['chkActive'])) {
    $active = true;
  }

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

  if ($_POST['tfPass'] == $_POST['tfCPass']) {

    if ($_FILES['tfProfile']['name'] == "") { //if image was not uploaded
      $profile = "";
      $userEntry = new UserOP();
      $userEntry->__construct1(
        $_POST['tfName'],
        $_POST['tfEmail'],
        password_hash($_POST['tfPass'], PASSWORD_DEFAULT),
        $_POST['tfPhone'],
        $profile,
        $active,
        $administration,
        $general,
        $person,
        $stockin,
        $stockout,
        $stockbalance
      );
      $entryReturn = $userEntry->insertUser();
      if ($entryReturn == "success") {
        echo "<script>alert('Successfully created an account')</script>";
        echo "<script>location='accountmng.php'</script>";
      } else if ($entryReturn == "already") {
        echo "<script>alert('Already Existed!This account had been already created!')</script>";
      } else {
        echo "<script>alert('Insert user query failed!')</script>";
      }
    } else {  // if image was uiploaded

      $target_file = "profileImg/" . basename($_FILES['tfProfile']['name']);
      $extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

      if (file_exists($target_file)) {
        echo "<script>alert('This file already existed so rename your file! You failed to create an account')</script>";
      } else if ($extension != "png" && $extension != "jpg" && $extension != "jpeg" && $extension != "gif") {
        echo "<script>alert('This upload file should be image file type (png/jpg/jpeg/gif)! You failed to create an account')</script>";
      } else {
        $userEntry = new UserOP();
        $userEntry->__construct1(
          $_POST['tfName'],
          $_POST['tfEmail'],
          password_hash($_POST['tfPass'], PASSWORD_DEFAULT),
          $_POST['tfPhone'],
          $target_file,
          $active,
          $administration,
          $general,
          $person,
          $stockin,
          $stockout,
          $stockbalance
        );

        $entryReturn = $userEntry->insertUser();
        if ($entryReturn == "success") {
          if (move_uploaded_file($_FILES['tfProfile']['tmp_name'], $target_file)) {
            echo "<script>alert('Successfully created an account with image')</script>";
            echo "<script>location='accountmng.php'</script>";
          } else {
            echo "<script>alert('Failed to upload file')</script>";
          }
        } else if ($entryReturn == "already") {
          echo "<script>alert('This account had been already created!')</script>";
        } else {
          echo "<script>alert('Insert user query failed!')</script>";
        }
      }
    }
  } else {
    echo "<script>alert('Password and Confirm Password are not same! Account creation failed!')</script>";
  }
}

//make confirm to delete
if (isset($_REQUEST['uidtodel'])) {
  $uidtodel = $_REQUEST['uidtodel'];

  echo "<script>
    if(confirm('Are you sure to delete this account?')){
      location='accountmng.php?uidtodelConfirm=$uidtodel';
    }else{
      location='accountmng.php';
    }
    </script>";

  // echo "<script>alert('click')</script>";
}
//do action to delete
if (isset($_REQUEST['uidtodelConfirm'])) {
  $uidtodelConfirm = $_REQUEST['uidtodelConfirm'];

  $userObj2 = new UserOP();
  if ($userObj2->deleteUserWithID($uidtodelConfirm)) {
    Util::alert("Successfully deleted...");
  } else {
    Util::alert("Failed to delete");
  }
}

if (isset($_REQUEST['disableId'])) {
  $userId = $_REQUEST['disableId'];
  $userAble = new UserOP();
  $userAble->makeDisable($userId);
  echo "<script>location='accountmng.php'</script>";
}

if (isset($_REQUEST['ableId'])) {
  $userId = $_REQUEST['ableId'];
  $userDisable = new UserOP();
  $userDisable->makeActive($userId);
  echo "<script>location='accountmng.php'</script>";
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
          <h1>User Account Management</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="">Administration</a></li>
            <li class="breadcrumb-item active">Account Management</li>
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
            <div class="card-header">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">User List</h3>
                <!-- <p>jskfldsjfls</p> -->
                <div class="card-tools">
                  <?php if ($userObj->administration=="rw") {  ?>
                  <button class="btn btn-primary" style="font-weight: bold;" data-toggle="modal" data-target="#newUserModal">
                    New Account
                  </button>
                  <?php } ?>
                </div>
              </div>
              <!-- <div class="card-tools">
                  <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                    <div class="input-group-append">
                      <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                      </button>
                      
                    </div>
                  </div>
                </div> -->
            </div>
            <!-- /.card-header -->
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Active</th>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Administration</th>
                    <th>General</th>
                    <th>Person</th>
                    <th>Stock In</th>
                    <th>Stock Out</th>
                    <th>Stock Balance</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $userOp = new UserOP();
                  $dataRst = $userOp->getAllUser();
                  if ($dataRst == false) {
                    echo "<tr><td colspan='13' style='text-align:center;'>Query Error</td></tr>";
                  } else if ($dataRst == "noresult") {
                    echo "<tr><td colspan='13' style='text-align:center;'>No Result Found</td></tr>";
                  } else {
                    $no = 0;
                    foreach ($dataRst as $user) {
                      $currentUserId = $user->userId;
                      $no++;
                      // check active status
                      if ($user->active) {
                        $active = "<i class='fas fa-circle' style='color:green;'></i>";
                      } else {
                        $active = "<i class='fas fa-circle' style='color:red;'></i>";
                      }
                      //check profile or not
                      if ($user->userProfile == "") {
                        $profileImg = "profileImg/img_avatar.png";
                      } else {
                        $profileImg = $user->userProfile;
                      }
                      // check administration
                      if ($user->administration=="rw") {
                        $administration = "<i class='fas fa-circle' style='color:green;'></i>";
                      }else if($user->administration=="r"){
                        $administration = "<i class='fas fa-adjust' style='color:green;'></i>";
                      }else {
                        $administration = "<i class='fas fa-circle' style='color:red;'></i>";
                      }
                      // check general
                      if ($user->general=="rw") {
                        $general = "<i class='fas fa-circle' style='color:green;'></i>";
                      }else if($user->general=="r"){
                        $general = "<i class='fas fa-adjust' style='color:green;'></i>";
                      }else {
                        $general = "<i class='fas fa-circle' style='color:red;'></i>";
                      }
                      //check person
                      if ($user->person=="rw") {
                        $person = "<i class='fas fa-circle' style='color:green;'></i>";
                      }else if($user->person=="r"){
                        $person = "<i class='fas fa-adjust' style='color:green;'></i>";
                      }else {
                        $person = "<i class='fas fa-circle' style='color:red;'></i>";
                      }
                      //check stockin
                      if ($user->stockin=="rw") {
                        $stockin = "<i class='fas fa-circle' style='color:green;'></i>";
                      }else if($user->stockin=="r"){
                        $stockin = "<i class='fas fa-adjust' style='color:green;'></i>";
                      }else {
                        $stockin = "<i class='fas fa-circle' style='color:red;'></i>";
                      }
                      //check stockout
                      if ($user->stockout=="rw") {
                        $stockout = "<i class='fas fa-circle' style='color:green;'></i>";
                      }else if($user->stockout=="r"){
                        $stockout = "<i class='fas fa-adjust' style='color:green;'></i>";
                      }else {
                        $stockout = "<i class='fas fa-circle' style='color:red;'></i>";
                      }
                      //check stock
                      if ($user->stockbalance=="rw") {
                        $stockbalance = "<i class='fas fa-circle' style='color:green;'></i>";
                      }else if($user->stockbalance=="r"){
                        $stockbalance = "<i class='fas fa-adjust' style='color:green;'></i>";
                      }else {
                        $stockbalance = "<i class='fas fa-circle' style='color:red;'></i>";
                      }
                      echo "
                        <tr>
                          <td>$no</td>
                          <td>$active</td>
                          <td><img src='$profileImg' class='img-circle elevation-1' width='30px'></td>
                          <td>" . $user->userName . "</td>
                          <td>" . $user->userEmail . "</td>
                          <td>" . $user->userPhone . "</td>
                          <td>$administration</td>
                          <td>$general</td>
                          <td>$person</td>
                          <td>$stockin</td>
                          <td>$stockout</td>
                          <td>$stockbalance</td>
                          <td>";
                    if ($userObj->administration=="rw") { 
                      if ($currentUserId != 1) {
                        echo "
                        <a href='accountmng.php?uidtodel=$currentUserId' class='btn btn-danger' title='Delete'><i class='fas fa-trash'></i></a>
                            <a href='updateuser.php?uidtoupd=$currentUserId' class='btn btn-secondary' title='Edit'><i class='fas fa-pen'></i></a>
                        ";
                        if ($user->active) {
                          echo "<a href='accountmng.php?disableId=$currentUserId' class='btn btn-warning' title='Disable Account' disable><i class='fas fa-stop'></i></a>";
                        } else {
                          echo "<a href='accountmng.php?ableId=$currentUserId' class='btn btn-success' title='Enable Account'><i class='fas fa-play'></i></a>";
                        }
                      }
                    }
                      echo "
                          </td>
                        </tr>
                        ";
                    }
                  }
                  ?>
                  <tr>
                    <td></td>
                  </tr>
                </tbody>
              </table>
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


<!-- Modal -->
<div class="modal fade" id="newUserModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Create new account</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" enctype="multipart/form-data">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputAddress">Name</label>
              <input type="text" class="form-control" name="tfName" id="inputAddress" placeholder="Admin" autocomplete="off" required>
            </div>
            <div class="form-group col-md-6">
              <label for="inputPassword4">Email</label>
              <input type="email" class="form-control" name="tfEmail" id="inputPassword4" placeholder="sample@gmail.com" autocomplete="off" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputAddress">Password</label>
              <input type="password" class="form-control" name="tfPass" id="inputAddress" placeholder="....." autocomplete="new-password" required>
            </div>
            <div class="form-group col-md-6">
              <label for="inputPassword4">Confirm Password</label>
              <input type="password" class="form-control" name="tfCPass" id="inputPassword4" placeholder="....." autocomplete="off" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputAddress">Phone</label>
              <input type="number" class="form-control" name="tfPhone" id="inputAddress" placeholder="09..........." autocomplete="off" required>
            </div>
            <div class="form-group col-md-6">
              <label for="inputPassword4" class="form-text">Profile</label>
              <input type="file" id="inputPassword4" name="tfProfile" autocomplete="off">
            </div>
          </div>
          <div class="form-group">
            <label for="" class="form-text">Access Control</label>
            <div class="form-row mb-3">
              <div class="form-check col-md-5 col-5"></div>
              <div class="form-check col-md-3 col-3">
                <input class="form-check-input" name="chkActive" type="checkbox" id="activechk" checked>
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
                <input class="form-check-input" name="chkAdministrationR" type="checkbox" id="chkAdministrationR">
                <label class="form-check-label" for="chkAdministrationR">
                  Read
                </label>
              </div>
              <div class="form-check col-md-3 col-3">
                <input class="form-check-input" name="chkAdministrationW" type="checkbox" id="chkAdministrationW">
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
                <input class="form-check-input" name="chkGeneralR" type="checkbox" id="chkGeneralR">
                <label class="form-check-label" for="chkGeneralR">
                  Read
                </label>
              </div>
              <div class="form-check col-md-3 col-3">
                <input class="form-check-input" name="chkGeneralW" type="checkbox" id="chkGeneralW">
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
                <input class="form-check-input" name="chkPersonR" type="checkbox" id="chkPersonR">
                <label class="form-check-label" for="chkPersonR">
                  Read
                </label>
              </div>
              <div class="form-check col-md-3 col-3">
                <input class="form-check-input" name="chkPersonW" type="checkbox" id="chkPersonW">
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
                <input class="form-check-input" name="chkStockInR" type="checkbox" id="chkStockInR">
                <label class="form-check-label" for="chkStockInR">
                  Read
                </label>
              </div>
              <div class="form-check col-md-3 col-3">
                <input class="form-check-input" name="chkStockInW" type="checkbox" id="chkStockInW">
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
                <input class="form-check-input" name="chkStockOutR" type="checkbox" id="chkStockOutR">
                <label class="form-check-label" for="chkStockOutR">
                  Read
                </label>
              </div>
              <div class="form-check col-md-3 col-3">
                <input class="form-check-input" name="chkStockOutW" type="checkbox" id="chkStockOutW">
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
                <input class="form-check-input" name="chkStockBalanceR" type="checkbox" id="chkStockBalanceR">
                <label class="form-check-label" for="chkStockBalanceR">
                  Read
                </label>
              </div>
              <div class="form-check col-md-3 col-3">
                <input class="form-check-input" name="chkStockBalanceW" type="checkbox" id="chkStockBalanceW">
                <label class="form-check-label" for="chkStockBalanceW">
                  Write
                </label>
              </div>
            </div>
          </div>
          <div class="row justify-content-end mx-1">
            <button type="reset" class="btn btn-secondary" style="margin-right: 10px;">Clear</button>
            <button type="submit" class="btn btn-primary" name="btnCreate">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php
include_once 'footer.php';
?>