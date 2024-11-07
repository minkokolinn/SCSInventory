<?php
session_start();
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/StockInOP.php';
include_once 'op/HardwareOP.php';
include_once 'op/HardwareDetailOP.php';

if (empty($userObj->stockin)) {
  Util::alert("Access Denied!");
  Util::gogo("home.php");
}

if (isset($_REQUEST['pidtodel'])) {
  $pidtodel = $_REQUEST['pidtodel'];
  echo "<script>
    if(confirm('Warning : If you delete this purchase order, all related serial key will be deleted. Are you sure to delete?')){
      location='silist.php?pidtodelConfirm=$pidtodel';
    }else{
      location='silist.php';
    }
    </script>";
}
//do action to delete
if (isset($_REQUEST['pidtodelConfirm'])) {
  $pidtodelConfirm = $_REQUEST['pidtodelConfirm'];

  $purObj2 = new StockIn();
  if ($purObj2->deletePurchaseAndRelated($pidtodelConfirm)) {
    Util::alert("Deleted Successfully");
    Util::gogo("silist.php");
  } else {
    Util::alert("Delete Query Error");
  }
}

if (isset($_POST['btnChange'])) {
  if ($_COOKIE['showSerialPO'] == "on") {
    echo "<script>
    document.cookie='showSerialPO=off'
    </script>";
    Util::gogo("silist.php");
  } else {
    echo "<script>
    document.cookie='showSerialPO=on'
    </script>";
    Util::gogo("silist.php");
  }
}

if (isset($_REQUEST['hdidtoed'])) {
  $hdid = $_REQUEST['hdidtoed'];
  $hdObj1=new HardwareDetail();
  $eachHd = $hdObj1->getHDWithHDID($hdid);

  echo "<script>$(document).ready(function() { document.querySelector('#showEditRemark').click(); });</script>";
}
if (isset($_POST['btnAddRemark'])) {
  $hdObj = new HardwareDetail();
  if ($hdObj->updateRemark($hdid, $_POST['tfRemark'])) {
    Util::alert("Suucessfully Edited!");
    // Util::goback();
    Util::gogo("silist.php");
    // Util::goback();
  }
}

?>
<script>
  $(document).ready(function() {
    let checkboxShowHide = document.getElementById('vhserial');
    checkboxShowHide.addEventListener('change', (event) => {
      document.getElementById('btnChange').click();
    });

    document.querySelector('#btnCloseDialog').addEventListener('click',function(){
      location='silist.php';
    })
  });
