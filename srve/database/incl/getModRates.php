<?php
error_reporting(0);
include "./lib/connection.php"; 
$query = $db->prepare("SELECT * FROM modrate"); 
$query->execute(); 
$rates = $query->fetchAll(); 
$echoArray = array(); 
foreach ($rates as $rate) 
{ 
$query = $db->prepare("SELECT * FROM accounts WHERE accountID = :accID"); 
$query->execute([':accID' => $rate["accountID"]]); 
$account = $query->fetchAll()[0]; 
$query = $db->prepare("SELECT * FROM levels WHERE levelID = :lvl"); 
$query->execute([':lvl' => $rate["levelID"]]); 
try { 
$level = $query->fetchAll()[0]; 
} 
catch(Exception $e) { 
continue; 
} 
$rateObj = array( 
"levelID" => $rate["levelID"], 
"levelName" => $level["levelName"], 
"ratedBy" => $account["userName"], 
"difficulty" => $rate["difficulty"], 
"feature" => $rate["feature"] 
); 
array_push($echoArray, $rateObj); 
} 
echo json_encode($echoArray); 
?>
