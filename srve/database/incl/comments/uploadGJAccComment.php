<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
require_once "../lib/mainLib.php";
$mainLib = new mainLib();
$ep = new exploitPatch();
$GJPCheck = new GJPCheck();
$gjp = $ep->remove($_POST["gjp"]);
$userName = $ep->remove($_POST["userName"]);
$comment = $ep->remove($_POST["comment"]);
$id = $ep->remove($_POST["accountID"]);
$userID = $mainLib->getUserID($id, $userName);
$uploadDate = time();
//usercheck

if($id != "" AND $comment != "" AND $GJPCheck->check($gjp,$id) == 1){
	$mainLib->updateStatusPlaying($id);
	$query = $db->prepare("INSERT INTO acccomments (userName, comment, userID, timeStamp)
										VALUES (:userName, :comment, :userID, :uploadDate)");
	$query->execute([':userName' => $userName, ':comment' => $comment, ':userID' => $userID, ':uploadDate' => $uploadDate]);
	echo 1;
}else{
	echo -1;
}
?>