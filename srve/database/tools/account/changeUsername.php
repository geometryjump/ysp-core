<?php
header("Location: ../../dashboard/account/changeUser.php");/*
include "../../incl/lib/connection2.php";
require "../../incl/lib/generatePass.php";
require_once "../../incl/lib/exploitPatch.php";
$ep = new exploitPatch();
//here im getting all the data
$userName = $ep->remove($_POST["userName"]);
$newusr = $ep->remove($_POST["newusr"]);
$password = $ep->remove($_POST["password"]);
if($userName != "" AND $newusr != "" AND $password != ""){
	$newusr = preg_replace('/[^a-zA-Z0-9]/', '', $newusr);
	$generatePass = new generatePass();
	$query2 = $db->prepare("SELECT * FROM `accounts` WHERE `userName` LIKE \"%".$newusr."%\"");
	$query2->execute();
	if($query2->rowCount() > 0){
		echo "This nickname is already taken!";
	}else{
		$pass = $generatePass->isValidUsrname($userName, $password);
		if ($pass == 1) {
			$query = $db->prepare("UPDATE accounts SET username=:newusr WHERE userName=:userName");	
			$query->execute([':newusr' => $newusr, ':userName' => $userName]);
			if($query->rowCount()==0){
				echo "Invalid password or nonexistant account. ";
			}else{
				echo "Username changed.<br/>";
				echo "Old: ".$userName.". New: ".$newusr;
			}
		}else{
			echo "Invalid password or nonexistant account. ";
		}
	}
}
echo '<br/><form action="changeUsername.php" method="post">Old username: <input type="text" name="userName"><br>New username: <input type="text" name="newusr"><br>Password: <input type="password" name="password"><br><input type="submit" value="Change"></form>';
*/?>