<?php
/**
 * Adds a trait to provide helper Functions for arbitrary operations.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 */

/**
 * Trait to provide helper Functions for arbitrary operations.
 *
 * Adds functions for:
 * Nonce validation
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 * @author     bedas <hello@tukutoi.com>
 */
trait Cp_Plgn_Drctry_Fx {

	/**
	 * Validates any POSTed nonce by key and nonce.
	 *
	 * @param string $key      The POST key where nonce is passed.
	 * @param string $nonce    The Nonce to validate (name).
	 * @param string $message The message to return on failure.
	 */
	private function validate_post_nonce( $key, $nonce, $message = 'Invalid or missing Nonce!' ) {

		if ( ! isset( $_POST[ sanitize_key( $key ) ] )
			|| empty( $_POST[ sanitize_key( $key ) ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ sanitize_key( $key ) ] ) ), sanitize_key( $nonce ) ) ) {
			wp_send_json( esc_html( $message ) );
		}

	}

	/**
	 * Validates any GET nonce by key and nonce.
	 *
	 * @param string $key      The GET key where nonce is passed.
	 * @param string $nonce    The Nonce to validate (name).
	 */
	private function validate_get_nonce( $key, $nonce ) {

		if ( isset( $_GET[ sanitize_key( $key ) ] )
			&& ! empty( $_GET[ sanitize_key( $key ) ] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET[ sanitize_key( $key ) ] ) ), sanitize_key( $nonce ) ) ) {
			return true;
		}

		return false;

	}

	/**
	 * Validates any POSTed nonce by key and nonce.
	 *
	 * @param string $key      The POST key where nonce is passed.
	 * @param string $message The message to return on failure.
	 */
	private function maybe_send_json_failure( $key, $message = 'Something went wrong' ) {

		/**
		 * Reviewers: Nonce is verified with ($this) validate_get_nonce
		 * before using maybe_send_json_failure this plugin always uses validate_get_nonce
		 */
		if ( ! isset( $_POST[ sanitize_key( $key ) ] ) ) {// phpcs:ignore.
			wp_send_json( esc_html( $message ) );
		}

	}

	/**
	 * Validates any POSTed nonce by key and nonce.
	 *
	 * @param string $key          The POST key where nonce is passed.
	 * @param string $sanitization The Sanitization function to use.
	 */
	private function get_posted_data( $key, $sanitization ) {

		/**
		 * Reviewers: $_POST is set because checked with maybe_send_json_failure
		 */
		return $sanitization( wp_unslash( $_POST[ $key ] ) );// phpcs:ignore.

	}

	/**
	 * CP Way of getting File Contents.
	 *
	 * @param string $file The file path to get contents from.
	 */
	private function get_file_contents( $file ) {

		global $wp_filesystem;

		if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
			$creds = request_filesystem_credentials( site_url() );
			wp_filesystem( $creds );
		}

		$contents = $wp_filesystem->get_contents( $file );

		return $contents;

	}

	/**
	 * CP Way of putting File Contentes.
	 *
	 * @param mixed  $contents The content to put.
	 * @param string $file     The file path to get contents from.
	 */
	private function put_file_contents( $contents, $file ) {

		global $wp_filesystem;

		if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
			$creds = request_filesystem_credentials( site_url() );
			wp_filesystem( $creds );
		}

		$wp_filesystem->put_contents( $file, $contents );

	}

	/**
	 * Get Remote body json decoded.
	 *
	 * @param string $url    Remote URL.
	 * @param array  $header Array of headers to send to remote. Empty by default.
	 */
	private function get_remote_decoded_body( $url, $header = array() ) {

		$r  = wp_remote_get( $url, $header );
		$rc = wp_remote_retrieve_response_code( $r );
		if ( 200 === $rc ) {

			return json_decode( wp_remote_retrieve_body( $r ) );

		} elseif ( 404 === $rc ) {

			return $rc;

		} else {

			return false;

		}

	}

	/**
	 * Get Remote body json decoded.
	 *
	 * @param string $url Remote URL.
	 * @param array  $header Array of headers to send to remote. Empty by default.
	 */
	private function get_remote_raw_body( $url, $header = array() ) {

		$r  = wp_remote_get( $url, $header );
		$rc = wp_remote_retrieve_response_code( $r );
		if ( 200 === $rc ) {

			return wp_remote_retrieve_body( $r );

		} elseif ( 404 === $rc ) {

			return $rc;

		} else {

			return false;

		}

	}

	/**
	 * Encode data to JSON or return empty string.
	 *
	 * @param mixed $data The Data to encode.
	 */
	private function encode_to_json( $data ) {

		// Encode all data to JSON or return empty string.
		if ( ! empty( $data ) ) {
			$data = wp_json_encode( $data );
		} else {
			$data = '';
		}

		return $data;

	}

	/**
	 * Get a list of vetted orgs
	 *
	 * @return array $_orgs An array of vetted orgs.
	 */
	private function vetted_orgs() {
		$orgs  = json_decode( $this->get_file_contents( __DIR__ . '/partials/github-orgs.txt' ) );
		$_orgs = array();
		foreach ( $orgs as $org ) {
			$_orgs[] = $org->slug;
		}

		return $_orgs;
	}

	/**
	 * Get string value between delimiters.
	 *
	 * @param string $str             The string to scan.
	 * @param string $start_delimiter The start delimiter to look for.
	 * @param string $end_delimiter   The end delimiter to look for.
	 * @return string The string between.
	 */
	private function get_content_between( $str, $start_delimiter, $end_delimiter ) {

		$contents               = array();
		$start_delimiter_length = strlen( $start_delimiter );
		$end_delimiter_length   = strlen( $end_delimiter );
		$start_from             = 0;
		$content_start          = 0;
		$content_end            = 0;

		while ( false !== ( $content_start = strpos( $str, $start_delimiter, $start_from ) ) ) {

			$content_start += $start_delimiter_length;
			$content_end    = strpos( $str, $end_delimiter, $content_start );
			if ( false === $content_end ) {

				break;

			}
			$contents[] = substr( $str, $content_start, $content_end - $content_start );
			$start_from = $content_end + $end_delimiter_length;

		}

		return $contents;

	}

	/**
	 * Create a paginated list out of an array of items
	 *
	 * @param array  $data   The data to paginate.
	 * @param string $return What part of the pagination assets to return.
	 */
	private function list_pagination( $data, $return ) {

		$paginated = array_chunk( $data, 15, false );
		// Last page is amount of array keys - 1 (because of start at 0).
		$last  = count( $paginated ) - 1;
		$paged = 0;
		/**
		 * Reviewers: we do check the nonce, CPCS just does not recognise this custom function.
		 */
		if ( isset( $_GET['paged'] ) // phpcs:ignore.
			&& $this->validate_get_nonce( 'tkt_page_nonce', 'tkt_page_nonce' )
		) {
			$paged = (int) $_GET['paged'];// phpcs:ignore.
		}
		// Build "prev" link "paged" value.
		$prev = filter_var(
			$paged - 1,
			FILTER_VALIDATE_INT,
			array(
				'options' => array(
					'default'   => 0,
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
					'default'   => 0,
					'min_range' => 1,
					'max_range' => $last,
				),
			)
		);

		return $$return;
	}

	/**
	 * Create a safe HTML search form input
	 */
	private function search_form() {

		?>
		<form class="tkt-src-form" action="<?php echo esc_url( remove_query_arg( array( 'paged', 'refresh' ), add_query_arg( array( 'page' => 'cp-plugins' ) ) ) ); ?>">
			<?php
			$query = '';
			/**
			 * Reviewers: we do check the nonce, CPCS just does not recognise this custom function.
			 */
			if ( isset( $_GET['s'] )// phpcs:ignore.
				&& $this->validate_get_nonce( 'tkt-src-nonce', 'tkt-src-nonce' )
			) {
				$query = sanitize_text_field( wp_unslash( $_GET['s'] ) );// phpcs:ignore.
			}
			?>
			<input type="text" placeholder="<?php esc_html_e( 'Press Return To Search...', 'cp-plgn-drctry' ); ?>" name="s" value="<?php echo esc_html( $query ); ?>">
			<?php
			if ( ! empty( $_GET ) ) {// phpcs:ignore.
				foreach ( $_GET as $key => $val ) {// phpcs:ignore.
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
	 * Maybe populate cache file.
	 *
	 * @param string $file The Cache file path.
	 */
	private function maybe_populate_cache( $file ) {

		/**
		 * If cache not yet built or refreshing cache.
		 */
		if ( 0 === filesize( $file ) ) {
			// Get plugins.
			$git_plugins = $this->get_git_plugins();
			$cp_plugins  = $this->get_cp_plugins();
			$all_plugins = array_merge( $cp_plugins, $git_plugins );
			// Populate cache.
			$this->put_file_contents( $this->encode_to_json( $all_plugins ), $this->plugins_cache_file );

			return true;
		}

		return false;

	}


}
