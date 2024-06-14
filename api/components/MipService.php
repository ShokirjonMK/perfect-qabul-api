<?php

namespace api\components;

use SoapClient;
use SoapFault;

class MipService
{
    public $user_number = '';
    public $numbers_array = [];


    public static function getPhotoService1()
    {
        $pinpp = "60111035440025";
        $doc_give_date = "2019-11-30";


        $xmlMK = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:idm="http://fido.com/IdmsEGMICServices">
   <soapenv:Header/>
   <soapenv:Body>
      <idm:GetDataByPinppRequest>
         <idm:Data><![CDATA[<?xml version="1.0"?>
         <DataByPinppRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="file:///d:/STS/workspaceEASU/IdmsEGMICServices/src/main/resources/xsdData/GetDatabyDoc.xsd">
         <pinpp>' . $pinpp . '</pinpp>
         <doc_give_date>' . $doc_give_date . '</doc_give_date>
         <langId>1</langId>
         <is_consent_pers_data>Y</is_consent_pers_data>
         </DataByPinppRequest>]]></idm:Data>
         <idm:Signature></idm:Signature>
         <idm:PublicCert></idm:PublicCert>
         <idm:SignDate></idm:SignDate>
      </idm:GetDataByPinppRequest>
   </soapenv:Body>
</soapenv:Envelope>';

        $url = "http://59.162.33.102:9301/Avalability";

        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // Following line is compulsary to add as it is:
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            "xmlRequest=" . $xmlMK
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
        $data = curl_exec($ch);
        curl_close($ch);

        //convert the XML result into array
        $array_data = json_decode(json_encode(simplexml_load_string($data)), true);

        return $array_data;
        print_r('<pre>');
        print_r($array_data);
        print_r('</pre>');
    }


    public static function getPhotoService($pinpp, $doc_give_date)
    {

        $xmlMK = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:idm="http://fido.com/IdmsEGMICServices">
   <soapenv:Header/>
   <soapenv:Body>
      <idm:GetDataByPinppRequest>
         <idm:Data><![CDATA[<?xml version="1.0"?>
         <DataByPinppRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="file:///d:/STS/workspaceEASU/IdmsEGMICServices/src/main/resources/xsdData/GetDatabyDoc.xsd">
         <pinpp>' . $pinpp . '</pinpp>
         <doc_give_date>' . $doc_give_date . '</doc_give_date>
         <langId>1</langId>
         <is_consent_pers_data>Y</is_consent_pers_data>
         </DataByPinppRequest>]]></idm:Data>
         <idm:Signature></idm:Signature>
         <idm:PublicCert></idm:PublicCert>
         <idm:SignDate></idm:SignDate>
      </idm:GetDataByPinppRequest>
   </soapenv:Body>
</soapenv:Envelope>';


        // dd(simplexml_load_string($xmlMK));


        $url = 'https://apimgw.egov.uz:8243/gcp/photoservice/v1';
        $mk_curl = curl_init();
        curl_setopt($mk_curl, CURLOPT_URL, $url);

        $token = MipTokenGen::getToken('token');
        // headers
        $headers = array(
            "Authorization: Bearer $token",
            'Content-Type: text/xml; charset=UTF-8'
        );

        // set headers
        curl_setopt($mk_curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($mk_curl, CURLOPT_HEADER, 1);


        // curl_setopt($mk_curl, CURLOPT_TIMEOUT, 30);

        // POST 
        curl_setopt($mk_curl, CURLOPT_POST, 1);
        curl_setopt($mk_curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($mk_curl, CURLOPT_POSTFIELDS, $xmlMK);

        // enable ssl
        // curl_setopt($mk_curl, CURLOPT_SSL_VERIFYPEER, 0);

        // curl execute (get response)


        if (curl_errno($mk_curl)) {

            // moving to display page to display curl errors
            echo curl_errno($mk_curl);
            // echo curl_error($mk_curl);
        } else {
            // return "res";

            //getting response from server
            $response = curl_exec($mk_curl);

            // dd($response);

            list($getHeader, $getContent) = explode("\r\n\r\n", $response, 2);
            dd($getContent);
            curl_close($mk_curl);
            // $getContent = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $getContent);
            // $getContent = utf8_encode($getContent);

            $getContent = str_replace('&lt;', '<', $getContent);
            $getContent = str_replace('&gt;', '>', $getContent);


            dd(json_decode(json_encode(simplexml_load_string($getContent))));

            // dd($response);
            // dd($getContent);
            // return simplexml_load_file($getContent);
            return $getContent;

            // \r\n\r\n 

        }


        $response = curl_exec($mk_curl);

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
    }



    protected function errors($response)
    {
        if (property_exists($response, 'Result')) {
            if ($response->Result != 1) {
                return false;
            }
        }
        if (property_exists($response, 'PinppAddressResult')) {
            if (property_exists($response->PinppAddressResult, 'AnswereId')) {
                if ($response->PinppAddressResult->AnswereId != 1) {
                    return false;
                }
            }
        }
        return true;
    }
}
