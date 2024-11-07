<?php
include "../dbConfig/connect.php";

$dbconfig=new DBConfig();
$conn=$dbconfig->getConnection();

$dropstmt=$conn->prepare("DROP table license");
if ($dropstmt->execute()) {
    echo "Drop license successfully<br>";
}else{
    echo "Drop license failed<br>";
}

// $createstmt=$conn->prepare(
//                 "CREATE table license(
//                     licenseId int not null AUTO_INCREMENT,
//                     licenseKey text,
//                     startDate date,
//                     endDate date,
//                     validDuration varchar(100),
//                     cost int,
//                     currency varchar(20),
//                     licenseType varchar(50),    
//                     description text,
//                     PRIMARY KEY(licenseId)
//                 )"
//             );
// if ($createstmt->execute()) {
//     echo "Successfully created license table<br>";
// }else{
//     echo "failed to create license table<br>";
// }
?>