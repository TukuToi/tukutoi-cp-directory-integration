/**
 * Admin side jQuery.
 * Adds script to install plugin,
 * show spinner on ajax and
 * catch the exception of no mailer being on the computer of user.
 */
(function( $ ) {
	'use strict';

	var die = function( msg ) {
		throw new Error( msg );
	}

	$( document ).on(
		'ready',
		function() {
			$( '.cp-dir-select2' ).select2(
				{
					tags: true,
					width: '100%',
					placeholder: settings_object.placeholder,
					templateSelection : function (tag, container){
						// here we are finding option element of tag and
						// if it has property 'locked' we will add class 'locked-tag'
						// to be able to style element in select.
						var $option = $( '.cp-dir-select2 option[value="' + tag.id + '"]' );

						if ($option.attr( 'locked' )) {
							$( container ).find( "button" ).replaceWith( '<button type="button" class="cp-dir-verified-contributor-badge" tabindex="-1" title="Verified Contributor" aria-label="Verified Contributor"><span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 589.537 589.591"><defs><linearGradient id="a" x1="362.895" y1="362.9" x2="180.38" y2="180.385" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#3ebca6"/><stop offset="0.5" stop-color="#057f99"/><stop offset="1" stop-color="#006b81"/></linearGradient></defs><title>classicpress-logo-feather-gradient-on-transparent</title><path d="M592.578,10.577l-.038-.06c-2.019-3.352-6.948-5.759-10.922-5.535C506.913,9.189,386.56,52.972,381.485,54.838a12.221,12.221,0,0,0-6.729,6.033L357.8,94.742l-12.5-12.5a12.187,12.187,0,0,0-14.368-2.189c-14.317,7.477-86.638,45.826-102.025,64.5-8.932,10.8-15.861,34.556-20.525,55.492l-8.2-16.308a12.169,12.169,0,0,0-8.608-6.556A12.023,12.023,0,0,0,181.115,180c-4.328,3.8-94.612,85.035-75.815,186.327,72.234-102.984,159.046-189.573,301.051-259.295a12.281,12.281,0,0,1,10.86,22.031c-.013,0-.026.012-.039.012-8.259,4.067-16.282,8.209-24.157,12.389-2,1.057-3.968,2.127-5.946,3.2q-9.068,4.907-17.836,9.914c-1.905,1.095-3.8,2.165-5.674,3.26q-22.595,13.172-43.163,27.1c-1.132.759-2.239,1.555-3.384,2.326q-8.955,6.138-17.576,12.414c-1.007.735-2,1.469-3.011,2.215C162.419,300.746,91.529,426.008,6.557,576.248a12.281,12.281,0,0,0,10.7,18.31l.012-.024a12.221,12.221,0,0,0,10.686-6.22c33.834-59.833,65.393-115.61,99.663-167.231,13.558,16.768,32.95,25.549,57.1,25.549,92.883,0,246.27-135.86,261.72-179.62a12.283,12.283,0,0,0-8.6-16L396.9,240.764l89.127-14.852a12.177,12.177,0,0,0,8.957-6.642L593.249,22.757A12.591,12.591,0,0,0,592.578,10.577Z" transform="translate(-4.972 -4.967)" style="fill:url(#a)"/></svg></span></button>' );
							// $(container).addClass('locked-tag');
							tag.locked = true;
						}
						return tag.text;
					},
				}
			)
			.on(
				'select2:unselecting',
				function(e){
					// before removing tag we check option element of tag and
					// if it has property 'locked' we will create error to prevent all select2 functionality.
					if ($( e.params.args.data.element ).attr( 'locked' )) {
						e.preventDefault();
					}
				}
			);
		}
	);

})( jQuery );
