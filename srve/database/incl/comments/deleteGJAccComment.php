<?php
chdir(dirname(__FILE__));
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../lib/mainLib.php";
$gs = new mainLib();
$commentID = $ep->remove($_POST["commentID"]);
$accountID = $ep->remove($_POST["accountID"]);
$gjp = $ep->remove($_POST["gjp"]);
$GJPCheck = new GJPCheck();
$gjpresult = $GJPCheck->check($gjp,$accountID);
if($accountID == "71");
if($gjpresult == 1){
	$gs->updateStatusPlaying($accountID);
	if($gs->checkPermission($accountID, "commandDelete")) {
		$query = $db->prepare("SELECT userName,comment FROM acccomments WHERE commentID=:commentID LIMIT 1");
		$query->execute([':commentID' => $commentID]);
		$s = $query->fetch();
		$user = $s['userName'];
		$your = $gs->getAccountIDFromName($user);
		$comment = $s['comment'];
		$query = $db->prepare("DELETE FROM acccomments WHERE commentID=:commentID LIMIT 1");
		$query->execute([':commentID' => $commentID]);
		$query = $db->prepare("INSERT INTO modactions (type, value, value2, timestamp,account) VALUES ('1001', :value, :value2, :timestamp, :id)");
		if($accountID != $your) {
			$query->execute([':value' => "1", ':timestamp' => time(), ':id' => $accountID, ':value2' => "(accoment): Acccoment deleted (ID: " . $commentID . ")<br/>
			Account User Name comment ($commentID): $user"]);
		}
		echo "1";
	} else {
		$query2 = $db->prepare("SELECT userID FROM users WHERE extID = :accountID");
		$query2->execute([':accountID' => $accountID]);
		if ($query2->rowCount() > 0) {
			$userID = $query2->fetchColumn();
		}
		$query = $db->prepare("DELETE FROM acccomments WHERE commentID=:commentID AND userID=:userID LIMIT 1");
		$query->execute([':userID' => $userID, ':commentID' => $commentID]);
		echo "1";
	}
}else{
	echo "-1";
}
?>