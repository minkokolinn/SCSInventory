<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/PersonOP.php';

if (empty($userObj->person) || $userObj->person!="rw") {
    Util::alert("Access Denied!");
    Util::gogo("home.php");
  }

if (isset($_POST['btnCreate'])) {
    $contactPerson=$_POST['tfContactPerson'];
    $companyName=$_POST['tfCompanyName'];
    $email=$_POST['tfEmail'];
    $type=$_POST['tfType'];
    $address=$_POST['tfAddress'];
    $phonearr=$_POST['tfPhone'];
    $phonefull=implode("||",$phonearr);

    $personObj=new Person();
    $personObj->__construct1($contactPerson,$companyName,$phonefull,$email,$type,$address);
    if ($personObj->insertPerson()) {
        Util::alert("Registered Successfully");
    }else{
        Util::alert("Insert Query Failed");
    }
}

?>

<script type="text/javascript">
    $(document).ready(function(){
        var maxField = 4;
        var addButton=$('.add_button');
        var field_wrapper=$('.rapper');
        var extraTextBox="<div class='row mt-3'><input type='number' id='inputPassword4' class='form-control col-8 mx-2' name='tfPhone[]' autocomplete='off'><a href='javascript:void(0)' class='remove_button btn btn-danger'> <i class='fas fa-minus'></i> </a></div>";

        var count=0;
        addButton.on('click',function(){
            if (count<maxField) {
                count++;
                field_wrapper.append(extraTextBox);
            }
        })

        var removeButton=$('.remove_button');
        field_wrapper.on('click','.remove_button',function(e){
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
                    <h1>Add New Person (Supplier/Customer)</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">Marketing</a></li>
                        <li class="breadcrumb-item active">Add Person</li>
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
                                <h3 class="card-title">Person Form</h3>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-4">
                            <form method="post">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Contact Person</label>
                                        <input type="text" class="form-control" name="tfContactPerson" id="inputAddress" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">Company Name</label>
                                        <input type="text" class="form-control" name="tfCompanyName" id="inputPassword4" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Email</label>
                                        <input type="text" class="form-control" name="tfEmail" id="inputAddress" autocomplete="off">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="exampleFormControlSelect1">Type</label>
                                        <select class="form-control" name="tfType" id="exampleFormControlSelect1" required>
                                            <option value="">None</option>
                                            <option value="supplier">Supplier</option>
                                            <option value="customer">Customer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Address</label>
                                        <textarea name="tfAddress" id="" rows="10" class="form-control"></textarea>
                                    </div>
                                    <div class="form-group col-md-6 rapper">
                                        <label for="inputPassword4" class="form-text">Phone Number</label>
                                        <div class="row">
                                            <input type="number" id="inputPassword4" class="form-control col-8 mx-2" name="tfPhone[]" autocomplete="off">
                                            <a href="javascript:void(0)" class="add_button btn btn-info"> <i class="fas fa-plus"></i> </a>
                                        </div>
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