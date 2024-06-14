<?php

namespace backend\widgets;

use yii\base\Widget;

class PageSeoWidget extends Widget
{
    public $active = true;

    public function run()
    {
        if (!$this->active) {
            return false;
        }

        return $this->render('page-seo-wiget');
    }
}
