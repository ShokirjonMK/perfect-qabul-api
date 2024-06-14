<?php
namespace backend\controllers\appearance;

use base\BackendController;

/**
 * Widgets controller
 */
class WidgetsController extends BackendController
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
