<?php

namespace api\components;

use yii\filters\auth\HttpBearerAuth as AuthHttpBearerAuth;
use yii\web\UnauthorizedHttpException;

class HttpBearerAuth extends AuthHttpBearerAuth
{

    /**
     * {@inheritdoc}
     */
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('Your access token is invalid or expired.');
    }
    
}
