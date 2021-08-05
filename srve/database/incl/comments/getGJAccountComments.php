<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../lib/connection.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../lib/mainLib.php";
$gs = new mainLib();
$commentstring = "";
$accountid = $ep->remove($_POST["accountID"]);
$page = $ep->remove($_POST["page"]);
$commentpage = $page*10;
$userID = $gs->getUserID($accountid);
$query = "SELECT comment, userID, likes, isSpam, commentID, timestamp FROM acccomments WHERE userID = :userID ORDER BY timeStamp DESC LIMIT 10 OFFSET $commentpage";
$query = $db->prepare($query);
$query->execute([':userID' => $userID]);
$result = $query->fetchAll();
if($query->rowCount() == 0){
	exit("#0:0:0");
}
$prequery = $db->prepare("SELECT UserPrefix FROM users WHERE userID = :userID");
$prequery->execute([':userID' => $userID]);
$prefixdb = $prequery->fetchColumn();
$prefixdb2 = $prefixdb;
if($prefixdb2 != "") {
    $prefixdb2 = "$prefixdb2 /";
}

$prequery = $db->prepare("SELECT clanID FROM users WHERE userID = :userID");
$prequery->execute([':userID' => $userID]);
$clanID = $prequery->fetchColumn();

if($clanID != 0) {
    $prequery = $db->prepare("SELECT clanName FROM clans WHERE clanID = :clanID");
    $prequery->execute([':clanID' => $clanID]);
    $clanName = $prequery->fetchColumn();
    $prefixdb = "$prefixdb / $clanName /";
    $prefixdb2 = $prefixdb;
}
else{
    $prefixdb = $prefixdb2;
    $prefixdb = "$prefixdb /";
}

$countquery = $db->prepare("SELECT count(*) FROM acccomments WHERE userID = :userID");
$countquery->execute([':userID' => $userID]);
$commentcount = $countquery->fetchColumn();
foreach($result as &$comment1) {
    if($prefixdb != "") {
        $prefixdb = "$prefixdb /";
    }
    $roleID = $gs->getRoleIDByAccID($accountid);
    $roleName = $gs->getRoleNameByRoleID($roleID);
	
	if($roleID != "") {
        $prefixdb = $prefixdb2;
	    $prefixdb = "$prefixdb $roleName /";
	}
	else if($userID == "17297") {
        $prefixdb = $prefixdb2;
	    $prefixdb = "$prefixdb Test account /";
	}
	else{
        $prefixdb = $prefixdb2;
	    $prefixdb = "$prefixdb Player /";
	}
	
	if($comment1["commentID"]!=""){
		$uploadDate = $gs->GetNewTimeAgo($comment1["timestamp"]);
		$commentstring .= "2~".$comment1["comment"]."~3~".$comment1["userID"]."~4~".$comment1["likes"]."~5~0~7~".$comment1["isSpam"]."~9~".$prefixdb." ".$uploadDate."~6~".$comment1["commentID"]."|";
	}
}
$commentstring = substr($commentstring, 0, -1);
echo $commentstring;
echo "#".$commentcount.":".$commentpage.":10";
?>