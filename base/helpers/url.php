<?php
// Assets URL
function assets_url($url = false)
{
   $assets_url = Yii::$app->params['assets_url'];
    // $assets_url = "http://e.utas.uz/";
    if ($url) {
        return $assets_url . $url;
    }

    return $assets_url;
}

// Admin URL
function admin_url($url = false)
{
    $admin_url = Yii::$app->params['admin_url'];

    if ($url) {
        return $admin_url . $url;
    }

    return $admin_url;
}

// Images URL
function images_url($url = false)
{
    $images_url = assets_url('images/');

    if ($url) {
        return $images_url . $url;
    }

    return $images_url;
}

// Site URL
function site_url($url = false, $language = false)
{
    $prefix = '';
    $site_url = Yii::$app->params['site_url'];

    if ($language === true) {
        $current_lang = get_current_lang();
        $prefix = $current_lang . '/';
    } elseif (is_string($language)) {
        $prefix = $language . '/';
    }

    if ($url) {
        return $site_url . $prefix . $url;
    }

    return $site_url;
}

// Home URL
function home_url($url = false, $language = false)
{
    $prefix = '';
    $site_url = Yii::$app->params['site_url'];

    if ($language === true) {
        $current_lang = get_current_lang();
        $prefix = $current_lang . '/';
    } elseif (is_string($language)) {
        $prefix = $language . '/';
    }

    if ($url) {
        return $site_url . $prefix . $url;
    }

    return $site_url;
}

// Uploads URL
function uploads_url($url = false)
{
    $uploads_url = assets_url('uploads/');

    if ($url) {
        return $uploads_url . $url;
    }

    return $uploads_url;
}

// Get current URL
function get_current_url($scheme = true)
{
    $url = ltrim(Yii::$app->request->url, '/');

    if ($scheme) {
        $url = yii\helpers\Url::base(true) . '/' . $url;
    }

    return $url;
}

// Get previous url
function get_previous_url($default = false)
{
    $request = Yii::$app->request;

    if ($request->referrer) {
        return $request->referrer;
    } elseif ($default) {
        return $default;
    }

    return admin_url();
}

// Set query params to url
function set_query_var($var, $value, $url = null)
{
    if ($var) {
        $params = \Yii::$app->request->queryParams;

        if ($value) {
            $params[$var] = $value;
        }

        if (is_null($url) || empty($url)) {
            $url = get_current_url();
        }

        if ($url) {
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $oldParams);

            if (empty($oldParams) && $params) {
                return rtrim($url, '?') . '?' . http_build_query($params);
            }

            if ($params) {
                $params = array_merge($oldParams, $params);
                return preg_replace('#\?.*#', '?' . http_build_query($params), $url);
            }
        }

        return $url;
    }
}

// Remove query params from url
function remove_query_var($var, $url = null)
{
    if (is_null($url) || empty($url)) {
        $url = get_current_url();
    }

    if (isset($_GET[$var]) && $url) {
        unset($_GET[$var]);

        $params = $_GET;
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $oldParams);

        if (empty($oldParams) && $params) {
            return rtrim($url, '?') . '?' . http_build_query($params);
        }

        if ($params) {
            return preg_replace('#\?.*#', '?' . http_build_query($params), $url);
        }

        return $url;
    }
}

