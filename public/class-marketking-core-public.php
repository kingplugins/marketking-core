<?php

class Marketkingcore_Public{

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
			add_action('plugins_loaded', function(){

				// Only load if WooCommerce is activated
				if ( class_exists( 'woocommerce' ) ) {

					// Add classes to body
					add_filter('body_class', array( $this, 'marketking_body_classes' ));

					// Enqueue resources
					add_action('wp_enqueue_scripts', [$this, 'enqueue_public_resources']);

					/* Dashboard section */
					// Load dashboard as full screen by removing templates
					add_filter( 'woocommerce_locate_template', array( $this, 'marketking_locate_template' ), 99999, 3 );
					add_filter( 'template_include', array( $this, 'marketking_template_include' ), 99999, 1 );
					// Page query var for dashboard
					add_filter( 'query_vars', array($this, 'marketking_add_query_vars_filter') );
					add_action( 'init', array($this, 'marketking_rewrite_dashboard_url' ));

					/* Registration section */
					// When registration in separate page, show "Become a vendor" in my account
					if (get_option( 'marketking_vendor_registration_setting', 'myaccount' ) === 'separate'){
						add_action('woocommerce_register_form_end', [$this, 'marketking_become_vendor_link_myaccount']);
					} else if (get_option( 'marketking_vendor_registration_setting', 'myaccount' ) === 'myaccount'){
						// Custom user registration fields
						add_action( 'woocommerce_register_form', array($this,'marketking_custom_registration_fields'), 9 );
					}
					// use user_register hook as well, seems to fix issues in certain installations
					add_action('woocommerce_created_customer', array($this,'marketking_save_custom_registration_fields') );
					add_action('user_register', array($this,'marketking_save_custom_registration_fields') );
					add_action('woocommerce_registration_redirect', array($this,'marketking_check_user_approval_on_registration'), 2);

					if (!apply_filters('marketking_allow_logged_in_register_vendor', false)){
						add_filter('woocommerce_process_login_errors', array($this,'marketking_check_user_approval_on_login'), 10, 3);
					}
					// Allow file upload in registration form for WooCommerce
					add_action( 'woocommerce_register_form_tag', array($this,'marketking_custom_registration_fields_allow_file_upload') );


					// Modify new account email to include notice of manual account approval, if needed
					if (!apply_filters('marketking_allow_logged_in_register_vendor', false)){
						add_action( 'woocommerce_email_footer', array($this,'marketking_modify_new_account_email'), 10, 1 );
					}

					// Replace customer new account email in case of vendors
					add_filter( 'woocommerce_locate_template', array($this, 'intercept_wc_template'), 10, 3 );

					/* Frontend display changes */
					// Add button in my account page to vendor dashboard for vendors
					add_action('woocommerce_account_dashboard', array($this, 'marketking_dashboard_button'), 10);

					// Vendor application pending
					add_action('woocommerce_account_dashboard', array($this, 'marketking_vendor_application_pending'), 10);


					// Show vendor in product page and other items such as checkout
					add_action('woocommerce_product_meta_end', [$this, 'marketking_show_vendor_product_page']);
					add_action( 'init', array($this, 'marketking_vendor_product_page_shortcode'));

					// optional shortcode, allows displaying a custom list of vendors by group
					add_action( 'init', array($this, 'marketking_vendors_list_shortcode'));

					// dashboard header shortcode for custom usage
					add_action( 'init', array($this, 'marketking_dashboard_header_shortcode'));


					// show vendor in cart/checkout (not in cart if using newcart template)
					add_filter( 'woocommerce_get_item_data', [$this,'marketking_show_vendor_cart'], 10, 2 );

					add_filter( 'woocommerce_product_tabs', [$this, 'product_vendor_tab'] );

					// frontend dashicons
					add_action( 'wp_enqueue_scripts', [$this, 'load_dashicons_front_end'] );

					/* Vendors / stores page */
					// Add vendors page content
					add_filter('the_content', [$this,'marketking_vendors_page']);
					// Rewrite url
					add_action('init', [$this, 'marketking_rewrite_url']);
					// Products in the vendor page filters
			
					add_filter( 'woocommerce_shortcode_products_query', [$this, 'marketking_filter_products_author'], 10, 2 );
					// Empty (no products) in vendor page
					add_action( 'woocommerce_shortcode_products_loop_no_results', [$this, 'action_woocommerce_shortcode_products_loop_no_results'], 10, 1 );
					// Modify stores list page title					
					add_filter( 'the_title', [$this, 'store_list_modify_title'], 10, 2 );



					// Add 'author attribute' to products shortcode
					add_filter('shortcode_atts_products', array($this, 'shortcode_atts_products'), 10, 4);			
					add_filter( 'woocommerce_shortcode_products_query', array($this, 'marketking_shortcode_vendor_products'), 100, 2 );

					/* Order management functions */
					// Replace WooCommerce Cart template with custom template for multivendor setup
					if (get_option( 'marketking_cart_display_setting', 'newcart' ) === 'newcart' or apply_filters('marketking_force_newcart_always', false)){
						add_filter( 'woocommerce_locate_template', array( $this, 'marketking_locate_template_cart' ), 10, 3 );
					}

					// Show Multiple Vendors Message in Cart
					add_action('woocommerce_before_cart', [$this,'message_multiple_vendors_cart'], 10);

					// Process order, assign vendor + calculate commissions
					add_action('woocommerce_checkout_order_processed', [$this,'process_order_vendors'], 1000, 3);
					add_action('woocommerce_thankyou', [$this,'process_order_vendors'], 10, 1);
					add_action('woocommerce_payment_complete', [$this,'process_order_vendors'], 10, 1);

					
					// Process order POS integrations
					add_action( 'woocommerce_rest_insert_shop_order_object', array( $this, 'process_order_vendors_pos' ), 100, 3 );


					// Set up order details for multivendor
					add_filter('wc_get_template', [$this, 'marketking_template_orderdetails'], 10, 5);
					// Set order status same as parents, on woocommerce thank you
					add_action('woocommerce_thankyou', [$this,'marketking_set_order_status_parent'], 10, 1);
					// Add vendor column in my account orders
					add_filter( 'woocommerce_my_account_my_orders_columns', array($this, 'marketking_orders_vendor_column') );
					// Add data to "Placed by" column
					add_action( 'woocommerce_my_account_my_orders_column_order-vendor', array($this, 'marketking_orders_vendor_content')  );

					// Set up view order template for multivendor
					add_filter('wc_get_template', [$this, 'marketking_template_vieworder'], 10, 5);
					// Filter thank you for your order email: only send the main 1, not 3 emails
					add_filter( 'woocommerce_email_recipient_customer_processing_order', array($this,'filter_customer_received_order_email_recipient'), 10, 2 );
					// For "New order" emails, each email has to be sent to its vendor, not to the admin
					add_filter( 'woocommerce_email_recipient_new_order', array($this,'filter_new_order_email_recipient'), 10, 2 );
					// Modify order received email template to show multiple vendors
					add_filter('wc_get_template', [$this, 'marketking_template_order_received'], 10, 5);
					// Do not reduce order stock in Composite orders, only in suborders
					add_filter('woocommerce_can_reduce_order_stock', [$this, 'marketking_orders_do_not_reduce_stock'], 10, 2);

					// if vacation module enabled, refresh vendor vacations
					add_action('wp_footer', function(){
						if (defined('MARKETKINGPRO_DIR')){
							if (intval(get_option('marketking_enable_vacation_setting', 1)) === 1){
								marketking()->refresh_vendor_vacations();
							}
						}
					});
					
					// Review Module 
					add_action('wp', function(){
						if(marketking()->is_vendor_store_page()){
							add_action('woocommerce_review_before_comment_meta', array($this, 'before_comment_meta'), 5);
							add_action('woocommerce_review_meta', array($this, 'review_meta'), 10, 1);
						}
						
					});
					
					// Set that vendor can't purchase his own products
					add_action('woocommerce_after_checkout_validation', array($this,'vendor_cannot_buy_own_products'), 10, 2);
					if (apply_filters('marketking_vendors_cannot_buy_own_products', true)){
						add_action( 'woocommerce_before_cart', array($this,'vendor_cannot_buy_own_products_before'), 10);
						add_action( 'woocommerce_before_checkout_form', array($this,'vendor_cannot_buy_own_products_before'), 10);
					}
					

					// Invoice order status allow
					add_filter('wpo_wcpdf_myaccount_allowed_order_statuses', array($this, 'allow_order_status_composite_invoice'), 10, 1);

					// max upload size 1mb for vendors
					add_filter( 'upload_size_limit', function($val){
						return apply_filters('marketking_vendor_upload_file_size', 1048576);
					});



				}

			});
		}
	}

	function allow_order_status_composite_invoice($statuses){
		array_push($statuses,'wc-composite');
		array_push($statuses,'composite');

		return $statuses;
	}

	function vendor_cannot_buy_own_products_before() {
		$vendors_in_cart = marketking()->get_vendors_in_cart();
		if (in_array(get_current_user_id(),$vendors_in_cart) && get_current_user_id() !== 1 && (marketking()->is_vendor(get_current_user_id()) or marketking()->is_vendor_team_member() ) ){
			// error
			wc_print_notice( esc_html__('Your cannot purchase your own products!', 'marketking-multivendor-marketplace-for-woocommerce'), 'error' );
		}
	}

	function vendor_cannot_buy_own_products($data, $errors){
		$vendors_in_cart = marketking()->get_vendors_in_cart();
		if (in_array(get_current_user_id(),$vendors_in_cart) && get_current_user_id() !== 1 && (marketking()->is_vendor(get_current_user_id()) or marketking()->is_vendor_team_member() ) ){
			// error
			$errors->add( 'validation', esc_html__('Your cannot purchase your own products!', 'marketking-multivendor-marketplace-for-woocommerce') );

		}
	}

	function before_comment_meta(){
		echo '<div class="marketking_top_review_row">';
	}

	function before_review_meta($comment){
		$product_id = $comment->comment_post_ID;
		$product = wc_get_product($product_id);
		$title = $product->get_title();
		echo esc_html($title);
	}


	function review_meta($comment){
		echo '</div>';
	}

	function marketking_orders_do_not_reduce_stock($val, $order){
		if (marketking()->is_multivendor_order($order->get_id())){
			$val = false;
		}
		return $val;
	}

	function filter_new_order_email_recipient( $recipient, $order ){
		// change the email recipient to be the vendor

		if (is_object($order)){
			if (marketking()->is_suborder($order->get_id())){
				// get vendor email
				$recipient = marketking()->get_vendor_email(marketking()->get_order_vendor($order->get_id()));
			} else if (!marketking()->is_multivendor_order($order->get_id())){
				// not suborder, but vendor order (just 1 vendor)
				$recipient = marketking()->get_vendor_email(marketking()->get_order_vendor($order->get_id()));

			}

			// for the "new order" email sent to admin (for the composite order), do not send it
			if (marketking()->is_multivendor_order($order->get_id())){
				// unless specifically set via filter, disable it.
				if (!apply_filters('marketking_enable_new_order_email_composite', false)){
					$recipient = '';
				}
			}
		}		

	    return apply_filters('marketking_new_order_email_recipient', $recipient);

	}


	function filter_customer_received_order_email_recipient( $recipient, $order ){
		// do not send this email for suborders
		if (marketking()->is_suborder($order->get_id())){
			$recipient = '';
		}
	    return $recipient;
	}

	// Add "Vendor" column to orders
	function marketking_orders_vendor_column( $columns ) {

	    $new_columns = array();
	    foreach ( $columns as $key => $name ) {
	        $new_columns[ $key ] = $name;
	        // add ship-to after order status column
	        if ( 'order-number' === $key ) {
	            $new_columns['order-vendor'] = esc_html__( 'Vendor', 'marketking-multivendor-marketplace-for-woocommerce' );
	        }
	    }
	    return $new_columns;
	}

	// Add content to the Vendor column
	function marketking_orders_vendor_content( $order ) {
		$vendors = marketking()->get_vendors_of_order($order->get_id());
		if (count($vendors) > 1){
			// if it's a parent order with multiple orders
			esc_html_e('Multiple','marketking-multivendor-marketplace-for-woocommerce');
		} else {
			$vendor_id = marketking()->get_order_vendor($order->get_id());
			$store = marketking()->get_store_name_display($vendor_id);
			echo '<a href="'.esc_attr(marketking()->get_store_link($vendor_id)).'">'.esc_html($store).'</a>';
		}
	    
	}

	function marketking_set_order_status_parent($order_id){

		$statuses_set = get_post_meta($order_id,'marketking_order_statuses_set', true);

		if ($statuses_set !== 'yes'){
			if (marketking()->is_multivendor_order($order_id)){
				$order = wc_get_order($order_id);
				$suborders = marketking()->get_suborders_of_order($order_id);
				foreach ($suborders as $suborder){
					$suborder->set_status( $order->get_status() );
					$suborder->save();
				}

				// for main order, set the status to composite
				$order->update_status('wc-composite');
				$order->save();
			}

			update_post_meta($order_id,'marketking_order_statuses_set', 'yes');
		}

		
	}

	function shortcode_atts_products( $out, $pairs, $atts, $shortcode ) {
	    if ( isset ( $atts['vendor'] ) ) {
	    	$out['vendor'] = $atts['vendor'];
	    }
	    return $out;
	}
	function marketking_shortcode_vendor_products( $query_args, $attributes ) {       
	    if ( isset( $attributes['vendor'] ) ) {
	    	$query_args['author'] = $attributes['vendor'];
	    }
	    
	    return $query_args;
	}


	function marketking_template_orderdetails($template, $template_name, $args, $template_path, $default_path){

		if ( 'order-details.php' === basename( $template ) ) {
			if (isset($args['order_id'])){

				$order_id = $args['order_id'];

				$vendors = marketking()->get_vendors_of_order($order_id);
				// if multivendor order, get special marketking multivendor template for order details ( main order )
				if (count($vendors) > 1){
					$template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/order-details.php';
				}
			}	
		}
		
		return $template;
	}

	function marketking_template_vieworder($template, $template_name, $args, $template_path, $default_path){

		if ( 'view-order.php' === basename( $template ) ) {
			if (isset($args['order_id'])){

				$order_id = $args['order_id'];

				if (marketking()->is_suborder($order_id) || marketking()->is_multivendor_order($order_id)){
					$template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/view-order.php';
				}
				
			}	
		}
		
		return $template;
	}

	function marketking_template_order_received($template, $template_name, $args, $template_path, $default_path){

		if ( 'email-order-details.php' === basename( $template ) ) {

			// only for html emails for now
			if ($args['plain_text'] === false && $args['sent_to_admin'] === false){

				$order = $args['order'];
				$order_id = $order->get_id();

				if (marketking()->is_multivendor_order($order)){
					$template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/email-order-details.php';
				}
				
			}	
		}
		
		return $template;
	}

	function process_order_vendors($order_id, $posted_data = array(), $order = array()){

		$already_processed = get_post_meta($order_id,'marketking_already_processed_order', true);

		if ($already_processed !== 'yes'){

			if (is_array($order)){
				$order = wc_get_order($order_id);
			}

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
						$defertoadmin = 0; // amount deferred to admin from non-connected vendors
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
								update_post_meta($suborder_id,'marketking_paid_via_stripe','yes');

							} else {
								if ($non_connected === 'yes'){
									$defertoadmin+=$suborder->get_total();
								} else {
									// throw error because vendor is not connected and not connected setting is disabled
									$error = 1;
								}
							}
						}
					}
							
				}
			} else {

				// if not admin, set commission
				$vendor_id = marketking()->get_order_vendor($order_id);
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
								update_post_meta($order_id,'marketking_paid_via_stripe','yes');
							} else {

								if ($non_connected === 'yes'){
									$defertoadmin+=$order->get_total();
								} else {
									// throw error because vendor is not connected and not connected setting is disabled
									$error = 1;
								}
							}
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
						'total_admin_amount' => $order->get_total()->$totalvendorcommission,
						'total_admin_amount_after_application_fees' => $order->get_total()-$totalvendorcommission-$applicationfees,
						'total_application_fees' => $applicationfees,
						'error' => $error,
						'distribution_list' => $split_payers
					);
				
					$paylist = apply_filters( 'marketking_paylist_split_pay_payment_args', $paylist, $order );

					update_post_meta($order_id, 'marketking_stripe_order_paylist', $paylist);

				}
			}

			update_post_meta($order_id,'marketking_already_processed_order', 'yes');
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

	function load_dashicons_front_end() {
		wp_enqueue_style( 'dashicons' );
	}

	function message_multiple_vendors_cart(){

		$vendorsincart = marketking()->get_vendors_in_cart();

		if (count($vendorsincart) > 1){
			if (!empty(apply_filters('marketking_cart_multiple_vendors_message', get_option('marketking_cart_vendors_text_setting', esc_html__('The products in your cart are sold by multiple different vendor partners. The order will be placed simultaneously with all vendors and you will receive a package from each of them.','marketking-multivendor-marketplace-for-woocommerce'))))){
				wc_print_notice( apply_filters('marketking_cart_multiple_vendors_message', get_option('marketking_cart_vendors_text_setting', esc_html__('The products in your cart are sold by multiple different vendor partners. The order will be placed simultaneously with all vendors and you will receive a package from each of them.','marketking-multivendor-marketplace-for-woocommerce'))), 'notice' );
			}
		} else if (count($vendorsincart) === 1){
			$vendorid = reset($vendorsincart);

			echo '<input type="hidden" name="marketking_cart_vendor" id="marketking_cart_vendor" value="'.esc_attr($vendorid).'">';

		}

		echo '<input type="hidden" name="marketking_number_vendors_cart" id="marketking_number_vendors_cart" value="'.esc_attr(count($vendorsincart)).'">';
	}

	public function marketking_locate_template_cart( $template, $template_name, $template_path ) {
    	if (count(marketking()->get_vendors_in_cart()) > 1 or apply_filters('marketking_force_newcart_always', false)){
	        if ( 'cart.php' === basename( $template ) ) {
	        	$template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'templates/cart.php';
	        }
	    }
	    return $template;
    }

	function intercept_wc_template( $template, $template_name, $template_path ) {
		// not for plain emails
		if ( 'customer-new-account.php' === basename( $template ) && strpos($template, 'plain') === false) {
			// if vendor
			if (isset($_POST['marketking_registration_options_dropdown'])){
				$template = '/emails/templates/vendor-new-account.php';

		    	$template_directory = untrailingslashit( plugin_dir_path( __FILE__ ) );
		    	$template = $template_directory . $template;
			}					
		}
		return $template;
	}



	function store_list_modify_title( $title, $post_id = null ) {
		global $post;
		if (isset($post->ID)){
			if ( intval($post->ID) === intval(apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' ), 'post' , true) ) ){
			    if( in_the_loop() && is_singular()) {
			    	// check if is general page, or if vendor id is set
			    	if (empty(get_query_var('vendorid'))){ 
			    		// general page
			    	} else {
			    		// vendor page, change title
			    		$title = '';
			    	}
			        
			    }
			}
			
		}
	    return $title;
	}
	
	function action_woocommerce_shortcode_products_loop_no_results( $attributes ) {
		// if current page is stores page      
		global $post;
        if ( intval($post->ID) === intval(apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' ), 'post' , true) ) ){


        	$vacation = 'no';
        	// check if vendor vacation, and show message if so
        	if (defined('MARKETKINGPRO_DIR')){
        		if (intval(get_option('marketking_enable_vacation_setting', 1)) === 1){
        			// get current vendor
        			$store_url = get_query_var('vendorid');
        			if (!empty($store_url)){
        				$vendorid = marketking()->get_vendor_id_by_url($store_url);
        				if (marketking()->is_on_vacation($vendorid)){
        					$vacation_message = get_user_meta($vendorid,'marketking_vacation_message', true);
        					if (!empty($vacation_message)){
        						wc_print_notice($vacation_message,'notice');
        					}
        					$vacation = 'yes';
        				}
        			}
        		}
        	}

        	if ($vacation === 'no'){
        		esc_html_e('This vendor doesn\'t have any products yet...','marketking-multivendor-marketplace-for-woocommerce');
        	}
	    }
	}


	function marketking_filter_products_author( $query_args, $attributes ) { 
		// if current page is stores page      
		global $post;
		if (isset($post->ID)){
			if ( intval($post->ID) === intval(apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' ), 'post' , true) ) ){
	        	// get the author if any
	        	$store_url = get_query_var('vendorid');
	        	if (!empty($store_url)){
	        		$vendorid = marketking()->get_vendor_id_by_url($store_url);
	        		$query_args['author'] = $vendorid;
	        	}
		    }
		}

	    // if current page is shortcode for favorite stores products, 
	    global $marketking_favorite_stores_products;
	    if ($marketking_favorite_stores_products === 'yes'){
	    	$favorite_vendors = marketking()->get_favorite_vendors(get_current_user_id());
	    	$vendor_ids = array();
	    	foreach ($favorite_vendors as $vendor){
	    		array_push($vendor_ids, $vendor->ID);
	    	}
	    	$query_args['author__in'] = $vendor_ids;
	    }

	    return $query_args;
	}

	function marketking_rewrite_url(){
		$pageid = apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' ), 'post' , true);
       		$slug = get_page_uri( $pageid );


	    add_rewrite_rule(
	        '^'.$slug.'/([^/]*)/?([^/]*)/?([^/]*)/?',
	        'index.php?pagename='.$slug.'&vendorid=$matches[1]'.'&pagenr=$matches[2]'.'&pagenr2=$matches[3]',
	        'top'
	    );

	    flush_rewrite_rules();
	}

	function marketking_vendors_page($content){
		global $post;
		if (isset($post->ID)){
	        if ( intval($post->ID) === intval(apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' ), 'post' , true) ) ){

	        	if (in_the_loop()){

	        		$exception = false;
	        		// astra exception
	        		if (!empty(get_query_var('vendorid')) && defined('ASTRA_THEME_VERSION')){
	        			$exception = true;
	        		}

		        	static $has_run = false;
		        	if ($has_run === false or $exception){

			        	// check if is general page, or if vendor id is set
			        	if (empty(get_query_var('vendorid'))){ 
				            // stores page here
				            // get all vendors
				            $users = marketking()->get_all_vendors();


				            // affects whether the stores list displayed correctly below the header or above
				            $theme_style = wp_get_theme()->get_stylesheet();	
				            $themeexceptions = apply_filters('marketking_theme_exceptions_header', array('twentytwentythree', 'twentytwentytwo', 'twentytwentyone'));
				            if (!in_array($theme_style, $themeexceptions)){
				            	echo marketking()->display_stores_list($users);
				            } else {
				            	$content = marketking()->display_stores_list($users);
				            }

				        } else {
				        	$store_url = get_query_var('vendorid');
				        	$content = apply_filters('marketking_store_page_display_content', marketking()->get_store_content_by_url($store_url), $store_url);
				        }

				        $has_run = true;

				    }
				}
	        }
    	}
        return $content;
	}

	function product_vendor_tab_content(){
		?>
		<h3><?php esc_html_e('Vendor Information', 'marketking-multivendor-marketplace-for-woocommerce'); ?></h3>
		<?php
		global $post;
		echo '<strong>'.esc_html__('Vendor: ','marketking-multivendor-marketplace-for-woocommerce').'</strong>';
		$vendor_id = marketking()->get_product_vendor($post->ID);
		$store_name = marketking()->get_store_name_display($vendor_id);

		echo '<a href='.marketking()->get_store_link($vendor_id).'>'.esc_html($store_name).'</a>';

		// display badges if applicable
		if (defined('MARKETKINGPRO_DIR')){
			if (intval(get_option('marketking_enable_badges_setting', 1)) === 1){
				echo '<br>';
				marketkingpro()->display_vendor_badges($vendor_id);
			}
		}

		// rating
		$rating = marketking()->get_vendor_rating($vendor_id);
		// if there's any rating
		if (intval($rating['count'])!==0){
			echo '<br>';
			// show rating
			if (intval($rating['count']) === 1){
				$review = esc_html__('review','marketking-multivendor-marketplace-for-woocommerce');
			} else {
				$review = esc_html__('reviews','marketking-multivendor-marketplace-for-woocommerce');
			}
			echo '<strong>'.esc_html__('Rating:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.esc_html($rating['rating']).' '.esc_html__('rating from','marketking-multivendor-marketplace-for-woocommerce').' '.esc_html($rating['count']).' '.esc_html($review);
		}

		// company name
		$company = get_user_meta($vendor_id,'billing_company', true);
		if (!empty($company)){
			echo '<br><strong>'.esc_html__('Company:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.$company.'<br>';

		} else {
			echo '<br>';
		}

		// if email or phone, show contact info
		$showphone = get_user_meta($vendor_id,'marketking_show_store_phone', true);
		$showemail = get_user_meta($vendor_id,'marketking_show_store_email', true);
		$phone = get_user_meta($vendor_id,'billing_phone', true);
		$email = get_userdata($vendor_id)->user_email;

		if ($showphone === 'yes'){
			echo '<strong>'.esc_html__('Phone:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.esc_html($phone).'<br>';
		}
		if ($showemail === 'yes'){
			echo '<strong>'.esc_html__('Email:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.esc_html($email).'<br>';
		}

		do_action('marketking_vendor_details_product_page', $vendor_id);

		echo '<br>';

		

	}

	// add vendor info tab as well
	function product_vendor_tab( $tabs ) {
		global $post;

		if (apply_filters('marketking_show_vendor_product_page', true)){
		    $tabs['vendor'] = [
		        'title'    => esc_html__( 'Vendor Details', 'marketking-multivendor-marketplace-for-woocommerce' ),
		        'priority' => 90,
		        'callback' => array($this, 'product_vendor_tab_content'),
		    ];
		}

	    // if enquiries module enable, and if product inquiries enabled
	    if (defined('MARKETKINGPRO_DIR')){
	    	if (intval(get_option('marketking_enable_inquiries_setting', 1)) === 1){
	    		if (intval(get_option('marketking_enable_product_page_inquiries_setting', 1)) === 1){
	    			$tabs['inquiry'] = [
	    			    'title'    => esc_html__( 'Product Inquiry', 'marketking-multivendor-marketplace-for-woocommerce' ),
	    			    'priority' => 91,
	    			    'callback' => array($this, 'product_inquiry_tab_content'),
	    			];
	    		}
	    	}
	    }

	    // if enquiries module enable, and if product inquiries enabled
	    if (defined('MARKETKINGPRO_DIR')){
	    	if (intval(get_option('marketking_enable_support_setting', 1)) === 1){
	    		if (intval(get_option('marketking_show_support_single_product_setting', 1)) === 1){
	    			if ( marketking()->customer_has_purchased( '', get_current_user_id(), $post->ID ) ) {
		    			$tabs['support'] = [
		    			    'title'    => esc_html__( 'Product Support', 'marketking-multivendor-marketplace-for-woocommerce' ),
		    			    'priority' => 92,
		    			    'callback' => array($this, 'product_support_tab_content'),
		    			];
		    		}
	    		}
	    	}
	    }

	    // SPMV + Inside product tabs show other offers
	    if (defined('MARKETKINGPRO_DIR')){
	    	if (intval(get_option('marketking_enable_spmv_setting', 1)) === 1){
	    		if (get_option('marketking_offers_position_setting', 'belowproduct') === 'insideproducttabs'){
	    			$tabs['otheroffers'] = [
	    			    'title'    => esc_html__( 'Other Offers', 'marketking-multivendor-marketplace-for-woocommerce' ),
	    			    'priority' => 93,
	    			    'callback' => array($this, 'other_offers_tab_content'),
	    			];
	    		}
	    	}
	    }

	    return $tabs;
	}

	function product_support_tab_content(){
		global $post;

		marketkingpro()->get_product_support_content($post->ID);

	}


	function other_offers_tab_content(){
		do_action('marketking_show_other_offers');
	}

	function product_inquiry_tab_content(){
		global $post;
		marketkingpro()->get_inquiries_form(false, $post->ID);
	}

	function marketking_vendor_product_page_shortcode(){
		add_shortcode('marketking_vendor_product_page', array($this, 'marketking_vendor_product_page_shortcode_content'));
	}
	function marketking_vendors_list_shortcode(){
		add_shortcode('marketking_vendors_list', array($this, 'marketking_vendors_list_shortcode_content'));
	}
	function marketking_vendor_product_page_shortcode_content(){
		ob_start();
		echo $this->marketking_show_vendor_product_page();
		$content = ob_get_clean();
		return $content;
	}
	function marketking_vendors_list_shortcode_content($atts){

		ob_start();

		$atts = shortcode_atts(
	        array(
	            'group' => 'all',
	            'category' => 'all',
	            'vendors' => 'all',
	        ), 
	    $atts);

	    $category = $atts['category'];
	    $vendors = $atts['vendors'];

		if ($atts['group'] === 'all'){
			$users = get_users(array(
			    'meta_key'     => 'marketking_group',
			    'meta_value'   => 'none',
			    'meta_compare' => '!=',
			));
		} else {
			$users = get_users(array(
			    'meta_key'     => 'marketking_group',
			    'meta_value'   => $atts['group'],
			    'meta_compare' => '=',
			));
		}

		if ($category !== 'all'){
			// only users who match categories
			$categories = explode(',', $category);
			$categories_slugs = array();


			foreach ($categories as $cat){
				if (!empty($cat)){
					$term = get_term_by('slug', $cat, 'storecat');
					if ($term){
						$id = $term->term_id;
						array_push($categories_slugs, $id);
					}
					
				}
				
			}


			$usersfinal = array();
			foreach ($users as $user){
				// if user does not match, remove
				$user_categories = get_user_meta($user->ID,'marketking_store_categories', true);
				$match = 'no';

				if (!empty($user_categories)){
					foreach ($user_categories as $user_category){

						// category IDs
						if (in_array($user_category, $categories)){
							$match = 'yes';
							break;
						}

						// category slugs
						if (in_array($user_category, $categories_slugs)){
							$match = 'yes';
							break;
						}
					}
				}
				

				if ($match === 'yes'){
					array_push($usersfinal, $user);
				}
			}

			$users = $usersfinal;
		}

		$showcat = 'yes';
		if ($category !== 'all'){
			$showcat = 'no';
		}

		if ($vendors !== 'all'){
			$users = array();
			$vendors = array_filter(array_unique(explode(',', $vendors)));
			foreach ($vendors as $vendor_id){
				$user = new WP_User(trim($vendor_id));
				array_push($users, $user);
			}
		}	

	   	// showcat is the category selector dropdown. 
	   	// if the shortcode specifices certain categories, we don't want to show it

		echo marketking()->display_stores_list($users, $showcat);
		$content = ob_get_clean();

		return $content;
	}


	function marketking_show_vendor_product_page(){
		global $post;
		if (isset($post->ID) && apply_filters('marketking_show_vendor_product_page', true)){
			?><span class="marketking_vendor_product_text"><?php
			esc_html_e('Vendor: ','marketking-multivendor-marketplace-for-woocommerce');
			?></span><span class="marketking_vendor_product_store"><?php
			$vendor_id = marketking()->get_product_vendor($post->ID);
			$store_name = marketking()->get_store_name_display($vendor_id);

			echo '<a href='.marketking()->get_store_link($vendor_id).'>'.esc_html($store_name).'</a>';
			?></span><?php

			do_action('marketking_after_vendor_product_page_text', $vendor_id);
		}
	}

	function marketking_show_vendor_cart( $item_data, $cart_item ) {

		// do not apply in the cart page if using the newcart system and if there are multiple vendors
		if ((is_cart() && get_option( 'marketking_cart_display_setting', 'newcart' ) === 'newcart') && (count(marketking()->get_vendors_in_cart()) > 1)){
			return $item_data;
		}

		$vendor_id = marketking()->get_product_vendor( $cart_item['product_id'] );
		if (!empty($vendor_id) && $vendor_id !== 1) {
		   $item_data[] = array(
		       'name'  => esc_html__( 'Vendor', 'marketking-multivendor-marketplace-for-woocommerce' ),
		       'value' => marketking()->get_store_name_display($vendor_id),
		   );
		}
	   
	      
	    return $item_data;		    				   
	}

	function marketking_vendor_application_pending(){
		if (apply_filters('marketking_allow_logged_in_register_vendor', false)){
			if (!marketking()->is_vendor(get_current_user_id())){
				// if application pending
				if (marketking()->has_vendor_application_pending(get_current_user_id())){
					// show message
					?>
					<span class="marketking-application-pending"><?php esc_html_e('We are currently reviewing your vendor application and it is pending.','marketking-multivendor-marketplace-for-woocommerce'); ?></span>
					<?php
				}
			}
		}
	}

	function marketking_dashboard_button(){
		if (marketking()->is_vendor(get_current_user_id()) or marketking()->is_vendor_team_member()){
			?>
			<a href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)));?>" class="marketking_go_vendor_dashboard_link"><button class="marketking_go_vendor_dashboard_button"><?php esc_html_e('Go to the Vendor Dashboard', 'marketking-multivendor-marketplace-for-woocommerce'); ?></button></a>
			<?php
		}
	}

	function marketking_check_user_approval_on_login ($errors, $username, $password) {

		// First need to get the user object
		if (!empty($username)){
			$user = get_user_by('login', $username);
			if(!$user) {
				$user = get_user_by('email', $username);
				if(!$user) {
					return $errors;
				}
			}
		}
		if (isset($user->ID)){
			$user_status = get_user_meta($user->ID, 'marketking_account_approved', true);
			if($user_status === 'no'){
				$errors->add('access', esc_html__('Your account is waiting for approval. Until approved, you cannot login.','marketking-multivendor-marketplace-for-woocommerce'));
			}
		}
	    return $errors;
	}
	// Modify new account email - Add approval needed notice
	function marketking_modify_new_account_email( $email ) { 

		if ( $email->id === 'customer_new_account' ) {
			$user = get_user_by('email', $email->user_email);
			$approval_needed = get_user_meta($user->ID, 'marketking_account_approved', true);
			if ($approval_needed === 'no'){
				?>
				<p>
					<?php
					$text = esc_html__('Before you can login, your account requires manual approval. Our team will review it as soon as possible. Thank you for understanding.', 'marketking-multivendor-marketplace-for-woocommerce');
					$text = apply_filters('marketking_new_account_email_approval_notification', $text );
					echo esc_html($text);
					?>
				</p>
				<?php
			}
		}
	}

	// If user approval is manual, stop automatic login on registration
	function marketking_check_user_approval_on_registration($redirection_url) {
		$user_id = get_current_user_id();
		$user_approval = get_user_meta($user_id, 'marketking_account_approved', true);
		$redir_change = 'no';

		if ($user_approval === 'no'){

			// for separate b2b reg
		    $separate_page = get_option( 'marketking_registration_separate_my_account_page_setting', 'disabled' );
		    if ($separate_page !== 'disabled'){
		    	$redirection_url = get_permalink( $separate_page );
		    	$redir_change = 'yes';
		    }

		    if (apply_filters('marketking_allow_logged_in_register_vendor', false)){

		    	update_user_meta($user_id,'marketking_vendor_application_pending','yes');

		    	wc_add_notice( esc_html__('Your account has been succesfully created. We are now reviewing your application to become a vendor. Please wait to be approved.', 'marketking-multivendor-marketplace-for-woocommerce'), 'success' );	

		    } else {
		    	wp_logout();

		    	do_action( 'woocommerce_set_cart_cookies',  true );

		    	wc_add_notice( esc_html__('Thank you for registering. Your vendor account requires manual approval. Please wait to be approved.', 'marketking-multivendor-marketplace-for-woocommerce'), 'success' );	
		    }
			

		
		}


		if ($redir_change === 'no'){

			$my_account_link = get_permalink( wc_get_page_id( 'myaccount' ) );

			$redirection_url = add_query_arg( 'redir', 1, $my_account_link );
		}


		return $redirection_url;
	}

		// Allow file upload in registration for WooCommerce
	public static function marketking_custom_registration_fields_allow_file_upload() {
	   	echo 'enctype="multipart/form-data"';
	}


	// Save Custom Registration Fields
	function marketking_save_custom_registration_fields($user_id){

		if (get_user_meta($user_id, 'marketking_registration_data_saved', true) === 'yes'){
			// function has already run
			if (!apply_filters('marketking_allow_logged_in_register_vendor', false)){
				return;
			}
			
		} else {
			update_user_meta($user_id,'marketking_registration_data_saved', 'yes');
		}

		$custom_fields_string = '';

		// get all enabled custom fields
		$custom_fields = get_posts([
			    		'post_type' => 'marketking_field',
			    	  	'post_status' => 'publish',
			    	  	'numberposts' => -1,
	  		    	  	'meta_key' => 'marketking_field_sort_number',
	  	    	  	    'orderby' => 'meta_value_num',
	  	    	  	    'order' => 'ASC',
			    	  	'meta_query'=> array(
			    	  		'relation' => 'AND',
			                array(
		                        'key' => 'marketking_field_status',
		                        'value' => 1
			                ),

		            	)
			    	]);

		// save email into store email if email is available
		$userobj = new WP_User($user_id);
		if (!empty($userobj->user_email)){
			update_user_meta($user_id,'marketking_store_email', $userobj->user_email);
		}
		// loop through fields
		foreach ($custom_fields as $field){

			// if field is checkbox, check checkbox options and save them
			$field_type = get_post_meta($field->ID, 'marketking_field_field_type', true);

			if ($field_type === 'checkbox'){

				// add field to fields string
				$custom_fields_string .= $field->ID.',';

				$select_options = get_post_meta($field->ID, 'marketking_field_user_choices', true);
				$select_options = explode(',', $select_options);
				$i = 1;
				foreach ($select_options as $option){

					// get field and check if set
					$field_value = sanitize_text_field(filter_input(INPUT_POST, 'marketking_field_'.$field->ID.'_option_'.$i)); 
					if (intval($field_value) === 1){
						update_user_meta( $user_id, 'marketking_field_'.$field->ID.'_option_'.$i, $option);
						// if have a selected value, give a value of 1 to the field, so we know to display it in the backend
						update_user_meta( $user_id, 'marketking_field_'.$field->ID, 1);
					}
					$i++;
				}

			}

			// get field and check if set
			$field_value = sanitize_text_field(filter_input(INPUT_POST, 'marketking_field_'.$field->ID)); 
			if ($field_value !== NULL && $field_type !== 'checkbox'){
				update_user_meta( $user_id, 'marketking_field_'.$field->ID, $field_value);

				// Also set related field data as user meta.
				// Relevant fields: field type, label and user_choices

				// add field to fields string
				$custom_fields_string .= $field->ID.',';

				$field_type = get_post_meta($field->ID, 'marketking_field_field_type', true);
				$field_label = get_post_meta($field->ID, 'marketking_field_field_label', true);
				if ($field_type === 'file' ){
					if ( ! empty( $_FILES['marketking_field_'.$field->ID]['name'] ) ){
					// has already been checked for errors (type/size) in marketking_custom_registration_fields_check_errors function
				        require_once( ABSPATH . 'wp-admin/includes/image.php' );
						require_once( ABSPATH . 'wp-admin/includes/file.php' );
						require_once( ABSPATH . 'wp-admin/includes/media.php' );

				        // Upload the file
				        $attachment_id = media_handle_upload( 'marketking_field_'.$field->ID, 0 );
				        // Set attachment author as the user who uploaded it
				        $attachment_post = array(
				            'ID'          => $attachment_id,
				            'post_author' => $user_id
				        );
				        wp_update_post( $attachment_post );   

				        // set attachment id as user meta
				        update_user_meta( $user_id, 'marketking_field_'.$field->ID, $attachment_id );
				    }
				}

				// if field has billing connection, update billing user meta
				$billing_connection = get_post_meta($field->ID, 'marketking_field_billing_connection', true);
				if ($billing_connection !== 'none'){
					// special situation for countrystate combined field
					if($billing_connection === 'billing_countrystate'){
						if (!empty($field_value)){
							update_user_meta ($user_id, 'billing_country', $field_value);
						}
						
						// get state as well 
						$state_value = sanitize_text_field(filter_input(INPUT_POST, 'billing_state')); 
						if (!empty($state_value)){
							update_user_meta ($user_id, 'billing_state', $state_value);
						}
						
					} else {
						if (!empty($field_value)){
							// field value name is identical to billing user meta field name
							if ($billing_connection !== 'custom_mapping'){
								update_user_meta ($user_id, $billing_connection, $field_value);
							} else {
								update_user_meta ($user_id, sanitize_text_field(get_post_meta($field->ID, 'marketking_field_mapping', true)), $field_value);
							}
							// if field is first name or last name, add it to account details (Sync)
							if ($billing_connection === 'billing_first_name'){
								update_user_meta( $user_id, 'first_name', $field_value );
							} else if ($billing_connection === 'billing_last_name'){
								update_user_meta( $user_id, 'last_name', $field_value );
							} else if ($billing_connection === 'billing_store_name'){
								// max 25 characters for the store name
								if(strlen($field_value) > 25){
									$field_value = substr($field_value, 0, 25);
								}
								update_user_meta( $user_id, 'marketking_store_name', $field_value );
							} else if ($billing_connection === 'billing_store_url'){
								// check if the URL already exists. If it does, change it
								if (marketking()->store_url_exists($field_value)){
									// user has cheated, so change this value
								    $field_value = marketking()->generate_unique_url();
								}
								// if user does not already have a store url 
								if (empty(get_user_meta($user_id,'marketking_store_url', true))){
									update_user_meta( $user_id, 'marketking_store_url', $field_value );
								}
							} else if ($billing_connection === 'billing_phone'){
								update_user_meta( $user_id, 'marketking_store_phone', $field_value );
							}
						}
					}
				}
			}
		}

		// set string of custom field ids as meta
		if ($custom_fields_string !== ''){
			update_user_meta( $user_id, 'marketking_fields_string', $custom_fields_string);
		}

		// if VIES VAT Validation is Enabled AND VAT field is not empty, set vies-validated vat meta
		if (isset($_POST['marketking_vat_number_registration_field_number'])){
			$vat_number_inputted = sanitize_text_field($_POST['marketking_field_'.$_POST['marketking_vat_number_registration_field_number']]);
			$vat_number_inputted = strtoupper(str_replace(array('.', ' '), '', $vat_number_inputted));
			if (!(empty($vat_number_inputted))){
				// check if VIES Validation is enabled in settings
				$vat_field_vies_validation_setting = get_post_meta($_POST['marketking_vat_number_registration_field_number'], 'marketking_field_VAT_VIES_validation', true);
				// proceed only if VIES validation is enabled
				if (intval($vat_field_vies_validation_setting) === 1){
					update_user_meta($user_id, 'marketking_user_vat_status', 'validated_vat');
				}

				// if cookie, set validate vat also
				if (isset($_COOKIE['marketking_validated_vat_status'])){
					update_user_meta($user_id, 'marketking_user_vat_status', sanitize_text_field($_COOKIE['marketking_validated_vat_status']));
				}
			}
		}

		if (isset($_POST['marketking_registration_options_dropdown'])){

			$user_role = sanitize_text_field(filter_input(INPUT_POST, 'marketking_registration_options_dropdown'));
			if ($user_role !== NULL){
				update_user_meta( $user_id, 'marketking_registration_option', $user_role);
			}

			$user_role_id = explode('_', $user_role);
			if (count($user_role_id) > 1){
				$user_role_id = $user_role_id[1];
			} else {
				$user_role_id = 0;
			}
			$user_role_approval = get_post_meta($user_role_id, 'marketking_option_approval', true);
			$user_role_automatic_customer_group = get_post_meta($user_role_id, 'marketking_option_automatic_approval_group', true);



			update_user_meta( $user_id, 'marketking_user_choice', 'vendor');	
			if ($user_role_approval === 'manual'){
				update_user_meta( $user_id, 'marketking_account_approved', 'no');
				// check if there is a setting to automatically send the user to a particular customer group
				if ($user_role_automatic_customer_group !== 'none' && $user_role_automatic_customer_group !== NULL && $user_role_automatic_customer_group !== ''){
					update_user_meta($user_id,'marketking_default_approval_manual', $user_role_automatic_customer_group);
				}

				// if sales agent, save info as meta
				if (substr($user_role_automatic_customer_group, 0, 6) === 'salesk'){
					update_user_meta($user_id,'registration_option_agent', 'yes');
				}

				do_action('marketking_vendor_approval_manual_save', $user_id);


			} else if ($user_role_approval === 'automatic'){
				// check if there is a setting to automatically send the user to a particular customer group
				if ($user_role_automatic_customer_group !== 'none' && $user_role_automatic_customer_group !== NULL && $user_role_automatic_customer_group !== '' && substr($user_role_automatic_customer_group, 0, 6) !== 'salesk'){
					$group_id = explode('_',$user_role_automatic_customer_group)[1];
					update_user_meta( $user_id, 'marketking_customergroup', sanitize_text_field($group_id));
					update_user_meta( $user_id, 'marketking_group', sanitize_text_field($group_id));

					if (apply_filters('marketking_use_wp_roles', false)){
						$user_obj = new WP_User($user_id);
						$user_obj->add_role('marketking_role_'.$group_id);
					}
				}

				// if salesking agent
				if (substr($user_role_automatic_customer_group, 0, 6) === 'salesk'){
					$group_id = explode('_',$user_role_automatic_customer_group)[1];
					update_user_meta( $user_id, 'salesking_group', sanitize_text_field($group_id));
					update_user_meta( $user_id, 'salesking_user_choice', 'agent');
					update_user_meta( $user_id, 'salesking_assigned_agent', 'none');
				}

				do_action('marketking_vendor_approval_automatic_save', $user_id);

			}
		}

		// if customer is being approved automatically, and group is other than none, set customer as B2B
		$user_role = sanitize_text_field(filter_input(INPUT_POST, 'marketking_registration_options_dropdown'));

		$user_role_id = 0;
		if (!empty($user_role)){
			$user_role_id = explode('_', $user_role);
			if (count($user_role_id) > 1){
				$user_role_id = $user_role_id[1];
			}
		}
		
		$user_role_approval = get_post_meta($user_role_id, 'marketking_option_approval', true);
		$user_role_automatic_customer_group = get_post_meta($user_role_id, 'marketking_option_automatic_approval_group', true);

		// if not sales agent
		if (substr($user_role_automatic_customer_group, 0, 6) !== 'salesk'){
			if ($user_role_approval === 'automatic'){
				if ($user_role_automatic_customer_group !== 'none' && metadata_exists('post', $user_role_id, 'marketking_option_automatic_approval_group')){
					update_user_meta($user_id, 'marketking_b2buser', 'yes');
				} else {

					update_user_meta($user_id, 'marketking_b2buser', 'no');
					update_user_meta($user_id, 'marketking_customergroup', 'no');
				}
			}

			$user_is_b2b = get_user_meta($user_id,'marketking_b2buser', true);

			if (!isset($_POST['marketking_registration_options_dropdown']) && $user_is_b2b !== 'yes'){
				update_user_meta($user_id, 'marketking_b2buser', 'no');
				update_user_meta($user_id, 'marketking_customergroup', 'no');
			}
		}

		do_action('marketking_after_vendor_registration_saved', $user_id);

	}

	function marketking_rewrite_dashboard_url() {

		$pageid = apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true);
		$slug = get_post_field( 'post_name', $pageid );

	    add_rewrite_rule(
	        '^'.$slug.'/([^/]*)/?([^/]*)/?([^/]*)/?',
	        'index.php?pagename='.$slug.'&dashpage=$matches[1]'.'&pagenr=$matches[2]'.'&pagenr2=$matches[3]',
	        'top'
	    );

	    flush_rewrite_rules();

	}

	public function marketking_add_query_vars_filter( $vars ) {
	  $vars[] = "closed";
	  $vars[] = "dashpage";
	  $vars[] = "vendorid";
	  $vars[] = "pagenr";
	  $vars[] = "pagenr2";
	  $vars[] = "id";
	  $vars[] = "regid";
	  $vars[] = "affid";
	  $vars[] = "mycart";
	  return $vars;
	}

	public function marketking_template_include( $template ) {
		global $post;
		if (isset($post->ID)){
			if ( intval($post->ID) === intval(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true) ) ){
			    $template = wc_locate_template( 'marketking-dashboard-login.php' );
			}
		}
        
        return $template;
    }

    public function marketking_locate_template( $template ) {

        if ( 'marketking-dashboard-login.php' === basename( $template ) ) {
        	$template = apply_filters('marketking_dashboard_template',trailingslashit( plugin_dir_path( __FILE__ ) ) . 'dashboard/marketking-dashboard-login.php');

        }
        return $template;
    }

    function marketking_dashboard_header_shortcode(){
    	add_shortcode('marketking_dashboard_header', array($this, 'marketking_dashboard_header_shortcode_content'));
    }
    function marketking_dashboard_header_shortcode_content($atts = array(), $content = null){

    	wp_enqueue_style('marketking_dashboard', plugins_url('dashboard/assets/css/dashlite-header.css', __FILE__), $deps = array(), filemtime( plugin_dir_path( __FILE__ ) . 'dashboard/assets/css/dashlite-header.css' ));

    	$run_script = 'yes';
    	if (defined('ELEMENTOR_VERSION')){
    		$current_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    		$is_preview = \Elementor\Plugin::$instance->preview->is_preview() ?? false;

    		if(strpos($current_url, 'preview=1') !== false || $is_preview !== false) {
    			$run_script = 'no';
    		} 
    	}
    	
    	if ($run_script === 'yes'){
    		add_action('wp_footer', function(){
    			?>
    			<script type="text/javascript">
    				jQuery(document).ready(function(){
    					<?php 
    					$bundle_script = file_get_contents(plugins_url('dashboard/assets/js/bundle.js', __FILE__));
    					echo $bundle_script; 
    					?>
    				});
    			</script>
    			<?php
    		}, 1000);
    	}

		$atts = shortcode_atts(
	        array(
	            'messages' => 'yes',
	            'announcements' => 'yes',
	            'profile' => 'yes',
	        ), 
	    $atts);



    	ob_start();
    	$user_id = get_current_user_id();
    	$currentuser = new WP_User($user_id);
    	$user = $currentuser->user_login;
    	$currentuserlogin = $currentuser -> user_login;
    	$agent_group = get_user_meta($user_id, 'marketking_group', true);

    	if (marketking()->is_vendor($user_id)){
    		$messages = get_posts(
                array( 
                    'post_type' => 'marketking_message', // only conversations
                    'post_status' => 'publish',
                    'numberposts' => -1,
                    'fields' => 'ids',
                    'meta_query'=> array(   // only the specific user's conversations
                        'relation' => 'OR',
                        array(
                            'key' => 'marketking_message_user',
                            'value' => $currentuserlogin, 
                        ),
                        array(
                            'key' => 'marketking_message_message_1_author',
                            'value' => $currentuserlogin, 
                        )
                    )
                )
            );

            if (current_user_can('activate_plugins')){
            	// include shop messages
            	$messages2 = get_posts(
	                array( 
	                    'post_type' => 'marketking_message', // only conversations
	                    'post_status' => 'publish',
	                    'numberposts' => -1,
	                    'fields' => 'ids',
	                    'meta_query'=> array(   // only the specific user's conversations
	                        'relation' => 'OR',
	                        array(
	                            'key' => 'marketking_message_user',
	                            'value' => 'shop'
	                        ),
	                        array(
	                            'key' => 'marketking_message_message_1_author',
	                            'value' => 'shop'
	                        )
	                    )
	                )
	            );
	            $messages = array_merge($messages, $messages2);
            }
            $announcements = get_posts(array( 'post_type' => 'marketking_announce',
              'post_status'=>'publish',
              'numberposts' => -1,
              'meta_query'=> array(
                    'relation' => 'OR',
                    array(
                        'key' => 'marketking_group_'.$agent_group,
                        'value' => '1',
                    ),
                    array(
                        'key' => 'marketking_user_'.$user, 
                        'value' => '1',
                    ),
                )));
            // check how many are unread
            $unread_ann = 0;
            foreach ($announcements as $announcement){
                $read_status = get_user_meta($user_id,'marketking_announce_read_'.$announcement->ID, true);
                if (!$read_status || empty($read_status)){
                    $unread_ann++;
                }
            }
            // check how many are unread
            $unread_msg = 0;
            foreach ($messages as $message){
                // check that last msg is not current user
                $nr_messages = get_post_meta ($message, 'marketking_message_messages_number', true);
                $last_message_author = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_author', true);
                if ($last_message_author !== $currentuserlogin){
                    // chek if last read time is lower than last msg time
                    $last_read_time = get_user_meta($user_id,'marketking_message_last_read_'.$message, true);
                    if (!empty($last_read_time)){
                        $last_message_time = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_time', true);
                        if (floatval($last_read_time) < floatval($last_message_time)){
                            $unread_msg++;
                        }
                    } else {
                        $unread_msg++;
                    }
                }
            }

            // load profile pic in user avatar if it is set
            $profile_pic = get_user_meta($user_id,'marketking_profile_logo_image', true);

            if (!empty($profile_pic)){
              $profile_pic = marketking()->get_resized_image($profile_pic,'thumbnail');
                ?>
                <style type="text/css">
                    .dropdown-body .user-avatar, .simplebar-content .user-avatar{
                        background-size: contain !important;
                    }

                </style>
                <?php
            }


    		?>
    		<ul class="nk-quick-nav">
    		    <!-- HIDDEN COMMENTS FOR SCRIPTS PURPOSES -->
    		    <em class="icon ni ni-comments ni-comments-hidden"></em>
    		    <?php
    		    if ($atts['messages'] === 'yes'){

	    		    if (defined('MARKETKINGPRO_DIR')){
	    		        if (intval(get_option( 'marketking_enable_messages_setting', 1 )) === 1){
	    		            if(marketking()->vendor_has_panel('messages')){
	    		                ?>
	    		                <li class="dropdown chats-dropdown hide-mb-xs">
	    		                    <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-toggle="dropdown">
	    		                        <div class="icon-status <?php if ($unread_msg !== 0) {echo 'icon-status-info';}?>"><em class="icon ni ni-comments"></em></div>
	    		                    </a>
	    		                    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right">
	    		                        <div class="dropdown-head">
	    		                            <span class="sub-title nk-dropdown-title"><?php echo apply_filters('marketking_recent_messages_text',esc_html__('Recent Messages', 'marketking-multivendor-marketplace-for-woocommerce')); ?></span>
	    		                        </div>
	    		                        <div class="dropdown-body">
	    		                            <ul class="chat-list">
	    		                                <?php
	    		                                // remove closed messages
	    		                                $closedmsg = array();
	    		                                foreach ($messages as $message){
	    		                                    $nr_messages = get_post_meta ($message, 'marketking_message_messages_number', true);
	    		                                    $last_closed_time = get_user_meta($user_id,'marketking_message_last_closed_'.$message, true);
	    		                                    if (!empty($last_closed_time)){
	    		                                        $last_message_time = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_time', true);
	    		                                        if (floatval($last_closed_time) > floatval($last_message_time)){
	    		                                            array_push($closedmsg, $message);
	    		                                        }
	    		                                    }
	    		                                }

	    		                                $messagesarr = array_diff($messages,$closedmsg);
	    		                                // show last 6 messages that are active (not closed)
	    		                                $messagesarr = array_slice($messagesarr, 0, 6);
	    		                                foreach ($messagesarr as $message){ // message is a message thread e.g. conversation

	    		                                    $title = substr(get_the_title($message), 0, 65);
	    		                                    if (strlen($title) === 65){
	    		                                        $title .= '...';
	    		                                    }
	    		                                    $nr_messages = get_post_meta ($message, 'marketking_message_messages_number', true);

	    		                                    $last_message_time = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_time', true);
	    		                                    // build time string
	    		                                    // if today
	    		                                    if((time()-$last_message_time) < 86400){
	    		                                        // show time
	    		                                        $timestring = date_i18n( 'h:i A', $last_message_time+(get_option('gmt_offset')*3600) );
	    		                                    } else if ((time()-$last_message_time) < 172800){
	    		                                    // if yesterday
	    		                                        $timestring = 'Yesterday at '.date_i18n( 'h:i A', $last_message_time+(get_option('gmt_offset')*3600) );
	    		                                    } else {
	    		                                    // date
	    		                                        $timestring = date_i18n( get_option('date_format'), $last_message_time+(get_option('gmt_offset')*3600) ); 
	    		                                    }

	    		                                    $last_message = get_post_meta ($message, 'marketking_message_message_'.$nr_messages, true);
	    		                                    // first 100 chars
	    		                                    $last_message = substr($last_message, 0, 100);

	    		                                    // check if message is unread
	    		                                    $is_unread = '';
	    		                                    $last_message_author = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_author', true);
	    		                                    if ($last_message_author !== $currentuserlogin){
	    		                                        $last_read_time = get_user_meta($user_id,'marketking_message_last_read_'.$message, true);
	    		                                        if (!empty($last_read_time)){
	    		                                            $last_message_time = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_time', true);
	    		                                            if (floatval($last_read_time) < floatval($last_message_time)){
	    		                                                $is_unread = 'is-unread';
	    		                                            }
	    		                                        } else {
	    		                                            $is_unread = 'is-unread';
	    		                                        }
	    		                                    } 
	    		                              
	    		                                   
	    		                                    ?>
	    		                                   <li class="chat-item <?php echo esc_attr($is_unread);?>">
                                                        <a class="chat-link" href="<?php echo get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)).'messages?id='.esc_attr($message);?>">

                                                            <?php

                                                            $otherparty = marketking()->get_other_chat_party($message);
                                                            $icon = marketking()->get_display_icon_image($otherparty);
                                                          
                                                            ?>
                                                            <div class="chat-media user-avatar" style="<?php
                                                            if (strlen($icon) != 2){
                                                                echo 'background-image: url('.$icon.') !important;background-size: contain!important;';
                                                            }
                                                            ?>">
                                                                <span><?php 
                                                                if (strlen($icon) == 2){
                                                                    echo esc_html($icon);
                                                                }
                                                                ?></span>
                                                            </div>
                                                            <div class="chat-info">
                                                                <div class="chat-from">
                                                                    <div class="name"><?php echo esc_html($title);?></div>
                                                                    <span class="time"><?php echo esc_html($timestring);?></span>
                                                                </div>
                                                                <div class="chat-context">
                                                                    <div class="text"><?php echo esc_html(strip_tags($last_message));?></div>

                                                                </div>
                                                            </div>
	    		                                        </a>
	    		                                    </li><!-- .chat-item -->
	    		                                    <?php

	    		                                }
	    		                                ?>
	    		                            </ul><!-- .chat-list -->
	    		                        </div><!-- .nk-dropdown-body -->
	    		                        <div class="dropdown-foot center">
	    		                            <a href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)).'messages'); ?>"><?php esc_html_e('View All', 'marketking-multivendor-marketplace-for-woocommerce'); ?></a>
	    		                        </div>
	    		                    </div>
	    		                </li>
	    		                <?php
	    		            }
	    		        }
	    		    }
	    		}
    		    ?>
    		    <?php

    		    if ($atts['announcements'] === 'yes'){

	    		    if (defined('MARKETKINGPRO_DIR')){
	    		        if (intval(get_option( 'marketking_enable_announcements_setting', 1 )) === 1){
	    		            if(marketking()->vendor_has_panel('announcements')){
	    		                ?>
	    		                <li class="dropdown notification-dropdown">
	    		                    <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-toggle="dropdown">
	    		                        <div class="icon-status <?php if ($unread_ann !== 0) {echo 'icon-status-info';}?>"><em class="icon ni ni-bell"></em></div>
	    		                    </a>
	    		                    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right">
	    		                        <div class="dropdown-head">
	    		                            <span class="sub-title nk-dropdown-title"><?php echo apply_filters('marketking_unread_announcements_text',esc_html__('Unread Announcements', 'marketking-multivendor-marketplace-for-woocommerce')); ?></span>
	    		                        </div>
	    		                        <div class="dropdown-body">
	    		                            <?php
	    		                            // show all announcements
	    		                            $i=1;
	    		                            foreach ($announcements as $announcement){
	    		                                $read_status = get_user_meta($user_id,'marketking_announce_read_'.$announcement->ID, true);
	    		                                if (!$read_status || empty($read_status)){
	    		                                    // is unread, so let's display it
	    		                                    $i++;
	    		                                } else {
	    		                                    continue;
	    		                                }

	    		                                if ($i>6){
	    		                                    continue;
	    		                                }

	    		                                ?>
	    		                                <div class="nk-notification">
	    		                                    <div class="nk-notification-item dropdown-inner">
	    		                                        <div class="nk-notification-icon">
	    		                                            <em class="icon icon-circle bg-warning-dim ni ni-curve-down-right"></em>
	    		                                        </div>
	    		                                        <div class="nk-notification-content">
	    		                                            <a href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)).'announcement/?id='.esc_attr($announcement->ID)); ?>"><div class="nk-notification-text"><?php echo esc_html($announcement->post_title);?></div></a>
	    		                                            <div class="nk-notification-time"><?php echo esc_html(get_the_date(get_option( 'date_format' ), $announcement));?></div>
	    		                                        </div>
	    		                                    </div>
	    		                                </div><!-- .nk-notification -->
	    		                                <?php
	    		                            }
	    		                            ?>
	    		                        </div><!-- .nk-dropdown-body -->
	    		                        <div class="dropdown-foot center">
	    		                            <a href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)).'announcements'); ?>"><?php esc_html_e('View All', 'marketking-multivendor-marketplace-for-woocommerce'); ?></a>
	    		                        </div>
	    		                    </div>
	    		                </li>
	    		                <?php
	    		            }
	    		        }
	    		    }
	    		}

	    		if ($atts['profile'] === 'yes'){

	    		    ?>
	    		    <li class="dropdown user-dropdown">
	    		        <a href="#" class="dropdown-toggle mr-n1" data-toggle="dropdown">
	    		            <div class="user-toggle">
	    		            	<?php
	    		            	$icon = marketking()->get_display_icon_image($user_id);
	    		            	?>
	    		                <div class="user-avatar sm" <?php
                                        if (strlen($icon)!=2){ echo 'style="background-image: url(\''.$icon.'\');background-size:contain !important;"';}
                                    ?>>
	    		                    <?php 
	    		                        if (strlen($icon)==2){
	    		                            echo $icon;
	    		                        }
	    		                        ?>
	    		                    
	    		                </div>
	    		                <div class="user-info d-none d-xl-block">
	    		                    <div class="user-status user-status-active"><?php esc_html_e('Vendor','marketking-multivendor-marketplace-for-woocommerce');?></div>
	    		                    <div class="user-name dropdown-indicator"><?php 
	    		                        $storename = marketking()->get_store_name_display($user_id);
	    		                        $firstlastname = $currentuser->first_name.' '.$currentuser->last_name;
	    		                        if(empty($storename)){
	    		                            echo esc_html($firstlastname);
	    		                        } else {
	    		                            echo esc_html($storename);
	    		                        }
	    		                        ?></div>
	    		                        
	    		                </div>
	    		            </div>
	    		        </a>
	    		        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
	    		            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
	    		                <div class="user-card">
	    		                    <div class="user-avatar" <?php
                                        	if (strlen($icon)!=2){ echo 'style="background-image: url(\''.$icon.'\');background-size:contain !important;"';}
                                    	?>>
	    		                        <span><?php 
		    		                        if (strlen($icon)==2){
		    		                            echo $icon;
		    		                        }

	    		                        ?></span>
	    		                    </div>
	    		                    <div class="user-info">
	    		                        <span class="lead-text"><?php 
	    		                        if(empty($storename)){
	    		                            echo esc_html($firstlastname);
	    		                        } else {
	    		                            echo esc_html($storename);
	    		                        }
	    		                        ?></span>
	    		                        <span class="sub-text"><?php 
	    		                        if(!empty($storename)){
	    		                            echo esc_html($firstlastname);
	    		                        }

	    		                         ?></span>
	    		                    </div>
	    		                </div>
	    		            </div>
	    		            <div class="dropdown-inner">
	    		                <ul class="link-list">
	    		                    <li><a href="<?php echo esc_attr(marketking()->get_store_link($user_id));?>"><em class="icon ni ni-home"></em><span><?php esc_html_e('Go to My Store','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
	    		                </ul>

	    		            </div>
	    		            <?php
	    		            if (!marketking()->is_vendor_team_member()){
	    		                ?>
	    		                <div class="dropdown-inner">
	    		                    <ul class="link-list">
	    		                        <li><a href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'profile';?>"><em class="icon ni ni-account-setting-fill"></em><span><?php esc_html_e('Store Settings','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
	    		                    </ul>

	    		                </div>
	    		                <?php
	    		            }
	    		            ?>
	    		            <div class="dropdown-inner">
	    		                <ul class="link-list">
	    		                    <li><a href="<?php echo esc_url(wp_logout_url()); ?>"><em class="icon ni ni-signout"></em><span><?php esc_html_e('Sign out','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
	    		                </ul>
	    		            </div>
	    		        </div>
	    		    </li>
	    		    <?php
	    		}
	    		?>
    		</ul>
    		<?php
    	}
    	

    	$content = ob_get_clean();
    	return $content;
    }

    function enqueue_dashboard_woocommerce_resources(){

    	// 1. INCLUDE FUNCTIONS

    	if (!defined('MARKETKING_WC_DIR_ADMIN')){
    		define('MARKETKING_WC_DIR_ADMIN', dirname( WC_PLUGIN_FILE ) . '/includes/admin');
    	}
    	include_once MARKETKING_WC_DIR_ADMIN . '/wc-admin-functions.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/wc-meta-box-functions.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-post-types.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-taxonomies.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-menus.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-customize.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-notices.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-assets.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-api-keys.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-webhooks.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-pointers.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-importers.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-exporters.php';

    	require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );


    	include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks.php';
    	include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-event.php';
    	include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-client.php';
    	include_once WC_ABSPATH . 'includes/tracks/class-wc-tracks-footer-pixel.php';
    	include_once WC_ABSPATH . 'includes/tracks/class-wc-site-tracking.php';

    	// Help Tabs.
    	if ( apply_filters( 'woocommerce_enable_admin_help_tab', true ) ) {
    	    include_once MARKETKING_WC_DIR_ADMIN . '/class-wc-admin-help.php';
    	}

    	// Helper.
    	include_once MARKETKING_WC_DIR_ADMIN . '/helper/class-wc-helper.php';

    	// Marketplace suggestions & related REST API.
    	include_once MARKETKING_WC_DIR_ADMIN . '/marketplace-suggestions/class-wc-marketplace-suggestions.php';
    	include_once MARKETKING_WC_DIR_ADMIN . '/marketplace-suggestions/class-wc-marketplace-updater.php';


    	if ( !function_exists( 'get_current_screen' ) ) { 
    	   require_once ABSPATH . '/wp-admin/includes/screen.php'; 
    	} 

    	// initialize classes that did not initialize correctly (for whatever reason)
    	$classes = array(
			'WC_Meta_Box_Product_Images' => 'includes/admin/meta-boxes/class-wc-meta-box-product-images.php',	
			'WC_Meta_Box_Order_Items' => 'includes/admin/meta-boxes/class-wc-meta-box-order-items.php',
    		'WC_Order_Item_Product' => 'includes/class-wc-order-item-product.php',
    		'WC_Order_Item_Coupon' => 'includes/class-wc-order-item-coupon.php',
    		'WC_Order_Item_Fee' => 'includes/class-wc-order-item-fee.php',
    		'WC_Order_Item_Shipping' => 'includes/class-wc-order-item-shipping.php', 
    		'WC_Order_Item_Tax' => 'includes/class-wc-order-item-tax.php',
    	);

    	foreach ($classes as $class => $fileurl){
    		if (!class_exists($class)){
    			include_once('WC_ABSPATH'.$fileurl);
    		}
    	}    	

    	// 2. INCLUDE ADMIN ASSETS
    	global $wp_scripts;

    	$version   = '1';

    	global $wp_query, $post;

    	$wc_screen_id = sanitize_title( esc_html__( 'WooCommerce', 'woocommerce' ) );
    	$suffix       = '';

    	// Register admin styles.
    	wp_register_style( 'woocommerce_admin_menu_styles', WC()->plugin_url() . '/assets/css/menu.css', array(), $version );
    	wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), $version );
    	wp_register_style( 'jquery-ui-style', WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), $version );
    	wp_register_style( 'woocommerce_admin_dashboard_styles', WC()->plugin_url() . '/assets/css/dashboard.css', array(), $version );
    	wp_register_style( 'woocommerce_admin_print_reports_styles', WC()->plugin_url() . '/assets/css/reports-print.css', array(), $version, 'print' );
    	wp_register_style( 'woocommerce_admin_marketplace_styles', WC()->plugin_url() . '/assets/css/marketplace-suggestions.css', array(), $version );
    	wp_register_style( 'woocommerce_admin_privacy_styles', WC()->plugin_url() . '/assets/css/privacy.css', array(), $version );

    	// Add RTL support for admin styles.
    	wp_style_add_data( 'woocommerce_admin_menu_styles', 'rtl', 'replace' );
    	wp_style_add_data( 'woocommerce_admin_styles', 'rtl', 'replace' );
    	wp_style_add_data( 'woocommerce_admin_dashboard_styles', 'rtl', 'replace' );
    	wp_style_add_data( 'woocommerce_admin_print_reports_styles', 'rtl', 'replace' );
    	wp_style_add_data( 'woocommerce_admin_marketplace_styles', 'rtl', 'replace' );
    	wp_style_add_data( 'woocommerce_admin_privacy_styles', 'rtl', 'replace' );

		wp_register_style( 'woocommerce-general', WC()->plugin_url() . '/assets/css/woocommerce.css', array(), $version );
		wp_style_add_data( 'woocommerce-general', 'rtl', 'replace' );

    	// Sitewide menu CSS.
    	wp_enqueue_style( 'woocommerce_admin_menu_styles' );

    	// Admin styles for WC pages only.
		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_enqueue_style( 'jquery-ui-style' );
		wp_enqueue_style( 'wp-color-picker' );


		// Register scripts.
		wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), $version );
		wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), $version, true );
		wp_register_script( 'round', WC()->plugin_url() . '/assets/js/round/round' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'wc-admin-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round', 'wc-enhanced-select', 'plupload-all', 'stupidtable', 'jquery-tiptip' ), $version );
		wp_register_script( 'zeroclipboard', WC()->plugin_url() . '/assets/js/zeroclipboard/jquery.zeroclipboard' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'qrcode', WC()->plugin_url() . '/assets/js/jquery-qrcode/jquery.qrcode' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'stupidtable', WC()->plugin_url() . '/assets/js/stupidtable/stupidtable' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'serializejson', WC()->plugin_url() . '/assets/js/jquery-serializejson/jquery.serializejson' . $suffix . '.js', array( 'jquery' ), '2.8.1' );
		wp_register_script( 'flot', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'flot-resize', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.resize' . $suffix . '.js', array( 'jquery', 'flot' ), $version );
		wp_register_script( 'flot-time', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.time' . $suffix . '.js', array( 'jquery', 'flot' ), $version );
		wp_register_script( 'flot-pie', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.pie' . $suffix . '.js', array( 'jquery', 'flot' ), $version );
		wp_register_script( 'flot-stack', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.stack' . $suffix . '.js', array( 'jquery', 'flot' ), $version );
		wp_register_script( 'wc-settings-tax', WC()->plugin_url() . '/assets/js/admin/settings-views-html-settings-tax' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-blockui' ), $version );
		wp_register_script( 'wc-backbone-modal', WC()->plugin_url() . '/assets/js/admin/backbone-modal' . $suffix . '.js', array( 'underscore', 'backbone', 'wp-util' ), $version );
		wp_register_script( 'wc-shipping-zones', WC()->plugin_url() . '/assets/js/admin/wc-shipping-zones' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-ui-sortable', 'wc-enhanced-select', 'wc-backbone-modal' ), $version );
		wp_register_script( 'wc-shipping-zone-methods', WC()->plugin_url() . '/assets/js/admin/wc-shipping-zone-methods' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-ui-sortable', 'wc-backbone-modal' ), $version );
		wp_register_script( 'wc-shipping-classes', WC()->plugin_url() . '/assets/js/admin/wc-shipping-classes' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone' ), $version );
		wp_register_script( 'wc-clipboard', WC()->plugin_url() . '/assets/js/admin/wc-clipboard' . $suffix . '.js', array( 'jquery' ), $version );
		wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );
		wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.6' );
		wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'selectWoo' ), $version );
		wp_register_script( 'js-cookie', WC()->plugin_url() . '/assets/js/js-cookie/js.cookie' . $suffix . '.js', array(), '2.1.4', true );

		wp_localize_script(
			'wc-enhanced-select',
			'wc_enhanced_select_params',
			array(
				'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
				'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
				'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
				'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
				'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
				'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
				'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'search_products_nonce'           => wp_create_nonce( 'search-products' ),
				'search_customers_nonce'          => wp_create_nonce( 'search-customers' ),
				'search_categories_nonce'         => wp_create_nonce( 'search-categories' ),
				'search_taxonomy_terms_nonce'     => wp_create_nonce( 'search-taxonomy-terms' ),
				'search_product_attributes_nonce' => wp_create_nonce( 'search-product-attributes' ),
				'search_pages_nonce'              => wp_create_nonce( 'search-pages' ),
			)
		);

		wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/accounting/accounting' . $suffix . '.js', array( 'jquery' ), '0.4.2' );
		wp_localize_script(
			'accounting',
			'accounting_params',
			array(
				'mon_decimal_point' => wc_get_price_decimal_separator(),
			)
		);

		wp_register_script( 'wc-orders', WC()->plugin_url() . '/assets/js/admin/wc-orders' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-blockui' ), $version );
		wp_localize_script(
			'wc-orders',
			'wc_orders_params',
			array(
				'ajax_url'      => admin_url( 'admin-ajax.php' ),
				'preview_nonce' => wp_create_nonce( 'woocommerce-preview-order' ),
			)
		);

		// WooCommerce admin pages.
		wp_enqueue_script( 'iris' );
		wp_enqueue_script( 'woocommerce_admin' );
		wp_enqueue_script( 'wc-enhanced-select' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );

		$locale  = localeconv();
		$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

		$params = array(
			/* translators: %s: decimal */
			'i18n_decimal_error'                => sprintf( esc_html__( 'Please enter with one decimal point (%s) without thousand separators.', 'woocommerce' ), $decimal ),
			/* translators: %s: price decimal separator */
			'i18n_mon_decimal_error'            => sprintf( esc_html__( 'Please enter with one monetary decimal point (%s) without thousand separators and currency symbols.', 'woocommerce' ), wc_get_price_decimal_separator() ),
			'i18n_country_iso_error'            => esc_html__( 'Please enter in country code with two capital letters.', 'woocommerce' ),
			'i18n_sale_less_than_regular_error' => esc_html__( 'Please enter in a value less than the regular price.', 'woocommerce' ),
			'i18n_delete_product_notice'        => esc_html__( 'This product has produced sales and may be linked to existing orders. Are you sure you want to delete it?', 'woocommerce' ),
			'i18n_remove_personal_data_notice'  => esc_html__( 'This action cannot be reversed. Are you sure you wish to erase personal data from the selected orders?', 'woocommerce' ),
			'decimal_point'                     => $decimal,
			'mon_decimal_point'                 => wc_get_price_decimal_separator(),
			'ajax_url'                          => admin_url( 'admin-ajax.php' ),
			'strings'                           => array(
				'import_products' => esc_html__( 'Import', 'woocommerce' ),
				'export_products' => esc_html__( 'Export', 'woocommerce' ),
			),
			'nonces'                            => array(
				'gateway_toggle' => wp_create_nonce( 'woocommerce-toggle-payment-gateway-enabled' ),
			),
			'urls'                              => array(
				'import_products' => current_user_can( 'import' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ) : null,
				'export_products' => current_user_can( 'export' ) ? esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ) : null,
			),
		);

		wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );

		// Edit product category pages.

		// Products.
		wp_enqueue_script( 'woocommerce_quick-edit', WC()->plugin_url() . '/assets/js/admin/quick-edit' . $suffix . '.js', array( 'jquery', 'woocommerce_admin' ), $version );

		$params = array(
			'strings' => array(
				'allow_reviews' => esc_js( esc_html__( 'Enable reviews', 'woocommerce' ) ),
			),
		);
		wp_localize_script( 'woocommerce_quick-edit', 'woocommerce_quick_edit', $params );


		// Meta boxes.
		wp_register_script( 'wc-admin-product-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-product' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'media-models' ), $version );
		wp_register_script( 'wc-admin-variation-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-product-variation' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'serializejson', 'media-models' ), $version );

		wp_enqueue_script( 'wc-admin-product-meta-boxes' );
		wp_enqueue_script( 'wc-admin-variation-meta-boxes' );

		// post id edited parameter
		$postid = strval(sanitize_text_field(marketking()->get_pagenr_query_var()));
		if (empty($postid)){
			$postid = 0;
		}

		$params = array(
			'post_id'                             => ( $postid !== 0) ? $postid : '',
			'plugin_url'                          => WC()->plugin_url(),
			'ajax_url'                            => admin_url( 'admin-ajax.php' ),
			'woocommerce_placeholder_img_src'     => wc_placeholder_img_src(),
			'add_variation_nonce'                 => wp_create_nonce( 'add-variation' ),
			'link_variation_nonce'                => wp_create_nonce( 'link-variations' ),
			'delete_variations_nonce'             => wp_create_nonce( 'delete-variations' ),
			'load_variations_nonce'               => wp_create_nonce( 'load-variations' ),
			'save_variations_nonce'               => wp_create_nonce( 'save-variations' ),
			'bulk_edit_variations_nonce'          => wp_create_nonce( 'bulk-edit-variations' ),
			/* translators: %d: Number of variations */
			'i18n_link_all_variations'            => esc_html__( 'Are you sure you want to link all variations? This will create a new variation for each and every possible combination of variation attributes (max %d per run).', 'woocommerce' ),
			'i18n_enter_a_value'                  => esc_js( esc_html__( 'Enter a value', 'woocommerce' ) ),
			'i18n_enter_menu_order'               => esc_js( esc_html__( 'Variation menu order (determines position in the list of variations)', 'woocommerce' ) ),
			'i18n_enter_a_value_fixed_or_percent' => esc_js( esc_html__( 'Enter a value (fixed or %)', 'woocommerce' ) ),
			'i18n_delete_all_variations'          => esc_js( esc_html__( 'Are you sure you want to delete all variations? This cannot be undone.', 'woocommerce' ) ),
			'i18n_last_warning'                   => esc_js( esc_html__( 'Last warning, are you sure?', 'woocommerce' ) ),
			'i18n_choose_image'                   => esc_js( esc_html__( 'Choose an image', 'woocommerce' ) ),
			'i18n_set_image'                      => esc_js( esc_html__( 'Set variation image', 'woocommerce' ) ),
			'i18n_variation_added'                => esc_js( esc_html__( 'variation added', 'woocommerce' ) ),
			'i18n_variations_added'               => esc_js( esc_html__( 'variations added', 'woocommerce' ) ),
			'i18n_no_variations_added'            => esc_js( esc_html__( 'No variations added', 'woocommerce' ) ),
			'i18n_remove_variation'               => esc_js( esc_html__( 'Are you sure you want to remove this variation?', 'woocommerce' ) ),
			'i18n_scheduled_sale_start'           => esc_js( esc_html__( 'Sale start date (YYYY-MM-DD format or leave blank)', 'woocommerce' ) ),
			'i18n_scheduled_sale_end'             => esc_js( esc_html__( 'Sale end date (YYYY-MM-DD format or leave blank)', 'woocommerce' ) ),
			'i18n_edited_variations'              => esc_js( esc_html__( 'Save changes before changing page?', 'woocommerce' ) ),
			'i18n_variation_count_single'         => esc_js( esc_html__( '%qty% variation', 'woocommerce' ) ),
			'i18n_variation_count_plural'         => esc_js( esc_html__( '%qty% variations', 'woocommerce' ) ),
			'variations_per_page'                 => absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) ),
		);

		wp_localize_script( 'wc-admin-variation-meta-boxes', 'woocommerce_admin_meta_boxes_variations', $params );
		
		$default_location = wc_get_customer_default_location();

		wp_enqueue_script( 'wc-admin-order-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-order' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'wc-backbone-modal', 'selectWoo', 'wc-clipboard' ), $version );
		wp_localize_script(
			'wc-admin-order-meta-boxes',
			'woocommerce_admin_meta_boxes_order',
			array(
				'countries'              => wp_json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
				'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
				'default_country'        => isset( $default_location['country'] ) ? $default_location['country'] : '',
				'default_state'          => isset( $default_location['state'] ) ? $default_location['state'] : '',
				'placeholder_name'       => esc_attr__( 'Name (required)', 'woocommerce' ),
				'placeholder_value'      => esc_attr__( 'Value (required)', 'woocommerce' ),
			)
		);

		wp_enqueue_script( 'wc-admin-coupon-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-coupon' . $suffix . '.js', array( 'wc-admin-meta-boxes' ), $version );
		wp_localize_script(
			'wc-admin-coupon-meta-boxes',
			'woocommerce_admin_meta_boxes_coupon',
			array(
				'generate_button_text' => esc_html__( 'Generate coupon code', 'woocommerce' ),
				'characters'           => apply_filters( 'woocommerce_coupon_code_generator_characters', 'ABCDEFGHJKMNPQRSTUVWXYZ23456789' ),
				'char_length'          => apply_filters( 'woocommerce_coupon_code_generator_character_length', 8 ),
				'prefix'               => apply_filters( 'woocommerce_coupon_code_generator_prefix', '' ),
				'suffix'               => apply_filters( 'woocommerce_coupon_code_generator_suffix', '' ),
			)
		);

		$currency           = '';
		$remove_item_notice = esc_html__( 'Are you sure you want to remove the selected items?', 'woocommerce' );

		if ( $postid && in_array( get_post_type( $postid ), wc_get_order_types( 'order-meta-boxes' ) ) ) {
			$order = wc_get_order( $postid );
			if ( $order ) {
				$currency = $order->get_currency();

				if ( ! $order->has_status( array( 'pending', 'failed', 'cancelled' ) ) ) {
					$remove_item_notice = $remove_item_notice . ' ' . esc_html__( "You may need to manually restore the item's stock.", 'woocommerce' );
				}
			}
		}

		$params = array(
			'remove_item_notice'            => $remove_item_notice,
			'i18n_select_items'             => esc_html__( 'Please select some items.', 'woocommerce' ),
			'i18n_do_refund'                => esc_html__( 'Are you sure you wish to process this refund? This action cannot be undone.', 'woocommerce' ),
			'i18n_delete_refund'            => esc_html__( 'Are you sure you wish to delete this refund? This action cannot be undone.', 'woocommerce' ),
			'i18n_delete_tax'               => esc_html__( 'Are you sure you wish to delete this tax column? This action cannot be undone.', 'woocommerce' ),
			'remove_item_meta'              => esc_html__( 'Remove this item meta?', 'woocommerce' ),
			'remove_attribute'              => esc_html__( 'Remove this attribute?', 'woocommerce' ),
			'name_label'                    => esc_html__( 'Name', 'woocommerce' ),
			'remove_label'                  => esc_html__( 'Remove', 'woocommerce' ),
			'click_to_toggle'               => esc_html__( 'Click to toggle', 'woocommerce' ),
			'values_label'                  => esc_html__( 'Value(s)', 'woocommerce' ),
			'text_attribute_tip'            => esc_html__( 'Enter some text, or some attributes by pipe (|) separating values.', 'woocommerce' ),
			'visible_label'                 => esc_html__( 'Visible on the product page', 'woocommerce' ),
			'used_for_variations_label'     => esc_html__( 'Used for variations', 'woocommerce' ),
			'new_attribute_prompt'          => esc_html__( 'Enter a name for the new attribute term:', 'woocommerce' ),
			'calc_totals'                   => esc_html__( 'Recalculate totals? This will calculate taxes based on the customers country (or the store base country) and update totals.', 'woocommerce' ),
			'copy_billing'                  => esc_html__( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
			'load_billing'                  => esc_html__( "Load the customer's billing information? This will remove any currently entered billing information.", 'woocommerce' ),
			'load_shipping'                 => esc_html__( "Load the customer's shipping information? This will remove any currently entered shipping information.", 'woocommerce' ),
			'featured_label'                => esc_html__( 'Featured', 'woocommerce' ),
			'prices_include_tax'            => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
			'tax_based_on'                  => esc_attr( get_option( 'woocommerce_tax_based_on' ) ),
			'round_at_subtotal'             => esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
			'no_customer_selected'          => esc_html__( 'No customer selected', 'woocommerce' ),
			'plugin_url'                    => WC()->plugin_url(),
			'ajax_url'                      => admin_url( 'admin-ajax.php' ),
			'order_item_nonce'              => wp_create_nonce( 'order-item' ),
			'add_attribute_nonce'           => wp_create_nonce( 'add-attribute' ),
			'save_attributes_nonce'         => wp_create_nonce( 'save-attributes' ),
			'calc_totals_nonce'             => wp_create_nonce( 'calc-totals' ),
			'get_customer_details_nonce'    => wp_create_nonce( 'get-customer-details' ),
			'search_products_nonce'         => wp_create_nonce( 'search-products' ),
			'grant_access_nonce'            => wp_create_nonce( 'grant-access' ),
			'revoke_access_nonce'           => wp_create_nonce( 'revoke-access' ),
			'add_order_note_nonce'          => wp_create_nonce( 'add-order-note' ),
			'delete_order_note_nonce'       => wp_create_nonce( 'delete-order-note' ),
			'calendar_image'                => WC()->plugin_url() . '/assets/images/calendar.png',
			'post_id'                       => ( $postid !== 0) ? $postid : '',
			'base_country'                  => WC()->countries->get_base_country(),
			'currency_format_num_decimals'  => wc_get_price_decimals(),
			'currency_format_symbol'        => get_woocommerce_currency_symbol( $currency ),
			'currency_format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
			'currency_format_thousand_sep'  => esc_attr( wc_get_price_thousand_separator() ),
			'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS.
			'rounding_precision'            => wc_get_rounding_precision(),
			'tax_rounding_mode'             => wc_get_tax_rounding_mode(),
			'product_types'                 => array_unique( array_merge( array( 'simple', 'grouped', 'variable', 'external' ), array_keys( wc_get_product_types() ) ) ),
			'i18n_download_permission_fail'      => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'woocommerce' ),
			'i18n_permission_revoke'             => __( 'Are you sure you want to revoke access to this download?', 'woocommerce' ),
			'i18n_tax_rate_already_exists'       => __( 'You cannot add the same tax rate twice!', 'woocommerce' ),
			'i18n_delete_note'                   => __( 'Are you sure you wish to delete this note? This action cannot be undone.', 'woocommerce' ),
			'i18n_apply_coupon'                  => __( 'Enter a coupon code to apply. Discounts are applied to line totals, before taxes.', 'woocommerce' ),
			'i18n_add_fee'                       => __( 'Enter a fixed amount or percentage to apply as a fee.', 'woocommerce' ),
			'i18n_product_simple_tip'            => __( '<b>Simple –</b> covers the vast majority of any products you may sell. Simple products are shipped and have no options. For example, a book.', 'woocommerce' ),
			'i18n_product_grouped_tip'           => __( '<b>Grouped –</b> a collection of related products that can be purchased individually and only consist of simple products. For example, a set of six drinking glasses.', 'woocommerce' ),
			'i18n_product_external_tip'          => __( '<b>External or Affiliate –</b> one that you list and describe on your website but is sold elsewhere.', 'woocommerce' ),
			'i18n_product_variable_tip'          => __( '<b>Variable –</b> a product with variations, each of which may have a different SKU, price, stock option, etc. For example, a t-shirt available in different colors and/or sizes.', 'woocommerce' ),
			'i18n_product_other_tip'             => __( 'Product types define available product details and attributes, such as downloadable files and variations. They’re also used for analytics and inventory management.', 'woocommerce' ),
			'i18n_product_description_tip'       => __( 'Describe this product. What makes it unique? What are its most important features?', 'woocommerce' ),
			'i18n_product_short_description_tip' => __( 'Summarize this product in 1-2 short sentences. We’ll show it at the top of the page.', 'woocommerce' ),
			/* translators: %1$s: maximum file size */
			'i18n_product_image_tip'             => sprintf( __( 'For best results, upload JPEG or PNG files that are 1000 by 1000 pixels or larger. Maximum upload file size: %1$s.', 'woocommerce' ) , size_format( wp_max_upload_size() ) ),
		);

		wp_localize_script( 'wc-admin-meta-boxes', 'woocommerce_admin_meta_boxes', $params );
		
		
    }

    function enqueue_dashboard_resources(){

    	if (apply_filters('marketking_enable_country_scripts_frontend', true)){
    		wp_enqueue_style('select2', plugins_url('dashboard/assets/b2bkingintegration/select2/select2.min.css', __FILE__) );
    		wp_enqueue_script('select2', plugins_url('dashboard/assets/b2bkingintegration/select2/select2.min.js', __FILE__), array('jquery') );

    		wp_enqueue_style('selectWoo', plugins_url('dashboard/assets/b2bkingintegration/select2/selectwoo.min.css', __FILE__) );
    		wp_enqueue_script('selectWoo', plugins_url('dashboard/assets/b2bkingintegration/select2/selectwoo.min.js', __FILE__), array('jquery') );

    		wp_enqueue_script('wc-country-select', plugins_url('dashboard/assets/b2bkingintegration/select2/country-select.min.js', __FILE__), array('jquery') );

    		$params = array(
    			'countries'                 => wp_json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
    			'i18n_select_state_text'    => esc_attr__( 'Select an option&hellip;', 'woocommerce' ),
    			'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
    			'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
    			'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
    			'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
    			'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
    			'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
    			'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
    			'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
    			'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
    			'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
    		);

    		wp_localize_script( 'marketking_dashboard_scripts', 'wc_country_select_params', $params );		

    	}
    	
    	// Simplebar
    	wp_enqueue_style('simplebar', plugins_url('../includes/assets/lib/simplebar/simplebar.css', __FILE__), $deps = array(), $ver = false, $in_footer =true);
    	wp_enqueue_script('simplebar', plugins_url('../includes/assets/lib/simplebar/simplebar.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);

    	if (apply_filters('marketking_dashboard_rtl', false)){ // filter to switch dashboard to RTL
    		add_filter('marketking_css_dashboard_file', function($val){
    		    return 'dashlite-rtl.css';
    		}, 10, 1);
    	}

    	// Dashboard
    	wp_enqueue_style('marketking_dashboard', plugins_url('dashboard/assets/css/'.apply_filters('marketking_css_dashboard_file','dashlite.css'), __FILE__), $deps = array(), filemtime( plugin_dir_path( __FILE__ ) . 'dashboard/assets/css/'.apply_filters('marketking_css_dashboard_file','dashlite.css') ));
    	wp_enqueue_script('marketking_dashboard_bundle', plugins_url('dashboard/assets/js/bundle.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
    	wp_enqueue_script('marketking_dashboard_scripts', plugins_url('dashboard/assets/js/scripts.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
    	wp_enqueue_script('marketking_dashboard_chart', plugins_url('dashboard/assets/js/charts/chart-ecommerce.js', __FILE__), $deps = array(), filemtime( plugin_dir_path( __FILE__ ) . 'dashboard/assets/js/charts/chart-ecommerce.js' ), $in_footer =true);
    	wp_enqueue_script('marketking_dashboard_chart_sales', plugins_url('dashboard/assets/js/charts/chart-sales.js', __FILE__), $deps = array(), filemtime( plugin_dir_path( __FILE__ ) . 'dashboard/assets/js/charts/chart-sales.js' ), $in_footer =true);
    	wp_enqueue_script('marketking_dashboard_messages', plugins_url('dashboard/assets/js/apps/messages.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
    	wp_enqueue_script('dataTablesButtons', plugins_url('../includes/assets/lib/dataTables/dataTables.buttons.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
    	wp_enqueue_script('dataTablesButtonsHTML', plugins_url('../includes/assets/lib/dataTables/buttons.html5.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
    	wp_enqueue_script('dataTablesButtonsPrint', plugins_url('../includes/assets/lib/dataTables/buttons.print.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
    	wp_enqueue_script('dataTablesButtonsColvis', plugins_url('../includes/assets/lib/dataTables/buttons.colVis.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);

    	wp_enqueue_media();


    	wp_enqueue_script('jszip', plugins_url('../includes/assets/lib/dataTables/jszip.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
    	wp_enqueue_script('pdfmake', plugins_url('../includes/assets/lib/dataTables/pdfmake.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
    	wp_enqueue_script('vfsfonts', plugins_url('../includes/assets/lib/dataTables/vfs_fonts.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);

    	// WOOCS set currency in vendor dashboard
    	if (class_exists('WOOCS')){
    	  global $WOOCS;
    	  $default = $WOOCS->default_currency;
    	  $WOOCS->set_currency($default);
    	}

    	// B2BKing Integration START
    	$currentp = get_query_var('dashpage');

    	if ((defined('B2BKING_DIR') && defined('MARKETKINGPRO_DIR') && intval(get_option('marketking_enable_b2bkingintegration_setting', 1)) === 1) || $currentp === 'refunds'){

    		wp_enqueue_style('marketkingpro_b2bkingintegrationcss', plugins_url('dashboard/assets/b2bkingintegration/b2bkingintegration.css', __FILE__), $deps = array(), filemtime( plugin_dir_path( __FILE__ ) . 'dashboard/assets/b2bkingintegration/b2bkingintegration.css' ));
    		wp_enqueue_script('marketkingpro_b2bkingintegrationjs', plugins_url('dashboard/assets/b2bkingintegration/b2bkingintegration.js', __FILE__), $deps = array(), filemtime( plugin_dir_path( __FILE__ ) . 'dashboard/assets/b2bkingintegration/b2bkingintegration.js' ));

    			// scripts and styles already registered by default
				wp_enqueue_script('dataTables', plugins_url('dashboard/assets/b2bkingintegration/dataTables/jquery.dataTables.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
				wp_enqueue_style( 'dataTables', plugins_url('dashboard/assets/b2bkingintegration/dataTables/jquery.dataTables.min.css', __FILE__));

				wp_enqueue_script('jquerymodalzz', plugins_url('dashboard/assets/b2bkingintegration/jquerymodal/jquery.modal.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
				wp_enqueue_style('jquerymodalzz', plugins_url('dashboard/assets/b2bkingintegration/jquerymodal/jquery.modal.min.css', __FILE__));
				wp_enqueue_style('select2', plugins_url('dashboard/assets/b2bkingintegration/select2/select2.min.css', __FILE__) );
				wp_enqueue_script('select2', plugins_url('dashboard/assets/b2bkingintegration/select2/select2.min.js', __FILE__), array('jquery') );

				// Send display settings to JS
		    	$data_to_be_passed = array(
		    		'marketking-multivendor-marketplace-for-woocommerce' => 'yes',
		    		'security'  => wp_create_nonce( 'b2bking_security_nonce' ),
		    		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		    		'min_quantity_text' => esc_html__('Min. Quantity','marketking-multivendor-marketplace-for-woocommerce'),
		    		'final_price_text' => esc_html__('Final Price', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'label_text' => esc_html__('Label', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'text_text' => esc_html__('Text', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'text_delete' => esc_html__('X', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'currency_symbol' => get_woocommerce_currency_symbol(),
		    		'are_you_sure_save_offer' => esc_html__('Are you sure you want to publish this offer?', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'are_you_sure_delete_offer' => esc_html__('Are you sure you want to delete this offer?', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'are_you_sure_save_rule' => esc_html__('Are you sure you want to publish this rule?', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'are_you_sure_delete_orule' => esc_html__('Are you sure you want to delete this rule?', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'rule_must_have_title' => esc_html__('You must enter a title for the rule!', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'offer_must_have_title' => esc_html__('You must enter a title for the offer!', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'offer_must_have_product' => esc_html__('You must have at least 1 product with quantity and price!', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'cart_total_quantity' => esc_html__('Cart Total Quantity', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'cart_total_value' => esc_html__('Cart Total Value', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'category_product_quantity' => esc_html__('Category Product Quantity', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'category_product_value' => esc_html__('Category Product Value', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'product_quantity' => esc_html__('Product Quantity', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'product_value' => esc_html__('Product Value', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'greater' => esc_html__('greater (>)', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'equal' => esc_html__('equal (=)', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'smaller' => esc_html__('smaller (<)', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'delete' => esc_html__('Delete', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'enter_quantity_value' => esc_html__('Enter the quantity/value', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'add_condition' => esc_html__('Add Condition' ,'marketking-multivendor-marketplace-for-woocommerce'),
		    		'conditions_apply_cumulatively' => esc_html__('Conditions must apply cumulatively.' ,'marketking-multivendor-marketplace-for-woocommerce'),
		    		'conditions_multiselect' => esc_html__('Each product must meet all product conditions.' ,'marketking-multivendor-marketplace-for-woocommerce'),
		    		'purchase_lists_language_option' => get_option('b2bking_purchase_lists_language_setting','english'),
		    		'replace_product_selector' => intval(get_option( 'b2bking_replace_product_selector_setting', 0 )),
		    		'vendorinurl' => get_query_var('vendor'),
		    		'request_many_vendors' => esc_html__('Your cart contains items from multiple vendors. Quote requests can only be sent to 1 vendor at a time. Please adjust cart items.'),
		    		'offers_link'	=> esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'offers',
		    		'email_offer'	=> esc_html__('Email Offer', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'email_offer_confirm' => esc_html__('This offer will be emailed to ALL users that have visibility. That includes all groups you selected, all users, and all email addresses entered. Make sure to save the offer first if you made changes to it! Are you sure you want to proceed?', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'email_has_been_sent' => esc_html__('The offer has been emailed successfully.', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'offers_endpoint_link' => apply_filters('b2bking_offers_link', get_permalink( get_option('woocommerce_myaccount_page_id') ).get_option('b2bking_offers_endpoint_setting','offers')),
		    		'also_email_offer'	=> esc_html__('The offer has been saved. Do you want to also email it to the user?', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'are_you_sure_delete_rule' => esc_html__('Are you sure you want to delete this rule?', 'marketking-multivendor-marketplace-for-woocommerce'),
		    		'b2bking_exists' => defined('B2BKING_DIR'),
		    	);

				wp_localize_script( 'marketkingpro_b2bkingintegrationjs', 'b2bkingmarketking_display_settings', $data_to_be_passed );


    	}
    	// B2BKing Integration END


    	// orders team member
    	// either not team member, or team member with permission to add
    	$checkedval = 0;
    	if (marketking()->is_vendor_team_member()){
    	    $checkedval = intval(get_user_meta(get_current_user_id(),'marketking_teammember_available_panel_editorders', true));
    	}
        if (!marketking()->is_vendor_team_member() || $checkedval === 1){
        	$removeorders = 0;
        } else {
        	$removeorders = 1;
        }

        $current_id = get_current_user_id();
        if (marketking()->is_vendor_team_member()){
        	$current_id = marketking()->get_team_member_parent();
        }

        $can_downloadable = 0;
        if (marketking()->vendor_can_product_type($current_id,'downloadable')){
        	$can_downloadable = 1;
        }

        $can_virtual = 0;
        if (marketking()->vendor_can_product_type($current_id,'virtual')){
        	$can_virtual = 1;
        }

        $remove_types = array();
        $product_types = wc_get_product_types();
        foreach ( $product_types as $value => $label ) { 
        	if(!marketking()->vendor_can_product_type($current_id, $value)){
        		array_push($remove_types, $value);
        	}
        }

    	// Dashboard end
		wp_enqueue_script('marketking_public_script', plugins_url('assets/js/public.js', __FILE__), $deps = array(), filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/public.js' ), $in_footer =true);

		$pagenr = sanitize_text_field(marketking()->get_pagenr_query_var());

		
		
		// Send display settings to JS
    	$data_to_be_passed = array(
    		'security'  => wp_create_nonce( 'marketking_security_nonce' ),
    		'ajaxurl' => admin_url( 'admin-ajax.php' ),
    		'carturl' => wc_get_cart_url(),
    		'shopurl' => apply_filters('marketking_shop_as_customer_link',get_permalink( wc_get_page_id( 'shop' ) )),
    		'accounturl' => get_permalink( wc_get_page_id( 'myaccount' ) ),
    		'adminurl' => admin_url(''),
    		'datatables_folder' => plugins_url('../includes/assets/lib/dataTables/i18n/', __FILE__),
    		'tables_language_option' => apply_filters('marketking_tables_language_option_setting','English'),
    		'currency_symbol' => get_woocommerce_currency_symbol(),
    		'dashboardurl' =>  esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))),
    		'customersurl' =>  esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'customers/'),
    		'messagesurl' =>  esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'messages/'),
    		'sure_delete_coupon' => esc_html__('Are you sure you want to delete this coupon?','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_create_cart' => esc_html__('Are you sure you want to save this cart?','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_send_refund' => esc_html__('Are you sure you want to send this refund request?','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_delete_cart' => esc_html__('Are you sure you want to delete this cart?','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_add_customer' => esc_html__('Are you sure you want to add this customer?','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_add_member' => esc_html__('Are you sure you want to add this team member?','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_save_info' => esc_html__('Are you sure you want to save the payout info?','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_disconnect_stripe' => esc_html__('Are you sure you want to disconnect this Stripe account?','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_withdraw' => esc_html__('Are you sure you want to make the withdrawal request?','marketking-multivendor-marketplace-for-woocommerce'),
    		'not_enough_funds' => esc_html__('You do not have sufficient funds for this withdrawal.','marketking-multivendor-marketplace-for-woocommerce'),
    		'ready' => esc_html__('Ready','marketking-multivendor-marketplace-for-woocommerce'),
    		'link_copied' => esc_html__('Link copied', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'copied' => esc_html__('Copied', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'searchtext'  => esc_html__('Search ', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'copy' => esc_html__('Copy', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'customer_created' => esc_html__('The customer account has been created. An email has been sent to the customer with account details.', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'customer_created_error' => esc_html__('The customer account could not be created. It may be because the username or email already exists. Here are the error details:', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'member_created' => esc_html__('The team member account has been created. An email has been sent to them with account details.', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'fill_all_required' => esc_html__('Please fill all required (*) fields / resolve all validation errors.','marketking-multivendor-marketplace-for-woocommerce'),
    		'member_created_error' => esc_html__('The team member account could not be created. It may be because the username or email already exists. Here are the error details: ', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'print' => esc_html__('Print', 'marketking-multivendor-marketplace-for-woocommerce'), 
    		'edit_columns' => esc_html__('Edit Columns', 'marketking-multivendor-marketplace-for-woocommerce'), 
    		'edit_columns_class' => apply_filters('marketking_edit_columns_button', 'btn btn-sm btn-gray'),
    		'hidden_columns_products' => apply_filters('marketking_hidden_columns_products_dashboard', array(4)),
    		'completed' => esc_html__('Completed', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'pending' => esc_html__('Pending', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'cancelled' => esc_html__('Cancelled', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'orders' => esc_html__('orders', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'queryid' => sanitize_text_field(get_query_var('id')),
    		'profile_pic' => plugins_url('../includes/assets/images/store-profile.png', __FILE__),
    		'sure_delete_product' => esc_html__('Are you sure you want to delete this product?', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_create_shipment' => esc_html__('Are you sure you want to create this shipment?', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_delete_team' => esc_html__('Are you sure you want to delete this team member account? This is irreversible.', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_delete_shipping_method' => esc_html__('Are you sure you want to delete this shipping method?', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_add_shipping_method' => esc_html__('Are you sure you want to add this shipping method?', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'products_dashboard_page' => esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'products',
    		'coupons_dashboard_page' => esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'coupons',
    		'product_must_name' => esc_html__('The product must have a name (title)!','marketking-multivendor-marketplace-for-woocommerce'),
    		'announcementsurl' =>  esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'announcements/'),
    		'product_edit_link' => esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'edit-product/'),
    		'products_link' => esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'products/'),
    		'order_edit_link' => esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'manage-order/'),
    		'coupon_edit_link' => esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'edit-coupon/'),
    		'team_members_link' => esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'team/'),
    		'edit_team_link' => esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'edit-team/'),
    		'payouts_link' => esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'payouts',
    		'import_products_link' => esc_attr(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )).'import-products/'),
    		'min_quantity_text' => esc_html__('Min. Quantity','marketking-multivendor-marketplace-for-woocommerce'),
    		'final_price_text' => esc_html__('Final Price', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'label_text' => esc_html__('Label', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'mkpror'	=> defined('MARKETKINGPRO_DIR'),
    		'auctions' =>  intval(get_option('marketking_enable_auctions_setting', 1)),
    		'chooseattr' => esc_html__('Choose an attribute:','marketking-multivendor-marketplace-for-woocommerce'),
    		'can_linked_products' => marketking()->vendor_can_linked_products($current_id),
    		'can_purchase_notes' => intval(get_option( 'marketking_vendors_can_purchase_notes_setting', 1 )),
    		'can_taxable_products' => marketking()->vendor_can_taxable($current_id),
    		'can_new_attributes' => marketking()->vendor_can_new_attributes($current_id),
    		'all_virtual' => marketking()->vendor_all_products_virtual($current_id),
    		'all_downloadable' => marketking()->vendor_all_products_downloadable($current_id),   		
    		'can_multiple_categories' => marketking()->vendor_can_multiple_categories($current_id),
    		'can_backorders' => marketking()->vendor_can_backorders($current_id),
    		'can_reviews' => intval(get_option( 'marketking_vendors_can_reviews_setting', 0 )),
    		'load_tables_with_ajax' => intval(marketking()->load_tables_with_ajax(get_current_user_id())),    		
    		'text_text' => esc_html__('Text', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'attributes_message' => esc_html__('You must save the product first before you can configure attributes.', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'variations_message' => esc_html__('You must save the product first before you can configure variations.', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'pagenr' => $pagenr,
    		'changecolor' => get_option( 'marketking_change_color_scheme_setting', 0 ),
    		'color' => get_option( 'marketking_main_dashboard_color_setting', '#854fff' ),
    		'hovercolor' => get_option( 'marketking_main_dashboard_hover_color_setting', '#6a29ff' ),
    		'request_many_vendors' => esc_html__('Your cart contains items from multiple vendors. Quote requests can only be sent to 1 vendor at a time. Please adjust cart items.'),
    		'sure_reply_review' => esc_html__('Are you sure you want to submit this reply? Please note that you can only reply once.','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_approve_refund' => esc_html__('Are you sure you want to approve this refund? This is irreversible.','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_reject_refund' => esc_html__('Are you sure you want to deny this refund? This is irreversible.','marketking-multivendor-marketplace-for-woocommerce'),
    		'review_reply_submitted' => esc_html__('Your reply has been submitted.','marketking-multivendor-marketplace-for-woocommerce'),
    		'review_report_submitted' => esc_html__('Your report has been submitted.','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_send_verification' => esc_html__('Are you sure you want to submit this for verification?','marketking-multivendor-marketplace-for-woocommerce'),
    		'user_id' => get_current_user_id(),
    		'removeorders' => $removeorders,
    		'can_downloadable' => $can_downloadable,
    		'can_virtual' => $can_virtual,
    		'remove_product_types' => json_encode($remove_types),
    		'membership_go_to_product' => apply_filters('marketking_membership_go_to_product', 0),
    		'remove_tab_b2bking' => apply_filters('marketking_remove_tab_b2bking', 0),
    		'remove_tab_product_layout_flatsome' => apply_filters('remove_tab_product_layout_flatsome', 0),
    		'remove_tab_extra_flatsome' => apply_filters('remove_tab_extra_flatsome', 0),
    		'remove_tab_extra_bubble_flatsome' => apply_filters('remove_tab_extra_bubble_flatsome', 0),
    		'producttype' => isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '',
    		'typetext' => esc_html__('type','marketking-multivendor-marketplace-for-woocommerce')

		);


		// statistics about orders for dashboard page
		if (empty(get_query_var('dashpage'))){ 

			$completed = 0;
			$pending = 0;
			$cancelled = 0; 

			$vendor_orders = get_posts( array( 
				'post_type' => 'shop_order',
				'post_status'=>'any',
				'date_query' => array(
				        'after' => date('Y-m-d', strtotime('-30 days')) 
				    ),
				'numberposts' => -1,
				'author'   => get_current_user_id(),
				'fields' =>'ids'
			));

			foreach ($vendor_orders as $order_id){
			    $orderobj = wc_get_order($order_id);
			    if ($orderobj !== false){
				    $status = $orderobj->get_status();
				    // check if approved
				    if (in_array($status,apply_filters('marketking_earning_completed_statuses', array('completed')))){
				        $completed++;
				    } else if ($status === 'processing'){
				    	$pending++;
				    } else if ($status === 'on-hold'){
				    	$pending++;
				    } else if ($status === 'pending'){
				    	$pending++;
				    } else if ($status === 'failed'){
				    	$cancelled++;
				    } else if ($status === 'cancelled'){
				    	$cancelled++;
				    } else if ($status === 'refunded'){
				    	$cancelled++;
				    }
				}
			}

			$data_to_be_passed['completedorders'] = $completed;
			$data_to_be_passed['pendingorders'] = $pending;
			$data_to_be_passed['cancelledorders'] = $cancelled;


			wp_localize_script( 'marketking_public_script', 'marketking_display_settings', $data_to_be_passed );
			wp_localize_script( 'marketking_dashboard_scripts', 'marketking_display_settings', $data_to_be_passed );		

		}


		// include earnings for js, if this is earnings page or empty = dashboard
		if (get_query_var('dashpage') === 'earnings' || empty(get_query_var('dashpage'))){ 

			// get month requested
			$months_removed = sanitize_text_field(get_query_var('id'));
			if (empty($months_removed)){
				$months_removed = 0;
			}
			$month_number = date('n', strtotime('-'.$months_removed.' months'));
			$month_year = date('Y', strtotime('-'.$months_removed.' months'));
			$days_number = date('t', mktime(0, 0, 0, $month_number, 1, $month_year)); 

			$days_array = array();

			// get labels (days in month)
			while ($days_number > 0){
				array_push($days_array, $days_number);
				$days_number--;
			}

			//let's query the database only once for the month earnings
			$earnings_array = array("1"=>0, "2"=>0, "3"=>0, "4"=>0, "5"=>0, "6"=>0, "7"=>0, "8"=>0, "9"=>0, "10"=>0, "11"=>0, "12"=>0, "13"=>0, "14"=>0, "15"=>0, "16"=>0, "17"=>0, "18"=>0, "19"=>0, "20"=>0, "21"=>0, "22"=>0, "23"=>0, "24"=>0, "25"=>0, "26"=>0, "27"=>0, "28"=>0, "29"=>0, "30"=>0, "31"=>0);

			
			$earnings = get_posts( array( 
			    'post_type' => 'marketking_earning',
			    'numberposts' => -1,
			    'post_status'    => 'any',
		    	'date_query' => array(
		            'year'  => $month_year,
		            'month' => $month_number,
		        ),
			    'meta_key'   => 'vendor_id',
			    'meta_value' => get_current_user_id(),
			));
			foreach ($earnings as $earning){
				$earnings_number = 0;
				$date = date("d", strtotime($earning->post_date));
			    $order_id = get_post_meta($earning->ID,'order_id', true);
			    $orderobj = wc_get_order($order_id);
			    if ($orderobj !== false){
			    	$status = $orderobj->get_status();
			    	$earnings_total = get_post_meta($earning->ID,'marketking_commission_total', true);
			    	// check if approved OR paid via stripe
			    	if (in_array($status,apply_filters('marketking_earning_completed_statuses', array('completed')))){
			    	    $earnings_number+=$earnings_total;
			    	}

			    	$earnings_array[intval($date)] += $earnings_number;
			    }
			    
			}

			$data_to_be_passed['earningslabels'] = array_reverse($days_array);

			// round to 2
			$earnings_array = array_map(function($v){return round($v,2);}, $earnings_array);

			$data_to_be_passed['earningsvalues'] = array_values($earnings_array);
		}


		wp_localize_script( 'marketking_public_script', 'marketking_display_settings', $data_to_be_passed );
		wp_localize_script( 'marketking_dashboard_scripts', 'marketking_display_settings', $data_to_be_passed );

    }

	// Add user classes to body
	function marketking_body_classes($classes) {
		// if user is vendor
		$user_id = get_current_user_id();

		$vendor_group = get_user_meta($user_id,'marketking_group',true);
		if ($vendor_group !== 'none' && !empty($vendor_group)){
			$classes[] = 'marketking_vendor';
			$classes[] = 'marketking_vendor_group_'.$vendor_group;
		} else {
			$classes[] = 'marketking_not_vendor';
		} 

		// vendor stores page
		global $post;
		if (isset($post->ID)){
	        if ( intval($post->ID) === intval(apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' ), 'post' , true) ) ){

	        	// check if is general page, or if vendor id is set
	        	if (empty(get_query_var('vendorid'))){ 
		            // stores page here
		            $classes[] = 'marketking_stores_page_all';

		        } else {
		        	$classes[] = 'marketking_store_page_individual';
		        }
		    }
		}

	    return $classes;
	}

	function marketking_become_vendor_link_myaccount(){
		$page = get_option('marketking_vendor_registration_page_setting');
		// if current page is become a vendor page, do not show it.
		global $post;
		if ($post->ID !== intval($page)){
			?>
			<p id="marketking_become_vendor_link_myaccount">
				<a href="<?php echo esc_attr(get_permalink($page)); ?>"><?php esc_html_e('Become a Vendor','marketking-multivendor-marketplace-for-woocommerce');?></a>
			</p>
			<?php
		}
	}

	// Custom Registration Fields
	public static function marketking_custom_registration_fields(){

		if (!is_checkout()){ // check against some errors in checkout
			global $woocommerce;    
			global $marketking_is_b2b_registration;
			global $marketking_is_b2b_registration_shortcode_option_id;

			if ($marketking_is_b2b_registration_shortcode_option_id === NULL || $marketking_is_b2b_registration_shortcode_option_id === ''){
				$marketking_is_b2b_registration_shortcode_option_id = 'none';
			}

			// if Registration Roles dropdown is enabled (enabled by default), show custom registration options and fields
			$registration_option_setting = intval(get_option( 'marketking_registration_options_dropdown_setting', 1 ));
			if ($registration_option_setting === 1 || $marketking_is_b2b_registration === 'yes'){

				// get options
				$custom_options = get_posts([
				    		'post_type' => 'marketking_option',
				    	  	'post_status' => 'publish',
				    	  	'numberposts' => -1,
				    	  	'meta_key' => 'marketking_option_sort_number',
			    	  	    'orderby' => 'meta_value_num',
			    	  	    'order' => 'ASC',
				    	  	'meta_query'=> array(
				    	  		'relation' => 'AND',
				                array(
			                        'key' => 'marketking_option_status',
			                        'value' => 1
				                ),
			            	)
				    	]);

				// if we're in the marketking core plugin, remove the customer option in a separate page
				if (!defined('MARKETKINGPRO_DIR') && get_option( 'marketking_vendor_registration_setting', 'myaccount' ) === 'separate'){
					unset($custom_options[0]);
				}

				?>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide marketking_registration_options_dropdown_section <?php if ($marketking_is_b2b_registration_shortcode_option_id !== 'none' || (count($custom_options) === 1) && get_option( 'marketking_vendor_registration_setting', 'myaccount' ) === 'separate'){ echo 'marketking_registration_options_dropdown_section_hidden'; } ?>">
					<label for="marketking_registration_options_dropdown">
						<?php esc_html_e('User Type','marketking-multivendor-marketplace-for-woocommerce'); ?>&nbsp;<span class="required">*</span>
					</label>
					<select id="marketking_registration_options_dropdown" name="marketking_registration_options_dropdown">

						<?php
						// show vendor options
						foreach ($custom_options as $option){
							echo '<option value="option_'.esc_attr($option->ID).'" '.selected($option->ID,$marketking_is_b2b_registration_shortcode_option_id,false).'>'.esc_html(get_the_title(apply_filters( 'wpml_object_id', $option->ID, 'post', true ))).'</option>';
						}
						?>
					</select>
				</p>
				<?php
			}

			$custom_fields = array();
			// if dropdown enabled, retrieve all enabled fields. Else, show only "All Roles" fields
			if ($registration_option_setting === 1 || $marketking_is_b2b_registration === 'yes'){
				$custom_fields = get_posts([
				    		'post_type' => 'marketking_field',
				    	  	'post_status' => 'publish',
				    	  	'numberposts' => -1,
				    	  	'meta_key' => 'marketking_field_sort_number',
			    	  	    'orderby' => 'meta_value_num',
			    	  	    'order' => 'ASC',
				    	  	'meta_query'=> array(
				    	  		'relation' => 'AND',
				                array(
			                        'key' => 'marketking_field_status',
			                        'value' => 1
				                ),
			            	)
				    	]);
			}

			// show all retrieved fields
			 	foreach ($custom_fields as $custom_field){
				$billing_exclusive = intval(get_post_meta($custom_field->ID, 'marketking_field_billing_exclusive', true));
				if ($billing_exclusive !== 1){
					$field_type = get_post_meta($custom_field->ID, 'marketking_field_field_type', true);
					$field_label = get_post_meta(apply_filters( 'wpml_object_id', $custom_field->ID, 'post', true ), 'marketking_field_field_label', true);
					$field_placeholder = get_post_meta(apply_filters( 'wpml_object_id', $custom_field->ID, 'post', true ), 'marketking_field_field_placeholder', true);
					$required = get_post_meta($custom_field->ID, 'marketking_field_required', true);
					$billing_connection = get_post_meta($custom_field->ID, 'marketking_field_billing_connection', true);
					// option identifier
					$option = get_post_meta($custom_field->ID, 'marketking_field_registration_option', true);
					if ($option !== 'multipleoptions'){
						$option_class = 'marketking_custom_registration_'.esc_attr($option);
					} else {
						$field_options = get_post_meta($custom_field->ID, 'marketking_field_multiple_options', true);
						$options_array = explode(',',$field_options);
						$option_class = '';
						foreach($options_array as $option){
							$option_class.='marketking_custom_registration_'.esc_attr($option).' ';
						}
					}
					// if error, get previous value and show it in the fields, for user friendliness
					$previous_value = '';
					if (isset($_POST['marketking_field_'.esc_attr($custom_field->ID)])){
						$previous_value = sanitize_text_field($_POST['marketking_field_'.esc_attr($custom_field->ID)]);
					}

					if (intval($required) === 1){
						$required = 'required';
					} else {
						$required = '';
					}

					$vat_container = '';
					if ($billing_connection === 'billing_vat'){
						$vat_container = 'marketking_vat_number_registration_field_container';
					}

					$class = '';
					// purely aesthethical fix, add a class to the P in countries, in order to remove the margin bottom
					if ($billing_connection === 'billing_countrystate' || $billing_connection === 'billing_country' || $billing_connection === 'billing_state'){
						$class = 'marketking_country_or_state';
					}
					
					echo '<div class="'.esc_attr($vat_container).' marketking_custom_registration_container '.esc_attr($option_class).'">';
					echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide '.$class.'">';

					$labelfor = 'marketking_field_'.esc_attr($custom_field->ID);
					if ($billing_connection === 'billing_vat'){
						$labelfor = 'marketking_vat_number_registration_field';
					}
					if ($billing_connection === 'billing_country') {
						$labelfor = 'marketking_field_'.esc_attr($custom_field->ID);
						
					}
					if ($billing_connection === 'billing_countrystate') {
						$labelfor = 'marketking_field_'.esc_attr($custom_field->ID);
					}
					echo '<label for="'.esc_attr($labelfor).'">'.esc_html($field_label).'&nbsp;';
						if ($required === 'required'){ 
							echo '<span class="required">*</span>'; 
						}
						if ($billing_connection === 'billing_store_url'){
							echo '<span class="marketking_availability"></span>';
						}
						echo '</label>';

					// if billing connection is country, replace field with countries dropdown
					if ($billing_connection !== 'billing_countrystate' && $billing_connection !== 'billing_country' && $billing_connection !== 'billing_vat'){

						if ($field_type === 'text'){
							if ($billing_connection === 'billing_store_name'){
								$maxl = 'maxlength="'.esc_attr(apply_filters('marketking_store_name_max_length', 25)).'"';
							} else {
								$maxl = '';
							}
							echo '<input type="text" id="marketking_field_'.esc_attr($custom_field->ID).'" class="marketking_custom_registration_field '.esc_attr($billing_connection).' marketking_field_req_'.esc_attr($required).'" name="marketking_field_'.esc_attr($custom_field->ID).'" '.$maxl.' value="'.esc_attr($previous_value).'" placeholder="'.esc_attr($field_placeholder).'" '.esc_attr($required).'>';
						} else if ($field_type === 'textarea'){
							echo '<textarea id="marketking_field_'.esc_attr($custom_field->ID).'" class="marketking_custom_registration_field marketking_custom_registration_field_textarea marketking_field_req_'.esc_attr($required).'" name="marketking_field_'.esc_attr($custom_field->ID).'" placeholder="'.esc_attr($field_placeholder).'" '.esc_attr($required).'>'.esc_html($previous_value).'</textarea>';
						} else if ($field_type === 'number'){
							echo '<input id="marketking_field_'.esc_attr($custom_field->ID).'" type="number" step="0.00001" class="marketking_custom_registration_field marketking_field_req_'.esc_attr($required).'" name="marketking_field_'.esc_attr($custom_field->ID).'" value="'.esc_attr($previous_value).'" placeholder="'.esc_attr($field_placeholder).'" '.esc_attr($required).'>';
						} else if ($field_type === 'email'){
							echo '<input id="marketking_field_'.esc_attr($custom_field->ID).'" type="email" class="marketking_custom_registration_field marketking_field_req_'.esc_attr($required).'" name="marketking_field_'.esc_attr($custom_field->ID).'" value="'.esc_attr($previous_value).'" placeholder="'.esc_attr($field_placeholder).'" '.esc_attr($required).'>';
						} else if ($field_type === 'date'){
							echo '<input id="marketking_field_'.esc_attr($custom_field->ID).'" type="date" class="marketking_custom_registration_field marketking_field_req_'.esc_attr($required).'" name="marketking_field_'.esc_attr($custom_field->ID).'" value="'.esc_attr($previous_value).'" placeholder="'.esc_attr($field_placeholder).'" '.esc_attr($required).'>';
						} else if ($field_type === 'tel'){
							echo '<input id="marketking_field_'.esc_attr($custom_field->ID).'" type="tel" class="marketking_custom_registration_field marketking_field_req_'.esc_attr($required).'" name="marketking_field_'.esc_attr($custom_field->ID).'" value="'.esc_attr($previous_value).'" placeholder="'.esc_attr($field_placeholder).'" '.esc_attr($required).'>';
						} else if ($field_type === 'file'){
							echo '<input id="marketking_field_'.esc_attr($custom_field->ID).'" type="file" class="marketking_custom_registration_field marketking_field_req_'.esc_attr($required).'" name="marketking_field_'.esc_attr($custom_field->ID).'" placeholder="'.esc_attr($field_placeholder).'" '.esc_attr($required).'>'.'<br /><span class="marketking_supported_types">'.esc_html__('Supported file types: jpg, jpeg, png, txt, pdf, doc, docx','marketking-multivendor-marketplace-for-woocommerce').'</span>';

						} else if ($field_type === 'select'){
							$select_options = get_post_meta(apply_filters( 'wpml_object_id', $custom_field->ID, 'post', true ), 'marketking_field_user_choices', true);
							$select_options = explode(',', $select_options);

							echo '<select id="marketking_field_'.esc_attr($custom_field->ID).'" class="marketking_custom_registration_field marketking_field_req_'.esc_attr($required).'" name="marketking_field_'.esc_attr($custom_field->ID).'" placeholder="'.esc_attr($field_placeholder).'" '.esc_attr($required).'>';
								foreach ($select_options as $option){
									// check if option is simple or value is specified via option:value
									$optionvalue = explode(':', $option);
									if (count($optionvalue) === 2 ){
										// value is specified
										echo '<option value="'.esc_attr(trim($optionvalue[0])).'" '.selected(trim($optionvalue[0]), $previous_value, false).'>'.esc_html(trim($optionvalue[1])).'</option>';
									} else {
										// simple
										echo '<option value="'.esc_attr(trim($option)).'" '.selected($option, $previous_value, false).'>'.esc_html(trim($option)).'</option>';
									}
								}
							echo '</select>';
						} else if ($field_type === 'checkbox'){

							$select_options = get_post_meta($custom_field->ID, 'marketking_field_user_choices', true);
							$select_options = explode(',', $select_options);
							$i = 1;

							// if required and only 1 option (might be like an "I accept privacy policy" box), set required
							if ($required === 'required' && count($select_options) === 1){
								$uniquerequired = 'required';
							} else {
								$uniquerequired = '';
							}
							foreach ($select_options as $option){

								// :checked 
								$checked = '';
								if (substr($option,-8) === ':checked'){
									$option = substr($option, 0, -8);
									$checked = 'checked="checked"';
								}
								
								$previous_value = '';
								if (isset($_POST['marketking_field_'.esc_attr($custom_field->ID).'_option_'.$i])){
									$previous_value = sanitize_text_field($_POST['marketking_field_'.esc_attr($custom_field->ID).'_option_'.$i]);
								}
								echo '<p class="form-row">';
								echo '<label class="woocommerce-form__label woocommerce-form__label-for-checkbox">';
								echo '<input id="marketking_field_'.esc_attr($custom_field->ID).'" type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox marketking_custom_registration_field marketking_checkbox_registration_field marketking_field_req_'.esc_attr($uniquerequired).'" value="1" name="marketking_field_'.esc_attr($custom_field->ID).'_option_'.$i.'" '.checked(1, $previous_value, false).' '.esc_attr($uniquerequired).' '.$checked.'>';
								echo '<span>'.trim(wp_kses( $option, 
									array( 
										'a'     => array(
							        		'href' => array(),
							        		'target' => array(),
							    		) 
									) 
								)).'</span></label></p>';

								$i++;
							}

						}

					} else if ($billing_connection === 'billing_country') {
						woocommerce_form_field( 'marketking_field_'.esc_attr($custom_field->ID), array( 'default' => $previous_value, 'type' => 'country', 'class' => array( 'marketking_country_field_selector', 'marketking_custom_registration_field', 'marketking_field_req_'.esc_attr($required), 'marketking_country_field_req_'.esc_attr($required))));
						echo '<input type="hidden" id="marketking_country_registration_field_number" name="marketking_country_registration_field_number" value="'.esc_attr($custom_field->ID).'">';
					} else if ($billing_connection === 'billing_countrystate') {
						if (isset($_POST['billing_state'])){
							$post_billing_state = sanitize_text_field($_POST['billing_state']);
						} else {
							$post_billing_state = '';
						}
						woocommerce_form_field( 'marketking_field_'.esc_attr($custom_field->ID), array( 'default' => $previous_value, 'type' => 'country', 'class' => array( 'marketking_country_field_selector', 'marketking_custom_registration_field', 'marketking_field_req_'.esc_attr($required), 'marketking_country_field_req_'.esc_attr($required))));
						woocommerce_form_field( 'billing_state', array( 'placeholder' => esc_attr__('State / County', 'marketking-multivendor-marketplace-for-woocommerce'), 'default' => $post_billing_state, 'type' => 'state', 'class' => array( 'marketking_custom_registration_field', 'marketking_field_req_'.esc_attr($required))));
						echo '<input type="hidden" id="marketking_country_registration_field_number" name="marketking_country_registration_field_number" value="'.esc_attr($custom_field->ID).'">';
					} else if ($billing_connection === 'billing_vat'){
						echo '<input type="text" id="marketking_vat_number_registration_field" class="marketking_custom_registration_field marketking_field_req_'.esc_attr($required).'" name="marketking_field_'.esc_attr($custom_field->ID).'" value="'.esc_attr($previous_value).'" placeholder="'.esc_attr($field_placeholder).'" '.esc_attr($required).'>';
						$vat_enabled_countries = get_post_meta($custom_field->ID, 'marketking_field_VAT_countries', true);
						echo '<input type="hidden" id="marketking_vat_number_registration_field_countries" value="'.esc_attr($vat_enabled_countries).'">';
						echo '<input type="hidden" id="marketking_vat_number_registration_field_number" name="marketking_vat_number_registration_field_number" value="'.esc_attr($custom_field->ID).'">';
					}
					echo '</p></div>';
				}
			}
		}
	}

	function enqueue_public_resources(){

		// scripts and styles already registered by default
		wp_enqueue_script('jquery'); 

		// the following 3 scripts enable WooCommerce Country and State selectors
		if (apply_filters('marketking_enable_country_scripts_frontend', true)){
			wp_enqueue_script( 'selectWoo' );
			wp_enqueue_style( 'select2' );
			wp_enqueue_script( 'wc-country-select' );
		}

		wp_enqueue_script('marketking_public_script', plugins_url('assets/js/public.js', __FILE__), $deps = array(), filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/public.js' ), $in_footer =true);
		wp_enqueue_style('marketking_main_style', plugins_url('../includes/assets/css/style.css', __FILE__), $deps = array(), filemtime( plugin_dir_path( __FILE__ ) . '../includes/assets/css/style.css' ));

		// scripts and styles already registered by default
		wp_enqueue_script('dataTables', plugins_url('dashboard/assets/b2bkingintegration/dataTables/jquery.dataTables.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
		wp_enqueue_style( 'dataTables', plugins_url('dashboard/assets/b2bkingintegration/dataTables/jquery.dataTables.min.css', __FILE__));


		// Send display settings to JS
    	$data_to_be_passed = array(
    		'security'  => wp_create_nonce( 'marketking_security_nonce' ),
    		'ajaxurl' => admin_url( 'admin-ajax.php' ),
    		'carturl' => wc_get_cart_url(),
    		'currency_symbol' => get_woocommerce_currency_symbol(),
    		'url_available' => esc_html__('This URL is available!', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'url_not_available' => esc_html__('This URL is not available!', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'url_searching' => esc_html__('Searching availability...', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'no_vendors_yet' => esc_html__('There are no sellers yet...','marketking-multivendor-marketplace-for-woocommerce'),
    		'profile_pic' => plugins_url('../includes/assets/images/store-profile.png', __FILE__),
    		'cartstyle' => get_option( 'marketking_cart_display_setting', 'newcart' ),
    		'inquiry_success' => esc_html__( 'Your message has been received. We will get back to you as soon as possible.', 'marketking-multivendor-marketplace-for-woocommerce' ),
    		'support_success' => esc_html__( 'Your support request has been received. The vendor will get back to you as soon as possible.', 'marketking-multivendor-marketplace-for-woocommerce' ),
    		'custom_inquiry' => esc_html__( 'Inquiry', 'marketking-multivendor-marketplace-for-woocommerce' ),
    		'support_request' => esc_html__( 'Support Request', 'marketking-multivendor-marketplace-for-woocommerce' ),
    		'send_inquiry' => esc_html__( 'Send inquiry', 'marketking-multivendor-marketplace-for-woocommerce' ),
    		'inquiry_empty_fields' => esc_html__( 'Please fill all fields to submit the inquiry', 'marketking-multivendor-marketplace-for-woocommerce' ),
    		'inquiry_invalid_email' => esc_html__( 'The email address you entered is invalid', 'marketking-multivendor-marketplace-for-woocommerce' ),
    		'loadertransparenturl' => plugins_url('../includes/assets/images/loadertransparent.svg', __FILE__),
    		'loggedin' => is_user_logged_in(),
    		'request_many_vendors' => esc_html__('Your cart contains items from multiple vendors. Quote requests can only be sent to 1 vendor at a time. Please adjust cart items. You may need to reload the cart page.'),
    		'follow_text' => esc_html__('Follow','marketking-multivendor-marketplace-for-woocommerce'),
    		'following_text' => apply_filters('marketking_following_text', esc_html__('Following','marketking-multivendor-marketplace-for-woocommerce')),   
    		'are_you_sure_abuse_report' => esc_html__('Are you sure you want to submit this abuse report?','marketking-multivendor-marketplace-for-woocommerce'), 		
    		'sure_shipment_received' => esc_html__('Are you sure you want to mark this order as Received? This means you are confirming you have correctly received all packages and items within this order.', 'marketking-multivendor-marketplace-for-woocommerce'),
    		'abuse_report_sent' => esc_html__('Thank you for your submission. Your report has been received.','marketking-multivendor-marketplace-for-woocommerce'), 		
    		'abuse_report_received' => esc_html__('Your abuse report has been received.','marketking-multivendor-marketplace-for-woocommerce'),
    		'product_added_store' => esc_html__('The product has been added to your store','marketking-multivendor-marketplace-for-woocommerce'),
    		'pagetab' => marketking()->get_pagenr_query_var(),
    		'pagetab2' => get_query_var('pagenr2'),
    		'defaulttab' => apply_filters('marketking_vendor_default_tab', 'products'),
    		'sure_reply_review' => esc_html__('Are you sure you want to submit this reply? Please note that you can only reply once.','marketking-multivendor-marketplace-for-woocommerce'),
    		'datatables_folder' => plugins_url('../includes/assets/lib/dataTables/i18n/', __FILE__),
    		'tables_language_option' => apply_filters('marketking_tables_language_option_setting','English'),
    		'review_reply_submitted' => esc_html__('Your reply has been submitted.','marketking-multivendor-marketplace-for-woocommerce'),
    		'review_report_submitted' => esc_html__('Your report has been submitted.','marketking-multivendor-marketplace-for-woocommerce'),
    		'refund_request_sent' => esc_html__('Your request has been sent.','marketking-multivendor-marketplace-for-woocommerce'),
    		'sure_send_refund' => esc_html__('Are you sure you want to send this refund request?','marketking-multivendor-marketplace-for-woocommerce'),
    		'partial_exceed_refund' => esc_html__('The value requested cannot exceed the order value.','marketking-multivendor-marketplace-for-woocommerce'),
    		'b2bking_exists' => defined('B2BKING_DIR'),
    		'user_id' => get_current_user_id(),
    		'currentvendor' => marketking()->get_vendor_id_in_store_url(),
    		'currentvendorlink' => marketking()->get_store_link(marketking()->get_vendor_id_in_store_url()),
    		'is_vendor_product_pagination' => isset($_GET['product-page']) ? 1 : 0,
    		'allow_dash_store_url' => apply_filters('marketking_allow_dash_store_url', 0),
    		'allcattext' => esc_html__('All Categories','marketking-multivendor-marketplace-for-woocommerce'),
		);

		wp_localize_script( 'marketking_public_script', 'marketking_display_settings', $data_to_be_passed );
		
		
    }

	
    	
}

