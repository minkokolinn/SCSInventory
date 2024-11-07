<?php

class HardwareDetail
{
    private $dbConfig;
    private $conn;

    // only information that need to be inserted during purchase comming
    private $serialKey;
    private $hardwareId;
    private $stockInId;
    private $status;


    public function __construct()
    {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();
    }

    public function __construct2(
        $serialKey,$hardwareId,$stockInId,$status
    ) {
        $this->dbConfig = new DBConfig();
        $this->conn=$this->dbConfig->getConnection();

        $this->serialKey=$serialKey;
        $this->hardwareId=$hardwareId;
        $this->stockInId=$stockInId;
        $this->status=$status;
    }

    public function insertHardwareDetail(){
        $insertStmt=$this->conn->prepare(
            "INSERT into hardwareDetail(serialKey,hardwareId,stockInId,status)
            values(?,?,?,?)"
        );
        $insertStmt->bindParam(1,$this->serialKey);
        $insertStmt->bindParam(2,$this->hardwareId);
        $insertStmt->bindParam(3,$this->stockInId);
        $insertStmt->bindParam(4,$this->status);

        if ($insertStmt->execute()) {
            return "success";
        }else{
            if ($insertStmt->errorCode()==23000) {
                return "already";
            }else{
                return "fail";
            }
        }
    }

    public function updateForSaleInvoice($serialArr,$lastStockOutId,$status,$startDate,$duration,$endDate){
        $result="";
        foreach ($serialArr as $serialKeyID) {
            $updateHardwareDetail=$this->conn->prepare(
                "UPDATE hardwareDetail set stockOutId=$lastStockOutId,status='$status',serviceStart='$startDate',
                serviceDuration='$duration',serviceEnd='$endDate' where hardwareDetailId=$serialKeyID"
            );
            if ($updateHardwareDetail->execute()) {
                $result=true;
            }else{
                $result=false;
                break;
            }
        }
        return $result;
    }

    public function getAllHDIDDueToStockOutQTY($hardwareId,$quantity){
        $select1Stmt=$this->conn->prepare("SELECT hardwareDetailId FROM hardwareDetail WHERE hardwareId=$hardwareId AND status='in stock' LIMIT $quantity;");
        $select1Stmt->execute();
        if ($select1Stmt->rowCount()>0) {
            $objArr=$select1Stmt->fetchAll(PDO::FETCH_OBJ);
            $myHDIDarr=array();
            foreach ($objArr as $obj) {
                array_push($myHDIDarr,$obj->hardwareDetailId);
            }
            return $myHDIDarr;
        }else{
            return "noresult";
        }
    }

    public function updateForSaleInvoiceNoSerial($hardwareId,$quantity,$lastStockOutId,$status,$startDate,$duration,$endDate){
        
    }

    public function getSerialKeyWithHardwareID($hid){
        $selectStmt=$this->conn->prepare("SELECT * from hardwareDetail where hardwareId=$hid and status='in stock'");
        $selectStmt->execute();
        if ($selectStmt->rowCount()>0) {
            $serialArr=$selectStmt->fetchAll(PDO::FETCH_OBJ);
        }else{
            $serialArr="noserial";
        }       
        return $serialArr;
    }

    public function updateRemark($hdid,$remark){
        $updateRemarkStmt=$this->conn->prepare("UPDATE hardwareDetail set remark='$remark' where hardwareDetailId=$hdid");
        if ($updateRemarkStmt->execute()) {
            return true;
        }else{
            return false;
        }
    }
    
    public function getHDWithHDID($hdid)
    {
        $selectHd=$this->conn->prepare("SELECT * from hardwareDetail where hardwareDetailId=$hdid");
        $selectHd->execute();
        $oneDataHd=$selectHd->fetch(PDO::FETCH_OBJ);
        return $oneDataHd;
    }
}

?>