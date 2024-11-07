<?php
session_start();
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/PersonOP.php';
include_once 'op/HardwareOP.php';
include_once 'op/HardwareDetailOP.php';
include_once 'op/StockInOP.php';

if (empty($userObj->stockin) || $userObj->stockin!="rw") {
    Util::alert("Access Denied!");
    Util::gogo("home.php");
  }

$duplicateStatus = 1;


if (isset($_POST['btnUploadFile'])) {
    $fileinfo = $_FILES['serialFile']['name'];
    if ($fileinfo) {
        $duplicateStatus = 0;
        $serialKeyArr = [];
        $duplicateEvent = "";
        $extension = strtolower(pathinfo($fileinfo, PATHINFO_EXTENSION));
        if ($extension == "csv" || $extension == "txt") {
            $handle = fopen($_FILES['serialFile']['tmp_name'], "r");
            $handleTemp = fopen($_FILES['serialFile']['tmp_name'], "r");
            $dataTemp = fgetcsv($handleTemp, 1000, ",");
            if (count($dataTemp) == 1) {
                Util::alert("Loaded data successfully");
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    //for loop 1, first row will appear
                    // for loop 2, second row
                    // Util::alert($data[0]);  //its mean column 1 of row 1;
                    // Util::alert("Row ".$counter." - ".$data[0]);
                    // $counter++;
                    array_push($serialKeyArr, $data[0]);
                }

                if ($serialKeyArr != "") {
                    $duplicateEvent = array_count_values($serialKeyArr);
                    foreach ($serialKeyArr as $serialKey) {
                        if ($duplicateEvent[$serialKey] > 1) {
                            $duplicateStatus = 1;
                        }
                    }
                }

                $_SESSION['SKS'] = $serialKeyArr;
            } else {
                Util::alert("Number of column should be one! It exceeds more than identified");
            }
        } else {
            Util::alert("This file is not supported! Supported File types are xlsx,csv and txt");
        }
    } else {
        Util::alert("No File Found! Please choose an excel supported files to load serial keys");
    }
}

if (isset($_POST['btnClearSerial'])) {
    unset($serialKeyArr);
    unset($duplicateEvent);
    unset($_SESSION['SKS']);
    header("location:purchaseorder.php");
}

