<?php

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'LBV_COMMENT_EDIT_TIME', 300 );
define( 'LBV_COMMENT_MIN_LENGTH', 3 );
define( 'LBV_COMMENT_MAX_LENGTH', 300 );
define( 'LBV_COMMENT_FLOOD_TIME', 10 );
define( 'LBV_IP_RATE_LIMIT', 5 );
define( 'LBV_IP_RATE_WINDOW', 60 );

class LBV_Ajax_Comment {

    private static $instance = null;

    private $allowed_comment_tags = [
            'a' => [
                    'href' => true,
                    'title' => true,
                    'rel' => true
            ],
            'br' => [],
            'p' => [],
            'strong' => [],
            'em' => [],
            'b' => [],
            'i' => [],
            'code' => [],
            'blockquote' => [
                    'cite' => true
            ]
    ];

    private $blocked_pattern = '/\[url|\[\/url\]|\.ru(?![a-z])|bitcoin|casino|viagra|porn|sex|xxx/i';
    private $cached_ip = null;
    private $cached_user_id = null;

    public function __construct() {
        add_filter('comment_text', [$this, 'add_edit_button'], 10, 2);
        add_filter('comment_flood_filter', [$this, 'lbv_throttle_comment_flood'], 10, 3);
        add_filter('comment_form_default_fields', [$this, 'lbv_remove_comment_fields'], 999);
        add_filter('preprocess_comment', [$this, 'lbv_comment_check'], 10);
        add_filter('manage_edit-comments_columns', [$this, 'add_user_agent_column']);
        add_action('manage_comments_custom_column', [$this, 'show_user_agent_column'], 10, 2);
        add_filter('comment_form_defaults', [$this, 'lbv_override_comment_form_defaults'], 10);

        add_action('wp_ajax_lbv_comment', [$this, 'lbv_comment']);
        add_action('wp_ajax_nopriv_lbv_comment', [$this, 'lbv_comment']);

        add_action('wp_ajax_lbv_load_more', [$this, 'lbv_load_more']);
        add_action('wp_ajax_nopriv_lbv_load_more', [$this, 'lbv_load_more']);

        add_action('wp_ajax_lbv_get_comment_nonce', [$this, 'get_comment_nonce']);
        add_action('wp_ajax_nopriv_lbv_get_comment_nonce', [$this, 'get_comment_nonce']);

        if (is_user_logged_in()) {
            $this->cached_user_id = get_current_user_id();
        }
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_comment_nonce() {
        if (ob_get_length()) {
            ob_end_clean();
        }

        wp_send_json_success([
                'nonce' => wp_create_nonce('lbv_posts_nonce')
        ]);
    }

    public function lbv_override_comment_form_defaults( $defaults ) {

        if ( ! is_user_logged_in() ) {

            $login_url = wp_login_url( get_permalink() );

            $message = sprintf(
                    __( 'You must be %s to post a comment.', 'lbv' ),
                    sprintf(
                            '<a href="%s" rel="nofollow">%s</a>',
                            esc_url( $login_url ),
                            esc_html__( 'logged in', 'lbv' )
                    )
            );

            $defaults['must_log_in'] =
                    '<p class="must-log-in">' . $message . '</p>';

        } else {

            $user = wp_get_current_user();

            $message = sprintf(
                    __( 'Logged in as %1$s. %2$s %3$s', 'lbv' ),
                    esc_html( $user->display_name ),
                    sprintf(
                            '<a href="%s" rel="nofollow">%s</a>',
                            esc_url( get_edit_user_link() ),
                            esc_html__( 'Edit your profile', 'lbv' )
                    ),
                    sprintf(
                            '<a href="%s" rel="nofollow">%s</a>',
                            esc_url( wp_logout_url( get_permalink() ) ),
                            esc_html__( 'Log out?', 'lbv' )
                    )
            );

            $defaults['logged_in_as'] =
                    '<p class="logged-in-as">' . $message . '</p>';
        }

        return $defaults;
    }

    public function add_user_agent_column($columns) {
        $columns['user_agent'] = __('User Agent', 'lbv');
        return $columns;
    }

    public function show_user_agent_column($column, $comment_ID) {
        if ($column === 'user_agent') {
            $comment = get_comment($comment_ID);
            echo esc_html($comment->comment_agent);
        }
    }

    public function lbv_comment() {
        try {
            if (!check_ajax_referer('lbv_posts_nonce', 'nonce', false)) {
                wp_send_json_error(__('Security check failed.', 'lbv'));
            }

            $ip = $this->get_client_ip();
            if (!$this->check_ip_rate_limit($ip)) {
                wp_send_json_error(__('Too many comments. Please wait a moment before commenting again.', 'lbv'));
            }

            $comment_post_ID = isset($_POST['comment_post_ID']) ? absint($_POST['comment_post_ID']) : 0;
            $comment_parent = isset($_POST['comment_parent']) ? absint($_POST['comment_parent']) : 0;
            $comment_content = isset($_POST['comment']) ? wp_kses($_POST['comment'], $this->allowed_comment_tags) : '';

            $comment_content_trimmed = trim($comment_content);
            if (empty($comment_content_trimmed)) {
                wp_send_json_error(__('Comment cannot be empty.', 'lbv'));
            }

            $length = mb_strlen(strip_tags($comment_content_trimmed), 'UTF-8');
            if ($length < LBV_COMMENT_MIN_LENGTH) {
                wp_send_json_error(sprintf(__('Comment is too short. Minimum %d characters.', 'lbv'), LBV_COMMENT_MIN_LENGTH));
            }
            if ($length > LBV_COMMENT_MAX_LENGTH) {
                wp_send_json_error(sprintf(__('Comment is too long. Maximum %d characters.', 'lbv'), LBV_COMMENT_MAX_LENGTH));
            }

            if (!$this->validate_comment_content($comment_content_trimmed)) {
                wp_send_json_error(__('Comment contains prohibited content.', 'lbv'));
            }

            if (isset($_POST['data_editing']) && $_POST['data_editing'] === '1') {
                $this->handle_edit_comment($comment_content, $comment_post_ID);
                return;
            }

            $this->handle_new_comment($comment_post_ID, $comment_parent, $comment_content);

        } catch (Exception $e) {
            error_log('LBV Comment Error: ' . $e->getMessage());
            wp_send_json_error(__('An error occurred. Please try again.', 'lbv'));
        }
    }

    private function check_ip_rate_limit($ip) {
        if (current_user_can('manage_options')) {
            return true;
        }

        $cache_key = 'lbv_ip_limit_' . md5($ip);
        $rate_data = get_transient($cache_key);

        if ($rate_data === false) {
            set_transient($cache_key, ['count' => 1, 'time' => time()], LBV_IP_RATE_WINDOW);
            return true;
        }

        $count = isset($rate_data['count']) ? $rate_data['count'] : 0;
        $first_time = isset($rate_data['time']) ? $rate_data['time'] : time();

        if ((time() - $first_time) > LBV_IP_RATE_WINDOW) {
            set_transient($cache_key, ['count' => 1, 'time' => time()], LBV_IP_RATE_WINDOW);
            return true;
        }

        if ($count >= LBV_IP_RATE_LIMIT) {
            return false;
        }

        $rate_data['count'] = $count + 1;
        set_transient($cache_key, $rate_data, LBV_IP_RATE_WINDOW);
        return true;
    }

    private function validate_comment_content($content) {
        return !preg_match($this->blocked_pattern, $content);
    }

    private function has_links($content) {
        return preg_match('/https?:\/\//i', $content);
    }

    private function handle_edit_comment($comment_content, $comment_post_ID) {
        $comment_ID = isset($_POST['edit_comment_id']) ? absint($_POST['edit_comment_id']) : 0;

        if (!$comment_ID) {
            wp_send_json_error(__('Invalid comment ID.', 'lbv'));
        }

        $comment = get_comment($comment_ID);
        if (!$comment) {
            wp_send_json_error(__('Comment not found.', 'lbv'));
        }

        $user_id = $this->get_cached_user_id();
        if (!$user_id || intval($comment->user_id) !== $user_id) {
            wp_send_json_error(__('You do not have permission to edit this comment.', 'lbv'));
        }

        if (!$this->allow_comment_edit($comment_ID)) {
            wp_send_json_error(sprintf(__('Edit time expired. You can only edit within %d seconds.', 'lbv'), LBV_COMMENT_EDIT_TIME));
        }

        $comment_approved = $comment->comment_approved;
        if ($this->has_links($comment_content) && $comment_approved == 1) {
            $comment_approved = 0;
        }

        $updated = wp_update_comment([
                'comment_ID' => $comment_ID,
                'comment_content' => $comment_content,
                'comment_approved' => $comment_approved,
        ]);

        if (is_wp_error($updated) || $updated === 0) {
            wp_send_json_error(__('Failed to update comment.', 'lbv'));
        }

        clean_comment_cache($comment_ID);
        delete_transient('lbv_comment_count_' . $comment_post_ID);

        $response_data = [
                'comment_edit' => wp_kses($comment_content, $this->allowed_comment_tags),
        ];

        if ($comment_approved == 0 && $comment->comment_approved == 1) {
            $response_data['moderation_notice'] = __('Your comment has been updated and is now pending moderation because it contains links.', 'lbv');
        }

        wp_send_json_success($response_data);
    }

    private function handle_new_comment($comment_post_ID, $comment_parent, $comment_content) {
        $user_id = $this->get_cached_user_id();

        if (!$this->check_duplicate_hash($comment_post_ID, $user_id, $comment_content)) {
            wp_send_json_error(__('You have already submitted this comment.', 'lbv'));
        }

        $comment_data = [
                'comment_post_ID'      => $comment_post_ID,
                'comment_parent'       => $comment_parent,
                'comment_content'      => $comment_content,
                'user_id'              => $user_id,
                'comment_author_IP'    => $this->get_client_ip(),
                'comment_agent'        => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : '',
                'comment_type'         => '',
                'comment_author_url'   => ''
        ];

        $has_links = $this->has_links($comment_content);
        if ($has_links && !current_user_can('moderate_comments')) {
            $comment_data['comment_approved'] = 0;
        }

        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            $comment_data['comment_author']       = $current_user->display_name;
            $comment_data['comment_author_email'] = $current_user->user_email;
        } else {
            $author = isset($_POST['author']) ? sanitize_text_field($_POST['author']) : '';
            $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

            if (empty($author) || empty($email)) {
                wp_send_json_error(__('Name and email are required.', 'lbv'));
            }

            if (!is_email($email)) {
                wp_send_json_error(__('Invalid email address.', 'lbv'));
            }

            $comment_data['comment_author']       = $author;
            $comment_data['comment_author_email'] = $email;

            if ($has_links) {
                $comment_data['comment_approved'] = 0;
            }
        }

        $comment_id = wp_new_comment($comment_data, true);

        if (is_wp_error($comment_id)) {
            wp_send_json_error($comment_id->get_error_message());
        }

        $comment = get_comment($comment_id);
        if (!$comment) {
            wp_send_json_error(__('Failed to retrieve comment.', 'lbv'));
        }

        delete_transient('lbv_comment_count_' . $comment_post_ID);

        $comment_counts = wp_count_comments($comment_post_ID);
        $total_approved = intval($comment_counts->approved);

        $response_data = [
                'comment_parent' => $comment->comment_parent,
                'total_comments' => $total_approved
        ];

        if ($comment->comment_approved == 1) {
            $response_data['comment_html'] = $this->render_comment_html($comment);
        } else {
            $response_data['moderation_notice'] = __('Your comment is awaiting moderation because it contains links.', 'lbv');
            $response_data['comment_status'] = 'pending';
        }

        wp_send_json_success($response_data);
    }

