<?php

namespace backend\controllers\system;

use backend\models\System;
use base\BackendController;
use Yii;
use yii\helpers\Url;

/**
 * Roles controller
 */
class RolesController extends BackendController
{
    public $url = '/system/roles';

    private $page_group = array(
        'all' => array(
            'name' => 'Все',
            'active' => false,
        ),
        'disabled' => array(
            'name' => 'Отключенные',
            'active' => false,
        ),
    );

    /**
     * Displays main page
     *
     * @return string
     */
    public function actionIndex()
    {
        $main_url = Url::to([$this->url]);
        $page_group = $this->page_group;
        $pgroup_key = input_get('group', 'all');

        if (isset($page_group[$pgroup_key])) {
            $page_group[$pgroup_key]['active'] = true;
        }

        return $this->render('index', array(
            'main_url' => $main_url,
            'page_group' => $page_group,
        ));
    }

    /**
     * Displays update page
     *
     * @return string
     */
    public function actionCreate()
    {
        $main_url = Url::to([$this->url]);

        return $this->render('create', array(
            'main_url' => $main_url,
        ));
    }

    /**
     * Displays update page
     *
     * @return string
     */
    public function actionUpdate($id)
    {
        $main_url = Url::to([$this->url]);

        return $this->render('update', array(
            'main_url' => $main_url,
        ));
    }
}
