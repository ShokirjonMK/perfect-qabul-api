<?php $this->title = _e('Themes'); ?>

<?php if ($themes && ($themes['current'] || $themes['themes'])) : ?>
    <div class="row">
        <?php if ($themes['current']) : ?>
            <?php $current_theme = $themes['current']; ?>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="themes-list-body">
                            <div class="theme-left-block">
                                <img src="<?= $current_theme['screenshot']; ?>" alt="Theme screenshot">
                            </div>
                            <div class="themes-right-block">
                                <h2 class="card-title mt-0" style="font-size:20px;"><?= $current_theme['name']; ?></h2>
                                <p class="card-text text-muted">
                                    <?= _e('Version'); ?>: <?= $current_theme['version']; ?>
                                    <br>
                                    <?= _e('Author'); ?>: <a href="<?= $current_theme['author_url']; ?>" target="_blank"><?= $current_theme['author']; ?></a>
                                </p>
                                <p class="card-text"><?= $current_theme['description']; ?></p>
                                <a href="<?= admin_url('appearance/customize'); ?>" class="btn btn-success waves-effect waves-light">
                                    <i class="ri-equalizer-line align-middle mr-1"></i>
                                    <span><?= _e('Customize theme'); ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($themes['themes'] as $theme) : ?>
            <div class="col-md-4">
                <div class="card">
                    <img class="card-img-top img-fluid" src="<?= $theme['screenshot']; ?>" alt="Theme screenshot" style="border-bottom: 1px solid rgba(0, 0, 0, 0.08);">
                    <div class="card-body">
                        <h4 class="card-title mt-0"><?= $theme['name']; ?></h4>
                        <p class="card-text text-muted">
                            <?= _e('Version'); ?>: <?= $theme['version']; ?>
                            <br>
                            <?= _e('Author'); ?>: <a href="<?= $theme['author_url']; ?>" target="_blank"><?= $theme['author']; ?></a>
                        </p>
                        <button type="button" data-set-theme="<?= $theme['theme_key']; ?>" class="btn btn-primary waves-effect waves-light">
                            <i class="ri-save-line align-middle mr-1"></i>
                            <?= _e('Set theme'); ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <div class="row">
        <div class="col-12">
            <div class="text-center my-5">
                <i class="ri-error-warning-line" style="font-size: 5em;color:#343a40;"></i>
                <h3 class="text-uppercase mt-1"><?= _e('Themes not found!'); ?></h3>
                <div class="mt-5 text-center">
                    <a href="<?= admin_url(); ?>" class="btn btn-primary btn-with-icon">
                        <i class="ri-arrow-left-line mr-1"></i>
                        <?= _e('Back to dashboard'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    trs.themes = {
        'change_message': '<?= _e('Are you sure you want to change store theme?'); ?>',
    }
</script>

<?php
$this->registerJs(
    <<<JS
    $(document).ready(function () {
        $('[data-set-theme]').click(function () {
            var theme_key = $(this).data('set-theme');

            if (theme_key != undefined && theme_key != '') {
                var message = trs.themes.change_message;

                if (confirm(message)) {
                    $.ajax({
                        type: 'POST',
                        data: {
                            ajax_action: 'set_theme',
                            theme_key: theme_key
                        },
                        dataType: 'json',
                        success: function (data) {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function () {
                            alert(ajax_error_msg);
                        }
                    });
                }
            } else {
                alert(ajax_error_msg);
            }
        });
    });
JS
);