    private function check_duplicate_hash($post_id, $user_id, $content) {
        $user_key = $user_id ?: $this->get_client_ip();
        $hash = md5($user_key . $post_id . trim(strip_tags($content)));
        $cache_key = 'lbv_comment_hash_' . md5($user_key);

        $last_hash = get_transient($cache_key);

        if ($last_hash === $hash) {
            return false;
        }

        set_transient($cache_key, $hash, 30);
        return true;
    }

    private function format_comment_content($comment) {
        $text = $comment->comment_content;
        $text = wpautop($text);
        $text = make_clickable($text);
        $text = wp_kses_post($text);
        return $text;
    }

    private function render_comment_html($comment) {
        $depth_class = ($comment->comment_parent != 0) ? 'depth-2' : 'depth-1';
        $parent_class = ($comment->comment_parent == 0) ? 'parent' : '';
        $user_classes = '';

        $current_user_id = $this->get_cached_user_id();
        if ($current_user_id && $current_user_id === intval($comment->user_id)) {
            $user_classes = 'byuser comment-author-' . sanitize_html_class($comment->comment_author);
        }

        ob_start();
        ?>
        <li id="comment-<?php echo absint($comment->comment_ID); ?>" class="comment <?php echo esc_attr($user_classes . ' ' . $depth_class . ' ' . $parent_class); ?>">
            <div class="comment-body">
                <div class="comment-author vcard">
                    <?php echo get_avatar($comment, 32); ?>
                </div>

                <div class="comment-content-wrapper">
                    <div class="comment-bubble">
                        <div class="comment-author-name">
                            <?php
                            $author_name = get_comment_author( $comment );
                            $user = get_userdata( $comment->user_id );
                            ?>
                            <strong class="fn">
                                <?php if ( $user ) : ?>
                                    <a href="<?php echo esc_url( get_author_posts_url( $user->ID ) ); ?>">
                                        <?php echo esc_html( $author_name ); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo esc_html__( 'Anonymous User', 'lbv' ); ?>
                                <?php endif; ?>
                            </strong>
                        </div>
                        <div class="comment-content">
                            <?php echo $this->format_comment_content($comment); ?>
                        </div>
                    </div>

                    <div class="comment-meta">
                        <time datetime="<?php echo esc_attr(get_comment_date('c', $comment)); ?>">
                            <?php
                            $comment_timestamp = strtotime($comment->comment_date_gmt . ' GMT');
                            $current_timestamp = time();
                            $time_diff = human_time_diff($comment_timestamp, $current_timestamp);
                            echo esc_html($time_diff) . ' ' . esc_html__('ago', 'lbv');
                            ?>
                        </time>

                        <span class="meta-separator">·</span>

                        <?php if (is_user_logged_in()) :?>
                            <a rel="nofollow" class="comment-reply-link"
                               href="<?php echo esc_url(get_permalink($comment->comment_post_ID)); ?>?replytocom=<?php echo absint($comment->comment_ID); ?>#respond"
                               data-commentid="<?php echo absint($comment->comment_ID); ?>"
                               data-postid="<?php echo absint($comment->comment_post_ID); ?>"
                               data-belowelement="comment-<?php echo absint($comment->comment_ID); ?>"
                               data-respondelement="respond"><?php echo esc_html__('Reply', 'lbv'); ?></a>
                        <?php endif; ?>

                        <?php if ($current_user_id && $current_user_id === intval($comment->user_id)) : ?>
                            <?php if ($this->allow_comment_edit($comment->comment_ID)) : ?>
                                <span class="meta-separator">·</span>
                                <a href="#" class="edit-comment-btn"
                                   data-editing="1"
                                   data-commentid="<?php echo absint($comment->comment_ID); ?>">
                                    <?php echo esc_html__('Edit', 'lbv'); ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </li>
        <?php
        return ob_get_clean();
    }

