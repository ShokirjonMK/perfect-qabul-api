<?php

namespace backend\controllers;

use backend\models\Product;
use base\BackendController;

/**
 * Site controller
 */
class SiteController extends BackendController
{

    /**
     * Displays homepage.
     *
     * @return string
     */

    public function actionIndex()
    {
        $this->registerCss(array(
            'dist/libs/admin-resources/jquery.vectormap/jquery-jvectormap-2.0.5.css',
            'dist/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
            'dist/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css',
        ));

        $this->registerJs(array(
            'dist/libs/apexcharts/apexcharts.min.js',
            'dist/libs/admin-resources/jquery.vectormap/jquery-jvectormap-2.0.5.min.js',
            'dist/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js',
            'dist/libs/datatables.net/js/jquery.dataTables.min.js',
            'dist/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
            'dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
            'dist/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',
            'theme/components/statistics.js',
        ));

        return $this->render('index');
    }

    /**
     * Displays error page.
     *
     * @return string
     */
    public function actionError()
    {
        return $this->render('error404');
    }
}
