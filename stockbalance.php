<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/HardwareOP.php';

if (empty($userObj->stockbalance)) {
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

if (isset($_POST['btnChange'])) {
    if ($_COOKIE['showSerialBalance'] == "on") {
        echo "<script>
      document.cookie='showSerialBalance=off'
      </script>";
        Util::gogo("stockbalance.php");
    } else {
        echo "<script>
      document.cookie='showSerialBalance=on'
      </script>";
        Util::gogo("stockbalance.php");
    }
}

?>
<script>
    $(document).ready(function() {
        let checkboxShowHide = document.getElementById('vhserial');
        checkboxShowHide.addEventListener('change', (event) => {
            document.getElementById('btnChange').click();
        });
    });
</script>

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
                    <h1>Stock Balance</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Stock Balance</a></li>
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
                                <h3 class="card-title">Stock Balance</h3>
                                <!-- <p>jskfldsjfls</p> -->
                                <div class="card-tools">
                                    <form class="form-inline" method="post">
                                        <div class="form-check mx-5">
                                            <?php
                                            if ($_COOKIE['showSerialBalance'] == "on") {
                                                echo "<input type='checkbox' class='form-check-input' id='vhserial' name='vhserial' checked>";
                                            } else {
                                                echo "<input type='checkbox' class='form-check-input' id='vhserial' name='vhserial'>";
                                            }
                                            ?>

                                            <label class="form-check-label" for="vhserial">View / Hide Serial Key</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary mb-2" name="btnChange" id="btnChange" style="display: none;">Change</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <?php
                            if ($_COOKIE['showSerialBalance'] == "on") {
                            ?>
                                <table id="example1" class="table table-bordered table-striped table-sm" data-page-length='10'>
                                    <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Name</th>
                                            <th>Brand</th>
                                            <th>Serial Key</th>
                                            <th>Status</th>
                                            <th>Customer</th>
                                            <th>Sales Date</th>
                                            <th>Warranty End Date</th>
                                            <th>Remaining</th>
                                            <!-- <th>Action</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $resultArr=Util::stockBalanceQuery();
                                        if ($resultArr == "fail") {
                                            echo "<td colspan='8' style='text-align:center;'>Select Query Failed</td>";
                                        } else if ($resultArr == "noresult") {
                                            echo "<td colspan='8' style='text-align:center;'>No Result Found</td>";
                                        } else {
                                            $count = 0;
                                            foreach ($resultArr as $hardware) {
                                                $count++;
                                                $hardwareId = $hardware->hardwareId;
                                                
                                                //Get Customer according to sold out serial
                                                $resultStockOut=Util::getStockOutInfoByHID($hardware->hardwareDetailId);
                                                //

                                                echo "<tr>";
                                                echo "<td>" . $hardware->itemCode . "</td>";
                                                echo "<td>" . $hardware->itemName . "</td>";
                                                echo "<td>" . $hardware->brandName . "</td>";
                                                echo "<td style='text-decoration:underline;'>" . $hardware->serialKey . "</td>";
                                                if ($hardware->status=="in stock") {
                                                    echo "<td style='color:green;'>" . $hardware->status . "</td>";
                                                }else{
                                                    echo "<td style='color:red;'>" . $hardware->status . "</td>";
                                                }
                                                if ($resultStockOut=="no result") {
                                                    echo "<td></td>";
                                                }else if($resultStockOut=="fail"){
                                                    echo "<td></td>";
                                                }else{
                                                    echo "<td>".$resultStockOut->companyName."</td>";
                                                }
                                                
                                                echo "<td>" . $hardware->serviceStart . "</td>";
                                                echo "<td>" . $hardware->serviceEnd . "</td>";
                                                $current=new DateTime(date('Y-m-d'));
                                                $targetdate=new DateTime($hardware->serviceEnd);
                                                $remaining=$targetdate->diff($current)->format("%a");
                                                $days=intval($remaining);
                                                if ($days!=0) {
                                                    echo "<td>" . $days. " days</td>";
                                                }else{
                                                    echo "<td></td>";
                                                }
                                                
                                                echo "</tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Name</th>
                                            <th>Brand</th>
                                            <th>Serial Key</th>
                                            <th>Status</th>
                                            <th>Customer</th>
                                            <th>Sales Date</th>
                                            <th>Warranty End Date</th>
                                            <th>Remaining</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            <?php
                            } else {
                            ?>
                                <table id="example1" class="table table-bordered table-striped table-sm" data-page-length='10'>
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Item Code</th>
                                            <th>Name</th>
                                            <th>Stock Balance</th>
                                            <th>Unit Price</th>
                                            <th>Brand</th>
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
                                            <th>Stock Balance</th>
                                            <th>Unit Price</th>
                                            <th>Brand</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            <?php
                            }
                            ?>
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