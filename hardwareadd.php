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

if (isset($_POST['btnCreate'])) {
    $itemCode=$_POST['tfItemCode'];
    $itemName=$_POST['tfItemName'];
    $quantity=$_POST['tfQuantity'];
    $unitPrice=$_POST['tfUnitPrice'];
    $currency=$_POST['cboCurrency'];
    $description=$_POST['tfDescription'];
    $brandId=$_POST['cboBrand'];

    $hardwareObj=new Hardware();
    $hardwareObj->__construct2($itemCode,$itemName,
    $quantity,$unitPrice,$currency,$description,
    $brandId);

    $resultInsert=$hardwareObj->insertHardware();
    if ($resultInsert=="alreadyexisted") {
        Util::alert("This Item Code has been already existed! Hardware Entry Failed");
    }else if($resultInsert=="success"){
        Util::alert("Successfully Inserted");
    }else{
        Util::alert("Insert Query Failed!");
    }
}

?>

<script type="text/javascript">
    $(document).ready(function(){
        var tfExistingCode=$("#tfExistingCode")[0];
        var existingItemArr=[];
        for(var i=0;i<tfExistingCode.length;i++){
            existingItemArr.push(tfExistingCode.options[i].text);
        }

        $("#tfItemCode").on("keyup",function(){
            var temp=$(this).val();
            if (temp == "") {
                $('#checkIcon').removeClass();
                $('#checkText').text("Empty Text");
                $('#checkText').css("color", "red");
            } else {
                var result = existingItemArr.filter(function(n) {
                    return n === temp;
                });
                if (result != "") {
                    $('#checkIcon').removeClass().addClass("fa fa-times");
                    $('#checkText').text("This code has already existed");
                    $('#checkText').css("color", "red");
                    $('#checkIcon').css("color", "red");
                } else {
                    $('#checkIcon').removeClass().addClass("fa fa-check");
                    $('#checkText').text("Code is available");
                    $('#checkText').css("color", "green");
                    $('#checkIcon').css("color", "green");
                }
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
                    <h1><a href="hardwarelist.php" class="pr-3"><i class="fas fa-angle-left"></i></a>Add New Hardware</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">General</a></li>
                        <li class="breadcrumb-item active">Add Hardware</li>
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
                                        <input type="text" class="form-control" name="tfItemCode" id="tfItemCode" placeholder="(e.g T0001)" autocomplete="off" required>
                                        <small id="emailHelp" class="form-text text-muted"><i class="" id="checkIcon"></i> <span id="checkText"></span></small>
                                    </div>
                                    <div class="form-group col-md-3" style="display: none;">
                                        <label for="inputAddress">Existing Item (Hidden)</label>
                                        <select name="" id="tfExistingCode" class="form-control">
                                            <?php
                                            $hardwareObj2=new Hardware();
                                            $dataItemCode=$hardwareObj2->getAllItemCode();
                                            foreach ($dataItemCode as $itemCode) {
                                                echo "<option>".$itemCode->itemCode."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label for="inputPassword4">Item Name</label>
                                        <input type="text" class="form-control" name="tfItemName" id="tfItemName" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label for="inputAddress">Quantity</label>
                                        <input type="number" class="form-control" name="tfQuantity" id="tfQuantity" value="0" readonly autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="inputPassword4">Unit Price</label>
                                        <input type="number" class="form-control" name="tfUnitPrice" id="tfUnitPrice" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="exampleFormControlSelect1">Currency</label>
                                        <select class="form-control" name="cboCurrency" id="cboCurrency" required>
                                            <option value="">No Currency Selected</option>
                                            <option value="MMK">MMK</option>
                                            <option value="USD">USD</option>
                                            <option value="SGD">SGD</option>
                                            <option value="Euro">Euro</option>
                                            <option value="Yen">Yen</option>
                                            <option value="Pound">Pound</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="exampleFormControlSelect1">Brand</label>
                                        <select class="form-control" name="cboBrand" id="cboBrand" required>
                                            <?php
                                            $brandObj=new Brand();
                                            $resultBrand=$brandObj->getAllBrand();
                                            if ($resultBrand=="noresult") {
                                                echo "<option value=''>No Brand Found</option>";
                                            }else{
                                                echo "<option value=''>No Brand Selected</option>";
                                                foreach ($resultBrand as $brand) {
                                                    echo "<option value='".$brand->brandId."'>".$brand->brandName."</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputAddress">Description</label>
                                        <textarea name="tfDescription" id="" rows="10" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="row justify-content-end mx-1">
                                    <button type="reset" class="btn btn-secondary" style="margin-right: 10px;">Clear</button>
                                    <button type="submit" class="btn btn-primary" name="btnCreate">Create</button>
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