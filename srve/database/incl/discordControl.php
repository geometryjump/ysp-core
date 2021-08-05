<?php
define("DS_OAUTH2_SECRET","hgYfA4AThu0bSsFb12uOuoGyhuJo46Aw");
define("DS_OAUTH2_CLIENTID","442365818395820032");
define("DS_OAUTH2","https://discordapp.com/api/oauth2/authorize?client_id=442365818395820032&redirect_uri=http%3A%2F%2Faccount.ykisl.ru%2FdiscordAuth&response_type=code&scope=identify");
define("DS_OAUTH3","https://discordapp.com/api/oauth2/authorize?client_id=442365818395820032&redirect_uri=http%3A%2F%2Faccount.ykisl.ru%2FdiscordAuth%2FfixPassword&response_type=code&scope=identify");
function DSREQ($url, $post=FALSE, $headers=array()) {
  require_once __DIR__ . "/encryptYourData.php";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    if($post)
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $headers[] = 'Accept: application/json';
    if(!empty($_COOKIE['discord']))
      $headers[] = 'Authorization: Bearer ' . aesdecrypt($_COOKIE['discord'],"RGYATEUqjadasu8gqwuieq2yweduiwaqhe4781e2q");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    return json_decode($response);
}
function DSREQ2($url, $post=FALSE, $headers=array(),$key) {
  require_once __DIR__ . "/encryptYourData.php";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($ch);
    if($post)
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $headers[] = 'Accept: application/json';
    if(!empty($_COOKIE['access_token']))
      $headers[] = 'Authorization: Bearer ' . $key;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    return json_decode($response);
}
function DS_sendDiscordPM($receiver, $message){
    include __DIR__ . "/../config/discord.php";
    if($discordEnabled != 1){
      return false;
    }error_reporting(-1);
    $data = array("recipient_id" => $receiver);                                                                    
    $data_string = json_encode($data);
    $url = "https://discordapp.com/api/v6/users/@me/channels";
    $crl = curl_init($url);
    $headr = array();
    $headr['User-Agent'] = 'Ykisl (https://ykisl.ru,1.0)';
    curl_setopt($crl, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($crl, CURLOPT_POSTFIELDS, $data_string);
    $headr[] = 'Content-type: application/json';
    $headr[] = 'Authorization: Bot '.$bottoken;
    curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1); 
    $response = curl_exec($crl);
    curl_close($crl);
    $responseDecode = json_decode($response, true);
    $channelID = $responseDecode["id"];
    //sending the msg
    $data = array("content" => $message);                                                                    
    $data_string = json_encode($data);
    $url = "https://discordapp.com/api/v6/channels/".$channelID."/messages";
    //echo $url;
    $crl = curl_init($url);
    $headr = array();
    $headr['User-Agent'] = 'Ykisl (https://ykisl.ru,1.0)';
    curl_setopt($crl, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($crl, CURLOPT_POSTFIELDS, $data_string);
    $headr[] = 'Content-type: application/json';
    $headr[] = 'Authorization: Bot '.$bottoken;
    curl_setopt($crl, CURLOPT_HTTPHEADER,$headr);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1); 
    $response = json_decode(curl_exec($crl));
    curl_close($crl);
    if(!empty($response->code) AND is_numeric($response->code)){
      return false;
    }
    return true;
  }
?>