<?php
include "../dbConfig/connect.php";

$dbconfig=new DBConfig();
$conn=$dbconfig->getConnection();

$dropstmt=$conn->prepare("DROP table brand");
if ($dropstmt->execute()) {
    echo "Drop brand successfully<br>";
}else{
    echo "Drop brand failed<br>";
}

$createstmt=$conn->prepare(
                "CREATE table brand(
                    brandId int not null AUTO_INCREMENT,
                    brandName varchar(130),
                    note text,
                    PRIMARY KEY(brandId)
                )"
            );
if ($createstmt->execute()) {
    echo "Successfully created brand table<br>";
}else{
    echo "failed to create brand table<br>";
}
?>
