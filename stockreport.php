<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';

?>

<?php
if (!isset($_POST['btnSearchReport'])) {
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Stock Report</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="">Stock Report</a></li>
                            <li class="breadcrumb-item active">Explorer</li>
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
                        <form method="post">
                            <div class="card">
                                <!-- /.card-header -->
                                <div class="card-body p-4">
                                    <div class="form-row">
                                        <div class="form-group col-md-1"></div>
                                        <div class="form-group col-md-4">
                                            <label for="inputPassword4">From</label>
                                            <input type="date" class="form-control" name="tfFromDate" id="tfFromDate" autocomplete="off" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="inputPassword4">To</label>
                                            <input type="date" class="form-control" name="tfToDate" id="tfToDate" autocomplete="off" required>
                                        </div>
                                        <div class="form-group col-md-1 ml-3">
                                            <label for="inputPassword4">&nbsp;</label><br>
                                            <input type="submit" class="btn btn-primary" name="btnSearchReport" id="btnSearchReport" value="Search">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
<?php
}
?>
<!-- Content Wrapper. Contains page content -->
<?php
if (isset($_POST['btnSearchReport'])) {
    $resultArr = Util::stockReport($_POST['tfFromDate'], $_POST['tfToDate']);
    $hardwareArr = $resultArr[0];
    $openingArr = $resultArr[1];
    $stockInArr = $resultArr[2];
    $stockOutArr = $resultArr[3];



?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1><a href="stockreport.php" class="mr-3"><i class="fas fa-angle-left"></i></a> Stock Balance (<?php echo "From ".$_POST['tfFromDate']." To ".$_POST['tfToDate'] ?>)</h1>
                        <?php
                        // echo "Hardware";
                        // foreach ($hardwareArr as $h) {
                        //     echo $h->itemName;
                        // }
                        // echo "<br>Opening <br>";
                        // foreach ($openingArr as $op) {
                        //     echo $op;
                        // }
                        // echo "<br>Stock In <br>";
                        // foreach ($stockInArr as $si) {
                        //     echo $si;
                        // }
                        // echo "<br>Stock Out <br>";
                        // foreach ($stockOutArr as $so) {
                        //     echo $so;
                        // }
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Stock Balance Report</a></li>
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
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped table-sm" data-page-length='10'>
                                    <thead>
                                        <tr>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Opening Inventory</th>
                                            <th>Stock In</th>
                                            <th>Stock Out</th>
                                            <th>Closing Inventory</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $counter=0;
                                        foreach ($hardwareArr as $hardware) {
                                            echo "<tr>";
                                            echo "<td>".$hardware->brandName."</td>";
                                            echo "<td>".$hardware->itemName."</td>";
                                            echo "<td>".$openingArr[$counter]."</td>";
                                            echo "<td>".$stockInArr[$counter]."</td>";
                                            echo "<td>".$stockOutArr[$counter]."</td>";
                                            echo "<td>".($openingArr[$counter]+$stockInArr[$counter]-$stockOutArr[$counter])."</td>";
                                            echo "</tr>";
                                            $counter++;
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Opening Inventory</th>
                                            <th>Stock In</th>
                                            <th>Stock Out</th>
                                            <th>Closing Inventory</th>
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
<?php
}
?>

<!-- /.content-wrapper -->
<!-- /.content-wrapper -->
<!-- JS for disabling resubmission  -->
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            "buttons": ["print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<?php
include_once 'footer.php';
?>