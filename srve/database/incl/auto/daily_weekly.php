<?php
if(!isset($_GET["hoster"])){
	if(empty($_GET['key'])){
		exit();
	}
	if($_GET['key'] != "afasguydhAJEwuieh21ehqwashiasdhrQHRWHIASDHASHDHUEIQ23BHH14HY2EWHDSAHINHSRBJHADHIASDNAHIDASDHASHIDHISDDASDHIASHIDUS"){
		exit();
	}
	if(empty($_GET['type'])){
		exit();
	}
}else{
	if($_SERVER["HTTP_X_FORWARDED_FOR"] != "31.31.196.201"){
		exit();
	}
	$data = explode("_", $_GET["hoster"]);
	if($data[0] != "".$_SERVER["HTTP_X_FORWARDED_FOR"]."key"){
		exit("1");
	}
	if($data[1] == "daily" OR $data[1] == "weekly"){
		$_GET["type"] = $data[1];
	}else{
		exit("2");
	}
}
echo "Type: ";
$text = "tomorrow 00:00:00";
$text2 = 86400;
$type2 = "0";
$type = $_GET['type'];
$data = "1";
$data2 = "starStars > 1 AND starStars < 10";
if($type == "weekly" OR $type == "daily"){
	if($type == "weekly")
	{
		$data = "1 AND starDemon=1";
		$data2 = "starStars > 9";
		$type2 = "1";
		$text = "next monday";
		$text2 = 604800;
	}
	echo $type;
}
else{
	echo "nope";
	exit();
}
echo "<hr/>";
//
include "../lib/connection2.php";
//get all levels with starStars > 1 но не меньше 10, $data
require_once "../sendLog.php";
$po = new sendLog();
$level = $db->prepare("SELECT levelID,starStars FROM levels WHERE ".$data2." AND starFeatured=".$data);
$level->execute();
$data = $level->fetchAll();
$i = 0;
$levels = array();
foreach($data as &$result){
	$levels[$i] = $result[0];
	echo $result[0] . "(stars: ".$result[1]."), i :".$i .", checked levels[i]: ".$levels[$i];

	echo "<br/>";
	$i++;
}
$j = rand(0,$i);
echo "<hr/>Randomed: ".$j.": ".$levels[$j];
$timestamp = 0;
echo "<br/>Register: type: ".$type." (ID: ".$type2."), level: ".$levels[$j];
$query = $db->prepare("SELECT timestamp FROM dailyfeatures WHERE timestamp >= :tomorrow AND type = ".$type2." ORDER BY timestamp DESC LIMIT 1");
$query->execute([':tomorrow' => strtotime($text)]);
if($query->rowCount() == 0){
	$timestamp = strtotime($text);
}else{
	$timestamp = $query->fetchColumn() + $text2;
}
echo ", register to: ".$timestamp." (day/moath/year)".date("F j, Y, g:i a",$timestamp-(60*60)).")";
$reg = $db->prepare("INSERT INTO `dailyfeatures`(`levelID`, `timestamp`, `type`) VALUES (:id,:time,:type)");
$reg->execute([":id" => $levels[$j],':time' => $timestamp,':type' => $type2]);
echo " | registered: ".$reg->lastInsertedId;
$encodedText = "Было выполнение регистрация `".$type."`!
Уровень: ".$levels[$j]."\n
Время активации дейли (может неправильно посчитывать!): ".date("F j, Y, g:i a",$timestamp-(60*60))." (за час)";
if(isset($_GET["hoster"])){
	$encodedText .= "\n\n**Операция была выполнена хостингом**";
}
$query = $db->prepare("INSERT INTO modactions (type, value, value3, timestamp, account, value2, value4) VALUES ('5', :value, :levelID, :timestamp, :id, :dailytime, 1)");
			$query->execute([':value' => "auto", ':timestamp' => time(), ':id' => "1", ':levelID' => $levels[$j], ':dailytime' => $timestamp]);
$po->send($encodedText);
?>