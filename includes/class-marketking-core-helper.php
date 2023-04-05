<?php

class Marketkingcore_Helper{

	private static $instance = null;

	private static $data = array();

	public static function get_data($var){
		if (isset(self::$data[$var])){
			return self::$data[$var];
		}
	}

	public static function set_data($var, $value){
		self::$data[$var] = $value;
	}

	public static function switch_to_user_locale($email_address){

		$user = get_user_by('email', $email_address);
		$locale = get_user_locale($user);
		switch_to_locale($locale);
		
		// Filter on plugin_locale so load_plugin_textdomain loads the correct locale.
		add_filter( 'plugin_locale', 'get_locale' );
		
		unload_textdomain( 'marketking-multivendor-marketplace-for-woocommerce' );
		load_textdomain( 'marketking-multivendor-marketplace-for-woocommerce', MARKETKINGCORE_LANG.'/MARKETKINGCORE_LANG-'.$locale.'.mo');
		load_plugin_textdomain( 'marketking-multivendor-marketplace-for-woocommerce', false, MARKETKINGCORE_LANG );  
	}

	public static function restore_locale(){
		restore_previous_locale();

		// Remove filter.
		remove_filter( 'plugin_locale', 'get_locale' );

		// Init WC locale.
		$locale = get_locale();
		unload_textdomain( 'marketking-multivendor-marketplace-for-woocommerce' );
		load_textdomain( 'marketking-multivendor-marketplace-for-woocommerce', MARKETKINGCORE_LANG.'/MARKETKINGCORE_LANG-'.$locale.'.mo');
		load_plugin_textdomain( 'marketking-multivendor-marketplace-for-woocommerce', false, MARKETKINGCORE_LANG );  
	}

