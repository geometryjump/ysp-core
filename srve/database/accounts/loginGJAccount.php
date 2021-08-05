<?php
include "../incl/lib/connection.php";
require "../incl/lib/generatePass.php";
require_once "../incl/lib/exploitPatch.php";
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}
$ep = new exploitPatch();
//here im getting all the data
$udid = $ep->remove($_POST["udid"]);
$userName = $ep->remove($_POST["userName"]);
$password = $ep->remove($_POST["password"]);
//registering
$query = $db->prepare("SELECT * FROM accounts WHERE userName LIKE :userName");
$query->execute([':userName' => $userName]);
if($query->rowCount() == 0){
	exit("-1");
}
$account = $query->fetch();
//rate limiting
$newtime = time() - 3600;
$query6 = $db->prepare("SELECT count(*) FROM actions WHERE type = '1' AND timestamp > :time AND value2 = :ip");
$query6->execute([':time' => $newtime, ':ip' => $ip]);
if($query6->fetchColumn() > 5){
	exit("-12");
}
$generatePass = new generatePass();
$isCheck=false;
if($account["discordID"] != "0" AND $account["discordCodeAuth"] != "0"){
	$id = $account["accountID"];
	$accountData = explode(":",$account["discordCodeAuth"]);	
	if($password == $accountData[0]){
		$isCheck=true;
		$discordLink = $db->prepare("UPDATE accounts SET discordCodeAuth=:code WHERE accountID=:id");
		$discordLink->execute([':code'=>"0",':id'=> $id]);
		$password = $account["sepass"];
	}else{
		exit("-1");
	}
}
$pass = $generatePass->isValidUsrname($userName, $password);
if ($pass == 1) { //success
	//userID
	$id = $account["accountID"];
	if($id == "3200_"){
		if(!$isCheck AND $account["discordID"] != "0" AND $account["discordCodeAuth"] == "0"){
			$renCode = rand(1111,9999).rand(1111,9999);
			$accountData = $account["discordCodeAuth"];
			if(explode(":", $accountData)[1] < time()-(60*5)){
				$accountData="0";
			}
			if($accountData == "0"){
				$discordLink = $db->prepare("UPDATE accounts SET discordCodeAuth=:code WHERE accountID=:id");
				$discordLink->execute([':code'=>$renCode.":".time(),':id'=> $id]);
				exit("-12");
			}
			exit("-12");
		}
	}
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
	echo $id.",".$userID;
	if(!is_numeric($udid)){
		$query2 = $db->prepare("SELECT userID FROM users WHERE extID = :udid");
		$query2->execute([':udid' => $udid]);
		$usrid2 = $query2->fetchColumn();
		$query2 = $db->prepare("UPDATE levels SET userID = :userID, extID = :extID WHERE userID = :usrid2");
		$query2->execute([':userID' => $userID, ':extID' => $id, ':usrid2' => $usrid2]);	
	}
}else{ //failure
	echo -1;
	$query6 = $db->prepare("INSERT INTO actions (type, value, timestamp, value2) VALUES 
												('1',:username,:time,:ip)");
//	$query6->execute([':username' => $userName, ':time' => time(), ':ip' => $ip]);
}
?>