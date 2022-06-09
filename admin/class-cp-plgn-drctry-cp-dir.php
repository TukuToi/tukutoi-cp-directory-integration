<?php
/**
 * The CP Directory features of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 */

/**
 * The CP Directory features of the plugin.
 *
 * Defines the API call and lists the contents.
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 * @author     bedas <hello@tukutoi.com>
 */
class Cp_Plgn_Drctry_Cp_Dir {

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
	 * Chunk plugins into "pages" and return as grid/list.
	 */
	public function list_plugins() {

		// All Plugins.
		$plugins = $this->get_plugins();
		$has_update = $this->has_update( $plugins );
		// Maybe search.
		$plugins = $this->search_plugins( $plugins );
		// Paginate them by 15.
		$paginated = array_chunk( $plugins, 15, false );
		// Last page is amount of array keys - 1 (because of start at 0).
		$last = count( $paginated ) - 1;
		// Check if the current page is a paged URL.
		$paged = 0;
		if ( isset( $_GET['paged'] )
			&& isset( $_GET['tkt_page_nonce'] )
			&& wp_verify_nonce( sanitize_key( wp_unslash( $_GET['tkt_page_nonce'] ) ), 'tkt_page_nonce' )
		) {
			$paged = (int) $_GET['paged'];
		}
		// Build "prev" link "paged" value.
		$prev = filter_var(
			$paged - 1,
			FILTER_VALIDATE_INT,
			array(
				'options' => array(
					'default' => 0,
					'min_range' => 0,
					'max_range' => $last,
				),
			)
		);
		// Build "next" link "paged" value.
		$next = filter_var(
			$paged + 1,
			FILTER_VALIDATE_INT,
			array(
				'options' => array(
					'default' => 0,
					'min_range' => 1,
					'max_range' => $last,
				),
			)
		);
		// Get current chunk of plugins.
		$current_plugins = $paginated[ $paged ];
		// Render everything in HTML.
		include( __DIR__ . '/partials/cp-plgn-drctry-admin-display.php' );

	}

	/**
	 * CP Way of getting File Contents.
	 */
	private function get_file_contents() {

		global $wp_filesystem;

		if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
			$creds = request_filesystem_credentials( site_url() );
			wp_filesystem( $creds );
		}

		$contents = $wp_filesystem->get_contents( __DIR__ . '/partials/cp-plugins.txt' );

