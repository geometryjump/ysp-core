<?php
if($_GET['key'] == "ykislnubskylord1223") {
    include "../lib/connection2.php";
    $starsgain = array();
$time = 86400;
$time = time() - $time;
$x = 0;
$query = $db->prepare("SELECT * FROM actions WHERE type = '9' AND timestamp > :time");
$query->execute([':time' => $time]);
$result = $query->fetchAll();
    foreach($result as &$gain){
        if(!empty($starsgain[$gain["account"]])){
            $starsgain[$gain["account"]] += $gain["value"];
        }else{
            $starsgain[$gain["account"]] = $gain["value"];
        }
    }
    $xz = 6 - 1;
    arsort($starsgain);
    foreach ($starsgain as $userID => $stars){
        if($x < $xz) {
            $query = $db->prepare("SELECT userName, isBanned FROM users WHERE userID = :userID");
            $query->execute([':userID' => $userID]);
            $userinfo = $query->fetchAll()[0];
            $query = $db->prepare("SELECT * FROM users WHERE userID = :userID");
            $query->execute([':userID' => $userID]);
            $result = $query->fetchAll();
            $money = "";
            foreach($result as &$data) {
                if($money == "") $money = $data['accMoney'];
            }
            
            $username = htmlspecialchars($userinfo["userName"], ENT_QUOTES);
            if($userinfo["isBanned"] == 0 AND $stars >4){
                //nul = '<td>('.time().') <a href="cron/giveMoney.php?give&userID='.$userID.'&top='.$x.'" class="btn btn-primary">Выдать '.$userID.'</a></td>';
                $query = $db->prepare("SELECT value FROM modactions WHERE type = '600' AND timestamp > :time AND value2 = '".$username."'");
                $query->execute([':time' => time()]);
                $data = $query->fetch();
                if($data['value'] == "gived") {
                    echo -1;
                } else {
                    $x++;
                    $userIDs = preg_replace("/[^0-9]/", '', $userID);
                    $query2 = $db->prepare("SELECT count(*) FROM users WHERE userID LIKE :userName");
                    $query2->execute([':userName' => $userIDs]);
                    $regusrs = $query2->fetchColumn();
                    if ($regusrs < 1) {
                        //$main = "Неизвестный аккаунт!";
                        echo -2;
                    }
                    else {
                        $query2 = $db->prepare("SELECT userName, accMoney FROM users WHERE userID LIKE :userName");
                        $query2->execute([':userName' => $userIDs]);
                        $data = $query2->fetch();
                        $query6 = $db->prepare("INSERT INTO modactions (type, value, timestamp, value2, account) VALUES 
                        ('600',:gived,:time,:name, :acc)");
                        $times = time() + 86400;
                        $xs = preg_replace("/[^0-9]/", '', $x);
                        $givereward = 0;
                        if($xs == 1) {$givereward = 25;}
                        else if($xs == 2) {$givereward = 20;}
                        else if($xs == 3) {$givereward = 15;}
                        else if($xs == 4) {$givereward = 10;}
                        else if($xs == 5) {$givereward = 5;}
                        $gives = $data['accMoney'] + $givereward;
                        $query6->execute([':gived' => "gived", ':time' => $times, ':name' => $data['userName'], ':acc' => "1"]);
                        $update = $db->prepare("UPDATE `users` SET accMoney = $gives WHERE userID = :id");
                        $update->execute([':id' => $userIDs]);
                        echo 1;
                        //$main = "Выдано $gives ($givereward) (место: $xs, время: ".time().",время2: ".$times.")";
                    }
                }
            }
        }
    }
}