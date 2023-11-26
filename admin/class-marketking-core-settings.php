<?php

/**
*
* PHP File that handles Settings management
*
*/

class Marketkingcore_Settings {

	public function register_all_settings() {



		// Current Tab Setting - Misc setting, hidden, only saves the last opened menu tab
		register_setting( 'marketking', 'marketking_current_tab_setting');
		add_settings_field('marketking_current_tab_setting', '', array($this, 'marketking_current_tab_setting_content'), 'marketking', 'marketking_hiddensettings');

		do_action('marketking_register_settings');

		// Registration settings
		register_setting('marketking', 'marketking_vendor_registration_setting'); // vendor registration option

		add_settings_section('marketking_vendor_registration_page_settings_section', '',	'',	'marketking');

		// Choose Sales vendors Page
		register_setting('marketking', 'marketking_vendor_registration_page_setting');
		add_settings_field('marketking_vendor_registration_page_setting', esc_html__('Vendor Registration Page', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_vendor_registration_page_setting_content'), 'marketking', 'marketking_vendor_registration_page_settings_section');

		// allow loggedin
		register_setting('marketking', 'marketking_vendor_registration_loggedin_setting');
		add_settings_field('marketking_vendor_registration_loggedin_setting', esc_html__('Existing Users Can Apply', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Existing users / customers will be able to apply to convert their account to a vendor account.','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_vendor_registration_loggedin_setting_content'), 'marketking', 'marketking_vendor_registration_page_settings_section');

		// Choose Stores page
		register_setting('marketking', 'marketking_stores_page_setting');
		add_settings_field('marketking_stores_page_setting', esc_html__('Vendor Stores Page', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_stores_page_setting_content'), 'marketking', 'marketking_vendordash_page_settings_section');

		// Vendor Dashboard settings
		add_settings_section('marketking_vendordash_page_settings_section', '',	'',	'marketking');
		add_settings_section('marketking_vendordash_color_fields_settings_section', '',	'',	'marketking');
		add_settings_section('marketking_vendordash_color_fields_settings_section2', '',	'',	'marketking');

		// Choose Sales Agents Page
		register_setting('marketking', 'marketking_vendordash_page_setting');
		add_settings_field('marketking_vendordash_page_setting', esc_html__('Vendor Dashboard Page', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_vendordash_page_setting_content'), 'marketking', 'marketking_vendordash_page_settings_section');

		register_setting('marketking', 'marketking_elementor_page_setting');

		// Appearance and colors settings
		add_settings_section('marketking_appearance_settings_section', '',	'',	'marketking');
		// Store Style
		register_setting( 'marketking', 'marketking_store_style_setting');
		add_settings_field('marketking_store_style_setting', esc_html__('Store Page Style','marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_store_style_setting_content'), 'marketking', 'marketking_appearance_settings_section');


		// Logo Upload
		register_setting( 'marketking', 'marketking_logo_setting');
		add_settings_field('marketking_logo_setting', esc_html__('Vendor Dashboard Logo','marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_logo_setting_content'), 'marketking', 'marketking_vendordash_page_settings_section');

		// Favicon Upload
		register_setting( 'marketking', 'marketking_logo_favicon_setting');
		add_settings_field('marketking_logo_favicon_setting', esc_html__('Vendor Dashboard Favicon','marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_logo_favicon_setting_content'), 'marketking', 'marketking_vendordash_page_settings_section');

		// Change Color
		if (intval(get_option('marketking_enable_colorscheme_setting', 1)) === 1) { 
			register_setting( 'marketking', 'marketking_change_color_scheme_setting');
			add_settings_field('marketking_change_color_scheme_setting', esc_html__('Change Color Scheme','marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_change_color_scheme_setting_content'), 'marketking', 'marketking_vendordash_color_fields_settings_section');

			// Main Color
			register_setting(
				'marketking',
				'marketking_main_dashboard_color_setting',
				array(
					'sanitize_callback' => function ( $input ) {
						return $input === null ? get_option( 'marketking_main_dashboard_color_setting', '#854fff' ) : $input;
					},
				)
			);
			add_settings_field( 'marketking_main_dashboard_color_setting', esc_html__( 'Dashboard Color', 'marketking-multivendor-marketplace-for-woocommerce' ), array( $this, 'marketking_main_dashboard_color_setting_content' ), 'marketking', 'marketking_vendordash_color_fields_settings_section2' );

			// Main Color Hover
			register_setting(
				'marketking',
				'marketking_main_dashboard_hover_color_setting',
				array(
					'sanitize_callback' => function ( $input ) {
						return $input === null ? get_option( 'marketking_main_dashboard_hover_color_setting', '#6a29ff' ) : $input;
					},
				)
			);
			add_settings_field( 'marketking_main_dashboard_hover_color_setting', esc_html__( 'Dashboard Color Hover', 'marketking-multivendor-marketplace-for-woocommerce' ), array( $this, 'marketking_main_dashboard_hover_color_setting_content' ), 'marketking', 'marketking_vendordash_color_fields_settings_section2' );

			// Main Color Hover
			register_setting(
				'marketking',
				'marketking_color_schemes_setting',
				array(
					'sanitize_callback' => function ( $input ) {
						return $input === null ? get_option( 'marketking_color_schemes_setting', '#6a29ff' ) : $input;
					},
				)
			);
			add_settings_field( 'marketking_color_schemes_setting', esc_html__( 'Pre-Built Color Schemes', 'marketking-multivendor-marketplace-for-woocommerce' ), array( $this, 'marketking_color_schemes_setting_content' ), 'marketking', 'marketking_vendordash_color_fields_settings_section2' );

		}

		

		add_settings_section('marketking_main_settings_section_commissions', '',	'',	'marketking');

		add_settings_section('marketking_main_settings_section_payouts', '',	'',	'marketking');
		register_setting('marketking', 'marketking_enable_custom_payouts_title_setting');
		register_setting('marketking', 'marketking_enable_custom_payouts_description_setting');

		// PayPal Payouts
		register_setting('marketking', 'marketking_enable_paypal_payouts_setting');
		add_settings_field('marketking_enable_paypal_payouts_setting', esc_html__('Enable PayPal Payouts', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_enable_paypal_payouts_setting_content'), 'marketking', 'marketking_main_settings_section_payouts');
		

		if (defined('MARKETKINGPRO_DIR')){
			if (intval(get_option( 'marketking_enable_stripe_setting', 1 )) === 1){
				// Stripe Payouts
				register_setting('marketking', 'marketking_enable_stripe_payouts_setting');
				add_settings_field('marketking_enable_stripe_payouts_setting', esc_html__('Enable Stripe Payouts', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_enable_stripe_payouts_setting_content'), 'marketking', 'marketking_main_settings_section_payouts');
			}
		}

		// Bank Payouts
		register_setting('marketking', 'marketking_enable_bank_payouts_setting');
		add_settings_field('marketking_enable_bank_payouts_setting', esc_html__('Enable Bank Transfer Payouts', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_enable_bank_payouts_setting_content'), 'marketking', 'marketking_main_settings_section_payouts');

		// Configure Custom Payout Method
		register_setting('marketking', 'marketking_enable_custom_payouts_setting');
		add_settings_field('marketking_enable_custom_payouts_setting', esc_html__('Enable Custom Payout Method', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_enable_custom_payouts_setting_content'), 'marketking', 'marketking_main_settings_section_payouts');


		if (defined('MARKETKINGPRO_DIR')){
			if (intval(get_option('marketking_enable_withdrawals_setting', 1)) === 1){

				// Withdrawal Limits Payouts
				register_setting('marketking', 'marketking_withdrawal_limit_setting');
				add_settings_field('marketking_withdrawal_limit_setting', esc_html__('Min. Withdrawal Threshold', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_withdrawal_limit_setting_content'), 'marketking', 'marketking_main_settings_section_payouts');
			}
		}

		/* License Settings */
		add_settings_section('marketking_license_settings_section', '',	'',	'marketking');
		// Hide prices to guests text
		register_setting('marketking', 'marketking_license_email_setting');
		add_settings_field('marketking_license_email_setting', esc_html__('License email', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_license_email_setting_content'), 'marketking', 'marketking_license_settings_section');

		register_setting('marketking', 'marketking_license_key_setting');
		add_settings_field('marketking_license_key_setting', esc_html__('License key', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_license_key_setting_content'), 'marketking', 'marketking_license_settings_section');



		// memberships
		add_settings_section('marketking_memberships_settings_section', '',	'',	'marketking');

		if (defined('MARKETKINGPRO_DIR')){
			if (intval(get_option('marketking_enable_memberships_setting', 1)) === 1){

				register_setting('marketking', 'marketking_memberships_page_name_setting');
				add_settings_field('marketking_memberships_page_name_setting', esc_html__('Dashboard Page Name', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_memberships_page_name_setting_content'), 'marketking', 'marketking_memberships_settings_section');

				register_setting('marketking', 'marketking_memberships_page_title_setting');
				add_settings_field('marketking_memberships_page_title_setting', esc_html__('Dashboard Page Title', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_memberships_page_title_setting_content'), 'marketking', 'marketking_memberships_settings_section');

				register_setting('marketking', 'marketking_memberships_page_description_setting');
				add_settings_field('marketking_memberships_page_description_setting', esc_html__('Dashboard Page Description', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_memberships_page_description_setting_content'), 'marketking', 'marketking_memberships_settings_section');
				
				register_setting('marketking', 'marketking_memberships_default_group_setting');
				add_settings_field('marketking_memberships_default_group_setting', esc_html__('Default Group', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_memberships_default_group_setting_content'), 'marketking', 'marketking_memberships_settings_section');

				register_setting('marketking', 'marketking_memberships_assign_group_time_setting');
				add_settings_field('marketking_memberships_assign_group_time_setting', esc_html__('Group Assignment', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_memberships_assign_group_time_setting_content'), 'marketking', 'marketking_memberships_settings_section');
			}				
		}

		// advertising
		add_settings_section('marketking_advertising_settings_section', '',	'',	'marketking');

		if (defined('MARKETKINGPRO_DIR')){
			if (intval(get_option('marketking_enable_advertising_setting', 0)) === 1){

				register_setting('marketking', 'marketking_credit_price_setting');
				add_settings_field('marketking_credit_price_setting', esc_html__('Cost Per 1 Credit', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('How much it costs vendors to purchase 1 credit point (in real currency).','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_credit_price_setting_content'), 'marketking', 'marketking_advertising_settings_section');

				register_setting('marketking', 'marketking_credit_cost_per_day_setting');
				add_settings_field('marketking_credit_cost_per_day_setting', esc_html__('Credit Cost Per Day', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('How many credits are needed to advertise a product for 1 day','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
				</div>', array($this,'marketking_credit_cost_per_day_setting_content'), 'marketking', 'marketking_advertising_settings_section');

				register_setting('marketking', 'marketking_advertising_featured_setting');
				add_settings_field('marketking_advertising_featured_setting', esc_html__('Mark Advertised Products as Featured', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Advertised productss will be marked as featured. Products will be automatically removed from featured list after advertisement is expired.','marketking-multivendor-marketplace-for-woocommerce').'" >
					<i class="question circle icon"></i>
				</div>', array($this,'marketking_advertising_featured_setting_content'), 'marketking', 'marketking_advertising_settings_section');

				register_setting('marketking', 'marketking_advertised_products_top_setting');
				add_settings_field('marketking_advertised_products_top_setting', esc_html__('Display Advertised Products on Top', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Advertised products will be displayed on top of the catalog listing pages: Shop page, Single Store Page etc.','marketking-multivendor-marketplace-for-woocommerce').'" >
					<i class="question circle icon"></i>
				</div>', array($this,'marketking_advertised_products_top_setting_content'), 'marketking', 'marketking_advertising_settings_section');

			}				
		}
		
		if(!defined('MARKETKINGPRO_DIR') || (intval(get_option( 'marketking_enable_complexcommissions_setting', 1 )) !== 1)){
			// Commission Type
			register_setting('marketking', 'marketking_commission_type_setting');
			add_settings_field('marketking_commission_type_setting', esc_html__('Commission Type', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_commission_type_setting_content'), 'marketking', 'marketking_main_settings_section_commissions');

			// Commission Value
			register_setting('marketking', 'marketking_commission_value_setting');
			add_settings_field('marketking_commission_value_setting', esc_html__('Commission Value','marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_commission_value_setting_content'), 'marketking', 'marketking_main_settings_section_commissions');
		}
		// Shipping Fee Recipient
		register_setting('marketking', 'marketking_shipping_fee_recipient_setting');
		add_settings_field('marketking_shipping_fee_recipient_setting', esc_html__('Shipping Fee Recipient', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_shipping_fee_recipient_setting_content'), 'marketking', 'marketking_main_settings_section_commissions');

		// Tax Fee Recipient
		register_setting('marketking', 'marketking_tax_fee_recipient_setting');
		add_settings_field('marketking_tax_fee_recipient_setting', esc_html__('Tax Fee Recipient', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_tax_fee_recipient_setting_content'), 'marketking', 'marketking_main_settings_section_commissions');

		// Exclude COD settings
		register_setting('marketking', 'marketking_cod_behaviour_setting');
		add_settings_field('marketking_cod_behaviour_setting', esc_html__('COD Orders', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_cod_behaviour_setting_content'), 'marketking', 'marketking_main_settings_section_commissions');	

		// when using complex commissions, give option to reverse if rules apply to vendor or to admin // default is to admin
		if(defined('MARKETKINGPRO_DIR') && (intval(get_option( 'marketking_enable_complexcommissions_setting', 1 )) === 1)){

			register_setting('marketking', 'marketking_reverse_commission_rules_setting');
			add_settings_field('marketking_reverse_commission_rules_setting', esc_html__('Rules Set Vendor Commission', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('By default, rules set the admin commission. Enabling this reverses it, and rules set the vendor commission.','marketking-multivendor-marketplace-for-woocommerce').'" >
				<i class="question circle icon"></i>
			</div>', array($this,'marketking_reverse_commission_rules_setting_content'), 'marketking', 'marketking_main_settings_section_commissions');

		}

		/* Social Sharing */
		add_settings_section('marketking_social_setings_section', '',	'',	'marketking');

		register_setting('marketking', 'marketking_social_sites_setting');
		add_settings_field('marketking_social_sites_setting', esc_html__('Social Media Sites', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Which social sites should be available to vendors?','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_social_sites_setting_content'), 'marketking', 'marketking_social_setings_section');

		// Enable support through external URL
		register_setting('marketking', 'marketking_social_icons_grayscale_setting');
		add_settings_field('marketking_social_icons_grayscale_setting', esc_html__('Grayscale Icons', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Turns frontend icons from color to black and white','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_social_icons_grayscale_setting_content'), 'marketking', 'marketking_social_setings_section');


		/* Shipping Tracking */
		add_settings_section('marketking_shippingtracking_setings_section', '',	'',	'marketking');

		register_setting('marketking', 'marketking_shipping_providers_setting');
		add_settings_field('marketking_shipping_providers_setting', esc_html__('Shipping Providers', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Which providers should be available to vendors for shipping tracking?','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_shipping_providers_setting_content'), 'marketking', 'marketking_shippingtracking_setings_section');

		register_setting('marketking', 'marketking_require_shipment_order_completed_setting');
		add_settings_field('marketking_require_shipment_order_completed_setting', esc_html__('Require shipment to complete order', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Vendors can mark order as completed only if there is an existing shipment','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_require_shipment_order_completed_setting_content'), 'marketking', 'marketking_shippingtracking_setings_section');

		register_setting('marketking', 'marketking_customers_mark_order_received_setting');
		add_settings_field('marketking_customers_mark_order_received_setting', esc_html__('Customers mark orders as received', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Customers have a button to mark that they have received a particular order','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_customers_mark_order_received_setting_content'), 'marketking', 'marketking_shippingtracking_setings_section');

		/* Invoices */
		add_settings_section('marketking_invoices_setings_section', '',	'',	'marketking');

		register_setting('marketking', 'marketking_enable_commission_invoices_setting');
		add_settings_field('marketking_enable_commission_invoices_setting', esc_html__('Enable commission invoices', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Commission invoices between sellers and marketplace are auto-generated.','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_enable_commission_invoices_setting_content'), 'marketking', 'marketking_invoices_setings_section');

		/* Single Product Multiple Vendors */
		add_settings_section('marketking_spmv_setings_section', '',	'',	'marketking');

		register_setting('marketking', 'marketking_vendor_priority_setting');
		add_settings_field('marketking_vendor_priority_setting', esc_html__('Vendor Priority', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('When multiple offers are available, which vendor offers should be shown first?','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_vendor_priority_setting_content'), 'marketking', 'marketking_spmv_setings_section');


		register_setting('marketking', 'marketking_stock_priority_setting');
		add_settings_field('marketking_stock_priority_setting', esc_html__('Stock Priority', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('By setting this to "in stock", the plugin will deprioritize out of stock offers and show them at the bottom of the offers list.','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_stock_priority_setting_content'), 'marketking', 'marketking_spmv_setings_section');


		register_setting('marketking', 'marketking_offers_position_setting');
		add_settings_field('marketking_offers_position_setting', esc_html__('Other Offers Position', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Control the location where the "other offers" tab is displayed on the product page.','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_offers_position_setting_content'), 'marketking', 'marketking_spmv_setings_section');


		register_setting('marketking', 'marketking_offers_shown_default_number_setting');
		add_settings_field('marketking_offers_shown_default_number_setting', esc_html__('Default Offers Number', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('This controls how many offers are shown by default. All offers will become visible after the user clicks on "show more".','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_offers_shown_default_number_setting_content'), 'marketking', 'marketking_spmv_setings_section');

		/* Other Settings */
		add_settings_section('marketking_other_settings_section', '',	'',	'marketking');
		/*
		register_setting('marketking', 'marketking_require_vendor_save_product_first_setting');
		add_settings_field('marketking_require_vendor_save_product_first_setting', esc_html__('Vendors must save products first (enhances compatibility).', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('When adding new products, vendors must first save them before being able to enter product details. This enhances 3rd party plugin compatibility and prevents errors when adding new products.','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_require_vendor_save_product_first_setting_content'), 'marketking', 'marketking_other_settings_section');
		*/

		// for future use of this section settings

		/* Vendor Capabilities */
		add_settings_section('marketking_vendor_capabilities_settings_section', '',	'',	'marketking');
		add_settings_section('marketking_vendor_capabilities_shipping_settings_section', '',	'',	'marketking');

		// Choose Sales vendors Page
		register_setting('marketking', 'marketking_vendor_publish_direct_setting');
		add_settings_field('marketking_vendor_publish_direct_setting', esc_html__('Vendors Publish Products Directly', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Vendors can bypass the "pending / review" status, and directly publish products in the shop. This is a global setting that applies to all vendors.','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_vendor_publish_direct_setting_content'), 'marketking', 'marketking_vendor_capabilities_settings_section');

		register_setting('marketking', 'marketking_vendor_status_direct_setting');
		add_settings_field('marketking_vendor_status_direct_setting', esc_html__('Vendors Change Order Status', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Vendors can directly control and change the status of orders (processing, completed, on-hold, etc). This is a global setting that applies to all vendors.','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_vendor_status_direct_setting_content'), 'marketking', 'marketking_vendor_capabilities_settings_section');

		if(defined('MARKETKINGPRO_DIR')){
			/* Product Management Capabilities */
			add_settings_section('marketking_vendor_capabilities_product_settings_section', '',	'',	'marketking');

			// Add new products
			register_setting('marketking', 'marketking_vendors_can_newproducts_setting');
			add_settings_field('marketking_vendors_can_newproducts_setting', esc_html__('Add New Products', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('With this disabled, only the admin can set products for the vendor. Vendors can still add existing products through the Single Product Multiple Vendors module.','marketking-multivendor-marketplace-for-woocommerce').'" >
				<i class="question circle icon"></i>
			</div>', array($this,'marketking_vendors_can_newproducts_setting_content'), 'marketking', 'marketking_vendor_capabilities_product_settings_section');

			// Edit product tags
			register_setting('marketking', 'marketking_vendors_can_tags_setting');
			add_settings_field('marketking_vendors_can_tags_setting', esc_html__('Edit Product Tags', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Vendors can set/modify product tags for their own products.','marketking-multivendor-marketplace-for-woocommerce').'" >
				<i class="question circle icon"></i>
			</div>', array($this,'marketking_vendors_can_tags_setting_content'), 'marketking', 'marketking_vendor_capabilities_product_settings_section');

			// Linked Products
			register_setting('marketking', 'marketking_vendors_can_linked_products_setting');
			add_settings_field('marketking_vendors_can_linked_products_setting', esc_html__('Add Linked Products', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Vendors can add upsell/cross-sell products to be promoted throughout the shop via the "Linked Products" tab.','marketking-multivendor-marketplace-for-woocommerce').'" >
				<i class="question circle icon"></i>
			</div>', array($this,'marketking_vendors_can_linked_products_setting_content'), 'marketking', 'marketking_vendor_capabilities_product_settings_section');

			// Purchase Notes
			register_setting('marketking', 'marketking_vendors_can_purchase_notes_setting');
			add_settings_field('marketking_vendors_can_purchase_notes_setting', esc_html__('Add Purchase Notes', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Vendors can set purchase notes for products, to be sent to customers after purchase.','marketking-multivendor-marketplace-for-woocommerce').'" >
				<i class="question circle icon"></i>
			</div>', array($this,'marketking_vendors_can_purchase_notes_setting_content'), 'marketking', 'marketking_vendor_capabilities_product_settings_section');

			// Enable / Disable Reviews
			register_setting('marketking', 'marketking_vendors_can_reviews_setting');
			add_settings_field('marketking_vendors_can_reviews_setting', esc_html__('Turn Reviews On/Off', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('Vendors can enable or disable reviews for their own products.','marketking-multivendor-marketplace-for-woocommerce').'" >
				<i class="question circle icon"></i>
			</div>', array($this,'marketking_vendors_can_reviews_setting_content'), 'marketking', 'marketking_vendor_capabilities_product_settings_section');

			// Store Categories
			if (intval(get_option('marketking_enable_storecategories_setting', 1)) === 1){
				register_setting('marketking', 'marketking_store_categories_singlemultiple_setting');
				add_settings_section('marketking_vendor_capabilities_store_settings_section', '',	'',	'marketking');

			}
		}



		register_setting('marketking', 'marketking_admin_only_shipping_methods_setting');
		add_settings_field('marketking_admin_only_shipping_methods_setting', esc_html__('Admin-Only Shipping Methods', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('If you are selling as the admin, which shipping methods are admin-only?','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_admin_only_shipping_methods_setting_content'), 'marketking', 'marketking_vendor_capabilities_shipping_settings_section');


		/* Refunds */
		if (defined('MARKETKINGPRO_DIR')){
			if (intval(get_option('marketking_enable_refunds_setting', 1)) === 1){
				add_settings_section('marketking_refunds_settings_section', '',	'',	'marketking');


				// Refund time limit
				register_setting('marketking', 'marketking_refund_time_limit_setting');
				add_settings_field('marketking_refund_time_limit_setting', esc_html__('Refund Request Time Limit', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_refund_time_limit_setting_content'), 'marketking', 'marketking_refunds_settings_section');

			}
		}


		/* Cart Section */
		add_settings_section('marketking_cart_settings_section', '',	'',	'marketking');
		register_setting('marketking', 'marketking_cart_display_setting'); // vendor registration option

		if (defined('MARKETKINGPRO_DIR')){
			if (intval(get_option('marketking_enable_inquiries_setting', 1)) === 1){
				/* Product Vendor Inquiry Section */
				add_settings_section('marketking_inquiries_settings_section', '',	'',	'marketking');

				// Enable product page inquiries
				register_setting('marketking', 'marketking_enable_product_page_inquiries_setting');
				add_settings_field('marketking_enable_product_page_inquiries_setting', esc_html__('Enable Product Page Inquiries', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_enable_product_page_inquiries_setting_content'), 'marketking', 'marketking_inquiries_settings_section');

				// Enable vendor page inquiries
				register_setting('marketking', 'marketking_enable_vendor_page_inquiries_setting');
				add_settings_field('marketking_enable_vendor_page_inquiries_setting', esc_html__('Enable Vendor Page Inquiries', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_enable_vendor_page_inquiries_setting_content'), 'marketking', 'marketking_inquiries_settings_section');

				// Inquiries use messaging system
				register_setting('marketking', 'marketking_inquiries_use_messaging_setting');
				add_settings_field('marketking_inquiries_use_messaging_setting', esc_html__('Inquiries Use Messaging System', 'marketking-multivendor-marketplace-for-woocommerce').'<div class="marketking_tooltip" data-tooltip="'.esc_html__('When this is enabled, inquiries use the messaging module (must be enabled in MarketKing -> Modules). Otherwise, they use email. Recommended.','marketking-multivendor-marketplace-for-woocommerce').'" >
			<i class="question circle icon"></i>
		</div>', array($this,'marketking_inquiries_use_messaging_setting_content'), 'marketking', 'marketking_inquiries_settings_section');
			}
		}


		if (defined('MARKETKINGPRO_DIR')){
			if (intval(get_option('marketking_enable_support_setting', 1)) === 1){
				/* Product Vendor Inquiry Section */
				add_settings_section('marketking_support_settings_section', '',	'',	'marketking');

				// Enable support through messaging module
				register_setting('marketking', 'marketking_enable_support_messaging_setting');
				add_settings_field('marketking_enable_support_messaging_setting', esc_html__('Allow Support Through Messaging Module', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_enable_support_messaging_setting_content'), 'marketking', 'marketking_support_settings_section');

				// Enable support through external URL
				register_setting('marketking', 'marketking_enable_support_external_setting');
				add_settings_field('marketking_enable_support_external_setting', esc_html__('Allow Support Through External URL', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_enable_support_external_setting_content'), 'marketking', 'marketking_support_settings_section');

				// Enable support through dedicated email
				register_setting('marketking', 'marketking_enable_support_email_setting');
				add_settings_field('marketking_enable_support_email_setting', esc_html__('Allow Support Through Dedicated Support Email', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_enable_support_email_setting_content'), 'marketking', 'marketking_support_settings_section');

				// Show support option on the order details page
				register_setting('marketking', 'marketking_show_support_order_details_setting');
				add_settings_field('marketking_show_support_order_details_setting', esc_html__('Show Support Option On Order Details Page', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_show_support_order_details_setting_content'), 'marketking', 'marketking_support_settings_section');

				// Show support option on the order details page
				register_setting('marketking', 'marketking_show_support_single_product_setting');
				add_settings_field('marketking_show_support_single_product_setting', esc_html__('Show Support Option On Single Product Pages', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_show_support_single_product_setting_content'), 'marketking', 'marketking_support_settings_section');
				
			}
		}


		/* Language Section */
		add_settings_section('marketking_language_settings_section', '',	'',	'marketking');

		// Hide prices to guests text
		register_setting('marketking', 'marketking_cart_vendors_text_setting');
		add_settings_field('marketking_cart_vendors_text_setting', esc_html__('Cart Multiple Vendors Text', 'marketking-multivendor-marketplace-for-woocommerce'), array($this,'marketking_cart_vendors_text_setting_content'), 'marketking', 'marketking_language_settings_section');



	}

	function marketking_main_dashboard_color_setting_content(){
		?>
		<input name="marketking_main_dashboard_color_setting" type="color" value="<?php echo esc_attr( get_option( 'marketking_main_dashboard_color_setting', '#854fff' ) ); ?>">
		<?php
	}

	function marketking_color_schemes_setting_content(){
		?>
		<!-- ADD COLOR SCHEME OPTIONS BUILT-IN -->
		<div class="marketking_color_scheme_container"><span class="marketking_color_scheme_description"><?php esc_html_e('Royal Gold','marketking-multivendor-marketplace-for-woocommerce');?></span><button type="button" class="marketking_color_scheme_button" value="gold"><img class="marketking_settings_color_scheme" src="<?php echo plugins_url('../includes/assets/images/goldscheme.png', __FILE__); ?>"></button></div>
		<div class="marketking_color_scheme_container"><span class="marketking_color_scheme_description"><?php esc_html_e('Deep Indigo','marketking-multivendor-marketplace-for-woocommerce');?></span><button type="button" class="marketking_color_scheme_button" value="indigo"><img class="marketking_settings_color_scheme" src="<?php echo plugins_url('../includes/assets/images/royalscheme.png', __FILE__); ?>"></button></div>
		<div class="marketking_color_scheme_container"><span class="marketking_color_scheme_description"><?php esc_html_e('Bold Jade','marketking-multivendor-marketplace-for-woocommerce');?></span><button type="button" class="marketking_color_scheme_button" value="jade"><img class="marketking_settings_color_scheme" src="<?php echo plugins_url('../includes/assets/images/jadescheme.png', __FILE__); ?>"></button></div>
		<?php
	}

	function marketking_main_dashboard_hover_color_setting_content(){
		?>
		<input name="marketking_main_dashboard_hover_color_setting" type="color" value="<?php echo esc_attr( get_option( 'marketking_main_dashboard_hover_color_setting', '#6a29ff' ) ); ?>">

		<?php
	}

	function marketking_refund_time_limit_setting_content(){
		echo '
		<div class="ui form">
			<div class="field">
				<label>'.esc_html__('Number of days after placing an order, during which customers can request a refund.','marketking-multivendor-marketplace-for-woocommerce').'</label>
				<input type="number" name="marketking_refund_time_limit_setting" value="'.esc_attr(get_option('marketking_refund_time_limit_setting', 90)).'">
			</div>
		</div>
		';	
	}

	function marketking_withdrawal_limit_setting_content(){
		echo '
		<div class="ui form">
			<div class="field">
				<label>'.esc_html__('Minimum balance required to make a withdrawal.','marketking-multivendor-marketplace-for-woocommerce').'</label>
				<input type="number" name="marketking_withdrawal_limit_setting" value="'.esc_attr(get_option('marketking_withdrawal_limit_setting', 500)).'">
			</div>
		</div>
		';
	}

	function marketking_memberships_page_name_setting_content(){
		echo '
		<div class="ui form">
			<div class="field">
				<label>'.esc_html__('Panel name on the vendor dashboard (e.g. "Member", "Upgrade!", "Subscription", etc.)','marketking-multivendor-marketplace-for-woocommerce').'</label>
				<input type="text" name="marketking_memberships_page_name_setting" value="'.esc_attr(get_option('marketking_memberships_page_name_setting', esc_html__('Member','marketking-multivendor-marketplace-for-woocommerce'))).'">
			</div>
		</div>
		';
	}
	function marketking_memberships_page_title_setting_content(){
		echo '
		<div class="ui form">
			<div class="field">
				<label>'.esc_html__('Title displayed on the vendor dashboard page.','marketking-multivendor-marketplace-for-woocommerce').'</label>
				<input type="text" name="marketking_memberships_page_title_setting" value="'.esc_attr(get_option('marketking_memberships_page_title_setting', esc_html__('Available Options','marketking-multivendor-marketplace-for-woocommerce'))).'">
			</div>
		</div>
		';
	}
	function marketking_memberships_page_description_setting_content(){
		echo '
		<div class="ui form">
			<div class="field">
				<label>'.esc_html__('Description displayed on the vendor dashboard page.','marketking-multivendor-marketplace-for-woocommerce').'</label>
				<input type="text" name="marketking_memberships_page_description_setting" value="'.esc_attr(get_option('marketking_memberships_page_description_setting', esc_html__('Choose your desired option and start enjoying our service.','marketking-multivendor-marketplace-for-woocommerce'))).'">
			</div>
		</div>
		';
	}

	function marketking_cart_vendors_text_setting_content(){
		echo '
		<div class="ui form">
			<div class="field">
				<label>'.esc_html__('Message shown when multiple vendors\'s products are in cart. Leave empty to hide it.','marketking-multivendor-marketplace-for-woocommerce').'</label>
				<input type="text" name="marketking_cart_vendors_text_setting" value="'.esc_attr(get_option('marketking_cart_vendors_text_setting', esc_html__('The products in your cart are sold by multiple different vendor partners. The order will be placed simultaneously with all vendors and you will receive a package from each of them.','marketking-multivendor-marketplace-for-woocommerce'))).'">
			</div>
		</div>
		';
	}

	function marketking_enable_custom_payouts_setting_content(){
		// get visibility status
		echo '
		<div class="ui toggle checkbox">
		  <input type="checkbox" name="marketking_enable_custom_payouts_setting" value="1" '.checked(1,get_option( 'marketking_enable_custom_payouts_setting', 0 ), false).'">
		  <label></label>
		</div>
		<br>
		<div id="marketking_custom_method_container">
			<input type="text" name="marketking_enable_custom_payouts_title_setting" value="'.get_option( 'marketking_enable_custom_payouts_title_setting', '' ).'" placeholder="'.esc_html__('Enter method title here...','marketking-multivendor-marketplace-for-woocommerce').'" id="marketking_custom_method_title"><br >
			<textarea name="marketking_enable_custom_payouts_description_setting" placeholder="'.esc_html__('Enter method description / instructions here...','marketking-multivendor-marketplace-for-woocommerce').'" id="marketking_custom_method_description">'.esc_html(get_option( 'marketking_enable_custom_payouts_description_setting', '' )).'</textarea>
		</div>
		';
	}
	
	function marketking_vendors_can_linked_products_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_vendors_can_linked_products_setting" value="1" '.checked(1,get_option( 'marketking_vendors_can_linked_products_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}


	function marketking_license_email_setting_content(){
		echo '
		<div class="ui form">
			<div class="field">
				<input type="text" class="marketking_license_field" name="marketking_license_email_setting" value="'.esc_attr(get_option('marketking_license_email_setting', '')).'">
			</div>
		</div>
		';
	}


	function marketking_license_key_setting_content(){
		echo '
		<div class="ui form">
			<div class="field">
				<input type="text" class="marketking_license_field" name="marketking_license_key_setting" value="'.esc_attr(get_option('marketking_license_key_setting', '')).'">
			</div>
		</div>
		';
	}

	function marketking_enable_product_page_inquiries_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_enable_product_page_inquiries_setting" value="1" '.checked(1,get_option( 'marketking_enable_product_page_inquiries_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_enable_vendor_page_inquiries_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_enable_vendor_page_inquiries_setting" value="1" '.checked(1,get_option( 'marketking_enable_vendor_page_inquiries_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_enable_support_external_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_enable_support_external_setting" value="1" '.checked(1,get_option( 'marketking_enable_support_external_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_social_icons_grayscale_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_social_icons_grayscale_setting" value="1" '.checked(1,get_option( 'marketking_social_icons_grayscale_setting', 0 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_advertising_featured_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_advertising_featured_setting" value="1" '.checked(1,get_option( 'marketking_advertising_featured_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_advertised_products_top_setting_content(){
		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_advertised_products_top_setting" value="1" '.checked(1,get_option( 'marketking_advertised_products_top_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_enable_support_email_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_enable_support_email_setting" value="1" '.checked(1,get_option( 'marketking_enable_support_email_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_show_support_order_details_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_show_support_order_details_setting" value="1" '.checked(1,get_option( 'marketking_show_support_order_details_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_show_support_single_product_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_show_support_single_product_setting" value="1" '.checked(1,get_option( 'marketking_show_support_single_product_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}	
	

	function marketking_enable_support_messaging_setting_content(){

		$checkval = 1;
		if (intval(get_option('marketking_enable_messages_setting', 1)) === 1){
			$disabled = '';
			$msgdisabled = '';
		} else {
			// if messaging module is not enabled, this setting cannot work so it must be unchecked, and disabled.
			$disabled = 'disabled="disabled"';
			$checkval = 'no';
			$msgdisabled = esc_html__('(messaging module is disabled)','marketking-multivendor-marketplace-for-woocommerce');
		}

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_enable_support_messaging_setting" value="1" '.checked($checkval,get_option( 'marketking_enable_support_messaging_setting', 1 ), false).' '.$disabled.'>
		  <label>'.$msgdisabled.'</label>
		</div>
		';
	}

	function marketking_inquiries_use_messaging_setting_content(){

		$checkval = 1;
		if (intval(get_option('marketking_enable_messages_setting', 1)) === 1){
			$disabled = '';
			$msgdisabled = '';
		} else {
			// if messaging module is not enabled, this setting cannot work so it must be unchecked, and disabled.
			$disabled = 'disabled="disabled"';
			$checkval = 'no';
			$msgdisabled = esc_html__('(messaging module is disabled)','marketking-multivendor-marketplace-for-woocommerce');
		}

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_inquiries_use_messaging_setting" value="1" '.checked($checkval,get_option( 'marketking_inquiries_use_messaging_setting', 1 ), false).' '.$disabled.'>
		  <label>'.$msgdisabled.'</label>
		</div>
		';
	}

	function marketking_vendors_can_purchase_notes_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_vendors_can_purchase_notes_setting" value="1" '.checked(1,get_option( 'marketking_vendors_can_purchase_notes_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_vendors_can_reviews_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_vendors_can_reviews_setting" value="1" '.checked(1,get_option( 'marketking_vendors_can_reviews_setting', 0), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_vendors_can_newproducts_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_vendors_can_newproducts_setting" value="1" '.checked(1,get_option( 'marketking_vendors_can_newproducts_setting', 1), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_vendors_can_tags_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_vendors_can_tags_setting" value="1" '.checked(1,get_option( 'marketking_vendors_can_tags_setting', 1), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_vendor_status_direct_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_vendor_status_direct_setting" value="1" '.checked(1,get_option( 'marketking_vendor_status_direct_setting', 1 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_reverse_commission_rules_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_reverse_commission_rules_setting" value="1" '.checked(1,get_option( 'marketking_reverse_commission_rules_setting', 0 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_vendor_registration_loggedin_setting_content(){
		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_vendor_registration_loggedin_setting" value="1" '.checked(1,get_option( 'marketking_vendor_registration_loggedin_setting', 0 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_vendor_publish_direct_setting_content(){

		echo '
		<div class="ui toggle checkbox" >
		  <input type="checkbox" name="marketking_vendor_publish_direct_setting" value="1" '.checked(1,get_option( 'marketking_vendor_publish_direct_setting', 0 ), false).'>
		  <label></label>
		</div>
		';
	}

	function marketking_cod_behaviour_setting_content(){

		$behaviour = get_option('marketking_cod_behaviour_setting', 'default');
		?>
		<div class="ui form">
			<div class="field">
				<label><?php esc_html_e('What happens with commisssions from Cash on Delivery (COD) orders.','marketking-multivendor-marketplace-for-woocommerce');?></label>
				<select name="marketking_cod_behaviour_setting">
				<option value="default" <?php selected($behaviour, 'default', true);?>><?php esc_html_e('- None - (COD orders are treated like all other orders)', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="exclude" <?php selected($behaviour, 'exclude', true);?>><?php esc_html_e('Ignored (COD orders have no effect on commissions or balances)', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="reverse" <?php selected($behaviour, 'reverse', true);?>><?php esc_html_e('Reversed (Admin commissions are deducted from vendor balance)', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				</select>
			</div>
		</div>
		<?php
	}

	function marketking_require_shipment_order_completed_setting_content(){
		echo '
		<div class="ui toggle checkbox">
		  <input type="checkbox" name="marketking_require_shipment_order_completed_setting" value="1" '.checked(1,get_option( 'marketking_require_shipment_order_completed_setting', 0 ), false).'">
		  <label></label>
		</div>
		';
	}

	function marketking_customers_mark_order_received_setting_content(){
		echo '
		<div class="ui toggle checkbox">
		  <input type="checkbox" name="marketking_customers_mark_order_received_setting" value="1" '.checked(1,get_option( 'marketking_customers_mark_order_received_setting', 0 ), false).'">
		  <label></label>
		</div>
		';
	}

	function marketking_enable_commission_invoices_setting_content(){
		echo '
		<div class="ui toggle checkbox">
		  <input type="checkbox" name="marketking_enable_commission_invoices_setting" value="1" '.checked(1,get_option( 'marketking_enable_commission_invoices_setting', 0 ), false).'">
		  <label></label>
		</div>
		';
	}

	function marketking_enable_bank_payouts_setting_content(){
		echo '
		<div class="ui toggle checkbox">
		  <input type="checkbox" name="marketking_enable_bank_payouts_setting" value="1" '.checked(1,get_option( 'marketking_enable_bank_payouts_setting', 0 ), false).'">
		  <label></label>
		</div>
		';
	}

	function marketking_change_color_scheme_setting_content(){
		echo '
		<div class="ui toggle checkbox">
		  <input type="checkbox" name="marketking_change_color_scheme_setting" value="1" '.checked(1,get_option( 'marketking_change_color_scheme_setting', 0 ), false).'">
		  <label></label>
		</div>
		';
	}

	function marketking_enable_paypal_payouts_setting_content(){
		echo '
		<div class="ui toggle checkbox">
		  <input type="checkbox" name="marketking_enable_paypal_payouts_setting" value="1" '.checked(1,get_option( 'marketking_enable_paypal_payouts_setting', 1 ), false).'">
		  <label></label>
		</div>
		';
	}	

	function marketking_enable_stripe_payouts_setting_content(){
		echo '
		<div class="ui toggle checkbox">
		  <input type="checkbox" name="marketking_enable_stripe_payouts_setting" value="1" '.checked(1,get_option( 'marketking_enable_stripe_payouts_setting', 1 ), false).'">
		  <label></label>
		</div>
		';
	}	

	// This function remembers the current tab as a hidden input setting. When the page loads, it goes to the saved tab
	function marketking_current_tab_setting_content(){
		echo '
		 <input type="hidden" id="marketking_current_tab_setting_input" name="marketking_current_tab_setting" value="'.esc_attr(get_option( 'marketking_current_tab_setting', 'registration' )).'">
		';
	}

	function marketking_store_style_setting_content(){
		?>
		<div class="ui form">
		  <div class="grouped fields">
		  	<div class="field">
		  	  <div class="ui radio checkbox">
		  	    <input type="radio" name="marketking_store_style_setting" value="3"  <?php checked(3,intval(get_option( 'marketking_store_style_setting', 1 )), true); ?>>
		  	    <label><?php esc_html_e('Light Simple','marketking-multivendor-marketplace-for-woocommerce');?></label>
		  	    <img class="marketking_settings_store_style_img" src="<?php echo plugins_url('../includes/assets/images/store_style_3.png', __FILE__); ?>">
		  	  </div>
		  	</div>
		    <div class="field">
		      <div class="ui radio checkbox">
		        <input type="radio" name="marketking_store_style_setting" value="1" <?php checked(1,intval(get_option( 'marketking_store_style_setting', 1 )), true); ?>>
		        <label><?php esc_html_e('Light Dashed','marketking-multivendor-marketplace-for-woocommerce');?></label>
		        <img class="marketking_settings_store_style_img" src="<?php echo plugins_url('../includes/assets/images/store_style_1.png', __FILE__); ?>">

		      </div>
		    </div>
		    <div class="field">
		      <div class="ui radio checkbox">
		        <input type="radio" name="marketking_store_style_setting" value="2"  <?php checked(2,intval(get_option( 'marketking_store_style_setting', 1 )), true); ?>>
		        <label><?php esc_html_e('Dark Mode','marketking-multivendor-marketplace-for-woocommerce');?></label>
		        <img class="marketking_settings_store_style_img" src="<?php echo plugins_url('../includes/assets/images/store_style_2.png', __FILE__); ?>">

		      </div>
		    </div>

		    <?php
		    if (defined('MARKETKINGPRO_DIR')){
		    	if (intval(get_option('marketking_enable_elementor_setting', 1)) === 1){
			    	?>
				    <div class="field">
				      <div class="ui radio checkbox">
				        <input type="radio" name="marketking_store_style_setting" value="4"  <?php checked(4,intval(get_option( 'marketking_store_style_setting', 1 )), true); ?>>
				        <label><?php esc_html_e('Elementor - Choose An Elementor Page','marketking-multivendor-marketplace-for-woocommerce');?></label>
				      </div>
				    </div>
				    <?php
				    echo '<br><select name="marketking_elementor_page_setting">';
				      	
				    // get pages
				    $pages = get_pages();
				    foreach ($pages as $page){
				    	echo '<option value="'.esc_attr($page->ID).'" '.selected($page->ID, apply_filters( 'wpml_object_id', get_option( 'marketking_elementor_page_setting', 'disabled' ), 'post' , true), false).'">'.esc_html($page->post_title).'</option>';
				    }

				    echo'</select><br>';
				    
				}
			}
			?>
		  </div>
		</div>
		<?php
	}

	function marketking_logo_setting_content(){
		echo '
			<div>
			    <input type="text" name="marketking_logo_setting" id="marketking_logo_setting" class="regular-text" placeholder="'.esc_attr__('Your Custom Logo', 'marketking-multivendor-marketplace-for-woocommerce').'" value="'.esc_attr(get_option('marketking_logo_setting','')).'"><br><br>
			    <input type="button" name="marketking-upload-btn" id="marketking-upload-btn" class="ui blue button" value="'.esc_attr__('Select Image','marketking-multivendor-marketplace-for-woocommerce').'">
			</div>
		';
	}

	function marketking_logo_favicon_setting_content(){
		echo '
			<div>
			    <input type="text" name="marketking_logo_favicon_setting" id="marketking_logo_favicon_setting" class="regular-text" placeholder="'.esc_attr__('Your Vendor Dashboard Favicon', 'marketking-multivendor-marketplace-for-woocommerce').'" value="'.esc_attr(get_option('marketking_logo_favicon_setting','')).'"><br><br>
			    <input type="button" name="marketking-upload-btn-favicon" id="marketking-upload-btn-favicon" class="ui blue button" value="'.esc_attr__('Select Image','marketking-multivendor-marketplace-for-woocommerce').'">
			</div>
		';
	}

	function marketking_admin_only_shipping_methods_setting_content(){
		$selected = get_option('marketking_admin_only_shipping_methods_setting',array());
		if (!is_array($selected)){
			$selected = array();
		}

		$zone_methods = array();
		?>
		<select name="marketking_admin_only_shipping_methods_setting[]" class="ui fluid search dropdown" multiple="">
			<?php
			if (defined('MARKETKINGPRO_DIR')){
				

				// list all shipping methods
				$methods_display = array();
				$shipping_methods = array();
				$zone_names = array();

				$delivery_zones = WC_Shipping_Zones::get_zones();
		        foreach ($delivery_zones as $key => $the_zone) {
		            foreach ($the_zone['shipping_methods'] as $value) {
		                array_push($shipping_methods, $value);
		                array_push($zone_names, $the_zone['zone_name']);
		            }
		        }
		        $zone = 0;
				foreach ($shipping_methods as $shipping_method){
					if( $shipping_method->enabled === 'yes' ){

						$methods_display[esc_attr($shipping_method->id).esc_attr($shipping_method->instance_id)] = esc_html($shipping_method->title).' ('.esc_html($zone_names[$zone]).')';

						array_push($zone_methods, $shipping_method->id);

					}
					$zone++;
		
				}

				foreach ($methods_display as $slug => $provider){

					// skip vendor shipping
					if (explode('_', $slug)[0] === 'marketking'){
						continue; // name is marketking_shipping
					}
					?>
					<option value="<?php echo esc_attr($slug); ?>" <?php

					if (in_array($slug, $selected)){
						echo 'selected="selected"';	
					}
					?>><?php echo esc_html($provider); ?></option>
					<?php
				} 

				// now include non-zone methods
				$shipping_methods = WC()->shipping->get_shipping_methods();
				foreach ($shipping_methods as $shipping_method){
					
					// skip vendor shipping
					if (explode('_', $shipping_method->id)[0] === 'marketking'){
						continue; // name is marketking_shipping
					}

					// don't show zone methods
					if (in_array($shipping_method->id, $zone_methods)){
					//	continue; // actually show them, so they can be enabled for vendor dashboard add removal
					}

					?>
					<option value="<?php echo esc_attr($shipping_method->id); ?>" <?php

					if (in_array($shipping_method->id, $selected)){
						echo 'selected="selected"';	
					}
					?>><?php echo esc_html($shipping_method->method_title); ?></option>
					<?php
				}
			}
			?>
		</select>
		<?php
	}


	function marketking_social_sites_setting_content(){
		$selected = get_option('marketking_social_sites_setting', array('facebook', 'twitter', 'youtube', 'instagram', 'linkedin', 'pinterest'));
		
		if (!is_array($selected)){
			$selected = array();
		}
		?>
		<select name="marketking_social_sites_setting[]" class="ui fluid search dropdown" multiple="">
			<?php
			if (defined('MARKETKINGPRO_DIR')){
				$providers = array('facebook', 'twitter', 'youtube', 'instagram', 'linkedin', 'pinterest');
				foreach ($providers as $provider){
					?>
					<option value="<?php echo esc_attr($provider); ?>" <?php

					if (in_array($provider, $selected)){
						echo 'selected="selected"';	
					}
					?>><?php echo ucfirst(esc_html($provider)); ?></option>
					<?php
				} 
			}
			?>
		</select>
		<?php
	}

	function marketking_shipping_providers_setting_content(){
		$selected = get_option('marketking_shipping_providers_setting',array('sp-other'));
		
		if (!is_array($selected)){
			$selected = array();
		}
		?>
		<select name="marketking_shipping_providers_setting[]" class="ui fluid search dropdown" multiple="">
			<?php
			if (defined('MARKETKINGPRO_DIR')){
				$providers = marketkingpro()->get_tracking_providers();
				foreach ($providers as $slug => $provider){
					?>
					<option value="<?php echo esc_attr($slug); ?>" <?php

					if (in_array($slug, $selected)){
						echo 'selected="selected"';	
					}
					?>><?php echo esc_html($provider['label']); ?></option>
					<?php
				} 
			}
			?>
		</select>
		<?php
	}

	function marketking_vendor_priority_setting_content(){
		$priority = get_option('marketking_vendor_priority_setting', 'lowerprice');
		?>
		<div class="ui form">
			<div class="field">
				<label><?php esc_html_e('Which offers should be shown first?','marketking-multivendor-marketplace-for-woocommerce');?></label>
				<select name="marketking_vendor_priority_setting">
				<option value="lowerprice" <?php selected($priority, 'minprice', true);?>><?php esc_html_e('Lowest Prices First', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="higherprice" <?php selected($priority, 'higherprice', true);?>><?php esc_html_e('Highest Prices First', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="higherrated" <?php selected($priority, 'higherrated', true);?>><?php esc_html_e('Top Rated Vendors First', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				</select>
			</div>
		</div>
		<?php
	}

	function marketking_memberships_default_group_setting_content(){
		$checkedgroup = get_option('marketking_memberships_default_group_setting', '');
		?>
		<div class="ui form">
			<div class="field">
				<label><?php esc_html_e('What group should vendors be assigned to, in case payment fails, subscription is cancelled, etc?','marketking-multivendor-marketplace-for-woocommerce');?></label>
				<select name="marketking_memberships_default_group_setting" class="marketking_full_width_select">
				<?php
					$groups = get_posts([
					  'post_type' => 'marketking_group',
					  'post_status' => 'publish',
					  'numberposts' => -1
					]);

					echo '<option value="none" '.selected('none', $checkedgroup, false).'>'.esc_html__('Deactivate vendor (disables selling)','marketking-multivendor-marketplace-for-woocommerce').'</option>';


					foreach ($groups as $group){
						echo '<option value="'.esc_attr($group->ID).'" '.selected($group->ID, $checkedgroup, false).'>'.esc_html($group->post_title).'</option>';
					}
				?>
				</select>
			</div>
		</div>
		<?php
	}

	function marketking_memberships_assign_group_time_setting_content(){
		$assignment = get_option('marketking_memberships_assign_group_time_setting', 'order_placed');
		?>
		<div class="ui form">
			<div class="field">
				<label><?php esc_html_e('When should vendors be assigned to the new group? (after purchasing a membership option)','marketking-multivendor-marketplace-for-woocommerce');?></label>
				<select name="marketking_memberships_assign_group_time_setting" class="marketking_full_width_select">
					<option value="order_placed" <?php selected($assignment, 'order_placed', true);?>><?php esc_html_e('When order is processed / placed', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
					<option value="order_completed" <?php selected($assignment, 'order_completed', true);?>><?php esc_html_e('When order status is marked as completed', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				</select>
			</div>
		</div>
		<?php
	}

	function marketking_stock_priority_setting_content(){
		$priority = get_option('marketking_stock_priority_setting', 'instock');
		?>
		<div class="ui form">
			<div class="field">
				<label><?php esc_html_e('Should in stock offers have priority?','marketking-multivendor-marketplace-for-woocommerce');?></label>
				<select name="marketking_stock_priority_setting">
				<option value="instock" <?php selected($priority, 'instock', true);?>><?php esc_html_e('In Stock Products First', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="none" <?php selected($priority, 'none', true);?>><?php esc_html_e('None', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				</select>
			</div>
		</div>
		<?php
	}

	function marketking_offers_position_setting_content(){
		$priority = get_option('marketking_offers_position_setting', 'belowproduct');
		?>
		<div class="ui form">
			<div class="field">
				<label><?php esc_html_e('Position of the "other offers" tab','marketking-multivendor-marketplace-for-woocommerce');?></label>
				<select name="marketking_offers_position_setting">
				<option value="belowproduct" <?php selected($priority, 'belowproduct', true);?>><?php esc_html_e('Immediately Below Product', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="insideproducttabs" <?php selected($priority, 'insideproducttabs', true);?>><?php esc_html_e('Inside Product Tabs', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="belowproducttabs" <?php selected($priority, 'belowproducttabs', true);?>><?php esc_html_e('Below Product Tabs', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				</select>
			</div>
		</div>
		<?php
	}

	function marketking_offers_shown_default_number_setting_content(){
		
		echo '<input type="number" name="marketking_offers_shown_default_number_setting" min="0" id="marketking_offers_shown_default_number_setting" class="regular-text" value="'.esc_attr(get_option('marketking_offers_shown_default_number_setting',1)).'">';
		
	}

	function marketking_credit_price_setting_content(){
		
		echo '<input type="number" name="marketking_credit_price_setting" min="0" id="marketking_credit_price_setting" step="0.01" class="regular-text" value="'.esc_attr(get_option('marketking_credit_price_setting',1)).'">';
		
	}

	function marketking_credit_cost_per_day_setting_content(){
		
		echo '<input type="number" name="marketking_credit_cost_per_day_setting" min="1" step="1" id="marketking_credit_cost_per_day_setting" class="regular-text" value="'.esc_attr(get_option('marketking_credit_cost_per_day_setting',1)).'">';
		
	}

	

	

	function marketking_commission_type_setting_content(){
		$type = get_option('marketking_commission_type_setting', 'percentage');
		?>
		<div class="ui form">
			<div class="field">
				<label><?php esc_html_e('Select a commission type for vendor sales','marketking-multivendor-marketplace-for-woocommerce');?></label>
				<select name="marketking_commission_type_setting">
				<option value="percentage" <?php selected($type, 'percentage', true);?>><?php esc_html_e('Percentage', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="flat" <?php selected($type, 'flat', true);?>><?php esc_html_e('Flat', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				</select>
			</div>
		</div>
		<?php
	}


	function marketking_shipping_fee_recipient_setting_content(){
		$type = get_option('marketking_shipping_fee_recipient_setting', 'vendor');
		?>
		<div class="ui form">
			<div class="field">
				<label><?php esc_html_e('Who receives the Shipping fees','marketking-multivendor-marketplace-for-woocommerce');?></label>
				<select name="marketking_shipping_fee_recipient_setting">
				<option value="vendor" <?php selected($type, 'vendor', true);?>><?php esc_html_e('Vendor', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="adminvendor" <?php selected($type, 'adminvendor', true);?>><?php esc_html_e('Admin + Vendor (Shipping included in commission calculation)', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="admin" <?php selected($type, 'admin', true);?>><?php esc_html_e('Admin', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				</select>
			</div>
		</div>
		<?php
	}

	function marketking_tax_fee_recipient_setting_content(){
		$type = get_option('marketking_tax_fee_recipient_setting', 'vendor');
		?>
		<div class="ui form">
			<div class="field">
				<label><?php esc_html_e('Who receives the Tax fees','marketking-multivendor-marketplace-for-woocommerce');?></label>
				<select name="marketking_tax_fee_recipient_setting">
				<option value="vendor" <?php selected($type, 'vendor', true);?>><?php esc_html_e('Vendor', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="adminvendor" <?php selected($type, 'adminvendor', true);?>><?php esc_html_e('Admin + Vendor (Tax included in commission calculation)', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				<option value="admin" <?php selected($type, 'admin', true);?>><?php esc_html_e('Admin', 'marketking-multivendor-marketplace-for-woocommerce');?></option>
				</select>
			</div>
		</div>
		<?php
	}



	


	function marketking_commission_value_setting_content(){
		echo '
			<div class="ui form">
				<div class="field">
					<label>'.esc_html__('Admin commission received for each sale','marketking-multivendor-marketplace-for-woocommerce').'</label>
			    	<input type="text" name="marketking_commission_value_setting" id="marketking_commission_value_setting" class="regular-text" placeholder="'.esc_attr__('Admin commission value', 'marketking-multivendor-marketplace-for-woocommerce').'" value="'.esc_attr(get_option('marketking_commission_value_setting',0)).'">
			   	</div>
			</div>
		';
	} 

	function marketking_vendordash_page_setting_content(){
		echo '<select name="marketking_vendordash_page_setting">';
		  	
		// get pages
		$pages = get_pages();
		foreach ($pages as $page){
			echo '<option value="'.esc_attr($page->ID).'" '.selected($page->ID, apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true), false).'">'.esc_html($page->post_title).'</option>';
		}

		echo'</select>';

	}

	function marketking_vendor_registration_page_setting_content(){
		echo '<select name="marketking_vendor_registration_page_setting" id="marketking_vendor_registration_page_setting_input">';
		

		// initial option first
		$page_initial = get_option('marketking_vendor_registration_page_setting_initial', 'none');
		if (get_post_type($page_initial) === 'page'){
			echo '<option value="'.esc_attr($page_initial).'" '.selected($page_initial, apply_filters( 'wpml_object_id', get_option( 'marketking_vendor_registration_page_setting', 'disabled' ), 'post' , true), false).'">'.esc_html(get_the_title($page_initial)).'</option>';
		}

		// get pages
		$pages = get_pages();
		foreach ($pages as $page){
			if (intval($page->ID) === intval($page_initial)){
				continue;
			}
			echo '<option value="'.esc_attr($page->ID).'" '.selected($page->ID, apply_filters( 'wpml_object_id', get_option( 'marketking_vendor_registration_page_setting', 'disabled' ), 'post' , true), false).'">'.esc_html($page->post_title).'</option>';
		}

		echo'</select>';
	}

	function marketking_stores_page_setting_content(){
		echo '<select name="marketking_stores_page_setting" id="marketking_stores_page_setting">';
		

		// initial option first
		$page_initial = get_option('marketking_stores_page_setting_initial', 'none');
		if (get_post_type($page_initial) === 'page'){
			echo '<option value="'.esc_attr($page_initial).'" '.selected($page_initial, apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'disabled' ), 'post' , true), false).'">'.esc_html(get_the_title($page_initial)).'</option>';
		}

		// get pages
		$pages = get_pages();
		foreach ($pages as $page){
			if (intval($page->ID) === intval($page_initial)){
				continue;
			}
			echo '<option value="'.esc_attr($page->ID).'" '.selected($page->ID, apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'disabled' ), 'post' , true), false).'">'.esc_html($page->post_title).'</option>';
		}

		echo'</select>';
	}
		
	public function render_settings_page_content() {
		?>

		<!-- Admin Menu Page Content -->
		<form id="marketking_admin_form" method="POST" action="options.php">
			<?php settings_fields('marketking'); ?>
			<?php do_settings_fields( 'marketking', 'marketking_hiddensettings' ); ?>

			<div id="marketking_admin_wrapper" >

				<!-- Admin Menu Tabs --> 
				<div id="marketking_admin_menu" class="ui labeled stackable large vertical menu attached">
					<img id="marketking_menu_logo" src="<?php echo plugins_url('../includes/assets/images/marketkinglogo10.png', __FILE__); ?>">
					<a class="green item <?php echo $this->marketking_isactivetab('registration'); ?>" data-tab="registration">
						<i class="user plus icon"></i>
						<div class="header"><?php esc_html_e('Registration','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
					</a>
					<a class="green item <?php echo $this->marketking_isactivetab('vendordashboard'); ?>" data-tab="vendordashboard">
						<i class="chart area icon"></i>
						<div class="header"><?php esc_html_e('Vendor Dashboard','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
					</a>
					<a class="green item <?php echo $this->marketking_isactivetab('vendorcapabilities'); ?>" data-tab="vendorcapabilities">
						<i class="id badge icon"></i>
						<div class="header"><?php esc_html_e('Vendor Capabilities','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
					</a>

					<a class="green item <?php echo $this->marketking_isactivetab('commissions'); ?>" data-tab="commissions">
						<i class="chart pie icon"></i>
						<div class="header"><?php esc_html_e('Commissions','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
					</a>

					<a class="green item <?php echo $this->marketking_isactivetab('payouts'); ?>" data-tab="payouts">
						<i class="university icon"></i>
						<div class="header"><?php esc_html_e('Payouts','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
					</a>
					<a class="green item <?php echo $this->marketking_isactivetab('cart'); ?>" data-tab="cart">
						<i class="shopping cart icon"></i>
						<div class="header"><?php esc_html_e('Cart','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
					</a>
					<a class="green item <?php echo $this->marketking_isactivetab('language'); ?>" data-tab="language">
						<i class="language icon"></i>
						<div class="header"><?php esc_html_e('Language and Text','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
					</a>
					<a class="green item <?php echo $this->marketking_isactivetab('appearance'); ?>" data-tab="appearance">
						<i class="pen square icon"></i>
						<div class="header"><?php esc_html_e('Appearance','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
					</a>
					<?php
					// optional modules
					if (defined('MARKETKINGPRO_DIR')){
						if (intval(get_option('marketking_enable_inquiries_setting', 1)) === 1){
							// product vendor inquiries
							?>
							<a class="green item <?php echo $this->marketking_isactivetab('inquiries'); ?>" data-tab="inquiries">
								<i class="comments icon"></i>
								<div class="header"><?php esc_html_e('Product & Vendor Inquiries','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
							</a>
							<?php
						}
					}

					// optional modules
					if (defined('MARKETKINGPRO_DIR')){
						if (intval(get_option('marketking_enable_support_setting', 1)) === 1){
							// product vendor inquiries
							?>
							<a class="green item <?php echo $this->marketking_isactivetab('support'); ?>" data-tab="support">
								<i class="ticket icon"></i>
								<div class="header"><?php esc_html_e('Store Support','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
							</a>
							<?php
						}
					}

					// optional modules
					if (defined('MARKETKINGPRO_DIR')){
						if (intval(get_option('marketking_enable_colorscheme_setting', 1)) === 1){
							?>
							<a class="green item <?php echo $this->marketking_isactivetab('colorscheme'); ?>" data-tab="colorscheme">
								<i class="paint brush icon"></i>
								<div class="header"><?php esc_html_e('Color Scheme','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
							</a>
							<?php
						}
					}

					// optional modules
					if (defined('MARKETKINGPRO_DIR')){
						if (intval(get_option('marketking_enable_spmv_setting', 1)) === 1){
							?>
							<a class="green item <?php echo $this->marketking_isactivetab('spmv'); ?>" data-tab="spmv">
								<i class="boxes icon"></i>
								<div class="header"><?php esc_html_e('Multiple Product Vendors','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
							</a>
							<?php
						}
					}


					if (defined('MARKETKINGPRO_DIR')){
						if (intval(get_option('marketking_enable_shippingtracking_setting', 1)) === 1){
							?>
							<a class="green item <?php echo $this->marketking_isactivetab('shippingtracking'); ?>" data-tab="shippingtracking">
								<i class="shipping fast icon"></i>
								<div class="header"><?php esc_html_e('Shipping Tracking','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
							</a>
							<?php
						}
					}

					// optional modules
					if (defined('MARKETKINGPRO_DIR')){
						if (intval(get_option('marketking_enable_refunds_setting', 1)) === 1){
							?>
							<a class="green item <?php echo $this->marketking_isactivetab('refunds'); ?>" data-tab="refunds">
								<i class="undo icon"></i>
								<div class="header"><?php esc_html_e('Refund Requests','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
							</a>
							<?php
						}
					}

					// optional modules
					if (defined('MARKETKINGPRO_DIR')){
						if (intval(get_option('marketking_enable_vendorinvoices_setting', 1)) === 1){
							?>
							<a class="green item <?php echo $this->marketking_isactivetab('invoices'); ?>" data-tab="invoices">
								<i class="wpforms icon"></i>
								<div class="header"><?php esc_html_e('Invoicing','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
							</a>
							<?php
						}
					}

					// optional modules
					if (defined('MARKETKINGPRO_DIR')){
						if (intval(get_option('marketking_enable_memberships_setting', 1)) === 1){
							?>
							<a class="green item <?php echo $this->marketking_isactivetab('memberships'); ?>" data-tab="memberships">
								<i class="users icon"></i>
								<div class="header"><?php esc_html_e('Memberships','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
							</a>
							<?php
						}
					}


					if (defined('MARKETKINGPRO_DIR')){
						if (intval(get_option('marketking_enable_social_setting', 1)) === 1){
							?>
							<a class="green item <?php echo $this->marketking_isactivetab('social'); ?>" data-tab="social">
								<i class="facebook square icon"></i>
								<div class="header"><?php esc_html_e('Social Sharing','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
							</a>
							<?php
						}
					}


					if (defined('MARKETKINGPRO_DIR')){
						if (intval(get_option('marketking_enable_advertising_setting', 0)) === 1){
							?>
							<a class="green item <?php echo $this->marketking_isactivetab('advertising'); ?>" data-tab="advertising">
								<i class="bullhorn icon"></i>
								<div class="header"><?php esc_html_e('Advertising','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
							</a>
							<?php
						}
					}


					if (defined('MARKETKINGPRO_DIR')){
						?>
						<a class="green item marketking_license marketking_othersettings_margin <?php  echo $this->marketking_isactivetab('license'); ?>" data-tab="license">
							<i class="key icon"></i>
							<div class="header"><?php  esc_html_e('License','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
						</a>
						<?php
					}

					/*
					?>
					<a class="green item <?php echo $this->marketking_isactivetab('other'); ?>" data-tab="other">
						<i class="cog icon"></i>
						<div class="header"><?php esc_html_e('Other & Advanced','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
					</a>
					<?php
					*/

					do_action('marketking_settings_panel_end_items');
					?>

					<div id="marketking_settings_last_item_panel"></div>
				
				</div>
			
				<!-- Admin Menu Tabs Content--> 
				<div id="marketking_tabs_wrapper">

					<!-- Registration Tab -->
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('registration'); ?>" data-tab="registration">
						<div class="marketking_attached_content_wrapper">

					
							<h3 class="ui block header">
								<i class="address book icon"></i>
								<?php esc_html_e('Set up vendor registration.','marketking-multivendor-marketplace-for-woocommerce'); ?>
							</h3>
							
							<table class="form-table">
								<div class="ui large form marketking_plugin_status_container">
								  <div class="inline fields">
								    <label><?php esc_html_e('Vendor Registration','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
								    <div class="field">
								      <div class="ui checkbox">
								        <input type="radio" tabindex="0" class="hidden" name="marketking_vendor_registration_setting" value="disabled" <?php checked('disabled',get_option( 'marketking_vendor_registration_setting', 'myaccount' ), true); ?>">
								        <label><?php esc_html_e('Disabled','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
								      </div>
								    </div>
								    <div class="field">
								      <div class="ui checkbox">
								        <input type="radio" tabindex="0" class="hidden" name="marketking_vendor_registration_setting" value="myaccount" <?php checked('myaccount',get_option( 'marketking_vendor_registration_setting', 'myaccount' ), true); ?>">
								        <label><i class="address book outline icon"></i>&nbsp;<?php esc_html_e('My Account Page','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
								      </div>
								    </div>
								    <div class="field">
								      <div class="ui checkbox">
								        <input type="radio" tabindex="0" class="hidden" name="marketking_vendor_registration_setting" value="separate" <?php checked('separate',get_option( 'marketking_vendor_registration_setting', 'myaccount' ), true); ?>">
								        <label><i class="clipboard list icon"></i>&nbsp;<?php esc_html_e('Separate Registration Page','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
								      </div>
								    </div>

								  </div>
								  <?php
								  	// check woocommerce registration setting and show warning
								  	if (get_option('woocommerce_enable_myaccount_registration') !== 'yes'){
								  		?>
								  		<div class="ui warning message" id="marketking_myaccount_message"><p><?php esc_html_e('My account registration is not enabled in WooCommerce. You must go to WooCommerce -> Settings -> Accounts & Privacy -> and enable "Allow customers to create an account on the "My account" page"','marketking-multivendor-marketplace-for-woocommerce');?></p></div>
								  		<?php
								  	}
								  ?>
								</div>
							</table>
							<table class="form-table" id="marketking_vendor_registration_page_container">
								<?php do_settings_fields( 'marketking', 'marketking_vendor_registration_page_settings_section' ); ?>
							</table>	
							<div id="marketking_registration_form_container">
								
								<?php
								if (!defined('MARKETKINGPRO_DIR')){
									?>
									<button type="button" name="marketking_form_button" id="marketking_form_button" class="ui grey button"><i class="lock icon"></i><?php esc_html_e('Manage Registration Form & Fields','marketking-multivendor-marketplace-for-woocommerce');?></button><br>
									<div class="ui pointing label" id="marketking_form_button_label">
									    <?php esc_html_e('Upgrade to MarketKing Premium to customize the registration form. Choose from 9 types of custom fields: text, dropdown, phone nr, file upload, etc.')?>
									</div>
									<?php
								} else {
									if (intval(get_option( 'marketking_enable_registration_setting', 1 )) === 1){
										?>
										<a href="<?php echo admin_url( 'admin.php?page=marketking_registration'); ?>">
										<button type="button" name="marketking_form_button" id="marketking_form_button" class="ui blue button"><i class="clipboard list icon"></i><?php esc_html_e('Manage Registration Form & Fields','marketking-multivendor-marketplace-for-woocommerce');?></button><br>
										</a>
										<?php
									}
								}
								?>
								
							</div>
					
							
						</div>
					</div>

					<!-- Vendor Dashboard Tab -->
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('vendordashboard'); ?>" data-tab="vendordashboard">
						<div class="marketking_attached_content_wrapper">

					
							<h3 class="ui block header">
								<i class="address book icon"></i>
								<?php esc_html_e('Set up the vendor dashboard.','marketking-multivendor-marketplace-for-woocommerce'); ?>
							</h3>
							
							<table class="form-table">
								<?php do_settings_fields( 'marketking', 'marketking_vendordash_page_settings_section' ); ?>
							</table>

						</div>
					</div>



					<!-- Appearance Tab -->
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('appearance'); ?>" data-tab="appearance">
						<div class="marketking_attached_content_wrapper">					
							<h3 class="ui block header">
								<i class="pen square icon"></i>
								<?php esc_html_e('Set up store appearance.','marketking-multivendor-marketplace-for-woocommerce'); ?>
							</h3>
							
							<table class="form-table">
								<?php do_settings_fields( 'marketking', 'marketking_appearance_settings_section' ); ?>
							</table>

						</div>
					</div>

					<!-- Support Tab -->
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('support'); ?>" data-tab="support">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="ticket icon"></i>
								<div class="content">
									<?php esc_html_e('Support Settings','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</h2>					
							
							<table class="form-table marketking_vendor_support_container">
								<?php do_settings_fields( 'marketking', 'marketking_support_settings_section' ); ?>
							</table>

						</div>
					</div>

					<!-- Vendor Capabilities Tab -->
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('vendorcapabilities'); ?>" data-tab="vendorcapabilities">
						<div class="marketking_attached_content_wrapper">

					
							<h3 class="ui block header">
								<i class="address book icon"></i>
								<?php esc_html_e('Main Capabilities','marketking-multivendor-marketplace-for-woocommerce'); ?>
							</h3>
							
							<table class="form-table marketking_vendor_capabilities_container">
								<?php do_settings_fields( 'marketking', 'marketking_vendor_capabilities_settings_section' ); ?>
							</table>	

							<?php
							if (defined('MARKETKINGPRO_DIR')){
								?>
								<h3 class="ui block header">
									<i class="dolly icon"></i>
									<?php esc_html_e('Product Management','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</h3>
								
								<table class="form-table marketking_vendor_capabilities_container">
									<?php do_settings_fields( 'marketking', 'marketking_vendor_capabilities_product_settings_section' ); ?>
								</table>

								<h3 class="ui block header">
									<i class="truck icon"></i>
									<?php esc_html_e('Shipping Management','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</h3>
								
								<table class="form-table marketking_vendor_capabilities_container">
									<?php do_settings_fields( 'marketking', 'marketking_vendor_capabilities_shipping_settings_section' ); ?>
								</table>
								<?php


								

								if (intval(get_option('marketking_enable_storecategories_setting', 1)) === 1){

									?>
									<h3 class="ui block header">
										<i class="warehouse icon"></i>
										<?php esc_html_e('Store Management','marketking-multivendor-marketplace-for-woocommerce'); ?>
									</h3>
									
									<table class="form-table marketking_vendor_capabilities_container">
										<div class="ui large form marketking_plugin_status_container">
											<div class="inline fields">
											  <label><?php esc_html_e('Store Categories','marketking-multivendor-marketplace-for-woocommerce'); ?><a href="https://woocommerce-multivendor.com/docs/store-categories/"><div class="marketking_tooltip" data-tooltip='Allow single or multiple categories for organizing vendor stores'><i class="question circle icon"></i></div></a></label>
											  <div class="field">
											    <div class="ui checkbox">
											      <input type="radio" tabindex="0" class="hidden" name="marketking_store_categories_singlemultiple_setting" value="single" <?php checked('single',get_option( 'marketking_store_categories_singlemultiple_setting', 'single' ), true); ?>">
											      <label><?php esc_html_e('Single Category','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
											    </div>
											  </div>
											  <div class="field">
											    <div class="ui checkbox">
											      <input type="radio" tabindex="0" class="hidden" name="marketking_store_categories_singlemultiple_setting" value="multiple" <?php checked('multiple',get_option( 'marketking_store_categories_singlemultiple_setting', 'single' ), true); ?>">
											      <label><?php esc_html_e('Multiple Categories','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
											    </div>
											  </div>
											</div>
										</div>
										<?php do_settings_fields( 'marketking', 'marketking_vendor_capabilities_store_settings_section' ); ?>
									</table>
									<?php
								}

							}
							?>						
						</div>
					</div>
					

					<!-- Commissions Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('commissions'); ?>" data-tab="commissions">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="chart pie icon"></i>
								<div class="content">
									<?php esc_html_e('Commissions','marketking-multivendor-marketplace-for-woocommerce'); ?>
									<div class="sub header">
										<?php esc_html_e('Set up commission settings','marketking-multivendor-marketplace-for-woocommerce'); ?>
									</div>
								</div>
							</h2>

							<?php
							if(defined('MARKETKINGPRO_DIR')){
								if (intval(get_option( 'marketking_enable_complexcommissions_setting', 1 )) === 1){
									// complex commissions is enabled, here we show link to commission rules
									?>
									<div class="ui message">
								    	<p>
								    		<?php
											esc_html_e('The complex commissions module is active. Please go to ','marketking-multivendor-marketplace-for-woocommerce');

											?>
											<a href="<?php echo esc_url(admin_url('edit.php?post_type=marketking_rule'));?>"><?php esc_html_e('Commission Rules','marketking-multivendor-marketplace-for-woocommerce');?></a>
											<?php

											esc_html_e('to configure commissions.','marketking-multivendor-marketplace-for-woocommerce');

											?>
										</p>
								    </div>
									<?php
								}
							}
							?>

							<table class="form-table marketking_main_settings_section_payouts">
								<?php do_settings_fields( 'marketking', 'marketking_main_settings_section_commissions' ); ?>
							</table>

							<br>
							<div class="ui info message">
							  <i class="close icon"></i>
							  <div class="header">
							  	<?php esc_html_e('Documentation & Complex Commissions','marketking-multivendor-marketplace-for-woocommerce'); ?>
							  </div>
							  <ul class="list">
							    <li><a href="https://woocommerce-multivendor.com/docs/admin-and-vendor-commissions-multivendor-marketplace-commissions/"><?php esc_html_e('Learn how to set up simple or combined commissions by category, product or vendor with the "complex commissions" module.','marketking-multivendor-marketplace-for-woocommerce');?></a></li>
							    
							  </ul>
							</div>							

						</div>
					</div>

					<!-- Payouts Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('payouts'); ?>" data-tab="payouts">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="university icon"></i>
								<div class="content">
									<?php esc_html_e('Payouts','marketking-multivendor-marketplace-for-woocommerce'); ?>
									<div class="sub header">
										<?php esc_html_e('Control available payout options','marketking-multivendor-marketplace-for-woocommerce'); ?>
									</div>
								</div>
							</h2>
						
							<table class="form-table marketking_main_settings_section_payouts">
								<?php do_settings_fields( 'marketking', 'marketking_main_settings_section_payouts' ); ?>
							</table>						

						</div>
					</div>

					<!-- Cart Tab -->
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('cart'); ?>" data-tab="cart">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="shopping cart icon"></i>
								<div class="content">
									<?php esc_html_e('Cart','marketking-multivendor-marketplace-for-woocommerce'); ?>
									<div class="sub header">
										<?php esc_html_e('Control cart options','marketking-multivendor-marketplace-for-woocommerce'); ?>
									</div>
								</div>
							</h2>
							<div class="ui large form marketking_plugin_status_container">
								<div class="inline fields">
								  <label><?php esc_html_e('Cart Display Template','marketking-multivendor-marketplace-for-woocommerce'); ?><a href="https://woocommerce-multivendor.com/docs/marketplace-cart-options/"><div class="marketking_tooltip" data-tooltip='MarketKing has its own cart template & design, specially built for multivendor sites. Click on the question mark for documentation, screenshots and details.'><i class="question circle icon"></i></div></a></label>
								  <div class="field">
								    <div class="ui checkbox">
								      <input type="radio" tabindex="0" class="hidden" name="marketking_cart_display_setting" value="newcart" <?php checked('newcart',get_option( 'marketking_cart_display_setting', 'newcart' ), true); ?>">
								      <label><i class="shopping basket icon"></i>&nbsp;<?php esc_html_e('MarketKing Cart (Recommended)','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
								    </div>
								  </div>
								  <div class="field">
								    <div class="ui checkbox">
								      <input type="radio" tabindex="0" class="hidden" name="marketking_cart_display_setting" value="classic" <?php checked('classic',get_option( 'marketking_cart_display_setting', 'newcart' ), true); ?>">
								      <label><i class="shopping cart icon"></i>&nbsp;<?php esc_html_e('Classic Cart','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
								    </div>
								  </div>
								</div>
							</div>
						</div>
					</div>

					<!-- Language & Text Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('language'); ?>" data-tab="language">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="language icon"></i>
								<div class="content">
									<?php esc_html_e('Language and Text','marketking-multivendor-marketplace-for-woocommerce'); ?>
									<div class="sub header">
										<?php esc_html_e('Easily change plugin text. ','marketking-multivendor-marketplace-for-woocommerce');
										esc_html_e('To fully translate the plugin, click ','marketking-multivendor-marketplace-for-woocommerce');
										?><a href="https://woocommerce-multivendor.com/docs/translate-marketking-to-any-language-localization/"><?php esc_html_e('here','marketking-multivendor-marketplace-for-woocommerce');?></a>	
									</div>
								</div>
							</h2>
						
							<table class="form-table marketking_main_settings_section_payouts">
								<?php do_settings_fields( 'marketking', 'marketking_language_settings_section' ); ?>
							</table>

						</div>
					</div>

					<!-- Inquiries Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('inquiries'); ?>" data-tab="inquiries">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="comments icon"></i>
								<div class="content">
									<?php esc_html_e('Product and Vendor Inquiries','marketking-multivendor-marketplace-for-woocommerce'); ?>
									<div class="sub header">
										<?php esc_html_e('Allow vendors and customers to communicate via email or messaging. ','marketking-multivendor-marketplace-for-woocommerce'); ?>
									</div>
								</div>
							</h2>
						
							<table class="form-table marketking_main_settings_section_inquiries">
								<?php do_settings_fields( 'marketking', 'marketking_inquiries_settings_section' ); ?>

							</table>
						</div>
					</div>

					<!-- Refund Requests Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('refunds'); ?>" data-tab="refunds">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="undo icon"></i>
								<div class="content">
									<?php esc_html_e('Refund Requests','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</h2>
						
							<table class="form-table">
								<?php do_settings_fields( 'marketking', 'marketking_refunds_settings_section' ); ?>
							</table>

						</div>
					</div>

					<!-- Advertising Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('advertising'); ?>" data-tab="advertising">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="bullhorn icon"></i>
								<div class="content">
									<?php esc_html_e('Advertising','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</h2>
						
							<table class="form-table">
								<?php do_settings_fields( 'marketking', 'marketking_advertising_settings_section' ); ?>
							</table>

						</div>
					</div>


					<!-- MEMBERSHIPS TAB -->
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('memberships'); ?>" data-tab="memberships">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="users icon"></i>
								<div class="content">
									<?php esc_html_e('Memberships','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</h2>

							<div class="ui message">
						    	<p>
						    		<?php
									esc_html_e('To configure the membership packages / options available to your vendors, please go to the','marketking-multivendor-marketplace-for-woocommerce');

									?>
									<a href="<?php echo esc_url(admin_url('edit.php?post_type=marketking_mpack'));?>"><?php esc_html_e('Memberships','marketking-multivendor-marketplace-for-woocommerce');?></a>
									<?php

									esc_html_e('section.','marketking-multivendor-marketplace-for-woocommerce');

									?>
								</p>
						    </div>
						
							<table class="form-table">
								<?php do_settings_fields( 'marketking', 'marketking_memberships_settings_section' ); ?>
							</table>

						</div>
					</div>


					<!-- Color Scheme Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('colorscheme'); ?>" data-tab="colorscheme">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="paint brush icon"></i>
								<div class="content">
									<?php esc_html_e('Color Scheme Customization','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</h2>
							
							<?php if (intval(get_option('marketking_enable_colorscheme_setting', 1)) === 1) {  ?>

								<table class="form-table">
									<?php do_settings_fields( 'marketking', 'marketking_vendordash_color_fields_settings_section' ); ?>
								</table>

								<table class="form-table marketking_change_color_scheme_container">
									<?php do_settings_fields( 'marketking', 'marketking_vendordash_color_fields_settings_section2' ); ?>
								</table>	

							<?php }	?>	

						</div>
					</div>

					<!-- SPMV Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('spmv'); ?>" data-tab="spmv">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="boxes icon"></i>
								<div class="content">
									<?php esc_html_e('Single Product Multiple Vendors','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</h2>
							
							<?php if (intval(get_option('marketking_enable_spmv_setting', 1)) === 1) {  ?>

								<table class="form-table">
									<?php do_settings_fields( 'marketking', 'marketking_spmv_setings_section' ); ?>
								</table>

							<?php }	?>	

						</div>
					</div>


					<!-- Invoices Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('invoices'); ?>" data-tab="invoices">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="wpforms icon"></i>
								<div class="content">
									<?php esc_html_e('Invoices','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</h2>
							
							<?php if (intval(get_option('marketking_enable_vendorinvoices_setting', 1)) === 1) {  ?>

								<table class="form-table marketking_vendor_capabilities_container" >
									<?php do_settings_fields( 'marketking', 'marketking_invoices_setings_section' ); ?>
								</table>

							<?php }	?>	

						</div>
					</div>


					<!-- Shipping Tracking Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('shippingtracking'); ?>" data-tab="shippingtracking">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="truck icon"></i>
								<div class="content">
									<?php esc_html_e('Shipping Tracking','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</h2>
							
							<?php if (intval(get_option('marketking_enable_shippingtracking_setting', 1)) === 1) {  ?>

								<table class="form-table marketking_vendor_capabilities_container">
									<?php do_settings_fields('marketking', 'marketking_shippingtracking_setings_section'); ?>
								</table>

							<?php }	?>	

						</div>
					</div>

					<!-- Social Tab -->
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('social'); ?>" data-tab="social">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="facebook square icon"></i>
								<div class="content">
									<?php esc_html_e('Social Sharing','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</h2>
							
							<?php if (intval(get_option('marketking_enable_social_setting', 1)) === 1) {  ?>

								<table class="form-table marketking_vendor_capabilities_container">
									<?php do_settings_fields('marketking', 'marketking_social_setings_section'); ?>
								</table>

							<?php }	?>	

						</div>
					</div>

					<!-- License Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('license'); ?>" data-tab="license">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="key icon"></i>
								<div class="content">
									<?php esc_html_e('License management','marketking-multivendor-marketplace-for-woocommerce'); ?>
									<div class="sub header">
										<?php esc_html_e('Activate the plugin','marketking-multivendor-marketplace-for-woocommerce'); ?>
									</div>
								</div>
							</h2>
							<table class="form-table">
								<?php do_settings_fields( 'marketking', 'marketking_license_settings_section' ); ?>
							</table>
							<!-- License Status -->
							<?php
							$license = get_option('marketking_license_key_setting', '');
							$email = get_option('marketking_license_email_setting', '');
							$info = parse_url(get_site_url());
							$host = $info['host'];
							$host_names = explode(".", $host);
							$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];

							if (strlen($host_names[count($host_names)-2]) <= 3){    // likely .com.au, .co.uk, .org.uk etc
							    $bottom_host_name_new = $host_names[count($host_names)-3] . "." . $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
							    // legacy, do not deactivate existing sites
							    /*
							    if (get_option('pluginactivation_'.$email.'_'.$license.'_'.$bottom_host_name) === 'active'){
							        // old activation active, proceed with old activation
							    } else {
							        $bottom_host_name = $bottom_host_name_new;
							    }
							    */
							    $bottom_host_name = $bottom_host_name_new;

							}

							
							$activation = get_option('pluginactivation_'.$email.'_'.$license.'_'.$bottom_host_name);

							if ($activation == 'active'){
								?>
								<div class="ui success message marketking_license_active">
								  <div class="header">
								    <?php esc_html_e('Your license is valid and active','marketking-multivendor-marketplace-for-woocommerce'); ?>
								  </div>
								  <p><?php esc_html_e('The plugin is registered to ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($email); ?> </p>
								</div>
								<?php		
							} else {
								?>
								<button type="button" name="marketking-activate-license" id="marketking-activate-license" class="ui teal button">
									<i class="key icon"></i>
									<?php esc_html_e('Activate License', 'marketking-multivendor-marketplace-for-woocommerce'); ?>
								</button>
								<?php
							}
							?>

							<br><br><div class="ui info message">
							  <i class="close icon"></i>
							  <div class="header"> <i class="question circle icon"></i>
							  	<?php esc_html_e('Documentation','marketking-multivendor-marketplace-for-woocommerce'); ?>
							  </div>
							  <ul class="list">
							    <li><a href="https://kingsplugins.com/licensing-faq/" target="_blank"><?php esc_html_e('Licensing and Activation FAQ & Guide','marketking-multivendor-marketplace-for-woocommerce'); ?></a></li>
							    <li><a href="https://kingsplugins.com/licensing-faq#headline-66-565" target="_blank"><?php esc_html_e('How to activate if you purchased on Envato Market','b2bking'); ?></a></li>
							    <li><a href="https://kingsplugins.com/woocommerce-multivendor/marketking/pricing/#div_block-10-84" target="_blank"><?php esc_html_e('Purchase a new license','salesking'); ?></a></li>

							  </ul>
							</div>
							
							
						</div>
					</div>


					<!-- Other Tab--> 
					<div class="ui bottom attached tab segment <?php echo $this->marketking_isactivetab('other'); ?>" data-tab="other">
						<div class="marketking_attached_content_wrapper">
							<h2 class="ui block header">
								<i class="cog icon"></i>
								<div class="content">
									<?php esc_html_e('Other & Advanced Settings','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</h2>
							
								<table class="form-table marketking_other_section_table">
									<?php do_settings_fields( 'marketking', 'marketking_other_settings_section' ); ?>
								</table>

						</div>
					</div>
					

					<?php

						do_action('marketking_settings_panel_end_items_tabs');

					?>

				</div>
			</div>

			<br>
			<input type="submit" name="submit" id="marketking-admin-submit" class="ui primary button" value="Save Settings">
		</form>

		<?php
	}


	function marketking_isactivetab($tab){
		$gototab = get_option( 'marketking_current_tab_setting', 'registration' );

		// if tab is a module that's disabled, set tab to the main settings tab
		if ($gototab === 'inquiries' &&(!defined('MARKETKINGPRO_DIR') || intval(get_option('marketking_enable_inquiries_setting', 1)) !== 1)){
			$gototab = 'registration';
		}
		if ($tab === $gototab){
			return 'active';
		} 
	}

}