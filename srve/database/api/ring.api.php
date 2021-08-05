<?php
$passed = 's8gyfhuis8fusbdnSJASASD';
/* KEY: user_api: root | pass_api:root: s8gyfhuis8fusbdnSJASASD 

ring:get - get ring money
ring:m - minis ring money
ring:user = user for get/minis ring money

Так. Для начало первые ключи. Пример использование: ykisl.ru/srve/database/api/ring.api.php?user_api&pass_api:user

user_api = пользователь (root)
pass_api:root = пароль от пользователя (s8gyfhuis8fusbdnSJASASD)
теперь к данныv
ring:get - получить колечек для пользователя. Доп.Ключи: user:ring - пользователь (аккаунт)
rint:minis - отнять колечки. Ключи: user:ring - пользователь, user:old - колечки (GET - для получение), user:new - отнять колечки

Пример: http://ykisl.ru/srve/database/api/ring.api.php?user_api=root&pass_api:root=s8gyfhuis8fusbdnSJASASD&ring:minis&user:ring=YOURUSERNAME&user:old=GET&user:new=10
тут мы спокойно говорим то что user:ring = пользователь (YOURUSERNAME - НА СВОЕ!!!), user:old=GET - мы получаем всего колечек, которые на аккаунте, user:new=10
мы отнимаем колечки 10. К примеру у аккаунта 10, мы отнимаем 10 = 0.

Получить колечки: http://ykisl.ru/srve/database/api/ring.api.php?user_api=root&pass_api:root=s8gyfhuis8fusbdnSJASASD&ring:get&user:ring=YOURUSERNAME
(ВОЗРАЩАЕТ ECHO. К ПРИМЕРУ НА АККАУНТЕ 10 ОН НАПИШЕТ 10. ЕСЛИ АККАУНТ НЕ НАЙДЕН - ТО БУДЕТ ПУСТОТА
*/
if(isset($_GET['user_api'])) {
    $user = $_GET['user_api'];
    if(isset($_GET['pass_api:'.$user])) {
        $pass = $_GET['pass_api:'.$user];
        if($pass == $passed) {
            //API
            //GET
            if(isset($_GET['ring:get'])) {
                include "../incl/lib/connection2.php";
                if(!isset($_GET['user:ring'])) exit("-1");
                $query = $db->prepare("SELECT accMoney FROM users WHERE userName = :userName");
                $query->execute([':userName' => $_GET['user:ring']]);
                $money_get = $query->fetch();
                $money = $money_get['accMoney'];
                exit($money);
                return;
            }
            //MINIS
            if(isset($_GET['ring:minis'])) {
                include "../incl/lib/connection2.php";
                if(!isset($_GET['user:ring'])) exit("-1");
                if(!isset($_GET['user:old'])) { $_GET['user:old'] = "GET"; }
                if(!isset($_GET['user:new'])) exit("-1");
                $users = $_GET['user:ring'];
                $money = $_GET['user:old'];
                if($money == "GET") {
                    $query = $db->prepare("SELECT accMoney FROM users WHERE userName = :userName");
                    $query->execute([':userName' => $_GET['user:ring']]);
                    $money_get = $query->fetch();
                    $money = $money_get['accMoney'];
                }
                $newmoney = $_GET['user:new'];
                $newmon = $money - $newmoney; // 3 - 1 = 2;
                $query = $db->prepare("UPDATE users SET `accMoney` = :moneys WHERE userName = :userName");
                $query->execute([':moneys' => $newmon, ':userName' => $_GET['user:ring']]);
                //echo $money."|".$newmon;
                exit("1");
                return;
            }
        } else {
            exit("-1");
        }
    }
} else {
    exit("DO YOU BANNED!");
}
?>