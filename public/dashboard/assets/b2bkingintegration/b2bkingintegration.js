/**
*
* JavaScript file that has public action in marketking dashboard
*
*/
(function($){

	"use strict";

	$( document ).ready(function() {

        /* Offers */
        // Initialize DataTables
        var mainTable;
        if (typeof $('#b2bkingmarketking_dashboard_offers_table').DataTable === "function") { 
            mainTable = $('#b2bkingmarketking_dashboard_offers_table').DataTable({
                "language": {
                    "url": marketking_display_settings.datatables_folder+marketking_display_settings.tables_language_option+'.json'
                },
                responsive: true
            });
        }


        $('#marketking_offers_search').keyup(function(){
              mainTable.search($(this).val()).draw() ;
        });

        // when page opens, check if quote is set (response to make offer)
        let params = (new URL(document.location)).searchParams;
        let quote = params.get('quote'); // is the string "Jonathan Smith".
        if (quote !== null && quote !== ''){
            // we have a number
            let quotenr = parseInt(quote);
            setTimeout(function(){
                $('.b2bking_marketking_new_offer').click();
            }, 100);

            // get values via AJAX and load into edit
            // first run ajax call based on the offer id
            var datavar = {
                action: 'b2bking_get_offer_data',
                security: b2bkingmarketking_display_settings.security,
                quoteid: quotenr
            };

            $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
               var results = response;
               var resultsArray = results.split('*');
               // load values into fields
               $('#b2bking_admin_offer_textarea').val(resultsArray[2]);
               $('#b2bking_category_users_textarea').val(resultsArray[0]);
            
                offerRetrieveHiddenField();
                offerCalculateTotals();
            });

            

        }

        // When New Offer modalzz is opened
        $('body').on('click', '.b2bking_marketking_new_offer', openOffermodalzz);
        function openOffermodalzz(){
            clearOfferValues();
            $('.b2bking_marketking_save_new_offer').val('new');
            setTimeout(function(){
                $('.b2bking_offer_product_selector').select2();
            }, 200);
        }

        // Delete offer 
        $('body').on('click', '.b2bking_offer_delete_table', function(){
            let offer = $(this).val();
            if (confirm(b2bkingmarketking_display_settings.are_you_sure_delete_offer)){
                var datavar = {
                    action: 'b2bking_delete_ajax_offer',
                    security: b2bkingmarketking_display_settings.security,
                    offerid: offer,
                    userid: $('#b2bking_new_offer_user_id').val()
                };

                $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
                   location.reload();
                });
            }
        });

        function clearOfferValues(){
            $('.b2bking_marketking_email_offer').remove();
            $('.b2bking_group_visibility_container_content_checkbox_input').prop('checked',false);
            $('#b2bking_category_users_textarea').val('');
            $('#b2bking_offer_customtext_textarea').val('');
            $('#b2bking_new_offer_title').val('');
            $('.b2bking_offer_line_number').each(function(){
                // remove all except first
                if ($(this).attr('ID') !== 'b2bking_offer_number_1'){
                    $(this).remove();
                }
                // clear first
                $('#b2bking_offer_number_1 .b2bking_offer_text_input').val('');
                $('#b2bking_offer_number_1 .b2bking_offer_product_selector').val('').trigger('change');
                offerCalculateTotals();
                offerSetHiddenField();
            });
        }

        // Email Offer
        $('body').on('click', '.b2bking_marketking_email_offer', function(){
            let offeridd = $(this).val();

            if (confirm(b2bkingmarketking_display_settings.email_offer_confirm)){
                var datavar = {
                    action: 'b2bking_email_offer_marketking',
                    security: b2bkingmarketking_display_settings.security,
                    offerid: offeridd,
                    offerlink: b2bkingmarketking_display_settings.offers_endpoint_link,
                };

                $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
                   
                   alert(b2bkingmarketking_display_settings.email_has_been_sent);
                });
            }
        });

        // Edit Offer
        $('body').on('click', '.b2bking_offer_edit_table', function (){
            var offer_id = $(this).val();
            // clear all values
            clearOfferValues();
            
            setTimeout(function(){
                // set button for save offer
                $('.b2bking_marketking_save_new_offer').val(offer_id);

                // add email offer button
                $('.b2bking_marketking_save_new_offer').after('<button type="button" value="'+offer_id+'" class="btn btn-secondary marketking-btn marketking-btn-theme b2bking_marketking_email_offer">'+b2bkingmarketking_display_settings.email_offer+'</button>');
              
            }, 200);
            // get values via AJAX and load into edit
            // first run ajax call based on the offer id
            var datavar = {
                action: 'b2bking_get_offer_data',
                security: b2bkingmarketking_display_settings.security,
                offerid: offer_id,
                userid: $('#b2bking_new_offer_user_id').val()
            };

            $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
               var results = response;
               var resultsArray = results.split('*');
               // load values into fields
               $('#b2bking_offer_customtext_textarea').val(resultsArray[3]);
               $('#b2bking_admin_offer_textarea').val(resultsArray[2]);
               $('#b2bking_category_users_textarea').val(resultsArray[0]);
               $('#b2bking_new_offer_title').val(resultsArray[4]);
                // foreach group visible
                let groups = resultsArray[1].split(',');
                groups.forEach((element) => {
                    $('#'+element).prop('checked', true);
                });
                offerRetrieveHiddenField();
                offerCalculateTotals();
            });
        });

        // Save Offers
        $('.b2bking_marketking_save_new_offer').on('click', function(){
            var vall = $(this).val();
            if (!$('#b2bking_new_offer_title').val()){
                alert(b2bkingmarketking_display_settings.offer_must_have_title);
                return;
            }
            if (!$('#b2bking_admin_offer_textarea').val()){
                alert(b2bkingmarketking_display_settings.offer_must_have_product);
                return;
            }

            if (confirm(b2bkingmarketking_display_settings.are_you_sure_save_offer)){
                var datavar = {
                    action: 'b2bking_save_new_ajax_offer',
                    security: b2bkingmarketking_display_settings.security,
                    uservisibility: $('#b2bking_category_users_textarea').val(),
                    customtext: $('#b2bking_offer_customtext_textarea').val(),
                    offerdetails: $('#b2bking_admin_offer_textarea').val(),
                    userid: $('#b2bking_new_offer_user_id').val(),
                    offertitle: $('#b2bking_new_offer_title').val(),
                    newedit: $('.b2bking_marketking_save_new_offer').val()
                };

                // send quote
                let quote = params.get('quote'); // is the string "Jonathan Smith".
                if (quote !== null && quote !== ''){
                    datavar.b2bking_quote_response = quote;
                }

               //  b2bking_group_visibility_container_content
                // for each checkbox adde
                var groupvisibilitytext = '';
                $('.b2bking_group_visibility_container_content_checkbox_input:checkbox:checked').each(function(){
                    groupvisibilitytext += $(this).attr('name')+',';
                });

                datavar.groupvisibility = groupvisibilitytext;

                $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
                    var offeridd = response;
                    // ask if email the offer
                    if (vall === 'new'){
                        if (confirm(b2bkingmarketking_display_settings.also_email_offer)){
                                var datavar = {
                                    action: 'b2bking_email_offer_marketking',
                                    security: b2bkingmarketking_display_settings.security,
                                    offerid: offeridd,
                                    offerlink: b2bkingmarketking_display_settings.offers_endpoint_link,
                                };

                                $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
                                   alert(b2bkingmarketking_display_settings.email_has_been_sent);
                                   window.location=b2bkingmarketking_display_settings.offers_link;
                                });
                        } else {
                            window.location=b2bkingmarketking_display_settings.offers_link;
                        }
                    } else {
                        window.location=b2bkingmarketking_display_settings.offers_link;
                    }
                    
                });
            }
        });

        // When click "add item" add new offer item
        $('body').on('click', '.b2bking_offer_add_item_button', addNewOfferItem);

        var offerItemsCounter = 1;
        function addNewOfferItem(){
            // destroy select2
            $('.b2bking_offer_product_selector').select2();
            $('.b2bking_offer_product_selector').select2('destroy');

            let currentItem = offerItemsCounter;
            let nextItem = currentItem+1;
            offerItemsCounter++;
            $('#b2bking_offer_number_1').clone().attr('id', 'b2bking_offer_number_'+nextItem).insertAfter('#b2bking_offer_number_1');
            // clear values from clone
            $('#b2bking_offer_number_'+nextItem+' .b2bking_offer_text_input').val('');
            $('#b2bking_offer_number_'+nextItem+' .b2bking_offer_product_selector').val('').trigger('change');
            // remove delete if it exists
            $('#b2bking_offer_number_'+nextItem+' .b2bking_offer_delete_item_button').remove();
            
            $('#b2bking_offer_number_'+nextItem+' .b2bking_item_subtotal').text(b2bkingmarketking_display_settings.currency_symbol+'0');
            // add delete button to new item
            $('<button type="button" class="secondary-button button b2bking_offer_delete_item_button btn btn-secondary">'+b2bkingmarketking_display_settings.text_delete+'</button>').insertAfter('#b2bking_offer_number_'+nextItem+' .b2bking_offer_add_item_button');
            
            //reinitialize select2
            $('.b2bking_offer_product_selector').select2();
        }

        // On click "delete"
        $('body').on('click', '.b2bking_offer_delete_item_button', function(){
            $(this).parent().parent().remove();
            offerCalculateTotals();
            offerSetHiddenField();
        });

        // On quantity or price change, calculate totals
        $('body').on('input', '.b2bking_offer_item_quantity, .b2bking_offer_item_name, .b2bking_offer_item_price', function(){
            offerCalculateTotals();
            offerSetHiddenField();
        });
        
        function offerCalculateTotals(){
            let total = 0;
            // foreach item calculate subtotal
            $('.b2bking_offer_item_quantity').each(function(){
                let quantity = $(this).val();
                let price = $(this).parent().parent().find('.b2bking_offer_item_price').val();
                if (quantity !== undefined && price !== undefined){
                    // set subtotal
                    total+=price*quantity;
                    $(this).parent().parent().find('.b2bking_item_subtotal').text(b2bkingmarketking_display_settings.currency_symbol+Number((price*quantity).toFixed(4)));
                }
            });

            // finished, add up subtotals to get total
            $('#b2bking_offer_total_text_number').text(b2bkingmarketking_display_settings.currency_symbol+Number((total).toFixed(4)));
        }

        function offerSetHiddenField(){
            let field = '';
            // clear textarea
            $('#b2bking_admin_offer_textarea').val('');
            // go through all items and list them IF they have PRICE AND QUANTITY
            $('.b2bking_offer_item_quantity').each(function(){
                let quantity = $(this).val();
                let price = $(this).parent().parent().find('.b2bking_offer_item_price').val();
                if (quantity !== undefined && price !== undefined && quantity !== null && price !== null && quantity !== '' && price !== ''){
                    // Add it to string
                    let name = $(this).parent().parent().find('.b2bking_offer_item_name').val();
                    if (name === undefined || name === ''){
                        name = '(no title)';
                    }
                    field+= name+';'+quantity+';'+price+'|';
                }
            });

            // at the end, remove last character
            field = field.substring(0, field.length - 1);
            $('#b2bking_admin_offer_textarea').val(field);
        }

        function offerRetrieveHiddenField(){
            // get field;
            let field = $('#b2bking_admin_offer_textarea').val();
            let itemsArray = field.split('|');
            // foreach condition, add condition, add new item
            itemsArray.forEach(function(item){
                let itemDetails = item.split(';');
                if (itemDetails[0] !== undefined && itemDetails[0] !== ''){
                    $('#b2bking_offer_number_'+offerItemsCounter+' .b2bking_offer_item_name').val(itemDetails[0]);
                    $('#b2bking_offer_number_'+offerItemsCounter+' .b2bking_offer_item_quantity').val(itemDetails[1]);
                    $('#b2bking_offer_number_'+offerItemsCounter+' .b2bking_offer_item_price').val(itemDetails[2]);
                    addNewOfferItem();
                }
            });
            // at the end, remove the last Item added
            if (offerItemsCounter > 1){
                $('#b2bking_offer_number_'+offerItemsCounter).remove();
            }

        }

		// on clicking "add tier" in the product dashboard page
		$('body').on('click', '.b2bking_product_add_tier', function() {
	    	var groupid = $(this).parent().find('.b2bking_groupid').val();
	    	$('<div class="marketking-form-group marketking-clearfix marketking-price-container show_if_simple"><div class="content-half-part"><div class="marketking-input-group"><span class="marketking-input-group-addon"><></span><input type="text" class="b2bking_tiered_pricing_element marketking-product-regular-price wc_input_price marketking-form-control" name="b2bking_group_'+groupid+'_pricetiers_quantity[]" placeholder="'+b2bkingmarketking_display_settings.min_quantity_text+'"></div></div><div class="content-half-part sale-price"><div class="marketking-input-group"><span class="marketking-input-group-addon">'+b2bkingmarketking_display_settings.currency_symbol+'</span><input type="text" class="b2bking_tiered_pricing_element marketking-product-sales-price wc_input_price marketking-form-control"  name="b2bking_group_'+groupid+'_pricetiers_price[]" placeholder="'+b2bkingmarketking_display_settings.final_price_text+'"></div></div></div>').insertBefore($(this).parent());
	    });

	    // on clicking "add tier" for variations in the product dashboard page
		$('body').on('click', '.b2bking_product_add_tier_variation', function() {
	    	var groupid = $(this).parent().find('.b2bking_groupid').val();
	    	var variationid = $(this).parent().find('.b2bking_variationid').val();
	    	$('<div class="marketking-form-group marketking-clearfix marketking-price-container show_if_simple"><div class="content-half-part"><div class="marketking-input-group"><span class="marketking-input-group-addon"><></span><input type="text" class="b2bking_tiered_pricing_element marketking-product-regular-price wc_input_price marketking-form-control" name="b2bking_group_'+groupid+'_'+variationid+'_pricetiers_quantity[]" placeholder="'+b2bkingmarketking_display_settings.min_quantity_text+'"></div></div><div class="content-half-part sale-price"><div class="marketking-input-group"><span class="marketking-input-group-addon">'+b2bkingmarketking_display_settings.currency_symbol+'</span><input type="text" class="b2bking_tiered_pricing_element marketking-product-sales-price wc_input_price marketking-form-control"  name="b2bking_group_'+groupid+'_'+variationid+'_pricetiers_price[]" placeholder="'+b2bkingmarketking_display_settings.final_price_text+'"></div></div></div>').insertBefore($(this).parent());
	    });

    	// on clicking "add row" in the product dashboard page
    	$('body').on('click', '.b2bking_product_add_row', function() {
        	var groupid = $(this).parent().find('.b2bking_groupid').val();
        	$('<div class="marketking-form-group marketking-clearfix marketking-price-container show_if_simple"><div class="content-half-part"><div class="marketking-input-group"><span class="marketking-input-group-addon">'+b2bkingmarketking_display_settings.label_text+'</span><input type="text" class="b2bking_customrow_element marketking-form-control" name="b2bking_group_'+groupid+'_customrows_label[]"></div></div><div class="content-half-part sale-price"><div class="marketking-input-group"><span class="marketking-input-group-addon">'+b2bkingmarketking_display_settings.text_text+'</span><input type="text" class="b2bking_customrow_element marketking-form-control"  name="b2bking_group_'+groupid+'_customrows_text[]" ></div></div></div>').insertBefore($(this).parent());
        });

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
        
        // On clicking the "Add user button in the Product Category User Visibility table"
        $("#b2bking_category_add_user").on("click",function(){
        	// Get username
        	let username = $("#b2bking_all_users_dropdown").children("option:selected").text();
        	// Get content and check if username already exists
        	let content = $("#b2bking_category_users_textarea").val();
        	let usersarray = content.split(',');
        	let exists = 0;

        	$.each( usersarray, function( i, val ) {
        		if (val.trim() === username){
        			exists = 1;
        		}
        	});

        	if (exists === 1){
        		// Show "Username already in the list" for 3 seconds
        		$("#b2bking_category_add_user").text(b2bkingmarketking_display_settings.username_already_list);
        		setTimeout(function(){
        			$("#b2bking_category_add_user").text(b2bkingmarketking_display_settings.add_user);
        		}, 2000);

        	} else {
        		// remove last comma and whitespace after
        		content = content.replace(/,\s*$/, "");
        		// if list is not empty, add comma
        		if (content.length > 0){
        			content = content + ', ';
        		}
        		// add username
        		content = content + username;
        		$("#b2bking_category_users_textarea").val(content);
        	}
        });

        /* Dynamic Rules */

        // Initialize Select2s
        $('#b2bking_rule_select_who').select2();
        $('#b2bking_rule_select_applies').select2();
        

        // initialize multiple products / categories selector as Select2
        $('.b2bking_select_multiple_product_categories_selector_select, .b2bking_select_multiple_users_selector_select').select2({'width':'100%', 'theme':'classic'});
        // show hide multiple products categories selector
        showHideMultipleProductsCategoriesSelector();
        $('#b2bking_rule_select_what').change(showHideMultipleProductsCategoriesSelector);
        $('#b2bking_rule_select_applies').change(showHideMultipleProductsCategoriesSelector);
        function showHideMultipleProductsCategoriesSelector(){
            let selectedValue = $('#b2bking_rule_select_applies').val();
            let selectedWhat = $('#b2bking_rule_select_what').val();
            if (selectedValue === 'multiple_options' && selectedWhat !== 'tax_exemption_user'){
                $('#b2bking_select_multiple_product_categories_selector').css('display','block');
            } else {
                $('#b2bking_select_multiple_product_categories_selector').css('display','none');
            }
        }

        showHideMultipleUsersSelector();
        $('#b2bking_rule_select_who').change(showHideMultipleUsersSelector);
        function showHideMultipleUsersSelector(){
            let selectedValue = $('#b2bking_rule_select_who').val();
            if (selectedValue === 'multiple_options'){
                $('#b2bking_select_multiple_users_selector').css('display','block');
            } else {
                $('#b2bking_select_multiple_users_selector').css('display','none');
            }
        }

        function setUpConditionsFromHidden(){
            // get all conditions
            let conditions = $('#b2bking_rule_select_conditions').val();
            if (conditions === undefined) {
                conditions = '';
            }

            if(conditions.trim() !== ''){  
                let conditionsArray = conditions.split('|');
                let i=1;
                // foreach condition, create selectors
                conditionsArray.forEach(function(item){
                    let conditionDetails = item.split(';');
                    // if condition not empty
                    if (conditionDetails[0] !== ''){
                        $('.b2bking_dynamic_rule_condition_name.b2bking_condition_identifier_'+i).val(conditionDetails[0]);
                        $('.b2bking_dynamic_rule_condition_operator.b2bking_condition_identifier_'+i).val(conditionDetails[1]);
                        $('.b2bking_dynamic_rule_condition_number.b2bking_condition_identifier_'+i).val(conditionDetails[2]);
                        addNewCondition(i, 'programatically');
                        i++;
                    }
                });
            }
        }

        // On clicking "add condition" in Dynamic rule
        $('body').on('click', '.b2bking_dynamic_rule_condition_add_button', function(event) {
            addNewCondition(1,'user');
        });

        function addNewCondition(buttonNumber = 1, type = 'user'){
            let currentNumber;
            let nextNumber;

            // If condition was added by user
            if (type === 'user'){
                // get its current number
                let classList = $('.b2bking_dynamic_rule_condition_add_button').attr('class').split(/\s+/);
                $.each(classList, function(index, item) {
                    if (item.includes('identifier')) {
                        var itemArray = item.split("_");
                        currentNumber = parseInt(itemArray[3]);
                    }
                });
                // set next number
                nextNumber = (currentNumber+1);
            } else {
                // If condition was added at page load automatically
                currentNumber = buttonNumber;
                nextNumber = currentNumber+1;
            }

            // add delete button same condition
            $('.b2bking_dynamic_rule_condition_add_button.b2bking_condition_identifier_'+currentNumber).after('<button type="button" class="b2bking_dynamic_rule_condition_delete_button b2bking_condition_identifier_'+currentNumber+'">'+b2bkingmarketking_display_settings.delete+'</button>');
            // add next condition
            $('#b2bking_condition_number_'+currentNumber).after('<div id="b2bking_condition_number_'+nextNumber+'" class="b2bking_rule_condition_container">'+
                '<select class="b2bking_dynamic_rule_condition_name b2bking_condition_identifier_'+nextNumber+'">'+
                    '<option value="product_quantity">'+b2bkingmarketking_display_settings.product_quantity+'</option>'+
                    '<option value="product_value">'+b2bkingmarketking_display_settings.product_value+'</option>'+
                '</select>'+
                '<select class="b2bking_dynamic_rule_condition_operator b2bking_condition_identifier_'+nextNumber+'">'+
                    '<option value="greater">'+b2bkingmarketking_display_settings.greater+'</option>'+
                    '<option value="equal">'+b2bkingmarketking_display_settings.equal+'</option>'+
                    '<option value="smaller">'+b2bkingmarketking_display_settings.smaller+'</option>'+
                '</select>'+
                '<input type="number" step="0.00001" class="b2bking_dynamic_rule_condition_number b2bking_condition_identifier_'+nextNumber+'" placeholder="'+b2bkingmarketking_display_settings.enter_quantity_value+'">'+
                '<button type="button" class="b2bking_dynamic_rule_condition_add_button b2bking_condition_identifier_'+nextNumber+'">'+b2bkingmarketking_display_settings.add_condition+'</button>'+
            '</div>');

            // remove self 
            $('.b2bking_dynamic_rule_condition_add_button.b2bking_condition_identifier_'+currentNumber).remove();

            // update available options
            updateDynamicRulesOptionsConditions();
        }

        // On clicking "delete condition" in Dynamic rule
        $('body').on('click', '.b2bking_dynamic_rule_condition_delete_button', function () {
            // get its current number
            let currentNumber;
            let classList = $(this).attr('class').split(/\s+/);
            $.each(classList, function(index, item) {
                if (item.includes('identifier')) {
                    var itemArray = item.split("_");
                    currentNumber = parseInt(itemArray[3]);
                }
            });
            // remove current element
            $('#b2bking_condition_number_'+currentNumber).remove();

            // update conditions hidden field
            updateConditionsHiddenField();
        });

        // On Rule selector change, update dynamic rule conditions
        $('#b2bking_rule_select_what, #b2bking_rule_select_who, #b2bking_rule_select_applies, #b2bking_rule_select, #b2bking_rule_select_showtax, #b2bking_container_tax_shipping').change(function() {
            updateDynamicRulesOptionsConditions();
        });

        function updateDynamicRulesOptionsConditions(){
            $('#b2bking_rule_select_applies_replaced_container').css('display','none');
            // Hide one-time fee
            $('#b2bking_one_time').css('display','none');
            // Hide all condition options
            $('.b2bking_dynamic_rule_condition_name option').css('display','none');
            // Hide quantity/value
            $('#b2bking_container_quantity_value').css('display','none');
            // Hide currency
            $('#b2bking_container_currency').css('display','none');
            // Hide payment methods
            $('#b2bking_container_paymentmethods').css('display','none');
            // Hide countries and requires
            $('#b2bking_container_countries, #b2bking_container_requires, #b2bking_container_showtax').css('display','none');
            // Hide tax name
            $('#b2bking_container_taxname, #b2bking_container_tax_shipping, #b2bking_container_tax_shipping_rate').css('display','none');
            // Hide discount checkbox
            $('.b2bking_dynamic_rule_discount_show_everywhere_checkbox_container, .b2bking_discount_options_information_box').css('display','none');
            $('#b2bking_container_discountname').css('display','none');
            $('.b2bking_rule_label_discount').css('display','none');

            // conditions box text
            $('#b2bking_rule_conditions_information_box_text').text(b2bkingmarketking_display_settings.conditions_apply_cumulatively);

            // Show all options
            $("#b2bking_container_howmuch").css('display','inline-block');
            $('#b2bking_container_applies').css('display','inline-block');
            // Show conditions + conditions info box
            $('#b2bking_rule_select_conditions_container').css('display','inline-block');
            $('.b2bking_rule_conditions_information_box').css('display','flex');

            let selectedWhat = $("#b2bking_rule_select_what").val();
            let selectedApplies = $("#b2bking_rule_select_applies").val();
            // Select Discount Amount or Percentage
            if (selectedWhat === 'discount_amount' || selectedWhat === 'discount_percentage'){
                // if select Cart: cart_total_quantity and cart_total_value
                if (selectedApplies === 'cart_total'){
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=cart_total_value]').css('display','block');
                } else if (selectedApplies.startsWith("category")){
                // if select Category also have: category_product_quantity and category_product_value
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=cart_total_value], .b2bking_dynamic_rule_condition_name option[value=category_product_quantity], .b2bking_dynamic_rule_condition_name option[value=category_product_value]').css('display','block');
                } else if (selectedApplies.startsWith("product")){
                // if select Product also have: product_quantity and product_value  
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=cart_total_value], .b2bking_dynamic_rule_condition_name option[value=product_quantity], .b2bking_dynamic_rule_condition_name option[value=product_value]').css('display','block');
                } else if (selectedApplies === 'multiple_options'){
                    $('.b2bking_dynamic_rule_condition_name option').css('display','block');
                    // conditions box text
                    $('#b2bking_rule_conditions_information_box_text').text(b2bkingmarketking_display_settings.conditions_multiselect);
                }
                // Show discount everywhere checkbox
                $('.b2bking_dynamic_rule_discount_show_everywhere_checkbox_container, .b2bking_discount_options_information_box').css('display','flex');
                $('.b2bking_rule_label_discount').css('display','block');
                $('#b2bking_container_discountname').css('display','inline-block');
            } else if (selectedWhat === 'fixed_price'){
                if (selectedApplies === 'cart_total'){
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=cart_total_value]').css('display','block');
                } else if (selectedApplies.startsWith("category")){
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=category_product_quantity]').css('display','block');
                } else if (selectedApplies.startsWith("product")){
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=product_quantity]').css('display','block');
                } else if (selectedApplies === 'multiple_options'){
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=category_product_quantity], .b2bking_dynamic_rule_condition_name option[value=product_quantity]').css('display','block');
                    $('#b2bking_rule_conditions_information_box_text').text(b2bkingmarketking_display_settings.conditions_multiselect);
                }
            } else if (selectedWhat === 'free_shipping'){
                // How much does not apply - hide
                $('#b2bking_container_howmuch').css('display','none');
                // if select Cart: cart_total_quantity and cart_total_value
                if (selectedApplies === 'cart_total'){
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=cart_total_value]').css('display','block');
                } else if (selectedApplies.startsWith("category")){
                // if select Category also have: category_product_quantity and category_product_value
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=cart_total_value], .b2bking_dynamic_rule_condition_name option[value=category_product_quantity], .b2bking_dynamic_rule_condition_name option[value=category_product_value]').css('display','block');
                } else if (selectedApplies.startsWith("product")){
                // if select Product also have: product_quantity and product_value 
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=cart_total_value], .b2bking_dynamic_rule_condition_name option[value=product_quantity], .b2bking_dynamic_rule_condition_name option[value=product_value]').css('display','block'); 
                } else if (selectedApplies === 'multiple_options'){
                    $('.b2bking_dynamic_rule_condition_name option').css('display','block');
                    $('#b2bking_rule_conditions_information_box_text').text(b2bkingmarketking_display_settings.conditions_multiselect);
                }
            } else if (selectedWhat === 'hidden_price'){
                // How much does not apply - hide
                $('#b2bking_container_howmuch').css('display','none');
                // hide Conditions input and available conditions text
                $('#b2bking_rule_select_conditions_container').css('display','none');
                $('.b2bking_rule_conditions_information_box').css('display','none');

            } else if (selectedWhat === 'required_multiple'){

                // if select Cart: cart_total_quantity and cart_total_value
                if (selectedApplies === 'cart_total'){
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=cart_total_value]').css('display','block');
                } else if (selectedApplies.startsWith("category")){
                // if select Category also have: category_product_quantity and category_product_value
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=cart_total_value], .b2bking_dynamic_rule_condition_name option[value=category_product_quantity], .b2bking_dynamic_rule_condition_name option[value=category_product_value]').css('display','block');
                } else if (selectedApplies.startsWith("product")){
                // if select Product also have: product_quantity and product_value  
                    $('.b2bking_dynamic_rule_condition_name option[value=cart_total_quantity], .b2bking_dynamic_rule_condition_name option[value=cart_total_value], .b2bking_dynamic_rule_condition_name option[value=product_quantity], .b2bking_dynamic_rule_condition_name option[value=product_value]').css('display','block');
                } else if (selectedApplies === 'multiple_options'){
                    $('.b2bking_dynamic_rule_condition_name option').css('display','block');
                    $('#b2bking_rule_conditions_information_box_text').text(b2bkingmarketking_display_settings.conditions_multiselect);
                }

            } else if (selectedWhat === 'minimum_order' || selectedWhat === 'maximum_order' ) {
                // show Quantity/value
                $('#b2bking_container_quantity_value').css('display','inline-block');
                // hide Conditions input and available conditions text
                $('#b2bking_rule_select_conditions_container').css('display','none');
                $('.b2bking_rule_conditions_information_box').css('display','none');
            }

            if (selectedApplies === 'replace_ids' && selectedWhat !== 'tax_exemption_user'){
                $('#b2bking_rule_select_applies_replaced_container').css('display','block');
            }

            // Check all conditions. If selected condition what is display none, change to Cart Total Quantity (available for all)
            $(".b2bking_dynamic_rule_condition_name").each(function (i) {
                let selected = $(this).val();
                let selectedOption = $(this).find("option[value="+selected+"]");
                if (selectedOption.css('display')==='none'){
                    $(this).val('cart_total_quantity');
                }
            });

            // Update Conditions
            updateConditionsHiddenField();
        }

        // On condition text change, update conditions hidden field
        $('body').on('input', '.b2bking_dynamic_rule_condition_number, .b2bking_dynamic_rule_condition_operator, .b2bking_dynamic_rule_condition_name', function () {
            updateConditionsHiddenField();
        });

        function updateConditionsHiddenField(){
            // Clear condtions field
            $('#b2bking_rule_select_conditions').val('');
            // For each condition, if not empty, add to field
            let conditions = '';

            $(".b2bking_dynamic_rule_condition_name").each(function (i) {
                // get its current number
                let currentNumber;
                let classList = $(this).attr('class').split(/\s+/);
                $.each(classList, function(index, item) {
                    if (item.includes('identifier')) {
                        var itemArray = item.split("_");
                        currentNumber = parseInt(itemArray[3]);
                    }
                });

                let numberField = $(".b2bking_dynamic_rule_condition_number.b2bking_condition_identifier_"+currentNumber).val();
                if (numberField === undefined){
                    numberField = '';
                }

                if (numberField.trim() !== ''){
                    conditions+=$(this).val()+';';
                    conditions+=$(".b2bking_dynamic_rule_condition_operator.b2bking_condition_identifier_"+currentNumber).val()+';';
                    conditions+=$(".b2bking_dynamic_rule_condition_number.b2bking_condition_identifier_"+currentNumber).val()+'|';
                }
            });
            // remove last character
            conditions = conditions.substring(0, conditions.length - 1);
            $('#b2bking_rule_select_conditions').val(conditions);
        }

        // Save Rules
        $('.b2bking_marketking_save_new_rule').on('click', function(){
            if (!$('#b2bking_new_rule_title').val()){
                alert(b2bkingmarketking_display_settings.rule_must_have_title);
                return;
            }

            if (confirm(b2bkingmarketking_display_settings.are_you_sure_save_rule)){
                var checked;
                if ($('#b2bking_dynamic_rule_discount_show_everywhere_checkbox_input').is(':checked')){
                    checked = 1;
                } else {
                    checked = 0;
                }

                var datavar = {
                    action: 'b2bking_save_new_ajax_rule',
                    security: b2bkingmarketking_display_settings.security,
                    ruletitle: $('#b2bking_new_rule_title').val(),
                    userid: $('#b2bking_new_rule_user_id').val(),
                    newedit: $('.b2bking_marketking_save_new_rule').val(),

                    b2bking_rule_select_what: $('#b2bking_rule_select_what').val(),
                    b2bking_rule_select_applies: $('#b2bking_rule_select_applies').val(),
                    b2bking_rule_select_who: $('#b2bking_rule_select_who').val(),
                    b2bking_rule_select_howmuch: $('#b2bking_rule_select_howmuch').val(),
                    b2bking_rule_select_quantity_value: $('#b2bking_rule_select_quantity_value').val(),
                    b2bking_rule_select_conditions: $('#b2bking_rule_select_conditions').val(),
                    b2bking_dynamic_rule_discount_show_everywhere_checkbox_input: checked,
                    b2bking_select_multiple_users_selector_select: $('#b2bking_select_multiple_users_selector_select').val(),
                    b2bking_select_multiple_product_categories_selector_select: $('#b2bking_select_multiple_product_categories_selector_select').val(), 
                    b2bking_rule_select_applies_replaced: $('#b2bking_rule_select_applies_replaced').val(),

                };

                $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
                   location.reload();
                });
            }
        });

        // Delete rule 
        $('body').on('click', '.b2bking_rule_delete_table', function(){
            let rule = $(this).val();
            if (confirm(b2bkingmarketking_display_settings.are_you_sure_delete_rule)){
                var datavar = {
                    action: 'b2bking_delete_ajax_rule',
                    security: b2bkingmarketking_display_settings.security,
                    ruleid: rule,
                    userid: $('#b2bking_new_rule_user_id').val()
                };

                $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
                   location.reload();
                });
            }
        });

        function clearRuleValues(){
            // clear all values
            $('#b2bking_new_rule_title').val('');
            $('#b2bking_rule_select_applies').prop('selectedIndex', 0);
            $('#b2bking_rule_select_who').prop('selectedIndex', 0);
            $('#b2bking_rule_select_howmuch').val('');
            $('#b2bking_rule_select_quantity_value').prop('selectedIndex', 0);
            $('#b2bking_select_multiple_users_selector_select').val('').trigger('change');
            $('#b2bking_select_multiple_product_categories_selector_select').val('').trigger('change');
            $('#b2bking_rule_select_applies_replaced').val('');
            $('.b2bking_dynamic_rule_condition_number').val('');

            // clear conditions
            $('.b2bking_dynamic_rule_condition_delete_button').each(function(){
               $(this).click();
            });
        }

        // on opening new rule, clear values
        $('.b2bking_marketking_new_rule').on('click', clearRuleValues);

        // Edit Rule
        $('body').on('click', '.b2bking_rule_edit_table', function (){
            var rule_id = $(this).val();
            setTimeout(function(){
                // set button for save rule
                $('.b2bking_marketking_save_new_rule').val(rule_id);
                clearRuleValues();
            }, 100);

            // get values via AJAX and load into edit
            // first run ajax call based on the rule id
            
            var datavar = {
                action: 'b2bking_get_rule_data',
                security: b2bkingmarketking_display_settings.security,
                ruleid: rule_id,
                userid: $('#b2bking_new_rule_user_id').val()
            };

            $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
                setTimeout(function(){
                   var results = response;
                   var resultsArray = results.split('*');
                   // load values into fields
                   $('#b2bking_new_rule_title').val(resultsArray[9]);
                   $('#b2bking_rule_select_what').val(resultsArray[0]).trigger('change');
                   $('#b2bking_rule_select_applies').val(resultsArray[1]).trigger('change');
                   $('#b2bking_rule_select_who').val(resultsArray[2]).trigger('change');
                   $('#b2bking_rule_select_howmuch').val(resultsArray[4]);
                   $('#b2bking_rule_select_quantity_value').val(resultsArray[3]).trigger('change');
                   $('#b2bking_rule_select_conditions').val(resultsArray[5]);

                   $('.b2bking_rule_condition_container').attr('id','b2bking_condition_number_1');
                   $('.b2bking_rule_condition_container select.b2bking_dynamic_rule_condition_name').attr('class','b2bking_dynamic_rule_condition_name b2bking_condition_identifier_1');
                   $('.b2bking_rule_condition_container select.b2bking_dynamic_rule_condition_operator').attr('class','b2bking_dynamic_rule_condition_operator b2bking_condition_identifier_1');
                   $('.b2bking_rule_condition_container input.b2bking_dynamic_rule_condition_number').attr('class','b2bking_dynamic_rule_condition_number b2bking_condition_identifier_1');
                   $('.b2bking_rule_condition_container button.b2bking_dynamic_rule_condition_add_button ').attr('class','b2bking_dynamic_rule_condition_add_button b2bking_condition_identifier_1');

                   var discountCheckbox = resultsArray[6];
                   if (parseInt(discountCheckbox) === 1){
                        $('#b2bking_dynamic_rule_discount_show_everywhere_checkbox_input').prop('checked', true);
                   } else {
                        $('#b2bking_dynamic_rule_discount_show_everywhere_checkbox_input').prop('checked', false);
                   }
                   var usersArray = resultsArray[8].split(',');
                   var productsArray = resultsArray[7].split(',');
                   $('#b2bking_select_multiple_users_selector_select').val(usersArray).trigger('change');
                   $('#b2bking_select_multiple_product_categories_selector_select').val(productsArray).trigger('change');

                   setTimeout(function(){
                    setUpConditionsFromHidden();
                    }, 100);
                }, 200);
            });
        });

        // on click Select all
        $('#b2bking_select_all').on('click', function(){
            $('#b2bking_select_multiple_product_categories_selector_select').select2('destroy').find('#b2bking_products_optgroup option').prop('selected', 'selected').end().select2();
        });
        $('#b2bking_unselect_all').on('click', function(){
            jQuery("#b2bking_select_multiple_product_categories_selector_select").val(null).trigger("change");
        });

        /* Registration */
        // if user selects "I am a vendor", hide b2bking user type dropdown
        var detached = '';
        $('input[type=radio][name=role]').change(function() {
            if (this.value === 'customer') {
                detached.insertAfter('.user-role');
            } else if (this.value === 'seller') {
                detached = $('.b2bking_registration_roles_dropdown_section, .b2bking_custom_registration_container').detach();
            }
        });

        /* Messages */
        // when page is opened, get conversation id in url and open that conversation
        var openUrl = window.location.href;
        var checkurl = new URL(openUrl);
        var conversationID = checkurl.searchParams.get("conversation");
        if (conversationID !== null){

            if (jQuery('#marketking_dashboard_refunds_tablse').val() === 'undefined') { 
                mainTable.search( conversationID ).draw();
                setTimeout(function(){
                    $('button[value='+conversationID+']').click();
                }, 100);
            } 

        }

        $('body').on('click', '.b2bking_conversation_table', function (){
            var conversation_id = $(this).val();
            var conversation_title = $(this).parent().parent().parent().children().eq(1).text();
            var conversation_type = $(this).parent().parent().parent().children().eq(2).text();
            var conversation_username = $(this).parent().parent().parent().children().eq(3).text();
            var conversation_reply = $(this).parent().parent().parent().children().eq(4).text();
            $('#b2bking_conversation_message_submit_vendor').val(conversation_id);

            var datavar = {
                action: 'b2bking_get_conversation_data',
                security: b2bkingmarketking_display_settings.security,
                conversationid: conversation_id,
            };

            $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){

                $('#b2bking_conversation_messages_container_container').empty();
                $(response).appendTo('#b2bking_conversation_messages_container_container');
                $('#b2bking_myaccount_conversation_endpoint_title').text(conversation_title);
                $('.b2bking_myaccount_conversation_endpoint_typed').text(conversation_type);
                $('.b2bking_myaccount_conversation_endpoint_usernamed').text(conversation_username);
                $('.b2bking_myaccount_conversation_endpoint_replyd').text(conversation_reply);


            });
        });

        $('body').on('click', '#b2bking_conversation_make_offer_vendor', function (){
            var conversation_id = jQuery('#b2bking_conversation_make_offer_vendor').val();
            window.location = b2bkingmarketking_display_settings.offers_link+'?quote='+conversation_id;
        });
        

        if (parseInt(b2bkingmarketking_display_settings.b2bking_exists) === 1){
            // use b2bking function
            $('body').on('click', '#b2bking_conversation_message_submit_vendor', function (){
                var conversation_id = jQuery('#b2bking_conversation_message_submit_vendor').val();

                var datavar = {
                    action: 'b2bkingconversationmessage',
                    security: b2bkingmarketking_display_settings.security,
                    message: $('#b2bking_conversation_user_new_message').val(),
                    conversationid: conversation_id,
                };

                $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
                    // set url with vendor as parameter
                    var currentUrl = window.location.href;
                    var url = new URL(currentUrl);
                    url.searchParams.set("conversation", conversation_id); // setting your param
                    var newUrl = url.href; 
                    window.location = newUrl;
                });
            });
        } else {
            // use proprietary function
            $('body').on('click', '#b2bking_conversation_message_submit_vendor', function (){
                var conversation_id = jQuery('#b2bking_conversation_message_submit_vendor').val();

                var datavar = {
                    action: 'b2bkingconversationmessagerefunds',
                    security: b2bkingmarketking_display_settings.security,
                    message: $('#b2bking_conversation_user_new_message').val(),
                    conversationid: conversation_id,
                };

                $.post(b2bkingmarketking_display_settings.ajaxurl, datavar, function(response){
                    // set url with vendor as parameter
                    var currentUrl = window.location.href;
                    var url = new URL(currentUrl);
                    url.searchParams.set("conversation", conversation_id); // setting your param
                    var newUrl = url.href; 
                    window.location = newUrl;
                });
            });
        }
        

        // if vendor is set in URL, open new message
        if (b2bkingmarketking_display_settings.vendorinurl !== ''){
            $('#b2bking_myaccount_make_inquiry_button').click();
        }
        
	});

})(jQuery);