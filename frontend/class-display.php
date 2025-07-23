<?php

if (!defined('ABSPATH')) exit;

class WIZEMAMO_Display {

    public static function init() {
        add_action('init', [__CLASS__, 'handle_password_form']);
        add_action('template_redirect', [__CLASS__, 'maybe_show_maintenance']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
    }

    public static function handle_password_form() {
        if (
            isset($_POST['wizemamo_password_input'], $_POST['wizemamo_nonce']) &&
            wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wizemamo_nonce'])), 'wizemamo_password_submit')
        ) {
            $settings = get_option('wizemamo_settings');
            $access_password = trim($settings['access_password'] ?? '');
            $user_input = sanitize_text_field(wp_unslash($_POST['wizemamo_password_input']));

            if ($user_input === $access_password) {
                setcookie('wizemamo_pass', hash('sha256', $access_password), time() + 3600, COOKIEPATH, COOKIE_DOMAIN);
                if (isset($_SERVER['REQUEST_URI'])) {
                    $redirect_url = esc_url_raw(wp_unslash($_SERVER['REQUEST_URI']));
                    wp_safe_redirect($redirect_url);
                    exit;
                }
                exit;
            } else {
                add_filter('wizemamo_wrong_password', '__return_true');
            }
        }
    }

    public static function maybe_show_maintenance() {
        if (!self::should_show_maintenance()) {
            return;
        }

        status_header(503);
        include WIZEMAMO_FRONTEND_PATH . 'templates/maintenance-template.php';
        exit;
    }

    public static function enqueue_assets() {
        if (!self::should_show_maintenance()) return;

        wp_enqueue_style(
            'wizemamo-maintenance-style',
            WIZEMAMO_FRONTEND_URL . 'assets/css/style.css',
            [],
            WIZEMAMO_VERSION
        );

        wp_enqueue_script(
            'wizemamo-maintenance-script',
            WIZEMAMO_FRONTEND_URL . 'assets/js/frontend.js',
            ['jquery'],
            WIZEMAMO_VERSION,
            true
        );

        $settings = get_option('wizemamo_settings');

        wp_localize_script('wizemamo-maintenance-script', 'WizeMAMOCountdown', [
            'datetime' => $settings['countdown_date_time'] ?? ''
        ]);

        if (!empty($settings['custom_css'])) {
            wp_add_inline_style('wizemamo-maintenance-style', wp_kses( $settings['custom_css'], [] ));
        }
    }

    public static function should_show_maintenance() {
        $is_preview = ( isset($_GET['wizemamo_preview']) && sanitize_text_field(wp_unslash($_GET['wizemamo_preview'])) === '1' && current_user_can('manage_options')); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin-only preview
        if ( $is_preview ) {
            return true;
        }
        
        if (is_user_logged_in() && current_user_can('manage_options')) {
            return false;
        }

        $settings = get_option('wizemamo_settings');
        $enabled = !empty($settings['enabled']);
        $password_enabled = !empty($settings['password_enabled']);
        $access_password = trim($settings['access_password'] ?? '');

        $cookie_hash = isset($_COOKIE['wizemamo_pass']) ? sanitize_text_field(wp_unslash($_COOKIE['wizemamo_pass'])) : '';
        $stored_hash = $access_password ? hash('sha256', $access_password) : '';

        $user_has_access = $password_enabled && $access_password && ($cookie_hash === $stored_hash);

        return $enabled && !$user_has_access;
    }
}
