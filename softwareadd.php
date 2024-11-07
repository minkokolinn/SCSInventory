<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/SoftwareOP.php';

if (empty($userObj->general) || $userObj->general!="rw") {
    Util::alert("Access Denied!");
    Util::gogo("home.php");
  }

if (isset($_POST['btnCreate'])) {
    $softwareObj=new Software();
    $softwareObj->__construct2($_POST['tfName'],$_POST['tfOrganisation'],
    $_POST['tfDevelopedBy'],$_POST['tfDuration'],$_POST['tfDescription']);
    if ($softwareObj->insertSoftware()) {
        Util::alert("Successfully inserted...");
    }else{
        Util::alert("Insert failed");
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
                    <h1><a href="softwarelist.php" class="pr-3"><i class="fas fa-angle-left"></i></a>Add New Software</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">General</a></li>
                        <li class="breadcrumb-item active">Add Software</li>
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
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Software Name</label>
                                        <input type="text" class="form-control" name="tfName" id="tfName" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">Target Organisation</label>
                                        <input type="text" class="form-control" name="tfOrganisation" id="tfOrganisation" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="inputAddress">Developed by</label>
                                        <input type="text" class="form-control" name="tfDevelopedBy" id="tfDevelopedBy" autocomplete="off" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="inputPassword4">Project Duration</label>
                                        <input type="text" class="form-control" name="tfDuration" id="tfDuration" autocomplete="off" required>
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