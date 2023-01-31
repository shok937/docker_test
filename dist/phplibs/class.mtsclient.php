<?php

class MTSClient
{
    /**
     * @var string
     */
    var $host;
    /**
     * @var string
     */
    var $auth;

    var $timeout;

    /**
     * @param $host string
     * @param $login string
     * @param $password string
     */
    function __construct($host='https://omnichannel.mts.ru/http-api/v1/', $login='gw_A84z7hw0Cdtt', $password='JHgTWEbZ', $timeout = 60)
    {
        $this->host = $host;
        $this->auth = "Basic " . base64_encode($login . ":" . $password);

        $this->timeout = $timeout;
    }

    /**
     * @param $naming string
     * @param $text string
     * @param $phone string
     * @return $id string
     */
    function sendSms($naming, $text, $phone)
    {
        $req = [
            "messages" => [
                [
                    "content" => [
                        "short_text" => $text
                    ],
                    "to" => [
                        [
                            "msisdn" => $phone
                        ]
                    ],
                ]
            ],
            "options" => [
                "class" => 1,
                "from" => [
                    "sms_address" => $naming,
                ],
            ]
        ];
        $respStr = $this->curlRequest("messages", json_encode($req), [], "POST");

        $resp = json_decode($respStr, true);

        if (isset($resp["code"])) {
            // var_dump($respStr);
            return "0";
        }


        if (!isset($resp["messages"][0]["internal_id"])) {
            // var_dump($respStr);
            return "0";
        }

        // var_dump("successes response:", $respStr);

        return $resp["messages"][0]["internal_id"];
    }

    /**
     * @param $ids []string
     * @return array []
     */
    function getSmsInfo($ids)
    {

        $respStr = $this->curlRequest("messages/info", json_encode(["int_ids" => $ids]), [], "POST");

        $resp = json_decode($respStr, true);

        if (isset($resp["code"])) {
            // var_dump($respStr);
            return [];
        }

        // var_dump("successes response:", $respStr);

        return $resp;
    }

    /**
     * @param $url
     * @param $data
     * @param $headers
     * @return bool|string
     */
    function curlRequest($url, $data = NULL, $headers = NULL, $method = "GET")
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->host . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $headers = array_merge($headers, ["Authorization: " . $this->auth, "Content-Type: application/json; charset=utf-8"]);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $response = curl_exec($ch);

        if (curl_error($ch)) {
            trigger_error('Curl Error:' . curl_error($ch));
        }


        curl_close($ch);
        return $response;
    }
}



/*

// $client = new MTSClient("https://omnichannel.mts.ru/http-api/v1/", "ЛОГИН", "ПАРОЛЬ");

// отправка сообщения
if (!isset($_mtsclient)) {
    $_mtsclient = new MTSClient();
}
$_sid = $_mtsclient->sendSms("AUTOGRAD", "test message", "79991112233"); // отправка смс
// если $_sid == 0, то была ошибка запроса

if ($_sid != "0") {
    // получение статуса по id сообщения
    $status = $_mtsclient->getSmsInfo([$_sid]);
    var_dump($status);
}

*/


?>