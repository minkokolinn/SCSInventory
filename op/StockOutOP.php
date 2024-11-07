<?php

class StockOut
{
    private $dbConfig;
    private $conn;

    private $stockOutDate;
    private $doNumber;
    private $doFile;
    private $quantity;
    private $remark;
    private $customerId;

    public function __construct()
    {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();
    }

    public function __construct2(
        $stockOutDate,
        $doNumber,
        $doFile,
        $quantity,
        $remark,
        $customerId
    ) {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();

        $this->stockOutDate = $stockOutDate;
        $this->doNumber = $doNumber;
        $this->doFile = $doFile;
        $this->quantity = $quantity;
        $this->remark = $remark;
        $this->customerId = $customerId;
    }

    public function insertStockOut()
    {
        $insertSale = $this->conn->prepare(
            "INSERT into stockout(stockOutDate,doNumber,doFile,quantity,remark,customerId) 
            values(?,?,?,?,?,?)"
        );
        $insertSale->bindParam(1, $this->stockOutDate);
        $insertSale->bindParam(2, $this->doNumber);
        $insertSale->bindParam(3, $this->doFile);
        $insertSale->bindParam(4, $this->quantity);
        $insertSale->bindParam(5, $this->remark);
        $insertSale->bindParam(6, $this->customerId);

        if ($insertSale->execute()) {
            $last_sale_id = $this->conn->lastInsertId();
            return $last_sale_id;
        } else {
            return "fail";
        }
    }

    public function getAllSaleList()
    {
        $selectStmt = $this->conn->prepare(
            "SELECT DISTINCT stockout.stockOutId,stockout.stockOutDate,person.contactPerson,person.companyName,hardware.itemCode,hardware.itemName,stockout.quantity,stockout.doNumber,stockout.doFile,stockout.remark from stockout,hardwareDetail,hardware,person WHERE stockout.stockOutId=hardwareDetail.stockOutId and hardwareDetail.hardwareId=hardware.hardwareId and stockout.customerId=person.personId order by stockout.stockOutDate desc"
        );
        $selectStmt->execute();
        if ($selectStmt->rowCount() > 0) {
            return $selectStmt->fetchAll();
        } else {
            return "noresult";
        }
    }

    public function getAllSaleListSerial()
    {
        $selectStmt = $this->conn->prepare(
            "SELECT *,hardwareDetail.remark as myrm from stockout,hardwareDetail,hardware,person WHERE stockout.stockOutId=hardwareDetail.stockOutId and hardwareDetail.hardwareId=hardware.hardwareId and stockout.customerId=person.personId and hardwareDetail.serialKey!='NULLee' order by stockout.stockOutDate desc"
        );
        $selectStmt->execute();
        if ($selectStmt->rowCount() > 0) {
            return $selectStmt->fetchAll();
        } else {
            return "noresult";
        }
    }

    public function deleteSaleAndRelated($sid)
    {
        $selectPoPath = $this->conn->prepare("SELECT doFile,quantity from stockout where stockOutId=$sid");
        $selectPoPath->execute();
        $path_arr = $selectPoPath->fetch(PDO::FETCH_OBJ);
        $path = $path_arr->doFile;

        $selectCompared = $this->conn->prepare("SELECT * from stockout where doFile='$path'");
        $selectCompared->execute();
        if ($selectCompared->rowCount() <= 1) {
            unlink($path);
        }

        // $selectTemp=$this->conn->prepare("SELECT hardwareId from hardwareDetail where stockOutId=$sid");
        // $selectTemp->execute();
        // $hid=$selectTemp->fetch();

        $deleteStmt1 = $this->conn->prepare("DELETE from hardwareDetail where stockOutId=$sid");
        if ($deleteStmt1->execute()) {
            $deleteStmt2 = $this->conn->prepare("DELETE from stockout where stockOutId=$sid");
            if ($deleteStmt2->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }
}
