<?php

namespace App\Library\CLASSES;
use Illuminate\Support\Facades\Log; //Login



class PushNotification
{

    public $oneSignal_app_id;
    public $oneSignal_rest_api_key;

    public function __construct()
    {
        // Your Account SID and Auth Token from twilio.com/console
        $this->oneSignal_app_id = env('ONESIGNAL_APP_ID');
        $this->oneSignal_rest_api_key = env('ONESIGNAL_REST_API_KEY');
        
    }

    function sendMessageAllSegments() {
        $content      = array(
            "en" => 'Mensaje'
        );
        $hashes_array = array();
        array_push($hashes_array, array(
            "id" => "like-button",
            "text" => "Like",
            "icon" => "http://i.imgur.com/N8SN8ZS.png",
            "url" => "https://yoursite.com"
        ));
        array_push($hashes_array, array(
            "id" => "like-button-2",
            "text" => "Like2",
            "icon" => "http://i.imgur.com/N8SN8ZS.png",
            "url" => "https://yoursite.com"
        ));
        $fields = array(
            'app_id' => "ec00d753-dc89-4910-b04f-c50140dc0236",
            'included_segments' => array(
                'All'
            ),
            'data' => array(
                "foo" => "bar"
            ),
            'contents' => $content,
            'web_buttons' => $hashes_array
        );
        
        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic NGEwMGZmMjItY2NkNy0xMWUzLTk5ZDUtMDAwYzI5NDBlNjJj'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }

    function sendMessage($mensaje, $titulo, $id_dispositivo){
        $content = array(
            "en" => $mensaje
            );

        $headings = array(
            "en" => $titulo
            );  
            
        $include_player_ids = array(
            $id_dispositivo
            );  
        
        $fields = array(
            'app_id' => $this->oneSignal_app_id,
            'data' => array("variable" => "123"),
            'contents' => $content,
            'headings' => $headings,
            'include_player_ids' => $include_player_ids
        );
        
        $fields = json_encode($fields);
        print("\nJSON sent:\n");
        print($fields);
        Log::info('[PushNotification][sendMessage] JSON sent: '. $fields);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   'Authorization: Basic '. $this->oneSignal_rest_api_key ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        
        Log::info('[PushNotification][sendMessage] status: '. $response);
        return $response;
    }

}

?>