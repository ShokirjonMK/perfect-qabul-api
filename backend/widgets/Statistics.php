<?php

namespace backend\widgets;

use yii\base\Widget;

class Statistics extends Widget
{
    public $type;
    public $load;

    public function init()
    {
        parent::init();

        if ($this->type === null || !$this->load) {
            $this->type = 'empty';
        }
    }
    
    public function run()
    {
        $this->type = '@backend/widgets/statistics/' . $this->type;
        return $this->render($this->type);
    }
}
