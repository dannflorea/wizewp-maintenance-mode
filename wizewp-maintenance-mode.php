<?php
/*
 Plugin Name: WizeWP Maintenance Mode
 Plugin URI: https://wizewp.com/plugins/maintenance-mode/
 Description: Lightweight and modern maintenance mode plugin. Custom text, image, countdown, password access. Built with simplicity and speed in mind.
 Version: 1.0.0
 Author: WizeWP
 Author URI:  https://www.wizewp.com
 License:     GPL2
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 Text Domain: wizewp-maintenance-mode
 Domain Path: /languages/
 */

if (!defined('ABSPATH')) exit;

// Define constants
define('WIZEMAMO_VERSION', '1.0.0');
define('WIZEMAMO_PATH', plugin_dir_path(__FILE__));
define('WIZEMAMO_URL', plugin_dir_url(__FILE__));

define('WIZEMAMO_ADMIN_PATH', WIZEMAMO_PATH . 'admin/');
define('WIZEMAMO_CORE_PATH', WIZEMAMO_PATH . 'core/');
define('WIZEMAMO_FRONTEND_PATH', WIZEMAMO_PATH . 'frontend/');

define('WIZEMAMO_ADMIN_URL', WIZEMAMO_URL . 'admin/');
define('WIZEMAMO_FRONTEND_URL', WIZEMAMO_URL . 'frontend/');

// Load core functionality
require_once WIZEMAMO_CORE_PATH . 'class-wizewp-loader.php';

// Initialize WizeWP loader (create main menu if needed)
WIZEMAMO_Loader::init();

// Load admin functionality if in wp-admin
if (is_admin()) {
    require_once WIZEMAMO_ADMIN_PATH . 'class-admin-ui.php';
    require_once WIZEMAMO_ADMIN_PATH . 'class-admin-settings.php';
    WIZEMAMO_Admin_UI::init();
    WIZEMAMO_Admin_Settings::init();
}

// Load frontend logic
require_once WIZEMAMO_FRONTEND_PATH . 'class-display.php';
WIZEMAMO_Display::init();