// Get content url
function get_content_url($id, $language = '', $prefix = '')
{
    $i = 0;
    $array = array();
    $object = array();
    $site_url = site_url();
    $content_type = false;
    $content_types = \common\models\Content::contentTypes();

    if ($language == 'admin_lexicon') {
        $language = admin_content_lexicon('lang_code');
    } elseif (empty($language)) {
        $language = get_current_lang();
    }

    if (is_object($id) && $id) {
        $object = $id;
    } elseif (is_numeric($id) && $id > 0) {
        $object = \common\models\ContentInfos::find()
            ->alias('info')
            ->join('INNER JOIN', 'site_content content', 'info.content_id = content.id')
            ->where(['content.id' => $id])
            ->andWhere(['info.language' => $language])
            ->one();
    }

    if ($object) {
        $parent_id = $object->content_id;
        $table_name = \common\models\Content::_tableName();
        $cached_url = \base\libs\Redis::cachedUrl($table_name, $object->content_id, $object->language, 'get');

        if ($cached_url) {
            return $cached_url;
        }

        do {
            $i++;

            $info = \common\models\ContentInfos::find()
                ->alias('info')
                ->join('INNER JOIN', 'site_content content', 'info.content_id = content.id')
                ->where(['content.id' => $parent_id])
                ->andWhere(['info.language' => $language])
                ->with('model')
                ->one();

            if ($info) {
                $array[] = $info->slug;
                $parent_id = $info->model->parent_id;

                if (!$content_type) {
                    $content_type = $info->model->type;
                }
            }
        } while ($info && $parent_id > 0 && $i < 10000);
    }

    if ($content_type) {
        $content_item = filter_array($content_types, ['key' => $content_type], true);

        if ($content_item) {
            $content_type = array_value($content_item, 'slug', $content_type);
        }
    }

    if ($array) {
        krsort($array);
        $string = implode('/', $array);
        $url = site_url($language . '/');

        if ($prefix) {
            $prefix = trim($prefix, '/');
            $site_url = $url . $prefix . '/' . $string;
        } elseif ($content_type && $content_type != 'page') {
            $site_url = $url . $content_type . '/' . $string;
        } else {
            $site_url = $url . $string;
        }

        \base\libs\Redis::cachedUrl($table_name, $object->content_id, $object->language, 'set', $site_url);
    }

    return $site_url;
}

// Get customer url
function get_customer_url($id, $language = '', $prefix = 'customer')
{
    $object = array();
    $site_url = site_url();

    if ($language == 'admin_lexicon') {
        $language = admin_content_lexicon('lang_code');
    } elseif (empty($language)) {
        $language = get_current_lang();
    }

    if (is_numeric($id) && $id > 0) {
        $url_prefix = \common\models\User::$url_prefix;

        $object = \common\models\User::find()
            ->where(['id' => $id])
            ->one();

        if ($object) {
            $prefix = trim($prefix, '/');
            $site_url = site_url($language . '/' . $prefix . '/' . $url_prefix .  $object->id);
        }
    }

    return $site_url;
}

// Get segment url
function get_segment_url($id, $language = '', $prefix = '')
{
    $i = 0;
    $array = array();
    $object = array();
    $site_url = site_url();
    $segment_type = false;
    $segment_types = \common\models\Segment::segmentTypes();

    if ($language == 'admin_lexicon') {
        $language = admin_content_lexicon('lang_code');
    } elseif (empty($language)) {
        $language = get_current_lang();
    }

    if (is_object($id) && $id) {
        $object = $id;
    } elseif (is_numeric($id) && $id > 0) {
        $object = \common\models\SegmentInfos::find()
            ->alias('info')
            ->join('INNER JOIN', 'site_segments segment', 'segment.id = info.segment_id')
            ->where(['segment.id' => $id])
            ->andWhere(['info.language' => $language])
            ->one();
    }

    if ($object) {
        $parent_id = $object->segment_id;
        $table_name = \common\models\Segment::_tableName();
        $cached_url = \base\libs\Redis::cachedUrl($table_name, $object->segment_id, $object->language, 'get');

        if ($cached_url) {
            return $cached_url;
        }

        do {
            $i++;

            $info = \common\models\SegmentInfos::find()
                ->alias('info')
                ->join('INNER JOIN', 'site_segments segment', 'info.segment_id = segment.id')
                ->where(['segment.id' => $parent_id])
                ->andWhere(['info.language' => $language])
                ->with('model')
                ->one();

            if ($info) {
                $array[] = $info->slug;
                $parent_id = $info->model->parent_id;

                if (!$segment_type) {
                    $segment_type = $info->model->type;
                }
            }
        } while ($info && $parent_id > 0 && $i < 10000);
    }

    if ($segment_type) {
        $segment_item = filter_array($segment_types, ['key' => $segment_type], true);

        if ($segment_item) {
            $segment_type = array_value($segment_item, 'slug', $segment_type);
        }
    }

    if ($array) {
        krsort($array);
        $string = implode('/', $array);
        $url = site_url($language . '/');

        if ($prefix) {
            $prefix = trim($prefix, '/');
            $site_url = $url . $prefix . '/' . $string;
        } elseif ($segment_type) {
            $site_url = $url . $segment_type . '/' . $string;
        } else {
            $site_url = $url . 'segment/' . $string;
        }

        \base\libs\Redis::cachedUrl($table_name, $object->segment_id, $object->language, 'set', $site_url);
    }

    return $site_url;
}
