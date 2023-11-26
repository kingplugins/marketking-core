<?php

class Marketkingcore {

	function __construct() {


		// filter to remove MarketKing in all API requests:
		require_once ( MARKETKINGCORE_DIR . 'includes/class-marketking-core-helper.php' );
		$helper = new Marketkingcore_Helper();
		$run_in_api_requests = true;
		if (apply_filters('marketking_force_cancel_api_requests', false)){
			if ($helper->marketking_is_rest_api_request()){
				$run_in_api_requests = false;
			}
		}

		if ($run_in_api_requests){

			// Handle form submission for become vendor loggedin
			add_action( 'admin_post_nopriv_marketking_become_vendor_loggedin', array($this, 'handle_form_become_vendor_loggedin') );
			add_action( 'admin_post_marketking_become_vendor_loggedin', array($this, 'handle_form_become_vendor_loggedin') );

			// Handle Ajax Requests
			if ( wp_doing_ajax() ){

				add_action('plugins_loaded', function(){

					
				});

				/* Verification */
				// admin mark verification as approved or rejected
				add_action( 'wp_ajax_marketking_mark_verification_approved', array($this, 'marketking_mark_verification_approved') );
				add_action( 'wp_ajax_nopriv_marketking_mark_verification_approved', array($this, 'marketking_mark_verification_approved') );

				add_action( 'wp_ajax_marketking_mark_verification_rejected', array($this, 'marketking_mark_verification_rejected') );
				add_action( 'wp_ajax_nopriv_marketking_mark_verification_rejected', array($this, 'marketking_mark_verification_rejected') );

				/* Refunds */
				// customer send request
				add_action( 'wp_ajax_marketking_send_refund', array($this, 'marketking_send_refund') );
	    		add_action( 'wp_ajax_nopriv_marketking_send_refund', array($this, 'marketking_send_refund') );

	    		// admin mark refund as pending or completed
	    		add_action( 'wp_ajax_marketking_mark_refund_completed', array($this, 'marketking_mark_refund_completed') );
	    		add_action( 'wp_ajax_nopriv_marketking_mark_refund_completed', array($this, 'marketking_mark_refund_completed') );

	    		add_action( 'wp_ajax_marketking_mark_refund_pending', array($this, 'marketking_mark_refund_pending') );
	    		add_action( 'wp_ajax_nopriv_marketking_mark_refund_pending', array($this, 'marketking_mark_refund_pending') );

	    		// handle commissions when order is refunded
	    		add_action( 'woocommerce_refund_created', array($this, 'handle_commissions_order_refunded'), 10, 2 ); 
	    		
				// Download vendor balance history
				add_action( 'wp_ajax_marketking_download_vendor_balance_history', array($this, 'marketking_download_vendor_balance_history') );
	    		add_action( 'wp_ajax_nopriv_marketking_download_vendor_balance_history', array($this, 'marketking_download_vendor_balance_history') );

				// Download vendor credit history
				add_action( 'wp_ajax_marketking_download_vendor_credit_history', array($this, 'marketking_download_vendor_credit_history') );
	    		add_action( 'wp_ajax_nopriv_marketking_download_vendor_credit_history', array($this, 'marketking_download_vendor_credit_history') );

				// Get page content function
				add_action( 'wp_ajax_marketking_get_page_content', array($this, 'marketking_get_page_content') );
	    		add_action( 'wp_ajax_nopriv_marketking_get_page_content', array($this, 'marketking_get_page_content') );

				// Dismiss "activate woocommerce" admin notice permanently
				add_action( 'wp_ajax_marketking_dismiss_activate_woocommerce_admin_notice', array($this, 'marketking_dismiss_activate_woocommerce_admin_notice') );

				// Save Product / Edit Product
				add_action( 'wp_ajax_marketkingsaveproduct', array($this, 'marketkingsaveproduct') );
				add_action( 'wp_ajax_nopriv_marketkingsaveproduct', array($this, 'marketkingsaveproduct') );

				// Delete Product
				add_action( 'wp_ajax_marketkingdeleteproduct', array($this, 'marketkingdeleteproduct') );
				add_action( 'wp_ajax_nopriv_marketkingdeleteproduct', array($this, 'marketkingdeleteproduct') );

				// Save Payout Info
				add_action( 'wp_ajax_marketkingsaveinfo', array($this, 'marketkingsaveinfo') );
				add_action( 'wp_ajax_nopriv_marketkingsaveinfo', array($this, 'marketkingsaveinfo') );

				// Make Withdrawal
				add_action( 'wp_ajax_marketking_make_withdrawal', array($this, 'marketking_make_withdrawal') );
				add_action( 'wp_ajax_nopriv_marketking_make_withdrawal', array($this, 'marketking_make_withdrawal') );

				// Save Payment
				add_action( 'wp_ajax_marketkingsaveadjustment', array($this, 'marketkingsaveadjustment') );
				add_action( 'wp_ajax_nopriv_marketkingsaveadjustment', array($this, 'marketkingsaveadjustment') );

				// Save Adjustment
				add_action( 'wp_ajax_marketkingsavepayment', array($this, 'marketkingsavepayment') );
				add_action( 'wp_ajax_nopriv_marketkingsavepayment', array($this, 'marketkingsavepayment') );

				// Check URL Exists
				add_action( 'wp_ajax_marketkingcheckurlexists', array($this, 'marketkingcheckurlexists') );
				add_action( 'wp_ajax_nopriv_marketkingcheckurlexists', array($this, 'marketkingcheckurlexists') );

				// Backend registration
				// Approve and Reject users
				add_action( 'wp_ajax_marketkingapproveuser', array($this, 'marketkingapproveuser') );
				add_action( 'wp_ajax_nopriv_marketkingapproveuser', array($this, 'marketkingapproveuser') );
				add_action( 'wp_ajax_marketkingrejectuser', array($this, 'marketkingrejectuser') );
				add_action( 'wp_ajax_nopriv_marketkingrejectuser', array($this, 'marketkingrejectuser') );
				add_action( 'wp_ajax_marketkingdeactivateuser', array($this, 'marketkingdeactivateuser') );
				add_action( 'wp_ajax_nopriv_marketkingdeactivateuser', array($this, 'marketkingdeactivateuser') );
				// Download file (e.g. registration files, company license etc)
				add_action( 'wp_ajax_marketkinghandledownloadrequest', array($this, 'marketkinghandledownloadrequest') );
				// Backend Update User Data
				add_action( 'wp_ajax_nopriv_marketkingupdateuserdata', array($this, 'marketkingupdateuserdata') );
				add_action( 'wp_ajax_marketkingupdateuserdata', array($this, 'marketkingupdateuserdata') );


				// Save User Profile Settings
				add_action( 'wp_ajax_marketking_save_profile_settings', array($this, 'marketking_save_profile_settings') );
				add_action( 'wp_ajax_nopriv_marketking_save_profile_settings', array($this, 'marketking_save_profile_settings') );

				
				// Save User Profile Info
				add_action( 'wp_ajax_marketking_save_profile_info', array($this, 'marketking_save_profile_info') );
				add_action( 'wp_ajax_nopriv_marketking_save_profile_info', array($this, 'marketking_save_profile_info') );

				// Load Products Table AJAX Vendor Dashboard
				add_action( 'wp_ajax_marketking_products_table_ajax', array($this, 'marketking_products_table_ajax') );
				add_action( 'wp_ajax_nopriv_marketking_products_table_ajax', array($this, 'marketking_products_table_ajax') );		
				
				// Load Orders Table AJAX Vendor Dashboard
				add_action( 'wp_ajax_marketking_orders_table_ajax', array($this, 'marketking_orders_table_ajax') );
				add_action( 'wp_ajax_nopriv_marketking_orders_table_ajax', array($this, 'marketking_orders_table_ajax') );


				// Save order status
				add_action( 'wp_ajax_marketkingsaveorder', array($this, 'marketkingsaveorder') );
				add_action( 'wp_ajax_nopriv_marketkingsaveorder', array($this, 'marketkingsaveorder') );		

				// Save modules
				add_action( 'wp_ajax_marketkingsavemodules', array($this, 'marketkingsavemodules') );
				add_action( 'wp_ajax_nopriv_marketkingsavemodules', array($this, 'marketkingsavemodules') );

				// Save Coupon / Edit Coupon
				add_action( 'wp_ajax_marketkingsavecoupon', array($this, 'marketkingsavecoupon') );
				add_action( 'wp_ajax_nopriv_marketkingsavecoupon', array($this, 'marketkingsavecoupon') );	

				// Receive inquiry
				add_action( 'wp_ajax_marketkingsendinquiry', array($this, 'marketkingsendinquiry') );
				add_action( 'wp_ajax_nopriv_marketkingsendinquiry', array($this, 'marketkingsendinquiry') );	

				// Receive support
				add_action( 'wp_ajax_marketkingsendsupport', array($this, 'marketkingsendsupport') );
				add_action( 'wp_ajax_nopriv_marketkingsendsupport', array($this, 'marketkingsendsupport') );	

				// Save Team
				add_action( 'wp_ajax_marketking_save_team_member', array($this, 'marketking_save_team_member') );
				add_action( 'wp_ajax_nopriv_marketking_save_team_member', array($this, 'marketking_save_team_member') );

				// Delete Team
				add_action( 'wp_ajax_marketking_delete_team_member', array($this, 'marketking_delete_team_member') );
				add_action( 'wp_ajax_nopriv_marketking_delete_team_member', array($this, 'marketking_delete_team_member') );

				// Create Shipment
				add_action( 'wp_ajax_marketkingcreateshipment', array($this, 'marketkingcreateshipment') );
				add_action( 'wp_ajax_nopriv_marketkingcreateshipment', array($this, 'marketkingcreateshipment') );

				// Order Received
				add_action( 'wp_ajax_marketkingshipmentreceived', array($this, 'marketkingshipmentreceived') );
				add_action( 'wp_ajax_nopriv_marketkingshipmentreceived', array($this, 'marketkingshipmentreceived') );

				// Duplicate product
				add_action( 'wp_ajax_marketking_duplicate_product', array($this, 'marketking_duplicate_product') );
				add_action( 'wp_ajax_nopriv_marketking_duplicate_product', array($this, 'marketking_duplicate_product') );

				add_action( 'wp_ajax_marketkingdisconnectstripe', array($this, 'marketkingdisconnectstripe') );
				add_action( 'wp_ajax_nopriv_marketkingdisconnectstripe', array($this, 'marketkingdisconnectstripe') );

				add_action( 'wp_ajax_marketkingactivatelicense', array($this, 'marketkingactivatelicense') );
				add_action( 'wp_ajax_nopriv_marketkingactivatelicense', array($this, 'marketkingactivatelicense') );


				/* Bookings */
				//save bookable resource
				add_action( 'wp_ajax_marketkingsavebookableresource', array($this, 'marketkingsavebookableresource')  );
				add_action( 'wp_ajax_nopriv_marketkingsavebookableresource', array($this, 'marketkingsavebookableresource') );

				//save booking order edit details
				add_action( 'wp_ajax_marketkingsavebookingorderedit', array($this, 'marketkingsavebookingorderedit') );
				add_action( 'wp_ajax_nopriv_marketkingsavebookingorderedit', array($this, 'marketkingsavebookingorderedit') );

				// Delete Bookable Product
				add_action( 'wp_ajax_marketkingdeletebookableresource', array($this, 'marketkingdeletebookableresource') );
				add_action( 'wp_ajax_nopriv_marketkingdeletebookableresource', array($this, 'marketkingdeletebookableresource') );

				//save bookable resource edit-booking-product
				add_action( 'wp_ajax_marketking_add_bookable_resource', array($this, 'marketking_add_bookable_resource'), 100 );
				add_action( 'wp_ajax_nopriv_marketking_add_bookable_resource', array($this, 'marketking_add_bookable_resource'), 100 );
				add_action( 'wp_ajax_marketking_remove_bookable_resource', array($this, 'marketking_remove_bookable_resource'), 100 );
				add_action( 'wp_ajax_nopriv_marketking_remove_bookable_resource', array($this, 'marketking_remove_bookable_resource'), 100 );

				// Commission Invoices
				add_action( 'wp_ajax_marketking_get_commission_invoice', array($this, 'marketking_get_commission_invoice') );
				add_action( 'wp_ajax_nopriv_marketking_get_commission_invoice', array($this, 'marketking_get_commission_invoice') );

			}


			add_action('plugins_loaded', function(){
				// B2BKing integration with pricing in the frontend
				if (!is_admin()){

					if (defined('B2BKING_DIR') && defined('MARKETKINGPRO_DIR') && intval(get_option('marketking_enable_b2bkingintegration_setting', 1)) === 1){
						require_once ( B2BKING_DIR . 'admin/class-b2bking-admin.php' );
						if (!isset($b2bking_admin)){
							$b2bking_admin = new B2bking_Admin;
						}
						
						/* Coupons */
						// add the ability to restrict coupons based on role
						add_action( 'woocommerce_coupon_options_usage_restriction', array($b2bking_admin,'b2bking_action_woocommerce_coupon_options_usage_restriction'), 10, 2 );
						add_action( 'woocommerce_coupon_options_save', array($b2bking_admin,'b2bking_action_woocommerce_coupon_options_save'), 10, 2 );
						
						/* Additional Product Data Tab for Fixed Price and Tiered Price */
						if (intval(get_option('b2bking_disable_group_tiered_pricing_setting', 0)) === 0){

							if(marketking()->vendor_has_panel('b2bkingtables')){
								add_filter( 'woocommerce_product_data_tabs', array( $b2bking_admin, 'b2bking_additional_panel_in_product_page' ) );
								add_action( 'woocommerce_product_data_panels', array( $b2bking_admin, 'b2bking_additional_panel_in_product_page_content' ) );
							}

							if(marketking()->vendor_has_panel('b2bkingpricing')){
								// simple
								add_action( 'woocommerce_product_options_pricing', array($b2bking_admin, 'additional_product_pricing_option_fields'), 99 );

								add_action( 'woocommerce_admin_process_product_object', array($b2bking_admin, 'b2bking_individual_product_pricing_data_save') );
								// variation
								// looks like not needed
							//	add_action( 'woocommerce_variation_options_pricing', array($b2bking_admin, 'additional_variation_pricing_option_fields'), 99, 3 );
								add_action( 'woocommerce_save_product_variation', array($b2bking_admin, 'save_variation_settings_fields'), 10, 2 );

								add_action('woocommerce_admin_process_product_object', array($b2bking_admin, 'b2bking_additional_panel_product_save'), 10, 1);

								add_action('b2bking_add_tier_button_classes', function(){
									echo 'btn btn-sm btn-gray';
								});
								add_action('b2bking_add_row_button_classes', function(){
									echo 'btn btn-sm btn-gray';
								});
							}
						}

					}
				}

				// woodmart error fix frequently bought together
				if (marketking()->is_vendor(get_current_user_id()) || marketking()->is_vendor_team_member(get_current_user_id())){
					global $woodmart_options;
					if (empty($woodmart_options)){
						$woodmart_options = array();
					}
					$woodmart_options['bought_together_enabled'] = 0;

					add_filter('woodmart_option', function($value, $slug){
						if ($slug === 'bought_together_enabled'){
							$value = 0;
						}
						return $value;
					}, 10, 2);
				}
				
					

				if (marketking()->vendor_all_products_downloadable(get_current_user_id())){
					add_action( 'woocommerce_admin_process_variation_object', function($variation, $i){
						$variation->set_props(array('downloadable' => 1,));
						$variation->save();
					}, 10, 2);
					add_action('marketking_dashboard_head', function(){
						?>
						<style type="text/css">
							.variable_is_downloadable, .woocommerce_variable_attributes .tips:nth-child(2){
								display:none;
							}
							.show_if_variation_downloadable{
								display: block !important;
							}
						</style>

						<?php
					});
				}

				if (marketking()->vendor_all_products_virtual(get_current_user_id())){
					add_action( 'woocommerce_admin_process_variation_object', function($variation, $i){
						$variation->set_props(array('virtual' => 1,));
						$variation->save();
					}, 10, 2);
					add_action('marketking_dashboard_head', function(){
						?>
						<style type="text/css">
							.variable_is_virtual, .woocommerce_variable_attributes .tips:nth-child(3){
								display:none;
							}
							.show_if_variation_virtual{
								display: block !important;
							}
						</style>

						<?php
					});
				}

				// hide learn more in vendor dashboard

				add_action('marketking_dashboard_head', function(){
					?>
					<style type="text/css">
						.variations-learn-more-link, #inventory_product_data .woocommerce-message a{
							display:none;
						}
					</style>

					<?php
				});
				

				// disallow backorders
				if (!marketking()->vendor_can_backorders(get_current_user_id())){
					add_filter('woocommerce_product_stock_status_options', function($arr){
						unset($arr['onbackorder']);
						return $arr;
					}, 10, 1);

					add_filter('wc_get_product_backorder_options', function($arr){
						unset($arr['notify']);
						unset($arr['yes']);
						return $arr;
					}, 10, 1);
					add_action('marketking_dashboard_head', function(){
						?>
						<style type="text/css">
							option[value="variable_stock_status_onbackorder"], ._backorders_field, .show_if_variation_manage_stock .form-row:nth-child(3){
								display:none;
							}
						</style>

						<?php
					});
				}

				// Add vendor registration shortcode
				add_action( 'init', array($this, 'marketking_vendor_registration_shortcode'));

				// Add vendor reviews shortcode
				add_action( 'init', array($this, 'marketking_vendor_reviews_shortcode'));

				// Shortcodes for other tabs
				add_action( 'init', array($this, 'marketking_advertised_products_shortcode'));
				add_action( 'init', array($this, 'marketking_vendor_products_shortcode'));
				add_action( 'init', array($this, 'marketking_vendor_details_shortcode'));
				add_action( 'init', array($this, 'marketking_vendor_contact_shortcode'));


				// Configure product class structures
				if (!is_admin()){
					add_filter('product_type_selector', function($arr){
						return array(
							'simple'   => esc_html__( 'Simple product', 'marketking-multivendor-marketplace-for-woocommerce' ),
						);
					}, 8, 1);
					add_filter('product_type_options', function($arr){
						return array();
					}, 8, 1);
				}
				
				
				if ((marketking()->is_vendor(get_current_user_id()) || marketking()->is_vendor_team_member()) && !current_user_can('administrator') && !current_user_can('shop_manager') && !current_user_can('demo_user')){
					// Prevent admin access for MarketKing vendors
					add_action('init',[$this, 'prevent_admin_access']);
					// do not show admin bar for marketking vendors
					add_filter( 'show_admin_bar', '__return_false' );	
					add_filter('get_edit_post_link', [$this, 'marketking_edit_post_link'], 10, 3);
				}

				if (intval(get_option('marketking_enable_elementor_setting', 1)) === 1){

					// Elementor module and support
					add_action( 'elementor/widgets/register', array($this, 'register_elementor_widgets') );
					// Register Elementor Category
					add_action( 'elementor/elements/categories_registered', array($this,'register_elementor_categories') );

				}

				// Highlight pages: Vendor Dashboard, Stores Page, etc.
				add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );
			
			});

			if (intval(get_option( 'marketking_vendor_registration_loggedin_setting', 0 )) === 1){
				add_filter('marketking_allow_logged_in_register_vendor','__return_true');
			}

			// Remove 'Hidden' status products from admin count in products backend
			// Hidden products are those created before new products are saved 
			add_filter( 'views_edit-product' , [$this, 'remove_hidden_products_admin_count'], 10, 1);
			// Remove old hidden products every 3 days (259200 s)
			add_action( 'wp_footer', [$this, 'clear_hidden_products']);


			// modify categories dropdown in edit product page
			add_filter( 'wp_dropdown_cats', [$this, 'wp_dropdown_cats_multiple'], 10, 2 );


			// custom URLs for vendor store pages
			function prefix_rewrite_rules(){

				// get all vendors with base store URLs
				$vendors = marketking()->get_all_vendors();

				if (!empty($vendors)){
					$stores_page = intval(apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' )));
					$stores_post = get_post($stores_page);
					if ($stores_post){
						$stores_slug = $stores_post->post_name;
					}
				}

				foreach ($vendors as $vendor){
					if (isset($vendor->ID)){
						$vendor_id = $vendor->ID;

						// check if vendor has its own base url
						$baseurl = get_user_meta($vendor_id,'marketking_vendor_store_url_base',true);
						if (!empty($baseurl)){
							if (intval($baseurl) === 1){

								$store_url = get_user_meta($vendor_id,'marketking_store_url', true);

								add_rewrite_rule(
								    '^'.$store_url.'$',
								    'index.php?pagename='.$stores_slug.'&vendorid='.$store_url,
								    'top' //Places it as the prioritary rewrite rule
								  );

								add_rewrite_rule(
								    '^'.$store_url.'/([^/]*)/?([^/]*)/?([^/]*)/?',
								    'index.php?pagename='.$stores_slug.'&vendorid='.$store_url.'&pagenr=$matches[1]&pagenr2=$matches[2]',
								    'top' //Places it as the prioritary rewrite rule
								  );
								
							}
						}
					}
					
				}			    
			  	
			  	if (apply_filters('marketking_flush_permalinks', true)){
			  		// Flush rewrite rules
			  		flush_rewrite_rules();
			  	}

			}
			  
			add_action( 'init', 'prefix_rewrite_rules' );

		}

		// Run Admin/Public code 
		if ( is_admin() ) { 
			require_once MARKETKINGCORE_DIR . '/admin/class-marketking-core-admin.php';
			global $marketking_admin;
			$marketking_admin = new Marketkingcore_Admin();
		} else if ( !$this->marketking_is_login_page() ) {
			require_once MARKETKINGCORE_DIR . '/public/class-marketking-core-public.php';
			global $marketking_public;
			$marketking_public = new Marketkingcore_Public();
		}	
		
		

		// Add email classes
		add_filter( 'woocommerce_email_classes', array($this, 'marketking_add_email_classes'));
		// Add extra email actions (account approved finish)
		add_filter( 'woocommerce_email_actions', array($this, 'marketking_add_email_actions'));


		// Prevent the agent from being sent to wp-admin on wrong login
		add_action('login_redirect', array($this, 'prevent_wp_login'), 10, 3);

		// also remove cookie
		add_action('wp_logout',array($this, 'auto_redirect_after_logout'), 10, 1);


		// add "Composite Order" status + exclude composite orders from wc reports
		add_action( 'init', array($this, 'marketking_register_status') );
		add_filter( 'wc_order_statuses', array($this, 'marketking_add_status'), 100, 1 );

