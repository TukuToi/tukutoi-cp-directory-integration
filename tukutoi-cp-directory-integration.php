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
 * @package           Cp_Plgn_Drctry
 *
 * @wordpress-plugin
 * Plugin Name:       CP Plugin Directory
 * Plugin URI:        https://www.tukutoi.com/
 * Description:       Integrates the ClassicPress Plugin Directory into the ClassicPress Admin Interface.
 * Version:           1.1.4
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
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CP_PLGN_DRCTRY_VERSION', '1.1.4' );

/**
 * Define the Plugin basename
 */
define( 'CP_PLGN_DRCTRY_BASE_NAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 *
 * This action is documented in includes/class-cp-plgn-drctry-activator.php
 * Full security checks are performed inside the class.
 */
function cp_plgn_drctry_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cp-plgn-drctry-activator.php';
	Cp_Plgn_Drctry_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 *
 * This action is documented in includes/class-cp-plgn-drctry-deactivator.php
 * Full security checks are performed inside the class.
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
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cp-plgn-drctry.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Generally you will want to hook this function, instead of callign it globally.
 * However since the purpose of your plugin is not known until you write it, we include the function globally.
 *
 * @since    1.0.0
 */
function cp_plgn_drctry_run() {

	$plugin = new Cp_Plgn_Drctry();
	$plugin->run();

}
add_action( 'init', 'cp_plgn_drctry_run' );
