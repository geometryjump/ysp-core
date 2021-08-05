<?php
    require_once "./incl/lib/poll.php";
    $polling = new Poll();
    $polling->push('event1', array('param2' => 'val1', 'param1' => 'val2'));
?>