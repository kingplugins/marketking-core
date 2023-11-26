(function($){

	"use strict";

	$( document ).ready(function() {

		/**
		* General Functions
		*/

		// Initialize SemanticUI Menu Functions

		// radio buttons
		$('.ui.checkbox').checkbox();

		// accordions
		$('.ui.accordion').accordion();

		$('#marketking_form_button_options').popup();

		// Tab transition effect
		var previous = $('.ui.tab.segment.active');
	    $(".menu .item").tab({
	        onVisible: function (e) {
	            var current = $('.ui.tab.segment.active');
	            // hide the current and show the previous, so that we can animate them
	            previous.show();
	            current.hide();

	            // hide the previous tab - once this is done, we can show the new one
	            previous.find('.marketking_attached_content_wrapper').css('opacity','0');
	            current.find('.marketking_attached_content_wrapper').css('opacity','0');
	            setTimeout(function(){
	            	previous.hide();
	            	current.show();
	            	setTimeout(function(){
		            	current.find('.marketking_attached_content_wrapper').css('opacity','1');
		            	// remember the current tab for next change
		            	previous = current;
		            },10);
	            },150);
	            
	        }
	    });
	    
		$('.ui.dropdown').dropdown();
	
		$('.message .close').on('click', function() {
		    $(this).closest('.message').transition('fade');
		});

		// On Submit (Save Settings), Get Current Tab and Pass The Tab as a Setting. 
		$('#marketking_admin_form').on('submit', function() {
			let tabInput = document.querySelector('#marketking_current_tab_setting_input');
		    tabInput.value = document.querySelector('.active').dataset.tab;
		    return true; 
		});

		// On change vendor registration, hide or show vendor registration page
		hideShowRegistrationPage();

		$('input[name=marketking_vendor_registration_setting]').change(function() {
			hideShowRegistrationPage();
		});

		function hideShowRegistrationPage(){
			let selectedValue = $("input[name=marketking_vendor_registration_setting]:checked").val();
			if(selectedValue === "separate") {
		      	$("#marketking_vendor_registration_page_container").css("display","block");
		   	} else {
				$("#marketking_vendor_registration_page_container").css("display","none");
			}

			if (selectedValue === 'myaccount'){
				$("#marketking_myaccount_message").css("display","block");
			} else {
				$("#marketking_myaccount_message").css("display","none");
			}
		}

		// Logo Upload
		$('#marketking-upload-btn').on('click', function(e) {
	       e.preventDefault();

	       var image = wp.media({ 
	           title: 'Upload Image',
	           multiple: false
	       }).open()
	       .on('select', function(e){
	           // This will return the selected image from the Media Uploader, the result is an object
	           var uploaded_image = image.state().get('selection').first();
	           // Convert uploaded_image to a JSON object 
	           var marketking_image_url = uploaded_image.toJSON().url;
	           // Assign the url value to the input field
	           $('#marketking_logo_setting').val(marketking_image_url);
	           $('.marketking_email_preview_logo').attr('src', marketking_image_url);
	       });
	   	});


   		// Favicon Upload
   		$('#marketking-upload-btn-favicon').on('click', function(e) {
   	       e.preventDefault();

   	       var image = wp.media({ 
   	           title: 'Upload Image',
   	           multiple: false
   	       }).open()
   	       .on('select', function(e){
   	           // This will return the selected image from the Media Uploader, the result is an object
   	           var uploaded_image = image.state().get('selection').first();
   	           // Convert uploaded_image to a JSON object 
   	           var marketking_image_url = uploaded_image.toJSON().url;
   	           // Assign the url value to the input field
   	           $('#marketking_logo_favicon_setting').val(marketking_image_url);
   	           $('.marketking_email_preview_logo').attr('src', marketking_image_url);
   	       });
   	   	});

   	   	/* Color Schemes */
   	   	$('.marketking_color_scheme_button').on('click', function(){
   	   		let color = $(this).val();
   	   		if (color === 'gold'){
   	   			jQuery('input[name="marketking_main_dashboard_color_setting"]').val('#BEA163');
   	   			jQuery('input[name="marketking_main_dashboard_hover_color_setting"]').val('#A58A50');
   	   		}
   	   		if (color === 'indigo'){
   	   			jQuery('input[name="marketking_main_dashboard_color_setting"]').val('#854fff');
   	   			jQuery('input[name="marketking_main_dashboard_hover_color_setting"]').val('#6530dc');
   	   		}
   	   		if (color === 'jade'){
   	   			jQuery('input[name="marketking_main_dashboard_color_setting"]').val('#138c38');
   	   			jQuery('input[name="marketking_main_dashboard_hover_color_setting"]').val('#107A30');
   	   		}
   	   	})

   	   	// check if license activation
   	   	const urlParams = new URLSearchParams(window.location.search);
   	   	const myParam = urlParams.get('tab');
   	   	if (myParam === 'activate'){
   	   		$('.marketking_license').click();
   	   	}



	});

})(jQuery);
