<?php
include_once 'header.php';
include_once 'dbConfig/connect.php';
include_once 'op/PersonOP.php';
include_once 'utility.php';

if (empty($userObj->person)) {
  Util::alert("Access Denied!");
  Util::gogo("home.php");
}

// MAKE CONFIRM FOR DELETE
if (isset($_REQUEST['pidtodel'])) {
    $pidtodel=$_REQUEST['pidtodel'];
    echo "<script>
    if(confirm('Are you sure to delete?')){
      location='suppliermng.php?pidtodelConfirm=$pidtodel';
    }else{
      location='suppliermng.php';
    }
    </script>";
}
//DO ACTION TO DELETE
if (isset($_REQUEST['pidtodelConfirm'])) {
    $pidtodelConfirm=$_REQUEST['pidtodelConfirm'];

    $personObj=new Person();
    if ($personObj->deletePerson($pidtodelConfirm)) {
        Util::alert("Deleted");
        Util::gogo("suppliermng.php");
    }else{
        Util::alert("Failed to delete");
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
</script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Supplier</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="">Marketing</a></li>
            <li class="breadcrumb-item active">Supplier</li>
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
                <h3 class="card-title">Supplier List</h3>

                <div class="card-tools">
                  <div class="input-group input-group-sm" style="width: 150px;">
                    <input type="text" name="table_search" id="mySearch" class="form-control float-right" placeholder="Search">

                    <div class="input-group-append">
                      <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Contact Person</th>
                      <th>Company</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <?php if ($userObj->person=="rw") {  ?>
                      <th>Action</th>
                      <?php } ?>
                    </tr>
                  </thead>
                  <tbody id="myTable">
                    <?php
                    $supplierObj=new Person();
                    $result=$supplierObj->getAllPerson("supplier");
                    if ($result=="noresult") {
                        echo "<tr><td colspan='6' style='text-align:center;'>No Result Found</td></tr>";
                    }else if($result==false){
                        echo "<tr><td colspan='6' style='text-align:center;'>Query Error</td></tr>";
                    }else{
                        $no=0;
                        foreach ($result as $person) {
                            $no++;
                            $personId=$person->personId;
                            $phonearr=explode("||",$person->phoneNo);
                            echo "
                            <tr>
                                <td>$no</td>
                                <td>".$person->contactPerson."</td>
                                <td>".$person->companyName."</td>
                                <td>".$person->email."</td>
                                <td>";
                            if ($phonearr!="") {
                              echo $phonearr[0];
                            }
                            if ($userObj->person=="rw") {
                            echo "</td>
                                <td><a href='suppliermng.php?pidtodel=$personId' class='btn btn-danger' title='Delete'><i class='fas fa-trash'></i></a>
                                <a href='personupdate.php?pidtoupd=$personId&&pupdfrom=s' class='btn btn-secondary' title='Edit'><i class='fas fa-pen'></i></a></td>
                            </tr>
                            ";
                            }
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
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<?php
include_once 'footer.php';
?>