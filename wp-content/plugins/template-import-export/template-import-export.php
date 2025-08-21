<?php
/**
 * Plugin Name: Template Import/Export
 * Plugin URI: https://example.com/
 * Description: Import and export WordPress block templates and template assignments between sites
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: template-import-export
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TIE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TIE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TIE_PLUGIN_VERSION', '1.0.0');

// Load the main plugin class
require_once TIE_PLUGIN_PATH . 'includes/class-template-import-export.php';

// Initialize the plugin
function tie_init() {
    $plugin = new Template_Import_Export();
    $plugin->run();
}
add_action('plugins_loaded', 'tie_init');

// Activation hook
register_activation_hook(__FILE__, 'tie_activate');
function tie_activate() {
    // Add activation tasks if needed
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'tie_deactivate');
function tie_deactivate() {
    // Add deactivation tasks if needed
    flush_rewrite_rules();
}