    public function allow_comment_edit($comment_ID) {
        $comment = get_comment($comment_ID);
        if (!$comment) {
            return false;
        }

        $time_difference = time() - strtotime($comment->comment_date_gmt);
        return $time_difference < LBV_COMMENT_EDIT_TIME;
    }

    public function add_edit_button($comment_text, $comment) {
        if (!$comment || !isset($comment->user_id)) {
            return $comment_text;
        }

        $current_user_id = $this->get_cached_user_id();
        if ($current_user_id && $current_user_id === intval($comment->user_id)) {
            if ($this->allow_comment_edit($comment->comment_ID)) {
                $edit_button = '<a href="#" class="edit-comment-btn" data-editing="1" data-commentid="' . esc_attr($comment->comment_ID) . '">' . esc_html__('Edit', 'lbv') . '</a>';
                $comment_text .= ' ' . $edit_button;
            }
        }
        return $comment_text;
    }

    public function lbv_load_more() {
        try {
            if (!isset($_POST['post_id']) || !isset($_POST['cpage'])) {
                wp_send_json_error(__('Missing parameters.', 'lbv'));
            }

            $post_id = absint($_POST['post_id']);
            $cpage   = absint($_POST['cpage']);

            if (!$post_id || !$cpage) {
                wp_send_json_error(__('Invalid parameters.', 'lbv'));
            }

            global $post;
            $post = get_post($post_id);

            if (!$post) {
                wp_send_json_error(__('Post not found.', 'lbv'));
            }

            setup_postdata($post);

            $walker_path = plugin_dir_path(__FILE__) . 'includes/walker-comment.php';
            if (!class_exists('Lbv_Walker_Comment') && file_exists($walker_path)) {
                require_once $walker_path;
            }

            ob_start();
            wp_list_comments([
                    'walker'      => class_exists('Lbv_Walker_Comment') ? new Lbv_Walker_Comment() : null,
                    'avatar_size' => 32,
                    'page'        => $cpage,
                    'per_page'    => get_option('comments_per_page'),
                    'style'       => 'ul',
                    'short_ping'  => true,
                    'reply_text'  => __('Reply', 'lbv'),
                    'max_depth'   => 3,
            ]);
            $html = ob_get_clean();

            wp_reset_postdata();
            wp_send_json_success(['html' => $html]);

        } catch (Exception $e) {
            error_log('LBV Load More Error: ' . $e->getMessage());
            wp_send_json_error(__('Failed to load comments.', 'lbv'));
        }
    }

