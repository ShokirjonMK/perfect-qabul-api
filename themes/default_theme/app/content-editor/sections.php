<?php
return array(
    'slider' => [
        'title' => _e('Slider'),
        'icon' => '<i class="ri-stack-line"></i>',
        'html' => '<div class="slider-section"></div>',
        'repeater_elements' => [
            'title' => _e('Slider item'),
            'button_text' => _e('Add slider'),
            'elements' => [
                'title' => [
                    'title' => _e('Title'),
                    'type' => 'input',
                    'input_type' => 'text',
                    'input_class' => 'form-control',
                ],
                'description' => [
                    'title' => _e('Description'),
                    'type' => 'tinymce',
                    'html' => '<p>' . _e('Start typing...') . '</p>',
                ],
                'button' => [
                    'title' => _e('Button'),
                    'type' => 'button',
                    'render_template' => 'button',
                ],
                'image' => [
                    'title' => _e('Image'),
                    'type' => 'image',
                    'render_template' => 'image',
                ],
            ],
        ],
    ],
    'testimonials' => [
        'title' => _e('Testimonials'),
        'icon' => '<i class="ri-chat-smile-2-line"></i>',
        'html' => '<div class="testimonial-section"></div>',
        'repeater_elements' => [
            'title' => _e('Testimonial item'),
            'button_text' => _e('Add testimonial'),
            'elements' => [
                'fullname' => [
                    'title' => _e('Fullname'),
                    'type' => 'input',
                    'input_type' => 'text',
                    'input_class' => 'form-control',
                ],
                'position' => [
                    'title' => _e('Position'),
                    'type' => 'input',
                    'input_type' => 'text',
                    'input_class' => 'form-control',
                ],
                'message' => [
                    'title' => _e('Message'),
                    'type' => 'tinymce',
                    'html' => '<p>' . _e('Start typing...') . '</p>',
                ],
            ],
        ],
    ],
);
