<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://example.com
 * @since             1.0.0
 * @package           Quick_Chat
 *
 * @wordpress-plugin
 * Plugin Name:       Instant Chat Assistant
 * Plugin URI:        https://github.com/abdullahnart/instant-chat-assistant
 * Description:       Instant Chat Assistant 
 * Version:           1.0.4
 * Author:            Abdullah Naseem
 * Author URI:        https://github.com/abdullahnart/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       instant-chat-assistant
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'QUICK_CHAT_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-quick-chat-activator.php
 */
function activate_instchas_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-quick-chat-activator.php';
	Quick_Chat_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-quick-chat-deactivator.php
 */
function deactivate_instchas_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-quick-chat-deactivator.php';
	Quick_Chat_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_instchas_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_instchas_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-quick-chat.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_instchas_plugin() {

	$plugin = new Quick_Chat();
	$plugin->run();

}
run_instchas_plugin();
