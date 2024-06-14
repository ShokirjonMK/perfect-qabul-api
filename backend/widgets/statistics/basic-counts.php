<?php
$panel_of_counts = array(
    'students' => array(
        'title' => _e('Students'),
        'icon' => '<i class="fas fa-users font-size-24"></i>',
        'count' => 5965,
        'compare_count' => 0.23,
        'compare_text' => _e('From previous year'),
    ),
    'masters' => array(
        'title' => _e('Masters'),
        'icon' => '<i class="fas fa-graduation-cap font-size-24"></i>',
        'count' => 564,
        'compare_count' => 0.05,
        'compare_text' => _e('From previous year'),
    ),
    'teachers' => array(
        'title' => _e('Teachers'),
        'icon' => '<i class="fas fa-briefcase font-size-24"></i>',
        'count' => 253,
        'compare_count' => 0.03,
        'compare_text' => _e('From previous year'),
    ),
    'phd' => array(
        'title' => _e('PhD'),
        'icon' => '<i class="fas fa-book-reader font-size-24"></i>',
        'count' => 96,
        'compare_count' => 0.01,
        'compare_text' => _e('From previous year'),
    ),
); ?>

<div class="row">
    <?php foreach ($panel_of_counts as $key => $panel_of_counts_items) : ?>
        <div class="col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="media">
                        <div class="media-body overflow-hidden">
                            <p class="text-truncate font-size-20 mb-2"><?= $panel_of_counts_items['title']; ?></p>
                            <h4 class="mb-0" style="font-size: 22px;"><?= $panel_of_counts_items['count']; ?></h4>
                        </div>
                        <div class="text-primary" data-dw-load="basic-count-<?= $key; ?>" style="position: absolute;top: 10px;right: 13px;">
                            <?= $panel_of_counts_items['icon']; ?>
                        </div>
                    </div>
                </div>

                <div class="card-body border-top py-3">
                    <div class="text-truncate">
                        <span class="badge badge-soft-secondary font-size-11">
                            <i class="mdi mdi-menu-up"></i><?= $panel_of_counts_items['compare_count']; ?>%
                        </span>
                        <span class="text-muted ml-2"><?= $panel_of_counts_items['compare_text']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<!-- end row -->