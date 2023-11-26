<?php

class Marketking_Order_Splitter{

	function __construct() {

	}

	public function get_fees_split( $parent_order, $vendor_id){
		$fees_total = 0;

		$parent_fees = $parent_order->get_fees();
		foreach ($parent_fees as $fee){
			$value = $fee->get_total();
			$fees_total+=$value;
		}

		$order_subtotal = $parent_order->get_subtotal();

		$vendor_subtotal = 0;
		foreach ( $parent_order->get_items() as $item ) {
			// if vendor not author, skip
			$product_vendor_id = get_post_field( 'post_author', $item->get_product_id() );
			if (intval($product_vendor_id) !== intval($vendor_id)){
				continue;
			}
		    $vendor_subtotal += $item->get_subtotal();
		}

		// division by 0 error
		if (floatval($order_subtotal) === 0 || empty($order_subtotal)){
			$order_subtotal = 1;
		}

		$proportion = ($vendor_subtotal/$order_subtotal);

		$fees_total = $fees_total*$proportion;

		return $fees_total;
	}

	public function get_fees($vendor_id, $args = array()){

		$fees_total = 0;

		$location = $args['location'];

		if ($location === 'cart'){

			$fees = WC()->cart->get_fees();
			foreach ($fees as $fee){
				$value = $fee->total;
				$fees_total+=$value;
			}

			// calculate proportion of cart
			// split proportional to vendor product value compared to order value
			if (isset($args['subtotal'])){
				$subtotal = $args['subtotal'];
			} else {
				$subtotal = $this->get_subtotal($vendor_id,['location' => 'cart']);
			}

			// prevent division by 0 error
			$cart_subtotal = WC()->cart->get_subtotal();
			if (empty($cart_subtotal) or floatval($cart_subtotal) === 0){
				$cart_subtotal = 1;
			}

			$proportion = ($subtotal/$cart_subtotal);
			$fees_total = $fees_total*$proportion;
		}

		return $fees_total;
	}

	public function get_taxes($vendor_id, $args = array()){
		$taxes = false;
		$location = $args['location'];

		if ($location === 'cart'){
			if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
				$tax_labels = array();
				$tax_values = array();

				$taxable_address = WC()->customer->get_taxable_address();
				$estimated_text  = '';

				if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
					/* translators: %s location. */
					$estimated_text = ' <small>' . esc_html__( '(estimated)', 'marketking-multivendor-marketplace-for-woocommerce' ) . '</small>';
				}

				// Add up products tax
				$products_tax = 0;
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

					// if vendor is not vendor id, skip.
					$product_vendor_id = marketking()->get_product_vendor( $cart_item['product_id'] );
					if (intval($vendor_id) !== intval($product_vendor_id)){
						continue;
					}

					$products_tax += $cart_item['line_subtotal_tax'];
				}

				// Get shipping tax and split it
				$shipping_tax = WC()->cart->get_shipping_tax();
				if (defined('MARKETKINGPRO_DIR') && intval(get_option('marketking_enable_shipping_setting', 1)) === 1){
					// vendor based shipping
					$shipping_tax_vendor = 0;
				} else {
					
					// split proportional to vendor product value compared to order value
					if (isset($args['subtotal'])){
						$subtotal = $args['subtotal'];
					} else {
						$subtotal = $this->get_subtotal($vendor_id,['location' => 'cart']);
					}

					// prevent division by 0 error
					$cart_subtotal = WC()->cart->get_subtotal();
					if (empty($cart_subtotal) or floatval($cart_subtotal) === 0){
						$cart_subtotal = 1;
					}

					$proportion = ($subtotal/$cart_subtotal);
					$shipping_tax_vendor = $shipping_tax*$proportion;
					
				}

				// add shipping tax
				$products_tax +=$shipping_tax_vendor;
				// Get coupon tax (negative)
				$coupon_tax_vendor = 0;
				// apply coupons to subtotal
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

					// if vendor is not vendor id, skip.
					$product_vendor_id = marketking()->get_product_vendor( $cart_item['product_id'] );
					if (intval($vendor_id) !== intval($product_vendor_id)){
						continue;
					}

