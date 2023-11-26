<?php

class Marketkingcore_Admin{

	function __construct() {

		// Require WooCommerce notification
		add_action( 'admin_notices', array($this, 'marketking_plugin_dependencies') );
		// Load admin notice resources (enables notification dismissal)
		add_action( 'admin_enqueue_scripts', array($this, 'load_global_admin_notice_resource') ); 
		// Allow shop manager to set plugin options
		add_filter( 'option_page_capability_marketking', array($this, 'marketking_options_capability' ) );


		// filter to remove MarketKing in all API requests:
		require_once ( MARKETKINGCORE_DIR . 'includes/class-marketking-core-helper.php' );
		$helper = new Marketkingcore_Helper();
		$run_in_api_requests = true;
		if (apply_filters('marketking_force_cancel_api_requests', false)){
			if ($helper->marketking_is_rest_api_request()){
				$run_in_api_requests = false;
			}
		}

		// Add  header bar in  post types
		add_action('in_admin_header', array($this,'marketking_show_header_bar_marketking_posts'));

		add_action( 'admin_notices', array( $this, 'marketking_activate_notification' ) );


		
		if ($run_in_api_requests){

			add_action( 'plugins_loaded', function(){
				if ( class_exists( 'woocommerce' ) ) {		


					/* Load resources */
					// Load global admin styles
					add_action( 'admin_enqueue_scripts', array($this, 'load_global_admin_resources') ); 
					// Only load scripts and styles in this specific admin page
					add_action( 'admin_enqueue_scripts', array($this, 'load_admin_resources') );


					/* Settings */
					// Registers settings
					if (!wp_doing_ajax()){
						add_action( 'admin_init', array( $this, 'marketking_settings_init' ) );
					}					// Renders settings 
					add_action( 'admin_menu', array( $this, 'marketking_settings_page' ) ); 

					/* Payouts */
					// Payour Request Count
					add_action( 'admin_head', array( $this, 'menu_order_count' ) );

					/* Custom User Meta */
					// Show the new user meta in New User, User Profile and Edit
					add_action( 'user_new_form', array($this, 'marketking_show_user_meta_profile'), 999, 1 );
					add_action( 'show_user_profile', array($this, 'marketking_show_user_meta_profile'), 999, 1 );
					add_action( 'edit_user_profile', array($this, 'marketking_show_user_meta_profile'), 999, 1 );
					// Save the new user meta (Update or Create)
					add_action( 'personal_options_update', array($this, 'marketking_save_user_meta_vendor_group') );
					add_action( 'edit_user_profile_update', array($this, 'marketking_save_user_meta_vendor_group') );
					add_action( 'user_register', array($this, 'marketking_save_user_meta_vendor_group') );
					// Add columns to Users Table
					add_filter( 'manage_users_columns',  array($this, 'marketking_add_columns_user_table') );
					add_filter( 'manage_users_custom_column', array($this, 'marketking_retrieve_group_column_contents_users_table'), 10, 3 );

					// Registration
					add_action( 'user_new_form', array($this, 'marketking_show_user_meta_profile_registration'), 999, 1 );
					add_action( 'show_user_profile', array($this, 'marketking_show_user_meta_profile_registration'), 999, 1 );
					add_action( 'edit_user_profile', array($this, 'marketking_show_user_meta_profile_registration'), 999, 1 );

					/* WooCommerce Backend */
					// Show author for products in backend
					add_action( 'after_setup_theme', [$this, 'marketking_products_add_author_column'] );
					// Change name from Author to Vendor
					add_action('add_meta_boxes', [$this, 'change_meta_box_titles']);
					add_filter( 'manage_edit-product_columns', [ $this, 'marketking_admin_product_edit_columns' ], 11 );
					add_action( 'manage_product_posts_custom_column', [ $this, 'marketking_product_custom_columns' ], 11 );

					add_action( 'woocommerce_process_product_meta', [ $this, 'save_product_meta' ], 12, 2 );
					// Orders
					add_filter( 'manage_edit-shop_order_columns', [ $this, 'marketking_admin_shop_order_edit_columns' ], 11 );
					add_action( 'manage_shop_order_posts_custom_column', [ $this, 'marketking_shop_order_custom_columns' ], 11 );
					add_filter( 'manage_edit-shop_subscription_columns', [ $this, 'marketking_admin_shop_subscription_edit_columns' ], 11 );
					add_action( 'manage_shop_subscription_posts_custom_column', [ $this, 'marketking_shop_subscription_custom_columns' ], 11 );
					
					// Add order filter by vendor in backend // also for verification requests
					add_action( 'restrict_manage_posts', [$this, 'display_admin_shop_order_by_meta_filter'] );
					add_filter( 'request', [$this, 'process_admin_shop_order_marketing_by_meta'], 99 );
					add_action( 'woocommerce_process_shop_order_meta', [ $this, 'save_product_meta' ], 12, 2 );

					/* Coupons */
					// Author column in backend
					add_filter( 'manage_edit-shop_coupon_columns', [ $this, 'marketking_admin_shop_coupon_edit_columns' ], 11 );
					add_action( 'manage_shop_coupon_posts_custom_column', [ $this, 'marketking_shop_coupon_custom_columns' ], 11 );

					// Add coupon filter by vendor in backend
					add_action( 'restrict_manage_posts', [$this, 'display_admin_shop_coupon_by_meta_filter'] );
					add_filter( 'request', [$this, 'process_admin_shop_coupon_marketing_by_meta'], 99 );
					add_action( 'woocommerce_process_shop_coupon_meta', [ $this, 'save_product_meta' ], 12, 2 );

					if(defined('MARKETKINGPRO_DIR')){
					    if (intval(get_option( 'marketking_enable_reviews_setting', 1 )) === 1){
							// Store Reviews
							add_action('admin_head', array($this,'marketking_reviews_page_prepare'));
							add_action( 'manage_comments_custom_column', [ $this, 'marketking_reviews_custom_column' ], 10, 2 );
						}
					}

					// lock order status for composite on order page backend (for clarit)
					add_filter('wc_order_statuses', array($this, 'lock_composite_status'), 10000, 1 );
					// add suffix to composite orders in backend for clarity
					add_filter( 'woocommerce_order_number', array($this,'add_suffix_composite_orders'), 1 );
										
				}
			});

		}
		
	}

	function marketking_reviews_custom_column($column, $comment_ID){
		if(get_current_screen()->id === 'marketking_page_marketking_reviews'){

			global $comment;
			switch ( $column ) :
				case 'rating' : {
					$rating = get_comment_meta($comment_ID,'rating', true);
					echo esc_html($rating).' ';
					if(intval($rating) === 1){
						esc_html_e('star','marketking-multivendor-marketplace-for-woocommerce');
					} else {
						esc_html_e('stars','marketking-multivendor-marketplace-for-woocommerce');
					}
					break;
				}
				case 'vendor' : {
					// try to print_r( $comment ); to see more comment information
					$product_id = $comment->comment_post_ID; // this will be printed inside the column
					$vendor_id = marketking()->get_product_vendor($product_id);
					$store_name = marketking()->get_store_name_display($vendor_id);
					$store_link = marketking()->get_store_link($vendor_id);
					echo '<a class="marketking_vendor_link" href="'.esc_attr($store_link).'">'.esc_html( $store_name ).'</a>';
					break;
				}
			endswitch;

		}
	}

	function add_suffix_composite_orders( $order_id ) {
		// get if customer is B2B
		$order = wc_get_order($order_id);
		if ($order){
			$is_composite = $order->get_meta('marketking_composite');

			$is_composite_page = false;
			if (isset($_GET['post_status'])){
				if ($_GET['post_status'] === 'wc-composite'){
					$is_composite_page = true;
				}
			}
			if ($is_composite === 'yes' && $is_composite_page === false){ // only show this notifier on the ALL page
				$suffix = ' ('.esc_html__('Composite Order','marketking-multivendor-marketplace-for-woocommerce').')';
				$order_id = $order_id . $suffix;
			}
		}
		
	            
		return $order_id;
	}

	// Admin order pages: order status dropdown
	function lock_composite_status( $order_statuses ) { 
	    global $post, $pagenow;

	    if( $pagenow === 'post.php') {
	        // Get ID
	        if (isset($_GET['post'])){
	        	$order_id = sanitize_text_field($_GET['post']);

	        	// Get an instance of the WC_Order object
	        	$order = wc_get_order( $order_id );
	        	// TRUE
	        	if ( $order ) { 
	        	    // Get current order status
	        	    $composite = $order->get_meta('marketking_composite');
	        	    $order_status = $order->get_status();

	        	    if ($order_status === 'composite'){
	        	    	// New order status
	        	    	$new_order_statuses = array();
	        	    	if ($composite === 'yes'){
	        	    		foreach ($order_statuses as $key => $option ) {
	        	    		    // Targeting "shop_manager"
	        	    		    if ($key === 'wc-composite'){
	        	    		        $new_order_statuses[$key] = $option;
	        	    		    }
	        	    		}

	        	    		if( sizeof($new_order_statuses) > 0 ) {
	        	    		    return $new_order_statuses;
	        	    		}
	        	    	}
	        	    }
	        	   

	        	    
	        	}
	        }
	        
	    }
	    return $order_statuses;
	}


	// Custom function where metakeys / labels pairs are defined
	function get_filter_shop_order_vendors_list(){

		global $user_ID;
		$admin_user = get_user_by( 'id', $user_ID );
		$vendorsarr = array($admin_user->ID=>$admin_user->display_name);

		$vendors = marketking()->get_all_vendors();
		foreach ($vendors as $vendor){
			$vendorsarr[$vendor->ID] = marketking()->get_store_name_display($vendor->ID);
		}

	    // Add below the metakey / label pairs to filter orders
	    return $vendorsarr;
	}

	// Add a dropdown to filter orders by meta
	function display_admin_shop_order_by_meta_filter(){
	    global $pagenow, $typenow;

	    if( 'shop_order' === $typenow && 'edit.php' === $pagenow || 'marketking_vreq' === $typenow && 'edit.php' === $pagenow) {
	        $filter_id = 'filter_shop_order_by_vendor';
	        $current   = isset($_GET[$filter_id])? sanitize_text_field($_GET[$filter_id]) : '';

	        echo '<select name="'.esc_html($filter_id).'">
	        <option value="">' . esc_html__('Filter by vendor...', 'marketking-multivendor-marketplace-for-woocommerce') . '</option>';

	        $options = $this->get_filter_shop_order_vendors_list( 'marketking-multivendor-marketplace-for-woocommerce' );

	        foreach ( $options as $key => $label ) {
	            printf( '<option value="%s"%s>%s</option>', $key, 
	                $key === $current ? '" selected="selected"' : '', $label );
	        }
	        echo '</select>';
	    }
	}

	// Process the filter dropdown for orders by MarketKing
	function process_admin_shop_order_marketing_by_meta( $vars ) {
	    global $pagenow, $typenow;
	    
	    $filter_id = 'filter_shop_order_by_vendor';

	    if ( $pagenow === 'edit.php' && 'shop_order' === $typenow && isset( $_GET[$filter_id] ) && ! empty($_GET[$filter_id]) ) {
	        $vars['author']   = sanitize_text_field($_GET[$filter_id]);
	    }

	    if ( $pagenow === 'edit.php' && 'marketking_vreq' === $typenow && isset( $_GET[$filter_id] ) && ! empty($_GET[$filter_id]) ) {
	        $vars['author']   = sanitize_text_field($_GET[$filter_id]);
	    }

	    return $vars;
	}

	// Add a dropdown to filter coupons by meta
	function display_admin_shop_coupon_by_meta_filter(){
	    global $pagenow, $typenow;

	    if( 'shop_coupon' === $typenow && 'edit.php' === $pagenow ) {
	        $filter_id = 'filter_shop_coupon_by_vendor';
	        $current   = isset($_GET[$filter_id])? sanitize_text_field($_GET[$filter_id]) : '';

	        echo '<select name="'.esc_html($filter_id).'">
	        <option value="">' . esc_html__('Filter by vendor...', 'marketking-multivendor-marketplace-for-woocommerce') . '</option>';

	        $options = $this->get_filter_shop_order_vendors_list( 'marketking-multivendor-marketplace-for-woocommerce' );

	        foreach ( $options as $key => $label ) {
	            printf( '<option value="%s"%s>%s</option>', $key, 
	                $key === $current ? '" selected="selected"' : '', $label );
	        }
	        echo '</select>';
	    }
	}

	// Process the filter dropdown for coupons by MarketKing
	function process_admin_shop_coupon_marketing_by_meta( $vars ) {
	    global $pagenow, $typenow;
	    
	    $filter_id = 'filter_shop_coupon_by_vendor';

	    if ( $pagenow === 'edit.php' && 'shop_coupon' === $typenow 
	    && isset( $_GET[$filter_id] ) && ! empty($_GET[$filter_id]) ) {
	        $vars['author']   = sanitize_text_field($_GET[$filter_id]);
	    }
	    return $vars;
	}


	function save_product_meta( $product_id, $post ) {

        if (isset($_POST['marketking_set_product_author'])){
        	$author_id = sanitize_text_field($_POST['marketking_set_product_author']);
        } else {
        	// cancel
        	return;
        }

		wp_update_post(
		   array(
				'ID'          => $product_id,
				'post_author' => $author_id,
		   )
		);

		// if not product, abort
		if (!wc_get_product($product_id)){
			return;
		}

		// if variable product, must update children. If this is child, check parent.
		$productobjj = wc_get_product($product_id);
		$children_ids = $productobjj->get_children();
		foreach ($children_ids as $child_id){
			wp_update_post(
			   array(
					'ID'          => $child_id,
					'post_author' => $author_id,
			   )
			);
		}
		$possible_parent_id = wp_get_post_parent_id($product_id);
		if ($possible_parent_id !== 0){
			$parent_author = get_post_field( 'post_author', $possible_parent_id );
			wp_update_post(
			   array(
					'ID'          => $product_id,
					'post_author' => $parent_author,
			   )
			);
		}


		if (isset($_POST['marketking_other_product_sellers_dummy'])){
			$dummy = $_POST['marketking_other_product_sellers_dummy'];
		} else {
			$dummy = 0;
		}

		if(isset($_POST['marketking_other_product_sellers']) or $dummy !== 0){
			$linkedproducts = marketking()->get_linkedproducts($product_id,'array');
			if (isset($_POST['marketking_other_product_sellers'])){
				$sellersarray = $_POST['marketking_other_product_sellers'];
			} else {
				$sellersarray = array();
			}
			// check if saved sellers are different than existing sellers (e.g. if anything changed)

			foreach($sellersarray as $vendor_id){
				// added new vendor
				// check if vendor does not already exist. If not exists, add it
				$existing_vendors = array();
				foreach ($linkedproducts as $linkedproduct_id){
					$linkedproductvendor_id = marketking()->get_product_vendor($linkedproduct_id);
					array_push($existing_vendors, $linkedproductvendor_id);
				}
				if (!in_array($vendor_id, $existing_vendors)){
					$admin = new WC_Admin_Duplicate_Product;
					$product = wc_get_product($product_id);
					$duplicate = $admin->product_duplicate( $product );
					$duplicate->set_name( $product->get_name() );
					$duplicate->set_status( 'publish' );
					$duplicate->save();

					wp_update_post(
					   array(
							'ID'          => $duplicate->get_id(),
							'post_author' => $vendor_id,
					   )
					);

					marketking()->set_new_linkedproduct($product_id, $duplicate->get_id());
				}
			}

			foreach ($linkedproducts as $linkedproduct_id){
				$linkedproductvendor_id = marketking()->get_product_vendor($linkedproduct_id);
				// if vendor does not exist in saved vendors, it means it was deleted, and we must delete the product and update lists
				if(!in_array($linkedproductvendor_id, $sellersarray)){
					wp_delete_post($linkedproduct_id, true);
				}
			}
		}
    }

	public function change_meta_box_titles() {
	    remove_meta_box( 'authordiv', 'product', 'normal' );

	    $tipproduct = esc_html__('Here you can change the vendor of this product.','marketking-multivendor-marketplace-for-woocommerce');

	    add_meta_box( 'vendordiv', esc_html__( 'Product Vendor', 'marketking-multivendor-marketplace-for-woocommerce' ), [ self::class, 'seller_meta_box_content' ], 'product', 'normal', 'core' );

	    if (intval(get_option('marketking_enable_advertising_setting', 0)) === 1){
	    	add_meta_box( 'advertisement', esc_html__( 'Product Advertising', 'marketking-multivendor-marketplace-for-woocommerce' ), [ self::class, 'product_advertising_content' ], 'product', 'normal', 'core' );

	    }

	    if (apply_filters('marketking_allow_change_order_vendor', false)){

	    	remove_meta_box( 'authordiv', 'shop_order', 'normal' );

	    	$tipproduct = esc_html__('Here you can change the vendor of this order.','marketking-multivendor-marketplace-for-woocommerce');

	    	add_meta_box( 'vendordiv', esc_html__( 'Order Vendor', 'marketking-multivendor-marketplace-for-woocommerce' ), [ self::class, 'seller_meta_box_content' ], 'shop_order', 'normal', 'core' );
	    }

	    // add admin shipping tracking box
	    if (defined('MARKETKINGPRO_DIR')){
	        if (intval(get_option('marketking_enable_shippingtracking_setting', 1)) === 1){

			    add_meta_box(
			    	'marketking_shipping_tracking_box',
			    	esc_html__( 'Shipping Tracking', 'marketking-multivendor-marketplace-for-woocommerce' ),
			    	array( $this, 'shipping_tracking_box' ),
			    	'shop_order',
			    	'side',
			    	'default'
			    );
			}
		}

	}

