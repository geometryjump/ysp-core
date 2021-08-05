<?php
chdir(dirname(__FILE__));
ini_set('display_errors', 1); 
error_reporting(E_ALL);
include "../lib/connection.php";
require_once "../lib/mainLib.php";
$mainLib = new mainLib();
require_once "../lib/XORCipher.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../misc/commands.php";
$cmds = new Commands();
$gjp = $ep->remove($_POST["gjp"]);
$userName = $ep->remove($_POST["userName"]);
$comment = $ep->remove($_POST["comment"]);
$gameversion = $_POST["gameVersion"];
if($gameversion < 20){
	$comment = base64_encode($comment);
}
$levelID = $ep->remove($_POST["levelID"]);
$percent = $ep->remove($_POST["percent"]);
if($percent == ""){
	$percent = 0;
}
$id = $ep->remove($_POST["udid"]);
if($_POST["accountID"]!="" AND $_POST["accountID"]!="0"){
	$id = $ep->remove($_POST["accountID"]);
	$register = 1;
	$GJPCheck = new GJPCheck();
	$gjpresult = $GJPCheck->check($gjp,$id);
	if($gjpresult == 0){
		exit("-1");
	}
}else{
	$register = 0;
}
$userID = $mainLib->getUserID($id, $userName);
$extID = $mainLib->getExtID($userID);
if(!$mainLib->GetLevelBanned($extID, "isCommentBanned")) {
	exit("-10");
} 
$uploadDate = time();
$decodecomment = base64_decode($comment);
if($cmds->doCommands($id, $decodecomment, $levelID)){
	$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('30', :value, :levelID, :timestamp, :id)");
	$query->execute([':value' => $decodecomment, ':timestamp' => time(), ':id' => $id, ':levelID' => $levelID]);
	exit("-1");
}
if($id != "" AND $comment != ""){
	if($mainLib->isMultiBan($extID,2) == 1){
		exit("-10");
	}
	$mainLib->updateStatusPlaying($extID);
	$query = $db->prepare("INSERT INTO comments (userName, comment, levelID, userID, timeStamp, percent) VALUES (:userName, :comment, :levelID, :userID, :uploadDate, :percent)");
	if($register == 1){
		$query->execute([':userName' => $userName, ':comment' => $comment, ':levelID' => $levelID, ':userID' => $userID, ':uploadDate' => $uploadDate, ':percent' => $percent]);
		echo 1;
		if($percent != 0){
			$query2 = $db->prepare("SELECT percent FROM levelscores WHERE accountID = :accountID AND levelID = :levelID");
			$query2->execute([':accountID' => $id, ':levelID' => $levelID]);
			$result = $query2->fetchAll();
			if ($query2->rowCount() == 0) {
				$query = $db->prepare("INSERT INTO levelscores (accountID, levelID, percent, uploadDate)
				VALUES (:accountID, :levelID, :percent, :uploadDate)");
			} else {
				if($result[0]["percent"] < $percent){
					$query = $db->prepare("UPDATE levelscores SET percent=:percent, uploadDate=:uploadDate WHERE accountID=:accountID AND levelID=:levelID");
					$query->execute([':accountID' => $id, ':levelID' => $levelID, ':percent' => $percent, ':uploadDate' => $uploadDate]);
				}
			}
		}
	}else{
		$query->execute([':userName' => $userName, ':comment' => $comment, ':levelID' => $levelID, ':userID' => $userID, ':uploadDate' => $uploadDate, ':percent' => $percent]);
		echo 1;
	}
}else{
	echo -1;
}
?>