<?php
/**
 * Adds a class to handle helper Functions for Plugin Management.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Plugins\CPDirectoryIntegration\Admin
 * @author     Beda Schmid <beda@tukutoi.com>
 */

/**
 * Class to handle helper Functions for Plugin Management.
 *
 * Adds functions for:
 * Install Plugins
 * Activate Plugins
 * Deactivate Plugins
 * Delete Plugins
 * Update Plugins
 * Check if has update
 * Check if is installed
 * Check if is active
 * Create safe Plugin slug
 *
 * @package    Plugins\CPDirectoryIntegration\Admin
 * @author     Beda Schmid <beda@tukutoi.com>
 */
class Cp_Plgn_Drctry_Plugin_Fx {

	/**
	 * Load Arbitrary Functions.
	 */
	use Cp_Plgn_Drctry_Fx, Cp_Plgn_Drctry_GitHub, Cp_Plgn_Drctry_Cp_Api, Cp_Plgn_Drctry_Cp_Api_v2;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The ClassicPress API URL.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      string    $cp_dir_url    The URL used by ClassicPress to present its API.
	 */
	private $cp_dir_url;

	/**
	 * The ClassicPress API URL V2.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      string    $cp_dir_url_v2    The URL used by ClassicPress to present its API since v2.
	 */
	private $cp_dir_url_v2;

	/**
	 * The Plugins Cache File path.
	 *
	 * @since    1.3.0
	 * @access   public
	 * @var      string    $plugins_cache_file    The File used by this plugin to store the Plugins in a cache.
	 */
	public $plugins_cache_file;

	/**
	 * The Options of this Plugin.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      array    $options    The options stored by the user for this  plugin.
	 */
	private $options;

	/**
	 * The Variations of readmes supported.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      array    $readme_vars    The different variations of readme supported by the plugin.
	 */
	private $readme_vars;

