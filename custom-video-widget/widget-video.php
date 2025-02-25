<?php
if (!defined('ABSPATH')) exit;

class Custom_Video_Widget extends \Elementor\Widget_Base {
    public function get_name() {
        return 'custom_video_widget';
    }

    public function get_title() {
        return __('Custom Video Widget', 'plugin-name');
    }

    public function get_icon() {
        return 'eicon-video-camera';
    }

    public function get_categories() {
        return ['basic'];
    }

    // private function convert_youtube_url($url) {
    //     if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/', $url, $matches)) {
    //         return 'https://www.youtube.com/embed/' . $matches[1];
    //     }
    //     return $url;
    // }

    private function get_youtube_id($url) {
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/', $url, $matches)) {
        return $matches[1];
    }
    return '';
}

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Cấu hình Video', 'plugin-name'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'video_source',
            [
                'label'=> __('Nguồn Video','plugin-name'),
                'type'=> \Elementor\Controls_Manager::SELECT,
                'option'=> [
                    'manual' => __('Nhập tay', 'plugin-name'),
                    'category' => __('Lấy từ danh mục', 'plugin-name'),
                ],
                    'default'=> 'manual',
            ]
        );

        for ($i = 1; $i <= 3; $i++) {
            $this->add_control(
                'video_url_' . $i,
                [
                    'label' => __('URL Video ' . $i, 'plugin-name'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'input_type' => 'url',
                    'placeholder' => __('Nhập URL video...', 'plugin-name'),
                ]
            );
        }

        $this->add_control(
        'video_width',
        [
            'label' => __('Chiều rộng video (%)', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 50,
                    'max' => 100,
                ],
            ],
            'default' => [
                'unit' => '%',
                'size' => 100,
            ],
        ]
    );

    $this->add_control(
            'video_category',
            [
                'label' => __('Chọn Danh Mục', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_categories_list(),
                'multiple' => false,
                'condition' => [
                    'video_source' => 'category',
                ],
            ]
        );

        $this->end_controls_section();
    }

    private function get_categories_list() {
        $categories = get_categories(['hide_empty' => false]);
        $options = [];
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }
        return $options;
    }

    private function get_videos_from_category($category_id) {
        $args = [
            'post_type' => 'post',
            'posts_per_page' => 3,
            'cat' => $category_id,
            'meta_query' => [
                [
                    'key' => 'video_url',
                    'compare' => 'EXISTS',
                ]
            ],
        ];
        $query = new WP_Query($args);
        $videos = [];

        while ($query->have_posts()) {
            $query->the_post();
            $videos[] = get_field('video_url');
        }
        wp_reset_postdata();
        return $videos;
    }

    protected function render() {
    wp_enqueue_script('custom-video-script', plugin_dir_url(__FILE__) . 'assets/script.js', ['jquery'], false, true);
    $settings = $this->get_settings_for_display();
    $video_width = $settings['video_width']['size'] . '%';
    $video = [];
    ?>
<div class="custom-video-container">
    <?php for ($i = 1; $i <= 3; $i++): 
                $video_url = esc_url($settings['video_url_' . $i]);
                $video_id = $this->get_youtube_id($video_url);
                if (!$video_id) continue;
                ?>
    <div class="video-item" data-video="<?php echo $video_url; ?>">
        <!-- Hiển thị thumbnail -->
        <div class="video-thumbnail"
            style="background-image: url('https://img.youtube.com/vi/<?php echo $video_id; ?>/hqdefault.jpg');">
            <button class="play-btn"><i class="eicon-play"></i></button>
        </div>
        <!-- Iframe ẩn đi -->
        <iframe class="custom-video hidden" src="https://www.youtube.com/embed/<?php echo $video_id; ?>?enablejsapi=1"
            frameborder="0" allowfullscreen></iframe>
    </div>

    <?php endfor; ?>
</div>
<div><button class="back-to-list">◀ Quay lại danh sách phát</button></div>
<?php
}}
?>