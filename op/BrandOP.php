<?php

class Brand{
    private $dbConfig;
    private $conn;

    private $brandName;
    private $brandNote;

    public function __construct()
    {
        $this->dbConfig=new DBConfig();
        $this->conn=$this->dbConfig->getConnection();
    }

    public function __construct1($brandName,$brandNote)
    {
        $this->dbConfig=new DBConfig();
        $this->conn=$this->dbConfig->getConnection();

        $this->brandName=$brandName;
        $this->brandNote=$brandNote;
    }

    public function insertBrand(){
        $insertStmt=$this->conn->prepare("INSERT into brand(brandName,note)
                                values(?,?)");
        $insertStmt->bindParam(1,$this->brandName);
        $insertStmt->bindParam(2,$this->brandNote);

        if ($insertStmt->execute()) {
            return true;
        }else{
            return false;
        }
    }

    public function getAllBrand(){
        $selectStmt=$this->conn->prepare("SELECT * from brand order by brandName asc");
        if ($selectStmt->execute()) {
            if ($selectStmt->rowCount()>0) {
                $dataBrand=$selectStmt->fetchAll();
                return $dataBrand;
            }else{
                return "noresult";
            }
        }else{
            return false;   // false means that error occur in query
        }
    }

    
    public function getBrandWithID($bid){   // return relevant brand information with desired id
        $selectOneStmt=$this->conn->prepare("SELECT * from brand where brandId=$bid");
        if ($selectOneStmt->execute()) {
            if ($selectOneStmt->rowCount()<=0) {
                return "noresult";
            }else{
                $dataOneBrand=$selectOneStmt->fetch();
                return $dataOneBrand;
            }
        }else{
            return false;
        }
    }


    public function deleteBrand($bid){
        $deleteStmt=$this->conn->prepare("DELETE from brand where brandId=$bid");
        if ($deleteStmt->execute()) {
            return true;
        }else{
            return false;
        }
    }

    public function updateBrand($bid){
        $updateStmt=$this->conn->prepare("UPDATE brand set brandName=:bname,note=:bnote where brandId=$bid");
        $updateStmt->bindParam("bname",$this->brandName);
        $updateStmt->bindParam("bnote",$this->brandNote);

        if ($updateStmt->execute()) {
            return true;
        }else{
            return false;
        }
    }
}

?>