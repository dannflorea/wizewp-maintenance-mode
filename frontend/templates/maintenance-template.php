<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$settings = get_option('wizemamo_settings');
$countdown = $settings['countdown_date_time'] ?? '';
$countdown_text = $settings['countdown_text'] ?? '';
$countdown_parts = $settings['countdown_parts'] ?? ['days', 'hours', 'minutes', 'seconds'];
$image_id = $settings['image'] ?? '';
$image_url = $image_id ? wp_get_attachment_url($image_id) : '';
$logo_id = $settings['logo'] ?? '';
$logo_url = $logo_id ? wp_get_attachment_url($logo_id) : '';
$return_date = $settings['return_date'] ?? '';
$password_enabled = $settings['password_enabled'] ?? false;
$access_password = trim($settings['access_password'] ?? '');
$has_access_cookie = isset($_COOKIE['wizemamo_pass']) && $_COOKIE['wizemamo_pass'] === hash('sha256', $access_password);
$wrong_password = apply_filters('wizemamo_mm_wrong_password', false);
if ( $password_enabled && isset($_POST['wizemamo_password_input'], $_POST['wizemamo_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wizemamo_nonce'])), 'wizemamo_password_submit')) {
    $user_input = sanitize_text_field(wp_unslash($_POST['wizemamo_password_input']));
    if ($user_input === $access_password) {
        setcookie('wizemamo_pass', hash('sha256', $access_password), time() + 3600, COOKIEPATH, COOKIE_DOMAIN);
        if (isset($_SERVER['REQUEST_URI'])) {
            $redirect_url = esc_url_raw(wp_unslash($_SERVER['REQUEST_URI']));
            wp_safe_redirect(esc_url_raw($redirect_url));
            exit;
        }
        exit;
    } else {
        $wrong_password = true;
    }
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo esc_html($settings['heading'] ?? __('This website is under maintenance', 'wizemamo-maintenance-mode')); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php if ($image_url) echo 'style="background-image: url(' . esc_url($image_url) . ');"'; ?>>
    <div class="wizemamo-container">
        <?php if (!$has_access_cookie): ?>
            <?php if ($password_enabled && $access_password): ?>
                <div class="wizemamo-password-form">
                    <?php if ($logo_url): ?>
                        <a href="<?php echo esc_url(home_url()); ?>"><img src="<?php echo esc_url($logo_url); ?>" alt="Logo" class="wizemamo-logo" /></a>
                    <?php endif; ?>
                    <h1><?php echo esc_html($settings['heading'] ?? __('This website is under maintenance', 'wizemamo-maintenance-mode')); ?></h1>
                    <p><?php echo esc_html($settings['message'] ?? __("We'll be back soon with something new!", 'wizemamo-maintenance-mode')); ?></p>

                    <?php if ($countdown): ?>
                        <div class="wizemamo-countdown-container">
                            <?php if ($countdown_text): ?>
                                <p class="wizemamo-countdown-label"><?php echo esc_html($countdown_text); ?></p>
                            <?php endif; ?>
                            <div class="wizemamo-countdown" data-target="<?php echo esc_attr($countdown); ?>" data-parts="<?php echo esc_attr(json_encode($countdown_parts)); ?>"></div>
                        </div>
                    <?php endif; ?>

                    <p><?php esc_html_e('Enter the password to access the site:', 'wizemamo-maintenance-mode'); ?></p>
                    <form method="post">
                        <input type="password" name="wizemamo_password_input" placeholder="<?php esc_attr_e('Password', 'wizemamo-maintenance-mode'); ?>" required />
                        <?php wp_nonce_field('wizemamo_password_submit', 'wizemamo_nonce'); ?>
                        <button type="submit"><?php esc_html_e('Submit', 'wizemamo-maintenance-mode'); ?></button>
                    </form>

                    <?php if ($wrong_password): ?>
                        <p class="wizemamo-error"><?php esc_html_e('Incorrect password. Please try again.', 'wizemamo-maintenance-mode'); ?></p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php if ($logo_url): ?>
                    <a href="<?php echo esc_url(home_url()); ?>"><img src="<?php echo esc_url($logo_url); ?>" alt="Logo" class="wizemamo-logo" /></a>
                <?php endif; ?>
                <h1><?php echo esc_html($settings['heading'] ?? __('This website is under maintenance', 'wizemamo-maintenance-mode')); ?></h1>
                <p><?php echo esc_html($settings['message'] ?? __("We'll be back soon with something new!", 'wizemamo-maintenance-mode')); ?></p>

                <?php if ($countdown): ?>
                    <div class="wizemamo-countdown-container">
                        <?php if ($countdown_text): ?>
                            <p class="wizemamo-countdown-label"><?php echo esc_html($countdown_text); ?></p>
                        <?php endif; ?>
                        <div class="wizemamo-countdown" data-target="<?php echo esc_attr($countdown); ?>" data-parts="<?php echo esc_attr(json_encode($countdown_parts)); ?>"></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($settings['button_text']) && !empty($settings['button_url'])): ?>
                    <?php $btn_color = $settings['button_color'] ?? '#0073aa'; ?>
                    <a href="<?php echo esc_url($settings['button_url']); ?>" class="wizemamo-button" style="background-color: <?php echo esc_attr($btn_color); ?>">
                        <?php echo esc_html($settings['button_text']); ?>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        <p class="sponsored">Powered by <a href="https://www.wizemamo.com" target="_blank" rel="noopener noreferrer">wizemamo</a></p>
    </div>
    <?php wp_footer(); ?>
</body>
</html>