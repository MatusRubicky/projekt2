<?php
include "config.php";

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$result = $db->query(sprintf("SELECT * from shops")) or die('Could not query');
$jsonData = array();

    while($row=$result->fetch_object()){
       $jsonData[] = $row;
}
echo json_encode($jsonData);