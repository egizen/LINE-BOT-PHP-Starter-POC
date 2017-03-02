<?php
$access_token = 'VTECj7jjrSAqG8amUkWnSTxghAsTISeZTAxeTq0cYkKkS1M8LfKl2dX/4lkoarWE9XlqL3fYmdBPiowrlfKEiiD44r/HgLFwt4O4GNU5ZDvAjJB4uesc04SqAIuHcNv0PjvpfPQKjg+VfSbkAuV+CQdB04t89/1O/w1cDnyilFU=';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text' && ((strpos($event['message']['text'], 'Man') !== FALSE) || (strpos($event['message']['text'], 'แมน') !== FALSE) || (strpos($event['message']['text'], 'สวัสดี007') !== FALSE))) {
			// Get text sent
			$text = 'พี่แมนมีคนเรียกมาตอบเร็วๆๆๆๆๆๆๆๆๆๆ';
			
			// Get replyToken
			$replyToken = $event['replyToken'];
			
			if(strpos($event['message']['text'], 'สวัสดี007') !== FALSE)
			{
				$text = '007 มารายงานตัวแล้วงับ!';
			}

			// Build message to reply back
			$messages = [
				'type' => 'text',
				'text' => $text
			];

			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],
			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);

			echo $result . "\r\n";
		}
	}
}
echo "OK";