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
if($gjpresult == 1) {
	$gs->updateStatusPlaying($accountID);
	if($gs->checkPermission($accountID, "commandDelete")) {
		$query = $db->prepare("SELECT userName,comment FROM comments WHERE commentID=:commentID LIMIT 1");
		$query->execute([':commentID' => $commentID]);
		$s = $query->fetch();
		$user = $s['userName'];
		$your = $gs->getAccountIDFromName($user);
		$comment = $s['comment'];
		$query = $db->prepare("DELETE FROM comments WHERE commentID=:commentID LIMIT 1");
		$query->execute([':commentID' => $commentID]);
		$query = $db->prepare("INSERT INTO modactions (type, value, value2, timestamp,account) VALUES ('1000', :value, :value2, :timestamp, :id)");
		if($accountID != $your) {
			$query->execute([':value' => "1", ':timestamp' => time(), ':id' => $accountID, ':value2' => "(comment level): Acccoment deleted (ID: " . $commentID . ")<br/>
			Account User Name comment ($commentID): $user"]);
		}
		echo "1";
	} else {
		$query = $db->prepare("SELECT userID FROM users WHERE extID = :accountID");
		$query->execute([':accountID' => $accountID]);
		$userID = $query->fetchColumn();
		$query = $db->prepare("DELETE FROM comments WHERE commentID=:commentID AND userID=:userID LIMIT 1");
		$query->execute([':commentID' => $commentID, ':userID' => $userID]);
		if($query->rowCount() == 0) {
			$query = $db->prepare("SELECT levelID FROM comments WHERE commentID = :commentID");
			$query->execute([':commentID' => $commentID]);
			$levelID = $query->fetchColumn();
			$query = $db->prepare("SELECT userID FROM levels WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			$creatorID = $query->fetchColumn();
			$query = $db->prepare("SELECT extID FROM users WHERE userID = :userID");
			$query->execute([':userID' => $creatorID]);
			$creatorAccID = $query->fetchColumn();
			if($creatorAccID == $accountID) {
				$query = $db->prepare("DELETE FROM comments WHERE commentID=:commentID AND levelID=:levelID LIMIT 1");
				$query->execute([':commentID' => $commentID, ':levelID' => $levelID]);
			}
		}
		echo "1";
	}
} else {
	echo "-1";
}
?>