<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/PersonOP.php';
include_once 'op/HardwareOP.php';
include_once 'op/HardwareDetailOP.php';
include_once 'op/StockOutOP.php';

if (empty($userObj->stockout) || $userObj->stockout != "rw") {
    Util::alert("Access Denied!");
    Util::gogo("home.php");
}

if (isset($_POST['btnCreate'])) {
    

    // die();

    //step 1 - insert sale invoice
    $so_date = $_POST['tfSoDate'];
    $so_doNumber = $_POST['tfDoNumber'];
    $so_quantity = $_POST['tfQuantity'];
    $so_remark = $_POST['tfRemark'];
    $so_customerId = $_POST['cboCustomer'];


    if (!empty($_FILES["tfDoFile"]["name"])) {
        $target_file = 'doDoc/' . basename($_FILES["tfDoFile"]["name"]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if ($fileType == "pdf") {
            if (!file_exists($target_file)) {
                if (move_uploaded_file($_FILES["tfDoFile"]["tmp_name"], $target_file)) {
                    $saleObj = new StockOut();
                    $saleObj->__construct2(
                        $so_date,
                        $so_doNumber,
                        $target_file,
                        $so_quantity,
                        $so_remark,
                        $so_customerId
                    );
                    $resultSaleInsert = $saleObj->insertStockOut();
                    if ($resultSaleInsert == "fail") {
                        Util::alert("Failed to insert an stock out in step 1");
                    } else {
                        if (isset($_POST['noSerialKeyCheck'])) {
                            $hdObj1=new HardwareDetail();
                            $hardwareIdArr = explode("||", $_POST['cboItemCode']);
                            $selectedValues=$hdObj1->getAllHDIDDueToStockOutQTY($hardwareIdArr[0],$so_quantity);
                        }else{
                            if (isset($_POST['serialMultiSelect'])) {
                                $selectedValues = $_POST['serialMultiSelect'];
                            }
                        }

                        $hardwareDetailObj = new HardwareDetail();
                        if ($hardwareDetailObj->updateForSaleInvoice($selectedValues, $resultSaleInsert, 'sold out', $_POST['tfStartDate'], $_POST['cboDuration'], $_POST['tfEndDate'])) {

                            $hardwareObj = new Hardware();

                            $hardwareIdArr = explode("||", $_POST['cboItemCode']);
                            if ($hardwareObj->updateHardwareStockQty($hardwareIdArr[0], $so_quantity, "sub")) {
                                Util::alert("Successfully created an stock out...");
                                Util::gogo("stockoutadd.php");
                            } else {
                                Util::alert("Failed to insert an stock out in step 3");
                            }
                        } else {
                            Util::alert("Failed to insert an stock out in step 2");
                        }
                    }
                } else {
                    Util::alert("Error in file uploading");
                }
            } else {
                $saleObj = new StockOut();
                $saleObj->__construct2(
                    $so_date,
                    $so_doNumber,
                    $target_file,
                    $so_quantity,
                    $so_remark,
                    $so_customerId
                );
                $resultSaleInsert = $saleObj->insertStockOut();
                if ($resultSaleInsert == "fail") {
                    Util::alert("Failed to insert an stock out in step 1");
                } else {
                    if (isset($_POST['noSerialKeyCheck'])) {
                        $hdObj1=new HardwareDetail();
                        $hardwareIdArr = explode("||", $_POST['cboItemCode']);
                        $selectedValues=$hdObj1->getAllHDIDDueToStockOutQTY($hardwareIdArr[0],$so_quantity);
                    }else{
                        if (isset($_POST['serialMultiSelect'])) {
                            $selectedValues = $_POST['serialMultiSelect'];
                        }
                    }

                    $hardwareDetailObj = new HardwareDetail();
                    if ($hardwareDetailObj->updateForSaleInvoice($selectedValues, $resultSaleInsert, 'sold out', $_POST['tfStartDate'], $_POST['cboDuration'], $_POST['tfEndDate'])) {

                        $hardwareObj = new Hardware();

                        $hardwareIdArr = explode("||", $_POST['cboItemCode']);
                        if ($hardwareObj->updateHardwareStockQty($hardwareIdArr[0], $so_quantity, "sub")) {
                            Util::alert("Successfully created an stock out...");
                            Util::gogo("stockoutadd.php");
                        } else {
                            Util::alert("Failed to insert an stock out in step 3");
                        }
                    } else {
                        Util::alert("Failed to insert an stock out in step 2");
                    }
                }
            }
        } else {
            Util::alert("Only pdf file is allowed!");
        }
    } else {
        
        // die();
        $saleObj = new StockOut();
        $saleObj->__construct2(
            $so_date,
            $so_doNumber,
            "",
            $so_quantity,
            $so_remark,
            $so_customerId
        );
        
        $resultSaleInsert = $saleObj->insertStockOut();
        if ($resultSaleInsert == "fail") {
            Util::alert("Failed to insert an stock out in step 1");
        } else {
            if (isset($_POST['noSerialKeyCheck'])) {
                $hdObj1=new HardwareDetail();
                $hardwareIdArr = explode("||", $_POST['cboItemCode']);
                $selectedValues=$hdObj1->getAllHDIDDueToStockOutQTY($hardwareIdArr[0],$so_quantity);
                Util::alert(count($selectedValues));
            }else{
                if (isset($_POST['serialMultiSelect'])) {
                    $selectedValues = $_POST['serialMultiSelect'];
                }
            }
            

            $hardwareDetailObj = new HardwareDetail();
            if ($hardwareDetailObj->updateForSaleInvoice($selectedValues, $resultSaleInsert, 'sold out', $_POST['tfStartDate'], $_POST['cboDuration'], $_POST['tfEndDate'])) {

                $hardwareObj = new Hardware();

                $hardwareIdArr = explode("||", $_POST['cboItemCode']);
                if ($hardwareObj->updateHardwareStockQty($hardwareIdArr[0], $so_quantity, "sub")) {
                    Util::alert("Successfully created a stock out...");
                    Util::gogo("stockoutadd.php");
                } else {
                    Util::alert("Failed to insert an stock out in step 3");
                }
            } else {
                Util::alert("Failed to insert an stock out in step 2");
            }
        }
    }
}

?>

<script>
    $(document).ready(function() {
        var myItemArray = "";
        $('#cboItemCode').on('change', function() {
            var selectedText = $(this).find(":selected").val();
            myItemArray = selectedText.split("||");

            $('#tfItemName').val(myItemArray[1]);
            let expectedIncreaseAmt = $('#tfQuantity').val() != "" ? $('#tfQuantity').val() : "";
            $('#tfItemQuantity').val(myItemArray[2]);
            $('#tfItemBrand').val(myItemArray[3]);
            $('#tfItemUnitPrice').val(myItemArray[4]);
            $('#tfItemDescription').val(myItemArray[5]);

            $('#tfTotalPrice').val();

            if (document.querySelector('#noSerialKeyCheck').checked) {
                document.querySelector('#qtyWarning').innerHTML="Maximum Available : "+myItemArray[2]+"<br> Quantity must not exceed than maximum!";
                document.querySelector('#tfQuantity').removeAttribute("readonly");
            }
            

            // -------------------------
            // Purchase Order Form
            var selectedItemCurrency = myItemArray[4].split(" - ");
            $('#tfCurrency').val(selectedItemCurrency[1]);

            //add to serial key box
            var serialArr = myItemArray[6];
            var selectBox = document.getElementById('serialMultiSelect');
            removeOptions(selectBox);
            if (serialArr == '"noserial"') {
                // let newOption = new Option('No Available Item (Serial Key) is found for this hardware', '');
                // selectBox.add(newOption, undefined);
            } else {
                var serialArr_js = JSON.parse(serialArr);
                var count = 0;
                for (var e of serialArr_js) {
                    count++;
                    let mySelectText = count + ' - ' + e.serialKey + ' - ' + e.status;
                    let newOption = new Option(mySelectText, e.hardwareDetailId);
                    selectBox.add(newOption, undefined);
                }
            }
            //


        });

        $('#selectedItemCount').text(0);

        $('#serialMultiSelect').on('change', function() {
            var count = $('#serialMultiSelect option:selected').length;
            if (count == 0) {
                $('#selectedItemCount').text(0);
            } else {
                $('#selectedItemCount').text(count);
            }

            $('#tfQuantity').val(count);

            //calculate total
            var selectedItemCurrency = myItemArray[4].split(" - ");
            $('#tfTotalPrice').val(selectedItemCurrency[0] * count);
        });

        function removeOptions(selectBox) {
            var i, L = selectBox.options.length - 1;
            for (i = L; i >= 0; i--) {
                selectBox.remove(i);
            }
        }

        $('#cboDuration').on('change', function() {
            calculateEndDate($('#cboDuration').find(":selected").val());
        });
        $('#tfStartDate').on('change', function() {
            calculateEndDate($('#cboDuration').find(":selected").val());
        });

        document.querySelector('#noSerialKeyCheck').addEventListener('change', function() {
            let rst = document.querySelector('#noSerialKeyCheck').checked;

            if (rst) { // if checked
                
                // document.querySelector('#serialMultiSelect').setAttribute("readonly", true);
                // document.querySelector('#serialMultiSelect').setAttribute("disabled");
                $('#selectedItemCount').text(0);
                $('#serialMultiSelect').val("");
                document.querySelector('#serialMultiSelect').removeAttribute("required");
                document.getElementById('hideNoSerialContent').style.display = "none";
            } else {
                document.querySelector('#tfQuantity').setAttribute("readonly", true);
                document.querySelector('#serialMultiSelect').removeAttribute("readonly");
                document.querySelector('#serialMultiSelect').removeAttribute("disabled");
                document.querySelector('#serialMultiSelect').setAttribute("required",true);
                document.getElementById('hideNoSerialContent').style.display = "block";

                document.querySelector('#qtyWarning').innerHTML="";
            }
        });

    });


    function calculateEndDate(chosenDuration) {
        var startDate = new Date($('#tfStartDate').val());

        switch (chosenDuration) {
            case "6 months":
                var recupDate = new Date(startDate);
                var endDate = new Date(recupDate.setMonth(recupDate.getMonth() + 6));
                document.getElementById('tfEndDate').valueAsDate = endDate;
                break;
            case "1 year":
                var recupDate = new Date(startDate);
                var endDate = new Date(recupDate.setFullYear(recupDate.getFullYear() + 1));
                document.getElementById('tfEndDate').valueAsDate = endDate;
                break;
            case "2 years":
                var recupDate = new Date(startDate);
                var endDate = new Date(recupDate.setFullYear(recupDate.getFullYear() + 2));
                document.getElementById('tfEndDate').valueAsDate = endDate;
                break;
            case "3 years":
                var recupDate = new Date(startDate);
                var endDate = new Date(recupDate.setFullYear(recupDate.getFullYear() + 3));
                document.getElementById('tfEndDate').valueAsDate = endDate;
                break;
            case "no service":
                $('#tfStartDate').val("");
                $('#tfEndDate').val("");
                break;
            default:

                break;
        }
    }
</script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><a href="solist.php" class="mr-3"><i class="fas fa-angle-left"></i></a> Stock Out</h1>

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Stock Out</a></li>
                        <li class="breadcrumb-item active">Stock Out Entry</li>
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
                    <form method="post" enctype="multipart/form-data">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title" style="font-weight: bold;">Stock Out Information</h3>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" value="" id="noSerialKeyCheck" name="noSerialKeyCheck">
                                        <label for="noSerialKeyCheck" class="form-check-label">No Serial Key</label>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-4">

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Delivery Order Number</label>
                                        <input type="text" class="form-control" name="tfDoNumber" id="tfDoNumber" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Delivery Order Document (Only PDF File is allowed)</label>
                                        <input type="file" class="form-control-file" name="tfDoFile" id="tfDoFile" autocomplete="off" accept="application/pdf">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="inputPassword4">Stock Out Date</label>
                                        <input type="date" class="form-control" name="tfSoDate" id="tfSoDate" value="<?php echo date("Y-m-d"); ?>" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="inputAddress">Quantity</label>
                                        <input type="number" class="form-control" name="tfQuantity" id="tfQuantity" value="" readonly autocomplete="off" required>
                                        <label for="" class="form-text" style="color:red" id="qtyWarning"></label>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="exampleFormControlSelect1">Customer</label>
                                        <select class="form-control" name="cboCustomer" id="cboCustomer" required>
                                            <option value="">No Currency Selected</option>
                                            <?php
                                            $supplierObj = new Person();
                                            $resultSupplier = $supplierObj->getAllPerson("customer");
                                            if ($resultSupplier == 0) {
                                                echo "<option value=''>Query Failed</option>";
                                            } else if ($resultSupplier == "noresult") {
                                                echo "<option value=''>No Supplier Found</option>";
                                            } else {
                                                foreach ($resultSupplier as $supplier) {
                                                    echo "<option value='" . $supplier->personId . "'>" . $supplier->contactPerson . " - " . $supplier->companyName . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="inputPassword4">Service Start Date</label>
                                        <input type="date" class="form-control" name="tfStartDate" id="tfStartDate" value="<?php echo date("Y-m-d"); ?>" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="inputAddress">Service Duration</label>
                                        <select class="form-control" name="cboDuration" id="cboDuration" required>
                                            <option value="">No Currency Selected</option>
                                            <option value="6 months">6 months</option>
                                            <option value="1 year">1 year</option>
                                            <option value="2 years">2 years</option>
                                            <option value="3 years">3 years</option>

                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="inputPassword4">Service End Date</label>
                                        <input type="date" class="form-control" name="tfEndDate" id="tfEndDate" autocomplete="off" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputAddress">Remark</label>
                                        <textarea name="tfRemark" id="" rows="5" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title" style="font-weight: bold;">Hardware Detail</h3>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-4">

                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label for="exampleFormControlSelect1">Item Code</label>
                                        <select class="form-control" name="cboItemCode" id="cboItemCode" required>
                                            <option value="">No Currency Selected</option>
                                            <?php
                                            $hardwareObj = new Hardware();
                                            $resultItemCode = $hardwareObj->getAllHardware();
                                            $hdetialObj = new HardwareDetail();
                                            foreach ($resultItemCode as $itemCode) {
                                                $serialArr = $hdetialObj->getSerialKeyWithHardwareID($itemCode->hardwareId);

                                                echo "<option value='";
                                                echo $itemCode->hardwareId . "||"
                                                    . $itemCode->itemName . "||" . $itemCode->quantity . "||"
                                                    . $itemCode->brandName . "||"
                                                    . $itemCode->unitPrice . " - " . $itemCode->currency . "||"
                                                    . $itemCode->description . "||"
                                                    . json_encode($serialArr);
                                                echo "'>";
                                                echo $itemCode->itemCode;
                                                echo "</option>";
                                            }
                                            ?>
                                        </select>

                                    </div>
                                    <div class="form-group col-md-5">
                                        <label for="inputPassword4">Item Name</label>
                                        <input type="text" class="form-control" name="tfItemName" id="tfItemName" autocomplete="off" readonly required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="inputPassword4">Existing Quantity</label>
                                        <input type="text" class="form-control" name="tfItemQuantity" id="tfItemQuantity" readonly autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputAddress">Brand</label>
                                        <input type="text" class="form-control" name="tfItemBrand" id="tfItemBrand" readonly autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="inputPassword4">Unit Price</label>
                                        <input type="text" class="form-control" name="tfItemUnitPrice" id="tfItemUnitPrice" readonly autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-9">
                                        <label for="inputAddress">Description</label>
                                        <textarea name="tfItemDescription" id="tfItemDescription" rows="5" class="form-control" readonly></textarea>
                                    </div>

                                </div>

                            </div>
                            <!-- /.card-body -->
                            <!-- /.card -->
                        </div>
                        <div class="card">
                            <form method="post" enctype="multipart/form-data">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="card-title" style="font-weight: bold;">Multiple selection of serial key for this sale</h3>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body p-4">
                                    <div class="form-row justify-content-center">
                                        <h4><span id="selectedItemCount"></span> items selected</h4>
                                    </div>
                                    <div class="form-row" id="hideNoSerialContent">
                                        <div class="form-group col-md-12">
                                            <select class="form-control form-control-lg" id="serialMultiSelect" name="serialMultiSelect[]" multiple style="height: 300px;" required>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-footer">
                                    <div class="row justify-content-end mx-1">
                                        <button type='submit' class='btn btn-primary' name='btnCreate'>Finish</button>
                                    </div>
                                </div>
                            </form>
                            <!-- /.card-body -->
                            <!-- /.card -->
                        </div>
                    </form>
                </div>
            </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<!-- JS for disabling resubmission  -->
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<?php
include_once 'footer.php';
?>