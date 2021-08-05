<?php
//notic - nofication
//nof - nofic.

//by NobDod
class nofic {
    

    //limit
    public function getLimit() {return 5;}
    //send nofication
    public function send($title = "No Title",$text = "This text:)",$value = "",
    $icon = 0,$acc = 0) {
        require __DIR__."/../lib/connection2.php";
        //require_once __DIR__."/../lib/connection.php";
        
        $query = $db->prepare("INSERT INTO `notfication`(`title`, `text`, `value`, `toextID`, `isNew`, `type_icon`) VALUES ('".$title."','".$text."','".$value."','".$acc."',1,'".$icon."')");
        //send
        //$query->execute();
        return true;
    }

    //get data
    public function getNumberRead($extID) {
        $where = "toextID = $extID";
        require __DIR__."/../lib/connection.php";
        $query = $db->prepare("SELECT * FROM notfication WHERE toextID = $extID AND isNew = 1 ORDER BY `id` DESC LIMIT ".$this->getLimit());
        //$query->execute();
        $z = $query->fetchAll();
        $i = 0;
        foreach($z as &$d) {
            if($d['isNew'] == 1) {$i++;}

        }
        return $i;
    }
    public function getAllRead($extID) {
        $where = "toextID = $extID AND isNew = 1";
        require __DIR__."/../lib/connection.php";
        $query = $db->prepare("SELECT * FROM notfication WHERE toextID = $extID AND isNew = 1 ORDER BY `id` DESC LIMIT ".$this->getLimit());
        //$query->execute();
        $z = $query->fetchAll();
        $i = 0;
        foreach($z as &$d) {
            $i++;

        }
        return $i;
    }
    public function get($a,$extID) {
        $where = "toextID = $extID AND isNew = 1";
        require __DIR__."/../lib/connection.php";
        $query = $db->prepare("SELECT * FROM notfication WHERE toextID = $extID AND isNew = 1 ORDER BY `id` DESC LIMIT ".$this->getLimit());
        //$query->execute();
        $z = $query->fetchAll();
        $i = "";
        $g = 0;
        $s = 0;
        foreach($z as &$d) {
            $s++;
            $title = $this->replaceLang($d['title']);
            $text = $this->replaceLang($d['text']);
            $value = "";
            if($d['value'] != "") {
                $value = $d['value'];
            }
            if($g > 0) { $i .= ""; }
            //icon: 1 - friend, 2-message 10 - give ring:)
            $i .= $a."<b>".$title."</b><br/>".$text."";
            if($value != "") {$i .= $value;}
            $i .= "</a>";
            $g++;
        }
        return $i;
    }

    //lang
    public function replaceLang($text) {
        $locale = "EN";
        if(empty($_COOKIE['lang'])) {
            $locale = "EN";
        } else {
            $locale = $_COOKIE['lang'];
        }
        include "text".$locale.".php";
        if(isset( $string[$text])) {return  $string[$text];}
        return $text;
    }
}
?>