<?
	$encodedEmbed = urlencode((string)"kek");
    $textToSend = "username=GDYS&content=succ";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/channels/424864391293435905/messages");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type' => 'multipart/form-data',
        'Authorization' => 'Bot NDUwMzg5NDY1ODk5MjA0NjA5.De23aw.uYKLLmKXID7jlx_9LwTvIsKlH4A'
    ));
    curl_setopt($ch, CURLOPT_HEADER,1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $textToSend);
    curl_exec($ch);
    curl_close($ch);
?>