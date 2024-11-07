<?php
class UserOP
{
    private $dbConfig;
    private $conn;

    private $userName;
    private $userEmail;
    private $userPassword;
    private $userPhone;
    private $userProfile;
    private $active;
    private $administration;
    private $general;
    private $person;
    private $stockin;
    private $stockout;
    private $stockbalance;


    public function __construct()
    {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();
    }

    public function __construct1($userName, $userEmail, $userPassword, $userPhone, $userProfile, $active, $administration, $general, $person, $stockin, $stockout, $stockbalance)
    {
        $this->dbConfig = new DBConfig();
        $this->conn = $this->dbConfig->getConnection();

        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userPassword = $userPassword;
        $this->userPhone = $userPhone;
        $this->userProfile = $userProfile;
        $this->active = $active;
        $this->administration = $administration;
        $this->general = $general;
        $this->person=$person;
        $this->stockin=$stockin;
        $this->stockout=$stockout;
        $this->stockbalance=$stockbalance;
    }

    public function authenticate($email, $pass)
    {
        $authenticateStmt = $this->conn->prepare("SELECT * FROM user where userEmail='$email'");
        $authenticateStmt->execute();
        if ($authenticateStmt->rowCount() == 1) {
            $dataofUser = $authenticateStmt->fetch();
            if (password_verify($pass, $dataofUser->userPassword)) {
                if ($dataofUser->active == TRUE) {
                    echo "<script>alert('Login Successful')</script>";
                    setcookie("auth", $dataofUser->userId, time() + 86400);
                    return true;
                }else{
                    echo "<script>alert('Account had been stopped! Contact admin to regain access.')</script>";
                    return false;
                }
            } else {
                echo "<script>alert('Wrong Password! Login Failed!')</script>";
                return false;
            }
        } else {
            echo "<script>alert('Wrong Email! Login Failed')</script>";
            return false;
        }
    }

    public function getAllUser()
    {
        $selectUserStmt = $this->conn->prepare("SELECT * from user");
        if ($selectUserStmt->execute()) {
            if ($selectUserStmt->rowCount() > 0) {
                $arrUserObj = $selectUserStmt->fetchAll();
                return $arrUserObj;
            } else {
                return "noresult";
            }
        } else {
            return false;
        }
    }

    public function insertUser()
    {
        $insertStmt = $this->conn->prepare("INSERT into user(userName,userEmail,userPassword,userPhone,
                    userProfile,active,administration,general,person,stockin,stockout,stockbalance)
                    values (?,?,?,?,?,?,?,?,?,?,?,?)");
        $insertStmt->bindParam(1, $this->userName);
        $insertStmt->bindParam(2, $this->userEmail);
        $insertStmt->bindParam(3, $this->userPassword);
        $insertStmt->bindParam(4, $this->userPhone);
        $insertStmt->bindParam(5, $this->userProfile);
        $insertStmt->bindParam(6, $this->active, PDO::PARAM_BOOL);
        $insertStmt->bindParam(7, $this->administration);
        $insertStmt->bindParam(8, $this->general);
        $insertStmt->bindParam(9, $this->person);
        $insertStmt->bindParam(10, $this->stockin);
        $insertStmt->bindParam(11, $this->stockout);
        $insertStmt->bindParam(12, $this->stockbalance);
        if ($insertStmt->execute()) {
            return "success";
        } else {
            if ($insertStmt->errorCode() == 23000) {
                return "already";
            } else {
                return "fail";
            }
        }
    }


    public function updateUserWithID($uid)
    {
        $updateStmt = "";
        if ($this->userPassword == "") {
            $updateStmt = $this->conn->prepare("UPDATE user set 
            userName=?,userEmail=?,userPhone=?,administration=?,general=?,
            person=?,stockin=?,stockout=?,stockbalance=? where userId=$uid");
            $updateStmt->bindParam(1, $this->userName);
            $updateStmt->bindParam(2, $this->userEmail);
            $updateStmt->bindParam(3, $this->userPhone);
            $updateStmt->bindParam(4, $this->administration);
            $updateStmt->bindParam(5, $this->general);
            $updateStmt->bindParam(6, $this->person);
            $updateStmt->bindParam(7, $this->stockin);
            $updateStmt->bindParam(8, $this->stockout);
            $updateStmt->bindParam(9, $this->stockbalance);
        } else {
            $updateStmt = $this->conn->prepare("UPDATE user set 
            userName=?,userEmail=?,userPassword=?,userPhone=?,administration=?,
            general=?,person=?,stockin=?,stockout=?,stockbalance=? where userId=$uid");
            $updateStmt->bindParam(1, $this->userName);
            $updateStmt->bindParam(2, $this->userEmail);
            $updateStmt->bindParam(3, $this->userPassword);
            $updateStmt->bindParam(4, $this->userPhone);
            $updateStmt->bindParam(5, $this->administration);
            $updateStmt->bindParam(6, $this->general);
            $updateStmt->bindParam(7, $this->person);
            $updateStmt->bindParam(8, $this->stockin);
            $updateStmt->bindParam(9, $this->stockout);
            $updateStmt->bindParam(10, $this->stockbalance);
        }

        //run the update statement
        if ($updateStmt->execute()) {
            return "success";
        } else {
            if ($updateStmt->errorCode() == 23000) {
                return "already";
            } else {
                return "fail";
            }
        }
    }

    public function makeActive($uid)
    {
        $updateActiveStmt = $this->conn->prepare("UPDATE user set active=? where userId=$uid");
        $updateActiveStmt->bindValue(1, TRUE, PDO::PARAM_BOOL);
        $updateActiveStmt->execute();
    }

    public function makeDisable($uid)
    {
        $updateDisableStmt = $this->conn->prepare("UPDATE user set active=? where userId=$uid");
        $updateDisableStmt->bindValue(1, FALSE, PDO::PARAM_BOOL);
        $updateDisableStmt->execute();
    }


    public function deleteUserWithID($uid)
    {
        $userObj = Util::getUserWithID($uid);
        $path = $userObj->userProfile;

        $deleteStmt = $this->conn->prepare("DELETE from user where userId=$uid");
        if ($deleteStmt->execute()) {
            if (unlink($path)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
