<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/BrandOP.php';
include_once 'op/HardwareOP.php';

if (empty($userObj->general) || $userObj->general!="rw") {
    Util::alert("Access Denied!");
    Util::gogo("home.php");
}

if (isset($_REQUEST['hidtoupd'])) {
    $hidtoupd = $_REQUEST['hidtoupd'];

    $hardwareObj = new Hardware();
    $resultHardware = $hardwareObj->getHardwareWithID($hidtoupd);

    if ($resultHardware == 0) {
        Util::alert("hardwarelist.php");
    } else if ($resultHardware == "noresult") {
        Util::alert("hardwarelist.php");
    } else {
    }
} else {
    Util::alert("Invalid action!");
    Util::gogo("hardwarelist.php");
}

if (isset($_POST['btnEdit'])) {
    $itemCode=$_POST['tfItemCode'];
    $itemName=$_POST['tfItemName'];
    $quantity=$_POST['tfQuantity'];
    $unitPrice=$_POST['tfUnitPrice'];
    $currency=$_POST['cboCurrency'];
    $description=$_POST['tfDescription'];
    $brandId=$_POST['cboBrand'];

    $updateObj=new Hardware();
    $updateObj->__construct2($itemCode,$itemName,
    $quantity,$unitPrice,$currency,$description,
    $brandId);
    if ($updateObj->updateHardware($hidtoupd)) {
        Util::alert("Successfully Edited");
        Util::gogo("hardwarelist.php");
    }else{
        Util::alert("Edit Failed");
    }
}

?>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><a href="hardwarelist.php" class="pr-3"><i class="fas fa-angle-left"></i></a>Edit Hardware</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">General</a></li>
                        <li class="breadcrumb-item active">Edit Hardware</li>
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
                        <div class="card-body p-4">
                            <form method="post">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="inputAddress">Item Code (must be unique)</label>
                                        <input type="text" class="form-control" name="tfItemCode" id="tfItemCode" value="<?php echo $resultHardware->itemCode; ?>" placeholder="(e.g T0001)" readonly autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label for="inputPassword4">Item Name</label>
                                        <input type="text" class="form-control" name="tfItemName" id="tfItemName" value="<?php echo $resultHardware->itemName; ?>" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label for="inputAddress">Quantity</label>
                                        <input type="number" class="form-control" name="tfQuantity" id="tfQuantity" value="<?php echo $resultHardware->quantity; ?>" readonly autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="inputPassword4">Unit Price</label>
                                        <input type="number" class="form-control" name="tfUnitPrice" id="tfUnitPrice" value="<?php echo $resultHardware->unitPrice; ?>" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="exampleFormControlSelect1">Currency</label>
                                        <select class="form-control" name="cboCurrency" id="cboCurrency" required>
                                            <option value="">No Currency Selected</option>
                                            <?php
                                            $currencyArr = ["MMK", "USD", "SGD", "Euro", "Yen", "Pound"];
                                            foreach ($currencyArr as $currency) {
                                                if ($currency == $resultHardware->currency) {
                                                    echo "<option value='$currency' selected>$currency</option>";
                                                } else {
                                                    echo "<option value='$currency'>$currency</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="exampleFormControlSelect1">Brand</label>
                                        <select class="form-control" name="cboBrand" id="cboBrand" required>
                                            <?php
                                            $brandObj = new Brand();
                                            $resultBrand = $brandObj->getAllBrand();
                                            foreach ($resultBrand as $brand) {
                                                if ($brand->brandId==$resultHardware->brandId) {
                                                    echo "<option value='" . $brand->brandId . "' selected>" . $brand->brandName . "</option>";
                                                }else{
                                                    echo "<option value='" . $brand->brandId . "'>" . $brand->brandName . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputAddress">Description</label>
                                        <textarea name="tfDescription" id="" rows="10" class="form-control"><?php echo $resultHardware->description; ?></textarea>
                                    </div>
                                </div>
                                <div class="row justify-content-end mx-1">                                    
                                    <button type="submit" class="btn btn-primary" name="btnEdit">Edit</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->

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