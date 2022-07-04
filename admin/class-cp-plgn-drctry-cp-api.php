<?php
/**
 * Adds a trait to get Plugins from the CP API.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 */

/**
 * Trait to get Plugins from the CP API.
 *
 * Adds functions for:
 * Get all pages from the CP Dir API
 * Get Plugins from the CP Dir API
 *
 * @package    Cp_Plgn_Drctry
 * @subpackage Cp_Plgn_Drctry/admin
 * @author     bedas <hello@tukutoi.com>
 */
trait Cp_Plgn_Drctry_Cp_Api {

	private function get_cp_pages() {

		$pages = $this->get_remote_decoded_body( $this->cp_dir_url );

		if ( false !== $pages
			&& 404 !== $pages
		) {

			/**
			 * On the first API page, the first meta:links link is null
			 * This is because there is no "previous page" on the first page.
			 * The last meta:links link is the "next" page, which we already
			 * have in the meta:links as well. Thus, we remove first and last
			 * to get all actual pages of the API.
			 */
			array_shift( $pages->meta->links );
			array_pop( $pages->meta->links );
			$pages = $pages->meta->links;

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
	private function get_cp_plugins() {

		$all_cp_plugins = array();
        $pages = $this->get_cp_pages();

		// loop over each page and get each pages's data.
		foreach (  $this->get_cp_pages() as $link ) {

			// Get current page's plugins.
            $current_page_plugins = $this->get_remote_decoded_body( $link->url );

			// Check response.
			if ( false !== $current_page_plugins ) {

				// Merge plugins into main plugins array.
				$all_cp_plugins = array_merge( $all_cp_plugins, $current_page_plugins->data );

			} else {

				echo '<script>jQuery("#cp-plgn-drctry-error").css("display","block").html("<p>' . esc_js( __( 'We could not reach sume SubPage of the ClassicPress API. It is possible you reached the limits of the ClassicPress API.', 'cp-plgn-drctry' ) ) . '</p>");</script>';

			}
		}

		return $all_cp_plugins;

	}
}
