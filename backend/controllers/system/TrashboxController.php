<?php
namespace backend\controllers\system;

use base\BackendController;

/**
 * Trashbox controller
 */
class TrashboxController extends BackendController
{
    /**
     * Displays main page
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
