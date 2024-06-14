<?php

namespace api\components;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HemisMK
{
    protected $urlToken = 'https://ministry.hemis.uz/app/rest/v2/oauth/token';
    protected $urlStudent = 'https://ministry.hemis.uz/app/rest/v2/services/student/get?pinfl=';
    protected $userName = 'ulaw';
    protected $password = 'tmeF3qFKmet8Y7D';

    public function getHemis($pinfl)
    {
        try {
            $getToken = self::getToken();
            if (!$getToken['status']) return false;
            $token = $getToken['access_token'];
            $url = $this->urlStudent . $pinfl;

            $client = new Client();
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json; charset=UTF-8',
                ],
            ]);

            $content = $response->getBody()->getContents();
            $getContentJson = json_decode($content);
            // dd($response->getStatusCode());

            return $getContentJson;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $errorMessage = $response->getBody()->getContents();
                return $errorMessage;
            }
            return $e->getMessage();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $pinfl;
    }


    public function getToken()
    {
        // dd("ssss");
        $client = new Client([
            'base_uri' => $this->urlToken,
            'headers' => [
                'Authorization' => 'Basic Y2xpZW50OnNlY3JldA==',
                'Content-Type' => 'multipart/form-data; boundary=<calculated when request is sent>'
            ],
            'timeout' => 30,
        ]);

        try {
            $response = $client->post('', [
                'form_params' => [
                    'grant_type' => 'password',
                    'username' => $this->userName,
                    'password' => $this->password
                ]
            ]);

            $responseContent = $response->getBody()->getContents();
            $contentJson = json_decode($responseContent);

            return [
                'status' => true,
                'access_token' => $contentJson->access_token,
                'message' => 'Success',
            ];
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function getHemissss($pinfl)
    {
        $url = $this->urlStudent . $pinfl;
        $mk_curl = curl_init();
        curl_setopt($mk_curl, CURLOPT_URL, $url);

        // $token = self::getToken();

        // return $token;
        $token = 'y4BRVRU6U2eHM6E3P7P28Yp7mNc';
        // headers
        $headers = array(
            "Authorization: Bearer $token",
            'Content-Type: application/json; charset=UTF-8'
        );

        // set headers
        curl_setopt($mk_curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($mk_curl, CURLOPT_HEADER, 1);


        // curl_setopt($mk_curl, CURLOPT_TIMEOUT, 30);

        // POST 
        // curl_setopt($mk_curl, CURLOPT_POST, 1);
        curl_setopt($mk_curl, CURLOPT_RETURNTRANSFER, TRUE);
        // curl_setopt($mk_curl, CURLOPT_POSTFIELDS, $xmlMK);

        // enable ssl
        // curl_setopt($mk_curl, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($mk_curl);

        if (curl_errno($mk_curl)) {
            $error_msg = curl_error($mk_curl);
            curl_close($mk_curl);
            return $error_msg;
        } else {
            list($getHeader, $getContent) = explode("\r\n\r\n", $response, 2);
            curl_close($mk_curl);
            $getContentJson = json_decode($getContent);

            return $getContentJson;
        }
        return $pinfl;
    }

    public static function refreshToken()
    {
        $url = 'http://ministry.hemis.uz/app/rest/v2/oauth/token';
        $mk_curl = curl_init();
        curl_setopt($mk_curl, CURLOPT_URL, $url);

        $refreshToken = 'N771lKEu1YoWHettr6Poca_c-HY';

        $defaults = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic Y2xpZW50OnNlY3JldA==",
                'Content-Type: multipart/form-data; boundary=<calculated when request is sent>'
            ),
            CURLOPT_HEADER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ),
            CURLOPT_TIMEOUT => 30,
        );

        $mk_curl = curl_init();
        curl_setopt_array($mk_curl, $defaults);

        $response = curl_exec($mk_curl);

        if (curl_errno($mk_curl)) {
            $error_msg = curl_error($mk_curl);
            curl_close($mk_curl);
            return $error_msg;
        } else {
            list($getHeader, $getContent) = explode("\r\n\r\n", $response, 2);
            curl_close($mk_curl);
            $getContentJson = json_decode($getContent);

            return $getContentJson;
        }
        return 0;
    }

    public function getTokenCurl()
    {
        // headers
        $headers = array(
            "Authorization: Basic Y2xpZW50OnNlY3JldA==",
            'Content-Type: multipart/form-data; boundary=<calculated when request is sent>'
        );

        $defaults = array(
            CURLOPT_URL => $this->urlToken,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic Y2xpZW50OnNlY3JldA==",
                'Content-Type: multipart/form-data; boundary=<calculated when request is sent>'
            ),
            CURLOPT_HEADER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                'grant_type' => 'password',
                'username' => $this->userName,
                'password' => $this->password
            ),
            CURLOPT_TIMEOUT => 30,
        );

        $mk_curl = curl_init();
        curl_setopt_array($mk_curl, $defaults);


        $response = curl_exec($mk_curl);

        // return $response;
        if (curl_errno($mk_curl)) {
            $error_msg = curl_error($mk_curl);
            curl_close($mk_curl);
            return $error_msg;
        } else {

            return $response;
            list($getHeader, $getContent) = explode("\r\n\r\n", $response, 2);
            curl_close($mk_curl);
            $getContentJson = json_decode($getContent);

            return $getContentJson;
        }
        return false;
    }


    /*
    $refreshToken = 'N771lKEu1YoWHettr6Poca_c-HY';

        $client = new Client([
            'headers' => [
                "Authorization: Basic Y2xpZW50OnNlY3JldA==",
                'Content-Type: multipart/form-data;'
            ]
        ]);

        $response = $client->post(
            'http://ministry.hemis.uz/app/rest/v2/oauth/token',
            ['body' => json_encode(
                [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ]
            )]
        );
        dd('sasdas');
        dd($response);
        if ($response->getStatusCode() == 200) {

            $res = json_decode($response->getBody()->getContents());

            dd($res);

            if (isset($res->result)) {
                $result = $res->result;

                // return  json_decode($response->getBody()->getContents());
                $data['status'] = true;
                $data['data'] = $result;

                return $data;
            } else {
                $error = $res->error;
                $data['error'] = $error;
                return $data;
            }
        } else {
            $data['status'] = false;
            return $data;
        }*/
}
