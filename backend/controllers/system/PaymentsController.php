<?php
namespace backend\controllers\system;

use backend\models\Currency;
use base\BackendController;
use common\models\CurrencyList;
use common\models\CurrencyRates;
use Yii;
use yii\helpers\Url;

/**
 * Payments controller
 */
class PaymentsController extends BackendController
{
    public $url = '/system/payments';

    /**
     * Displays main page
     *
     * @return string
     */
    public function actionIndex()
    {
        $main_url = Url::to([$this->url]);
        $rates = Currency::getRates();
        $cRate = new CurrencyRates();

        return $this->render('index', array(
            'main_url' => $main_url,
            'rates' => $rates,
            'cRate' => $cRate,
        ));
    }

    /**
     * Refresh currency rates
     *
     * @return string
     */

    public function actionRefreshRate()
    {
        /* eski malumotlarni ochirib olamiz, ochirishdan oldin esa valueni ozgaruvchiga saqlab olamiz*/
        $cbvalues = array();
        $currencies = CurrencyList::find()->where(['status' => 1])->all();

        if (sizeof($currencies) > 0) {
            $rates = CurrencyRates::find()->all();

            foreach ($rates as $rate) {
                $cbvalues[$rate->ckey] = round($rate->cvalue, 6);
            }

            CurrencyRates::deleteAll();
            Yii::$app->db->createCommand('ALTER TABLE currency_rates AUTO_INCREMENT = 1')
                ->execute();
        }

        /*malumotlarni qayta yozib olamiz, eskki malumotni valuesini bu yerda yozib qoyamiz*/
        $i = -1;

        foreach ($currencies as $cura) {
            foreach ($currencies as $key => $cur) {
                if ($cura->currency_code != $cur->currency_code) {
                    $i++;
                    $req_url = "https://api.exchangerate.host/convert?from=$cura->currency_code&to=$cur->currency_code";
                    $response_json = file_get_contents($req_url);
                    $response = json_decode($response_json);
                    $model = new CurrencyRates();
                    $model->ckey = $cura->currency_code . $cur->currency_code;
                    $model->cname = $cura->currency_name . ' / ' . $cur->currency_name;
                    $model->cfrom = $response->query->from;
                    $model->cto = $response->query->to;
                    $model->cvalue = $response->info->rate;

                    if (isset($cbvalues[$model->ckey])) {
                        $model->cvbefore = $cbvalues[$model->ckey];
                    } else {
                        $model->cvbefore = $model->cvalue;
                    }

                    $model->update_on = date('Y-m-d H:i:s');
                    $model->save(false);

                    // Create temp
                    create_temp_for('currency');
                }
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Displays settings page
     *
     * @return void
     */
    public function actionSettings()
    {
        // Get ajax action
        $ajax = input_post('ajax');

        if ($ajax == 'update-action') {
            $output['error'] = true;
            $output['success'] = false;

            $id = input_post('id');
            $checked = input_post('checked');

            if ($checked == 'true') {
                $update = Currency::setItemStatus($id, 1);
            } else {
                $update = Currency::setItemStatus($id, 0);
            }

            if ($update) {
                $output['error'] = false;
                $output['success'] = true;
            }

            // Create temp
            create_temp_for('currency');

            // Output
            echo json_encode($output);
            exit();
        }

        $main_url = Url::to([$this->url]);
        $currencyNames = CurrencyList::find()->orderBy('sort ASC')->all();

        return $this->render('settings', array(
            'main_url' => $main_url,
            'currencyNames' => $currencyNames,
        ));
    }

    /**
     * Displays create page
     *
     * @return string
     */
    public function actionCreate()
    {
        $main_url = Url::to([$this->url]);
        $model = new CurrencyList();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['settings']);
        }

        return $this->render('create', array(
            'main_url' => $main_url,
            'model' => $model,
        ));
    }
}
