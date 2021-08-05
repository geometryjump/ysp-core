<?php
class launcherGetIP{
    function getUserIP(){
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                  $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
    
        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }
    
        return $ip;
    }
}
/*
CLASS BY NOBDOD!
 */
class sendLog {
    // log_channel // отправляет логи на сервер
    public function randomHex() {
	   $chars = 'ABCDEF0123456789';
	   $color = '#';
	   for ( $i = 0; $i < 6; $i++ ) {
	      $color .= $chars[rand(0, strlen($chars) - 1)];
	   }
	   return $color;
	}
    public function send($text) {

        $encodedText = urlencode($text);
        $textToSend = json_encode([
            "username" => "YSP",
            "embeds" => [
                [
                    // Embed Title
                    "title" => "<:attention:599932468568522762> Логирование сервера",

                    // Embed Type
                    "type" => "rich",

                    // Embed Description
                    "description" => $text,

                    "footer" => [
                        "text" => "Автоматическая система оповещения."
                    ],

                    "color" => hexdec($this->randomHex())
                ]
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/695592583975403521/YbBsYwGN80vEdp1sNwSsbTZqrs5KKG3NwARsRj7ec6BJOSeBmED-3brCACwSYRFA8nmx"); //ВЕЕБСУКИ ИЗМЕНИТЬ НА СВОЕ
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
        return 1;
    }
     public function send2($text) {
        $encodedText = urlencode($text);
        $textToSend = "username=YSP&content=" . $encodedText;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/695592583975403521/YbBsYwGN80vEdp1sNwSsbTZqrs5KKG3NwARsRj7ec6BJOSeBmED-3brCACwSYRFA8nmx"); //ВЕЕБСУКИ ИЗМЕНИТЬ НА СВОЕ
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
        curl_exec($ch);
        curl_close($ch);
        return 1;
    }
    public function send3($text) {
        $encodedText = urlencode($text);
        $textToSend = json_encode([
        	"username" => "YSP",
	    	"embeds" => [
		        [
		            // Embed Title
		            "title" => "<:attention:599932468568522762> Внимание!",

		            // Embed Type
		            "type" => "rich",

		            // Embed Description
		            "description" => $text,

		            "footer" => [
		                "text" => "Автоматическая система оповещения."
		            ],

		            "color" => hexdec($this->randomHex())
		        ]
	    	]
	    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/695232678785449994/B0pADO8bAKe1Jqht4BShsIvlwlEnkfVqgQFHbPdymY0K6E4ba7EzAdifvrhTw3xSJE3c"); //ВЕЕБСУКИ ИЗМЕНИТЬ НА СВОЕ
        //curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $r=curl_exec($ch);
        //echo $r;
        //$this->send($r."!");
        curl_close($ch);
        return 1;
    }
    
    public function send4($text) {
        $encodedText = urlencode($text);
        $textToSend = json_encode([
        	"username" => "YSP",
        	"content" => "<@&686052872060010536>",
	    	"embeds" => [
		        [
		            // Embed Title
		            "title" => "<:attention:599932468568522762> Внимание!",

		            // Embed Type
		            "type" => "rich",

		            // Embed Description
		            "description" => $text,

		            "footer" => [
		                "text" => "Заявки на модератора."
		            ],

		            "color" => hexdec($this->randomHex())
		        ]
	    	]
	    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/716971997451780116/ow-x7HdSaL_2ld3_so2orhaoBDnPzptPeYFn2CXXWbSPVkqSS9tozpXdqPaK2nEaHi6a"); //ВЕЕБСУКИ ИЗМЕНИТЬ НА СВОЕ
        //curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $r=curl_exec($ch);
        //echo $r;
        //$this->send($r."!");
        curl_close($ch);
        return 1;
    }
    
    public function send5($text) {
        $encodedText = urlencode($text);
        $textToSend = json_encode([
        	"username" => "YSP",
        	"content" => "",
	    	"embeds" => [
		        [
		            // Embed Title
		            "title" => "<:attention:599932468568522762> Внимание!",

		            // Embed Type
		            "type" => "rich",

		            // Embed Description
		            "description" => $text,

		            "footer" => [
		                "text" => "Заявки на модератора."
		            ],

		            "color" => hexdec($this->randomHex())
		        ]
	    	]
	    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/716971997451780116/ow-x7HdSaL_2ld3_so2orhaoBDnPzptPeYFn2CXXWbSPVkqSS9tozpXdqPaK2nEaHi6a"); //ВЕЕБСУКИ ИЗМЕНИТЬ НА СВОЕ
        //curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $r=curl_exec($ch);
        //echo $r;
        //$this->send($r."!");
        curl_close($ch);
        return 1;
    }
    
    
    public function sendLauncher($text) {
        include __DIR__ .'lib/connection.php';
        $getIP = new launcherGetIP();
        $forLauncherIP = $getIP->getUserIP();
        $encodedText = urlencode($text);
        $textToSend = json_encode([
	    	"content" => "CONNECT IP: $forLauncherIP",
	    	"embeds" => [
		        [
		            // Embed Title
		            "title" => "<:attention:599932468568522762> Внимание!",

		            // Embed Type
		            "type" => "rich",

		            // Embed Description
		            "description" => $text,

		            "footer" => [
		                "text" => "Логирование лаунчера."
		            ],

		            "color" => hexdec($this->randomHex())
		        ]
	    	]
	    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/725420084268367952/5clre5kKnU26YOPJ0wWnM58HHKTq3YgcIV8zzIIvCtQxdvLEOJtkkj1ngv1saPkZK1Jm"); //ВЕЕБСУКИ ИЗМЕНИТЬ НА СВОЕ
        //curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $r=curl_exec($ch);
        //echo $r;
        //$this->send($r."!");
        curl_close($ch);
        return 1;
    }
    
    public function sendMain() {
        include __DIR__ .'lib/connection.php';
        $getIP = new launcherGetIP();
        $forLauncherIP = $getIP->getUserIP();
        $encodedText = urlencode($text);
        $textToSend = json_encode([
	    	"content" => "",
	    	"embeds" => [
		        [
		            // Embed Title
		            "title" => "<:attention:599932468568522762> Внимание!",

		            // Embed Type
		            "type" => "rich",

		            // Embed Description
		            "description" => "New connect. IP: $forLauncherIP",

		            "footer" => [
		                "text" => "Логирование сервера."
		            ],

		            "color" => hexdec($this->randomHex())
		        ]
	    	]
	    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/725420084268367952/5clre5kKnU26YOPJ0wWnM58HHKTq3YgcIV8zzIIvCtQxdvLEOJtkkj1ngv1saPkZK1Jm"); //ВЕЕБСУКИ ИЗМЕНИТЬ НА СВОЕ
        //curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $r=curl_exec($ch);
        //echo $r;
        //$this->send($r."!");
        curl_close($ch);
        return 1;
    }
    
    
 public function sendchat($text) {
        $encodedText = urlencode($text);
        $textToSend = json_encode([
            "username" => "YSP",
            "content" => $text
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/699282913413431366/VbqroRcrDiJscyNLZeZhcJHjEwmkia9FXcvEaVRD6kw8YfnboOKIONWAtsatjCgEtUUy"); //ВЕЕБСУКИ ИЗМЕНИТЬ НА СВОЕ
        //curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        $r=curl_exec($ch);
        //echo $r;
        //$this->send($r."!");
        curl_close($ch);
        return 1;
    }
    public function sendMod($text1, $text) {

        $textToSend = json_encode([
            "username" => "YSP - Mod System",
            "content" => $text1,
            "embeds" => [
                [
                    // Embed Title
                    "title" => "<:attention:599932468568522762> Мод система",

                    // Embed Type
                    "type" => "rich",

                    // Embed Description
                    "description" => $text,

                    "color" => hexdec($this->randomHex())
                ]
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/698873893368365107/fumQaV-Ngd3gyOTYn4GmZGR8fwKNZkUHC5sylDdsfS55HLVRBSCYgFlM9BIL1F1mNoL4"); //ВЕЕБСУКИ ИЗМЕНИТЬ НА СВОЕ
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
        return 1;
    }
    // send_channel // отправляет сенды на канал
    public function sendSend($text) {
        $encodedText = urlencode($text);
        $textToSend = "username=YSP&content=" . $encodedText;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/695592583975403521/YbBsYwGN80vEdp1sNwSsbTZqrs5KKG3NwARsRj7ec6BJOSeBmED-3brCACwSYRFA8nmx"); //ВЕЕБСУКИ ИЗМЕНИТЬ НА СВОЕ
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
        curl_exec($ch);
        curl_close($ch);
        return 1;
    }
}
?>