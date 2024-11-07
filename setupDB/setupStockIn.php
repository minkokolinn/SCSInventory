<?php
include "../dbConfig/connect.php";

$dbconfig=new DBConfig();
$conn=$dbconfig->getConnection();

$dropstmt=$conn->prepare("DROP table stockin");
if ($dropstmt->execute()) {
    echo "Drop purchase successfully<br>";
}else{
    echo "Drop purchase failed<br>";
}

$createstmt=$conn->prepare(
                "CREATE table stockin(
                    stockInId int not null AUTO_INCREMENT,
                    stockInDate date,
                    poNumber text,
                    poFile text,
                    quantity int,
                    remark text,
                    supplierId int,
                    PRIMARY KEY(stockinId),
                    FOREIGN KEY(supplierId) REFERENCES person(personId)
                )"
            );
if ($createstmt->execute()) {
    echo "Successfully created purchase table<br>";
}else{
    echo "failed to create purchase table<br>";
}
?>