<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/includes
 * @author     bedas <hello@tukutoi.com>
 */
class Cp_Plgn_Drctry {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cp_Plgn_Drctry_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	protected $plugin_prefix;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'CP_PLGN_DRCTRY_VERSION' ) ) {

			$this->version = CP_PLGN_DRCTRY_VERSION;

		} else {

			$this->version = '1.0.0';

		}

		$this->plugin_name   = 'cp-plgn-drctry';
		$this->plugin_prefix = 'cp_plgn_drctry_';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cp_Plgn_Drctry_Loader. Orchestrates the hooks of the plugin.
	 * - Cp_Plgn_Drctry_i18n. Defines internationalization functionality.
	 * - Cp_Plgn_Drctry_Admin. Defines all hooks for the admin area.
	 * - Cp_Plgn_Drctry_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin and autoloading the Classes.
		 *
		 * @since 1.4.0 Added autoloader.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cp-plgn-drctry-loader.php';

		$this->loader = new Cp_Plgn_Drctry_Loader();
		spl_autoload_register( array( $this->loader, 'autoloader' ) );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cp_Plgn_Drctry_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cp_Plgn_Drctry_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		/**
		 * Security/access check is performed here for all Plugin.
		 */
		if ( current_user_can( 'install_plugins' )
			&& is_user_logged_in()
			&& is_admin()
		) {

			$plugin_admin  = new Cp_Plgn_Drctry_Admin( $this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version() );
			$plugin_manage = new Cp_Plgn_Drctry_Plugin_Fx( $this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version() );

			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_pages' );
			$this->loader->add_action( 'wp_ajax_install_cp_plugin', $plugin_manage, 'install_cp_plugin' );
			$this->loader->add_action( 'wp_ajax_update_cp_plugin', $plugin_manage, 'update_cp_plugin' );
			$this->loader->add_action( 'wp_ajax_deactivate_cp_plugin', $plugin_manage, 'deactivate_cp_plugin' );
			$this->loader->add_action( 'wp_ajax_activate_cp_plugin', $plugin_manage, 'activate_cp_plugin' );
			$this->loader->add_action( 'wp_ajax_delete-plugin', $plugin_manage, 'delete_cp_plugin' );
			$this->loader->add_action( 'wp_ajax_refresh_list', $plugin_manage, 'get_plugins' );

		}
		/**
		 * Add Settings Screen.
		 *
		 * @since 1.3.0
		 */
		if ( current_user_can( 'manage_options' )
			&& is_user_logged_in()
			&& is_admin()
		) {
			$cp_dir_options = new Cp_Plgn_Drctry_Settings( $this->get_plugin_name(), $this->get_plugin_prefix(), $this->get_version() );
			add_action( 'admin_init', array( $cp_dir_options, 'settings_init' ) );
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The unique prefix of the plugin used to uniquely prefix technical functions.
	 *
	 * @since     1.0.0
	 * @return    string    The prefix of the plugin.
	 */
	public function get_plugin_prefix() {
		return $this->plugin_prefix;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cp_Plgn_Drctry_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
