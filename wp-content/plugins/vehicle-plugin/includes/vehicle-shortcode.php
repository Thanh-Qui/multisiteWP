<?php
namespace CustomVehiclePlugin;


class Vehicle_Shortcode implements plugin_vehicle_module {

    // init the shortcodes
    public function init() {
        add_shortcode('vehicle_list', array($this, 'render_vehicle_list'));
        add_shortcode('vehicle_single', array($this, 'render_vehicle_single'));
        add_shortcode('greeting', array($this, 'render_greeting'));
    }

   
    public function vehicle_list_shortcode() {
        ob_start();

        $template_path = plugin_dir_path(__FILE__) . '../templates/vehicle-list.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo esc_html__('Vehicle list template not found.', 'bluehost-blueprint');
        }

        return ob_get_clean();
    }

    //vehicle single shortcode.
    public function vehicle_single_shortcode() {
        ob_start();

        $template_path = plugin_dir_path(__FILE__) . '../templates/vehicle-single.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo esc_html__('Vehicle single template not found.', 'bluehost-blueprint');
        }

        return ob_get_clean();
    }

    public function render_greeting($atts, $content = null) {
        // Set default attributes
        $atts = shortcode_atts(
            [
                'color' => '#ffffff',
            ],
            $atts,
            'greeting'
        );

        // Process content with WordPress autop
        $content = wpautop($content);

        // Build HTML with proper escaping
        $html = '<h1 style="color:' . esc_attr($atts['color']) . ';">';
        $html .= $content;
        $html .= '</h1>';

        return $html;
    }
}