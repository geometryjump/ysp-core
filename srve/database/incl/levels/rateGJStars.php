<?php

chdir(dirname(__FILE__));

include "../lib/connection.php";

require_once "../lib/GJPCheck.php";

require_once "../lib/exploitPatch.php";

$ep = new exploitPatch();

require_once "../lib/mainLib.php";

$gs = new mainLib();

$gjp = $ep->remove($_POST["gjp"]);

$stars = $ep->remove($_POST["stars"]);

$levelID = $ep->remove($_POST["levelID"]);

$accountID = $ep->remove($_POST["accountID"]);

if($accountID != "" AND $gjp != ""){

	$GJPCheck = new GJPCheck();

	$gjpresult = $GJPCheck->check($gjp,$accountID);

	if($gjpresult == 1){
        $gs->updateStatusPlaying($accountID);
		if(!$gs->GetLevelBanned($accountID, "isRatingBanned")) {
			exit("-1");
		} 
		$permState = $gs->checkPermission($accountID, "actionRateStars");

		if($permState){
			if($gs->GetIsRated($levelID) == 1) {
				echo -1;
				exit();
			}
			$difficulty = $gs->getDiffFromStars($stars);

			$gs->rateLevel($accountID, $levelID, 0, $difficulty["diff"], $difficulty["auto"], $difficulty["insane"]);

			echo 1;

		}else{

			echo -1;

		}

	}else{echo -1;}

}else{echo -1;}