    public function lbv_throttle_comment_flood($flood_control, $time_last, $time_new) {
        if (($time_new - $time_last) < LBV_COMMENT_FLOOD_TIME) {
            return true;
        }
        return false;
    }

    public function lbv_remove_comment_fields($fields) {
        unset($fields['url']);
        return $fields;
    }

    public function lbv_comment_check($commentData) {
        $comment_content = trim($commentData['comment_content']);
        $length = mb_strlen(strip_tags($comment_content), 'UTF-8');

        if ($length < LBV_COMMENT_MIN_LENGTH) {
            wp_die(sprintf(__('Comment is too short. Please write at least %d characters.', 'lbv'), LBV_COMMENT_MIN_LENGTH));
        }

        if ($length > LBV_COMMENT_MAX_LENGTH) {
            wp_die(sprintf(__('Comment is too long. Please write no more than %d characters.', 'lbv'), LBV_COMMENT_MAX_LENGTH));
        }

        return $commentData;
    }

    private function get_client_ip() {
        if ($this->cached_ip !== null) {
            return $this->cached_ip;
        }

        $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];

        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    $this->cached_ip = $ip;
                    return $ip;
                }
            }
        }

        $this->cached_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        return $this->cached_ip;
    }

    private function get_cached_user_id() {
        if ($this->cached_user_id !== null) {
            return $this->cached_user_id;
        }

        if (is_user_logged_in()) {
            $this->cached_user_id = get_current_user_id();
        } else {
            $this->cached_user_id = 0;
        }

        return $this->cached_user_id;
    }
}

LBV_Ajax_Comment::get_instance();