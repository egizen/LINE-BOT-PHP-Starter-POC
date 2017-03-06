<?php // callback.php
echo "OK";

define("LINE_MESSAGING_API_CHANNEL_SECRET", '0741b7e3da24f7b071637fb731ef1777');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'VTECj7jjrSAqG8amUkWnSTxghAsTISeZTAxeTq0cYkKkS1M8LfKl2dX/4lkoarWE9XlqL3fYmdBPiowrlfKEiiD44r/HgLFwt4O4GNU5ZDvAjJB4uesc04SqAIuHcNv0PjvpfPQKjg+VfSbkAuV+CQdB04t89/1O/w1cDnyilFU=');

require __DIR__."/../vendor/autoload.php";

$bot = new \LINE\LINEBot(
    new \LINE\LINEBot\HTTPClient\CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN),
    ['channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET]
);

$signature = $_SERVER["HTTP_".\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
$body = file_get_contents("php://input");

$events = $bot->parseEventRequest($body, $signature);

foreach ($events as $event) {
    if ($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage) {
		
		$text = $event->getText();
		
		//get group id
		if($event->isGroupEvent()) {
			$text22 = $event->getGroupId();
			$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text22);
			$response = $bot->pushMessage('C92ba367859d8098c1b4308ca158150a0', $textMessageBuilder);
		}
		
		//fwd mobile inc from war room to team group.
		if($event->isGroupEvent() && $event->getGroupId() == 'C92ba367859d8098c1b4308ca158150a0' && strpos($text, 'mobile') !== FALSE) {
			$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text);
			$response = $bot->pushMessage('C92ba367859d8098c1b4308ca158150a0', $textMessageBuilder);
		}

		
 		if (strpos($text, '007 อยากรู้') !== FALSE) { //get info from wiki.
			$text_ex = explode(' ', $text);
			$ch1 = curl_init();
			curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch1, CURLOPT_URL, 'https://th.wikipedia.org/w/api.php?format=json&action=query&prop=extracts&exintro=&explaintext=&titles='.$text_ex[2]);
			$result1 = curl_exec($ch1);
			curl_close($ch1);
			
			$obj = json_decode($result1, true);
			
			foreach($obj['query']['pages'] as $key => $val){ 
				$result_text = $val['extract']; 
			}
			
			if(empty($result_text)){
				$result_text = 'ไม่พบข้อมูล';
			}
		}else if (strpos($text, '007 อากาศ') !== FALSE) { //get weather from api.
			$text_ex = explode(' ', $text);
			$ch2 = curl_init();
			curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch2, CURLOPT_URL, 'http://api.wunderground.com/api/71503e2adeb68c63/forecast/lang:TH/q/Thailand/'.str_replace(' ', '%20', $text_ex[2]).'.json');
			$result1 = curl_exec($ch2);
			curl_close($ch2);
			
			$obj = json_decode($result1, true);
			
			if(isset($obj['forecast']['txt_forecast']['forecastday'][0]['fcttext_metric'])){
				$result_text = $obj['forecast']['txt_forecast']['forecastday'][0]['fcttext_metric'];
			}else {
				$result_text = 'ไม่พบข้อมูล';
			}
		}
		
		if(!empty($result_text)){
			$reply_token = $event->getReplyToken();
			$bot->replyText($reply_token, $result_text);
		}
		
    }
	
	
/* 	else if($event instanceof \LINE\LINEBot\Event\MessageEvent\ImageMessage)
	{
		$fullImage = "https://enigmatic-coast-62856.herokuapp.com/BotLine/image/memeFull.jpg";
		$preImage = "https://enigmatic-coast-62856.herokuapp.com/BotLine/image/memePre.jpg";
		$reply_token = $event->getReplyToken();
		$imageMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($fullImage, $preImage);
		$bot->replyMessage($reply_token, $imageMessageBuilder);
	} */
}
