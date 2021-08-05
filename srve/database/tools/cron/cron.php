<?php
chdir(dirname(__FILE__));
set_time_limit(0);
include "fixcps.php";
flush();
include "autoban.php";
flush();
include "friendsLeaderboard.php";
flush();
include "removeBlankLevels.php";
flush();
include "songsCount.php";
flush();
?>
