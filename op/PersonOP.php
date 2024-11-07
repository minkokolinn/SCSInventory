<?php

class Person
{
    private $dbConfig;
    private $conn;

    private $contactPerson;
    private $companyName;
    private $phoneNo;
    private $email;
    private $personType;
    private $address;

    public function __construct()
    {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();
    }

    public function __construct1($contactPerson, $companyName, $phoneNo, $email, $personType, $address)
    {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();

        $this->contactPerson = $contactPerson;
        $this->companyName = $companyName;
        $this->phoneNo = $phoneNo;
        $this->email = $email;
        $this->personType = $personType;
        $this->address = $address;
    }

    public function insertPerson()
    {
        $insertStmt = $this->conn->prepare("INSERT into person(contactPerson,companyName,
                        phoneNo,email,personType,personAddress)
                        values(?,?,?,?,?,?)");
        $insertStmt->bindParam(1, $this->contactPerson);
        $insertStmt->bindParam(2, $this->companyName);
        $insertStmt->bindParam(3, $this->phoneNo);
        $insertStmt->bindParam(4, $this->email);
        $insertStmt->bindParam(5, $this->personType);
        $insertStmt->bindParam(6, $this->address);

        if ($insertStmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllPerson($persontype)
    {
        $selectStmt = "";
        if ($persontype == "supplier") {
            $selectStmt = $this->conn->prepare("SELECT * from person where personType='supplier' order by contactPerson asc");
        } else {
            $selectStmt = $this->conn->prepare("SELECT * from person where personType='customer' order by contactPerson asc");
        }

        if ($selectStmt->execute()) {
            if ($selectStmt->rowCount() > 0) {
                $dataPerson = $selectStmt->fetchAll();
                return $dataPerson;
            } else {
                return "noresult";
            }
        } else {
            return false;   // false means that error occur in query
        }
    }

    public function deletePerson($pid)
    {
        $deleteStmt = $this->conn->prepare("DELETE from person where personId=$pid");
        if ($deleteStmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function updatePerson($pid)
    {
        $updateStmt = $this->conn->prepare("UPDATE person set contactPerson=?,companyName=?,
                    phoneNo=?,email=?,personType=?,personAddress=? where personId=$pid");
        $updateStmt->bindParam(1, $this->contactPerson);
        $updateStmt->bindParam(2, $this->companyName);
        $updateStmt->bindParam(3, $this->phoneNo);
        $updateStmt->bindParam(4, $this->email);
        $updateStmt->bindParam(5, $this->personType);
        $updateStmt->bindParam(6, $this->address);

        if ($updateStmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getPersonWithID($pid)
    {
        $selectOneStmt = $this->conn->prepare("SELECT * from person where personId=$pid");
        if ($selectOneStmt->execute()) {
            if ($selectOneStmt->rowCount() == 1) {
                $dataPerson = $selectOneStmt->fetch(PDO::FETCH_OBJ);
                return $dataPerson;
            } else {
                return "noresult";
            }
            
        } else {
            return false;
        }
    }
}
