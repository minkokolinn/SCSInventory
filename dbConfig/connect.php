<?php
class DBConfig{
    private $dbhost;
    private $dbname;
    private $username;
    private $password;
    private $conn;

    public function __construct()
    {
        $this->dbhost="localhost:3306";
        $this->dbname="scs_imsdb";
        $this->username="root";
        $this->password="admin";
    }
    
    public function getConnection(){
        try {
            $this->conn=new PDO("mysql:host=$this->dbhost;dbname=$this->dbname",$this->username,$this->password,[
                PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING,
                PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_OBJ,
            ]);
            return $this->conn;
        } catch (PDOException $e) {
            return $e->getMessage();
        }        
    }
}

?>