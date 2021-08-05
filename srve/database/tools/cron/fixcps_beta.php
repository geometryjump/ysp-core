<?php
const autoLog = false;
function dlog($text){
	if(autoLog){
		echo "[LOG ".date("Y-m-d h:i:sa")."]: ".$text."<br/>";
	}else{
		$GLOBALS['log_data'] .= "[LOG ".date("Y-m-d h:i:sa")."]: ".$text."<br/>";
	}
	//
	return;
}


chdir(dirname(__FILE__));
echo "Waiting...<br>";
//echo "We a created log. <a href=\"../logs/update_cp_log.txt\">Click</a> for show";
include "../../incl/lib/connection.php";
if(empty($db)){
	dlog("Database not connected.");
	exit();
}
dlog("Database connected.");

$query = $db->prepare("SELECT userID, userName FROM users");
$query->execute();
$result = $query->fetchAll();
$users = $query->rowCount();
dlog("We a getted users. Waiting operations...");
echo "Waiting for users...<br/>";
$totalcp = 0;
foreach ($result as $user) {
	$userID = $user["userID"];
	$cp_with_stars = $db->prepare("SELECT count(*) FROM levels WHERE userID = :userID AND starStars != 0");
	$cp_with_stars->execute([':userID' => $userID]);
	$cp_with_stars_data = $cp_with_stars->fetchColumn();
	$cp_with_feature = $db->prepare("SELECT count(*) FROM levels WHERE userID = :userID AND starFeatured != 0");
	$cp_with_feature->execute([':userID' => $userID]);
	$cp_with_feature_data = $cp_with_feature->fetchColumn();
	$cp_with_epic = $db->prepare("SELECT count(*) FROM levels WHERE userID = :userID AND starEpic != 0");
	$cp_with_epic->execute([':userID' => $userID]);
	$cp_with_epic_data = $cp_with_epic->fetchColumn();
	$total = $cp_with_stars_data + $cp_with_feature_data + $cp_with_epic_data;
	dlog($user["userName"]." - selected count from users. Values: [stars:".$cp_with_stars_data."],[featured:".$cp_with_feature_data."],[epic:".$cp_with_epic_data."]. Total: ".$total);
	$totalcp += $total;
}
//done
$query = $db->prepare("SELECT count(*) FROM users WHERE creatorPoints > 0");
$query->execute();
$usersc = $query->fetchColumn();
echo "<br><br>Done. Total cp from all users (".$users.", WITH CP: ".$usersc."): ".$totalcp;
if(!autoLog){
	echo "<hr/><h1>Logs</h1>".$GLOBALS['log_data'];
}