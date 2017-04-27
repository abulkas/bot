<?php

/* Webhook Class for Messanger Bot
 * Author: Mohammed Abulkas
 * Date: 26/03/2017
 * Time: 11:38 PM
 */

class SDK {

    var $page_token;
    var $verify_token;
    var $api_url;

    function __construct($page_token, $verify_token) {
        $this->page_token = $page_token;
        $this->verify_token = $verify_token;
        $this->setAPIURL();
    }

    function setInput() {
        file_put_contents("fb.txt", file_get_contents('php://input'));
    }

    function getInput() {
        return file_get_contents('php://input');
    }

    function setAPIURL() {
        $this->api_url = "https://graph.facebook.com/v2.6/me/messages?access_token=" . $this->page_token;
    }

    function getFBfile() {
        return json_decode(file_get_contents("fb.txt"));
    }

    function getID($fb) {
        return $fb->entry[0]->messaging[0]->sender->id;
    }

    function isReply($fb) {
        if (isset($fb->entry[0]->messaging[0]->read) || isset($fb->entry[0]->messaging[0]->delivery)) {
            return true;
        }
    }

    function isGEOPlace($fb) {
        return isset($fb->entry[0]->messaging[0]->message->attachments[0]->payload->coordinates);
    }

    function getUserMessage($fb) {
        if (isset($fb->entry[0]->messaging[0]->message->text)) {
            return $fb->entry[0]->messaging[0]->message->text;
        }
    }

    function sendMessage($text, $rid, $template = "text") {
        $data = $this->template_text($rid, $text);
        $this->log_message("<------Start of Message------>\n" . $this->getInput());
        $this->log_message("Sent at: " . date("Y-m-d h:i:s"));

        if ($template == "text") {
            $data = $this->template_text($rid, $text);
        } elseif ($template == "button") {
            $data = $this->template_button($rid, $text, "http://freeway-soft.com", "Visit Our Website");
        }


        //Start chat button
        elseif ($template == "hello") {
            $data = $this->template_button_hello($rid, $text, "http://freeway-soft.com", "Visit Our Website");
        }
        $options = array(
            "http" => array(
                "method" => "POST",
                "content" => json_encode($data),
                "header" => "Content-Type: application/json\n"
            )
        );

        $this->log_message("Response at: " . date("Y-m-d h:i:s"));
        // $this->log_message("Robot said: " . json_encode($data->message) . "\n<------End of Message------>\n");

        $context = stream_context_create($options);

        //Send
        file_get_contents($this->api_url, false, $context);
    }

    function getNearestHotels($coordinates, $type) {
        $place_url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=" . $coordinates[0] . "," . $coordinates[1] . "&radius=400&types=$type&key=AIzaSyCiGNcpvIWvyjn-GVn39rZAs1YeZypEQqI";
        return json_decode(file_get_contents($place_url));
    }

    function answer($message) {
        if (stripos($message, 'hotel') !== false) {
            return "Ok, Please send me your location";
        } elseif (stripos($message, 'hello') !== false) {
            return "Hi, Nice to meet you .. Please send me what do you want to find place or hotel?";
        } elseif (stripos($message, 'place') !== false) {
            return "Ok, Please send me your location";
        } else {
            return "I didn't understand what you mean by " . $message . "?";
        }
    }

    function template_button($rid, $text, $url, $title) {
        $data = '{"recipient":{"id":"' . $rid . '"},"message":{"attachment":{"type":"template","payload":{"template_type":"button","text":"' . $text . '","buttons":[
{"type":"web_url","url":"' . $url . '","title":"' . $title . '"}]}}}}';
        return json_decode($data);
    }

    function template_button_hello($rid, $text, $url, $title) {
        $data = '{"recipient":{"id":"' . $rid . '"},"message":{"attachment":{"type":"template","payload":{"template_type":"button","text":"What do you want to do next?","buttons":[{"type":"postback","title":"Start Chatting","payload":"USER_DEFINED_PAYLOAD"}]}}}}';
        return json_decode($data);
    }

    function template_text($rid, $text) {
        $data = array(
            'recipient' => array("id" => "$rid"),
            'message' => array(
                "text" => $this->answer($text)
            )
        );
        return $data;
    }

    function selectTemplate($message) {
        return "";
    }

    function selectPlaceType($message) {
        $types = [
            "point_of_interest",
            "place_of_worship",
            "night_club",
            "park",
            "resturant"
        ];
    }

    function getCoordinatesFromMessage($fb) {
        return [$fb->entry[0]->messaging[0]->message->attachments[0]->payload->coordinates->long, $fb->entry[0]->messaging[0]->message->attachments[0]->payload->coordinates->lat];
    }

    function verify_token() {
        if (isset($_GET['hub_challenge'])) {
            echo $_GET['hub_challenge'];
            die;
        }
    }

    function checkLogsFolder() {
        if (!file_exists('logs/')) {
            mkdir('logs', 0777, true);
        }
    }

    function log_message($message) {

        $this->checkLogsFolder();
        file_put_contents('logs/log.' . date('Ymd', time()) . '', $message . PHP_EOL . "\n\n", FILE_APPEND | LOCK_EX);
    }

}
