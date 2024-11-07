<?php
include_once 'header.php';
?>
<script>
    $(document).ready(function(){
        
    });
    function doAction(){
        let valQty=$('#tfQuantity').val();
        for (let index = 0; index < valQty; index++) {
            document.querySelector('#serialMultiSelect').getElementsByTagName('option')[index].selected='selected';
        }
    }
</script>
<div class="content-wrapper">
<section>
    <div class="row" style="height: 400px;">
        <div class="col-6">
            <div class="form-group col-12">
                <label for="inputAddress">Quantity</label>
                <input type="number" class="form-control" name="tfQuantity" id="tfQuantity" value="" autocomplete="off" required>
            </div>
        </div>
        <div class="col-6">
            <div class="form-group col-md-12">
                <select class="form-control form-control-lg" id="serialMultiSelect" name="serialMultiSelect[]" multiple style="height: 300px;" required>
                    <?php
                    for ($i=0; $i <100 ; $i++) { 
                        echo "<option value='$i'>$i</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
</section>
</div>
<?php
include_once 'footer.php';
?>