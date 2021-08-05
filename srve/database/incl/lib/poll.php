<?php
    class Poll {
        function setInterval($f, $milliseconds) {
            $seconds=(int)$milliseconds/1000;
            while(true){
                $f();
                sleep($seconds);
            }
        }

        public function push($event, $data) {
            $file = fopen(dirname(__FILE__).'/events.txt', 'r');
            $content = fread($file, filesize(dirname(__FILE__).'/events.txt'));
            fclose($file);
            $parsed = json_decode($content);
            $event = array(
                'event' => $event,
                'data' => $data
            );
            array_push($parsed, $event);
            file_put_contents($file, json_encode($parsed));
            return true;
        }

        public function listen() {
            $time = time();
            $t = $time;
            $ftime = filemtime(dirname(__FILE__).'/events.txt');
            $this->setInterval(function() {
                $ftime = filemtime(dirname(__FILE__).'/events.txt');
                if($time < $ftime) {
                    $file = fopen(dirname(__FILE__).'/events.txt', 'r');
                    $content = fread($file, filesize(dirname(__FILE__).'/events.txt'));
                    fclose($file);
                    $parsed = json_decode($content);
                    $event = end($parsed);
                    return $event;
                }
            }, 1000);
        }
    }
?>