if (isset($_POST['btnCreate'])) {
    $si_date = $_POST['tfStockInDate'];
    $si_poNumber = $_POST['tfPoNumber'];
    $si_quantity = $_POST['tfQuantity'];
    $si_remark = $_POST['tfRemark'];
    $si_supplierid = $_POST['cboSupplier'];

    if (isset($_POST['noSerialKeyCheck'])) {
        // if you have no serial
        if (!empty($_FILES["tfPoFile"]["name"])) {  //if file is uploaded
            // Check PO File
            $target_file = "poDoc/" . basename($_FILES["tfPoFile"]["name"]);
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if ($fileType == "pdf") {
                if (!file_exists($target_file)) {
                    if (move_uploaded_file($_FILES["tfPoFile"]["tmp_name"], $target_file)) {
                        $stockinObj = new StockIn();
                        $stockinObj->__construct2(
                            $si_date,
                            $si_poNumber,
                            $target_file,
                            $si_quantity,
                            $si_remark,
                            $si_supplierid
                        );
                        $laststockinid = $stockinObj->insertStockIn();
                        if ($laststockinid == "fail") {
                            Util::alert("Failed to create an stock in!");
                        } else {
                            // Loop adding to hardware detail
                            $hardwareDetailObj = new HardwareDetail();
                            $hardwareIdData = explode("||", $_POST['cboItemCode']);
                            $hardwareId = $hardwareIdData[0];
                            $status = "in stock";

                            $statusWhileHardwareDetail = "";
                            $serialKeyArr = giveNullArrayForNOSerial($si_quantity);
                            foreach ($serialKeyArr as $serialKey) {
                                $hardwareDetailObj->__construct2($serialKey, $hardwareId, $laststockinid, $status);
                                $resultHardwareDetail = $hardwareDetailObj->insertHardwareDetail();
                                if ($resultHardwareDetail == "success") {
                                    $statusWhileHardwareDetail = "success";
                                } else if ($resultHardwareDetail == "already") {
                                    $statusWhileHardwareDetail = "already";
                                    break;
                                } else {
                                    $statusWhileHardwareDetail = "fail";
                                    break;
                                }
                            }


                            // if success to loop, update master hardware stock
                            if ($statusWhileHardwareDetail == "success") {
                                $updateStockObj = new Hardware();

                                if ($updateStockObj->updateHardwareStockQty($hardwareId, $si_quantity, "add")) {
                                    Util::alert("Successfully added a stock in...");
                                    Util::gogo("stockinadd.php");
                                } else {
                                    Util::alert("Failed to purchase an order!");
                                }
                            } else if ($statusWhileHardwareDetail == "already") {
                                Util::alert("Some serial key are already existed! Failed to purchase an order");
                                Util::doRecoverOfSerialKeyFailureOnPurchase($laststockinid);
                            } else {
                                Util::alert("Something wrong in serial key looping");
                            }
                        }
                    } else {
                        Util::alert("Error in PO File uploading!");
                    }
                } else {
                    $stockinObj = new StockIn();
                    $stockinObj->__construct2(
                        $si_date,
                        $si_poNumber,
                        $target_file,
                        $si_quantity,
                        $si_remark,
                        $si_supplierid
                    );
                    $laststockinid = $stockinObj->insertStockIn();
                    if ($laststockinid == "fail") {
                        Util::alert("Failed to create an stock in!");
                    } else {
                        // Loop adding to hardware detail
                        $hardwareDetailObj = new HardwareDetail();
                        $hardwareIdData = explode("||", $_POST['cboItemCode']);
                        $hardwareId = $hardwareIdData[0];
                        $status = "in stock";

                        $statusWhileHardwareDetail = "";
                        $serialKeyArr = giveNullArrayForNOSerial($si_quantity);
                        foreach ($serialKeyArr as $serialKey) {
                            $hardwareDetailObj->__construct2($serialKey, $hardwareId, $laststockinid, $status);
                            $resultHardwareDetail = $hardwareDetailObj->insertHardwareDetail();
                            if ($resultHardwareDetail == "success") {
                                $statusWhileHardwareDetail = "success";
                            } else if ($resultHardwareDetail == "already") {
                                $statusWhileHardwareDetail = "already";
                                break;
                            } else {
                                $statusWhileHardwareDetail = "fail";
                                break;
                            }
                        }


                        // if success to loop, update master hardware stock
                        if ($statusWhileHardwareDetail == "success") {
                            $updateStockObj = new Hardware();

                            if ($updateStockObj->updateHardwareStockQty($hardwareId, $si_quantity, "add")) {
                                Util::alert("Successfully added a stock in...");
                                Util::gogo("stockinadd.php");
                            } else {
                                Util::alert("Failed to purchase an order!");
                            }
                        } else if ($statusWhileHardwareDetail == "already") {
                            Util::alert("Some serial key are already existed! Failed to purchase an order");
                            Util::doRecoverOfSerialKeyFailureOnPurchase($laststockinid);
                        } else {
                            Util::alert("Something wrong in serial key looping");
                        }
                    }
                }
            } else {
                Util::alert("Only PDF file is allowed");
            }
        } else {
            $stockinObj = new StockIn();
            $stockinObj->__construct2(
                $si_date,
                $si_poNumber,
                "",
                $si_quantity,
                $si_remark,
                $si_supplierid
            );
            $laststockinid = $stockinObj->insertStockIn();
            if ($laststockinid == "fail") {
                Util::alert("Failed to create an stock in!");
            } else {
                // Loop adding to hardware detail
                $hardwareDetailObj = new HardwareDetail();
                $hardwareIdData = explode("||", $_POST['cboItemCode']);
                $hardwareId = $hardwareIdData[0];
                $status = "in stock";

                $statusWhileHardwareDetail = "";
                $serialKeyArr = giveNullArrayForNOSerial($si_quantity);
                foreach ($serialKeyArr as $serialKey) {
                    $hardwareDetailObj->__construct2($serialKey, $hardwareId, $laststockinid, $status);
                    $resultHardwareDetail = $hardwareDetailObj->insertHardwareDetail();
                    if ($resultHardwareDetail == "success") {
                        $statusWhileHardwareDetail = "success";
                    } else if ($resultHardwareDetail == "already") {
                        $statusWhileHardwareDetail = "already";
                        break;
                    } else {
                        $statusWhileHardwareDetail = "fail";
                        break;
                    }
                }


                // if success to loop, update master hardware stock
                if ($statusWhileHardwareDetail == "success") {
                    $updateStockObj = new Hardware();

                    if ($updateStockObj->updateHardwareStockQty($hardwareId, $si_quantity, "add")) {
                        Util::alert("Successfully added a stock in...");
                        Util::gogo("stockinadd.php");
                    } else {
                        Util::alert("Failed to purchase an order!");
                    }
                } else if ($statusWhileHardwareDetail == "already") {
                    Util::alert("Some serial key are already existed! Failed to purchase an order");
                    Util::doRecoverOfSerialKeyFailureOnPurchase($laststockinid);
                } else {
                    Util::alert("Something wrong in serial key looping");
                }
            }
        }
    } else {
        // if you need to upload serial
        if (!empty($_FILES["tfPoFile"]["name"])) {  //if file is uploaded
            // Check PO File
            $target_file = "poDoc/" . basename($_FILES["tfPoFile"]["name"]);
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if ($fileType == "pdf") {
                if (!file_exists($target_file)) {
                    if (move_uploaded_file($_FILES["tfPoFile"]["tmp_name"], $target_file)) {
                        $stockinObj = new StockIn();
                        $stockinObj->__construct2(
                            $si_date,
                            $si_poNumber,
                            $target_file,
                            $si_quantity,
                            $si_remark,
                            $si_supplierid
                        );
                        $laststockinid = $stockinObj->insertStockIn();
                        if ($laststockinid == "fail") {
                            Util::alert("Failed to create an stock in!");
                        } else {
                            // Loop adding to hardware detail
                            $hardwareDetailObj = new HardwareDetail();
                            $hardwareIdData = explode("||", $_POST['cboItemCode']);
                            $hardwareId = $hardwareIdData[0];
                            $status = "in stock";

                            $statusWhileHardwareDetail = "";
                            $serialKeyArr = $_SESSION['SKS'];
                            foreach ($serialKeyArr as $serialKey) {
                                $hardwareDetailObj->__construct2($serialKey, $hardwareId, $laststockinid, $status);
                                $resultHardwareDetail = $hardwareDetailObj->insertHardwareDetail();
                                if ($resultHardwareDetail == "success") {
                                    $statusWhileHardwareDetail = "success";
                                } else if ($resultHardwareDetail == "already") {
                                    $statusWhileHardwareDetail = "already";
                                    break;
                                } else {
                                    $statusWhileHardwareDetail = "fail";
                                    break;
                                }
                            }


                            // if success to loop, update master hardware stock
                            if ($statusWhileHardwareDetail == "success") {
                                $updateStockObj = new Hardware();

                                if ($updateStockObj->updateHardwareStockQty($hardwareId, $si_quantity, "add")) {
                                    Util::alert("Successfully added a stock in...");
                                    Util::gogo("stockinadd.php");
                                } else {
                                    Util::alert("Failed to purchase an order!");
                                }
                            } else if ($statusWhileHardwareDetail == "already") {
                                Util::alert("Some serial key are already existed! Failed to purchase an order");
                                Util::doRecoverOfSerialKeyFailureOnPurchase($laststockinid);
                            } else {
                                Util::alert("Something wrong in serial key looping");
                            }
                        }
                    } else {
                        Util::alert("Error in PO File uploading!");
                    }
                } else {
                    $stockinObj = new StockIn();
                    $stockinObj->__construct2(
                        $si_date,
                        $si_poNumber,
                        $target_file,
                        $si_quantity,
                        $si_remark,
                        $si_supplierid
                    );
                    $laststockinid = $stockinObj->insertStockIn();
                    if ($laststockinid == "fail") {
                        Util::alert("Failed to create an stock in!");
                    } else {
                        // Loop adding to hardware detail
                        $hardwareDetailObj = new HardwareDetail();
                        $hardwareIdData = explode("||", $_POST['cboItemCode']);
                        $hardwareId = $hardwareIdData[0];
                        $status = "in stock";

                        $statusWhileHardwareDetail = "";
                        $serialKeyArr = $_SESSION['SKS'];
                        foreach ($serialKeyArr as $serialKey) {
                            $hardwareDetailObj->__construct2($serialKey, $hardwareId, $laststockinid, $status);
                            $resultHardwareDetail = $hardwareDetailObj->insertHardwareDetail();
                            if ($resultHardwareDetail == "success") {
                                $statusWhileHardwareDetail = "success";
                            } else if ($resultHardwareDetail == "already") {
                                $statusWhileHardwareDetail = "already";
                                break;
                            } else {
                                $statusWhileHardwareDetail = "fail";
                                break;
                            }
                        }


                        // if success to loop, update master hardware stock
                        if ($statusWhileHardwareDetail == "success") {
                            $updateStockObj = new Hardware();

                            if ($updateStockObj->updateHardwareStockQty($hardwareId, $si_quantity, "add")) {
                                Util::alert("Successfully added a stock in...");
                                Util::gogo("stockinadd.php");
                            } else {
                                Util::alert("Failed to purchase an order!");
                            }
                        } else if ($statusWhileHardwareDetail == "already") {
                            Util::alert("Some serial key are already existed! Failed to purchase an order");
                            Util::doRecoverOfSerialKeyFailureOnPurchase($laststockinid);
                        } else {
                            Util::alert("Something wrong in serial key looping");
                        }
                    }
                }
            } else {
                Util::alert("Only PDF file is allowed");
            }
        } else {
            $stockinObj = new StockIn();
            $stockinObj->__construct2(
                $si_date,
                $si_poNumber,
                "",
                $si_quantity,
                $si_remark,
                $si_supplierid
            );
            $laststockinid = $stockinObj->insertStockIn();
            if ($laststockinid == "fail") {
                Util::alert("Failed to create an stock in!");
            } else {
                // Loop adding to hardware detail
                $hardwareDetailObj = new HardwareDetail();
                $hardwareIdData = explode("||", $_POST['cboItemCode']);
                $hardwareId = $hardwareIdData[0];
                $status = "in stock";

                $statusWhileHardwareDetail = "";
                $serialKeyArr = $_SESSION['SKS'];   // check here whether there's serial or not
                foreach ($serialKeyArr as $serialKey) {
                    $hardwareDetailObj->__construct2($serialKey, $hardwareId, $laststockinid, $status);
                    $resultHardwareDetail = $hardwareDetailObj->insertHardwareDetail();
                    if ($resultHardwareDetail == "success") {
                        $statusWhileHardwareDetail = "success";
                    } else if ($resultHardwareDetail == "already") {
                        $statusWhileHardwareDetail = "already";
                        break;
                    } else {
                        $statusWhileHardwareDetail = "fail";
                        break;
                    }
                }


                // if success to loop, update master hardware stock
                if ($statusWhileHardwareDetail == "success") {
                    $updateStockObj = new Hardware();

                    if ($updateStockObj->updateHardwareStockQty($hardwareId, $si_quantity, "add")) {
                        Util::alert("Successfully added a stock in...");
                        Util::gogo("stockinadd.php");
                    } else {
                        Util::alert("Failed to purchase an order!");
                    }
                } else if ($statusWhileHardwareDetail == "already") {
                    Util::alert("Some serial key are already existed! Failed to purchase an order");
                    Util::doRecoverOfSerialKeyFailureOnPurchase($laststockinid);
                } else {
                    Util::alert("Something wrong in serial key looping");
                }
            }
        }
    }
}

