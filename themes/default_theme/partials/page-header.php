<div class="page_header_block">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="title">
                    <h2><?= isset($title) ? $title : 'Page title'; ?></h2>

                    <ul class="nav">
                        <li>
                            <a href="<?= home_url(); ?>"><?= _e('Home'); ?></a>
                        </li>
                        <?php if (isset($links) && $links) : ?>
                            <?php foreach ($links as $item_url => $item_name) : ?>
                                <li>
                                    <a href="<?= $item_url; ?>"><?= $item_name; ?></a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li>
                            <span><?= isset($title) ? $title : 'Page title'; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>