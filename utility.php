<?php
class Util{

    public static function checkAuth(){
        if (isset($_COOKIE['auth'])) {
            return true;
        }else{
            return false;
        }
    }

    
    public static function getUserWithID($uid){ 
        //return object from database query return
        $dbConfig=new DBConfig();
        $conn=$dbConfig->getConnection();
        $selectUserStmt=$conn->prepare("SELECT * from user where userId=$uid");
        if ($selectUserStmt->execute()) {
            $userObj=$selectUserStmt->fetch();
            return $userObj;
        }else{
            return false;
        }
    }

    public static function alert($message){
        echo "<script>alert('$message')</script>";
    }

    public static function gogo($location){
        echo "<script>location='$location'</script>";
    }

    public static function goback(){
        echo "<script>history.back()</script>";
    }

    public static function doRecoverOfSerialKeyFailureOnPurchase($lastInsertedPuchasedID){
        $dbConfig=new DBConfig();
        $conn=$dbConfig->getConnection();
        //step 1 - clear all inserted hardware detail with this last insert pid
        $deleteHardwareDetail=$conn->prepare("DELETE from hardwareDetail where stockInId=$lastInsertedPuchasedID");
        $deleteHardwareDetail->execute();
        //step 2 - clear this last insert pid
        $selectstockinunlink=$conn->prepare("SELECT poFile from stockin where stockInId=$lastInsertedPuchasedID");
        $selectstockinunlink->execute();
        $file_to_delete_obj=$selectstockinunlink->fetch(PDO::FETCH_OBJ);
        unlink($file_to_delete_obj->poFile);

        $deleteLastInsertedPurchase=$conn->prepare("DELETE from stockin where stockInId=$lastInsertedPuchasedID");
        $deleteLastInsertedPurchase->execute();
    }

    public static function stockBalanceQuery(){
        $dbConfig=new DBConfig();
        $conn=$dbConfig->getConnection();

        $selectStmt=$conn->prepare("SELECT * FROM hardware,hardwareDetail,brand WHERE hardware.hardwareId=hardwareDetail.hardwareId AND hardware.brandId=brand.brandId AND hardwareDetail.serialKey!='NULL' order by hardware.itemName asc");
        if ($selectStmt->execute()) {
            if ($selectStmt->rowCount()>0) {
                return $selectStmt->fetchAll(PDO::FETCH_OBJ);   
            }else{
                return "noresult";
            }
        }else{
            return "fail";
        }
    }

    public static function getStockOutInfoByHID($hardwareDetailID){
        $dbConfig=new DBConfig();
        $conn=$dbConfig->getConnection();

        $selectStmt=$conn->prepare("SELECT * FROM hardwareDetail,stockout,person WHERE hardwareDetail.stockOutId=stockout.stockOutId AND stockout.customerId=person.personId AND hardwareDetail.hardwareDetailId=$hardwareDetailID");
        if ($selectStmt->execute()) {
            if ($selectStmt->rowCount()>0) {
                return $selectStmt->fetch(PDO::FETCH_OBJ);
            }else{
                return "no result";
            }
        }else{
            return "fail";
        }
    }

    public static function stockReport($from,$to){
        $dbConfig=new DBConfig();
        $conn=$dbConfig->getConnection();

        $selectHardware=$conn->prepare("SELECT * from hardware,brand WHERE hardware.brandId=brand.brandId");
        $selectHardware->execute();
        $hardwareArr=$selectHardware->fetchAll(PDO::FETCH_OBJ);

        
        $openingInventoryArr=array();
        $stockInArr=array();
        $stockOutArr=array();
        foreach ($hardwareArr as $hardware) {
            $hardwareId=$hardware->hardwareId;

            // For each hardware

            // Getting Opening Inventory (sum of all before stock in - sum of all before stock out)
            $openingInventory=0;
            $sumofbeforestockin=0;
            $sumofbeforestockout=0;
            
            $selectStockInBeforeSum=$conn->prepare("SELECT DISTINCT hardware.itemName,stockin.quantity,stockin.stockInDate FROM hardware,hardwareDetail,stockin WHERE hardware.hardwareId=hardwareDetail.hardwareId AND hardwareDetail.stockInId=stockin.stockInId AND hardware.hardwareId=$hardwareId AND stockin.stockInDate<'$from'");
            $selectStockInBeforeSum->execute();
            $selectStockInBeforeSumArr=$selectStockInBeforeSum->fetchAll(PDO::FETCH_OBJ);
            foreach ($selectStockInBeforeSumArr as $stockInSumBefore) {
                $sumofbeforestockin=$sumofbeforestockin+$stockInSumBefore->quantity;
            }

            $selectStockOutBeforeSum=$conn->prepare("SELECT DISTINCT hardware.itemName,stockout.quantity,stockout.stockOutDate FROM hardware,hardwareDetail,stockout WHERE hardware.hardwareId=hardwareDetail.hardwareId AND hardwareDetail.stockOutId=stockout.stockOutId AND hardware.hardwareId=$hardwareId AND stockout.stockOutDate<'$from'");
            $selectStockOutBeforeSum->execute();
            $selectStockOutBeforeSumArr=$selectStockOutBeforeSum->fetchAll(PDO::FETCH_OBJ);
            foreach ($selectStockOutBeforeSumArr as $stockOutSumBefore) {
                $sumofbeforestockout=$sumofbeforestockout+$stockOutSumBefore->quantity;
            }

            $openingInventory=$sumofbeforestockin-$sumofbeforestockout;
            array_push($openingInventoryArr,$openingInventory);


            // Getting Between Stock In
            $sumofallBetweenStockIn=0;
            $selectBetweenStockIn=$conn->prepare("SELECT DISTINCT hardware.itemName,stockin.quantity,stockin.stockInDate FROM hardware,hardwareDetail,stockin WHERE hardware.hardwareId=hardwareDetail.hardwareId AND hardwareDetail.stockInId=stockin.stockInId AND hardware.hardwareId=$hardwareId AND stockin.stockInDate BETWEEN '$from' AND '$to'");
            $selectBetweenStockIn->execute();
            $selectBetweenStockInArr=$selectBetweenStockIn->fetchAll(PDO::FETCH_OBJ);
            foreach ($selectBetweenStockInArr as $betweenStockIn) {
                $sumofallBetweenStockIn=$sumofallBetweenStockIn+$betweenStockIn->quantity;
            }
            array_push($stockInArr,$sumofallBetweenStockIn);


            //Getting Between Stock Out
            $sumofallBetweenStockOut=0;
            $selectBetweenStockOut=$conn->prepare("SELECT DISTINCT hardware.itemName,stockout.quantity,stockout.stockOutDate FROM hardware,hardwareDetail,stockout WHERE hardware.hardwareId=hardwareDetail.hardwareId AND hardwareDetail.stockOutId=stockout.stockOutId AND hardware.hardwareId=$hardwareId AND stockout.stockOutDate BETWEEN '$from' AND '$to'");
            $selectBetweenStockOut->execute();
            $selectBetweenStockOutArr=$selectBetweenStockOut->fetchAll(PDO::FETCH_OBJ);
            foreach ($selectBetweenStockOutArr as $betweenStockOut) {
                $sumofallBetweenStockOut=$sumofallBetweenStockOut+$betweenStockOut->quantity;
            }
            array_push($stockOutArr,$sumofallBetweenStockOut);


        }


        return [$hardwareArr,$openingInventoryArr,$stockInArr,$stockOutArr];

    }
}
?>