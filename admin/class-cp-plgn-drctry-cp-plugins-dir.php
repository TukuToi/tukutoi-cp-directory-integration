<?php
/**
 * Adds a class to gather and list all Plugins from all apis.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 */

/**
 * The class to gather and list all Plugins from all apis.
 *
 * Lists all plugins in a grid
 * Provides a Search form
 * Provides "More info" data for each plugin
 * Provides Pagination trhough the list of plugins
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 * @author     bedas <hello@tukutoi.com>
 */
class Cp_Plgn_Drctry_Cp_Plugins_Dir {

	/**
	 * Include arbitrary functionality for the Plugins list.
	 */
	use Cp_Plgn_Drctry_Fx;

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
	 * The Instance of the Plugin Functionality.
	 *
	 * @since    1.3.0
	 * @access   private
	 * @var      object    $plugin_fx    The instance of the Cp_Plgn_Drctry_Plugin_Fx() Class handling plugins.
	 */
	private $plugin_fx;

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
		$this->version       = $version;
		$this->plugin_fx     = new Cp_Plgn_Drctry_Plugin_Fx( $plugin_name, $plugin_prefix, $version );

	}

	/**
	 * List all plugins in a paginated, searchable grid.
	 */
	public function list_plugins() {

		/**
		 * Abort early if no plugins cached.
		 */
		$plugins = $this->get_plugins();
		/**
		 * Note, stringified booleans are intended here.
		 * We use them also for data-attributes in links.
		 * They transform to int in js. So it is easier for us to just use strings
		 */
		$is_fresh = empty( $plugins ) ? 'true' : 'false';

		/**
		 * If we have plugins, build data - otherwise provide empty array
		 */
		if ( 'false' === $is_fresh ) {

			/**
			 * Define variables for Display conditions needed BEFORE pagination is done.
			 *
			 * @var array $has_update The array of Plugins that feature an update.
			 */
			$has_update = $this->plugin_fx->has_update( $plugins );

			/**
			 * Apply search.
			 *
			 * @var array $plugins The array of found plugins. Returns ALL if nothing found.
			 */
			$plugins = $this->search_plugins( $plugins );

		} else {

			$plugins = array();

		}
		/**
		 * Paginate.
		 *
		 * @var array $paginated Array chunks of plugins.
		 * @var int   $last      The Last Page.
		 * @var int   $paged     The Current Page.
		 * @var int   $prev      The Previous Page.
		 * @var int   $next      The Next page.
		 * @var array $current_plugins Array chunk of current plugins.
		 */
		$paginated       = $this->list_pagination( $plugins, 'paginated' );
		$last            = $this->list_pagination( $plugins, 'last' );
		$paged           = $this->list_pagination( $plugins, 'paged' );
		$prev            = $this->list_pagination( $plugins, 'prev' );
		$next            = $this->list_pagination( $plugins, 'next' );
		$current_plugins = empty( $plugins ) ? array() : $paginated[ $paged ];

		// Render everything in HTML.
		include __DIR__ . '/partials/cp-plgn-drctry-admin-display.php';

	}

	/**
	 * Search through the cached plugins.
	 *
	 * @param  array $plugins The Array of Plugin objects.
	 * @return array $plugins The Found Plugins (or nothing, if nothing found).
	 */
	private function search_plugins( $plugins ) {

		/**
		 * Reviewers: we do check the nonce, CPCS just does not recognise this custom function.
		 */
		if ( isset( $_GET['s'] )// phpcs:ignore.
			&& $this->validate_get_nonce( 'tkt-src-nonce', 'tkt-src-nonce' )
		) {

			$search_term = sanitize_text_field( wp_unslash( $_GET['s'] ) );// phpcs:ignore.

			foreach ( $plugins as $key => $plugin ) {

				if ( stripos( $plugin->description, $search_term ) !== false
					|| stripos( $plugin->developer->name, $search_term ) !== false
					|| stripos( $plugin->name, $search_term ) !== false
				) {

					$found_plugins[] = $plugins[ $key ];

				}
			}

			if ( empty( $found_plugins ) ) {

				echo '<script>jQuery(".notice-error").css("display","block").html("<p>' . esc_js( __( 'Nothing Found', 'cp-plgn-drctry' ) ) . '</p>");</script>';
				$plugins = array();

			} else {

				$plugins = $found_plugins;

			}
		}

		return $plugins;
	}

	/**
	 * Merge all Plugins from all APIs.
	 *
	 * @return array The array of all plugins objects.
	 */
	private function get_plugins() {

		return json_decode( $this->get_file_contents( $this->plugin_fx->plugins_cache_file ) );

	}

	/**
	 * Returns the more info content.
	 *
	 * @param object $plugin The Current Plugin Object.
	 * @return string $html The HTML to produce the more info content.
	 */
	private function more_info( $plugin ) {

		$html = '';

		foreach ( $plugin as $prop => $value ) {
			$html .= '<h2>' . esc_html( $prop ) . '</h2>';
			if ( is_object( $value ) ) {
				foreach ( $value as $sub_prop => $sub_value ) {
					$html .= '<li>' . esc_html( $sub_prop ) . ': ' . esc_html( $sub_value ) . '</li>';
				}
			} else {
				$html .= '<p>' . esc_html( $value ) . '</p>';
			}
		}

		return $html;

	}

}
