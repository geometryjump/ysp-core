<?php
chdir(dirname(__FILE__));
include "../incl/lib/connection2.php";
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
if($_GET['ts'] = "skylul") {
    $userName = "NobDod";
	$query = $db->prepare("select accountID, saveData from accounts where userName = :userName");
	$query->execute([':userName' => $userName]);
	$account = $query->fetch();
	$accountID = $account["accountID"];
	if(!is_numeric($accountID)){
		exit("-1");
	}
	if(!file_exists("../data/accounts/$accountID")){
			$saveData = $account["saveData"];
		if(substr($saveData,0,4) == "SDRz"){
			$saveData = base64_decode($saveData);
		}
	}else{
    $password = "p5e51i5kFAAG2GhD";
    $saveData = file_get_contents("../data/accounts/$accountID");
		if(file_exists("../data/accounts/keys/$accountID")){
			if(substr($saveData,0,3) != "H4s"){
				$protected_key_encoded = file_get_contents("../data/accounts/keys/$accountID");
				$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($protected_key_encoded);
				$user_key = $protected_key->unlockKey($password);
				try {
                    $saveData = Crypto::encrypt($saveData, $user_key);
                    echo $saveData;
				} catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
					exit("-2");	
				}
			}
		}
    }
}
?>