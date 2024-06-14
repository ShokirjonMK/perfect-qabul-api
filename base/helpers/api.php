<?php
// Api pagination
function api_pagination()
{
    $limit = 1;
    $offset = 0;

    $_page = input_get('page', 1);
    $_limit = input_get('limit', 10);
    $_offset = input_get('offset', 0);

    if (is_numeric($_limit) && $_limit > 0) {
        $limit = $_limit;
    }

    if (is_numeric($_offset) && $_offset > 0) {
        $offset = $_offset;
    }

    if (is_numeric($_page) && $_page > 1) {
        $offset = ($_page - 1) * $limit;
    }

    $output['limit'] = $limit;
    $output['offset'] = $offset;

    return $output;
}

// JSON output
function api_json_output($type = 'error', $array = array())
{
    $output = [
        'error' => true,
        'statusCode' => 404,
        'success' => false,
        'message' => _e('An error occurred while processing your request.'),
        'data' => array(),
    ];

    if ($type == 'success') {
        $output = [
            'error' => false,
            'statusCode' => 200,
            'success' => true,
            'message' => '',
            'data' => array(),
        ];
    }

    if (isset($array['message'])) {
        $output['message'] = $array['message'];
    }

    return $output;
}

// Simplify AR Model errors
function simplify_errors($errors)
{
    return $errors;
    $result = [];
    foreach ($errors as $lev1) {
        // foreach ($lev1 as $error) {
        $result[] = $lev1;
        // }
    }
    return array_unique($result);
}


// Model errors

function model_errors($errs)
{
    $res = [];
    foreach ($errs as $key => $val) {
        $res[$key] = $val;
    }
    return $res;
}


function double_errors($errs, $trErrs)
{
    $res = [];
    if (isset($errs)) {
        foreach ($errs as $val) {
            foreach ($val as $key => $valIn) {
                $res[$key] = $valIn;
            }
        }
    }

    if (isset($trErrs)) {
        foreach ($trErrs as $tval) {
            foreach ($tval as $tkey => $tvalIn) {
                $res[$tkey] = $tvalIn;
            }
        }
    }
    $result[] = $res;

    return $result;
}

// Api sortby
function api_sortby($sortby, $sort, $prefix = null)
{
    if (strtoupper($sort) == 'ASC') {
        $sort = SORT_ASC;
    } elseif (strtoupper($sort) == 'DESC') {
        $sort = SORT_DESC;
    }

    if ($prefix && is_string($prefix)) {
        $sortby = $prefix . '.' . $sortby;
    }

    return array($sortby => $sort);
}

function isJsonMK($string)
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}
