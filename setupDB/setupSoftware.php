<?php
include "../dbConfig/connect.php";

$dbconfig=new DBConfig();
$conn=$dbconfig->getConnection();

$dropstmt=$conn->prepare("DROP table software");
if ($dropstmt->execute()) {
    echo "Drop software successfully<br>";
}else{
    echo "Drop software failed<br>";
}

$createstmt=$conn->prepare(
                "CREATE table software(
                    softwareId int not null AUTO_INCREMENT,
                    softwareName varchar(200),
                    targetOrganisation text,
                    developedBy varchar(200),
                    projectDuration varchar(100),
                    description text,
                    PRIMARY KEY(softwareId)
                )"
            );
if ($createstmt->execute()) {
    echo "Successfully created software table<br>";
}else{
    echo "failed to create software table<br>";
}
?>
<!-- MMK,USD,SGD,Euro,Yen,British Pound -->