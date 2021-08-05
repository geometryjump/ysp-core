<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../incl/lib/connection.php";
require_once "../incl/lib/exploitPatch.php";
require_once "../incl/lib/GJPCheck.php";
$ep = new exploitPatch();
include "../incl/sendLog.php";
$sl = new sendLog();
include "../incl/lib/mainLib.php";
$gs = new mainLib();
$stars = 0;
$count = 0;
$xi = 0;
$lbstring = "";
$sign = "> 19";
if(isset($_POST["type"])) {
    $type = $ep->remove($_POST["type"]);
}
else {
    $type = "top";
}

if($type == "top" OR $type == "creators" OR $type == "relative"){
	if($type == "top"){//top
		if(is_file("top100_stopped_loooool")){
			exit(file_get_contents("top100_stopped_loooool"));
		}
		$query = "SELECT * FROM users WHERE isBanned = '0' AND extID > 0 AND gameVersion $sign AND stars > 50 ORDER BY stars DESC LIMIT 1000";
	}
	if($type == "creators"){//creators
		$query = "SELECT * FROM users WHERE isCreatorBanned = '0' AND extID > 0 AND creatorPoints > 0 ORDER BY creatorPoints DESC LIMIT 1000";
	}
	$query = $db->prepare($query);
	$query->execute([':stars' => $stars, ':count' => $count]);
	$result = $query->fetchAll();
	if($type == "relative"){
		$user = $result[0];
		$extid = $user["extID"];
		$e = "SET @rownum := 0;";
		$query = $db->prepare($e);
		$query->execute();
		$f = "SELECT rank, stars FROM (
							SELECT @rownum := @rownum + 0 AS rank, stars, extID, isBanned
							FROM users WHERE extID > 0 AND isBanned = '0' AND gameVersion $sign ORDER BY stars DESC
							) as result WHERE extID=:extid";
		$query = $db->prepare($f);
		$query->execute([':extid' => $extid]);
		$leaderboard = $query->fetchAll();
		//var_dump($leaderboard);
		$leaderboard = $leaderboard[0];
		$xi = $leaderboard["rank"] - 0;
	}
	foreach($result as &$user) {
		$extid = 0;
		if(is_numeric($user["extID"])){
			$extid = $user["extID"];
		}
		$xi++;
		$lbstring .= "1:".$user["userName"].":2:".$user["userID"].":13:".$user["coins"].":17:".$user["userCoins"].":6:".$xi.":9:".$user["icon"].":10:".$user["color1"].":11:".$user["color2"].":14:".$user["iconType"].":15:".$user["special"].":16:".$extid.":3:".$user["stars"].":8:".round($user["creatorPoints"],0,PHP_ROUND_HALF_DOWN).":4:".$user["demons"].":7:".$extid.":46:".$user["diamonds"]."|";
	}
}
if($type == "friends"){
	$query = "SELECT * FROM friendships WHERE person1 = :accountID OR person2 = :accountID";
	$query = $db->prepare($query);
	$query->execute([':accountID' => $accountID]);
	$result = $query->fetchAll();
	$people = "";
	foreach ($result as &$friendship) {
		$person = $friendship["person1"];
		if($friendship["person1"] == $accountID){
			$person = $friendship["person2"];
		}
		$people .= ",".$person;
	}
	$query = "SELECT * FROM users WHERE extID IN (:accountID $people ) ORDER BY stars DESC";
	$query = $db->prepare($query);
	$query->execute([':accountID' => $accountID]);
	$result = $query->fetchAll();
	foreach($result as &$user){
		if(is_numeric($user["extID"])){
			$extid = $user["extID"];
		}else{
			$extid = 0;
		}
		$xi++;
		$lbstring .= "1:".$user["userName"].":2:".$user["userID"].":13:".$user["coins"].":17:".$user["userCoins"].":6:".$xi.":9:".$user["icon"].":10:".$user["color1"].":11:".$user["color2"].":14:".$user["iconType"].":15:".$user["special"].":16:".$extid.":3:".$user["stars"].":8:".round($user["creatorPoints"],0,PHP_ROUND_HALF_DOWN).":4:".$user["demons"].":7:".$extid.":46:".$user["diamonds"]."|";
	}
}
if($lbstring == ""){
	exit("-1");
}
$lbstring = substr($lbstring, 0, -1);
$postUN = $_POST["userID"];
$postUN2 = $gs->getUserName($postUN);
$logStr = ("**$postUN2** запрос на **$type топ**. \nОтвет: __слишком большой__");
$sl->sendLauncher($logStr);
echo $lbstring;
?>