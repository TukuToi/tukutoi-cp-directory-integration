/**
 * Admin side jQuery.
 * Adds script to install plugin,
 * show spinner on ajax and
 * catch the exception of no mailer being on the computer of user.
 */

(function( $ ) {
	'use strict';

	$( window ).on(
		'load',
		function() {
			/**
			 * On click, install the plugin.
			 *
			 * Only do this if text of link is not "Activate".
			 */
			$( '.plugin-action' ).each(
				function() {
					$( this ).on(
						'click',
						function(e){
							e.preventDefault();
							if ( 'install' === $( this ).data( 'action' ) ) {
								install_plugin( $( this ).attr( 'href' ), $( this ).data( "slug" ) )
							} else if ( 'update' === $( this ).data( 'action' ) ) {
								update_plugin( $( this ).attr( 'href' ), $( this ).data( "slug" ) )
							} else if ( 'deactivate' === $( this ).data( 'action' ) ) {
								deactivate_plugin( $( this ).attr( 'href' ), $( this ).data( "slug" ) )
							} else if ( 'activate' === $( this ).data( 'action' ) ) {
								activate_plugin( $( this ).attr( 'href' ), $( this ).data( "slug" ) )
							} else if ( 'delete' === $( this ).data( 'action' ) ) {
								delete_plugin( $( this ).attr( 'href' ), $( this ).data( "slug" ) )
							}
						}
					);
				}
			);
			/**
			 * When AJAX starts, show an overlay with spinner.
			 * When AJAX ends, remove the spinner.
			 */
			$( '#loadingDiv' )
			.hide()
			.ajaxStart(
				function() {
					$( this ).show();
					$( '.spinner' ).css( "visibility", "visible" );
				}
			)
			.ajaxStop(
				function() {
					$( this ).hide();
					$( '.spinner' ).css( "visibility", "hidden" );
				}
			);
			/**
			 * Some people still do not have an email client...
			 */
			$( 'a[href^=mailto]' ).each(
				function() {
					$( this ).on(
						'click',
						function( e ) {
							var t;
							$( window ).blur(
								function() {
									// The browser apparently responded, so stop the timeout.
									clearTimeout( t );
								}
							);
							t = setTimeout(
								function() {
									// The browser did not respond after 500ms, so open an alternative URL.
									alert( 'You do not have a local mailclient, or did not set your mailclient as the default. Please contact the ClassicPress Plugin Review Team at plugins@classicpress.net' );
								},
								500
							);
						}
					);
				}
			);
		}
	);

	/**
	 * AJAX POST function to install plugin.
	 * On success (which can only be determined if a messy WP response contains "true"),
	 * reload the page.
	 * On failure (only detectable by the absence of "true"), show an error div.
	 *
	 * @param string href The URL to download asset.
	 * @param string slug The Slug of the Plugin.
	 */
	function install_plugin( href, slug ) {
		var data = {
			'action': 'install_cp_plugin',
			'url': href,
			'slug': slug,
			'nonce': ajax_object.nonce,
		};
		$.post(
			ajax_object.ajax_url,
			data,
			function( response ) {
				if ( response.indexOf( "true" ) >= 0) {
					// Reload the page if success.
					window.location.reload();
				} else {
					// If failure, display the error in an error DIV.
					$( '.notice-error' ).css( "display", "block" );
					response = response.replace( 'null', '' );
					$( '.notice-error' ).html( response );
				}
			}
		);
	}

	/**
	 * AJAX POST function to update plugin.
	 * On success (which can only be determined if a messy WP response contains "true"),
	 * reload the page.
	 * On failure (only detectable by the absence of "true"), show an error div.
	 *
	 * @param string href The URL to download asset.
	 * @param string slug The Slug of the Plugin.
	 */
	function update_plugin( href, slug ) {
		var data = {
			'action': 'update_cp_plugin',
			'url': href,
			'slug': slug,
			'nonce': ajax_object.nonce,
		};
		$.post(
			ajax_object.ajax_url,
			data,
			function( response ) {
				if ( response.indexOf( "true" ) >= 0) {
					// Reload the page if success.
					window.location.reload();
				} else {
					// If failure, display the error in an error DIV.
					$( '.notice-error' ).css( "display", "block" );
					response = response.replace( 'null', '' );
					$( '.notice-error' ).html( response );
				}
			}
		);
	}

	/**
	 * AJAX POST function to deactivate plugin.
	 * Reload the page in any case.
	 *
	 * @param string href The URL to download asset.
	 * @param string slug The Slug of the Plugin.
	 */
	function deactivate_plugin( href, slug ) {
		var data = {
			'action': 'deactivate_cp_plugin',
			'url': href,
			'slug': slug,
			'nonce': ajax_object.nonce,
		};
		$.post(
			ajax_object.ajax_url,
			data,
			function( response ) {
				// Reload the page if success.
				window.location.reload();
			}
		);
	}

	/**
	 * AJAX POST function to activate plugin.
	 * Reload the page in any case.
	 *
	 * @param string href The URL to download asset.
	 * @param string slug The Slug of the Plugin.
	 */
	function activate_plugin( href, slug ) {
		var data = {
			'action': 'activate_cp_plugin',
			'url': href,
			'slug': slug,
			'nonce': ajax_object.nonce,
		};
		$.post(
			ajax_object.ajax_url,
			data,
			function( response ) {
				// Reload the page if success.
				window.location.reload();
			}
		);
	}

	/**
	 * AJAX POST function to delete plugin.
	 * On success reload page
	 * On failure (only detectable by the absence of "true"), show an error div.
	 *
	 * @param string href The URL to download asset.
	 * @param string slug The Slug of the Plugin.
	 */
	function delete_plugin( href, slug ) {
		var data = {
			'action': 'delete_cp_plugin',
			'url': href,
			'slug': slug,
			'nonce': ajax_object.nonce,
		};
		$.post(
			ajax_object.ajax_url,
			data,
			function( response ) {

				if ( true === response ) {
					// Reload the page if success.
					window.location.reload();
				} else {
					// If failure, display the error in an error DIV.
					$( '.notice-error' ).css( "display", "block" );
					response = response.replace( 'null', '' );
					$( '.notice-error' ).html( response );
				}
			}
		);
	}

})( jQuery );