	public function shipping_tracking_box($post_or_order_object){

		$order = ( $post_or_order_object instanceof \WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
		$order_id = $order->get_id();

		// SHIPPING TRACKING
		if (defined('MARKETKINGPRO_DIR')){
		    if (intval(get_option('marketking_enable_shippingtracking_setting', 1)) === 1){
		        ?>
		        <div class="card-inner">
		            <?php
		            // if order already has shipment, show shipping history
		            $shipping_history = get_post_meta($order_id,'marketking_shipment_history', true);
		            $providers = marketkingpro()->get_tracking_providers();
		            $selectedproviders = get_option('marketking_shipping_providers_setting',array('sp-other'));

		            if (empty($providers)){
		                $providers = array();
		            }
		            if (empty($selectedproviders)){
		                $selectedproviders = array();
		            }

		            if (!empty($shipping_history)){
		                ?>
		                    <?php
		                // show packages
		                foreach ($shipping_history as $shipment){
		                    esc_html_e('Shipment via ','marketking-multivendor-marketplace-for-woocommerce');

		                    $providername = $providers[$shipment['provider']]['label'];
		                    if ($shipment['provider'] === 'sp-other'){
		                        $providername = $shipment['providername'];
		                    }
		                    echo $providername.': <a href="'.esc_url($shipment['trackingurl']).'">'.esc_html($shipment['trackingnr']).'</a><br>';

		                }

		                // show button with ' add new shipment '
		                ?>
		                <br><button class="btn btn-sm btn-gray button-secondary button" id="marketking_add_another_shipment_button" value="<?php echo esc_attr($order_id);?>"><?php esc_html_e('Add another','marketking-multivendor-marketplace-for-woocommerce');?></button>
		                <?php
		            }

		            ?>
		            <div class="row gy-3 <?php if (!empty($shipping_history)){ echo 'marketking_new_shipment_hidden'; }?>">
		                <div class="col-sm-12 marketking_shipping_tracking_container">
		                    <div class="form-group">
		                        <label class="form-label" for="default-06"><?php esc_html_e('Create Shipment','marketking-multivendor-marketplace-for-woocommerce');?></label>
		                        <div class="form-control-wrap ">
		                            <div class="form-control-select">
		                                <select class="form-control" id="marketking_create_shipment_provider">
		                                    <?php
		                                    foreach ($providers as $slug => $provider){
		                                        if (in_array($slug,$selectedproviders)){
		                                            ?>
		                                            <option value="<?php echo esc_attr($slug); ?>"><?php echo esc_html($provider['label']); ?></option>
		                                            <?php
		                                            }
		                                    } 
		                                    ?>
		                                </select>
		                            </div>
		                        </div>
		                    </div>
		                </div>
		                <div class="col-sm-12 marketking_create_shipment_other marketking_shipping_tracking_container">
		                    <div class="form-group">
		                        <label class="form-label" for="default-01"><?php esc_html_e('Provider Name','marketking-multivendor-marketplace-for-woocommerce');?></label>
		                        <div class="form-control-wrap">
		                            <input type="text" class="form-control" id="marketking_create_shipment_provider_name" placeholder="<?php esc_html_e('Enter the shipping provider name','marketking-multivendor-marketplace-for-woocommerce');?>">
		                        </div>
		                    </div>
		                </div>
		                <div class="col-sm-12 marketking_shipping_tracking_container">
		                    <div class="form-group">
		                        <label class="form-label" for="default-01"><?php esc_html_e('Tracking Number','marketking-multivendor-marketplace-for-woocommerce');?></label>
		                        <div class="form-control-wrap">
		                            <input type="text" class="form-control" id="marketking_create_shipment_tracking_number" placeholder="<?php esc_html_e('Enter the tracking number','marketking-multivendor-marketplace-for-woocommerce');?>">
		                        </div>
		                    </div>
		                </div>
		                <div class="col-sm-12 marketking_create_shipment_other marketking_shipping_tracking_container">
		                    <div class="form-group">
		                        <label class="form-label" for="default-01"><?php esc_html_e('Tracking URL','marketking-multivendor-marketplace-for-woocommerce');?></label>
		                        <div class="form-control-wrap">
		                            <input type="text" class="form-control" id="marketking_create_shipment_tracking_url" placeholder="<?php esc_html_e('Enter the tracking URL','marketking-multivendor-marketplace-for-woocommerce');?>">
		                        </div>
		                    </div>
		                </div>
		                <div class="col-sm-12">
		                    <button class="btn btn-sm btn-secondary button button-primary" type="button" id="marketking_create_shipment_button" value="<?php echo esc_attr($order_id);?>"><?php esc_html_e('Create shipment','marketking-multivendor-marketplace-for-woocommerce');?></button>
		                </div>
		            </div>
		        </div>
		        <?php
		    }
		}

	}

	public static function product_advertising_content( $post ) {
    	$product_id = $post->ID;
        if (marketking()->is_advertised($product_id)){
            $daysleft = marketking()->get_ad_days_left($product_id);
            echo '<br>'.esc_html__('This product is already advertised:','marketking-multivendor-marketplace-for-woocommerce').' '.$daysleft.' '.esc_html__('days left','marketking-multivendor-marketplace-for-woocommerce'); 
            
        } else {
            echo '<br>'.esc_html__('This product is not advertised','marketking-multivendor-marketplace-for-woocommerce');
        }

        // advertise by admin
        ?>
        <h3><?php esc_html_e('Advertise','marketking-multivendor-marketplace-for-woocommerce'); ?></h3>
        <input type="number" min="1" step="1" class="advertising_days_input" placeholder="<?php esc_attr_e('Number of advertising days','marketking-multivendor-marketplace-for-woocommerce');?>">
        <button type="button" class="button button-primary" id="marketking_advertise_admin"><?php esc_html_e('Advertise','marketking-multivendor-marketplace-for-woocommerce');?></button>
        <button type="button" class="button" id="marketking_remove_advertise_admin"><?php esc_html_e('Remove advertisement','marketking-multivendor-marketplace-for-woocommerce');?></button>

        <?php
	}


	public static function seller_meta_box_content( $post ) {

		$admin_user_id = apply_filters('marketking_admin_user_id', 1);
        $admin_user = get_user_by( 'id', $admin_user_id );

        $selected   = empty( $post->ID ) ? $admin_user_id : $post->post_author;
        $vendors = marketking()->get_all_vendors();

        ?>
        <label class="screen-reader-text" for="marketking_set_product_author"><?php esc_html_e( 'Vendor', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></label>
        <select name="marketking_set_product_author" id="marketking_set_product_author" class="">
            <?php if ( empty( $vendors ) ) { ?>
                <option value="<?php echo esc_attr( $admin_user_id ); ?>"><?php echo esc_html( marketking()->get_store_name_display($admin_user_id) ); ?></option>
            <?php } else { ?>
                <option value="<?php echo esc_attr( $admin_user_id ); ?>" <?php selected( $selected, $admin_user_id ); ?>><?php echo esc_html( marketking()->get_store_name_display($admin_user_id) ); ?></option>
                <?php foreach ( $vendors as $vendor ) { ?>
                    <option value="<?php echo esc_attr( $vendor->ID ); ?>" <?php selected( $selected, $vendor->ID ); ?>><?php
	                    $store_name = marketking()->get_store_name_display($vendor->ID);
	                    echo esc_html($store_name);
	                    
                    ?></option>
                <?php } 
            	} 
            ?>
        </select>

        <?php

        // Single Product Multiple Vendors
        if(defined('MARKETKINGPRO_DIR') && wc_get_product($post->ID)){
        	if(intval(get_option('marketking_enable_spmv_setting', 1)) === 1){
        		// show other vendors selling this item
        		?>
        		<br>
        		<h4><?php esc_html_e('Other Vendors Selling This Product','marketking-multivendor-marketplace-for-woocommerce');?></h4>
        		<input name="marketking_other_product_sellers_dummy" type="hidden" value="1">
        		<select name="marketking_other_product_sellers[]" id="marketking_other_product_sellers" class="" multiple>
        			<?php
        			$allvendors = marketking()->get_all_vendors();
        			$otherproducts = marketking()->get_linkedproducts($post->ID,'array');

        			// build array of othervendors
        			$othervendors = array();
        			foreach ($otherproducts as $productid){
        				array_push($othervendors, marketking()->get_product_vendor($productid));
        			}
        			foreach ($allvendors as $vendor){
        				// not show current vendor
        				if($vendor->ID !== intval($post->post_author)){
        					$storename = marketking()->get_store_name_display($vendor->ID);
        					?>
        					<option value="<?php echo esc_attr($vendor->ID);?>" name="<?php echo esc_attr($vendor->ID);?>" <?php if(in_array($vendor->ID, $othervendors)){echo 'selected="selected"';} ?>>
        						<?php echo esc_html($storename);?>
        					</option>
        					<?php
        				}
        				
        			}
        			?>
				</select>
        		<?php
        	}
        }
        ?>
         <?php
    }

    function marketking_product_custom_columns( $col ) {
    	global $post;
	    if ($col === 'links'){
	    	$vendor_id = get_post_field( 'post_author', $post->ID );

	    	$vendor_name = marketking()->get_store_name_display($vendor_id);

	    	echo '<a href="'.get_edit_user_link($vendor_id).'">'.esc_html($vendor_name).'</a><br><br>';

	    }

	    if ($col === 'ads'){
	    	$product_id = $post->ID;
            if (marketking()->is_advertised($product_id)){
                $daysleft = marketking()->get_ad_days_left($product_id);
                echo '<div class="marketking_advertised_column">'.$daysleft.' '.esc_html__('days left','marketking-multivendor-marketplace-for-woocommerce').'</div>';
                
            } else {
                echo '-';
            }
	    }
	    
        
	}

    function marketking_shop_coupon_custom_columns( $col ) {
    	
		require_once ( MARKETKINGCORE_DIR . 'includes/class-marketking-core-helper.php' );
	    global $post, $the_coupon;

	    if ( ! current_user_can( 'manage_woocommerce' ) ) {
	        return;
	    }

	    if ( ! in_array( $col, [ 'vendor' ], true ) ) {
	        return;
	    }

	    if (is_object($the_coupon)){
	    	$coupon_id = $the_coupon->get_id();
	    }

	    if ($col === 'vendor'){
	    	$vendor_id = get_post_field( 'post_author', $coupon_id );
	    	$vendor_name = marketking()->get_store_name_display($vendor_id);

        	$output = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=shop_coupon&author=' . $vendor_id ) ), esc_html( $vendor_name ) );
	    }
	    
        echo $output;
        
	}


	function marketking_shop_order_custom_columns( $col ) {
		require_once ( MARKETKINGCORE_DIR . 'includes/class-marketking-core-helper.php' );
	    global $post, $the_order;

	    if ( empty( $the_order ) || $the_order->get_id() !== $post->ID ) {
	        $the_order = new \WC_Order( $post->ID );
	    }

	    $order_id = $the_order->get_id();

	    if ( ! current_user_can( 'manage_woocommerce' ) ) {
	        return;
	    }

	    if ( ! in_array( $col, [ 'vendor','suborder' ,'earnings','received'], true ) ) {
	        return;
	    }

	    if ($col === 'vendor'){
	    	$vendor_id = get_post_field( 'post_author', $order_id );
	    	$vendor_name = marketking()->get_store_name_display($vendor_id);

        	$output = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=shop_order&author=' . $vendor_id ) ), esc_html( $vendor_name ) );
	    }

	    if ($col === 'suborder'){
	    	$parent = $the_order->get_parent_id();
	    	if (intval($parent) === 0){
	    		$output = '—';
	    	} else {

	    		// sequential
	    		$order_id = $parent;

	    		$order = wc_get_order($order_id);
	    		$order_nr_sequential = $order->get_meta('_order_number');

	    		if (!empty($order_nr_sequential)){
	    			$order_id = $order_nr_sequential;
	    		}

	    		$output = '<a href="'.esc_attr(get_edit_post_link($parent)).'">'.esc_html__('Composite order ','marketking-multivendor-marketplace-for-woocommerce').'#'.esc_html($order_id).'</a>';
	    	}

	    }

	    if ($col === 'earnings'){
	    	$earnings = ($the_order->get_total()-marketking()->get_order_earnings($order_id));
	    	if (floatval($earnings) === 0){
	    	    $output = '—';
	    	} else {
	    	    $output = wc_price($earnings, array('currency' => $the_order->get_currency()));
	    	}
	    }

	    if ($col === 'received'){
	    	$order = wc_get_order($order_id);
	    	$received = $order->get_meta('marked_received');

	    	if ($received === 'yes'){
	    		$output = '<span class="dashicons dashicons-yes-alt"></span>';
	    	} else {
	    	    $output = '—';
	    	}
	    }

	    
        echo $output;

	}

	public function marketking_admin_product_edit_columns( $columns ) {

		unset($columns['author']);
		// links name ensures proper formatting, do not change

        // advertisement
        if (intval(get_option('marketking_enable_advertising_setting', 0)) === 1){
        	$columns['ads'] = esc_html__( 'Advertisement', 'marketking-multivendor-marketplace-for-woocommerce' );
        }

        $columns['links'] = esc_html__( 'Vendor', 'marketking-multivendor-marketplace-for-woocommerce' );


        return apply_filters( 'marketking_edit_product_columns', $columns );

	}

	public function marketking_admin_shop_order_edit_columns( $existing_columns ) {

		// disable for wc composite
		$disable = 'no';
		if (isset($_GET['post_status'])){
			if ($_GET['post_status'] === 'wc-composite'){
				$disable = 'yes';
			}
		}

		$columns = $existing_columns;
		if ($disable === 'no'){
			$tip = esc_html__('This represents the admin commission (earnings) for the order.','marketking-multivendor-marketplace-for-woocommerce');

			$columns = array_slice( $existing_columns, 0, count( $existing_columns ), true ) +
			    array(
			        'earnings'     => esc_html__( 'Admin Commission', 'marketking-multivendor-marketplace-for-woocommerce' ).' '.wc_help_tip($tip, false),
			        'vendor'     => esc_html__( 'Vendor', 'marketking-multivendor-marketplace-for-woocommerce' ),
			        'suborder'   => esc_html__( 'Sub-order of', 'marketking-multivendor-marketplace-for-woocommerce' ),
			    )
			    + array_slice( $existing_columns, count( $existing_columns ), count( $existing_columns ) - 1, true );

		    // Shipping tracking show backend admin that order has been received
			if(defined('MARKETKINGPRO_DIR')){
			    if (intval(get_option('marketking_enable_shippingtracking_setting', 1)) === 1) {  
			    	if (intval(get_option( 'marketking_customers_mark_order_received_setting', 0 )) === 1){
			    		if (apply_filters('marketking_show_column_receipt_confirmed_order', true)){
			    			$new_existing_columns = $columns;

			    			$tip = esc_html__('The customer has confirmed that they received this order.','marketking-multivendor-marketplace-for-woocommerce');

			    			$columns = array_slice( $new_existing_columns, 0, count( $new_existing_columns )-5, true ) +
			    			    array(
			    			        'received'   => esc_html__( 'Received', 'marketking-multivendor-marketplace-for-woocommerce' ).' '.wc_help_tip($tip, false),
			    			    )
			    			    + array_slice( $new_existing_columns, count( $new_existing_columns )-5, count( $new_existing_columns ) - 1, true );

			    		}
			    		
			    	}
			    }
			}
		}
		

        return apply_filters( 'marketking_edit_shop_order_columns', $columns );

	}
	public function marketking_admin_shop_subscription_edit_columns( $existing_columns ) {

		$columns = array_slice( $existing_columns, 0, count( $existing_columns ), true ) +
		    array(
		        'vendor'     => esc_html__( 'Vendor', 'marketking-multivendor-marketplace-for-woocommerce' ),
		    )
		    + array_slice( $existing_columns, count( $existing_columns ), count( $existing_columns ) - 1, true );

        return apply_filters( 'marketking_edit_shop_order_columns', $columns );

	}

	function marketking_shop_subscription_custom_columns( $col ) {
		require_once ( MARKETKINGCORE_DIR . 'includes/class-marketking-core-helper.php' );
	    global $post, $the_order;

	    if ( empty( $the_order ) || $the_order->get_id() !== $post->ID ) {
	        $the_order = new \WC_Order( $post->ID );
	    }

	    $order_id = $the_order->get_id();

	    if ( ! current_user_can( 'manage_woocommerce' ) ) {
	        return;
	    }

	    if ( ! in_array( $col, [ 'vendor','suborder' ,'earnings','received'], true ) ) {
	        return;
	    }

	    if ($col === 'vendor'){
	    	$vendor_id = get_post_field( 'post_author', $order_id );
	    	$vendor_name = marketking()->get_store_name_display($vendor_id);

        	$output = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=shop_order&author=' . $vendor_id ) ), esc_html( $vendor_name ) );
	    }

	    
        echo $output;
	}

	public function marketking_admin_shop_coupon_edit_columns( $existing_columns ) {

        $columns = array_slice( $existing_columns, 0, count( $existing_columns ), true ) +
            array(
                'vendor'     => esc_html__( 'Vendor', 'marketking-multivendor-marketplace-for-woocommerce' ),
            )
            + array_slice( $existing_columns, count( $existing_columns ), count( $existing_columns ) - 1, true );

        return apply_filters( 'marketking_edit_shop_coupon_columns', $columns );

	}

	function marketking_products_add_author_column() {
		add_post_type_support( 'product', 'author' );

		if (apply_filters('marketking_allow_change_order_vendor', false)){
			add_post_type_support( 'shop_order', 'author' );
		}
		
	}

	function marketking_show_user_meta_profile_registration($user){
		if (isset($user->ID)){
			$user_id = $user->ID;
		} else {
			$user_id = 0;
		}

		    // Only show B2B Enabled and User customer group if user account is not in approval process
		    // Also don't show for subaccounts

		    // check this is not "new panel"
		    if (isset($user->ID)){
		    	$account_type = get_user_meta($user->ID, 'marketking_account_type', true);
		    	if ($account_type === 'subaccount'){
		    		esc_html_e('This account is a subaccount. Its parent account is: ', 'marketking-multivendor-marketplace-for-woocommerce');
		    		$parent_account = get_user_meta($user->ID, 'marketking_account_parent', true);
		    		$parent_user = get_user_by('id', $parent_account);
		    		echo esc_html($parent_user->user_login);
		    	}

		    	$user_approved = get_user_meta($user->ID, 'marketking_account_approved', true);
		    } else {
		    	$user_approved = 'newuser';
		    	$account_type =  'newuser';
		    	$user = (object) [
		    	    "ID" => "-2",
		    	];
		    }
		   
	
		    // show all custom user data gathered on registration (registration role + fields) 
		    $custom_fields = get_user_meta($user->ID, 'marketking_fields_string', true);
		    $custom_fields_string_received = $custom_fields;
		    $editable_added = array();


	        // if this is not the add new user panel
	        $account_approved = '';
	        if( get_current_screen()->action !== 'add'){
	    	    $account_approved = get_user_meta($user->ID, 'marketking_account_approved', true);
	    	}

	    	// add editable fields
	   		$custom_fields_editable = get_posts([
    			    		'post_type' => 'marketking_field',
    			    	  	'post_status' => 'publish',
    			    	  	'numberposts' => -1,
    			    	  	'meta_key' => 'marketking_field_sort_number',
    		    	  	    'orderby' => 'meta_value_num',
    		    	  	    'order' => 'ASC',
    		    	  	    'fields' => 'ids',
    			    	  	'meta_query'=> array(
    			    	  		'relation' => 'AND',
    			                array(
    		                        'key' => 'marketking_field_status',
    		                        'value' => 1
    			                ),
    		            	)
    			    	]);
    		$custom_fields = '';
    		$custom_fields_array_exploded = array();

    		
    		foreach ($custom_fields_editable as $editable_field){
    			if (!in_array($editable_field, $custom_fields_array_exploded)){

    				// if account not approved, don't show fields the user didnt add in registration
    				if ($account_approved === 'no'){
    					// check if user has field
    					$value = get_user_meta($user->ID, 'marketking_field_'.$editable_field, true);
    					if (empty($value)){
    						continue;
    					}
    				}

    				// don't show files
    				$afield_type = get_post_meta($editable_field, 'marketking_field_field_type', true);
    				$afield_billing_connection = get_post_meta($editable_field, 'marketking_field_billing_connection', true);
    				if ($afield_type === 'file'){
    					$custom_fields_string_received_array = explode(',',$custom_fields_string_received);
    					if (!in_array($editable_field, $custom_fields_string_received_array)){
    						continue;
    					}

    					// if empty, skip
    					$value = get_user_meta($user->ID, 'marketking_field_'.$editable_field, true);
    					if (empty($value)){
    						continue;
    					}
    				}
    				if ($afield_billing_connection !== 'billing_vat' && $afield_billing_connection !== 'none' && $afield_billing_connection !== 'custom_mapping'){
    					continue;
    				}


    				$custom_fields .= $editable_field.',';
    				array_push($editable_added,$editable_field);
    			}
    		}

		    ?>
		    <input type="hidden" id="marketking_admin_user_fields_string" value="<?php echo esc_attr($custom_fields);?>">
		    <?php

		    // if this is not the add new user panel
		    if( get_current_screen()->action !== 'add'){
			    $registration_option = get_user_meta($user->ID, 'marketking_registration_option', true);
			    $account_approved = get_user_meta($user->ID, 'marketking_account_approved', true);

			    // show this panel if user 1) has custom fields OR 2) manual user approval is needed OR 3) there is a chosen registration role
			    if((trim($custom_fields) !== '' && $custom_fields !== NULL) || ($registration_option !== NULL && $registration_option !== '' && $registration_option !== false) || ($account_approved === 'no') ){

			    	?>
			    	
			    	<div id="marketking_registration_data_container" class="marketking_user_shipping_payment_methods_container">
			    		<div class="marketking_user_shipping_payment_methods_container_top">
			    			<div class="marketking_user_shipping_payment_methods_container_top_title">
			    				<?php esc_html_e('Vendor Registration Data','marketking-multivendor-marketplace-for-woocommerce'); ?>
			    			</div>		
			    		</div>

			    		<?php

					    // if there are custom fields or registration role, show 'Data collected at registration' (there may be no fields, only a need for approval)
			    		if((trim($custom_fields) !== '' && $custom_fields !== NULL) || ($registration_option !== NULL && $registration_option !== '') || ($account_approved === 'no')){
			    			// show header
			    			?>
			    			<div class="marketking_user_registration_user_data_container">
			    				<div class="marketking_user_registration_user_data_container_title">
			    					<svg class="marketking_user_registration_user_data_container_title_icon" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="none" viewBox="0 0 35 35">
			    					  <path fill="#C4C4C4" d="M29.531 0H3.281A3.29 3.29 0 000 3.281V31.72A3.29 3.29 0 003.281 35h26.25a3.29 3.29 0 003.282-3.281V3.28A3.29 3.29 0 0029.53 0zm-1.093 30.625H4.375V4.375h24.063v26.25zM8.75 15.312h15.313V17.5H8.75v-2.188zm0 4.376h15.313v2.187H8.75v-2.188zm0 4.375h15.313v2.187H8.75v-2.188zm0-13.125h15.313v2.187H8.75v-2.188z"/>
			    					</svg>
			    					<?php esc_html_e('Data Collected at Registration','marketking-multivendor-marketplace-for-woocommerce'); ?>
			    				</div>

			    				<?php
			    				if ($registration_option !== NULL && $registration_option !== '' && $registration_option !== false){
			    					$role_name = get_the_title(explode('_',$registration_option)[1]);
			    					?>
			    					<div class="marketking_user_registration_user_data_container_element">
			    						<div class="marketking_user_registration_user_data_container_element_label">
			    							<?php esc_html_e('Registration Option','marketking-multivendor-marketplace-for-woocommerce'); ?>
			    						</div>
			    						<input type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($role_name); ?>" readonly>
			    					</div>
			    					<?php
			    				}

			    				if((trim($custom_fields) !== '' && $custom_fields !== NULL)){
			    					$custom_fields_array = explode(',', $custom_fields);
			    					foreach ($custom_fields_array as $field){
			    						if ($field !== '' && $field !== NULL){
			    							// get field data
			    							$field_value = get_user_meta($user->ID, 'marketking_field_'.$field, true);
			    							$field_label = get_post_meta($field, 'marketking_field_field_label', true);

			    							$field_type = get_post_meta($field, 'marketking_field_field_type', true);
			    							$field_billing_connection = get_post_meta($field, 'marketking_field_billing_connection', true);

			    							// display checkboxes
			    							if ($field_value !== '' && $field_value !== NULL && $field_type === 'checkbox'){

			    								?>
			    								<div class="marketking_user_registration_user_data_container_element">
			    									<div class="marketking_user_registration_user_data_container_element_label">
			    										<?php echo esc_html($field_label); ?>
			    									</div>
			    								<?php

		    									$select_options = get_post_meta($field, 'marketking_field_user_choices', true);
		    									$select_options = explode(',', $select_options);
		    									$i = 1;
		    									foreach ($select_options as $option){
		    										// get field and check if set
		    										$field_value_second = get_user_meta($user->ID, 'marketking_field_'.$field.'_option_'.$i, true);

		    										if ($field_value_second !== NULL && $field_value_second !== ''){
		    											// field is set, display it
		    											?>
		    											<input name="marketking_field_<?php echo esc_attr($field);?>" type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php echo esc_attr(strip_tags($field_value_second)); ?>" >
		    											<?php
		    										}
		    										$i++;
		    									}
		    									?>
		    									</div>
		    									<?php
			    							}
			    							// display other fields
			    							if (($field_value !== '' && $field_value !== NULL && $field_type !== 'checkbox') || (in_array($field, $editable_added)&& $field_type !== 'checkbox')){
			    								?>
			    								<div class="marketking_user_registration_user_data_container_element">
			    									<div class="marketking_user_registration_user_data_container_element_label">
			    										<?php echo esc_html($field_label); ?>
			    									</div>
			    									<?php

			    									if ($field_type !== 'textarea' && $field_type !== 'file'){
			    										?>
			    										<input name="marketking_field_<?php echo esc_attr($field);?>" type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($field_value); ?>" >
			    										<?php
			    									} else if ($field_type === 'textarea'){
			    										?>
			    										<textarea name="marketking_field_<?php echo esc_attr($field);?>" class="marketking_user_registration_user_data_container_element_textarea" ><?php echo esc_html($field_value); ?></textarea>
			    										<?php

			    									} else if ($field_type === 'file'){
			    										if (!is_wp_error($field_value)){
			    										?>
			    										<button class="marketking_user_registration_user_data_container_element_download" value="<?php echo esc_attr($field_value); ?>" type="button">
			    											<svg class="marketking_user_registration_user_data_container_element_download_icon" xmlns="http://www.w3.org/2000/svg" width="37" height="37" fill="none" viewBox="0 0 37 37">
			    											  <path fill="#fff" d="M22.547 25.52h-2.678v-8.754a.29.29 0 00-.289-.29h-2.168a.29.29 0 00-.289.29v8.755h-2.67a.288.288 0 00-.227.466l4.046 5.12a.289.289 0 00.456 0l4.046-5.12a.288.288 0 00-.227-.466z"/>
			    											  <path fill="#fff" d="M29.318 13.25c-1.655-4.365-5.871-7.469-10.81-7.469-4.94 0-9.157 3.1-10.812 7.465a7.23 7.23 0 00-5.383 6.988 7.224 7.224 0 007.222 7.227h1.45a.29.29 0 00.288-.29v-2.167a.29.29 0 00-.289-.29H9.535a4.454 4.454 0 01-3.215-1.361 4.478 4.478 0 01-1.261-3.274c.032-.954.357-1.85.946-2.605a4.528 4.528 0 012.389-1.58l1.37-.357.501-1.322a8.874 8.874 0 013.183-4.094 8.748 8.748 0 015.06-1.597c1.824 0 3.573.553 5.058 1.597a8.88 8.88 0 013.183 4.094l.499 1.319 1.366.36a4.5 4.5 0 013.327 4.34 4.455 4.455 0 01-1.311 3.17 4.446 4.446 0 01-3.165 1.31h-1.45a.29.29 0 00-.288.29v2.168c0 .159.13.289.289.289h1.449a7.224 7.224 0 007.223-7.227c0-3.35-2.28-6.168-5.37-6.984z"/>
			    											</svg>
			    											<?php esc_html_e('Download file','marketking-multivendor-marketplace-for-woocommerce'); ?>
			    										</button>
			    										<?php
			    										} else {
			    											// error
			    											?>
			    											<input type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php esc_html_e('The file did not upload correctly','marketking-multivendor-marketplace-for-woocommerce'); ?>" readonly>
			    											<?php
			    										}
			    									}

			    									?>
			    								</div>
			    								<?php
			    								// if field is billing_countrystate (country + state combined), show state as well
			    								if ($field_billing_connection === 'billing_countrystate'){
			    									$state_label = esc_html__('State', 'marketking-multivendor-marketplace-for-woocommerce');
			    									$state_value = get_user_meta($user->ID, 'billing_state', true);
			    									if ($state_value !== NULL && $state_value !== ''){
				    									?>
				    									<div class="marketking_user_registration_user_data_container_element">
				    										<div class="marketking_user_registration_user_data_container_element_label">
				    											<?php echo esc_html($state_label); ?>
				    										</div>
				    										<input type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($state_value); ?>" >
				    									</div>
				    									<?php
			    									}
			    								}
			    								?>
			    								<?php
			    							}
			    						} 
			    					}
								}

								if ($account_approved === 'no'){
									?>
									<div class="marketking_user_registration_user_data_container_element">
										<div class="marketking_user_registration_user_data_container_element_label">
											<?php esc_html_e('Registration Approval','marketking-multivendor-marketplace-for-woocommerce'); ?>
										</div>
										<div class="marketking_user_registration_user_data_container_element_approval">
											<?php do_action('marketking_before_registration_approval');?>
											<select class="marketking_user_registration_user_data_container_element_select_group">
												<?php
												$groups = get_posts([
												  'post_type' => 'marketking_group',
												  'post_status' => 'publish',
												  'numberposts' => -1
												]);
												$automatic_approval_default = get_user_meta($user->ID, 'marketking_default_approval_manual', true);
												$default_checked = 'none';
												if ($automatic_approval_default !== NULL && !empty($automatic_approval_default)){
													$default_checked = explode('_',$automatic_approval_default)[1];
												}
												foreach ($groups as $group){
													echo '<option value="'.esc_attr($group->ID).'" '.selected($group->ID, $default_checked, false).'>'.esc_html($group->post_title).'</option>';
												}
												if (empty($groups)){
													echo '<option value="nogroup">'.esc_html__('No group is set up. Please create a customer group', 'marketking-multivendor-marketplace-for-woocommerce').'</option>';
												}



												?>
											</select>
											<div class="marketking_user_registration_user_data_container_element_approval_buttons_container">
												<input type="hidden" value="<?php echo esc_attr($user->ID); ?>" id="marketking_user_registration_data_id">
												<button type="button" class="marketking_user_registration_user_data_container_element_approval_button_approve">
													<svg class="marketking_user_registration_user_data_container_element_approval_button_approve_icon" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="none" viewBox="0 0 35 35">
													  <path fill="#fff" d="M17.5 0C7.85 0 0 7.85 0 17.5S7.85 35 17.5 35 35 27.15 35 17.5 27.15 0 17.5 0zm9.108 11.635L15.3 25.096a1.346 1.346 0 01-1.01.48h-.022a1.345 1.345 0 01-1-.445L8.42 19.746a1.346 1.346 0 112-1.8l3.811 4.234L24.546 9.903a1.346 1.346 0 012.062 1.732z"/>
													</svg>
													<?php esc_html_e('Approve user','marketking-multivendor-marketplace-for-woocommerce'); ?>
												</button>
												<button type="button" class="marketking_user_registration_user_data_container_element_approval_button_reject">
													<svg class="marketking_user_registration_user_data_container_element_approval_button_reject_icon" xmlns="http://www.w3.org/2000/svg" width="29" height="29" fill="none" viewBox="0 0 29 29">
													  <path fill="#fff" d="M9.008 2.648h-.29a.29.29 0 00.29-.289v.29h10.984v-.29c0 .16.13.29.29.29h-.29V5.25h2.602V2.36A2.315 2.315 0 0020.28.046H8.72a2.315 2.315 0 00-2.313 2.312V5.25h2.602V2.648zm18.21 2.602H1.782c-.64 0-1.156.517-1.156 1.156v1.157c0 .158.13.289.29.289h2.181l.893 18.897a2.314 2.314 0 002.309 2.204h16.404a2.31 2.31 0 002.309-2.204l.893-18.897h2.182a.29.29 0 00.289-.29V6.407c0-.64-.517-1.156-1.156-1.156zm-4.794 21.102H6.576l-.874-18.5h17.596l-.874 18.5z"/>
													</svg>
													<?php esc_html_e('Reject and delete user','marketking-multivendor-marketplace-for-woocommerce'); ?>
												</button>
											</div>
										</div>
									</div>
									<?php
								} else {
									// set up button for "Update registration fields"
									// if user is b2b
									if (get_user_meta($user->ID,'marketking_account_approved',true) !== 'no'){
										?>
										<div class="marketking_user_registration_user_data_container_element">
											<div class="marketking_user_registration_user_data_container_element_label">
												<?php esc_html_e('Registration Approval','marketking-multivendor-marketplace-for-woocommerce'); ?>
											</div>
											<div class="marketking_user_registration_user_data_container_element_approval">
												<div class="marketking_user_registration_user_data_container_element_approval_buttons_container">
													<input type="hidden" value="<?php echo esc_attr($user->ID); ?>" id="marketking_user_registration_data_id">
													<button type="button" class="marketking_user_registration_user_data_container_element_approval_button_deactivate">
														<svg class="marketking_user_registration_user_data_container_element_approval_button_reject_icon" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="none" viewBox="0 0 35 35">
														  <path fill="#fff" d="M35 17.5a17.5 17.5 0 11-35 0 17.5 17.5 0 0135 0zm-23.288-7.337a1.095 1.095 0 00-1.549 1.549l5.79 5.788-5.79 5.788a1.095 1.095 0 001.549 1.549l5.788-5.79 5.788 5.79a1.096 1.096 0 001.549-1.549l-5.79-5.788 5.79-5.788a1.096 1.096 0 00-1.549-1.549l-5.788 5.79-5.788-5.79z"/>
														</svg>
														<?php esc_html_e('Deactivate / unapprove user','marketking-multivendor-marketplace-for-woocommerce'); ?>
													</button>
												</div>
											</div>
										</div>
										<?php
									}
									if (!empty($custom_fields_array) && apply_filters('marketking_show_update_registration', true)){
										?>
										<button id="marketking_update_registration_data_button" type="button" class="button button-primary"><?php esc_html_e('Update Registration Data','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
										<?php
									}
								}

								// if subscription module is active, show subscription ID here
			    				?>
			    			</div>
			    			<?php
			    		}
						?>
					</div><br>
					<button type="button" class="button button-secondary" id="marketking_print_user_data"><?php esc_html_e('Print','marketking-multivendor-marketplace-for-woocommerce');?></button>
				<?php
				} else {
					// show unapproval option only
					$member = get_user_meta($user_id,'marketking_parent_vendor', true);
					if (empty($member)){
					?>
					    	<div id="marketking_registration_data_container" class="marketking_user_shipping_payment_methods_container">
					    		<div class="marketking_user_shipping_payment_methods_container_top">
					    			<div class="marketking_user_shipping_payment_methods_container_top_title">
					    				<?php esc_html_e('Vendor Registration','marketking-multivendor-marketplace-for-woocommerce'); ?>
					    			</div>		
					    		</div>
				    			<div class="marketking_user_registration_user_data_container">
									<div class="marketking_user_registration_user_data_container_element">
										<div class="marketking_user_registration_user_data_container_element_label">
											<?php esc_html_e('Registration Approval','marketking-multivendor-marketplace-for-woocommerce'); ?>
										</div>
										<div class="marketking_user_registration_user_data_container_element_approval">
											<div class="marketking_user_registration_user_data_container_element_approval_buttons_container">
												<input type="hidden" value="<?php echo esc_attr($user->ID); ?>" id="marketking_user_registration_data_id">
												<button type="button" class="marketking_user_registration_user_data_container_element_approval_button_deactivate">
													<svg class="marketking_user_registration_user_data_container_element_approval_button_reject_icon" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="none" viewBox="0 0 35 35">
													  <path fill="#fff" d="M35 17.5a17.5 17.5 0 11-35 0 17.5 17.5 0 0135 0zm-23.288-7.337a1.095 1.095 0 00-1.549 1.549l5.79 5.788-5.79 5.788a1.095 1.095 0 001.549 1.549l5.788-5.79 5.788 5.79a1.096 1.096 0 001.549-1.549l-5.79-5.788 5.79-5.788a1.096 1.096 0 00-1.549-1.549l-5.788 5.79-5.788-5.79z"/>
													</svg>
													<?php esc_html_e('Deactivate / unapprove user','marketking-multivendor-marketplace-for-woocommerce'); ?>
												</button>
											</div>
										</div>
									</div>
								</div>
							</div>
					<?php
					}
				}

				if (!(get_user_meta($user->ID,'marketking_b2buser',true) !== 'yes')){
					do_action('marketking_after_user_registration_data', $user->ID);
				}
			}
		    ?>
		<?php 
	}

	function marketking_show_user_meta_profile($user){
		if (isset($user->ID)){
			$user_id = $user->ID;
		} else {
			$user_id = 0;
		}

		// get if this user is a team member of a vendor
		$member = get_user_meta($user_id,'marketking_parent_vendor', true);
		if (empty($member)){
			?>
			<input type="hidden" id="marketking_admin_user_id" value="<?php echo esc_attr($user_id);?>">
		    <h3 id="marketking_user_vendor_profile"><?php esc_html_e("Vendor Settings (MarketKing)", "marketking-multivendor-marketplace-for-woocommerce"); ?></h3>

		    <?php
		    	$customer_vendor = get_user_meta($user_id,'marketking_user_choice', true);
		    	if (empty($customer_vendor)){
		    		$customer_vendor = 'customer';
		    	}

		    	if (isset($_GET['add'])){
		    		if ($_GET['add'] === 'vendor'){
		    			$customer_vendor = 'vendor';
		    		}
		    	}
		    ?>
	    	<h2 class="marketking_inline_header" ><?php esc_html_e('This user is a','marketking-multivendor-marketplace-for-woocommerce');?></h2>
	    	<div class="marketking_switch-field">
	    		<input type="radio" id="marketking_radio-one" name="marketking_user_choice" value="customer" <?php checked('customer',$customer_vendor, true);?>/>
	    		<label for="marketking_radio-one"><strong><?php esc_html_e('Customer','marketking-multivendor-marketplace-for-woocommerce');?></strong></label>
	    		<input type="radio" id="marketking_radio-two" name="marketking_user_choice" value="vendor" <?php checked('vendor',$customer_vendor, true);?> />
	    		<label for="marketking_radio-two"><strong><?php esc_html_e('Vendor','marketking-multivendor-marketplace-for-woocommerce');?></strong></label>
	    	</div>


	    	<div id="marketking_user_profile_customer_vendor" class="marketking_user_shipping_payment_methods_container">
	    		<div class="marketking_user_shipping_payment_methods_container_top">
	    			<div class="marketking_user_shipping_payment_methods_container_top_title">
	    				<?php esc_html_e('Vendor Settings','marketking-multivendor-marketplace-for-woocommerce'); ?>
	    			</div>		
	    		</div>
	    		<div class="marketking_user_settings_container marketking_vendor_settings_vendor">
	    			<div class="marketking_user_settings_container_column">
	    				<div class="marketking_user_settings_container_column_title">
	    					<svg class="marketking_user_settings_container_column_title_icon_right" xmlns="http://www.w3.org/2000/svg" width="45" height="45" fill="none" viewBox="0 0 45 45">
	    					  <path fill="#C4C4C4" d="M22.382 7.068c-3.876 0-7.017 3.668-7.017 8.193 0 3.138 1.51 5.863 3.73 7.239l-2.573 1.192-6.848 3.176c-.661.331-.991.892-.991 1.686v7.541c.054.943.62 1.822 1.537 1.837h24.36c1.048-.091 1.578-.935 1.588-1.837v-7.541c0-.794-.33-1.355-.992-1.686l-6.6-3.175-2.742-1.3c2.128-1.407 3.565-4.073 3.565-7.132 0-4.525-3.142-8.193-7.017-8.193zM11.063 9.95c-1.667.063-2.99.785-3.993 1.935a7.498 7.498 0 00-1.663 4.663c.068 2.418 1.15 4.707 3.076 5.905l-7.69 3.573c-.529.198-.793.661-.793 1.389v6.053c.041.802.458 1.477 1.24 1.488h5.11v-6.401c.085-1.712.888-3.095 2.333-3.77l5.109-2.43a4.943 4.943 0 001.141-.944c-2.107-3.25-2.4-7.143-1.041-10.567-.883-.54-1.876-.888-2.829-.894zm22.822 0c-1.09.023-2.098.425-2.926.992 1.32 3.455.956 7.35-.993 10.37.43.495.877.876 1.34 1.14l4.912 2.333c1.496.82 2.267 2.216 2.282 3.77v6.401h5.259c.865-.074 1.233-.764 1.241-1.488v-6.053c0-.662-.264-1.124-.794-1.39l-7.59-3.622c1.968-1.452 2.956-3.627 2.976-5.855-.053-1.763-.591-3.4-1.663-4.663-1.12-1.215-2.51-1.922-4.044-1.935z"/>
	    					</svg>
	    					<?php esc_html_e('Vendor Group','marketking-multivendor-marketplace-for-woocommerce'); ?>
	    				</div>
	    				<select name="marketking_group" id="marketking_group" class="marketking_user_settings_select">
	    					<?php
	    						$vendorgroup = get_user_meta( $user_id, 'marketking_group', true );
	    					 	echo '<option value="none" '.selected('none', $vendorgroup, false).'>'.esc_html__('- No group (inactive) -', 'marketking-multivendor-marketplace-for-woocommerce').'</option>'; 
	    					 	?>
	  	    					<optgroup label="<?php esc_html_e('Vendor Groups', 'marketking-multivendor-marketplace-for-woocommerce'); ?>">
	  	    					
	  	    					<?php
		    					$posts = get_posts([
		    					  'post_type' => 'marketking_group',
		    					  'post_status' => 'publish',
		    					  'numberposts' => -1
		    					]);
		    					foreach ($posts as $post){
		    						echo '<option value="'.esc_attr($post->ID).'" '.selected($post->ID, $vendorgroup, false).'>'.esc_html($post->post_title).'</option>';
		    					}
			    				?>
	    					</optgroup>
	    				</select>
	    				<!-- START VENDOR PROFILE TAB -->
	    				<br><br>
	    				<div class="marketking-vendor">
		    				<div class="marketking-tab-contents">
	    						<input type="hidden" id="marketking_profile_logo_image" name="marketking_profile_logo_image" value="<?php echo esc_attr(get_user_meta($user_id,'marketking_profile_logo_image', true));?>">
	    						<input type="hidden" id="marketking_profile_logo_image_banner" name="marketking_profile_logo_image_banner" value="<?php echo esc_attr(get_user_meta($user_id,'marketking_profile_logo_image_banner', true));?>">

	    						<div class="marketking-content-header">
	    				        	<?php esc_html_e('Vendor Profile', 'marketking-multivendor-marketplace-for-woocommerce');?>
	    				    	</div> 
	    				   		<div class="marketking-content-body"><div class="marketking-vendor-image"><div class="picture"><div class="marketking_clear_image" id="marketking_clear_image_profile"><?php esc_html_e('clear', 'marketking-multivendor-marketplace-for-woocommerce');?></div><p class="marketking-picture-header"><?php esc_html_e('Vendor Picture', 'marketking-multivendor-marketplace-for-woocommerce');?></p> <div class="marketking-profile-image"><div class="marketking-upload-image"><img src="<?php 

	    				   		$imageprof = get_user_meta($user_id,'marketking_profile_logo_image', true);
	    				   		if (empty($imageprof)){
	    				   			$imageprof = plugins_url('../includes/assets/images/store-profile.png', __FILE__);
	    				   		}
	    				   		echo esc_attr($imageprof);

	    				   		?>"> <!----></div></div> <p class="marketking-picture-footer"><?php esc_html_e('Click to select / upload a profile picture for the vendor.', 'marketking-multivendor-marketplace-for-woocommerce');?></p></div> <div class="picture banner" style="<?php
	    				   		$imageprof = get_user_meta($user_id,'marketking_profile_logo_image_banner', true);
	    				   		if (!empty($imageprof)){
	    				   			echo 'background-image:url('.esc_attr($imageprof).')';
	    				   		}

	    				   		// show slug of stores page

	    				   		$dashboard_page_id = apply_filters( 'wpml_object_id', get_option( 'marketking_stores_page_setting', 'none' ), 'post' , true);
	    				   		$post_slug = get_post_field( 'post_name', get_post($dashboard_page_id) );
	    				   	
	    				   		?>"><div class="marketking_clear_image" id="marketking_clear_image_profile_banner"><?php esc_html_e('clear', 'marketking-multivendor-marketplace-for-woocommerce');?></div><div class="marketking-banner-image"><div class="marketking-upload-image"><!----> <button type="button">
	    				        <?php esc_html_e('Upload Banner', 'marketking-multivendor-marketplace-for-woocommerce');?>
	    				    	</button></div></div> <p class="marketking-picture-footer"><?php esc_html_e('Click to select / upload a banner for the store.', 'marketking-multivendor-marketplace-for-woocommerce');?></p></div></div> <div class="marketking-form-group"><div class="column"><label for="marketking_store_name"><?php esc_html_e('Store Name', 'marketking-multivendor-marketplace-for-woocommerce');?></label> <span class="marketking-required-field">*</span> <input type="text" maxlength="<?php echo esc_attr(apply_filters('marketking_store_name_max_length', 25)); ?>" name="marketking_store_name" id="marketking_store_name" placeholder="<?php esc_attr_e('Store Name', 'marketking-multivendor-marketplace-for-woocommerce');?>" class="marketking-form-input" value="<?php echo esc_attr(get_user_meta($user_id,'marketking_store_name', true));?>"></div> <div class="column"><label for="marketking_store_url"><?php esc_html_e('Store URL', 'marketking-multivendor-marketplace-for-woocommerce');?></label>  <span class="marketking-required-field">*</span><span class="marketking_availability"></span><input type="text" id="marketking_store_url" name="marketking_store_url" value="<?php echo esc_attr(get_user_meta($user_id,'marketking_store_url', true));?>" placeholder="<?php esc_attr_e('Store URL', 'marketking-multivendor-marketplace-for-woocommerce');?>" class="marketking-form-input"> <div class="marketking-store-avaibility-info"><p class="marketking_store_url"><?php 

	    				    	$urlpreview = get_home_url().'/'.esc_attr($post_slug).'/'.'<strong>URL</strong>';

	    				    	$baseurl = get_user_meta($user_id,'marketking_vendor_store_url_base',true);
	    				    	if (!empty($baseurl)){
	    				    		if (intval($baseurl) === 1){
	    				    			$urlpreview = get_home_url().'/'.'<strong>URL</strong>';
	    				    		}
	    				    	}
	    				    	echo $urlpreview;

	    				    	?></p> <span class="marketking-not-available"></span></div></div> <div class="column"><label for="marketking_store_phone"><?php esc_html_e('Phone Number', 'marketking-multivendor-marketplace-for-woocommerce');?></label> <input type="number" name="marketking_store_phone" id="marketking_store_phone" value="<?php echo esc_attr(get_user_meta($user_id,'marketking_store_phone', true));?>" placeholder="123456789" class="marketking-form-input"></div> <div class="column"><label for="marketking_store_email"><?php esc_html_e('Email', 'marketking-multivendor-marketplace-for-woocommerce');?></label> <input type="email" name="marketking_store_email" id="marketking_store_email" placeholder="contact@youremail.com" class="marketking-form-input" value="<?php echo esc_attr(get_user_meta($user_id,'marketking_store_email', true));?>"></div></div></div>
		    				</div><br>

		    				<div class="marketking-tab-contents">
		    					<div class="marketking-content-header">
	    				        	<?php esc_html_e('Vendor Settings', 'marketking-multivendor-marketplace-for-woocommerce');?>
	    				    	</div> 
	    						<div class="marketking-content-body">
		    				    	<div class="marketking-form-group">
		    				    		<div class="column">
		    				    			<div class="marketking-form-checkbox-container">
		    				    				<label for="marketking_store_phone"><?php esc_html_e('Vendor can publish products directly','marketking-multivendor-marketplace-for-woocommerce'); 

		    				    				$checked = intval(get_user_meta($user_id,'marketking_vendor_publish_products',true));

		    				    				$global = get_option( 'marketking_vendor_publish_direct_setting', 0 );
		    				    				$disabled = '';

		    				    				if (intval($global) === 1){
		    				    					$disabled = 'disabled="disabled"';
		    				    				} else {
		    				    					// check at group level
		    				    					$group_id = get_user_meta($user_id,'marketking_group', true);
		    				    					if (!empty($group_id)){
		    				    						$groupval = get_post_meta($group_id,'marketking_group_vendor_publish_direct_setting', true);
		    				    						if(intval($groupval) === 1){
		    				    							$disabled = 'disabled="disabled"';
		    				    						}
		    				    					}
		    				    				}

				    				    		$tip = esc_html__('Vendor can bypass the "pending / review" status, and directly publish products in the shop.','marketking-multivendor-marketplace-for-woocommerce');
				    				    		if ($disabled !== ''){
				    				    			$tip .= esc_html__(' This setting cannot be disabled, because it is enabled globally in MarketKing -> Settings, or at the group level.','marketking-multivendor-marketplace-for-woocommerce');
				    				    			$checked = 1;
				    				    		}
				    				    		echo ' '.wc_help_tip($tip, false);

				    				    		?></label> <input type="checkbox" value="1" name="marketking_vendor_publish_products" id="marketking_vendor_publish_products" class="marketking-checkbox-input" <?php checked(1,$checked,true); echo ' '.$disabled;?>>
				    				    	</div>			    				    		
				    				    </div>
			    				    	<div class="marketking-form-checkbox-container">
	    				    				<label for="marketking_store_phone"><?php esc_html_e('Vendor can change order statuses','marketking-multivendor-marketplace-for-woocommerce'); 

	    				    				$checked = intval(get_user_meta($user_id,'marketking_vendor_change_status',true));

	    				    				$global = get_option( 'marketking_vendor_status_direct_setting', 0 );
	    				    				$disabled = '';

	    				    				if (intval($global) === 1){
	    				    					$disabled = 'disabled="disabled"';
	    				    				} else {
	    				    					// check at group level
	    				    					$group_id = get_user_meta($user_id,'marketking_group', true);
	    				    					if (!empty($group_id)){
	    				    						$groupval = get_post_meta($group_id,'marketking_group_vendor_status_direct_setting', true);
	    				    						if(intval($groupval) === 1){
	    				    							$disabled = 'disabled="disabled"';
	    				    						}
	    				    					}
	    				    				}

			    				    		$tip = esc_html__('Vendor can directly change order status for their own orders.','marketking-multivendor-marketplace-for-woocommerce');
			    				    		if ($disabled !== ''){
			    				    			$tip .= esc_html__(' This setting cannot be disabled, because it is enabled globally in MarketKing -> Settings, or at the group level.','marketking-multivendor-marketplace-for-woocommerce');
			    				    			$checked = 1;
			    				    		}
			    				    		
			    				    		echo ' '.wc_help_tip($tip, false);

			    				    		?></label> <input type="checkbox" value="1" name="marketking_vendor_change_status" id="marketking_vendor_change_status" class="marketking-checkbox-input" <?php checked(1,$checked,true); echo ' '.$disabled;?>>
			    				    	</div>
			    				    	<?php

			    				    	if (intval(get_option('marketking_enable_storecategories_setting', 1)) === 1){
				    				    	$selectedarr = get_user_meta($user_id,'marketking_store_categories', true);
				    				    	if (empty($selectedarr)){
				    				    	    $selectedarr = array();
				    				    	}

				    				    	$args =  array(
				    				    	    'hierarchical'     => 1,
				    				    	    'hide_empty'       => 0,
				    				    	    'class'            => 'form_select',
				    				    	    'name'             => 'marketking_select_storecategories',
				    				    	    'id'               => 'marketking_select_storecategories',
				    				    	    'taxonomy'         => 'storecat',
				    				    	    'orderby'          => 'name',
				    				    	    'title_li'         => '',
				    				    	    'selected'         => implode(',',$selectedarr)
				    				    	);

				    				    	// Mutiple categories in pro

				    				    	if(defined('MARKETKINGPRO_DIR')){
				    				    	    $current_id = $user_id;
				    				    	    
				    				    	    if (marketking()->vendor_can_multiple_store_categories($current_id)){
				    				    	        $args['multiple'] = true;
				    				    	    }
				    				    	}

				    				    	?>
				    				    	<div class="marketking-form-select-container">
		    			    					<div class="marketking-select-content-header">
		    		    				        	<?php esc_html_e('Store Categories', 'marketking-multivendor-marketplace-for-woocommerce');?>
		    		    				    	</div> 
				    				    		<?php

				    				    		$args =  array(
				    				    		    'hierarchical'     => 1,
				    				    		    'hide_empty'       => 0,
				    				    		    'class'            => 'form_select',
				    				    		    'name'             => 'marketking_select_storecategories',
				    				    		    'id'               => 'marketking_select_storecategories',
				    				    		    'taxonomy'         => 'storecat',
				    				    		    'orderby'          => 'name',
				    				    		    'title_li'         => '',
				    				    		    'selected'         => implode(',',$selectedarr)
				    				    		);
				    				    		wp_dropdown_categories( $args );
				    				    		?>
				    				    	</div>

    			    				    	
				    				    	<?php
				    				    }
			    				    	?>
		    				    	</div>
		    				    </div>
		    				</div><br>

		    				<div class="marketking-tab-contents">
		    					<div class="marketking-content-header">
	    				        	<?php esc_html_e('Advanced Settings', 'marketking-multivendor-marketplace-for-woocommerce');?>
	    				    	</div> 
	    						<div class="marketking-content-body">
		    				    	<div class="marketking-form-group">
		    				    		<div class="column">
		    				    			<div class="marketking-form-checkbox-container">
		    				    				<label for="marketking_store_phone"><?php esc_html_e('Enable base site URL for this store','marketking-multivendor-marketplace-for-woocommerce'); 

		    				    				$checked = get_user_meta($user_id,'marketking_vendor_store_url_base',true);

		    				    				if (empty($checked)){
		    				    					$checked = 0;
		    				    				} else {
		    				    					$checked = intval($checked);
		    				    				}

				    				    		$tip = esc_html__('This vendor store URL will be added to the website base URL, and can be accessed directly e.g. yoursite.com/storeurl','marketking-multivendor-marketplace-for-woocommerce');
				    				    		
				    				    		echo ' '.wc_help_tip($tip, false);

				    				    		?></label> <input type="checkbox" value="1" name="marketking_vendor_store_url_base" id="marketking_vendor_store_url_base" class="marketking-checkbox-input" <?php checked(1,$checked,true); ?>>
				    				    	</div>

				    				    	<?php
				    				    	if (defined('MARKETKINGPRO_DIR')){
				    				    	  if (intval(get_option('marketking_enable_memberships_setting', 1)) === 1){
				    				    	  	?>
				    				    	  	<br><label for="marketking_vendor_active_subscription"><?php esc_html_e('Vendor Subscription ID', 'marketking-multivendor-marketplace-for-woocommerce');?></label> <input type="text" name="marketking_vendor_active_subscription" id="marketking_vendor_active_subscription" value="<?php echo esc_attr(get_user_meta($user_id,'marketking_vendor_active_subscription', true));?>" placeholder="123456789" class="marketking-form-input">

				    				    	  	<?php
				    				    	  }
				    				    	} ?>
				    				    </div>
				    				    	    				  
				    				    			    				    	
		    				    	</div>

		    				    </div>
		    				</div>

		    				<br>
		    				<?php
		    				if (intval(get_option( 'marketking_enable_advertising_setting', 0 )) === 1){
		    					?>
			    				<div class="marketking-tab-contents">
			    					<div class="marketking-content-header">
		    				        	<?php esc_html_e('Advertising Credits', 'marketking-multivendor-marketplace-for-woocommerce');?>
		    				    	</div> 
		    						<div class="marketking-content-body">
			    				    	<div class="marketking-form-group">
			    				    		<div class="column">
			    				    			<div class="marketking-form-select-container marketking_credit_left_container">
			    				    				<div class="marketking-select-content-header">
			    				    			    	<?php esc_html_e('Advertising Credits', 'marketking-multivendor-marketplace-for-woocommerce');

			    				    			    	$tip = esc_html__('The vendor can use credits to purchase advertising for their own products.','marketking-multivendor-marketplace-for-woocommerce');
			    				    			    	
			    				    			    	echo ' '.wc_help_tip($tip, false);

			    				    			    	?>
			    				    				</div> 
			    				    				<input type="number" name="marketking_store_credits" id="marketking_store_credits" value="<?php echo esc_attr(marketking()->get_advertising_credits($user_id));?>" class="marketking-form-input">
			    				    			</div>
			    				    		</div>
			    				    		<div class="column">
			    				    			<div class="marketking-form-select-container marketking_credit_history_container">
			    				    				<div class="marketking-select-content-header">
			    				    			    	<?php esc_html_e('Credit History Log', 'marketking-multivendor-marketplace-for-woocommerce');

			    				    			    	$tip = esc_html__('Download a log of all credit changes for this vendor.','marketking-multivendor-marketplace-for-woocommerce');
			    				    			    	
			    				    			    	echo ' '.wc_help_tip($tip, false);

			    				    			    	?>
			    				    				</div> 
				    				    			<button id="marketking_download_vendor_credit_history" type="button" class="button button-secondary" value="<?php echo esc_attr($user_id); ?>"><?php esc_html_e('Download Vendor Credits History','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
				    				    		</div>

			    				    		</div>	
					    				</div>	
					    				    			    				    	
			    				    </div>

			    				</div>
			    				<?php
			    			}
			    			?>
		    			</div>

		    		</div>

	    				<!-- END VENDOR PROFILE TAB -->
	    			</div>
	    		</div>
	    	

			</div>
						        	
			<br /><br />
		<?php
		} else {
			esc_html_e('This user is a team member of the following vendor: ','marketking-multivendor-marketplace-for-woocommerce');
			$vendor = new WP_User($member);
			if ($vendor){
				$link = get_edit_user_link($member);
				$name = $vendor->first_name.' '.$vendor->last_name.' ('.$vendor->user_login.')';
				echo '<a href="'.esc_attr($link).'">'.esc_html($name).'</a><br><br>';
			}
		}
	}

	function marketking_save_user_meta_vendor_group($user_id ){

		if ( !current_user_can( 'edit_user', $user_id ) ) { 
		    return false; 
		}

		if (isset($_POST['marketking_group'])){
			$vendor_group = sanitize_text_field($_POST['marketking_group']);

			// if user chose customer, we must set the vendor value to none
			if (isset($_POST['marketking_user_choice'])){
				$customer_or_vendor = sanitize_text_field($_POST['marketking_user_choice']);
				if ($customer_or_vendor === 'customer'){
					$vendor_group = 'none';
				}
			}
			update_user_meta( $user_id, 'marketking_group', $vendor_group);	


			if (apply_filters('marketking_use_wp_roles', false)){

				// remove existing roles of marketking, and add new role
				$groups = get_posts([
				  'post_type' => 'marketking_group',
				  'post_status' => 'publish',
				  'numberposts' => -1,
				  'fields' => 'ids',
				]);

				$user_obj = new WP_User($user_id);
				foreach ($groups as $group){
					$user_obj->remove_role('marketking_role_'.$group);
				}
				$user_obj->add_role('marketking_role_'.$vendor_group);
			}
		}

		if (isset($_POST['marketking_group_vendor'])){
			$assigned_vendor = sanitize_text_field($_POST['marketking_group_vendor']);
			// if user chose vendor, we must set the customer value to none
			if (isset($_POST['marketking_user_choice'])){
				$customer_or_vendor = sanitize_text_field($_POST['marketking_user_choice']);
				if ($customer_or_vendor === 'vendor'){
					$assigned_vendor = 'none';
				}
			}
			update_user_meta( $user_id, 'marketking_assigned_vendor', $assigned_vendor);	
		}

		if (isset($_POST['marketking_user_choice'])){
			$customer_or_vendor = sanitize_text_field($_POST['marketking_user_choice']);
			update_user_meta( $user_id, 'marketking_user_choice', $customer_or_vendor);	
		}

		if (isset($_POST['marketking_store_name'])){
			$val = sanitize_text_field($_POST['marketking_store_name']);
			// max 25 characters for the store name
			$maxstorelength = apply_filters('marketking_store_name_max_length', 25);

			if(strlen($val) > $maxstorelength){
				$val = substr($val, 0, $maxstorelength);
			}
			update_user_meta( $user_id, 'marketking_store_name', sanitize_text_field($_POST['marketking_store_name']));	
		}
		if (isset($_POST['marketking_store_url'])){
			update_user_meta( $user_id, 'marketking_store_url', sanitize_text_field($_POST['marketking_store_url']));	
		}
		if (isset($_POST['marketking_store_email'])){
			update_user_meta( $user_id, 'marketking_store_email', sanitize_text_field($_POST['marketking_store_email']));	
		}
		if (isset($_POST['marketking_store_phone'])){
			update_user_meta( $user_id, 'marketking_store_phone', sanitize_text_field($_POST['marketking_store_phone']));	
		}

		if (isset($_POST['marketking_store_credits'])){
			$new_credits = intval(sanitize_text_field($_POST['marketking_store_credits']));
			$credits = intval(get_user_meta($user_id, 'marketking_advertising_credits_available', true));
			if ($credits !== $new_credits){
				$amount = $new_credits - $credits;
				// update and add to history
				update_user_meta( $user_id, 'marketking_advertising_credits_available', $new_credits);	

				// get user history
				$user_credit_history = sanitize_text_field(get_user_meta($user_id,'marketking_user_credit_history', true));
				// create reimbursed transaction
				$date = date_i18n( 'Y/m/d', time()+(get_option('gmt_offset')*3600) ); 

				$operation = 'modified';
				$note = esc_html__('Modified by admin for user profile (backend)', 'marketking-multivendor-marketplace-for-woocommerce');
				$transaction_new = $date.':'.$operation.':'.$amount.':'.$new_credits.':'.$note;

				// update credit history
				update_user_meta($user_id,'marketking_user_credit_history',$user_credit_history.';'.$transaction_new);
			}
		}

		if (isset($_POST['marketking_vendor_active_subscription'])){
			update_user_meta( $user_id, 'marketking_vendor_active_subscription', sanitize_text_field($_POST['marketking_vendor_active_subscription']));	
		}



		if (isset($_POST['marketking_profile_logo_image'])){
			update_user_meta( $user_id, 'marketking_profile_logo_image', sanitize_text_field($_POST['marketking_profile_logo_image']));	
		}
		if (isset($_POST['marketking_profile_logo_image_banner'])){
			update_user_meta( $user_id, 'marketking_profile_logo_image_banner', sanitize_text_field($_POST['marketking_profile_logo_image_banner']));	
		}

		$marketking_vendor_publish_products = sanitize_text_field(filter_input(INPUT_POST, 'marketking_vendor_publish_products'));
		if ($marketking_vendor_publish_products !== NULL){
			update_user_meta( $user_id, 'marketking_vendor_publish_products', $marketking_vendor_publish_products);
		}

		$marketking_vendor_change_status = sanitize_text_field(filter_input(INPUT_POST, 'marketking_vendor_change_status'));
		if ($marketking_vendor_change_status !== NULL){
			update_user_meta( $user_id, 'marketking_vendor_change_status', $marketking_vendor_change_status);
		}

		$marketking_vendor_store_url_base = sanitize_text_field(filter_input(INPUT_POST, 'marketking_vendor_store_url_base'));
		if ($marketking_vendor_store_url_base !== NULL){
			update_user_meta( $user_id, 'marketking_vendor_store_url_base', $marketking_vendor_store_url_base);
		}

		// Store categories
		$selectedcategories = $_POST['marketking_select_storecategories'];
		if (!empty($selectedcategories)){
			if (is_array($selectedcategories)){
				$arraycats = array_map('sanitize_text_field',$selectedcategories);
			} else {
				$arraycats = array(sanitize_text_field($selectedcategories));
			}
			update_user_meta($user_id,'marketking_store_categories', $arraycats);
		}
		

	}
	function marketking_add_columns_user_table ($columns){

	    $columns['marketking_group'] = esc_html__('Vendor Group','marketking-multivendor-marketplace-for-woocommerce');

		return $columns;
	}

	function marketking_retrieve_group_column_contents_users_table( $val, $column_name, $user_id ) {
	    if ($column_name === 'marketking_group') {

	    	// if requires approval, show here first
	    	$approved = get_user_meta($user_id,'marketking_account_approved', true);
	    	if ($approved === 'no'){
	    		return esc_html__('Requires Approval','marketking-multivendor-marketplace-for-woocommerce');
	    	}

        	$vendorgroup = get_user_meta( $user_id, 'marketking_group', true );

        	if (!empty($vendorgroup) && $vendorgroup !== 'none'){
            	return esc_html(get_the_title($vendorgroup));
            } else {
            	// check if user is team member
            	$parent = get_user_meta($user_id,'marketking_parent_vendor', true);
            	if (!empty($parent)){
            		$vendorgroup = get_user_meta( $parent, 'marketking_group', true );
            		return esc_html__('Team Member: ','marketking-multivendor-marketplace-for-woocommerce').esc_html(get_the_title($vendorgroup));

            	}
            	return '-';
            }

	    }
	    return $val;
	}


	function marketking_show_header_bar_marketking_posts(){
		global $post;
		if (isset($post->ID)){
			$post_type = get_post_type($post->ID);
			if (substr($post_type,0,10) === 'marketking'){
				echo self::get_header_bar();
			}
		} else {
			if (isset($_GET['post_type'])){
				if (substr($_GET['post_type'],0,10) === 'marketking'){
					echo self::get_header_bar();
				}
			}
		}
	}

	public static function marketking_view_payouts_content($useridd = 0){
		if (isset($_GET['user'])){
			$user_id = sanitize_text_field($_GET['user']);
		} else {
			$user_id = 0;
		}

		// user id was supplied via ajax and this function is being run by ajax
		if (intval($useridd) !== 0){
			$user_id = $useridd;
		}
		
		$userinfo = get_userdata($user_id);
		$store_name = get_user_meta($user_id, 'marketking_store_name', true);
		$info = base64_decode(get_user_meta($user_id,'marketking_payout_info', true));
		$info = explode('**&&', $info);

		echo self::get_header_bar();

		?>
		<!-- User-specific shipping and payment methods -->
		<div class="marketking_user_shipping_payment_methods_container marketking_special_group_container">
			<input type="hidden" name="marketking_admin_user_id" value="<?php echo esc_attr($user_id);?>">
			<div class="marketking_above_top_title_button">
				<div class="marketking_above_top_title_button_left">
					<?php esc_html_e('Vendor Payouts','marketking-multivendor-marketplace-for-woocommerce'); ?>
				</div>
				<div class="marketking_above_top_title_button_right">
					<a href="<?php echo admin_url( 'admin.php?page=marketking_payouts'); ?>" class="marketking_vendor_link" id="marketking_go_back_page"><button class="marketking_back_button marketking-btn marketking-btn-light marketking-btn-sm marketking_manage_vendors_button"><em class="icon marketking-ni marketking-ni-back-arrow"></em><span><?php esc_html_e('Go Back','marketking-multivendor-marketplace-for-woocommerce');?></span></button></a>

				</div>
			</div>
			<div class="marketking_user_shipping_payment_methods_container_top">
				<div class="marketking_user_shipping_payment_methods_container_top_title">
					<?php esc_html_e('Payouts for','marketking-multivendor-marketplace-for-woocommerce'); echo ': '.esc_html($store_name); ?>
				</div>		
			</div>

			<!-- BEGIN CONTENT -->
			<div class="marketking_user_payouts_container">
				<div class="marketking_user_registration_user_data_container_title">
				    <svg class="marketking_user_registration_user_data_container_title_icon" xmlns="http://www.w3.org/2000/svg" width="36" height="31" fill="none" viewBox="0 0 36 31">
				      <path fill="#C4C4C4" d="M20.243 25.252c0-.553.065-1.09.147-1.628H3.964v-9.767h26.047v1.628c1.14 0 2.23.211 3.256.57V4.088A3.245 3.245 0 0030.01.833H3.964A3.245 3.245 0 00.708 4.09v19.535a3.256 3.256 0 003.256 3.256H20.39a10.807 10.807 0 01-.147-1.628zM3.964 4.089h26.047v3.256H3.964V4.089zm24.012 26.047l-4.477-4.884 1.888-1.888 2.589 2.588 5.844-5.844 1.888 2.295-7.732 7.733z"/>
				    </svg>
				    <?php esc_html_e('Current Vendor Payout Information','marketking-multivendor-marketplace-for-woocommerce'); ?>
				</div>
				<div class="marketking_user_registration_user_data_container_element">
				    <div class="marketking_user_registration_user_data_container_element_label">
				        <?php esc_html_e('Chosen payout method','marketking-multivendor-marketplace-for-woocommerce'); ?>
				    </div>
				    <?php 
				    $method = get_user_meta($user_id,'marketking_agent_selected_payout_method', true);
				    if ($method === 'paypal'){
				        $method = 'PayPal';
				    } else if ($method === 'bank'){
				        $method = 'Bank';
				    } else if ($method === 'stripe'){
				        $method = 'Stripe';
				        // check if connected successfully or not
				        if ( get_user_meta($user_id, 'vendor_connected', true) == 1 ) {
				        	$method .= ' ('.esc_html__('connected successfully','marketking-multivendor-marketplace-for-woocommerce').')';
				        } else {
				        	$method .= ' ('.esc_html__('not connected yet','marketking-multivendor-marketplace-for-woocommerce').')';
				        }
				    } else if ($method === 'custom'){
				        $method = get_option( 'marketking_enable_custom_payouts_title_setting', '' );
				    }
				    if (empty($method)){
				    	$method = esc_html__('The vendor has not configured a payout method yet', 'marketking-multivendor-marketplace-for-woocommerce');
				    }
				    ?>
				    <input type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($method);?>" readonly>
				</div>
				<?php
				if ($method === 'PayPal'){
					?>
					<div class="marketking_user_registration_user_data_container_element">
					    <div class="marketking_user_registration_user_data_container_element_label">
					        <?php esc_html_e('PayPal email address','marketking-multivendor-marketplace-for-woocommerce'); ?>
					    </div>
					    <input type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($info[0]);?>" readonly>
					</div>
					<?php
				}
				?>
				<?php
				if ($method === get_option( 'marketking_enable_custom_payouts_title_setting', '' )){
					?>
					<div class="marketking_user_registration_user_data_container_element">
					    <div class="marketking_user_registration_user_data_container_element_label">
					        <?php esc_html_e('Details','marketking-multivendor-marketplace-for-woocommerce'); ?>
					    </div>
					    <input type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($info[1]);?>" readonly>
					</div>
					<?php
				}
				?>
				<?php
				if ($method === 'Bank'){
					for ($i=2; $i<=18; $i++){
						if (!empty($info[$i])){
						?>
						<div class="marketking_user_registration_user_data_container_element">
						    <div class="marketking_user_registration_user_data_container_element_label">
						        <?php 
						        switch($i){
						        	case 2:
						        	esc_html_e('Full Name', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 3:
						        	esc_html_e('Billing Address Line 1', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 4:
						        	esc_html_e('Billing Address Line 2', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 5:
						        	esc_html_e('City', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 6:
						        	esc_html_e('State', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 7:
						        	esc_html_e('Postcode', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 8:
						        	esc_html_e('Country', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 9:
						        	esc_html_e('Bank Account Holder Name', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 10:
						        	esc_html_e('Bank Account Number/IBAN', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 11:
						        	esc_html_e('Bank Branch City', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 12:
						        	esc_html_e('Bank Branch Country', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 13:
						        	esc_html_e('Intermediary Bank - Bank Code', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 14:
						        	esc_html_e('Intermediary Bank - Name', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 15:
						        	esc_html_e('Intermediary Bank - City', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 16:
						        	esc_html_e('Intermediary Bank - Country', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 17:
						        	esc_html_e('Bank Name', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;

						        	case 18:
						        	esc_html_e('Bank BIC / SWIFT', 'marketking-multivendor-marketplace-for-woocommerce');
						        	break;


						        }

						        ?>
						    </div>
						    <input type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php echo esc_attr($info[$i]);?>" readonly>
						</div>
						<?php
						}
					}
				}
				?>
				<br />
				<!-- 2. REIMBURSEMENT SECTION -->
			    <div class="marketking_user_registration_user_data_container_title">
			        <svg class="marketking_user_registration_user_data_container_title_icon" xmlns="http://www.w3.org/2000/svg" width="36" height="29" fill="none" viewBox="0 0 36 29">
			          <g clip-path="url(#clip0)">
			            <path fill="#C4C4C4" d="M14.4 18.952h-.001c0-.913.075-.493-4.784-10.238-.993-1.99-3.836-1.995-4.83 0C-.115 18.543 0 18.068 0 18.952H0c0 2.492 3.224 4.512 7.2 4.512s7.2-2.02 7.2-4.512zM7.2 9.927l4.05 8.122h-8.1L7.2 9.927zm28.799 9.025c0-.913.075-.493-4.784-10.238-.993-1.99-3.836-1.995-4.83 0-4.9 9.829-4.784 9.354-4.784 10.238H21.6c0 2.492 3.224 4.512 7.2 4.512s7.2-2.02 7.2-4.512h-.001zm-11.249-.903l4.05-8.122 4.05 8.122h-8.1zm4.95 7.22h-9.9V8.644a4.513 4.513 0 002.61-3.23h7.29c.497 0 .9-.403.9-.902V2.707a.901.901 0 00-.9-.902h-8.12C20.759.715 19.468 0 18 0s-2.758.715-3.58 1.805H6.3c-.497 0-.9.404-.9.902v1.805c0 .499.403.903.9.903h7.29a4.513 4.513 0 002.61 3.229v16.625H6.3c-.497 0-.9.404-.9.903v1.805c0 .498.403.902.9.902h23.4c.497 0 .9-.404.9-.902v-1.805a.901.901 0 00-.9-.903z"/>
			          </g>
			          <defs>
			            <clipPath id="clip0">
			              <path fill="#fff" d="M0 0h36v28.879H0z"/>
			            </clipPath>
			          </defs>
			        </svg>
			        <?php esc_html_e('Manage Payments','marketking-multivendor-marketplace-for-woocommerce'); ?>
			    </div>
			    <?php
			    $active = get_user_meta($user_id,'marketking_active_withdrawal', true);
			    $amount = get_user_meta($user_id,'marketking_withdrawal_amount', true);
			    $time = get_user_meta($user_id,'marketking_withdrawal_time', true);
			    if ($active === 'yes'){
			    	?>
			    	<strong><?php 
			    	echo esc_html__('Active Withdrawal Request: ','marketking-multivendor-marketplace-for-woocommerce').' ';
			    	echo wc_price($amount).' - '.esc_html(date('F j, Y', $time)).'<br><br>';

			    	?></strong>
			    	<?php
			    }
			    ?>
			    <div class="marketking_user_registration_user_data_container_element">
			        <div class="marketking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Payment Amount','marketking-multivendor-marketplace-for-woocommerce'); ?>
			        </div>
			        <input type="number" id="marketking_reimbursement_value" class="marketking_user_registration_user_data_container_element_text marketking_user_registration_user_data_container_element_text_editable" placeholder="<?php esc_attr_e('Enter the amount that has been sent...','marketking-multivendor-marketplace-for-woocommerce');?>" <?php 
			        if ($active === 'yes'){
			        	echo 'value="'.esc_attr($amount).'"';
			        }
			    ?>>
			    </div>
			    <div class="marketking_user_registration_user_data_container_element">
			        <div class="marketking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Payment Method','marketking-multivendor-marketplace-for-woocommerce'); ?>
			        </div>
			        <input type="text" id="marketking_reimbursement_method" class="marketking_user_registration_user_data_container_element_text marketking_user_registration_user_data_container_element_text_editable" placeholder="<?php esc_attr_e('Enter payment method used here...','marketking-multivendor-marketplace-for-woocommerce');?>" <?php 

			        if ($active === 'yes'){
			        	?>
			        	value="<?php
			        	if ($method === 'paypal'){
			        	    $method = 'PayPal';
			        	} else if ($method === 'bank'){
			        	    $method = 'Bank';
			        	} else if ($method === 'stripe'){
			        	    $method = 'Stripe';
			        	} else if ($method === 'custom'){
			        	    $method = get_option( 'marketking_enable_custom_payouts_title_setting', '' );
			        	}
			        	echo $method;
			        	?>"
			        	<?php			        
			        }

			       ?>>
			    </div>
			    <div class="marketking_user_registration_user_data_container_element">
			        <div class="marketking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Note / Details','marketking-multivendor-marketplace-for-woocommerce'); ?>
			        </div>
			        <input type="text" id="marketking_reimbursement_note" class="marketking_user_registration_user_data_container_element_text marketking_user_registration_user_data_container_element_text_editable" placeholder="<?php esc_attr_e('Enter note / details here...','marketking-multivendor-marketplace-for-woocommerce');?>">
			    </div>
			    <div class="marketking_user_registration_user_data_container_element">
			        <div class="marketking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Bonus / Extra Payment (is not deducted from outstanding balance)','marketking-multivendor-marketplace-for-woocommerce'); ?>
			        </div>
			        <input type="checkbox" id="marketking_bonus_payment">
			    </div>
			    <button id="marketking_save_payment" type="button" class="button button-primary"><?php esc_html_e('Save Payment and Notify Vendor','marketking-multivendor-marketplace-for-woocommerce'); ?></button>

			    <br /><br /><br />
			  <!-- 3. TRANSACTION HISTORY SECTION -->
			    <div class="marketking_user_registration_user_data_container_title">
			        <svg class="marketking_user_registration_user_data_container_title_icon" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="none" viewBox="0 0 35 35">
			          <path fill="#C4C4C4" d="M29.531 0H3.281A3.29 3.29 0 000 3.281V31.72A3.29 3.29 0 003.281 35h26.25a3.29 3.29 0 003.282-3.281V3.28A3.29 3.29 0 0029.53 0zm-1.093 30.625H4.375V4.375h24.063v26.25zM8.75 15.312h15.313V17.5H8.75v-2.188zm0 4.376h15.313v2.187H8.75v-2.188zm0 4.375h15.313v2.187H8.75v-2.188zm0-13.125h15.313v2.187H8.75v-2.188z"/>
			        </svg>
			        <?php esc_html_e('Payouts History','marketking-multivendor-marketplace-for-woocommerce'); ?>
			    </div>
			    <div class="marketking_user_registration_user_data_container_element">
			        <div class="marketking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Current oustanding balance (unpaid earnings)','marketking-multivendor-marketplace-for-woocommerce'); ?>
			        </div>
			        <?php
			        $user_outstanding_earnings = get_user_meta($user_id,'marketking_outstanding_earnings', true);
			        if (empty($user_outstanding_earnings)){ // no earnings yet
			        	$user_outstanding_earnings = 0;
			        }
			        ?>
			        <input type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php echo strip_tags(wc_price($user_outstanding_earnings));?>" readonly>
			    </div>
			    <br />

			    <table id="marketking_payout_history_table">
			        <thead>
			            <tr>
			                <th><?php esc_html_e('Date','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <th><?php esc_html_e('Amount','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <th><?php esc_html_e('Payment Method','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <th><?php esc_html_e('Outstanding (Unpaid) Balance','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <th><?php esc_html_e('Note','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			            </tr>
			        </thead>
			        <tbody>
			            <?php
			            $user_payout_history = sanitize_text_field(get_user_meta($user_id,'marketking_user_payout_history', true));

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
			                if (isset($elements[5])){
			                	$bonus = $elements[5];
			                } else {
			                	$bonus = 'no';
			                }
			                ?>
			                <tr>
			                    <td data-order="<?php echo esc_attr(strtotime($date));?>"><?php echo esc_html($date);?></td>
			                    <td data-order="<?php echo esc_attr($amount);?>"><?php echo wc_price($amount);
			                    if ($bonus === 'yes'){
			                    	echo ' '.esc_html__('(bonus)','marketking-multivendor-marketplace-for-woocommerce');
			                    }
			                    ?></td>
			                    <td><?php echo esc_html($method);?></td>
			                    <td data-order="<?php echo esc_attr($oustanding_balance);?>"><?php echo wc_price($oustanding_balance);?></td>
			                    <td><?php echo esc_html($note);?></td>
			                </tr>
			                <?php
			            }
			            ?>
			       
			        </tbody>

			    </table>

			    <br><br>
			    <div class="marketking_user_registration_user_data_container_title">
			        <svg class="marketking_user_registration_user_data_container_title_icon" xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="none" viewBox="0 0 35 35">
			          <path fill="#C4C4C4" d="M29.531 0H3.281A3.29 3.29 0 000 3.281V31.72A3.29 3.29 0 003.281 35h26.25a3.29 3.29 0 003.282-3.281V3.28A3.29 3.29 0 0029.53 0zm-1.093 30.625H4.375V4.375h24.063v26.25zM8.75 15.312h15.313V17.5H8.75v-2.188zm0 4.376h15.313v2.187H8.75v-2.188zm0 4.375h15.313v2.187H8.75v-2.188zm0-13.125h15.313v2.187H8.75v-2.188z"/>
			        </svg>
			        <?php esc_html_e('Vendor Balance History & Manual Adjustments','marketking-multivendor-marketplace-for-woocommerce'); ?>
			    </div>
			    <div class="marketking_user_registration_user_data_container_element">
			        <div class="marketking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Current oustanding balance (unpaid earnings)','marketking-multivendor-marketplace-for-woocommerce'); ?>
			        </div>
			        <?php
			        $user_outstanding_earnings = get_user_meta($user_id,'marketking_outstanding_earnings', true);
			        if (empty($user_outstanding_earnings)){ // no earnings yet
			        	$user_outstanding_earnings = 0;
			        }
			        ?>
			        <input type="text" class="marketking_user_registration_user_data_container_element_text" value="<?php echo strip_tags(wc_price($user_outstanding_earnings));?>" readonly>
			    </div>

			    <div class="marketking_user_registration_user_data_container_element">
			        <div class="marketking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Manual Adjustment Amount','marketking-multivendor-marketplace-for-woocommerce'); ?>
			        </div>
			        <input type="number" id="marketking_adjustment_value" class="marketking_user_registration_user_data_container_element_text marketking_user_registration_user_data_container_element_text_editable" placeholder="<?php esc_attr_e('Enter the adjustment amount (you can enter a positive / negative value to increase / reduce balance).','marketking-multivendor-marketplace-for-woocommerce');?>">
			    </div>
			    <div class="marketking_user_registration_user_data_container_element">
			        <div class="marketking_user_registration_user_data_container_element_label">
			            <?php esc_html_e('Note / Details','marketking-multivendor-marketplace-for-woocommerce'); ?>
			        </div>
			        <input type="text" id="marketking_adjustment_note" class="marketking_user_registration_user_data_container_element_text marketking_user_registration_user_data_container_element_text_editable" placeholder="<?php esc_attr_e('Enter note / explanation here...','marketking-multivendor-marketplace-for-woocommerce');?>">
			    </div>
			    <br />
			    <button id="marketking_make_vendor_balance_adjustment" class="button button-primary" value="<?php echo esc_attr($user_id); ?>"><?php esc_html_e('Save Adjustment','marketking-multivendor-marketplace-for-woocommerce'); ?></button> &nbsp; 

			    <button id="marketking_download_vendor_balance_history" class="button button-secondary" value="<?php echo esc_attr($user_id); ?>"><?php esc_html_e('Download Vendor Balance History','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
			</div>

			<!--- END CCONTENT -->
			
		</div>
		<?php
	}

	function marketking_activate_notification(){
		if ( defined( 'WC_PLUGIN_FILE' ) && defined('MARKETKINGPRO_VERSION') ) {
			$license = get_option('marketking_license_key_setting', '');
			if (empty($license)){
				?>
				<div class="marketking_dismiss_review_notice notice notice-info is-dismissible marketking_main_notice">
					<?php
					$iconurl = plugins_url('../includes/assets/images/marketking-icon5.svg', __FILE__);
					?>
					<div class="marketking_notice_left_screen">
						<img src="<?php echo esc_attr($iconurl);?>" class="marketking_notice_icon">
					</div>
					<div class="marketking_notice_right_screen">
						<h3><?php esc_html_e('Welcome to MarketKing Pro!','marketking-multivendor-marketplace-for-woocommerce');?></h3>
						<p><?php esc_html_e('Please activate your license to get important plugin updates and premium support.','marketking-multivendor-marketplace-for-woocommerce');?></p>
						<a href="<?php echo esc_attr(admin_url('admin.php?page=marketking&tab=activate'));?>"><button type="button" class="button-primary marketking_notice_button"><?php esc_html_e('Activate License','marketking-multivendor-marketplace-for-woocommerce');?></button></a>
						<br><br>
					</div>
				</div>
				<?php
			}
		}
	}

	
	function marketking_reviews_page_prepare(){
		// if page is reviews
		if(get_current_screen()->id === 'marketking_page_marketking_reviews'){
			global $marketking_page_reviews;
			ob_start();
			$this->marketking_reviews_page_content();
			$marketking_page_reviews = ob_get_clean();
		}
	}

	function marketking_reviews_page_display(){
		global $marketking_page_reviews;
		echo $marketking_page_reviews;
	}

	public static function marketking_groups_page_content(){
		echo self::get_header_bar();
		?>
		<div id="marketking_admin_groups_main_container">
			<div class="marketking_admin_groups_main_title">
				<?php esc_html_e('Groups', 'marketking-multivendor-marketplace-for-woocommerce'); ?>
			</div>
			<div class="marketking_admin_groups_main_container_main_row">
				<div class="marketking_admin_groups_main_container_main_row_left">
					<div class="marketking_admin_groups_main_container_main_row_title">
						<?php esc_html_e('Vendor Groups','marketking-multivendor-marketplace-for-woocommerce'); ?>
					</div>
					<div class="marketking_admin_groups_main_container_main_row_subtitle">
						<?php esc_html_e('Create, edit and manage vendor user groups & permissions.','marketking-multivendor-marketplace-for-woocommerce'); ?>
					</div>
					<a href="<?php echo admin_url( 'edit.php?post_type=marketking_group'); ?>" class="marketking_admin_groups_box_link">
						<div class="marketking_admin_groups_main_container_main_row_left_box marketking_groups_left_box">
							<svg class="marketking_admin_groups_main_container_main_row_left_box_icon" xmlns="http://www.w3.org/2000/svg" width="93" height="80" fill="#CED8E2"  viewBox="0 0 93 80">
							  <path d="M46.362 38.944c7.618 0 13.793-6.098 13.793-13.619S53.98 11.706 46.362 11.706c-7.617 0-13.793 6.098-13.793 13.619s6.176 13.619 13.793 13.619zM25.441 26.687h1.391V25.44A19.414 19.414 0 0137.698 8.055 13.387 13.387 0 1025.5 26.774l-.058-.087zM65.95 25.325v1.246h1.39A13.271 13.271 0 1055.056 7.968 19.414 19.414 0 0165.95 25.325zM58.793 40.306A62.154 62.154 0 0175.338 46.1a7.85 7.85 0 011.97 1.536h15.416v-9.91a2.086 2.086 0 00-1.101-1.855 52.158 52.158 0 00-24.34-5.94H65.37a19.298 19.298 0 01-6.578 10.374zM13.126 53.258a7.998 7.998 0 014.26-7.1 62.155 62.155 0 0116.545-5.794 19.299 19.299 0 01-6.577-10.287H25.44a52.158 52.158 0 00-24.34 5.94 2.086 2.086 0 00-1.1 1.855v18.516h13.125v-3.13zM56.388 69.977h17.27v4.057h-17.27v-4.057z"/>
							  <path d="M89.276 54.243H69.369v-2.897a2.898 2.898 0 00-5.795 0v2.897h-5.621v-8.2a58.447 58.447 0 00-11.59-1.246A55.837 55.837 0 0020.11 51.23a2.202 2.202 0 00-1.188 1.97v16.256h20.602v17.646A2.898 2.898 0 0042.42 90h46.855a2.898 2.898 0 002.898-2.898V57.141a2.898 2.898 0 00-2.898-2.898zm-2.898 30.02h-41.06V60.039h18.256v2.636a2.898 2.898 0 005.795 0V60.04h17.01v24.224z"/>
							</svg>
							<div class="marketking_admin_groups_main_container_main_row_box_text">
								<?php esc_html_e('Go to Vendor Groups','marketking-multivendor-marketplace-for-woocommerce'); ?>
							</div>
						</div>
					</a>
				</div>
				<div class="marketking_admin_groups_main_container_main_row_right">
					<div class="marketking_admin_groups_main_container_main_row_title">
						<?php esc_html_e('Group Transfer Rules','marketking-multivendor-marketplace-for-woocommerce'); ?>
					</div>
					<div class="marketking_admin_groups_main_container_main_row_subtitle">
						<?php esc_html_e('Transfer vendors across groups automatically (e.g. group ranks or tiers).','marketking-multivendor-marketplace-for-woocommerce'); ?>
					</div>
					<div class="marketking_admin_groups_main_container_main_row_right_boxes">
						<a href="<?php echo admin_url( 'edit.php?post_type=marketking_grule'); ?>" class="marketking_admin_groups_box_link">
							<div class="marketking_admin_groups_main_container_main_row_right_box marketking_admin_groups_main_container_main_row_right_box_first marketking_groups_right_box">

								<svg class="marketking_admin_groups_main_container_main_row_right_box_icon_first"  xmlns="http://www.w3.org/2000/svg" width="79" height="71" fill="#62666A" viewBox="0 0 79 71">
								  <path d="M22 .5a8.75 8.75 0 00-8.75 8.75v17.5H22V9.25h35v13.125H46.062l15.313 15.313 15.313-15.313H65.75V9.25A8.75 8.75 0 0057 .5H22zM.125 35.5v8.75h35V35.5h-35zm0 13.125v8.75h35v-8.75h-35zm43.75 0v8.75h35v-8.75h-35zM.125 61.75v8.75h35v-8.75h-35zm43.75 0v8.75h35v-8.75h-35z"/>
								</svg>
								<div class="marketking_admin_groups_main_container_main_row_right_box_text marketking_admin_groups_main_container_main_row_right_box_first_text">
									<?php esc_html_e('Go to Group Rules','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</div>
						</a>
						
					</div>
				</div>
			</div>
		</div>
		<?php

	}

	public static function marketking_reviews_page_content(){

		echo self::get_header_bar();

		// WooCommerce 6.7.0 add reviews to comments
		add_filter(
			'comments_list_table_query_args',
			function( $args ) : array {

				if (!is_array($args['post_type'])){
					$args['post_type'] = array();
				}
				$args['post_type']['product'] = 'product';

				return $args;
			}, 100
		);
		
		/**
		 * Edit Comments Administration Screen.
		 *
		 * @package WordPress
		 * @subpackage Administration
		 */

		global $comment_type;
		$comment_type = 'review';
		$_REQUEST['comment_type'] = 'review';

		add_filter('admin_comment_types_dropdown', function($val){return false;});

		/** WordPress Administration Bootstrap */
		$admin_path = str_replace( get_bloginfo( 'url' ) . '/', ABSPATH, get_admin_url() );

		require_once ABSPATH . 'wp-admin/admin.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-comments-list-table.php';
		require_once ( MARKETKINGCORE_DIR . 'admin/class-marketking-reviews-page.php' );

		$wp_list_table = new WP_Comments_List_Table_Reviews;

		$pagenum       = $wp_list_table->get_pagenum();

		$doaction = $wp_list_table->current_action();

		if ( $doaction ) {
			check_admin_referer( 'bulk-comments' );

			if ( 'delete_all' === $doaction && ! empty( $_REQUEST['pagegen_timestamp'] ) ) {
				$comment_status = wp_unslash( $_REQUEST['comment_status'] );
				$delete_time    = wp_unslash( $_REQUEST['pagegen_timestamp'] );
				$comment_ids    = $wpdb->get_col( $wpdb->prepare( "SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = %s AND %s > comment_date_gmt", $comment_status, $delete_time ) );
				$doaction       = 'delete';
			} elseif ( isset( $_REQUEST['delete_comments'] ) ) {
				$comment_ids = $_REQUEST['delete_comments'];
				$doaction    = $_REQUEST['action'];
			} elseif ( isset( $_REQUEST['ids'] ) ) {
				$comment_ids = array_map( 'absint', explode( ',', $_REQUEST['ids'] ) );
			} elseif ( wp_get_referer() ) {
				wp_safe_redirect( wp_get_referer() );
				exit;
			}

			$approved   = 0;
			$unapproved = 0;
			$spammed    = 0;
			$unspammed  = 0;
			$trashed    = 0;
			$untrashed  = 0;
			$deleted    = 0;

			$redirect_to = remove_query_arg( array( 'trashed', 'untrashed', 'deleted', 'spammed', 'unspammed', 'approved', 'unapproved', 'ids' ), wp_get_referer() );
			$redirect_to = add_query_arg( 'paged', $pagenum, $redirect_to );

			wp_defer_comment_counting( true );

			foreach ( $comment_ids as $comment_id ) { // Check the permissions on each.
				if ( ! current_user_can( 'edit_comment', $comment_id ) ) {
					continue;
				}

				switch ( $doaction ) {
					case 'approve':
						wp_set_comment_status( $comment_id, 'approve' );
						$approved++;
						break;
					case 'unapprove':
						wp_set_comment_status( $comment_id, 'hold' );
						$unapproved++;
						break;
					case 'spam':
						wp_spam_comment( $comment_id );
						$spammed++;
						break;
					case 'unspam':
						wp_unspam_comment( $comment_id );
						$unspammed++;
						break;
					case 'trash':
						wp_trash_comment( $comment_id );
						$trashed++;
						break;
					case 'untrash':
						wp_untrash_comment( $comment_id );
						$untrashed++;
						break;
					case 'delete':
						wp_delete_comment( $comment_id );
						$deleted++;
						break;
				}
			}

			if ( ! in_array( $doaction, array( 'approve', 'unapprove', 'spam', 'unspam', 'trash', 'delete' ), true ) ) {
				$screen = get_current_screen()->id;

				/** This action is documented in wp-admin/edit.php */
				$redirect_to = apply_filters( "handle_bulk_actions-{$screen}", $redirect_to, $doaction, $comment_ids ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			}

			wp_defer_comment_counting( false );

			if ( $approved ) {
				$redirect_to = add_query_arg( 'approved', $approved, $redirect_to );
			}
			if ( $unapproved ) {
				$redirect_to = add_query_arg( 'unapproved', $unapproved, $redirect_to );
			}
			if ( $spammed ) {
				$redirect_to = add_query_arg( 'spammed', $spammed, $redirect_to );
			}
			if ( $unspammed ) {
				$redirect_to = add_query_arg( 'unspammed', $unspammed, $redirect_to );
			}
			if ( $trashed ) {
				$redirect_to = add_query_arg( 'trashed', $trashed, $redirect_to );
			}
			if ( $untrashed ) {
				$redirect_to = add_query_arg( 'untrashed', $untrashed, $redirect_to );
			}
			if ( $deleted ) {
				$redirect_to = add_query_arg( 'deleted', $deleted, $redirect_to );
			}
			if ( $trashed || $spammed ) {
				$redirect_to = add_query_arg( 'ids', implode( ',', $comment_ids ), $redirect_to );
			}

			wp_safe_redirect( $redirect_to );
			exit;
		} elseif ( ! empty( $_GET['_wp_http_referer'] ) ) {
			wp_redirect( remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
			exit;
		}


		$wp_list_table->prepare_items();
		wp_enqueue_script( 'admin-comments' );
		enqueue_comment_hotkeys_js();

		$comments_count = wp_count_comments();

		if ( $comments_count->moderated > 0 ) {
			// Used in the HTML title tag.
			$title = sprintf(
				/* translators: %s: Comments count. */
				esc_html__( 'Reviews (%s)','marketking-multivendor-marketplace-for-woocommerce' ),
				number_format_i18n( $comments_count->moderated )
			);
		} else {
			// Used in the HTML title tag.
			$title = esc_html__( 'Reviews','marketking-multivendor-marketplace-for-woocommerce' );
		}

		add_screen_option( 'per_page' );

	
		get_current_screen()->set_screen_reader_content(
			array(
				'heading_views'      => esc_html__( 'Filter reviews list','marketking-multivendor-marketplace-for-woocommerce' ),
				'heading_pagination' => esc_html__( 'Reviews list navigation','marketking-multivendor-marketplace-for-woocommerce' ),
				'heading_list'       => esc_html__( 'Reviews list','marketking-multivendor-marketplace-for-woocommerce' ),
			)
		);

		require_once ABSPATH . 'wp-admin/admin-header.php';
		?>

		<div class="wrap">
		<h1 class="wp-heading-inline">
		<?php
		esc_html_e( 'Reviews','marketking-multivendor-marketplace-for-woocommerce' );

		?>
		</h1>

		<?php

		if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
			echo '<span class="subtitle">';
			printf(
				/* translators: %s: Search query. */
				esc_html__( 'Search results for: %s' ),
				'<strong>' . esc_html( wp_unslash( $_REQUEST['s'] ) ) . '</strong>'
			);
			echo '</span>';
		}
		?>

		<hr class="wp-header-end">

		<?php
		if ( isset( $_REQUEST['error'] ) ) {
			$error     = (int) $_REQUEST['error'];
			$error_msg = '';
			switch ( $error ) {
				case 1:
					$error_msg = esc_html__( 'Invalid review ID.' );
					break;
				case 2:
					$error_msg = esc_html__( 'Sorry, you are not allowed to edit reviews on this post.' );
					break;
			}
			if ( $error_msg ) {
				echo '<div id="moderated" class="error"><p>' . $error_msg . '</p></div>';
			}
		}

		if ( isset( $_REQUEST['approved'] ) || isset( $_REQUEST['deleted'] ) || isset( $_REQUEST['trashed'] ) || isset( $_REQUEST['untrashed'] ) || isset( $_REQUEST['spammed'] ) || isset( $_REQUEST['unspammed'] ) || isset( $_REQUEST['same'] ) ) {
			$approved  = isset( $_REQUEST['approved'] ) ? (int) $_REQUEST['approved'] : 0;
			$deleted   = isset( $_REQUEST['deleted'] ) ? (int) $_REQUEST['deleted'] : 0;
			$trashed   = isset( $_REQUEST['trashed'] ) ? (int) $_REQUEST['trashed'] : 0;
			$untrashed = isset( $_REQUEST['untrashed'] ) ? (int) $_REQUEST['untrashed'] : 0;
			$spammed   = isset( $_REQUEST['spammed'] ) ? (int) $_REQUEST['spammed'] : 0;
			$unspammed = isset( $_REQUEST['unspammed'] ) ? (int) $_REQUEST['unspammed'] : 0;
			$same      = isset( $_REQUEST['same'] ) ? (int) $_REQUEST['same'] : 0;

			if ( $approved > 0 || $deleted > 0 || $trashed > 0 || $untrashed > 0 || $spammed > 0 || $unspammed > 0 || $same > 0 ) {
				if ( $approved > 0 ) {
					/* translators: %s: Number of comments. */
					$messages[] = sprintf( _n( '%s review approved.', '%s reviews approved.', $approved ), $approved );
				}

				if ( $spammed > 0 ) {
					$ids = isset( $_REQUEST['ids'] ) ? $_REQUEST['ids'] : 0;
					/* translators: %s: Number of comments. */
					$messages[] = sprintf( _n( '%s review marked as spam.', '%s reviews marked as spam.', $spammed ), $spammed ) . ' <a href="' . esc_url( wp_nonce_url( "admin.php?page=marketking_reviews&doaction=undo&action=unspam&ids=$ids", 'bulk-comments' ) ) . '">' . esc_html__( 'Undo' ) . '</a><br />';
				}

				if ( $unspammed > 0 ) {
					/* translators: %s: Number of comments. */
					$messages[] = sprintf( _n( '%s comment restored from the spam.', '%s comments restored from the spam.', $unspammed ), $unspammed );
				}

				if ( $trashed > 0 ) {
					$ids = isset( $_REQUEST['ids'] ) ? $_REQUEST['ids'] : 0;
					/* translators: %s: Number of comments. */
					$messages[] = sprintf( _n( '%s comment moved to the Trash.', '%s comments moved to the Trash.', $trashed ), $trashed ) . ' <a href="' . esc_url( wp_nonce_url( "admin.php?page=marketking_reviews&doaction=undo&action=untrash&ids=$ids", 'bulk-comments' ) ) . '">' . esc_html__( 'Undo' ) . '</a><br />';
				}

				if ( $untrashed > 0 ) {
					/* translators: %s: Number of comments. */
					$messages[] = sprintf( _n( '%s review restored from the Trash.', '%s reviews restored from the Trash.', $untrashed ), $untrashed );
				}

				if ( $deleted > 0 ) {
					/* translators: %s: Number of comments. */
					$messages[] = sprintf( _n( '%s review permanently deleted.', '%s reviews permanently deleted.', $deleted ), $deleted );
				}

				if ( $same > 0 ) {
					$comment = get_comment( $same );
					if ( $comment ) {
						switch ( $comment->comment_approved ) {
							case '1':
								$messages[] = esc_html__( 'This review is already approved.' ) . ' <a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$same" ) ) . '">' . esc_html__( 'Edit review' ) . '</a>';
								break;
							case 'trash':
								$messages[] = esc_html__( 'This review is already in the Trash.' ) . ' <a href="' . esc_url( admin_url( 'admin.php?page=marketking_reviews&comment_status=trash' ) ) . '"> ' . esc_html__( 'View Trash' ) . '</a>';
								break;
							case 'spam':
								$messages[] = esc_html__( 'This review is already marked as spam.' ) . ' <a href="' . esc_url( admin_url( "comment.php?action=editcomment&c=$same" ) ) . '">' . esc_html__( 'Edit review' ) . '</a>';
								break;
						}
					}
				}

				echo '<div id="moderated" class="updated notice is-dismissible"><p>' . implode( "<br/>\n", $messages ) . '</p></div>';
			}
		}
		?>

		<?php // $wp_list_table->views(); ?>

		<form id="comments-form" method="get">

		<?php // $wp_list_table->search_box( esc_html__( 'Search Reviews' ), 'comment' ); ?>

		<?php if (isset($comment_status)){
			?>
			<input type="hidden" name="comment_status" value="<?php echo esc_attr( $comment_status ); ?>" />

			<?php
		}
		?>
		<input type="hidden" name="pagegen_timestamp" value="<?php echo esc_attr( current_time( 'mysql', 1 ) ); ?>" />

		<input type="hidden" name="_total" value="<?php echo esc_attr( $wp_list_table->get_pagination_arg( 'total_items' ) ); ?>" />
		<input type="hidden" name="_per_page" value="<?php echo esc_attr( $wp_list_table->get_pagination_arg( 'per_page' ) ); ?>" />
		<input type="hidden" name="_page" value="<?php echo esc_attr( $wp_list_table->get_pagination_arg( 'page' ) ); ?>" />
		<input type="hidden" name="page" value="<?php echo esc_attr( 'marketking_reviews' ); ?>" />

		<?php if ( isset( $_REQUEST['paged'] ) ) { ?>
			<input type="hidden" name="paged" value="<?php echo esc_attr( absint( $_REQUEST['paged'] ) ); ?>" />
		<?php } ?>

		<?php 

		$wp_list_table->display(); ?>

		</form>
		</div>

		<div id="ajax-response"></div>

		<?php
		wp_comment_reply( '-1', true, 'detail' );
		wp_comment_trashnotice();
		require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
		<?php
	}

	public static function marketking_vendors_page_content(){
		echo self::get_header_bar();

		// get all vendors
		$users = marketking()->get_all_vendors();
		
		?>

		<div class="marketking_page_title_container">
			<h1 class="marketking_page_title"><?php esc_html_e('Vendors','marketking-multivendor-marketplace-for-woocommerce');?></h1>
			<div class="marketking_add_new_container"><a href="<?php echo esc_attr(admin_url( 'user-new.php')).'?add=vendor'; ?>" class="marketking-btn marketking-btn-outline-light marketking-btn-dim marketking-btn-sm marketking_manage_vendors_button"><em class="icon marketking-ni marketking-ni-pen2"></em><span><?php esc_html_e('Add New','marketking-multivendor-marketplace-for-woocommerce');?></span></a></div>
			
		</div>
		<div id="marketking_admin_vendors_table_container">
			<table id="marketking_admin_vendors_table">
		        <thead>
		            <tr>
		                <th><?php esc_html_e('Vendor','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
		                <th><?php esc_html_e('Vendor Group','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
		                <th><?php esc_html_e('Contact Info','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
		                <?php do_action('marketking_vendors_table_column_header'); ?>
		                <?php
		                if (apply_filters('marketking_show_vendor_total_sales_column', true)){
		                	?>
		                	<th><?php esc_html_e('Total Sales Value','marketking-multivendor-marketplace-for-woocommerce'); ?></th>

		                	<?php
		                }
		                ?>
		                <th><?php esc_html_e('Actions','marketking-multivendor-marketplace-for-woocommerce'); ?></th>

		            </tr>
		        </thead>
		        <tbody>
		        	<?php

		        	foreach ( $users as $user ) {

		        		$user_id = $user->ID;
		        		$original_user_id = $user_id;
		        		$username = $user->user_login;
		        		$store_name = marketking()->get_store_name_display($user_id);

		        		$group_name = get_the_title(get_user_meta($user_id, 'marketking_group', true));
		        		
		        		if (get_user_meta($user_id, 'marketking_group', true) === 'none'){
		        			$group_name = '<i>'.esc_html__('Inactive Vendor - No Group','marketking-multivendor-marketplace-for-woocommerce').'</i>';
		        		}

		        		if (empty($group_name)){
		        			$group_name = '-';
		        		}
       		
		        		$profile_pic = get_user_meta($user_id,'marketking_profile_logo_image', true);
		        		if (empty($profile_pic)){
		        			$profile_pic = plugins_url('../includes/assets/images/store-profile.png', __FILE__);
		        		}

		        		$store_phone = get_user_meta($user_id, 'marketking_store_phone', true);
		        		$store_email = get_user_meta($user_id, 'marketking_store_email', true);

		        		if (!empty($store_email) && !empty($store_phone)){
		        			$contact_info = $store_email.' - '.$store_phone;
		        		} else if (!empty($store_email)){
		        			$contact_info = $store_email;
		        		} else if (!empty($store_phone)){
		        			$contact_info = $store_phone;
		        		} else {
		        			$contact_info = '-';
		        		}

		        		if (apply_filters('marketking_show_vendor_total_sales_column', true)){

		        			$total_sales = marketking()->get_vendor_total_sales($user_id);
		        		}

		        		$vacation = '';
		        		if (marketking()->is_on_vacation($user_id)){
		        			$vacation = ' <i>'.esc_html__('(on vacation)','marketking-multivendor-marketplace-for-woocommerce').'</i>';
		        		}

		        		echo
		        		'<tr>
		        		    <td class="marketking_vendor_td"><img class="marketking_vendor_profile" src='.esc_attr($profile_pic).'><a href="'.esc_attr(get_edit_user_link($original_user_id)).'#marketking_user_vendor_profile">'.esc_html( $store_name ).$vacation.'</a></td>
		        		    <td>'.( $group_name ).'</td>
		        		    <td>'.esc_html( $contact_info ).'</td>';

		        		    do_action('marketking_vendors_table_column_content', $user);

		        		    if (apply_filters('marketking_show_vendor_total_sales_column', true)){
		        		   		echo'<td data-order="'.$total_sales.'">'.wc_price( $total_sales ).'</td>';
		        		   	}
		        		    ?>
		        		    <td>
		        		    	
		        		    	<a class="marketking_vendor_link" href="<?php echo esc_attr( admin_url( 'edit.php?post_type=product' ).'&author='.$original_user_id );?>"><button class="marketking-btn marketking-btn-outline-light marketking-btn-sm marketking_manage_vendors_button"><em class="icon marketking-ni marketking-ni-package-fill"></em><span><?php esc_html_e('Products','marketking-multivendor-marketplace-for-woocommerce');?></span></button></a>
		        		    	</a>
		        		    	<a class="marketking_vendor_link" href="<?php echo esc_attr( admin_url( 'edit.php?post_type=shop_order' ).'&author='.$original_user_id );?>"><button class="marketking-btn marketking-btn-outline-light marketking-btn-sm marketking_manage_vendors_button"><em class="icon marketking-ni marketking-ni-bag-fill"></em><span><?php esc_html_e('Orders','marketking-multivendor-marketplace-for-woocommerce');?></span></button></a>
		        		    	</a>
		        		    	<a class="marketking_vendor_link" href="<?php echo esc_attr(admin_url('admin.php?page=marketking_view_payouts').'&user='.$original_user_id);?>"><button class="marketking-btn marketking-btn-outline-light marketking-btn-sm marketking_manage_payouts_button" value="<?php echo esc_attr($original_user_id); ?>"><em class="icon marketking-ni marketking-ni-wallet-out"></em><span><?php esc_html_e('Payouts','marketking-multivendor-marketplace-for-woocommerce');?></span></button></a>
		        		    	<a class="marketking_vendor_link" href="<?php echo esc_attr(get_edit_user_link($original_user_id));?>#marketking_user_vendor_profile"> <button class="marketking-btn marketking-btn-light-blue marketking-btn-sm marketking_manage_vendors_button"><em class="icon marketking-ni marketking-ni-user-fill-c"></em><span><?php esc_html_e('Vendor Profile','marketking-multivendor-marketplace-for-woocommerce');?></span></button></a>
		        		    </td>
		        		    <?php
		        		echo '</tr>';
		        	}

		        	?>
		           
		        </tbody>
		        <tfoot>
		            <tr>
		                <th><?php esc_html_e('Vendor','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
		                <th><?php esc_html_e('Vendor Group','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
		                <th><?php esc_html_e('Contact Info','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
		                <?php
		                if (apply_filters('marketking_show_vendor_total_sales_column', true)){
		                	?>
			                <th><?php esc_html_e('Total Sales Value','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <?php
			            }
			            ?>
		                <th><?php esc_html_e('Actions','marketking-multivendor-marketplace-for-woocommerce'); ?></th>

		            </tr>
		        </tfoot>
		    </table>
		</div>
		<?php

	}

	public static function marketking_payouts_page_content(){
		echo self::get_header_bar();

		// get all vendors
		$users = marketking()->get_all_vendors();
		?>

		<h1 class="marketking_page_title"><?php esc_html_e('Payouts','marketking-multivendor-marketplace-for-woocommerce');?></h1>

		<div id="marketking_admin_payouts_table_container">
			<table id="marketking_admin_payouts_table">
			        <thead>
			            <tr>
			                <th><?php esc_html_e('Vendor','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <th><?php esc_html_e('Vendor Group','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <th><?php esc_html_e('Last Payment','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <th><?php esc_html_e('Outstanding Balance','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <?php
			                if (defined('MARKETKINGPRO_DIR')){
			                	if (intval(get_option('marketking_enable_withdrawals_setting', 1)) === 1){
			                		?>
			                		<th><?php esc_html_e('Withdrawal Requests','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                		<?php
			                	}
			                }
			                ?>
			                <th><?php esc_html_e('Actions','marketking-multivendor-marketplace-for-woocommerce'); ?></th>

			            </tr>
			        </thead>
			        <tbody>
			        	<?php

			        	foreach ( $users as $user ) {

			        		$user_id = $user->ID;
			        		$original_user_id = $user_id;
			        		$username = $user->user_login;
			        		$store_name = marketking()->get_store_name_display($user_id);

			        		$group_name = get_the_title(get_user_meta($user_id, 'marketking_group', true));
			        		if (empty($group_name)){
			        			$group_name = '-';
			        		}
	       		
			        		$profile_pic = get_user_meta($user_id,'marketking_profile_logo_image', true);
			        		if (empty($profile_pic)){
			        			$profile_pic = plugins_url('../includes/assets/images/store-profile.png', __FILE__);
			        		}

			        		$user_outstanding_earnings = get_user_meta($user_id,'marketking_outstanding_earnings', true);
			        		if (empty($user_outstanding_earnings)){ // no earnings yet
			        			$user_outstanding_earnings = 0;
			        		}

			        		$user_payout_history = sanitize_text_field(get_user_meta($user_id,'marketking_user_payout_history', true));

			        		if ($user_payout_history){
			        		    $transactions = explode(';', $user_payout_history);
			        		    $transactions = array_filter($transactions);
		    	        		$transactions = array_reverse($transactions);
		            		    $elements = explode(':', $transactions[0]);
		            		    $last_payment = $elements[0];
		            		    $last_payment = date('F j, Y', strtotime($last_payment));
			        		} else {
			        		    // empty, no transactions
			        		    $transactions = array();
			        		    $last_payment = esc_html__('No payment yet', 'marketking-multivendor-marketplace-for-woocommerce');
			        		}

			        		
			        		echo
			        		'<tr>
			        		   <td class="marketking_vendor_td"><img class="marketking_vendor_profile" src='.esc_attr($profile_pic).'><a href="'.esc_attr(get_edit_user_link($original_user_id)).'#marketking_user_profile_customer_vendor">'.esc_html( $store_name ).'</a></td>
		        		    	<td>'.esc_html( $group_name ).'</td>
			        		    <td data-order="'.esc_attr(strtotime($last_payment)).'">'.esc_html( $last_payment ).'</td>
			        		    <td data-order="'.esc_attr($user_outstanding_earnings).'">'.wc_price( $user_outstanding_earnings ).'</td>';

			        		    if (defined('MARKETKINGPRO_DIR')){
			        		    	if (intval(get_option('marketking_enable_withdrawals_setting', 1)) === 1){
			        		    		$active = get_user_meta($user_id,'marketking_active_withdrawal', true);
			        		    		$amount = get_user_meta($user_id,'marketking_withdrawal_amount', true);
			        		    		$time = get_user_meta($user_id,'marketking_withdrawal_time', true);
			        		    		if ($active === 'yes'){
			        		    			?>
			        		    			<td data-order="<?php echo esc_attr($time); ?>"><?php echo wc_price($amount).' - '.esc_html(date('F j, Y', $time)); ?></td>
			        		    			<?php
			        		    		} else {
			        		    			?>
			        		    			<td>-</td>
			        		    			<?php
			        		    		}
			        		    		
			        		    	}
			        		    }

			        		    echo '<td><a class="marketking_vendor_link" href="'.admin_url( 'admin.php?page=marketking_view_payouts').'&user='.esc_attr($original_user_id).'" ><button class="marketking-btn marketking-btn-light-blue marketking-btn-sm marketking_manage_payouts_button" value="'.esc_attr($original_user_id).'"><em class="icon marketking-ni marketking-ni-wallet-out"></em><span>'. esc_html__('View Payouts','marketking-multivendor-marketplace-for-woocommerce').'</span></button></a></td>

			        		</tr>';
			        	}

			        	?>
			           
			        </tbody>
			        <tfoot>
			            <tr>
			                <th><?php esc_html_e('Vendor','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <th><?php esc_html_e('Vendor Group','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <th><?php esc_html_e('Last Payment','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <th><?php esc_html_e('Outstanding Balance','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                <?php
			                if (defined('MARKETKINGPRO_DIR')){
			                	if (intval(get_option('marketking_enable_withdrawals_setting', 1)) === 1){
			                		?>
			                		<th><?php esc_html_e('Withdrawal Requests','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                		<?php
			                	}
			                }
			                ?>
			                <th><?php esc_html_e('Actions','marketking-multivendor-marketplace-for-woocommerce'); ?></th>

			            </tr>
			        </tfoot>
			    </table>
			</div>
		<?php

	}

	public static function marketking_modules_page_content(){
		echo self::get_header_bar();
		?>

		<div class="wrap plugin-install-tab-featured marketking_pro_modules_container">
			<div class="marketking_core_modules_header">
				<div>
					<h1 class="wp-heading-inline"><strong><?php esc_html_e('Pro Modules','marketking-multivendor-marketplace-for-woocommerce');?></strong><?php
					if (!defined('MARKETKINGPRO_DIR')){
						?><span class="dashicons dashicons-lock"></span><?php
					}
					?></h1>
					<?php if (!defined('MARKETKINGPRO_DIR')){
						?>
						<a href="#" class="marketkingproswitch"><strong><?php esc_html_e('(Unlock all with a Premium License! - 35% OFF TODAY)','marketking-multivendor-marketplace-for-woocommerce');?></strong></a>
						<?php
					}?>
				</div>
				<?php if (defined('MARKETKINGPRO_DIR')){
					?>
					<div class="marketking_modules_buttons">
						<button type="button" name="marketking_disable_all_modules" class="marketking_disable_all_modules button button-secondary"><?php esc_html_e('Disable all','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
						<button type="button" name="marketking_enable_all_modules" class="marketking_enable_all_modules button button-secondary"><?php esc_html_e('Enable all','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
						<button type="button" name="marketking_save_modules_settings" class="marketking_save_modules_settings button button-primary"><?php esc_html_e('Save Settings','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
					</div>
					<?php
				}
				?>
			</div>
			
			<form class="plugin-filter" method="post">			
				<div class="wp-list-table widefat plugin-install">
					<div id="the-list" class="marketking_pro_plugin_cards">
						<?php 
						self::marketking_display_modules_cards('pro');
						?>
					</div>
				</div>			
			</form>
		</div>

		<div class="wrap plugin-install-tab-featured">
			<div class="marketking_core_modules_header">
				<div>
				<h1 class="wp-heading-inline"><strong><?php esc_html_e('Pro Plugin Integrations','marketking-multivendor-marketplace-for-woocommerce');?></strong></h1>
					<?php if (!defined('MARKETKINGPRO_DIR')){
						?>
						<a href="#" class="marketkingproswitch"><strong><?php esc_html_e('(Unlock all with a Premium License! - 35% OFF TODAY)','marketking-multivendor-marketplace-for-woocommerce');?></strong></a>
						<?php
					}?>
				</div>
				<?php if (defined('MARKETKINGPRO_DIR')){
					?>
					<div class="marketking_modules_buttons">
						<button type="button" name="marketking_disable_all_modules" class="marketking_disable_all_modules button button-secondary"><?php esc_html_e('Disable all','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
						<button type="button" name="marketking_enable_all_modules" class="marketking_enable_all_modules button button-secondary"><?php esc_html_e('Enable all','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
						<button type="button" name="marketking_save_modules_settings" class="marketking_save_modules_settings button button-primary"><?php esc_html_e('Save Settings','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
					</div>
					<?php
				}
				?>
			</div>
			<hr class="wp-header-end">
			<form class="plugin-filter" method="post">			
				<div class="wp-list-table widefat plugin-install">
					<div id="the-list" class="marketking_pro_plugin_cards">
						<?php 
						self::marketking_display_modules_cards('integrations');
						?>
					</div>
				</div>			
			</form>
		</div>

		
			
			<?php
		
	}

	public static function marketking_display_modules_cards($type, $preload = false){

		// title, description, documentation link, image
		$integration_modules = array(
			array('title' => 'Wholesale & B2B - B2BKing Integration', 'description' => 'Adds wholesale prices, B2B, minimums, quote requests, etc. Plugin integration with B2BKing.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/wholesale-b2b-b2bking-integration/', 'image' => plugins_url('../includes/assets/images/module-b2bking.png', __FILE__), 'slug' => 'b2bkingintegration', 'cardbottom' => 'Requires the B2BKing Pro plugin installed.'),
		);

		$pro_modules = array(
			array('title' => 'Announcements', 'description' => 'Write and publish announcements for your marketplace vendors.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/announcements/', 'image' => plugins_url('../includes/assets/images/module-announce.png', __FILE__), 'slug' => 'announcements'),
			array('title' => 'Messaging', 'description' => 'Allow conversations and communication with vendors via the messaging module.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/messaging/', 'image' => plugins_url('../includes/assets/images/module-messaging.png', __FILE__), 'slug' => 'messages'),
			array('title' => 'Vendor Registration', 'description' => 'Configure registration options and custom fields for vendor registration.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/vendor-registration/', 'image' => plugins_url('../includes/assets/images/module-registration3.png', __FILE__), 'slug' => 'registration'),
			array('title' => 'Coupon Management', 'description' => 'Allow vendors to create and manage coupons for their products.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/vendor-coupon-management/', 'image' => plugins_url('../includes/assets/images/module-coupons5.png', __FILE__), 'slug' => 'coupons'),
			array('title' => 'Vendor Withdrawals', 'description' => 'Allows vendors to make withdrawal requests, rather than paying them on a fixed schedule.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/vendor-withdrawals/', 'image' => plugins_url('../includes/assets/images/module-payouts.png', __FILE__),'slug' => 'withdrawals'),
			array('title' => 'Product & Vendor Inquiries', 'description' => 'Adds an inquiry form to vendor or product pages. Works via email or the messaging module.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/product-vendor-inquiries/', 'image' => plugins_url('../includes/assets/images/module-inquiry.png', __FILE__), 'slug' => 'inquiries'),
			array('title' => 'Color Scheme Customizer', 'description' => 'Allows customization of the vendor dashboard color scheme.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/color-scheme-customization/', 'image' => plugins_url('../includes/assets/images/module-customize5.png', __FILE__), 'slug' => 'colorscheme'),
			array('title' => 'Vendor Vacation', 'description' => 'Allows your vendors to enter vacation mode when they temporarily pause their service.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/vendor-vacation/', 'image' => plugins_url('../includes/assets/images/vendor-vacation.png', __FILE__), 'slug' => 'vacation'),
			array('title' => 'Store Notice', 'description' => 'Allows vendors to post a visible notice / important message at the top of their store pages.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/store-notice/', 'image' => plugins_url('../includes/assets/images/module-notice.png', __FILE__), 'slug' => 'storenotice'),
			array('title' => 'Favorite Stores', 'description' => 'Customers can follow, and stay connected with their favorite sellers.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/favorite-follow-stores/', 'image' => plugins_url('../includes/assets/images/module-favorite4.png', __FILE__), 'slug' => 'favorite'),
			array('title' => 'Abuse Reports', 'description' => 'Customers can report abusive / spam / infringent products to the shop admin.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/abuse-reports/', 'image' => plugins_url('../includes/assets/images/module-report.png', __FILE__),'slug' => 'abusereports'),
			array('title' => 'Single Product Multiple Vendors', 'description' => 'Multiple sellers can sell the same product. A list of vendors is available in the product page.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/single-product-multiple-vendors/', 'image' => plugins_url('../includes/assets/images/module-spmv-optimized.png', __FILE__),'slug' => 'spmv'),
			array('title' => 'Store Reviews', 'description' => 'Customer reviews for stores, vendor replies, notifications, admin control, and more! ', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/store-reviews/', 'image' => plugins_url('../includes/assets/images/module-reviews.png', __FILE__),'slug' => 'reviews'),
			array('title' => 'Complex Commissions', 'description' => 'Set up and combine flat/percentage commissions by product, category, or vendor.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/admin-and-vendor-commissions-multivendor-marketplace-commissions/', 'image' => plugins_url('../includes/assets/images/module-commission.png', __FILE__),'slug' => 'complexcommissions'),
			array('title' => 'Vendor Documentation', 'description' => 'Set up a knowledge base of articles and information for your vendors.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/seller-documentation/', 'image' => plugins_url('../includes/assets/images/module-docs3.png', __FILE__),'slug' => 'vendordocs'),
			array('title' => 'Refund Requests', 'description' => 'Customers can request refunds - vendors manage requests from their dashboard.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/refund-requests/', 'image' => plugins_url('../includes/assets/images/module-refund.png', __FILE__),'slug' => 'refunds'),
			array('title' => 'Seller Verification', 'description' => 'Request documents from vendors and verify identity, address, company, etc.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/vendor-verification/', 'image' => plugins_url('../includes/assets/images/module-verification.png', __FILE__),'slug' => 'verification'),
			array('title' => 'Vendor Product Import & Export', 'description' => 'Allows vendors to import and export products in bulk.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/vendor-products-import-export/', 'image' => plugins_url('../includes/assets/images/module-impexp2.png', __FILE__),'slug' => 'importexport'),
			array('title' => 'Store Policy', 'description' => 'Allows vendors to enter and display their own policies on their store pages.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/store-policy/', 'image' => plugins_url('../includes/assets/images/module-policyo.png', __FILE__),'slug' => 'storepolicy'),
			array('title' => 'Store SEO', 'description' => 'Allows vendors to configure their store page title, meta description, keywords, etc.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/store-seo/', 'image' => plugins_url('../includes/assets/images/module-seo-optimized2.png', __FILE__),'slug' => 'storeseo'),
			array('title' => 'Invoices & Packing', 'description' => 'Vendors can generate PDF invoices, packing slips, shipping labels, etc. Requires a <a href="https://woocommerce-multivendor.com/docs/invoices-packing-slips-shipping-labels">compatible invoicing</a> plugin.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/invoices-packing-slips-shipping-labels', 'image' => plugins_url('../includes/assets/images/module-bills2.png', __FILE__),'slug' => 'vendorinvoices'),
			array('title' => 'Vendor Teams & Staff', 'description' => 'Allows vendors to add staff or team members and configure account permissions.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/vendor-teams-staff/', 'image' => plugins_url('../includes/assets/images/module-teams2.png', __FILE__),'slug' => 'teams'),
			array('title' => 'Vendor Membership Packages', 'description' => 'Configure flexible packages or upgrades, that vendors can purchase for extra perks.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/vendor-membership-packages/', 'image' => plugins_url('../includes/assets/images/module-membership.png', __FILE__), 'slug' => 'memberships'),
			array('title' => 'Vendor Badges & Achievements', 'description' => 'Configure and award badges to vendors based on groups or conditions.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/vendor-badges-achievements/', 'image' => plugins_url('../includes/assets/images/module-badges6.png', __FILE__), 'slug' => 'badges'),
			array('title' => 'Advanced Shipping', 'description' => 'Allows vendors to configure their own shipping methods by zone.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/advanced-shipping-module/', 'image' => plugins_url('../includes/assets/images/module-shipping.png', __FILE__), 'slug' => 'shipping'),
			array('title' => 'Store Support', 'description' => 'Enables several ways for your vendors to provide support to their customers.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/store-support/', 'image' => plugins_url('../includes/assets/images/module-help1.png', __FILE__), 'slug' => 'support'),
			array('title' => 'Product Addons', 'description' => 'Enables vendors to configure product addons via compatible plugins.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/product-add-ons-extra-options/', 'image' => plugins_url('../includes/assets/images/module-addons2.png', __FILE__), 'slug' => 'addons'),
			array('title' => 'Shipping Tracking', 'description' => 'Vendors can enter package tracking details. Supports DHL, UPS, TNT, DPD, Fedex, USPS, Royal Mail, and more. ', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/shipping-tracking/', 'image' => plugins_url('../includes/assets/images/module-shippingtracking.png', __FILE__), 'slug' => 'shippingtracking'),
			array('title' => 'Stripe Connect', 'description' => 'Enables split payments (adaptive), allowing vendors to be paid automatically via Stripe.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/stripe-connect/', 'image' => plugins_url('../includes/assets/images/module-stripe.png', __FILE__), 'slug' => 'stripe'),
			array('title' => 'Elementor', 'description' => 'Allows vendor store page to be designed and edited with the Elementor visual editor.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/elementor/', 'image' => plugins_url('../includes/assets/images/module-elementor.png', __FILE__), 'slug' => 'elementor'),
			array('title' => 'Auctions', 'description' => 'Allows vendors to create and manage their own auctions. Requires the <a href="https://codecanyon.net/item/woocommerce-simple-auctions-wordpress-auctions/6811382">Simple Auctions</a> plugin.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/auctions/', 'image' => plugins_url('../includes/assets/images/module-auctions.png', __FILE__), 'slug' => 'auctions'),
			array('title' => 'Store Categories', 'description' => 'Allows creating and organizing vendors by store categories.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/store-categories/', 'image' => plugins_url('../includes/assets/images/module-categories.png', __FILE__), 'slug' => 'storecategories'),
			array('title' => 'Product Bundles', 'description' => 'Vendors can create and manage product bundles. Requires the <a href="https://woocommerce.com/products/product-bundles/">WooCommerce Bundles</a> plugin.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/product-bundles/', 'image' => plugins_url('../includes/assets/images/module-bundle.png', __FILE__), 'slug' => 'bundles'),
			array('title' => 'Bookings', 'description' => 'Vendors create and manage classes, appointments, rentals, rooms, etc. Requires the <a href="https://woocommerce.com/products/woocommerce-bookings/">Bookings</a> plugin.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/bookings/', 'image' => plugins_url('../includes/assets/images/module-bookings.png', __FILE__), 'slug' => 'bookings'),
			array('title' => 'Social Sharing', 'description' => 'Vendors can add and link social media profiles: Facebook, Twitter, Pinterest, etc.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/social-media-sharing/', 'image' => plugins_url('../includes/assets/images/module-social2.png', __FILE__), 'slug' => 'social'),
			array('title' => 'Subscriptions', 'description' => 'Allows vendors to create and manage product subscriptions. Requires the <a href="https://woocommerce.com/products/woocommerce-subscriptions/">WooCommerce Subscriptions</a> plugin.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/subscriptions/', 'image' => plugins_url('../includes/assets/images/module-subscriptions.png', __FILE__), 'slug' => 'subscriptions'),
			array('title' => 'Advertising', 'description' => 'Allows vendors to purchase advertising for their products.', 'documentation_url'=> 'https://woocommerce-multivendor.com/docs/product-advertising/', 'image' => plugins_url('../includes/assets/images/module-advertising.png', __FILE__), 'slug' => 'advertising'),
		);

		$disabled_modules = apply_filters('marketking_disabled_modules', array());

		$pro_features = array(
			array('title' => 'Vendor Earnings & Reports Panel', 'description' => 'Vendors can optionally add new products, edit tags, multiple product categories, add purchase notes, etc.'),
			array('title' => 'Vendor Registration', 'description' => 'Vendors can optionally add new products, edit tags, multiple product categories, add purchase notes, etc.', 'image' => plugins_url('../includes/assets/images/module-help1.png', __FILE__)),
			array('title' => 'Vendor Groups & Management', 'description' => 'Vendors can optionally add new products, edit tags, multiple product categories, add purchase notes, etc.', 'image' => plugins_url('../includes/assets/images/module-help1.png', __FILE__)),
			array('title' => 'Variable, Grouped & External Products', 'description' => 'Write and publish announcements for your marketplace vendors.', 'image' => plugins_url('../includes/assets/images/module-announce.png', __FILE__)),
			array('title' => 'Admin Reports & Analytics Panel', 'description' => 'Vendors can optionally add new products, edit tags, multiple product categories, add purchase notes, etc.', 'image' => plugins_url('../includes/assets/images/module-help1.png', __FILE__)),
			array('title' => 'Linked Products: Upsells & Cross-sells', 'description' => 'Allow conversations and communication with vendors via the messaging module.', 'image' => plugins_url('../includes/assets/images/module-messaging.png', __FILE__)),
			array('title' => 'Virtual & Downloadable Products', 'description' => 'Configure registration options and custom fields for vendor registration.','image' => plugins_url('../includes/assets/images/module-registration3.png', __FILE__)),
			array('title' => 'Enhanced Vendor Capabilities', 'description' => 'Vendors can optionally add new products, edit tags, multiple product categories, add purchase notes, etc.', 'image' => plugins_url('../includes/assets/images/module-help1.png', __FILE__)),
			array('title' => 'Many more features, options & integrations...', 'description' => 'Vendors can optionally add new products, edit tags, multiple product categories, add purchase notes, etc.', 'image' => plugins_url('../includes/assets/images/module-help1.png', __FILE__)),
		);

		$display_upgrade_modal = 'no';
		$modules = array();
		if ($type === 'integrations'){
			$modules = $integration_modules;
			$type = 'pro';
		} else if ($type === 'pro'){
			$modules = $pro_modules;

			if (!defined('MARKETKINGPRO_DIR')){
				if ($preload === false){
					$display_upgrade_modal = 'yes';
				}
			}
		}

		if ($type === 'profeatures'){
			$modules = $pro_features;
		}

		if ($preload === false){
			foreach ($modules as $module){
				?>
				<div class="plugin-card">
					<div class="plugin-card-top">
						<div class="name column-name">
							<h3>
								<a href="#" class="thickbox open-plugin-details-modal">
								<?php echo esc_html($module['title']); ?>
								<img src="<?php echo esc_url($module['image']); ?>" class="plugin-icon" alt="">
								</a>
							</h3>
						</div>
						<?php if ($type!=='profeatures') { ?>
						<div class="action-links">
							<ul class="plugin-action-buttons"><li>
								<div class="marketking-checkbox-switch <?php if ($type === 'pro' && !defined('MARKETKINGPRO_DIR')){ echo 'marketkingproswitch';}?>">
								   <input type="checkbox" <?php
								   if ($type === 'pro' && !defined('MARKETKINGPRO_DIR')){
								   } else {
								   		$setting = 0;
								   		// get if module is enabled and if so, show "checked"
								   		$setting = intval(get_option( 'marketking_enable_'.$module['slug'].'_setting', 1 ));
								   		
								   		if ($setting === 1 && !in_array($module['slug'], $disabled_modules)){
								   			echo 'checked="checked"';
								   		}
								   		
								   }
								   ?> value="1" name="status" class="marketking-input-checkbox slug_<?php echo esc_attr($module['slug']);?>" <?php
								   if ($type === 'pro' && !defined('MARKETKINGPRO_DIR')){
								   	// echo 'disabled="disabled"';
								   }

								   if (in_array($module['slug'], $disabled_modules)){
								   	  echo 'disabled="disabled"';
								   }
								   ?>>

								   
								   <div class="marketking-checkbox-animate <?php if ($type === 'pro' && !defined('MARKETKINGPRO_DIR')){ echo 'marketking-pro-checkbox';}?>">
								      <span class="marketking-checkbox-off"><?php 
								      if ($type === 'pro' && !defined('MARKETKINGPRO_DIR')){
								      	esc_html_e('PRO','marketking-multivendor-marketplace-for-woocommerce');
								      } else {
								      	esc_html_e('OFF','marketking-multivendor-marketplace-for-woocommerce');
								      }
								      
								      ?></span>
								      <span class="marketking-checkbox-on"><?php esc_html_e('ON','marketking-multivendor-marketplace-for-woocommerce');?></span>
								   </div>
								</div>
							</li></ul></div>
							<?php } ?>

						<div class="desc column-description">
							<p><?php 
								echo wp_kses( $module['description'], array( 'a'     => array(
							        'href' => array()
							    ) ) );
							;?></p>
						</div>
					</div>
					<?php if ($type!=='profeatures') { ?>
						<div class="plugin-card-bottom">

							<div class="column-downloaded">
								<a href="<?php echo esc_url($module['documentation_url']);?>"><?php esc_html_e('View Documentation','marketking-multivendor-marketplace-for-woocommerce');?></a>				</div>
							<?php
							if (!isset($module['cardbottom'])){
								if ($type === 'pro' && !defined('MARKETKINGPRO_DIR')){
									?>
									<div class="column-compatibility">
										<span class="compatibility-compatible"><strong><?php esc_html_e('Unlock now','marketking-multivendor-marketplace-for-woocommerce');?></strong><?php esc_html_e(' with a Premium License','marketking-multivendor-marketplace-for-woocommerce');?></span>				</div>
									<?php
								}
								if ($type === 'pro' && defined('MARKETKINGPRO_DIR')){
									?>
									<div class="column-compatibility">
										<span class="compatibility-compatible"><strong><?php esc_html_e('Compatible','marketking-multivendor-marketplace-for-woocommerce');?></strong><?php esc_html_e(' with your version of MarketKing','marketking-multivendor-marketplace-for-woocommerce');?></span>				</div>
									<?php
								}
							} else {
								?>
								<div class="column-compatibility"><span><?php echo esc_html($module['cardbottom']);?></span></div>
								<?php
							}
							
							?>

						</div>		
					<?php } ?>
				</div>
				<?php
			}
		} else if ($preload === true){
			$images_array = array();
			foreach ($modules as $module){
				array_push($images_array,$module['image']);
			}
			return $images_array;
		}

		if ($display_upgrade_modal === 'yes'){
			echo self::display_upgrade_modal();
		}
		
	}

	public static function display_upgrade_modal(){
		?>
		<div class="marketking_modal_init"></div>

		<div id="marketking_pro_upgrade_modal_container">
			<div id="marketking_pro_upgrade_modal">
				<div id="marketking_pro_upgrade_modal_image_container">
					<img class="marketking_pro_upgrade_modal_img" src="<?php echo esc_attr(plugins_url('../includes/assets/images/upgrade-pro-optimized.png', __FILE__));?>">
				</div>
				<div id="marketking_pro_upgrade_header">
					<h2>
						<?php esc_html_e('Unlock 137+ Pro Features','marketking-multivendor-marketplace-for-woocommerce');?>
					</h2>
				</div>
				<div class="marketking_upgrade_header_description">
					<?php esc_html_e('with a ','marketking-multivendor-marketplace-for-woocommerce');?><strong><?php esc_html_e('Premium License','marketking-multivendor-marketplace-for-woocommerce');?></strong>
				</div>
				<div class="marketking_upgrade_header_small_description">
					<?php esc_html_e('Get full lifetime access to 25+ powerful modules, as well as hundreds of features & integrations. Pay once, get lifetime updates.','marketking-multivendor-marketplace-for-woocommerce');?> 
				</div>
				<div class="marketking_modal_bottom_half">
					<a href="https://woocommerce-multivendor.com/pricing"><button type="button" id="marketking_modal_upgrade_now_button"><?php esc_html_e('UPGRADE NOW','marketking-multivendor-marketplace-for-woocommerce');?></button></a>
				</div>
			</div>
		</div>
		<?php
	}

	public static function marketking_premium_page_content(){
		echo self::get_header_bar();
		echo self::display_upgrade_modal();
	}

	public static function get_header_bar(){
		?>
		<div id="marketing_admin_header_bar">
			<div id="marketking_admin_header_bar_left">
				<img style="width:140px" src="<?php echo plugins_url('../includes/assets/images/marketkinglogo10.png', __FILE__); ?>">
				<div id="marketking_admin_header_version2"><?php echo MARKETKINGCORE_VERSION; ?></div>
			</div>
			<div id="marketking_admin_header_bar_right">
				<?php
				if (defined('MARKETKINGPRO_DIR')){
					$supportlink = 'https://webwizards.ticksy.com';
				} else {
					$supportlink =	'https://wordpress.org/support/plugin/marketking-multivendor-marketplace-for-woocommerce/';
				}
				?>
				<a class="marketking_admin_header_right_element" target="_blank" href="https://woocommerce-multivendor.com/docs"><span class="dashicons dashicons-edit-page marketking_header_icon"></span><?php esc_html_e('Documentation', 'marketking-multivendor-marketplace-for-woocommerce');?></a>
				<a class="marketking_admin_header_right_element" target="_blank" href="<?php echo esc_attr($supportlink);?>"><span class="dashicons dashicons-universal-access-alt marketking_header_icon"></span><?php esc_html_e('Support', 'marketking-multivendor-marketplace-for-woocommerce');?></a>
				<?php
				if (!defined('MARKETKINGPRO_DIR')){
					?>
					<a class="marketking_admin_header_right_element_button" target="_blank" href="https://woocommerce-multivendor.com/pricing"><button class="marketking_header_button_admin"><span class="dashicons dashicons-superhero marketking_header_icon_button"></span><?php esc_html_e('Upgrade to Pro', 'marketking-multivendor-marketplace-for-woocommerce');?></button></a>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}

	public static function marketking_registration_page_content(){
		echo self::get_header_bar();
		?>

		<div id="marketking_admin_groups_main_container">
			<div class="marketking_admin_groups_main_title">
				<?php esc_html_e('Registration', 'marketking-multivendor-marketplace-for-woocommerce'); ?>
			</div>
			<div class="marketking_admin_groups_main_container_main_row">
				<div class="marketking_admin_groups_main_container_main_row_left">
					<div class="marketking_admin_groups_main_container_main_row_title">
						<?php esc_html_e('Fields','marketking-multivendor-marketplace-for-woocommerce'); ?>
					</div>
					<div class="marketking_admin_groups_main_container_main_row_subtitle">
						<?php esc_html_e('Create & Edit Registration Fields. Choose from 9+ Custom Field Types.','marketking-multivendor-marketplace-for-woocommerce'); ?>
					</div>
					<a href="<?php echo admin_url( 'edit.php?post_type=marketking_field'); ?>" class="marketking_admin_groups_box_link">
						<div class="marketking_admin_groups_main_container_main_row_left_box marketking_registration_left_box">
							<svg class="marketking_admin_groups_main_container_main_row_left_box_icon" xmlns="http://www.w3.org/2000/svg" width="60" height="75" fill="none" viewBox="0 0 60 75">
							  <path fill="#CED8E2" d="M36.563 0H23.437a8.437 8.437 0 00-8.385 7.5H8.439A8.437 8.437 0 000 15.938v50.624A8.437 8.437 0 008.438 75h43.124A8.438 8.438 0 0060 66.562V15.938A8.436 8.436 0 0051.562 7.5h-6.614A8.437 8.437 0 0036.561 0zM23.437 5.625h13.125a2.812 2.812 0 110 5.625H23.438a2.812 2.812 0 110-5.625zm8.438 25.313a2.812 2.812 0 012.813-2.813h13.124a2.812 2.812 0 110 5.625H34.688a2.812 2.812 0 01-2.813-2.813zm2.813 17.812h13.124a2.812 2.812 0 110 5.625H34.688a2.813 2.813 0 110-5.625zm-9.263-19.575l-7.5 7.5a2.812 2.812 0 01-3.975 0l-3.75-3.75a2.813 2.813 0 113.975-3.975l1.762 1.762L21.45 25.2a2.813 2.813 0 113.975 3.975zm0 16.65a2.813 2.813 0 010 3.975l-7.5 7.5a2.812 2.812 0 01-3.975 0l-3.75-3.75a2.813 2.813 0 113.975-3.975l1.762 1.762 5.513-5.512a2.812 2.812 0 013.975 0z"/>
							</svg>
							<div class="marketking_admin_groups_main_container_main_row_box_text">
								<?php esc_html_e('Manage Registration Fields','marketking-multivendor-marketplace-for-woocommerce'); ?>
							</div>
						</div>
					</a>
				</div>
				<div class="marketking_admin_groups_main_container_main_row_right">
					<div class="marketking_admin_groups_main_container_main_row_title">
						<?php esc_html_e('Options','marketking-multivendor-marketplace-for-woocommerce'); ?>
					</div>
					<div class="marketking_admin_groups_main_container_main_row_subtitle">
						<?php esc_html_e('Manage Options (e.g. "Customer" / "Vendor")','marketking-multivendor-marketplace-for-woocommerce'); ?>
					</div>
					<div class="marketking_admin_groups_main_container_main_row_right_boxes">
						<a href="<?php echo admin_url( 'edit.php?post_type=marketking_option'); ?>" class="marketking_admin_groups_box_link">
							<div class="marketking_admin_groups_main_container_main_row_right_box marketking_admin_groups_main_container_main_row_right_box_first marketking_registration_right_box">
								<svg class="marketking_admin_groups_main_container_main_row_right_box_icon_first" xmlns="http://www.w3.org/2000/svg" width="49" height="61" fill="#62666A" viewBox="0 0 49 61">
								  <path d="M42.87 61a6.145 6.145 0 004.335-1.787A6.085 6.085 0 0049 54.9V6.1c0-1.618-.646-3.17-1.795-4.313A6.145 6.145 0 0042.87 0H6.13a6.145 6.145 0 00-4.335 1.787A6.085 6.085 0 000 6.1v48.8c0 1.618.646 3.17 1.795 4.313A6.145 6.145 0 006.13 61h36.74zM15.324 9.15h18.389v6.1H15.324v-6.1zm16.09 19.063a6.876 6.876 0 01-2.027 4.845 6.944 6.944 0 01-4.869 2.02c-3.785 0-6.895-3.096-6.895-6.866 0-3.77 3.11-6.862 6.895-6.862a6.94 6.94 0 014.868 2.018 6.873 6.873 0 012.028 4.845zm-20.687 21.16c0-5.075 6.215-10.293 13.791-10.293 7.577 0 13.792 5.218 13.792 10.293v1.718H10.727v-1.718z"/>
								</svg>
								<div class="marketking_admin_groups_main_container_main_row_right_box_text marketking_admin_groups_main_container_main_row_right_box_first_text">
									<?php esc_html_e('Manage Registration Options','marketking-multivendor-marketplace-for-woocommerce'); ?>
								</div>
							</div>
						</a>
						
					</div>
				</div>
			</div>
		</div>
		<?php

	}

	function marketking_settings_init(){
		require_once ( MARKETKINGCORE_DIR . 'admin/class-marketking-core-settings.php' );
		$settings = new Marketkingcore_Settings;
		$settings-> register_all_settings();

		// if a POST variable exists indicating the user saved settings, flush permalinks
		if (isset($_POST['marketking_vendor_registration_setting'])){
			require_once ( MARKETKINGCORE_DIR . 'public/class-marketking-core-public.php' );
			$publicobj = new Marketkingcore_Public;

			

			/*
			$this->marketking_register_post_type_announcement();

			$this->marketking_register_post_type_conversation();
			$this->marketking_register_post_type_offer();
			$this->marketking_register_post_type_dynamic_rules();
			$this->marketking_register_post_type_custom_option();
			$this->marketking_register_post_type_custom_field();
			$publicobj->marketking_custom_endpoints();
			*/

			if (apply_filters('marketking_flush_permalinks', true)){
				// Flush rewrite rules
				flush_rewrite_rules();
			}
			
		}

		// Check vendor registration option, and if / not a separate page, set the become a vendor page as a draft / published
		$vendor_setting = get_option( 'marketking_vendor_registration_setting', 'myaccount' );
		$page_id = get_option('marketking_vendor_registration_page_setting_initial', 'none');
		if (get_post_type($page_id) === 'page'){
			if ($vendor_setting === 'separate'){
				// publish page
				$post = array( 'ID' => $page_id, 'post_status' => 'publish' );
				if (get_post_status($page_id) !== 'publish'){
					if(apply_filters('marketking_vendor_page_set_post_status', true)){
						wp_update_post($post);
					}
				}

			} else {
				// draft page
				$post = array( 'ID' => $page_id, 'post_status' => 'draft' );
				if (get_post_status($page_id) !== 'draft'){
					if(apply_filters('marketking_vendor_page_set_post_status', true)){
						wp_update_post($post);
					}
				}

			}
		}
	}
	
	public static function marketking_settings_page_content() {
		require_once ( MARKETKINGCORE_DIR . 'admin/class-marketking-core-settings.php' );
		$settings = new Marketkingcore_Settings;
		$settings-> render_settings_page_content();
	}

	public static function marketking_get_dashboard_data(){

		global $marketking_data;

		global $marketking_data_read;

		if ($marketking_data_read !== 'yes'){
			if (!is_array($marketking_data)){
				$marketking_data = array();

				$data = array();


				$dashboarddata = get_transient('webwizards_dashboard_data_cache_marketking');
				if ($dashboarddata){
					$data = $dashboarddata;

					// check cache time - clear every 12 hours
					$time = intval(get_transient('webwizards_dashboard_data_cache_time_marketking'));
					if ((time()-$time) > apply_filters('marketking_cache_time_setting', 1600)){
						// clear cache
						delete_transient('webwizards_dashboard_data_cache_marketking');
						delete_transient('webwizards_dashboard_data_cache_time_marketking');
						$dashboarddata = false;
						$data = array();

					}
				}


				if (!$dashboarddata){

					// get all orders in past 31 days for calculations
					global $wpdb;


					$date_to = date('Y-m-d H:i:s');
					$date_from = date('Y-m-d');

					if (apply_filters('marketking_dashboard_set_timezone', true)){
						$timezone = get_option('timezone_string');
						if (empty($timezone) || $timezone === null){
							$timezone = 'UTC';
						}
						//date_default_timezone_set($timezone);

						$site_time = time()+(get_option('gmt_offset')*3600);
						$date_to = date('Y-m-d H:i:s', $site_time);
						$date_from = date('Y-m-d', $site_time);

					}

					$date_to = apply_filters('marketking_demo_dateto', $date_to);

					$post_status = implode("','", array('wc-processing', 'wc-completed') );
					$orders_today = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
					            WHERE post_type = 'shop_order'
					            AND post_status IN ('{$post_status}')
					            AND post_date BETWEEN '{$date_from}  00:00:00' AND '{$date_to}'
					        ");


					$date_from = date('Y-m-d', strtotime('-6 days'));
					$orders_seven_days = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
					            WHERE post_type = 'shop_order'
					            AND post_status IN ('{$post_status}')
					            AND post_date BETWEEN '{$date_from}  00:00:00' AND '{$date_to}'
					        ");

					$date_from = date('Y-m-d', strtotime('-30 days'));
					$orders_thirtyone_days = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
					            WHERE post_type = 'shop_order'
					            AND post_status IN ('{$post_status}')
					            AND post_date BETWEEN '{$date_from}  00:00:00' AND '{$date_to}'
					        ");

					// if marketking is in b2b mode, ignore whether user is B2B
					$plugin_status = get_option( 'marketking_plugin_status_setting', 'b2b' );

					// total b2b sales
					$total_b2b_sales_today = 0;
					$total_b2b_sales_seven_days = 0;
					$total_b2b_sales_thirtyone_days = 0;

					// total tax
					$tax_b2b_sales_today = 0;
					$tax_b2b_sales_seven_days = 0;
					$tax_b2b_sales_thirtyone_days = 0;

					// nr of orders
					$number_b2b_sales_today = 0;
					$number_b2b_sales_seven_days = 0;
					$number_b2b_sales_thirtyone_days = 0;

					// nr of vendor signups
					$signups_b2b_sales_today = 0;
					$signups_b2b_sales_seven_days = 0;
					$signups_b2b_sales_thirtyone_days = 0;

					// today signups
					$vendors = get_users(array(
					    'meta_query'=> array(
			    	  		'relation' => 'AND',
			                array(
			                    'key' => 'marketking_account_approved',
			                    'value' => 'no',
			                    'compare' => '!=',
			                ),
			                array(
			                    'key' => 'marketking_group',
			                    'value' => 'none',
			                    'compare' => '!=',
			                ),
			        	),
					    'date_query'    => array(
				            array(
				                'after'     => date('Y-m-d H:i:s', strtotime('-1 days')),
				                'inclusive' => true,
				            ),
				         )
					));
					$signups_b2b_sales_today = count($vendors);

					// 7 day signups
					$vendors = get_users(array(
						'meta_query'=> array(
			    	  		'relation' => 'AND',
			                array(
			                    'key' => 'marketking_account_approved',
			                    'value' => 'no',
			                    'compare' => '!=',
			                ),
			                array(
			                    'key' => 'marketking_group',
			                    'value' => 'none',
			                    'compare' => '!=',
			                ),
			        	),
					    'date_query'    => array(
				            array(
				                'after'     => date('Y-m-d H:i:s', strtotime('-7 days')),
				                'inclusive' => true,
				            ),
				         )
					));
					$signups_b2b_sales_seven_days = count($vendors);


					// 31 day signups
					$vendors = get_users(array(
					    'meta_query'=> array(
			    	  		'relation' => 'AND',
			                array(
			                    'key' => 'marketking_account_approved',
			                    'value' => 'no',
			                    'compare' => '!=',
			                ),
			                array(
			                    'key' => 'marketking_group',
			                    'value' => 'none',
			                    'compare' => '!=',
			                ),
			        	),
					    'date_query'    => array(
				            array(
				                'after'     => date('Y-m-d H:i:s', strtotime('-31 days')),
				                'inclusive' => true,
				            ),
				         )
					));
					$signups_b2b_sales_thirtyone_days = count($vendors);


					//calculate today
					foreach ($orders_today as $order){

						$total_b2b_sales_today += floatval(get_post_meta($order->ID,'_order_total', true));
						$tax_b2b_sales_today += floatval(get_post_meta($order->ID,'_order_tax', true))+floatval(get_post_meta($order->ID,'_order_shipping_tax', true));
						$number_b2b_sales_today++;
					}

					//calculate seven days
					foreach ($orders_seven_days as $order){

						$total_b2b_sales_seven_days += get_post_meta($order->ID,'_order_total', true);
						$tax_b2b_sales_seven_days += floatval(get_post_meta($order->ID,'_order_tax', true))+floatval(get_post_meta($order->ID,'_order_shipping_tax', true));
						$number_b2b_sales_seven_days++;
					}

					//calculate thirtyone days
					foreach ($orders_thirtyone_days as $order){

						$total_b2b_sales_thirtyone_days += floatval(get_post_meta($order->ID,'_order_total', true));
						$tax_b2b_sales_thirtyone_days += floatval(get_post_meta($order->ID,'_order_tax', true))+floatval(get_post_meta($order->ID,'_order_shipping_tax', true));
						$number_b2b_sales_thirtyone_days++;
					}


					// get each day in the past 31 days and form an array with day and total sales
					$i=1;
					$days_sales_array = array();
					$hours_sales_array = array(
						'00' => 0,
						'01' => 0,
						'02' => 0,
						'03' => 0,
						'04' => 0,
						'05' => 0,
						'06' => 0,
						'07' => 0,
						'08' => 0,
						'09' => 0,
						'10' => 0,
						'11' => 0,
						'12' => 0,
						'13' => 0,
						'14' => 0,
						'15' => 0,
						'16' => 0,
						'17' => 0,
						'18' => 0,
						'19' => 0,
						'20' => 0,
						'21' => 0,
						'22' => 0,
						'23' => 0,
					);

					while ($i<32){
						$date_from = $date_to = date('Y-m-d', strtotime('-'.($i-1).' days'));

						$post_status = implode("','", array('wc-processing', 'wc-completed') );

						if ($i===1){
							$date_to = date('Y-m-d H:i:s');
							$date_from = date('Y-m-d');
							$orders_day = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
						            WHERE post_type = 'shop_order'
						            AND post_status IN ('{$post_status}')
						            AND post_date BETWEEN '{$date_from} 00:00:00' AND '{$date_to}'
						        ");
						} else {
							$orders_day = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
						            WHERE post_type = 'shop_order'
						            AND post_status IN ('{$post_status}')
						            AND post_date BETWEEN '{$date_from} 00:00:00' AND '{$date_to} 23:59:59'
						        ");
						}
						//calculate totals
						$sales_total = 0;
						foreach ($orders_day as $order){
							$order_user_id = get_post_meta($order->ID,'_customer_user', true);

							$sales_total += get_post_meta($order->ID,'_order_total', true);
						}

						// if first day, get this by hour
						if ($i===1){
							$date_to = date('Y-m-d H:i:s');
							$date_from = date('Y-m-d');

							$post_status = implode("','", array('wc-processing', 'wc-completed') );
							$orders_day = $wpdb->get_results( "SELECT ID FROM $wpdb->posts 
							            WHERE post_type = 'shop_order'
							            AND post_status IN ('{$post_status}')
							            AND post_date BETWEEN '{$date_from} 00:00:00' AND '{$date_to}'
							        ");

							foreach ($orders_day as $order){
								// get hour of the order
								$hour = get_post_time('H', false, $order->ID);
								$hours_sales_array[$hour] += get_post_meta($order->ID,'_order_total', true);
							}
						}

						array_push ($days_sales_array, $sales_total);
						$i++;
					}

					// get admin commissions
					$earnings_today = marketking()->get_earnings('allvendors', 'last_days', 1, false, false, true);
					$earnings_seven_days = marketking()->get_earnings('allvendors', 'last_days', 7, false, false, true);
					$earnings_thirtyone_days = marketking()->get_earnings('allvendors', 'last_days', 31, false, false, true);

					$data['days_sales_array'] = $days_sales_array;
					$data['hours_sales_array'] = $hours_sales_array;
					$data['total_b2b_sales_today'] = $total_b2b_sales_today;
					$data['total_b2b_sales_seven_days'] = $total_b2b_sales_seven_days;
					$data['total_b2b_sales_thirtyone_days'] = $total_b2b_sales_thirtyone_days;
					$data['number_b2b_sales_today'] = $number_b2b_sales_today;
					$data['number_b2b_sales_seven_days'] = $number_b2b_sales_seven_days;
					$data['number_b2b_sales_thirtyone_days'] = $number_b2b_sales_thirtyone_days;
					$data['signups_b2b_sales_today'] = $signups_b2b_sales_today;
					$data['signups_b2b_sales_seven_days'] = $signups_b2b_sales_seven_days;
					$data['signups_b2b_sales_thirtyone_days'] = $signups_b2b_sales_thirtyone_days;

					$data['earnings_today'] = $earnings_today;
					$data['earnings_seven_days'] = $earnings_seven_days;
					$data['earnings_thirtyone_days'] = $earnings_thirtyone_days;
					
					set_transient('webwizards_dashboard_data_cache_marketking', $data);
					set_transient('webwizards_dashboard_data_cache_time_marketking', time());

				}

				$marketking_data = $data;

				if (!is_array($marketking_data)){
					$marketking_data = array();
				}
			}
			
			$marketking_data_read = 'yes';
		}

		return $marketking_data;
	}

	public static function marketking_reports_page_content(){
		echo self::get_header_bar();

		// preloader if not in ajax - in ajax preloader is added via JS for smoother animations
		if (!wp_doing_ajax()){
			?>
			<div class="marketkingpreloader">
			    <img class="marketking_loader_icon_button" src="<?php echo esc_attr(plugins_url('../includes/assets/images/loaderpagegold5.svg', __FILE__));?>">
			</div>
			<?php
		}

		$data = self::marketking_get_dashboard_data();
		
		// Send data to JS
		$translation_array = array(
			'days_sales_b2b' => $data['days_sales_array'],
			'hours_sales_b2b' => array_values($data['hours_sales_array']),
			'currency_symbol' => get_woocommerce_currency_symbol(),
		);

		wp_localize_script( 'marketking_global_admin_script', 'marketking_dashboard', $translation_array );

		?>
		<div id="marketking_dashboard_wrapper">
		    <div class="marketking_dashboard_page_wrapper marketking_reports_page_wrapper">
		        <div class="container-fluid">
		            <div class="row">
		                <div class="col-12">
		                    <div class="card card-hover">
		                        <div class="card-body">
		                            <div class="d-md-flex align-items-center">
		                                <div>
		                                    <h3 class="card-title"><?php esc_html_e('Sales Reports','marketking-multivendor-marketplace-for-woocommerce');?></h3>
		                                    <h5 class="card-subtitle"><?php esc_html_e('Total Sales Value','marketking-multivendor-marketplace-for-woocommerce');?></h5>
		                                </div>
		                                <div class="ml-auto d-flex no-block align-items-center">
		                                    <ul class="list-inline font-12 dl m-r-15 m-b-0">
		                                        <li class="list-inline-item text-primary"><i class="icon marketking-ni marketking-ni-circle-fill"></i> <?php esc_html_e('Commission','marketking-multivendor-marketplace-for-woocommerce');?></li>
		                                        <li class="list-inline-item text-cyan"><i class="icon marketking-ni marketking-ni-circle-fill"></i> <?php esc_html_e('Total Sales','marketking-multivendor-marketplace-for-woocommerce');?></li>
		                                        <li class="list-inline-item text-info"><i class="icon marketking-ni marketking-ni-circle-fill"></i> <?php esc_html_e('Number of Orders','marketking-multivendor-marketplace-for-woocommerce');?></li>
		                                        
		                                    </ul>
		                                    <div class="marketking_reports_topright_container">
			                                    <div class="dl marketking_reports_topright">
			                                        <select id="marketking_dashboard_days_select" class="custom-select">
			                                            <option value="all" selected><?php esc_html_e('All Vendors (Market)','marketking-multivendor-marketplace-for-woocommerce');?></option>
			                                            <optgroup label="<?php esc_html_e('Vendor Stores', 'marketking-multivendor-marketplace-for-woocommerce'); ?>">
			                                            	<option value="1"><?php esc_html_e('Admin Store (Self)','marketking-multivendor-marketplace-for-woocommerce');?></option>

				                                            <?php

				                                            $vendors = marketking()->get_all_vendors();
				                                            foreach ($vendors as $vendor){
				                                            	?>
		                                	                    <option value="<?php echo esc_attr( $vendor->ID ); ?>"><?php
		                                		                    $store_name = marketking()->get_store_name_display($vendor->ID);
		                                		                    echo esc_html($store_name);
		                                	                    ?></option>
				                                            	<?php
				                                            }
				                                            ?>
				                                        </optgroup>	
			                                        </select>
			                                        <div class="marketking_reports_fromto">
				                                        <div class="marketking_reports_fromto_text"><?php esc_html_e('From:','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
				                                        <input type="date" class="marketking_reports_date_input marketking_reports_date_input_from">
				                                    </div>
				                                    <div class="marketking_reports_fromto">
				                                        <div class="marketking_reports_fromto_text"><?php esc_html_e('To:','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
				                                        <input type="date" class="marketking_reports_date_input marketking_reports_date_input_to">
				                                    </div>	
			                                    </div>
			                                    <div id="marketking_reports_quick_links">
			                                    	<div class="marketking_reports_linktext"><?php esc_html_e('Quick Select:','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
			                                    	<a id="marketking_reports_link_thismonth" hreflang="thismonth" class="marketking_reports_link"><?php esc_html_e('This Month','marketking-multivendor-marketplace-for-woocommerce'); ?></a>
			                                    	<a hreflang="lastmonth" class="marketking_reports_link"><?php esc_html_e('Last Month','marketking-multivendor-marketplace-for-woocommerce'); ?></a>
			                                    	<a hreflang="thisyear" class="marketking_reports_link"><?php esc_html_e('This Year','marketking-multivendor-marketplace-for-woocommerce'); ?></a>
			                                    	<a hreflang="lastyear" class="marketking_reports_link"><?php esc_html_e('Last Year','marketking-multivendor-marketplace-for-woocommerce'); ?></a>
			                                    </div>
			                                </div>


		                                </div>
		                            </div>
		                            <div class="row">
		                                <!-- column -->
		                                <div class="col-lg-3">
		                                    <h1 class="marketking_total_b2b_sales_today m-b-0 m-t-30"><?php echo wc_price($data['total_b2b_sales_today']); ?></h1>
		                                    <h6 class="font-light text-muted"><?php esc_html_e('Sales','marketking-multivendor-marketplace-for-woocommerce');?></h6>
		                                    <h3 class="marketking_number_orders_today m-t-30 m-b-0"><?php echo esc_html($data['number_b2b_sales_today']); ?></h3>
		                                    <h6 class="font-light text-muted"><?php esc_html_e('Orders','marketking-multivendor-marketplace-for-woocommerce');?></h6>
		                                    <a id="marketking_dashboard_blue_button" class="btn btn-info m-t-20 p-15 p-l-25 p-r-25 m-b-20" href="javascript:void(0)"></a>
		                                </div>
		                                <!-- column -->
		                                <div class="col-lg-9">
		                                    <div class="campaign ct-charts"></div>
		                                </div>
		                                <div class="col-lg-3">
		                                </div>
		                                <div class="col-lg-9">
		                                    <div class="campaign2 ct-charts"></div>
		                                </div>
		                                <!-- column -->
		                            </div>
		                        </div>
		                        <!-- ============================================================== -->
		                        <!-- Info Box -->
		                        <!-- ============================================================== -->
		                        <div class="card-body border-top">
		                            <div class="row m-b-0">
		                            	<!-- col -->
		                            	<div class="col-lg-3 col-md-6">
		                            	    <div class="d-flex align-items-center">
		                            	        <div class="m-r-10"><span class="text-orange display-5"><i class="icon marketking-ni marketking-ni-user-circle-fill"></i></span></div>
		                            	        <div><span><?php esc_html_e('New Vendors','marketking-multivendor-marketplace-for-woocommerce');?></span>
		                            	            <h3 class="marketking_number_customers_today font-medium m-b-0"><?php echo esc_html($data['signups_b2b_sales_today']); ?></h3>
		                            	        </div>
		                            	    </div>
		                            	</div>
		                                <!-- col -->
		                                <div class="col-lg-3 col-md-6">
		                                    <div class="d-flex align-items-center">
		                                        <div class="m-r-10"><span class="text-cyan display-5"><i class="icon marketking-ni marketking-ni-cart-fill"></i></span></div>
		                                        <div><span><?php esc_html_e('Total Sales','marketking-multivendor-marketplace-for-woocommerce');?></span>
		                                            <h3 class="marketking_total_b2b_sales_today font-medium m-b-0">
		                                            	<?php echo wc_price($data['total_b2b_sales_today']); ?>
		                                           	</h3>
		                                        </div>
		                                    </div>
		                                </div>
		                                <!-- col -->
		                                <div class="col-lg-3 col-md-6">
		                                    <div class="d-flex align-items-center">
		                                        <div class="m-r-10"><span class="text-info display-5"><i class="icon marketking-ni marketking-ni-package-fill"></i></span></div>
		                                        <div><span><?php esc_html_e('Number of Orders','marketking-multivendor-marketplace-for-woocommerce');?></span>
		                                            <h3 class="marketking_number_orders_today font-medium m-b-0"><?php echo esc_html($data['number_b2b_sales_today']); ?></h3>
		                                        </div>
		                                    </div>
		                                </div>
		                                
		                                <!-- col -->
		                                <div class="col-lg-3 col-md-6">
		                                    <div class="d-flex align-items-center">
		                                        <div class="m-r-10"><span class="text-primary display-5"><i class="icon marketking-ni marketking-ni-reports"></i></span></div>
		                                        <div><span><?php esc_html_e('Commission','marketking-multivendor-marketplace-for-woocommerce');?></span>
		                                            <h3 class="marketking_net_earnings_today font-medium m-b-0"><?php echo wc_price($data['earnings_today']); ?></h3>
		                                        </div>
		                                    </div>
		                                </div>
		                                <!-- col -->
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>

		        </div>
		    </div>
		</div>
		<?php

	}

	public static function marketking_dashboard_page_content(){
		echo self::get_header_bar();

		// preloader if not in ajax - in ajax preloader is added via JS for smoother animations
		if (!wp_doing_ajax()){
			?>
			<div class="marketkingpreloader">
			    <img class="marketking_loader_icon_button" src="<?php echo esc_attr(plugins_url('../includes/assets/images/loaderpagegold5.svg', __FILE__));?>">
			</div>
			<?php
		}

		$data = self::marketking_get_dashboard_data();
		
		// Send data to JS
		$translation_array = array(
			'days_sales_b2b' => $data['days_sales_array'],
			'hours_sales_b2b' => array_values($data['hours_sales_array']),
			'currency_symbol' => get_woocommerce_currency_symbol(),
		);

		wp_localize_script( 'marketking_global_admin_script', 'marketking_dashboard', $translation_array );

		?>
		<div id="marketking_dashboard_wrapper">
		    <div class="marketking_dashboard_page_wrapper">
		        <div class="container-fluid">
		            <div class="row">
		                <div class="col-12">
		                    <div class="card card-hover">
		                        <div class="card-body">
		                            <div class="d-md-flex align-items-center">
		                                <div>
		                                    <h3 class="card-title"><?php esc_html_e('Sales Summary','marketking-multivendor-marketplace-for-woocommerce');?></h3>
		                                    <h5 class="card-subtitle"><?php esc_html_e('Total Sales Value','marketking-multivendor-marketplace-for-woocommerce');?></h5>
		                                </div>
		                                <div class="ml-auto d-flex no-block align-items-center">
		                                    <ul class="list-inline font-12 dl m-r-15 m-b-0">
		                                        <li class="list-inline-item text-info"><i class="icon marketking-ni marketking-ni-circle-fill"></i> <?php esc_html_e('Sales','marketking-multivendor-marketplace-for-woocommerce');?></li>
		                                        
		                                    </ul>
		                                    <div class="dl">
		                                        <select id="marketking_dashboard_days_select" class="custom-select">
		                                            <option value="0" selected><?php esc_html_e('Today','marketking-multivendor-marketplace-for-woocommerce');?></option>
		                                            <option value="1"><?php esc_html_e('Last 7 Days','marketking-multivendor-marketplace-for-woocommerce');?></option>
		                                            <option value="2"><?php esc_html_e('Last 31 Days','marketking-multivendor-marketplace-for-woocommerce');?></option>
		                                        </select>
		                                    </div>
		                                </div>
		                            </div>
		                            <div class="row">
		                                <!-- column -->
		                                <div class="col-lg-3">
		                                    <h1 class="marketking_total_b2b_sales_today m-b-0 m-t-30"><?php echo wc_price($data['total_b2b_sales_today']); ?></h1>
		                                    <h1 class="marketking_total_b2b_sales_seven_days m-b-0 m-t-30"><?php echo wc_price($data['total_b2b_sales_seven_days']); ?></h1>
		                                    <h1 class="marketking_total_b2b_sales_thirtyone_days m-b-0 m-t-30"><?php echo wc_price($data['total_b2b_sales_thirtyone_days']); ?></h1>
		                                    <h6 class="font-light text-muted"><?php esc_html_e('Sales','marketking-multivendor-marketplace-for-woocommerce');?></h6>
		                                    <h3 class="marketking_number_orders_today m-t-30 m-b-0"><?php echo esc_html($data['number_b2b_sales_today']); ?></h3>
		                                    <h3 class="marketking_number_orders_seven m-t-30 m-b-0"><?php echo esc_html($data['number_b2b_sales_seven_days']); ?></h3>
		                                    <h3 class="marketking_number_orders_thirtyone m-t-30 m-b-0"><?php echo esc_html($data['number_b2b_sales_thirtyone_days']); ?></h3>
		                                    <h6 class="font-light text-muted"><?php esc_html_e('Orders','marketking-multivendor-marketplace-for-woocommerce');?></h6>
		                                    <a id="marketking_dashboard_blue_button" class="btn btn-info m-t-20 p-15 p-l-25 p-r-25 m-b-20" href="javascript:void(0)"></a>
		                                </div>
		                                <!-- column -->
		                                <div class="col-lg-9">
		                                    <div class="campaign ct-charts"></div>
		                                </div>
		                                <!-- column -->
		                            </div>
		                        </div>
		                        <!-- ============================================================== -->
		                        <!-- Info Box -->
		                        <!-- ============================================================== -->
		                        <div class="card-body border-top">
		                            <div class="row m-b-0">
		                                <!-- col -->
		                                <div class="col-lg-3 col-md-6">
		                                    <div class="d-flex align-items-center">
		                                        <div class="m-r-10"><span class="text-orange display-5"><i class="icon marketking-ni marketking-ni-cart-fill"></i></span></div>
		                                        <div><span><?php esc_html_e('Total Sales','marketking-multivendor-marketplace-for-woocommerce');?></span>
		                                            <h3 class="marketking_total_b2b_sales_today font-medium m-b-0">
		                                            	<?php echo wc_price($data['total_b2b_sales_today']); ?>
		                                           	</h3>
		                                           	<h3 class="marketking_total_b2b_sales_seven_days font-medium m-b-0">
	                                           	 		<?php echo wc_price($data['total_b2b_sales_seven_days']); ?>
	                                           		</h3>
		                                           	<h3 class="marketking_total_b2b_sales_thirtyone_days font-medium m-b-0">
	                                           	 		<?php echo wc_price($data['total_b2b_sales_thirtyone_days']); ?>
	                                           		</h3>
		                                        </div>
		                                    </div>
		                                </div>
		                                <!-- col -->
		                                <!-- col -->
		                                <div class="col-lg-3 col-md-6">
		                                    <div class="d-flex align-items-center">
		                                        <div class="m-r-10"><span class="text-cyan display-5"><i class="icon marketking-ni marketking-ni-package-fill"></i></span></div>
		                                        <div><span><?php esc_html_e('Orders Nr.','marketking-multivendor-marketplace-for-woocommerce');?></span>
		                                            <h3 class="marketking_number_orders_today font-medium m-b-0"><?php echo esc_html($data['number_b2b_sales_today']); ?></h3>
		                                            <h3 class=" marketking_number_orders_seven font-medium m-b-0"><?php echo esc_html($data['number_b2b_sales_seven_days']); ?></h3>
		                                            <h3 class="marketking_number_orders_thirtyone font-medium m-b-0"><?php echo esc_html($data['number_b2b_sales_thirtyone_days']); ?></h3>
		                                        </div>
		                                    </div>
		                                </div>
		                                <!-- col -->
		                                <!-- col -->
		                                <div class="col-lg-3 col-md-6">
		                                    <div class="d-flex align-items-center">
		                                        <div class="m-r-10"><span class="text-info display-5"><i class="icon marketking-ni marketking-ni-user-circle-fill"></i></span></div>
		                                        <div><span><?php esc_html_e('New Vendors','marketking-multivendor-marketplace-for-woocommerce');?></span>
		                                            <h3 class="marketking_number_customers_today font-medium m-b-0"><?php echo esc_html($data['signups_b2b_sales_today']); ?></h3>
		                                            <h3 class="marketking_number_customers_seven font-medium m-b-0"><?php echo esc_html($data['signups_b2b_sales_seven_days']); ?></h3>
		                                            <h3 class="marketking_number_customers_thirtyone font-medium m-b-0"><?php echo esc_html($data['signups_b2b_sales_thirtyone_days']); ?></h3>
		                                        </div>
		                                    </div>
		                                </div>
		                                <!-- col -->
		                                <!-- col -->
		                                <div class="col-lg-3 col-md-6">
		                                    <div class="d-flex align-items-center">
		                                        <div class="m-r-10"><span class="text-primary display-5"><i class="icon marketking-ni marketking-ni-reports"></i></span></div>
		                                        <div><span><?php esc_html_e('Commission','marketking-multivendor-marketplace-for-woocommerce');?></span>
		                                            <h3 class="marketking_net_earnings_today font-medium m-b-0"><?php echo wc_price($data['earnings_today']); ?></h3>
		                                            <h3 class="marketking_net_earnings_seven font-medium m-b-0"><?php echo wc_price($data['earnings_seven_days']); ?></h3>
		                                            <h3 class="marketking_net_earnings_thirtyone font-medium m-b-0"><?php echo wc_price($data['earnings_thirtyone_days']); ?></h3>
		                                        </div>
		                                    </div>
		                                </div>
		                                <!-- col -->
		                            </div>
		                        </div>
		                    </div>
		                </div>
		            </div>
		            <div class="row">
		                <div class="col-sm-12 col-lg-8">
		                    <div class="card card-hover">
		                        <div class="card-body">
		                        	<?php
			                        	// get all users that need approval
			                        	$users_not_approved = get_users(array(
			                        		'meta_key'     => 'marketking_account_approved',
			                        		'meta_value'   => 'no',
			                        	));
			                        	$reg_count = count($users_not_approved);

			                        	if ($reg_count === 0){
			                        		echo '<h2>'.esc_html__('Nothing here...', 'marketking-multivendor-marketplace-for-woocommerce').'<br />'.esc_html__('No registrations need approval!', 'marketking-multivendor-marketplace-for-woocommerce').'</h2><br>';
			                        	} else {

			                        	?>
			                            <h4 class="card-title"><?php echo esc_html($reg_count); esc_html_e(' Vendor Registrations - Approval Needed' ,'marketking-multivendor-marketplace-for-woocommerce'); ?></h4>
			                            <div class="table-responsive">
			                                <table class="table v-middle">
			                                    <thead>
			                                        <tr>
			                                            <th class="border-top-0"><?php esc_html_e('Name and Email','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                                            <th class="border-top-0"><?php esc_html_e('Reg. Role','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                                            <th class="border-top-0"><?php esc_html_e('Reg. Date','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                                            <th class="border-top-0"><?php esc_html_e('Approval','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
			                                        </tr>
			                                    </thead>
			                                    <tbody>
			                                    	<?php
			                                    	$i=1;
			                                    	foreach ($users_not_approved as $user){
			                                    		// get role string
			                                    		$user_role = get_user_meta($user->ID, 'marketking_registration_option', true);
			                                    		if (isset(explode('_',$user_role)[1])){
			                                    			$user_role_id = explode('_',$user_role)[1];
			                                    		} else {
			                                    			$user_role_id = 0;
			                                    		}
			                                    		$user_role_name = get_the_title($user_role_id);

			                                    		?>
			                                    		<tr>
			                                    		    <td>
			                                    		        <div class="d-flex align-items-center">
			                                    		            <div class="m-r-10"><img src="<?php echo plugins_url('assets/dashboard/usersicons/d'.$i.'.jpg', __FILE__);?>" alt="user" class="rounded-circle" width="45" /></div>
			                                    		            <div class="">
			                                    		                <h4 class="m-b-0 font-16"><?php echo esc_html($user->user_firstname.' '.$user->user_lastname); ?></h4><span><?php echo esc_html($user->user_email); ?></span></div>
			                                    		        </div>
			                                    		    </td>
			                                    		    <td><?php echo esc_html($user_role_name); ?></td>
			                                    		    <td><?php echo esc_html(date( "d/m/Y", strtotime( $user->user_registered ) ));?></td>
			                                    		    <td class="font-medium">
			                                    		    	<div class="product-action ml-auto m-b-5 align-self-end">
			                                    		    		<a href="<?php echo esc_attr(get_edit_user_link($user->ID).'#marketking_registration_data_container'); ?>">
			                                    		    	    <button class="btn btn-success"><?php esc_html_e('Review','marketking-multivendor-marketplace-for-woocommerce'); ?></button></a>
			                                    		    	   
			                                    		    	</div>
			                                    		    </td>
			                                    		</tr>
			                                    		<?php
			                                    		$i++;
			                                    		if ($i===4){
			                                    			$i = 1;
			                                    		}
			                                    	}
			                                    	?>
			                                        
			                                    </tbody>
			                                </table>
			                            </div>
			                            <?php
			                        }
			                        ?>
		                        </div>
		                    </div>
		                </div>
		                <div class="col-sm-12 col-lg-4">
		                	<a href="<?php 
		                	if (defined('MARKETKINGPRO_DIR')){
		                		echo admin_url('/edit.php?post_type=marketking_message'); 
		                	} else {
		                		echo '#';
		                	}
		                	?>">
		                        <div class="card card-hover bg-info">
		                            <div class="card-body">
		                                <h4 class="card-title text-white op-5"><?php 
		                                if (defined('MARKETKINGPRO_DIR')){
		                                	esc_html_e('You Have','marketking-multivendor-marketplace-for-woocommerce');
		                                } else {
		                                	esc_html_e('Get Premium','marketking-multivendor-marketplace-for-woocommerce');
		                                }
		                                ?></h4>
		                                <h3 class="text-white">
		                                <?php
		                                // New messages are: How many conversations are not "resolved" AND do not have a response from admin.

		                                // first get all conversations that are new or open
		                                $new_open_conversations = get_posts( array( 
		                                	'post_type' => 'marketking_message',
		                                	'post_status' => 'publish',
		                                	'numberposts' => -1,
		                                	'fields' => 'ids',
		                                ));

		                                // go through all of them to find which ones have the latest response from someone who is a vendor
		                                $message_nr = 0;
		                                foreach ($new_open_conversations as $conversation){
		                                	// check latest response and role
		                                	$conversation_msg_nr = get_post_meta($conversation, 'marketking_message_messages_number', true);
		                                	$latest_message_author = get_post_meta($conversation, 'marketking_message_message_'.$conversation_msg_nr.'_author', true);
                                			// Get the user object.
                                			if (get_post_meta($conversation,'marketking_conversation_status', true) !== 'resolved'){
                                	            $user = get_user_by('login', $latest_message_author);
                                	            if (is_object($user)){
                                	            	if (!$user->has_cap('manage_woocommerce') || $user->has_cap('demo_user')){
                                	            		$message_nr++;
                                	            	}
                                	            } else {
                                	            	$message_nr++;
                                	            }
                                	        }
		                                }


		                                if (defined('MARKETKINGPRO_DIR')){
		                                	echo esc_html($message_nr);
		                                	esc_html_e(' New Messages','marketking-multivendor-marketplace-for-woocommerce');
		                                } else {
		                                	esc_html_e('Messages (Premium)','marketking-multivendor-marketplace-for-woocommerce');
		                                }
		                                
		                                ?>
		                                	
		                                </h3>
		                                <i class="icon marketking-ni marketking-ni-chat marketking-dashboard-icon"></i>
		                            </div>
		                        </div>
	                    	</a>
	                    	<a href="<?php echo admin_url('edit.php?post_status=pending&post_type=product'); ?>">
		                        <div class="card card-hover bg-orange">
		                            <div class="card-body">
		                                <h4 class="card-title text-white op-5"><?php esc_html_e('You have','marketking-multivendor-marketplace-for-woocommerce');?></h4>
		                                <h3 class="text-white">
		                                	<?php

		                                	$args = array(
	                                	        'numberposts'   => -1,
	                                	        'post_type'     => 'product',
	                                	        'post_status'   => 'pending',
	                                	        'fields'		=> 'ids'
	                                	    );
	                                	    $count_posts = count( get_posts( $args ) ); 


		                                	echo esc_html($count_posts);
		                                	esc_html_e(' Products Pending Review','marketking-multivendor-marketplace-for-woocommerce');
		                                	?>
		                                </h3>
		                                <i class="icon marketking-ni marketking-ni-bag marketking-dashboard-icon"></i>
		                            </div>
		                        </div>
		                    </a>
	                    </div>
		            </div>
		        </div>
		    </div>
		</div>
		<?php


	}


	// Add custom items to My account WooCommerce user menu
	function marketking_my_account_custom_items( $items ) {
		// Get current user
		$user_id = get_current_user_id();
		
		// Add messages
		if (intval(get_option('marketking_enable_messages_setting', 1)) === 1){
	    	$items = array_slice($items, 0, 2, true) +
	    	    array(get_option('marketking_messages_endpoint_setting','messages') => esc_html__( 'messages', 'marketking-multivendor-marketplace-for-woocommerce' )) + 
	    	    array_slice($items, 2, count($items)-2, true);
		}

	    return $items;

	}

	// Add custom endpoints
	function marketking_custom_endpoints() {
		
		// Add messages endpoints
		if (intval(get_option('marketking_enable_messages_setting', 1)) === 1){
			add_rewrite_endpoint( get_option('marketking_messages_endpoint_setting','messages'), EP_ROOT | EP_PAGES | EP_PERMALINK );
			add_rewrite_endpoint( get_option('marketking_message_endpoint_setting','message'), EP_ROOT | EP_PAGES | EP_PERMALINK );
		}
		do_action('marketking_extend_endpoints');


	}

	function force_permalinks_rewrite() {

	    $this->marketking_custom_endpoints();
	    
	    if (apply_filters('marketking_flush_permalinks', true)){
	    	// Flush rewrite rules
	    	flush_rewrite_rules();
	    }
	    
	}

	// messages endpoint content
	function marketking_messages_endpoint_content() {

		// Get user login
		$currentuser = wp_get_current_user();
		$currentuserlogin = $currentuser -> user_login;

		$account_type = get_user_meta($currentuser->ID, 'marketking_account_type', true);
		if ($account_type === 'subaccount'){
			// Check if user has permission to view all account messages
			$permission_view_account_messages = filter_var(get_user_meta($currentuser->ID, 'marketking_account_permission_view_messages', true), FILTER_VALIDATE_BOOLEAN); 
			if ($permission_view_account_messages === true){
				// for all intents and purposes set current user as the subaccount parent
				$parent_user_id = get_user_meta($currentuser->ID, 'marketking_account_parent', true);
				$currentuser = get_user_by('id', $parent_user_id);
				$currentuserlogin = $currentuser -> user_login;
			}
		}

		
		$accounts_login_array = array($currentuserlogin);

		// Add subaccounts to accounts array
		$subaccounts_list = get_user_meta($currentuser->ID, 'marketking_subaccounts_list', true);
		$subaccounts_list = explode(',', $subaccounts_list);
		$subaccounts_list = array_filter($subaccounts_list);
		foreach ($subaccounts_list as $subaccount_id){
			$accounts_login_array[$subaccount_id] = get_user_by('id', $subaccount_id) -> user_login;
		}

		

	    // Define custom query parameters
	    $custom_query_args = array( 'post_type' => 'marketking_message', // only messages
	    					'posts_per_page' => 8,
					        'meta_query'=> array(	// only the specific user's messages
					        	'relation' => 'OR',
			                    array(
			                        'key' => 'marketking_message_user',
			                        'value' => $accounts_login_array, 
			                        'compare' => 'IN'
			                    )

			                ));

	    // Get current page and append to custom query parameters array
	    $custom_query_args['paged'] = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

	    // Instantiate custom query
	    $custom_query = new WP_Query( $custom_query_args );

	    // Pagination fix
	    $temp_query = NULL;
	    $wp_query   = NULL;
	    $wp_query   = $custom_query;

	    // Get message Endpoint URL
	    $endpointurl = wc_get_endpoint_url(get_option('marketking_message_endpoint_setting','message'));

		?>
		<div id="marketking_myaccount_messages_container">
			<div id="marketking_myaccount_messages_container_top">
				<div id="marketking_myaccount_messages_title">
					<?php esc_html_e('Messages','marketking-multivendor-marketplace-for-woocommerce'); ?>
				</div>
				<button type="button" id="marketking_myaccount_make_inquiry_button">
					<svg class="marketking_myaccount_new_message_button_icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
					  <path fill="#fff" d="M18 0H2a2 2 0 00-2 2v18l4-4h14a2 2 0 002-2V2a2 2 0 00-2-2zM4 7h12v2H4V7zm8 5H4v-2h8v2zm4-6H4V4h12"/>
					</svg>
					<?php esc_html_e('New message','marketking-multivendor-marketplace-for-woocommerce'); ?>
				</button>
			</div>

			<!-- New message hidden panel-->
			<div class="marketking_myaccount_new_message_container">
	            <div class="marketking_myaccount_new_message_top">
	            	<div class="marketking_myaccount_new_message_top_item marketking_myaccount_new_message_new"><?php esc_html_e('New message','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
	            	<div class="marketking_myaccount_new_message_top_item marketking_myaccount_new_message_close"><?php esc_html_e('Close X','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
	            </div>
	            <div class="marketking_myaccount_new_message_content">
	            	<?php do_action('marketking_start_new_message'); ?>
	            	<div class="marketking_myaccount_new_message_content_element">
	            		<div class="marketking_myaccount_new_message_content_element_text"><?php esc_html_e('Type','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
	            		<select id="marketking_myaccount_message_type">
	            			<?php
	            				ob_start();
	            				?>
		            			<option value="inquiry"><?php esc_html_e('Inquiry','marketking-multivendor-marketplace-for-woocommerce'); ?></option>
		            			<option value="message"><?php esc_html_e('Message','marketking-multivendor-marketplace-for-woocommerce'); ?></option>
		            			<option value="quote"><?php esc_html_e('Quote Request','marketking-multivendor-marketplace-for-woocommerce'); ?></option>
		            			<?php
		            			$content = ob_get_clean();
		            			$content = apply_filters('marketking_filter_message_types_dropdown', $content);
		            			echo $content;
		            		?>
	            		</select>
	            	</div>
	            	<div class="marketking_myaccount_new_message_content_element">
	            		<div class="marketking_myaccount_new_message_content_element_text"><?php esc_html_e('Title','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
	            		<input type="text" id="marketking_myaccount_title_message_start" placeholder="<?php esc_attr_e('Enter the title here...','marketking-multivendor-marketplace-for-woocommerce') ?>">
	            	</div>
	            	<div class="marketking_myaccount_new_message_content_element">
	            		<div class="marketking_myaccount_new_message_content_element_text"><?php esc_html_e('Message','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
	            		<textarea id="marketking_myaccount_textarea_message_start" placeholder="<?php esc_attr_e('Enter your message here...','marketking-multivendor-marketplace-for-woocommerce') ?>"></textarea>
	            	</div>
	                <div class="marketking_myaccount_start_message_bottom">
	                	<button id="marketking_myaccount_send_inquiry_button" class="marketking_myaccount_start_message_button" type="button">
	                		<svg class="marketking_myaccount_start_message_button_icon" xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="none" viewBox="0 0 21 21">
	            		  	<path fill="#fff" d="M5.243 12.454h9.21v4.612c0 .359-.122.66-.368.906-.246.245-.567.377-.964.396H5.243L1.955 21v-2.632h-.651a1.19 1.19 0 01-.907-.396A1.414 1.414 0 010 17.066V8.52c0-.358.132-.67.397-.934.264-.264.567-.387.907-.368h3.939v5.236zM19.696.002c.378 0 .69.123.936.368.245.245.368.566.368.962V9.85c0 .359-.123.66-.368.906a1.37 1.37 0 01-.936.396h-.652v2.632l-3.287-2.632H6.575v-9.82c0-.377.123-.698.368-.962.246-.264.558-.387.936-.368h11.817z"/>
	            			</svg>
	                		<?php esc_html_e('Start message','marketking-multivendor-marketplace-for-woocommerce'); ?>
	                	</button>
	                </div>
	            </div>
	        </div>


			<?php
			// Display each message
			// Output custom query loop
			if ( $custom_query->have_posts() ) {
			    while ( $custom_query->have_posts() ) {
			        $custom_query->the_post();
			        global $post;

			        $message_title = $post->post_title;
			        $message_type = get_post_meta($post->ID, 'marketking_message_type', true);
			        $username = get_post_meta($post->ID, 'marketking_message_user', true);

			        $nr_messages = get_post_meta ($post->ID, 'marketking_message_messages_number', true);
			        $last_reply_time = intval(get_post_meta ($post->ID, 'marketking_message_message_'.$nr_messages.'_time', true));

			        // build time string
				    // if today
				    if((time()-$last_reply_time) < 86400){
				    	// show time
				    	$message_last_reply = date_i18n( 'h:i A', $last_reply_time+(get_option('gmt_offset')*3600) );
				    } else if ((time()-$last_reply_time) < 172800){
				    // if yesterday
				    	$message_last_reply = 'Yesterday at '.date_i18n( 'h:i A', $last_reply_time+(get_option('gmt_offset')*3600) );
				    } else {
				    // date
				    	$message_last_reply = date_i18n( get_option('date_format'), $last_reply_time+(get_option('gmt_offset')*3600) ); 
				    }

			        ?>
	    			<div class="marketking_myaccount_individual_message_container">
	                    <div class="marketking_myaccount_individual_message_top">
	                    	<div class="marketking_myaccount_individual_message_top_item"><?php esc_html_e('Title','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
	                    	<div class="marketking_myaccount_individual_message_top_item"><?php esc_html_e('Type','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
	                    	<div class="marketking_myaccount_individual_message_top_item"><?php esc_html_e('User','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
	                    	<?php do_action('marketking_myaccount_messages_items_title', $post->ID); ?>
	                    	<div class="marketking_myaccount_individual_message_top_item"><?php esc_html_e('Last Reply','marketking-multivendor-marketplace-for-woocommerce'); ?></div>
	                    </div>
	                    <div class="marketking_myaccount_individual_message_content">
	                    	<div class="marketking_myaccount_individual_message_content_item"><?php echo esc_html($message_title); ?></div>
	                    	<div class="marketking_myaccount_individual_message_content_item"><?php
	                    	switch ($message_type) {
	                    	  case "inquiry":
	                    	    esc_html_e('inquiry','marketking-multivendor-marketplace-for-woocommerce');
	                    	    break;
	                    	  case "message":
	                    	    esc_html_e('message','marketking-multivendor-marketplace-for-woocommerce');
	                    	    break;
	                    	  case "support":
	                    	    esc_html_e('support','marketking-multivendor-marketplace-for-woocommerce');
	                    	    break;
	                    	  case "quote":
	                    	    esc_html_e('quote','marketking-multivendor-marketplace-for-woocommerce');
	                    	    break;
	                    	}
	                    	?></div>
	                    	<div class="marketking_myaccount_individual_message_content_item"><?php echo esc_html($username); ?></div>
	                    	<?php do_action('marketking_myaccount_messages_items_content', $post->ID); ?>
	                    	<div class="marketking_myaccount_individual_message_content_item"><?php echo esc_html($message_last_reply); ?></div>
	                    </div>
	                    <div class="marketking_myaccount_individual_message_bottom">
	                    	<a href="<?php echo esc_url(add_query_arg('id',$post->ID,$endpointurl)); ?>">
	                        	<button class="marketking_myaccount_view_message_button" type="button">
	                        		<svg class="marketking_myaccount_view_message_button_icon" xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="none" viewBox="0 0 21 21">
	                        		  <path fill="#fff" d="M5.243 12.454h9.21v4.612c0 .359-.122.66-.368.906-.246.245-.567.377-.964.396H5.243L1.955 21v-2.632h-.651a1.19 1.19 0 01-.907-.396A1.414 1.414 0 010 17.066V8.52c0-.358.132-.67.397-.934.264-.264.567-.387.907-.368h3.939v5.236zM19.696.002c.378 0 .69.123.936.368.245.245.368.566.368.962V9.85c0 .359-.123.66-.368.906a1.37 1.37 0 01-.936.396h-.652v2.632l-3.287-2.632H6.575v-9.82c0-.377.123-.698.368-.962.246-.264.558-.387.936-.368h11.817z"/>
	                        		</svg>
	                        		<?php esc_html_e('View message','marketking-multivendor-marketplace-for-woocommerce'); ?>
	                        	</button>
	                        </a>
	                    </div>
	    	        </div>

			        <?php

			    }
			} else {
				wc_print_notice(esc_html__('No messages exist.', 'marketking-multivendor-marketplace-for-woocommerce'), 'notice');
			}

			?>

		</div>

		<?php
		
	    // Reset postdata
	    wp_reset_postdata();
	    ?>
	   	<div class="marketking_myaccount_messages_pagination_container">
		    <div class="marketking_myaccount_messages_pagination_button marketking_newer_messages_button">
		    	<?php previous_posts_link( esc_html__('← Newer messages','marketking-multivendor-marketplace-for-woocommerce') ); ?>
		    </div>
		    <div class="marketking_myaccount_messages_pagination_button marketking_older_messages_button">
		    	<?php next_posts_link( esc_html__('Older messages →','marketking-multivendor-marketplace-for-woocommerce'), $custom_query->max_num_pages ); ?>
		    </div>
		</div>
	    <?php

	    // Reset main query object
	    $wp_query = NULL;
	    $wp_query = $temp_query;

	}


	// Individual message endpoint
	function marketking_message_endpoint_content() {

		$message_id = sanitize_text_field( $_GET['id'] );
		$message_title = get_the_title($message_id);
		$message_type = get_post_meta($message_id, 'marketking_message_type',true);
	    $starting_time = intval(get_post_meta ($message_id, 'marketking_message_message_1_time', true));

	    // build time string
	    // if today
	    if((time()-$starting_time) < 86400){
	    	// show time
	    	$message_started_time = date_i18n( 'h:i A', $starting_time+(get_option('gmt_offset')*3600));
	    } else if ((time()-$starting_time) < 172800){
	    // if yesterday
	    	$message_started_time = 'Yesterday at '.date_i18n( 'h:i A', $starting_time+(get_option('gmt_offset')*3600) );
	    } else {
	    // date
	    	$message_started_time = date_i18n( get_option('date_format'), $starting_time+(get_option('gmt_offset')*3600) ); 
	    }

		// Get messages Endpoint URL
		$endpointurl = wc_get_endpoint_url(get_option('marketking_messages_endpoint_setting','messages'));

		?>
		<div id="marketking_myaccount_message_endpoint_container">
			<div id="marketking_myaccount_message_endpoint_container_top">
				<div id="marketking_myaccount_message_endpoint_title">
					<?php echo esc_html($message_title); ?>
				</div>
				<a href="<?php echo esc_url($endpointurl); ?>">
					<button type="button">
						<?php esc_html_e('←  Go Back','marketking-multivendor-marketplace-for-woocommerce'); ?>
					</button>
				</a>
			</div>
			<div id="marketking_myaccount_message_endpoint_container_top_header">
				<div class="marketking_myaccount_message_endpoint_container_top_header_item"><?php esc_html_e('Type:','marketking-multivendor-marketplace-for-woocommerce'); ?> <span class="marketking_myaccount_message_endpoint_top_header_text_bold"><?php echo esc_html($message_type); ?></span></div>
				<div class="marketking_myaccount_message_endpoint_container_top_header_item"><?php esc_html_e('Date Started:','marketking-multivendor-marketplace-for-woocommerce'); ?> <span class="marketking_myaccount_message_endpoint_top_header_text_bold"><?php echo esc_html($message_started_time); ?></span></div>
			</div>
		<?php
		
		// Check user permission against message user meta
		$user = get_post_meta ($message_id, 'marketking_message_user', true);
		// build array of current login + subaccount logins
		$current_user = wp_get_current_user();
		$subaccounts_list = get_user_meta($current_user->ID, 'marketking_subaccounts_list', true);
		$subaccounts_list = explode (',',$subaccounts_list);
		$subaccounts_list = array_filter($subaccounts_list);
		$logins_array = array($current_user->user_login);
		foreach($subaccounts_list as $subaccount_id){
			$username = get_user_by('id', $subaccount_id)->user_login;
			$logins_array[$subaccount_id] = $username;
		}

		// if current user is a subaccount, give access to parent + subaccounts, IF it has permission to see all account messages
		$account_type = get_user_meta($current_user->ID, 'marketking_account_type', true);
		if($account_type === 'subaccount'){
			$permission_view_messages = filter_var(get_user_meta($current_user->ID, 'marketking_account_permission_view_messages', true), FILTER_VALIDATE_BOOLEAN); 
			if ($permission_view_messages === true){
				// give access to parent
				$parent_id = get_user_meta($current_user->ID, 'marketking_account_parent', true);
				$parent_user = get_user_by('id', $parent_id);
				$logins_array[$parent_id] = $parent_user->user_login;
				// give access to parent subaccounts
				$parent_subaccounts_list = get_user_meta($parent_id, 'marketking_subaccounts_list', true);
				$parent_subaccounts_list = explode (',',$parent_subaccounts_list);
				$parent_subaccounts_list = array_filter($parent_subaccounts_list);
				foreach($parent_subaccounts_list as $subaccount_id){
					$username = get_user_by('id', $subaccount_id)->user_login;
					$logins_array[$subaccount_id] = $username;
				}
			}
		}

		// if message user is part of the logins array (user + subaccounts), give permission
		if (in_array($user, $logins_array)){
			// Display message

			// get number of messages
			$nr_messages = get_post_meta ($message_id, 'marketking_message_messages_number', true);
			?>
			<div id="marketking_message_messages_container">
				<?php	
				// loop through and display messages
				for ($i = 1; $i <= $nr_messages; $i++) {
				    // get message details
				    $message = get_post_meta ($message_id, 'marketking_message_message_'.$i, true);
				    $author = get_post_meta ($message_id, 'marketking_message_message_'.$i.'_author', true);
				    $time = get_post_meta ($message_id, 'marketking_message_message_'.$i.'_time', true);
				    // check if message author is self, parent, or subaccounts
				    $current_user_id = get_current_user_id();
				    $subaccounts_list = get_user_meta($current_user_id,'marketking_subaccounts_list', true);
				    $subaccounts_list = explode(',', $subaccounts_list);
				    $subaccounts_list = array_filter($subaccounts_list);
				    array_push($subaccounts_list, $current_user_id);

					// add parent account+all subaccounts lists
				    $account_type = get_user_meta($current_user_id, 'marketking_account_type', true);
				    if ($account_type === 'subaccount'){
						$parent_account = get_user_meta($current_user_id, 'marketking_account_parent', true);
			    		$parent_subaccounts_list = explode(',', get_user_meta($parent_account, 'marketking_subaccounts_list', true));
			    		$parent_subaccounts_list = array_filter($parent_subaccounts_list); // filter blank, null, etc.
			    		array_push($parent_subaccounts_list, $parent_account); // add parent itself to form complete parent accounts list

			    		$subaccounts_list = array_merge($subaccounts_list, $parent_subaccounts_list);
				    }



				    foreach ($subaccounts_list as $user){
				    	$subaccounts_list[$user] = get_user_by('id', $user)->user_login;
				    }
				    if (in_array($author, $subaccounts_list)){
				    	$self = ' marketking_message_message_self';
				    } else {
				    	$self = '';
				    }
				    // build time string
					    // if today
					    if((time()-$time) < 86400){
					    	// show time
					    	$timestring = date_i18n( 'h:i A', $time+(get_option('gmt_offset')*3600) );
					    } else if ((time()-$time) < 172800){
					    // if yesterday
					    	$timestring = 'Yesterday at '.date_i18n( 'h:i A', $time+(get_option('gmt_offset')*3600) );
					    } else {
					    // date
					    	$timestring = date_i18n( get_option('date_format'), $time+(get_option('gmt_offset')*3600) ); 
					    }
				    ?>
				    <div class="marketking_message_message <?php echo esc_attr($self).' '; 

				    // check system message
				    if ($author === esc_html__('System Message','marketking-multivendor-marketplace-for-woocommerce')){
				    	echo 'marketking_message_system_message';
				    }
				    ?>">
				    	<?php echo nl2br($message); ?>
				    	<div class="marketking_message_message_time">
				    		<?php echo esc_html($author).' - '; ?>
				    		<?php echo esc_html($timestring); ?>
				    	</div>
				    </div>
				    <?php
				}
				?>
			</div>
			<textarea name="marketking_message_user_new_message" id="marketking_message_user_new_message"></textarea><br />
			<input type="hidden" id="marketking_message_id" value="<?php echo esc_attr($message_id); ?>">
			<div class="marketking_myaccount_message_endpoint_bottom">
		    	<button id="marketking_message_message_submit" class="marketking_myaccount_message_endpoint_button" type="button">
		    		<svg class="marketking_myaccount_message_endpoint_button_icon" xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="none" viewBox="0 0 21 21">
				  	<path fill="#fff" d="M5.243 12.454h9.21v4.612c0 .359-.122.66-.368.906-.246.245-.567.377-.964.396H5.243L1.955 21v-2.632h-.651a1.19 1.19 0 01-.907-.396A1.414 1.414 0 010 17.066V8.52c0-.358.132-.67.397-.934.264-.264.567-.387.907-.368h3.939v5.236zM19.696.002c.378 0 .69.123.936.368.245.245.368.566.368.962V9.85c0 .359-.123.66-.368.906a1.37 1.37 0 01-.936.396h-.652v2.632l-3.287-2.632H6.575v-9.82c0-.377.123-.698.368-.962.246-.264.558-.387.936-.368h11.817z"/>
					</svg>
		    		<?php esc_html_e('Send Message','marketking-multivendor-marketplace-for-woocommerce'); ?>
		    	</button>
			</div>
			<?php
		} else {
			esc_html_e('message does not exist!','marketking-multivendor-marketplace-for-woocommerce'); // or user does not have permission
		}
		echo '</div>';

	}
	
	
		/**
		 * Adds the order processing count to the menu.
		 */
		public function menu_order_count() {
			global $submenu;
			
			// get all users that need approval
			$users_not_approved = get_users(array(
				'meta_key'     => 'marketking_account_approved',
				'meta_value'   => 'no',
			));
			$reg_count = count($users_not_approved);

			$message_nr = 0;
			$total_notifications = 0;

			// get messages in need of reply
			if (defined('MARKETKINGPRO_DIR')){
				// first get all conversations that are new or open
				$new_open_conversations = get_posts( array( 
					'post_type' => 'marketking_message',
					'post_status' => 'publish',
					'numberposts' => -1,
					'fields' => 'ids',
				));

				// go through all of them to find which ones have the latest response from someone who is a vendor
				foreach ($new_open_conversations as $conversation){
					// check latest response and role
					$conversation_msg_nr = get_post_meta($conversation, 'marketking_message_messages_number', true);
					$latest_message_author = get_post_meta($conversation, 'marketking_message_message_'.$conversation_msg_nr.'_author', true);
					// Get the user object.
					if (get_post_meta($conversation,'marketking_conversation_status', true) !== 'resolved'){
			            $user = get_user_by('login', $latest_message_author);
			            if (is_object($user)){
			            	if (!$user->has_cap('manage_woocommerce') || $user->has_cap('demo_user')){
			            		$message_nr++;
			            	}
			            } else {
			            	$message_nr++;
			            }
			        }
				}
			}
			$reg_count += $message_nr;

			// get pending products
			$args = array(
		        'numberposts'   => -1,
		        'post_type'     => 'product',
		        'post_status'   => 'pending',
		        'fields'		=> 'ids'
		    );
		    $count_posts = count( get_posts( $args ) ); 


		    $reg_count+=$count_posts;

			if ($reg_count > 0){
				if (isset($submenu['marketking'])){
					if (is_array($submenu['marketking'])){
						foreach ( $submenu['marketking'] as $key => $menu_item ) {
							if ( 0 === strpos( $menu_item[0], _x( 'Dashboard', 'Admin menu name', 'marketking-multivendor-marketplace-for-woocommerce' ) ) ) {
								$submenu['marketking'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $reg_count ) . '"><span class="processing-count">' . number_format_i18n( $reg_count ) . '</span></span>'; 
								break;
							}
						}
					}
				}
			}

			$total_notifications += $message_nr;
			$total_notifications += $reg_count;

			// withdrawal requests

			if (defined('MARKETKINGPRO_DIR')){
				if (intval(get_option('marketking_enable_withdrawals_setting', 1)) === 1){
					$withdrawnr = marketking()->get_withdrawal_requests_number();

					if (intval($withdrawnr) !== 0){
						if (isset($submenu['marketking'])){
							if (is_array($submenu['marketking'])){
								foreach ( $submenu['marketking'] as $key => $menu_item ) {
									if ( 0 === strpos( $menu_item[0], _x( 'Payouts', 'Admin menu name', 'marketking-multivendor-marketplace-for-woocommerce' ) ) ) {
										$submenu['marketking'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $withdrawnr ) . '"><span class="processing-count">' . number_format_i18n( $withdrawnr ) . '</span></span>'; 
										break;
									}
								}
							}
						}
					}

					$total_notifications += $withdrawnr;

				}
			}

			// pending refunds
			if (defined('MARKETKINGPRO_DIR')){
				if (intval(get_option('marketking_enable_refunds_setting', 1)) === 1){
					$refundsnr = marketking()->get_refund_requests_number();

					if (intval($refundsnr) !== 0){
						if (isset($submenu['marketking'])){
							if (is_array($submenu['marketking'])){
								foreach ( $submenu['marketking'] as $key => $menu_item ) {
									if ( 0 === strpos( $menu_item[0], _x( 'Refunds', 'Admin menu name', 'marketking-multivendor-marketplace-for-woocommerce' ) ) ) {
										$submenu['marketking'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $refundsnr ) . '"><span class="processing-count">' . number_format_i18n( $refundsnr ) . '</span></span>'; 
										break;
									}
								}
							}
						}
					}

					$total_notifications += $refundsnr;

				}
			}


			// pending verifications
			if (defined('MARKETKINGPRO_DIR')){
				if (intval(get_option('marketking_enable_verification_setting', 1)) === 1){
					$verifnr = marketking()->get_pending_verifications_number();

					if (intval($verifnr) !== 0){
						if (isset($submenu['marketking'])){
							if (is_array($submenu['marketking'])){
								foreach ( $submenu['marketking'] as $key => $menu_item ) {
									if ( 0 === strpos( $menu_item[0], _x( 'Verifications', 'Admin menu name', 'marketking-multivendor-marketplace-for-woocommerce' ) ) ) {
										$submenu['marketking'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $verifnr ) . '"><span class="processing-count">' . number_format_i18n( $verifnr ) . '</span></span>'; 
										break;
									}
								}
							}
						}
					}

					$total_notifications += $verifnr;

				}
			}

			global $menu;
			if ($total_notifications > 0){
				foreach ($menu as $key => $menu_item){
					if ($menu_item[2] === 'marketking'){
						$menu[$key][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $total_notifications ) . '"><span class="processing-count">' . number_format_i18n( $total_notifications ) . '</span></span>'; 
					}
				}			
			}

		}	

		function marketking_settings_page() {

			// Admin Menu Settings 
			$page_title = esc_html__('MarketKing','marketking-multivendor-marketplace-for-woocommerce');
			$menu_title = esc_html__('MarketKing','marketking-multivendor-marketplace-for-woocommerce');
			$capability = 'manage_woocommerce';
			$slug = 'marketking';
			$callback = array( $this, 'marketking_settings_page_content' );

			$iconurl = plugins_url('../includes/assets/images/marketking-icon-graphik.svg', __FILE__);
			$position = 54;
			add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $iconurl, $position );

			// Build plugin file path relative to plugins folder
			$absolutefilepath = dirname(plugins_url('', __FILE__),1);
			$pluginsurllength = strlen(plugins_url())+1;
			$relativepath = substr($absolutefilepath, $pluginsurllength);

			// Add the action links
			add_filter('plugin_action_links_'.$relativepath.'/marketking-core.php', array($this, 'marketking_action_links') );


			// Add "Dashboard" submenu page
	    	add_submenu_page(
	            'marketking',
	            esc_html__('Dashboard','marketking-multivendor-marketplace-for-woocommerce'), //page title
	            esc_html__('Dashboard','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	            'manage_woocommerce', //capability,
	            'marketking_dashboard',//menu slug
	            array( $this, 'marketking_dashboard_page_content' ), //callback function
	        	1
	        );


		 	if (defined('MARKETKINGPRO_DIR')){

	 		    // Add "Announcements" submenu page
	 		    if (intval(get_option( 'marketking_enable_announcements_setting', 1 )) === 1){
	 				add_submenu_page(
	 			        'marketking',
	 			        esc_html__('Announcements','marketking-multivendor-marketplace-for-woocommerce'), //page title
	 			        esc_html__('Announcements','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	 			        'manage_woocommerce', //capability,
	 			        'edit.php?post_type=marketking_announce',//menu slug
	 			        '', //callback function
	 			    	2	
	 			    );
	 			}


	 			// vendor groups
	 			add_submenu_page(
	 		        'marketking',
	 		        esc_html__('Vendor Groups','marketking-multivendor-marketplace-for-woocommerce'), //page title
	 		        esc_html__('Vendor Groups','marketking-multivendor-marketplace-for-woocommerce'), //menu title
			            'manage_woocommerce', //capability,
			            'marketking_groups', //menu slug
			            array( $this, 'marketking_groups_page_content' ), //callback function
			        	4	
	 		    );
				
		 	}

	        add_submenu_page(
	            'marketking',
	            esc_html__('Vendors','marketking-multivendor-marketplace-for-woocommerce'), //page title
	            esc_html__('Vendors','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	            'manage_woocommerce', //capability,
	            'marketking_vendors',//menu slug
	            array( $this, 'marketking_vendors_page_content' ), //callback function
	        	10
	        );

	        if (defined('MARKETKINGPRO_DIR')){

		        // Add "Messages" submenu page
	 		    if (intval(get_option( 'marketking_enable_messages_setting', 1 )) === 1){
	 				add_submenu_page(
	 			        'marketking',
	 			        esc_html__('Messages','marketking-multivendor-marketplace-for-woocommerce'), //page title
	 			        esc_html__('Messages','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	 			        'manage_woocommerce', //capability,
	 			        'edit.php?post_type=marketking_message',//menu slug
	 			        '', //callback function
	 			    	12	
	 			    );
	 			}
	 		}

	        add_submenu_page(
	            'marketking',
	            esc_html__('Payouts','marketking-multivendor-marketplace-for-woocommerce'), //page title
	            esc_html__('Payouts','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	            'manage_woocommerce', //capability,
	            'marketking_payouts',//menu slug
	            array( $this, 'marketking_payouts_page_content' ), //callback function
	        	13
	        );

			if(defined('MARKETKINGPRO_DIR')){
				add_submenu_page(
			        'marketking',
			        esc_html__('Reports','marketking-multivendor-marketplace-for-woocommerce'), //page title
			        esc_html__('Reports','marketking-multivendor-marketplace-for-woocommerce'), //menu title
			        'manage_woocommerce', //capability,
			        'marketking_reports',//menu slug
			        array( $this, 'marketking_reports_page_content' ), //callback function
			    	14	
			    );
	        }

	        if(defined('MARKETKINGPRO_DIR')){
	        	if (intval(get_option( 'marketking_enable_complexcommissions_setting', 1 )) === 1){
	    			add_submenu_page(
	    		        'marketking',
	    		        esc_html__('Commission Rules','marketking-multivendor-marketplace-for-woocommerce'), //page title
	    		        esc_html__('Commission Rules','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	    		        'manage_woocommerce', //capability,
	    		        'edit.php?post_type=marketking_rule',//menu slug
	    		        '', //callback function
	    		    	15	
	    		    );
	        	}
	        }

	        if(defined('MARKETKINGPRO_DIR')){
	        	if (intval(get_option( 'marketking_enable_abusereports_setting', 1 )) === 1){
	    			add_submenu_page(
	    		        'marketking',
	    		        esc_html__('Abuse Reports','marketking-multivendor-marketplace-for-woocommerce'), //page title
	    		        esc_html__('Abuse Reports','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	    		        'manage_woocommerce', //capability,
	    		        'edit.php?post_type=marketking_abuse',//menu slug
	    		        '', //callback function
	    		    	16	
	    		    );
	        	}
	        }

	        if(defined('MARKETKINGPRO_DIR')){
	        	if (intval(get_option( 'marketking_enable_memberships_setting', 1 )) === 1){
	    			add_submenu_page(
	    		        'marketking',
	    		        esc_html__('Memberships','marketking-multivendor-marketplace-for-woocommerce'), //page title
	    		        esc_html__('Memberships','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	    		        'manage_woocommerce', //capability,
	    		        'edit.php?post_type=marketking_mpack',//menu slug
	    		        '',
	    		    	18	
	    		    );
	        	}
	        }

	        if(defined('MARKETKINGPRO_DIR')){
	        	if (intval(get_option( 'marketking_enable_verification_setting', 1 )) === 1){
	    			add_submenu_page(
	    		        'marketking',
	    		        esc_html__('Verifications','marketking-multivendor-marketplace-for-woocommerce'), //page title
	    		        esc_html__('Verifications','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	    		        'manage_woocommerce', //capability,
	    		        'edit.php?post_type=marketking_vreq',//menu slug
	    		        '',
	    		    	19	
	    		    );
	        	}
	        }

	        if(defined('MARKETKINGPRO_DIR')){
	        	if (intval(get_option( 'marketking_enable_badges_setting', 1 )) === 1){
	    			add_submenu_page(
	    		        'marketking',
	    		        esc_html__('Badges','marketking-multivendor-marketplace-for-woocommerce'), //page title
	    		        esc_html__('Badges','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	    		        'manage_woocommerce', //capability,
	    		        'edit.php?post_type=marketking_badge',//menu slug
	    		        '',
	    		    	20	
	    		    );
	        	}
	        }

	        if(defined('MARKETKINGPRO_DIR')){
	        	if (intval(get_option( 'marketking_enable_refunds_setting', 1 )) === 1){
	    			add_submenu_page(
	    		        'marketking',
	    		        esc_html__('Refunds','marketking-multivendor-marketplace-for-woocommerce'), //page title
	    		        esc_html__('Refunds','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	    		        'manage_woocommerce', //capability,
	    		        'edit.php?post_type=marketking_refund',//menu slug
	    		        '',
	    		    	21	
	    		    );
	        	}
	        }
	      

	    	add_submenu_page(
	            'marketking',
	            esc_html__('Modules','marketking-multivendor-marketplace-for-woocommerce'), //page title
	            esc_html__('Modules','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	            'manage_woocommerce', //capability,
	            'marketking_modules',//menu slug
	            array( $this, 'marketking_modules_page_content' ), //callback function
	        	22
	        );

	         // Individual Payout Page
	    	add_submenu_page(
	            '',
	            esc_html__('View Payouts','marketking-multivendor-marketplace-for-woocommerce'), //page title
	            esc_html__('View Payouts','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	            'manage_woocommerce', //capability,
	            'marketking_view_payouts', //menu slug
	            array( $this, 'marketking_view_payouts_content' ), //callback function
	        	1
	        );

		    if (defined('MARKETKINGPRO_DIR')){
		    	if (intval(get_option( 'marketking_enable_registration_setting', 1 )) === 1){
		    	    // Add "Registration" submenu page
		    		add_submenu_page(
		    	        'marketking',
		    	        esc_html__('Registration','marketking-multivendor-marketplace-for-woocommerce'), //page title
		    	        esc_html__('Registration','marketking-multivendor-marketplace-for-woocommerce'), //menu title
		    	        'manage_woocommerce', //capability,
		    	        'marketking_registration', //menu slug
		    	        array( $this, 'marketking_registration_page_content' ), //callback function
		    	    	23
		    	    );
		    	}
		    }

		    if (defined('MARKETKINGPRO_DIR')){
		    	if (intval(get_option( 'marketking_enable_vendordocs_setting', 1 )) === 1){
		    	    // Add "Registration" submenu page
		    		add_submenu_page(
		    	        'marketking',
		    	        esc_html__('Seller Docs','marketking-multivendor-marketplace-for-woocommerce'), //page title
		    	        esc_html__('Seller Docs','marketking-multivendor-marketplace-for-woocommerce'), //menu title
		    	        'manage_woocommerce', //capability,
		    	        'edit.php?post_type=marketking_docs',//menu slug
		    	        '', //callback function
		    	    	24
		    	    );
		    	}
		    }

		    if (!defined('MARKETKINGPRO_DIR')){
	    	    global $submenu;
	    	    $submenu['marketking'][] = array( '<b style="color:#d6a228">'.esc_html__('Get Premium','marketking-multivendor-marketplace-for-woocommerce').'</b>', 'manage_options' , 'https://woocommerce-multivendor.com/pricing', 'https://woocommerce-multivendor.com/pricing', 'marketking-upgrade-to-premium' ); 


		    }

	        if(defined('MARKETKINGPRO_DIR')){
	        	if (intval(get_option( 'marketking_enable_reviews_setting', 1 )) === 1){
	    			add_submenu_page(
	    		        'marketking',
	    		        esc_html__('Store Reviews','marketking-multivendor-marketplace-for-woocommerce'), //page title
	    		        esc_html__('Store Reviews','marketking-multivendor-marketplace-for-woocommerce'), //menu title
	    		        'manage_woocommerce', //capability,
	    		        'marketking_reviews',//menu slug
	    		        array( $this, 'marketking_reviews_page_display' ), //callback function
	    		    	27	
	    		    );
	        	}

	        	if (intval(get_option('marketking_enable_storecategories_setting', 1)) === 1){

        			add_submenu_page(
        		        'marketking',
        		        esc_html__('Store Categories','marketking-multivendor-marketplace-for-woocommerce'), //page title
        		        esc_html__('Store Categories','marketking-multivendor-marketplace-for-woocommerce'), //menu title
        		        'manage_woocommerce', //capability,
        		        'edit-tags.php?taxonomy=storecat',//menu slug
        		        '',
        		    	28	
        		    );

        		}
	        						
	        }

	        // Add "Settings" submenu page
			add_submenu_page(
		        'marketking',
		        esc_html__('Settings','marketking-multivendor-marketplace-for-woocommerce'), //page title
		        esc_html__('Settings','marketking-multivendor-marketplace-for-woocommerce'), //menu title
		        'manage_woocommerce', //capability,
		        'marketking',//menu slug
		        '', //callback function
		    	30	
		    );


		 	remove_submenu_page('marketking','marketking');


		}	

		function marketking_action_links( $links ) {
			// Build and escape the URL.
			$url = esc_url( add_query_arg('page', 'marketking', get_admin_url() . 'admin.php') );

			// Create the link.
			$settings_link = '<a href='.esc_attr($url).'>' . esc_html__( 'Settings', 'marketking-multivendor-marketplace-for-woocommerce' ) . '</a>';
			
			// Adds the link to the end of the array.
			array_unshift($links,	$settings_link );
			return $links;
		}


		function marketking_options_capability( $capability ) {
		    return 'manage_woocommerce';
		}

		function load_global_admin_notice_resource(){
			wp_enqueue_script( 'marketking_global_admin_notice_script', plugins_url('assets/js/adminnotice.js', __FILE__), $deps = array(), $ver = MARKETKINGCORE_VERSION, $in_footer =true);

			// Send data to JS
			$data_js = array(
				'security'  => wp_create_nonce( 'marketking_notice_security_nonce' ),
			);
			wp_localize_script( 'marketking_global_admin_notice_script', 'marketking_notice', $data_js );
			
		}

		function load_global_admin_resources( $hook ){
			// compatibility with welaunch single variations plugin
			wp_enqueue_script('jquery');
			if ($hook !== 'woocommerce_page_woocommerce_single_variations_options_options'){
				wp_enqueue_style('select2', plugins_url('../includes/assets/lib/select2/select2.min.css', __FILE__) );
				wp_enqueue_script('select2', plugins_url('../includes/assets/lib/select2/select2.min.js', __FILE__), array('jquery') );
			}

			if (isset($_GET['post_type'])){
				$type = sanitize_text_field($_GET['post_type']);
			} else {
				$type = '';
			}

			$post_type = '';
			if (isset($_GET['post'])){
				$post_type = get_post_type(sanitize_text_field($_GET['post'] ));
			}

			wp_enqueue_style ( 'marketking_global_admin_style', plugins_url('assets/css/adminglobal.css', __FILE__));
			// Enqueue color picker
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_media();

			wp_enqueue_script('notify', plugins_url('../includes/assets/lib/notify/notify.min.js', __FILE__) );


			wp_enqueue_script( 'marketking_global_admin_script', plugins_url('assets/js/adminglobal.js', __FILE__), $deps = array('wp-color-picker'), $ver = MARKETKINGCORE_VERSION, $in_footer =true);

			if ($hook === 'marketking_page_marketking_dashboard' || $hook === 'marketking_page_marketking_reports'){
				wp_enqueue_style( 'marketking_admin_dashboard', plugins_url('assets/dashboard/cssjs/dashboardstyle.min.css', __FILE__));
			}
			if (substr( $hook, 0, 10 ) === "marketking" || substr( $hook, 0, 21 ) === "admin_page_marketking" || substr($type, 0, 10) === 'marketking' || substr($post_type, 0, 10) === 'marketking' || $hook === 'toplevel_page_marketking'){

				wp_enqueue_script('dataTables', plugins_url('../includes/assets/lib/dataTables/jquery.dataTables.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
				wp_enqueue_style( 'dataTables', plugins_url('../includes/assets/lib/dataTables/jquery.dataTables.min.css', __FILE__));
				wp_enqueue_style ( 'marketking_pages_admin_style', plugins_url('assets/css/adminmkpages.css', __FILE__), $deps = array(), $ver = MARKETKINGCORE_VERSION);

				// Dashboard
				wp_enqueue_style ('marketking_chartist', plugins_url('assets/dashboard/chartist/chartist.min.css', __FILE__));
				wp_enqueue_script('marketking_chartist', plugins_url('assets/dashboard/chartist/chartist.min.js', __FILE__), $deps = array(), $ver = MARKETKINGCORE_VERSION, $in_footer =true);
				wp_enqueue_script('marketking_chartist-plugin-tooltip', plugins_url('assets/dashboard/chartist/chartist-plugin-tooltip.min.js', __FILE__), $deps = array(), $ver = MARKETKINGCORE_VERSION, $in_footer =true);
			}

			$pageslug = '';
			if (isset($_GET['page'])){
				$pageslug = sanitize_text_field($_GET['page']);
			} else if (isset($_GET['post_type'])){
				$pageslug = sanitize_text_field($_GET['post_type']);
			} else if (isset($_GET['post'])){
				$pageslug = sanitize_text_field($_GET['post']);
			}
			// Send data to JS
			$translation_array = array(
				'admin_url' => get_admin_url(),
				'security'  => wp_create_nonce( 'marketking_security_nonce' ),
			    'currency_symbol' => get_woocommerce_currency_symbol(),
			    'loaderurl' => plugins_url('../includes/assets/images/loaderpagegold5.svg', __FILE__),
			    'pageslug' => $pageslug,
			    'ajax_pages_load' => apply_filters('marketking_ajax_pages_load', 'enabled'), // disable ajax backend page load via snippets
			    'profile_pic' => plugins_url('../includes/assets/images/store-profile.png', __FILE__),
			    'sure_save_payment' => esc_html__('Are you sure you want to save this payment?', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'sure_add_ad' => esc_html__('Are you sure you want to add an ad for this product?', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'sure_remove_ad' => esc_html__('Are you sure you want to remove all ads for this product?', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'sure_save_adjustment' => esc_html__('Are you sure you want to make this manual adjustment?', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'are_you_sure_approve' => esc_html__('Are you sure you want to approve this vendor account?', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'are_you_sure_reject' => esc_html__('Are you sure you want to REJECT and DELETE this user? This is irreversible.', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'are_you_sure_deactivate' => esc_html__('Are you sure you want to DEACTIVATE this user? The user will no longer be approved and they will be unable to login.', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'url_available' => esc_html__('This URL is available!', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'url_not_available' => esc_html__('This URL is unavailable!', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'dashboardstyleurl' => plugins_url('assets/dashboard/cssjs/dashboardstyle.min.css', __FILE__),
			    'username_already_list' => esc_html__('Username already in the list!', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'add_user' => esc_html__('Add user', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'group_rules_link' => admin_url( 'edit.php?post_type=marketking_grule'),
			    'group_rules_text' => esc_html__('Set up group rules (optional)', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'go_back_text' => esc_html__('Go back', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'go_vitem' => esc_html__('Configure Verification Items', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'vitems_link' => admin_url( 'edit.php?post_type=marketking_vitem'),
			    'groups_link' => admin_url( 'admin.php?page=marketking_groups'),
			    'rejection_reason_text' => esc_html__('Enter a reason for the rejection - this will be shown to the vendor.','marketking-multivendor-marketplace-for-woocommerce'),
			    'inlineeditpostjsurl' => admin_url('js/inline-edit-post.js'),
			    'commonjsurl' => plugins_url('assets/js/common.js', __FILE__),
			    'groupspage' => admin_url( 'admin.php?page=marketking_groups'),
			    'registrationpage' => admin_url( 'admin.php?page=marketking_registration'),
			    'grulespage' => admin_url( 'admin.php?page=marketking_grule'),
			    'modulesimg' => self::marketking_display_modules_cards('pro', true),
			    'allow_dash_store_url' => apply_filters('marketking_allow_dash_store_url', 0),
			    'sending_request' => esc_html__('Processing activation request...', 'marketking-multivendor-marketplace-for-woocommerce'),
			    'datatables_folder' => plugins_url('../includes/assets/lib/dataTables/i18n/', __FILE__),
			    'tables_language_option' => apply_filters('marketking_tables_language_option_setting','English'),
			    'sure_create_shipment' => esc_html__('Are you sure you want to create this shipment?', 'marketking-multivendor-marketplace-for-woocommerce'),


			);

			if (isset($_GET['post'])){
				$translation_array['current_post_type'] = get_post_type(sanitize_text_field($_GET['post'] ));
			} else {
				$translation_array['current_post_type'] = 'notpost';
			}
			if (isset($_GET['action'])){
				$translation_array['current_action'] = sanitize_text_field($_GET['action'] );
			}

			wp_localize_script( 'marketking_global_admin_script', 'marketking', $translation_array );

			// pass dashboard data, for cases when dashboard is not loaded directly
			$data = self::marketking_get_dashboard_data();
			// Send data to JS
			$translation_array = array(
				'days_sales_b2b' => $data['days_sales_array'],
				'hours_sales_b2b' => array_values($data['hours_sales_array']),
				'currency_symbol' => get_woocommerce_currency_symbol(),
			);
			wp_localize_script( 'marketking_global_admin_script', 'marketking_dashboard', $translation_array );



			if ($hook === 'marketking_page_marketking_tools'){
				wp_enqueue_script('semantic', plugins_url('../includes/assets/lib/semantic/semantic.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
				wp_enqueue_style( 'semantic', plugins_url('../includes/assets/lib/semantic/semantic.min.css', __FILE__));
				wp_enqueue_style ( 'marketking_admin_style', plugins_url('assets/css/adminstyle.css', __FILE__), $deps = array(), $ver = MARKETKINGCORE_VERSION);
				wp_enqueue_script( 'marketking_admin_script', plugins_url('assets/js/admin.js', __FILE__), $deps = array(), $ver = MARKETKINGCORE_VERSION, $in_footer =true);
			}
		}
		
		function load_admin_resources($hook) {
			// Load only on this specific plugin admin
			if($hook != 'toplevel_page_marketking') {
				return;
			}

			// remove boostrap
			global $wp_scripts;
			foreach ($wp_scripts->queue as $index => $name){
				if ($name === 'bootstrap'){
					unset($wp_scripts->queue[$index]);
				}
			}
			
			wp_enqueue_script('jquery');

			wp_enqueue_script('semantic', plugins_url('../includes/assets/lib/semantic/semantic.min.js', __FILE__), $deps = array(), $ver = false, $in_footer =true);
			wp_enqueue_style( 'semantic', plugins_url('../includes/assets/lib/semantic/semantic.min.css', __FILE__));

			wp_enqueue_style ( 'marketking_admin_style', plugins_url('assets/css/adminstyle.css', __FILE__), $deps = array(), $ver = MARKETKINGCORE_VERSION);
			wp_enqueue_script( 'marketking_admin_script', plugins_url('assets/js/admin.js', __FILE__), $deps = array(), $ver = MARKETKINGCORE_VERSION, $in_footer =true);

			wp_enqueue_style( 'marketking_style', plugins_url('../includes/assets/css/style.css', __FILE__)); 

		}


		function marketking_plugin_dependencies() {
			if ( ! class_exists( 'woocommerce' ) ) {
				// if notice has not already been dismissed once by the current user
				if (intval(get_user_meta(get_current_user_id(),'marketking_dismiss_activate_woocommerce_notice', true)) !== 1){
		    		?>
		    	    <div class="marketking_activate_woocommerce_notice notice notice-warning is-dismissible">
		    	        <p><?php esc_html_e( 'Warning: The plugin "MarketKing" requires WooCommerce to be installed and activated.', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></p>
		    	    </div>
	    	    	<?php
	    	    }
			}
		}



}
