<?php
include __DIR__ .'/../incl/lib/connection3.php';
if(!empty($_POST["type"])) {
    $type = $_POST["type"];
}
else if(!empty($_GET["type"])) {
    $type = $_GET["type"];
}
else {
    exit("-2");
}
$query = $db->prepare('SELECT * FROM launcher WHERE type = :type');
$query->execute([':type' => $type]);
$data = $query->fetch();
echo $data["value"];
?>