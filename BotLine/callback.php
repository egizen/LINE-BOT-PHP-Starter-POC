<?php // callback.php
echo "OK";

ob_start();
$raw = file_get_contents('php://input');
var_dump(json_decode($raw,1));
$raw = ob_get_clean();
file_put_contents('/tmp/dump.txt', $raw."\n=====================================\n", FILE_APPEND);

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
        $reply_token = $event->getReplyToken();
        $text = $event->getText();
        $bot->replyText($reply_token, $text);
    }
}