<?php

$db = new mysqli('s.ics.upjs.sk','store-locator','Pas123pas','store-locator');

$result = $db->query(sprintf("SELECT * from shops")) or die('Could not query');
$jsonData = array();

    while($row=$result->fetch_object()){
       $jsonData[] = $row;
}
echo json_encode($jsonData);