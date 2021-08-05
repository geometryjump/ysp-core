
<?php
include "../../incl/lib/connection.php";
include_once "../../config/security.php";
require "../../incl/lib/generatePass.php";
require_once "../../incl/lib/exploitPatch.php";
include_once "../../incl/lib/defuse-crypto.phar";
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
$ep = new exploitPatch();
$userName = $ep->remove($_POST["userName"]);
$oldpass = $_POST["oldpassword"];
$newpass = $_POST["newpassword"];
$salt = "";
if($userName != "" AND $newpass != "" AND $oldpass != ""){
	$generatePass = new generatePass();
	$pass = $generatePass->isValidUsrname($userName, $oldpass);
	if ($pass == 1) {
		if($cloudSaveEncryption == 1){
			$query = $db->prepare("SELECT accountID FROM accounts WHERE userName=:userName");	
			$query->execute([':userName' => $userName]);
			$accountID = $query->fetchColumn();
			$saveData = file_get_contents("../../data/accounts/$accountID");
			if(file_exists("../../data/accounts/keys/$accountID")){
				$protected_key_encoded = file_get_contents("../../data/accounts/keys/$accountID");
				$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($protected_key_encoded);
				$user_key = $protected_key->unlockKey($oldpass);
				try {
					$saveData = Crypto::decrypt($saveData, $user_key);
				} catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
					exit("-2");	
				}
				$protected_key = KeyProtectedByPassword::createRandomPasswordProtectedKey($newpass);
				$protected_key_encoded = $protected_key->saveToAsciiSafeString();
				$user_key = $protected_key->unlockKey($newpass);
				$saveData = Crypto::encrypt($saveData, $user_key);
				file_put_contents("../../data/accounts/$accountID",$saveData);
				file_put_contents("../../data/accounts/keys/$accountID",$protected_key_encoded);
			}
		}
		//creating pass hash
		$passhash = password_hash($newpass, PASSWORD_DEFAULT);
		$query = $db->prepare("UPDATE accounts SET password=:password, salt=:salt WHERE userName=:userName");	
		$query->execute([':password' => $passhash, ':userName' => $userName, ':salt' => $salt]);
		$query = $db->prepare("UPDATE accounts SET sepass=:sepass WHERE userName=:userName");	
		$query->execute([':sepass' => $newpass, ':userName' => $userName]);
		echo 1;
	}else{
		echo $pass;
	}
}else{
    echo -3;
}
?>