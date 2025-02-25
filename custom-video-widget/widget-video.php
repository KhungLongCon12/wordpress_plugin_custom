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

        for ($i = 1; $i <= 3; $i++) {
            $this->add_control(
                'video_url_' . $i,
                [
                    'label' => __('URL Video ' . $i, 'plugin-name'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'input_type' => 'url',
                    'placeholder' => __('Nhập URL video...', 'plugin-name'),
                    'condition' => [
                        'video_source'=> 'manual',
                    ]
                ]
            );
        }

        $this->add_control(
            'video_count',
            [   
                'label'=> __('Số video hiển thị', 'plugin_name'),
                'type'=> \Elementor\Controls_Manager::NUMBER,
                'min'=> 1,
                'max' => 10,
                'step' => 1,
                'default' => 3,
            ]
        );

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
            ]
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

    private function get_latest_videos($category_id) {
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 3,
        'category__in'   => [$category_id],
        'meta_query'     => [
            [
                'key'     => 'video_url', // ACF hoặc custom field chứa link YouTube
                'compare' => 'EXISTS',
            ]
        ]
    ];

    $query = new WP_Query($args);
    $videos = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
             $video_url = function_exists('get_field') ? get_field('video_url') : get_post_meta(get_the_ID(), 'video_url', true);
       if (!empty($video_url)) {
                $videos[] = $video_url;
            }
        }
        wp_reset_postdata();
    }

    return $videos;
}

    protected function render() {
    wp_enqueue_script('custom-video-script', plugin_dir_url(__FILE__) . 'assets/script.js', ['jquery'], false, true);
    $settings = $this->get_settings_for_display();
    $video_width = $settings['video_width']['size'] . '%';
    $video_url =[];
    if ($settings['video_source'] === 'posts') {
        $category_id = !empty($settings['category']) ? $settings['category'] : get_option('default_category');
        $limit = !empty($settings['video_count']) ? $settings['video_count'] : 3;
        $video_urls = $this->get_latest_videos($category_id, $limit);
    } else {
        for ($i = 1; $i <= 3; $i++) {
            $video_urls[] = esc_url($settings['video_url_' . $i]);
        }
    }
    ?>
<div class="custom-video-container">
    <?php foreach ($video_urls as $video_url): 
            $video_id = $this->get_youtube_id($video_url);
            if (!$video_id) continue;
        ?>
    <div class="video-item" data-video="<?php echo $video_url; ?>">
        <!-- Hiển thị thumbnail -->
        <div class="video-thumbnail" style="background-image: url('https://img.youtube.com/vi/<?php echo $video_id; ?>/hqdefault.jpg'); background-color: 
            <?php echo esc_attr($settings['thumbnail_bg']); ?>;">
            <button class="play-btn">
                <i class="<?php echo esc_attr($settings['play_icon']['value']); ?>"
                    style="color: <?php echo esc_attr($settings['icon_color']); ?>;"></i>
            </button>
        </div>
        <!-- Iframe ẩn đi -->
        <iframe class="custom-video hidden" src="https://www.youtube.com/embed/<?php echo $video_id; ?>?enablejsapi=1"
            frameborder="0" allowfullscreen></iframe>
    </div>

    <?php endforeach; ?>
</div>
<div><button class="back-to-list">◀ Quay lại danh sách phát</button></div>
<?php
}}
?>