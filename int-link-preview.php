<?php
/**
 * Plugin Name:         Internal Link Preview Tooltip
 * Description:         Displays a preview tooltip when hovering over internal links in posts.
 * Version:             1.0
 * Requires at least:   5.2
 * Requires PHP:        7.4
 * Author:              Chout
 * Author URI:          https://profiles.wordpress.org/nmtnguyen56/
 * License:             GPLv2 or later
 * Text Domain:         int-link-preview
 * Domain Path:         /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// 0. Load plugin textdomain for translations
add_action( 'plugins_loaded', 'ilpt_load_textdomain' );
function ilpt_load_textdomain() {
    load_plugin_textdomain( 'int-link-preview', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

// 1. Initialize Settings Menu
add_action('admin_menu', 'ilpt_add_settings_page');
function ilpt_add_settings_page() {
    add_options_page(
        __('Link Preview Tooltip Settings', 'int-link-preview'), 
        __('Link Preview Tooltip', 'int-link-preview'), 
        'manage_options', 
        'int-link-preview', 
        'ilpt_render_settings_page'
    );
}

// 2. Register Settings
add_action('admin_init', 'ilpt_register_settings');
function ilpt_register_settings() {
    register_setting('ilpt_settings_group', 'ilpt_content_type');
    register_setting('ilpt_settings_group', 'ilpt_word_limit', array('default' => 80));
    // Đăng ký thêm setting cho chữ Read more
    register_setting('ilpt_settings_group', 'ilpt_read_more_text', array('default' => __('Read more', 'int-link-preview')));
}

// 3. Settings Page UI
function ilpt_render_settings_page() { ?>

    <style>
        /* Donate */

        h1>span {
            padding-left: 10px;
            color: dimgray;
            font-size: 14px;
            font-weight: 400;
        }

        .donate {
            a {
                --dash-color-default: #f42d4d;
                --dash-color-hover: #e97084;
                --dash-bg-inner: #FFFFFF;
                position: relative;
                display: inline-block;
                padding: 1px;
                background-image: repeating-linear-gradient(45deg,
                        var(--dash-color-default),
                        var(--dash-color-default) 3px,
                        transparent 0px,
                        transparent 7px);
                background-size: 10px 10px;
                border-radius: 4px;
                text-decoration: none;
                animation: moveDashes 0.6s linear infinite;
            }

            a>span {
                display: block;
                padding: 2px 8px;
                background-color: var(--dash-bg-inner);
                border-radius: 3px;
                color: var(--dash-color-default);
                font-size: 14px;
                font-weight: 400;
                transition: color .2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            a:hover {
                background-image: repeating-linear-gradient(45deg,
                        var(--dash-color-hover),
                        var(--dash-color-hover) 3px,
                        transparent 0px,
                        transparent 7px);
            }

            a:hover>span {
                color: var(--dash-color-hover);
            }
        }
        
        @keyframes moveDashes {
            to {
                background-position: 15px 5px;
            }
        }

        @keyframes gradientBackgroundAnimation {
            0% {
                background-position: 200% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>

    <div class="wrap">
        <h1><?php esc_html_e('Internal Link Preview Tooltip Settings', 'int-link-preview'); ?><span class="author">by <a href="https://profiles.wordpress.org/nmtnguyen56/" target="_blank" rel="noopener noreferrer">Chout</a></span><span class="donate"><?php ilpt_donate_link_html(); ?></span></h1>
        <form method="post" action="options.php">
            <?php settings_fields('ilpt_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Content type to display', 'int-link-preview'); ?></th>
                    <td>
                        <?php $content_type = get_option('ilpt_content_type', 'excerpt'); ?>
                        <label>
                            <input type="radio" name="ilpt_content_type" value="excerpt" <?php checked($content_type, 'excerpt'); ?> /> <?php esc_html_e('Excerpt', 'int-link-preview'); ?>
                        </label><br>
                        <label>
                            <input type="radio" name="ilpt_content_type" value="content" <?php checked($content_type, 'content'); ?> /> <?php esc_html_e('Post Content', 'int-link-preview'); ?>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Number of words to display', 'int-link-preview'); ?></th>
                    <td>
                        <input type="number" name="ilpt_word_limit" value="<?php echo esc_attr(get_option('ilpt_word_limit', 80)); ?>" />
                        <p class="description"><?php esc_html_e('Default is 80 words.', 'int-link-preview'); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Button text', 'int-link-preview'); ?></th>
                    <td>
                        <input type="text" name="ilpt_read_more_text" class="regular-text" value="<?php echo esc_attr(get_option('ilpt_read_more_text', __('Read more', 'int-link-preview'))); ?>" />
                        <p class="description"><?php esc_html_e('Text to display on the link button inside tooltip.', 'int-link-preview'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>

<?php }

// 4. Load Scripts and Styles to Frontend
add_action('wp_enqueue_scripts', 'ilpt_enqueue_scripts');
function ilpt_enqueue_scripts() {
    wp_enqueue_style('ilpt-style', plugin_dir_url(__FILE__) . 'assets/int-link-preview.css', array(), '1.0.0');
    wp_enqueue_script('ilpt-script', plugin_dir_url(__FILE__) . 'assets/int-link-preview.js', array('jquery'), '1.0.0', true);
    
    // Pass AJAX URL and translatable/custom strings to JS
    wp_localize_script('ilpt-script', 'ilpt_ajax', array(
        'ajax_url'     => admin_url('admin-ajax.php'),
        'loading_text' => __('Loading preview...', 'int-link-preview'),
        // Lấy giá trị chữ từ DB ra thay vì fix cứng
        'read_more'    => get_option('ilpt_read_more_text', __('Read more', 'int-link-preview'))
    ));
}

// 5. AJAX handler to get post data
add_action('wp_ajax_ilpt_get_preview', 'ilpt_get_preview_data');
add_action('wp_ajax_nopriv_ilpt_get_preview', 'ilpt_get_preview_data');
function ilpt_get_preview_data() {
    $url = isset($_POST['url']) ? esc_url_raw($_POST['url']) : '';
    $post_id = url_to_postid($url); // Find Post ID based on URL

    if (!$post_id) {
        wp_send_json_error(__('Post not found', 'int-link-preview'));
    }

    $title = get_the_title($post_id);
    $type = get_option('ilpt_content_type', 'excerpt');
    $limit = (int) get_option('ilpt_word_limit', 80);

    // Get content based on settings
    if ($type === 'excerpt') {
        $post = get_post($post_id);
        $content = has_excerpt($post_id) ? $post->post_excerpt : $post->post_content;
    } else {
        $content = get_post_field('post_content', $post_id);
    }

    // Remove shortcodes, HTML tags, and trim words
    $content = strip_tags(strip_shortcodes($content));
    $trimmed_content = wp_trim_words($content, $limit, '...');

    wp_send_json_success(array(
        'title' => $title,
        'content' => $trimmed_content,
        'url' => get_permalink($post_id)
    ));
}

/* Donate */
function ilpt_donate_link_html() {  //_//
	$donate_url = 'https://chout.id.vn/donate';
	printf(
		'<a href="%1$s" target="_blank" rel="noopener noreferrer" class="err-donate-link" aria-label="%2$s"><span>%3$s 🚀</span></a>',
		esc_url( $donate_url ),
		esc_attr__( 'Donate to support this plugin', 'int-link-preview' ),  //-//
		esc_html__( 'Donate', 'int-link-preview' )  //-//
	);
}