		// potentially problematic capability check - to be paid attention to
		add_action('plugins_loaded', function(){
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				// does not apply in admin - in admin leads to some issues like cross-sales product search problems
				// to not apply in admin, we make it instead not apply to manage_woocommerce users
				add_filter( 'user_has_cap', [$this, 'vendor_edit_own_products'], 10000, 3 );
			}
		});
		

		// allow vendors to upload images
		add_filter( 'user_has_cap', [$this, 'vendor_upload_items'], 1000, 3 );
		add_filter( 'ajax_query_attachments_args', [$this, 'vendor_library_own_images'], 10, 1);


		// Register earning post type
		/* Earning Post Type */
		add_action( 'init', array($this, 'marketking_register_post_type_earning'), 0 );

		// When order status changes, change earning status
		add_action('woocommerce_order_status_changed', array($this,'change_earning_status'), 10, 3);

		// set the composite status as a status which can be completed
		add_filter('woocommerce_valid_order_statuses_for_payment_complete', array($this, 'composite_status_for_payment'), 10, 2);
		//add_filter('woocommerce_valid_order_statuses_for_payment', array($this, 'composite_status_for_payment'), 10, 2);


		// template file overwrite in theme folder
		add_filter('marketking_dashboard_template', array($this,'template_file_overwrite_theme_dashboard'), 10, 1);
		add_filter('marketking_template', array($this,'template_file_overwrite_theme'), 10, 1);


		// integration with webtoffee packing slips permissions
		add_filter('wf_pklist_alter_admin_print_role_access', array($this,'webtoffee_permissions_invoices_labels'), 10, 1);

		// integration with WOOF products filter, force enable 'swoof';
		if (!is_admin()){
			if (isset($_GET['max_price']) || isset($_GET['min_price'])){
				add_filter('woof_get_request_data', function($data){
					$data['swoof'] = 1;
					return $data;
				}, 10, 1);
			}
		}

		// woocommerce importer columns names
		add_filter( 'woocommerce_csv_product_import_mapping_options', array($this,'marketking_woo_importer_columns_display'), 10000, 1 );
		// woocommerce importer process
		add_action('woocommerce_product_import_inserted_product_object', array($this,'marketking_woo_importer_columns_process'), 10, 2);
		// column to exporter
		add_filter( 'woocommerce_product_export_product_column_vendor_id', array($this,'add_export_data'), 10, 2 );
		add_filter( 'woocommerce_product_export_column_names', array($this, 'add_export_column') );
		add_filter( 'woocommerce_product_export_product_default_columns', array($this, 'add_export_column') );

		// Add custom page header to pages
		add_action('all_admin_notices', array($this,'marketking_custom_page_header'));

		// Fire "product approved " email
		add_action( 'transition_post_status', array($this,'marketking_pending_to_published'), 10, 3 );

		// Filter edit order url
		add_filter('woocommerce_get_edit_order_url', array($this,'marketking_filter_edit_order_url'), 10, 2);

		// commission invoices backend
		add_filter('wpo_wcpdf_meta_box_actions', function($meta_box_actions, $post_id){
			ob_start();
			?>
			<a href="<?php echo admin_url( 'admin-ajax.php' ).'?action=marketking_get_commission_invoice&security='.wp_create_nonce('marketking_security_nonce').'&orderid='.$post_id;?>" class="button " target="_blank" alt="Commission Invoice"><?php esc_html_e('Commission Invoice','marketking-multivendor-marketplace-for-woocommerce');?></a>
			<?php
			echo ob_get_clean();
			
			return $meta_box_actions;
		}, 10, 2);

		// inactive vendor, products not purchasable

		if (apply_filters('marketking_inactive_vendor_apply', true)){
			add_filter( 'woocommerce_is_purchasable', array($this, 'inactive_vendor_items_not_purchasable'), 10, 2);
			add_filter( 'woocommerce_variation_is_purchasable', array($this, 'inactive_vendor_items_not_purchasable'), 10, 2);
		}

		add_action('woocommerce_single_product_summary', array($this, 'inactive_product_message'), 10);

		// modify email recipient for low stock / no stock notifications
		add_filter('woocommerce_email_recipient_low_stock', array($this, 'stock_product_email_notifications_recipient'), 10, 3);
		add_filter('woocommerce_email_recipient_no_stock', array($this, 'stock_product_email_notifications_recipient'), 10, 3);

		// Loco failed to start up error:
		add_action('plugins_loaded', function(){
			remove_action( 'admin_notices', ['Loco_hooks_AdminHooks','print_hook_failure'] );
		});

		// optional related products query only vendor's own products
		if (apply_filters('marketking_related_products_same_vendor', false)){
			add_filter('woocommerce_related_products', array($this, 'marketking_related_products_same_vendor'), 99999, 3);
			add_filter( 'woocommerce_products_widget_query_args', array($this, 'marketking_related_products_same_vendor_widget'), 10, 1 );
		}

		// Process order, assign vendor + calculate commissions
		add_action('woocommerce_checkout_order_processed', [$this,'process_order_vendors'], 1000, 3);
		add_action('woocommerce_thankyou', [$this,'process_order_vendors'], 10, 1);
		add_action('woocommerce_payment_complete', [$this,'process_order_vendors'], 10, 1);
		add_action('marketking_process_order', [$this,'process_order_vendors'], 10, 1);
		add_action( 'woocommerce_rest_insert_shop_order_object', array( $this, 'process_order_vendors_pos' ), 100, 3 );

		// subscriptions integration commission
		add_filter('wcs_new_order_created', function($order, $subscription, $type){
			// run code here to process marketking commissions
			do_action('marketking_process_order', $order->get_id());
			return $order;
		}, 10, 3);

		add_filter( 'get_avatar_url', [$this, 'marketking_filter_avatar_url'], 10, 3);

		add_filter('wc_price_based_country_stop_pricing', array($this, 'price_based_country_stop_switch'));
		
	}

	function price_based_country_stop_switch($val){
		if (marketking()->is_vendor_dashboard()){
			$val = true;
		}
		return $val;		
	}

	function composite_status_for_payment($statuses, $order){
		$statuses[] = 'composite';
		$statuses[] = 'wc-composite';
		return $statuses;
	}

	function marketking_filter_avatar_url( $url, $id_or_email, $args ){

		if (is_string($id_or_email) || is_float($id_or_email) || is_integer($id_or_email) ){
			if(strpos($id_or_email, '@') !== false){
				$user = get_user_by( 'email', $id_or_email );
				if ($user){
					$user_id = $user->ID;
				} else {
					$user_id = 0;
				}
			} else {
				$user_id = $id_or_email;

			}
			
			$marketking_img = marketking()->get_store_profile_image_link($user_id);
			if (!empty($marketking_img)){
				$url = $marketking_img;
			}
		}
	
		return $url;
	}

	function marketking_related_products_same_vendor( $related_posts, $product_id, $args ){

		// we have an array of product ids and we remove the ones that do not belong to the same vendor
		$vendor_id = marketking()->get_product_vendor($product_id);

		foreach ($related_posts as $key => $relatedid){
			if(marketking()->get_product_vendor($relatedid) !== $vendor_id){
				unset($related_posts[$key]);
			}
		}

		return $related_posts;
	}

	function marketking_related_products_same_vendor_widget( $query_args ){
					
		global $post;
		if (isset($post->ID)){
			$product_id = $post->ID;
			$author = marketking()->get_product_vendor($product_id);
		    $query_args['post_author'] = $author;
		    $query_args['author'] = $author;
		}
		
	    return $query_args;
	}

	

	function stock_product_email_notifications_recipient($recipient, $product, $nul){
		$vendor_id = intval(marketking()->get_product_vendor( $product->get_id() ));
		$admin_user_id = apply_filters('marketking_admin_user_id', 1);
		if ($vendor_id !== $admin_user_id){
			$vendor = new WP_User($vendor_id);
			$recipient = $vendor->user_email;
		}

		return $recipient;
	}

	function inactive_product_message(){
		global $post;
		if (isset($post->ID)){
			$product_id = $post->ID;
			$product_vendor = marketking()->get_product_vendor($product_id);
			if (marketking()->vendor_is_inactive($product_vendor)){
				esc_html_e('This product has been temporarily deactivated and cannot be purchased.','marketking-multivendor-marketplace-for-woocommerce');
			}
		}
		
	}

	function inactive_vendor_items_not_purchasable($purchasable, $product){

		if (!apply_filters('marketking_inactive_vendor_apply', true)){
			return $purchasable;	
		}

		$current_product_id = intval($product->get_id());

		$product_vendor = marketking()->get_product_vendor($current_product_id);

		if (marketking()->vendor_is_inactive($product_vendor)){
			$purchasable = false;
		}

		return $purchasable;
	}

	function marketking_filter_edit_order_url($url, $order){

		if (!current_user_can('manage_woocommerce')){
			$order_id = $order->get_id();
			$vendor_id = marketking()->get_order_vendor($order_id);
			$admin_user_id = apply_filters('marketking_admin_user_id', 1);

			if ($vendor_id !== $admin_user_id && !user_can($vendor_id,'manage_woocommerce')){
				// modify to go to vendor dashboard
				$url = trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'manage-order/'.$order_id;
			}
		}
		

		return $url;
	}

	function marketking_pending_to_published( $new, $old, $post ) {

		$post_id = $post->ID;
	    if (get_post_type($post_id) === 'product'){
	    	if ($new === 'publish' && $old === 'pending' ){
				// fire product approved email		
				do_action( 'marketking_product_has_been_approved', $post_id);
    	
		    }
	    }
	}

	function wp_dropdown_cats_multiple( $output, $r ) {

	    if( isset( $r['multiple'] ) && $r['multiple'] ) {

	        $output = preg_replace( '/^<select/i', '<select multiple data-live-search="true" data-style="btn-info"', $output );

	        $output = str_replace( "name='{$r['name']}'", "name='{$r['name']}[]'", $output );

	        $selected = is_array($r['selected']) ? $r['selected'] : explode( ",", $r['selected'] );
	        foreach ( array_map( 'trim', $selected ) as $value ){
	            $output = str_replace( "value=\"{$value}\"", "value=\"{$value}\" selected", $output );
	        }

	    }

	    return $output;
	}

	function marketking_custom_page_header(){
		global $pagenow;
		  
		if(!empty($_GET['taxonomy']) && in_array($pagenow,array( 'edit-tags.php','term.php')) && $_GET['taxonomy'] == 'storecat') {
			echo Marketkingcore_Admin::get_header_bar();
		}
	}

	function add_export_column( $columns ) {

		// column slug => column name
		$columns['vendor_id'] = esc_html__('Vendor ID','marketking-multivendor-marketplace-for-woocommerce');

		return $columns;
	}

	function add_export_data( $value, $product ) {
		$value = get_post_field( 'post_author', $product->get_id() );

		return $value;
	}

	function marketking_woo_importer_columns_display( $mappings ){

		$new_options = array();
		$new_options['vendor_id'] = esc_html__( 'Vendor ID', 'marketking-multivendor-marketplace-for-woocommerce' );

		$generic_mappings = array( 
			'marketking'  => array(
				'name'    => 'MarketKing',
				'options' => $new_options,
			),
		);

		return array_merge( $mappings, $generic_mappings );
	}

	function marketking_woo_importer_columns_process($object, $data){

		// b2c price tiers
		if (isset($data['vendor_id'])) {

			wp_update_post(
			   array(
					'ID'          => $object->get_id(),
					'post_author' => $data['vendor_id'],
			   )
			);
		}

	}

	public function add_display_post_states( $post_states, $post ) {

		// Dashboard page
		$dashboardid = intval(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true));

		if ( $dashboardid === $post->ID ) {
			$post_states['marketking_vendor_dashboard_page'] = esc_html__( 'Vendor Dashboard Page', 'marketking-multivendor-marketplace-for-woocommerce' );
		}


		// Stores page
		$storesid = intval(apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' ), 'post' , true) );
		if ( $storesid === $post->ID  ){

			$post_states['marketking_stores_page'] = esc_html__( 'Vendor Stores Page', 'marketking-multivendor-marketplace-for-woocommerce' );
		}

		// Elementor store page template
		$have_elementor = 'no';
		if (intval(get_option('marketking_enable_elementor_setting', 1)) === 1){
			$store_style = intval(get_option( 'marketking_store_style_setting', 1 ));

			if ($store_style === 4){
				if (intval(get_option( 'marketking_elementor_page_setting', 'disabled' )) === $post->ID){
					$post_states['marketking_store_elementor_template_page'] = esc_html__( 'Vendor Store Page Template (Elementor)', 'marketking-multivendor-marketplace-for-woocommerce' );

				}
			}
		}



		return $post_states;
	}

	function webtoffee_permissions_invoices_labels($allowedarr){
		// allow access only to own orders
		if (isset($_GET['post'])){
			$postid = sanitize_text_field($_GET['post']);
			// check if order belongs to him
			$current_id = get_current_user_id();
			if (marketking()->is_vendor_team_member()){
				$current_id = marketking()->get_team_member_parent();
			}
			if (intval(marketking()->get_order_vendor($postid)) === intval($current_id)){
				$allowedarr = array('manage_options', 'manage_woocommerce', 'upload_files');
			}
		}
		
	    return $allowedarr;
	}

	function marketking_add_bookable_resource() {
		check_ajax_referer( 'add-bookable-resource', 'security' );


		$post_id           = intval( $_POST['post_id'] );
		$loop              = intval( $_POST['loop'] );
		$add_resource_id   = intval( $_POST['add_resource_id'] );
		$add_resource_name = wc_clean( $_POST['add_resource_name'] );

		if ( ! $add_resource_id ) {
			$resource = new WC_Product_Booking_Resource();
			$resource->set_name( $add_resource_name );
			$add_resource_id = $resource->save();
		} else {
			$resource = new WC_Product_Booking_Resource( $add_resource_id );
		}

		if ( $add_resource_id ) {
			$product      = get_wc_product_booking( $post_id );
			$resource_ids = $product->get_resource_ids();

			if ( in_array( $add_resource_name, $resource_ids ) ) {
				wp_send_json( array( 'error' => __( 'The resource has already been linked to this product', 'marketking-multivendor-marketplace-for-woocommerce' ) ) );
			}

			$resource_ids[] = $add_resource_id;
			$product->set_resource_ids( $resource_ids );
			$product->save();


			// get the post object due to it is used in the included template
			$post = get_post( $post_id );

			ob_start();
			include( MARKETKINGPRO_DIR . 'includes/wcbookings/integrations/wc-bookings/includes/views/html-booking-resource.php' );
			wp_send_json( array( 'html' => ob_get_clean() ) );
		}

		wp_send_json( array( 'error' => __( 'Unable to add resource', 'marketking-multivendor-marketplace-for-woocommerce' ) ) );
	}

	/**
	 * Remove resource link from product.
	 */
	function marketking_remove_bookable_resource() {
		check_ajax_referer( 'delete-bookable-resource', 'security' );


		$post_id      = absint( $_POST['post_id'] );
		$resource_id  = absint( $_POST['resource_id'] );
		$product      = get_wc_product_booking( $post_id );
		$resource_ids = $product->get_resource_ids();
		$resource_ids = array_diff( $resource_ids, array( $resource_id ) );
		$product->set_resource_ids( $resource_ids );
		$product->save();
		die();


	}

	function marketkingdeletebookableresource(){
		// Check security nonce.
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
			wp_send_json_error( 'Invalid security token sent.' );
			wp_die();
		}

		$id = sanitize_text_field( $_POST['id'] );
		// check that current user is author of the product
		$author_id = get_post_field( 'post_author', $id );

		$current_id = get_current_user_id();
		if ( marketking()->is_vendor_team_member() ) {
			$current_id = marketking()->get_team_member_parent();
		}

		if ( intval( $author_id ) === $current_id || intval( $author_id ) === intval( get_current_user_id() ) ) {
			wp_trash_post( $id );
		}
	}

	function marketkingsavebookingorderedit() {

		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
			wp_send_json_error( 'Invalid security token sent.' );
			wp_die();
		}


		$post_data = wp_unslash( $_POST );
		$post_id   = intval( $post_data['resource_id'] );


		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		/*if ( empty( $_POST['post_ID'] ) || intval( $_POST['post_ID'] ) !== $post_id ) {
			return $post_id;
		}*/

		/*if ( ! in_array( $post->post_type, $this->post_types ) ) {
			return $post_id;
		}*/

		/*if ( $saved_meta_box ) {
			return $post_id;
		}*/


		// Get booking object.
		$booking = new WC_Booking( $post_id );

		if (isset($_POST['product_or_resource_id'])){
			$product_id = wc_clean( $_POST['product_or_resource_id'] ) ?: $booking->get_product_id();
		} else {
			$product_id = $booking->get_product_id();
		}
		
		if ( ! $product_id ) {
			echo esc_html( $post_id );
			exit();
			//return $post_id;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		// remove_action( current_filter(), __METHOD__ );
		// But cannot be used due to https://github.com/woocommerce/woocommerce/issues/6485
		// When that is patched in core we can use the above. For now:


		//	 $saved_meta_box = true;

		$start_date = wc_clean( $_POST['booking_start_date'] );
		$end_date   = wc_clean( $_POST['booking_end_date'] );

		if ( strtotime( $end_date ) < strtotime( $start_date ) ) {
			/*WC_Admin_Notices::add_custom_notice(
				'bookings_invalid_date_range',
				'<strong>' . esc_html__( 'Bookings', 'marketking-multivendor-marketplace-for-woocommerce' ) . '</strong> ' . esc_html__( 'Start date cannot be greater than end date.', 'marketking-multivendor-marketplace-for-woocommerce' )
			);*/
			wc_print_notice(
				'bookings_invalid_date_range',
				'<strong>' . esc_html__( 'Bookings', 'marketking-multivendor-marketplace-for-woocommerce' ) . '</strong> ' . esc_html__( 'Start date cannot be greater than end date.', 'marketking-multivendor-marketplace-for-woocommerce' )
			);
			echo esc_html( $post_id );
			exit();
		//		return $post_id;
		}

		/**
		 * Server-side validation for start and end dates to check if the format
		 * is yyyy-mm-dd in case client-side validation fails.
		 */
		$is_valid_start_date = DateTime::createFromFormat( 'Y-m-d', $start_date );
		$is_valid_end_date   = DateTime::createFromFormat( 'Y-m-d', $end_date );

		if ( false === $is_valid_start_date || false === $is_valid_end_date ) {
			/*WC_Admin_Notices::add_custom_notice(
				'bookings_invalid_date_format',
				'<strong>' . esc_html__( 'Bookings', 'marketking-multivendor-marketplace-for-woocommerce' ) . '</strong> ' . esc_html__( 'Date should be of the format yyyy-mm-dd and cannot be empty.', 'marketking-multivendor-marketplace-for-woocommerce' )
			);*/
			wc_print_notice(
				'bookings_invalid_date_format',
				'<strong>' . esc_html__( 'Bookings', 'marketking-multivendor-marketplace-for-woocommerce' ) . '</strong> ' . esc_html__( 'Date should be of the format yyyy-mm-dd and cannot be empty.', 'marketking-multivendor-marketplace-for-woocommerce' )
			);
			echo esc_html( $post_id );
			exit();
		//		return $post_id;
		}

		if ( wc_has_notice( 'bookings_invalid_date_format' ) ) {
			wc_clear_notices();
		}

		if ( wc_has_notice( 'bookings_invalid_date_range' ) ) {
			wc_clear_notices();
		}

		/*if ( WC_Admin_Notices::has_notice( 'bookings_invalid_date_format' ) ) {
			WC_Admin_Notices::remove_notice( 'bookings_invalid_date_format' );
		}

		if ( WC_Admin_Notices::has_notice( 'bookings_invalid_date_range' ) ) {
			WC_Admin_Notices::remove_notice( 'bookings_invalid_date_range' );
		}*/

		$booking_start_time = wc_clean( $_POST['booking_start_time'] );
		$booking_end_time   = wc_clean( $_POST['booking_end_time'] );

		if ( empty( $booking_start_time ) ) {
			$booking_start_time = '00:00';
		}

		if ( empty( $booking_end_time ) ) {
			$booking_end_time = '23:59';
		}

		$end_date   = explode( '-', $end_date );
		$start_date = explode( '-', $start_date );
		$start_time = explode( ':', $booking_start_time );
		$end_time   = explode( ':', $booking_end_time );
		$start      = mktime( $start_time[0], $start_time[1], 0, $start_date[1], $start_date[2], $start_date[0] );
		$end        = mktime( $end_time[0], $end_time[1], 0, $end_date[1], $end_date[2], $end_date[0] );

		if ( strstr( $product_id, '=>' ) ) {
			list( $product_id, $resource_id ) = explode( '=>', $product_id );
		} else {
			$resource_id = 0;
		}

		$person_counts     = $booking->get_person_counts( 'edit' );
		$product           = wc_get_product( $product_id );
		$booking_types_ids = array_keys( $booking->get_person_counts( 'edit' ) );
		$booking_order_id  = isset( $_POST['_booking_order_id'] ) ? absint( $_POST['_booking_order_id'] ) : '';
		$product_types_ids = $product ? array_keys( $product->get_person_types() ) : array();
		$booking_persons   = array();

		foreach ( array_unique( array_merge( $booking_types_ids, $product_types_ids ) ) as $person_id ) {
			$booking_persons[ $person_id ] = absint( $_POST[ '_booking_person_' . $person_id ] );
		}

		$booking->set_props( array(
			'all_day'       => isset( $_POST['_booking_all_day'] ),
			'customer_id'   => isset( $_POST['_booking_customer_id'] ) ? absint( $_POST['_booking_customer_id'] ) : '',
			'date_created'  => empty( $_POST['booking_date'] ) ? current_time( 'timestamp' ) : strtotime( $_POST['booking_date'] . ' ' . (int) $_POST['booking_date_hour'] . ':' . (int) $_POST['booking_date_minute'] . ':00' ),
			'end'           => $end,
			'order_id'      => $booking_order_id,
			'parent_id'     => absint( $_POST['_booking_parent_id'] ),
			'person_counts' => $booking_persons,
			'product_id'    => absint( $product_id ),
			'resource_id'   => absint( $resource_id ),
			'start'         => $start,
			'status'        => wc_clean( $_POST['_booking_status'] ),
		) );

		do_action( 'woocommerce_admin_process_booking_object', $booking );

		// Link booking with an order item.
		if ( ! empty( $booking_order_id ) ) {
			$order       = wc_get_order( $booking_order_id );
			$order_items = $order->get_items();

			foreach ( $order_items as $order_item ) {
				$order_item_id = $order_item->get_id();
				if ( ! $order_item_id ) {
					throw new Exception( __( 'Error: Could not create item', 'marketking-multivendor-marketplace-for-woocommerce' ) );
				}

				// Link only if the booking doesn't have an existing order or product of order item does not match with booking's product.
				$order_item_product_id = (int) wc_get_order_item_meta( $order_item_id, '_product_id' );
				if ( empty( $booking->get_order_item_id( $order_item_id ) ) || absint( $product_id ) !== $order_item_product_id ) {
					$booking->set_order_item_id( $order_item_id );
				}
			}
		}

		$booking->save();
		do_action( 'woocommerce_booking_process_meta', $post_id );

		echo esc_html( $post_id );
		exit();
	}

	function marketkingsavebookableresource(){

		// Check security nonce.
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
			wp_send_json_error( 'Invalid security token sent.' );
			wp_die();
		}

		$vendor_id = get_current_user_id();
		if ( marketking()->is_vendor_team_member() ) {
			$vendor_id = marketking()->get_team_member_parent();
		}

		$post_data = wp_unslash( $_POST );


		$status = sanitize_text_field( $_POST['marketking_edit_resource_status'] );
		// if status is published, check that the user didn't cheat and that they have permission
		if ( $status === 'publish' ) {
			// if vendor doesn't have permission, set it to draft
			if ( ! marketking()->vendor_can_publish_products( $vendor_id ) ) {
				$status = 'draft';
			}
		}
		$action = sanitize_text_field( $_POST['actionedit'] );
		if ( $action === 'add' ) {
			$add_resource_name = sanitize_text_field( wp_unslash( sanitize_text_field( $_POST['title'] ) ) );
			$resource          = array(
				'post_title'   => $add_resource_name,
				'post_content' => '',
				'post_status'  => 'publish',
				'post_author'  => $vendor_id,
				'post_type'    => 'bookable_resource',
				'meta_input'   => [ 'qty' => 1 ],
			);

			$resource_id = wp_insert_post( $resource );
			marketking()->save_posted_availability( $resource_id );

		} else if ( $action === 'edit' ) {

			$resource_id       = intval( $post_data['id'] );
			$author_id         = get_post_field( 'post_author', $resource_id );
			$post              = get_post( $resource_id );
			$post->post_title  = sanitize_text_field( $_POST['title'] );
			$post->post_status = $status;

			if ( intval( $author_id ) === $vendor_id || intval( $author_id ) === intval( get_current_user_id() ) ) {
				wp_update_post( $post );
				marketking()->save_posted_availability( $resource_id );
			}
		}
	}


	function clear_hidden_products(){

		$lastcleartime = get_option('marketking_clear_hidden_products_time', '');
		if (empty($lastcleartime)){
			$lastcleartime = intval(time())-9999999; //if first time, let's clear it
		}

		$lastcleartime = intval($lastcleartime);

		$time_elapsed = intval(time()) - intval($lastcleartime);

		// checks
		if ($time_elapsed < 0 or !is_numeric($time_elapsed) or $time_elapsed > 10000099){
			$time_elapsed = 1;
			update_option('marketking_clear_hidden_products_time', time());
		}

		if ($time_elapsed >= intval(apply_filters('marketking_clear_hidden_products_time_setting', 259200))){ //3 days
			$articles = get_posts(
			 array(
			  'numberposts' => -1,
			  'post_type' => 'product',
			  'fields'  => 'ids',
			 )
			);
			
			foreach ($articles as $post_id){
				if (get_post_status($post_id) === 'hidden'){
					// if not product standby
					$is_standby = get_post_meta($post_id,'marketking_is_product_standby', true);
					if (!$is_standby !== 'yes'){
						wp_delete_post($post_id, true);
					}
				}
			}

			update_option('marketking_clear_hidden_products_time', time());
		}
	}

	function register_elementor_widgets( $widgets_manager ) {

		require_once( __DIR__ . '/elementor/classes.php' );

		$widgets_manager->register( new \Elementor_Store_Title_Widget() );
		$widgets_manager->register( new \Elementor_Store_Profile_Image_Widget() );
		$widgets_manager->register( new \Elementor_Store_Banner_Image_Widget() );
		$widgets_manager->register( new \Elementor_Vendor_Badges_Widget() );
		$widgets_manager->register( new \Elementor_Store_Tabs_Widget() );
		$widgets_manager->register( new \Elementor_Store_Tabs_Follow_Widget() );
		$widgets_manager->register( new \Elementor_Store_Tabs_Content_Widget() );
		$widgets_manager->register( new \Elementor_Store_Notice_Widget() );
		$widgets_manager->register( new \Elementor_Social_Icons_Widget() );
	}

	function register_elementor_categories( $elements_manager ) {

		$elements_manager->add_category(
			'marketking',
			[
				'title' => esc_html__( 'MarketKing Multivendor Marketplace', 'marketking-multivendor-marketplace-for-woocommerce' ),
				'icon' => 'fa fa-plug',
			]
		);

	}

	function remove_hidden_products_admin_count( $views ){
	    global $current_screen;

	    switch( $current_screen->id ) 
	    {
	        case 'edit-product':

	        	global $user_ID, $wpdb;

	        	$total = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE (post_status = 'publish' OR post_status = 'draft' OR post_status = 'pending' OR post_status = 'private') AND (post_type = 'product') ");

	        	$views['all'] = preg_replace( '/\(.+\)/U', '('.$total.')', $views['all'] ); 

	            break;
	    }
	    return $views;
	}

	function template_file_overwrite_theme($templatefile){

		$theme_directory = get_stylesheet_directory();

    	$templatefilearray = explode('/', $templatefile);

		if ( file_exists( $theme_directory . '/marketking/' . end($templatefilearray) ) ) {
			return $theme_directory . '/marketking/' . end($templatefilearray) ;
		} else {
			// old behaviour, directly under theme folder
			if (apply_filters('marketking_allow_template_overwrite_direct_theme_folder', false)){
				if ( file_exists( $theme_directory . '/' . end($templatefilearray) ) ) {
					return $theme_directory . '/' . end($templatefilearray) ;
				}
			}
		}

        return $templatefile;
	}

	function template_file_overwrite_theme_dashboard($templatefile){

		$theme_directory = get_stylesheet_directory();

		if ( file_exists( $theme_directory . '/marketking/' . $templatefile ) ) {
			return $theme_directory . '/marketking/' . $templatefile ;
		} else {
			// check marketking pro file
			$templatefilearray = explode('/', $templatefile);

			// we are in a marketking pro file
			if ( file_exists( $theme_directory . '/marketking/' . end($templatefilearray) ) ) {
				return $theme_directory . '/marketking/' . end($templatefilearray) ;
			}

		    // old behaviour, directly under theme folder
			if (apply_filters('marketking_allow_template_overwrite_direct_theme_folder', false)){
				if ( file_exists( $theme_directory . '/' . $templatefile ) ) {
				    return $theme_directory . '/' . $templatefile ;
				} else {
					// check marketking pro file
					$templatefilearray = explode('/', $templatefile);

					// we are in a marketking pro file
					// sidebar exists in many themes
					if (end($templatefilearray) !== 'sidebar.php'){
						if ( file_exists( $theme_directory . '/' . end($templatefilearray) ) ) {
							return $theme_directory . '/' . end($templatefilearray) ;
						}
					}
					
				}
			}
		}

		return $templatefile;

	}


	function marketking_duplicate_product(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$product_id = sanitize_text_field($_POST['productid']);

		$vendor_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$vendor_id = marketking()->get_team_member_parent();
		}

		$product = wc_get_product($product_id);

		if ($product!==false){

			if(marketking()->vendor_can_add_more_products($vendor_id)){
				$admin = new WC_Admin_Duplicate_Product;
				$duplicate = $admin->product_duplicate( $product );
				$duplicate->set_name( $product->get_name() );
				$duplicate->save();

				// change author if current user is subteam
				if (marketking()->is_vendor_team_member()){
					wp_update_post(
					   array(
							'ID'          => $duplicate->get_id(),
							'post_author' => $vendor_id,
					   )
					);
				}

				// do not feature product
				$duplicate->set_featured(0);
				$duplicate->save();
			}
		}

		do_action('marketking_after_duplicate_product', $duplicate, $vendor_id);

		echo 'success';

		exit();
	}

	function marketkingdisconnectstripe(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$user_id = get_current_user_id();
		$stripe_user_id = get_user_meta($user_id, 'stripe_user_id', true);
		$settings = get_option('woocommerce_marketking_stripe_gateway_settings');

		$testmode = false;

		if (isset( $settings['test_mode'] )){
		    if ($settings['test_mode'] === 'yes'){
		        $testmode = true;
		    }
		}
		
		$client_id = $testmode ? sanitize_text_field( $settings['test_client_id'] ) : sanitize_text_field( $settings['client_id'] );
		$secret_key = $testmode ? sanitize_text_field( $settings['test_secret_key'] ) : sanitize_text_field( $settings['secret_key'] );
		$token_request_body = array(
			'client_id' => $client_id,
			'stripe_user_id' => $stripe_user_id,
			'client_secret' => $secret_key
		);
		
		$target_url = 'https://connect.stripe.com/oauth/deauthorize';
		$headers = array(
			'User-Agent'    => 'MarketKing Stripe Split Pay',
			'Authorization' => 'Bearer ' . $secret_key,
		);
		$response    = wp_remote_post( $target_url, array(
			'sslverify'   => apply_filters( 'https_local_ssl_verify', false ),
			'timeout'     => 70,
			'redirection' => 5,
			'blocking'    => true,
			'headers'     => $headers,
			'body'        => $token_request_body
			)
		);

		ob_start();
		if ( !is_wp_error( $response ) ) {
			$resp = (array) json_decode( $response['body'] );
			if ( ( isset($resp['error']) && ( $resp['error'] == 'invalid_client' ) )  || isset( $resp['stripe_user_id'] ) ) {
				delete_user_meta( $user_id, 'vendor_connected');
				delete_user_meta( $user_id, 'admin_client_id');
				delete_user_meta( $user_id, 'access_token');
				delete_user_meta( $user_id, 'refresh_token');
				delete_user_meta( $user_id, 'stripe_publishable_key');
				delete_user_meta( $user_id, 'stripe_user_id');
				echo 'Disconnected successfully';
			} else {
				_e( 'Unable to disconnect your account, please try again', 'marketking-multivendor-marketplace-for-woocommerce');
			}
		} else {
			_e( 'Unable to disconnect your account, please try again', 'marketking-multivendor-marketplace-for-woocommerce');
		}
		$content = ob_get_clean();

		echo $content;
		exit();
	}

	function marketking_get_edit_post_type_page($post_type_input){

		echo Marketkingcore_Admin::get_header_bar();


		/** WordPress Administration Bootstrap */
		//require_once ABSPATH . 'wp-admin/admin.php';
		global $post_type;
		global $post_type_object;
		$post_type = $post_type_input;
		$post_type_object = get_post_type_object( $post_type );
		set_current_screen('edit-'.$post_type);

		if ( ! $post_type_object ) {
			wp_die( __( 'Invalid post type.' ) );
		}

		if ( ! current_user_can( $post_type_object->cap->edit_posts ) ) {
			wp_die(
				'<h1>' . __( 'You need a higher level of permission.' ) . '</h1>' .
				'<p>' . __( 'Sorry, you are not allowed to edit posts in this post type.' ) . '</p>',
				403
			);
		}
		$args = array();
		$args['screen'] = get_current_screen();

		$wp_list_table = _get_list_table( 'WP_Posts_List_Table', $args );
		$pagenum       = $wp_list_table->get_pagenum();

		// Back-compat for viewing comments of an entry.
		foreach ( array( 'p', 'attachment_id', 'page_id' ) as $_redirect ) {
			if ( ! empty( $_REQUEST[ $_redirect ] ) ) {
				wp_redirect( admin_url( 'edit-comments.php?p=' . absint( $_REQUEST[ $_redirect ] ) ) );
				exit;
			}
		}
		unset( $_redirect );

		if ( 'post' !== $post_type ) {
			$parent_file   = "edit.php?post_type=$post_type";
			$submenu_file  = "edit.php?post_type=$post_type";
			$post_new_file = "post-new.php?post_type=$post_type";
		} else {
			$parent_file   = 'edit.php';
			$submenu_file  = 'edit.php';
			$post_new_file = 'post-new.php';
		}

		global $wp_query;
		$args = array('post_type' => $post_type,'post_status' => 'any', 'posts_per_page' => 20 );                                              
		$wp_query = new WP_Query( $args );

		$wp_list_table->prepare_items();

		wp_enqueue_script( 'inline-edit-post' );
		wp_enqueue_script( 'heartbeat' );

		if ( 'wp_block' === $post_type ) {
			wp_enqueue_script( 'wp-list-reusable-blocks' );
			wp_enqueue_style( 'wp-list-reusable-blocks' );
		}

		// Used in the HTML title tag.
		$title = $post_type_object->labels->name;


		get_current_screen()->set_screen_reader_content(
			array(
				'heading_views'      => $post_type_object->labels->filter_items_list,
				'heading_pagination' => $post_type_object->labels->items_list_navigation,
				'heading_list'       => $post_type_object->labels->items_list,
			)
		);

		add_screen_option(
			'per_page',
			array(
				'default' => 20,
				'option'  => 'edit_' . $post_type . '_per_page',
			)
		);

		$bulk_counts = array(
			'updated'   => isset( $_REQUEST['updated'] ) ? absint( $_REQUEST['updated'] ) : 0,
			'locked'    => isset( $_REQUEST['locked'] ) ? absint( $_REQUEST['locked'] ) : 0,
			'deleted'   => isset( $_REQUEST['deleted'] ) ? absint( $_REQUEST['deleted'] ) : 0,
			'trashed'   => isset( $_REQUEST['trashed'] ) ? absint( $_REQUEST['trashed'] ) : 0,
			'untrashed' => isset( $_REQUEST['untrashed'] ) ? absint( $_REQUEST['untrashed'] ) : 0,
		);

		$bulk_messages             = array();
		$bulk_messages['post']     = array(
			'updated'   => _n( '%s post updated.', '%s posts updated.', $bulk_counts['updated'] ),
			'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 post not updated, somebody is editing it.' ) :
							
							_n( '%s post not updated, somebody is editing it.', '%s posts not updated, somebody is editing them.', $bulk_counts['locked'] ),
		
			'deleted'   => _n( '%s post permanently deleted.', '%s posts permanently deleted.', $bulk_counts['deleted'] ),
			'trashed'   => _n( '%s post moved to the Trash.', '%s posts moved to the Trash.', $bulk_counts['trashed'] ),
			'untrashed' => _n( '%s post restored from the Trash.', '%s posts restored from the Trash.', $bulk_counts['untrashed'] ),
		);
		$bulk_messages['page']     = array(
			'updated'   => _n( '%s page updated.', '%s pages updated.', $bulk_counts['updated'] ),
			'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 page not updated, somebody is editing it.' ) :
							_n( '%s page not updated, somebody is editing it.', '%s pages not updated, somebody is editing them.', $bulk_counts['locked'] ),
			'deleted'   => _n( '%s page permanently deleted.', '%s pages permanently deleted.', $bulk_counts['deleted'] ),
			'trashed'   => _n( '%s page moved to the Trash.', '%s pages moved to the Trash.', $bulk_counts['trashed'] ),
			'untrashed' => _n( '%s page restored from the Trash.', '%s pages restored from the Trash.', $bulk_counts['untrashed'] ),
		);
		$bulk_messages['wp_block'] = array(
			'updated'   => _n( '%s block updated.', '%s blocks updated.', $bulk_counts['updated'] ),
			'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 block not updated, somebody is editing it.' ) :
							_n( '%s block not updated, somebody is editing it.', '%s blocks not updated, somebody is editing them.', $bulk_counts['locked'] ),
			'deleted'   => _n( '%s block permanently deleted.', '%s blocks permanently deleted.', $bulk_counts['deleted'] ),
			'trashed'   => _n( '%s block moved to the Trash.', '%s blocks moved to the Trash.', $bulk_counts['trashed'] ),
			'untrashed' => _n( '%s block restored from the Trash.', '%s blocks restored from the Trash.', $bulk_counts['untrashed'] ),
		);

		$bulk_messages = apply_filters( 'bulk_post_updated_messages', $bulk_messages, $bulk_counts );
		$bulk_counts   = array_filter( $bulk_counts );


		?>
		<div class="wrap">
		<h1 class="wp-heading-inline">
		<?php
		echo esc_html( $post_type_object->labels->name );
		?>
		</h1>

		<?php

		if ( current_user_can( $post_type_object->cap->create_posts ) ) {
			echo ' <a href="' . esc_url( admin_url( $post_new_file ) ) . '" class="page-title-action">' . esc_html( $post_type_object->labels->add_new ) . '</a>';
		}

		if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
			echo '<span class="subtitle">';
			printf(
				__( 'Search results for: %s' ),
				'<strong>' . get_search_query() . '</strong>'
			);
			echo '</span>';
		}
		?>

		<hr class="wp-header-end">

		<?php
		do_action( 'admin_notices' );

		// If we have a bulk message to issue:
		$messages = array();
		foreach ( $bulk_counts as $message => $count ) {
			if ( isset( $bulk_messages[ $post_type ][ $message ] ) ) {
				$messages[] = sprintf( $bulk_messages[ $post_type ][ $message ], number_format_i18n( $count ) );
			} elseif ( isset( $bulk_messages['post'][ $message ] ) ) {
				$messages[] = sprintf( $bulk_messages['post'][ $message ], number_format_i18n( $count ) );
			}

			if ( 'trashed' === $message && isset( $_REQUEST['ids'] ) ) {
				$ids        = preg_replace( '/[^0-9,]/', '', $_REQUEST['ids'] );
				$messages[] = '<a href="' . esc_url( wp_nonce_url( "edit.php?post_type=$post_type&doaction=undo&action=untrash&ids=$ids", 'bulk-posts' ) ) . '">' . __( 'Undo' ) . '</a>';
			}

			if ( 'untrashed' === $message && isset( $_REQUEST['ids'] ) ) {
				$ids = explode( ',', $_REQUEST['ids'] );

				if ( 1 === count( $ids ) && current_user_can( 'edit_post', $ids[0] ) ) {
					$messages[] = sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url( get_edit_post_link( $ids[0] ) ),
						esc_html( get_post_type_object( get_post_type( $ids[0] ) )->labels->edit_item )
					);
				}
			}
		}

		if ( $messages ) {
			echo '<div id="message" class="updated notice is-dismissible"><p>' . implode( ' ', $messages ) . '</p></div>';
		}
		unset( $messages );

		$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'locked', 'skipped', 'updated', 'deleted', 'trashed', 'untrashed' ), $_SERVER['REQUEST_URI'] );
		?>

		<?php $wp_list_table->views(); ?>

		<form id="posts-filter" method="get">

		<?php $wp_list_table->search_box( $post_type_object->labels->search_items, 'post' ); ?>

		<input type="hidden" name="post_status" class="post_status_page" value="<?php echo ! empty( $_REQUEST['post_status'] ) ? esc_attr( $_REQUEST['post_status'] ) : 'all'; ?>" />
		<input type="hidden" name="post_type" class="post_type_page" value="<?php echo $post_type; ?>" />

		<?php if ( ! empty( $_REQUEST['author'] ) ) { ?>
		<input type="hidden" name="author" value="<?php echo esc_attr( $_REQUEST['author'] ); ?>" />
		<?php } ?>

		<?php if ( ! empty( $_REQUEST['show_sticky'] ) ) { ?>
		<input type="hidden" name="show_sticky" value="1" />
		<?php } ?>

		<?php
		// set server URI for pagination to work
		$_SERVER['REQUEST_URI'] = '/wp-admin/edit.php?post_type='.$post_type;
		?>

		<?php $wp_list_table->display(); ?>

		</form>

		<?php
		if ( $wp_list_table->has_items() ) {
			$wp_list_table->inline_edit();
		}


		?>

		<div id="ajax-response"></div>
		<div class="clear"></div>
		</div>

		<?php

		
	}

	
	function change_earning_status($order_id, $status_from, $status_to){

		$order = wc_get_order($order_id);
		$method = $order->get_payment_method();

		// If order is a parent order, entirely virtual, and entirely downloadable, being set to completed, THEN set child orders to completed as well here
		if (marketking()->is_virtual_downloadable_order($order_id)){
			if (marketking()->is_multivendor_order($order_id)){
				$suborders = marketking()->get_suborders_of_order($order_id);
				foreach ($suborders as $suborder){
					if ($status_to === 'completed' || $status_to === 'wc-completed' || floatval($suborder->get_subtotal()) === floatval(0)){
						// set suborder to completed as well
						$suborder->update_status('completed', esc_html__('Parent order completed','marketking-multivendor-marketplace-for-woocommerce'));
						$suborder->save();

					}
				}
			} else {
				// may be credit order
				$credit_points = 0;
				// Get and Loop Over Order Items
				foreach ( $order->get_items() as $item_id => $item ) {
				   $product_id = $item->get_product_id();
				   if ($product_id === intval(get_option('marketking_credit_product_id_setting', 0))){
				   		$total = $item->get_quantity();
				   		$credit_points+= $total;	
				   }
				}
				if ($credit_points > 0){
					// set to completed
					if ($status_to === 'wc-processing' || $status_to === 'processing'){
						$order->update_status('completed', esc_html__('Credit order completed','marketking-multivendor-marketplace-for-woocommerce'));
						$order->save();
					}
					
				}
			}
		}

		// if order is parent order being set to completed, children should not be pending payment, if they are, set them to processing
		if (marketking()->is_multivendor_order($order_id)){
			$suborders = marketking()->get_suborders_of_order($order_id);
			foreach ($suborders as $suborder){
				if ($status_to === 'completed' || $status_to === 'wc-completed' || $status_to === 'wc-processing' || $status_to === 'processing'){
					// set suborder to completed as well
					if ($suborder->get_status() === 'pending' || $suborder->get_status() === 'wc-pending'){
						$suborder->update_status('processing', esc_html__('Parent order completed','marketking-multivendor-marketplace-for-woocommerce'));
						$suborder->save();

					}
				}
			}
		}


		// get earning id, if any
		$earning_id = $order->get_meta('marketking_earning_id');
		if (!empty($earning_id)){
			update_post_meta($earning_id,'order_status', $status_to);
		}

		$vendor_id = get_post_meta($earning_id,'vendor_id', true);
		$outstanding_balance = get_user_meta($vendor_id,'marketking_outstanding_earnings', true);
		if (empty($outstanding_balance)){
			$outstanding_balance = 0;
		}
		$total_earnings_on_order = get_post_meta($earning_id, 'marketking_commission_total', true);

		// add or remove balance for payouts
		
		// if order is paid with COD, and COD is excluded, ignore earnings
		if(get_option( 'marketking_cod_behaviour_setting', 'default' ) === 'exclude'){

			if ($method === 'cod' or apply_filters('marketking_exclude_commissions_method', false, $method)){
				// abort
				return;
			}
		}

	
		// if order is paid via Stripe, ignore earnings
		if( $order->get_meta('marketking_paid_via_stripe') === 'yes'){
			// abort
			return;
		}


		if (in_array($status_to,apply_filters('marketking_earning_completed_statuses', array('completed'))) && !in_array($status_from,apply_filters('marketking_earning_completed_statuses', array('completed')))){

			if(get_option( 'marketking_cod_behaviour_setting', 'default' ) === 'reverse' && $method === 'cod' && apply_filters('marketking_apply_cod_reverse', true, $order)){
				// COD ORDER = REVERSED COMMISSION = admin commission is deducted from vendor balance
				$admin_commission = marketking()->get_order_earnings($order_id,true);

				$new_balance = $outstanding_balance - $admin_commission;
				update_user_meta($vendor_id, 'marketking_outstanding_earnings', $new_balance);

				// user balance history update
				$amount = '-'.$admin_commission;
				$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
				$note = '(COD REVERSED) Order #'.$order_id.' status was changed to completed -> vendor balance was reduced.';
				$method = '-';
				$user_balance_history = sanitize_text_field(get_user_meta($vendor_id,'marketking_user_balance_history', true));
				$transaction_new = $date.':'.$amount.':'.$new_balance.':'.$note.':'.$method;
				update_user_meta($vendor_id,'marketking_user_balance_history',$user_balance_history.';'.$transaction_new);

			} else {
				// add balance for payout
				$new_balance = $outstanding_balance + $total_earnings_on_order;
				update_user_meta($vendor_id, 'marketking_outstanding_earnings', $new_balance);

				// user balance history update
				$amount = $total_earnings_on_order;
				$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
				$note = 'Order #'.$order_id.' status was changed to completed -> vendor balance was increased.';
				$method = '-';
				$user_balance_history = sanitize_text_field(get_user_meta($vendor_id,'marketking_user_balance_history', true));
				$transaction_new = $date.':'.$amount.':'.$new_balance.':'.$note.':'.$method;
				update_user_meta($vendor_id,'marketking_user_balance_history',$user_balance_history.';'.$transaction_new);

			}
			
		}

		if (! in_array($status_to,apply_filters('marketking_earning_completed_statuses', array('completed'))) && in_array($status_from,apply_filters('marketking_earning_completed_statuses', array('completed')))){

			if(get_option( 'marketking_cod_behaviour_setting', 'default' ) === 'reverse' && $method === 'cod' && apply_filters('marketking_apply_cod_reverse', true, $order)){
				// COD ORDER = REVERSED COMMISSION = admin commission is deducted from vendor balance
				$admin_commission = marketking()->get_order_earnings($order_id,true);

				$new_balance = $outstanding_balance + $admin_commission;
				update_user_meta($vendor_id, 'marketking_outstanding_earnings', $new_balance);

				// user balance history update
				$amount = $admin_commission;
				$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
				$note = '(COD REVERSED) Order #'.$order_id.' status was changed from completed -> vendor balance was increased.';
				$method = '-';
				$user_balance_history = sanitize_text_field(get_user_meta($vendor_id,'marketking_user_balance_history', true));
				$transaction_new = $date.':'.$amount.':'.$new_balance.':'.$note.':'.$method;
				update_user_meta($vendor_id,'marketking_user_balance_history',$user_balance_history.';'.$transaction_new);
			} else {
				// remove balance for payout
				$new_balance = $outstanding_balance - $total_earnings_on_order;
				update_user_meta($vendor_id, 'marketking_outstanding_earnings', $new_balance);

				// user balance history update
				$amount = '-'.$total_earnings_on_order;
				$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
				$note = 'Order #'.$order_id.' status was changed from completed -> vendor balance was reduced.';
				$method = '-';
				$user_balance_history = sanitize_text_field(get_user_meta($vendor_id,'marketking_user_balance_history', true));
				$transaction_new = $date.':'.$amount.':'.$new_balance.':'.$note.':'.$method;
				update_user_meta($vendor_id,'marketking_user_balance_history',$user_balance_history.';'.$transaction_new);
			}
		}


	}

	public static function marketking_register_post_type_earning(){
			// Build labels and arguments
		    $labels = array(
		        'name'                  => esc_html__( 'Earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'singular_name'         => esc_html__( 'Earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'all_items'             => esc_html__( 'Earnings', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'menu_name'             => esc_html__( 'Earnings', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'add_new'               => esc_html__( 'Create new earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'add_new_item'          => esc_html__( 'Create new customer earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'edit'                  => esc_html__( 'Edit', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'edit_item'             => esc_html__( 'Edit earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'new_item'              => esc_html__( 'New earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'view_item'             => esc_html__( 'View earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'view_items'            => esc_html__( 'View earnings', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'search_items'          => esc_html__( 'Search earnings', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'not_found'             => esc_html__( 'No earnings found', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'not_found_in_trash'    => esc_html__( 'No earnings found in trash', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'parent'                => esc_html__( 'Parent earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'featured_image'        => esc_html__( 'Earning image', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'set_featured_image'    => esc_html__( 'Set earning image', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'remove_featured_image' => esc_html__( 'Remove earning image', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'use_featured_image'    => esc_html__( 'Use as earning image', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'insert_into_item'      => esc_html__( 'Insert into earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'uploaded_to_this_item' => esc_html__( 'Uploaded to this earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'filter_items_list'     => esc_html__( 'Filter earnings', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'items_list_navigation' => esc_html__( 'Earnings navigation', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'items_list'            => esc_html__( 'Earnings list', 'marketking-multivendor-marketplace-for-woocommerce' )
		    );
		    $args = array(
		        'label'                 => esc_html__( 'Earning', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'description'           => esc_html__( 'Agent earnings', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'labels'                => $labels,
		        'supports'              => array( 'title' ),
		        'hierarchical'          => false,
		        'public'                => false,
		        'show_ui'               => true,
		        'show_in_menu'          => false,
		        'menu_position'         => 105,
		        'show_in_admin_bar'     => true,
		        'show_in_nav_menus'     => false,
		        'can_export'            => true,
		        'has_archive'           => false,
		        'exclude_from_search'   => true,
		        'publicly_queryable'    => false,
		        'capability_type'       => 'product',
		        'capabilities' => array(
		            'create_posts' => false, // Removes support for the "Add New" function
		          ),
		        'map_meta_cap'          => true,
		        'show_in_rest'          => true,
		        'rest_base'             => 'marketking_earning',
		        'rest_controller_class' => 'WP_REST_Posts_Controller',
		    );

			// Actually register the post type
			register_post_type( 'marketking_earning', $args );
	}

	public function vendor_edit_own_products( $allcaps, $caps, $args )	{

		if($args[0] !== 'read_product' || !isset($args[2])){
			return $allcaps;
		}

		$product_id = $args[2];
		$product_author = get_post_field( 'post_author', $product_id );

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		if (intval($current_id) !== intval($product_author)){
			return false;
		}

		return $allcaps;
	}


	public function vendor_upload_items( $allcaps, $caps, $args )	{

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		if (marketking()->is_vendor($current_id)){
			$allcaps['upload_files'] = true;
			$allcaps['edit_posts'] = true;
			$allcaps['read'] = true;
			$allcaps['publish_posts'] = true;
			$allcaps['delete_published_posts'] = true;
			$allcaps['edit_published_posts'] = true;
			$allcaps['delete_posts'] = true;
			$allcaps['manage_categories'] = true;
			$allcaps['moderate_comments'] = true;
			$allcaps['unfiltered_html'] = true;
			$allcaps['edit_shop_orders'] = true;
			$allcaps['edit_product'] = true;
			$allcaps['read_product'] = true;
			$allcaps['delete_product'] = true;
			$allcaps['edit_products'] = true;
			$allcaps['publish_products'] = true;
			$allcaps['read_private_products'] = true;
			$allcaps['delete_products'] = true;
			$allcaps['delete_private_products'] = true;
			$allcaps['delete_published_products'] = true;
			$allcaps['edit_private_products'] = true;
			$allcaps['edit_published_products'] = true;
			$allcaps['manage_product_terms'] = true;
			$allcaps['delete_product_terms'] = true;
			$allcaps['assign_product_terms'] = true;
			$allcaps['publish_event_magic_tickets'] = true;
		}

		return $allcaps;
	}

	public function vendor_library_own_images( $query ) {

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		if (marketking()->is_vendor($current_id) && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts')){  
		    $query['author'] = $current_id;
		}   
		return $query;
	}

	function marketking_register_status() {

		register_post_status( 'wc-composite', array(
			'label'		=> 'Composite order',
			'public'	=> true,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true, // show count All (12) , Completed (9) , Credit purchase (2) ...
			'label_count'	=> _n_noop( 'Composite order (%s)', 'Composite order (%s)' )
		) );

		// set up option to exclude status in woocommerce reports
		$ran_already = get_option('marketking_composite_status_ran');
		if ($ran_already !== 'yes'){

			$excluded_statuses = get_option( 'woocommerce_excluded_report_order_statuses', array( 'pending', 'failed', 'cancelled' ) );
			$statuses = array_merge( array( 'composite' ), $excluded_statuses );
			update_option('woocommerce_excluded_report_order_statuses', $statuses);
			update_option('marketking_composite_status_ran', 'yes');
		}
		
	}
	function marketking_add_status( $wc_statuses_arr ) {

		$new_statuses_arr = array();

		// add new order status after processing
		foreach ( $wc_statuses_arr as $id => $label ) {
			$new_statuses_arr[ $id ] = $label;

			if ( 'wc-pending' === $id ) { // after "Completed" status
				$new_statuses_arr['wc-composite'] = 'Composite order';
			}
		}

		return $new_statuses_arr;

	}

	// Add email classes to the list of email classes that WooCommerce loads
	function marketking_add_email_classes( $email_classes ) {

		$email_classes['Marketking_New_Product_Requires_Approval_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-product-requires-approval-email.php';

		$email_classes['Marketking_Product_Has_Been_Approved_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-product-has-been-approved-email.php';

	    $email_classes['Marketking_New_Vendor_Requires_Approval_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-vendor-requires-approval-email.php';

	    $email_classes['Marketking_Your_Account_Approved_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-your-account-approved-email.php';

	    $email_classes['Marketking_New_Payout_Email'] = include MARKETKINGCORE_DIR .'public/emails/class-marketking-new-payout-email.php';

	    if (defined('MARKETKINGPRO_DIR')){
	 	    $email_classes['Marketking_New_Announcement_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-announcement-email.php';
	  		$email_classes['Marketking_New_Message_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-message-email.php';
	  		$email_classes['Marketking_New_Rating_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-rating-email.php';
	  		$email_classes['Marketking_New_Refund_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-refund-email.php';
	  		$email_classes['Marketking_New_Verification_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-verification-email.php';

	  		// BOOKINGS
	  	//	$email_classes['WC_Marketking_Email_marketking_vendor_new_booking'] = include( MARKETKINGPRO_DIR . "includes/wcbookings/integrations/wc-bookings/includes/class-marketking-wc-bookings-email-vendor-new-booking.php" );
	  	//	$email_classes['WC_Marketking_Email_marketking_vendor_booking_cancelled'] = include( MARKETKINGPRO_DIR . "includes/wcbookings/integrations/wc-bookings/includes/class-marketking-wc-bookings-email-vendor-booking-cancelled.php" );

	  	}

	    return $email_classes;
	}

	// Add email actions
	function marketking_add_email_actions( $actions ) {
	    $actions[] = 'marketking_account_approved_finish';
	    $actions[] = 'marketking_new_payout';
	    $actions[] = 'marketking_new_announcement';
	    $actions[] = 'marketking_new_message';
	    $actions[] = 'marketking_new_rating';
	    $actions[] = 'marketking_new_refund';
	    $actions[] = 'marketking_new_verification';
	    $actions[] = 'marketking_new_user_requires_approval';
	    $actions[] = 'marketking_new_product_requires_approval';
	    $actions[] = 'marketking_product_has_been_approved';
	    $actions[] = 'marketking_vendor_new_booking';
	    $actions[] = 'marketking_vendor_booking_cancelled';

	    return $actions;
	}

	function marketking_edit_post_link($link, $post_id, $context){
		if (get_post_type($post_id) === 'product'){
			// if current vendor is the author of the product
			$current_id = get_current_user_id();
			if (marketking()->is_vendor_team_member()){
				$current_id = marketking()->get_team_member_parent();
			}

			if ($current_id === intval(get_post_field( 'post_author', $post_id ))){
				$link = trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'edit-product/'.$post_id;
			}
		}
		return $link;
	}

	function prevent_admin_access(){
	  if( is_admin() && !defined('DOING_AJAX')){
	    wp_redirect(home_url());
	    exit;
	  }
	}

	

	function marketking_save_profile_settings(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		$user_id = sanitize_text_field($_POST['userid']);

		$ann = sanitize_text_field($_POST['announcementsemails']);
		$ann = filter_var($ann,FILTER_VALIDATE_BOOLEAN);

		$msg = sanitize_text_field($_POST['messagesemails']);
		$msg = filter_var($msg,FILTER_VALIDATE_BOOLEAN);

		$refund = sanitize_text_field($_POST['refundemails']);
		$refund = filter_var($refund,FILTER_VALIDATE_BOOLEAN);

		$review = sanitize_text_field($_POST['reviewemails']);
		$review = filter_var($review,FILTER_VALIDATE_BOOLEAN);

		$ajax = sanitize_text_field($_POST['dashboardajax']);
		$ajax = filter_var($ajax,FILTER_VALIDATE_BOOLEAN);


		if ($ann === true){
			update_user_meta($user_id,'marketking_receive_new_announcements_emails', 'yes');
		} else {
			update_user_meta($user_id,'marketking_receive_new_announcements_emails', 'no');
		}

		if ($msg === true){
			update_user_meta($user_id,'marketking_receive_new_messages_emails', 'yes');
		} else {
			update_user_meta($user_id,'marketking_receive_new_messages_emails', 'no');
		}

		if ($review === true){
			update_user_meta($user_id,'marketking_receive_new_review_emails', 'yes');
		} else {
			update_user_meta($user_id,'marketking_receive_new_review_emails', 'no');
		}

		if ($refund === true){
			update_user_meta($user_id,'marketking_receive_new_refund_emails', 'yes');
		} else {
			update_user_meta($user_id,'marketking_receive_new_refund_emails', 'no');
		}

		if ($ajax === true){
			update_user_meta(get_current_user_id(),'marketking_vendor_load_tables_ajax', 'yes');
		} else {
			update_user_meta(get_current_user_id(),'marketking_vendor_load_tables_ajax', 'no');
		}

		echo 'success';
		exit();
	}


	function handle_commissions_order_refunded( $refund_id, $args ){

		/*
		1) First calculate initial admin commission / calculation basis and save the proportion
		 
		2) Calculate new calculation basis

		3) Apply previous proportion to it
		*/
		
		$refund_amount = $args['amount']; 
		$order_id = $args['order_id'];
		$order = wc_get_order($order_id);

		// if COD order and COD is set to ignored => ignore transaciton
		$method = $order->get_payment_method();
		// if order is paid with COD, and COD is excluded, ignore earnings
		if(get_option( 'marketking_cod_behaviour_setting', 'default' ) === 'exclude'){

			if ($method === 'cod' or apply_filters('marketking_exclude_commissions_method', false, $method)){
				// abort
				return;
			}
		}

		// if order is paid via stripe, abort

		if( $order->get_meta('marketking_paid_via_stripe') === 'yes'){
			return;
		}
		
		$order_total = $order->get_total();

		$shipping_total = $order->get_shipping_total();
		$taxes = $order->get_taxes();
		$tax_total = 0;
		foreach ($taxes as $tax){
			$tax_total += $tax->get_tax_total();
		}
		$tax_total+=+$order->get_shipping_tax();

		$tax_fee_recipient = $order->get_meta('tax_fee_recipient');
		if (empty($tax_fee_recipient)){
			$tax_fee_recipient = get_option('marketking_tax_fee_recipient_setting', 'vendor');
		}

		$shipping_fee_recipient = get_option('marketking_shipping_fee_recipient_setting', 'vendor');

	
		// 1. Get proportion admin commission / calc basis
		$proportion = $order->get_meta('marketking_refund_proportion');
		if (empty($proportion)){
			$calculation_basis = $order_total;		

			if ($tax_fee_recipient === 'vendor' || $tax_fee_recipient === 'admin'){
				$calculation_basis -= $tax_total;
			}

			if ($shipping_fee_recipient === 'vendor' || $shipping_fee_recipient === 'admin'){
				$calculation_basis -= $shipping_total;
			}

			$admin_commission = marketking()->get_order_earnings($order_id,true);

			if ($tax_fee_recipient === 'admin'){
				$admin_commission -= $tax_total; // we remove it for tax calculation
			}
			if ($shipping_fee_recipient === 'admin'){
				$admin_commission -= $shipping_total; // we remove it for tax calculation
			}

			$proportion = floatval($admin_commission) / $calculation_basis;

			$order->update_meta_data('marketking_refund_proportion', $proportion);
			$order->save();
		}
		

		// 2. Get NEW calculation basis
		$new_order_total = $order_total-$order->get_total_refunded();
		$new_calculation_basis = $new_order_total;
		if ($tax_fee_recipient === 'vendor' || $tax_fee_recipient === 'admin'){
			$new_calculation_basis -= $tax_total;
		}

		if ($shipping_fee_recipient === 'vendor' || $shipping_fee_recipient === 'admin'){
			$new_calculation_basis -= $shipping_total;
		}

		if ($new_calculation_basis < 0){
			$new_calculation_basis = 0;
		}

		// New calculation basis end


		// 3. Apply proportion
		$new_admin_commission = $proportion * $new_calculation_basis;
		if ($tax_fee_recipient === 'admin'){
			$new_admin_commission += $tax_total;
		}
		if ($shipping_fee_recipient === 'admin'){
			$new_admin_commission += $shipping_total;
		}


		$earnings = get_posts( array( 
		    'post_type' => 'marketking_earning',
		    'numberposts' => -1,
		    'post_status'    => 'any',
		    'fields'    => 'ids',
		    'meta_key'   => 'order_id',
		    'meta_value' => $order_id,
		));


		if (!empty($earnings)){
			
			$earning_id = $earnings[0];
			// there is an associated commission, continue

			// we need to first adjust the earnings. And then, if the order is completed status (commission granted, we need to modify vendor balance)

			$commission_total = get_post_meta($earning_id,'marketking_commission_total', true);
			$admin_commission = marketking()->get_order_earnings($order_id,true); // 2.5

			$new_commission_total = $order_total-$new_admin_commission;
			// update commission
			update_post_meta($earning_id,'marketking_commission_total', $new_commission_total);

			// if completed status (commission granted), edit balance and write in history log
			$status = $order->get_status();

			// check if approved
			if (in_array($status,apply_filters('marketking_earning_completed_statuses', array('completed')))){


				$vendor_id = get_post_meta($earning_id,'vendor_id', true);
				$user_outstanding_balance = get_user_meta($vendor_id,'marketking_outstanding_earnings', true);

				if(get_option( 'marketking_cod_behaviour_setting', 'default' ) === 'reverse' && $method === 'cod' && apply_filters('marketking_apply_cod_reverse', true, $order)){
					// COD ORDER = REVERSED COMMISSION = admin commission is deducted from vendor balance

					$new_outstanding_balance = $user_outstanding_balance + $admin_commission - $new_admin_commission;
					update_user_meta($vendor_id, 'marketking_outstanding_earnings', $new_outstanding_balance);

					// update balance history
					// user balance history update
					$amount = ($admin_commission - $new_admin_commission);
					$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
					$note = '(COD REVERSED) Order #'.$order_id.' was refunded -> vendor balance was increased.';
					$method = '-';
					$user_balance_history = sanitize_text_field(get_user_meta($vendor_id,'marketking_user_balance_history', true));
					$transaction_new = $date.':'.$amount.':'.$new_outstanding_balance.':'.$note.':'.$method;
					update_user_meta($vendor_id,'marketking_user_balance_history',$user_balance_history.';'.$transaction_new);

				} else {
					// substract old commission, add new commission to balance
					$new_outstanding_balance = $user_outstanding_balance - $commission_total + $new_commission_total;
					update_user_meta($vendor_id,'marketking_outstanding_earnings', $new_outstanding_balance);


					// update balance history
					// user balance history update
					$amount = ($new_commission_total - $commission_total);
					$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
					$note = 'Order #'.$order_id.' was refunded -> vendor commission and balance were reduced.';
					$method = '-';
					$user_balance_history = sanitize_text_field(get_user_meta($vendor_id,'marketking_user_balance_history', true));
					$transaction_new = $date.':'.$amount.':'.$new_outstanding_balance.':'.$note.':'.$method;
					update_user_meta($vendor_id,'marketking_user_balance_history',$user_balance_history.';'.$transaction_new);
				}


			    
			}

			

			
		}

		
		
	}

	function marketking_mark_verification_approved(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$vreqid = sanitize_text_field($_POST['vreqid']);
		update_post_meta($vreqid,'status','approved');

		$vendor_id = get_post_field( 'post_author', $vreqid );
		$vendor_email = marketking()->get_vendor_email($vendor_id);
		$vitem = get_post_meta($vreqid,'vitem', true);


		// send email
		do_action( 'marketking_new_verification', $vendor_email, 'approved', '', $vitem );
	}

	function marketkingactivatelicense(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		// If nonce verification didn't fail, run further

		$email = sanitize_text_field($_POST['email']);
		$key = sanitize_text_field($_POST['key']);

		$info = parse_url(get_site_url());
		$host = $info['host'];
		$host_names = explode(".", $host);
		$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];

		if (strlen($host_names[count($host_names)-2]) <= 3){    // likely .com.au, .co.uk, .org.uk etc
		    $bottom_host_name = $host_names[count($host_names)-3] . "." . $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
		}

		// send activation request
		$curl = curl_init();

		curl_setopt_array($curl, [
		  CURLOPT_URL => "https://kingsplugins.com/wp-json/licensing/v1/request?email=".$email."&license=".$key."&requesttype=siteactivation&plugin=MK&website=".$bottom_host_name,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => [
			"Content-Type: application/json"
		  ],
		]);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  $response = $err;
		} else {
		   $response = json_decode($response);
		}

		if ($response === 'success'){
			echo 'success';
			// activate
			update_option('pluginactivation_'.$email.'_'.$key.'_'.$bottom_host_name, 'active');
			update_option('marketking_use_legacy_activation', 'no');
			update_option('marketking_failed_license_'.$key, 0);

		} else {
			if (empty($response)){
				$response = "connection issue, there may be a temporary timeout of the activation server. Please try it again later. It could also be a conflict with another plugin blocking the connection.";
			}

			echo 'Failed to activate: '.$response;
		}

		exit();	
	}

	function marketking_mark_verification_rejected(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$vreqid = sanitize_text_field($_POST['vreqid']);
		$reason = sanitize_text_field($_POST['reason']);
		update_post_meta($vreqid,'status','rejected');
		update_post_meta($vreqid,'rejection_reason',$reason);

		$vendor_id = get_post_field( 'post_author', $vreqid );
		$vendor_email = marketking()->get_vendor_email($vendor_id);
		$vitem = get_post_meta($vreqid,'vitem', true);

		// send email
		do_action( 'marketking_new_verification', $vendor_email, 'rejected', $reason, $vitem );

	}

	function marketking_mark_refund_completed(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$refundid = sanitize_text_field($_POST['refundvalue']);
		update_post_meta($refundid,'completion_status','completed');

	}

	function marketking_mark_refund_pending(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$refundid = sanitize_text_field($_POST['refundvalue']);

		update_post_meta($refundid,'completion_status','pending');
	}

	function marketking_send_refund(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$orderid = sanitize_text_field($_POST['orderid']);
		$value = sanitize_text_field($_POST['value']);
		$reason = sanitize_text_field($_POST['reason']);
		$partialamount = sanitize_text_field($_POST['partialamount']);
		
		//create product first and set title
		$post_id = wp_insert_post( array(
			'post_title' => 'MarketKing Refund',
			'post_status' => 'publish',
			'post_type' => 'marketking_refund',
		));

		$vendor_id = marketking()->get_order_vendor($orderid);

		update_post_meta($post_id,'order_id', $orderid);
		update_post_meta($post_id,'reason', $reason);
		update_post_meta($post_id,'value', $value);
		update_post_meta($post_id,'partialamount', $partialamount);
		update_post_meta($post_id,'vendor_id', $vendor_id);
		update_post_meta($post_id,'request_status', 'open');

		// send email
		$user = wp_get_current_user();
		if (marketking()->is_vendor_team_member()){
			$user = new WP_User(marketking()->get_team_member_parent());
		}

		$vendor_email = marketking()->get_vendor_email($vendor_id);
		$permission = get_user_meta($vendor_id, 'marketking_receive_new_refund_emails', true);
		if (empty($permission)){
			$permission = 'yes';
		}

		if ($permission === 'yes'){

			do_action( 'marketking_new_refund', apply_filters('marketking_refund_request_recipient_email', $vendor_email), $post_id, $reason, $user->user_login );

			// custom action hook
			do_action( 'marketking_new_refund_request', $vendor_email, $post_id, $reason, $user->user_login );
		}


	}

	function marketkingdeleteproduct(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$id = sanitize_text_field($_POST['id']);
		// check that current user is author of the product
		$author_id = get_post_field( 'post_author', $id );

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		if (intval($author_id) === $current_id || intval($author_id) === intval(get_current_user_id())){
			if (apply_filters('marketking_allow_vendor_product_delete', true)){
				wp_trash_post($id);
			}
		}
	}

	function marketking_delete_team_member(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$team_member_id = sanitize_text_field($_POST['id']);
		
		$parent = get_user_meta($team_member_id,'marketking_parent_vendor', true);

		if (intval($parent) === get_current_user_id()){
			// delete user
			wp_delete_user($team_member_id);

		}

		exit();

	}

	function marketking_save_team_member(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$team_member_id = sanitize_text_field($_POST['id']);
		$panels = sanitize_text_field($_POST['panels']);

		$panel_slugs = explode(':', $panels);

		$parent = get_user_meta($team_member_id,'marketking_parent_vendor', true);

		if (intval($parent) === get_current_user_id()){
			foreach ($panel_slugs as $panel){
				$value = sanitize_text_field($_POST[$panel]);
				$value = filter_var($value,FILTER_VALIDATE_BOOLEAN);

				if ($value === true){
					update_user_meta($team_member_id, 'marketking_teammember_available_panel_'.$panel, 1);
				} else {
					update_user_meta($team_member_id, 'marketking_teammember_available_panel_'.$panel, 0);
				}
			}
		}

		echo $team_member_id;

		exit();

	}

	function marketkingsavecoupon(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		// if no products selected for coupon, abort (cheating attempt)
		if (empty($_POST['product_ids'])){
			wp_die();
		}

		/* verify settings - prevent tampering */
		// linked products
		$pro = defined('MARKETKINGPRO_DIR');

		// prevent fixed cart coupons
		if($_POST['discount_type'] === 'fixed_cart'){
			$_POST['discount_type'] = 'percent';
		}

		// menu order 
		$_POST['menu_order'] = 0;

		$id = sanitize_text_field($_POST['id']);
		$title = sanitize_text_field($_POST['title']);

		$allowed_html = wp_kses_allowed_html( 'post' );
		unset ( $allowed_html['textarea'] );
		array_walk_recursive(
		    $allowed_html,
		    function ( &$value ) {
		        if ( is_bool( $value ) ) {
		            $value = array();
		        }
		    }
		);
		// Run sanitization.
		$excerpt = wp_kses( $_POST['excerpt'], $allowed_html );
		$action = sanitize_text_field($_POST['actionedit']);
		$status = sanitize_text_field($_POST['marketking_edit_coupon_status']);

		if ($action === 'edit'){
			// check that current user is author of the product
			$author_id = get_post_field( 'post_author', $id );

			$current_id = get_current_user_id();
			if (marketking()->is_vendor_team_member()){
				$current_id = marketking()->get_team_member_parent();
			}

			if (intval($author_id) === $current_id || intval($author_id) === intval(get_current_user_id())){

				WC_Meta_Box_Coupon_Data::save($id, get_post($id));

				// update title
				$update_args = array(
				    'ID' => $id,
				    'post_title' => $title,
				    'post_excerpt'=> $excerpt,
				    'post_status' => $status,
				);
				$result = wp_update_post($update_args);

			}

			echo esc_html($id);

		} else if ($action === 'add'){

			$current_id = get_current_user_id();
			if (marketking()->is_vendor_team_member()){
				$current_id = marketking()->get_team_member_parent();
			}

			//create product first and set title
			$post_id = wp_insert_post( array(
				'post_title' => $title,
				'post_excerpt'=> $excerpt,
				'post_status' => $status,
				'post_type' => 'shop_coupon',
				'post_author' => $current_id
			));

			WC_Meta_Box_Coupon_Data::save($post_id, get_post($post_id));

			// return id of new product
			echo esc_html($post_id);
		}


		exit();

	}

	function marketkingsaveproduct(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		/* verify settings - prevent tampering */
		// linked products
		$pro = defined('MARKETKINGPRO_DIR');
		$can_linked = marketking()->vendor_can_linked_products($current_id);
		$can_purchase = intval(get_option( 'marketking_vendors_can_purchase_notes_setting', 1 ));
		$can_reviews = intval(get_option( 'marketking_vendors_can_reviews_setting', 1 ));


		if (!$pro || !$can_linked){
			// set linked products array to empty one
			$_POST['upsell_ids'] = array();
		}

		// product type
		if (!$pro){
			if ($_POST['product-type'] !== 'simple'){
				$_POST['product-type'] = 'simple';
			}
		}

		// purchase notes
		if (!$pro || !$can_purchase){
			// set linked products array to empty one
			$_POST['_purchase_note'] = '';
		}

		// all products virtual
		if (marketking()->vendor_all_products_virtual($current_id)){
			$_POST['_virtual'] = 1;
		}

		// all products downloadable
		if (marketking()->vendor_all_products_downloadable($current_id)){
			$_POST['_downloadable'] = 1;
		}

		// all products sold individually
		if (marketking()->vendor_all_products_individually($current_id)){
			$_POST['_sold_individually'] = 1;
		}
		
		// menu order 
		$_POST['menu_order'] = 0;

		$id = sanitize_text_field($_POST['id']);

		do_action('marketking_before_save_product', $id, $current_id);

		$title = sanitize_text_field($_POST['title']);
		$title = urldecode($title);
		$title = str_replace('*plus*', '+', $title);


		$allowed_html = wp_kses_allowed_html( 'post' );
		unset ( $allowed_html['textarea'] );
		array_walk_recursive(
		    $allowed_html,
		    function ( &$value ) {
		        if ( is_bool( $value ) ) {
		            $value = array();
		        }
		    }
		);
		// Run sanitization.
		$longexcerpt = wp_kses( $_POST['longexcerpt'], $allowed_html );
		$excerpt = wp_kses( $_POST['excerpt'], $allowed_html );
		$action = sanitize_text_field($_POST['actionedit']);

		if (isset($_POST['marketking_select_categories'])){
			if (is_array($_POST['marketking_select_categories'])){
				$arraycats = array_map('sanitize_text_field',$_POST['marketking_select_categories']);
			} else {
				$arraycats = array(sanitize_text_field($_POST['marketking_select_categories']));
			}
		}
		if (isset($_POST['marketking_select_tags'])){
			$arraytags = array_map('sanitize_text_field',$_POST['marketking_select_tags']);
		}
		$image_id = intval(sanitize_text_field($_POST['marketking_edit_product_main_image_value']));
		$status = sanitize_text_field($_POST['marketking_edit_product_status']);

		// if status is published, check that the user didn't cheat and that they have permission
		if ($status === 'publish'){
			// if vendor doesn't have permission, set it to draft
			if (!marketking()->vendor_can_publish_products($current_id)){
				$status = 'draft';
			}
		}

		// check that current user is author of the product
		$author_id = get_post_field( 'post_author', $id );

		if (apply_filters('marketking_default_product_save_process', true)){
			if (intval($author_id) === $current_id || intval($author_id) === intval(get_current_user_id())){

				// update categories tags
				$product=wc_get_product($id);

				// reviews
				if (!$pro || !$can_reviews){
					// reviews must be set to the default value of the product
					$val = $product->get_reviews_allowed() ? 'open' : 'closed';
					$_POST['comment_status'] = $val;
				}


				WC_Meta_Box_Product_Data::save($id, get_post($id));
				WC_Meta_Box_Product_Images::save($id, get_post($id));

				// update title
				$update_args = array(
				    'ID' => $id,
				    'post_title' => $title,
				    'post_content' => $longexcerpt,
				    'post_excerpt'=> $excerpt,
				    'post_status' => $status,
				    'post_name' => sanitize_title($title),
				    'post_author' => $current_id

				);
				$result = wp_update_post($update_args);

				// update categories tags
				$product=wc_get_product($id);


				if (isset($_POST['marketking_select_categories'])){
					$product->set_category_ids($arraycats);
				}
				if (isset($_POST['marketking_select_tags'])){
					$product->set_tag_ids($arraytags);
				}
				$product->set_image_id($image_id);
				$product->save();

				// save visibility
				if (defined('B2BKING_DIR') && defined('MARKETKINGPRO_DIR') && intval(get_option('marketking_enable_b2bkingintegration_setting', 1)) === 1){
					require_once ( B2BKING_DIR . 'admin/class-b2bking-admin.php' );
					if (!isset($b2bking_admin)){
					    $b2bking_admin = new B2bking_Admin;
					}
					$b2bking_admin->b2bking_product_visibility_meta_update($id);
				}

				echo esc_html($id);

			}
		}
		

		if (marketking()->is_on_vacation($current_id)){
			if (apply_filters('marketking_vacation_sets_visibility', true)){
				marketking()->set_vendor_products_visibility($current_id,'hidden');
			}
		}

		// Integrations
		if (!is_admin()){
			if (defined('WOO_VOU_PLUGIN_VERSION')){
				include ( MARKETKINGCORE_DIR . 'public/dashboard/integrations/woo_vou_pdf_vouchers.php' );
				include_once ( WOO_VOU_DIR . '/includes/admin/woo-vou-admin-functions.php' );
				$woo_vou = new Marketking_Woo_Vou;
				woo_vou_product_save_data( $id, get_post( $id ) );
			}
			
			// FooEvents
			if (class_exists('FooEvents_Config')){
				$config = new FooEvents_Config();
				require_once $config->class_path . 'class-fooevents-woo-helper.php';
				$woo_helper = new FooEvents_Woo_Helper( $config );
				$woo_helper->process_meta_box($id);
			}

			if ( class_exists( 'WC_Accommodation_Bookings_Plugin' ) ) {

				$wc_accommodation = new Marketking_WC_Accommodation_Booking_Metabox();
				$wc_accommodation->save_product_data( $id );
			}
		}	

		// Subscriptions
		if (class_exists('WC_Subscriptions')){
			//update_post_meta( $id, '_subscription_price', $_REQUEST['_subscription_price'] );
			add_filter('wcs_admin_is_subscription_product_save_request', function($is, $postid, $prod_types){
				if ($_POST['product-type'] === 'subscription' or $_POST['product-type'] === 'variable-subscription'){
					$is = true;
				}
				return $is;
			},10, 3);

			if ($_POST['product-type'] === 'subscription'){
		    	WC_Subscriptions_Admin::save_subscription_meta($id);
			}
			
			if ($_POST['product-type'] === 'variable-subscription'){
		    	WC_Subscriptions_Admin::save_variable_subscription_meta($id);
			}
			
		}	
		
		do_action('marketking_after_save_product', $id, $current_id);

		if (apply_filters('marketking_default_product_save_process', true)){

			global $post;
			if (isset($post)){
				$post->ID = $id;
			}

			$continue = 'yes';

			if (!isset($product) or !isset($id)){
				$continue = 'no';
			} else {
				if (empty($id) or empty($product)){
					$continue = 'no';
				}
			}

			if ($continue === 'yes'){
				do_action('woocommerce_process_product_meta', $id, $product);
			}

		}

		if ($status === 'pending'){
			do_action( 'marketking_new_product_requires_approval', $id);
		}

		// add product first time
		if ($action === 'add'){
			do_action('marketking_add_product_first', $id, $current_id); // product id, vendor id
		} 

		// woo 3dviewer integration
		if(defined('WOO3DV_VERSION')){
			if (isset($_POST['product_model'])) {
				woo3dv_save_model_meta($id, $_POST);
				woo3dv_save_model($id);
			}
		}

		// if this is the product, clear product standby
		marketking()->clear_product_standby($id);
		// create new one
		marketking()->set_product_standby();

		exit();

	}

	function marketking_products_table_ajax(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$start = sanitize_text_field($_POST['start']);
		$length = sanitize_text_field($_POST['length']);
		$search = sanitize_text_field($_POST['search']['value']);
		$pagenr = ($start/$length)+1;

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		$args = array( 
		    'posts_per_page' => -1,
		    'post_status'    => array( 'draft', 'pending', 'private', 'publish' ),
		    'post_type'		=> 'product',
		    'author'   => $current_id,
		    'fields' => 'ids',
		    's' => $search,
		);

		$total_items = get_posts( $args );
		$itemnr = count($total_items);
		
		$data = array(
			'length'=> $length,
			'data' => array(),
			'recordsTotal' => $itemnr,
			'recordsFiltered' => $itemnr
		);
		
		
		$args = array( 
		    'posts_per_page' => $length,
		    'post_status'    => 'any',
		    'post_type'		=> 'product',
		    'author'   => $current_id,
		    'paged'   => floatval($pagenr),
		    'fields' => 'ids',
		    's' => $search,
		    'orderby' => 'date',
	        'order' => 'DESC',
		);

		$vendor_products = get_posts( $args );

		foreach ($vendor_products as $productid){
			$product = wc_get_product($productid);

			$allowed_product = apply_filters('marketking_allowed_vendor_edit_product', true, $product);
			if (!$allowed_product){
			    continue;
			}
			
		    ?>
		    	<?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-sm marketking-column-large">
		            <a href="<?php 

		            if (apply_filters('marketking_vendors_can_edit_products', true)){

			            echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'edit-product/'.$product->get_id());

			        } else {
			        	echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'products');

			        }

		            ?>"><span class="tb-product"><?php
		                $src = wp_get_attachment_url( $product->get_image_id() );
		                if (empty($src)){
		                    $src = wc_placeholder_img_src();
		                }
		                $title = $product->get_title();
		                if (empty($title)){
		                    $title = '—';
		                }
		                $sku = $product->get_sku();
		                if (empty($sku)){
		                    $sku = '—';
		                }

		                $price = apply_filters('marketking_products_page_price', $product->get_price(), $product);

		                
		                $categories = $product->get_category_ids();
		                $categoriestext = '';
		                foreach ($categories as $cat){
		                    if( $term = get_term_by( 'id', $cat, 'product_cat' ) ){
		                        $categoriestext .= $term->name.', ';
		                    }
		                }
		                $categoriestext = substr($categoriestext, 0, -2);
		                if (empty($categoriestext)){
		                    $categoriestext = '—';
		                }

		                $tags = $product->get_tag_ids();
		                $tagstext = '';
		                foreach ($tags as $tag){
		                    if( $term = get_term_by( 'id', $tag, 'product_tag' ) ){
		                        $tagstext .= $term->name.', ';
		                    }
		                }
		                $tagstext = substr($tagstext, 0, -2);
		                if (empty($tagstext)){
		                    $tagstext = '—';
		                }
		                $type = ucfirst($product->get_type());
		                $time = $product->get_date_created();
		                if ($time === null){
		                    $time = $product->get_date_modified();
		                }

		                $timestamp = $time->getTimestamp();

		                $date = $time->date_i18n( get_option('date_format'), $timestamp+(get_option('gmt_offset')*3600) );
		                

		                ?><img src="<?php echo esc_attr($src);?>" alt="" class="thumb"><span class="title"><?php echo esc_html($title);?></span></span></a>
		        </td>
		        <?php $col1 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col">
		            <span class="tb-sub marketking-column-small"><?php echo esc_html($sku);?></span>
		        </td>
		        <?php $col2 = ob_get_clean(); ?>


		        <?php
		        $col_advertising = false;
		        // product advertisement
		        if (intval(get_option( 'marketking_enable_advertising_setting', 0 )) === 1){
		            if(marketking()->vendor_has_panel('advertising')){
		            	ob_start();
		                ?>
		                <td class="nk-tb-col tb-col-md marketking-column-mid" data-order="<?php 

		                    if (marketking()->is_advertised($product->get_id())){
		                        echo marketking()->get_ad_days_left_val($product->get_id());
		                    } else {
		                        echo '-1';
		                    }

		                ?>">
		                    <span class="tb-sub marketking-column-small">
		                        <?php 
		                        if (marketking()->is_advertised($product->get_id())){
		                            $daysleft = marketking()->get_ad_days_left($product->get_id());
		                            echo '<div class="marketking_advertised_column">'.$daysleft.' '.esc_html__('days left','marketking-multivendor-marketplace-for-woocommerce').'</div>';
		                            
		                        } else {
		                            echo '-';
		                        }
		                        ?></span>
		                </td>
		                <?php
		                $col_advertising = ob_get_clean();
		            }
		        }
		        ?>


		        <?php ob_start(); ?>
		        <td class="nk-tb-col marketking-column-small" data-order="<?php echo esc_attr($price);?>">
		            <span class="tb-lead"><?php 
		            if (!empty($price)){
		                echo wc_price($price);
		            } else {
		                echo '—';
		            }
		            ?></span>
		        </td>
		        <?php $col3 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col">
		            <?php
		            $stock = $product->get_stock_status();
		            $stocktext = $badge = '';
		            if ($stock === 'instock'){
		                $badge = 'badge-gray';
		                $stocktext = esc_html__('In stock', 'marketking-multivendor-marketplace-for-woocommerce');
		            } else if ($stock === 'outofstock'){
		                $badge = 'badge-warning';
		                $stocktext = esc_html__('Out of stock', 'marketking-multivendor-marketplace-for-woocommerce');
		            } else if ($stock === 'onbackorder'){
		                $badge = 'badge-gray';
		                $stocktext = esc_html__('On backorder', 'marketking-multivendor-marketplace-for-woocommerce');
		            }
		            ?>
		            <span class="badge badge-sm badge-dot has-bg <?php echo esc_attr($badge);?> d-none d-mb-inline-flex"><?php
		            echo esc_html(ucfirst($stocktext));
		            ?></span>
		        </td>
		        <?php $col4 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-md marketking-column-mid">
		            <span class="tb-sub"><?php echo esc_html($categoriestext);?></span>
		        </td>
		        <?php $col5 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-md marketking-column-small">
		            <span class="tb-sub"><?php echo esc_html($type);?></span>
		        </td>
		        <?php $col6 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-md marketking-column-small">
		            <?php
		            $status = get_post_status($product->get_id());
		            $statustext = $badge = '';
		            if ($status === 'publish'){
		                $badge = 'badge-success';
		                $statustext = esc_html__('Published','marketking-multivendor-marketplace-for-woocommerce');
		            } else if ($status === 'draft'){
		                $badge = 'badge-gray';
		                $statustext = esc_html__('Draft','marketking-multivendor-marketplace-for-woocommerce');
		            } else if ($status === 'pending'){
		                 $badge = 'badge-info';
		                 $statustext = esc_html__('Pending','marketking-multivendor-marketplace-for-woocommerce');
		            } else {
		                $badge = 'badge-gray';
		                $statustext = ucfirst($status);
		            }
		            ?>
		            <span class="badge badge-sm badge-dot has-bg <?php echo esc_attr($badge);?> d-none d-mb-inline-flex"><?php
		            echo esc_html(ucfirst($statustext));
		            ?></span>
		        </td>
		        <?php $col7 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-md marketking-column-mid">
		            <span class="tb-sub"><?php echo esc_html($tagstext);?></span>
		        </td>
		        <?php $col8 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-md marketking-column-mid" data-order="<?php echo esc_attr($timestamp);?>">
		            <span class="tb-sub"><?php echo esc_html($date);?></span>
		        </td>
		        <?php $col9 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col">
		            <ul class="nk-tb-actions gx-1 my-n1">
		                <li class="mr-n1">
		                    <div class="dropdown">
		                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
		                        <div class="dropdown-menu dropdown-menu-right">
		                            <ul class="link-list-opt no-bdr">
		                                <li><a href="<?php echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'edit-product/'.$product->get_id());?>"><em class="icon ni ni-edit"></em><span><?php esc_html_e('Edit Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
		                                <li><a target="_blank" href="<?php 
		                                $permalink = $product->get_permalink();
		                                echo esc_attr($permalink);
		                                ?>
		                                "><em class="icon ni ni-eye"></em><span><?php esc_html_e('View Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
		                                <?php
		                                if(intval(get_option( 'marketking_vendors_can_newproducts_setting',1 )) === 1){
		                                    if (apply_filters('marketking_vendors_can_add_products', true)){
		                                        // either not team member, or team member with permission to add
		                                        if (!marketking()->is_vendor_team_member()){
		                                            if(marketking()->vendor_can_add_more_products($current_id)){
		                                                ?>
		                                                <li><input type="hidden" class="marketking_input_id" value="<?php echo esc_attr($product->get_id());?>"><a href="#" class="marketking_clone_product"><em class="icon ni ni-copy-fill"></em><span><?php esc_html_e('Clone Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
		                                                <?php
		                                            }
		                                        }
		                                    }
		                                }
		                                ?>
		                                <li><a href="#" class="toggle marketking_delete_button" value="<?php echo esc_attr($product->get_id());?>"><em class="icon ni ni-trash"></em><span><?php esc_html_e('Delete Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
		                            </ul>
		                        </div>
		                    </div>
		                </li>
		            </ul>
		        </td>
		        <?php $col10 = ob_get_clean(); 

		        if ($col_advertising === false){
		        	array_push($data['data'],array($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col10));
		        } else {
		        	array_push($data['data'],array($col1, $col_advertising, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col10));
		        }

		        ?>

		    <?php
		}
		
		echo json_encode($data);

		exit();
	}

	function marketking_orders_table_ajax(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}


		$start = sanitize_text_field($_POST['start']);
		$length = sanitize_text_field($_POST['length']);
		$search = sanitize_text_field($_POST['search']['value']);
		$pagenr = ($start/$length)+1;

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}


		// get total nr of records
		$args = array( 
		    'posts_per_page' => -1,
		    'post_status'    => 'any',
		    'post_type'		=> 'shop_order',
		    'author'   => $current_id,
		    'fields' => 'ids',
		    's' => $search,
		);

		$total_orders = get_posts( $args );
		$itemnr = count($total_orders);

		$data = array(
			'length'=> $length,
			'data' => array(),
			'recordsTotal' => $itemnr,
			'recordsFiltered' => $itemnr,
		);

	
		$args = array( 
		    'posts_per_page' => $length,
		    'post_status'    => 'any',
		    'post_type'		=> 'shop_order',
		    'author'   => $current_id,
		    'paged'   => floatval($pagenr),
		    'fields' => 'ids',
		    's' => $search,
		);

		$vendor_orders = get_posts( $args );



		foreach ($vendor_orders as $orderid){
			$orderobj = wc_get_order($orderid);

			if ($orderobj !== false){
			    ?>	
		    	<?php ob_start(); ?>
		        <td class="nk-tb-col">
		            <a href="<?php echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'manage-order/'.$orderobj->get_id());?>">

		                <div>
		                    <span class="tb-lead">#<?php 

		                    $order_id = $orderobj->get_id();

		                    // sequential
		                    $order_nr_sequential = $orderobj->get_meta('_order_number');
		                    if (!empty($order_nr_sequential)){
		                        echo $order_nr_sequential;
		                    } else {
		                        echo esc_html($order_id);
		                    }

		                    echo ' '.$orderobj->get_formatted_billing_full_name();

		                    // subscription renewal
		                    $renewal = $orderobj->get_meta('_subscription_renewal');
		                    if (!empty($renewal)){
		                        echo ' ('.esc_html__('susbcription renewal', 'marketking-multivendor-marketplace-for-woocommerce').')';
		                    }


		                ?></span>
		                </div>
		            </a>
		        </td>
		        <?php $col1 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-md" data-order="<?php
		            $date = $orderobj->get_date_created();
		            echo $date->getTimestamp();
		        ?>">
		            <div>
		                <span class="tb-sub"><?php 
		                
		                 echo $date->date_i18n( get_option('date_format'), $date->getTimestamp()+(get_option('gmt_offset')*3600) );
		                ?></span>
		            </div>
		        </td>
		        <?php $col2 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col"> 
		            <div >
		                <span class="dot bg-warning d-mb-none"></span>
		                <?php
		                $status = $orderobj->get_status();
		                $statustext = $badge = '';
		                if ($status === 'processing'){
		                    $badge = 'badge-success';
		                    $statustext = esc_html__('Processing','marketking-multivendor-marketplace-for-woocommerce');
		                } else if ($status === 'on-hold'){
		                    $badge = 'badge-warning';
		                    $statustext = esc_html__('On Hold','marketking-multivendor-marketplace-for-woocommerce');
		                } else if (in_array($status,apply_filters('marketking_earning_completed_statuses', array('completed')))){
		                    $badge = 'badge-info';
		                    $statustext = esc_html__('Completed','marketking-multivendor-marketplace-for-woocommerce');
		                } else if ($status === 'refunded'){
		                    $badge = 'badge-gray';
		                    $statustext = esc_html__('Refunded','marketking-multivendor-marketplace-for-woocommerce');
		                } else if ($status === 'cancelled'){
		                    $badge = 'badge-gray';
		                    $statustext = esc_html__('Cancelled','marketking-multivendor-marketplace-for-woocommerce');
		                } else if ($status === 'pending'){
		                    $badge = 'badge-dark';
		                    $statustext = esc_html__('Pending Payment','marketking-multivendor-marketplace-for-woocommerce');
		                } else if ($status === 'failed'){
		                    $badge = 'badge-danger';
		                    $statustext = esc_html__('Failed','marketking-multivendor-marketplace-for-woocommerce');
		                }
		                ?>
		                <span class="badge badge-sm badge-dot has-bg <?php echo esc_attr($badge);?> d-none d-mb-inline-flex"><?php
		                echo esc_html($statustext);
		                ?></span>
		            </div>
		        </td>
		        <?php $col3 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-sm">
		            <div>
		                 <span class="tb-sub"><?php
		                 $name = $orderobj -> get_formatted_billing_full_name();


		                 echo $name;
		                 ?></span>
		            </div>
		        </td>
		        <?php $col4 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-md"> 
		            <div>
		                <span class="tb-sub text-primary"><?php
		                $items = $orderobj->get_items();
		                $items_count = count( $items );
		                if ($items_count > apply_filters('marketking_dashboard_item_count_limit', 4)){
		                    echo esc_html($items_count).' '.esc_html__('Items', 'marketking-multivendor-marketplace-for-woocommerce');
		                } else {
		                    // show the items
		                    foreach ($items as $item){
		                        echo apply_filters('marketking_item_display_dashboard', $item->get_name().' x '.$item->get_quantity().'<br>', $item);
		                    }
		                }
		                ?></span>
		            </div>
		        </td>
		        <?php $col5 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col" data-order="<?php echo esc_attr($orderobj->get_total());?>"> 
		            <div>
		                <span class="tb-lead"><?php echo wc_price($orderobj->get_total());?></span>
		            </div>
		        </td>
		        <?php $col6 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col" data-order="<?php echo esc_attr(marketking()->get_order_earnings($orderobj->get_id()));?>"> 
		            <div>
		                <span class="tb-lead"><?php 
		                $earnings = marketking()->get_order_earnings($orderobj->get_id());
		                if ($earnings === 0){
		                    echo '—';
		                } else {
		                    echo wc_price($earnings);
		                }

		                $tax_fee_recipient = get_post_meta($orderobj->get_id(),'tax_fee_recipient', true);
		                if (empty($tax_fee_recipient)){
		                    $tax_fee_recipient = get_option('marketking_tax_fee_recipient_setting', 'vendor');
		                }
		                if ($tax_fee_recipient === 'vendor'){
		                	$tax = $orderobj->get_total_tax();
		                	if (floatval($tax) > 0){
		                		echo ' ('.esc_html__('tax','marketking-multivendor-marketplace-for-woocommerce').' '.wc_price($tax).')';
		                	}
		                }

		                ?></span>
		            </div>
		        </td>
		        <?php $col7 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
                <td class="nk-tb-col">
                    <div class="marketking_manage_order_container"> 

                        <a href="<?php echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'manage-order/'.$orderobj->get_id());?>"><button class="btn btn-sm btn-dim btn-secondary marketking_manage_order" value="<?php echo esc_attr($orderobj->get_id());?>"><em class="icon ni ni-bag-fill"></em><span><?php esc_html_e('View Order','marketking-multivendor-marketplace-for-woocommerce');?></span></button></a>
                    </div>
                </td>
		        <?php
		        $col8 = ob_get_clean();

	        	array_push($data['data'],array($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8));

		    }

		}
		
		echo json_encode($data);

		exit();
	}

	function marketking_get_commission_invoice(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

  		$order_id = sanitize_text_field($_GET['orderid']);
  		$vendor_id = get_current_user_id();

  		// check that order belongs to vendor, else wp die
  		$order_vendor = marketking()->get_order_vendor($order_id);

		if (intval($vendor_id) !== intval($order_vendor) && ! current_user_can( 'manage_woocommerce' ) ){
			wp_die(); // trying to download invoice which doesn't belong to them
		}
  		

		$document_type = 'invoice';
		
		// disable deprecation notices during email sending
		add_filter( 'wcpdf_disable_deprecation_notices', '__return_true' );
		
		// WooCommerce Cost of Good Fix
		if ( class_exists( 'Alg_WC_Cost_of_Goods_Core' ) ) {
			remove_all_actions( 'woocommerce_before_order_itemmeta' );
		}
		
		// Process Template
		$order = wc_get_order( $order_id );
		
		// reload translations because WC may have switched to site locale (by setting the plugin_locale filter to site locale in wc_switch_to_site_locale())
		WPO_WCPDF()->translations();
		do_action( 'wpo_wcpdf_reload_attachment_translations' );
		
		// prepare document
		$document = wcpdf_get_document( $document_type, (array) $order_id, true );
		if( !$document ) { return; }
		
		do_action( 'wpo_wcpdf_process_template', $document_type, $document );
		
		do_action( 'wpo_wcpdf_before_pdf', $document_type, $document );
		

		$template = MARKETKINGCORE_DIR . 'public/templates/invoices/commission-invoice.php';

		ob_start();

		if (file_exists($template)) {
			include($template);
		}
		$output_body = ob_get_clean();

		// Fetching tempplate wrapper
		$template_wrapper = MARKETKINGCORE_DIR . 'public/templates/invoices/html-document-wrapper.php';
		ob_start();
		if (file_exists($template_wrapper)) {
			include($template_wrapper);
		}
		$complete_document = ob_get_clean();
		unset($output_body);
		
		// clean up special characters
		$complete_document = utf8_decode(mb_convert_encoding($complete_document, 'HTML-ENTITIES', 'UTF-8'));
		
		$invoice_settings = array(
			'paper_size'		    => 'A4',
			'paper_orientation'	=> 'portrait',
			'font_subsetting'	  => true
		);
		
		$pdf_maker = wcpdf_get_pdf_maker( $complete_document, $invoice_settings );
		$pdf = $pdf_maker->output();
		
		do_action( 'wpo_wcpdf_after_pdf', $document_type, $document );
		
		$filename = esc_html__( 'invoice', 'marketking-multivendor-marketplace-for-woocommerce' ) . '-' . $order_id . '.pdf';

		do_action( 'wpo_wcpdf_created_manually', $pdf, $filename );

		// Get output setting
		$output_mode = 'download'; //isset($general_settings['download_display']) ? $general_settings['download_display'] : '';

		// Set PDF output header 
		wcpdf_pdf_headers( $filename, $output_mode, $pdf );

		// output PDF data
		echo($pdf);
		die;
	
		exit();
	}


	function marketking_save_profile_info(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		$user_id = $current_id;

		do_action('marketking_save_profile_info_start', $user_id);

		$address1 = sanitize_text_field($_POST['address1']);
		$address2 = sanitize_text_field($_POST['address2']);
		$postcode = sanitize_text_field($_POST['postcode']);
		$city = sanitize_text_field($_POST['city']);
		$state = sanitize_text_field($_POST['state']);
		$country = sanitize_text_field($_POST['country']);

		$aboutusraw = $_POST['aboutus'];
		$allowed = array('<h2>','</h2>','<h3>','<h4>','<i>','<strong>','</h3>','</h4>','</i>','</strong>');
		if (apply_filters('marketking_aboutus_allow_youtube', true)){
			array_push($allowed, '<youtube>');
			array_push($allowed, '</youtube>');
		}
		$replaced = array('***h2***','***/h2***','***h3***','***h4***','***i***','***strong***','***/h3***','***/h4***','***/i***','***/strong***');
		if (apply_filters('marketking_aboutus_allow_youtube', true)){

			array_push($replaced, '***youtube***');
			array_push($replaced, '***/youtube***');

			//array_push($replaced, '<iframe width="500" height="281" src="');
			//array_push($replaced, '" frameborder="0" allowfullscreen=""></iframe>');
		}

		$aboutusraw = str_replace($allowed, $replaced, $aboutusraw);
		$aboutus = sanitize_textarea_field($aboutusraw);

		update_user_meta($user_id, 'marketking_store_aboutus', $aboutus);

		update_user_meta($user_id, 'billing_address_1', $address1);
		update_user_meta($user_id, 'billing_address_2', $address2);
		update_user_meta($user_id, 'billing_city', $city);
		update_user_meta($user_id, 'billing_postcode', $postcode);
		update_user_meta($user_id, 'billing_state', $state);
		update_user_meta($user_id, 'billing_country', $country);

		$fn = sanitize_text_field($_POST['firstname']);
		$ln = sanitize_text_field($_POST['lastname']);
		$cn = sanitize_text_field($_POST['companyname']);
		$em = sanitize_text_field($_POST['emailad']);
		$ph = sanitize_text_field($_POST['phone']);
		$sn = sanitize_text_field($_POST['storename']);
		$profileimage = sanitize_text_field($_POST['profileimage']);
		$bannerimage = sanitize_text_field($_POST['bannerimage']);

		$showphone = sanitize_text_field($_POST['showphone']);
		$showemail = sanitize_text_field($_POST['showemail']);
		$showemail = filter_var($showemail,FILTER_VALIDATE_BOOLEAN);
		$showphone = filter_var($showphone,FILTER_VALIDATE_BOOLEAN);

		update_user_meta( $user_id, 'marketking_profile_logo_image', $profileimage);	
		update_user_meta( $user_id, 'marketking_profile_logo_image_banner', $bannerimage);	

		if ($showphone === true){
			update_user_meta($user_id, 'marketking_show_store_phone', 'yes');
		} else {
			update_user_meta($user_id, 'marketking_show_store_phone', 'no');
		}

		if ($showemail === true){
			update_user_meta($user_id, 'marketking_show_store_email', 'yes');
		} else {
			update_user_meta($user_id, 'marketking_show_store_email', 'no');
		}

		$maxstorelength = apply_filters('marketking_store_name_max_length', 25);

		if(strlen($sn) > $maxstorelength){
			// max 25 characters
			$sn = substr($sn, 0, $maxstorelength);
		}

		update_user_meta($user_id, 'first_name', $fn);
		update_user_meta($user_id, 'last_name', $ln);
		update_user_meta($user_id, 'billing_company', $cn);
		update_user_meta($user_id, 'billing_phone', $ph);
		update_user_meta($user_id, 'marketking_store_name', $sn);

		wp_update_user( array( 'ID' => $user_id, 'user_email' => $em ) );

		do_action('marketking_save_profile_info_end', $user_id);

		echo 'success';
		exit();
	}

	function marketkingsavemodules(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$sluglist = sanitize_text_field($_POST['sluglist']);
		$sluglist = explode('-', $sluglist);
		foreach ($sluglist as $module_slug){
			if (!empty($module_slug)){
				if (isset($_POST[$module_slug])){
					$value = sanitize_text_field($_POST[$module_slug]);
					$value = filter_var($value,FILTER_VALIDATE_BOOLEAN);

					if ($value === true){
						update_option('marketking_enable_'.$module_slug.'_setting', 1);
					} else {
						update_option('marketking_enable_'.$module_slug.'_setting', 0);
					}
				}
			}
		}

		echo 'success';
		exit();
	}

	function marketkingsaveorder(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$id = sanitize_text_field($_POST['id']);
		$status = sanitize_text_field($_POST['status']);
		
		// verify the order belongs to the user
		$vendor_id = get_post_field( 'post_author', $id );

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		if (intval($vendor_id) === $current_id){
			// verify the user has permission to change status
			if (marketking()->vendor_can_change_order_status($current_id)){
				$order = wc_get_order($id);
				$order->update_status($status, '', true);
				$order->save();

				$params = array();
				parse_str($_POST['formdata'], $params);

				do_action('marketking_order_save', $id, get_post($id), $params);

				// ADMIN CUSTOM FIELDS INTEGRATION START
				$updated_custom_fields = isset( $params['wc-admin-custom-order-fields'] ) ? $params['wc-admin-custom-order-fields'] : null;
				if ( !empty( $updated_custom_fields ) ) {
						
					$order_fields = wc_admin_custom_order_fields()->get_order_fields();

					foreach ( $order_fields as $custom_field ) {

						$field_id       = $custom_field->get_id();
						$field_meta_key = $custom_field->get_meta_key();
						$updated_value  = isset( $updated_custom_fields[ $field_id ] ) ? $updated_custom_fields[ $field_id ] : '';

						// Update a custom field value unless it's empty...
						// A value of 0 is valid, so check for that first.
						// Empty string is also allowed to clear out custom fields completely.
						if ( '0' === $updated_value || '' === $updated_value || ! empty( $updated_value ) ) {

							// Special handling for date fields.
							if ( 'date' === $order_fields[ $field_id ]->get_type() ) {

								$updated_value = strtotime( $updated_value );

								$order_fields[ $field_id ]->set_value( $updated_value );

								$order->update_meta_data( $field_meta_key, $order_fields[ $field_id ]->get_value() );

								// This column is used so that date fields can be searchable.
								$order->update_meta_data( $field_meta_key . '_formatted', $order_fields[ $field_id ]->get_value_formatted() );

							} else {

								$order->update_meta_data( $field_meta_key, $updated_value );
							}

						// ...Or if it's empty, delete the custom field meta altogether.
						} else {

							$order->delete_meta_data( $field_meta_key );
							$order->delete_meta_data( $field_meta_key . '_formatted' );
						}

						$order->save_meta_data();
					}
				}
				$order->save();

				// ADMIN CUSTOM FIELDS INTEGRATION END

			}
		}


		echo $id;

		exit();
	}

	function marketkingsendsupport(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$product_id = sanitize_text_field($_POST['product']);
		$vendor_id = sanitize_text_field($_POST['vendor']);
		$order_id = sanitize_text_field($_POST['order']);

		// check vendor option
		$support_option = get_user_meta($vendor_id,'marketking_support_option', true);
		if (empty($support_option)){
			$support_option = 'messaging';
		}

		$support_email = get_user_meta($vendor_id,'marketking_support_email', true);
		if ($support_option === 'messaging'){
			// in the case of messaging, there is not a dedicated support email address. The store email is used.
			$support_email = marketking()->get_vendor_email($vendor_id);
		}

		// build email
		if (!empty($product_id)){
			$product = wc_get_product($product_id);
			$productname = $product->get_formatted_name();
			$message = esc_html__( 'Product: ', 'marketking-multivendor-marketplace-for-woocommerce' ) . $productname . ' <br />'.apply_filters('marketking_filter_message_general',sanitize_textarea_field( $_POST['message'] )).'<br>';
		} else if (!empty($order_id)){

			$message = esc_html__( 'Order: #', 'marketking-multivendor-marketplace-for-woocommerce' ) . esc_html($order_id) . ' <br />'.apply_filters('marketking_filter_message_general',sanitize_textarea_field( $_POST['message'] )).'<br>';
		} else {
			$message = apply_filters('marketking_filter_message_general',sanitize_textarea_field( $_POST['message'] ) ).'<br>';
		}
		

		$user = wp_get_current_user();
		$currentuser = $user->user_login;

		// add user to message email
		$message = esc_html__('User:', 'marketking-multivendor-marketplace-for-woocommerce').' '.$user->first_name.' '.$user->last_name.' ('.$user->user_email.')<br>'.$message;

		do_action('marketking_new_message', $support_email, $message, $vendor_id, 'support');

		if ($support_option === 'email'){
			// in the case of email, there is no messaging conversation to redirect to, therefore we echo 0
			echo 0;

		} else if ($support_option === 'messaging'){
			$vendorobj = new WP_User($vendor_id);

			$custom_discussion_info = esc_html__('The user sent this support request from the order page.','marketking-multivendor-marketplace-for-woocommerce');

			// Insert post
	    	$args         = array(
	    		'post_title'  => esc_html__('Support Request','marketking-multivendor-marketplace-for-woocommerce'),
	    		'post_type'   => 'marketking_message',
	    		'post_status' => 'publish',
	    	);
	    	$discussionid = wp_insert_post( $args );

	    	// set it as customer query
	    	// if the person asking the question is not admin
	    	if(!current_user_can('manage_woocommerce')){
	    		// if vendor is not the admin, we add a mark to not show it in the admin backend
	    		if (!$vendorobj->has_cap('manage_woocommerce')){
	    			update_post_meta($discussionid,'customer_query_non_admin', 'yes');
	    		}
	    	}

	    	update_post_meta( $discussionid, 'marketking_message_user', $vendorobj->user_login );
	    	update_post_meta( $discussionid, 'marketking_message_status', 'new' );
	    	update_post_meta( $discussionid, 'marketking_message_type', 'support' );
	    	update_post_meta( $discussionid, 'marketking_message_message_1', $message );
	    	update_post_meta( $discussionid, 'marketking_message_messages_number', 1 );
	    	update_post_meta( $discussionid, 'marketking_message_message_1_author', $currentuser );
	    	update_post_meta( $discussionid, 'marketking_message_message_1_time', time() );
	    	update_post_meta( $discussionid, 'marketking_custom_discussion_info', $custom_discussion_info );
	    	
	    	echo esc_url(add_query_arg('id', $discussionid, wc_get_account_endpoint_url(get_option('marketking_message_endpoint_setting','message'))));
		}
		
	

		exit();
	}

	function marketkingshipmentreceived(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$orderid = sanitize_text_field($_POST['orderid']);

		$order = wc_get_order($orderid);
		$ordercus = $order->get_customer_id();
		// security check, order belongs to user
		if (intval($ordercus) === intval(get_current_user_id())){
			// proceed
			update_post_meta($orderid,'marked_received', 'yes');

			// complete order automatically when customer marked it received it
			$status = 'wc-completed';
			$order->update_status($status, '', true);
			$order->save();
		}

		exit();
	}

	function marketkingcreateshipment(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$orderid = sanitize_text_field($_POST['orderid']);
		$provider = sanitize_text_field($_POST['provider']);
		$trackingnr = sanitize_text_field($_POST['trackingnr']);
		$trackingurl = sanitize_text_field($_POST['trackingurl']);

		update_post_meta($orderid,'has_shipment','yes');

		if ($provider !== 'sp-other'){
			// get tracking URL automatically
			$providers = marketkingpro()->get_tracking_providers();
			$urltemplate = $providers[$provider]['url'];
			$trackingurl = str_replace('{tracking_number}', $trackingnr, $urltemplate);			
		}

		$shipment = array('provider' => $provider, 'trackingnr' => $trackingnr, 'trackingurl' => $trackingurl);

		// add order note with shipment
		$order_note = esc_html__('A new shipment was created.','marketking-multivendor-marketplace-for-woocommerce').'<br>';

		if ($provider !== 'sp-other'){
			$order_note.= esc_html__('Provider: ','marketking-multivendor-marketplace-for-woocommerce').$providers[$shipment['provider']]['label'].'<br>';
		} else {
			$providername = sanitize_text_field($_POST['providername']);
			$order_note.= esc_html__('Provider: ','marketking-multivendor-marketplace-for-woocommerce').$providername.'<br>';
			$shipment['providername'] = $providername;

		}
		$order_note.= esc_html__('Tracking Number: ','marketking-multivendor-marketplace-for-woocommerce').$trackingnr.'<br>';
		$order_note.= esc_html__('Tracking URL: ','marketking-multivendor-marketplace-for-woocommerce').'<a href="'.$trackingurl.'">'.esc_html__('Click to track shipment','marketking-multivendor-marketplace-for-woocommerce').'</a>';

		$order = wc_get_order($orderid);
		$order->add_order_note($order_note, 1);

		// add shipment to shipments history
		$shipment_history = get_post_meta($orderid,'marketking_shipment_history', true);
		if (empty($shipment_history)){
			$shipment_history = array();
		}

		array_push($shipment_history, $shipment);
		update_post_meta($orderid,'marketking_shipment_history', $shipment_history);

		exit();
	}

	function marketkingsendinquiry(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		// 1. first of all, send vendor email with inquiry
		$messagecart = '';

		$vendor_id = sanitize_text_field($_POST['vendor']);

		if(isset($_POST['product'])){
			$product_id = sanitize_text_field($_POST['product']);
			if ($vendor_id === $product_id){
				// this inquiry is for a product, and the vendor ID needs to be recalculated
				$vendor_id = marketking()->get_product_vendor($product_id);
			}
		}
		

		$vendor_email = marketking()->get_vendor_email($vendor_id);
		$vendorobj = new WP_User($vendor_id);
		

		if ( isset( $_POST['name'] ) ) {
			$name         = sanitize_text_field( $_POST['name'] );
			$messagecart .= esc_html__( 'Name: ', 'marketking-multivendor-marketplace-for-woocommerce' ) . $name . ' <br />';
		}
		if ( isset( $_POST['email'] ) ) {
			$email        = sanitize_text_field( $_POST['email'] );
			$messagecart .= esc_html__( 'Email: ', 'marketking-multivendor-marketplace-for-woocommerce' ) . $email . ' <br />';
		}
		if ( isset( $_POST['phone'] ) ) {
			if (!empty($_POST['phone'])){
				$phone        = sanitize_text_field( $_POST['phone'] );
				$messagecart .= esc_html__( 'Phone: ', 'marketking-multivendor-marketplace-for-woocommerce' ) . $phone . ' <br />';
			}
		}
			
		if ( isset( $_POST['message'] ) ) {
			$message      = apply_filters('marketking_filter_message_general',sanitize_textarea_field( $_POST['message'] ));
			if(isset($_POST['product'])){
				$product = wc_get_product($product_id);
				$productname = $product->get_formatted_name();
				$message = esc_html__( 'Product: ', 'marketking-multivendor-marketplace-for-woocommerce' ) . $productname . ' <br />'.$message;
				$messagecart .= $message . ' <br />';
			} else {
				$messagecart .= esc_html__( 'Message: ', 'marketking-multivendor-marketplace-for-woocommerce' ) . $message . ' <br />';
			}
		}

		// if quote request is made by guest or B2C
		if (!is_user_logged_in()){
			$currentuser = sanitize_text_field(esc_html__('Name:', 'marketking-multivendor-marketplace-for-woocommerce')).' '.$name.' '.sanitize_text_field(esc_html__(' Email:', 'marketking-multivendor-marketplace-for-woocommerce')).' '.$email;
			$messagecartemail = $messagecart;
		} else {
			$user = wp_get_current_user();
			$currentuser = $user->user_login;

			$userdetails = apply_filters('marketking_inquiry_user', $user->first_name.' '.$user->last_name.' ('.$user->user_email.')', $user);
			
			// add user to message email
			$messagecartemail = esc_html__('User:', 'marketking-multivendor-marketplace-for-woocommerce').' '.$userdetails.'<br>'.$messagecart;

		}

		
		// send email notification
		$recipient = apply_filters('marketking_inquiry_email_recipient', $vendor_email, $vendor_id);


		$custom_discussion_info = '';
		// if this is a product inquiry from a logged in user, include whether the user purchased the product or not.
		if (is_user_logged_in()){
			if ( isset( $_POST['message'] ) && isset($_POST['product'])){
				if ( ! marketking()->customer_has_purchased( '', get_current_user_id(), $product_id ) ) {

					$messagecartemail .= '<br>'.esc_html__('The user has not purchased this product.','marketking-multivendor-marketplace-for-woocommerce');
					$custom_discussion_info = esc_html__('The user has not purchased this product.','marketking-multivendor-marketplace-for-woocommerce');
				} else {

					$messagecartemail .= '<br>'.esc_html__('The user has purchased this product.','marketking-multivendor-marketplace-for-woocommerce');
					$custom_discussion_info = esc_html__('The user has purchased this product.','marketking-multivendor-marketplace-for-woocommerce');

				}
			} 
		}

		do_action('marketking_new_message', $recipient, $messagecartemail, $vendor_id, 'inquiry');


		// 2. if messaging system is enabled, also create a conversation
		$discussionid = 0;
		if (intval(get_option( 'marketking_enable_messages_setting', 1 )) === 1){
		    if (intval( get_option( 'marketking_inquiries_use_messaging_setting', 1 ) ) ){
		    	// Insert post
		    	$args         = array(
		    		'post_title'  => esc_html__('Inquiry','marketking-multivendor-marketplace-for-woocommerce'),
		    		'post_type'   => 'marketking_message',
		    		'post_status' => 'publish',
		    	);
		    	$discussionid = wp_insert_post( $args );

		    	// set it as customer query
		    	// if the person asking the question is not admin
		    	if(!current_user_can('manage_woocommerce')){
		    		// if vendor is not the admin, we add a mark to not show it in the admin backend
		    		if (!$vendorobj->has_cap('manage_woocommerce')){
		    			update_post_meta($discussionid,'customer_query_non_admin', 'yes');
		    		}
		    	}

		    	update_post_meta( $discussionid, 'marketking_message_user', $vendorobj->user_login );
		    	update_post_meta( $discussionid, 'marketking_message_status', 'new' );
		    	update_post_meta( $discussionid, 'marketking_message_type', 'inquiry' );
		    	update_post_meta( $discussionid, 'marketking_message_message_1', $message );
		    	update_post_meta( $discussionid, 'marketking_message_messages_number', 1 );
		    	update_post_meta( $discussionid, 'marketking_message_message_1_author', $currentuser );
		    	update_post_meta( $discussionid, 'marketking_message_message_1_time', time() );
		    	update_post_meta( $discussionid, 'marketking_custom_discussion_info', $custom_discussion_info );

		    	if (!is_user_logged_in()){
	    			update_post_meta( $discussionid, 'marketking_message_message_2', sanitize_text_field(esc_html__('This inquiry was sent by a logged out user, without an account. Please email the user directly!', 'marketking-multivendor-marketplace-for-woocommerce')));
	    			update_post_meta( $discussionid, 'marketking_message_messages_number', 2);
	    			update_post_meta( $discussionid, 'marketking_message_message_2_author', esc_html__('System Message','marketking-multivendor-marketplace-for-woocommerce') );
	    			update_post_meta( $discussionid, 'marketking_message_message_2_time', time() );
		    	}

		    }
		}

		if ($discussionid === 0){
			echo 0;
		} else {
			echo esc_url(add_query_arg('id', $discussionid, wc_get_account_endpoint_url(get_option('marketking_message_endpoint_setting','message'))));
		}

		exit();
	}

	function marketkingcheckurlexists(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$url = sanitize_text_field($_POST['url']);
		if (marketking()->store_url_exists($url)){
			echo 'yes';
		} else {
			echo 'no';
		}

		exit();
	}

	function marketkingupdateuserdata(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$user_id = sanitize_text_field($_POST['userid']);
		$fields_string = sanitize_text_field($_POST['field_strings']);
		$fields_array = explode(',',$fields_string);
		foreach ($fields_array as $field_id){
			if ($field_id !== NULL && !empty($field_id)){

				// first check if field is VAT, then update user meta if field not empty
				$billing_connection = get_post_meta($field_id,'marketking_field_billing_connection', true);
				if ($billing_connection !== 'billing_vat'){
					// proceed normally,this is not VAT
					update_user_meta($user_id, 'marketking_field_'.$field_id, sanitize_text_field($_POST['field_'.$field_id]));
				} else {
					// check if VIES is enabled
					$vies_enabled = get_post_meta($field_id, 'marketking_field_VAT_VIES_validation', true);
					
					if (intval($vies_enabled) === 1){
						// run VIES check on the data
						$vatnumber = sanitize_text_field($_POST['field_'.$field_id]);
						$vatnumber = strtoupper(str_replace(array('.', ' '), '', $vatnumber));

						$client = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
						$country_code = substr($vatnumber, 0, 2); // take first 2 chars
						$vat_number = substr($vatnumber, 2); // remove first 2 chars

						$validation = new \stdClass();
						$validation->valid = false;
						
						// check vat
						try {
							$validation = $client->checkVat(array(
							  'countryCode' => $country_code,
							  'vatNumber' => $vat_number
							));

						} catch (Exception $e) {
							$error = $e->getMessage();
							$validation->valid=0;
						}

						$countries_list_eu = array('AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE');
						if (!in_array($country_code, $countries_list_eu)){
							$validation->valid = 1;
						}

						if (intval($validation->valid) === 1){
							// update data
							update_user_meta($user_id, 'marketking_field_'.$field_id, $vatnumber);
							// also set validated vat
							update_user_meta( $user_id, 'marketking_user_vat_status', 'validated_vat');
						} else {
							echo 'vatfailed';
						}
					} else {
						update_user_meta($user_id, 'marketking_field_'.$field_id, sanitize_text_field($_POST['field_'.$field_id])); 
					}
				}
			}
		}

		echo 'success';
		exit();
	}
	
	function marketkingapproveuser(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		// If nonce verification didn't fail, run further

		$user_id = sanitize_text_field($_POST['user']);
		$group = sanitize_text_field($_POST['chosen_group']);

		if (isset($_POST['credit'])){
			$creditlimit = sanitize_text_field($_POST['credit']);
			if (!empty($creditlimit)){
				update_user_meta($user_id,'marketking_user_credit_limit',$creditlimit);
			}
		}
		
		// approve account
		update_user_meta($user_id, 'marketking_account_approved', 'yes');

		if ($group !== 'b2c'){
			// place user in customer group 
			update_user_meta($user_id, 'marketking_group', $group);

			if (apply_filters('marketking_use_wp_roles', false)){
				// add role
				$user_obj = new WP_User($user_id);
				$user_obj->add_role('marketking_role_'.$group);
			}
			// set user as b2b enabled
			update_user_meta($user_id, 'marketking_b2buser', 'yes');

			// create action hook to send "account approved" email
			$email_address = sanitize_text_field(get_user_by('id', $user_id)->user_email);
			do_action( 'marketking_account_approved_finish', $email_address );

		} else {
			// b2c user
			if (apply_filters('marketking_use_wp_roles', false)){
				// add role
				$user_obj = new WP_User($user_id);
				$user_obj->add_role('marketking_role_'.$group);
			}
			// set user as b2b enabled
			update_user_meta($user_id, 'marketking_b2buser', 'no');
		}


	

		echo 'success';
		exit();	
	}

	function marketkingdeactivateuser(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		// If nonce verification didn't fail, run further

		$user_id = sanitize_text_field($_POST['user']);

		// approve account
		update_user_meta($user_id, 'marketking_account_approved', 'no');
		update_user_meta($user_id, 'marketking_b2buser', 'no');

		echo 'success';
		exit();	
	}

	function marketkingrejectuser(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		// If nonce verification didn't fail, run further
		$user_id = sanitize_text_field($_POST['user']);

		// check if this function is being run by delete subaccount in the frontend
		if(isset($_POST['issubaccount'])){
			$current_user = get_current_user_id();
			// remove subaccount from user meta
			$subaccounts_number = get_user_meta($current_user, 'marketking_subaccounts_number', true);
			$subaccounts_number = $subaccounts_number - 1;
			update_user_meta($current_user, 'marketking_subaccounts_number', sanitize_text_field($subaccounts_number));

			$subaccounts_list = get_user_meta($current_user, 'marketking_subaccounts_list', true);
			$subaccounts_list = str_replace(','.$user_id,'',$subaccounts_list);
			update_user_meta($current_user, 'marketking_subaccounts_list', sanitize_text_field($subaccounts_list));

			// assign orders to parent
			$args = array(
			    'customer_id' => $user_id
			);
			$orders = wc_get_orders($args);
			foreach ($orders as $order){
				$order_id = $order->get_id();
				$parent_user_id = get_user_meta($user_id,'marketking_account_parent', true);

				$order->set_customer_id($parent_user_id);
				$order->save();
			}
		}

		// delete account
		wp_delete_user($user_id);

		echo 'success';
		exit();	
	}

	function marketking_download_vendor_balance_history(){
    	// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		
		$vendorid = sanitize_text_field($_GET['userid']);

		$list_name = 'vendor_balance_history';
		$list_name = apply_filters('marketking_balance_history_file_name', $list_name);

		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=".$list_name."_".$vendorid.".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		$output = fopen("php://output", "wb");
		// build header

		$headerrow = apply_filters('marketking_balance_history_columns_header',array(esc_html__('Date','marketking-multivendor-marketplace-for-woocommerce'), esc_html__('Amount','marketking-multivendor-marketplace-for-woocommerce'), esc_html__('Outstanding balance','marketking-multivendor-marketplace-for-woocommerce'), esc_html__('Note', 'marketking-multivendor-marketplace-for-woocommerce'), esc_html__('Payment method', 'marketking-multivendor-marketplace-for-woocommerce')));

		fputcsv($output, $headerrow);


		$user_balance_history = sanitize_text_field(get_user_meta($vendorid,'marketking_user_balance_history', true));

		if ($user_balance_history){
		    $transactions = explode(';', $user_balance_history);
		    $transactions = array_filter($transactions);
		} else {
		    // empty, no transactions
		    $transactions = array();
		}
		$transactions = array_reverse($transactions);
		foreach ($transactions as $transaction){
		    $elements = explode(':', $transaction);
		    $date = $elements[0];
		    $amount = $elements[1];
		    $oustanding_balance = $elements[2];
		    $note = $elements[3];
		    $method = $elements[4];

		    $csv_array = apply_filters('marketking_balance_history_download_columns_items', array($date, $amount, $oustanding_balance, $note, $method), $transaction);

		    fputcsv($output, $csv_array); 
		}    

		fclose($output);
		exit();
	}

	function marketking_download_vendor_credit_history(){
    	// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		
		$vendorid = sanitize_text_field($_GET['userid']);

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		// check that either current user is admin or this vendor or team
		if ( ! current_user_can( 'manage_woocommerce' ) && $current_id != $vendorid) {
			return; // abort, user does not have permissions
		}


		$list_name = 'vendor_credits_history';
		$list_name = apply_filters('marketking_credit_history_file_name', $list_name);

		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=".$list_name.".csv");
		header("Pragma: no-cache");
		header("Expires: 0");

		$output = fopen("php://output", "wb");
		// build header

		$headerrow = apply_filters('marketking_credit_history_columns_header',array(esc_html__('Date','marketking-multivendor-marketplace-for-woocommerce'), esc_html__('Operation','marketking-multivendor-marketplace-for-woocommerce'), esc_html__('Amount','marketking-multivendor-marketplace-for-woocommerce'), esc_html__('Credit balance','marketking-multivendor-marketplace-for-woocommerce'), esc_html__('Note', 'marketking-multivendor-marketplace-for-woocommerce')));

		fputcsv($output, $headerrow);


		$user_balance_history = sanitize_text_field(get_user_meta($vendorid,'marketking_user_credit_history', true));

		if ($user_balance_history){
		    $transactions = explode(';', $user_balance_history);
		    $transactions = array_filter($transactions);
		} else {
		    // empty, no transactions
		    $transactions = array();
		}
		$transactions = array_reverse($transactions);
		foreach ($transactions as $transaction){
		    $elements = explode(':', $transaction);
		    $date = $elements[0];
		    $operation = $elements[1];
		    $amount = $elements[2];
		    $new_balance = $elements[3];
		    $note = $elements[4];

		    $csv_array = apply_filters('marketking_credit_history_download_columns_items', array($date, $operation, $amount, $new_balance, $note), $transaction);

		    fputcsv($output, $csv_array); 
		}    

		fclose($output);
		exit();
	}

	// Handles AJAX Download requests, enabling the download of user attachment during registration
	function marketkinghandledownloadrequest(){

    	// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}
		
		$requested_file = $_REQUEST['attachment'];
		// If nonce verification didn't fail, run further
		$file = wp_get_attachment_url( $requested_file );

		if( ! $file ) {
			return;
		}

		//clean the fileurl
		$file_url  = stripslashes( trim( $file ) );
		//get filename
		$file_name = basename( $file );

		header("Expires: 0");
		header("Cache-Control: no-cache, no-store, must-revalidate"); 
		header('Cache-Control: pre-check=0, post-check=0, max-age=0', false); 
		header("Pragma: no-cache");	
		header("Content-Disposition:attachment; filename={$file_name}");
		header("Content-Type: application/force-download");

		readfile("{$file_url}");
		exit();

	}

	function marketking_make_withdrawal(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		$user_id = $current_id;
		$amount = sanitize_text_field($_POST['amount']);	


		// cancel current request here
		$active_withdrawal = get_user_meta($user_id,'marketking_active_withdrawal', true);
		if ($active_withdrawal === 'yes'){
			$amount = 0;
		}


		update_user_meta($user_id,'marketking_active_withdrawal', 'yes');
		update_user_meta($user_id,'marketking_withdrawal_amount', $amount);
		update_user_meta($user_id,'marketking_withdrawal_time', time());

		$vendor_name = marketking()->get_store_name_display($user_id);
		// fire email
		$message = esc_html__('Vendor:', 'marketking-multivendor-marketplace-for-woocommerce').' '.$vendor_name.'<br>'.esc_html__('Amount:', 'marketking-multivendor-marketplace-for-woocommerce').' '.wc_price($amount);

		if (apply_filters('marketking_withdrawal_request_message_enable', true)){
			do_action('marketking_new_message', apply_filters('marketking_withdrawal_request_admin_email',get_option( 'admin_email' )), $message, $user_id, 'withdrawal');

		}


		if (floatval($amount) < 0.1){
			update_user_meta($user_id,'marketking_active_withdrawal', 'no');

		}

		
		echo 'success';
		exit();
	}

	function marketkingsaveinfo(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}
		$user_id = $current_id;

		$method = sanitize_text_field($_POST['chosenmethod']);
		update_user_meta($user_id,'marketking_agent_selected_payout_method', $method);

		$paypalemail = sanitize_text_field($_POST['paypal']);
		$custominfo = sanitize_text_field($_POST['custom']);

		$fullname = sanitize_text_field($_POST['fullname']);
		$billingaddress1 = sanitize_text_field($_POST['billingaddress1']);
		$billingaddress2 = sanitize_text_field($_POST['billingaddress2']);
		$city = sanitize_text_field($_POST['city']);
		$state = sanitize_text_field($_POST['state']);
		$postcode = sanitize_text_field($_POST['postcode']);
		$country = sanitize_text_field($_POST['country']);
		$bank_account_holder_name = sanitize_text_field($_POST['bankholdername']);
		$bank_account_number = sanitize_text_field($_POST['bankaccountnumber']);
		$branchcity = sanitize_text_field($_POST['branchcity']);
		$branchcountry = sanitize_text_field($_POST['branchcountry']);
		$intermediarycode = sanitize_text_field($_POST['intermediarycode']);
		$intermediaryname = sanitize_text_field($_POST['intermediaryname']);
		$intermediarycity = sanitize_text_field($_POST['intermediarycity']);
		$intermediarycountry = sanitize_text_field($_POST['intermediarycountry']);
		$bankname = sanitize_text_field($_POST['bankname']);
		$bankswift = sanitize_text_field($_POST['bankswift']);

		$linkedinfo = $paypalemail.'**&&'.$custominfo.'**&&'.$fullname.'**&&'.$billingaddress1.'**&&'.$billingaddress2.'**&&'.$city.'**&&'.$state.'**&&'.$postcode.'**&&'.$country.'**&&'.$bank_account_holder_name.'**&&'.$bank_account_number.'**&&'.$branchcity.'**&&'.$branchcountry.'**&&'.$intermediarycode.'**&&'.$intermediaryname.'**&&'.$intermediarycity.'**&&'.$intermediarycountry.'**&&'.$bankname.'**&&'.$bankswift;

		update_user_meta($user_id,'marketking_payout_info', base64_encode($linkedinfo));

		echo 'success';
		exit();

	}


	function auto_redirect_after_logout($user_id){

		// if sales agent, redirect to sales agent page
		$is_sales_agent = get_user_meta($user_id,'marketking_group', true);
		if ($is_sales_agent === 'none' || empty($is_sales_agent)){

		} else {
		    //wp_redirect( apply_filters('marketking_vendor_logout_redirect', trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))) );
		    wp_redirect(get_home_url());
		    exit();
		}
	}


	function prevent_wp_login($redirect_to, $requested_redirect_to, $user) {
	    // WP tracks the current page - global the variable to access it
	    global $pagenow;
	    if( $pagenow === 'wp-login.php' && isset($_POST['marketking_dashboard_login'])) {

	    	if (is_wp_error($user)) {

    	        //Login failed, find out why...
    	        $error_types = array_keys($user->errors);
    	        //Error type seems to be empty if none of the fields are filled out
    	        $error_type = 'both_empty';

    	        if (is_array($error_types) && !empty($error_types)) {
    	            $error_type = $error_types[0];
    	        }

    	        wp_redirect(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))). "?login=failed&reason=" . $error_type);
    	        // Stop execution to prevent the page loading for any reason
    	        exit();
    	    } else {

    	    	wp_redirect(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))));
    	    }
	    }

	    if (intval(url_to_postid($redirect_to)) === marketking()->get_dashboard_page_id()){

	    	if (!empty($_SERVER["HTTP_REFERER"])){
	    		$redirect_to = $_SERVER["HTTP_REFERER"];
	    	}

	    }
	    

	    return $redirect_to;
	}

	function marketkingsaveadjustment(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$amount = sanitize_text_field($_POST['pamount']);
		$note = '(MANUAL ADJUSTMENT) '.sanitize_text_field($_POST['pnote']);
		$user_id = sanitize_text_field($_POST['userid']);
		$method = '-';
		$user_balance_history = sanitize_text_field(get_user_meta($user_id,'marketking_user_balance_history', true));

		// create transaction
		$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
		$outstanding_balance = get_user_meta($user_id,'marketking_outstanding_earnings', true);
		$new_outstanding_balance = floatval($outstanding_balance) + floatval($amount);
		$transaction_new = $date.':'.$amount.':'.$new_outstanding_balance.':'.$note.':'.$method;

		// update credit history
		update_user_meta($user_id,'marketking_user_balance_history',$user_balance_history.';'.$transaction_new);
		// update user consumed balance
		update_user_meta($user_id,'marketking_outstanding_earnings',$new_outstanding_balance);

		// create earning
		$earning_id = marketking()->create_earning($user_id, 'manual', $amount, $note);

		do_action('marketking_save_adjustment', $user_id, $amount, $note);


		echo 'success';
		exit();
	}

	function marketkingsavepayment(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		$amount = sanitize_text_field($_POST['pamount']);
		$method = sanitize_text_field($_POST['pmethod']);
		$note = sanitize_text_field($_POST['pnote']);
		$user_id = sanitize_text_field($_POST['userid']);
		$havebonus = sanitize_text_field($_POST['bonus']); //bool
		$havebonus = filter_var($havebonus,FILTER_VALIDATE_BOOLEAN);


		// get user history: tracks payouts only
		$user_payout_history = sanitize_text_field(get_user_meta($user_id,'marketking_user_payout_history', true));

		// create transaction
		$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
		$outstanding_balance = get_user_meta($user_id,'marketking_outstanding_earnings', true);
		$new_outstanding_balance = floatval($outstanding_balance) - floatval($amount);
		if ($havebonus === true){
			$new_outstanding_balance = $outstanding_balance; // is bonus, so does not count
			$bonus = 'yes';
		} else {
			$bonus = 'no';
		}
		$transaction_new = $date.':'.$amount.':'.$new_outstanding_balance.':'.$note.':'.$method.':'.$bonus;

		// update credit history
		update_user_meta($user_id,'marketking_user_payout_history',$user_payout_history.';'.$transaction_new);
		// update user consumed balance
		update_user_meta($user_id,'marketking_outstanding_earnings',$new_outstanding_balance);

		if ($havebonus !== true){
			// update user balance history
			// get user history: tracks everything that changes balance
			$user_balance_history = sanitize_text_field(get_user_meta($user_id,'marketking_user_balance_history', true));
			$transaction_new = $date.':'.'-'.$amount.':'.$new_outstanding_balance.':'.$note.':'.'Payout - '.$method;
			update_user_meta($user_id,'marketking_user_balance_history',$user_balance_history.';'.$transaction_new);
		}


		// send email to user
		$userdata = get_userdata($user_id);
		$recipient = $userdata->user_email;
		do_action( 'marketking_new_payout', $recipient, $amount, $method, $note );

		// cancels the active withdrawal mark
		update_user_meta($user_id,'marketking_active_withdrawal', 'no');



		echo 'success';
		exit();
	}

	function marketking_get_page_content(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		// get page here
		$page = sanitize_text_field($_POST['page']);
		$user_id = sanitize_text_field($_POST['userid']);

		ob_start();

		if ($page === 'payouts'){
			Marketkingcore_Admin::marketking_payouts_page_content();
		} else if ($page === 'dashboard'){
			Marketkingcore_Admin::marketking_dashboard_page_content();
		} else if ($page === 'modules'){
			Marketkingcore_Admin::marketking_modules_page_content();
		} else if ($page === 'registration'){
			Marketkingcore_Admin::marketking_registration_page_content();
		} else if ($page === 'premium'){
			Marketkingcore_Admin::marketking_premium_page_content();
		} else if ($page === 'vendors'){
			Marketkingcore_Admin::marketking_vendors_page_content();
		} else if ($page === 'view_payouts'){
			Marketkingcore_Admin::marketking_view_payouts_content($user_id);
		} else if ($page === 'reports'){
			Marketkingcore_Admin::marketking_reports_page_content($user_id);
		} else if ($page === 'groups'){
			Marketkingcore_Admin::marketking_groups_page_content();
		} else if ($page === 'reviews'){
			Marketkingcore_Admin::marketking_reviews_page_content();
		} else {
			// post type
			$pageexplode = explode('_', $page, 2);
			if ($pageexplode[0] === 'edit'){
				$page = $pageexplode[1];
				$this->marketking_get_edit_post_type_page($page);
			}
		}
	
		$content = ob_get_clean();

		echo $content;
		exit();

	}

	function handle_form_become_vendor_loggedin() {

		global $marketking_public;
		if (empty($marketking_public)){
			require_once MARKETKINGCORE_DIR . '/public/class-marketking-core-public.php';
			$marketking_public = new Marketkingcore_Public();
		}
		$marketking_public->marketking_save_custom_registration_fields(get_current_user_id());
		update_user_meta(get_current_user_id(),'marketking_vendor_application_pending','yes');

		$becomepage = get_option('marketking_vendor_registration_page_setting');

		do_action( 'marketking_new_user_requires_approval', get_current_user_id(), '','');

		wp_redirect(get_permalink($becomepage));

	}

	function marketking_advertised_products_shortcode(){
		add_shortcode('marketking_advertised_products', array($this, 'marketking_advertised_products_shortcode_content'));
	}
	function marketking_vendor_products_shortcode(){
		add_shortcode('marketking_vendor_products', array($this, 'marketking_vendor_products_shortcode_content'));
	}
	function marketking_vendor_details_shortcode(){
		add_shortcode('marketking_vendor_details', array($this, 'marketking_vendor_details_shortcode_content'));
	}
	function marketking_vendor_contact_shortcode(){
		add_shortcode('marketking_vendor_contact', array($this, 'marketking_vendor_contact_shortcode_content'));
	}

	function marketking_vendor_details_shortcode_content($atts = array(), $content = null){
		$atts = shortcode_atts(
	        array(
	            'vendor_id' => '',
	        ), 
	    $atts);

	    $vendor_id = $atts['vendor_id'];

	    if (empty($vendor_id) || $vendor_id == 0){
	    	// if current page is product, get vendor of product
	    	global $post;
	    	if (isset($post->ID)){
	    		$product_id = $post->ID;
	    		$vendor_id = marketking()->get_product_vendor($product_id);
	    	}

	    	if (empty($vendor_id)){
	    		$vendor_id = marketking()->get_vendor_id_in_store_url();
	    	}
	    }

	    ob_start();

      	marketking()->get_vendor_details_tab($vendor_id);

	    $content = ob_get_clean();
	    return $content;
	}

	function marketking_advertised_products_shortcode_content($atts = array(), $content = null){
		$atts = shortcode_atts(
	        array(
	            'count' => '12',
	            'paginate' => 'false',
	            'orderby' => 'rand'
	        ), 
	    $atts);

	    $count = $atts['count'];
	    $paginate = $atts['paginate'];
	    $orderby = $atts['orderby'];

	    $products_advertised = marketking()->get_advertised_product_ids();
	    shuffle($products_advertised);
	    $products_advertised_list = implode(',', $products_advertised);

	    set_transient('marketking_is_ad_shortcode', 'yes');

	    ob_start();
       	
      	echo do_shortcode(apply_filters('marketking_advertised_products_shortcode','[products limit="'.$count.'" paginate="'.$paginate.'" visibility="visible" cache="false" orderby="'.$orderby.'" ids="'.$products_advertised_list.'"]'));	

	    $content = ob_get_clean();

	    set_transient('marketking_is_ad_shortcode', 'no');

	    return $content;
	}

	function marketking_vendor_products_shortcode_content($atts = array(), $content = null){
		$atts = shortcode_atts(
	        array(
	            'vendor_id' => '',
	        ), 
	    $atts);

	    $vendor_id = $atts['vendor_id'];

	    if (empty($vendor_id) || $vendor_id == 0){
	    	// if current page is product, get vendor of product
	    	global $post;
	    	if (isset($post->ID)){
	    		$product_id = $post->ID;
	    		$vendor_id = marketking()->get_product_vendor($product_id);
	    	}

	    	if (empty($vendor_id)){
	    		$vendor_id = marketking()->get_vendor_id_in_store_url();
	    	}
	    }

	    ob_start();

      	// Store Notice
      	if (defined('MARKETKINGPRO_DIR')){
    	  	if (intval(get_option('marketking_enable_storenotice_setting', 1)) === 1){
    			// get current vendor
    			$notice_enabled = get_user_meta($vendor_id,'marketking_notice_enabled', true);
    			if ($notice_enabled === 'yes'){
    				$notice_message = get_user_meta($vendor_id,'marketking_notice_message', true);
    				if (!empty($notice_message)){
    					wc_print_notice($notice_message,'notice');
    				}
    			}
    		}
      	}
       	
      	echo do_shortcode(apply_filters('marketking_products_shortcode','[products limit="'.apply_filters('marketking_default_products_number',12).'" paginate="true" visibility="visible "cache="false"]'));	

	    $content = ob_get_clean();
	    return $content;
	}

	function marketking_vendor_contact_shortcode_content($atts = array(), $content = null){
		$atts = shortcode_atts(
	        array(
	            'vendor_id' => '',
	        ), 
	    $atts);

	    $vendor_id = $atts['vendor_id'];

	    if (empty($vendor_id) || $vendor_id == 0){
	    	// if current page is product, get vendor of product
	    	global $post;
	    	if (isset($post->ID)){
	    		$product_id = $post->ID;
	    		$vendor_id = marketking()->get_product_vendor($product_id);
	    	}

	    	if (empty($vendor_id)){
	    		$vendor_id = marketking()->get_vendor_id_in_store_url();
	    	}
	    }

	    ob_start();

	    if (defined('MARKETKINGPRO_DIR')){
	      	marketkingpro()->get_vendor_inquiries_tab($vendor_id);
      	}

	    $content = ob_get_clean();
	    return $content;
	}




	function marketking_vendor_reviews_shortcode(){
		add_shortcode('marketking_vendor_reviews', array($this, 'marketking_vendor_reviews_shortcode_content'));
	}
	function marketking_vendor_reviews_shortcode_content($atts = array(), $content = null){
		$atts = shortcode_atts(
	        array(
	            'vendor_id' => '',
	            'show_pagination' => 'yes',
	            'reviews_per_page' => '5',
	        ), 
	    $atts);

	    $vendor_id = $atts['vendor_id'];
	    $reviews_per_page = $atts['reviews_per_page'];
	    $show_pagination = $atts['show_pagination'];

	    if (empty($vendor_id) || $vendor_id == 0){
	    	// if current page is product, get vendor of product
	    	global $post;
	    	if (isset($post->ID)){
	    		$product_id = $post->ID;
	    		$vendor_id = marketking()->get_product_vendor($product_id);
	    	}

	    	if (empty($vendor_id)){
	    		$vendor_id = marketking()->get_vendor_id_in_store_url();
	    	}

	    }

	    ob_start();

	    ?>
	    <div id="marketking_vendor_tab_reviews" class="marketking_tab " style="display: block;">
	    	<?php

	    if (defined('MARKETKINGPRO_DIR')){
	      	if (intval(get_option('marketking_enable_reviews_setting', 1)) === 1){
	      		$items_per_page = $reviews_per_page;

	      		$pagenr = get_query_var('pagenr2');
	      		if (empty($pagenr)){
	      			$pagenr = 1;
	      		}

	    		// last 10 reviews here
	    		$args = array ('post_type' => 'product', 'post_author' => $vendor_id, 'number' => $items_per_page, 'paged' => $pagenr);
	    	    $comments = get_comments( $args );

	    	    if (empty($comments)){
	    	    	esc_html_e('There are no reviews yet...','marketking-multivendor-marketplace-for-woocommerce');
	    	    } else {
	    	    	// show review average
	    	    	$rating = marketking()->get_vendor_rating($vendor_id);
	    	    	// if there's any rating
	    	    	if (intval($rating['count'])!==0){
	    	    		?>
	    	    		<div class="marketking_rating_header">
	    		    		<?php
	    		    		// show rating
	    		    		if (intval($rating['count']) === 1){
	    		    			$review = esc_html__('review','marketking-multivendor-marketplace-for-woocommerce');
	    		    		} else {
	    		    			$review = esc_html__('reviews','marketking-multivendor-marketplace-for-woocommerce');
	    		    		}
	    		    		echo '<strong>'.esc_html__('Rating:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.esc_html($rating['rating']).' '.esc_html__('rating from','marketking-multivendor-marketplace-for-woocommerce').' '.esc_html($rating['count']).' '.esc_html($review);
	    		    		echo '<br>';
	    		    	?>
	    		   		</div>
	    		    	<?php
	    	    	}
	    	    }
	    	    wp_list_comments( array( 'callback' => 'woocommerce_comments' ), $comments);

	    	    // display pagination

	    	    // get total nr
	    	    $args = array ('post_type' => 'product', 'post_author' => $vendor_id, 'fields' => 'ids');
	    	    $comments = get_comments( $args );
	    	    $totalnr = count($comments); //total nr of reviews
	    	    $nrofpages = ceil($totalnr/$items_per_page);
	    	    $store_link = marketking()->get_store_link($vendor_id);

	    	    if ($show_pagination === 'yes'){
	    	    	$i = 1;
	    	    	while($i <= $nrofpages){
	    	    		$pagelink = $store_link.'/reviews/'.$i;
	    	    		$active = '';
	    	    		if ($i === intval($pagenr)){
	    	    			$active = 'marketking_review_active_page';
	    	    		}
	    	    		// display page
	    	    		?>
	    	    		<a href="<?php echo esc_attr($pagelink);?>" class="marketking_review_pagination_page <?php echo esc_html($active);?>"><?php echo esc_html($i); ?></a>
	    	    		<?php
	    	    		$i++;
	    	    	}
	    	    }

	    	   


	    		?>
	    		<?php
	    		}
	    }

	    echo '</div>';

	    $content = ob_get_clean();
	    return $content;
	}

	function marketking_vendor_registration_shortcode(){
		add_shortcode('marketking_vendor_registration', array($this, 'marketking_vendor_registration_shortcode_content'));
	}
	function marketking_vendor_registration_shortcode_content($atts = array(), $content = null){

		// prevent errors in rest api
		if (!function_exists('wc_print_notices')){
			return;
		}

		require_once MARKETKINGCORE_DIR . '/public/class-marketking-core-public.php';

		ob_start();

		if (get_option( 'marketking_vendor_registration_setting', 'myaccount' ) === 'separate'){
			// if user is logged in, show message instead of shortcode
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();

				if (marketking()->is_vendor($user_id) or marketking()->is_vendor_team_member()){
					// go to vendor dashboard
					?>
					<a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))));?>" class="marketking_go_vendor_dashboard_link"><button class="marketking_go_vendor_dashboard_button"><?php esc_html_e('Go to the Vendor Dashboard', 'marketking-multivendor-marketplace-for-woocommerce'); ?></button></a>
					<?php
				} else {

					if (apply_filters('marketking_allow_logged_in_register_vendor', false)){

						if (marketking()->has_vendor_application_pending($user_id)){
							// wait to be approved
							echo '<span class="marketking_already_logged_in_message">';
							esc_html_e('You have applied for a vendor account. We are currently reviewing your application.','marketking-multivendor-marketplace-for-woocommerce');
							echo '</span>';
						} else {
							// register

							?>
							<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="POST" class="woocommerce-form woocommerce-form-register register">


								<?php

								Marketkingcore_Public::marketking_custom_registration_fields();
								?>

								<p class="woocommerce-form-row form-row">
									<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Send application', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>"><?php esc_html_e( 'Send application', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></button>
								</p>

								<input type="hidden" name="action" value="marketking_become_vendor_loggedin">

							</form>
							<?php
							
						}

					} else {
						echo '<span class="marketking_already_logged_in_message">';
						$text = esc_html__('You are already logged in and cannot apply for a new account. To apply for a new Vendor account, please logout first. ','marketking-multivendor-marketplace-for-woocommerce');
						echo apply_filters('marketking_you_are_logged_in_text', $text);
						echo '<a href="'.esc_url(wp_logout_url(get_permalink())).'">'.esc_html__('Click here to log out','marketking-multivendor-marketplace-for-woocommerce').'</a></span>';
					}

				}

				
			} else {
				$message = apply_filters( 'woocommerce_my_account_message', '' );
				if ( ! empty( $message ) ) {
					wc_add_notice( $message );
				}
				wc_print_notices();
				?>
				<h2>
				<?php esc_html_e( 'Register', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></h2>
				<div class="woocommerce">
					<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

						<?php do_action( 'woocommerce_register_form_start' ); ?>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) { ?>

							<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<label for="reg_username"><?php esc_html_e( 'Username', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
								<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
							</p>

						<?php } ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_email"><?php esc_html_e( 'Email address', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
							<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" />
						</p>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) { ?>

							<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<label for="reg_password"><?php esc_html_e( 'Password', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
								<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
							</p>

						<?php } else { ?>

							<p><?php esc_html_e( 'A password will be sent to your email address.', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></p>

						<?php } ?>

						<?php 

						Marketkingcore_Public::marketking_custom_registration_fields();

						do_action( 'woocommerce_register_form' ); 

						?>

						<p class="woocommerce-form-row form-row">
							<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
							<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>"><?php esc_html_e( 'Register', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></button>
						</p>

						<?php do_action( 'woocommerce_register_form_end' ); ?>

					</form>
				</div>
				<?php
			}
		}

		$output = ob_get_clean();
		return $output;

	}


	// Helps prevent public code from running on login / register pages, where is_admin() returns false
	function marketking_is_login_page() {
		if(isset($GLOBALS['pagenow'])){
	    	return in_array( $GLOBALS['pagenow'],array( 'wp-login.php', 'wp-register.php', 'admin.php' ),  true  );
	    }
	}

	
	function marketking_dismiss_activate_woocommerce_admin_notice(){
		// Check security nonce. 
		if ( ! check_ajax_referer( 'marketking_notice_security_nonce', 'security' ) ) {
		  	wp_send_json_error( 'Invalid security token sent.' );
		    wp_die();
		}

		update_user_meta(get_current_user_id(), 'marketking_dismiss_activate_woocommerce_notice', 1);

		echo 'success';
		exit();
	}


	function process_order_vendors($order_id, $posted_data = array(), $order = array()){

		if (is_array($order)){
			$order = wc_get_order($order_id);
		}

		if ($order){
			$already_processed = $order->get_meta('marketking_already_processed_order');

			$parent = wc_get_order($order->get_parent_id());
			if ($parent){
				$already_processed_parent = $parent->get_meta('marketking_already_processed_order');
			} else {
				$already_processed_parent = 'no';
			}


			if ($already_processed !== 'yes' && $already_processed_parent !== 'yes'){

				$defertoadmin = 0; // amount deferred to admin from non-connected vendors

				// Stripe integration start
				if (defined('MARKETKINGPRO_DIR')){
			        if (intval(get_option( 'marketking_enable_stripe_setting', 1 )) === 1){
						$split_payers = array(); 
						$error = 0;
						$totalvendorcommission = 0;
						$applicationfees = 0;
						$settings = get_option('woocommerce_marketking_stripe_gateway_settings');
						if (isset($settings['non_connected'])){
							$non_connected = $settings['non_connected']; // yes or no
						} else {
							$non_connected = 'no';
						}
					}
				}
				// Stripe end
					
				// check if the items in the order belong to a single or multiple vendor

				$vendors = marketking()->get_vendors_of_order($order_id);
				// if single, set the order author here as the vendor
				if (count($vendors) === 1){
					if (intval($vendors[0]) !== 1){
						// if not admin, then assign it
						$update_args = array(
						    'ID' => $order_id,
						    'post_author' => $vendors[0],
						);
						$result = wp_update_post($update_args);

					}
				} else {
					// split into multiple orders
					$splitter = new Marketking_Order_Splitter;
					$splitter->split_orders($order, $vendors);
				}

				// apply group rules
				foreach ($vendors as $vendor_id){
					marketking()->apply_group_rules($vendor_id);
				}

				// calculate commissions
				if (marketking()->is_multivendor_order($order_id)){
					$suborders = marketking()->get_suborders_of_order($order_id);
					foreach ($suborders as $suborder){
						$suborder_id = $suborder->get_id();
						$vendor_id = marketking()->get_order_vendor($suborder_id);

						$suborder->update_meta_data('_marketking_vendor_name', marketking()->get_store_name_display($vendor_id));
						$suborder->update_meta_data('_marketking_vendor_id', $vendor_id);

						$vendor_commission = marketking()->calculate_vendor_commission($vendor_id, $suborder_id);
						$vendor_commission = apply_filters('marketking_vendor_commission_change', $vendor_commission, $vendor_id, $suborder_id);
						$earning_id = marketking()->create_earning($vendor_id, $suborder_id, $vendor_commission);

						// Stripe integration
						if (defined('MARKETKINGPRO_DIR')){
					        if (intval(get_option( 'marketking_enable_stripe_setting', 1 )) === 1){
								$vendor_connected = intval(get_user_meta( $vendor_id, 'vendor_connected', true ));
								$vendor_connect_user_id = get_user_meta( $vendor_id, 'stripe_user_id', true );

								if ($vendor_connected === 1){
									$split_payers[$vendor_id] = array(
										'destination' => $vendor_connect_user_id,
										'commission'  => $vendor_commission,
										'gross_sales'  => $suborder->get_total(),
										'application_fee'  => ($suborder->get_total()-$vendor_commission),
									);

									$totalvendorcommission += $vendor_commission;
									$applicationfees += $split_payers[$vendor_id]['application_fee'];

									//$suborder->update_meta_data('marketking_paid_via_stripe', 'yes');
									//changed to setting this for all orders, so vendors can refund even orders that are nonconnected


								} else {
									if ($non_connected === 'yes'){
										$defertoadmin+=$suborder->get_total();
									} else {
										// throw error because vendor is not connected and not connected setting is disabled
										$error = 1;
									}
								}

								$suborder->update_meta_data('marketking_paid_via_stripe', 'yes');

							}
						}

						$suborder->save();

								
					}
				} else {

					// if not admin, set commission
					$vendor_id = marketking()->get_order_vendor($order_id);

					$order->update_meta_data('_marketking_vendor_name', marketking()->get_store_name_display($vendor_id));
					$order->update_meta_data('_marketking_vendor_id', $vendor_id);

					if (intval($vendor_id) !== 1){

						// calculate commission
						$vendor_commission = marketking()->calculate_vendor_commission($vendor_id, $order_id);
						$vendor_commission = apply_filters('marketking_vendor_commission_change', $vendor_commission, $vendor_id, $order_id);

						$earning_id = marketking()->create_earning($vendor_id, $order_id, $vendor_commission);

						// Stripe integration
						if (defined('MARKETKINGPRO_DIR')){
					        if (intval(get_option( 'marketking_enable_stripe_setting', 1 )) === 1){

								$vendor_connected = intval(get_user_meta( $vendor_id, 'vendor_connected', true ));
								$vendor_connect_user_id = get_user_meta( $vendor_id, 'stripe_user_id', true );

								if ($vendor_connected === 1){

									$split_payers[$vendor_id] = array(
										'destination' => $vendor_connect_user_id,
										'commission'  => $vendor_commission,
										'gross_sales'  => $order->get_total(),
										'application_fee'  => ($order->get_total()-$vendor_commission),
									);

									$totalvendorcommission += $vendor_commission;
									$applicationfees += $split_payers[$vendor_id]['application_fee'];

									//$order->update_meta_data('marketking_paid_via_stripe', 'yes'); 
									//changed to setting this for all orders, so vendors can refund even orders that are nonconnected
									
								} else {

									if ($non_connected === 'yes'){
										$defertoadmin+=$order->get_total();
									} else {
										// throw error because vendor is not connected and not connected setting is disabled
										$error = 1;
									}
								}

								$order->update_meta_data('marketking_paid_via_stripe', 'yes'); 


							}
						}
					}

				}

				// Stripe integration
				if (defined('MARKETKINGPRO_DIR')){
			        if (intval(get_option( 'marketking_enable_stripe_setting', 1 )) === 1){

						$paylist = array(
							'total_amount'   => number_format($order->get_total(), 2, '.', ''),
							'currency'       => $order->get_currency(),
							'transfer_group' => __('Split Pay for Order #', 'wc-multivendor-marketplace') . $order->get_order_number(),
							'description'    => __('Payment for Order #', 'wc-multivendor-marketplace') . $order->get_order_number(),
							'totalvendorcommission' => $totalvendorcommission,
							'defertoadmin' => $defertoadmin,
							'total_admin_amount' => $order->get_total()-$totalvendorcommission,
							'total_admin_amount_after_application_fees' => $order->get_total()-$totalvendorcommission-$applicationfees,
							'total_application_fees' => $applicationfees,
							'error' => $error,
							'distribution_list' => $split_payers
						);
					
						$paylist = apply_filters( 'marketking_paylist_split_pay_payment_args', $paylist, $order );

						$order->update_meta_data('marketking_stripe_order_paylist', $paylist);


					}
				}

				$order->update_meta_data('marketking_already_processed_order', 'yes');

			}

			$order->save();
		}

		


	
	}

	function process_order_vendors_pos($order, $request, $creating){

		if (function_exists('yith_pos_is_pos_order')){
			if (yith_pos_is_pos_order($order)){

				// check if the items in the order belong to a single or multiple vendor
				$order_id = $order->get_id();
				$vendors = marketking()->get_vendors_of_order($order_id);
				// if single, set the order author here as the vendor
				
				if (count($vendors) === 1){
					if (intval($vendors[0]) !== 1){
						// if not admin, then assign it
						$update_args = array(
						    'ID' => $order_id,
						    'post_author' => $vendors[0],
						);
						$result = wp_update_post($update_args);

					}
				} else {
					// split into multiple orders
					$splitter = new Marketking_Order_Splitter;
					$splitter->split_orders($order, $vendors);
				}

				// apply group rules
				foreach ($vendors as $vendor_id){
					marketking()->apply_group_rules($vendor_id);
				}

				// calculate commissions
				if (marketking()->is_multivendor_order($order_id)){
					$suborders = marketking()->get_suborders_of_order($order_id);
					foreach ($suborders as $suborder){
						$suborder_id = $suborder->get_id();
						$vendor_id = marketking()->get_order_vendor($suborder_id);

						$vendor_commission = marketking()->calculate_vendor_commission($vendor_id, $suborder_id);
						$earning_id = marketking()->create_earning($vendor_id, $suborder_id, $vendor_commission);
					}
				} else {
					// if not admin, set commission
					$vendor_id = marketking()->get_order_vendor($order_id);
					if (intval($vendor_id) !== 1){
						// calculate commission
						$vendor_commission = marketking()->calculate_vendor_commission($vendor_id, $order_id);
						$earning_id = marketking()->create_earning($vendor_id, $order_id, $vendor_commission);
					}
				}
				
			}
		}

	
	}
		
}

