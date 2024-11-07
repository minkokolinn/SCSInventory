<?php
include "../dbConfig/connect.php";

$dbconfig=new DBConfig();
$conn=$dbconfig->getConnection();

$dropstmt=$conn->prepare("DROP table person");
if ($dropstmt->execute()) {
    echo "Drop person successfully<br>";
}else{
    echo "Drop person failed<br>";
}

$createstmt=$conn->prepare(
                "CREATE table person(
                    personId int not null AUTO_INCREMENT,
                    contactPerson varchar(100),
                    companyName text,
                    phoneNo text,
                    email text,
                    personType varchar(10),
                    personAddress text,
                    PRIMARY KEY(personId)
                )"
            );
if ($createstmt->execute()) {
    echo "Successfully created person table<br>";
}else{
    echo "failed to create person table<br>";
}
?>
