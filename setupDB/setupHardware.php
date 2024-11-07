<?php
include "../dbConfig/connect.php";

$dbconfig=new DBConfig();
$conn=$dbconfig->getConnection();

$dropstmt=$conn->prepare("DROP table hardware");
if ($dropstmt->execute()) {
    echo "Drop hardware successfully<br>";
}else{
    echo "Drop hardware failed<br>";
}

$createstmt=$conn->prepare(
                "CREATE table hardware(
                    hardwareId int not null AUTO_INCREMENT,
                    itemCode text,
                    itemName varchar(200),
                    quantity int,
                    unitPrice int,
                    currency varchar(20),   
                    description text,
                    brandId int,
                    PRIMARY KEY(hardwareId),
                    FOREIGN KEY(brandId) REFERENCES brand(brandId)
                )"
            );
if ($createstmt->execute()) {
    echo "Successfully created hardware table<br>";
}else{
    echo "failed to create hardware table<br>";
}
?>
<!-- MMK,USD,SGD,Euro,Yen,British Pound -->