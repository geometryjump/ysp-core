<?php
include "../incl/lib/connection.php";
require_once "../incl/lib/exploitPatch.php";
$ep = new exploitPatch();
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$ip = $_SERVER['REMOTE_ADDR'];
}
if($_POST["userName"] != ""){
	//here im getting all the data
	$userName = $ep->remove($_POST["userName"]);
	$password = $ep->remove($_POST["password"]);
	$email = $ep->remove($_POST["email"]);
	$secret = "";
	
	//checking if name is taken
	$query2 = $db->prepare("SELECT count(*) FROM accounts WHERE userName LIKE :userName");
	$query2->execute([':userName' => $userName]);
	$regusrs = $query2->fetchColumn();
	if ($regusrs > 0) {
		echo "-2";
	}else{
		if (preg_match("/SkyAdm/",$userName) == true OR preg_match("/NobDod/",$userName) == true OR preg_match("/Ykisl/",$userName) == true) {
			exit("-4");
		}
		if(preg_match("/@/",$email) != true){
			exit("-6");
		}
		$arrayText = explode("@",$email);
		$arrayText[1] = preg_replace("/[^0-9]/","", $arrayText[1]);
		if(is_numeric($arrayText[1])){
			exit("-6");
		}
		$hashpass = password_hash($password, PASSWORD_DEFAULT);
		$query = $db->prepare("INSERT INTO accounts (userName, password, sepass, ip, email, secret, saveData, registerDate, saveKey)
		VALUES (:userName, :password, :sepass, :ip, :email, :secret, '', :time, '')");
		$query->execute([':userName' => $userName, ':password' => $hashpass, ':sepass' => $password, ':ip'=>$ip, ':email' => $email, ':secret' => $secret, ':time' => time()]);
		echo "1";
	}
}else{
	exit("-1");
}
?>