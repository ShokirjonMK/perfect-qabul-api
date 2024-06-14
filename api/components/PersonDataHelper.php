<?php

namespace api\components;

use common\models\IpsGovUz;
use common\models\IpsService;
use common\models\Logging;
use phpDocumentor\Reflection\Types\False_;
use Yii;
use SoapClient;
use SoapFault;
use yii\console\Exception;

class PersonDataHelper
{
    public $url;
    public $function;
    public $service_name;

    public function services($pinfl, $passport)
    {
        $ipsServices = IpsService::find()
            ->where(['status' => IpsService::STATUS_ACTIVE])
            ->andWhere(['is_working' => 1])
            ->all();
        $array = [];
        $result = '';

        foreach ($ipsServices as $service) {
            $this->service_name = $service->service_name;
            $this->function = $service->function;
            $this->url = $service->url;
            $array[] = $this->getData($pinfl, $passport);
            //            $result = call_user_func_array("array_merge", $array);
        }
        return $array;
    }

    protected function getData($pinfl, $passport)
    {
        $data = null;
        $url = $this->url;
        $function = $this->function;
        $form = $this->serviceForm($this->service_name, $pinfl, $passport);

        try {
            $params = [
                'verifypeer' => false,
                'verifyhost' => false,
                //  http://10.190.2.36
                //  http://10.0.42.3:9444
                //  http://10.0.42.3:8243
                // 'host' => '10.0.42.3',
                // 'port' => '9444',
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ])
            ];

            $soap = new SoapClient($url, $params);
            $response = $soap->__soapCall($function, $form);
            $array = [];
            if (!$this->errors($response)) {
                return $array;
            } else {
                if ($this->service_name == 'passport_info') {
                    $data = simplexml_load_string($response->Data);
                } else {
                    $data = $response;
                }
                if ($this->service_name == 'passport_info') {
                    $data = (array)$data;
                    $row = (array)$data['row'];
                    $array['document'] = $row['document'];
                    $array['surname_latin'] = $row['surname_latin'];
                    $array['name_latin'] = $row['name_latin'];
                    $array['patronym_latin'] = $row['patronym_latin'];
                    // $array['surname_engl'] = $row['surname_engl'];
                    //    $array['name_engl'] = $row['name_engl'];
                    $array['birth_date'] = $row['birth_date'];
                    $array['birth_place'] = $row['birth_place'];
                    //    $array['birth_country'] = $row['birth_country'];
                    $array['nationality'] = $row['nationality'];
                }
                if ($this->service_name == "address_by_prop") {
                    $array['propiska_region'] = $data->PinppAddressResult->Data->PermanentRegistration->Region->Value;
                    $array['propiska_tuman'] = $data->PinppAddressResult->Data->PermanentRegistration->District->Value;
                    //    $array['propis_country'] = $data->PinppAddressResult->Data->PermanentRegistration->Country->Value;
                    $array['Cadastre'] = $data->PinppAddressResult->Data->PermanentRegistration->Cadastre;
                    $array['Address'] = $data->PinppAddressResult->Data->PermanentRegistration->Address;
                    $array['RegistrationDate'] = $data->PinppAddressResult->Data->PermanentRegistration->RegistrationDate;
                }
            }
            return $array;
        } catch (SoapFault $soapFault) {
            $service = IpsService::find()->where(['like', 'service_name', $this->service_name])->one();
            if ($service) {
                //  $service->is_working = 0;
                $service->save(false);
            }
        }
    }

    protected function serviceForm($service_name, $pinfl, $passport)
    {
        $array = [];
        //        $pass_ser = substr($applicant->passport, 0, 2);
        //        $pass_num = substr($applicant->passport, 2, 7);
        if ($service_name == 'passport_info') {
            $xml = "<?xml version='1.0' encoding=\"utf-8\"?>
                        <DataCEPRequest>
                             <pinpp>$pinfl</pinpp>
                             <document>$passport</document>
                             <langId>3</langId>
                        </DataCEPRequest>";
            $array = [
                'AuthInfo' => [
                    'WS_ID' => '',
                    'LE_ID' => '',
                ],
                'Data' => $xml,
                'Signature' => '',
                'PublicCert' => '',
                'SignDate' => '',
            ];
        }
        if ($service_name == "address_by_prop") {
            $array = [
                'pinpp' => $pinfl,
                'document' => $passport,
                'langId' => '2',
            ];
        }

        $result = [
            'DataCEPRequest' => $array
        ];

        return $result;
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
