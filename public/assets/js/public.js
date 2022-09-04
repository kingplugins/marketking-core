/**
*
* JavaScript file that handles public side JS
*
*/
(function($){

	"use strict";

	$( document ).ready(function() {

		let ajaxtables = parseInt(marketking_display_settings.load_tables_with_ajax);

		// Fix for pluginrepublic addons sortable issue mobile click
		setTimeout(function(){
			if ($(window). width() < 1460) {
				jQuery( "#pewc_group_wrapper" ).sortable( "disable" ); 
			}
		}, 200);


		// Fix icons loading issue
		failsafeicons();
		setTimeout(function(){
			failsafeicons();
		}, 500);
		function failsafeicons(){
			if (jQuery('.ni-comments').val()!==undefined){
				if(getComputedStyle(document.querySelector('.ni-comments'), ':before').getPropertyValue('content') === '"î²Ÿ"'){
					reloaddashlite();
				}
			}
		}
		function reloaddashlite(){
			let hrnew = jQuery('#marketking_dashboard-css').attr('href')+1;
			jQuery('#marketking_dashboard-css').attr('href', hrnew);
			console.log('reloaded dashicons');
		}

		// refresh cart totals (in new cart only) when changing the shipping method
		if(marketking_display_settings.cartstyle === 'newcart'){
			jQuery('body').on('change', '.woocommerce-shipping-methods input[type=radio]', function() {

	 			jQuery('button[name="update_cart"]').prop('disabled', false);
	 			setTimeout(function(){
	 				jQuery('button[name="update_cart"]').click();
	 			}, 500);
		        
	        });
		}

		// Vendor page tabs

		// set tab when page first loads
		setTimeout(function(){
			if(marketking_display_settings.pagetab !== ''){
				// go to tab

				//save link
				let link = window.location.href;
				$('.marketking_tablinks[value="marketking_vendor_tab_'+marketking_display_settings.pagetab+'"]').click();

				// restore link 
				window.history.pushState('marketking-multivendor-marketplace-for-woocommerce', '', link);

			} else {
				// default page is products

				// except if we're on vendor product page pagination
				if(parseInt(marketking_display_settings.is_vendor_product_pagination) === 0){
					$('.marketking_tablinks[value="marketking_vendor_tab_'+marketking_display_settings.defaulttab+'"]').click();
				}
			}
		}, 10);

		function endsWithNumber( str ){
		  return isNaN(str.slice(-1)) ? false : true;
		}

		// set global ending nr
		let globalurl = window.location.href;
		if (window.location.href.endsWith("/")){
			globalurl = window.location.href.substring(0, window.location.href.length - 1);
		}
		let endingmatchnr = false;
		let endingmatch = globalurl.replace(/^.*?(\d+(?:[.,]\d+)?)\s*$/, "$1");


		$('.marketking_tablinks').on('click', function(evt){
			$('.marketking_tab_active').removeClass('marketking_tab_active');

		
		    let cityName = $(this).val();

		    let tabname = cityName.split('_')[3];

		    // vendor base url
		    let baseurl = marketking_display_settings.currentvendorlink;
		    // remove ending slash for URL if it exists
		    if (baseurl.endsWith("/")){
		    	baseurl = baseurl.substring(0, baseurl.length - 1);
		    }
		    // add ending slash
		    baseurl = baseurl+'/';

		    // if a tab is selected
		    let available_tabs = ['reviews/','info/','products/','inquiries/','policies/'];
		    

		    tabname = baseurl+tabname;
		    // set tab in url
		    window.history.pushState('marketking-multivendor-marketplace-for-woocommerce', '', tabname);

		    // Declare all variables
		    var i, marketking_tab, marketking_tablinks;

		    // Get all elements with class="marketking_tab" and hide them
		    marketking_tab = document.getElementsByClassName("marketking_tab");
		    for (i = 0; i < marketking_tab.length; i++) {
		      marketking_tab[i].style.display = "none";
		    }

		    // Get all elements with class="marketking_tablinks" and remove the class "active"
		    marketking_tablinks = document.getElementsByClassName("marketking_tablinks");
		    for (i = 0; i < marketking_tablinks.length; i++) {
		      marketking_tablinks[i].className = marketking_tablinks[i].className.replace(" active", "");
		    }

		    // Show the current tab, and add an "active" class to the button that opened the tab
		    document.getElementById(cityName).style.display = "block";
		    evt.currentTarget.className += " active";
		});


		// beautify buttons
		jQuery('.marketking_edit_product_page .b2bking_product_add_tier, .marketking_edit_product_page .b2bking_product_add_row, .marketking_edit_product_page .save_attributes, .marketking_edit_product_page .add_attribute').addClass('btn btn-sm btn-secondary');
		jQuery('.marketking_manage_order_page button.add_note, .marketking_manage_order_page .grant_access, .marketking_manage_order_page .revoke_access, .marketking_manage_order_page .refund-items').addClass('btn btn-sm btn-secondary');
		// remove refund
		jQuery('.wc-order-bulk-actions.wc-order-data-row-toggle').remove();
		// remove download log
		jQuery('.order_download_permissions .wc-metabox-content td:nth-child(4)').remove();
		$('.grant_access').on('click', function(){

			setTimeout(function(){
				jQuery('.order_download_permissions .wc-metabox-content td:nth-child(4)').remove();
			}, 500);

		});
		
		/* B2BKing Integration */
		/* Product Visibility */
		// On page load, update product visibility options
		updateProductVisibilityOptions();

		// On Product Visibility option change, update product visibility options 
		$('#b2bking_product_visibility_override').change(function() {
			updateProductVisibilityOptions();
		});

		// Checks the selected Product Visibility option and hides or shows Automatic / Manual visibility options
		function updateProductVisibilityOptions(){
			let selectedValue = $("#b2bking_product_visibility_override").children("option:selected").val();
			if(selectedValue === "manual") {
		      	$("#b2bking_metabox_product_categories_wrapper").css("display","none");
		      	$("#b2bking_product_visibility_override_options_wrapper").css("display","block");
		   	} else if (selectedValue === "default"){
				$("#b2bking_product_visibility_override_options_wrapper").css("display","none");
				$("#b2bking_metabox_product_categories_wrapper").css("display","block");
			}
		}

		// Remove user list from product visibility
		$('.b2bking_category_users_textarea_buttons_container').remove();

		// remove marketplace suggestions (e.g. enhance your products
		$('.marketplace-suggestions_options').remove();

		/* SHIPPING TRACKING START */
		showHideTrackingURL();
		$('#marketking_create_shipment_provider').change(showHideTrackingURL);
		function showHideTrackingURL(){
			let selectedValue = $('#marketking_create_shipment_provider').val();
			if (selectedValue === 'sp-other'){
				// show
				$('.marketking_create_shipment_other').css('display','block');
			} else {
				// hide
				$('.marketking_create_shipment_other').css('display','none');

			}
		}

		$('#marketking_mark_order_received').on('click', function(){
	    	if (confirm(marketking_display_settings.sure_shipment_received)){
	    		// delete product
	    		var datavar = {
		            action: 'marketkingshipmentreceived',
		            security: marketking_display_settings.security,
		           	orderid: $(this).attr('value'),
		        };


		        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
		        	setTimeout(function(){
		        		location.reload();
		        	}, 250);
		        });
		    }
		});

		$('#marketking_create_shipment_button').on('click', function(){
			if ($('#marketking_create_shipment_tracking_number').val() !== ''){
		    	if (confirm(marketking_display_settings.sure_create_shipment)){
		    		// delete product
		    		var datavar = {
			            action: 'marketkingcreateshipment',
			            security: marketking_display_settings.security,
			           	orderid: $(this).attr('value'),
			           	provider: $('#marketking_create_shipment_provider').val(),
			           	providername: $('#marketking_create_shipment_provider_name').val(),
			           	trackingnr: $('#marketking_create_shipment_tracking_number').val(),
			           	trackingurl: $('#marketking_create_shipment_tracking_url').val(),
			        };


			        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
			        	setTimeout(function(){
			        		location.reload();
			        	}, 250);
			        });
			    }
			}
		});

		$('#marketking_add_another_shipment_button').on('click', function(){
			$('.marketking_new_shipment_hidden').removeClass('marketking_new_shipment_hidden');
			$(this).remove();
		});
		/* SHIPPING TRACKING END */

		/* Custom Registration Fields START */
		// Dropdown
		addCountryRequired(); // woocommerce_form_field does not allow required for country, so we add it here
		// On load, show hide fields depending on dropdown option
		showHideRegistrationFields();

		$('.country_to_state').trigger('change');
		$('#marketking_registration_options_dropdown').change(showHideRegistrationFields);
		$('.marketking_country_field_selector select').change(showHideRegistrationFields);
		
		function addCountryRequired(){
			$('.marketking_country_field_req_required').prop('required','true');
			$('.marketking_field_req_required select').prop('required','true');
		}

		function showHideRegistrationFields(){

			// Hide all custom fields. Remove 'required' for hidden fields with required
			$('.marketking_custom_registration_container').css('display','none');
			$('.marketking_field_req_required').removeAttr('required');
			$('.marketking_field_req_required select').removeAttr('required');
			$('.marketking_field_req_required #billing_state').removeAttr('required');
			
			// Show fields of all options. Set required
			$('.marketking_custom_registration_alloptions').css('display','block');
			$('.marketking_custom_registration_alloptions .marketking_field_req_required').prop('required','true');
			$('.marketking_custom_registration_alloptions .marketking_field_req_required select').prop('required','true');
			setTimeout(function(){
				$('.marketking_custom_registration_alloptions .marketking_field_req_required #billing_state').prop('required','true');
	        },125);

			// Show all fields of the selected option. Set required
			let selectedValue = $('#marketking_registration_options_dropdown').val();
			$('.marketking_custom_registration_'+selectedValue).css('display','block');
			$('.marketking_custom_registration_'+selectedValue+' .marketking_field_req_required').prop('required','true');
			$('.marketking_custom_registration_'+selectedValue+' .marketking_field_req_required select').prop('required','true');
			setTimeout(function(){
	        	$('.marketking_custom_registration_'+selectedValue+' .marketking_field_req_required #billing_state').prop('required','true');
	        },225);

			// if there is more than 1 country
			if(parseInt(marketking_display_settings.number_of_countries) !== 1){
				// check VAT available countries and selected country. If vat not available, remove vat and required
				let vatCountries = $('#marketking_vat_number_registration_field_countries').val();
				let selectedCountry = $('.marketking_country_field_selector select').val();
				if (selectedCountry === undefined){
					selectedCountry = $('select#billing_country').val();
				}
				if (vatCountries !== undefined){
					if ( (! (vatCountries.includes(selectedCountry))) || selectedCountry.trim().length === 0 ){
						// hide and remove required
						$('.marketking_vat_number_registration_field_container').css('display','none');
						$('#marketking_vat_number_registration_field').removeAttr('required');
					}
				}
			}

			// New for My Account VAT
			if (parseInt(marketking_display_settings.myaccountloggedin) === 1){
				// check VAT countries
				let vatCountries = $('#marketking_custom_billing_vat_countries_field input').prop('placeholder');
				let billingCountry = $('#billing_country').val();
				if (vatCountries !== undefined){
					if ( (! (vatCountries.includes(billingCountry))) || billingCountry.trim().length === 0){
						$('.marketking_vat_field_container, #marketking_checkout_registration_validate_vat_button').removeClass('marketking_vat_visible, marketking_vat_hidden').addClass('marketking_vat_hidden');
						$('.marketking_vat_field_required_1 input').removeAttr('required');
					} else {
						$('.marketking_vat_field_container, #marketking_checkout_registration_validate_vat_button').removeClass('marketking_vat_visible, marketking_vat_hidden').addClass('marketking_vat_visible');
						$('.marketking_vat_field_required_1 .optional').after('<abbr class="required" title="required">*</abbr>');
						$('.marketking_vat_field_required_1 .optional').remove();
						$('.marketking_vat_field_required_1 input').prop('required','true');
					}
				}
			}
			
		}


		var buttonclass = 'btn btn-sm btn-gray';

		var buttonclassedit = marketking_display_settings.edit_columns_class;
		/* PRODUCTS */

		// Scripts for b2bking integration 

        // on clicking "add tier" in the product page
        $('.b2bking_product_add_tier').on('click', function(){
        	var groupid = $(this).parent().find('.b2bking_groupid').val();
        	$('<span class="wrap b2bking_product_wrap"><input name="b2bking_group_'+groupid+'_pricetiers_quantity[]" placeholder="'+marketking_display_settings.min_quantity_text+'" class="b2bking_tiered_pricing_element" type="number" step="any" min="0" /><input name="b2bking_group_'+groupid+'_pricetiers_price[]" placeholder="'+marketking_display_settings.final_price_text+'" class="b2bking_tiered_pricing_element" type="number" step="any" min="0"  /></span>').insertBefore($(this).parent());
        });

        // on clicking "add row" in the product page
        $('.b2bking_product_add_row').on('click', function(){
        	var groupid = $(this).parent().find('.b2bking_groupid').val();
        	$('<span class="wrap b2bking_customrows_wrap"><input name="b2bking_group_'+groupid+'_customrows_label[]" placeholder="'+marketking_display_settings.label_text+'" class="b2bking_customrow_element" type="text" /><input name="b2bking_group_'+groupid+'_customrows_text[]" placeholder="'+marketking_display_settings.text_text+'" class="b2bking_customrow_element" type="text" /></span>').insertBefore($(this).parent());
        });

        // on clicking "add tier" in the product variation page
        $('body').on('click', '.b2bking_product_add_tier_variation', function(event) {
        	var groupid = $(this).parent().find('.b2bking_groupid').val();
        	var variationid = $(this).parent().find('.b2bking_variationid').val();
            $('<span class="wrap b2bking_product_wrap_variation"><input name="b2bking_group_'+groupid+'_'+variationid+'_pricetiers_quantity[]" placeholder="'+marketking_display_settings.min_quantity_text+'" class="b2bking_tiered_pricing_element_variation" type="number" step="any" min="0" /><input name="b2bking_group_'+groupid+'_'+variationid+'_pricetiers_price[]" placeholder="'+marketking_display_settings.final_price_text+'" class="b2bking_tiered_pricing_element_variation" type="number" step="any" min="0"  /></span>').insertBefore($(this).parent());
        });


        $('#b2bking_b2b_pricing_variations').detach().insertAfter('option[value=delete_all]');

		// Edit Products page
		jQuery('.hndle').append(jQuery('.type_box.hidden').detach());

		// On new product, replace attributes with a message to save the product
		if (marketking_display_settings.pagenroriginal === 'add'){
		//	jQuery('#variable_product_options_inner').html('<div class="marketking_new_attributes_message">'+marketking_display_settings.variations_message+'</div>');
		}

		// Manage order page: remove links from items (backend links to wp admin)
		jQuery('.marketking_manage_order_page #woocommerce-order-items #order_line_items a').removeAttr('href');


		$('#marketking_edit_product_status, #marketking_edit_coupon_status').on('change', function(){
			let selectedval = $(this).children("option:selected").val();
			if (selectedval==='publish'){
				$('#marketking_edit_product_status, #marketking_edit_coupon_status').removeClass('marketking_status_pending');
				$('#marketking_edit_product_status, #marketking_edit_coupon_status').removeClass('marketking_status_draft');
				$('#marketking_edit_product_status, #marketking_edit_coupon_status').addClass('marketking_status_publish');
			}

			if (selectedval==='pending'){
				$('#marketking_edit_product_status, #marketking_edit_coupon_status').removeClass('marketking_status_publish');
				$('#marketking_edit_product_status, #marketking_edit_coupon_status').removeClass('marketking_status_draft');
				$('#marketking_edit_product_status, #marketking_edit_coupon_status').addClass('marketking_status_pending');
			}

			if (selectedval==='draft'){
				$('#marketking_edit_product_status, #marketking_edit_coupon_status').removeClass('marketking_status_publish');
				$('#marketking_edit_product_status, #marketking_edit_coupon_status').removeClass('marketking_status_pending');
				$('#marketking_edit_product_status, #marketking_edit_coupon_status').addClass('marketking_status_draft');
			}


		});

	    // remove menu order capability
	    jQuery('.menu_order_field').remove();

	    // reviews
	    if (parseInt(marketking_display_settings.can_reviews) !== 1 || parseInt(marketking_display_settings.mkpror) !== 1){
	    	jQuery('.comment_status_field').remove();
	    }

	    // linked
	    if (parseInt(marketking_display_settings.can_linked_products) !== 1 || parseInt(marketking_display_settings.mkpror) !== 1){
	    	jQuery('.product_data_tabs .linked_product_options').remove();
	    }

	    // taxable
	    if (parseInt(marketking_display_settings.can_taxable_products) !== 1){
	    	jQuery('._tax_status_field').parent().remove();
	    }

	    // new attributes
	    if (parseInt(marketking_display_settings.can_new_attributes) !== 1){
	    	jQuery('select[name="attribute_taxonomy"] option[value=""]').remove();
	    }

	    $( 'button.add_attribute' ).on( 'click', function(e) {
	    	if (jQuery('select[name="attribute_taxonomy"]').children("option:selected").prop('disabled') || jQuery('select[name="attribute_taxonomy"]').children("option:selected").prop('disabled') === undefined){
	    		e.stopImmediatePropagation();
	    	}	   
	    });

	    // atributes fixes for usability
	    // When clicking on the add button, open the new attribute automatically
	    // also select the first non-disabled option in the dropdown so it doesn't show as blanks
	    jQuery('select[name="attribute_taxonomy"]').before('<span style="font-size:12px">'+marketking_display_settings.chooseattr+' </span>');

	    $( 'button.add_attribute' ).on( 'click', function(e) {
	    	// open new attribute
	    	let selected = $('select[name="attribute_taxonomy"]').val();

	    	// identical functions
	    	setTimeout(function(){
	    		let isclosed = $('.'+selected+' h3').parent().hasClass('open'); // they are mixed up
	    		if (isclosed){
	    			// open it
	    			$('.'+selected+' h3').click();
	    		}

	    		// select first non-disabled
	    		jQuery('select[name="attribute_taxonomy"] option').each(function() {
	    			if(!jQuery(this).prop('disabled')){
	    				jQuery('select[name="attribute_taxonomy"] option[value="'+this.value+'"]').prop('selected', true);
	    			}
	    		});
	    	}, 350);

	    	setTimeout(function(){
	    		let isclosed = $('.'+selected+' h3').parent().hasClass('open'); // they are mixed up
	    		if (isclosed){
	    			// open it
	    			$('.'+selected+' h3').click();
	    		}

	    		// select first non-disabled
	    		jQuery('select[name="attribute_taxonomy"] option').each(function() {
	    			if(!jQuery(this).prop('disabled')){
	    				jQuery('select[name="attribute_taxonomy"] option[value="'+this.value+'"]').prop('selected', true);
	    			}
	    		});
	    	}, 550);

	    	setTimeout(function(){
	    		let isclosed = $('.'+selected+' h3').parent().hasClass('open'); // they are mixed up
	    		if (isclosed){
	    			// open it
	    			$('.'+selected+' h3').click();
	    		}

	    		// select first non-disabled
	    		jQuery('select[name="attribute_taxonomy"] option').each(function() {
	    			if(!jQuery(this).prop('disabled')){
	    				jQuery('select[name="attribute_taxonomy"] option[value="'+this.value+'"]').prop('selected', true);
	    			}
	    		});
	    	}, 950);

	    	
	    });

	    // purchase notes
	    if (parseInt(marketking_display_settings.can_purchase_notes) !== 1 || parseInt(marketking_display_settings.mkpror) !== 1){
	    	jQuery('._purchase_note_field').remove();
	    }

	    // if purchase notes and reviews are off, remove advanced tab
	    if ((parseInt(marketking_display_settings.can_purchase_notes) !== 1 || parseInt(marketking_display_settings.mkpror) !== 1) && (parseInt(marketking_display_settings.can_reviews) !== 1 || parseInt(marketking_display_settings.mkpror) !== 1)){
	    	jQuery('.advanced_options').remove();
	    }

	    // remove wooVIP
	    jQuery('.vip_options, .vip_tab, .woovip-membership_options').remove();

	    // remove sliceWP
	    jQuery('.slicewp_options').remove();
   

	    if (parseInt(marketking_display_settings.mkpror) !== 1){
	    	
	    	jQuery('#product-type option[value="grouped"],#product-type option[value="external"],#product-type option[value="variable"]').remove();
	    	jQuery('#_virtual').parent().remove();
	    	jQuery('#_downloadable').parent().remove();
	    }

	    // if vendor can't downloadable or virtual based on group settings
	    if(parseInt(marketking_display_settings.can_downloadable) !== 1){
	    	jQuery('#_downloadable').parent().remove();
	    }
	    if(parseInt(marketking_display_settings.can_virtual) !== 1){
	    	jQuery('#_virtual').parent().remove();
	    }

	    // if all virtual, we need to hide it (remove it), but first enable it
	    if(parseInt(marketking_display_settings.all_virtual) === 1){
	    	jQuery('#_virtual').prop('checked', true);
	    	setTimeout(function(){
	    		jQuery('#_virtual').parent().remove();
	    		jQuery('.shipping_options').remove();
	    	}, 450);
	    }
	    // if all downloadable, we need to hide it (remove it), but first enable it
	    if(parseInt(marketking_display_settings.all_downloadable) === 1){
	    	jQuery('#_downloadable').prop('checked', true);
	    	setTimeout(function(){
	    		jQuery('#_downloadable').parent().remove();
	    	}, 450);
	    }


	    // remove disallowed product types
	    if (marketking_display_settings.remove_product_types !== undefined){
	    	let disallowed_types = JSON.parse(marketking_display_settings.remove_product_types);
	    	$(disallowed_types).each( function (i) {
	    		$('#product-type option[value="'+disallowed_types[i]+'"]').remove();
	    	});
	    }
	    


	    if (parseInt(marketking_display_settings.removeorders) === 1){
	    	jQuery('.add_note').remove();
	    	jQuery('.order_download_permissions').remove();
	    	jQuery('.revoke_access').remove();
	    	jQuery('.delete_note').remove();
	    }


	    // Delete product
	    $('body').on('click', '.marketking_delete_button', function(){
	    	if (confirm(marketking_display_settings.sure_delete_product)){
	    		// delete product
	    		var datavar = {
		            action: 'marketkingdeleteproduct',
		            security: marketking_display_settings.security,
		           	id: $(this).attr('value'),
		        };


		        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
		        	// redirect to products page
		        	window.location = marketking_display_settings.products_dashboard_page;
		        });

	    		
	    	}
	    });

	    // Delete coupon
	    $('body').on('click', '.marketking_delete_button_coupon', function(){
	    	if (confirm(marketking_display_settings.sure_delete_coupon)){
	    		// delete product
	    		var datavar = {
		            action: 'marketkingdeleteproduct',
		            security: marketking_display_settings.security,
		           	id: $(this).attr('value'),
		        };


		        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
		        	// redirect to products page
		        	window.location = marketking_display_settings.coupons_dashboard_page;
		        });

	    		
	    	}
	    });

	    // Save / Update Order
	    
	    $('#marketking_save_order_button').on('click', function(){
	    	
	    	var datavar = {
	            action: 'marketkingsaveorder',
	            security: marketking_display_settings.security,
	           	id: $('#marketking_save_order_button_id').val(),
	           	status: $('#marketking_order_status').val(),
	        };

	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){

	        	window.location = marketking_display_settings.order_edit_link+response+'?update=success';

	        });
	    });

	    // on page load, switch descriptions to visual
	    setTimeout(function(){
	    	$('.switch-tmce').click();
	    }, 250);
	    // Save / Update product
	    $('#marketking_save_product_button').on('click', function(){

	    	if ($('#marketking_product_title').val() !== ''){

	    		jQuery('#marketking_save_product_button .btn-primary').addClass('disabled');
	    		$(this).off('click');


		    	// switch descriptions to html before saving, helps pass the data correctly
		    	$('.switch-html').click();

		    	var title = $('#marketking_product_title').val();
		    	var actionedit = $('#marketking_edit_product_action_edit').val();
		    	var formprev = 'action=marketkingsaveproduct&security='+marketking_display_settings.security+'&id='+$('#marketking_save_product_button_id').val()+'&actionedit='+actionedit+'&title='+title+'&';
		    	var formdata = $('#marketking_save_product_form').serialize();
		    	var formtotal = formprev+formdata;

		    	$.post(marketking_display_settings.ajaxurl, formtotal, function(response){
		    		if(actionedit === 'edit'){
		    			window.location = marketking_display_settings.product_edit_link+response+'?update=success';
		    		} else if (actionedit === 'add'){
		    			// go to newly created product
		    			window.location = marketking_display_settings.product_edit_link+response+'?add=success';
		    		}
		    	});
		    } else {
		    	alert(marketking_display_settings.product_must_name);
		    }
	    });

	    // Save / Update coupon
	    $('#marketking_save_coupon_button').on('click', function(){
	    	// switch descriptions to html before saving, helps pass the data correctly
	    	var title = $('#marketking_coupon_code').val();
	    	var actionedit = $('#marketking_edit_coupon_action_edit').val();
	    	var formprev = 'action=marketkingsavecoupon&security='+marketking_display_settings.security+'&id='+$('#marketking_save_coupon_button_id').val()+'&actionedit='+actionedit+'&title='+title+'&';
	    	var formdata = $('#marketking_save_coupon_form').serialize();
	    	var formtotal = formprev+formdata;

	    	$.post(marketking_display_settings.ajaxurl, formtotal, function(response){
	    		if(actionedit === 'edit'){
	    			window.location = marketking_display_settings.coupon_edit_link+response+'?update=success';
	    		} else if (actionedit === 'add'){
	    			// go to newly created coupon
	    			window.location = marketking_display_settings.coupon_edit_link+response+'?add=success';
	    		}
	    	});
	    });

	    // Initialize category dropdown select
	    if (typeof jQuery('#marketking_select_categories').select2 === "function") {  
	    	jQuery('#marketking_select_categories').select2();
	    }

	    // Load Products Table
		if (typeof $('#marketking_dashboard_products_table').DataTable === "function") {  
			
			if (ajaxtables === 0){
				var abbatable = $('#marketking_dashboard_products_table').DataTable({
					"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 7, "desc" ]],
		            columnDefs: [
		                { "width": "40%", "targets": 0 },
		                { targets: marketking_display_settings.hidden_columns_products, visible: false}
		              ],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            }
				});

				// Products datatable
			    $('#marketking_dashboard_products_table tfoot tr:eq(0) th.tb-non-tools').each( function (i) {
			    	// except for actions
			    	if (i!==9){
				        var title = $(this).text();
				        $(this).html( '<input type="text" class="marketking_search_column" placeholder="'+marketking_display_settings.searchtext+title+'..." />' );
				        $( 'input', this ).on( 'keyup change', function () {
				            if ( abbatable.column(i).search() !== this.value ) {
				                abbatable
				                    .column(i)
				                    .search( this.value )
				                    .draw();
				            }
				        } );
				    }
			    } );
			} else {
				var abbatable = $('#marketking_dashboard_products_table').DataTable({

	       			"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            columnDefs: [
		                { "width": "18%", "targets": 0 },
		                { targets: [4], visible: false}
		              ],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            },
	       			"processing": true,
	       			"serverSide": true,
	       			"info": false,
	       		    "ajax": {
	       		   		"url": marketking_display_settings.ajaxurl,
	       		   		"type": "POST",
	       		   		"data":{
	       		   			action: 'marketking_products_table_ajax',
	       		   			security: marketking_display_settings.security,
	       		   		}
	       		   	},
	       		   	createdRow: function( row, data, dataIndex ) {
       		   	        // Set the data-status attribute, and add a class
       		   	        $( row ).addClass('nk-tb-item');
       		   	        $( row ).find('td').addClass('nk-tb-col');
       		   	        $( row ).find('td:eq(0)').addClass('marketking-column-large');
       		   	        
       		   	    }

	            });
			}
			

			$('#marketking_products_search').keyup(function(){
			      abbatable.search($(this).val()).draw() ;
			});

		}

		if (typeof $('#marketking_dashboard_orders_table').DataTable === "function") {  
			if (ajaxtables === 0){
				var abbtable = $('#marketking_dashboard_orders_table').DataTable({
					"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            }

				});

				// Orders datatable
			    $('#marketking_dashboard_orders_table tfoot tr:eq(0) th').each( function (i) {
			        var title = $(this).text();
			        $(this).html( '<input type="text" class="marketking_search_column" placeholder="'+marketking_display_settings.searchtext+title+'..." />' );
			 
			        $( 'input', this ).on( 'keyup change', function () {
			            if ( abbtable.column(i).search() !== this.value ) {
			                abbtable
			                    .column(i)
			                    .search( this.value )
			                    .draw();
			            }
			        } );
			    } );
			
			} else {
				var abbtable = $('#marketking_dashboard_orders_table').DataTable({
					"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            },
		            "processing": true,
	       			"serverSide": true,
	       			"info": false,
	       		    "ajax": {
	       		   		"url": marketking_display_settings.ajaxurl,
	       		   		"type": "POST",
	       		   		"data":{
	       		   			action: 'marketking_orders_table_ajax',
	       		   			security: marketking_display_settings.security,
	       		   		}
	       		   	},
	       		   	createdRow: function( row, data, dataIndex ) {
       		   	        // Set the data-status attribute, and add a class
       		   	        $( row ).addClass('nk-tb-item');
       		   	        $( row ).find('td').addClass('nk-tb-col');
       		   	        $( row ).find('td:eq(0)').addClass('marketking-column-large');
       		   	        
       		   	    }

				});
			}


			$('#marketking_orders_search').keyup(function(){
			      abbtable.search($(this).val()).draw() ;
			});
			
		}

		if (typeof $('#marketking_dashboard_earnings_table').DataTable === "function") {  

			if (ajaxtables === 0){

				var table = $('#marketking_dashboard_earnings_table').DataTable({
					"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            }
				});

				// Earnings datatable
			    $('#marketking_dashboard_earnings_table tfoot tr:eq(0) th').each( function (i) {

			    	if (i!==7){

				        var title = $(this).text();
				        $(this).html( '<input type="text" class="marketking_search_column" placeholder="'+marketking_display_settings.searchtext+title+'..." />' );
				 
				        $( 'input', this ).on( 'keyup change', function () {
				            if ( table.column(i).search() !== this.value ) {
				                table
				                    .column(i)
				                    .search( this.value )
				                    .draw();
				            }
				        } );
				    }
			    } );
	
			} else {
				var table = $('#marketking_dashboard_earnings_table').DataTable({
					"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            },
		            "processing": true,
	       			"serverSide": true,
	       			"info": false,
	       		    "ajax": {
	       		   		"url": marketking_display_settings.ajaxurl,
	       		   		"type": "POST",
	       		   		"data":{
	       		   			action: 'marketking_earnings_table_ajax',
	       		   			security: marketking_display_settings.security,
	       		   		}
	       		   	},
	       		   	createdRow: function( row, data, dataIndex ) {
       		   	        // Set the data-status attribute, and add a class
       		   	        $( row ).addClass('nk-tb-item');
       		   	        $( row ).find('td').addClass('nk-tb-col');
       		   	        $( row ).find('td:eq(0)').addClass('marketking-column-large');
       		   	        
       		   	    }

				});
			}

			$('#marketking_earnings_search').keyup(function(){
			      table.search($(this).val()).draw() ;
			});
		}

		// Load Coupons Table
		if (typeof $('#marketking_dashboard_coupons_table').DataTable === "function") {  
			
			if (ajaxtables === 0){
				var coupontable = $('#marketking_dashboard_coupons_table').DataTable({
					"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            }
				});

				// Products datatable
			    $('#marketking_dashboard_coupons_table tfoot tr:eq(0) th.tb-non-tools').each( function (i) {
			        var title = $(this).text();
			        $(this).html( '<input type="text" class="marketking_search_column" placeholder="'+marketking_display_settings.searchtext+title+'..." />' );
			        $( 'input', this ).on( 'keyup change', function () {
			            if ( coupontable.column(i).search() !== this.value ) {
			                coupontable
			                    .column(i)
			                    .search( this.value )
			                    .draw();
			            }
			        } );
			    } );
			} else {
				var coupontable = $('#marketking_dashboard_coupons_table').DataTable({

	       			"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            },
	       			"processing": true,
	       			"serverSide": true,
	       			"info": false,
	       		    "ajax": {
	       		   		"url": marketking_display_settings.ajaxurl,
	       		   		"type": "POST",
	       		   		"data":{
	       		   			action: 'marketking_coupons_table_ajax',
	       		   			security: marketking_display_settings.security,
	       		   		}
	       		   	},
	       		   	createdRow: function( row, data, dataIndex ) {
       		   	        // Set the data-status attribute, and add a class
       		   	        $( row ).addClass('nk-tb-item');
       		   	        $( row ).find('td').addClass('nk-tb-col');
       		   	        $( row ).find('td:eq(0)').addClass('marketking-column-large');
       		   	        
       		   	    }

	            });
			}
			

			$('#marketking_coupons_search').keyup(function(){
			      coupontable.search($(this).val()).draw() ;
			});

		}

		// Load Reviews Table
		if (typeof $('#marketking_dashboard_reviews_table').DataTable === "function") {  
			
			if (ajaxtables === 0){
				var reviewstable = $('#marketking_dashboard_reviews_table').DataTable({
					"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            }
				});

				// Products datatable
			    $('#marketking_dashboard_reviews_table tfoot tr:eq(0) th.tb-non-tools').each( function (i) {
			        var title = $(this).text();
			        $(this).html( '<input type="text" class="marketking_search_column" placeholder="'+marketking_display_settings.searchtext+title+'..." />' );
			        $( 'input', this ).on( 'keyup change', function () {
			            if ( reviewstable.column(i).search() !== this.value ) {
			                reviewstable
			                    .column(i)
			                    .search( this.value )
			                    .draw();
			            }
			        } );
			    } );
			} else {
				var reviewstable = $('#marketking_dashboard_reviews_table').DataTable({

	       			"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            },
	       			"processing": true,
	       			"serverSide": true,
	       			"info": false,
	       		    "ajax": {
	       		   		"url": marketking_display_settings.ajaxurl,
	       		   		"type": "POST",
	       		   		"data":{
	       		   			action: 'marketking_reviews_table_ajax',
	       		   			security: marketking_display_settings.security,
	       		   		}
	       		   	},
	       		   	createdRow: function( row, data, dataIndex ) {
       		   	        // Set the data-status attribute, and add a class
       		   	        $( row ).addClass('nk-tb-item');
       		   	        $( row ).find('td').addClass('nk-tb-col');
       		   	        $( row ).find('td:eq(0)').addClass('marketking-column-large');
       		   	        
       		   	    }

	            });
			}
			

			$('#marketking_reviews_search').keyup(function(){
			      reviewstable.search($(this).val()).draw() ;
			});

		}

		// Load Refunds Table
		if (typeof $('#marketking_dashboard_refunds_table').DataTable === "function") {  
			
			if (ajaxtables === 0){
				var refundstable = $('#marketking_dashboard_refunds_table').DataTable({
					"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            }
				});

				// Products datatable
			    $('#marketking_dashboard_refunds_table tfoot tr:eq(0) th.tb-non-tools').each( function (i) {
			        var title = $(this).text();
			        $(this).html( '<input type="text" class="marketking_search_column" placeholder="'+marketking_display_settings.searchtext+title+'..." />' );
			        $( 'input', this ).on( 'keyup change', function () {
			            if ( refundstable.column(i).search() !== this.value ) {
			                refundstable
			                    .column(i)
			                    .search( this.value )
			                    .draw();
			            }
			        } );
			    } );
			} else {
				var refundstable = $('#marketking_dashboard_refunds_table').DataTable({

	       			"language": {
					    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
					},
					oLanguage: {
		                sSearch: ""
		            },
		            dom: 'Bfrtip',
		            order: [[ 0, "desc" ]],
		            buttons: {
		                buttons: [
		                    { extend: 'csvHtml5', className: buttonclass, text: '↓ CSV', exportOptions: { columns: ":visible" } },
		                    { extend: 'pdfHtml5', className: buttonclass, text: '↓ PDF', exportOptions: { columns: ":visible" } },
		                    { extend: 'print', className: buttonclass, text: marketking_display_settings.print, exportOptions: { columns: ":visible" } },
		                    { extend: 'colvis', className: buttonclassedit, text: marketking_display_settings.edit_columns },
		                ]
		            },
	       			"processing": true,
	       			"serverSide": true,
	       			"info": false,
	       		    "ajax": {
	       		   		"url": marketking_display_settings.ajaxurl,
	       		   		"type": "POST",
	       		   		"data":{
	       		   			action: 'marketking_refunds_table_ajax',
	       		   			security: marketking_display_settings.security,
	       		   		}
	       		   	},
	       		   	createdRow: function( row, data, dataIndex ) {
       		   	        // Set the data-status attribute, and add a class
       		   	        $( row ).addClass('nk-tb-item');
       		   	        $( row ).find('td').addClass('nk-tb-col');
       		   	        $( row ).find('td:eq(0)').addClass('marketking-column-large');
       		   	        
       		   	    }

	            });
			}
			

			$('#marketking_refunds_search').keyup(function(){
			      refundstable.search($(this).val()).draw() ;
			});

		}

		// refunds
		// when page is opened, get conversation id in url and open that conversation
		var openUrl = window.location.href;
		var checkurl = new URL(openUrl);
		var conversationID = checkurl.searchParams.get("conversation");
		if (conversationID !== null){

			// cancel if not refunds page
		    if (jQuery('#marketking_dashboard_refunds_tablse').val() === 'undefined') { 
		        return;
		    }

		    $('#marketking_refunds_search').val(conversationID );
		    refundstable.search(conversationID);
		    setTimeout(function(){
		        $('button[value='+conversationID+']').click();
		    }, 100);
		}

		// approve reject buttons

		$('body').on('click', '.marketking_refund_approve', function(){
 			if (confirm(marketking_display_settings.sure_approve_refund)){
 				var datavar = {
 		            action: 'marketking_approve_refund',
 		            security: marketking_display_settings.security,
 		            refundid: $(this).val(),
 		        };

 		        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
 		        	location.reload();
 		        });
 		    }
		});

		$('body').on('click', '.marketking_refund_reject', function(){
			if (confirm(marketking_display_settings.sure_reject_refund)){
				var datavar = {
		            action: 'marketking_reject_refund',
		            security: marketking_display_settings.security,
		            refundid: $(this).val(),
		        };

		        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
		        	location.reload();
		        });
		    }
		});
		
		/* Payouts */
		showhidepaymentmethods();

		$('input[type=radio][name="marketkingpayoutMethod"]').change(function() {
			showhidepaymentmethods();
		});

		function showhidepaymentmethods(){
			// first hide all methods

			$('.marketking_paypal_info, .marketking_bank_info, .marketking_custom_info, .marketking_stripe_info').css('display', 'none');
			// Show which payment method the user chose
			let selectedValue = $('input[type=radio][name="marketkingpayoutMethod"]:checked').val();
			if (selectedValue === "paypal") {
				// show paypal
				$('.marketking_paypal_info').css('display', 'block');
			} else if (selectedValue === "bank"){
				$('.marketking_bank_info').css('display', 'block');
			} else if (selectedValue === "stripe"){
				$('.marketking_stripe_info').css('display', 'block');
			} else if (selectedValue === "custom"){
				$('.marketking_custom_info').css('display', 'block');
			}
		}

		$('#disconnect_stripe').on('click', function(){	
			if (confirm(marketking_display_settings.sure_disconnect_stripe)){
				var datavar = {
		            action: 'marketkingdisconnectstripe',
		            security: marketking_display_settings.security,
		        };


		        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
		        	location.reload();
		        });
			}
		});

		// save payout info
		$('#marketking_save_payout').on('click', function(){	
			if (confirm(marketking_display_settings.sure_save_info)){
				var datavar = {
		            action: 'marketkingsaveinfo',
		            security: marketking_display_settings.security,
		            chosenmethod: $('input[type=radio][name="marketkingpayoutMethod"]:checked').val(),
		            paypal: $('#paypal-email').val(),
		            custom: $('#custom-method').val(),
		            fullname: $('#full-name').val(),
		            billingaddress1: $('#billing-address-1').val(),
		            billingaddress2: $('#billing-address-2').val(),
		            city: $('#city').val(),
		            state: $('#state').val(),
		            postcode: $('#postcode').val(),
		            country: $('#country').val(),
		            bankholdername: $('#bank-account-holder-name').val(),
		            bankaccountnumber: $('#bank-account-number').val(),
		            branchcity: $('#bank-branch-city').val(),
		            branchcountry: $('#bank-branch-country').val(),
		            intermediarycode: $('#intermediary-bank-bank-code').val(),
		            intermediaryname: $('#intermediary-bank-name').val(),
		            intermediarycity: $('#intermediary-bank-city').val(),
		            intermediarycountry: $('#intermediary-bank-country').val(),
		        };


		        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
		        	location.reload();
		        });
		    }
		});

		// make withdrawal
		$('#marketking_make_withdrawal').on('click', function(){	
			let max = $('#marketking_max_withdraw').val();
				if (parseFloat(max) >= parseFloat($('#withdrawal-amount').val())){
					if (confirm(marketking_display_settings.sure_withdraw)){
					var datavar = {
			            action: 'marketking_make_withdrawal',
			            security: marketking_display_settings.security,
			            amount: $('#withdrawal-amount').val(),
			        };

			        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
			        	location.reload();
			        });
			    }
			} else {
				alert(marketking_display_settings.not_enough_funds);
			}
			
		});

		// save user profile settings
		$('#marketking_save_settings').on('click', function(){	
			var datavar = {
	            action: 'marketking_save_profile_settings',
	            security: marketking_display_settings.security,
	            announcementsemails: $('#new-announcements').is(":checked"),
	            messagesemails: $('#new-messages').is(":checked"),
	            reviewemails: $('#new-review').is(":checked"),
	            refundemails: $('#new-refund').is(":checked"),
	            dashboardajax: $('#dashajax').is(":checked"),
	            userid: $(this).val(),
	        };


	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	location.reload();
	        });

		});

	
		$('.marketking_update_profile').on('click', function(){	
			var datavar = {
	            action: 'marketking_save_profile_info',
	            security: marketking_display_settings.security,
	            firstname: $('#first-name').val(),
	            lastname: $('#last-name').val(),
	            companyname: $('#company-name').val(),
	            storename: $('#store-name').val(),
	            emailad: $('#email').val(),
	            aboutus: $('#aboutusdescription').val(),
	            phone: $('#phone').val(),
	            showphone: $('#showphone').is(":checked"),
	            showemail: $('#showemail').is(":checked"),
	            profileimage: $('#marketking_profile_logo_image').val(),
	            bannerimage: $('#marketking_profile_logo_image_banner').val(),
	            address1: $('#address1').val(),
	            address2: $('#address2').val(),
	            city: $('#city').val(),
	            postcode: $('#postcode').val(),
	            state: $('#billing_state').val(),
	            country: $('#billing_country').val(),


	        };

	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	location.reload();
	        });

		});

		// personal and images tab - this makes clicking on images go to the image tab
		$('.data-item-image').on('click', function(){
			$('.nav-tab-images').addClass('active');
			$('#images').addClass('active');

			$('.nav-tab-personal, .nav-tab-address, .nav-tab-aboutus, #personal, #aboutus, #address').removeClass('active');
		});
		$('.data-item-profile').on('click', function(){
			$('.nav-tab-personal').addClass('active');
			$('#personal').addClass('active');

			$('.nav-tab-images, .nav-tab-address, .nav-tab-aboutus, #images, #aboutus, #address').removeClass('active');
		});

		$('.data-item-address').on('click', function(){
			$('.nav-tab-address').addClass('active');
			$('#address').addClass('active');

			$('.nav-tab-images, .nav-tab-personal, .nav-tab-aboutus, #images, #aboutus, #personal').removeClass('active');
		});

		$('.data-item-aboutus').on('click', function(){
			$('.nav-tab-aboutus').addClass('active');
			$('#aboutus').addClass('active');

			$('.nav-tab-images, .nav-tab-personal, .nav-tab-address, #images, #address, #personal').removeClass('active');
		});



		// Edit Product main image 
		// Profile Upload
		$('#marketking_edit_product_main_image').on('click', function(e) {
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
	           $('#marketking_edit_product_main_image_value').val(uploaded_image.toJSON().id);
	           $('#marketking_edit_product_main_image').attr('src', marketking_image_url);
	       });
	   	});


		// Profile Upload
		$('.marketking-profile-image .marketking-upload-image').on('click', function(e) {
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
	           $('#marketking_profile_logo_image').val(marketking_image_url);
	           $('.marketking-upload-image img').attr('src', marketking_image_url);
	           hideshowclearbutton();
	       });
	   	});

	   	// Banner Upload
	   	$('.marketking-banner-image .marketking-upload-image').on('click', function(e) {
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
	           $('#marketking_profile_logo_image_banner').val(marketking_image_url);
	           $('.marketking-vendor-image .picture.banner').css('background-image', 'url("'+marketking_image_url+'")');
	           hideshowclearbutton();
	       });
	   	});

	   	// Invoice Upload Logo
	   	$('#marketking_invoice_logo_choose').on('click', function(e) {
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
	           $('#invoicestorelogo').val(marketking_image_url);
	       });
	   	});


	   	$('#marketking_clear_image_profile').on('click', function(e) {
	   		$('#marketking_profile_logo_image').val('');
	   		$('.marketking-upload-image img').attr('src', marketking_display_settings.profile_pic);

	   		hideshowclearbutton();
	   	});

	   	$('#marketking_clear_image_profile_banner').on('click', function(e) {
	   		$('#marketking_profile_logo_image_banner').val('');
	   		$('.marketking-vendor-image .picture.banner').css('background-image', '');

	   		hideshowclearbutton();
	   	});

	   	hideshowclearbutton();
	   	function hideshowclearbutton(){
	   		let selectedValue = $('#marketking_profile_logo_image').val();
	   		let selectedValue2 = $('#marketking_profile_logo_image_banner').val();

	   		if(selectedValue === "") {
	   			$('#marketking_clear_image_profile').css('display','none');
	   		} else {
	   			$('#marketking_clear_image_profile').css('display','block');
	   		}


	   	   	if(selectedValue2 === "") {
	   	   		$('#marketking_clear_image_profile_banner').css('display','none');
	   	   	} else {
	   	   		$('#marketking_clear_image_profile_banner').css('display','block');
	   	   	}
	   	}

		// force store url characters and check url availability
		$(document).on('keypress', '.billing_store_url', function(e) {

		    if ((e.keyCode < 48)) {
		        e.preventDefault();
		    }

		    if ((e.keyCode > 57)&&(e.keyCode < 65)) {
		        e.preventDefault();
		    }

		    if ((e.keyCode > 90)&&(e.keyCode < 97)) {
		        e.preventDefault();
		    }	

		    // max 25 characters
		    if ($('.billing_store_url').val().length > 25){
		    	e.preventDefault();
		    }

		});

		$(document).on('paste', '.billing_store_url', function(e) {
		    e.preventDefault();
		});

		$(document).on('input', '.billing_store_url', function(e) {

			// check entire text, and if it contains text, prevent
			let text = $(this).val();

		    // get the text and check it's availability
		    let storeurl = $('.billing_store_url').val();
		    var datavar = {
	            action: 'marketkingcheckurlexists',
	            security: marketking_display_settings.security,
	            url: storeurl,
	        };

	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	$('.marketking_availability').removeClass('marketking_url_unavailable');
	        	$('.marketking_availability').removeClass('marketking_url_available');


	        	if (response === 'yes'){
	        		$('.marketking_availability').html('('+marketking_display_settings.url_not_available+')');
	        		$('.marketking_availability').addClass('marketking_url_unavailable');
	        		$('.button.woocommerce-form-register__submit').prop('disabled', true);
	        	} else if (response === 'no'){
	        		$('.marketking_availability').html('('+marketking_display_settings.url_available+')');
	        		$('.marketking_availability').addClass('marketking_url_available');
	        		$('.button.woocommerce-form-register__submit').prop('disabled', false);
	        	}
	        	
	        });

		});

		// if url unavailable prevent submission
		$('.woocommerce-form-register').on('submit', function(e){
			if ($('.marketking_availability').hasClass('marketking_url_unavailable')){
				e.preventDefault();
			}
		});

		//Vendor Stores Page in frontend
		if (typeof $('#marketking_stores_vendors_table').DataTable === "function") { 
			$('#marketking_stores_vendors_table').DataTable({
				oLanguage: {
	                sEmptyTable: marketking_display_settings.no_vendors_yet
	            }
			});
		}

		// On clicking "Mark as read" for announcements
		$('#marketking_mark_announcement_read').on('click', function(){
			// Run ajax request
			var datavar = {
	            action: 'marketkingmarkread',
	            security: marketking_display_settings.security,
	            announcementid: $('#marketking_mark_announcement_read').val(),
	        };

			$.post(marketking_display_settings.ajaxurl, datavar, function(response){
				window.location = marketking_display_settings.announcementsurl;
			});
		});

		// On clicking "Mark all as read" for announcements
		$('#marketking_mark_all_announcement_read').on('click', function(){
			// Run ajax request
			var datavar = {
	            action: 'marketkingmarkallread',
	            security: marketking_display_settings.security,
	            announcementsid: $('#marketking_mark_all_announcement_read').val(),
	        };

			$.post(marketking_display_settings.ajaxurl, datavar, function(response){
				window.location = marketking_display_settings.announcementsurl;
			});
		});

		// On clicking "Mark as read" for conversations
		$('#marketking_mark_conversation_read').on('click', function(){
			// Run ajax request
			var datavar = {
	            action: 'marketkingmarkreadmessage',
	            security: marketking_display_settings.security,
	            messageid: $('#marketking_mark_conversation_read').val(),
	        };

			$.post(marketking_display_settings.ajaxurl, datavar, function(response){
				location.reload();
			});
		});

		// On clicking "Mark as closed" for conversations
		$('#marketking_mark_conversation_closed').on('click', function(){
			// Run ajax request
			var datavar = {
	            action: 'marketkingmarkclosedmessage',
	            security: marketking_display_settings.security,
	            messageid: $('#marketking_mark_conversation_closed').val(),
	        };

			$.post(marketking_display_settings.ajaxurl, datavar, function(response){
				window.location=marketking_display_settings.messagesurl;
			});
		});


		// On click Send in existing conversation
		$('#marketking_dashboard_reply_message').on('click', function(){

			// Run ajax request
			var datavar = {
	            action: 'marketkingreplymessage',
	            security: marketking_display_settings.security,
	            messagecontent: $('#marketking_dashboard_reply_message_content').val(),
	            messageid: $(this).val(),
	        };

			$.post(marketking_display_settings.ajaxurl, datavar, function(response){
				location.reload();
			});
		});

		// On clicking send (compose message)
		$('#marketking_compose_send_message').on('click', function(){

			// Run ajax request
			var datavar = {
	            action: 'marketkingcomposemessage',
	            security: marketking_display_settings.security,
	            messagecontent: $('#marketking_compose_send_message_content').val(),
	            recipient: $('#marketking_dashboard_recipient').val(),
	            title: $('#marketking_compose_send_message_title').val(),
	        };

			$.post(marketking_display_settings.ajaxurl, datavar, function(response){
				window.location = response;
			});
		});

		/* Coupons */
		$('.marketking_edit_coupon_page #discount_type option[value="fixed_cart"]').remove();
		$('.marketking_edit_coupon_page .free_shipping_field').remove();
		$('#product_categories').parent().parent().remove();

		/* Product Vendor Inquiries*/
		$('body').on('click', '#marketking_send_inquiry_button', function(){

			// if no fields are empty
			let empty = 'no';
			if ($('#marketking_send_inquiry_textarea').val() === '' || $('#marketking_send_inquiry_name').val() === '' || $('#marketking_send_inquiry_email').val() === '' || $('#marketking_send_inquiry_phone').val() === ''){
				empty = 'yes';
			}

			if (empty === 'no'){
				// validate email
				if (validateEmail($('#marketking_send_inquiry_email').val()) || $('#marketking_send_inquiry_email').val() === undefined){
					// run ajax request
					var datavar = {
			            action: 'marketkingsendinquiry',
			            security: marketking_display_settings.security,
			            message: $('#marketking_send_inquiry_textarea').val(),
			            name: $('#marketking_send_inquiry_name').val(),
			            email: $('#marketking_send_inquiry_email').val(),
			            phone: $('#marketking_send_inquiry_phone').val(),
			            title: marketking_display_settings.custom_inquiry,
			            vendor: $('#marketking_send_inquiry_button').val(),
			            product: $('#marketking_product_id').val(),
			            type: 'inquiry',
			        };

					$.post(marketking_display_settings.ajaxurl, datavar, function(response){
						let discussionurl = response;

						alert(marketking_display_settings.inquiry_success);
						if (parseInt(marketking_display_settings.loggedin) === 1){
							// go to my account conversation
							if (parseInt(discussionurl) !== 0){
								window.location = discussionurl;
							} else {
								location.reload();
							}
						} else {
							location.reload();
						}
						
					});

				} else {
					alert(marketking_display_settings.inquiry_invalid_email);
				}
				
			} else {
				alert(marketking_display_settings.inquiry_empty_fields);
			}
		});

		function validateEmail(email) {
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			return regex.test(email);
		}
		// messages send my account inquiries
		/* messages START */

		// On load message, scroll to message end
		// if message exists
		if ($('#marketking_message_messages_container').length){
			$("#marketking_message_messages_container").scrollTop($("#marketking_message_messages_container")[0].scrollHeight);
		}


		// On clicking "Send message" inside message in My account
		if (parseInt(marketking_display_settings.b2bking_exists) !== 1){
			$('#b2bking_conversation_message_submit').on('click', function(){
				// loader icon
				$('<img class="marketking_loader_icon_button" src="'+marketking_display_settings.loadertransparenturl+'">').insertBefore('.marketking_myaccount_message_endpoint_button_icon');
				$('.marketking_myaccount_message_endpoint_button_icon').remove();
				// Run ajax request
				var datavar = {
		            action: 'b2bkingconversationmessagerefunds',
		            security: marketking_display_settings.security,
		            message: $('#b2bking_conversation_user_new_message').val(),
		            conversationid: $('#b2bking_conversation_id').val(),
		        };

				$.post(marketking_display_settings.ajaxurl, datavar, function(response){
					location.reload();
				});
			});
		}
		

		// On clicking "Send message" inside message in My account
		$('#marketking_message_message_submit').on('click', function(){
			// loader icon
			$('<img class="marketking_loader_icon_button" src="'+marketking_display_settings.loadertransparenturl+'">').insertBefore('.marketking_myaccount_message_endpoint_button_icon');
			$('.marketking_myaccount_message_endpoint_button_icon').remove();
			// Run ajax request
			var datavar = {
	            action: 'marketkingmessagemessage',
	            security: marketking_display_settings.security,
	            message: $('#marketking_message_user_new_message').val(),
	            messageid: $('#marketking_message_id').val(),
	        };

			$.post(marketking_display_settings.ajaxurl, datavar, function(response){
				location.reload();
			});
		});

		// On clicking "New message" button
		$('#marketking_myaccount_make_inquiry_button').on('click', function(){
			// hide make inquiry button
			$('#marketking_myaccount_make_inquiry_button').css('display','none');
			// hide messages
			$('.marketking_myaccount_individual_message_container').css('display','none');
			// hide messages pagination
			$('.marketking_myaccount_messages_pagination_container').css('display','none');
			// show new message panel
			$('.marketking_myaccount_new_message_container').css('display','block');
		});

		// On clicking "Close X" button
		$('.marketking_myaccount_new_message_close').on('click', function(){
			// hide new message panel
			$('.marketking_myaccount_new_message_container').css('display','none');
			// show new message button
			$('#marketking_myaccount_make_inquiry_button').css('display','inline-flex');
			// show messages
			$('.marketking_myaccount_individual_message_container').css('display','block');
			// show pagination
			$('.marketking_myaccount_messages_pagination_container').css('display','flex');
			
		});

		/* messages END */

		/* Follow Favorite Stores START */

		$('.marketking_follow_button').on('click', function(){	
			var datavar = {
	            action: 'marketking_change_follow_status',
	            security: marketking_display_settings.security,
	            vendorid: $(this).val(),
	        };

	        let th = $(this);

	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	if (response === 'followed'){
	        		// set text
	        		$(th).text(marketking_display_settings.following_text);
	        	} else if (response === 'unfollowed'){
	        		$(th).text(marketking_display_settings.follow_text);
	        	}
	        });

		});
		

		/* Follow Favorite Stores END */

		/* Store Notice START */

		$('#marketking_save_notice_settings').on('click', function(){	
			var datavar = {
	            action: 'marketking_save_notice_settings',
	            security: marketking_display_settings.security,
	            noticeenabled: $('#noticeenabled').is(":checked"),
	            noticemessage: $('#noticemessage').val(),
	        };

	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	location.reload();
	        });

		});

		/* Store Notice END */

		/* Store Policy */

		$('#marketking_save_policy_settings').on('click', function(){	
			var datavar = {
	            action: 'marketking_save_policy_settings',
	            security: marketking_display_settings.security,
	            policyenabled: $('#policyenabled').is(":checked"),
	            policymessage: $('#policymessage').val(),
	        };

	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	location.reload();
	        });

		});

		/* Store Policy END */

		/* Store SEO START */

		$('#marketking_save_seo_settings').on('click', function(){	
			var datavar = {
		        action: 'marketking_save_seo_settings',
		        security: marketking_display_settings.security,
		        seotitle: $('#seotitle').val(),
		        metadescription: $('#metadescription').val(),
		        metakeywords: $('#metakeywords').val(),
		    };

		    $.post(marketking_display_settings.ajaxurl, datavar, function(response){
		    	location.reload();
		    });

		});
		/* Store SEO END */

		/* Invoice START */

		$('#marketking_save_invoice_settings').on('click', function(){	
			var datavar = {
		        action: 'marketking_save_invoice_settings',
		        security: marketking_display_settings.security,
		        invoicestorename: $('#invoicestorename').val(),
		        invoicestoreaddress: $('#invoicestoreaddress').val(),
		        invoicecustominfo: $('#invoicecustominfo').val(),
		        invoicestorelogo: $('#invoicestorelogo').val(),
		    };

		    $.post(marketking_display_settings.ajaxurl, datavar, function(response){
		    	location.reload();
		    });

		});

		/* Invoice END */

		/* Vacation Mode START */
		$('#marketking_save_vacation_settings').on('click', function(){	
			var datavar = {
	            action: 'marketking_save_vacation_settings',
	            security: marketking_display_settings.security,
	            vacationenabled: $('#vacationenabled').is(":checked"),
	            vacationmessage: $('#vacationmessage').val(),
	            closingtime: $('#closingtime').val(),
	            closestart: $('#marketking_scheduled_close_start').val(),
	            closeend: $('#marketking_scheduled_close_end').val(),
	        };

	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	location.reload();
	        });
		});

		// Start End Date Vacation
		showhidestartendvacation();

		$('#closingtime').change(function() {
			showhidestartendvacation();
		});

		function showhidestartendvacation(){
			// first hide all methods
			let time = $('#closingtime').val();
			if (time === 'dates'){
				// show
				$('#marketking_scheduled_close_dates').css('display','flex');
			} else {
				// hide
				$('#marketking_scheduled_close_dates').css('display','none');
			}
		}
		/* Vacation Mode END */

		/* Abuse Reports */
		$('.marketking_report_abuse_flagtext').on('click', function(){
			$('.marketking_report_abuse_hidden').css('display', 'block');
		});
		$('.marketking_send_report_abuse').on('click', function(){
			// if not empty
			if($.trim($(".marketking_report_abuse_message").val())!==''){
				if(confirm(marketking_display_settings.are_you_sure_abuse_report)){
		    		var datavar = {
			            action: 'marketkingabusereport',
			            security: marketking_display_settings.security,
			           	message: $(".marketking_report_abuse_message").val(),
			           	productid: $('.marketking_send_report_abuse').val()
			        };


			        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
			        	alert(marketking_display_settings.abuse_report_sent);
			        	$('.marketking_report_abuse_container').html('<br>'+marketking_display_settings.abuse_report_received);

			        });

				}
			}
			
		});

		// Duplicate product (Clone product function)
		$('body').on('click', '.marketking_clone_product', function(e){

			e.preventDefault();
			// get product id
			let productid = $(this).parent().find('.marketking_input_id').val();

    		var datavar = {
	            action: 'marketking_duplicate_product',
	            security: marketking_display_settings.security,
	           	productid: productid
	        };


	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	window.location = marketking_display_settings.products_link+response+'?add=success';

	        });
		});

		/* Single Product Multiple Vendors */
		$('#marketking_add_product_to_my_store').on('click', function(){

			// loader 
			$('<img class="marketking_loader_icon_button" src="'+marketking_display_settings.loadertransparenturl+'">').insertBefore('#marketking_add_product_to_my_store_icon');

    		var datavar = {
	            action: 'marketkingaddproductstore',
	            security: marketking_display_settings.security,
	           	productid: $(this).val()
	        };


	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	$('#marketking_add_product_to_my_store').html('&nbsp;&nbsp;&nbsp;&nbsp;'+marketking_display_settings.product_added_store);
	        	$('#marketking_add_product_to_my_store').prop('disabled', true);

	        	// remove loader
	        	$('.marketking_loader_icon_button').remove();
	        });
		});

		jQuery('.marketking_offers_show_more').on('click', function(){
			jQuery('.marketking_offer_hidden_initial').removeClass('marketking_offer_hidden_initial');
			jQuery(this).remove();
		});

		/* Store Reviews */
		$('body').on('click', '.marketking_view_review_button', function(event) {
			window.location = $(this).val();
		});


		$('body').on('click', '.marketking_report_review_button', function(event) {
			let reviewid = $(this).val();
			$('#review_id').val(reviewid);
			$('.marketking_report_review_button_hidden').click();
		});

		$('body').on('click', '.marketking_reply_review_button', function(event) {
			let reviewid = $(this).val();
			$('#review_id').val(reviewid);
			$('.marketking_reply_review_button_hidden').click();
		});

		// On clicking send report review
		$('body').on('click', '#marketking_report_review', function(event) {

			// Run ajax request
			var datavar = {
	            action: 'marketkingreportreview',
	            security: marketking_display_settings.security,
	            messagecontent: $('#marketking_report_review_content').val(),
	            reviewid: $('#review_id').val(),
	        };

			$.post(marketking_display_settings.ajaxurl, datavar, function(response){
				alert(marketking_display_settings.review_report_submitted);
				location.reload();
			});
		});

			// On clicking send report review
		$('body').on('click', '#marketking_reply_review', function(event) {

			if (confirm(marketking_display_settings.sure_reply_review)){
				// Run ajax request
				var datavar = {
		            action: 'marketkingreplyreview',
		            security: marketking_display_settings.security,
		            messagecontent: $('#marketking_reply_review_content').val(),
		            reviewid: $('#review_id').val(),
		        };

				$.post(marketking_display_settings.ajaxurl, datavar, function(response){
					alert(marketking_display_settings.review_reply_submitted);

					location.reload();
				});	
			}
			
		});

		// Refunds
		$('#marketking_request_refund_initial_button').on('click', function(){
			$('#marketking_request_refund_initial_button').css('display','none');
			$('#marketking_refund_request_panel').css('display','block');
		});

		$('body').on('click', '#marketking_refund_request_send', function(event) {
			let value = $('#marketking_refund_request_value').val();
			let partialamount = $('#marketking_refund_partial_amount').val();

			if (value === 'partial'){
				if (parseFloat(partialamount) > parseFloat($('#marketking_refund_partial_amount').prop('max'))){
					alert(marketking_display_settings.partial_exceed_refund);
					return;
				}
			}

			if (confirm(marketking_display_settings.sure_send_refund)){
				let orderid = $('#marketking_refund_order_id').val();
				let reason = $('#marketking_refund_request_reason').val();

				// Run ajax request
				var datavar = {
		            action: 'marketking_send_refund',
		            security: marketking_display_settings.security,
		            orderid: orderid,
		            value: value,
		            reason: reason,
		            partialamount: partialamount,
		        };

				$.post(marketking_display_settings.ajaxurl, datavar, function(response){
					alert(marketking_display_settings.refund_request_sent);

					location.reload();
				});	
			}	
		});

		$('#marketking_refund_request_value').change(showHidePartialRefund);
		function showHidePartialRefund(){
			let value = $('#marketking_refund_request_value').val();
			if (value === 'partial'){
				$('#marketking_refund_partial_container').css('display','block');
			} else {
				$('#marketking_refund_partial_container').css('display','none');
			}
		}

		// verification
		$('.marketking_verification_page .data-item-profile').on('click', function(){
			let id = jQuery(this).parent().find('.marketking_input_verification_id').val();
			let name = jQuery(this).parent().find('.marketking_input_verification_name').val();
			$('#marketking_label_file_verification').text(name);
			$('#marketking_upload_verification_id').val(id);
		});

		$('.marketking_upload_verification_file_button').on('click', function(){
			let id = $('#marketking_upload_verification_id').val();
			let file_url = $('#marketking_upload_file_verification').val();

			if (confirm(marketking_display_settings.sure_send_verification)){

				// Run ajax request
				var datavar = {
		            action: 'marketkingsendverification',
		            security: marketking_display_settings.security,
		            vitem: id,
		            fileurl: file_url
		        };

				$.post(marketking_display_settings.ajaxurl, datavar, function(response){

					location.reload();
				});	
			}
		});

		// Vendor Verification Upload
		$('.marketking_verification_choose_file_button').on('click', function(e) {
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
	           $('#marketking_upload_file_verification').val(marketking_image_url);
	           hideshowclearbutton();
	       });
	   	});

		// PRODUCT EXPORTER - based on wc-product-export.js
		;(function ( $, window ) {
			/**
			 * productExportForm handles the export process.
			 */
			var productExportForm = function( $form ) {
				this.$form = $form;
				this.xhr   = false;

				// Initial state.
				this.$form.find('.woocommerce-exporter-progress').val( 0 );

				// Methods.
				this.processStep = this.processStep.bind( this );

				// Events.
				$form.on( 'submit', { productExportForm: this }, this.onSubmit );
				$form.find( '.woocommerce-exporter-types' ).on( 'change', { productExportForm: this }, this.exportTypeFields );
			};

			/**
			 * Handle export form submission.
			 */
			productExportForm.prototype.onSubmit = function( event ) {
				event.preventDefault();

				var currentDate    = new Date(),
					day            = currentDate.getDate(),
					month          = currentDate.getMonth() + 1,
					year           = currentDate.getFullYear(),
					timestamp      = currentDate.getTime(),
					filename       = 'wc-product-'+marketking_display_settings.user_id+'-download.csv';

				event.data.productExportForm.$form.addClass( 'woocommerce-exporter__exporting' );
				event.data.productExportForm.$form.find('.woocommerce-exporter-progress').val( 0 );
				event.data.productExportForm.$form.find('.woocommerce-exporter-button').prop( 'disabled', true );
				event.data.productExportForm.processStep( 1, $( this ).serialize(), '', filename );
			};

			/**
			 * Process the current export step.
			 */
			productExportForm.prototype.processStep = function( step, data, columns, filename ) {
				var $this         = this,
					selected_columns = $( '.woocommerce-exporter-columns' ).val(),
					export_meta      = $( '#woocommerce-exporter-meta:checked' ).length ? 1: 0,
					export_types     = $( '.woocommerce-exporter-types' ).val(),
					export_category  = $( '.woocommerce-exporter-category' ).val();

				$.ajax( {
					type: 'POST',
					url: marketking_display_settings.ajaxurl,
					data: {
						form             : data,
						action           : 'woocommerce_do_ajax_product_export',
						step             : step,
						columns          : columns,
						selected_columns : selected_columns,
						export_meta      : export_meta,
						export_types     : export_types,
						export_category  : export_category,
						filename         : filename,
					},
					dataType: 'json',
					success: function( response ) {
						if ( response.success ) {
							if ( 'done' === response.data.step ) {
								$this.$form.find('.woocommerce-exporter-progress').val( response.data.percentage );
								window.location = response.data.url;
								setTimeout( function() {
									$this.$form.removeClass( 'woocommerce-exporter__exporting' );
									$this.$form.find('.woocommerce-exporter-button').prop( 'disabled', false );
								}, 2000 );
							} else {
								$this.$form.find('.woocommerce-exporter-progress').val( response.data.percentage );
								$this.processStep( parseInt( response.data.step, 10 ), data, response.data.columns, filename );
							}
						}


					}
				} ).fail( function( response ) {
					window.console.log( response );
				} );
			};

			/**
			 * Handle fields per export type.
			 */
			productExportForm.prototype.exportTypeFields = function() {
				var exportCategory = $( '.woocommerce-exporter-category' );

				if ( -1 !== $.inArray( 'variation', $( this ).val() ) ) {
					exportCategory.closest( 'tr' ).hide();
					exportCategory.val( '' ).trigger( 'change' ); // Reset WooSelect selected value.
				} else {
					exportCategory.closest( 'tr' ).show();
				}
			};

			/**
			 * Function to call productExportForm on jquery selector.
			 */
			$.fn.wc_product_export_form = function() {
				new productExportForm( this );
				return this;
			};

			$( '.woocommerce-exporter' ).wc_product_export_form();

		})( jQuery, window );

		// PRODUCT IMPORTER - based on wc-product-import.js
		window.marketking_run_import = function ( $ ) {

			if(jQuery('#importparams').val() === 'yes'){
				var zfile = jQuery('#file').val();
				var zupdate_existing = jQuery('#update_existing').val();
				var zdelimiter = jQuery('#delimiter').val();
				var zimport_nonce = jQuery('#import_nonce').val();
				var mapping_from = JSON.parse(jQuery('#mapping_from').val());
				var mapping_to = JSON.parse(jQuery('#mapping_to').val());

				var zmapping = {'from':mapping_from, 'to':mapping_to};
			}

			var productImportForm = function( $form ) {
				this.$form           = $form;
				this.xhr             = false;
				this.mapping         = zmapping;
				this.position        = 0;
				this.file            = zfile;
				this.update_existing = zupdate_existing;
				this.delimiter       = zdelimiter;
				this.security        = zimport_nonce;

				// Number of import successes/failures.
				this.imported = 0;
				this.failed   = 0;
				this.updated  = 0;
				this.skipped  = 0;

				// Initial state.
				this.$form.find('.woocommerce-importer-progress').val( 0 );

				this.run_import = this.run_import.bind( this );

				// Start importing.
				this.run_import();
			};

			productImportForm.prototype.run_import = function() {

				var $this = this;

				jQuery.ajax( {
					type: 'POST',
					url: marketking_display_settings.ajaxurl,
					data: {
						action          : 'woocommerce_do_ajax_product_import',
						position        : $this.position,
						mapping         : $this.mapping,
						file            : $this.file,
						update_existing : $this.update_existing,
						delimiter       : $this.delimiter,
						security        : $this.security
					},
					dataType: 'json',
					success: function( response ) {
						if ( response.success ) {
							$this.position  = response.data.position;
							$this.imported += response.data.imported;
							$this.failed   += response.data.failed;
							$this.updated  += response.data.updated;
							$this.skipped  += response.data.skipped;
							$this.$form.find('.woocommerce-importer-progress').val( response.data.percentage );

							if ( 'done' === response.data.position ) {
								var file_name = $this.file.split( '/' ).pop();
								let doneurl = response.data.url;
								window.location.href = doneurl;
							} else {
								$this.run_import();
							}
						}
					}
				} ).fail( function( response ) {
					console.log( response );
				} );
			};


			new productImportForm(jQuery( '.woocommerce-importer' ));

		};


		/* Team Member - Staff */
		// add subagent
		$('#marketking_add_member').on('click', function(){	

			if ($('#marketking_add_member_form')[0].checkValidity()){

				if (confirm(marketking_display_settings.sure_add_member)){
					var datavar = {
			            action: 'marketkingaddmember',
			            security: marketking_display_settings.security,
			            firstname: $('#first-name').val(),
			            description: $('#description').val(),
			            lastname: $('#last-name').val(),
			            phoneno: $('#phone-no').val(),
			            username: $('#username').val(),
			            emailaddress: $('#email-address').val(),
			            password: $('#password').val(),

			        };

			        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
			        	if (response.startsWith('error')){
			        		alert(marketking_display_settings.member_created_error+' '+response);
			        		console.log(response);
			        	} else {
			        		alert(marketking_display_settings.member_created);
			        		window.location = marketking_display_settings.team_members_link+'?add=success';
			        	}
			        	
			        });
			    }
			} else {
				alert(marketking_display_settings.fill_all_required);
			}

		});

		// Save / Update Team
		$('#marketking_save_team_button').on('click', function(){
			let panels = $('#marketking_team_dashboard_panels').val();
			let panelsarr = panels.split(':');

			var datavar = {
	            action: 'marketking_save_team_member',
	            security: marketking_display_settings.security,
	           	id: $('#marketking_save_team_button_id').val(),
	           	editproducts: $('#marketking_group_available_panel_editproducts').is(":checked"),
	           	editorders: $('#marketking_group_available_panel_editorders').is(":checked"),
	           	editcoupons: $('#marketking_group_available_panel_editcoupons').is(":checked"),
	           	panels: panels+':editproducts:editorders:editcoupons',
	        };

			$(panelsarr).each( function (i) {
		        let checked = $('#marketking_group_available_panel_'+panelsarr[i]).is(":checked");
		        datavar[panelsarr[i]] = checked;
			});


			$.post(marketking_display_settings.ajaxurl, datavar, function(response){
				window.location = marketking_display_settings.edit_team_link+response+'?update=success';

			});

		});

		// delete team
		$('.marketking_delete_team').on('click', function(){
			let userid = $(this).val();

			if (confirm(marketking_display_settings.sure_delete_team)){


				var datavar = {
		            action: 'marketking_delete_team_member',
		            security: marketking_display_settings.security,
		           	id: userid
		        };

				$.post(marketking_display_settings.ajaxurl, datavar, function(response){
					location.reload();
				});

			}

		});

		/* Membership */

		$('.marketking_membership_select_plan_button').on('click', function(){
			let prodid = $(this).val();

    		var datavar = {
	            action: 'marketking_member_select_plan',
	            security: marketking_display_settings.security,
	           	prodid: prodid
	        };


	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	// redirect to cart page (can be product page based on filters)
	        	window.location = response;
	        });
		});


		/* Integrations */

		// Subscriptio plugin
		// remove subscriptions checkbox in vendor dashboard
		$('label[for="rp_sub_subscription_product"]').remove();
		// YITH
		$('label[for="_ywsbs_subscription"]').remove();
		// Sumo Subscriptions
		$('.sumo_subscription_fields').remove();
		// WooCommerce Subscriptions: Remove subscription options for vendor (would require specific integrations)
		$('#product-type option[value="subscription"]').remove();
		$('#product-type option[value="variable-subscription"]').remove();


		/* Store Support */
		showhidesupport();

		$('#supportchoice').change(function() {
			showhidesupport();
		});

		function showhidesupport(){
			// first hide all methods
			let option = $('#supportchoice').val();
			if (option === 'messaging'){

				$('#marketking_support_url_container').css('display','none');
				$('#marketking_support_email_container').css('display','none');

			} else if(option === 'external') {
				
				$('#marketking_support_url_container').css('display','block');
				$('#marketking_support_email_container').css('display','none');

			} else if (option === 'email'){

				$('#marketking_support_url_container').css('display','none');
				$('#marketking_support_email_container').css('display','block');
			}
		}

		$('#marketking_save_support_settings').on('click', function(){	
			var datavar = {
	            action: 'marketking_save_support_settings',
	            security: marketking_display_settings.security,
	            supportemail: $('#supportemail').val(),
	            supporturl: $('#supporturl').val(),
	            supportchoice: $('#supportchoice').val(),	           
	        };

	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	location.reload();
	        });
		});

		$('body').on('click', '#marketking_send_support_button', function(){

			// if no fields are empty
			let empty = 'no';
			if ($('#marketking_send_support_textarea').val() === ''){
				empty = 'yes';
			}

			if (empty === 'no'){
				// run ajax request
				var datavar = {
		            action: 'marketkingsendsupport',
		            security: marketking_display_settings.security,
		            message: $('#marketking_send_support_textarea').val(),
		            title: marketking_display_settings.support_request,
		            vendor: $('#marketking_send_support_button').val(),
		            product: $('#marketking_product_id').val(),
		            order: $('#marketking_support_order').val(),
		            type: 'support',
		        };

				$.post(marketking_display_settings.ajaxurl, datavar, function(response){
					let discussionurl = response;


					alert(marketking_display_settings.support_success);

					window.location = discussionurl;

					
				});
				
			} else {
				alert(marketking_display_settings.inquiry_empty_fields);
			}
		});

		$('#marketking_request_support_initial_button').on('click', function(){
			$('#marketking_request_support_initial_button').css('display','none');
			$('#marketking_support_request_panel').css('display','block');
		});
		/* Store Support END */

		/* Advanced Shipping START */

		// add new method
		$('#marketking_add_shipping_method_insert').on('click', function(){

			if (confirm(marketking_display_settings.sure_add_shipping_method)){

				var datavar = {
		            action: 'marketking_add_shipping_method_vendor',
		            security: marketking_display_settings.security,
		            method_value: $('#marketking_add_shipping_method_select').val(),      
		            method_name: $('#marketking_add_shipping_method_select option:selected').text(),
		            zone_id: $('#marketking_current_zone_id').val()      
		        };

		        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
		        	location.reload();
		        });
		    }
		});

	
		// delete method
		$('.marketking_delete_shipping_button').on('click', function(){

			if (confirm(marketking_display_settings.sure_delete_shipping_method)){

				var datavar = {
		            action: 'marketking_delete_shipping_method_vendor',
		            security: marketking_display_settings.security,
		            deletedid: $(this).val(),
		        };

		        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
		        	location.reload();
		        });
		    }
		});

		// retrieve content when clicking configure
		$('.marketking_configure_shipping_button').on('click', function(){

			// retrieve content into the configure modal
			var datavar = {
	            action: 'marketking_configure_shipping_method_retrieve',
	            security: marketking_display_settings.security,
	            methodid: $(this).val(),
	        };

	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	$('#marketking_configure_method_details_content').html(response);
	        	// remove unwanted free shipping options
	        	$('#woocommerce_free_shipping_requires option[value="coupon"]').remove();
	        	$('#woocommerce_free_shipping_requires option[value="either"]').remove();
	        	$('#woocommerce_free_shipping_requires option[value="both"]').remove();

	        	$('label[for="woocommerce_free_shipping_ignore_discounts"]').parent().parent().remove();

	        	// remove taxable if vendor's group cannot have taxable
	        	if (parseInt(marketking_display_settings.can_taxable_products) !== 1){
	        		jQuery('#woocommerce_flat_rate_tax_status').val('none');
	        		jQuery('#woocommerce_local_pickup_tax_status').val('none');
		        	jQuery('#woocommerce_flat_rate_tax_status').parent().parent().parent().css('display','none');
		        	jQuery('#woocommerce_local_pickup_tax_status').parent().parent().parent().css('display','none');
		        	
		        }
	        });

	        
		});

		// save existing method
		$('#marketking_save_shipping_method_insert').on('click', function(){

			var method_value = $('#marketking_configure_method_value').val();
			var method_id = $('#marketking_configure_method_instance').val();
			var formprev = 'action=marketking_configure_shipping_method_save&security='+marketking_display_settings.security+'&method_value='+method_value+'&method_id='+method_id+'&';
			var formdata = $('#marketking_configure_method_form').serialize();
			var formtotal = formprev+formdata;

			$.post(marketking_display_settings.ajaxurl, formtotal, function(response){
				location.reload();
			});
		
		});

		// enable or disable method
		$('.marketking_method_enabled').on('change', function(){

			let val;
			if ($(this).is(":checked")){
				val = 1;
			} else {
				val = 0;
			}

			var datavar = {
	            action: 'marketking_enable_disable_shipping_method',
	            security: marketking_display_settings.security,
	            methodid: $(this).val(),
	            value: val,
	        };

	        $.post(marketking_display_settings.ajaxurl, datavar, function(response){
	        	// do nothing
	        });
		
		});
		
		
		/* Advanced Shipping END */



	});

})(jQuery);
