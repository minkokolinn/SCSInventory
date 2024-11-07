<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/PersonOP.php';

if (empty($userObj->person) || $userObj->person!="rw") {
    Util::alert("Access Denied!");
    Util::gogo("home.php");
  }

$pidtoupd="";

if (isset($_REQUEST['pidtoupd']) && isset($_REQUEST['pupdfrom'])) {
    if ($_REQUEST['pupdfrom']=="s") {
        $tolocation="suppliermng.php";
    }else if($_REQUEST['pupdfrom']=="c"){
        $tolocation="customermng.php";
    }else{
        $tolocation="";
    }

    $pidtoupd = $_REQUEST['pidtoupd'];

    $personObj = new Person();
    $personResult = $personObj->getPersonWithID($pidtoupd);
    if ($personResult == false) {
        // do something if query error happened
        Util::alert("Query Error!");
        Util::gogo($tolocation);
    } else if ($personResult == "noresult") {
        // do something if no result found
        Util::alert("There is no result found with this person id!");
        Util::gogo($tolocation);
    } else {

    }

    $phoneArr = explode("||", $personResult->phoneNo);
    $sizeOfPhoneArr = sizeof($phoneArr);
}else{
    Util::alert("Invalid Action!");
    Util::gogo("home.php");
}

if (isset($_POST['btnEdit'])) {
    $contactPerson = $_POST['tfContactPerson'];
    $companyName = $_POST['tfCompanyName'];
    $email = $_POST['tfEmail'];
    $type = $_POST['tfType'];
    $address = $_POST['tfAddress'];
    $phonearr = $_POST['tfPhone'];
    $phonefull = implode("||", $phonearr);

    $personObj2=new Person();
    $personObj2->__construct1($contactPerson,$companyName,$phonefull,$email,$type,$address);
    if ($personObj2->updatePerson($pidtoupd)) {
        Util::alert("Successfully updated");
        Util::gogo($tolocation);
    }else{
        Util::alert("Update Failed");
    }
}

?>

<script type="text/javascript">
    $(document).ready(function() {
        var maxField = 5;
        var addButton = $('.add_button');
        var field_wrapper = $('.rapper');
        var extraTextBox = "<div class='row mt-3'><input type='number' id='inputPassword4' class='form-control col-8 mx-2' name='tfPhone[]' autocomplete='off'><a href='javascript:void(0)' class='remove_button btn btn-danger'> <i class='fas fa-minus'></i> </a></div>";

        var count = <?php echo $sizeOfPhoneArr; ?>;
        addButton.on('click', function() {
            if (count < maxField) {
                count++;
                field_wrapper.append(extraTextBox);
            }
        })

        var removeButton = $('.remove_button');
        field_wrapper.on('click', '.remove_button', function(e) {
            e.preventDefault();
            $(this).parent('div').remove();
            count--;
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
                    <h1><a href="<?php echo $tolocation; ?>" class="pr-3"><i class="fas fa-angle-left"></i></a>Edit Person</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Marketing</a></li>
                        <li class="breadcrumb-item active">Edit Person</li>
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
                                <h3 class="card-title">Person Edit Form</h3>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-4">
                            <form method="post">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Contact Person</label>
                                        <input type="text" class="form-control" name="tfContactPerson" id="inputAddress" value="<?php echo $personResult->contactPerson; ?>" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">Company Name</label>
                                        <input type="text" class="form-control" name="tfCompanyName" id="inputPassword4" value="<?php echo $personResult->companyName; ?>" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Email</label>
                                        <input type="text" class="form-control" name="tfEmail" id="inputAddress" value="<?php echo $personResult->email; ?>" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="exampleFormControlSelect1">Type</label>
                                        <input type="text" class="form-control" name="tfType" id="inputAddress" value="<?php echo $personResult->personType; ?>" autocomplete="off" readonly>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Address</label>
                                        <textarea name="tfAddress" id="" rows="10" class="form-control"><?php echo $personResult->personAddress; ?></textarea>
                                    </div>
                                    <div class="form-group col-md-6 rapper">
                                        <label for="inputPassword4" class="form-text">Phone Number</label>
                                        <?php
                                        $status = 0;
                                        foreach ($phoneArr as $ph) {
                                            $status++;
                                            if ($status == 1) {
                                                echo "<div class='row mb-3'><input type='number' id='inputPassword4' class='form-control col-8 mx-2' name='tfPhone[]' value='$ph' autocomplete='off'>
                                                <a href='javascript:void(0)' class='add_button btn btn-info'> <i class='fas fa-plus'></i> </a></div>";
                                            }else{
                                                echo "<div class='row mb-3'><input type='number' id='inputPassword4' class='form-control col-8 mx-2' name='tfPhone[]' value='$ph' autocomplete='off'>
                                                <a href='javascript:void(0)' class='remove_button btn btn-danger'> <i class='fas fa-minus'></i> </a></div>";
                                            }
                                        }
                                        ?>
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