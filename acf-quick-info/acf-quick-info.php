<?php
/**
 * Plugin Name: ACF Quick Info
 * Plugin URI:  https://yourwebsite.com
 * Description: Tạo trang cài đặt thông tin Website bằng ACF, giúp dễ dàng chỉnh sửa thông tin.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://yourwebsite.com
 * License:     GPL-2.0+
 * Text Domain: acf-quick-info
 */

// Đảm bảo file không bị truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Tạo trang ACF Options Page
function acf_quick_info_add_options_page()
{
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title' => 'Thay đổi thông tin Website',
            'menu_title' => 'Thông tin Website',
            'menu_slug' => 'quick-info',
            'capability' => 'manage_options',
            'redirect' => false,
            'menu_position' => 2,
        ));
    }
}
add_action('acf/init', 'acf_quick_info_add_options_page');

// Hiển thị thông báo hướng dẫn trong Admin
function acf_quick_info_admin_notice()
{
    $screen = get_current_screen();
    if ($screen && $screen->id === 'toplevel_page_quick-info') {
        ?>
        <div class="notice notice-info">
            <p><strong>Chỉnh sửa thông tin Website</strong></p>
            <p>Chỉnh sửa thông tin và nhấn <b>[Cập nhật]</b> để lưu thay đổi.</p>
            <p>Xem thêm hướng dẫn tại <a href="index.php"><b>[đây]</b></a>.</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'acf_quick_info_admin_notice');

// Thêm CSS để làm đẹp trang ACF Admin
function acf_quick_info_enqueue_styles($hook)
{
    $screen = get_current_screen();
    if ($screen && $screen->id === 'toplevel_page_quick-info') {
        wp_enqueue_style('acf-quick-info-style', plugin_dir_url(__FILE__) . 'assets/styles.css');
    }
}
add_action('admin_enqueue_scripts', 'acf_quick_info_enqueue_styles');