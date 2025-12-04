<?php

function vehicle_list_shortcode()
{
    // start buffer
    ob_start();

    $vehicle_list_template = plugin_dir_path(__FILE__) . '../templates/vehicle-list.php';
    if (file_exists($vehicle_list_template)) {
        include $vehicle_list_template;
    } else {
        echo 'No matching file found';
    }
    // get data ad clean buffer
    return ob_get_clean();
}
add_shortcode('vehicle_list', 'vehicle_list_shortcode');

function vehicle_single_shortcode()
{
    ob_start();

    $vehicle_single_template = plugin_dir_path(__FILE__) . '../templates/vehicle-single.php';
    if (file_exists($vehicle_single_template)) {
        include $vehicle_single_template;
    } else {
        echo 'No match file found';
    }

    return ob_get_clean();
}
add_shortcode('vehicle_single', 'vehicle_single_shortcode');

// creat shortcode with $atts and $content
function greeting_shortcode($atts, $content = null)
{
    // default value
    $atts = shortcode_atts(
        [
            'color' => '#ffffff',
        ],
        $atts,
        'greeting'
    );

    $content = wpautop($content);
    $html = '<h1 style="color:' . esc_attr($atts['color']) .' ">';
    $html .= $content;
    $html .= '</h1>';

    return $html;
}
add_shortcode('greeting', 'greeting_shortcode');