function giveNullArrayForNOSerial($si_quantity){
    $nullArray=array();
    for ($i=0; $i <$si_quantity ; $i++) { 
        array_push($nullArray,NULL);
    }
    return $nullArray;
}

?>

<script>
    $(document).ready(function() {
        $('#cboItemCode').on('change', function() {
            var selectedText = $(this).find(":selected").val();
            var myItemArray = selectedText.split("||");

            $('#tfItemName').val(myItemArray[1]);
            let expectedIncreaseAmt = $('#tfQuantity').val() != "" ? $('#tfQuantity').val() : "";
            $('#tfItemQuantity').val(myItemArray[2] + "( +" + expectedIncreaseAmt + " )");
            $('#tfItemBrand').val(myItemArray[3]);
            $('#tfItemUnitPrice').val(myItemArray[4]);
            $('#tfItemDescription').val(myItemArray[5]);

        });

        $('#tfQuantity').on('change',function() {
            if($('#tfItemQuantity').val()!=""){
                var existing=$('#tfItemQuantity').val().split("(");
                var expectedIncreaseAmt=$('#tfQuantity').val();
                $('#tfItemQuantity').val("");
                $('#tfItemQuantity').val(existing[0] +"( +"+ expectedIncreaseAmt+" )");
            }
        });

        $('#tfQuantity').on('keyup',function() {
            if($('#tfItemQuantity').val()!=""){
                var existing=$('#tfItemQuantity').val().split("(");
                var expectedIncreaseAmt=$('#tfQuantity').val();
                $('#tfItemQuantity').val("");
                $('#tfItemQuantity').val(existing[0] +"( +"+ expectedIncreaseAmt+" )");
            }
        });

        

        document.querySelector('#noSerialKeyCheck').addEventListener('change', function() {
            let rst = document.querySelector('#noSerialKeyCheck').checked;
            if (rst) { // if checked
                document.querySelector('#serialFileBox').setAttribute("disabled", true);

                document.querySelector('#cboItemCode').removeAttribute("disabled");
                document.querySelector('#tfPoNumber').removeAttribute("disabled");
                document.querySelector('#tfPoFile').removeAttribute("disabled");
                document.querySelector('#tfQuantity').removeAttribute("disabled");
                document.querySelector('#tfQuantity').removeAttribute("readonly");
                document.querySelector('#cboSupplier').removeAttribute("disabled");
                document.querySelector('#tfRemark').removeAttribute("disabled");
                document.querySelector('#tfRemark').removeAttribute("readonly");
                document.querySelector('#tfStockInDate').removeAttribute("disabled");

                document.querySelector('#btnCreate').removeAttribute("disabled");

            } else {
                document.querySelector('#serialFileBox').removeAttribute("disabled");

                document.querySelector('#cboItemCode').setAttribute("disabled", true);
                document.querySelector('#tfPoNumber').setAttribute("disabled", true);
                document.querySelector('#tfPoFile').setAttribute("disabled", true);
                document.querySelector('#tfQuantity').setAttribute("disabled", true);
                document.querySelector('#cboSupplier').setAttribute("disabled", true);
                document.querySelector('#tfRemark').setAttribute("disabled", true);

                document.querySelector('#btnCreate').setAttribute("disabled", true);
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
                    <h1><a href="silist.php" class="mr-3"><i class="fas fa-angle-left"></i></a> Stock In</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Stock In</a></li>
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
                                    <h3 class="card-title" style="font-weight: bold;">Stock In Information</h3>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" value="" id="noSerialKeyCheck" name="noSerialKeyCheck">
                                        <label for="noSerialKeyCheck" class="form-check-label">No Serial Key</label>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-4">
                                <?php
                                if ($duplicateStatus == 0) {
                                ?>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputAddress">Purchase Order Number</label>
                                            <input type="text" class="form-control" name="tfPoNumber" id="tfPoNumber" autocomplete="off" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputAddress">Purchase Order Document (Only PDF File is allowed)</label>
                                            <input type="file" class="form-control-file" name="tfPoFile" id="tfPoFile" autocomplete="off" accept="application/pdf">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="inputPassword4">Stock In Date</label>
                                            <input type="date" class="date form-control" name="tfStockInDate" id="tfStockInDate" value="<?php echo date('Y-m-d'); ?>" autocomplete="off" required>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="inputAddress">Quantity</label>
                                            <input type="number" class="form-control" name="tfQuantity" id="tfQuantity" value="<?php if (isset($serialKeyArr)) {
                                                                                                                                    echo sizeof($serialKeyArr);
                                                                                                                                } else {
                                                                                                                                    echo "";
                                                                                                                                } ?>" readonly autocomplete="off" required>
                                            <small>Choose serial key file and load data to display quanity</small>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="exampleFormControlSelect1">Supplier</label>
                                            <select class="form-control" name="cboSupplier" id="cboSupplier" required>
                                                <option value="">No Supplier Selected</option>
                                                <?php
                                                $supplierObj = new Person();
                                                $resultSupplier = $supplierObj->getAllPerson("supplier");
                                                if ($resultSupplier == 0) {
                                                    echo "<option value=''>Query Failed</option>";
                                                } else if ($resultSupplier == "noresult") {
                                                    // echo "<option value=''>No Supplier Found</option>";
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
                                        <div class="form-group col-md-12">
                                            <label for="inputAddress">Remark</label>
                                            <textarea name="tfRemark" id="tfRemark" rows="5" class="form-control"></textarea>
                                        </div>
                                    </div>
                                <?php
                                } else {
                                ?>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="inputAddress">Purchase Order Number</label>
                                            <input type="text" class="form-control" name="tfPoNumber" id="tfPoNumber" autocomplete="off" required disabled>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="inputAddress">Purchase Order Document (Only PDF File is allowed)</label>
                                            <input type="file" class="form-control-file" name="tfPoFile" id="tfPoFile" autocomplete="off" disabled>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="inputPassword4">Stock In Date</label>
                                            <input type="date" class="date form-control" name="tfStockInDate" id="tfStockInDate" value="<?php echo date('Y-m-d'); ?>" autocomplete="off" required disabled>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="inputAddress">Quantity</label>
                                            <input type="number" class="form-control" name="tfQuantity" id="tfQuantity" value="<?php if (isset($serialKeyArr)) {
                                                                                                                                    echo sizeof($serialKeyArr);
                                                                                                                                } else {
                                                                                                                                    echo "";
                                                                                                                                } ?>" readonly autocomplete="off" required>
                                            <small>Choose serial key file and load data to display quanity</small>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="exampleFormControlSelect1">Supplier</label>
                                            <select class="form-control" name="cboSupplier" id="cboSupplier" required disabled>
                                                <option value="">No Supplier Selected</option>
                                                <?php
                                                $supplierObj = new Person();
                                                $resultSupplier = $supplierObj->getAllPerson("supplier");
                                                if ($resultSupplier == 0) {
                                                    echo "<option value=''>Query Failed</option>";
                                                } else if ($resultSupplier == "noresult") {
                                                    // echo "<option value=''>No Supplier Found</option>";
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
                                        <div class="form-group col-md-12">
                                            <label for="inputAddress">Remark</label>
                                            <textarea name="tfRemark" id="tfRemark" rows="5" class="form-control" disabled></textarea>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
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
                                        <?php
                                        if ($duplicateStatus == 0) {
                                        ?>
                                            <select class="form-control" name="cboItemCode" id="cboItemCode" required>
                                                <option value="">No Currency Selected</option>
                                                <?php
                                                $hardwareObj = new Hardware();
                                                $resultItemCode = $hardwareObj->getAllHardware();
                                                foreach ($resultItemCode as $itemCode) {
                                                    echo "<option value='";
                                                    echo $itemCode->hardwareId . "||"
                                                        . $itemCode->itemName . "||" . $itemCode->quantity . "||"
                                                        . $itemCode->brandName . "||"
                                                        . $itemCode->unitPrice . " - " . $itemCode->currency . "||"
                                                        . $itemCode->description;
                                                    echo "'>";
                                                    echo $itemCode->itemCode;
                                                    echo "</option>";
                                                }
                                                ?>
                                            </select>
                                        <?php
                                        } else {
                                        ?>
                                            <select class="form-control" name="cboItemCode" id="cboItemCode" required disabled>
                                                <option value="">No Item Code Selected</option>
                                                <?php
                                                $hardwareObj = new Hardware();
                                                $resultItemCode = $hardwareObj->getAllHardware();
                                                foreach ($resultItemCode as $itemCode) {
                                                    echo "<option value='";
                                                    echo $itemCode->hardwareId . "||"
                                                        . $itemCode->itemName . "||" . $itemCode->quantity . "||"
                                                        . $itemCode->brandName . "||"
                                                        . $itemCode->unitPrice . " - " . $itemCode->currency . "||"
                                                        . $itemCode->description;
                                                    echo "'>";
                                                    echo $itemCode->itemCode;
                                                    echo "</option>";
                                                }
                                                ?>
                                            </select>
                                        <?php
                                        }
                                        ?>
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
                            <div class="card-footer">
                                <div class="row justify-content-end mx-1">
                                    <!-- <button type="reset" class="btn btn-secondary" style="margin-right: 10px;">Clear</button> -->
                                    <?php
                                    if (isset($serialKeyArr) && $duplicateStatus == 0) {
                                        echo "<button type='submit' class='btn btn-primary' name='btnCreate' id='btnCreate'>Finish</button>";
                                    } else {
                                        echo "<button type='submit' class='btn btn-primary' name='btnCreate' id='btnCreate' disabled>Finish</button>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <!-- /.card -->
                        </div>
                    </form>
                    <div class="card">
                        <form method="post" enctype="multipart/form-data">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="card-title" style="font-weight: bold;">Input Serial Key File </h3>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body p-4">

                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="serialFileBox">Serial key file input</label>
                                        <input type="file" class="form-control-file" name="serialFile" id="serialFileBox">
                                    </div>
                                </div>

                                <?php
                                if (isset($serialKeyArr)) {
                                ?>
                                    <hr>
                                    <center>
                                        <h4 style="font-weight: bold;">Number of detected serial keys : <span class="text-primary"><?php echo sizeof($serialKeyArr); ?></span></h4>
                                    </center>
                                    <div class="form-row justify-content-center">
                                        <ul class="list-group col-1">
                                            <li class="list-group-item list-group-item-dark"><b>No</b></li>
                                            <?php
                                            $count = 1;
                                            foreach ($serialKeyArr as $serialNo) {
                                                if ($duplicateEvent[$serialNo] > 1) {
                                                    echo "<li class='list-group-item list-group-item-danger'>$count</li>";
                                                } else {
                                                    echo "<li class='list-group-item list-group-item-success'>$count</li>";
                                                }
                                                $count++;
                                            }
                                            ?>
                                        </ul>
                                        <ul class="list-group col-8">
                                            <li class="list-group-item list-group-item-dark"><b>Serial Key</b></li>
                                            <?php
                                            foreach ($serialKeyArr as $serialNo) {
                                                if ($duplicateEvent[$serialNo] > 1) {
                                                    echo "<li class='list-group-item list-group-item-danger'>$serialNo</li>";
                                                } else {
                                                    echo "<li class='list-group-item list-group-item-success'>$serialNo</li>";
                                                }
                                            }
                                            ?>
                                        </ul>
                                        <ul class="list-group col-3">
                                            <li class="list-group-item list-group-item-dark"><b>Status</b></li>
                                            <?php
                                            foreach ($serialKeyArr as $serialNo) {
                                                if ($duplicateEvent[$serialNo] > 1) {
                                                    echo "<li class='list-group-item list-group-item-danger'>Duplicate</li>";
                                                } else {
                                                    echo "<li class='list-group-item list-group-item-success'>Correct</li>";
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="card-footer">
                                <div class="row justify-content-end mx-1">
                                    <?php
                                    if (isset($serialKeyArr)) {
                                        echo "<button type='submit' class='btn btn-secondary' name='btnClearSerial'>Clear Serial</button>";
                                    } else {
                                        echo "<button type='submit' class='btn btn-info' name='btnUploadFile'>Load Data</button>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </form>
                        <!-- /.card-body -->
                        <!-- /.card -->
                    </div>
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
    //   $(".date").datepicker({
    //     format: "yyyy/mm/dd",
    //   });
</script>
<?php
if (isset($serialKeyArr)) {
    echo "<script>document.querySelector('#noSerialKeyCheck').setAttribute('disabled',true)</script>";
}
include_once 'footer.php';
?>