<?php
chdir(dirname(__FILE__));
//error_reporting(0);
include "../incl/lib/connection.php";
include "../incl/lib/mainLib.php";
$gs = new mainLib();
require_once "../incl/lib/exploitPatch.php";
if(!empty($_POST["accID"])) {
    $postUserID = $_POST["accID"];
}
else {
    if(!empty($_GET["accID"])) {
        $postUserID = $_GET["accID"];
    }
    else {
        exit("-2");
    }
}

if($postUserID == "3548") {
    echo'1';
}
?>