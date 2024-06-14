<?php
namespace backend\components;

class View extends \yii\web\View
{
    public $breadcrumbs = array();
    public $page_title = '';
    public $breadcrumb_title = '';

    public function imagesUrl($name)
    {
        return $this->getAssetManager()->getBundle('backend\assets\AppAsset')->baseUrl . '/dist/' . $name;
    }

    public function assetsUrl($name)
    {
        return $this->getAssetManager()->getBundle('backend\assets\AppAsset')->baseUrl . '/' . $name;
    }
}
