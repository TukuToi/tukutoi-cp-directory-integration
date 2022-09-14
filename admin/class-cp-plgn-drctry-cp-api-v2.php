<?php
/**
 * Adds a trait to get Plugins from the CP API.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Plugins\CPDirectoryIntegration\Admin
 * @author     Beda Schmid <beda@tukutoi.com>
 */

/**
 * Trait to get Plugins from the CP API.
 *
 * Adds functions for:
 * Get all pages from the CP Dir API
 * Get Plugins from the CP Dir API
 *
 * @package    Plugins\CPDirectoryIntegration\Admin
 * @author     Beda Schmid <beda@tukutoi.com>
 */
trait Cp_Plgn_Drctry_Cp_Api_v2 {

	/**
	 * Build an array of Pages of the GitHub Results.
	 *
	 * @return array $pages The found page numbers in array.
	 */
	private function get_cp_pages_v2() {

		$n = $this->get_remote_header( $this->cp_dir_url_v2, array(), 'X-WP-TotalPages' );

		if ( false !== $n
			&& 404 !== $n
		) {

			$pages = array_fill( 1, $n, '' );

		} else {

			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'We could not reach the ClassicPress API. It is possible you reached the limits of the ClassicPress API.', 'cp-plgn-drctry' ) . '</p></div>';
			$pages = array();

		}

		return $pages;

	}
	/**
	 * Retrieve all plugins from the CP API.
	 *
	 * @return array $all_cp_plugins An array of all plugins objects.
	 */
	private function get_cp_plugins_v2() {

		/**
		 * This is not yet live. When it is live, the URL will be added to:
		 * var cp_dir_url_v2 in Class Cp_Plgn_Drctry_Plugin_Fx
		 */
		if ( empty( $this->cp_dir_url_v2 ) ) {
			return array();
		}

		$all_cp_plugins_v2 = array();
		$pages             = $this->get_cp_pages_v2();

		// loop over each page and get each pages's data.
		foreach ( $pages as $key => $link ) {

			// Get current page's plugins.
			$current_page_plugins = $this->get_remote_decoded_body( $this->cp_dir_url_v2 . '?page=' . $key );

			// Check response.
			if ( false !== $current_page_plugins ) {

				// Merge plugins into main plugins array.
				$all_cp_plugins_v2 = array_merge( $all_cp_plugins_v2, $current_page_plugins );

			} else {

				echo '<script>jQuery("#cp-plgn-drctry-error").css("display","block").html("<p>' . esc_js( __( 'We could not reach sume SubPage of the ClassicPress API. It is possible you reached the limits of the ClassicPress API.', 'cp-plgn-drctry' ) ) . '</p>");</script>';

			}
		}

		$all_cp_plugins_v2 = $this->adjust_v2_api( $all_cp_plugins_v2 );
		return $all_cp_plugins_v2;

	}

	/**
	 * The new api is not like the old api.
	 * Fix that by rebuilding the single plugin objects.
	 *
	 * @param array $data The raw data array from remote.
	 */
	private function adjust_v2_api( $data ) {

		$new_data = array();

		foreach ( $data as $key => $object ) {

			unset( $return );
			$return                               = new stdClass();
			$return->name                         = $object->title->rendered;
			$return->description                  = $object->excerpt->rendered;
			$return->downloads                    = '';
			$return->changelog                    = '';
			$return->developer                    = new stdClass();
			$return->developer->name              = $object->meta->developer_name;
			$return->developer->slug              = '';
			$return->developer->web_url           = '';
			$return->developer->username          = '';
			$return->developer->website           = '';
			$return->developer->published_at      = '';
			$return->slug                         = $object->meta->slug;
			$return->web_url                      = '';
			$return->minimum_wp_version           = '';
			$return->minimum_cp_version           = $object->meta->requires_cp;
			$return->current_version              = $object->meta->current_version;
			$return->latest_cp_compatible_version = '';
			$return->git_provider                 = $object->meta->git_provider;
			$return->repo_url                     = '';
			$return->download_link                = $object->meta->download_link;
			$return->comment                      = '';
			$return->type                         = new stdClass();
			$return->type->key                    = '';
			$return->type->value                  = '';
			$return->type->description            = '';
			$return->published_at                 = '';

			$new_data[ $key ] = $return;

		}

		return $new_data;
	}
}
