<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../incl/lib/connection.php";
include "../incl/lib/mainLib.php";
$gs = new mainLib();
include "../incl/sendLog.php";
$sl = new sendLog();
require_once "../incl/lib/exploitPatch.php";
if(!empty($_POST["userID"])) {
    $postUserID = $_POST["userID"];
}
else {
    if(!empty($_GET["userID"])) {
        $postUserID = $_GET["userID"];
    }
    else {
        exit("-1");
    }
}

	$query = $db->prepare("SELECT * FROM users WHERE userID = :userID LIMIT 1");
	$query->execute([':userID' => $postUserID]);
	$user = $query->fetch();
	$accountid = $gs->getExtID($postUserID);
	$roleID = $gs->getRoleIDByAccID($accountid);
    $roleName = $gs->getRoleNameByRoleID($roleID);
	$lbstring = ($user["userName"].":".$roleName);

if($lbstring == ""){
	exit("-1");
}
$logStr = ("**".$user["userName"]."** запустил **сеанс**. \nОтвет: ".$lbstring);
$sl->sendLauncher($logStr);
?>