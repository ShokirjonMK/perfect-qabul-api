<?php

namespace api\components;

use app\models\MspdToken;
use app\models\WorklyToken;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use yii\base\Component;

class MspdApiService
{
    public static function sendRequest($method, $body, $url)
    {
        $base_url = \Yii::$app->params['mspdValues']['base_url'];
        $token = '';
        $mspdToken = MspdToken::find()->orderBy(['id' => SORT_DESC])->one();
        if ($mspdToken) {
            $token = $mspdToken->token;
        }
        $headers = array(
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'text/xml'
        );

        $client = new Client(array(
            'headers' => $headers
        ));
        try {
            if ($method == 'post') {
                $response = $client->post($base_url . $url, $body);
            } elseif ($method == 'get') {
                $response = $client->get($base_url . $url, $body);
            } elseif ($method == 'put') {
                $response = $client->put($base_url . $url, $body);
            }
            return $response->getBody();
        } catch (\Exception $exception) {
            $responseAddGroup['body'] = $exception->getMessage();
            $responseAddGroup['result'] = $exception->getCode();
            return $responseAddGroup;
        }
    }
}
