<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../lib/connection.php";
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../lib/mainLib.php";
$gs = new mainLib();
//here im getting all the data
$mS = $ep->remove($_POST["mS"]);
$frS = $ep->remove($_POST["frS"]);
$cS = $ep->remove($_POST["cS"]);
$youtubeurl = $ep->remove($_POST["yt"]);
$gjp = $ep->remove($_POST["gjp"]);
$accountID = $ep->remove($_POST["accountID"]);
$twitter = $ep->remove($_POST["twitter"]);
$twitch = $ep->remove($_POST["twitch"]);
//query
$query = $db->prepare("UPDATE accounts SET mS=:mS, frS=:frS, cS=:cS, youtubeurl=:youtubeurl, twitter=:twitter, twitch=:twitch WHERE accountID=:accountID");
$GJPCheck = new GJPCheck();
$gjpresult = $GJPCheck->check($gjp,$accountID);
if($gjpresult == 1){
	$gs->updateStatusPlaying($accountID);
	$query->execute([':mS' => $mS, ':frS' => $frS, ':cS' => $cS, ':youtubeurl' => $youtubeurl, ':accountID' => $accountID, ':twitch' => $twitch, ':twitter' => $twitter]);
	echo 1;
}else{
	echo -1;
}
?>
