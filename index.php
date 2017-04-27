<?php

// Validate the website with facebook developers
//echo $_GET['hub_challenge'];
//die;

/* Webhook for Test Messanger Bot
 * Author: Mohammed Abulkas
 * Date: 17/02/2017
 * Time: 08:10 PM
 */
//The access token for my page on fb "Abulkas"
$access_token = "EAAaZApd9AGQEBAPtFJZCV3BddiE3GyUxpvMEzZB3qzDMiGxi6XtoJf5tJPgCByLHkPsGt04TDhE4DOelCOhrd9TRL8S2f6vAZAzNjQQYZCpEfQQLIr15ZBfh6mQ9yTKdiyTHmj5awbVFYpOONk2wZCzq3RvGN3S37Ahqod9QFf3DgZDZD";

//Verify token used with the app page to verfiy the URL is correct
$verify_token = "fb_time_bot";

//Get the Headers that submitted to this page from FB
file_put_contents("fb.txt", file_get_contents('php://input'));

// Messenger graph API URL with the token
$api_url = "https://graph.facebook.com/v2.6/me/messages?access_token=" . $access_token;

// Get the content of this file which are the headers that submitted by FB to this page
$fb = json_decode(file_get_contents("fb.txt"));

// The receiptent ID to send him a message later
$rid = $fb->entry[0]->messaging[0]->sender->id;

//Check if the request is not a facebook delivery receipt
$isReply = isset($fb->entry[0]->messaging[0]->delivery);

//Handle the message received from the user
$user_message = $fb->entry[0]->messaging[0]->message->text;

// Prepare the message and choose the template
//Button $data = template_button($rid, answer($user_message), "http://freeway-soft.com", "Visit Our website");
//Text
$data = template_text($rid, $user_message);
//
// Message Options
$options = array(
    "http" => array(
        "method" => "POST",
        "content" => json_encode($data),
        "header" => "Content-Type: application/json\n"
    )
);

// Create context to submit it
$context = stream_context_create($options);

//Log what happened
if (!$isReply) {
    log_message("<------Start of Message------>\n" . file_get_contents('php://input'));
    log_message("Sent at: " . date("Y-m-d h:i:s"));
}

// Run the call -- its cURL alternative
if (!$isReply) {
    file_get_contents($api_url, false, $context);

    //Log what happened
    log_message("Response at: " . date("Y-m-d h:i:s"));
    log_message("Robot said: " . json_encode($data['message']) . "\n<------End of Message------>\n");
}


/*
 * AI function determining the user message
 */

function answer($message) {
    if (stripos($message, 'how old') !== false) {
        return "Your Age is 26";
    } elseif (stripos($message, 'name') !== false) {
        return "You are Mohammed";
    } elseif (stripos($message, 'job') !== false) {
        return "Software Engineer";
    } elseif (stripos($message, 'hello') !== false) {
        return "Hi, Nice to meet you";
    } else {
        return "I didn't understand you";
    }
}

/*
 *  Template Button
 */

function template_button($rid, $text, $url, $title) {
    $data = '{
  "recipient":{
    "id":"' . $rid . '"
  },
  "message":{
    "attachment":{
      "type":"template",
      "payload":{
        "template_type":"button",
        "text":"' . $text . '",
        "buttons":[
          {
            "type":"web_url",
            "url":"' . $url . '",
            "title":"' . $title . '"
          }
        ]
      }
    }
  }
}';
    return json_decode($data);
}

/*
 * Template Normal Text
 */

function template_text($rid, $text) {
    $data = array(
        'recipient' => array("id" => "$rid"),
        'message' => array(
            "text" => answer($text)
        )
    );
    return $data;
}

/*
 * Logging function
 */

function log_message($message) {
    file_put_contents('log.' . date('Ymd', time()) . '', $message . PHP_EOL . "\n\n", FILE_APPEND | LOCK_EX);
}
