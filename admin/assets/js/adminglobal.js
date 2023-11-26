/**
*
* JavaScript file that has global action in the admin menu
*
*/
(function($){

	"use strict";

	$( document ).ready(function() {

		var page_slug = marketking.pageslug;
		var old_page_slug = 'none';

		// Go back: groups etc.
		setTimeout(function(){
			$("body.post-type-marketking_group .wrap a.page-title-action").after('&nbsp;<a href="'+marketking.groups_link+'" class="page-title-action marketking_go_groups">'+marketking.go_back_text+'</a>');
			$("body.post-type-marketking_grule .wrap a.page-title-action").after('&nbsp;<a href="'+marketking.groups_link+'" class="page-title-action marketking_go_groups">'+marketking.go_back_text+'</a>');
		}, 150);
		setTimeout(function(){
			$("body.post-type-marketking_field .wrap a.page-title-action").after('&nbsp;<a href="'+marketking.registrationpage+'" class="page-title-action marketking_go_registration">'+marketking.go_back_text+'</a>');
			$("body.post-type-marketking_option .wrap a.page-title-action").after('&nbsp;<a href="'+marketking.registrationpage+'" class="page-title-action marketking_go_registration">'+marketking.go_back_text+'</a>');
		}, 150);
		// Go back: vitems etc.
		setTimeout(function(){
			//vreq -> vitems
			$("body.post-type-marketking_vreq .wrap .wp-heading-inline").after('&nbsp;<a href="'+marketking.vitems_link+'" class="page-title-action marketking_go_vitem">'+marketking.go_vitem+'</a>');
		}, 150);

		// In admin emails, modify email path for theme folder.
		if (($('#woocommerce_marketking_new_vendor_email_enabled').val() !== undefined)||($('#woocommerce_marketking_your_account_approved_email_enabled').val() !== undefined)||($('#woocommerce_marketking_new_vendor_requires_approval_email_enabled').val() !== undefined)||($('#woocommerce_marketking_new_message_email_enabled').val() !== undefined)){
			var text = $('.template_html').html();
			var newtext = text.replace("/woocommerce/", "/");
			$('.template_html').html(newtext);
			$('.template_html p a:nth-child(2)').remove();
		}

		$('.marketking-upgrade-to-premium').attr('target','_blank');


		$('#marketking_other_product_sellers').select2();

		$('#marketking_group_allowed_products_type').select2();

		$('#marketking_group_visible_groups_settings').select2();
		$('#marketking_group_visible_vendors_settings').select2();
		

		$('#marketking_group_allowed_categories').select2();
		$('#marketking_group_allowed_tags').select2();
		$('#marketking_group_allowed_tabs').select2();

		$('#marketking_select_storecategories').select2();

		

		var availablepages = ['payouts', 'premium', 'registration', 'modules', 'dashboard', 'vendors','reports','groups'];
		var availablepagesmarketking = ['marketking_payouts', 'marketking_premium', 'marketking_registration', 'marketking_modules', 'marketking_dashboard', 'marketking_vendors', 'marketking_reports', 'marketking_groups'];
		var availableposts = ['marketking_vitem','marketking_option','marketking_field','marketking_group','marketking_grule','marketking_mpack', 'marketking_vreq', 'marketking_badge', 'marketking_refund','marketking_rule', 'marketking_docs','marketking_abuse','marketking_message','marketking_announce'];


		$('#toplevel_page_marketking a').on('click', function(e){
			// check list of pages with ajax switch. If page is in list, prevent default and load via ajax
			// make sure current page is a marketking page but not settings

			if (marketking.ajax_pages_load === 'enabled'){
				let location = $(this).prop('href');
				let page = location.split('page=marketking_');
				let switchto = page[1];
			
				if (availablepages.includes(switchto) && (page_slug.startsWith('marketking') || marketking.current_post_type.startsWith('marketking') )){
					// prevent link click
					e.preventDefault();
					page_switch(switchto);

					// change link classes
					$('#adminmenu #toplevel_page_marketking').find('.current').each(function(i){
						$(this).removeClass('current');
					});
					$(this).addClass('current');
					$(this).parent().addClass('current');
					$(this).blur();
				}

				// edit post type
				page = location.split('post_type=');
				switchto = page[1];

				if (availableposts.includes(switchto) && (page_slug.startsWith('marketking') || marketking.current_post_type.startsWith('marketking') ) ){
					// prevent link click
					e.preventDefault();
					page_switch('edit_'+switchto);

					// change link classes
					$('#adminmenu #toplevel_page_marketking').find('.current').each(function(i){
						$(this).removeClass('current');
					});
					$(this).addClass('current');
					$(this).parent().addClass('current');
					$(this).blur();
				}
			}
		});

		function check_vitems(){
			if (jQuery('#marketking_backend_page').val() === 'marketking_vreq'){
				if (jQuery('.marketking_go_vitem').val() === undefined){
					$("body.post-type-marketking_vreq .wrap .wp-heading-inline").after('&nbsp;<a href="'+marketking.vitems_link+'" class="page-title-action marketking_go_vitem">'+marketking.go_vitem+'</a>');
					$("body.marketking_page_marketking_vreq .wrap .wp-heading-inline").after('&nbsp;<a href="'+marketking.vitems_link+'" class="page-title-action marketking_go_vitem">'+marketking.go_vitem+'</a>');
				} else {
					setTimeout(function(){
						check_vitems();
					}, 800);
				}
				
			}
		}

		$('body').on('click','#marketking_download_vendor_balance_history', function(){
			let user_id = $('#marketking_download_vendor_balance_history').val();
			window.location = ajaxurl + '?action=marketking_download_vendor_balance_history&userid='+user_id+'&security=' + marketking.security;
			console.log(ajaxurl + '?action=marketking_download_vendor_balance_history&userid='+user_id+'&security=' + marketking.security);
		});

		$('body').on('click','#marketking_download_vendor_credit_history', function(){
			let user_id = $('#marketking_download_vendor_credit_history').val();
			window.location = ajaxurl + '?action=marketking_download_vendor_credit_history&userid='+user_id+'&security=' + marketking.security;
			console.log(ajaxurl + '?action=marketking_download_vendor_credit_history&userid='+user_id+'&security=' + marketking.security);
		});

		

		// view payouts button
		$('body').on('click', '.marketking_manage_payouts_button', function(e) {
			// make sure current page is a marketking page but not settings
			if (marketking.ajax_pages_load === 'enabled'){
				let user_id_payouts = $(this).val();
				let switchto = 'view_payouts';
				// prevent link click
				e.preventDefault();
				page_switch(switchto, user_id_payouts);

				// change link classes to payouts
				$('#adminmenu #toplevel_page_marketking').find('.current').each(function(i){
					$(this).removeClass('current');
				});
				jQuery('#toplevel_page_marketking a[href$="payouts"]').addClass('current');
				jQuery('#toplevel_page_marketking a[href$="payouts"]').parent().addClass('current');
			}
		});

		// go back to previous button
		$('body').on('click', '#marketking_go_back_page', function(e) {
				// make sure current page is a marketking page but not settings
			if (marketking.ajax_pages_load === 'enabled'){

				if (old_page_slug !== 'none'){
					// // prevent link click
					e.preventDefault();

					// get page
					let page = old_page_slug.split('marketking_')[1];
					
					page_switch(page);

					// change link classes to page
					$('#adminmenu #toplevel_page_marketking').find('.current').each(function(i){
						$(this).removeClass('current');
					});
					jQuery('#toplevel_page_marketking a[href$="'+page+'"]').addClass('current');
					jQuery('#toplevel_page_marketking a[href$="'+page+'"]').parent().addClass('current');
				}				
			}
		});

		$('body').on('click','.marketking_go_groups', function(e){
			if (marketking.ajax_pages_load === 'enabled'){
				e.preventDefault();
				page_switch('groups');
			}
		});

		$('body').on('click','.marketking_go_registration', function(e){
			if (marketking.ajax_pages_load === 'enabled'){
				e.preventDefault();
				page_switch('registration');
			}
		});
		$('body').on('click','.marketking_go_vitem', function(e){
			if (marketking.ajax_pages_load === 'enabled'){
				e.preventDefault();
				page_switch('edit_marketking_vitem');
			}
		});	

		$('body').on('click','.card.card-hover.bg-info', function(e){
			if (marketking.ajax_pages_load === 'enabled'){
				e.preventDefault();
				page_switch('edit_marketking_message');
			}
		});	


		// groups -> go to vendor groups
		$('body').on('click','.marketking_groups_left_box, .marketking_go_edit_groups', function(e){
			if (marketking.ajax_pages_load === 'enabled'){
				e.preventDefault();
				page_switch('edit_marketking_group');
				var linkstext2 = '<a href="'+marketking.groupspage+'" class="page-title-action marketking_go_groups">'+marketking.go_back_text+'</a>';

				setTimeout(function(){
					$(".page-title-action").after(linkstext2);
				}, 650);
			}
		});

		// groups -> go to vendor group rules
		$('body').on('click','.marketking_groups_right_box', function(e){
			if (marketking.ajax_pages_load === 'enabled'){
				e.preventDefault();
				page_switch('edit_marketking_grule');
				var linkstext2 = '<a href="'+marketking.grulespage+'" class="page-title-action marketking_go_groups">'+marketking.go_back_text+'</a>';

				setTimeout(function(){
					$(".page-title-action").after(linkstext2);
				}, 650);
			}
		});

		// registration -> go to registration fields
		$('body').on('click','.marketking_registration_left_box', function(e){
			if (marketking.ajax_pages_load === 'enabled'){
				e.preventDefault();
				page_switch('edit_marketking_field');
				var linkstext2 = '<a href="'+marketking.registrationpage+'" class="page-title-action marketking_go_registration">'+marketking.go_back_text+'</a>';

				setTimeout(function(){
					$(".page-title-action").after(linkstext2);
				}, 650);
			}
		});
		
		// registration -> go to registration options
		$('body').on('click','.marketking_registration_right_box', function(e){
			if (marketking.ajax_pages_load === 'enabled'){
				e.preventDefault();
				page_switch('edit_marketking_option');
				var linkstext2 = '<a href="'+marketking.registrationpage+'" class="page-title-action marketking_go_registration">'+marketking.go_back_text+'</a>';

				setTimeout(function(){
					$(".page-title-action").after(linkstext2);
				}, 650);
			}
		});
		

		// activate plugin
		$('#marketking-activate-license').on('click', function(){
			var datavar = {
	            action: 'marketkingactivatelicense',
	            email: $('input[name="marketking_license_email_setting"]').val().trim(),
	            key: $('input[name="marketking_license_key_setting"]').val().trim(),
	            security: marketking.security,
	        };
	        
	        $('#marketking-activate-license').notify(marketking.sending_request,{  position: "right",  className: 'info'});

			$.post(ajaxurl, datavar, function(response){
				if (response === 'success'){
					$('#marketking-admin-submit').click();
				} else {
					$('#marketking-activate-license').notify(response,{  position: "right",  className: 'error'});
				}
			});
		});

		function ajax_page_reload(){
			// 1. Replace current page content with loader
			// add overlay and loader
			jQuery('#wpbody-content').prepend('<div id="marketking_admin_overlay"><img class="marketking_loader_icon_button" src="'+marketking.loaderurl+'">');

			// if pageslug contains user, remove it
			let slugsplit = window.location.href.split('&user=');
			let switchto = slugsplit[0].split('marketking_')[1];
			let userid = 0;
			if (slugsplit[1] !== undefined){
				userid = slugsplit[1];
			}

			// 2. Get page content
			var datavar = {
	            action: 'marketking_get_page_content',
	            security: marketking.security,
	            page: switchto,
	            userid: userid
	        };

			jQuery.post(ajaxurl, datavar, function(response){

				// response is the HTML content of the page
				jQuery('#wpbody-content').html(response);

				// initialize JS
				initialize_elements();

				initialize_on_marketking_page_load();

			});
		}

		var modalcontent = $('#marketking_pro_upgrade_modal_container').detach().html();
		
		$('.marketking_modal_init').on('click', function(){
			upgrade_open_modal();
			$(this).remove();
		});

		$('.marketking_modal_init').click();

		function upgrade_open_modal(){
			// add overlay and loader
			if (modalcontent === undefined){
				modalcontent = $('#marketking_pro_upgrade_modal_container').detach().html();
			}
			jQuery('#wpbody-content').prepend('<div id="marketking_admin_overlay" class="marketking_admin_overlay_removable">'+modalcontent+'</div<');
		}

		$('body').on('click', '.marketking_admin_overlay_removable', function(){
			$(this).remove();
		});
		$('body').on('click', '#marketking_pro_upgrade_modal', function(e){
			 e.stopPropagation();
		});
		$('body').on('click', '.marketkingproswitch', function(e){
			upgrade_open_modal();
			e.preventDefault();
		});

		function preload(arrayOfImages) {
		    $(arrayOfImages).each(function(){
		        $('<img/>')[0].src = this;
		    });
		}

		function page_switch(switchto, userid = 0){

			// 1. Replace current page content with loader
			// add overlay and loader
			jQuery('#wpbody-content').prepend('<div id="marketking_admin_overlay"><img class="marketking_loader_icon_button" src="'+marketking.loaderurl+'">');

			// 2. Get page content
			var datavar = {
	            action: 'marketking_get_page_content',
	            security: marketking.security,
	            page: switchto,
	            userid: userid
	        };

			jQuery.post(ajaxurl, datavar, function(response){

				// the current one becomes the old one
				old_page_slug = page_slug;

				// response is the HTML content of the page
				// if page is dashboard, drop preloader first
				let preloaderhtml = '<div class="marketkingpreloader"><img class="marketking_loader_icon_button" src="'+marketking.loaderurl+'"></div>';
				if (switchto === 'dashboard' || switchto === 'reports'){
					jQuery('#wpbody-content').html(preloaderhtml);
					setTimeout(function(){
						jQuery('.marketkingpreloader').after(response);

					}, 10);

				} else {

					if (switchto === 'modules'){
						// need to preload modules image
						preload(marketking.modulesimg);
						setTimeout(function(){
							jQuery('#wpbody-content').html(response);
						}, 500);

					} else {
						jQuery('#wpbody-content').html(response);
					}


				}

				// if pageslug contains user, remove it
				let slugtemp = page_slug.split('&user=')[0];

				// remove current page slug and set new page slug
				jQuery('body').removeClass('admin_page_'+slugtemp);
				jQuery('body').removeClass('marketking_page_'+slugtemp);
				jQuery('body').removeClass('post-type-'+slugtemp);
				jQuery('body').removeClass('toplevel_page_marketking');

				jQuery('#marketking_admin_style-css').prop('disabled', true);
				jQuery('#marketking_style-css').prop('disabled', true);
				jQuery('#semantic-css').prop('disabled', true);


				// remove post php because page switch can never switch to a single post yet
				jQuery('body').removeClass('post-php');

				let new_page_slug = 'marketking_'+switchto;

				jQuery('body').addClass('post-type-'+new_page_slug);
				jQuery('body').addClass('marketking_page_not_initial');
				jQuery('body').removeClass('marketking_page_initial');


				// if post type, remove 'marketking_edit'
				if (new_page_slug.startsWith('marketking_edit')){
					new_page_slug = new_page_slug.split('marketking_edit_')[1];	
				}


				if (userid!== 0){
					new_page_slug = new_page_slug+'&user='+userid;
				}

				// link difference between pages and posts
				let newlocation = window.location.href.replace('='+page_slug,'='+new_page_slug);

				// removed paged
				newlocation = newlocation.split('&paged=')[0];
				newlocation = newlocation.split('&action=edit')[0];

				if (newlocation.includes('admin.php?page=') && availableposts.includes(new_page_slug)){
					newlocation = newlocation.replace('admin.php?page=','edit.php?post_type=');
				}

				if (newlocation.includes('edit.php?post_type=') && ( availablepages.includes(new_page_slug) || availablepagesmarketking.includes(new_page_slug)) ){
					newlocation = newlocation.replace('edit.php?post_type=','admin.php?page=');
				}

				if (newlocation.includes('post.php?post=') && ( availablepages.includes(new_page_slug) || availablepagesmarketking.includes(new_page_slug)) ){
					newlocation = newlocation.replace('post.php?post=','admin.php?page=');
				}

				if (newlocation.includes('post.php?post=') && availableposts.includes(new_page_slug)){
					newlocation = newlocation.replace('post.php?post=','edit.php?post_type=');
				}

				// set page url
				window.history.pushState('marketking_'+switchto, '', newlocation);

				page_slug = new_page_slug;

				// if pageslug contains user, remove it
				slugtemp = page_slug.split('&user=')[0];
				jQuery('body').addClass('marketking_page_'+slugtemp);

				// initialize JS
				initialize_elements();

				// initialize upgrade modal
				modalcontent = $('#marketking_pro_upgrade_modal_container').detach().html();

				$('.marketking_modal_init').on('click', function(){
					upgrade_open_modal();
					$(this).remove();
				});

				$('.marketking_modal_init').click();

				// expand menu if not already open (expanded)
				$('.toplevel_page_marketking').removeClass('wp-not-current-submenu');
				$('.toplevel_page_marketking').addClass('wp-has-current-submenu wp-menu-open');

				initialize_on_marketking_page_load();

				// remove browser 'Leave Page?' warning
				jQuery(window).off('beforeunload');

			});

		}
		jQuery('body').addClass('marketking_page_initial');


		function initialize_on_marketking_page_load(){
			// run default WP ADMIN JS FILES
			$.ajax({ url: marketking.inlineeditpostjsurl, dataType: "script", });
			$.ajax({ url: marketking.commonjsurl, dataType: "script", });


			// special cases
			check_vitems();
		}

		initialize_elements();

		function initialize_elements(){
			/* Payouts */
			if (typeof $('#marketking_admin_payouts_table').DataTable === "function") { 
				$('#marketking_admin_payouts_table').DataTable({
					"language": {
					    "url": marketking.datatables_folder+marketking.tables_language_option+'.json'
					},
					retrieve: true,
				});
			}
			if (typeof $('#marketking_payout_history_table').DataTable === "function") { 
				$('#marketking_payout_history_table').DataTable({
			        order: [[ 0, "desc" ]],
			        "language": {
			            "url": marketking.datatables_folder+marketking.tables_language_option+'.json'
			        },
			        retrieve: true,

			    });
			}

			/* Vendors */
			if (typeof $('#marketking_admin_vendors_table').DataTable === "function") { 
				$('#marketking_admin_vendors_table').DataTable({
					"language": {
					    "url": marketking.datatables_folder+marketking.tables_language_option+'.json'
					},
					retrieve: true,

				});
			}

			// Move header to top of page
			jQuery('#wpbody-content').prepend(jQuery('#marketing_admin_header_bar').detach());

			// Dashboard
			if ($(".marketkingpreloader").val()!== undefined){
				if (jQuery('#marketking_admin_dashboard-css').val() === undefined){
					// add it to page
					jQuery('#marketking_chartist-css').after('<link rel="stylesheet" id="marketking_admin_dashboard-css" href="'+marketking.dashboardstyleurl+'" media="all">');
				}
				jQuery('#marketking_admin_dashboard-css').prop('disabled', false);

				setTimeout(function(){
					// hide preloader and show page
					$(".marketkingpreloader").fadeOut();
					$(".marketking_dashboard_page_wrapper").show();
					// draw chart
					drawSalesChart();

					$('#marketking_dashboard_days_select').change(drawSalesChart);

					// load first chart in reports
					setTimeout(function(){
						$('#marketking_reports_link_thismonth').click();
					}, 150);

					//failsafe in case the page did not show, try again in 50 ms
					setTimeout(function(){
						dashboard_failsafe();
					}, 60);	
					setTimeout(function(){
						dashboard_failsafe();
					}, 110);
					setTimeout(function(){
						dashboard_failsafe();
					}, 150);		
					
				}, 35);
				
				

			} else {
				jQuery('#marketking_admin_dashboard-css').prop('disabled', true);
			}
		}

		function dashboard_failsafe(){
			if ($(".marketking_dashboard_page_wrapper").css('display') !== 'block'){
				setTimeout(function(){
					$(".marketking_dashboard_page_wrapper").show();
					drawSalesChart();
				}, 50);	
			}
		}

		/* Reports */

		function reports_set_chart(){

			if ($(".marketking_dashboard_page_wrapper").val()!== undefined && $(".marketking_reports_page_wrapper").val()!== undefined ){

				let vendor = jQuery('#marketking_dashboard_days_select').val();
				let firstday = jQuery('.marketking_reports_date_input_from').val();
				let lastday = jQuery('.marketking_reports_date_input_to').val();

				// if dates are set
				if (firstday !== '' && lastday !== ''){

					// get data
					var datavar = {
			            action: 'marketking_reports_get_data',
			            security: marketking.security,
			            vendor: vendor,
			            firstday: firstday,
			            lastday: lastday,
			        };

					$.post(ajaxurl, datavar, function(response){

						let data = response.split('*');
						let sales_total = data[0];
						let sales_total_wc = data[1];
						let order_number = data[2];
						let new_vendors = data[3];
						let commission = data[4];
						let commission_wc = data[5];
						let labels = JSON.parse(data[6]);
						let salestotal = JSON.parse(data[7]);
						let ordernumbers = JSON.parse(data[8]);
						let commissiontotal = JSON.parse(data[9]);

						$('.marketking_reports_page_wrapper .marketking_total_b2b_sales_today').html(sales_total_wc);
						$('.marketking_number_orders_today').html(order_number);
						$('.marketking_number_customers_today').html(new_vendors);
						$('.marketking_net_earnings_today').html(commission_wc);

						drawReportsSalesChart(labels,salestotal, ordernumbers, commissiontotal);

					});
				}
			}


		}

		$('body').on('click', '.marketking_reports_link' ,function(){
			let quicklink = jQuery(this).prop('hreflang');

			if (quicklink === 'thismonth'){
				var date = new Date(), y = date.getFullYear(), m = date.getMonth();
				var firstDay = new Date(y, m, 1);
				var lastDay = new Date(y, m + 1, 0);

			}

			if (quicklink === 'lastmonth'){
				var date = new Date(), y = date.getFullYear(), m = date.getMonth()-1;

				var firstDay = new Date(y, m, 1);
				var lastDay = new Date(y, m + 1, 0);
			}

			if (quicklink === 'thisyear'){
				var date = new Date(), y = date.getFullYear();
				var firstDay = new Date(y, 0, 1);
				var lastDay = new Date(y, 11, 31);

			}
			if (quicklink === 'lastyear'){
				var date = new Date(), y = date.getFullYear()-1;
				var firstDay = new Date(y, 0, 1);
				var lastDay = new Date(y, 11, 31);

			}


			var day = firstDay.getDate();
				if (day<10) { day="0"+day;}

			var month = firstDay.getMonth()+1;
			if (month<10) { month="0"+month;}

			jQuery('.marketking_reports_date_input_from').val(firstDay.getFullYear()+'-'+month+'-'+day);

			var day = lastDay.getDate();
				if (day<10) { day="0"+day;}

			var month = lastDay.getMonth()+1;
			if (month<10) { month="0"+month;}

			jQuery('.marketking_reports_date_input_to').val(lastDay.getFullYear()+'-'+month+'-'+day);

			reports_set_chart();
		
		});

		$('body').on('change', '.marketking_reports_date_input', function(){
			reports_set_chart();
		});
		$('body').on('change', '#marketking_dashboard_days_select', function(){
			reports_set_chart();
		});
		

		/* User Profile Vendor */
		// Customer or vendor group show hide
		vendorcustomershowhide();
		$('input[type=radio][name="marketking_user_choice"]').change(function () {   
		    vendorcustomershowhide();
		});
		function vendorcustomershowhide(){
			let selectedValue = $('input[type=radio][name="marketking_user_choice"]:checked').val();
			if(selectedValue === "customer") {
		      	$("#marketking_user_profile_customer_vendor").css("display","none");
		   	} else if (selectedValue === "vendor"){
				$("#marketking_user_profile_customer_vendor").css("display","block");
			}
		}

	
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

	   	$('#marketking_clear_image_profile').on('click', function(e) {
	   		$('#marketking_profile_logo_image').val('');
	   		$('.marketking-upload-image img').attr('src', marketking.profile_pic);

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

	   	$('body').on('click', '.marketking_mark_button_completed' ,function(){
			var datavar = {
	            action: 'marketking_mark_refund_completed',
	            security: marketking.security,
	            refundvalue: $(this).val(),
	        };

			$.post(ajaxurl, datavar, function(response){
				location.reload();
			});
		});

	   	$('body').on('click', '.marketking_mark_button_pending' ,function(){
			var datavar = {
	            action: 'marketking_mark_refund_pending',
	            security: marketking.security,
	            refundvalue: $(this).val(),
	        };

			$.post(ajaxurl, datavar, function(response){
				location.reload();
			});
		});

	   	$('body').on('click', '.marketking_mark_button_verification_approve' ,function(){
			var datavar = {
	            action: 'marketking_mark_verification_approved',
	            security: marketking.security,
	            vreqid: $(this).val(),
	        };

			$.post(ajaxurl, datavar, function(response){
				location.reload();
			});
		});

	   	$('body').on('click', '.marketking_mark_button_verification_reject' ,function(){

			// get rejection reason
			var reason = prompt(marketking.rejection_reason_text);

			if (reason !== null){
				var datavar = {
		            action: 'marketking_mark_verification_rejected',
		            security: marketking.security,
		            vreqid: $(this).val(),
		            reason: reason
		        };

				$.post(ajaxurl, datavar, function(response){
					location.reload();
				});
			}
			
		});

		

	   	// On clicking "Save Payment"
	   	$('body').on('click', '#marketking_save_payment', function(e) {
	    	var amount = $('#marketking_reimbursement_value').val();
	    	var method = $('#marketking_reimbursement_method').val();
	    	var note = $('#marketking_reimbursement_note').val();
	    	var uid = $('input[name=marketking_admin_user_id]').val();

	    	if (confirm(marketking.sure_save_payment)){
				var datavar = {
		            action: 'marketkingsavepayment',
		            security: marketking.security,
		            pamount: amount,
		            pmethod: method,
		            pnote: note,
		            userid: uid,
		            bonus: $('#marketking_bonus_payment').is(":checked"),
		        };

				$.post(ajaxurl, datavar, function(response){
					ajax_page_reload();
				});
	    	}
	    });

	    // On clicking "Save Adjustment"
	   	$('body').on('click', '#marketking_make_vendor_balance_adjustment', function(e) {
	    	var amount = $('#marketking_adjustment_value').val();
	    	var note = $('#marketking_adjustment_note').val();
	    	var uid = $('input[name=marketking_admin_user_id]').val();

	    	if (confirm(marketking.sure_save_adjustment)){
				var datavar = {
		            action: 'marketkingsaveadjustment',
		            security: marketking.security,
		            pamount: amount,
		            pnote: note,
		            userid: uid,
		        };

				$.post(ajaxurl, datavar, function(response){
					ajax_page_reload();
				});
	    	}
	    });

	    // Show Hide payout methods
	    payoutshowhide();

	    $('input[name=marketking_enable_custom_payouts_setting]').change(function() {
	    	payoutshowhide();
	    });

	    function payoutshowhide(){
	    	let selectedValue = $('input[name=marketking_enable_custom_payouts_setting]').is(":checked");
	    	if(selectedValue === true) {
	          	$("#marketking_custom_method_container").css("display","block");
	       	} else {
	    		$("#marketking_custom_method_container").css("display","none");
	    	}
	    }


	    // Show Hide Color Scheme Settings
	    colorschemeshowhide();

	    $('input[name=marketking_change_color_scheme_setting]').change(function() {
	    	colorschemeshowhide();
	    });

	    function colorschemeshowhide(){
	    	let selectedValue = $('input[name=marketking_change_color_scheme_setting]').is(":checked");
	    	if(selectedValue === true) {
	          	$(".marketking_change_color_scheme_container").css("display","block");
	       	} else {
	    		$(".marketking_change_color_scheme_container").css("display","none");
	    	}
	    }

	    // Backend registration functions
	    /* USER REGISTRATION DATA - APPROVE REJECT */
	    $('.marketking_user_registration_user_data_container_element_approval_button_approve').on('click', function(){
	    	if (confirm(marketking.are_you_sure_approve)){
	    		var datavar = {
	                action: 'marketkingapproveuser',
	                security: marketking.security,
	                chosen_group: $('.marketking_user_registration_user_data_container_element_select_group').val(),
	                credit: $('#marketking_approval_credit_user').val(),
	                salesagent: $('#salesking_assign_sales_agent').val(),
	                user: $('#marketking_user_registration_data_id').val(),
	            };

	    		$.post(ajaxurl, datavar, function(response){
	    			location.reload();
	    		});
	    	}
	    });

	    $('.marketking_user_registration_user_data_container_element_approval_button_reject').on('click', function(){
	    	if (confirm(marketking.are_you_sure_reject)){
	    		var datavar = {
	                action: 'marketkingrejectuser',
	                security: marketking.security,
	                user: $('#marketking_user_registration_data_id').val(),
	            };

	    		$.post(ajaxurl, datavar, function(response){
	    			window.location = marketking.admin_url+'/users.php';
	    		});
	    	}
	    });

	    $('.marketking_user_registration_user_data_container_element_approval_button_deactivate').on('click', function(){
	    	if (confirm(marketking.are_you_sure_deactivate)){
	    		var datavar = {
	                action: 'marketkingdeactivateuser',
	                security: marketking.security,
	                user: $('#marketking_user_registration_data_id').val(),
	            };

	    		$.post(ajaxurl, datavar, function(response){
	    			location.reload();
	    		});
	    	}
	    });

	    // Download registration files
	    $('.marketking_user_registration_user_data_container_element_download').on('click', function(){
	    	let attachment = $(this).val();
	    	window.location = ajaxurl + '?action=marketkinghandledownloadrequest&attachment='+attachment+'&security=' + marketking.security;
	    });

	         // On clicking update marketking user data (registration data)
        $('#marketking_update_registration_data_button').on('click', function(){
	    	if (confirm(marketking.are_you_sure_update_user)){

	    		var fields = $('#marketking_admin_user_fields_string').val();
	    		var fieldsArray = fields.split(',');

				var datavar = {
		            action: 'marketkingupdateuserdata',
		            security: marketking.security,
		            userid: $('#marketking_admin_user_id').val(),
		            field_strings: fields,
		        };

		        fieldsArray.forEach(myFunction);

		        function myFunction(item, index) {
		        	if (parseInt(item.length) !== 0){
		        		let value = $('input[name=marketking_field_'+item+']').val();
		        		if (value !== null){
		        			let key = 'field_'+item;
		        			datavar[key] = value;
		        		}
		        	}
		        }

				$.post(ajaxurl, datavar, function(response){
					if (response.startsWith('vatfailed')){
						alert(marketking.user_has_been_updated_vat_failed);
					} else {
						alert(marketking.user_has_been_updated);
					}

					location.reload();
					
				});
	    	}
        });

        var originalstoreurl = $('#marketking_store_url').val();
        // edit store url in admin
        // force store url characters and check url availability
		$(document).on('keypress', '#marketking_store_url', function(e) {

		    if (parseInt(marketking.allow_dash_store_url) === 1 ){
		    	// dash allowed
		    	if ((e.keyCode < 48) && e.keyCode !== 45) {
		    	    e.preventDefault();
		    	}
		    } else {
		    	// dash not allowed
		    	if (e.keyCode < 48) {
		    	    e.preventDefault();
		    	}
		    }

		    if ((e.keyCode > 57)&&(e.keyCode < 65)) {
		        e.preventDefault();
		    }

		    if ((e.keyCode > 90)&&(e.keyCode < 97)) {
		        e.preventDefault();
		    }	

		    // max 25 characters
		    if ($('#marketking_store_url').val().length > 25){
		    	e.preventDefault();
		    }

		});

		$(document).on('paste', '#marketking_store_url', function(e) {
		    e.preventDefault();
		});

		$(document).on('input', '#marketking_store_url', function(e) {

		    // get the text and check it's availability
		    let storeurl = $('#marketking_store_url').val();
		    var datavar = {
	            action: 'marketkingcheckurlexists',
	            security: marketking.security,
	            url: storeurl,
	        };

	        $.post(ajaxurl, datavar, function(response){
	        	$('.marketking_availability').removeClass('marketking_url_unavailable');
	        	$('.marketking_availability').removeClass('marketking_url_available');

	        	if (originalstoreurl === storeurl){
	        		response = 'no';
	        	}
	        	if ('' === storeurl){
	        		response = 'no';
	        	}

	        	if (response === 'yes'){
	        		$('.marketking_availability').html('('+marketking.url_not_available+')');
	        		$('.marketking_availability').addClass('marketking_url_unavailable');
	        		$('#submit').prop('disabled', true);
	        	} else if (response === 'no'){
	        		$('.marketking_availability').html('('+marketking.url_available+')');
	        		$('.marketking_availability').addClass('marketking_url_available');
	        		$('#submit').prop('disabled', false);
	        	}
	        	
	        });

		});

		// if url unavailable prevent submission
		$('#your-profile').on('submit', function(e){
			if ($('.marketking_availability').hasClass('marketking_url_unavailable')){
				e.preventDefault();
			}
		});


		// Dashboard
		/*
		* Draw the Sales Chart
		*/
		function drawSalesChart(){
			// dashboard but not reports
			
			if ($(".marketking_dashboard_page_wrapper").val()!== undefined && $(".marketking_reports_page_wrapper").val()=== undefined ){
			    var selectValue = parseInt($('#marketking_dashboard_days_select').val());
			    $('#marketking_dashboard_blue_button').text($('#marketking_dashboard_days_select option:selected').text());

			    if (selectValue === 0){
			        $('.marketking_total_b2b_sales_seven_days,.marketking_total_b2b_sales_thirtyone_days, .marketking_number_orders_seven, .marketking_number_orders_thirtyone, .marketking_number_customers_seven, .marketking_number_customers_thirtyone, .marketking_net_earnings_seven, .marketking_net_earnings_thirtyone').css('display', 'none');
			        $('.marketking_total_b2b_sales_today, .marketking_number_orders_today, .marketking_number_customers_today, .marketking_net_earnings_today').css('display', 'block');
			    } else if (selectValue === 1){
			        $('.marketking_total_b2b_sales_today,.marketking_total_b2b_sales_thirtyone_days, .marketking_number_orders_today, .marketking_number_orders_thirtyone, .marketking_number_customers_today, .marketking_number_customers_thirtyone, .marketking_net_earnings_today, .marketking_net_earnings_thirtyone').css('display', 'none');
			        $('.marketking_total_b2b_sales_seven_days, .marketking_number_orders_seven, .marketking_number_customers_seven, .marketking_net_earnings_seven').css('display', 'block');
			    } else if (selectValue === 2){
			        $('.marketking_total_b2b_sales_today,.marketking_total_b2b_sales_seven_days, .marketking_number_orders_today, .marketking_number_orders_seven, .marketking_number_customers_today, .marketking_number_customers_seven, .marketking_net_earnings_today, .marketking_net_earnings_seven').css('display', 'none');
			        $('.marketking_total_b2b_sales_thirtyone_days, .marketking_number_orders_thirtyone, .marketking_number_customers_thirtyone, .marketking_net_earnings_thirtyone').css('display', 'block');
			    }

			    if (selectValue === 0){
			        // set label
			        var labelsdraw = ['00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];
			        // set series
			        var seriesdrawb2b = marketking_dashboard.hours_sales_b2b.concat();
			        

			    } else if (selectValue === 1){
			        // set label
			        var date = new Date();
			        var d = date.getDate();
			        var labelsdraw = [d-6, d-5, d-4, d-3, d-2, d-1, d];
			        labelsdraw.forEach(myFunction);
			        function myFunction(item, index) {
			          if (parseInt(item)<=0){
			            let last = new Date();
			            let month = last.getMonth()-1;
			            let year = last.getFullYear();
			            let lastMonthDays = new Date(year, month, 0).getDate();
			            labelsdraw[index] = lastMonthDays+item;
			          }
			        }
			        // set series

			        var seriesdrawb2b = marketking_dashboard.days_sales_b2b.concat();
			        seriesdrawb2b.splice(7,24);
			        seriesdrawb2b.reverse();
			    } else if (selectValue === 2){
			        // set label
			        var labelsdraw = [];
			        let i = 0;
			        while (i<31){
			            let now = new Date();
			            let pastDate = new Date(now.setDate(now.getDate() - i));
			            let day = pastDate.getDate();
			            labelsdraw.unshift(day);
			            i++;
			        }
			        // set series
			        var seriesdrawb2b = marketking_dashboard.days_sales_b2b.concat();
			        seriesdrawb2b.reverse();
			    }

			    var chart = new Chartist.Line('.campaign', {
			        labels: labelsdraw,
			        series: [
			            seriesdrawb2b,
			        ]
			    }, {
			        low: 0,
			        high: Math.max(seriesdrawb2b),

			        showArea: true,
			        fullWidth: true,
			        plugins: [
			            Chartist.plugins.tooltip()
			        ],
			        axisY: {
			            onlyInteger: true,
			            scaleMinSpace: 40,
			            offset: 55,
			            labelInterpolationFnc: function(value) {
			                return marketking_dashboard.currency_symbol + (value / 1);
			            }
			        },
			    });

			    // Offset x1 a tiny amount so that the straight stroke gets a bounding box
			    // Straight lines don't get a bounding box 
			    // Last remark on -> http://www.w3.org/TR/SVG11/coords.html#ObjectBoundingBox
			    chart.on('draw', function(ctx) {
			        if (ctx.type === 'area') {
			            ctx.element.attr({
			                x1: ctx.x1 + 0.001
			            });
			        }
			    });

			    // Create the gradient definition on created event (always after chart re-render)
			    chart.on('created', function(ctx) {
			        var defs = ctx.svg.elem('defs');
			        defs.elem('linearGradient', {
			            id: 'gradient',
			            x1: 0,
			            y1: 1,
			            x2: 0,
			            y2: 0
			        }).elem('stop', {
			            offset: 0,
			            'stop-color': 'rgba(255, 255, 255, 1)'
			        }).parent().elem('stop', {
			            offset: 1,
			            'stop-color': 'rgba(64, 196, 255, 1)'
			        });
			    });

			    var chart = [chart];
			}
		}

		/* Reports Sales Chart */
		function drawReportsSalesChart(labelsdraw, salestotal, ordernumbers, commissiontotal){
			// dashboard but not reports
			
			if ($(".marketking_reports_page_wrapper").val()!== undefined ){
			    var selectValue = parseInt($('#marketking_dashboard_days_select').val());
			    $('#marketking_dashboard_blue_button').text($('#marketking_dashboard_days_select option:selected').text());

			    var chart = new Chartist.Line('.campaign', {
			        labels: labelsdraw,
			        series: [
			            salestotal,commissiontotal
			        ]
			    }, {
			        low: 0,
			        high: Math.max(commissiontotal,salestotal),

			        showArea: true,
			        fullWidth: true,
			        plugins: [
			            Chartist.plugins.tooltip()
			        ],
			        axisY: {
			            onlyInteger: true,
			            scaleMinSpace: 40,
			            offset: 55,
			            labelInterpolationFnc: function(value) {
			                return marketking_dashboard.currency_symbol + (value / 1);
			            }
			        },
			    });

			    var chart = new Chartist.Line('.campaign2', {
			        labels: labelsdraw,
			        series: [
			            [],ordernumbers
			        ]
			    }, {
			        low: 0,
			        high: Math.max(ordernumbers),

			        showArea: true,
			        fullWidth: true,
			        plugins: [
			            Chartist.plugins.tooltip()
			        ],
			        axisY: {
			            onlyInteger: true,
			            scaleMinSpace: 40,
			            offset: 55,
			        },
			    });

			    // Offset x1 a tiny amount so that the straight stroke gets a bounding box
			    // Straight lines don't get a bounding box 
			    // Last remark on -> http://www.w3.org/TR/SVG11/coords.html#ObjectBoundingBox
			    chart.on('draw', function(ctx) {
			        if (ctx.type === 'area') {
			            ctx.element.attr({
			                x1: ctx.x1 + 0.001
			            });
			        }
			    });

			    // Create the gradient definition on created event (always after chart re-render)
			    chart.on('created', function(ctx) {
			        var defs = ctx.svg.elem('defs');
			        defs.elem('linearGradient', {
			            id: 'gradient',
			            x1: 0,
			            y1: 1,
			            x2: 0,
			            y2: 0
			        }).elem('stop', {
			            offset: 0,
			            'stop-color': 'rgba(255, 255, 255, 1)'
			        }).parent().elem('stop', {
			            offset: 1,
			            'stop-color': 'rgba(64, 196, 255, 1)'
			        });
			    });

			    var chart = [chart];
			}
		}

		// Modules
		$('body').on('click', '.marketking_enable_all_modules' ,function(){
			jQuery('.marketking-checkbox-switch').find('input').each(function(i){
				if (jQuery(this).is(':not(:disabled')){
					jQuery(this).prop('checked', true);
				}
			});
		});
		
		$('body').on('click', '.marketking_disable_all_modules' ,function(){
			jQuery('.marketking-checkbox-switch').find('input').each(function(i){
				if (jQuery(this).is(':not(:disabled')){
					jQuery(this).prop('checked', false);
				}
			});
		});
		
		$('body').on('click', '.marketking_save_modules_settings', function(){
			var datavar = {
	            action: 'marketkingsavemodules',
	            security: marketking.security,
	        };

	        var sluglist = '';

	        jQuery('.marketking-checkbox-switch').find('input').each(function(i){
	        	// get its current number
	        	let checked = jQuery(this).prop('checked');
	        	let classList = $(this).attr('class').split(/\s+/);
	        	$.each(classList, function(index, item) {
	        	    if (item.includes('slug')) {
	        	    	var itemArray = item.split("_");
	        	    	let currentSlug = itemArray[1];
	        	    	datavar[currentSlug] = checked;
	        	    	sluglist += currentSlug+'-';
	        	    }
	        	});
	        });
	        datavar.sluglist = sluglist;

			$.post(ajaxurl, datavar, function(response){
				location.reload();
			});
		});

		/* Commission Rules */
		// update dynamic pricing rules
		updateDynamicRulesOptionsConditions();

		// Initialize Select2s
		$('#marketking_rule_select_who').select2();
		$('#marketking_rule_select_vendors_who').select2();

		$('#marketking_rule_select_applies').select2();


		// initialize multiple products / categories selector as Select2
		$('.marketking_select_multiple_product_categories_selector_select, .marketking_select_multiple_users_selector_select').select2({'width':'100%', 'theme':'classic'});
		// show hide multiple products categories selector
		showHideMultipleProductsCategoriesSelector();
		$('#marketking_rule_select_what').change(showHideMultipleProductsCategoriesSelector);
		$('#marketking_rule_select_applies').change(showHideMultipleProductsCategoriesSelector);
		function showHideMultipleProductsCategoriesSelector(){
			let selectedValue = $('#marketking_rule_select_applies').val();
			let selectedWhat = $('#marketking_rule_select_what').val();
			if ( (selectedValue === 'multiple_options' && selectedWhat !== 'tax_exemption_user') || (selectedValue === 'excluding_multiple_options' && selectedWhat !== 'tax_exemption_user')){
				$('#marketking_select_multiple_product_categories_selector').css('display','block');
			} else {
				$('#marketking_select_multiple_product_categories_selector').css('display','none');
			}
		}

		showHideMultipleUsersSelector();
		$('#marketking_rule_select_who').change(showHideMultipleUsersSelector);
		function showHideMultipleUsersSelector(){
			let selectedValue = $('#marketking_rule_select_who').val();
			if (selectedValue === 'multiple_options'){
				$('#marketking_select_multiple_users_selector').css('display','block');
			} else {
				$('#marketking_select_multiple_users_selector').css('display','none');
			}
		}

		showHideMultipleVendorsSelector();
		$('#marketking_rule_select_vendors_who').change(showHideMultipleVendorsSelector);
		function showHideMultipleVendorsSelector(){
			let selectedValue = $('#marketking_rule_select_vendors_who').val();
			if (selectedValue === 'multiple_options'){
				$('#marketking_select_multiple_vendors_selector').css('display','block');
			} else {
				$('#marketking_select_multiple_vendors_selector').css('display','none');
			}
		}

		showHideMultipleOptionsSelector();
		$('.marketking_field_settings_metabox_top_column_registration_option_select').change(showHideMultipleOptionsSelector);
		function showHideMultipleOptionsSelector(){
			let selectedValue = $('.marketking_field_settings_metabox_top_column_registration_option_select').val();
			if (selectedValue === 'multipleoptions'){
				$('#marketking_select_multiple_options_selector').css('display','block');
			} else {
				$('#marketking_select_multiple_options_selector').css('display','none');
			}
		}




		// On Rule selector change, update dynamic rule conditions
		$('#marketking_rule_select_what, #marketking_rule_select_who, #marketking_rule_select_orders, #marketking_rule_select_vendors_who, #marketking_rule_select_applies, #marketking_rule_select, #marketking_rule_select_showtax, #marketking_container_tax_shipping').change(function() {
			updateDynamicRulesOptionsConditions();
		});

		function updateDynamicRulesOptionsConditions(){
			$('#marketking_rule_select_applies_replaced_container').css('display','none');
			// Hide one-time fee
			$('#marketking_one_time').css('display','none');
			// Hide all condition options
			$('.marketking_dynamic_rule_condition_name option').css('display','none');
			// Hide quantity/value
			$('#marketking_container_quantity_value').css('display','none');
			// Hide currency
			$('#marketking_container_currency').css('display','none');
			// Hide payment methods
			$('#marketking_container_paymentmethods, #marketking_container_paymentmethods_minmax, #marketking_container_paymentmethods_percentamount').css('display','none');
			// Hide countries and requires
			$('#marketking_container_countries, #marketking_container_requires, #marketking_container_showtax').css('display','none');
			// Hide tax name
			$('#marketking_container_taxname, #marketking_container_tax_shipping, #marketking_container_tax_shipping_rate').css('display','none');
			// Hide discount checkbox
			$('.marketking_dynamic_rule_discount_show_everywhere_checkbox_container, .marketking_discount_options_information_box').css('display','none');
			$('#marketking_container_discountname').css('display','none');
			$('.marketking_rule_label_discount').css('display','none');
			$("#marketking_container_x").css('display','none');

			// conditions box text
			$('#marketking_rule_conditions_information_box_text').text(marketking.conditions_apply_cumulatively);

			// Show all options
			$("#marketking_container_howmuch").css('display','inline-block');
			$('#marketking_container_applies').css('display','inline-block');
			// Show conditions + conditions info box
			$('#marketking_rule_select_conditions_container').css('display','inline-block');
			$('.marketking_rule_conditions_information_box').css('display','flex');

			let selectedWhat = $("#marketking_rule_select_what").val();
			let selectedApplies = $("#marketking_rule_select_applies").val();
			let selectedOrders = $('#marketking_rule_select_orders').val();

			if (selectedOrders !== 'all' && selectedOrders !== 'all_vendor' && selectedOrders !== 'all_earnings'){
				$("#marketking_container_x").css('display','inline-block');
			}


			// Select Discount Amount or Percentage
			if (selectedWhat === 'discount_amount' || selectedWhat === 'discount_percentage'){
				// if select Cart: cart_total_quantity and cart_total_value
				if (selectedApplies === 'cart_total' || selectedApplies === 'excluding_multiple_options'){
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value]').css('display','block');
				} else if (selectedApplies.startsWith("category")){
				// if select Category also have: category_product_quantity and category_product_value
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value], .marketking_dynamic_rule_condition_name option[value=category_product_quantity], .marketking_dynamic_rule_condition_name option[value=category_product_value]').css('display','block');
				} else if (selectedApplies.startsWith("product")){
				// if select Product also have: product_quantity and product_value  
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value], .marketking_dynamic_rule_condition_name option[value=product_quantity], .marketking_dynamic_rule_condition_name option[value=product_value]').css('display','block');
				} else if (selectedApplies === 'multiple_options' || selectedApplies === 'excluding_multiple_options' || selectedApplies === 'replace_ids'){
					$('.marketking_dynamic_rule_condition_name option').css('display','block');
					// conditions box text
					$('#marketking_rule_conditions_information_box_text').text(marketking.conditions_multiselect);
				}
				// Show discount everywhere checkbox
				$('.marketking_dynamic_rule_discount_show_everywhere_checkbox_container, .marketking_discount_options_information_box').css('display','flex');
				$('.marketking_rule_label_discount').css('display','block');
				$('#marketking_container_discountname').css('display','inline-block');
			} else if (selectedWhat === 'fixed_price'){
				if (selectedApplies === 'cart_total'){
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value]').css('display','block');
				} else if (selectedApplies.startsWith("category")){
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=category_product_quantity]').css('display','block');
				} else if (selectedApplies.startsWith("product")){
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=product_quantity]').css('display','block');
				} else if (selectedApplies === 'multiple_options' || selectedApplies === 'replace_ids'){
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=category_product_quantity], .marketking_dynamic_rule_condition_name option[value=product_quantity]').css('display','block');
					$('#marketking_rule_conditions_information_box_text').text(marketking.conditions_multiselect);
				}
			} else if (selectedWhat === 'free_shipping'){
				// How much does not apply - hide
				$('#marketking_container_howmuch').css('display','none');
				// if select Cart: cart_total_quantity and cart_total_value
				if (selectedApplies === 'cart_total'){
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value]').css('display','block');
				} else if (selectedApplies.startsWith("category")){
				// if select Category also have: category_product_quantity and category_product_value
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value], .marketking_dynamic_rule_condition_name option[value=category_product_quantity], .marketking_dynamic_rule_condition_name option[value=category_product_value]').css('display','block');
				} else if (selectedApplies.startsWith("product")){
				// if select Product also have: product_quantity and product_value 
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value], .marketking_dynamic_rule_condition_name option[value=product_quantity], .marketking_dynamic_rule_condition_name option[value=product_value]').css('display','block'); 
				} else if (selectedApplies === 'multiple_options' || selectedApplies === 'replace_ids'){
					$('.marketking_dynamic_rule_condition_name option').css('display','block');
					$('#marketking_rule_conditions_information_box_text').text(marketking.conditions_multiselect);
				}
			} else if (selectedWhat === 'hidden_price'){
				// How much does not apply - hide
				$('#marketking_container_howmuch').css('display','none');
				// hide Conditions input and available conditions text
				$('#marketking_rule_select_conditions_container').css('display','none');
				$('.marketking_rule_conditions_information_box').css('display','none');

			} else if (selectedWhat === 'required_multiple'){

				// if select Cart: cart_total_quantity and cart_total_value
				if (selectedApplies === 'cart_total'){
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value]').css('display','block');
				} else if (selectedApplies.startsWith("category")){
				// if select Category also have: category_product_quantity and category_product_value
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value], .marketking_dynamic_rule_condition_name option[value=category_product_quantity], .marketking_dynamic_rule_condition_name option[value=category_product_value]').css('display','block');
				} else if (selectedApplies.startsWith("product")){
				// if select Product also have: product_quantity and product_value  
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value], .marketking_dynamic_rule_condition_name option[value=product_quantity], .marketking_dynamic_rule_condition_name option[value=product_value]').css('display','block');
				} else if (selectedApplies === 'multiple_options' || selectedApplies === 'replace_ids'){
					$('.marketking_dynamic_rule_condition_name option').css('display','block');
					$('#marketking_rule_conditions_information_box_text').text(marketking.conditions_multiselect);
				}

			} else if (selectedWhat === 'minimum_order' || selectedWhat === 'maximum_order' ) {
				// show Quantity/value
				$('#marketking_container_quantity_value').css('display','inline-block');
				// hide Conditions input and available conditions text
				$('#marketking_rule_select_conditions_container').css('display','none');
				$('.marketking_rule_conditions_information_box').css('display','none');
			} else if (selectedWhat === 'tax_exemption' ) {
				// How much does not apply - hide
				$('#marketking_container_howmuch').css('display','none');
				// show countries and requires
				$('#marketking_container_countries, #marketking_container_requires').css('display','inline-block');
				// hide Conditions input and available conditions text
				$('#marketking_rule_select_conditions_container').css('display','none');
				$('.marketking_rule_conditions_information_box').css('display','none');
			} else if (selectedWhat === 'tax_exemption_user' ) {
				// How much does not apply - hide
				$('#marketking_container_howmuch').css('display','none');
				// Applies does not apply - hide
				$('#marketking_container_applies').css('display','none');
				// show countries and requires
				$('#marketking_container_countries, #marketking_container_requires, #marketking_container_showtax').css('display','inline-block');
				if ($('#marketking_rule_select_showtax').val() === 'display_only'){
					$('#marketking_container_tax_shipping').css('display','inline-block');
					if ($('#marketking_rule_select_tax_shipping').val() === 'yes'){
						$('#marketking_container_tax_shipping_rate').css('display', 'inline-block');
					}
				}
				// hide Conditions input and available conditions text
				$('#marketking_rule_select_conditions_container').css('display','none');
				$('.marketking_rule_conditions_information_box').css('display','none');
			} else if (selectedWhat === 'add_tax_amount' || selectedWhat === 'add_tax_percentage' ) {
				// show one time
				$('#marketking_one_time').css('display','inline-block');
				// show tax name
				$('#marketking_container_taxname').css('display','inline-block');
				if (selectedApplies === 'one_time' && selectedWhat === 'add_tax_percentage'){
					$('#marketking_container_tax_shipping').css('display','inline-block');
				}
				// if select Cart: cart_total_quantity and cart_total_value
				if (selectedApplies === 'cart_total' || selectedApplies === 'one_time'){
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value]').css('display','block');
				} else if (selectedApplies.startsWith("category")){
				// if select Category also have: category_product_quantity and category_product_value
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value], .marketking_dynamic_rule_condition_name option[value=category_product_quantity], .marketking_dynamic_rule_condition_name option[value=category_product_value]').css('display','block');
				} else if (selectedApplies.startsWith("product")){
				// if select Product also have: product_quantity and product_value  
					$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value], .marketking_dynamic_rule_condition_name option[value=product_quantity], .marketking_dynamic_rule_condition_name option[value=product_value]').css('display','block');
				} else if (selectedApplies === 'multiple_options' || selectedApplies === 'replace_ids'){
					$('.marketking_dynamic_rule_condition_name option').css('display','block');
					$('#marketking_rule_conditions_information_box_text').text(marketking.conditions_multiselect);
				}
			} else if (selectedWhat === 'replace_prices_quote'){
				// How much does not apply - hide
				$('#marketking_container_howmuch, #marketking_container_applies').css('display','none');
				// hide Conditions input and available conditions text
				$('#marketking_rule_select_conditions_container').css('display','none');
				$('.marketking_rule_conditions_information_box').css('display','none');
			} else if (selectedWhat === 'rename_purchase_order'){
				// How much does not apply - hide
				$('#marketking_container_howmuch, #marketking_container_applies').css('display','none');
				// hide Conditions input and available conditions text
				$('#marketking_rule_select_conditions_container').css('display','none');
				$('.marketking_rule_conditions_information_box').css('display','none');
				$('#marketking_container_taxname').css('display','inline-block');
			} else if (selectedWhat === 'set_currency_symbol'){
				// How much does not apply - hide
				$('#marketking_container_howmuch, #marketking_container_applies').css('display','none');
				// hide Conditions input and available conditions text
				$('#marketking_rule_select_conditions_container').css('display','none');
				$('.marketking_rule_conditions_information_box').css('display','none');
				$('#marketking_container_currency').css('display','inline-block');
			} else if (selectedWhat === 'payment_method_minmax_order'){
				// How much does not apply - hide
				$('#marketking_container_applies').css('display','none');
				// hide Conditions input and available conditions text
				$('#marketking_rule_select_conditions_container').css('display','none');
				$('.marketking_rule_conditions_information_box').css('display','none');
				$('#marketking_container_paymentmethods, #marketking_container_paymentmethods_minmax').css('display','inline-block');
			}  else if (selectedWhat === 'payment_method_discount'){
				// How much does not apply - hide
				$('#marketking_container_applies').css('display','none');
				// hide Conditions input and available conditions text
				$('#marketking_rule_select_conditions_container').css('display','none');
				$('.marketking_rule_conditions_information_box').css('display','none');
				$('#marketking_container_paymentmethods, #marketking_container_paymentmethods_percentamount').css('display','inline-block');
			}  else if (selectedWhat === 'bogo_discount'){
				$('.marketking_dynamic_rule_condition_name option[value=cart_total_quantity], .marketking_dynamic_rule_condition_name option[value=cart_total_value], .marketking_dynamic_rule_condition_name option[value=category_product_quantity], .marketking_dynamic_rule_condition_name option[value=category_product_value]').css('display','block');
				$('.marketking_dynamic_rule_condition_name option[value=product_quantity], .marketking_dynamic_rule_condition_name option[value=product_value]').css('display','block');

			}

			if (selectedApplies === 'replace_ids' && selectedWhat !== 'tax_exemption_user'){
				$('#marketking_rule_select_applies_replaced_container').css('display','block');
			}


			if (selectedOrders === 'all_earnings' || selectedOrders === 'reach_x_number' || selectedOrders === 'first_x_earnings' || selectedOrders === 'first_x_days'){
				$('#marketking_container_applies, #marketking_container_forcustomers, #marketking_select_multiple_product_categories_selector, #marketking_select_multiple_users_selector').css('display','none');
			} else {
				$('#marketking_container_applies, #marketking_container_forcustomers').css('display','inline-block');
				if (selectedApplies === 'multiple_options'){
					$('#marketking_select_multiple_product_categories_selector').css('display','block');
				}
				if ($('#marketking_rule_select_who').val() === 'multiple_options'){
					$('#marketking_select_multiple_users_selector').css('display','block');
				}
			}


		}

		/* Group Rules */
		showHideMultipleAgentsSelector();
		$('#marketking_rule_select_agents_who').change(showHideMultipleAgentsSelector);
		function showHideMultipleAgentsSelector(){
			let selectedValue = $('#marketking_rule_select_agents_who').val();
			if (selectedValue === 'multiple_options'){
				$('#marketking_select_multiple_agents_selector').css('display','block');
			} else {
				$('#marketking_select_multiple_agents_selector').css('display','none');
			}
		}

		/* Memberships */
		$('body').on('click', '#marketking_pack_image' ,function(e){
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
	           $('#marketking_pack_image').val(marketking_image_url);
	       });
	   	});

	   	/* Advertising */
	   	// remove ads admin
   		$('#marketking_remove_advertise_admin').on('click', function(){
   			if (confirm(marketking.sure_remove_ad)){

	   			var datavar = {
	   	            action: 'marketking_remove_advertise_admin',
	   	            productid: $('#post_ID').val(),
	   	            security: marketking.security,
	   	        };
	   	        
	   			$.post(ajaxurl, datavar, function(response){
	   				if (response === 'success'){
	   					location.reload();
	   				}
	   			});
	   		}
   		});

	   	// add ads admin
   		$('#marketking_advertise_admin').on('click', function(){
   			if (confirm(marketking.sure_add_ad)){

	   			var datavar = {
	   	            action: 'marketking_add_advertise_admin',
	   	            productid: $('#post_ID').val(),
	   	            days: $('.advertising_days_input').val(),
	   	            security: marketking.security,
	   	        };
	   	        
	   			$.post(ajaxurl, datavar, function(response){
	   				if (response === 'success'){
	   					location.reload();
	   				}
	   			});
	   		}
   		});

   		// shipping tracking admin
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
   		$('#marketking_create_shipment_button').on('click', function(){
   			if ($('#marketking_create_shipment_tracking_number').val() !== ''){
                if (confirm(marketking.sure_create_shipment)) {
    	    		// delete product
    	    		var datavar = {
    		            action: 'marketkingcreateshipment',
    		            security: marketking.security,
    		           	orderid: $(this).attr('value'),
    		           	provider: $('#marketking_create_shipment_provider').val(),
    		           	providername: $('#marketking_create_shipment_provider_name').val(),
    		           	trackingnr: $('#marketking_create_shipment_tracking_number').val(),
    		           	trackingurl: $('#marketking_create_shipment_tracking_url').val(),
    		        };

    		        $.post(ajaxurl, datavar, function(response){
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

 
	});

})(jQuery);