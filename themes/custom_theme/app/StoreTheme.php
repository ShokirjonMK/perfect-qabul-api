<?php

use themes\custom_theme\app\AppAsset;

/**
 * Theme class
 */
class StoreTheme
{
    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        // Set components
        \Yii::$app->setComponents([
            'view' => [
                'class' => 'themes\custom_theme\app\View'
            ],
            'assetManager' => [
                'class' => 'yii\web\AssetManager',
                'bundles' => [
                    'yii\web\JqueryAsset' => [
                        'jsOptions' => ['position' => \yii\web\View::POS_HEAD],
                    ]
                ]
            ],
            'i18n' => [
                'class' => 'yii\i18n\I18N',
                'translations' => [
                    'app*' => [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'basePath' => '@themes/custom_theme/app/translations',
                        'fileMap' => [
                            'app' => 'app.php',
                        ]
                    ],
                    'common*' => [
                        'class' => 'yii\i18n\PhpMessageSource',
                        'basePath' => '@common/translations',
                        'fileMap' => [
                            'common' => 'app.php',
                        ]
                    ],
                ],
            ],
        ]);
    }

    /**
     * Register JS file or files
     *
     * @param string $file
     * @return void
     */
    public function registerJs($file)
    {
        if ($file) {
            $ver = APP_VERSION;
            $bundle = AppAsset::register(\Yii::$app->view);

            if (is_array($file)) {
                foreach ($file as $fi) {
                    if (strpos($fi, '.js?') !== false) {
                        $bundle->js[] = $fi;
                    } else {
                        $bundle->js[] = $fi . "?ver={$ver}";
                    }
                }
            } else {
                if (strpos($file, '.js?') !== false) {
                    $bundle->js[] = $file . "?ver={$ver}";
                } else {
                    $bundle->js[] = $file . "?ver={$ver}";
                }
            }
        }
    }

    /**
     * Register CSS file or files
     *
     * @param string $file
     * @return void
     */
    public function registerCss($file)
    {
        if ($file) {
            $ver = APP_VERSION;
            $bundle = AppAsset::register(\Yii::$app->view);

            if (is_array($file)) {
                foreach ($file as $fi) {
                    if (strpos($fi, '.css?') !== false) {
                        $bundle->css[] = $fi;
                    } else {
                        $bundle->css[] = $fi . "?ver={$ver}";
                    }
                }
            } else {
                if (strpos($file, '.css?') !== false) {
                    $bundle->css[] = $file . "?ver={$ver}";
                } else {
                    $bundle->css[] = $file . "?ver={$ver}";
                }
            }
        }
    }
}
