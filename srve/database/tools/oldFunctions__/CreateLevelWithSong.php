<?php
//error_reporting(0);
include "../incl/lib/connection.php";
require_once "../incl/lib/exploitPatch.php";
require_once "../incl/lib/mainLib.php";
$mainLib = new mainLib();
$ep = new exploitPatch();
$api_key = "dc467dd431fc48eb0244b0aead929ccd";
if(!empty($_POST["songid"])){
    
    $gameVersion = 21;
    $binaryVersion = 35;
    $gdw = 0;
    $accountID = 3183;
    $gjp = 'W15GRVlBW1E';
    $userName = 'MusicBOT';
    $levelID = 0;
    $levelName = "Song XnameX Reup";
    $levelDesc = '';
    $levelVersion = '1';
    $levelLength = 0;
    $audioTrack = 0;
    $auto = 1;
    $password = 0;
    $original = 0;
    $requestedStars = 0;
    $twoPlayer = 0;
    $songID = $_POST["songid"];
    $objects = 8;
    $coins = 0;
    $requestedStars = 0;
    $unlisted = 1;
    $wt = 57;
    $wt2 = 0;
    $ldm = 0;
    $extraString = '0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0_0';
    $seed = 'pHMtMMgwVP&seed2=VVULAQwMUwYCBFEBAgJSVwUAUVYAUlMADAADAA4GV1ULVQYEVVAABA==';
    $levelString = 'H4sIAAAAAAAAC62RTU7EMAyFL-SxYjt2HKFZ9AijGSGxihBILOYAbDg8ToM0bRl-RrCIX1p_79VJz0dxoJYax5JYRI1VG_EQmSW3HTVrlFJqpVEj7cUD90ZvN9jpb_Z61d6ZYRj8DyHcuv8fgvTLoHRLjH0bQ8q_iLl2MXCeSCB10SE2JEPUsS_jzYd4l6PU-YnnOgLmxpTnOrqUhhCkO4IamQxMjGoVBNSQ4qMGu94gIBCC02G_v7DK6GLBWrCafQM_vS5hJ3TOARdB8WDW8MMKjsCatCdXNGO7wLXDj6sxJCOXPrIRKvuGfV6yYhmT9pGroLtu2PtpyYpjSSVYL3Enn453Wk4sZFi0H88ZU42ftk4-vAT8DhWdXW-pAwAA';
    $levelInfo = 'H4sIAAAAAAAACzPUszC2tgYAVWkengYAAAA';
    $secret = Wmfd2893gb7;
    $userID = $mainLib->getUserID($accountID, $userName);
    
    $uploadDate = time();
    
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$hostname = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$hostname = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
	$hostname = $_SERVER['REMOTE_ADDR'];
}
    
    
	$query = $db->prepare("SELECT count(*) FROM songs WHERE ID = :id");
	$query->execute([':id' => $_POST["songid"]]);	
	$count = $query->fetchColumn();
	if($count > 0){
	    
	$Musicquery = $db->prepare("SELECT name FROM songs WHERE ID = :id");
	$Musicquery->execute([':id' => $_POST["songid"]]);	
	$musicname = $Musicquery->fetchColumn();
	if($musicname != ''){
	    $levelName = "Song ".$musicname." Reup";
	}
	else{
	    $levelName = 'Song NULL Reup';
	}
	
	
	$Levelquery = $db->prepare("SELECT count(*) FROM levels WHERE songID = :id AND userName = :msq");
	$Levelquery->execute([':id' => $_POST["songid"], ':msq' => $userName]);	
	$Levcount = $Levelquery->fetchColumn();
	if($Levcount > 0){
	    
	$GetIdquery = $db->prepare("SELECT levelID FROM levels WHERE songID = :id AND userName = :msq");
	$GetIdquery->execute([':id' => $_POST["songid"], ':msq' => $userName]);	
	$levelID = $GetIdquery->fetchColumn();
	echo '<h2>Все готово!</h2> ИД уровня: '.$levelID;
	
	}
	else{
	
	

	$queryx = $db->prepare("INSERT INTO levels (levelName, gameVersion, binaryVersion, userName, levelDesc, levelVersion, levelLength, audioTrack, auto, password, original, twoPlayer, songID, objects, coins, requestedStars, extraString, levelString, levelInfo, secret, uploadDate, userID, extID, updateDate, unlisted, hostname, isLDM)
VALUES (:levelName, :gameVersion, :binaryVersion, :userName, :levelDesc, :levelVersion, :levelLength, :audioTrack, :auto, :password, :original, :twoPlayer, :songID, :objects, :coins, :requestedStars, :extraString, :levelString, :levelInfo, :secret, :uploadDate, :userID, :id, :uploadDate, :unlisted, :hostname, :ldm)");

$querye=$db->prepare("SELECT levelID FROM levels WHERE levelName = :levelName AND userID = :userID");
$querye->execute([':levelName' => $levelName, ':userID' => $userID]);
$levelID = $querye->fetchColumn();


$queryx->execute([':levelName' => $levelName, ':gameVersion' => $gameVersion, ':binaryVersion' => $binaryVersion, ':userName' => $userName, ':levelDesc' => $levelDesc, ':levelVersion' => $levelVersion, ':levelLength' => $levelLength, ':audioTrack' => $audioTrack, ':auto' => $auto, ':password' => $password, ':original' => $original, ':twoPlayer' => $twoPlayer, ':songID' => $songID, ':objects' => $objects, ':coins' => $coins, ':requestedStars' => $requestedStars, ':extraString' => $extraString, ':levelString' => "", ':levelInfo' => $levelInfo, ':secret' => $secret, ':uploadDate' => $uploadDate, ':userID' => $userID, ':id' => $accountID, ':unlisted' => $unlisted, ':hostname' => $hostname, ':ldm' => $ldm]);
		$levelID = $db->lastInsertId();
		file_put_contents("../data/levels/$levelID",$levelString);
		echo '<h2>Все готово!</h2> ИД уровня: '.$levelID;


	}
	
	}
	else{
	    echo 'Не нашел музыку...';
	}

}
	if(isset($_GET['music'])) { echo '<h2>Загрузить (устарело и не будет обновляться!)</h2><form action="" method="post">ИД музыки: <input type="text" name="songid"><br><input type="submit" value="Загрузить"></form>'; }
	else { echo "<h2>Ой!</h2>Это уже устарело. Может по этой ссылке <a href='../dashboard/upload/createLevelForMusic.php'>лучше будет</a> без регистрации? Или вы этот хотите? Тогда <a href='CreateLevelWithSong.php?music'>сюда кликай</a>"; }
?>