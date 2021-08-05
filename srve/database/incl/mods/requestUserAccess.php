<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../lib/mainLib.php";
$gs = new mainLib();
require_once "../sendLog.php";
$lo = new sendLog();
$gjp = $ep->remove($_POST["gjp"]);
$id = $ep->remove($_POST["accountID"]);
if($id != "" AND $gjp != ""){
	$GJPCheck = new GJPCheck();
	$gjpresult = $GJPCheck->check($gjp,$id);
	if($gjpresult == 1){
		$gs->updateStatusPlaying($id);
		$permState = $gs->getMaxValuePermission($id, "actionRequestMod");
		if($permState > 0) {
			echo $permState;
			// Account
			$query = $db->prepare("SELECT * FROM accounts WHERE accountID=:id");
			$query->execute([':id' => $id]);
			$account = $query->fetchAll()[0];
			// RoleAssign
			$query1 = $db->prepare("SELECT * FROM roleassign WHERE accountID=:id");
			$query1->execute([':id' => $id]);
			$roleID = $query1->fetchAll()[0]["roleID"];
			
			// Role
			$role = "";
			if($roleID > 0 AND $roleID < 4){
				if($permState == 2){
					$role = "<:elder_mod:599955233786822657>";
				}else{
					$role = "<:mod:599955233514323985>";
				}
			}else{
				if($roleID == 0){
					$role = "<:admin:695591779046522901>";
				}else if($roleID == 4){
					$role = "<:attention:599932468568522762>";
				}
			}
			$query2 = $db->prepare("SELECT * FROM roles WHERE roleID=:roleID");
			$query2->execute([':roleID' => $roleID]);
			$roleName = $query2->fetchAll()[0]["roleName"];
			$encodedText = "**Пользователь авторизовался в мод системе!**\nИгрок: **" . $account["userName"] . "**\nСтатус:**" . $role . $roleName . "**";
			$lo->send($encodedText);
	    } else {
	    	echo -1;
	    }
	}else{
		echo -1;
	}
}else{
	echo -1;
}
?>