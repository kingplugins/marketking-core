<?php
/**
 * Cart Page
 *
 * This template is based on the WooCommerce Cart Template templates/cart/cart.php
 * @version 3.8.0
 *
 * The purpose of this template is to separate products by vendor, which was not possible with WC hooks.
 * The template will be updated if any changes occur to the original WC cart template
 * 
 *
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form marketking_split_cart_form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>
	
	<?php
	// INSERTION START 
	// get an array of vendor ids in cart, so we can then list products by vendor
	$vendors_in_cart = marketking()->get_vendors_in_cart();
	foreach ($vendors_in_cart as $vendor_id){
		vendor_table($vendor_id);
	}

	// INSERTION END
	?>

	<?php do_action( 'woocommerce_cart_contents' ); ?>
	<table class="shop_table shop_table_responsive cart">
		<tr>
			<td colspan="6" class="actions">

				<?php if ( wc_coupons_enabled() ) { ?>
					<div class="coupon">
						<label for="coupon_code"><?php esc_html_e( 'Coupon:', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></button>
						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php } ?>

				<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></button>

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
			</td>
		</tr>
	</table>
	<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

<div class="cart-collaterals">
	<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );
	?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>

<?php
// define function to output a products table by vendor
function vendor_table($vendor_id){

	// INSERTION START
	// show vendor name
	$store_name = marketking()->get_store_name_display($vendor_id);
	echo '<h3 class="marketking_sold_by_title">'.esc_html__('Products sold by ','marketking-multivendor-marketplace-for-woocommerce').$store_name.'</h3>';

	// INSERTION END
	?>
	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents marketking_vendor_cart_container" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name"><?php esc_html_e( 'Product', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Price', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
				<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

				// INSERTION START
				// if vendor is not vendor id, skip.
				$product_vendor_id = marketking()->get_product_vendor( $cart_item['product_id'] );
				if ($vendor_id !== $product_vendor_id){
					continue;
				}
				// INSERTION END 

				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-remove">
							<?php
								echo apply_filters( 
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove this item', 'marketking-multivendor-marketplace-for-woocommerce' ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
							?>
						</td>

						<td class="product-thumbnail">
						<?php
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo $thumbnail; 
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); 
						}
						?>
						</td>

						<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
						} else {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); 

						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'marketking-multivendor-marketplace-for-woocommerce' ) . '</p>', $product_id ) );
						}
						?>
						</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); 
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
						} else {
							$product_quantity = woocommerce_quantity_input(
								array(
									'input_name'   => "cart[{$cart_item_key}][qty]",
									'input_value'  => $cart_item['quantity'],
									'max_value'    => $_product->get_max_purchase_quantity(),
									'min_value'    => '0',
									'product_name' => $_product->get_name(),
								),
								$_product,
								false
							);
						}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); 
						?>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'marketking-multivendor-marketplace-for-woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); 
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>

			<?php

			$splitter = new Marketking_Order_Splitter;
			echo $splitter->output('vendor_item_totals', ['vendor_id' => $vendor_id, 'location' => 'cart']);
			?>

			
		</tbody>
	</table>
	<?php
	
	
}

?>
