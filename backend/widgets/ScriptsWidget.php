<?php

namespace backend\widgets;

use yii\base\Widget;

class ScriptsWidget extends Widget
{
    public function run()
    {
        return $this->render('scripts');
    }
}
