<?php
/*
Plugin Name: Custom Viewport Adjuster
Description: Tự động điều chỉnh viewport cho các thiết bị có màn hình từ 768px đến 1366px.
Version: 1.1
Author: Bạn
*/

if (!defined('ABSPATH'))
    exit; // Ngăn truy cập trực tiếp

// Thêm meta viewport vào <head>
add_action('wp_head', function () {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1" id="viewport-meta">';
});

// Thêm JavaScript vào trang
add_action('wp_footer', function () {
    ?>
    <script>
        (function () {
            function adjustViewport() {
                var sw = window.innerWidth;
                var viewportMeta = document.getElementById("viewport-meta");

                if (!viewportMeta) return; // Nếu không có thẻ meta, thoát luôn

                if (sw < 1366 && sw >= 768) {
                    var scale = sw / 1366;
                    var newContent = "width=1366px, initial-scale=" + scale;
                } else {
                    var newContent = "width=device-width, initial-scale=1";
                }

                // Chỉ thay đổi nếu giá trị mới khác giá trị hiện tại (tối ưu hiệu suất)
                if (viewportMeta.getAttribute("content") !== newContent) {
                    viewportMeta.setAttribute("content", newContent);
                    console.log("Viewport updated:", newContent);
                }
            }

            // Chạy khi tải trang
            document.addEventListener("DOMContentLoaded", adjustViewport);

            // Cập nhật khi resize cửa sổ hoặc xoay màn hình
            window.addEventListener("resize", adjustViewport);
            window.addEventListener("orientationchange", function () {
                setTimeout(adjustViewport, 300);
            });
        })();
    </script>
    <?php
});