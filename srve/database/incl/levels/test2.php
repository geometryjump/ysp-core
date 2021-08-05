<?php
	      $embedText = urlencode('[{"title": "A level has just been rated!", "type": "rich", "description": "<:Galo4ka:428890563228598292>Suck My Dick by OctoDownich\n**Description:** *My dick is inside your ass succccc*\n***Level stats:***\n**Downloads:**As people\n**Likes:**As sluts\n**Length:**My dick", "footer": {"text": "ID: 228 1337 666"}}]');
	      $formdata = "username=GDYS&embeds=" . $embedText;
	      $ch = curl_init();
	      curl_setopt($ch, CURLOPT_URL, "https://discordapp.com/api/webhooks/443784042886856704/bUvVFjFpvY-cXbMj-Mai-S4vtsTkV4mZeBim8BKMpx4eFfM-PQ7MHEhNWlo4wyzndyuB");
	      curl_setopt($ch, CURLOPT_POST, 1);
	      curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
	      curl_exec($ch);
	      curl_close($ch);
?>