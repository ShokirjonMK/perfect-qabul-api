<?php
// JSON output
function json_output($type = 'error', $array = array())
{
    $output = [
        'error' => true,
        'success' => false,
        'message' => _e('An error occurred while processing your request. Please try again.'),
    ];

    if ($type == 'success') {
        $output = [
            'error' => false,
            'success' => true,
            'message' => '',
        ];
    }

    if (isset($array['message'])) {
        $output['message'] = $array['message'];
    }

    return $output;
}

// Gender
function gender($key = null)
{
    $array = array(
        '1' => _e('Male'),
        '0' => _e('Female'),
    );

    if (is_numeric($key)) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Category sorting
function category_sorting($key = false)
{
    $array = array(
        'date-desc' => _e('New items'),
        'date-asc' => _e('Old items'),
        'price-asc' => _e('Price: low to high'),
        'price-desc' => _e('Price: high to low'),
        'name-asc' => _e('Name: A-Z'),
        'name-desc' => _e('Name: Z-A'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Subcategory view types
function subcategory_view_types($key = false)
{
    $array = array(
        'hidden' => _e('Hidden'),
        '1-col' => _e('1 column'),
        '2-col' => _e('2 columns'),
        '3-col' => _e('3 columns'),
        '4-col' => _e('4 columns'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Category products view types
function category_products_view_types($key = false)
{
    $array = array(
        'grids' => _e('Grids'),
        'list' => _e('List'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Category products view icon
function category_products_view_icon($key = false)
{
    $output = '';

    switch ($key) {
        case 'grids':
            $output = '<i class="ri-grid-fill"></i>';
            break;

        default:
            $output = '<i class="ri-list-check"></i>';
            break;
    }

    return $output;
}

// Posts column types
function posts_column_types($key = false)
{
    $array = array(
        '1-col' => _e('1 column'),
        '2-col' => _e('2 columns'),
        '3-col' => _e('3 columns'),
        '4-col' => _e('4 columns'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Posts sorting
function posts_sorting($key = false)
{
    $array = array(
        'latest' => _e('Latest'),
        'oldets' => _e('The oldest'),
        'az' => _e('A-Z'),
        'za' => _e('Z-A'),
        'comments' => _e('Most commented'),
        'reading' => _e('Most read'),
        'views' => _e('Most viewed'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Length types
function length_type_list($key = false)
{
    $array = array(
        'm' => _e('Meter'),
        'cm' => _e('Centimeter'),
        'mm' => _e('Millimeter'),
        'in' => _e('Inch'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Weight types
function weight_type_list($key = false)
{
    $array = array(
        't' => _e('Tonne'),
        'kg' => _e('Kilogram'),
        'g' => _e('Gram'),
        'mg' => _e('Milligram'),
        'lb' => _e('Pounds'),
        'l' => _e('Liter'),
        'ml' => _e('Milliliter'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Product special types
function product_special_types($key = false)
{
    $array = array(
        'is_recomended' => _e('Featured product'),
        'is_sponsored' => _e('Sponsored product'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Company status types list
function company_status_types($key = false)
{
    $array = array(
        'confirmed' => _e('Confirmed company'),
        'sponsored' => _e('Sponsored company'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Company types list
function company_types($key = false)
{
    $array = array(
        'mchj' => _e('МЧЖ'),
        'ooo' => _e('ООО'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}

// Company person status list
function company_person_status($key = false)
{
    $array = array(
        'individual' => _e('Individual'),
        'legal-person' => _e('Legal person'),
    );

    if ($key) {
        return isset($array[$key]) ? $array[$key] : '';
    }

    return $array;
}
