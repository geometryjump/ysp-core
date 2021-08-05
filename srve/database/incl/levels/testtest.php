<?
$accountID = 3200;
require __DIR__ . "/../../incl/lib/connection.php";
$accountID = 3200;
require __DIR__ . "/../../incl/lib/mainLib.php";
$gs = new mainLib();
if(!$gs->GetLevelBanned($accountID)) {
	exit("-1");
} 
echo "1";
?>