<?php
chdir(dirname(__FILE__));
include "../incl/lib/connection.php";
require_once "../incl/lib/exploitPatch.php";
require_once "../incl/lib/GJPCheck.php";
$ep = new exploitPatch();
include "../incl/sendLog.php";
$sl = new sendLog();
include "../incl/lib/mainLib.php";
$gs = new mainLib();

$modtable = "";
$modtable2 = "";
$modtable3 = "";
$modtable4 = "";
$accounts = implode(",",$gs->getAccountsWithPermission("toolModactions"));
if($accounts == ""){
	exit();
}
$query = $db->prepare("SELECT accountID, userName FROM accounts WHERE accountID IN ($accounts) ORDER BY userName ASC");
$query->execute();
$result = $query->fetchAll();
$row4 = 0;
$row3 = 0;
$row2 = 0;
$row = 0;
foreach($result as &$mod){
	$color = "";
	$query = $db->prepare("SELECT lastPlayed FROM users WHERE extID = :id");
	$query->execute([':id' => $mod["accountID"]]);
	$time = $query->fetchColumn();
	$time2 = $time;
	$query = $db->prepare("SELECT count(*) FROM modactions WHERE account = :id");
	$query->execute([':id' => $mod["accountID"]]);
	$actionscount = $query->fetchColumn();
	$query = $db->prepare("SELECT count(*) FROM modactions WHERE account = :id AND type = '1'");
	$query->execute([':id' => $mod["accountID"]]);
	$lvlcount = $query->fetchColumn();

	$roleID_DATA = $db->prepare("SELECT * FROM `roleassign` WHERE `accountID` = :id");
	$roleID_DATA->execute([':id' => $mod["accountID"]]);
	$roleIDs = $roleID_DATA->fetch();
	$roleID = $roleIDs["roleID"];
	$time_ = time() - 1296000;
	$logs = $db->prepare("SELECT count(*) FROM modactions WHERE type=50 AND timestamp > :stamp AND account = :id");
	$logs->execute([':id'=>$mod["accountID"],':stamp'=>$time_]);
	$count = $logs->fetchColumn();
	$warnings=$roleIDs["warned"];
	if(($count < 5 OR $warnings > 2) AND $roleIDs["noCheck"] != "1" AND $gs->checkPermission($mod["accountID"], "isCronFunction") != "1") {
		$color = "#FF0000";
	}

	$imageCount = $gs->getPermission($mod["accountID"], "modBadgeLevel");
	$imageCount2 = $roleID;
	if($imageCount == 1){
		$image = '';
	} 
	else{
		$image = '';
	}

	
	$warnings.="/3";
	if($imageCount2 == 0 OR $imageCount2 == 4){
		$image = '';
		$row4++;
		$modtable4 .= "<tr><th scope='row'>".$row4."</th><td><font color=\"".$color."\">".$image." ".$mod["userName"]."</font></td><td>".$actionscount."</td><td>".$lvlcount."</td><td>".$time2."</td><td>".$count."</td></tr>";
	}else if($imageCount2 == 1){
		$row3++;
		$modtable3 .= "<tr><th scope='row'>".$row3."</th><td><font color=\"".$color."\">".$image." ".$mod["userName"]."</font></td><td>".$actionscount."</td><td>".$lvlcount."</td><td>".$time2."</td><td>".$warnings."</td><td>".$count."</td></tr>";
	}else if($imageCount2 == 2){
		$row2++;
		$modtable2 .= "<tr><th scope='row'>".$row2."</th><td><font color=\"".$color."\">".$image." ".$mod["userName"]."</font></td><td>".$actionscount."</td><td>".$lvlcount."</td><td>".$time2."</td><td>".$warnings."</td><td>".$count."</td></tr>";
	}else if($imageCount2 == 3){
		$row++;
		$modtable .= "<tr><th scope='row'>".$row."</th><td><font color=\"".$color."\">".$image." ".$mod["userName"]."</font></td><td>".$actionscount."</td><td>".$lvlcount."</td><td>".$time2."</td><td>".$warnings."</td><td>".$count."</td></tr>";
	}
}
echo'
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Ник</th>
      <th>колво</th>
      <th>рейтед</th>
	  <th>вход</th>
	  <th>действий</th>
    </tr>
  </thead>
  <tbody>
    '.$modtable4.'
  </tbody>
</table>

<h2>Staff</h2>
<table class="table table-inverse">
  <thead>
    <tr>
      <th>#</th>
      <th>Ник</th>
      <th>колво</th>
      <th>рейтед</th>
	  <th>вход</th>
	  <th>варнед</th>
	  <th>действий</th>
    </tr>
  </thead>
  <tbody>
    '.$modtable3.'
  </tbody>
</table>

<h2>Elders Mods</h2>
<table class="table table-inverse">
  <thead>
    <tr>
      <th>#</th>
      <th>Ник</th>
      <th>колво</th>
      <th>рейтед</th>
	  <th>вход</th>
	  <th>варнед</th>
	  <th>действий</th>
    </tr>
  </thead>
  <tbody>
    '.$modtable2.'
  </tbody>
</table>

<h2>Mods</h2>
<table class="table table-inverse">
  <thead>
    <tr>
      <th>#</th>
      <th>Ник</th>
      <th>колво</th>
      <th>рейтед</th>
	  <th>вход</th>
	  <th>варнед</th>
	  <th>действий</th>
    </tr>
  </thead>
  <tbody>
    '.$modtable.'
  </tbody>
</table>';
?>