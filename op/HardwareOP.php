<?php

class Hardware
{
    private $dbConfig;
    private $conn;

    private $itemCode;
    private $itemName;
    private $quantity;
    private $unitPrice;
    private $currency;
    private $description;
    private $brandId;

    public function __construct()
    {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();
    }

    public function __construct2(
        $itemCode,
        $itemName,
        $quantity,
        $unitPrice,
        $currency,
        $description,
        $brandId
    ) {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();

        $this->itemCode = $itemCode;
        $this->itemName = $itemName;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->currency = $currency;
        $this->description = $description;
        $this->brandId = $brandId;
    }

    public function insertHardware()
    {
        // check existing item code
        $typeditemcode = $this->itemCode;
        $selectWithItemCode = $this->conn->prepare("SELECT * from hardware where itemCode=?");
        $selectWithItemCode->bindParam(1, $typeditemcode);
        $selectWithItemCode->execute();
        if ($selectWithItemCode->rowCount() > 0) {
            return "alreadyexisted";
        } else {
            $insertStmt = $this->conn->prepare("INSERT into hardware(itemCode,itemName,
            quantity,unitPrice,currency,description,brandId) values(?,?,?,?,?,?,?)");
            $insertStmt->bindParam(1,$this->itemCode);
            $insertStmt->bindParam(2,$this->itemName);
            $insertStmt->bindParam(3,$this->quantity);
            $insertStmt->bindParam(4,$this->unitPrice);
            $insertStmt->bindParam(5,$this->currency);
            $insertStmt->bindParam(6,$this->description);
            $insertStmt->bindParam(7,$this->brandId);

            if ($insertStmt->execute()) {
                return "success";
            }else{
                return "fail";
            }
        }
    }

    public function getAllHardware(){
        $selectStmt=$this->conn->prepare("SELECT * from hardware h, brand b where h.brandId=b.brandId");
        if ($selectStmt->execute()) {
            if ($selectStmt->rowCount()>0) {
                return $selectStmt->fetchAll(PDO::FETCH_OBJ);
            }else{
                return "noresult";
            }
        }else{
            return false;
        }
    }

    public function deleteHardware($hid){
        $deleteStmt=$this->conn->prepare("DELETE from hardware where hardwareId=$hid");
        if ($deleteStmt->execute()) {
            return true;
        }else{
            return false;
        }
    }

    public function getHardwareWithID($hid){
        $selectStmt=$this->conn->prepare("SELECT * from hardware where hardwareId=$hid");
        if ($selectStmt->execute()) {
            if ($selectStmt->rowCount()>0) {
                return $selectStmt->fetch(PDO::FETCH_OBJ);
            }else{
                return "noresult";
            }
        }else{
            return false;
        }
    }

    public function updateHardware($hid){
        $updateHardware=$this->conn->prepare("UPDATE hardware set itemCode=?,itemName=?,
        quantity=?,unitPrice=?,currency=?,description=?,brandId=? where hardwareId=$hid");
        $updateHardware->bindParam(1,$this->itemCode);
        $updateHardware->bindParam(2,$this->itemName);
        $updateHardware->bindParam(3,$this->quantity);
        $updateHardware->bindParam(4,$this->unitPrice);
        $updateHardware->bindParam(5,$this->currency);
        $updateHardware->bindParam(6,$this->description);
        $updateHardware->bindParam(7,$this->brandId);

        if ($updateHardware->execute()) {
            return true;
        }else{
            return false;
        }
    }


    public function getAllItemCode()
    {
        $selectStmt=$this->conn->prepare("SELECT itemCode from hardware");
        $selectStmt->execute();
        $dataItemCode=$selectStmt->fetchAll(PDO::FETCH_OBJ);
        return $dataItemCode;
    }

    public function updateHardwareStockQty($hid,$amount,$type){
        //select first existing qty
        $selectExistingQty=$this->conn->prepare("SELECT quantity from hardware where hardwareId=$hid");
        $selectExistingQty->execute();
        $dataHardware=$selectExistingQty->fetch(PDO::FETCH_OBJ);
        $existing_qty=$dataHardware->quantity;
        $updated_qty=0;
        if ($type=="add") {
            $updated_qty=$existing_qty+$amount;
        }else if ($type=="sub") {
            $updated_qty=$existing_qty-$amount;
        }else{
            $updated_qty=$amount;
        }
        //update stock value
        $updateHardware=$this->conn->prepare("UPDATE hardware set quantity=$updated_qty where hardwareId=$hid");
        if ($updateHardware->execute()) {
            return true;   
        }else{
            return false;
        }
    }
}
