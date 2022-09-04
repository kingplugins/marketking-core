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

				// Duplicate product
				add_action( 'wp_ajax_marketkingdisconnectstripe', array($this, 'marketkingdisconnectstripe') );
				add_action( 'wp_ajax_nopriv_marketkingdisconnectstripe', array($this, 'marketkingdisconnectstripe') );




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
							add_filter( 'woocommerce_product_data_tabs', array( $b2bking_admin, 'b2bking_additional_panel_in_product_page' ) );
							add_action( 'woocommerce_product_data_panels', array( $b2bking_admin, 'b2bking_additional_panel_in_product_page_content' ) );
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

				// Add vendor registration shortcode
				add_action( 'init', array($this, 'marketking_vendor_registration_shortcode'));

				// Configure product class structures
				add_filter('product_type_selector', function($arr){
					return array(
						'simple'   => esc_html__( 'Simple product', 'woocommerce' ),
					);
				}, 8, 1);
				add_filter('product_type_options', function($arr){
					return array();
				}, 8, 1);
				
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

			// Remove 'Hidden' status products from admin count in products backend
			// Hidden products are those created before new products are saved 
			add_filter( 'views_edit-product' , [$this, 'remove_hidden_products_admin_count'], 10, 1);
			// Remove old hidden products every 6 hours
			add_action( 'wp_footer', [$this, 'clear_hidden_products']);

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



		return $post_states;
	}

	function webtoffee_permissions_invoices_labels($allowedarr){
		// allow access only to own orders
		$postid = sanitize_text_field($_GET['post']);
		// check if order belongs to him
		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}
		if (intval(marketking()->get_order_vendor($postid)) === intval($current_id)){
			$allowedarr = array('manage_options', 'manage_woocommerce', 'upload_files');
		}
	    return $allowedarr;
	}

	function template_file_overwrite_theme($templatefile){

		$theme_directory = get_stylesheet_directory();

    	$templatefilearray = explode('/', $templatefile);
		if ( file_exists( $theme_directory . '/' . end($templatefilearray) ) ) {
			return $theme_directory . '/' . end($templatefilearray) ;
		}
        return $templatefile;
	}

	function clear_hidden_products(){

		$lastcleartime = get_option('marketking_clear_hidden_products_time', true);
		if (empty($lastcleartime)){
			$lastcleartime = intval(time()-9999999); //if first time, set to current time
		} else {
			$lastcleartime = intval($lastcleartime);
		}

		$time_elapsed = intval(time() - $lastcleartime);

		if ($time_elapsed >= intval(apply_filters('marketking_clear_hidden_products_time_setting', 21600))){

			$articles = get_posts(
			 array(
			  'numberposts' => -1,
			  'post_status' => 'hidden',
			  'post_type' => 'product',
			 )
			);
			foreach ($articles as $post){
				if (get_post_status($post) === 'hidden'){
					// if not product standby
					$is_standby = get_post_meta($post->ID,'marketking_is_product_standby', true);
					if (!$is_standby !== 'yes'){
						wp_delete_post($post->ID, true);
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

	function template_file_overwrite_theme_dashboard($templatefile){

		$theme_directory = get_stylesheet_directory();

	    if ( file_exists( $theme_directory . '/' . $templatefile ) ) {
	        return $theme_directory . '/' . $templatefile ;
	    } else {
	    	// check marketking pro file
	    	$templatefilearray = explode('/', $templatefile);
	    	if (isset($templatefilearray[2])){
	    		// we are in a marketking pro file
	    		if ( file_exists( $theme_directory . '/' . $templatefilearray[2] ) ) {
	    			return $theme_directory . '/' . $templatefilearray[2] ;
	    		}
	    	}
	        return $templatefile;
	    }
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
			}
		}


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

		$testmode = isset( $settings['test_mode'] ) ? true : false;
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
				_e( 'Unable to disconnect your account, please try again', 'marketking');
			}
		} else {
			_e( 'Unable to disconnect your account, please try again', 'marketking');
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

		// get earning id, if any
		$earning_id = get_post_meta($order_id, 'marketking_earning_id', true);
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

			if ($method === 'cod'){
				// abort
				return;
			}
		}

		// if order is paid via Stripe, ignore earnings
		if(get_post_meta($order_id, 'marketking_paid_via_stripe', true ) === 'yes'){
			// abort
			return;
		}


		if (in_array($status_to,apply_filters('marketking_earning_completed_statuses', array('completed'))) && !in_array($status_from,apply_filters('marketking_earning_completed_statuses', array('completed')))){

			if(get_option( 'marketking_cod_behaviour_setting', 'default' ) === 'reverse' && $method === 'cod'){
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

			if(get_option( 'marketking_cod_behaviour_setting', 'default' ) === 'reverse' && $method === 'cod'){
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
		}

		return $allcaps;
	}

	public function vendor_library_own_images( $query ) {

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		if (marketking()->is_vendor($current_id) && !current_user_can( 'manage_woocommerce' )){
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

	    $email_classes['Marketking_New_Vendor_Requires_Approval_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-vendor-requires-approval-email.php';

	    $email_classes['Marketking_Your_Account_Approved_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-your-account-approved-email.php';

	    $email_classes['Marketking_New_Payout_Email'] = include MARKETKINGCORE_DIR .'public/emails/class-marketking-new-payout-email.php';

	    if (defined('MARKETKINGPRO_DIR')){
	 	    $email_classes['Marketking_New_Announcement_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-announcement-email.php';
	  		$email_classes['Marketking_New_Message_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-message-email.php';
	  		$email_classes['Marketking_New_Rating_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-rating-email.php';
	  		$email_classes['Marketking_New_Refund_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-refund-email.php';
	  		$email_classes['Marketking_New_Verification_Email'] = include MARKETKINGCORE_DIR .'/public/emails/class-marketking-new-verification-email.php';

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
				$link = get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'edit-product/'.$post_id;
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

			if ($method === 'cod'){
				// abort
				return;
			}
		}

		// if order is paid via stripe, abort

		if(get_post_meta($order_id, 'marketking_paid_via_stripe', true ) === 'yes'){
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
		$tax_fee_recipient = get_option('marketking_tax_fee_recipient_setting', 'vendor');
		$shipping_fee_recipient = get_option('marketking_shipping_fee_recipient_setting', 'vendor');

	
		// 1. Get proportion admin commission / calc basis
		$proportion = get_post_meta($order_id,'marketking_refund_proportion', true);
		if (empty($proportion)){
			$calculation_basis = $order_total;		

			if ($tax_fee_recipient === 'vendor'){
				$calculation_basis -= $tax_total;
			}

			if ($shipping_fee_recipient === 'vendor'){
				$calculation_basis -= $shipping_total;
			}

			$admin_commission = marketking()->get_order_earnings($order_id,true);
			$proportion = floatval($admin_commission) / $calculation_basis;

			update_post_meta($order_id,'marketking_refund_proportion', $proportion);
		}
		

		// 2. Get NEW calculation basis
		$new_order_total = $order_total-$order->get_total_refunded();
		$new_calculation_basis = $new_order_total;
		if ($tax_fee_recipient === 'vendor'){
			$new_calculation_basis -= $tax_total;
		}

		if ($shipping_fee_recipient === 'vendor'){
			$new_calculation_basis -= $shipping_total;
		}

		if ($new_calculation_basis < 0){
			$new_calculation_basis = 0;
		}

		// New calculation basis end


		// 3. Apply proportion
		$new_admin_commission = $proportion * $new_calculation_basis;


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

				if(get_option( 'marketking_cod_behaviour_setting', 'default' ) === 'reverse' && $method === 'cod'){
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

		if ($permission === 'yes'){

			do_action( 'marketking_new_refund', $vendor_email, $post_id, $reason, $user->user_login );
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

		if (intval($author_id) === $current_id){
			wp_trash_post($id);
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

			if (intval($author_id) === $current_id){

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

		// reviews
		if (!$pro || !$can_reviews){
			// set linked products array to empty one
			$_POST['comment_status'] = false;
		}

		// all products virtual
		if (marketking()->vendor_all_products_virtual($current_id)){
			$_POST['_virtual'] = 1;
		}

		// all products downloadable
		if (marketking()->vendor_all_products_downloadable($current_id)){
			$_POST['_downloadable'] = 1;
		}

		// menu order 
		$_POST['menu_order'] = 0;

		$id = sanitize_text_field($_POST['id']);

		do_action('marketking_before_save_product', $id, $current_id);

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
		if (intval($author_id) === $current_id){
			WC_Meta_Box_Product_Data::save($id, get_post($id));
			WC_Meta_Box_Product_Images::save($id, get_post($id));

			// update title
			$update_args = array(
			    'ID' => $id,
			    'post_title' => $title,
			    'post_content' => $longexcerpt,
			    'post_excerpt'=> $excerpt,
			    'post_status' => $status,
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

		if (marketking()->is_on_vacation($current_id)){
			marketking()->set_vendor_products_visibility($current_id,'hidden');
		}

		// Integrations
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

		do_action('marketking_after_save_product', $id, $current_id);

		do_action('woocommerce_process_product_meta', $id, $product);

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
		    ?>
		    	<?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-sm marketking-column-large">
		            <a href="<?php echo esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'edit-product/'.$product->get_id());?>"><span class="tb-product"><?php
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
		                $price = $product->get_price();
		                
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
		        <td class="nk-tb-col tb-col-md">
		            <ul class="nk-tb-actions gx-1 my-n1">
		                <li class="mr-n1">
		                    <div class="dropdown">
		                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
		                        <div class="dropdown-menu dropdown-menu-right">
		                            <ul class="link-list-opt no-bdr">
		                                <li><a href="<?php echo esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'edit-product/'.$product->get_id());?>"><em class="icon ni ni-edit"></em><span><?php esc_html_e('Edit Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
		                                <li><a href="<?php 
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

		        array_push($data['data'],array($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col10));

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
		            <a href="<?php echo esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'manage-order/'.$orderobj->get_id());?>">

		                <div>
		                    <span class="tb-lead">#<?php echo esc_html($orderobj->get_id()).' '.$orderobj->get_formatted_billing_full_name();?></span>
		                </div>
		            </a>
		        </td>
		        <?php $col1 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
		        <td class="nk-tb-col tb-col-md" data-order="<?php
		            $date = explode('T',$orderobj->get_date_created())[0];
		            echo strtotime($date);
		        ?>">
		            <div>
		                <span class="tb-sub"><?php 
		                
		                echo ucfirst(strftime("%B %e, %G", strtotime($date)));
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

		                ?></span>
		            </div>
		        </td>
		        <?php $col7 = ob_get_clean(); ?>
		        <?php ob_start(); ?>
                <td class="nk-tb-col">
                    <div class="marketking_manage_order_container"> 

                        <a href="<?php echo esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'manage-order/'.$orderobj->get_id());?>"><button class="btn btn-sm btn-dim btn-secondary marketking_manage_order" value="<?php echo esc_attr($orderobj->get_id());?>"><em class="icon ni ni-bag-fill"></em><span><?php esc_html_e('View Order','marketking-multivendor-marketplace-for-woocommerce');?></span></button></a>
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

		$address1 = sanitize_text_field($_POST['address1']);
		$address2 = sanitize_text_field($_POST['address2']);
		$postcode = sanitize_text_field($_POST['postcode']);
		$city = sanitize_text_field($_POST['city']);
		$state = sanitize_text_field($_POST['state']);
		$country = sanitize_text_field($_POST['country']);

		$aboutusraw = $_POST['aboutus'];
		$allowed = array('<h2>','</h2>','<h3>','<h4>','<i>','<strong>','</h3>','</h4>','</i>','</strong>');
		$replaced = array('***h2***','***/h2***','***h3***','***h4***','***i***','***strong***','***/h3***','***/h4***','***/i***','***/strong***');
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

		$support_email = get_user_meta($vendor_id,'marketking_support_email', true);
		if ($support_option === 'messaging'){
			// in the case of messaging, there is not a dedicated support email address. The store email is used.
			$support_email = marketking()->get_vendor_email($vendor_id);
		}

		// build email
		if (!empty($product_id)){
			$product = wc_get_product($product_id);
			$productname = $product->get_formatted_name();
			$message = esc_html__( 'Product: ', 'marketking-multivendor-marketplace-for-woocommerce' ) . $productname . ' <br />'.sanitize_textarea_field( $_POST['message'] ).'<br>';
		} else if (!empty($order_id)){

			$message = esc_html__( 'Order: #', 'marketking-multivendor-marketplace-for-woocommerce' ) . esc_html($order_id) . ' <br />'.sanitize_textarea_field( $_POST['message'] ).'<br>';
		} else {
			$message = sanitize_textarea_field( $_POST['message'] ).'<br>';
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
			$message      = sanitize_textarea_field( $_POST['message'] );
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

			// add user to message email
			$messagecartemail = esc_html__('User:', 'marketking-multivendor-marketplace-for-woocommerce').' '.$user->first_name.' '.$user->last_name.' ('.$user->user_email.')<br>'.$messagecart;

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
	    			update_post_meta( $discussionid, 'marketking_message_message_2', sanitize_text_field(esc_html__('This inquiry was sent by a logged out user, without an account. Please email the user directly!', 'b2bking')));
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

				update_post_meta($order_id,'_customer_user', $parent_user_id);
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
		update_user_meta($user_id,'marketking_active_withdrawal', 'yes');
		update_user_meta($user_id,'marketking_withdrawal_amount', $amount);
		update_user_meta($user_id,'marketking_withdrawal_time', time());



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

		$linkedinfo = $paypalemail.'**&&'.$custominfo.'**&&'.$fullname.'**&&'.$billingaddress1.'**&&'.$billingaddress2.'**&&'.$city.'**&&'.$state.'**&&'.$postcode.'**&&'.$country.'**&&'.$bank_account_holder_name.'**&&'.$bank_account_number.'**&&'.$branchcity.'**&&'.$branchcountry.'**&&'.$intermediarycode.'**&&'.$intermediaryname.'**&&'.$intermediarycity.'**&&'.$intermediarycountry;

		update_user_meta($user_id,'marketking_payout_info', base64_encode($linkedinfo));

		echo 'success';
		exit();

	}


	function auto_redirect_after_logout($user_id){

		// if sales agent, redirect to sales agent page
		$is_sales_agent = get_user_meta($user_id,'marketking_group', true);
		if ($is_sales_agent === 'none' || empty($is_sales_agent)){

		} else {
		    //wp_redirect( apply_filters('marketking_vendor_logout_redirect', get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))) );
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

    	        wp_redirect(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)). "?login=failed&reason=" . $error_type);
    	        // Stop execution to prevent the page loading for any reason
    	        exit();
    	    } else {

    	    	wp_redirect(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)));
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

	function marketking_vendor_registration_shortcode(){
		add_shortcode('marketking_vendor_registration', array($this, 'marketking_vendor_registration_shortcode_content'));
	}
	function marketking_vendor_registration_shortcode_content($atts = array(), $content = null){

		ob_start();

		if (get_option( 'marketking_vendor_registration_setting', 'myaccount' ) === 'separate'){
			// if user is logged in, show message instead of shortcode
			if ( is_user_logged_in() ) {
				echo '<span class="marketking_already_logged_in_message">';
				$text = esc_html__('You are already logged in and cannot apply for a new account. To apply for a new Vendor account, please logout first. ','marketking-multivendor-marketplace-for-woocommerce');
				echo apply_filters('marketking_you_are_logged_in_text', $text);
				echo '<a href="'.esc_url(wc_logout_url(get_permalink())).'">'.esc_html__('Click here to log out','marketking-multivendor-marketplace-for-woocommerce').'</a></span>';
			} else {
				$message = apply_filters( 'woocommerce_my_account_message', '' );
				if ( ! empty( $message ) ) {
					wc_add_notice( $message );
				}
				wc_print_notices();
				?>
				<h2>
				<?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>
				<div class="woocommerce">
					<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

						<?php do_action( 'woocommerce_register_form_start' ); ?>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) { ?>

							<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
								<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
							</p>

						<?php } ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
							<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" />
						</p>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) { ?>

							<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
								<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
							</p>

						<?php } else { ?>

							<p><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

						<?php } ?>

						<?php 

						Marketkingcore_Public::marketking_custom_registration_fields();

						do_action( 'woocommerce_register_form' ); 

						?>

						<p class="woocommerce-form-row form-row">
							<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
							<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
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
		
}

