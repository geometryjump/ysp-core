<?
include "../../incl/lib/connection.php";
require_once "../../incl/lib/mainLib.php";
$gs = new mainLib();
$stars_24h_max = $gs->GetStarsForAntiCheat(1);
$stars_24h_min = $gs->GetStarsForAntiCheat(11);
$newstars_24h_max = $gs->GetStarsForAntiCheat(2);
$newstars_24h_min = $gs->GetStarsForAntiCheat(22);
$starsgain = array();
	$time = time() - 86400;
$query = $db->prepare("SELECT * FROM actions WHERE type = '9' AND timestamp > :time");
$query->execute([':time' => $time]);
$result = $query->fetchAll();

foreach($result as &$gain){
    if(!empty($starsgain[$gain["account"]])){
        $starsgain[$gain["account"]] += $gain["value"];
    }else{
        $starsgain[$gain["account"]] = $gain["value"];
    }
}

arsort($starsgain);

foreach ($starsgain as $userID => $stars){
    $query = $db->prepare("SELECT userName, isBanned FROM users WHERE userID = :userID");
    $query->execute([':userID' => $userID]);
    $userinfo = $query->fetchAll()[0];
    $userinfs = $query->fetchAll();
    $usernames = $gs->getUserName($userID);
    $extID = $gs->getExtID($userID);
    $query = $db->prepare("SELECT * FROM users WHERE userName = :user");
	$query->execute([':user' => $usernames]);
    $dataplayer = $query->fetchAll();
    $newstars = "0";
    foreach($dataplayer as &$starsload) {
        if($newstars == "0") {$newstars = $starsload['stars'];}
    }
	
    if($userinfo["isBanned"] == 0){ 
        if($stars > $stars_24h_max or $stars < $stars_24h_min
        or $newstars > $newstars_24h_max or $newstars < $newstars_24h_min) {
			if($userID == 8891){
				echo "no ban";
			}else{
				$query = $db->prepare("UPDATE users SET isBanned = '1' WHERE userID = :id");
				$query->execute([':id' => $userID]);
				echo ":d";
			}
        } else {
			echo ":n";
        }
		echo $userID."|";
    }
}
?>