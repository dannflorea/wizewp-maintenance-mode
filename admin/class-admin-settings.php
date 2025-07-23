<?php

if (!defined('ABSPATH')) exit;

class WIZEMAMO_Admin_Settings {

    public static function init() {
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    public static function register_settings() {
        register_setting('wizemamo_settings_group', 'wizemamo_settings', [__CLASS__, 'sanitize_settings']);

        add_settings_section(
            'wizemamo_main_section',
            __('Plugin Settings', 'wizemamo-maintenance-mode'),
            '__return_false',
            'wizemamo-maintenance-mode'
        );

        self::add_field('enabled', 'Activate maintenance mode', 'field_enabled');
        self::add_field('logo', 'Website Logo', 'field_logo');
        self::add_field('heading', 'Heading', 'field_heading');
        self::add_field('message', 'Message text', 'field_message');
        self::add_field('image', 'Background Image', 'field_image');
        self::add_field('button_text', 'Button Text', 'field_button_text');
        self::add_field('button_url', 'Button Link', 'field_button_url');
        self::add_field('button_color', 'Button Color', 'field_button_color');
        self::add_field('password_enabled', 'Enable password access', 'field_password_enabled');
        self::add_field('access_password', 'Access password', 'field_access_password');
        self::add_field('countdown_enabled', 'Enable countdown', 'field_countdown_enabled');
        self::add_field('countdown_text', 'Countdown Text', 'field_countdown_text');
        self::add_field('countdown_parts', 'Countdown Parts', 'field_countdown_parts');
        self::add_field('countdown_date_time', 'Countdown date & time', 'field_countdown_date_time');
        self::add_field('custom_css', 'Custom CSS (for maintenance page)', 'field_custom_css');
    }
    
    public static function sanitize_settings($input) {
        $sanitized = [];

        $sanitized['enabled'] = isset($input['enabled']) ? 1 : 0;
        $sanitized['password_enabled'] = isset($input['password_enabled']) ? 1 : 0;
        $sanitized['countdown_enabled'] = isset($input['countdown_enabled']) ? 1 : 0;

        $sanitized['heading'] = sanitize_text_field($input['heading'] ?? '');
        $sanitized['message'] = sanitize_textarea_field($input['message'] ?? '');
        $sanitized['access_password'] = sanitize_text_field($input['access_password'] ?? '');
        $sanitized['button_text'] = sanitize_text_field($input['button_text'] ?? '');
        $sanitized['button_url'] = esc_url_raw($input['button_url'] ?? '');
        $sanitized['button_color'] = sanitize_hex_color($input['button_color'] ?? '');
        $sanitized['countdown_text'] = sanitize_text_field($input['countdown_text'] ?? '');
        $sanitized['countdown_date_time'] = sanitize_text_field($input['countdown_date_time'] ?? '');

        $sanitized['logo'] = absint($input['logo'] ?? 0);
        $sanitized['image'] = absint($input['image'] ?? 0);

        $allowed_parts = ['days', 'hours', 'minutes', 'seconds'];
        $sanitized['countdown_parts'] = array_values(
            array_intersect($allowed_parts, $input['countdown_parts'] ?? [])
        );

        $sanitized['custom_css'] = wp_kses_post($input['custom_css'] ?? '');

        return $sanitized;
    }
    
    private static function add_field($id, $label, $callback) {
        add_settings_field(
            $id,
            $label,
            [__CLASS__, $callback],
            'wizemamo-maintenance-mode',
            'wizemamo_main_section'
        );
    }

    public static function field_enabled() {
        $options = get_option('wizemamo_settings');
        ?>
        <label>
            <input type="checkbox" name="wizemamo_settings[enabled]" value="1" <?php checked(1, $options['enabled'] ?? 0); ?> />
            <?php esc_html_e('Show maintenance page for visitors', 'wizemamo-maintenance-mode'); ?>
        </label>
        <?php
    }

    public static function field_logo() {
        $options = get_option('WIZEMAMO_settings');
        $logo_id = $options['logo'] ?? '';
        $logo_url = $logo_id ? wp_get_attachment_url($logo_id) : '';
        ?>
        <div class="wizemamo-logo-upload">
            <input type="hidden" name="wizemamo_settings[logo]" id="wizemamo_logo_id" value="<?php echo esc_attr($logo_id); ?>" />
            <button type="button" class="button" id="wizemamo_logo_upload">
                <?php echo $logo_url ? esc_html_e('Change logo', 'wizemamo-maintenance-mode') : esc_html_e('Choose logo', 'wizemamo-maintenance-mode'); ?>
            </button>
            <?php if ($logo_url): ?>
                <br><img src="<?php echo esc_url($logo_url); ?>" style="margin-top:10px; max-height:80px;" />
            <?php endif; ?>
        </div>
        <?php
    }

    public static function field_heading() {
        $options = get_option('wizemamo_settings');
        ?>
        <input type="text" name="wizemamo_settings[heading]" value="<?php echo esc_attr($options['heading'] ?? 'Website in under maintenance!'); ?>" class="regular-text" />
        <?php
    }

    public static function field_message() {
        $options = get_option('wizemamo_settings');
        ?>
        <textarea name="wizemamo_settings[message]" rows="3" class="large-text"><?php echo esc_textarea($options['message'] ?? 'We will be back soon!'); ?></textarea>
        <?php
    }

    public static function field_image() {
        $options = get_option('wizemamo_settings');
        $image_id = $options['image'] ?? '';
        $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
        ?>
        <div class="wizemamo-image-upload">
            <input type="hidden" name="wizemamo_settings[image]" id="wizemamo_image_id" value="<?php echo esc_attr($image_id); ?>" />
            <button type="button" class="button" id="wizemamo_upload_button">
                <?php echo $image_url ? esc_html_e('Change image', 'wizemamo-maintenance-mode') : esc_html_e('Choose image', 'wizemamo-maintenance-mode'); ?>
            </button>
            <?php if ($image_url): ?>
                <br><img src="<?php echo esc_url($image_url); ?>" style="margin-top:10px; max-height:100px;" />
            <?php endif; ?>
        </div>
        <?php
    }

    public static function field_button_text() {
        $options = get_option('wizemamo_settings');
        ?>
        <input type="text" name="wizemamo_settings[button_text]" value="<?php echo esc_attr($options['button_text'] ?? 'Back to homepage'); ?>" class="regular-text" />
        <?php
    }

    public static function field_button_url() {
        $options = get_option('wizemamo_settings');
        ?>
        <input type="url" name="wizemamo_settings[button_url]" value="<?php echo esc_url($options['button_url'] ?? home_url()); ?>" class="regular-text" />
        <?php
    }

    public static function field_button_color() {
        $options = get_option('wizemamo_settings');
        ?>
        <input type="text" name="wizemamo_settings[button_color]" value="<?php echo esc_attr($options['button_color'] ?? '#0073aa'); ?>" class="regular-text" placeholder="#0073aa" />
        <?php
    }
    
    public static function field_password_enabled() {
        $options = get_option('wizemamo_settings');
        ?>
        <label>
            <input type="checkbox" name="wizemamo_settings[password_enabled]" value="1" <?php checked(1, $options['password_enabled'] ?? 0); ?> />
            <?php esc_html_e('Require password to view site', 'wizemamo-maintenance-mode'); ?>
        </label>
        <?php
    }

    public static function field_access_password() {
        $options = get_option('wizemamo_settings');
        ?>
        <input type="text" name="wizemamo_settings[access_password]" value="<?php echo esc_attr($options['access_password'] ?? ''); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Users must enter this password to bypass the maintenance page.', 'wizemamo-maintenance-mode'); ?></p>
        <?php
    }

    public static function field_countdown_enabled() {
        $options = get_option('wizemamo_settings');
        ?>
        <label>
            <input type="checkbox" name="wizemamo_settings[countdown_enabled]" value="1" <?php checked(1, $options['countdown_enabled'] ?? 0); ?> />
            <?php esc_html_e('Show countdown until site is back', 'wizemamo-maintenance-mode'); ?>
        </label>
        <?php
    }
    
    public static function field_countdown_text() {
        $options = get_option('wizemamo_settings');
        ?>
        <input type="text" name="wizemamo_settings[countdown_text]" value="<?php echo esc_attr($options['countdown_text'] ?? 'Remaining time:'); ?>" class="regular-text" />
        <?php
    }

    public static function field_countdown_parts() {
        $options = get_option('wizemamo_settings');
        $parts = $options['countdown_parts'] ?? ['days', 'hours', 'minutes', 'seconds'];
        $available = ['days' => 'Days', 'hours' => 'Hours', 'minutes' => 'Minutes', 'seconds' => 'Seconds'];

        foreach ($available as $key => $label) {
            $checked = in_array($key, $parts) ? 'checked' : '';
            echo '<label style="display:inline-block; margin-right:15px;">';
            echo '<input type="checkbox" name="wizemamo_settings[countdown_parts][]" value="' . esc_attr($key) . '" ' . esc_attr($checked) . '> ' . esc_html($label);
            echo '</label>';
        }
    }
    
    public static function field_countdown_date_time() {
        $options = get_option('wizemamo_settings');
        ?>
        <input type="datetime-local" name="wizemamo_settings[countdown_date_time]" value="<?php echo esc_attr($options['countdown_date_time'] ?? ''); ?>" class="regular-text" />
        <p class="description"><?php esc_html_e('Target date & time for the countdown.', 'wizemamo-maintenance-mode'); ?></p>
        <?php
        $countdown = $options['countdown_date_time'] ?? '2025-06-10T00:00';
    }

    public static function field_custom_css() {
    $options = get_option('wizemamo_settings');
    ?>
    <textarea name="wizemamo_settings[custom_css]" id="wizemamo_custom_css" rows="10"><?php echo esc_textarea($options['custom_css'] ?? ''); ?></textarea>
    <p class="description"><?php esc_html_e('This CSS will be injected on the maintenance page.', 'wizemamo-maintenance-mode'); ?></p>
    <?php
}
}