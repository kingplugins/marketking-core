/**
*
* JavaScript file that controls global admin notices (enables permanent dismissal)
*
*/
(function($){

	"use strict";

	$( document ).ready(function() {


		/* Admin notice permanent dismissal */
		$('.marketking_activate_woocommerce_notice button').on('click', function(){
			// Run ajax function that permanently dismisses notice
			var datavar = {
	            action: 'marketking_dismiss_activate_woocommerce_admin_notice',
	            security: marketking_notice.security,
	        };

			$.post(ajaxurl, datavar, function(response){
				// do nothing
			});

		});


	});

})(jQuery);