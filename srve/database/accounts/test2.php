<?php
$text = $_GET['text'];
$arrayText = explode("@", $text);
$arrayText2 = explode("@", $text);
if(empty($arrayText[1])){
	return;
}
$arrayText[1] = preg_replace("/[^0-9]/","", $arrayText[1]);
if(is_numeric($arrayText[1])){
	echo -1;
}else{
	echo 1;
}
echo "<hr/>";
$arrayText[0]."_1";
echo json_encode($arrayText);
echo "<br/>old:".json_encode($arrayText2);
?>