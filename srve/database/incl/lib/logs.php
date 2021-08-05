<?php
const LT_ACCOUNT_LOGINED = 1;
const LT_ACCOUNT_FAILED_LOGIN = 41;
const LT_ACCOUNT_UPDATED = 2;
const LT_ACCOUNT_SPECIAL_UPDATE = 4;
const LT_ACCOUNT_NEW_LEVEL = 5;
const LT_ACCOUNT_UPDATED_LEVEL = 6;
const LT_ACCOUNT_UPDATED_LEVEL_DESC = 7;
const LT_ACCOUNT_DELETED_LEVEL = 8;
const LT_ACCOUNT_NEW_COMMENT = 9;
const LT_ACCOUNT_NEW_COMMENT2 = 10;
const LT_ACCOUNT_DELETED_COMMENT = 11;
const LT_ACCOUNT_NEW_ACCCOMMENT = 12;
const LT_ACCOUNT_DELETED_ACCCOMMENT = 13;
const LT_ACCOUNT_NEW_MESSAGE = 20;
const LT_ACCOUNT_DELETED_MESSAGE = 21;
const LT_ACCOUNT_VIEW_MESSAGE = 22;
const LT_ACCOUNT_ADD_FRIEND = 23;
const LT_ACCOUNT_DELETED_FRIEND = 24;
const LT_ACCOUNT_VIEW_NEW_FRIEND = 25;
const LT_ACCOUNT_UPDATED_PROFILE = 26;
const LT_ACCOUNT_LINKED_DISCORD = 27;
const LT_ACCOUNT_FIXED_PASSWORD = 41;
const LT_ACCOUNT_USED_CRON=42;
const LT_ACCOUNT_LEVEL_DOWNLOADED = 36;
const LT_ACCOUNT_LEVEL_FAKE_RATE = 37;
const LT_ACCOUNT_OPENED_REWARDS = 39;
const LT_ACCOUNT_BUYED_ITEM = 40;//dashboard
const LT_ACCOUNT_REPORTED = 47;
const LT_ACCOUNT_BLOCKED_USER = 48;
const LT_ACCOUNT_UNBLOCKED_USER = 49;
const LT_ACCOUNT_ = 50;
///views
const LT_ACCOUNT_VIEW_LEVEL = 14;
const LT_ACCOUNT_VIEW_COMMENTS = 15;
const LT_ACCOUNT_VIEW_ACCCOMMENTS = 15;
const LT_ACCOUNT_VIEW_PROFILE = 16;
const LT_ACCOUNT_VIEW_LEVEL_PACK = 17;
const LT_ACCOUNT_VIEW_GAUNTLETS = 18;
const LT_ACCOUNT_VIEW_ACCCOMMENTS_ = 19;
const LT_ACCOUNT_GET_USER = 38;
const LT_ACCOUNT_GETTED_SONG = 46;
const LT_ACCOUNT_GETTED_MONEY_REWARD = 51;
const LT_ACCOUNT_VIEW_DAILY = 54;
const LT_ACCOUNT_VIEW_WEEKLY = 55;
//mods
const LT_MODS_REQ = 28;
const LT_MODS_NEW_SEND = 29;
const LT_MODS_NEW_RATE = 30;
const LT_MODS_NEW = 31;
const LT_MODS_DELETED_COMMENTS = 32;
const LT_MODS_DELETED_ACCCOMENTS = 33;
const LT_MODS_DELETED_LEVEL = 34;
const LT_MODS_UNRATED_LEVEL = 35;
const LT_MODS_BAN_OR_MUTE = 43;
const LT_MODS_UNBAN_OR_UNMUTE = 44;
const LT_MODS_RENAME_LEVEL = 45;
const LT_MODS_USED_DAILY = 52;
const LT_MODS_USED_WEEKLY = 53;
class logs{
	public function add($accountID, $type, $value,$special_value="")
	{ 
		$ip = "not getted";
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    }else{
	        $ip = $_SERVER['REMOTE_ADDR'];
	    }
		include __DIR__ ."/connection2.php";
		$URL = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']));
		$prepare = $db->prepare("INSERT INTO `logs`(`accountID`, `ip`, `value`, `special_value`, `url`, `type`, `timestamp`) VALUES (:accountID,:ip,:value,:special_value,:url,:type,:time)");
		$prepare->execute([':accountID'=>$accountID,
			':ip'=>$ip,
			':value'=>$value,
			':special_value' => $special_value,
			':type'=>$type,
			':time'=>time(),
			':url'=>$URL]);
	}
	public function get($accountID, $type, $LIMIT = 1, $where = "")
	{ 
		$ip = "not getted";
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    }else{
	        $ip = $_SERVER['REMOTE_ADDR'];
	    }
		include __DIR__ ."/connection2.php";
		$URL = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']));
		$prepare = $db->prepare("SELECT * FROM logs WHERE type = :type AND accountID = :accountID ".$where." ORDER BY `id` DESC LIMIT ".$LIMIT);
		$prepare->execute([":type"=>$type,':accountID'=>$accountID]);
		if($LIMIT < 2){
			return $prepare->fetch();
		}else{
			return $prepare->fetchAll();
		}
	}
}
?>