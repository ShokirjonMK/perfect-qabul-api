<?php

namespace base;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    public $token_key = false;
    private $token_keys = array();

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        // Init container
        $container = new Container();
        $container::$prefix = 'api';
        $container::$context = 'api';

        // Init language
        $languages = get_languages();
        $lang_array = array();
        $default_lang_code = get_setting_value('site_language', false);

        if ($languages) {
            $lang_code = input_get('lang', $default_lang_code);

            foreach ($languages as $item) {
                if ($item['lang_code'] == $lang_code) {
                    $lang_array = $item;
                    $lang_array['flag'] = images_url('flags/svg/' . $lang_code . '.svg');
                }
            }
        }

        if ($lang_array) {
            Yii::$app->language = $lang_array['locale'];
        }

        Container::$language = $lang_array;
    }

    /**
     * Before action
     *
     * @param $action
     * @return void
     */
    public function beforeAction($action)
    {
        $this->generate_access_key();
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$this->check_access_key()) {
            $data = json_output();
            $data['message'] = 'Incorrect token key!';
            $this->asJson($data);

            return false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Generate api access key
     *
     * @return void
     */
    public function generate_access_key()
    {
        $api_salt_key = API_SALT_KEY;
        $api_secret_key = API_SECRET_KEY;
        $api_token = $api_salt_key . '-' . $api_secret_key;

        $date1 = gmdate('Y-m-d H:i', strtotime('+1 min'));
        $date2 = gmdate('Y-m-d H:i', strtotime('+2 min'));

        $generated_key_1 = md5($api_token . $date1);
        $generated_key_2 = md5($api_token . $date2);

        $this->token_key = $generated_key_1;
        $this->token_keys = array($generated_key_1, $generated_key_2);
    }

    /**
     * Check api access key
     *
     * @return void
     */
    private function check_access_key()
    {
        $token = '';
        $headers = Yii::$app->request->headers;
        $header_token = $headers->get('api-token');
        $param_token = Yii::$app->request->get('token');

        if ($header_token && is_string($header_token)) {
            $token = $header_token;
        }

        if ($param_token && is_string($param_token)) {
            $token = $param_token;
        }

        if (YII_DEBUG && $token == API_MASTER_KEY) {
            return true;
        } elseif ($token && in_array($token, $this->token_keys)) {
            return true;
        }

        return false;
    }
    
    /**
     * JSON output
     *
     * @param array $data
     * @return mixed
     */
    public function output($data)
    {
        $statusCode = array_value($data, 'statusCode');

        if (is_numeric($statusCode)) {
            Yii::$app->response->statusCode = $statusCode;
        } else {
            Yii::$app->response->statusCode = 404;
        }

        return $this->asJson($data);
    }

}
