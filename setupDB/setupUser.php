<?php
include "../dbConfig/connect.php";

$dbconfig=new DBConfig();
$conn=$dbconfig->getConnection();

$dropstmt=$conn->prepare("DROP table user");
if ($dropstmt->execute()) {
    echo "Drop user successfully<br>";
}else{
    echo "Drop user failed<br>";
}

$createstmt=$conn->prepare(
                "CREATE table user(
                    userId int not null AUTO_INCREMENT,
                    userName varchar(80),
                    userEmail varchar(200) UNIQUE,
                    userPassword text,
                    userPhone varchar(15),
                    userProfile text,
                    active boolean,
                    administration varchar(5), 
                    general varchar(5),
                    person varchar(5),
                    stockin varchar(5),
                    stockout varchar(5),
                    stockbalance varchar(5),
                    PRIMARY KEY(userId)
                )"
            );
if ($createstmt->execute()) {
    echo "Successfully created user table<br>";
}else{
    echo "failed to create user table<br>";
}

$hashedpass=password_hash("admin123",PASSWORD_DEFAULT);

$insertstmt=$conn->prepare(
            "
            INSERT into user(userName,userEmail,userPassword,userPhone,userProfile
            ,active,administration,general,person,stockin,stockout,stockbalance)
            values('Admin','admin@gmail.com','$hashedpass','09254325731','profileImg/img_avatar.png'
            ,TRUE,'rw','rw','rw','rw','rw','rw')
            "
        );
if ($insertstmt->execute()) {
    echo "Successfully inserted a new admin record";
}else{
    echo "Failed to insert admin";
}


?>
