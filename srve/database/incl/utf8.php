<?php
echo "key: text<br/>";
$text = $_GET['text'];
$text = mb_convert_encoding($text, 'utf-8', mb_detect_encoding($text));
echo $text;
?>