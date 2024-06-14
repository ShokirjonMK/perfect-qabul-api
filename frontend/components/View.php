<?php
namespace frontend\components;

class View extends \yii\web\View
{
    public $breadcrumbs = array();
    public $page_title = '';
    public $breadcrumb_title = '';

    public function getAssetsUrl($name = '')
    {
        return $this->getAssetManager()->getBundle('frontend\assets\AppAsset')->baseUrl . '/theme/' . $name;
    }

    public function section()
    {
        return 'Hello';
    }
}
