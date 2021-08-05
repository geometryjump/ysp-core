<?php
//error_reporting(0);
chdir(dirname(__FILE__));
include "../lib/connection2.php";
require_once "../lib/polling.php";
$polling = new Polling();
require_once "../lib/GJPCheck.php";
require_once "../lib/exploitPatch.php";
$ep = new exploitPatch();
require_once "../lib/mainLib.php";
$gs = new mainLib();
require_once "../sendLog.php";
$pol = new sendLog();
$gjp = $ep->remove($_POST["gjp"]);
$stars = $ep->remove($_POST["stars"]);
$feature = $ep->remove($_POST["feature"]);
$levelID = $ep->remove($_POST["levelID"]);
$accountID = $ep->remove($_POST["accountID"]);
if($accountID != "" AND $gjp != ""){
    $GJPCheck = new GJPCheck();
    $gjpresult = $GJPCheck->check($gjp,$accountID);
    if($gjpresult == 1){
        $gs->updateStatusPlaying($accountID);
        $permState = $gs->checkPermission($accountID, "actionRateStars");
        $rateState = $gs->checkPermission($accountID, "actionSetStars");
        if($rateState){
            if($gs->GetIsRated($levelID) == 1) {
				echo -1;
				exit();
            }
            if(!$rateState) {
                echo -1;
				exit();
            }
            $difficulty = $gs->getDiffFromStars($stars);
            $gs->rateLevel($accountID, $levelID, $stars, $difficulty["diff"], $difficulty["auto"], $difficulty["demon"]);
            $gs->featureLevel($accountID, $levelID, $feature);
            $gs->verifyCoinsLevel($accountID, $levelID, 1);
            $query = $db->prepare("UPDATE modrate SET isRate = 1 WHERE levelID=$levelID");
            $query->execute();
            echo 1;
            $icons=true;
            if($icons) {
                $diff = "0";
                switch($stars) {
                    case 1:
                        $diff = " <:icon_auto:599955233803862016>1<:star:599955234453717021>  ";
                        break;
                    case 2:
                        $diff = " <:easy:599955234076491776> 2<:star:599955234453717021>  ";
                        break;
                    case 3:
                        $diff = " <:normal:599955233543684111> 3<:star:599955234453717021>  ";
                        break;
                    case 4:
                        $diff = " <:hard:599955233900068864> 4<:star:599955234453717021>  ";
                        break;
                    case 5:
                        $diff = " <:hard:599955233900068864> 5<:star:599955234453717021>  ";
                        break;
                    case 6:
                        $diff = " <:harder:599955233962983424> 6<:star:599955234453717021>  ";
                        break;
                    case 7:
                        $diff = " <:harder:599955233962983424> 7<:star:599955234453717021>  ";
                        break;
                    case 8:
                        $diff = " <:insane:599955234219098142> 8<:star:599955234453717021>  ";
                        break;
                    case 9:
                        $diff = " <:insane:599955234219098142> 9<:star:599955234453717021>  ";
                        break;
                    case 10:
                         $diff = " <:Demon:599955234319761417>10<:star:599955234453717021>  ";
                }
                if($feature == 1) {
                    $diff = $diff . "(FEATURED) нет иконки братишка....";
                }
            }
            $query = $db->prepare("SELECT * FROM levels WHERE levelID = :levelID");
            $query->execute([':levelID' => $levelID]);
            $level = $query->fetchAll()[0];

            if($gs->checkPermission($level["extID"], "actionSetStars")){
                $i = 3;
                if($feature == 1){
                    $i = 6;
                }
                for($j = 1; $j < $i + 1; $j++){
                    $query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, timestamp, account) VALUES ('50', :value, :value2, :levelID, :timestamp, :id)");
                    $query->execute([':value' => "level_rated", ':timestamp' => time(), ':id' => $accountID, ':value2' => $stars, ':levelID' => $levelID]);
                }
            }
            
            $query = $db->prepare("SELECT * FROM accounts WHERE accountID = :accountID");
            $query->execute([':accountID' => $accountID]);
            $account = $query->fetchAll()[0];
            $polling->push("levelRated", array('levelName' => $level["levelName"], 'levelAuthor' => $level["userName"]));
            $encodedText = "Ykisl&Sky оценил это! <:success:599955233992605706> \n**" . $level["levelName"] . "** от **" . $level["userName"] . "**\n
Сложность:" . $diff . "\nОценка от **" . $account["userName"] . "**";
            $pol->send($encodedText);
            $query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, timestamp, account) VALUES ('50', :value, :value2, :levelID, :timestamp, :id)");
            $query->execute([':value' => "rated", ':timestamp' => time(), ':id' => $accountID, ':value2' => $stars, ':levelID' => $levelID]);
        }
        else if($permState) {
            if(!$permState) {
                echo -1;
                exit();
            }
            $query = $db->prepare("SELECT * FROM modrate WHERE levelID=:levelID LIMIT 1");
            $query->execute([':levelID' => $levelID]);
            $dats = $query->fetchAll();
            $data = $query->rowCount();
            
            $dass = 1;
            $query = $db->prepare("SELECT * FROM levels WHERE levelID = :levelID LIMIT 1");
            $query->execute([':levelID' => $levelID]);
            $level = $query->fetchAll()[0];
            if($level["starStars"] > 0){
            	exit("-1");
            }
            $query = $db->prepare("SELECT * FROM accounts WHERE accountID = :accountID LIMIT 1");
            $query->execute([':accountID' => $accountID]);
            $account = $query->fetchAll()[0];
            if($data < 1) {
                $diff = "0";
                switch($stars) {
                    case 1:
                        $diff = " <:icon_auto:599955233803862016>1<:star:599955234453717021>  ";
                        break;
                    case 2:
                        $diff = " <:easy:599955234076491776> 2<:star:599955234453717021>  ";
                        break;
                    case 3:
                        $diff = " <:normal:599955233543684111> 3<:star:599955234453717021>  ";
                        break;
                    case 4:
                        $diff = " <:hard:599955233900068864> 4<:star:599955234453717021>  ";
                        break;
                    case 5:
                        $diff = " <:hard:599955233900068864> 5<:star:599955234453717021>  ";
                        break;
                    case 6:
                        $diff = " <:harder:599955233962983424> 6<:star:599955234453717021>  ";
                        break;
                    case 7:
                        $diff = " <:harder:599955233962983424> 7<:star:599955234453717021>  ";
                        break;
                    case 8:
                        $diff = " <:insane:599955234219098142> 8<:star:599955234453717021>  ";
                        break;
                    case 9:
                        $diff = " <:insane:599955234219098142> 9<:star:599955234453717021>  ";
                        break;
                    case 10:
                         $diff = " <:Demon:599955234319761417>10<:star:599955234453717021>  ";
                         break;
                }
                if($feature == 1) {
                    $diff = $diff . "(FEATURED) нет иконки братишка....";
                }
                $query = $db->prepare("INSERT INTO modrate (accountID, levelID, difficulty, feature, sendmod, isRate) VALUES (:accountID, :levelID, :difficulty, :feature, :sendmod, :isRate)");
                $query->execute([':accountID' => $accountID, ':levelID' => $levelID, 'difficulty' => $stars, ':feature' => $feature, ':sendmod' => '1', ':isRate' => '0']);
                $encodedText = "<:success:599955233992605706> " . $level["levelName"] . "** от **" . $level["userName"] . " был отправлен на проверку Администрации!\nОценка:" . $diff . "\nОтправитель **" . $account["userName"] . "**";
                $pol->sendSend($encodedText);
                //$pol->send($encodedText);
            $query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, timestamp, account) VALUES ('50', :value, :value2, :levelID, :timestamp, :id)");
            $query->execute([':value' => "send data", ':timestamp' => time(), ':id' => $accountID, ':value2' => $stars, ':levelID' => $levelID]);
            } else {
                foreach($dats as &$server) {
                    if($dass == 1) $dass = $server['sendmod'] + 1;
                }
                $query = $db->prepare("UPDATE modrate SET difficulty=:difficulty, feature=:feature, sendmod=:sendmod, isRate=:isRate WHERE levelID=:levelID");
                $query->execute([':difficulty' => $stars, ':feature' => $feature, ':sendmod' => $dass, ':isRate' => '0', ':levelID' => $levelID]);
                $query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, timestamp, account) VALUES ('50', :value, :value2, :levelID, :timestamp, :id)");
            	$query->execute([':value' => "send2", ':timestamp' => time(), ':id' => $accountID, ':value2' => $stars, ':levelID' => $levelID]);
            	exit("1");
            }
            echo 1;
        }else{
            echo -1;
        }
    }else{echo -1;}
}else{echo -1;}
?>