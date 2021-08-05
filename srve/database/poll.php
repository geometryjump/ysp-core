<?php
    require_once "./incl/lib/poll.php";
    $polling = new Poll();
    $res = $polling->listen();
    echo json_encode($res);
?>