	public static function marketking_is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			// Probably a CLI request
			return false;
		}
		
		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) !== false;

		if (defined('REST_REQUEST')){
			$is_rest_api_request = true;
		}

		return apply_filters( 'is_rest_api_request', $is_rest_api_request );
	}

	public static function get_withdrawal_requests_number(){
		$number = 0;
		$vendors = marketking()->get_all_vendors();
		foreach ($vendors as $vendor){
			$active = get_user_meta($vendor->ID,'marketking_active_withdrawal', true);
			if ($active === 'yes'){
				$number++;
			}
		}
		return $number;
	}

	public static function get_vendor_shipping_methods($vendor_id){
		$vendor_shipping_methods = get_user_meta($vendor_id,'marketking_vendor_shipping_methods', true);
		return $vendor_shipping_methods;
	}

	public static function is_virtual_downloadable_order( $order_id ) {

	    // Get order
	    $order = wc_get_order( $order_id );

	    // get order items = each product in the order
	    $items = $order->get_items();

	    // Set variable
	    $is_virdown = 'yes';

	    foreach ( $items as $item ) {
	        // Get product id
	        $product = wc_get_product( $item['product_id'] );

	        if ($product){
	        	// Is virtual
	        	$is_virtual = $product->is_virtual();

	        	// Is_downloadable
	        	$is_downloadable = $product->is_downloadable();
	        	
	        	// also supports learndash products
	        	if( ($is_virtual && $is_downloadable) or $product->is_type('course') or $product->is_type('group') or $product->is_type('courses') or $product->is_type('license')){
	        	    //
	        	} else {
	        		// found not virtual or downloadable
	        		$is_virdown = 'no';
	        	}
	        } else {
	        	$is_virdown = 'no';
	        }
	        
	    }

	    // true
	    if( $is_virdown === 'yes') {
	        return true;
	    } else {
	    	return false;
	    }
	}

	public static function vendor_has_panel($panel_slug, $vendor_id = 'currentuser'){
		
		// does not apply if marketking pro not enabled
		if (!defined('MARKETKINGPRO_DIR')){
			return true;
		}

		if ($vendor_id === 'currentuser'){
			if (!marketking()->is_vendor_team_member()){

				$user_id = get_current_user_id();

				$group = get_user_meta($user_id,'marketking_group', true);

				if (!metadata_exists('post', $group, 'marketking_group_available_panel_'.esc_attr($panel_slug))){
					$checkedval = 1;
				} else {
					$checkedval = intval(get_post_meta($group, 'marketking_group_available_panel_'.esc_attr($panel_slug), true));
				}

				if ($checkedval === 1){
					return true;
				}

			}

			if (marketking()->is_vendor_team_member()){
				$vendor_id = marketking()->get_team_member_parent();
				
				// first check if parent has panel
				$group = get_user_meta($vendor_id,'marketking_group', true);

				if (!metadata_exists('post', $group, 'marketking_group_available_panel_'.esc_attr($panel_slug))){
					$checkedval = 1;
				} else {
					$checkedval = intval(get_post_meta($group, 'marketking_group_available_panel_'.esc_attr($panel_slug), true));
				}

				if ($checkedval === 1){
					// parent has panel = proceed
					// now check that this vendor has permission for this panel
					$checkedval = intval(get_user_meta(get_current_user_id(), 'marketking_teammember_available_panel_'.esc_attr($panel_slug), true));
					if ($checkedval === 1){
						return true;
					}

				}


			}
		} else {
			$user_id = $vendor_id;

			$group = get_user_meta($user_id,'marketking_group', true);

			if (!metadata_exists('post', $group, 'marketking_group_available_panel_'.esc_attr($panel_slug))){
				$checkedval = 1;
			} else {
				$checkedval = intval(get_post_meta($group, 'marketking_group_available_panel_'.esc_attr($panel_slug), true));
			}

			if ($checkedval === 1){
				return true;
			}
		}

		return false;
	}

	public static function get_dashboard_page_id(){
		return intval(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true));
	}

	public static function get_stores_page_id(){
		return intval(apply_filters( 'wpml_object_id', get_option ('marketking_stores_page_setting', 'none' ), 'post' , true));
	}

	public static function display_about_us($vendor_id){
		$aboutus = get_user_meta($vendor_id,'marketking_store_aboutus', true);
		if (!empty($aboutus)){

			$aboutus = nl2br(esc_html($aboutus));
			$allowed = array('<h2>','</h2>','<h3>','<h4>','<i>','<strong>','</h3>','</h4>','</i>','</strong>');
			$replaced = array('***h2***','***/h2***','***h3***','***h4***','***i***','***strong***','***/h3***','***/h4***','***/i***','***/strong***');

			if (apply_filters('marketking_aboutus_allow_youtube', true)){
				array_push($replaced, '***youtube***');
				array_push($replaced, '***/youtube***');
				array_push($allowed, '<iframe width="726" height="408" src="//www.youtube.com/embed/');
				array_push($allowed, '" frameborder="0" allowfullscreen=""></iframe>');
			}			

			$aboutus = str_replace($replaced, $allowed, $aboutus);
			echo '<br><br>'.$aboutus.'<br><br>';
		}

		do_action('marketking_after_vendor_aboutus', $vendor_id);
	}

	public static function is_vendor_team_member(){
		$user_id = get_current_user_id();
		$parent_vendor = get_user_meta($user_id,'marketking_parent_vendor', true);
		if (!empty($parent_vendor)){
			// check if parent is indeed a vendor
			$is_vendor = get_user_meta($parent_vendor,'marketking_group', true);
			$is_approved = get_user_meta($parent_vendor,'marketking_account_approved', true);
			if ($is_vendor === 'none' || empty($is_vendor) || ($is_approved === 'no')){
				// nothing
			} else {
				return true;
			}
		}

		return false;
	}

	public static function get_team_member_parent(){
		$user_id = get_current_user_id();
		$parent_vendor = get_user_meta($user_id,'marketking_parent_vendor', true);
		return intval($parent_vendor);
	}

	public static function get_pending_verifications_number(){
		$requestsnr = get_posts([
				'post_type' => 'marketking_vreq',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'	=> 'ids',
				'meta_query'=> array(
					'relation' => 'AND',
					array(
						'key' => 'status',
						'value' => 'pending',
						'compare' => '=',
					)
				)
			]);


		return count($requestsnr);
	}

	public static function get_refund_requests_number(){
		$requestsnr = get_posts([
				'post_type' => 'marketking_refund',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'	=> 'ids',
				'meta_query'=> array(
					'relation' => 'AND',
					array(
						'relation' => 'OR',
						array(
							'key' => 'completion_status',
							'value' => 'completed',
							'compare' => '!=',
						),
						array(
							'key' => 'completion_status',
							'compare' => 'NOT EXISTS',
						)
					),
					array(
						'key' => 'request_status',
						'value' => 'approved',
						'compare' => '='
					)
				)
			]);


		return count($requestsnr);
	}

	// returns all group rules (marketking_grule) that apply to this group id
	public static function get_group_rules($vendor_id){
		$vendor_group_id = get_user_meta($vendor_id,'marketking_group', true);
		$rules_that_apply = array();
		// get all group rules
		$group_rules = get_posts([
				'post_type' => 'marketking_grule',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'	=> 'ids',
			]);

		foreach ($group_rules as $grule_id){
			$who = get_post_meta($grule_id,'marketking_rule_agents_who', true);
			if ($who === 'group_'.$vendor_group_id){
				array_push($rules_that_apply, $grule_id);
				continue;
			}

			if ($who === 'multiple_options'){
				$multiple_options = get_post_meta($rule_id, 'marketking_rule_agents_who_multiple_options', true);
				$multiple_options_array = explode(',', $multiple_options);

				if (in_array('group_'.$vendor_group_id, $multiple_options_array)){
					array_push($rules_that_apply, $rule_id);
					continue;
				}
			}
		}

		return $rules_that_apply;
	}

	public static function apply_group_rules($vendor_id){
		$group_rules_applicable = marketking()->get_group_rules($vendor_id);
		// foreach rule, check if the condition is met, and then apply it
		foreach ($group_rules_applicable as $group_rule_id){
			$howmuch = floatval(get_post_meta($group_rule_id,'marketking_rule_howmuch', true));
			$newgroup = get_post_meta($group_rule_id, 'marketking_rule_who', true);
			$newgroup_id = explode('_', $newgroup)[1];

			$condition = get_post_meta($group_rule_id, 'marketking_rule_applies', true);

			$total_orders_amount = 0;

			$vendor_orders = get_posts( array( 'post_type' => 'shop_order','post_status'=>'any','numberposts' => -1, 'author'   => $vendor_id, 'fields' =>'ids') );

			foreach ($vendor_orders as $order){
				$orderobj = wc_get_order($order);

				$status = $orderobj->get_status();
				// check if approved
				if (in_array($status,apply_filters('marketking_earning_completed_statuses', array('completed')))){
					$total_orders_amount += $orderobj->get_total();
				}
				
			}

			if ($condition === 'order_value_total'){

				// calculate agent order value total
				if ($total_orders_amount >= $howmuch){
					// change group
					update_user_meta($vendor_id,'marketking_group', $newgroup_id);
				}
			}
		}
	}

	// BOOKINGS helper
	public static function get_posted_availability() {
		$availability = array();
		$row_size     = isset( $_POST['wc_booking_availability_type'] ) ? sizeof( $_POST['wc_booking_availability_type'] ) : 0;
		for ( $i = 0; $i < $row_size; $i ++ ) {
			$availability[ $i ]['type']     = wc_clean( $_POST['wc_booking_availability_type'][ $i ] );
			$availability[ $i ]['bookable'] = wc_clean( $_POST['wc_booking_availability_bookable'][ $i ] );
			$availability[ $i ]['priority'] = intval( $_POST['wc_booking_availability_priority'][ $i ] );

			switch ( $availability[ $i ]['type'] ) {
				case 'custom':
					$availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_date'][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_date'][ $i ] );
					break;
				case 'months':
					$availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_month'][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_month'][ $i ] );
					break;
				case 'weeks':
					$availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_week'][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_week'][ $i ] );
					break;
				case 'days':
					$availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_day_of_week'][ $i ] );
					$availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_day_of_week'][ $i ] );
					break;
				case 'time':
				case 'time:1':
				case 'time:2':
				case 'time:3':
				case 'time:4':
				case 'time:5':
				case 'time:6':
				case 'time:7':
					$availability[ $i ]['from'] = wc_booking_sanitize_time( $_POST['wc_booking_availability_from_time'][ $i ] );
					$availability[ $i ]['to']   = wc_booking_sanitize_time( $_POST['wc_booking_availability_to_time'][ $i ] );
					break;
				case 'time:range':
				case 'custom:daterange':
					$availability[ $i ]['from'] = wc_booking_sanitize_time( $_POST['wc_booking_availability_from_time'][ $i ] );
					$availability[ $i ]['to']   = wc_booking_sanitize_time( $_POST['wc_booking_availability_to_time'][ $i ] );

					$availability[ $i ]['from_date'] = wc_clean( $_POST['wc_booking_availability_from_date'][ $i ] );
					$availability[ $i ]['to_date']   = wc_clean( $_POST['wc_booking_availability_to_date'][ $i ] );
					break;
			}
		}

		return $availability;
	}

	// BOOKINGS helper
	public static function save_posted_availability( $resource_id ) {
		$resource = new WC_Product_Booking_Resource( $resource_id );
		$resource->set_props( array(
			'qty'          => wc_clean( $_POST['_wc_booking_qty'] ),
			'availability' => marketking()->get_posted_availability(),
		) );
		$resource->save();
		echo esc_html( $resource_id );
		exit();
	}


	public static function vendor_products_are_hidden($vendor_id){
		$hidden = get_transient('marketking_vendor_products_hidden'.$vendor_id);
		if (!$hidden){
			// get a random vendor product
			$products = get_posts( array( 
				'post_type' => 'product',
				'numberposts' => 1,
				'post_status'    => 'any',
				'fields'    => 'ids',
				'author'	=> $vendor_id
			));

			$hidden = 'no';

			foreach($products as $productid){
				$product = wc_get_product($productid);
				if ($product->get_catalog_visibility() === 'hidden'){
					$hidden = 'yes';
				}
			}
			set_transient('marketking_vendor_products_hidden'.$vendor_id, $hidden);
		}
		

		return $hidden;
	}

	public static function set_vendor_products_visibility($vendor_id, $visibility){
		$products = marketking()->get_vendor_products($vendor_id);

		foreach ($products as $product_id){

			$product = wc_get_product($product_id);
			$product->set_catalog_visibility($visibility);
			$product->save();
			
		}

		if ($visibility === 'visible'){
			set_transient('marketking_vendor_products_hidden'.$vendor_id, 'no');
		} else if ($visibility === 'hidden'){
			set_transient('marketking_vendor_products_hidden'.$vendor_id, 'yes');

		}

	}

	public static function customer_has_purchased($customer_email, $user_id, $product_id){
		global $wpdb; 
		
		$transient_name = 'wc_cbp_' . md5( $customer_email . $user_id . WC_Cache_Helper::get_transient_version( 'orders' ) ); 
		
		if ( false === ( $result = get_transient( $transient_name ) ) ) { 
			$customer_data = array( $user_id ); 
		
			if ( $user_id ) { 
				$user = get_user_by( 'id', $user_id ); 
		
				if ( isset( $user->user_email ) ) { 
					$customer_data[] = $user->user_email; 
				} 
			} 
		
			if ( is_email( $customer_email ) ) { 
				$customer_data[] = $customer_email; 
			} 
		
			$customer_data = array_map( 'esc_sql', array_filter( array_unique( $customer_data ) ) ); 
			$statuses = array_map( 'esc_sql', apply_filters('marketking_has_purchased_statuses', array('processing','completed','on-hold')) ); 
		
			if ( sizeof( $customer_data ) == 0 ) { 
				return false; 
			} 
		
			$result = $wpdb->get_col( " 
				SELECT im.meta_value FROM {$wpdb->posts} AS p 
				INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id 
				INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id 
				INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id 
				WHERE p.post_status IN ( 'wc-" . implode( "', 'wc-", $statuses ) . "' ) 
				AND pm.meta_key IN ( '_billing_email', '_customer_user' ) 
				AND im.meta_key IN ( '_product_id', '_variation_id' ) 
				AND im.meta_value != 0 
				AND pm.meta_value IN ( '" . implode( "', '", $customer_data ) . "' ) 
			" ); 
			$result = array_map( 'absint', $result ); 
		
			set_transient( $transient_name, $result, DAY_IN_SECONDS * 30 ); 
		} 
		return in_array( absint( $product_id ), $result ); 
	}

	public static function refresh_vendor_vacations(){

		// check for all vendors if they're on vacation, for the purpose of updating visibility /cronjoblike
		$vendors = marketking()->get_all_vendors();
		foreach ($vendors as $vendor){
			$vacation = marketking()->is_on_vacation($vendor->ID);
		}
	}

	public static function is_on_vacation($vendor_id){

		$vacation_enabled = get_user_meta($vendor_id, 'marketking_vacation_enabled', true);
		if ($vacation_enabled === 'yes'){
			$closingtime = get_user_meta($vendor_id,'marketking_vacation_closingtime', true);
			if ($closingtime === 'now'){
				return true;
			} else if ($closingtime === 'dates'){
				$closingstart = strtotime(get_user_meta($vendor_id,'marketking_vacation_closingstart', true));
				$closingend = strtotime(get_user_meta($vendor_id,'marketking_vacation_closingend', true));

				// check that current time is between start and end
				$currenttime = time();
				if ($currenttime > $closingstart && $currenttime < $closingend){

					if (marketking()->vendor_products_are_hidden($vendor_id) === 'no'){
						marketking()->set_vendor_products_visibility($vendor_id,'hidden');
					}

					return true;

				} else {

					if (marketking()->vendor_products_are_hidden($vendor_id) === 'yes'){
						marketking()->set_vendor_products_visibility($vendor_id,'visible');

						// vacation finished, close vacation mode
						update_user_meta($vendor_id, 'marketking_vacation_enabled', 'no');

					}

					return false;

				}
			}
			
		} else {
			return false;
		}
	}

	// if count is set to true, this function returns the number of products
	public static function get_vendor_products($vendor_id, $count = false){

		$products = get_posts( array( 
			'post_type' => 'product',
			'numberposts' => -1,
			'post_status'    => 'any',
			'fields'    => 'ids',
			'author'	=> $vendor_id
		));

		foreach ($products as $index=>$product_id){
			if(get_post_meta($product_id,'marketking_is_product_standby', true) === 'yes'){
				if ($product_id == get_option('marketking_product_standby_'.get_current_user_id(), 'none')){
					unset($products[$index]);
				}
			}
		}

		if ($count === true){
			return count($products);
		}

		return $products;
	}

	public static function get_vendor_rating($vendor_id, $decimals = 2){
		global $wpdb;

		$result = $wpdb->get_row( $wpdb->prepare(
			"SELECT AVG(cm.meta_value) as average, COUNT(wc.comment_ID) as count FROM $wpdb->posts p
			INNER JOIN $wpdb->comments wc ON p.ID = wc.comment_post_ID
			LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = wc.comment_ID
			WHERE p.post_author = %d AND p.post_type = 'product' AND p.post_status = 'publish'
			AND ( cm.meta_key = 'rating' OR cm.meta_key IS NULL) AND wc.comment_approved = 1
			ORDER BY wc.comment_post_ID", $vendor_id ) );

		if (!isset($result->average) or $result->average === null){
			$average = 0;
		} else {
			$average = $result->average;
		}

		$rating_value = apply_filters( 'marketking_vendor_rating', array(
			'rating' => number_format( $average, $decimals ),
			'count'  => (int) $result->count
		), $vendor_id );

		return $rating_value;
	}

	public static function get_vendor_email($vendor_id){
		// if store email is set, store email, else user email
		$store_email = get_user_meta($vendor_id, 'marketking_store_email', true);
		if (empty($store_email)){
			$data = get_userdata($vendor_id);
			$store_email = $data->user_email;
		}
		return $store_email;
	}

	public static function is_admin($user_id){
		// check if user is admin. if so, then vendor also for some calculations
		$vendor = new WP_User($user_id);
		if ($vendor->has_cap('manage_woocommerce')){
			return true;
		}

		return false;
	}

	public static function has_vendor_application_pending($user_id){

		$pending = get_user_meta($user_id,'marketking_vendor_application_pending',true);
		if ($pending === 'yes'){
			return true;
		} else {
			return false;
		}
	}


	public static function is_vendor($user_id){
		$user_id = intval($user_id);
		$vendor_group = get_user_meta($user_id,'marketking_group',true);
		if ($vendor_group !== 'none' && !empty($vendor_group)){
			return true;
		} else {
			return false;
		}
	}

	public static function get_suborders_of_order($order_id){

		$args = array(
			'parent' => $order_id,
		);
		$orders = wc_get_orders( $args );

		return $orders;
	}

	public static function get_parent_order($order_id){
		$order = wc_get_order($order_id);
		return $order->get_parent_id();
	}

	public static function is_suborder($order_id){
		$order = wc_get_order($order_id);
		if($order->get_parent_id()){
			if($order->get_parent_id()>0){
				return true;
			}
		}
		return false;
	}

	public static function is_multivendor_order($order_id){
		if (count(marketking()->get_vendors_of_order($order_id))>1){
			return true;
		} else {
			return false;
		}
	}

	//single vendor order
	public static function get_order_vendor($order_id){
		$vendor_id = get_post_field( 'post_author', $order_id );
		return $vendor_id;
	}	

	// get vendor invoice data
	// accepts: store, address, custom, logo
	public static function get_vendor_invoice_data($vendor_id, $data){
		$data = get_user_meta($vendor_id, 'marketking_invoice'.$data, true);
		return $data;
	}

	// for multivendor orders
	public static function get_vendors_of_order($order_id){

		if (is_object($order_id)){
			$orderobj = $order_id;
		} else {
			$orderobj = wc_get_order($order_id);
		}

		$vendors = array();
		$items = $orderobj->get_items();
		foreach ($items as $product){
			$vendor_id = get_post_field( 'post_author', $product->get_product_id() );
			array_push($vendors, $vendor_id);
		}
		return array_unique(array_filter($vendors));
	}

	public static function is_connected_stripe($vendor_id){
		$is_connected = intval(get_user_meta($vendor_id, 'vendor_connected', true));

		if( $is_connected === 1) {
			return true;
		}

		return false;
	}

	public static function admin_is_only_vendor(){
		$vendors = marketking()->get_vendors_in_cart();
		if (count($vendors) == 1){
			if (intval(reset($vendors)) == 1){
				return true;
			}
		}
		return false;
	}

	// delete all data related to earnings and payouts
	public static function reset_earnings_data(){
		$earnings = get_posts( array( 
		    'post_type' => 'marketking_earning',
		    'numberposts' => -1,
		    'post_status'    => 'any',
		    'fields'    => 'ids',
		));

		foreach ($earnings as $earning){
			wp_delete_post($earning);
		}

		$agents = get_users(array(
		    'meta_key'     => 'marketking_group',
		    'meta_value'   => 'none',
		    'meta_compare' => '!=',
		    'fields' => 'ids',
		));

		foreach ($agents as $agent){
			delete_user_meta($agent,'marketking_user_payout_history');
			delete_user_meta($agent,'marketking_outstanding_earnings');
			delete_user_meta($agent,'marketking_user_balance_history');
		}
	}



	public static function get_vendors_in_cart(){
		$cart = WC()->cart;
		$vendors = array();
		if (is_object($cart)){
			foreach($cart->get_cart() as $cart_item){

				$vendor_id = marketking()->get_product_vendor( $cart_item['product_id'] );
				if (!empty($vendor_id)) {
				   array_push($vendors, $vendor_id);
				}
			}
		}
		return array_unique(array_filter($vendors));
	}

	public static function load_tables_with_ajax($vendor_id){
		// determine whether should load tables with ajax: true or false for the vendor
		$vendor_setting = get_user_meta($vendor_id, 'marketking_vendor_load_tables_ajax', true);

		if ($vendor_setting === 'yes'){
			return true;
		}

		return false;
	}

	public static function get_all_vendors(){
		$vendors_active = get_users(array(
			'meta_key'     => 'marketking_group',
			'meta_value'   => 'none',
			'meta_compare' => '!=',
		));

		$vendors_inactive  = get_users(array(
			'meta_key'     => 'marketking_group',
			'meta_value'   => 'none',
			'meta_compare' => '=',
		));

		foreach ($vendors_inactive as $index => $inactive_vendor){
			$customer_vendor = get_user_meta($inactive_vendor->ID,'marketking_user_choice', true);
			if ($customer_vendor !== 'vendor'){
				unset($vendors_inactive[$index]);
			}
		}

		$users = array_merge($vendors_active, $vendors_inactive);

		return $users;
	}

	public static function vendor_is_inactive($vendor_id){

		$admin_user_id = apply_filters('marketking_admin_user_id', 1);

		// admin cannot be inactive
		if (intval($vendor_id) === intval($admin_user_id)){
			return false;
		}
		if (intval($vendor_id) === 1){
			return false;
		}

		if (get_user_meta($vendor_id, 'marketking_group', true) === 'none'){
			return true;
		}
		return false;
	}

	public static function display_stores_list($vendors, $showcat = 'yes'){
		ob_start();

		include(apply_filters('marketking_template', MARKETKINGCORE_DIR . 'public/templates/stores-list.php'));

		$content = ob_get_clean();
		$content = apply_filters('marketking_stores_list_page_content', $content, $vendors);
		return $content;

	}


	public static function get_favorite_vendors($user_id){
		// get all vendors and then go through them
		$vendors = marketking()->get_all_vendors();
		$followed_vendors = array();
		// remove vendors that are not favorite
		foreach ($vendors as $vendor){
			$follows = get_user_meta($user_id,'marketking_follows_vendor_'.$vendor->ID, true);
			if ($follows === 'yes'){
				array_push($followed_vendors, $vendor);
			}
		}
		return $followed_vendors;
	}

	public static function get_number_of_followers($vendor_id){
		$users = get_users(array(
			'meta_query'=> array(
				'relation' => 'AND',
				array(
					'key' => 'marketking_follows_vendor_'.$vendor_id,
					'value' => 'yes',
					'compare' => '=',
				),
			),
			'fields' => 'ids'
		));

		$nr = count($users);
		if (empty($nr)){
			$nr = 0;
		}

		return $nr;
	}

	public static function get_resized_image( $logo, $size = 'full' ) {

		if (apply_filters('marketking_skip_image_resize', false, $logo, $size)){
			return $logo;
		}

		if ( is_array( $logo ) ) {
		    $logo = array_shift( $logo );  
		}

		global $_wp_additional_image_sizes;

		if ( 'full' !== $size  && ( isset( $_wp_additional_image_sizes[ $size ] ) || in_array( $size, array(            'thumbnail', 'medium', 'large', 'medium_large'), true ) ) ) {

		  	if ( in_array( $size, array('thumbnail', 'medium', 'large', 'medium_large'), true ) ) {
		  		$img_width  = get_option( $size . '_size_w' );         
		  		$img_height = get_option( $size . '_size_h' );     
				$img_crop   = get_option( $size . '_size_crop' );  
			} else {         
				$img_width  = $_wp_additional_image_sizes[ $size ]['width'];   
				$img_height = $_wp_additional_image_sizes[ $size ]['height'];      
				$img_crop   = $_wp_additional_image_sizes[ $size ]['crop'];     
			}
		
			$upload_dir        = wp_upload_dir(); 
			$logo_path         = str_replace( array($upload_dir['baseurl'], $upload_dir['url'], WP_CONTENT_URL), array( $upload_dir['basedir'], $upload_dir['path'], WP_CONTENT_DIR ), $logo ); 
			$path_parts        = pathinfo( $logo_path );      
			$dims              = $img_width . 'x' . $img_height;      

			$file_ext = isset($path_parts['extension']) ? $path_parts['extension'] : '';
			
			$resized_logo_path = str_replace( '.' . $file_ext, '-' . $dims . '.' . $file_ext, $logo_path );

			if ( strstr( $resized_logo_path, 'http:' ) || strstr( $resized_logo_path, 'https:' ) ) { 
				return $logo;   
			}

			if ( ! file_exists( $resized_logo_path ) ) { 
				ob_start();
				$image = wp_get_image_editor( $logo_path ); 

			  	if ( ! is_wp_error( $image ) ) {   
			 		$resize = $image->resize( $img_width, $img_height, $img_crop );
					if ( ! is_wp_error( $resize ) ) { 
				  		$save = $image->save( $resized_logo_path );  
				 		if ( ! is_wp_error( $save ) ) {     
				 			$logo = dirname( $logo ) . '/' . basename( $resized_logo_path );
				 		}
				 	}
				}
			 	ob_get_clean();     
			} else {
			 	$logo = dirname( $logo ) . '/' . basename( $resized_logo_path );      
			}
		}

		return $logo;
	}


	public static function get_store_name_display($vendor_id){
		$store_name = get_user_meta($vendor_id,'marketking_store_name', true);
		if (empty($store_name)){
			$udata = get_userdata($vendor_id);
			if (is_object($udata)){
				$store_name = $udata->display_name;		
			} else {
				$store_name = '';
			}
		}
		return $store_name;
	}


	// returns user ID
	public static function get_other_chat_party($message_id){

		$otherparty = '';

		$party1 = get_post_meta ($message_id, 'marketking_message_message_1_author', true);
		$party2 = get_post_meta ($message_id, 'marketking_message_user', true);
		$adminuser = new WP_User(1);
		$admin_user_login = $adminuser->user_login;


		// replace shop with admin user
		if ($party1 == 'shop'){
			$party1 = $admin_user_login;
		}
		if ($party2 == 'shop'){
			$party2 = $admin_user_login;
		}

		$user = wp_get_current_user();
		$current_user_login = $user->user_login;

		if ($current_user_login == $party1){
			// party 2 is the other party
			$otherparty = $party2;
		}
		if ($current_user_login == $party2){
			// party 1 is the other party
			$otherparty = $party1;
		}

		// return user ID
		$otherpartyuser = get_user_by('login', $otherparty);

		if ($otherpartyuser){
			return $otherpartyuser->ID;
		} else {
			return 0;
		}
		
		
	}

	// returns correct icon or letters for messaging, avatar, top right corner
	public static function get_display_icon_image($vendor_id){

		if ($vendor_id == esc_html__('System Message','marketking-multivendor-marketplace-for-woocommerce')){
			return 'SY';
		}

		if (!is_numeric($vendor_id)){
			// may be vendor username, try to get ID
			$possible_user = get_user_by('login', $vendor_id);
			if ($possible_user){
				$vendor_id = $possible_user->ID;
			} else {
				$vendor_id = 0;
			}
		}

		if($vendor_id == 0){
			// unknown
			return 'NA';
		}

		if ($vendor_id === 'shop'){
			$vendor_id = 1;
		}

		$img = get_user_meta($vendor_id, 'marketking_profile_logo_image', true);

		if (empty($img)){
		    // get the current vendor's first 2 letters name

		    $store_name = get_user_meta($vendor_id,'marketking_store_name', true);
		    if (empty($store_name)){
		    	$currentuser = new WP_User($vendor_id);
		    	$store_name = $currentuser->user_login;
		    }

		    $img = esc_html(strtoupper(substr($store_name, 0, 2)));


		} else {
		    $img = marketking()->get_resized_image($img,'thumbnail');
		}

		return $img;
	}

	public static function get_store_profile_image_link($vendor_id){
		$img = get_user_meta($vendor_id, 'marketking_profile_logo_image', true);
		return $img;
	}

	public static function get_store_banner_image_link($vendor_id){
		$img = get_user_meta($vendor_id, 'marketking_profile_logo_image_banner', true);
		return $img;
	}


	public static function calculate_vendor_commission($vendor_id, $order_id){

		// by default, commission rules set the admin commission, but it can be reversed in settings
		if(defined('MARKETKINGPRO_DIR') && (intval(get_option( 'marketking_enable_complexcommissions_setting', 1 )) === 1) && (intval(get_option( 'marketking_reverse_commission_rules_setting', 0 )) === 1)){
			$commission_rules_set_vendor_commission = true;
		} else {
			$commission_rules_set_vendor_commission = false;
		}

		// Step 1 = We calculate Admin Commission (later we substract it to get the vendor commission)

		// get order totals first
		$order = wc_get_order($order_id);
		$order_total = $order->get_total();
		$shipping_total = $order->get_shipping_total();
		$taxes = $order->get_taxes();
		$tax_total = 0;
		foreach ($taxes as $tax){
			$tax_total += $tax->get_tax_total();
		}
		$tax_total+=+$order->get_shipping_tax();

		// Price based on Country Integration (apply exchange rates for earnings here)
		if (function_exists('wcpbc_get_base_currency')){
			$rate = 1;
			$order_currency = $order->get_currency();

			$base_currency = wcpbc_get_base_currency();
			$rates         = WCPBC_Pricing_Zones::get_currency_rates();

			if ($order_currency !== $base_currency){
				// not using default currency, we must convert
				// first get the rate for the order currency
				foreach ( $rates as $currency => $ratec ) {
					if ($currency === $order_currency){
						$rate = $ratec; 
					}
				}
				$order_total = $order_total / $rate;
				$shipping_total = $shipping_total / $rate;
				$tax_total = $tax_total / $rate;
			}
		}
		

		$direction = apply_filters('marketking_competing_rules_calculation_direction', 'lowest');

		// get calculation basis
		$calculation_basis = $order_total;
		$tax_fee_recipient = apply_filters('marketking_tax_fee_recipient_vendor', get_option('marketking_tax_fee_recipient_setting', 'vendor'), $vendor_id);
		// check for vendor ID overwrite


		$shipping_fee_recipient = get_option('marketking_shipping_fee_recipient_setting', 'vendor');

		if ($tax_fee_recipient === 'vendor' || $tax_fee_recipient === 'admin'){
			$calculation_basis -= $tax_total;
		}

		if ($shipping_fee_recipient === 'vendor' || $shipping_fee_recipient === 'admin'){
			$calculation_basis -= $shipping_total;
		}

		update_post_meta($order_id,'tax_fee_recipient', $tax_fee_recipient);

		// get calculation formula
		// simple formula
		if(!defined('MARKETKINGPRO_DIR') || (intval(get_option( 'marketking_enable_complexcommissions_setting', 1 )) !== 1)){

			$type = get_option('marketking_commission_type_setting', 'percentage');
			// this is the ADMIN commission value
			$value = get_option('marketking_commission_value_setting', 0); 

			if ($type==='percentage'){
				$commission = $value/100*$calculation_basis;
			} else if ($type==='flat'){
				$commission = $value;
			}

			if ($tax_fee_recipient === 'admin'){
				$commission += $tax_total; // admin gets all tax
			}

			if ($shipping_fee_recipient === 'admin'){
				$commission += $shipping_total; // admin gets all shipping
			}

		} else {
			// complex commissions enabled
			$commission_rules_total = 0;
			$rules = marketking()->get_all_vendor_rules($vendor_id);

			// 1. apply rules that apply once per order
			$rules_that_apply_once = marketking()->filter_which_rules_apply_once($rules);

			if (!empty($rules_that_apply_once)){

				$fixed_commission_amount = marketking()->get_commission_amount_once($rules_that_apply_once, $calculation_basis, 'fixed', $direction);
				$percentage_commission_amount = marketking()->get_commission_amount_once($rules_that_apply_once, $calculation_basis, 'percentage',$direction);

				$item_commission = $fixed_commission_amount + $percentage_commission_amount;
				$commission_rules_total += $item_commission;
			}

			$commission_rules_total = round($commission_rules_total, 2);

			// 2. apply rules that apply per product
			foreach ($order->get_items() as $item_id => $item ) {

				// Get the WC_Order_Item_Product object properties in an array
				$item_data = $item->get_data();

				if ($item['quantity'] > 0) {
					// get the WC_Product object
					$product_id = $item['product_id'];

					$rules_that_apply_to_product = marketking()->filter_which_rules_apply_to_product($rules, $order_id, $product_id);

					if (!empty($rules_that_apply_to_product)){
						// get the calculation basis for the product
						$item_total = round($item->get_total(), 2); // Get the item line total discounted
						$item_total_tax = round($item->get_total_tax(), 2); // Get the item line total  tax discounted

						if (function_exists('wcpbc_get_base_currency')){
							// convert
							$item_total = $item_total / $rate;
							$item_total_tax = $item_total_tax / $rate;
						}

						if ($tax_fee_recipient === 'adminvendor'){
							$calculation_basis = $item_total + $item_total_tax;
						} else {
							$calculation_basis = $item_total;
						}

						$quantity = $item->get_quantity();

						$fixed_commission_amount = marketking()->get_commission_amount($rules_that_apply_to_product, $calculation_basis, 'fixed', $quantity, $direction);

						$percentage_commission_amount = marketking()->get_commission_amount($rules_that_apply_to_product, $calculation_basis, 'percentage', $quantity, $direction);
				
						$item_commission = $fixed_commission_amount + $percentage_commission_amount;

						$item_commission = apply_filters('marketking_item_commission', $item_commission, $item, $product_id, $quantity);
						$commission_rules_total += $item_commission;

					}
				}
			}

			$commission = $commission_rules_total;



			if (!$commission_rules_set_vendor_commission){
				// rules set admin commission
				if ( $tax_fee_recipient === 'admin' ){
					$commission += $tax_total; // admin gets all tax
				}
			} else {
				// commission rules set vendor commission
				if ( $tax_fee_recipient === 'vendor' ){
					$commission += $tax_total; // admin gets all tax
				}
			}

			if (!$commission_rules_set_vendor_commission){
				// rules set admin commission
				if ( $shipping_fee_recipient === 'admin' ){
					$commission += $shipping_total; // admin gets all tax
				}
			} else {
				// commission rules set vendor commission
				if ( $shipping_fee_recipient === 'vendor' ){
					$commission += $shipping_total; // admin gets all tax
				}
			}
			

		}

		if($commission_rules_set_vendor_commission){
			$vendor_commission = $commission;
		} else {
			// the vendor's commission is order total - admin commission
			$vendor_commission = $order_total - $commission;
		}

		return apply_filters('marketking_final_vendor_commission', $vendor_commission, $order_id, $vendor_id);

	}

	public static function vendor_can_add_more_products($vendor_id){
		// get vendor group and check if there's any maximum in the nr of products.
		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		$current_id = $vendor_id;

		$groupid = get_user_meta($current_id,'marketking_group', true);
		if (!empty($groupid)){
			$group_allowed_products_number = intval(get_post_meta($groupid, 'marketking_group_allowed_products_number', true));
			if (!empty($group_allowed_products_number)){
				// get current number of products a vendor has
				$vendor_products_nr = marketking()->get_vendor_products($current_id, true);
				if ($vendor_products_nr >= $group_allowed_products_number){
					return false;
				}
			}
		}

		return true;
	}

	// takes an array of rule IDs and returns the ones that apply to the product
	public static function filter_which_rules_apply_to_product($rules, $order_id, $product_id){

		$rules_that_apply = array();
		foreach ($rules as $rule_id){
			$rule_applies = get_post_meta($rule_id,'marketking_rule_applies', true);

		
			if ($rule_applies === 'cart_total'){
				array_push($rules_that_apply, $rule_id);
				continue;
			}

			$explosion = explode('_', $rule_applies);
			// if is category rule
			if ($explosion[0] === 'category'){
				$category_id = $explosion[1];
				// check if product has category
				if( has_term( $explosion[1], 'product_cat', $product_id ) ){
					array_push($rules_that_apply, $rule_id);
					continue;
				}
			}

			// if is category rule
			if ($explosion[0] === 'tag'){
				$tag_id = $explosion[1];
				if( has_term( $explosion[1], 'product_tag', $product_id ) ){
					array_push($rules_that_apply, $rule_id);
					continue;
				}
			}

			// if is multiple
			if ($explosion[0] === 'multiple'){
				$multiple_options = get_post_meta($rule_id, 'marketking_rule_applies_multiple_options', true);
				$multiple_options_array = explode(',', $multiple_options);

				// check each option against the product
				foreach ($multiple_options_array as $option){
					$explosionoption = explode('_', $option);
					if ($explosionoption[0] === 'category'){
						// check if product has category
						if( has_term( $explosionoption[1], 'product_cat', $product_id ) ){
							array_push($rules_that_apply, $rule_id);
							break;
						}
					} else if ($explosionoption[0] === 'tag'){
						// check if product has tag
						if( has_term( $explosionoption[1], 'product_tag', $product_id ) ){
							array_push($rules_that_apply, $rule_id);
							break;
						}
					}
				}

			}

		}

		$rules_that_apply = array_filter(array_unique($rules_that_apply));
		return $rules_that_apply;
	}

	public static function get_commission_amount_once($rules, $calculation_basis, $type_given, $direction){
		// find the highest commission of this type, among rules
		if ($direction === 'highest'){
			$highest_commission_howmuch = 0;
			foreach($rules as $rule_id){
				// if rule matches type
				$type = get_post_meta($rule_id,'marketking_rule_what', true);
				if ($type === $type_given){
					// rule matches
					$howmuch = floatval(get_post_meta($rule_id,'marketking_rule_howmuch', true));
					if ($howmuch > $highest_commission_howmuch){
						$highest_commission_howmuch = $howmuch;
					}
				}
			}

			if ($highest_commission_howmuch !== 0){
				$commission_value = 0;

				if ($type_given === 'percentage'){
					$commission_value = $calculation_basis * ($highest_commission_howmuch / 100);

				} else if ($type_given === 'fixed'){
					$commission_value = $highest_commission_howmuch;
				}

				return $commission_value;

			} else {
				return 0;
			}
		}

		if ($direction === 'lowest'){
			$lowest_commission_howmuch = 'none';
			foreach($rules as $rule_id){
				// if rule matches type
				$type = get_post_meta($rule_id,'marketking_rule_what', true);
				if ($type === $type_given){
					// rule matches
					$howmuch = floatval(get_post_meta($rule_id,'marketking_rule_howmuch', true));
					if ($lowest_commission_howmuch === 'none'){
						$lowest_commission_howmuch = $howmuch;
					}

					if ($howmuch < $lowest_commission_howmuch){
						$lowest_commission_howmuch = $howmuch;
					}
				}
			}

			if ($lowest_commission_howmuch !== 'none'){
				$commission_value = 0;

				if ($type_given === 'percentage'){
					$commission_value = $calculation_basis * ($lowest_commission_howmuch / 100);

				} else if ($type_given === 'fixed'){
					$commission_value = $lowest_commission_howmuch;
				}

				return $commission_value;

			} else {
				return 0;
			}
		}
		
	}

	public static function get_commission_amount($rules, $calculation_basis, $type_given, $quantity, $direction){

		if ($direction === 'highest'){
			// find the highest commission of this type, among rules
			$highest_commission_howmuch = 0;
			foreach($rules as $rule_id){
				// if rule matches type
				$type = get_post_meta($rule_id,'marketking_rule_what', true);
				if ($type === $type_given){
					// rule matches
					$howmuch = floatval(get_post_meta($rule_id,'marketking_rule_howmuch', true));
					if ($howmuch > $highest_commission_howmuch){
						$highest_commission_howmuch = $howmuch;
					}
				}
			}

			if ($highest_commission_howmuch !== 0){
				$commission_value = 0;

				if ($type_given === 'percentage'){
					$commission_value = $calculation_basis * ($highest_commission_howmuch / 100);

				} else if ($type_given === 'fixed'){
					$commission_value = $highest_commission_howmuch * $quantity;
				}

				return $commission_value;

			} else {
				return 0;
			}
		}

		if ($direction === 'lowest'){
			// find the highest commission of this type, among rules
			$lowest_commission_howmuch = 'none';
			foreach($rules as $rule_id){
				// if rule matches type
				$type = get_post_meta($rule_id,'marketking_rule_what', true);
				if ($type === $type_given){
					// rule matches
					$howmuch = floatval(get_post_meta($rule_id,'marketking_rule_howmuch', true));
					if ($lowest_commission_howmuch === 'none'){
						$lowest_commission_howmuch = $howmuch;
					}

					if ($howmuch < $lowest_commission_howmuch){
						$lowest_commission_howmuch = $howmuch;
					}
				}
			}

			if ($lowest_commission_howmuch !== 'none'){
				$commission_value = 0;

				if ($type_given === 'percentage'){
					$commission_value = $calculation_basis * ($lowest_commission_howmuch / 100);

				} else if ($type_given === 'fixed'){
					$commission_value = $lowest_commission_howmuch * $quantity;
				}

				return $commission_value;

			} else {
				return 0;
			}
		}
		
	}

	// takes an array of rule IDs and returns the ones that apply once per order
	public static function filter_which_rules_apply_once($rules){

		$rules_that_apply = array();
		foreach ($rules as $rule_id){
			$rule_applies = get_post_meta($rule_id,'marketking_rule_applies', true);

			if ($rule_applies === 'once_per_order'){
				array_push($rules_that_apply, $rule_id);
				continue;
			}
		}

		$rules_that_apply = array_filter(array_unique($rules_that_apply));
		return $rules_that_apply;
	}

	// returns an ARRAY of rule ids that apply to the vendor
	public static function get_all_vendor_rules($vendor_id){
		// get rules that apply to all vendors
		$all_vendor_rules = get_posts([
				'post_type' => 'marketking_rule',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'	=> 'ids',
				'meta_query'=> array(
					'relation' => 'AND',
					array(
						'key' => 'marketking_rule_vendors_who',
						'value' => 'all_vendors'
					)
				)
			]);

		// get all individual rules
		$individual_rules = get_posts([
				'post_type' => 'marketking_rule',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'	=> 'ids',
				'meta_query'=> array(
					'relation' => 'AND',
					array(
						'key' => 'marketking_rule_vendors_who',
						'value' => 'vendor_'.$vendor_id
					)
				)
			]);

		// get all group rules
		$vendor_group_id = get_user_meta($vendor_id,'marketking_group', true);

		$group_rules = get_posts([
				'post_type' => 'marketking_rule',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'	=> 'ids',
				'meta_query'=> array(
					'relation' => 'AND',
					array(
						'key' => 'marketking_rule_vendors_who',
						'value' => 'group_'.$vendor_group_id
					)
				)
			]);

		// get all multiple option rules
		$multiple_option_rules = get_posts([
				'post_type' => 'marketking_rule',
				'post_status' => 'publish',
				'numberposts' => -1,
				'fields'	=> 'ids',
				'meta_query'=> array(
					'relation' => 'AND',
					array(
						'key' => 'marketking_rule_vendors_who',
						'value' => 'multiple_options'
					)
				)
			]);

		$rules_that_apply = array();
		foreach ($multiple_option_rules as $rule_id){
			$multiple_options = get_post_meta($rule_id, 'marketking_rule_vendors_who_multiple_options', true);
			$multiple_options_array = explode(',', $multiple_options);
			if (in_array('vendor_'.$vendor_id, $multiple_options_array)){
				array_push($rules_that_apply, $rule_id);
			} else {
				// try group
				if (in_array('group_'.$vendor_group_id, $multiple_options_array)){
					array_push($rules_that_apply, $rule_id);
				}
			}
		}

		$final_rules_array = array_merge($all_vendor_rules, $individual_rules, $group_rules, $rules_that_apply);
		$final_rules_array = array_filter(array_unique($final_rules_array));
		return $final_rules_array;
	}

	public static function get_earnings($vendor_id,$timeframe, $days = false, $months = false, $years = false, $admin_earnings = false, $from = false, $to = false, $reports = false){

		$earnings_number = 0;

		if ($admin_earnings === false){
			if ($timeframe === 'current_month'){
				$site_time = time()+(get_option('gmt_offset')*3600);
				$current_day = date_i18n( 'd', $site_time );

				$earnings_number = 0;
				$earnings = get_posts( array( 
					'post_type' => 'marketking_earning',
					'numberposts' => -1,
					'post_status'    => 'any',
					'date_query' => array(
							'after' => date('Y-m-d', strtotime('-'.$current_day.' days')) 
						),
					'fields'    => 'ids',
					'meta_key'   => 'vendor_id',
					'meta_value' => $vendor_id,
				));

			}

			if ($timeframe === 'last_days'){
				if ($days!== false){
					$earnings_number = 0;
					$earnings = get_posts( array( 
						'post_type' => 'marketking_earning',
						'numberposts' => -1,
						'post_status'    => 'any',
						'date_query' => array(
								'after' => date('Y-m-d', strtotime('-'.$days.' days')) 
							),
						'fields'    => 'ids',
						'meta_key'   => 'vendor_id',
						'meta_value' => $vendor_id,
					));

				}
			}

			if ($timeframe === 'by_month'){
				if ($months!== false && $years !== false){
					$earnings_number = 0;

					// get the total month earnings
					$earnings = get_posts( array( 
						'post_type' => 'marketking_earning',
						'numberposts' => -1,
						'post_status'    => 'any',
						'date_query' => array(
							'year'  => $years, // month year
							'month' => $months, // month number
						),
						'meta_key'   => 'vendor_id',
						'fields'	=> 'ids',
						'meta_value' => get_current_user_id(),
					));

				}
			}

			foreach ($earnings as $earning_id){
				$order_id = get_post_meta($earning_id,'order_id', true);
				$orderobj = wc_get_order($order_id);
				if ($orderobj !== false){
					$status = $orderobj->get_status();
					$earnings_total = get_post_meta($earning_id,'marketking_commission_total', true);
					// check if approved
					if (in_array($status,apply_filters('marketking_earning_completed_statuses', array('completed')))){
						$earnings_number+=$earnings_total;
					}
				}
			}
			return $earnings_number;

		} else if ($admin_earnings === true){
			// admin earnings
			if ($vendor_id === 'allvendors'){
				if ($timeframe === 'last_days'){

					$earnings_number = 0;
					$earnings = get_posts( array( 
						'post_type' => 'marketking_earning',
						'numberposts' => -1,
						'post_status'    => 'any',
						'date_query' => array(
								'after' => date('Y-m-d', strtotime('-'.$days.' days')) 
							),
						'fields'    => 'ids',
					));

				}
				if ($timeframe === 'fromto'){

					$earnings_number = 0;
					$earnings = get_posts( array( 
						'post_type' => 'marketking_earning',
						'numberposts' => -1,
						'post_status'    => 'any',
						'date_query' => array(
								'after' => $from, 
								'before' => $to 
							),
						'fields'    => 'ids',
					));

				}

			} else {
				// specific vendor
				if ($timeframe === 'fromto'){

					$earnings_number = 0;
					$earnings = get_posts( array( 
						'post_type' => 'marketking_earning',
						'numberposts' => -1,
						'post_status'    => 'any',
						'date_query' => array(
								'after' => $from, 
								'before' => $to 
							),
						'fields'    => 'ids',
						'meta_key'   => 'vendor_id',
						'meta_value' => $vendor_id,
					));

				}
			}

			if ($reports === true){
				// organize info by day, month, year to be able to display the charts
				$timestamps_commissions = array();
			}

			foreach ($earnings as $earning_id){
				$order_id = get_post_meta($earning_id,'order_id', true);
				$orderobj = wc_get_order($order_id);

				if ($orderobj !== false){
					$status = $orderobj->get_status();
					if ($status !== 'refunded' && $status !== 'cancelled'){

						$order_total = $orderobj->get_total();
						$vendor_earnings = get_post_meta($earning_id,'marketking_commission_total', true);
						
						$admin_earnings = $order_total-$vendor_earnings;
						$earnings_number+=$admin_earnings;


						if ($reports === true){
							$date = $orderobj->get_date_created()->getTimestamp()+(get_option('gmt_offset')*3600);
							if (!isset($timestamps_commissions[$date])){
								$timestamps_commissions[$date] = $admin_earnings;
							} else {
								$timestamps_commissions[$date] += $admin_earnings;
							}
						}
					}
					
				}
			}

			if ($reports === true){
				return $earnings_number.'***'.serialize($timestamps_commissions);
			}
			return $earnings_number;
		}



		// if something went wrong
		return 0;
	}

	public static function get_vendor_order_number($vendor_id){
		$vendor_orders = get_posts( array( 'post_type' => 'shop_order','post_status'=>'any','numberposts' => -1, 'author'   => $vendor_id, 'fields' =>'ids') );
		return count($vendor_orders);
	}

	public static function get_vendor_total_sales($vendor_id){
		$vendor_orders = get_posts( array( 'post_type' => 'shop_order','post_status'=>'any','numberposts' => -1, 'author'   => $vendor_id, 'fields' =>'ids') );
		$total = 0;
		foreach ($vendor_orders as $order){
			$orderobj = wc_get_order($order);
			$total += $orderobj->get_total();
		}
		return $total;
	}

	public static function create_earning($vendor_id, $order_id, $value){
		// Create transaction
		$earning = array(
			'post_title' => sanitize_text_field(esc_html__('Earning','marketking-multivendor-marketplace-for-woocommerce')),
			'post_status' => 'publish',
			'post_type' => 'marketking_earning',
			'post_author' => 1,
		);
		$earning_post_id = wp_insert_post($earning);
		$order = wc_get_order($order_id);

		// set meta
		update_post_meta($earning_post_id, 'time', time());
		update_post_meta($earning_post_id, 'order_id', $order_id);
		update_post_meta($earning_post_id, 'customer_id', $order->get_customer_id());
		update_post_meta($earning_post_id, 'order_status', $order->get_status());

		update_post_meta($earning_post_id, 'vendor_id', $vendor_id);

		update_post_meta($order_id, 'marketking_earning_id', $earning_post_id);
		update_post_meta($earning_post_id, 'marketking_commission_total', $value);

		return $earning_post_id;
	}


	public static function get_store_link($vendor_id){
		$store_url = get_user_meta($vendor_id,'marketking_store_url', true);
		if (empty($store_url)){
			$store_link = '';		
		} else {
			// get page
			$stores_page = get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' )));
			$store_link = $stores_page.$store_url;

			// check if vendor has its own base url
			$baseurl = get_user_meta($vendor_id,'marketking_vendor_store_url_base',true);
			if (!empty($baseurl)){
				if (intval($baseurl) === 1){
					// does have
					$store_link = rtrim(get_home_url(),'/').'/'.$store_url;
				}
			}
		}
		if (apply_filters('marketking_admin_store_link_standard_shop', false)){
			if (intval($vendor_id) === 1){
				$store_link = get_permalink( wc_get_page_id( 'shop' ) );
			}
		}
			
		return apply_filters('marketking_store_url_final', $store_link);
	}

	public static function get_not_visible_ids_cache(){

		$not_visible_ids = get_transient('marketking_not_visible_ajax_visibility');

		$not_visible_ids = apply_filters('marketking_ids_post_in_visibility', $not_visible_ids);

		return $not_visible_ids;
	}

	// 'all'for all users, OR a user ID for a specific user
	public static function recalculate_vendor_earnings($who){

		if ($who === 'all'){
			// get all agents
			$agents = get_users(array(
				'meta_key'     => 'marketking_group',
				'meta_value'   => 'none',
				'meta_compare' => '!=',
				'fields' => 'ids',
			));
		} else {
			// who is a user ID
			$agents = array(intval($who));
		}
		
		foreach ($agents as $agent){
			$earnings = get_posts( array( 
				'post_type' => 'marketking_earning',
				'numberposts' => -1,
				'post_status'    => 'any',
				'fields'    => 'ids',
				'meta_key'   => 'vendor_id',
				'meta_value' => $agent,
			));
			$total_agent_commissions = 0;
			foreach ($earnings as $earning_id){
				$order_id = get_post_meta($earning_id,'order_id', true);
				$orderobj = wc_get_order($order_id);
				if ($orderobj !== false){
					$earnings_total = get_post_meta($earning_id,'marketking_commission_total', true);
					if (!empty($earnings_total) && floatval($earnings_total) !== 0){
						$status = $orderobj->get_status();
						if (in_array($status,apply_filters('marketking_earning_completed_statuses', array('completed')))){
							$total_agent_commissions+=floatval($earnings_total);
						}
					}
				}
			}

			// also take into account all payments
			$user_payout_history = sanitize_text_field(get_user_meta($agent,'marketking_user_payout_history', true));

			if ($user_payout_history){
				$transactions = explode(';', $user_payout_history);
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
				
				// substract the amount paid from the commission
				$total_agent_commissions -= $amount;
			}


			// user balance history update
			$old_balance = get_user_meta($agent,'marketking_outstanding_earnings', true);
			$new_balance = $total_agent_commissions;
			$amount = 'RECALCULATION';
			$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 
			$note = 'RECALCULATION';
			$user_balance_history = sanitize_text_field(get_user_meta($agent,'marketking_user_balance_history', true));
			$new_entry = $date.':'.$amount.':'.$old_balance.':'.$new_balance.':'.$note;
			update_user_meta($agent,'marketking_user_balance_history', $user_balance_history.';'.$new_entry);
			update_user_meta($agent,'marketking_outstanding_earnings', $total_agent_commissions);
		}
	}

	// Updates the visibility cache for a specific item only
	// Should run when the product stock changes, product is updated, etc.
	public static function update_visibility_cache($product_id){

		/* WPML Integration
		We check if the current product that's being updated is a copy or the original
		If it's a copy, we get back to the original
		*/
		if (defined('WPML_PLUGIN_FILE')){
			$default_lang = apply_filters( 'wpml_default_language', '' );
			$product_id = apply_filters( 'wpml_object_id', $product_id, 'post', TRUE, $default_lang );
		}
		// WPML Integration END


		$new_cache = marketking()->get_not_visible_ids_cache();

		if (!$new_cache){
			$new_cache = array();
		}

		// First remove the product and all linked from the cache.
		$linkedproducts = marketking()->get_linkedproducts($product_id,'array');
		array_push($linkedproducts, $product_id); // add initial id to the list
		$new_cache = array_diff($new_cache, $linkedproducts);

		// Recalculate the product visibility
		// get priority settings
		$stockpriority = get_option('marketking_stock_priority_setting', 'instock');
		$vendorpriority = get_option('marketking_vendor_priority_setting', 'lowerprice');

		$not_visible_ids = array();

		if ($stockpriority === 'instock'){
			// split offers into 2 groups, instock and out of stock
			$instockoffers = marketking()->split_linkedproducts($linkedproducts, 'instock');
			$outofstockoffers = marketking()->split_linkedproducts($linkedproducts, 'outofstock');

			// merge groups again
			$instockoffers = marketking()->sort_linkedproducts($instockoffers, $vendorpriority);
			$outofstockoffers = marketking()->sort_linkedproducts($outofstockoffers, $vendorpriority);
			$linkedproducts = array_merge($instockoffers, $outofstockoffers);
		} else if ($stockpriority === 'none'){
			$linkedproducts = marketking()->sort_linkedproducts($linkedproducts, $vendorpriority);
		}

		// remove winner = first item
		array_shift($linkedproducts);
		$not_visible_ids = $linkedproducts;

		// Update the cache
		$newcache = array_merge($new_cache, $not_visible_ids);

		$newcache = marketking()->add_all_wpml_products($newcache);

		set_transient('marketking_not_visible_ajax_visibility', $newcache);


	}

	// Completely rebuilds the visibility cache from scratch. 
	// Only targets products that have linkedproducts, therefore should not take too much performance
	public static function rebuild_visibility_cache(){
		// Get all products with linkedproducts (should be fairly low number at least at first), and sort invisible ones into the cache
		$products = get_posts([
			'post_type' => 'product',
			'post_status' => 'publish',
			'numberposts' => -1,
			'meta_key' => 'marketking_product_linkedproducts',
			'fields' => 'ids',
			'suppress_filters' => true // 
		]);

		// get priority settings
		$stockpriority = get_option('marketking_stock_priority_setting', 'instock');
		$vendorpriority = get_option('marketking_vendor_priority_setting', 'lowerprice');

		$not_visible_ids = array();

		// handle product pairs until all are solved
		while (!empty($products)){
			// get product group and remove them from this array
			$product_id = reset($products);
			$linkedproducts = marketking()->get_linkedproducts($product_id,'array');
			array_push($linkedproducts, $product_id); // add initial id to the list

			// remove these ids from the main group to signify they have been handled
			$products = array_diff($products, $linkedproducts);

			// find priority winner, and remove him from list, then move list into not visible array
			if ($stockpriority === 'instock'){
				// split offers into 2 groups, instock and out of stock
				$instockoffers = marketking()->split_linkedproducts($linkedproducts, 'instock');
				$outofstockoffers = marketking()->split_linkedproducts($linkedproducts, 'outofstock');

				// merge groups again
				$instockoffers = marketking()->sort_linkedproducts($instockoffers, $vendorpriority);
				$outofstockoffers = marketking()->sort_linkedproducts($outofstockoffers, $vendorpriority);
				$linkedproducts = array_merge($instockoffers, $outofstockoffers);
			} else if ($stockpriority === 'none'){
				$linkedproducts = marketking()->sort_linkedproducts($linkedproducts, $vendorpriority);
			}

			// remove winner = first item
			array_shift($linkedproducts);

			// add all other items into not visible list	
			$not_visible_ids = array_merge($not_visible_ids, $linkedproducts);		
		}

		// apply wpml
		$not_visible_ids = marketking()->add_all_wpml_products($not_visible_ids);

		// update cache
		set_transient('marketking_not_visible_ajax_visibility', $not_visible_ids);

	}

	// Receives an array of product ids, and returns the same array + all wpml translations
	public static function add_all_wpml_products($product_ids){

		if (defined('WPML_PLUGIN_FILE')){

			// 0. Get all WPML languages
			$languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );
			
			if ( !empty( $languages ) ) {

				foreach( $languages as $lang ) {
					foreach ($product_ids as $id){
						$translation = apply_filters( 'wpml_object_id', $id, 'post', FALSE, $lang['language_code'] );
						array_push($product_ids, $translation);
					}
				}
			}

			$product_ids = array_filter(array_unique($product_ids));
		}

		return $product_ids;

	}

	// sorts array of linked products by lower price, higher price, top rated vendor
	public static function sort_linkedproducts($linkedproducts, $sortby){
		$newarray = array();
		$newarray2 = array();
		$newarray3 = array();

		if ($sortby === 'lowerprice' || $sortby === 'higherprice'){
			foreach ($linkedproducts as $offerproduct_id){
				$offerproduct = wc_get_product($offerproduct_id);
				$price = $offerproduct->get_price();
				$newarray[$offerproduct_id] = $price;
			}
			asort($newarray, SORT_NUMERIC);

			foreach ($newarray as $offerproduct_id => $price){
				array_push($newarray2, $offerproduct_id);
			}

			$linkedproducts = $newarray2;

			if ($sortby === 'higherprice'){
				$linkedproducts = array_reverse($linkedproducts);
			}
		}		

		if ($sortby === 'higherrated'){
			$productratingarray = array();
			foreach ($linkedproducts as $linkedproduct_id){
				$vendor_id = marketking()->get_product_vendor($linkedproduct_id);
				$rating = marketking()->get_vendor_rating($vendor_id);
				// if there's any rating
				if (intval($rating['count'])!==0){
					$productratingarray[$linkedproduct_id] = floatval($rating['rating']);
				} else {
					$productratingarray[$linkedproduct_id] = 0;
				}
			}

			asort($productratingarray, SORT_NUMERIC);

			foreach ($productratingarray as $offerproduct_id => $rating){
				array_push($newarray3, $offerproduct_id);
			}

			$linkedproducts = $newarray3;
			$linkedproducts = array_reverse($linkedproducts);
		}
		return $linkedproducts;
	}

	// splits array of linked products by instock/outofstock
	// returns only in stock or out of stock items
	public static function split_linkedproducts($linkedproducts, $sortby){

		$newarray = array();

		if ($sortby === 'instock'){
			foreach ($linkedproducts as $offerproduct_id){
				$offerproduct = wc_get_product($offerproduct_id);
				if ($offerproduct){
					$stock_status = $offerproduct->get_stock_status();
					if ($stock_status === 'instock'){
						array_push($newarray, $offerproduct_id);
					}
				}
				
			}
			$linkedproducts = $newarray;
		}

		if ($sortby === 'outofstock'){
			foreach ($linkedproducts as $offerproduct_id){
				$offerproduct = wc_get_product($offerproduct_id);
				if ($offerproduct){
					$stock_status = $offerproduct->get_stock_status();
					if ($stock_status !== 'instock'){
						array_push($newarray, $offerproduct_id);
					}
				}
			}
			$linkedproducts = $newarray;
		}

		return $linkedproducts;
	}

	// returns array of linkedproducts ids
	public static function get_linkedproducts($product_id, $type='array'){

		if (defined('WPML_PLUGIN_FILE')){
			$default_lang = apply_filters( 'wpml_default_language', '' );
			$product_id = apply_filters( 'wpml_object_id', $product_id, 'post', TRUE, $default_lang );
		}

		$linkedproducts = get_post_meta($product_id,'marketking_product_linkedproducts', true);

		if ($type === 'array'){
			$linkedproducts = array_filter(array_unique(explode(',', $linkedproducts)));
			// remove products that no longer exist
			foreach ($linkedproducts as $key=> $linkedproduct){
				$product = wc_get_product($linkedproduct);
				if(!$product){
					// remove
					unset($linkedproducts[$key]);
				} else {
					// also remove if trash
					if ($product->get_status() === 'trash'){
						unset($linkedproducts[$key]);
					}
				}
			}

			if (empty($linkedproducts)){
				return array();
			}
		} else if ($type === 'string'){
			// do nothing
			if(empty($linkedproducts)){
				return '';
			}
		}

		return $linkedproducts;
	}


	public static function set_new_linkedproduct($product_id, $newproduct_id){

		/* WPML Integration
		We check if the current product that's being updated is a copy or the original
		If it's a copy, we get back to the original
		*/
		if (defined('WPML_PLUGIN_FILE')){
			$default_lang = apply_filters( 'wpml_default_language', '' );
			$product_id = apply_filters( 'wpml_object_id', $product_id, 'post', TRUE, $default_lang );
			$newproduct_id = apply_filters( 'wpml_object_id', $newproduct_id, 'post', TRUE, $default_lang );
		}
		// WPML Integration END

		// 1. For the new product, add a list of all other products
		$linkedproducts = marketking()->get_linkedproducts($product_id,'string');
		$linkedproductsarr = marketking()->get_linkedproducts($product_id, 'array');

		$linkedproductsnew = $linkedproducts.','.$product_id; // include main product
		
		update_post_meta($newproduct_id, 'marketking_product_linkedproducts', $linkedproductsnew);


		// 2. For the main product , add the new product to the list
		$linkedproductsnew = $linkedproducts.','.$newproduct_id; // include new product
		update_post_meta($product_id, 'marketking_product_linkedproducts', $linkedproductsnew);


		// 3. For all other linked products, add the new product to the list
		foreach ($linkedproductsarr as $linkedproduct_id){
			// need to add new product
			$linkedlist = marketking()->get_linkedproducts($linkedproduct_id,'string');
			$linkedlistnew = $linkedlist.','.$newproduct_id;
			update_post_meta($linkedproduct_id,'marketking_product_linkedproducts', $linkedlistnew);
		}

	}

	// checks if the vendor has already added that product to their store 
	public static function vendor_has_linkedproduct($vendor_id, $product_id){

		/* WPML Integration
		We check if the current product that's being updated is a copy or the original
		If it's a copy, we get back to the original
		*/
		if (defined('WPML_PLUGIN_FILE')){
			$default_lang = apply_filters( 'wpml_default_language', '' );
			$product_id = apply_filters( 'wpml_object_id', $product_id, 'post', TRUE, $default_lang );
		}
		// WPML Integration END
		
		// get list of linked products
		$linkedproducts_array = marketking()->get_linkedproducts($product_id,'array');

		// build list of linked product vendors
		$vendors = array();
		foreach ($linkedproducts_array as $linkedproduct_id){
			// get vendor
			$vendorid = marketking()->get_product_vendor($linkedproduct_id);
			array_push($vendors, $vendorid);
		}
		$vendors = array_unique(array_filter($vendors));

		// check if vendor is in list
		if (in_array($vendor_id, $vendors)){
			return true;
		} else {
			return false;
		}

	}

	public static function vendor_can_change_order_status($vendor_id){

		// check if it's enabled at the user level, or globally
		$global = get_option( 'marketking_vendor_status_direct_setting', 1 );
		if (intval($global) === 1){
			return true;
		} else {
			// check group level
			$groupid = get_user_meta($vendor_id,'marketking_group', true);
			if (!empty($groupid)){
				$group = intval(get_post_meta($groupid, 'marketking_group_vendor_status_direct_setting', true));
				if ($group === 1){
					return true;
				} else {
					// check individual setting
					$individual = intval(get_user_meta($vendor_id,'marketking_vendor_change_status',true));
					if ($individual === 1){
						return true;
					}
				}
			}
		}

		return false;
	}

	
	public static function get_order_earnings($order_id, $admin = false){

		$earnings_number = 0;
		$order_total = 0;
		$earnings = get_posts( array( 
			'post_type' => 'marketking_earning',
			'numberposts' => -1,
			'post_status'    => 'any',
			'fields'    => 'ids',
			'meta_key'   => 'order_id',
			'meta_value' => $order_id,
		));

		foreach ($earnings as $earning_id){
			$orderobj = wc_get_order($order_id);
			if ($orderobj !== false){
				$status = $orderobj->get_status();
				$earnings_total = get_post_meta($earning_id,'marketking_commission_total', true);

				$earnings_number+=$earnings_total;
				$order_total = $orderobj->get_total();
			}
		}

		$admin_earnings = $order_total-$earnings_number;

		if ($admin === false ){
			return $earnings_number;
		} else {
			return $admin_earnings;
		}
	}

	public static function vendor_can_publish_products($vendor_id){

		// check if it's enabled at the user level, or globally
		$global = get_option( 'marketking_vendor_publish_direct_setting', 0 );
		if (intval($global) === 1){
			return true;
		} else {
			// check group level
			$groupid = get_user_meta($vendor_id,'marketking_group', true);
			if (!empty($groupid)){
				$group = intval(get_post_meta($groupid, 'marketking_group_vendor_publish_direct_setting', true));
				if ($group === 1){
					return true;
				} else {
					// check individual setting
					$individual = intval(get_user_meta($vendor_id,'marketking_vendor_publish_products',true));
					if ($individual === 1){
						return true;
					}
				}
			}
		}

		return false;
	}

	public static function vendor_all_products_virtual($vendor_id){
		$groupid = get_user_meta($vendor_id,'marketking_group', true);

		$all_virtual = get_post_meta($groupid, 'marketking_group_vendors_all_virtual_setting', true );
		if (intval($all_virtual) === 1){
			return true;
		}

		return false;
	}

	public static function vendor_all_products_downloadable($vendor_id){
		$groupid = get_user_meta($vendor_id,'marketking_group', true);

		$all_down = get_post_meta($groupid, 'marketking_group_vendors_all_downloadable_setting', true );
		if (intval($all_down) === 1){
			return true;
		}

		return false;
	}

	public static function vendor_can_product_type($vendor_id, $type){

		// check vendor group
		$groupid = get_user_meta($vendor_id,'marketking_group', true);
		if (!empty($groupid)){
			$selected_options_string = get_post_meta($groupid, 'marketking_group_allowed_products_type_settings', true);
			if (!empty($selected_options_string)){
				$selected_options = explode(',', $selected_options_string);			
				if (!in_array($type, $selected_options)){
					return false;
				}
			}
		}

		return true;
	}

	public static function vendor_can_product_category($vendor_id, $category){

		// check vendor group
		$groupid = get_user_meta($vendor_id,'marketking_group', true);
		if (!empty($groupid)){
			$selected_options_string = get_post_meta($groupid, 'marketking_group_allowed_categories_settings', true);
			if (!empty($selected_options_string)){
				$selected_options = explode(',', $selected_options_string);			
				if (!in_array($category, $selected_options) && !in_array('category_'.$category, $selected_options)){
					return false;
				}
			}
		}

		return true;
	}

	public static function vendor_can_product_tag($vendor_id, $category){

		// check vendor group
		$groupid = get_user_meta($vendor_id,'marketking_group', true);
		if (!empty($groupid)){
			$selected_options_string = get_post_meta($groupid, 'marketking_group_allowed_tags_settings', true);
			if (!empty($selected_options_string)){
				$selected_options = explode(',', $selected_options_string);			
				if (!in_array($category, $selected_options) && !in_array('category_'.$category, $selected_options)){
					return false;
				}
			}
		}

		return true;
	}

	public static function get_excluded_category_ids($vendor_id){
		$excluded_ids = array();
		$categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
		foreach ($categories as $category){
			if (!marketking()->vendor_can_product_category($vendor_id, $category->term_id)){
				array_push($excluded_ids, $category->term_id);
			}
		}
		return $excluded_ids;
	}

	public static function get_excluded_tag_ids($vendor_id){
		$excluded_ids = array();
		$categories = get_terms( array( 'taxonomy' => 'product_tag', 'hide_empty' => false ) );
		foreach ($categories as $category){
			if (!marketking()->vendor_can_product_tag($vendor_id, $category->term_id)){
				array_push($excluded_ids, $category->term_id);
			}
		}
		return $excluded_ids;
	}


	public static function vendor_can_taxable($vendor_id){
		$groupid = get_user_meta($vendor_id,'marketking_group', true);

		$non_taxable = get_post_meta($groupid, 'marketking_group_vendors_non_taxable_setting', true );
		if (intval($non_taxable) === 1){
			return false;
		}

		return true;
	}

	public static function vendor_can_new_attributes($vendor_id){
		$groupid = get_user_meta($vendor_id,'marketking_group', true);

		// enabled by default
		if (!metadata_exists('post', $groupid, 'marketking_group_vendors_new_attributes_setting')){
			return true;
		}

		$new_attributes = get_post_meta($groupid, 'marketking_group_vendors_new_attributes_setting', true );
		if (empty($new_attributes)){
			return false;
		}

		return true;
	}

	public static function vendor_can_multiple_categories($vendor_id){
		$groupid = get_user_meta($vendor_id,'marketking_group', true);

		// enabled by default
		if (!metadata_exists('post', $groupid, 'marketking_group_vendors_multiple_categories_setting')){
			return true;
		}

		$multiple_cat = get_post_meta($groupid, 'marketking_group_vendors_multiple_categories_setting', true );
		if (empty($multiple_cat)){
			return false;
		}

		return true;
	}

	public static function vendor_can_backorders($vendor_id){

		if (marketking()->is_vendor_team_member()){
			$vendor_id = marketking()->get_team_member_parent();
		}

		$groupid = get_user_meta($vendor_id,'marketking_group', true);

		// enabled by default
		if (!metadata_exists('post', $groupid, 'marketking_group_vendors_allow_backorders_setting')){
			return true;
		}

		$multiple_cat = get_post_meta($groupid, 'marketking_group_vendors_allow_backorders_setting', true );
		if (empty($multiple_cat)){
			return false;
		}

		return true;
	}

	public static function vendor_can_multiple_store_categories($vendor_id){

		$multiple = get_option( 'marketking_store_categories_singlemultiple_setting', 'single' );

		if ($multiple === 'single'){
			return false;
		}

		return true;
	}


	public static function vendor_can_linked_products($vendor_id){

		// check if it's enabled at the user level, or globally
		$global = get_option( 'marketking_vendors_can_linked_products_setting', 1 );
		if (intval($global) === 1){
			return true;
		} else {
			// check group level
			$groupid = get_user_meta($vendor_id,'marketking_group', true);
			if (!empty($groupid)){
				$group = intval(get_post_meta($groupid, 'marketking_group_vendors_can_linked_products_setting', true));
				if ($group === 1){
					return true;
				}
			}
		}

		return false;
	}

	public static function get_vendor_id_by_url($store_url){
		$users = get_users(array(
			'meta_key'     => 'marketking_store_url',
			'meta_value'   => $store_url,
			'meta_compare' => '=',
		));

		if (empty($users)){
			return 0;
		} else {
			return $users[0]->ID;
		}
	}

	public static function get_vendor_id_in_store_url(){
		$store_url = get_query_var('vendorid');
		$users = get_users(array(
			'meta_key'     => 'marketking_store_url',
			'meta_value'   => $store_url,
			'meta_compare' => '=',
		));

		if (!empty($users)){
			$vendor_id = $users[0]->ID;
		} else {
			$vendor_id = 0;
		}

		return $vendor_id;
	}

	public static function get_store_content_by_url($store_url){

		$users = get_users(array(
			'meta_key'     => 'marketking_store_url',
			'meta_value'   => $store_url,
			'meta_compare' => '=',
		));

		if (empty($users)){
			$content = esc_html__('There is no store here...','marketking-multivendor-marketplace-for-woocommerce');
		} else {
			$vendor_id = $users[0]->ID;		


			if (marketking()->vendor_is_inactive($vendor_id)){
                $content = esc_html__('There is no store here...','marketking-multivendor-marketplace-for-woocommerce');
            } else {
            	ob_start();

            	// Elementor
            	$have_elementor = 'no';
            	if (intval(get_option('marketking_enable_elementor_setting', 1)) === 1){
            		$store_style = intval(get_option( 'marketking_store_style_setting', 1 ));

            		if ($store_style === 4){
            			$the_query = new WP_Query( 'page_id='.get_option( 'marketking_elementor_page_setting', 'disabled' ) );
            			while ( $the_query->have_posts() ) {	

            				// Set products content transient
            				$content = do_shortcode('[products limit="'.apply_filters('marketking_default_products_number',12).'" paginate="true" visibility="visible"]');
            				set_transient('marketking_display_products_shortcode_elementor_content'.get_current_user_id(), $content);					

            				$the_query->the_post();
            				the_content();
            				$have_elementor = 'yes';
            			}
            			wp_reset_postdata();
            		}
            	}            	
            	
            	if ($have_elementor === 'no'){
            		include(apply_filters('marketking_template', MARKETKINGCORE_DIR . 'public/templates/store-page.php'));	
            	}

            	$content = ob_get_clean();
            	$content = apply_filters('marketking_store_page_content', $content, $vendor_id);
            }

			

		}

		return $content;
	}

	// active tab
	public static function marketking_tab_active($tab){
		$pagetab = marketking()->get_pagenr_query_var();
		if (empty($pagetab)){
			// products is default
			$pagetab = 'products';
		}

		if ($tab === $pagetab){
			return 'marketking_tab_active';
		}
	}

	public static function get_pagenr_query_var(){
		$value = get_query_var('pagenr', 1);
		$dashpage = get_query_var('dashpage');
		
		if ($dashpage === 'edit-product'){
			if ($value === 'add'){
				global $marketking_product_add_id;

				if (empty($marketking_product_add_id) || $marketking_product_add_id === 'add'){
					// if empty, create new product and assign it
					$productid = marketking()->get_product_standby();
					
					$marketking_product_add_id = $productid;
				}


				$value = $marketking_product_add_id;			
			}
		}
		
		return $value;
	}

	public static function clear_product_standby($productid){

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		$product_id = get_option('marketking_product_standby_'.$current_id, 'none');
		if ($product_id == $productid){
			delete_option('marketking_product_standby_'.$current_id);
			update_post_meta($productid,'marketking_is_product_standby', 'no');

		}
	}

	public static function is_pack_product($productid){

		$ispackproduct = 'no';

		$packs = get_posts([
		  'post_type' => 'marketking_mpack',
		  'post_status' => 'publish',
		  'numberposts' => -1,
		  'meta_key' => 'marketking_pack_sort_order',
		  'orderby' => 'meta_value_num',
		  'order' => 'ASC',
		]);

		// for each pack, check if it's visible to the current vendor group, otherwise, remove it
		foreach ($packs as $index => $pack){

			$packprodid = get_post_meta($pack->ID,'marketking_pack_product', true);
			if ($productid == $packprodid){
				$ispackproduct = 'yes';
				break;
			}
		}

		if ($ispackproduct === 'yes'){
			return true;
		}

		return false;
	}

	public static function set_product_standby(){

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		if (marketking()->get_product_standby() === 'none'){
			// create product
			$product = new WC_Product_Simple();
			$product->set_name( 'Product Name' ); // product title
			$product->set_short_description( '' );
			$product->set_status('hidden');
			$product->save();
			$productid = $product->get_id();
			update_post_meta($productid,'marketking_is_product_standby', 'yes');

			update_option('marketking_product_standby_'.$current_id, $productid);
		}
		
	}

	public static function get_product_standby(){

		$current_id = get_current_user_id();
		if (marketking()->is_vendor_team_member()){
			$current_id = marketking()->get_team_member_parent();
		}

		$product_id = get_option('marketking_product_standby_'.$current_id, 'none');

		// check that product is not false 
		$obj = wc_get_product($product_id);
		if (!$obj){
			// delete it
			delete_option('marketking_product_standby_'.$current_id);
			return 'none';
		}

		return $product_id;	
	}

	
	public static function is_vendor_dashboard(){

		global $post;
		if (isset($post->ID)){
			if ( intval($post->ID) === intval(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true) ) ){
				return true;
			}
		}
		return false;
	}


	public static function is_vendor_store_page(){
		global $post;

		if (isset($post->ID)){
			if ( intval($post->ID) === intval(apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' ), 'post' , true) ) ){

				// get the author if any
				$store_url = get_query_var('vendorid');
				if (!empty($store_url)){
					return true;
				}
			}
		}

		return false;
	}

	public static function generate_unique_url(){
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$unique_url = '';
		for ($i = 0; $i < 10; $i++){
			$unique_url .= $characters[mt_rand(0, 35)];
			$unique_url = strtoupper($unique_url);
			$field_value = $unique_url;
		}
		return $unique_url;
	}

	public static function store_url_exists($store_url){
		$users = get_users(array(
			'meta_key'     => 'marketking_store_url',
			'meta_value'   => $store_url,
			'meta_compare' => '=',
		));

		if (empty($users)){
			return false;
		} else {
			return true;
		}
	}

	public static function get_display_name($vendor_id){
		$udata = get_userdata($vendor_id);
		$display_name = $udata->display_name;
		return $display_name;
	}

	public static function get_product_vendor($product_id){
		$author = get_post_field( 'post_author', $product_id );
		return $author;
	}

	public static function init() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function logdata( $message, $level = 'debug', $source = 'marketking' ) {
		$logger  = wc_get_logger();
		$context = array( 'source' => $source );

		return $logger->log( $level, $message, $context );
	}

}