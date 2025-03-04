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
        return 'eicon-play';
    }

    public function get_categories() {
        return ['basic'];
    }

    private function get_youtube_id($url) {
    // if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/', $url, $matches)) {
    //     return $matches[1];
    // }
    // return '';
    if (is_array($url)) {
        return ''; // Trả về chuỗi rỗng thay vì tiếp tục xử lý
    }
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches);
    
    return $matches[1] ?? '';
}

    protected function _register_controls() {
        //TAB 1 CONTENT
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Cấu hình video', 'plugin-name'),
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

    // Hiển thị thông tin bài viết
        $this->add_control(
            'show_title',
            [
                'label' => __( 'Show Title', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [ 'video_source' => 'posts' ],
            ]
        );

        $this->add_control(
            'title_limit',
            [
                'label' => __( 'Title Character Limit', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'step' => 1,
                'default' => 25,
                'condition' => [ 
                    'video_source'=> 'posts',
                    'show_title' => 'yes' 
                ],
            ]
        );

        $this->add_control(
            'title_prefix',
            [
                'label' => __( 'Title Prefix', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'condition' => [ 
                    'video_source'=> 'posts',
                    'show_title' => 'yes' 
                ],
            ]
        );

        $this->add_control(
            'title_suffix',
            [
                'label' => __( 'Title Suffix', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'condition' => [ 
                    'video_source'=> 'posts',
                    'show_title' => 'yes' 
                ],
            ]
        );

        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'list_url',
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
                    ['list_url'=> 'https://youtu.be/aAO_Mw8f04A'], // Mặc định 1 video
                ],
                'title_field' => '{{{ list_url }}}',
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
                        'value' => 'fas fa-play',
                        'library' => 'solid',
                    ],
            ]
        );

        //Nút quay lại custom
        $this->add_control(
            'back_to_list',
            [
                'label' => __('Nút quay lại', 'plugin-name'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'Quay lại danh sách phát',
            ]
        );
        $this->end_controls_section(); // End Content TAB 1

        //TAB 2 STYLE
        //Display Advanced Styling Options
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Kiểu hiển thị', 'plugin-name'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
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
        $this->end_controls_section(); // End Display Advanced Styling Options
        
        //Add Display Advanced Title Styling Options
        $this->start_controls_section(
            'title_style_section',
            [
                'label' => __('Title Video', 'plugin-name'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_control(
        'title_color',
        [
            'label' => __( 'Title Color', 'plugin-name' ),
            'type' => \Elementor\Controls_Manager::COLOR,
            'default'=> '#fff',
            'selectors' => [
                '{{WRAPPER}} .video-title' => 'color: {{VALUE}};',
            ],
        ]
    );

    $this->add_group_control(
        \Elementor\Group_Control_Typography::get_type(),
        [
            'name' => 'title_typography',
            'selector' => '{{WRAPPER}} .video-title',
            'default' => [
                'font-size' => '16px',
                'font-weight' => '400',
            ],
            'condition' => [
                'show_title' => 'yes',
            ],
        ]
    );

    $this->add_control(
        'padding', 
        [
            'label'=> __('Padding', 'plugin-name'),
            'type'=> \Elementor\Controls_Manager::DIMENSIONS,
            'size_units'=> ['px','%', 'em', 'rem'],
            'default'=> [
                'top'=> '0',
                'right'=> '15',
                'bottom'=> '20',
                'left'=> '15',
                'unit'=> 'px',
                'isLinked'=> false,   
            ],
            'selectors' => [
                '{{WRAPPER}} .video-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

     $this->add_control(
        'margin', 
        [
            'label'=> __('Margin', 'plugin-name'),
            'type'=> \Elementor\Controls_Manager::DIMENSIONS,
            'size_units'=> [  'px','%', 'em', 'rem'],
            'default'=> [
                'top'=> '0',
                'right'=> '0',
                'bottom'=> '8',
                'left'=> '0',
                'unit'=> 'px',
                'isLinked'=> false,   
            ],
            'selectors' => [
                '{{WRAPPER}} .video-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );

    $this->add_control(
        'title_alignment',
        [
            'label' => __( 'Title Alignment', 'plugin-name' ),
            'type' => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => __( 'left', 'plugin-name' ),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => __( 'center', 'plugin-name' ),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => __( 'right', 'plugin-name' ),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'toggle' => true,
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .video-title' => 'text-align: {{VALUE}};',
            ],
        ]
    );

    $this->end_controls_section(); // End Title Style
    }

    private function get_categories_list() {
        $categories = get_categories();
        $option = [];
        foreach($categories as $category) {
            $option[$category->term_id] = $category->name;
        }
        return $option;
    }

    private function get_latest_videos($category_id, $limit = 10) {
    $args = [
        'cat' => $category_id,
        'posts_per_page' => $limit,
        'post_status' => 'publish',
        'post_type' => 'post',
        'orderby' => 'date',
    ];

    $query = new WP_Query($args);
    $videos_data = [];

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $title = get_the_title();
            $description = get_the_excerpt();
            $video_urls = [];
            $video_lists = []; // Danh sách video từ bài viết

            //Kiểm tra xem có sử dụng ACF không 
            if (function_exists('get_field')) { 
                $acf_video = get_field('youtube_video');
                if (!empty($acf_video)) {
                    $video_urls = esc_url($acf_video);
                    $videos_data[] = [
                        'title' => $title,
                        'description' => $description,
                        'url' => $video_urls,
                        'list_url' => $video_lists,
                    ];
                    continue;
                }
            }

            //Nếu không có video từ ACF thì lấy video từ bài viết
            $content = get_the_content();
            preg_match_all('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([\w\-]+)/', $content, $matches);
            if (empty($video_urls)) {
                if (!empty($matches[0])) {
                    foreach ($matches[0] as $url) {
                        if ($this->is_video_url($url)) {
                            $video_urls[] = esc_url($url);
                        }        
                    }
                }
            }

            //Nếu có URL hợp lệ, thêm vào danh sách
            if (!empty($video_urls)) {
                $videos_data[] = [
                    'title'=> $title,
                    'description'=> $description,
                    'url'=> $video_urls,
                    'list_url' => $video_lists,
                ];
            }

            //Nếu đã đủ số lượng video thì thoát vòng lặp
            if (count($video_urls) >= $limit) {
                break;
            }
        }
    }
    
    wp_reset_postdata();
    
    return !empty($videos_data) ? array_slice($videos_data, 0, $limit) : [];
}

//Kiểm tra phải Video từ youtube không
private function is_video_url($url) {
    return preg_match('/(youtube\.com|youtu\.be|vimeo\.com|\.mp4|\.webm)/', $url);
}
protected function render() {
    wp_enqueue_script('custom-video-script', plugin_dir_url(__FILE__) . 'assets/script.js', ['jquery'], false, true); // check có chay file JS này hay không
    $settings = $this->get_settings_for_display();
    $widget_id = $this->get_id();
    $videos_data = [];

    // Lấy video từ bài viết
    if ($settings['video_source'] === 'posts') {
        $category_id = !empty($settings['category']) ? $settings['category'] : get_option('default_category');
        $limit = !empty($settings['video_count']['size']) ? $settings['video_count']['size'] : 3;
        $videos_data = $this->get_latest_videos($category_id, $limit);
        
    if(empty($videos_data)) {
        echo '<p>' . __('Không có video nào để hiển thị.', 'plugin-name') . '</p>';
        return;
    }
    } elseif ($settings['video_source'] === 'ACF') {
        if (function_exists('get_field')) {
            $acf_videos = get_field('acf_video_urls');
            if (!empty($acf_videos)) {
                //Nếu ACF lưu nhiều video (Repeater Field or Array [])
                if (is_array($acf_videos))
                {
                    foreach ($acf_videos as $acf_video) {
                        if(!empty($acf_video)){
                            $videos_data[] = [
                                'title' => '', // Không có tiêu đề từ ACF
                                'description' => '', // Không có mô tả từ ACF
                                'video_url' => esc_url($acf_video),
                                'list_url' => '',
                            ];
                        }
                    }
                } else {
                    // Nếu ACF chỉ lưu 1 URL duy nhất
                    $videos_data[] = [
                        'title' => '', // Không có tiêu đề từ ACF
                        'description' => '', // Không có mô tả từ ACF
                        'video_url' => esc_url($acf_videos),
                        'list_url' => '',
                    ];
                }
            }
        }
    } else {
        if (!empty($settings['video_list']) && is_array($settings['video_list'])) {
            foreach ($settings['video_list'] as $video) {
                $videos_data[] = [
                    'title'=> '', // Không có tiêu đề
                    'description'=> '', // Không có mô tả
                    'url'=> '',
                    'list_url'=> esc_url($video['list_url']),
                ];
            }
        }
    }
    //Kiểm tra xem có video nào không
    if (empty($videos_data)) {
        echo '<p>' . __('Không có video nào để hiển thị.', 'plugin-name') . '</p>';
        return;
    }
    ?>

<!--Render widget -->
<section id="widget-video-<?php echo esc_attr($widget_id); ?>" class="widget-video">
    <div class="custom-video-container" data-widget-id="<?php echo esc_attr($widget_id); ?>">
        <?php foreach ($videos_data as $video): 
                    // $video_id = $this->get_youtube_id($video['url']);
                    // $video_url = $video['url'];
                    $video_url = !empty($video['list_url']) ? $video['list_url'] : $video['url'];
                    $video_id = $this->get_youtube_id($video_url);
                    $video_title = $video['title'];
                    if (!$video_id) continue;

                    //Thêm prefix & suffix vào tiêu đề
                    $video_title = (!empty($settings['title_prefix']) ? esc_html($settings['title_prefix']) . ' ' : '') 
                                . $video_title 
                                . (!empty($settings['title_suffix']) ? ' ' . esc_html($settings['title_suffix']) : '');
                    //Giới hạn ký tự
                    if (!empty($settings['title_limit']) && is_numeric($settings['title_limit'])) {
                        $limit = (int) $settings['title_limit'];
                        if (mb_strlen($video_title, 'UTF-8') > $limit) {
                            $video_title = mb_substr($video_title, 0, $limit, 'UTF-8') . '...';
                        }
                    }
        ?>
        <div class="video-item" data-video="<?php echo $video_url; ?>">
            <!-- Hiển thị thumbnail -->
            <div class="video-thumbnail"
                style="background-image: url('https://img.youtube.com/vi/<?php echo $video_id; ?>/hqdefault.jpg');">
                <button type="button" aria-label="Play Video" class="play-btn">
                    <i class="<?php echo esc_attr($settings['play_icon']['value']); ?> "
                        style="color: <?php echo esc_attr($settings['icon_color']); ?>;">
                    </i>
                </button>
                <div class="overlay"></div>
                <p
                    class="video-post-info <?php echo (!empty($settings['show_title']) && $settings['show_title'] === 'yes') ? 'video-title' : 'hidden'; ?>">
                    <?php echo esc_html($video_title ); ?>
                </p>
            </div>
            <!-- Iframe ẩn đi -->
            <iframe class=" custom-video hidden" aria-label="Video Player"
                src="https://www.youtube.com/embed/<?php echo $video_id; ?>?enablejsapi=1" frameborder="0"
                allowfullscreen>
            </iframe>
        </div>
        <?php endforeach; ?>
    </div>
    <button type="button" aria-label="Back To List" class="back-to-list"
        data-widget-id="<?php echo esc_attr($widget_id); ?>">
        <?php echo esc_html($settings['back_to_list']); ?>
    </button>
</section>
<?php
}   
}
?>