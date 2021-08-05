d<?php
class Commands {
	public function ownCommand($comment, $command, $accountID, $targetExtID){
		require_once "../lib/mainLib.php";
		$gs = new mainLib();
		$commandInComment = strtolower("!".$command);
		$commandInPerms = ucfirst(strtolower($command));
		$commandlength = strlen($commandInComment);
		if(substr($comment,0,$commandlength) == $commandInComment AND (($gs->checkPermission($accountID, "command".$commandInPerms."All") OR ($targetExtID == $accountID AND $gs->checkPermission($accountID, "command".$commandInPerms."Own"))))){
			return true;
		}
		return false;
	}
	public function doCommands($accountID, $comment, $levelID) {
		include dirname(__FILE__)."/../lib/connection.php";
		require_once "../lib/exploitPatch.php";
		require_once "../lib/mainLib.php";
		$ep = new exploitPatch();
		$gs = new mainLib();
		require_once "../sendLog.php";
		require_once "../lib/clockparser.php";
		$po = new sendLog();
		$cp = new ClockParser();
		$commentarray = explode(' ', $comment);
		$uploadDate = time();
		//LEVELINFO
		$query2 = $db->prepare("SELECT extID FROM levels WHERE levelID = :id");
		$query2->execute([':id' => $levelID]);
		$targetExtID = $query2->fetchColumn();
		//ADMIN COMMANDS
		if($commentarray[0] == '!rate' AND $gs->checkPermission($accountID, "commandRate")){
			if($gs->GetIsRated($levelID) == 1) {
				echo -1;
				exit();
			}
			$starStars = $commentarray[2];
			if($starStars == ""){
				$starStars = 0;
			}
			$starCoins = $commentarray[3];
			$starFeatured = $commentarray[4];
			$diffArray = $gs->getDiffFromName($commentarray[1]);
			$starDemon = $diffArray[1];
			$starAuto = $diffArray[2];
			$starDifficulty = $diffArray[0];
			if($starStars == 0) {
				$query3 = $db->prepare("SELECT * FROM levels WHERE levelID = :levelID LIMIT 1");
				$query3->execute([':levelID' => $levelID]);
				$level = $query3->fetchAll()[0];
				$query3 = $db->prepare("SELECT * FROM accounts WHERE accountID = :accountID LIMIT 1");
				$query3->execute([':accountID' => $accountID]);
				$account = $query3->fetchAll()[0];
				$encodedText = "**Эх, уровень больше не популярен**\n**" . $level["levelName"] . "** от " . $level["userName"] . "\n\nСнят рейт от: **" . $account["userName"] . "**";
				$po->send($encodedText);
			}
			$query = $db->prepare("UPDATE levels SET starStars=:starStars, starDifficulty=:starDifficulty, starDemon=:starDemon, starAuto=:starAuto WHERE levelID=:levelID");
			$query->execute([':starStars' => $starStars, ':starDifficulty' => $starDifficulty, ':starDemon' => $starDemon, ':starAuto' => $starAuto, ':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value2, value3, timestamp, account) VALUES ('1', :value, :value2, :levelID, :timestamp, :id)");
			$query->execute([':value' => $commentarray[1], ':timestamp' => $uploadDate, ':id' => $accountID, ':value2' => $starStars, ':levelID' => $levelID]);
			if($starFeatured != ""){
				$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :levelID, :timestamp, :id)");
				$query->execute([':value' => $starFeatured, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);	
				$query = $db->prepare("UPDATE levels SET starFeatured=:starFeatured WHERE levelID=:levelID");
				$query->execute([':starFeatured' => $starFeatured, ':levelID' => $levelID]);
			}
			if($starCoins != ""){
				$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('3', :value, :levelID, :timestamp, :id)");
				$query->execute([':value' => $starCoins, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
				$query = $db->prepare("UPDATE levels SET starCoins=:starCoins WHERE levelID=:levelID");
				$query->execute([':starCoins' => $starCoins, ':levelID' => $levelID]);
			}
			return true;
		}
		if($commentarray[0] == '!mod_ask' AND $gs->checkPermission($accountID, "actionSetStars")){
			$po->sendMod("<@&599835555722821632> <@&599835429801295872> <@&599827464893497345> - кто-то не может оценить уровень, может вы сможете?","Модерация кажись застряла на этом уровне `".$levelID."`. Пожалуйста, обсудите в мод чате (хотя вы в нем находитесь) какую оценку этому уровню давать.");
			return true;
		}
		if($commentarray[0] == '!unmute' AND $gs->checkPermission($accountID, "toolModactions")) {	
			$userName = $commentarray[1];
			$extID=$gs->getAccountIDFromName($userName);
			$userName="не определено";
			if($extID == 0){
				$query = $db->prepare("SELECT extID FROM users WHERE extID=:id");
				$query->execute([':id'=>$userName]);
				if($query->rowCount() < 1){
					return false;
				}
				$extID  = $query->fetch()[0];
				$userName = $gs->getAccountName($extID);
			}else{
				$userName = $gs->getAccountName($extID);
			}
			$query = $db->prepare("SELECT * FROM usersBan WHERE extID=:id AND type=2");
			$query->execute([':id'=>$extID]);
			if($query->rowCount() < 1){
				return false;
			}
			$lolBan = $db->prepare("UPDATE users SET isMuted=0 WHERE extID=:accountID");
			$lolBan->execute([':accountID' => $extID]);
			$po->send3("<:attention:599932468568522762> **".$userName."** был разбанен.");
			if($query->rowCount() > 0){
				$lolBan = $db->prepare("UPDATE usersBan SET unix=0 WHERE extID=:accountID AND type=2");
				$lolBan->execute([':accountID' => $extID]);
			}
			return true;
		}
		if($commentarray[0] == '!mute' AND $gs->checkPermission($accountID, "toolModactions")) {	
			$userName = $commentarray[1];
			$seconds = $cp->parse($commentarray[2]);
			$reason = $commentarray[3];
			if(empty($seconds) OR is_numeric($seconds) != true OR $seconds < 1){
				$seconds=0;
			}
			if(empty($reason)){
				$reason = "empty";
			}
			$extID=$gs->getAccountIDFromName($userName);
			$userName="не определено";
			if($extID == 0){
				$query = $db->prepare("SELECT extID FROM users WHERE extID=:id");
				$query->execute([':id'=>$userName]);
				if($query->rowCount() < 1){
					return false;
				}
				$extID  = $query->fetch()[0];
				$userName = $gs->getAccountName($extID);
			}else{
				$userName = $gs->getAccountName($extID);
			}
			if($gs->isMultiBan($extID,2) == 1){
				return false;
			}
			$seconds2 = $seconds." секунд";
			if($seconds == 0){
				$seconds2 = "навсегда";
			}
			$query = $db->prepare("INSERT INTO `usersBan`(`extID`, `type`, `unix`, `secondsForBan`, `reason`) VALUES (:extID,2,:unix,:s,:r)");
			$query->execute([':extID'=>$extID,':unix'=>time(),':s'=>$seconds,':r'=>$reason]);
			$po->send3("<:attention:599932468568522762> **".$userName."** был временно забанен на **".$seconds2."** по причине: **".$reason."**.");
			$reason .= " (banned by accountID: ".$accountID.")";
			$lolBan = $db->prepare("UPDATE users SET isMuted=1 WHERE extID=:accountID");
			$lolBan->execute([':accountID' => $extID]);
			return true;
		}
		if($commentarray[0] == '!unulock' AND $gs->checkPermission($accountID, "isSuperRules")) {
			if($accountID == 3200 OR $accountID == 2576) {			
				$query = $db->prepare("UPDATE levels SET isSettingLevel='0' WHERE levelID=:levelID");
				$query->execute([':levelID' => $levelID]);
				return true;
			}
			return false;
		}
		if($commentarray[0] == '!ulock' AND $gs->checkPermission($accountID, "isSuperRules")) {
			if($accountID == 3200 OR $accountID == 2576) {			
				$query = $db->prepare("UPDATE levels SET isSettingLevel='1' WHERE levelID=:levelID");
				$query->execute([':levelID' => $levelID]);
				return true;
			}
			return false;
		}

		if($commentarray[0] == '!unlock' AND $gs->checkPermission($accountID, "isCronFunction")) {
			if($gs->GetIsSetting($levelID) == 1) {
				return false;
			}
			$query = $db->prepare("UPDATE levels SET isRateLevel='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			return true;
		}
		if($commentarray[0] == '!lock' AND $gs->checkPermission($accountID, "isCronFunction")) {
			if($gs->GetIsSetting($levelID) == 1) {
				return false;
			//	exit();
			}
			$query = $db->prepare("UPDATE levels SET isRateLevel='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			return true;
		}
		if($commentarray[0] == '!unrate' AND $gs->checkPermission($accountID, "commandRate")) {
			if($gs->GetIsRated($levelID) == 1) {
				echo -1;
				exit();
			}
			$query3 = $db->prepare("SELECT * FROM levels WHERE levelID = :levelID");
			$query3->execute([':levelID' => $levelID]);
			$level = $query3->fetchAll()[0];
			if($query3->fetchAll()[0]["starStars"] >= 10) {
				$newStars = 9;
			} else {
				$newStars = $query3->fetchAll()[0]["starStars"];
			}
			switch($newStars) {
				case 1:
					$diff = 0;
					break;
				case 2:
					$diff = 10;
					break;
				case 3:
					$diff = 20;
					break;
				case 4:
					$diff = 30;
					break;
				case 5:
					$diff = 30;
					break;
				case 6:
					$diff = 40;
					break;
				case 7:
					$diff = 40;
					break;
				case 8:
					$diff = 50;
					break;
				case 9:
					$diff = 50;
					break;
				case 10:
					$diff = 50;
					break;
			}
			$query4 = $db->prepare("UPDATE `levels` SET starDifficulty=:diff, starDemon=0, starAuto=0, starStars=0, starCoins=0, starFeatured=0, starHall=0, starEpic=0, starDemonDiff=0 WHERE levelID=:levelID");
			$query4->execute([':diff' => $diff, ':levelID' => $levelID]);
			$query5 = $db->prepare("SELECT * FROM accounts WHERE accountID = :accountID LIMIT 1");
			$query5->execute([':accountID' => $accountID]);
			$account = $query5->fetchAll()[0];
			$encodedText = "**Эх, уровень больше не популярен**\n**" . $level["levelName"] . "** от " . $level["userName"] . "\n\nСнят рейт от: **" . $account["userName"] . "**";
			$po->send($encodedText);
			/*$ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/v6/webhooks/443784042886856704/bUvVFjFpvY-cXbMj-Mai-S4vtsTkV4mZeBim8BKMpx4eFfM-PQ7MHEhNWlo4wyzndyuB");
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=GDYS&content=" . $encodedText);
	        curl_exec($ch);
	        curl_close($ch);*/
			return true;
		}
		if($commentarray[0] == '!feature' AND $gs->checkPermission($accountID, "commandFeature")){
			if($gs->GetIsRated($levelID) == 1) {
				echo -1;
				exit();
			}
			$query = $db->prepare("UPDATE levels SET starFeatured='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($commentarray[0] == '!epic' AND $gs->checkPermission($accountID, "commandEpic")){
			if($gs->GetIsRated($levelID) == 1) {
				echo -1;
				exit();
			}
			$query = $db->prepare("UPDATE levels SET starEpic='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('4', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($commentarray[0] == '!unepic' AND $gs->checkPermission($accountID, "commandUnepic")){
			if($gs->GetIsRated($levelID) == 1) {
				echo -1;
				exit();
			}
			$query = $db->prepare("UPDATE levels SET starEpic='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('4', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
				return true;
		}
		if($commentarray[0] == '!verifycoins' AND $gs->checkPermission($accountID, "commandVerifycoins")){
			if($gs->GetIsRated($levelID) == 1) {
				echo -1;
				exit();
			}
			$query = $db->prepare("UPDATE levels SET starCoins='1' WHERE levelID = :levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($commentarray[0] == '!setorigin' AND $gs->checkPermission($accountID, "commandSetOrigin"))
		{
		    $query = $db->prepare("UPDATE levels SET original = :orig WHERE levelID = :levelID");
		    $query->execute([':levelID' => $levelID, ':orig' => $commentarray[1]]);
		    $query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, value2, account) VALUES ('20', :value, :levelID, :timestamp, :value2, :id)");
		    $query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':value2' => $commentarray[1], ':levelID' => $levelID]);
		    return true;
		}
		if($commentarray[0] == '!daily' AND $gs->checkPermission($accountID, "commandDaily")){
			$query = $db->prepare("SELECT count(*) FROM dailyfeatures WHERE levelID = :level AND type = 0");
				$query->execute([':level' => $levelID]);
			if($query->fetchColumn() != 0){
				return false;
			}
			$query = $db->prepare("SELECT timestamp FROM dailyfeatures WHERE timestamp >= :tomorrow AND type = 0 ORDER BY timestamp DESC LIMIT 1");
			$query->execute([':tomorrow' => strtotime("tomorrow 00:00:00")]);
			if($query->rowCount() == 0){
				$timestamp = strtotime("tomorrow 00:00:00");
			}else{
				$timestamp = $query->fetchColumn() + 86400;
			}
			$query = $db->prepare("INSERT INTO dailyfeatures (levelID, timestamp, type) VALUES (:levelID, :uploadDate, 0)");
				$query->execute([':levelID' => $levelID, ':uploadDate' => $timestamp]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account, value2, value4) VALUES ('5', :value, :levelID, :timestamp, :id, :dailytime, 0)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID, ':dailytime' => $timestamp]);
			return true;
		}
		if($commentarray[0] == '!weekly' AND $gs->checkPermission($accountID, "commandWeekly")){
			$query = $db->prepare("SELECT count(*) FROM dailyfeatures WHERE levelID = :level AND type = 1");
			$query->execute([':level' => $levelID]);
			if($query->fetchColumn() != 0){
				return false;
			}
			$query = $db->prepare("SELECT timestamp FROM dailyfeatures WHERE timestamp >= :tomorrow AND type = 1 ORDER BY timestamp DESC LIMIT 1");
				$query->execute([':tomorrow' => strtotime("next monday")]);
			if($query->rowCount() == 0){
				$timestamp = strtotime("next monday");
			}else{
				$timestamp = $query->fetchColumn() + 604800;
			}
			$query = $db->prepare("INSERT INTO dailyfeatures (levelID, timestamp, type) VALUES (:levelID, :uploadDate, 1)");
			$query->execute([':levelID' => $levelID, ':uploadDate' => $timestamp]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account, value2, value4) VALUES ('5', :value, :levelID, :timestamp, :id, :dailytime, 1)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID, ':dailytime' => $timestamp]);
			return true;
		}
		if($commentarray[0] == '!delete'){
		    $commentarray[0]="!del";
		}
		if($commentarray[0] == '!delet'){
		    $commentarray[0]="!del";
		}
		if($commentarray[0] == '!del' AND $gs->checkPermission($accountID, "commandDelete")){
			if(!is_numeric($levelID)){
				return false;
			}
			if($gs->GetIsRated($levelID) == 1) {
				echo -1;
				exit();
			}
			$query = $db->prepare("DELETE from levels WHERE levelID=:levelID LIMIT 1");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('6', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			if(file_exists(dirname(__FILE__)."../../data/levels/$levelID")){
				rename(dirname(__FILE__)."../../data/levels/$levelID",dirname(__FILE__)."../../data/levels/deleted/$levelID");
			}
			return true;
		}
		if($commentarray[0] == '!setacc' AND $gs->checkPermission($accountID, "commandSetacc")){
			$query = $db->prepare("SELECT accountID FROM accounts WHERE userName = :userName OR accountID = :userName LIMIT 1");
			$query->execute([':userName' => $commentarray[1]]);
			if($query->rowCount() == 0){
				return false;
			}
			$targetAcc = $query->fetchColumn();
			//var_dump($result);
			$query = $db->prepare("SELECT userID FROM users WHERE extID = :extID LIMIT 1");
			$query->execute([':extID' => $targetAcc]);
			$userID = $query->fetchColumn();
			$query = $db->prepare("UPDATE levels SET extID=:extID, userID=:userID, userName=:userName WHERE levelID=:levelID");
			$query->execute([':extID' => $targetAcc["accountID"], ':userID' => $userID, ':userName' => $commentarray[1], ':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('7', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => $commentarray[1], ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($commentarray[0] == '!ban' AND $gs->checkPermission($accountID, "actionBanMod")){

			$extID=$gs->getAccountIDFromName($commentarray[1]);
			$userName="не определено";
			if($extID == 0){
				$query = $db->prepare("SELECT extID FROM users WHERE extID=:id");
				$query->execute([':id'=>$commentarray[1]]);
				if($query->rowCount() < 1){
					return false;
				}
				$extID  = $query->fetch()[0];
				$userName = $gs->getAccountName($extID);
			}else{
				$userName = $gs->getAccountName($extID);
			}
			$query = $db->prepare("UPDATE users SET isBanned='1' WHERE userName=:userID");
			$query->execute([':userID' => $userName]);
			$po->send3("<:attention:599932468568522762> **".$userName."** был забанен в **топе**");
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :userID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':userID' => $userID]);
			return true;
		}

		if($commentarray[0] == '!uncpban' AND $gs->checkPermission($accountID, "actionBanMod")){
			//$po->send2("[object Object]");
			$extID=$gs->getAccountIDFromName($commentarray[1]);
			$userName="не определено";
			if($extID == 0){
				$query = $db->prepare("SELECT extID FROM users WHERE extID=:id");
				$query->execute([':id'=>$commentarray[1]]);
				if($query->rowCount() < 1){
					return false;
				}
				$extID  = $query->fetch()[0];
				$userName = $gs->getAccountName($extID);
			}else{
				$userName = $gs->getAccountName($extID);
			}
			$query = $db->prepare("UPDATE users SET isCreatorBanned=0 WHERE userName=:userID");
			$query->execute([':userID' => $userName]);
			$po->send3("<:attention:599932468568522762> **".$userName."** был разбанен в **креатор топе**.");
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :userID, :timestamp, :id)");
			$query->execute([':value' => $commentarray[1]."", ':timestamp' => $uploadDate, ':id' => $accountID, ':userID' => $userID]);
			return true;
		}
		if($commentarray[0] == '!cpban' AND $gs->checkPermission($accountID, "actionBanMod")){
			//$po->send2("[object Object]");
			$reason = "не указана";
			if($commentarray[2] == "copyLevel_"){
				$reason = "копирование чужих уровней";
			}
			if($commentarray[2] == "copyLevel"){
				$reason = "копирование чужих уровней c оригинала";
			}
			if($commentarray[2] == "sw1"){
				$reason = "секрет вей.";
			}

			$extID=$gs->getAccountIDFromName($commentarray[1]);
			$userName="не определено";
			if($extID == 0){
				$query = $db->prepare("SELECT extID FROM users WHERE extID=:id");
				$query->execute([':id'=>$commentarray[1]]);
				if($query->rowCount() < 1){
					return false;
				}
				$extID  = $query->fetch()[0];
				$userName = $gs->getAccountName($extID);
			}else{
				$userName = $gs->getAccountName($extID);
			}
			$query = $db->prepare("UPDATE users SET isCreatorBanned=1 WHERE userName=:userID");
			$query->execute([':userID' => $userName]);
			$po->send3("<:attention:599932468568522762> **".$userName."** был забанен в **креатор топе**. Причина: **".$reason."**.");
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :userID, :timestamp, :id)");
			$query->execute([':value' => $commentarray[1]."", ':timestamp' => $uploadDate, ':id' => $accountID, ':userID' => $userID]);
			return true;
		}
		if($commentarray[0] == '!unban' AND $gs->checkPermission($accountID, "actionBanMod")){
			$extID=$gs->getAccountIDFromName($commentarray[1]);
			$userName="не определено";
			if($extID == 0){
				$query = $db->prepare("SELECT extID FROM users WHERE extID=:id");
				$query->execute([':id'=>$commentarray[1]]);
				if($query->rowCount() < 1){
					return false;
				}
				$extID  = $query->fetch()[0];
				$userName = $gs->getAccountName($extID);
			}else{
				$userName = $gs->getAccountName($extID);
			}
			$query = $db->prepare("UPDATE users SET isBanned='0' WHERE userName=:userID");
			$query->execute([':userID' => $userName]);
			$po->send3("<:attention:599932468568522762> **".$userName."** был разбанен в **топе**");
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('2', :value, :userID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':userID' => $userID]);
			return true;
		}

		
		//NON-ADMIN COMMANDS
		if($this->ownCommand($comment, "rename", $accountID, $targetExtID)){
			$name = $ep->remove(str_replace("!rename ", "", $comment));
			$query = $db->prepare("UPDATE levels SET levelName=:levelName WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID, ':levelName' => $name]);
			$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('8', :value, :timestamp, :id, :levelID)");
			$query->execute([':value' => $name, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($this->ownCommand($comment, "pass", $accountID, $targetExtID)){
			$pass = $ep->remove(str_replace("!pass ", "", $comment));
			if(is_numeric($pass)){
				$pass = sprintf("%06d", $pass);
				if($pass == "000000"){
					$pass = "";
				}
				$pass = "1".$pass;
				$query = $db->prepare("UPDATE levels SET password=:password WHERE levelID=:levelID");
				$query->execute([':levelID' => $levelID, ':password' => $pass]);
				$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('9', :value, :timestamp, :id, :levelID)");
				$query->execute([':value' => $pass, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
				return true;
			}
		}
		if($this->ownCommand($comment, "description", $accountID, $targetExtID)){
			$desc = base64_encode($ep->remove(str_replace("!description ", "", $comment)));
			$query = $db->prepare("UPDATE levels SET levelDesc=:desc WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID, ':desc' => $desc]);
			$query = $db->prepare("INSERT INTO modactions (type, value, timestamp, account, value3) VALUES ('13', :value, :timestamp, :id, :levelID)");
			$query->execute([':value' => $desc, ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($this->ownCommand($comment, "public", $accountID, $targetExtID)){
			$query = $db->prepare("UPDATE levels SET unlisted='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('12', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($this->ownCommand($comment, "unlist", $accountID, $targetExtID)){
			$query = $db->prepare("UPDATE levels SET unlisted='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('12', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		/*
		if($this->ownCommand($comment, "sharecp", $accountID, $targetExtID)){
			$query = $db->prepare("SELECT userID FROM users WHERE userName = :userName ORDER BY isRegistered DESC LIMIT 1");
			$query->execute([':userName' => $commentarray[1]]);
			$targetAcc = $query->fetchColumn();
			//var_dump($result);
			$query = $db->prepare("INSERT INTO cpshares (levelID, userID) VALUES (:levelID, :userID)");
			$query->execute([':userID' => $targetAcc, ':levelID' => $levelID]);
			$query = $db->prepare("UPDATE levels SET isCPShared='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('11', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => $commentarray[1], ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($this->ownCommand($comment, "ldm", $accountID, $targetExtID)){
			$query = $db->prepare("UPDATE levels SET isLDM='1' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('14', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "1", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}
		if($this->ownCommand($comment, "unldm", $accountID, $targetExtID)){
			$query = $db->prepare("UPDATE levels SET isLDM='0' WHERE levelID=:levelID");
			$query->execute([':levelID' => $levelID]);
			$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account) VALUES ('14', :value, :levelID, :timestamp, :id)");
			$query->execute([':value' => "0", ':timestamp' => $uploadDate, ':id' => $accountID, ':levelID' => $levelID]);
			return true;
		}*/
		return false;
	}
public function doProfileCommands($accountID, $command){
		include dirname(__FILE__)."/../lib/connection.php";
		require_once "../lib/exploitPatch.php";
		require_once "../lib/mainLib.php";
		$ep = new exploitPatch();
		$gs = new mainLib();
		if(substr($command, 0, 8) == '!discord'){
			if(substr($command, 9, 6) == "accept"){
				$query = $db->prepare("UPDATE accounts SET discordID = discordLinkReq, discordLinkReq = '0' WHERE accountID = :accountID AND discordLinkReq <> 0");
				$query->execute([':accountID' => $accountID]);
				$query = $db->prepare("SELECT discordID, userName FROM accounts WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				$account = $query->fetch();
				$gs->sendDiscordPM($account["discordID"], "Ваш игровой аккаунт " . $account["userName"] . " был привязан!");
				return true;
			}
			if(substr($command, 9, 4) == "deny"){
				$query = $db->prepare("SELECT discordLinkReq, userName FROM accounts WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				$account = $query->fetch();
				$gs->sendDiscordPM($account["discordLinkReq"], "Ваш запрос на привязку ирового аккаунта " . $account["userName"] . " был отменён!");
				$query = $db->prepare("UPDATE accounts SET discordLinkReq = '0' WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				return true;
			}
			if(substr($command, 9, 6) == "unlink"){
				$query = $db->prepare("SELECT discordID, userName FROM accounts WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				$account = $query->fetch();
				$gs->sendDiscordPM($account["discordID"], "Ваш игровой аккаунт " . $account["userName"] . " был отвязан!");
				$query = $db->prepare("UPDATE accounts SET discordID = '0' WHERE accountID = :accountID");
				$query->execute([':accountID' => $accountID]);
				return true;
			}
		}
		return false;
	}
}
?>