<?php
include "../dbConfig/connect.php";

$dbconfig=new DBConfig();
$conn=$dbconfig->getConnection();

$dropstmt=$conn->prepare("DROP table hardwareDetail");
if ($dropstmt->execute()) {
    echo "Drop hardwareDetail successfully<br>";
}else{
    echo "Drop hardwareDetail failed<br>";
}

$createstmt=$conn->prepare(
                "CREATE table hardwareDetail(
                    hardwareDetailId int not null AUTO_INCREMENT,
                    serialKey text,
                    hardwareId int,
                    stockInId int,
                    stockOutId int,
                    status varchar(10),
                    remark text,
                    serviceStart date,
                    serviceEnd date,
                    serviceDuration varchar(30),
                    PRIMARY KEY(hardwareDetailId),
                    FOREIGN KEY(hardwareId) REFERENCES hardware(hardwareId),
                    FOREIGN KEY(stockInId) REFERENCES stockin(stockInId),
                    FOREIGN KEY(stockOutId) REFERENCES stockout(stockOutId)
                )"
            );
if ($createstmt->execute()) {
    echo "Successfully created hardwareDetail table<br>";
}else{
    echo "failed to create hardwareDetail table<br>";
}

?>
<!-- MMK,USD,SGD,Euro,Yen,British Pound -->