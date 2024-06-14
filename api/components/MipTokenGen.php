<?php

namespace api\components;


class MipTokenGen
{
    public static function getToken($type = null)
    {
        $username = 'M_fM1f5fxdS0XXjBBLBH79cJ8kIa';
        $password = 'VhOOBPrpPiPI_G00cls0ENP5frUa';
        $url = 'https://iskm.egov.uz:9444/oauth2/token?grant_type=password&username=justice-user2&password=KN8akqXsEg';
        $mk_curl = curl_init();
        curl_setopt($mk_curl, CURLOPT_URL, $url);

        // headers
        $headers = array(
            "Authorization: Basic TV9mTTFmNWZ4ZFMwWFhqQkJMQkg3OWNKOGtJYTpWaE9PQlBycFBpUElfRzAwY2xzMEVOUDVmclVh",
            'Content-Type: application/json',
            'Content-Length: 0',
            'Accept: application/json'
        );

        // set headers
        curl_setopt($mk_curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($mk_curl, CURLOPT_HEADER, 1);


        // Authorization set basic auth
        curl_setopt($mk_curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // Basic Auth username and password
        curl_setopt($mk_curl, CURLOPT_USERPWD, $username . ":" . $password);
        // curl_setopt($mk_curl, CURLOPT_TIMEOUT, 30);

        // POST 
        curl_setopt($mk_curl, CURLOPT_POST, 1);
        curl_setopt($mk_curl, CURLOPT_RETURNTRANSFER, TRUE);

        // enable ssl
        // curl_setopt($mk_curl, CURLOPT_SSL_VERIFYPEER, 0);

        // curl execute (get response)

        if (curl_errno($mk_curl)) {
            $error_msg = curl_error($mk_curl);
            curl_close($mk_curl);
            return $error_msg;
        } else {
            $response = curl_exec($mk_curl);
            list($getHeader, $getContent) = explode("\r\n\r\n", $response, 2);
            curl_close($mk_curl);
            $getContentJson = json_decode($getContent);
            if ($type == "token" || $type == "access_token") {
                return $getContentJson->access_token;
            } elseif ($type == "token_type") {
                return $getContentJson->token_type;
            } elseif ($type == "refresh_token") {
                return $getContentJson->refresh_token;
            } elseif ($type == "expires_in" || $type == "expired" || $type == "expired_at") {
                return $getContentJson->expires_in;
            }
            return $getContentJson;
        }
    }
}
