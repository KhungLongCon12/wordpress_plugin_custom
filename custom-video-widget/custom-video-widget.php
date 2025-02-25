<?php
/**
 * Plugin Name: Custom Video Widget
 * Description: Widget video tùy chỉnh cho Elementor.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit; // Chặn truy cập trực tiếp

// Load Elementor Widget
function register_custom_video_widget() {
    require_once(plugin_dir_path(__FILE__) . 'widget-video.php');
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Custom_Video_Widget());
}
add_action('elementor/widgets/widgets_registered', 'register_custom_video_widget');

// Load CSS & JS
function custom_video_assets() {
    wp_enqueue_style('custom-video-style', plugin_dir_url(__FILE__) . 'assets/style.css', [], time()); // Load CSS
    wp_enqueue_script('custom-video-script', plugin_dir_url(__FILE__) . 'assets/script.js', ['jquery'], time(), true); // Load JS
}
add_action('wp_enqueue_scripts', 'custom_video_assets');

?>