<?php

class Marketkingcore_Activator {

	public static function activate() {

		// clear marketking transients
		global $wpdb;
		$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%transient_marketking%'" );
		foreach( $plugin_options as $option ) {
		    delete_option( $option->option_name );
		}
		wp_cache_flush();

		// prevent option update issues due to caching
		wp_cache_delete ( 'alloptions', 'options' );

		
		// Flush rewrite rules
		if (apply_filters('marketking_flush_permalinks', true)){
			// Flush rewrite rules
			flush_rewrite_rules();
		}

		// Set admin notice state to enabled ('activate woocommerce' notice)
		update_user_meta(get_current_user_id(), 'marketking_dismiss_activate_woocommerce_notice', 0);


		// create default group if it doesn't exist:
		// create vendors group
		$groups = get_posts([
		  'post_type' => 'marketking_group',
		  'post_status' => 'publish',
		  'numberposts' => -1,
		  'fields' => 'ids',
		]);

		if (intval(count($groups)) === 0){
			// there are no groups, let's create a vendor group
			$groupp = array(
				'post_title'  => sanitize_text_field( esc_html__( 'Vendors', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
				'post_status' => 'publish',
				'post_type'   => 'marketking_group',
				'post_author' => 1,
			);
			$group_id = wp_insert_post( $groupp );
		}
		


		// check if first activation
		$first_activation = get_option('marketking_first_time_setup', 'yes');
		if ($first_activation === 'yes'){
			// create sales vendors registration page with shortcode
			$post_details = array(
			'post_title'    => esc_html__('Become a Vendor', 'marketking-multivendor-marketplace-for-woocommerce'),
			'post_content'  => '[marketking_vendor_registration]',
			'post_status'   => 'draft',
			'post_author'   => 1,
			'post_type' => 'page'
			 );
			$id = wp_insert_post( $post_details );
			
			update_option('marketking_first_time_setup', 'no');
			update_option('marketking_vendor_registration_page_setting', $id);
			update_option('marketking_vendor_registration_page_setting_initial', $id);

			$post_details = array(
			'post_title'    => esc_html__('Stores List', 'marketking-multivendor-marketplace-for-woocommerce'),
			'post_content'  => '',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type' => 'page'
			 );
			$id = wp_insert_post( $post_details );
			
			update_option('marketking_stores_page_setting', $id);
			update_option('marketking_stores_page_setting_initial', $id);

			// create vendor dashboard page 
			$post_details = array(
			'post_title'    => esc_html__('Vendor Dashboard', 'marketking-multivendor-marketplace-for-woocommerce'),
			'post_content'  => '',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type' => 'page'
			 );
			$id = wp_insert_post( $post_details );
			update_option('marketking_vendordash_page_setting', $id);


			// Create default verification items: proof of identity, proof of address
			$post_details = array(
			'post_title'    => esc_html__('Proof of Identity', 'marketking-multivendor-marketplace-for-woocommerce'),
			'post_content'  => '',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type' => 'marketking_vitem'
			 );
			$id = wp_insert_post( $post_details );

			update_post_meta($id,'marketking_vitem_description_textarea','Please upload an ID card, passport, or driving license.');

			$post_details = array(
			'post_title'    => esc_html__('Proof of Address', 'marketking-multivendor-marketplace-for-woocommerce'),
			'post_content'  => '',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type' => 'marketking_vitem'
			 );
			$id = wp_insert_post( $post_details );

			update_post_meta($id,'marketking_vitem_description_textarea','Accepted documents are: bank statements, utility bills, company registration files, etc.');

			// create 3 vendor membership packages with sample data
			$post_details = array(
			'post_title'    => esc_html__('Starter', 'marketking-multivendor-marketplace-for-woocommerce'),
			'post_content'  => '',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type' => 'marketking_mpack'
			 );
			$id = wp_insert_post( $post_details );

			update_post_meta($id,'marketking_pack_price', '$89');
			update_post_meta($id,'marketking_pack_price_description', '2 Sites, One-Time Purchase');
			update_post_meta($id,'marketking_pack_description', 'Excellent choice for professionals and businesses in every field');
			update_post_meta($id,'marketking_pack_image', 'https://woocommerce-multivendor.com/wp-content/uploads/2022/03/plan-s1.svg');
			update_post_meta($id,'marketking_pack_sort_order', 1);

			$post_details = array(
			'post_title'    => esc_html__('Professional', 'marketking-multivendor-marketplace-for-woocommerce'),
			'post_content'  => '',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type' => 'marketking_mpack'
			 );
			$id = wp_insert_post( $post_details );

			update_post_meta($id,'marketking_mpack_featured_pack_setting', 1);
			update_post_meta($id,'marketking_pack_price', '$99 /yr');
			update_post_meta($id,'marketking_pack_price_description', '5 Sites, Annual Billing');
			update_post_meta($id,'marketking_pack_description', 'Excellent choice for professionals and businesses in every field');
			update_post_meta($id,'marketking_pack_image', 'https://woocommerce-multivendor.com/wp-content/uploads/2022/03/plan-s2.svg');
			update_post_meta($id,'marketking_pack_sort_order', 2);



			$post_details = array(
			'post_title'    => esc_html__('Business', 'marketking-multivendor-marketplace-for-woocommerce'),
			'post_content'  => '',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type' => 'marketking_mpack'
			 );
			$id = wp_insert_post( $post_details );

			update_post_meta($id,'marketking_pack_price', '$199 /yr');
			update_post_meta($id,'marketking_pack_price_description', '10 Sites, Annual Billing');
			update_post_meta($id,'marketking_pack_description', 'Excellent choice for professionals and businesses in every field');
			update_post_meta($id,'marketking_pack_image', 'https://woocommerce-multivendor.com/wp-content/uploads/2022/03/plan-s3.svg');
			update_post_meta($id,'marketking_pack_sort_order', 3);



		}
		

		// Create 2 default registration options if they don't exist: Customer and Vendor
		$customer_option_id = intval(get_option('marketking_customer_option_id_setting', 0));
		// if option does not exist, = 0
		if (intval($customer_option_id === 0)){
			$option = array(
			    'post_title' => sanitize_text_field(esc_html__('Customer','marketking-multivendor-marketplace-for-woocommerce')),
			    'post_status' => 'publish',
			    'post_type' => 'marketking_option',
			    'post_author' => 1,
			);
			$option_id = wp_insert_post($option);
			// set option status as enabled
			update_post_meta($option_id, 'marketking_option_status', 1);
			// set option approval as automatic
			update_post_meta( $option_id, 'marketking_option_approval', 'automatic');
			update_post_meta( $option_id, 'marketking_option_sort_number', 1);

			// set option 
			update_option( 'marketking_customer_option_id_setting', intval($option_id) );
		}

		$vendor_option_id = intval(get_option('marketking_vendor_option_id_setting', 0));
		// if option does not exist, = 0
		if (intval($vendor_option_id === 0)){
			$option = array(
			    'post_title' => sanitize_text_field(esc_html__('Vendor','marketking-multivendor-marketplace-for-woocommerce')),
			    'post_status' => 'publish',
			    'post_type' => 'marketking_option',
			    'post_author' => 1,
			);
			$option_id = wp_insert_post($option);
			// set option status as enabled
			update_post_meta($option_id, 'marketking_option_status', 1);
			// set option approval as manual
			update_post_meta($option_id, 'marketking_option_approval', 'manual');
			update_post_meta( $option_id, 'marketking_option_sort_number', 2);

			// set option 
			update_option( 'marketking_vendor_option_id_setting', intval($option_id) );
		}


		// Create default Vendor fields
		$first_name_initial_field_id = intval(get_option('marketking_first_name_initial_field_id_setting', 0));
		// if field does not exist, = 0
		if (intval($first_name_initial_field_id === 0)){
			$field = array(
			    'post_title' => sanitize_text_field(esc_html__('First Name','marketking-multivendor-marketplace-for-woocommerce')),
			    'post_status' => 'publish',
			    'post_type' => 'marketking_field',
			    'post_author' => 1,
			);
			$field_id = wp_insert_post($field);

			$vendor_option_id = intval(get_option('marketking_vendor_option_id_setting', 0));

			// set field meta
			update_post_meta($field_id, 'marketking_field_status', 1);
			update_post_meta($field_id, 'marketking_field_required', 1);
			update_post_meta($field_id, 'marketking_field_sort_number', 1);
			update_post_meta($field_id, 'marketking_field_registration_option', 'option_'.$vendor_option_id);
			update_post_meta($field_id, 'marketking_field_editable', 1);
			update_post_meta($field_id, 'marketking_field_field_type', 'text');
			update_post_meta($field_id, 'marketking_field_field_label', sanitize_text_field(esc_html__('First Name','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_field_placeholder', sanitize_text_field(esc_html__('Enter your first name here...','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_billing_connection', 'billing_first_name');

			// set option 
			update_option( 'marketking_first_name_initial_field_id_setting', intval($field_id) );
		}

		$last_name_initial_field_id = intval(get_option('marketking_last_name_initial_field_id_setting', 0));
		// if field does not exist, = 0
		if (intval($last_name_initial_field_id === 0)){

			$field = array(
			    'post_title' => sanitize_text_field(esc_html__('Last Name','marketking-multivendor-marketplace-for-woocommerce')),
			    'post_status' => 'publish',
			    'post_type' => 'marketking_field',
			    'post_author' => 1,
			);
			$field_id = wp_insert_post($field);

			$vendor_option_id = intval(get_option('marketking_vendor_option_id_setting', 0));

			// set field meta
			update_post_meta($field_id, 'marketking_field_status', 1);
			update_post_meta($field_id, 'marketking_field_required', 1);
			update_post_meta($field_id, 'marketking_field_sort_number', 2);
			update_post_meta($field_id, 'marketking_field_registration_option', 'option_'.$vendor_option_id);
			update_post_meta($field_id, 'marketking_field_editable', 1);
			update_post_meta($field_id, 'marketking_field_field_type', 'text');
			update_post_meta($field_id, 'marketking_field_field_label', sanitize_text_field(esc_html__('Last Name','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_field_placeholder', sanitize_text_field(esc_html__('Enter your last name here...','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_billing_connection', 'billing_last_name');

			// set option 
			update_option( 'marketking_last_name_initial_field_id_setting', intval($field_id) );
		}


		$company_name_initial_field_id = intval(get_option('marketking_company_name_initial_field_id_setting', 0));
		// if field does not exist, = 0
		if (intval($company_name_initial_field_id === 0)){

			$field = array(
			    'post_title' => sanitize_text_field(esc_html__('Company Name','marketking-multivendor-marketplace-for-woocommerce')),
			    'post_status' => 'publish',
			    'post_type' => 'marketking_field',
			    'post_author' => 1,
			);
			$field_id = wp_insert_post($field);

			$vendor_option_id = intval(get_option('marketking_vendor_option_id_setting', 0));

			// set field meta
			update_post_meta($field_id, 'marketking_field_status', 1);
			update_post_meta($field_id, 'marketking_field_required', 1);
			update_post_meta($field_id, 'marketking_field_sort_number', 3);
			update_post_meta($field_id, 'marketking_field_registration_option', 'option_'.$vendor_option_id);
			update_post_meta($field_id, 'marketking_field_editable', 1);
			update_post_meta($field_id, 'marketking_field_field_type', 'text');
			update_post_meta($field_id, 'marketking_field_field_label', sanitize_text_field(esc_html__('Company Name','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_field_placeholder', sanitize_text_field(esc_html__('Enter your company name here...','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_billing_connection', 'billing_company');

			// set option 
			update_option( 'marketking_company_name_initial_field_id_setting', intval($field_id) );
		}


		$phone_initial_field_id = intval(get_option('marketking_phone_initial_field_id_setting', 0));
		// if field does not exist, = 0
		if (intval($phone_initial_field_id === 0)){

			$field = array(
			    'post_title' => sanitize_text_field(esc_html__('Phone Number','marketking-multivendor-marketplace-for-woocommerce')),
			    'post_status' => 'publish',
			    'post_type' => 'marketking_field',
			    'post_author' => 1,
			);
			$field_id = wp_insert_post($field);

			$vendor_option_id = intval(get_option('marketking_vendor_option_id_setting', 0));

			// set field meta
			update_post_meta($field_id, 'marketking_field_status', 1);
			update_post_meta($field_id, 'marketking_field_required', 1);
			update_post_meta($field_id, 'marketking_field_sort_number', 4);
			update_post_meta($field_id, 'marketking_field_registration_option', 'option_'.$vendor_option_id);
			update_post_meta($field_id, 'marketking_field_editable', 1);
			update_post_meta($field_id, 'marketking_field_field_type', 'tel');
			update_post_meta($field_id, 'marketking_field_field_label', sanitize_text_field(esc_html__('Phone Number','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_field_placeholder', sanitize_text_field(esc_html__('Enter your phone here...','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_billing_connection', 'billing_phone');

			// set option 
			update_option( 'marketking_phone_initial_field_id_setting', intval($field_id) );
		}

		$store_name_initial_field_id = intval(get_option('marketking_store_name_initial_field_id_setting', 0));
		// if field does not exist, = 0
		if (intval($store_name_initial_field_id === 0)){

			$field = array(
			    'post_title' => sanitize_text_field(esc_html__('Store Name','marketking-multivendor-marketplace-for-woocommerce')),
			    'post_status' => 'publish',
			    'post_type' => 'marketking_field',
			    'post_author' => 1,
			);
			$field_id = wp_insert_post($field);

			$vendor_option_id = intval(get_option('marketking_vendor_option_id_setting', 0));

			// set field meta
			update_post_meta($field_id, 'marketking_field_status', 1);
			update_post_meta($field_id, 'marketking_field_required', 1);
			update_post_meta($field_id, 'marketking_field_sort_number', 5);
			update_post_meta($field_id, 'marketking_field_registration_option', 'option_'.$vendor_option_id);
			update_post_meta($field_id, 'marketking_field_editable', 1);
			update_post_meta($field_id, 'marketking_field_field_type', 'text');
			update_post_meta($field_id, 'marketking_field_field_label', sanitize_text_field(esc_html__('Store Name','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_field_placeholder', sanitize_text_field(esc_html__('Enter your desired store name here...','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_billing_connection', 'billing_store_name');

			// set option 
			update_option( 'marketking_store_name_initial_field_id_setting', intval($field_id) );
		}

		$store_url_initial_field_id = intval(get_option('marketking_store_url_initial_field_id_setting', 0));
		// if field does not exist, = 0
		if (intval($store_url_initial_field_id === 0)){

			$field = array(
			    'post_title' => sanitize_text_field(esc_html__('Store URL','marketking-multivendor-marketplace-for-woocommerce')),
			    'post_status' => 'publish',
			    'post_type' => 'marketking_field',
			    'post_author' => 1,
			);
			$field_id = wp_insert_post($field);

			$vendor_option_id = intval(get_option('marketking_vendor_option_id_setting', 0));

			// set field meta
			update_post_meta($field_id, 'marketking_field_status', 1);
			update_post_meta($field_id, 'marketking_field_required', 1);
			update_post_meta($field_id, 'marketking_field_sort_number', 6);
			update_post_meta($field_id, 'marketking_field_registration_option', 'option_'.$vendor_option_id);
			update_post_meta($field_id, 'marketking_field_editable', 1);
			update_post_meta($field_id, 'marketking_field_field_type', 'text');
			update_post_meta($field_id, 'marketking_field_field_label', sanitize_text_field(esc_html__('Store URL','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_field_placeholder', sanitize_text_field(esc_html__('Enter your desired store URL here...','marketking-multivendor-marketplace-for-woocommerce')));
			update_post_meta($field_id, 'marketking_field_billing_connection', 'billing_store_url');

			// set option 
			update_option( 'marketking_store_url_initial_field_id_setting', intval($field_id) );
		}


		// rebuild visibility cache
	    update_option('marketking_rebuild_visibility_cache', 'yes');

	    // wp roles
	    $groups = get_posts([
	      'post_type' => 'marketking_group',
	      'post_status' => 'publish',
	      'numberposts' => -1,
	      'fields' => 'ids',
	    ]);

	    if (apply_filters('marketking_use_wp_roles', false)){
	    	
	    	foreach ($groups as $group){
	    		add_role('marketking_role_'.$group, get_the_title($group));
	    	}
	    }


	}

}
