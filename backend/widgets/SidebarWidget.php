<?php

namespace backend\widgets;

use yii\base\Widget;

class SidebarWidget extends Widget
{
    public function run()
    {
        return $this->render('sidebar');
    }
}
