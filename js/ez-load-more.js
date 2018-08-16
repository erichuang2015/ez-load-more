jQuery( document ).ready( function( $ ) {

	// JS to submit ajax for load more button
	$(document).on('click', '#ez-load-more', function(e) {
		$( '#ez-load-more-error' ).hide();
		
		var previous_paged = $( this ).attr( 'data-paged' );
		var current_paged = parseInt( previous_paged ) + 1;

		// Check for custom loader div
		var div_class_name = $(this).attr('data-loader').length > 0 ? '.' + $(this).attr('data-loader') : '.lds-ring';

		jQuery.ajax({
			type: 'POST',
			url: wp_ajax_url,
			data: {
				action : 'ez_load_more',
				paged: current_paged,
				template: $(this).attr( 'data-template' ),
				context: $(this).attr( 'data-context' ),
				query: load_more_data.query,
				nonce: $( '#ez-load-more-nonce' ).val(),
			},
			dataType: 'html',
			beforeSend:  function() {
				// Show loader and hide load more text
				$(div_class_name).show();
				$('#ez-load-more').hide();
			}
		})
		.done( function( response ) {
			// Hide loader
			$(div_class_name).hide();

			if ( response ) {
				$('#ez-load-more-area-wrapper').append( response );
				if(current_paged < parseInt($('#ez-load-more').attr('data-max-pages'))) {
					$('#ez-load-more').show();
					$('#ez-load-more').attr('data-paged', current_paged);
				} 	
			} 
			else {
				$('#ez-load-more-posts').hide();
				$('#ez-load-more-error').show();
			}
		} );

		e.preventDefault();
	});

});