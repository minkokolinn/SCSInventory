<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/HardwareOP.php';

if (empty($userObj->general)) {
  Util::alert("Access Denied!");
  Util::gogo("home.php");
}

//do confirm to delete
if (isset($_REQUEST['hidtodel'])) {
  $hidtodel = $_REQUEST['hidtodel'];
  echo "<script>
    if(confirm('Are you sure to delete this item?')){
      location='hardwarelist.php?hidtodelConfirm=$hidtodel';
    }else{
      location='hardwarelist.php';
    }
    </script>";
}
//do action to delete
if (isset($_REQUEST['hidtodelConfirm'])) {
  $hidtodelConfirm = $_REQUEST['hidtodelConfirm'];

  $hardwareObj1 = new Hardware();
  if ($hardwareObj1->deleteHardware($hidtodelConfirm)) {
    Util::alert("Successfully deleted");
  } else {
    Util::alert("Delete Failed!");
  }
}

?>
<style>
  .makeline {
    display: -webkit-box;
    width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
  }
</style>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Hardware</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">General</a></li>
            <li class="breadcrumb-item active">Hardware List</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Warehouse's Hardware List</h3>
                <!-- <p>jskfldsjfls</p> -->
                <div class="card-tools">
                  <?php if ($userObj->general == "rw") {  ?>
                    <a href="hardwareadd.php" class="btn btn-primary" style="font-weight: bold;">
                      New Item
                    </a>
                  <?php } ?>
                </div>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped" data-page-length='10'>
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Item Code</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Brand</th>
                    <th>Description</th>
                    <?php if ($userObj->general=="rw") {  ?>
                    <th>Action</th>
                    <?php } ?>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $hardwareObj = new Hardware();
                  $resultHardware = $hardwareObj->getAllHardware();
                  if ($resultHardware == 0) {
                    echo "<td colspan='8' style='text-align:center;'>Select Query Failed</td>";
                  } else if ($resultHardware == "noresult") {
                    echo "<td colspan='8' style='text-align:center;'>No Result Found</td>";
                  } else {
                    $count = 0;
                    foreach ($resultHardware as $hardware) {
                      $count++;
                      $hardwareId = $hardware->hardwareId;
                      echo "<tr>";
                      echo "<td>" . $count . "</td>";
                      echo "<td>" . $hardware->itemCode . "</td>";
                      echo "<td>" . $hardware->itemName . "</td>";
                      echo "<td>" . $hardware->quantity . "</td>";
                      echo "<td>" . $hardware->unitPrice . " - " . $hardware->currency . "</td>";
                      echo "<td>" . $hardware->brandName . "</td>";
                      echo "<td><p class='makeline'>" . $hardware->description . "</p></td>";
                      if ($userObj->general=="rw") {
                      echo "<td><a href='hardwarelist.php?hidtodel=$hardwareId' class='btn btn-danger' title='Delete'><i class='fas fa-trash'></i></a>
                          <a href='hardwareupdate.php?hidtoupd=$hardwareId' class='btn btn-secondary' title='Edit'><i class='fas fa-pen'></i></a>
                        </td>";
                      }
                      echo "</tr>";
                    }
                  }
                  ?>
                </tbody>
                <tfoot>
                  <tr>
                    <th>No</th>
                    <th>Item Code</th>
                    <th>Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Brand</th>
                    <th>Description</th>
                    <?php if ($userObj->general=="rw") {  ?>
                    <th>Action</th>
                    <?php } ?>
                  </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
  $(function() {
    $("#example1").DataTable({
      "responsive": true,
      "lengthChange": true,
      "autoWidth": false,
      // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      // "buttons": ["print","colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>


<?php
include_once 'footer.php';
?>