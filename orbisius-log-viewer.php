<?php
/**
 * Plugin Name: Orbisius Log Viewer
 * Plugin URI: https://orbisius.com/products/wordpress-plugins/orbisius-log-viewer/
 * Description: View and download log files from your WordPress installation. Quickly identify debug.log, error_log, and other log files.
 * Version: 1.0.0
 * Author: Orbisius
 * Author URI: https://orbisius.com
 * License: GPLv2 or later
 * Text Domain: orbisius-log-viewer
 */

defined('ABSPATH') || exit();

define('ORBISIUS_LOG_VIEWER_BASE_DIR', __DIR__);
define('ORBISIUS_LOG_VIEWER_BASE_PLUGIN', __FILE__);
define('ORBISIUS_LOG_VIEWER_VERSION', '1.0.0');

// Load utilities
require_once ORBISIUS_LOG_VIEWER_BASE_DIR . '/includes/util.php';

// Load admin functionality if in the admin dashboard
if (is_admin()) {
    require_once ORBISIUS_LOG_VIEWER_BASE_DIR . '/admin/admin.php';
    Orbisius_Log_Viewer_Admin::getInstance()->installHooks();
}
