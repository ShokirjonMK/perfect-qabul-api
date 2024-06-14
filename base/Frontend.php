<?php

namespace base;

use common\models\ContentInfos;
use common\models\SegmentInfos;
use common\models\User;

class Frontend
{
    /**
     * Get content to controller
     *
     * @param array $url_array
     * @param string $language
     * @param array $where
     * @return mixed
     */
    public static function contentToController($url_array, $language = null, $where_query = array())
    {
        $object = false;
        $walked_id_array = array();
        $current_langs = get_languages('lang_code');

        if (is_null($language)) {
            $language = get_current_lang();
        }

        if ($url_array && in_array($language, $current_langs)) {
            $item = false;
            $draw_url = array();
            $translations = new \stdClass();
            $translation_links = array();

            foreach ($url_array as $url_item) {
                $query = ContentInfos::find()
                    ->alias('info')
                    ->join('INNER JOIN', 'site_content content', 'content.id = info.content_id')
                    ->where(['info.slug' => $url_item, 'info.language' => $language]);

                if ($where_query && is_array($where_query)) {
                    $query->andWhere($where_query);
                }

                $item = $query->with('model')->one();

                if ($item) {
                    $draw_url[] = $item->slug;
                    $walked_id_array[] = $item->model->id;
                }
            }

            if ($draw_url && $item && $item->model->deleted != 1 && $item->model->status == 1) {
                $content_id = $item->content_id;

                $translations = ContentInfos::find()
                    ->alias('info')
                    ->join('INNER JOIN', 'site_content content', 'content.id = info.content_id')
                    ->where(['content.id' => $content_id, 'content.deleted' => 0, 'content.status' => 1])
                    ->andWhere(['in', 'info.language', $current_langs])
                    ->all();

                if ($translations) {
                    foreach ($translations as $translation) {
                        $info_item_lang = $translation->language;
                        $translation_links[$info_item_lang] = get_content_url($translation, $info_item_lang);
                    }
                }

                \base\Container::push('frontend_translations', $translations);
                \base\Container::push('frontend_translation_links', $translation_links);

                $url_string = implode('/', $url_array);
                $_string = implode('/', $draw_url);

                if ($url_string == $_string) {
                    $object = $item;
                }
            }
        } else if (is_null($url_array) && is_null($url_array) && $where_query) {
            $object = $where_query;
        }

        if ($object) {
            $item = new \stdClass();
            $item->model = new \stdClass();
            $item->oldAttributes = new \stdClass();

            $item->model->info = (object) $object->attributes;
            $item->model->content = (object) $object->model->attributes;

            $item->oldAttributes->info = (object) $object->oldAttributes;
            $item->oldAttributes->content = (object) $object->model->oldAttributes;

            \base\Container::push('frontend_walked_id_array', $walked_id_array);

            return $item;
        }
    }

    /**
     * Get segment to controller
     *
     * @param array $url_array
     * @param string $language
     * @return mixed
     */
    public static function segmentToController($url_array, $language = null, $where_query = array())
    {
        $object = false;
        $walked_id_array = array();
        $current_langs = get_languages('lang_code');

        if (is_null($language)) {
            $language = get_current_lang();
        }

        if ($url_array && in_array($language, $current_langs)) {
            $item = false;
            $draw_url = array();
            $translations = new \stdClass();
            $translation_links = array();

            foreach ($url_array as $url_item) {
                $query = SegmentInfos::find()
                    ->alias('info')
                    ->join('INNER JOIN', 'site_segments segment', 'segment.id = info.segment_id')
                    ->where(['info.slug' => $url_item])
                    ->andWhere(['in', 'info.language', $current_langs]);

                if ($where_query && is_array($where_query)) {
                    $query->andWhere($where_query);
                }

                $item = $query->with('model')->one();

                if ($item) {
                    $draw_url[] = $item->slug;
                    $walked_id_array[] = $item->model->id;
                }
            }

            if ($draw_url && $item && $item->model->deleted != 1 && $item->model->status == 1) {
                $segment_id = $item->segment_id;

                $translations = SegmentInfos::find()
                    ->alias('info')
                    ->join('INNER JOIN', 'site_segments segment', 'segment.id = info.segment_id')
                    ->where(['segment.id' => $segment_id, 'segment.deleted' => 0, 'segment.status' => 1])
                    ->andWhere(['in', 'info.language', $current_langs])
                    ->all();

                if ($translations) {
                    foreach ($translations as $translation) {
                        $info_item_lang = $translation->language;
                        $translation_links[$info_item_lang] = get_segment_url($translation, $info_item_lang);
                    }
                }

                \base\Container::push('frontend_translations', $translations);
                \base\Container::push('frontend_translation_links', $translation_links);

                $url_string = implode('/', $url_array);
                $_string = implode('/', $draw_url);

                if ($url_string == $_string) {
                    $object = $item;
                }
            }
        }

        if ($object) {
            $item = new \stdClass();
            $item->model = new \stdClass();
            $item->oldAttributes = new \stdClass();

            $item->translation_links = $translation_links;
            $item->translations = $translations;

            $item->model->info = (object) $object->attributes;
            $item->model->segment = (object) $object->model->attributes;

            $item->oldAttributes->info = (object) $object->oldAttributes;
            $item->oldAttributes->segment = (object) $object->model->oldAttributes;

            \base\Container::push('frontend_walked_id_array', $walked_id_array);

            return $item;
        }
    }

    /**
     * Get customer to controller
     *
     * @param array $url_array
     * @param string $language
     * @return mixed
     */
    public static function customerToController($url_array, $language = null)
    {
        $object = false;

        if (is_null($language)) {
            $language = get_current_lang();
        }

        if ($url_array && $language) {
            $model = false;
            $draw_url = array();

            foreach ($url_array as $url_item) {
                $id = $url_item;
                $_prefix = User::$url_prefix;
                $_prefix_len = strlen($_prefix);

                if ($_prefix_len > 0) {
                    $id = substr($url_item, $_prefix_len);
                }

                $model = User::find()
                    ->where(['id' => $id, 'status' => User::STATUS_ACTIVE])
                    ->with('profile')
                    ->one();

                if ($model) {
                    $draw_url[] = $_prefix . $model->id;
                }
            }

            if ($draw_url) {
                $url_string = implode('/', $url_array);
                $_string = implode('/', $draw_url);

                if ($url_string == $_string) {
                    $object = $model;
                }
            }
        }

        if ($object) {
            $item = new \stdClass();
            $item->model = new \stdClass();
            $item->oldAttributes = new \stdClass();

            $item->model->customer = (object) $object->attributes;
            $item->model->profile = (object) $object->profile->attributes;

            $item->oldAttributes->customer = (object) $object->oldAttributes;
            $item->oldAttributes->profile = (object) $object->profile->oldAttributes;

            return $item;
        }
    }
}
