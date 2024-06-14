<?php

namespace backend\widgets;

use yii\base\Widget;

class BulkActions extends Widget
{
    public $show_clang = true;
    public $limit_default = 20;
    public $sort_default = 'newest';
    public $countries = false;
    public $cities = false;
    public $departments = false;
    public $subjects = false;
    public $departmentTypes = false;

    public $sort_array;
    public $limit_array;

    public $actions = array('publish', 'unpublish', 'trash', 'restore', 'delete');

    public function run()
    {
        $this->sort_array = array(
            'newest' => _e('Newest'),
            'oldest' => _e('Oldest'),
            'a-z' => _e('A-Z'),
            'z-a' => _e('Z-A'),
        );

        $this->limit_array = array(
            20 => _e('20 items'),
            40 => _e('40 items'),
            80 => _e('80 items'),
            100 => _e('100 items'),
            200 => _e('200 items'),
        );

        $data = array(
            'show_clang' => $this->show_clang,
            'actions' => $this->actions,
            'sort_array' => $this->sort_array,
            'sort_default' => $this->sort_default,
            'limit_default' => $this->limit_default,
            'limit_array' => $this->limit_array,
            'countries' => $this->countries,
            'cities' => $this->cities,
            'departments' => $this->departments,
            'subjects' => $this->subjects,
            'departmentTypes' => $this->departmentTypes,
        );

        return $this->render('bulk-actions', $data);
    }
}
