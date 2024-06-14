<?php

namespace common\models;

use yii\base\Model;

class EncryptPass extends Model
{
    protected $ciphering = "AES-128-CTR";
    protected $encryption_iv = 'ShokirjonMK12345';
    protected $options = 0;
    public function encrypt($string, $encryption_key)
    {
        $encryption = openssl_encrypt($string, $this->ciphering, $encryption_key, $this->options, $this->encryption_iv);
        return $encryption;
    }

    public function decrypt($string, $encryption_key)
    {
        $decryption = openssl_decrypt($string, $this->ciphering, $encryption_key, $this->options, $this->encryption_iv);
        return $decryption;
    }
}
