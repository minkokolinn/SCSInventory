<?php

class Software
{
    private $dbConfig;
    private $conn;

    private $softwareName;
    private $targetOrganisation;
    private $developedBy;
    private $projectDuration;
    private $description;

    public function __construct()
    {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();
    }

    public function __construct2(
        $softwareName,
        $targetOrganisation,
        $developedBy,
        $projectDuration,
        $description
    ) {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();

        $this->softwareName=$softwareName;
        $this->targetOrganisation=$targetOrganisation;
        $this->developedBy=$developedBy;
        $this->projectDuration=$projectDuration;
        $this->description=$description;
    }

    public function insertSoftware()
    {
        $insertStmt=$this->conn->prepare(
            "INSERT into software(softwareName,targetOrganisation,
            developedBy,projectDuration,description) values(?,?,?,?,?)");
        $insertStmt->bindParam(1,$this->softwareName);
        $insertStmt->bindParam(2,$this->targetOrganisation);
        $insertStmt->bindParam(3,$this->developedBy);
        $insertStmt->bindParam(4,$this->projectDuration);
        $insertStmt->bindParam(5,$this->description);
        if ($insertStmt->execute()) {
            return true;
        }else{
            return false;
        }
    }

    public function deleteSoftware($softId){
        $deleteStmt=$this->conn->prepare("DELETE from software where softwareId=$softId");
        if ($deleteStmt->execute()) {
            return true;
        }else{
            return false;
        }
    }

    public function getAllSoftware(){
        $selectStmt=$this->conn->prepare("SELECT * from software order by softwareName asc");
        $selectStmt->execute();
        if ($selectStmt->rowCount()>0) {
            return $selectStmt->fetchAll();
        }else{
            return "noresult";
        }
    }

    public function getSoftwareWithID($softId){
        $selectOneStmt=$this->conn->prepare("SELECT * from software where softwareId=$softId");
        if ($selectOneStmt->execute()) {
            if ($selectOneStmt->rowCount()>0) {
                return $selectOneStmt->fetch(PDO::FETCH_OBJ);
            }else{
                return "noresult";
            }
        }else{
            return "fail";
        }
    }
}