	/**
	 * The Topics searched for.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      string    $plugins_topic    The Topic searched for in the Github repos.
	 */
	private $plugins_topic;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $plugin_prefix    The unique prefix of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name        = $plugin_name;
		$this->plugin_prefix      = $plugin_prefix;
		$this->version            = $version;
		$this->plugins_cache_file = __DIR__ . '/partials/cp-plugins.txt';
		$this->cp_dir_url         = 'https://directory.classicpress.net/api/plugins/';
		$this->cp_dir_url_v2      = '';
		$this->options            = get_option( 'cp_dir_opts_options', array( 'cp_dir_opts_exteranal_org_repos' => $this->vetted_orgs() ) );
		$this->readme_vars        = array(
			'README.txt',
			'readme.txt',
			'README.md',
			'readme.md',
		);
		$this->plugins_topic      = 'classicpress-plugin';

	}

	/**
	 * Install a Plugin.
	 *
	 * @since 1.1.3 Added overwrite_package argument
	 * @param bool $overwrite Whether to overwrite the plugin or not. Default False.
	 */
	public function install_cp_plugin( $overwrite = false ) {

		$this->validate_post_nonce( '_ajax_nonce', 'updates' );
		$this->maybe_send_json_failure( 'url' );
		$plugin = $this->get_posted_data( 'url', 'esc_url_raw' );

		/**
		 * We include Upgrader Class.
		 *
		 * @todo Check this path on EACH CP UPDATE. It might change!
		 */
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$upgrader = new Plugin_Upgrader();
		$response = $upgrader->install( $plugin, array( 'overwrite_package' => $overwrite ) );

		wp_send_json( $response );

	}

	/**
	 * Activate a Plugin.
	 */
	public function activate_cp_plugin() {

		$this->validate_post_nonce( '_ajax_nonce', 'updates' );
		$this->maybe_send_json_failure( 'slug' );
		$plugin = $this->get_posted_data( 'slug', 'sanitize_text_field' );

		/**
		 * The function returns a WP error if something went wrong,
		 * null otherwise.
		 */
		$activated = activate_plugin( $plugin );

		wp_send_json( $activated );

	}

	/**
	 * Deactivate a Plugin.
	 */
	public function deactivate_cp_plugin() {

		$this->validate_post_nonce( '_ajax_nonce', 'updates' );
		$this->maybe_send_json_failure( 'slug' );
		$plugin = $this->get_posted_data( 'slug', 'sanitize_text_field' );

		/**
		 * This function does not return anything.
		 * We have no way of knowing whether the plugin was deactivated or not.
		 * We however reload the page in JS after this operation, so the new status will tell.
		 */
		deactivate_plugins( $plugin, true );

		// This string is never seen by anyone, so it does not need to be translated nor escaped.
		wp_send_json( 'Plugin Possibly Updated' );

	}

	/**
	 * Delete a Plugin.
	 */
	public function delete_cp_plugin() {

		$this->validate_post_nonce( '_ajax_nonce', 'updates' );
		$this->maybe_send_json_failure( 'plugin' );
		$plugin = $this->get_posted_data( 'plugin', 'sanitize_text_field' );

		/**
		 * This returns true on success, false if $Plugin is empty,
		 * null if creds are missing, WP Error on failure.
		 */
		$deleted = delete_plugins( array( $plugin ) );

		if ( false === $deleted ) {
			// creds are missing.
			$deleted = esc_html__( 'The Plugin Slug is missing from delete_plugins() function.', 'cp-plgn-drctry' );
		} elseif ( null === $deleted ) {
			$deleted = esc_html__( 'Filesystem Credentials are required. You are not allowed to perform this action.', 'cp-plgn-drctry' );
		} elseif ( is_wp_error( $deleted ) ) {
			$deleted = esc_html__( 'There has been an error. Please check the error logs.', 'cp-plgn-drctry' );
		} elseif ( true !== $deleted ) {
			$deleted = esc_html__( 'Unknown error occurred', 'cp-plgn-drctry' );
		}

		wp_send_json( $deleted );

	}

	/**
	 * Update a Plugin.
	 */
	public function update_cp_plugin() {

		$this->validate_post_nonce( '_ajax_nonce', 'updates' );
		$this->maybe_send_json_failure( 'slug' );

		/**
		 * We cannot use Upgrader Class, because CP has no way of
		 * selecting custom file URL. Only WP Can do that.
		 *
		 * We simply replace the plugin entirely.
		 *
		 * @since 1.0.0 Update Plugin
		 * @since 1.1.3 Update itself
		 */
		$this->install_cp_plugin( true );

	}

	/**
	 * Helper function to check if plugin has update.
	 *
	 * @param object $plugins All Plugin Objects in array.
	 * @return bool $is_installed If the plugin is installed or not.
	 */
	public function has_update( $plugins ) {

		$updates = array();
		foreach ( $plugins as $plugin ) {
			if ( $this->check_plugin_installed( $plugin ) ) {

				$current_installed_version = get_plugins()[ $this->plugin_slug( $plugin ) ]['Version'];
				$remote_version            = $plugin->current_version;
				$has_update                = version_compare( $current_installed_version, $remote_version );
				if ( -1 === $has_update ) {
					$updates[ $this->plugin_slug( $plugin ) ] = array( $current_installed_version, $remote_version );
				}
			}
		}

		return $updates;

	}

	/**
	 * Helper function to check if plugin is installed.
	 *
	 * @param object $plugin The Current Plugin Object.
	 * @return bool $is_installed If the plugin is installed or not.
	 */
	public function check_plugin_installed( $plugin ) {

		$plugin_filename   = str_replace( '.zip', '', basename( $plugin->download_link ) );
		$plugin_slug       = $plugin_filename . '/' . $plugin_filename . '.php';
		$installed_plugins = get_plugins();

		$is_installed = array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, true ) || array_search( $plugin->name, array_column( $installed_plugins, 'Name' ) ) !== false;

		return $is_installed;

	}

	/**
	 * Check if plugin is active.
	 *
	 * @param object $plugin The Current Plugin Object.
	 * @return bool $is_active If the Plugin is active.
	 */
	public function check_plugin_active( $plugin ) {

		$is_active = is_plugin_active( $this->plugin_slug( $plugin ) );

		return $is_active;
	}

	/**
	 * Is Plugin active and installed.
	 *
	 * @param object $plugin The Current Plugin Object.
	 * @return string $plugin_slug The Plugin Slug.
	 */
	public function plugin_slug( $plugin ) {

		$is_active       = false;
		$plugin_filename = str_replace( '.zip', '', basename( $plugin->download_link ) );
		$plugin_slug     = $plugin_filename . '/' . $plugin_filename . '.php';
		$is_active       = is_plugin_active( $plugin_slug );

		if ( false === $is_active ) {
			/**
			 * Handle bad plugins.
			 *
			 * It could be that some bad practice was followed
			 * and the plugin-folder/name.php does not match the downloaded item.
			 * This is not best practice,
			 * but unfortunately WP has allowed it,
			 * so for backward(s compatibility) reasons we make sure that
			 * if folder/name are a mismatch we can still check on the plugin state.
			 *
			 * First, get all installed plugins.
			 * From that array, search if the current Plugin Name (from API) is
			 * within the installed plugins.
			 * If so, get the key of that active plugin from the array.
			 * Then, fetch the proper slug of that plugin from the keys array.
			 * Then, repopulate $plugin_slug for later usage too.
			 */
			$installed_plugins = get_plugins();
			$plugin_key        = array_search( $plugin->name, array_column( $installed_plugins, 'Name' ) );
			$keys              = array_keys( $installed_plugins );
			if ( false !== $plugin_key ) {
				$plugin_slug = $keys[ $plugin_key ];
			}
		}

		return $plugin_slug;
	}

	/**
	 * Merge all Plugins from all APIs.
	 */
	public function get_plugins() {

		$this->validate_post_nonce( '_ajax_nonce', 'updates' );

		$this->put_file_contents( '', $this->plugins_cache_file );

		$populated = $this->maybe_populate_cache( $this->plugins_cache_file );

		/**
		 * Send response. Data is parsed with Cp_Plgn_Drctry_Cp_Plugins_Dir::get_plugins()
		 */
		if ( false === $populated ) {
			wp_send_json( esc_html__( 'The Cache could not be populated.', 'cp-plgn-drctry' ) );
		} elseif ( true === $populated ) {
			wp_send_json( 'loaded' );
		} else {
			wp_send_json( esc_html__( 'Somethign went wrong.', 'cp-plgn-drctry' ) );
		}

	}

	/**
	 * Provide a minimal function for CRON Operation. We cannot really do any safety checks here.
	 *
	 * @since 1.4.0
	 */
	public function cron_get_plugins() {

		$this->put_file_contents( '', $this->plugins_cache_file );

		$populated = $this->maybe_populate_cache( $this->plugins_cache_file );

	}

}
