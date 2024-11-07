<?php
include "../dbConfig/connect.php";

$dbconfig = new DBConfig();
$conn = $dbconfig->getConnection();

$dropstmt = $conn->prepare("DROP table stockout");
if ($dropstmt->execute()) {
    echo "Drop stockout successfully<br>";
} else {
    echo "Drop stockout failed<br>";
}

$createstmt = $conn->prepare(
            "CREATE table stockout(
                stockOutId int not null AUTO_INCREMENT,
                stockOutDate date,
                doNumber text,
                doFile text,
                quantity int,
                remark text,
                customerId int,
                PRIMARY KEY(stockOutId),
                FOREIGN KEY(customerId) REFERENCES person(personId)
            )"
);

if ($createstmt->execute()) {
    echo "Successfully created stockout table<br>";
} else {
    echo "failed to create stockout table<br>";
}
?>
<!-- MMK,USD,SGD,Euro,Yen,British Pound -->