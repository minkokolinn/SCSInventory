<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'utility.php';
include_once 'op/SoftwareOP.php';

if (empty($userObj->general)) {
    Util::alert("Access Denied!");
    Util::gogo("home.php");
}

//do confirm to delete
if (isset($_REQUEST['softidtodel'])) {
    $softidtodel = $_REQUEST['softidtodel'];
    echo "<script>
      if(confirm('Are you sure to delete this software?')){
        location='softwarelist.php?softidtodelConfirm=$softidtodel';
      }else{
        location='softwarelist.php';
      }
      </script>";
}
//do action to delete
if (isset($_REQUEST['softidtodelConfirm'])) {
    $softidtodelConfirm = $_REQUEST['softidtodelConfirm'];

    $softwareObj2 = new Software();
    if ($softwareObj2->deleteSoftware($softidtodelConfirm)) {
        Util::alert("Successfully deleted...");
    } else {
        Util::alert("Delete failed!");
    }
}

?>
<style>
    .makeline {
        display: -webkit-box;
        width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Software</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">General</a></li>
                        <li class="breadcrumb-item active">Software List</li>
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
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Software List</h3>
                                <!-- <p>jskfldsjfls</p> -->
                                <div class="card-tools">
                                    <?php if ($userObj->general == "rw") {  ?>
                                        <a href="softwareadd.php" class="btn btn-primary" style="font-weight: bold;">
                                            New Software
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped" data-page-length='10'>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Software Name</th>
                                        <th>Target Organisation</th>
                                        <th>Developed By</th>
                                        <th>Duration</th>
                                        <th>Description</th>
                                        <?php if ($userObj->general=="rw") {  ?>
                                        <th>Action</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $softwareObj = new Software();
                                    $resultSoftware = $softwareObj->getAllSoftware();
                                    if ($resultSoftware == "noresult") {
                                    } else {
                                        $count = 0;
                                        foreach ($resultSoftware as $software) {
                                            $count++;
                                            $softwareId = $software->softwareId;
                                            echo "<tr>";
                                            echo "<td>$count</td>";
                                            echo "<td>" . $software->softwareName . "</td>";
                                            echo "<td>" . $software->targetOrganisation . "</td>";
                                            echo "<td>" . $software->developedBy . "</td>";
                                            echo "<td>" . $software->projectDuration . "</td>";
                                            echo "<td><p class='makeline'>" . $software->description . "</p></td>";
                                            if ($userObj->general=="rw") {
                                            echo "<td><a href='softwarelist.php?softidtodel=$softwareId' class='btn btn-danger' title='Delete'><i class='fas fa-trash'></i></a>
                                            </td>";
                                            }
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>No</th>
                                        <th>Software Name</th>
                                        <th>Target Organisation</th>
                                        <th>Developed By</th>
                                        <th>Duration</th>
                                        <th>Description</th>
                                        <?php if ($userObj->general=="rw") {  ?>
                                        <th>Action</th>
                                        <?php } ?>
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
<!-- /.content-wrapper -->

<script>
    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            // "buttons": ["print","colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>


<?php
include_once 'footer.php';
?>