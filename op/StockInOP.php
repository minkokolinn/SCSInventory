<?php

class StockIn
{
    private $dbConfig;
    private $conn;

    private $stockInDate;
    private $poNumber;
    private $poFile;
    private $quantity;
    private $remark;
    private $supplierId;


    public function __construct()
    {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();
    }

    public function __construct2(
        $stockInDate,
        $poNumber,
        $poFile,
        $quantity,
        $remark,
        $supplierId
    ) {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();

        $this->stockInDate=$stockInDate;
        $this->poNumber=$poNumber;
        $this->poFile=$poFile;
        $this->quantity=$quantity;
        $this->remark=$remark;
        $this->supplierId=$supplierId;
    }

    public function insertStockIn()
    {
        $insertPurchase = $this->conn->prepare(
            "INSERT into stockin(stockInDate,poNumber,poFile,quantity,remark,supplierId)
            values(?,?,?,?,?,?)"
        );
        $insertPurchase->bindParam(1,$this->stockInDate);
        $insertPurchase->bindParam(2,$this->poNumber);
        $insertPurchase->bindParam(3,$this->poFile);
        $insertPurchase->bindParam(4,$this->quantity);
        $insertPurchase->bindParam(5,$this->remark);
        $insertPurchase->bindParam(6,$this->supplierId);

        if ($insertPurchase->execute()) {
            $last_pur_id = $this->conn->lastInsertId();
            return $last_pur_id;
        } else {
            return "fail";
        }
    }

    public function getAllPurchaseList()
    {
        $selectStmt = $this->conn->prepare(
            "SELECT DISTINCT stockin.stockInId,stockin.stockInDate,person.contactPerson,person.companyName,hardware.itemCode,hardware.itemName,stockin.quantity,stockin.poNumber,stockin.poFile,stockin.remark from stockin,hardwareDetail,hardware,person WHERE stockin.stockInId=hardwareDetail.stockInId and hardwareDetail.hardwareId=hardware.hardwareId and stockin.supplierId=person.personId order by stockin.stockInDate DESC"
        );
        $selectStmt->execute();
        if ($selectStmt->rowCount() > 0) {
            $pArr = $selectStmt->fetchAll();
            return $pArr;
        } else {
            return "noresult";
        }
    }

    public function getAllPurchaseListSerial()
    {

        $selectStmt = $this->conn->prepare(
            "SELECT *,hardwareDetail.remark as myrm from stockin,hardwareDetail,hardware,person WHERE stockin.stockInId=hardwareDetail.stockInId and hardwareDetail.hardwareId=hardware.hardwareId and stockin.supplierId=person.personId and hardwareDetail.serialKey!='NULL' order by stockin.stockInDate DESC"
        );
        $selectStmt->execute();
        if ($selectStmt->rowCount() > 0) {
            $pArr = $selectStmt->fetchAll();
            return $pArr;
        } else {
            return "noresult";
        }
    }

    public function deletePurchaseAndRelated($pid){
        $selectPoPath=$this->conn->prepare("SELECT poFile,quantity from stockin where stockInId=$pid");
        $selectPoPath->execute();
        $path_arr=$selectPoPath->fetch(PDO::FETCH_OBJ);
        $path=$path_arr->poFile;

        $selectCompared=$this->conn->prepare("SELECT * from stockin where poFile='$path'");
        $selectCompared->execute();
        if ($selectCompared->rowCount()<=1) {
            unlink($path);
        }

        $selectTemp=$this->conn->prepare("SELECT hardwareId from hardwareDetail where stockInId=$pid");
        $selectTemp->execute();
        $hid=$selectTemp->fetch();

        $deleteStmt1=$this->conn->prepare("DELETE from hardwareDetail where stockInId=$pid");
        if ($deleteStmt1->execute()) {
            $deleteStmt2=$this->conn->prepare("DELETE from stockin where stockInId=$pid");
            if ($deleteStmt2->execute()) {
                $updateStockObj=new Hardware();
                if ($updateStockObj->updateHardwareStockQty($hid->hardwareId,$path_arr->quantity,"sub")) {
                    return true;
                }else{
                    return false;
                }
                
            }
        }
    }
}
