<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/Endoman123
 * @since             1.0.1
 * @package           Robot_Pages
 *
 * @wordpress-plugin
 * Plugin Name:       Robot Pages
 * Plugin URI:        https://github.com/Endoman123/robot-pages
 * Description:       Generate WordPress pages for FRC robots
 * Version:           2.0.0
 * Author:            Jared Tulayan
 * Author URI:        https://github.com/Endoman123
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       robot-pages
 * Domain Path:       /languages
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
define( 'ROBOT_PAGES_VERSION', '2.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-robot-pages-activator.php
 */
function activate_robot_pages() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-robot-pages-activator.php';
	Robot_Pages_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-robot-pages-deactivator.php
 */
function deactivate_robot_pages() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-robot-pages-deactivator.php';
	Robot_Pages_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_robot_pages' );
register_deactivation_hook( __FILE__, 'deactivate_robot_pages' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-robot-pages.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_robot_pages() {

	$plugin = new Robot_Pages();
	$plugin->run();

}
run_robot_pages();
