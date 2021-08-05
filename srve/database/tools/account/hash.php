<?php
error_reporting(0);
$pass = $_GET['password_hash'];
echo password_hash($pass, PASSWORD_DEFAULT);
?>