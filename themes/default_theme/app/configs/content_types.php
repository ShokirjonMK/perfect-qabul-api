<?php return [
    array(
        'key' => 'service',
        'slug' => 'service',
        'slug_generator' => 'self',
        'items_with_parent' => false,
        'image_fields' => array('icon', 'image', 'cover_image'),
        'segments' => array(
            'service_category' => array(
                'label' => _e('Categories'),
                'input' => 'select',
                'required' => 0,
            ),
        ),
        'lexicon' => array(
            'title' => _e('Services'),
            'menu_title' => _e('Services'),
            'new_item_title' => _e('New service'),
            'edit_item_title' => _e('Edit service'),
            'edit_item_title2' => _e('Edit service: {title}'),
            'successfully_created' => _e('The service was created successfully.'),
            'successfully_updated' => _e('The service has been successfully updated.'),
            'back_to_message' => _e('Back to services'),
            'not_found_message' => _e('Services not found.'),
            'not_found_message_full' => _e('The service you were looking for does not exist, unavailable for you or deleted.'),
        ),
    ),
];
