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

    private function convert_youtube_url($url) {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        return $url;
    }

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
            'label' => __('Nguồn Video', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'manual' => __('Nhập URL thủ công', 'plugin-name'),
                'posts' => __('Lấy từ bài viết', 'plugin-name'),
                'ACF'=> __('Lấy từ ACF ','plugin-name'),
            ],
            'default' => 'manual',
        ]
    );

    $this->add_control(
        'category',
        [
            'label' => __('Chọn danh mục bài viết', 'plugin-name'),
            'type' => \Elementor\Controls_Manager::SELECT2,
            'options' => $this->get_categories_list(),
            'multiple' => false,
            'condition' => [
                'video_source' => 'posts',
            ],
        ]
    );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'video_url',
            [
                'label' => __('URL Video', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => __('Nhập URL video...', 'plugin-name'),
            ]
        );

        $this->add_control(
            'video_list',
            [
                'label' => __('Danh sách video', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default'=> [
                    ['video_url'=> 'https://youtu.be/aAO_Mw8f04A'], // Mặc định 1 video
                ],
                'title_field' => '{{{ video_url }}}',
                'condition' => [
                    'video_source' => 'manual',
                ],
            ]
        );


        $this->add_control(
            'play_icon',
            [
                'label' => __('Chọn icon', 'plugin-name'),
                    'type'=> \Elementor\Controls_Manager::ICONS,
                    'default'=> [
                        'value'=> 'fas fa-play',
                        'library' => 'solid',
                    ],

            ]
        );

         $this->add_control(
            'icon_color',
            [
                'label' => __('Màu icon', 'plugin-name'),
                    'type'=> \Elementor\Controls_Manager::COLOR,
                    'default' => '#fff',
            ]
        );

        
         $this->add_control(
            'thumbnail_bg',
            [
                'label' => __('Màu nền Thumbnail', 'plugin-name'),
                    'type'=> \Elementor\Controls_Manager::COLOR,
                    'default' => '#00000099',
            ],
        );

        $this->end_controls_section();
    }

    private function get_categories_list() {
        $categories = get_categories();
        $option = [];
        foreach($categories as $category) {
            $option[$category->term_id] = $category->name;
        }
        return $option;
    }

    private function get_latest_videos($category_id, $limit = 3) {
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => $limit,
        'category__in'   => [$category_id],
    ];

    $query = new WP_Query($args);
    // var_dump($query->posts);
    $videos = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            //Kiểm tra xem có sử dụng ACF không 
            if (function_exists('get_field')) {
                $acf_video = get_field('video_url');
                if (!empty($acf_video)) {
                    $videos[] = esc_url($acf_video);
                    continue;
                }
            }
            
            $content = get_the_content();
            preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/', $content, $matches);
            if (!empty($matches[0])) {
                $videos[] = esc_url($matches[0]);
            }
        }
        wp_reset_postdata();
    }
    return $videos;
}

protected function render() {
    wp_enqueue_script('custom-video-script', plugin_dir_url(__FILE__) . 'assets/script.js', ['jquery'], false, true); // check có chay file JS này hay không
    $settings = $this->get_settings_for_display();
    $widget_id = $this->get_id();
    $video_urls = [];

    if ($settings['video_source'] === 'posts') {
    $category_id = !empty($settings['category']) ? $settings['category'] : get_option('default_category');
    $limit = !empty($settings['video_count']['size']) ? $settings['video_count']['size'] : 3;
    $video_urls = $this->get_latest_videos($category_id, $limit);
} elseif ($settings['video_source'] === 'ACF') {
    if (function_exists('get_field')) {
        $acf_videos = get_field('acf_video_urls');
        if (!empty($acf_videos) && is_array($acf_videos)) {
            $video_urls = array_map('esc_url', $acf_videos);
        }
    }
} else {
    if (!empty($settings['video_list']) && is_array($settings['video_list'])) {
        foreach ($settings['video_list'] as $video) {
            $video_urls[] = esc_url($video['video_url']);
        }
    }
}

    if (empty($video_urls)) {
        echo '<p>' . __('Không có video nào để hiển thị.', 'plugin-name') . '</p>';
        return;
    }
    ?>
<section id="widget-video-<?php echo esc_attr($widget_id); ?>" class="widget-video">
    <div class="custom-video-container" data-widget-id="<?php echo esc_attr($widget_id); ?>">
        <?php foreach ($video_urls as $video_url): 
                $video_id = $this->get_youtube_id($video_url);
                if (!$video_id) continue;
            ?>
        <div class="video-item" data-video="<?php echo $video_url; ?>">
            <!-- Hiển thị thumbnail -->
            <div class="video-thumbnail" style="background-image: url('https://img.youtube.com/vi/<?php echo $video_id; ?>/hqdefault.jpg'); background-color: 
                    <?php echo esc_attr($settings['thumbnail_bg']); ?>;">
                <button class="play-btn">
                    <i class="<?php echo esc_attr($settings['play_icon']['value']); ?> "
                        style="color: <?php echo esc_attr($settings['icon_color']); ?>;">
                    </i>
                </button>
            </div>
            <!-- Iframe ẩn đi -->
            <iframe class="custom-video hidden"
                src="https://www.youtube.com/embed/<?php echo $video_id; ?>?enablejsapi=1" frameborder="0"
                allowfullscreen>
            </iframe>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="back-to-list" data-widget-id="<?php echo esc_attr($widget_id); ?>">
        ◀ Quay lại danh sách phát
    </button>
</section>
<?php
}   
}
?>