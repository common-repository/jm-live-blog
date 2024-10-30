jQuery( document ).ready(function( $ ) {

	let updateIds = [];

	window.setInterval( checkUpdates, 30000 );

	$( '.jm-live-blog-outer .jm-live-blog-section .jm-live-blog-update' ).each( function () {
		let updateID = this.id;
		updateID = updateID.replace( /\D/g,'' );
		updateIds.push( updateID );
	} );

	function checkUpdates() {
		$.post({
			url: jmliveblog.url,
			data: {
				nonce: jmliveblog.nonce,
				action: 'jm_live_blog_ajax',
				post_id: jmliveblog.post_id,
				update_ids: updateIds,
			},
			success: function ( response ) {
				$( response.data ).each( function() {
					var updateID = this.id;
					updateID = updateID.replace( /\D/g,'' );
					updateIds.push( updateID );
				} );
				if ( response.data != '' ) {
					$( '.jm-live-blog-outer .jm-live-blog-section-outer #jm-live-blog-new-updates' ).css( 'display', 'flex' );
					$( '.jm-live-blog-outer .jm-live-blog-section-outer #jm-live-blog-new-updates' ).on( 'click', function() {
						$( '.jm-live-blog-outer .jm-live-blog-section-outer #jm-live-blog-new-updates' ).hide();
						$( ".jm-live-blog-outer .jm-live-blog-section" ).animate( { scrollTop: 0 }, "fast" );
						if ( updateIds.length > 1 ) {
							$(response.data).insertBefore('.jm-live-blog-outer .jm-live-blog-section .jm-live-blog-update:first').fadeIn('fast');
						} else {
							$( '.jm-live-blog-section' ).html( response.data );
						}
						response.data = '';
					} );
				} else {
				}
			}
		})
	}
});
