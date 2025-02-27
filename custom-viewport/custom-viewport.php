<?php
/*
Plugin Name: Custom Viewport Adjuster
Description: Tự động điều chỉnh viewport cho các thiết bị có màn hình từ 768px đến 1366px.
Version: 1.0
Author: Bạn
*/

if (!defined('ABSPATH')) exit; // Ngăn truy cập trực tiếp

add_filter('generate_meta_viewport', function () {
    return '<meta name="viewport" content="width=device-width,initial-scale=1" id="viewport-meta" />';
});

function wpb_hook_javascript() {
    ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var sw = window.innerWidth;
    var viewportMeta = document.getElementById("viewport-meta");

    if (sw < 1366 && sw >= 768) {
        var scale = sw / 1366;
        viewportMeta.setAttribute("content", "width=1366px, initial-scale=" + scale);
    }
});

window.addEventListener("orientationchange", function() {
    setTimeout(function() {
        var sw = window.innerWidth;
        var viewportMeta = document.getElementById("viewport-meta");

        if (sw < 1366 && sw >= 768) {
            var scale = sw / 1366;
            viewportMeta.setAttribute("content", "width=1366px, initial-scale=" + scale);
        } else {
            viewportMeta.setAttribute("content", "width=device-width, initial-scale=1");
        }
    }, 300);
});
</script>
<?php
}
add_action('wp_head', 'wpb_hook_javascript');