</script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Stock In</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Stock In</a></li>
            <li class="breadcrumb-item active">Stock In List</li>
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
                <h3 class="card-title">Stock In List</h3>
                <!-- <p>jskfldsjfls</p> -->
                <div class="card-tools">
                  <form class="form-inline" method="post">
                    <div class="form-check mx-5">
                      <?php
                      if ($_COOKIE['showSerialPO'] == "on") {
                        echo "<input type='checkbox' class='form-check-input' id='vhserial' name='vhserial' checked>";
                      } else {
                        echo "<input type='checkbox' class='form-check-input' id='vhserial' name='vhserial'>";
                      }
                      ?>

                      <label class="form-check-label" for="vhserial">View / Hide Serial Key</label>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2" name="btnChange" id="btnChange" style="display: none;">Change</button>
                    <?php if ($userObj->stockin=="rw") {  ?>
                    <a href="stockinadd.php" class="btn btn-primary" style="font-weight: bold;">
                      New Stock In
                    </a>
                    <?php } ?>
                  </form>
                  <button class="btn btn-primary" style="display:none;" data-toggle="modal" data-target="#editRemark" id="showEditRemark">
                    Edit Remark
                  </button>
                </div>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <?php
              $purchaseObj = new StockIn();
              // With Serial Key
              if ($_COOKIE['showSerialPO'] == "on") {
                echo "
                <table id='example1' class='table table-bordered table-striped' data-page-length='10'>
                <thead>
                  <tr>
                    <th>Stock-in Date</th>
                    <th>PO Number</th>
                    <th>Item Code</th>
                    <th>Hardware</th>
                    <th>Serial Key</th>
                    <th>Status</th>
                    <th>Serial Key's Remark</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                ";
                $result = $purchaseObj->getAllPurchaseListSerial();
                if ($result == "noresult") {
                } else {
                  foreach ($result as $purchase) {
                    $hardwareDetailId = $purchase->hardwareDetailId;
                    $serialKey = $purchase->serialKey;
                    echo "<tr>";
                    echo "<td>" . $purchase->stockInDate . "</td>";
                    echo "<td>" . $purchase->poNumber . "</td>";
                    echo "<td>" . $purchase->itemCode . "</td>";
                    echo "<td>" . $purchase->itemName . "</td>";
                    echo "<td style='text-decoration:underline;'>" . $purchase->serialKey . "</td>";
                    if ($purchase->status == "sold out") {
                      echo "<td style='color:red;'>" . $purchase->status . "</td>";
                    } else {
                      echo "<td style='color:green;'>" . $purchase->status . "</td>";
                    }

                    echo "<td>" . $purchase->myrm . "</td><td>";
                    if ($userObj->stockin=="rw") {
                    echo "
                    <a href='silist.php?hdidtoed=$hardwareDetailId' class='btn btn-secondary' title='Edit'><i class='fas fa-pen'></i></a>
                    ";
                    }
                    echo "</td></tr>";
                  }
                }
                echo "
                </tbody>
                <tfoot>
                  <tr>
                    <th>Stock-in Date</th>
                    <th>PO Number</th>
                    <th>Item Code</th>
                    <th>Hardware</th>
                    <th>Serial Key</th>
                    <th>Status</th>
                    <th>Serial Key's Remark</th>
                    <th>Action</th>
                  </tr>
                </tfoot>
                </table>";
              } else {  // Without Serial Key
                echo "
                <table id='example1' class='table table-bordered table-striped' data-page-length='10'>
                <thead>
                  <tr>
                    <th>Stock-in Date</th>
                    <th>Supplier</th>
                    <th>Item Code</th>
                    <th>Hardware</th>
                    <th>Quantity</th>
                    <th>PO Number</th>
                    <th>PO File</th>
                    <th>Remark</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                ";
                $result = $purchaseObj->getAllPurchaseList();
                if ($result == "noresult") {
                } else {
                  foreach ($result as $purchase) {
                    $stockInId = $purchase->stockInId;
                    echo "<tr>";
                    echo "<td>" . $purchase->stockInDate . "</td>";
                    echo "<td>" . $purchase->companyName . "</td>";
                    echo "<td>" . $purchase->itemCode . "</td>";
                    echo "<td>" . $purchase->itemName . "</td>";
                    echo "<td>" . $purchase->quantity . "</td>";
                    echo "<td>" . $purchase->poNumber . "</td>";
                    $file = $purchase->poFile;
                    echo "<td><a href='$file' target='_thapa'>" . $purchase->poFile . "</a></td>";
                    echo "<td>" . $purchase->remark . "</td><td>";
                    if ($userObj->stockin=="rw") {
                    echo "
                    <a href='silist.php?pidtodel=$stockInId' class='btn btn-danger' title='Delete'><i class='fas fa-trash'></i></a>
                    ";
                    }
                    echo "</td></tr>";
                  }
                }
                echo "
                </tbody>
                <tfoot>
                  <tr>
                    <th>Stock-in Date</th>
                    <th>Supplier</th>
                    <th>Item Code</th>
                    <th>Hardware</th>
                    <th>Quantity</th>
                    <th>PO Number</th>
                    <th>PO File</th>
                    <th>Remark</th>
                    <th>Action</th>
                  </tr>
                </tfoot>
                </table>";
              }
              ?>
              <!-- With Serial Key -->

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

<!-- Modal -->
<div class="modal fade" id="editRemark" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Edit Remark to <?php echo $eachHd->serialKey; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btnCloseDialog">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post">
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputAddress">Remark</label>
              <textarea name="tfRemark" id="" rows="5" class="form-control"><?php echo $eachHd->remark; ?></textarea>
            </div>
          </div>
          <div class="row justify-content-end mx-1">
            <button type="reset" class="btn btn-secondary" style="margin-right: 10px;">Clear</button>
            <button type="submit" class="btn btn-primary" name="btnAddRemark">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


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
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
</script>

<?php
include_once 'footer.php';
?>