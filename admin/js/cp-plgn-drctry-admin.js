/**
 * Admin side jQuery.
 * Adds script to install plugin,
 * show spinner on ajax and
 * catch the exception of no mailer being on the computer of user.
 *
 * @package    Plugins\CPDirectoryIntegration\Admin\JS
 * @author     Beda Schmid <beda@tukutoi.com>
 */

(function( $ ) {
	'use strict';

	var die = function( msg ) {
		throw new Error( msg );
	}

	$( document ).on(
		'ready',
		function() {

			/**
			 * Display a spinner on AJAX events.
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
			 * Load plugins by ajax if cache is empty on newpage load
			 */
			if ( true === $( '#button-refresh' ).data( 'refresh' ) ) {
				refresh_list();
			}
		}
	);

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
								avoid_coreplugin_managment( $( this ).data( "slug" ), $( this ).data( 'action' ) );
								deactivate_plugin( $( this ).attr( 'href' ), $( this ).data( "slug" ) )
							} else if ( 'activate' === $( this ).data( 'action' ) ) {
								activate_plugin( $( this ).attr( 'href' ), $( this ).data( "slug" ) )
							} else if ( 'delete' === $( this ).data( 'action' ) ) {
								avoid_coreplugin_managment( $( this ).data( "slug" ), $( this ).data( 'action' ) );
								delete_plugin( $( this ).attr( 'href' ), $( this ).data( "slug" ) )
							}
						}
					);
				}
			);
			/**
			 * If Refresh button is clicked, load new plugins with AJAX.
			 */
			$( '#button-refresh' ).on(
				'click',
				function(e) {
					e.preventDefault();
					refresh_list();
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
	 * Avoid core plugin(s) from being managed in the CP Plugins screen.
	 */
	function avoid_coreplugin_managment( slug, action ) {

		if ( 'tukutoi-cp-directory-integration/tukutoi-cp-directory-integration.php' === slug ) {
			$( '#cp-plgn-drctry-error' ).css( "display", "block" );
			$( '#cp-plgn-drctry-error' ).html( '<p>Please ' + action + ' this plugin the "Installed Plugins" screen</p>' );
			die( 'Please ' + action + ' this plugin the "Installed Plugins" screen' );
		}

	}

	/**
	 * AJAX POST function to refresh list.
	 */
	function refresh_list() {
		var data = {
			'action': 'refresh_list',
			'_ajax_nonce': ajax_object.nonce,
		};
		$.post(
			ajax_object.ajax_url,
			data,
			function( response ) {
				if ( 'loaded' === response ) {
					window.location.reload();
				} else {
					// If failure, display the error in an error DIV.
					$( '#cp-plgn-drctry-error' ).css( "display", "block" );
					response = response.replace( 'null', '' );
					$( '#cp-plgn-drctry-error' ).html( response );
				}
			}
		);
	}

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
			'_ajax_nonce': ajax_object.nonce,
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
					$( '#cp-plgn-drctry-error' ).css( "display", "block" );
					response = response.replace( 'null', '' );
					$( '#cp-plgn-drctry-error' ).html( response );
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
			'_ajax_nonce': ajax_object.nonce,
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
					$( '#cp-plgn-drctry-error' ).css( "display", "block" );
					response = response.replace( 'null', '' );
					$( '#cp-plgn-drctry-error' ).html( response );
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
			'_ajax_nonce': ajax_object.nonce,
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
			'_ajax_nonce': ajax_object.nonce,
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
			'action': 'delete-plugin',
			'url': href,
			'plugin': slug,
			'slug': slug,
			'_ajax_nonce': ajax_object.nonce,
		};
		$.post(
			ajax_object.ajax_url,
			data,
			function( response ) {

				if ( true === response.success ) {
					// Reload the page if success.
					window.location.reload();
				} else {
					// If failure, display the error in an error DIV.
					$( '#cp-plgn-drctry-error' ).css( "display", "block" );
					$( '#cp-plgn-drctry-error' ).html( response );
				}
			}
		);
	}

})( jQuery );
