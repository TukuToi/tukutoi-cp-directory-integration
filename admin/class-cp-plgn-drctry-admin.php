<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two hooks to
 * enqueue the admin-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 * @author     bedas <hello@tukutoi.com>
 */
class Cp_Plgn_Drctry_Admin {

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
	 * The CP Dir Class instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $cp_dir    The Class reference for the ClassicPress Plugin Directory.
	 */
	private $cp_dir;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $plugin_prefix    The unique prefix of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;
		$this->cp_dir = new Cp_Plgn_Drctry_Cp_Dir( $plugin_name, $plugin_prefix, $version );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {

		if ( 'plugins_page_cp-plugins' === $hook_suffix ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cp-plgn-drctry-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {

		if ( 'plugins_page_cp-plugins' === $hook_suffix ) {
			add_thickbox();
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cp-plgn-drctry-admin.js', array( 'jquery' ), $this->version, false );
			wp_localize_script(
				$this->plugin_name,
				'ajax_object',
				array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'admin_url' => esc_url( get_admin_url( null, '', 'admin' ) ),
					'nonce' => wp_create_nonce( 'updates' ),
				)
			);
		}
	}

	/**
	 * Install a Plugin.
	 *
	 * @since 1.1.3 Added overwrite_package argument
	 * @param bool $overwrite Whether to overwrite the plugin or not. Default False.
	 */
	public function install_cp_plugin( $overwrite = false ) {

		if ( ! isset( $_POST['_ajax_nonce'] )
			|| empty( $_POST['_ajax_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'updates' ) ) {
			 die( 'Invalid or missing Nonce!' );
		}

		if ( ! isset( $_POST['url'] ) ) {
			wp_send_json( 'Something went wrong' );
		}

		/**
		 * We include Upgrader Class.
		 *
		 * @todo Check this path on EACH CP UPDATE. It might change!
		 */
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		$upgrader = new Plugin_Upgrader();
		$response = $upgrader->install( esc_url_raw( wp_unslash( $_POST['url'] ) ), array( 'overwrite_package' => $overwrite ) );

		wp_send_json( $response );

	}

	/**
	 * Update a Plugin.
	 */
	public function update_cp_plugin() {

		if ( ! isset( $_POST['_ajax_nonce'] )
			|| empty( $_POST['_ajax_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'updates' ) ) {
			 die( __( 'Invalid or missing Nonce!', 'cp-plgn-drctry' ) );
		}

		if ( ! isset( $_POST['slug'] ) ) {
			wp_send_json( 'Something went wrong' );
		}

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
	 * Delete a Plugin.
	 */
	public function delete_cp_plugin() {

		if ( ! isset( $_POST['_ajax_nonce'] )
			|| empty( $_POST['_ajax_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'updates' ) ) {
			die( __( 'Invalid or missing Nonce!', 'cp-plgn-drctry' ) );
		}

		if ( ! isset( $_POST['plugin'] ) ) {
			wp_send_json( __( 'Something went wrong', 'cp-plgn-drctry' ) );
		}

		/**
		 * This returns true on success, false if $Plugin is empty,
		 * null if creds are missing, WP Error on failure.
		 */
		$deleted = delete_plugins( array( sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) ) );

		if ( false === $deleted ) {
			// creds are missing.
			$deleted = 'The Plugin Slug is missing from delete_plugins() function.';
		} elseif ( null === $deleted ) {
			$deleted = 'Filesystem Credentials are required. You are not allowed to perform this action.';
		} elseif ( is_wp_error( $deleted ) ) {
			$deleted = 'There has been an error. Please check the error logs.';
		} elseif ( true !== $deleted ) {
			$deleted = 'Unknown error occurred';
		}

		wp_send_json( $deleted );

	}

	/**
	 * Deactivate a Plugin.
	 */
	public function deactivate_cp_plugin() {

		if ( ! isset( $_POST['_ajax_nonce'] )
			|| empty( $_POST['_ajax_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'updates' ) ) {
			 die( 'Invalid or missing Nonce!' );
		}

		if ( ! isset( $_POST['slug'] ) ) {
			wp_send_json( 'Something went wrong' );
		}

		/**
		 * This function does not return anything.
		 * We have no way of knowing whether the plugin was deactivated or not.
		 * We however reload the page in JS after this operation, so the new status will tell.
		 */
		deactivate_plugins( sanitize_text_field( wp_unslash( $_POST['slug'] ) ), true );

		wp_send_json( 'Plugin Possibly Updated' );

	}

	/**
	 * Activate a Plugin.
	 */
	public function activate_cp_plugin() {

		if ( ! isset( $_POST['_ajax_nonce'] )
			|| empty( $_POST['_ajax_nonce'] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_ajax_nonce'] ) ), 'updates' ) ) {
			 die( 'Invalid or missing Nonce!' );
		}

		if ( ! isset( $_POST['slug'] ) ) {
			wp_send_json( 'Something went wrong' );
		}

		/**
		 * The function returns a WP error if something went wrong,
		 * null otherwise.
		 */
		$activated = activate_plugin( sanitize_text_field( wp_unslash( $_POST['slug'] ) ) );

		wp_send_json( $activated );

	}

	/**
	 * Creates the submenu item and calls on the Submenu Page object to render
	 * the actual contents of the page.
	 */
	public function add_plugins_list() {

		add_submenu_page(
			'plugins.php',
			esc_html__( 'ClassicPress Plugins', 'cp-plgn-drctry' ),
			esc_html__( 'Manage CP Plugins', 'cp-plgn-drctry' ),
			'install_plugins',
			'cp-plugins',
			array( $this, 'render' ),
			3
		);

	}

	/**
	 * Render the admin page.
	 */
	public function render() {
		?>
		<div id="loadingDiv" style="display:none"><span class="spinner"></span></div>
		<div class="wrap">
			<h1><?php esc_html_e( 'ClassicPress Plugins', 'cp-plgn-drctry' ); ?></h1>
			<p><?php esc_html_e( 'Browse, Install and Activate ClassicPress Plugins', 'cp-plgn-drctry' ); ?></p>
			<div class="notice notice-error" id="cp-plgn-drctry-error" style="display:none;"></div>
			<?php $this->cp_dir->list_plugins(); ?>
		</div>
		<?php
	}

}
