<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/BrandOP.php';

if (empty($userObj->general)) {
    Util::alert("Access Denied!");
    Util::gogo("home.php");
}

if (isset($_POST['btnSave'])) {
    $brandName = $_POST['tfBrand'];
    $brandNote = $_POST['tfNote'];

    $brandObj = new Brand();
    $brandObj->__construct1($brandName, $brandNote);
    if ($brandObj->insertBrand()) {
        Util::alert("Successfully Inserted");
    } else {
        Util::alert("Failed to insert");
    }
}

// alert confirmation for deletion
if (isset($_REQUEST['bidtodel'])) {
    $bidtodel = $_REQUEST['bidtodel'];
    echo "<script>
    if(confirm('Are you sure to delete?')){
      location='brandmng.php?bidtodelConfirm=$bidtodel';
    }else{
      location='brandmng.php';
    }
    </script>";
}
// do action to delete
if (isset($_REQUEST['bidtodelConfirm'])) {
    $bidtodelConfirm = $_REQUEST['bidtodelConfirm'];

    $brandObj3 = new Brand();
    if ($brandObj3->deleteBrand($bidtodelConfirm)) {
        Util::alert("Deleted");
    } else {
        Util::alert("Failed to delete");
    }
}

$bidtoedit = "";
if (isset($_REQUEST['bidtoedit'])) {
    $bidtoedit = $_REQUEST['bidtoedit'];

    $brandObj4 = new Brand();

    $resultBrandOne = $brandObj4->getBrandWithID($bidtoedit);
    if ($resultBrandOne == "noresult") {
        Util::alert("Selected brand does not acutally exist!");
    } else if ($resultBrandOne == false) {
        Util::alert("Query brand information failed!");
    } else {
        $edBrandName = $resultBrandOne->brandName;
        $edBrandNote = $resultBrandOne->note;

        echo "<script>
        $(document).ready(function(){
            $('#brandEdit').modal('show');
        });
        </script>";
    }
}

if (isset($_REQUEST['btnEdit'])) {
    $brandName = $_REQUEST['tfedBrand'];
    $brandNote = $_REQUEST['tfedNote'];

    $brandObj5 = new Brand();
    $brandObj5->__construct1($brandName, $brandNote);
    if ($brandObj5->updateBrand($bidtoedit)) {
        Util::alert("Successfully updated");
        Util::gogo("brandmng.php");
    } else {
        Util::alert("Failed to update!");
    }
}

?>
<script>
    $(document).ready(function() {
        $("#mySearch").on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    function goReload() {
        location = 'brandmng.php';
    }
</script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Brand (Hardware)</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">General</a></li>
                        <li class="breadcrumb-item active">Brand (Hardware)</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <?php if ($userObj->general == "rw") {  ?>
                <form method="post" class="row mx-5 mb-3">
                    <div class="form-group col-md-3 mx-sm-3 mb-2">
                        <label for="inputPassword2" class="sr-only">Brand</label>
                        <input type="text" name="tfBrand" class="form-control" id="inputPassword2" placeholder="Brand" autocomplete="off">
                    </div>
                    <div class="form-group col-md-6 mx-sm-3 mb-2">
                        <label for="inputPassword2" class="sr-only">Note</label>
                        <input type="text" name="tfNote" class="form-control" id="inputPassword2" placeholder="Note" autocomplete="off">
                    </div>
                    <button type="submit" name="btnSave" class="btn btn-primary btn-sm mb-2">Save</button>
                </form>
            <?php } ?>
            <!-- /.row -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Brand List</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <input type="text" name="table_search" class="form-control float-right" id="mySearch" placeholder="Search">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 400px;">
                            <table class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Brand</th>
                                        <th>Note</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="myTable">
                                    <?php
                                    $brandObj2 = new Brand();
                                    $arrBrand = $brandObj2->getAllBrand();
                                    if ($arrBrand == "noresult") {
                                        echo "<tr><td colspan='4' style='text-align:center;'>No Result Found</td></tr>";
                                    } else if ($arrBrand == false) {
                                        echo "<tr><td colspan='4' style='text-align:center;'>Query Failed</td></tr>";
                                    } else {
                                        $no = 0;
                                        foreach ($arrBrand as $brand) {
                                            $no++;
                                            $brandId = $brand->brandId;
                                            echo "
                                            <tr>
                                                <td>$no</td>
                                                <td>" . $brand->brandName . "</td>
                                                <td>" . $brand->note . "</td>
                                                <td>
                                            ";
                                            if ($userObj->general == "rw") {
                                                echo "
                                                <a href='brandmng.php?bidtodel=$brandId' class='btn btn-danger' title='Delete'><i class='fas fa-trash'></i></a>
                                                <a href='brandmng.php?bidtoedit=$brandId' class='btn btn-secondary' title='Edit'><i class='fas fa-pen'></i></a>
                                            ";
                                            }
                                            echo "</td></tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
</div><!-- /.container-fluid -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->



<!-- Modal -->
<div class="modal fade" id="brandEdit" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="goReload()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputAddress">Brand</label>
                            <input type="text" class="form-control" name="tfedBrand" value="<?php echo $edBrandName; ?>" id="inputAddress" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputAddress">Note</label>
                            <textarea name="tfedNote" id="" rows="8" class="form-control"><?php echo $edBrandNote; ?></textarea>
                        </div>
                    </div>
                    <div class="row justify-content-end mx-1">
                        <button type="submit" class="btn btn-primary" name="btnEdit">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once 'footer.php';
?>