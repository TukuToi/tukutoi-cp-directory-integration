<?php
/**
 * Adds a class to handle general Admin Area features.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 */

/**
 * Class to handle general Admin Area Features.
 *
 * Enqueues scripts and styles
 * Adds Plugin Pages to Admin Menu
 * Renders settings and plugin list page
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

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {

		if ( 'plugins_page_cp-plugins' === $hook_suffix ) {
			wp_enqueue_style( $this->plugin_prefix . 'plugins', plugin_dir_url( __FILE__ ) . 'css/cp-plgn-drctry-admin.css', array(), $this->version, 'all' );
		} elseif ( 'settings_page_cp_dir_opts' === $hook_suffix ) {
			wp_enqueue_style( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0-rc.0', 'all' );
			wp_enqueue_style( $this->plugin_prefix . 'settings', plugin_dir_url( __FILE__ ) . 'css/cp-plgn-drctry-settings.css', array( 'select2' ), $this->version, 'all' );
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
			wp_enqueue_script( $this->plugin_prefix . 'plugins', plugin_dir_url( __FILE__ ) . 'js/cp-plgn-drctry-admin.js', array( 'jquery' ), $this->version, false );
			wp_localize_script(
				$this->plugin_prefix . 'plugins',
				'ajax_object',
				array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'admin_url' => esc_url( get_admin_url( null, '', 'admin' ) ),
					'nonce' => wp_create_nonce( 'updates' ),
				)
			);
		} elseif ( 'settings_page_cp_dir_opts' === $hook_suffix ) {
			wp_enqueue_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array( 'jquery' ), '4.1.0-rc.0', false );
			wp_enqueue_script( $this->plugin_prefix . 'settings', plugin_dir_url( __FILE__ ) . 'js/cp-plgn-drctry-settings.js', array( 'select2' ), $this->version, false );
		}
	}

	/**
	 * Add Menu Pages.
	 *
	 * @since 1.0.0 Add Plugins List Page.
	 * @since 1.3.0 Add Settings Page.
	 */
	public function add_menu_pages() {

		add_submenu_page(
			'plugins.php',
			esc_html__( 'ClassicPress Plugins', 'cp-plgn-drctry' ),
			esc_html__( 'Manage CP Plugins', 'cp-plgn-drctry' ),
			'install_plugins',
			'cp-plugins',
			array( $this, 'render' ),
			3
		);

		add_submenu_page(
			'options-general.php',
			esc_html__( 'ClassicPress Repositories', 'cp-plgn-drctry' ),
			esc_html__( 'Manage CP Repos', 'cp-plgn-drctry' ),
			'manage_options',
			'cp_dir_opts',
			array( $this, 'dir_settings_render' ),
			3
		);

	}

	/**
	 * Render the plugins list page.
	 */
	public function render() {
		?>
		<div id="loadingDiv" style="display:none"><span class="spinner"></span></div>
		<div class="wrap">
			<h1><?php esc_html_e( 'ClassicPress Plugins', 'cp-plgn-drctry' ); ?></h1>
			<p><?php esc_html_e( 'Browse, Install and Activate ClassicPress Plugins', 'cp-plgn-drctry' ); ?></p>
			<div class="notice notice-error" id="cp-plgn-drctry-error" style="display:none;"></div>
			<?php
			$cp_dir = new Cp_Plgn_Drctry_Cp_Plugins_Dir( $this->plugin_name, $this->plugin_prefix, $this->version );
			$cp_dir->list_plugins();
			?>
		</div>
		<?php
	}

	/**
	 * Render the settings page.
	 *
	 * @since 1.3.0
	 */
	public function dir_settings_render() {

		/**
		 * Show error/update message.
		 */
		settings_errors( 'cp_dir_opts_messages' );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'ClassicPress Repositories', 'cp-plgn-drctry' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "cp_dir_opts".
				settings_fields( 'cp_dir_opts' );
				/**
				 * Output setting sections and their fields
				 * Sections are registered for "cp_dir_opts",
				 * each field is registered to a specific section)
				 */
				do_settings_sections( 'cp_dir_opts' );
				// output save settings button.
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

}
