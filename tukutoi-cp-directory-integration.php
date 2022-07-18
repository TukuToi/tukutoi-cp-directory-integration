<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress or ClassicPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.tukutoi.com/
 * @since             1.0.0
 * @package           Plugins\CPDirectoryIntegration
 * @author            Beda Schmid <beda@tukutoi.com>
 *
 * @wordpress-plugin
 * Plugin Name:       CP Plugin Directory
 * Plugin URI:        https://www.tukutoi.com/
 * Description:       Integrates the ClassicPress Plugin Directory and Plugins stored on GitHub (tagged with classicpress-plugin) into the ClassicPress Admin Interface.
 * Version:           1.4.0
 * Author:            bedas
 * Requires at least: 4.9.15
 * Requires PHP:      7.0.0
 * Tested up to:      6.0.0
 * Author URI:        https://www.tukutoi.com/
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       cp-plgn-drctry
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 *
 * Start at version 1.0.0 and use SemVer.
 * Rename this for your plugin and update it as you release new versions.
 *
 * @link https://semver.org
 * @var string $CP_PLGN_DRCTRY_VERSION The Plugin Version.
 */
define( 'CP_PLGN_DRCTRY_VERSION', '1.4.0' );

/**
 * Define the Plugin basename.
 *
 * @var string $CP_PLGN_DRCTRY_BASE_NAME The Plugin Basename.
 */
define( 'CP_PLGN_DRCTRY_BASE_NAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 *
 * Full security checks are performed inside the Cp_Plgn_Drctry_Activator Class.
 *
 * @see Cp_Plgn_Drctry_Activator
 */
function cp_plgn_drctry_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cp-plgn-drctry-activator.php';
	Cp_Plgn_Drctry_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * Full security checks are performed inside the Cp_Plgn_Drctry_Deactivator class.
 *
 * @see Cp_Plgn_Drctry_Deactivator
 */
function cp_plgn_drctry_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cp-plgn-drctry-deactivator.php';
	Cp_Plgn_Drctry_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'cp_plgn_drctry_activate' );
register_deactivation_hook( __FILE__, 'cp_plgn_drctry_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 *
 * @see Cp_Plgn_Drctry
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cp-plgn-drctry.php';

/**
 * Add a Cron Operation to check for plugins daily.
 *
 * @since 1.4.0
 */
if ( ! wp_next_scheduled( 'cp_plgn_drctry_cron_hook' ) ) {
	wp_schedule_event( time(), 'daily', 'cp_plgn_drctry_cron_hook' );
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 * @return void
 */
function cp_plgn_drctry_run() {

	$plugin = new Cp_Plgn_Drctry();
	$plugin->run();

}
add_action( 'init', 'cp_plgn_drctry_run' );
