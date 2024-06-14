<?php

namespace backend\widgets;

use yii\base\Widget;

class ContentWidget extends Widget
{
    public $array;
    public $key;
    public $info;
    public $form;
    public $model;
    public $type;
    public $values;

    public function init()
    {
        parent::init();

        $this->array = array(
            'form' => $this->form,
            'model' => $this->model,
            'info' => $this->info,
            'values' => $this->values,
            'segment_key' => $this->key,
        );
    }

    public function run()
    {
        if ($this->type) {
            $this->type = '@backend/widgets/content/' . $this->type;
            return $this->render($this->type, $this->array);
        }
    }
}
