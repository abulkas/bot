<?php

require('vendor/autoload.php');
require ('sdk.class.php');

//new SDK object
$sdk = new SDK("EAAaZApd9AGQEBAIHJcC8ZAeCAsg2DG0tSEub8VqcbyAyNmSLjReezSQKt3Im10BpWl3YEJ2uXe8egv2FsNqp3XmJDYp60C7e1yTAAhrZBk4HM5JlwnwYXsxq1xFi3QRzkQWN8wtEPdzBkR3oR7s5cE67rrqaq7Bwfh4B8QNcAZDZD", "abulkas");

$sdk->verify_token();

//Set up the ordinary config
$sdk->setInput();

$inputs = $sdk->getInput();

$fbFILE = $sdk->getFBfile();

$user_id = $sdk->getID($fbFILE);

$isReply = $sdk->isReply($fbFILE);

$user_message = $sdk->getUserMessage($fbFILE);

$isGEOPlace = $sdk->isGEOPlace($fbFILE);

$coordinates;



//Answer the message
if (!$isReply) {
    if ($isGEOPlace) {
        $sdk->log_message(json_encode($sdk->getNearestHotels(["31.745805", "35.593155"], "")));
        die;
    }
    $sdk->sendMessage($sdk->answer($user_message), $user_id, $template = "button");
}

