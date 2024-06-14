<?php return [
    array(
        'key' => 'service_category',
        'slug' => 'service-category',
        'slug_generator' => 'self',
        'items_with_parent' => true,
        'image_fields' => array('icon', 'image', 'cover_image'),
        'lexicon' => array(
            'title' => _e('Categories'),
            'menu_title' => _e('Categories'),
            'new_item_title' => _e('New category'),
            'edit_item_title' => _e('Edit category'),
            'edit_item_title2' => _e('Edit category: {title}'),
            'successfully_created' => _e('The category was created successfully.'),
            'successfully_updated' => _e('The category has been successfully updated.'),
            'back_to_message' => _e('Back to categories'),
            'not_found_message' => _e('Categories not found!'),
            'not_found_message_full' => _e('The category you were looking for does not exist, unavailable for you or deleted.'),
        ),
    ),
];
