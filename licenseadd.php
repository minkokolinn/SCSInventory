<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/HardwareOP.php';
include_once 'op/SoftwareOP.php';

if (isset($_REQUEST['hidtolic']) || isset($_REQUEST['softidtolic'])) {

} else {
    Util::alert("Invalid Action!");
    Util::goback();
}

if (isset($_POST['btnCreate'])) {
    
}

?>
<script>
    $(document).ready(function() {
        $('#tfSD').attr("readonly", "");
        $('#cboLicenseType').on('change', function() {
            var selectLTValue = $(this).find(":selected").val();
            if (selectLTValue == "") {
                $('#tfSD').attr("readonly", "");
            } else {
                $('#tfSD').removeAttr("readonly");

                doAutoFormatting($('#cboLicenseType').find(":selected").val());

            }


        });
        $('#tfSD').on('change', function() {
            doAutoFormatting($('#cboLicenseType').find(":selected").val());

        })
    });

    function doAutoFormatting(chosenLT) {
        var startDate = new Date($('#tfSD').val());

        switch (chosenLT) {
            case "":
                $('#tfSD').val("");
                $('#tfSD').attr("readonly", "");
                $('#tfED').val("");
                $('#tfED').attr("readonly", "");
                $('#tfValid').val("");
                $('#tfValid').attr("readonly", "");
                break;
            case "annually":
                var recupDate = new Date(startDate);
                var plusTwoYears = new Date(recupDate.setFullYear(recupDate.getFullYear() + 1));
                document.getElementById('tfED').valueAsDate = plusTwoYears;
                break;
            case "monthly":
                var recupDate = new Date(startDate);
                var plusTwoYears = new Date(recupDate.setMonth(recupDate.getMonth() + 1));
                document.getElementById('tfED').valueAsDate = plusTwoYears;
                break;
            case "2year":
                var recupDate = new Date(startDate);
                var plusTwoYears = new Date(recupDate.setFullYear(recupDate.getFullYear() + 2));
                document.getElementById('tfED').valueAsDate = plusTwoYears;
                break;
            case "3year":
                var recupDate = new Date(startDate);
                var plusTwoYears = new Date(recupDate.setFullYear(recupDate.getFullYear() + 3));
                document.getElementById('tfED').valueAsDate = plusTwoYears;
                break;
            case "permanent":
                $('#tfSD').val("");
                $('#tfSD').attr("readonly", "");
                $('#tfED').val("");
                $('#tfED').attr("readonly", "");
                $('#tfValid').val("");
                $('#tfValid').attr("readonly", "");
                break;
            default:
                $('#tfSD').val("");
                $('#tfSD').attr("readonly", "");
                $('#tfED').val("");
                $('#tfED').attr("readonly", "");
                $('#tfValid').val("");
                $('#tfValid').attr("readonly", "");
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
                    <h1>
                        <?php
                        if (isset($_REQUEST['hidtolic'])) {
                            echo "<a href='hardwarelist.php' class='pr-3'><i class='fas fa-angle-left'></i></a>";
                        }
                        if (isset($_REQUEST['softidtolic'])) {
                            echo "<a href='softwarelist.php' class='pr-3'><i class='fas fa-angle-left'></i></a>";
                        }
                        ?>
                        Add License
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">General</a></li>
                        <li class="breadcrumb-item active">Add License</li>
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
                                <?php
                                if (isset($_REQUEST['hidtolic'])) {
                                    $select_hid = $_REQUEST['hidtolic'];
                                    $hardwareObj = new Hardware();
                                    $hardwareInfo = $hardwareObj->getHardwareWithID($select_hid);

                                    echo "<h3 class='card-title'><b><u>For Hardware</u></b></h3>";
                                    echo "<h3 class='card-title'><b><u>" . $hardwareInfo->itemCode . " - " . $hardwareInfo->itemName . "</u></b></h3>";
                                }
                                if (isset($_REQUEST['softidtolic'])) {
                                    $select_softid = $_REQUEST['softidtolic'];
                                    $softwareObj = new Software();
                                    $softwareInfo = $softwareObj->getSoftwareWithID($select_softid);

                                    echo "<h3 class='card-title'><b><u>For Software</u></b></h3>";
                                    echo "<h3 class='card-title'><b><u>" . $softwareInfo->softwareName . "</u></b></h3>";
                                }
                                ?>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-4">
                            <form method="post">
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputAddress">License Key</label>
                                        <input type="text" class="form-control" name="tfLicenseKey" id="tfLicenseKey" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Start Date</label>
                                        <input type="date" class="form-control" name="tfSD" id="tfSD" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">End Date</label>
                                        <input type="date" class="form-control" name="tfED" id="tfED" autocomplete="off" readonly required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-5">
                                        <label for="inputAddress">Valid Duration</label>
                                        <input type="text" class="form-control" name="tfValid" id="tfValid" autocomplete="off" readonly required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="inputAddress">Cost</label>
                                        <input type="number" class="form-control" name="tfCost" id="tfCost" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="inputAddress">Currency</label>
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
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="inputAddress">License Type</label>
                                        <select name="cboLicenseType" id="cboLicenseType" class="form-control" required>
                                            <option value="" selected disabled>No License Type Selected</option>
                                            <option value="annually">Annually</option>
                                            <option value="monthly">Monthly</option>
                                            <option value="2year">2 Year</option>
                                            <option value="3year">3 Year</option>
                                            <option value="permanent">Permanent</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <label for="inputAddress">Description</label>
                                        <textarea name="tfDescription" id="tfDescription" rows="10" class="form-control"></textarea>
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