					$difference = $cart_item['line_subtotal_tax']-$cart_item['line_tax'];
					if ($difference>0){
						$coupon_tax_vendor+=$difference;
					}

				}
				// remove coupons tax
				$products_tax -= $coupon_tax_vendor;

				// Get fees taxes (negative)
				$fee_tax_vendor = 0;
				$fees = WC()->cart->get_fees();
				foreach ($fees as $fee){
					$value = $fee->tax;
					// split proportional to vendor product value compared to order value
					if (isset($args['subtotal'])){
						$subtotal = $args['subtotal'];
					} else {
						$subtotal = $this->get_subtotal($vendor_id,['location' => 'cart']);
					}

					// prevent division by 0 error
					$cart_subtotal = WC()->cart->get_subtotal();
					if (empty($cart_subtotal) or floatval($cart_subtotal) === 0){
						$cart_subtotal = 1;
					}

					$proportion = ($subtotal/$cart_subtotal);

					$fee_tax_vendor +=($value*$proportion);
				}

				
				// remove fees tax
				$products_tax += $fee_tax_vendor;


				if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
					foreach ( WC()->cart->get_tax_totals() as $code => $tax ) { 
						array_push($tax_labels, esc_html( $tax->label ) . $estimated_text);
						array_push($tax_values, $products_tax);
					}
				} else {
					array_push($tax_labels, esc_html( WC()->countries->tax_or_vat() ) . $estimated_text);
					array_push($tax_values, $products_tax);
					
				}
				$taxes = array($tax_labels, $tax_values);
				return $taxes;

			}
		}


		return $taxes;

	}

	public function get_shipping($vendor_id, $args = array()){
		$shipping = false;
		$location = $args['location'];

		// if marketking core only, then shipping should not be displayed = false
		if (defined('MARKETKINGPRO_DIR') && intval(get_option('marketking_enable_shipping_setting', 1)) === 1){
			if ($location === 'cart'){
				// get pro data
			}
		} else {
			
			// split proportional to vendor product value compared to order value
			if (isset($args['subtotal'])){
				$subtotal = $args['subtotal'];
			} else {
				$subtotal = $this->get_subtotal($vendor_id,['location' => 'cart']);
			}

			$shipping_total = WC()->cart->get_shipping_total();

			// prevent division by 0 error
			$cart_subtotal = WC()->cart->get_subtotal();
			if (empty($cart_subtotal) or floatval($cart_subtotal) === 0){
				$cart_subtotal = 1;
			}
			
			$proportion = ($subtotal/$cart_subtotal);
			$shipping = $shipping_total*$proportion;
			
		}

		return round($shipping, wc_get_price_decimals());

	}

	public function get_total($vendor_id, $args = array()){
		$location = $args['location'];

		if (isset($args['subtotal'])){
			$subtotal = $args['subtotal'];
		} else {
			$subtotal = $this->get_subtotal($vendor_id,['location' => 'cart']);
		}

		if (isset($args['fees'])){
			$fees = $args['fees'];
		} else {
			$fees = $this->get_fees($vendor_id,['location' => 'cart']);
		}

		if (isset($args['discounts'])){
			$discounts = $args['discounts'];
		} else {
			$discounts = $this->get_discounts($vendor_id,['location' => 'cart']);
		}

		if (isset($args['shipping'])){
			$shipping = $args['shipping'];
		} else {
			$shipping = $this->get_shipping($vendor_id,['location' => 'cart']);
		}

		if (isset($args['taxes'])){
			$taxes = $args['taxes'];
		} else {
			$taxes = $this->get_taxes($vendor_id,['location' => 'cart']);
		}
		
		// start with the subtotal, and add / remove from it
		$total = $subtotal;

		
		if ($location === 'cart'){
			// run calculations

			// add tax
			if ($taxes!==false){
				// calculate
				$tax = 0;
				if (isset($taxes[1][0])){
					$tax = $taxes[1][0];
				}
				$total+=$tax;
			}

			// remove discounts
			if ($discounts!==false){
				// calculate
				$total-=$discounts;
			}

			// add shipping
			if ($shipping!==false){
				// calculate
				$total+=$shipping;
			}

			// add shipping
			if ($fees!==false){
				if ($fees>0){ // else they're already added under discounts
					// calculate
					$total+=$fees;
				}
			}
		}

		return $total;

	}

	public function get_subtotal($vendor_id, $args = array()){
		$subtotal = 0;
		$location = $args['location'];
		
		if ($location === 'cart'){
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				// if vendor is not vendor id, skip.
				$product_vendor_id = marketking()->get_product_vendor( $cart_item['product_id'] );
				if (intval($vendor_id) !== intval($product_vendor_id)){
					continue;
				}

				$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

				if (get_option('woocommerce_tax_display_cart') === 'excl'){	
					$subtotal+=$cart_item['line_subtotal'];
				} else {
					$subtotal+=$cart_item['line_subtotal']+$cart_item['line_subtotal_tax'];
				}
			}
			
		}

		return $subtotal;
	}

	public function get_discounts($vendor_id, $args = array()){
		$discounts = false;
		$location = $args['location'];
		$fees = $args['fees'];
		if ($location === 'cart'){
			// apply coupons to subtotal
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				// if vendor is not vendor id, skip.
				$product_vendor_id = marketking()->get_product_vendor( $cart_item['product_id'] );
				if (intval($vendor_id) !== intval($product_vendor_id)){
					continue;
				}

				$difference = $cart_item['line_subtotal']-$cart_item['line_total'];
				if ($difference>0){
					$discounts+=$difference;
				}
			}

			// if fees are discounts, add them here
			if ($fees){
				if ($fees < 0){
					if ($discounts === false){
						$discounts = 0;
					}

					$discounts -= $fees;
				}
			}

		}
		return $discounts;
	}

	public function split_orders($parent_order, $vendors){

		foreach ( $vendors as $vendor_id ) {

			$metakeys = array(
	            'billing_country',
				'billing_first_name',
				'billing_last_name',
				'billing_company',
	            'billing_address_1',
				'billing_address_2',
				'billing_city',
				'billing_state',
				'billing_postcode',
	            'billing_email',
				'billing_phone',
				'shipping_country',
				'shipping_first_name',
				'shipping_last_name',
	            'shipping_company',
				'shipping_address_1',
				'shipping_address_2',
				'shipping_city',
	            'shipping_state',
				'shipping_postcode',
	        );

			$order = new \WC_Order();

			// save billing and shipping address
			foreach ( $metakeys as $key ) {
			    if ( is_callable( array( $order, "set_{$key}" ) ) ) {
			        $order->{"set_{$key}"}( $parent_order->{"get_{$key}"}() );
			    }
			}

			$fees = $this->get_fees_split($parent_order, $vendor_id);

			// create line items
			$this->create_line_items( $order, $parent_order, $vendor_id );

			// do shipping
			$this->create_shipping( $order, $parent_order, $vendor_id );

			// create fees
			$this->create_fees($order, $parent_order, $vendor_id, $fees);

			// save other details
			$order->set_created_via( 'marketking-multivendor-marketplace-for-woocommerce' );
			$order->set_cart_hash( $parent_order->get_cart_hash() );
			$order->set_customer_id( $parent_order->get_customer_id() );
			$order->set_currency( $parent_order->get_currency() );
			$order->set_prices_include_tax( $parent_order->get_prices_include_tax() );
			$order->set_customer_ip_address( $parent_order->get_customer_ip_address() );
			$order->set_customer_user_agent( $parent_order->get_customer_user_agent() );
			$order->set_customer_note( $parent_order->get_customer_note() );
			$order->set_payment_method( esc_html__('Payment','marketking-multivendor-marketplace-for-woocommerce').' - '.$parent_order->get_payment_method_title() );
			$order->set_payment_method_title( esc_html__('Payment','marketking-multivendor-marketplace-for-woocommerce').' - '.$parent_order->get_payment_method_title() );

			$order->set_status( $parent_order->get_status() );
			$order->set_parent_id( $parent_order->get_id() );
			
			// finally, let the order re-calculate itself and save
			$order->calculate_totals();

			$order->update_meta_data('marketking_already_processed_order', 'yes');

			$order_id = $order->save();

			// update total_sales count for sub-order
			wc_update_total_sales_counts( $order_id );

			// assign the vendor as author
			$update_args = array(
			    'ID' => $order_id,
			    'post_author' => $vendor_id,
			);
			$result = wp_update_post($update_args);

			remove_action('woocommerce_order_item_shipping_after_calculate_taxes', array($this, 'set_shipping_tax_false'), 10, 2);

			do_action('marketking_order_split_created', $order_id);

			//  check if order is paid for and entirely virtual / downloadable, and if so, automatically complete it
			/*
			if($order->is_paid()){
				$virtual = 'yes';
				foreach ($order->get_items() as $order_item){
				    $item = wc_get_product($order_item->get_product_id());
				    if (!$item->is_virtual()) {
				       $virtual = 'no';
				    }
				}
				if ($virtual === 'yes'){
					if ($parent_order->get_status() === 'completed'){
						$order->set_status('completed');
					}
				}
			}
			*/
			
		}

		// set parent order note
		// set order notes about order parent
		$note = '<p>'.esc_html__('This is a composite order that contains products sold by different vendors.','marketking-multivendor-marketplace-for-woocommerce').'</p>'.esc_html__('Here is a list of its sub-orders:','marketking-multivendor-marketplace-for-woocommerce').'<br />';

		$orders = marketking()->get_suborders_of_order($parent_order->get_id());
		foreach ($orders as $order){
			$vendor_id = marketking()->get_order_vendor($order->get_id());
			$note.='- '.esc_html__('order ','marketking-multivendor-marketplace-for-woocommerce').'<a href="'.esc_attr(get_edit_post_link($order->get_id())).'">#'.esc_html($order->get_id()).'</a>, '.esc_html__('handled by ','marketking-multivendor-marketplace-for-woocommerce').'<a href="'.esc_attr(get_edit_user_link($vendor_id)).'">'.marketking()->get_store_name_display($vendor_id).'</a><br>';
		}

		$parent_order->add_order_note( $note );


	}

	function create_fees($order, $parent_order, $vendor_id, $fees){

		if (floatval($fees) > 0){
			$item = new \WC_Order_Item_Fee();
			$item->set_props( array(
				'name'      => esc_html__('Fees','marketking-multivendor-marketplace-for-woocommerce'),
				'tax_class' => 0,
				'total'     => $fees,
				'total_tax' => 0,
				'taxes'     => array(
					'total' => 0,
				),
				'tax_status' => 'none',
				'order_id'  => $order_id,
			) );
			$item->set_tax_status('none');
			$item->save();
			$order->add_item( $item );

			$order->calculate_totals();
			$order->save();
		}
	}

	public static function set_shipping_tax_false($obj, $calculate_tax_for){
    	$obj->set_taxes(false);
    }

	function create_shipping($order, $parent_order, $vendor_id){

		if (!defined('MARKETKINGPRO_DIR') || intval(get_option('marketking_enable_shipping_setting', 1)) !== 1){
			// copy shipping from parent order, and then set its value to be proportional

			// get proportion
			$ordersubtotal = $parent_order->get_subtotal();
			if (floatval($ordersubtotal) === 0 || empty($ordersubtotal)){
				$ordersubtotal = 1;
			}
			$proportion = $order->get_subtotal()/$ordersubtotal;

	        $item = new \WC_Order_Item_Shipping();

	        $parent_shipping = $parent_order->get_shipping_methods();
	        if (!empty($parent_shipping)){
	        	$parent_shipping = reset($parent_shipping);
    	        $item->set_props(
    	            array(
    					'method_title' => $parent_shipping->get_name(),
    					'method_id'    => $parent_shipping->get_id(),
    					'total'        => $parent_shipping->get_total()*$proportion,
    					'taxes'        => $parent_shipping->get_total_tax()*$proportion,
    	            )
    	        );

    	        if (intval($parent_shipping->get_total_tax()) === 0){
    	        	add_action('woocommerce_order_item_shipping_after_calculate_taxes', array($this, 'set_shipping_tax_false'), 10, 2);
    	        }

    	        $order->add_item( $item );
    	        $order->set_shipping_total( $parent_shipping->get_total()*$proportion );
    	        $order->calculate_totals();
    	        $order->save();

	        }

		} else {

			// advanced shipping
			$item = new \WC_Order_Item_Shipping();

	        $parent_shipping = $parent_order->get_shipping_methods();
	        if (!empty($parent_shipping)){

	        	// get the respective method for this vendor ID
	        	foreach ($parent_shipping as $method){
	        		$vendormethod = $method->get_meta('vendor_id');
	        		if (intval($vendormethod) === intval($vendor_id)){
        				// found it
        				 $item->set_props(
		    	            array(
		    					'method_title' => $method->get_name(),
		    					'method_id'    => $method->get_id(),
		    					'total'        => $method->get_total(),
		    					'taxes'        => $method->get_total_tax(),
		    	            )
		    	         );

		    	        // Verified that $method->get_total() si $method->get_total_tax() have correct values
	    				 if (intval($method->get_total_tax()) === 0){
	    				 	add_action('woocommerce_order_item_shipping_after_calculate_taxes', array($this, 'set_shipping_tax_false'), 10, 2);
	    				 }

	    				 
		    	        $order->add_item( $item );
		    	        $order->set_shipping_total( $method->get_total() );
		    	        $order->calculate_totals();
		    	        $order->save();	
	        		}
	        		
	        	}

	        }


		}

	}

	function create_line_items($order, $parent_order, $vendor_id){
		foreach ( $parent_order->get_items() as $item ) {

			// if vendor not author, skip
			$product_vendor_id = get_post_field( 'post_author', $item->get_product_id() );
			if ($product_vendor_id !== $vendor_id){
				continue;
			}

		    $product_item = new \WC_Order_Item_Product();

		    $product_item->set_name( $item->get_name() );
		    $product_item->set_product_id( $item->get_product_id() );
		    $product_item->set_variation_id( $item->get_variation_id() );
		    $product_item->set_quantity( $item->get_quantity() );
		    $product_item->set_tax_class( $item->get_tax_class() );
		    $product_item->set_subtotal( $item->get_subtotal() );
		    $product_item->set_subtotal_tax( $item->get_subtotal_tax() );
		    $product_item->set_total_tax( $item->get_total_tax() );
		    $product_item->set_total( $item->get_total() );
		    $product_item->set_taxes( $item->get_taxes() );

		    $metadata = $item->get_meta_data();
		    if ( $metadata ) {
		        foreach ( $metadata as $meta ) {
		            $product_item->add_meta_data( $meta->key, $meta->value );
		        }
		    }

		    $order->add_item( $product_item );
		}

		$order->save();
	}

	public function output($requested, $args = array()){
		ob_start();
		$vendor_id = $args['vendor_id'];
		$location = $args['location'];

		if ( $requested === 'vendor_item_totals' ){
			if ($location === 'cart'){
				// get the cart
				$cart = WC()->cart;
				if (!is_object($cart)){
					// abort
					return;
				} else {
					// have cart, continue

					$subtotal = $this->get_subtotal($vendor_id,['location' => 'cart']);
					$fees = $this->get_fees($vendor_id,['location' => 'cart','subtotal' =>$subtotal]);
					$discounts = $this->get_discounts($vendor_id,['location' => 'cart','fees'=>$fees,'subtotal' => $subtotal]);
					$shipping = $this->get_shipping($vendor_id,['location' => 'cart','subtotal' => $subtotal]);
					$taxes = $this->get_taxes($vendor_id,['location' => 'cart','subtotal' => $subtotal,'discounts'=>$discounts]);

					$total = $this->get_total($vendor_id,['location' => 'cart','subtotal' => $subtotal,'discounts'=>$discounts,'shipping'=>$shipping,'taxes'=>$taxes,'fees'=>$fees]);
					// do not show both subtotal and total if they're the same
					if ($total === $subtotal){
						$total = false;
					}

					?>
					<tr class="marketking_vendor_subtotals_cart_tr">
						<td class="marketking_vendor_subtotals_cart_hidden_lowwidth">&nbsp;</td>
						<td class="marketking_vendor_subtotals_cart_hidden_lowwidth">&nbsp;</td>
						<td class="marketking_vendor_subtotals_cart_hidden_lowwidth">&nbsp;</td>
						<td class="marketking_vendor_subtotals_cart_hidden_lowwidth">&nbsp;</td>
						<td class="marketking_vendor_subtotals_cart_td_left">
							<?php 
							if ($subtotal){
								echo esc_html__('Subtotal','marketking-multivendor-marketplace-for-woocommerce').':<br>';
							}
							if ($discounts){
								echo esc_html__('Discounts','marketking-multivendor-marketplace-for-woocommerce').':<br>';
							}
							if ($fees){
								if ($fees>0){ // else they're displayed as discounts
									echo esc_html__('Fees','marketking-multivendor-marketplace-for-woocommerce').':<br>';
								}
							}
							if ($shipping){
								echo esc_html__('Shipping','marketking-multivendor-marketplace-for-woocommerce').':<br>';
							}
							if ($taxes){
								foreach ($taxes[0] as $tax_label){
									echo $tax_label.':<br>';
								}
							}

							if ($total){
								echo esc_html__('Total','marketking-multivendor-marketplace-for-woocommerce').'<small> '.esc_html__('(estimated)','marketking-multivendor-marketplace-for-woocommerce').':<br>';
							}

							?>
						</td>
						<td class="marketking_vendor_subtotals_cart_td_right">
							<?php

							if ($subtotal){
								echo wc_price($subtotal).'<br>';
							}
							if ($discounts){
								echo '-'.wc_price($discounts).'<br>';
							}
							if ($fees){
								if ($fees>0){ // else they're displayed as discounts
									echo wc_price($fees).'<br>';
								}
							}
							if ($shipping){
								echo wc_price($shipping).'<br>';
							}
							if ($taxes){
								foreach ($taxes[1] as $tax_value){
									echo wc_price($tax_value).'<br>';
								}
							}
							if ($total){
								echo wc_price($total);
							}

							?>
						</td>
					</tr>
					<?php
				}
			}

		}	

		$output = ob_get_clean();
		return $output;	

	}



}
