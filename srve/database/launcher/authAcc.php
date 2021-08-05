<?php
include "../incl/lib/connection.php";
require "../incl/lib/generatePass.php";
require_once "../incl/lib/exploitPatch.php";
include "../incl/sendLog.php";
$sl = new sendLog();
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}
$ep = new exploitPatch();
//here im getting all the data
$userName = $_POST["userName"];
$password = $_POST["password"];
//registering
$query = $db->prepare("SELECT * FROM accounts WHERE userName LIKE :userName");
$query->execute([':userName' => $userName]);
if($query->rowCount() == 0){
	exit("-1");
}
$account = $query->fetch();
$accountUserName = $account["userName"];
$accountUserPass = $account["password"];

$generatePass = new generatePass();
$isCheck=false;
$pass = $generatePass->isValidUsrname($userName, $password);
if ($pass == 1) { //success
	//userID
	$id = $account["accountID"];

	$query2 = $db->prepare("SELECT userID FROM users WHERE extID = :id");
	$query2->execute([':id' => $id]);
	if ($query2->rowCount() > 0) {
		$userID = $query2->fetchColumn();
	} else {
		$query = $db->prepare("INSERT INTO users (isRegistered, extID, userName)
		VALUES (1, :id, :userName)");

		$query->execute([':id' => $id, ':userName' => $userName]);
		$userID = $db->lastInsertId();
	}
	//logging
	$query6 = $db->prepare("INSERT INTO actions (type, value, timestamp, value2) VALUES 
												('2',:username,:time,:ip)");
	$query6->execute([':username' => $userName, ':time' => time(), ':ip' => $ip]);

	$query6 = $db->prepare("UPDATE accounts SET sepass=:sepass, ip=:ip WHERE userName=:userName");	
	$query6->execute([':sepass' => $password, ':ip'=>$ip, ':userName' => $userName]);
	//result
	echo $userName.":".$password.":".$id.":".$userID;
		
		$logStr = ("**".$userName."** запрос на **вход/верифи юзера**. \nОтвет: ".$userName.":__PASSWORD__:".$id.":".$userID);
        $sl->sendLauncher($logStr);
	if(!is_numeric($udid)){
		$query2 = $db->prepare("SELECT userID FROM users WHERE extID = :udid");
		$query2->execute([':udid' => $udid]);
		$usrid2 = $query2->fetchColumn();
		$query2 = $db->prepare("UPDATE levels SET userID = :userID, extID = :extID WHERE userID = :usrid2");
		$query2->execute([':userID' => $userID, ':extID' => $id, ':usrid2' => $usrid2]);	
	}
}else{ //failure
	echo "-1";
}
?>