<?php

namespace backend\widgets;

use yii\base\Widget;

class StorageWidget extends Widget
{
    public $action;
    public $label;
    public $input;
    public $items;
    public $format;
    public $select_type;

    public function init()
    {
        parent::init();

        if ($this->action === null) {
            $this->action = '';
        }

        if ($this->label === null) {
            $this->label = 'Label';
        }

        if ($this->format === null) {
            $this->format = 'file';
        }

        if ($this->select_type === null) {
            $this->select_type = 'single';
        }

        if ($this->input === null) {
            $this->input = array();
        }
        
        if ($this->items === null) {
            $this->items = array();
        }
    }

    public function run()
    {
        $this->format = '@backend/widgets/storage-browser/' . $this->format;

        return $this->render($this->format, array(
            'label' => $this->label,
            'input' => $this->input,
            'items' => $this->items,
            'action' => $this->action,
            'select_type' => $this->select_type,
        ));
    }
}
