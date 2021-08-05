<?php
error_reporting(-1);
include dirname(__FILE__)."/../../config/connection.php";
@header('Content-Type: text/html; charset=utf-8');
try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname; charset=utf8", $username, $password, array(
    PDO::ATTR_PERSISTENT => true
));
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //данный запрос на проверку ипа будет выполен в том случаи, если база данных была запущена нормально. Если была бы ошибка, он данный запрос бы не сделал из-за ошибки в 
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$EFAEBYFASHUDFASHDASDJHU = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$EFAEBYFASHUDFASHDASDJHU = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$EFAEBYFASHUDFASHDASDJHU = $_SERVER['REMOTE_ADDR'];
	}
	$forLauncherIP = $EFAEBYFASHUDFASHDASDJHU;
	
	$sdgyhjzoefy8ufyasudhasudsdsd = $db->prepare("SELECT ip FROM banip WHERE ip = :ip");
	$sdgyhjzoefy8ufyasudhasudsdsd->execute([':ip' => $EFAEBYFASHUDFASHDASDJHU]);
	if($sdgyhjzoefy8ufyasudhasudsdsd->rowCount() != 0) { 
		$db = null;
		exit("<h1>Вы были забанены на сервере по IP адресу. Скорее всего вы заебали админ состав настолько, что они не хотят ВООБЩЕ видеть вас на своём сервере. Удачи в разбане :)</h1>");
	}
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}
?>