<?php

namespace backend\controllers;

use Yii;
use backend\models\User;
/**
 * ListActionsTrait
 */
trait ListActionsTrait 
{
    /**
     * Displays main page
     *
     * @return string
     */
    public function actionIndex()
    {
        $bulk_actions = array('activate', 'block', 'trash');
        $where = ['users.deleted' => 0];

        return $this->page('', $where, $bulk_actions);
    }

    /**
     * Displays active page
     *
     * @return string
     */
    public function actionActive()
    {
        $bulk_actions = array('block', 'trash');
        $where = ['users.deleted' => 0, 'users.status' => User::ACTIVE];

        return $this->page('active', $where, $bulk_actions);
    }

    /**
     * Displays pending page
     *
     * @return string
     */
    public function actionPending()
    {
        $bulk_actions = array('activate', 'block', 'trash');
        $where = ['users.deleted' => 0, 'users.status' => User::PENDING];

        return $this->page('pending', $where, $bulk_actions);
    }

    /**
     * Displays blocked page
     *
     * @return string
     */
    public function actionBlocked()
    {
        $bulk_actions = array('activate', 'trash');
        $where = ['users.deleted' => 0, 'users.status' => User::BANNED];

        return $this->page('blocked', $where, $bulk_actions);
    }

    /**
     * Displays deleted page
     *
     * @return string
     */
    public function actionDeleted()
    {
        $bulk_actions = array('activate', 'block', 'restore', 'delete');
        $where = ['users.deleted' => 1];

        return $this->page('deleted', $where, $bulk_actions);
    }

}
