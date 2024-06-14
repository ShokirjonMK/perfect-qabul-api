<?php

namespace backend\widgets;

use Yii;
use yii\base\Widget;

class ContentEditorWidget extends Widget
{
    public $fields;
    public $save_input;

    public function init()
    {
        parent::init();

        if ($this->fields === null) {
            $this->fields = array();
        }

        if ($this->save_input === null) {
            $this->save_input = '';
        }
    }

    public function run()
    {
        Yii::$app->controller->registerCss(array(
            'dist/libs/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css',
            'theme/components/content-editor/style.css',
            'theme/components/content-editor/text-style.css',
        ));

        Yii::$app->controller->registerJs(array(
            'dist/libs/sortablejs/sortable.min.js',
            'dist/libs/sortablejs/jquery-sortable.min.js',
            'dist/libs/tinymce/tinymce.min.js',
            'dist/libs/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js',
            'theme/components/tinymce-editor.js',
            'theme/components/content-editor/init.js',
            'theme/components/content-editor/elements.js',
        ));

        return $this->render('@backend/widgets/content-editor/index', [
            'save_input' => $this->save_input,
            'sections' => $this->sections(),
            'elements' => $this->elements(),
        ]);
    }

    /**
     * Sections
     *
     * @return array
     */
    public function sections()
    {
        $field_sections = array_value($this->fields, 'sections');

        $sections = array(
            'container' => [
                'title' => 'Container',
                'image' => 'images/icons/ce-container.svg',
                'html' => '<div class="container"></div>',
            ],
            'container_fluid' => [
                'title' => 'Full width',
                'image' => 'images/icons/ce-full-width.svg',
                'html' => '<div class="container-fluid"></div>',
            ],
        );

        if (is_array($field_sections) && $field_sections) {
            $sections = array_merge($sections, $field_sections);
        }

        return $sections;
    }

    /**
     * Groups
     *
     * @return array
     */
    public function groups()
    {
        $field_groups = array_value($this->fields, 'groups');

        $groups = array(
            'blocks' => _e('Blocks'),
            'media' => _e('Media'),
            'others' => _e('Others'),
        );

        if (is_array($field_groups) && $field_groups) {
            $groups = array_merge($groups, $field_groups);
        }

        return $groups;
    }

    /**
     * Elements
     *
     * @return array
     */
    public function elements()
    {
        $elements = array();
        $field_elements = array_value($this->fields, 'elements');

        $elements['blocks'] = array(
            'text_heading' => [
                'title' => _e('Heading'),
                'icon' => '<i class="ri-heading"></i>',
                'type' => 'tinymce',
                'tinymce' => 'heading',
                'html' => '<h2>' . _e('Heading') . '</h2>',
            ],
            'text_editor' => [
                'title' => _e('Text editor'),
                'icon' => '<i class="ri-font-size-2"></i>',
                'type' => 'tinymce',
                'html' => '<p>' . _e('Start typing...') . '</p>',
            ],
            'text_list' => [
                'title' => _e('List'),
                'icon' => '<i class="ri-list-check"></i>',
                'type' => 'tinymce',
                'tinymce' => 'list',
                'html' => '<ul><li>' . _e('Start typing...') . '</li></ul>',
            ],
            'text_quote' => [
                'title' => _e('Quote'),
                'icon' => '<i class="ri-double-quotes-l"></i>',
                'type' => 'tinymce',
                'tinymce' => 'quote',
                'html' => '<blockquote><p>' . _e('Start typing...') . '</p></blockquote>',
            ],
            'text_table' => [
                'title' => _e('Table'),
                'icon' => '<i class="ri-table-2"></i>',
                'type' => 'tinymce',
                'tinymce' => 'table',
                'html' => '<table border="1" style="border-collapse: collapse; width: 100%;"><tr><td></td><td></td></tr></table>',
            ],
            'horizontal_line' => [
                'title' => _e('Horizontal line'),
                'icon' => '<i class="ri-separator"></i>',
                'type' => 'html',
                'html' => '<hr>',
            ],
            'html_code' => [
                'title' => _e('HTML code'),
                'icon' => '<i class="ri-code-s-slash-line"></i>',
                'type' => 'input',
                'input_type' => 'textarea',
                'input_attrs' => 'rows="5"',
                'input_class' => 'form-control ceditor-input-code',
            ],
            'short_code' => [
                'title' => _e('Short code'),
                'icon' => '<i class="ri-brackets-line"></i>',
                'type' => 'input',
                'input_type' => 'text',
                'input_attrs' => '',
                'input_class' => 'form-control ceditor-input-short-code',
            ],
        );

        $elements['media'] = array(
            'media_image' => [
                'title' => _e('Image'),
                'icon' => '<i class="ri-image-fill"></i>',
                'type' => 'image',
                'render_template' => 'image_block',
            ],
            'media_gallery' => [
                'title' => _e('Gallery'),
                'icon' => '<i class="fas fa-images"></i>',
                'type' => 'gallery',
                'render_template' => 'gallery_block',
            ],
            'media_audio' => [
                'title' => _e('Audio'),
                'icon' => '<i class="ri-disc-line"></i>',
                'type' => 'audio',
                'render_template' => 'audio',
            ],
            'media_video' => [
                'title' => _e('Video'),
                'icon' => '<i class="ri-video-fill"></i>',
                'type' => 'video',
                'render_template' => 'video',
            ],
            'media_instagram' => [
                'title' => 'Instagram',
                'icon' => '<i class="ri-instagram-line"></i>',
                'type' => 'social_media',
                'embed' => 'instagram',
                'render_template' => 'social_media',
            ],
            'media_facebook' => [
                'title' => 'Facebook',
                'icon' => '<i class="ri-facebook-fill"></i>',
                'type' => 'social_media',
                'embed' => 'facebook',
                'render_template' => 'social_media',
            ],
            'media_pinterest' => [
                'title' => 'Pinterest',
                'icon' => '<i class="ri-pinterest-line"></i>',
                'type' => 'social_media',
                'embed' => 'pinterest',
                'render_template' => 'social_media',
            ],
            'media_tiktok' => [
                'title' => 'TikTok',
                'icon' => '<i class="ri-music-fill"></i>',
                'type' => 'social_media',
                'embed' => 'tiktok',
                'render_template' => 'social_media',
            ],
            'media_twitter' => [
                'title' => 'Twitter',
                'icon' => '<i class="ri-twitter-fill"></i>',
                'type' => 'social_media',
                'embed' => 'twitter',
                'render_template' => 'social_media',
            ],
            'media_vk' => [
                'title' => 'VK',
                'image' => 'images/icons/ce-vk.svg',
                'type' => 'social_media',
                'embed' => 'vk',
                'render_template' => 'social_media',
            ],
            'media_vimeo' => [
                'title' => 'Vimeo',
                'icon' => '<i class="ri-vimeo-fill"></i>',
                'type' => 'social_media',
                'embed' => 'vimeo',
                'render_template' => 'social_media',
            ],
            'media_youtube' => [
                'title' => 'Youtube',
                'icon' => '<i class="ri-youtube-fill"></i>',
                'type' => 'social_media',
                'embed' => 'youtube',
                'render_template' => 'social_media',
            ],
        );

        if (is_array($field_elements) && $field_elements) {
            foreach ($field_elements as $field_element_key => $field_element) {
                if (isset($elements[$field_element_key])) {
                    $elements[$field_element_key] = array_merge($elements[$field_element_key], $field_element);
                } else {
                    $elements[$field_element_key] = $field_element;
                }
            }
        }

        $elements['groups'] = $this->groups();

        return $elements;
    }
}
