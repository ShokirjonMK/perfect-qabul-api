<?php
namespace backend\controllers\appearance;

use base\BackendController;

/**
 * Customize theme controller
 */
class CustomizeController extends BackendController
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