		return $contents;

	}

	/**
	 * CP Way of putting File Contentes.
	 *
	 * @param mixed $contents The content to put.
	 */
	private function put_file_contents( $contents ) {

		global $wp_filesystem;

		if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
			$creds = request_filesystem_credentials( site_url() );
			wp_filesystem( $creds );
		}

		$wp_filesystem->put_contents( __DIR__ . '/partials/cp-plugins.txt', $contents );

	}

	/**
	 * Retrieve all plugins and store in file for cache.
	 *
	 * @return array $plugins An array of all plugins objects.
	 */
	private function get_plugins() {

		$plugins = array();
		$all_plugins = array();

		/**
		 * If cache not yet built or refreshing cache.
		 */
		if ( 0 === filesize( __DIR__ . '/partials/cp-plugins.txt' )
			|| ( isset( $_GET['refresh'] )
				&& isset( $_GET['tkt_nonce'] )
				&& wp_verify_nonce( sanitize_key( wp_unslash( $_GET['tkt_nonce'] ) ), 'tkt-refresh-data' )
				&& 1 === (int) $_GET['refresh']
			)
		) {

			// Empty the cache if set.
			if ( 0 !== filesize( __DIR__ . '/partials/cp-plugins.txt' ) ) {
				$this->put_file_contents( '' );
			}

			// get first page.
			$plugins = wp_remote_get( 'https://directory.classicpress.net/api/plugins/' );

			// Check response.
			if ( wp_remote_retrieve_response_code( $plugins ) === 200 ) {

				// get first page body.
				$plugins_body = wp_remote_retrieve_body( $plugins );
				$plugins_body = json_decode( $plugins_body );

				/**
				 * On the first API page, the first meta:links link is null
				 * This is because there is no "previous page" on the first page.
				 * The last meta:links link is the "next" page, which we already
				 * have in the meta:links as well. Thus, we remove first and last
				 * to get all actual pages of the API.
				 */
				array_shift( $plugins_body->meta->links );
				array_pop( $plugins_body->meta->links );

				// loop over each page and get each pages's data.
				foreach ( $plugins_body->meta->links as $link ) {

					// Get current page's plugins.
					$current_page_plugins = wp_remote_get( $link->url );

					// Check response.
					if ( wp_remote_retrieve_response_code( $current_page_plugins ) === 200 ) {

						// Get current page's body.
						$current_page_plugins_body = wp_remote_retrieve_body( $current_page_plugins );
						$current_page_plugins_body = json_decode( $current_page_plugins_body );

						// Merge plugins into main plugins array.
						$all_plugins = array_merge( $all_plugins, $current_page_plugins_body->data );

					}
				}

				// Re-encode all plugins to JSON.
				$plugins = wp_json_encode( $all_plugins );
				$this->put_file_contents( $plugins );

			}
		}

		// Get data from cache.
		$plugins = $this->get_file_contents();
		// Decode data.
		$plugins = json_decode( $plugins );
		// Return as array.
		return $plugins;

	}

	/**
	 * Search through the cached plugins.
	 *
	 * @param array $plugins The Array of Plugin objects.
	 * @return array $plugins The Found Plugins (or all, if nothing found).
	 */
	private function search_plugins( $plugins ) {
		if ( isset( $_GET['s'] )
			&& isset( $_GET['tkt-src-nonce'] )
			&& wp_verify_nonce( sanitize_key( wp_unslash( $_GET['tkt-src-nonce'] ) ), 'tkt-src-nonce' )
		) {
			$search_term = sanitize_text_field( wp_unslash( $_GET['s'] ) );
			foreach ( $plugins as $key => $plugin ) {
				if ( stripos( $plugin->description, $search_term ) !== false
					|| stripos( $plugin->developer->name, $search_term ) !== false
					|| stripos( $plugin->name, $search_term ) !== false
				) {
					$found_plugins[] = $plugins[ $key ];
				}
			}
			if ( ! empty( $found_plugins ) ) {
				$plugins = $found_plugins;
			} else {
				// Nothing wrong with this, since it is hardcoded no need to escape.
				echo '<script>jQuery(".error").css("display","block").html("' . esc_js( __( 'Nothing Found', 'cp-plgn-drctry' ) ) . '");</script>';
			}
		}

		return $plugins;
	}

	/**
	 * Create a simple search form.
	 */
	private function search_form() {

		?>
		<form class="tkt-src-form" action="<?php echo esc_url( remove_query_arg( 'paged', add_query_arg( array( 'page' => 'cp-plugins' ) ) ) ); ?>">
			<?php
			$query = '';
			if ( isset( $_GET['s'] )
				&& isset( $_GET['tkt-src-nonce'] )
				&& wp_verify_nonce( sanitize_key( wp_unslash( $_GET['tkt-src-nonce'] ) ), 'tkt-src-nonce' )
			) {
				$query = sanitize_text_field( wp_unslash( $_GET['s'] ) );
			}
			?>
			<input type="text" placeholder="Hit Enter To Search..." name="s" value="<?php echo esc_html( $query ); ?>">
			<?php
			if ( ! empty( $_GET ) ) {
				foreach ( $_GET as $key => $val ) {
					if ( 'paged' !== $key
						&& 'refresh' !== $key
						&& 's' !== $key
					) {
						echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_html( $val ) . '" />';
					}
				}
			}
			wp_nonce_field( 'tkt-src-nonce', 'tkt-src-nonce', false );
			?>
		</form>
		<?php

	}

	/**
	 * Helper function to check if plugin has update.
	 *
	 * @param object $plugins All Plugin Objects in array.
	 * @return bool $is_installed If the plugin is installed or not.
	 */
	private function has_update( $plugins ) {

		$updates = array();
		foreach ( $plugins as $plugin ) {
			if ( $this->check_plugin_installed( $plugin ) ) {

				$current_installed_version = get_plugins()[ $this->plugin_slug( $plugin ) ]['Version'];
				$remote_version = $plugin->current_version;
				$has_update = version_compare( $current_installed_version, $remote_version );
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
	private function check_plugin_installed( $plugin ) {

		$plugin_filename = str_replace( '.zip', '', basename( $plugin->download_link ) );
		$plugin_slug = $plugin_filename . '/' . $plugin_filename . '.php';
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
	private function check_plugin_active( $plugin ) {

		$is_active = is_plugin_active( $this->plugin_slug( $plugin ) );

		return $is_active;
	}

	/**
	 * Is Plugin active and installed.
	 *
	 * @param object $plugin The Current Plugin Object.
	 * @return string $plugin_slug The Plugin Slug.
	 */
	private function plugin_slug( $plugin ) {

		$is_active = false;
		$plugin_filename = str_replace( '.zip', '', basename( $plugin->download_link ) );
		$plugin_slug = $plugin_filename . '/' . $plugin_filename . '.php';
		$is_active = is_plugin_active( $plugin_slug );

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
			$plugin_key = array_search( $plugin->name, array_column( $installed_plugins, 'Name' ) );
			$keys = array_keys( $installed_plugins );
			if ( false !== $plugin_key ) {
				$plugin_slug = $keys[ $plugin_key ];
			}
		}

		return $plugin_slug;
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
