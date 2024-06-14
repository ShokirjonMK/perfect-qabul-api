<?php
return array(
    'services' => array(
        'url' => '#',
        'name' => _e('Services'),
        'sort' => 235,
        'icon' => '<i class="ri-briefcase-2-line"></i>',
        'active_menu' => ['service', 'content/service', 'segment/service-category'],
        'childs' => array(
            array(
                'url' => '/content/service/create',
                'name' => _e('Add new'),
            ),
            array(
                'url' => '/content/service/all',
                'name' => _e('All services'),
            ),
            array(
                'url' => '/segment/service-category/all',
                'name' => _e('Categories'),
            ),
        ),
    ),
);
