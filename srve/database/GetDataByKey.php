<?php
include "incl/lib/connection.php";
if(!empty($_POST["KeyName"])){
    
	$BackgroundURL = '0xFF';
	$GameArchiveName = 'GDCSAR.zip';
	$GameDataURL = '';
	$LauncherVer = '0';
	$LogoURL = '0xFF';
	
    $keyname = $_POST["KeyName"];
    
    if($keyname == 'BackgroundURL'){
		echo $BackgroundURL;
	}
	elseif($keyname == 'GameArchiveName'){
		echo $GameArchiveName;
	}
	elseif($keyname == 'GameDataURL'){
		echo $GameDataURL;
	}
	elseif($keyname == 'LauncherVer'){
		echo $LauncherVer;
	}
	elseif($keyname == 'LogoURL'){
		echo $LogoURL;
	}
}
else{
    echo 'This key was not found';
}
?>