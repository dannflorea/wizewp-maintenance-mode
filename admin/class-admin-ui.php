<?php

if (!defined('ABSPATH')) exit;

class WIZEMAMO_Admin_UI {

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_plugin_subpage']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('admin_bar_menu', [__CLASS__, 'add_admin_bar_link'], 100);
        add_action('admin_notices', [__CLASS__, 'render_remote_notices']);
        add_action('wp_ajax_wizemamo_dismiss_notice', [__CLASS__, 'ajax_dismiss_notice']);
    }

    public static function add_plugin_subpage() {
        add_submenu_page(
            'wizemamo',
            __('Maintenance Mode', 'wizemamo-maintenance-mode'),
            __('Maintenance Mode', 'wizemamo-maintenance-mode'),
            'manage_options',
            'wizemamo-maintenance-mode',
            [__CLASS__, 'render_plugin_page']
        );
    }
    public static function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_wizemamo' && $hook !== 'wizewp_page_wizemamo-maintenance-mode') return;

        wp_enqueue_style(
            'wizemamo-admin-style',
            WIZEMAMO_ADMIN_URL . 'assets/css/admin.css',
            [],
            WIZEMAMO_VERSION
        );
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();
        
        wp_enqueue_code_editor(['type' => 'text/css']);
        wp_enqueue_script('wp-codemirror');
        wp_enqueue_style('wp-codemirror');
        
        wp_enqueue_script(
            'wizemamo-codemirror-init',
            WIZEMAMO_ADMIN_URL . 'assets/js/codemirror-init.js',
            ['jquery', 'wp-codemirror'],
            WIZEMAMO_VERSION,
            true
        );
        wp_enqueue_script(
            'wizemamo-admin-js',
            WIZEMAMO_ADMIN_URL . 'assets/js/admin.js',
            ['jquery', 'wp-color-picker', 'wp-codemirror'],
            WIZEMAMO_VERSION,
            true
        );
        wp_localize_script('wizemamo-admin-js', 'wizemamo_admin_data', [
            'nonce' => wp_create_nonce('wizemamo_dismiss_notice'),
        ]);
    }

    public static function add_admin_bar_link($wp_admin_bar) {
        if (!current_user_can('manage_options')) {
            return;
        }

        $wp_admin_bar->add_node([
            'id'    => 'wizemamo_maintenance',
            'title' => '⚙️ Maintenance Mode',
            'href'  => admin_url('admin.php?page=wizemamo-maintenance-mode'),
            'meta'  => ['title' => 'Maintenance Mode Settings'],
        ]);
    }

    public static function render_plugin_page() {
        ?>
        <div class="wrap">
        <div class="wizemamo-admin-wrapper">
            <h1>Maintenance Mode</h1>
            <p class="description">Activate a professional maintenance mode for your website.</p>
            <p>Thank you for using our plugin! If you are satisfied, please reward it a full five-star <span class="stars">★★★★★</span> rating </p>
            <p><a href="https://wordpress.org/support/plugin/wizewp-maintenance-mode/reviews/" target="_blank">Reviews</a> | <a href="https://wordpress.org/plugins/wizewp-maintenance-mode/#developers" target="_blank">Changelog</a> | <a href="https://wordpress.org/support/plugin/wizewp-maintenance-mode/" target="_blank">Discussion</a></p>
            <form method="post" action="options.php">
            <?php
                settings_fields('wizemamo_settings_group');
                do_settings_sections('wizemamo-maintenance-mode');
                submit_button(__('Save settings', 'wizemamo-maintenance-mode'));
            ?>
            </form>
            <hr>
            <h2>🔍 Real-time preview</h2>
            <p>See what your maintenance page looks like. Make sure to save settings before!</p>
            <iframe id="wizemamo-preview" src="<?php echo esc_url(home_url('?wizemamo_preview=1')); ?>" style="width: 100%; height: 800px; border: 1px solid #ddd; border-radius: 8px; margin-top: 20px;"></iframe>
        </div>
        </div>
        <?php
    }
    public static function render_remote_notices() {
        if (!current_user_can('manage_options')) return;
        $nonce = wp_create_nonce('wizemamo_dismiss_notice');
        $dismissed = get_user_meta(get_current_user_id(), 'wizemamo_dismissed_notices', true);
        if (!is_array($dismissed)) $dismissed = [];

        $response = wp_remote_get('https://wizewp.com/info/notices.json', ['timeout' => 5]);
        if (is_wp_error($response)) return;

        $notices = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($notices) || !is_array($notices)) return;

        foreach ($notices as $notice) {
            if (empty($notice['id']) || in_array($notice['id'], $dismissed)) continue;
            ?>
            <div class="notice notice-info is-dismissible wizemamo-remote-notice" data-notice-id="<?php echo esc_attr($notice['id']); ?>">
                <p><strong><?php echo esc_html($notice['title']); ?></strong></p>
                <p><?php echo wp_kses_post($notice['message']); ?>
                    <?php if (!empty($notice['link_url']) && !empty($notice['link_text'])): ?>
                        <a href="<?php echo esc_url($notice['link_url']); ?>" target="_blank" class="button button-primary">
                            <?php echo esc_html($notice['link_text']); ?>
                        </a>
                    <?php endif; ?>
                </p>
            </div>
            <?php
        }
    }
    
    public static function ajax_dismiss_notice() {
        $nonce = isset($_POST['_ajax_nonce']) ? sanitize_text_field(wp_unslash($_POST['_ajax_nonce'])) : '';
        if (!wp_verify_nonce($nonce, 'wizemamo_dismiss_notice')) {
            wp_send_json_error(['message' => 'Nonce check failed']);
            wp_die();
        }

        $notice_id = isset($_POST['notice_id']) ? sanitize_text_field(wp_unslash($_POST['notice_id'])) : '';
        if (!$notice_id) {
            wp_send_json_error(['message' => 'Invalid notice ID']);
            wp_die();
        }

        $dismissed = get_user_meta(get_current_user_id(), 'wizemamo_dismissed_notices', true);
        if (!is_array($dismissed)) $dismissed = [];

        $dismissed[] = $notice_id;
        $dismissed = array_unique($dismissed);
        update_user_meta(get_current_user_id(), 'wizemamo_dismissed_notices', $dismissed);

        wp_send_json_success();
    }
}