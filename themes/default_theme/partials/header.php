<?php

use frontend\models\MenuModel;
?>
<header class="full_bg">
    <!-- header inner -->
    <div class="header">
        <div class="header_top">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <ul class="contat_infomations">
                            <li>
                                <i class="ri-phone-fill"></i>
                                <?= _e('Call'); ?>: <?= get_setting_value('phone_number'); ?>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-3">
                        <ul class="contat_infomations text_align_right">
                            <li>
                                <a href="Javascript:void(0)">
                                    <i class="ri-mail-send-fill"></i>
                                    <?= get_setting_value('email_address'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="header_bottom">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                                <div class="full">
                                    <div class="center-desk">
                                        <div class="logo">
                                            <a href="<?= home_url(); ?>">
                                                <img src="<?= theme_logo_image(); ?>" alt="logo">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                                <nav class="navigation navbar navbar-expand-md navbar-dark ">
                                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample04" aria-controls="navbarsExample04" aria-expanded="false" aria-label="Toggle navigation">
                                        <span class="navbar-toggler-icon"></span>
                                    </button>
                                    <div class="collapse navbar-collapse" id="navbarsExample04">
                                        <?= MenuModel::get('header-menu'); ?>
                                    </div>
                                    <ul class="search">
                                        <li>
                                            <a href="Javascript:void(0)">
                                                <i class="ri-search-line"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>