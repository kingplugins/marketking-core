<?php

/**
 * Order Details
 *
 * This template is based on the WooCommerce Cart Template templates/order/order-details.php
 * @version 4.6.0
 *
 * The purpose of this template is to separate products by vendor, which was not possible with WC hooks.
 * The template will be updated if any changes occur to the original WC template
 * 
 *
 */


defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); 

if ( ! $order ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<section class="woocommerce-order-details">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></h2>
	<?php
	wc_print_notice( apply_filters('marketking_order_split_notice',esc_html__('Since your order contains products sold by different vendors, it has been split into multiple sub-orders. Each sub-order will be handled by their respective vendor independently.','marketking-multivendor-marketplace-for-woocommerce')), 'notice' );
	?>

	<?php
	$suborders = array_reverse(marketking()->get_suborders_of_order($order_id));
	foreach ($suborders as $suborder){
		order_details_vendor_table($suborder, $order, $order_items, $show_purchase_note, $show_customer_details, $downloads, $show_downloads);
	}
	?>

	<h4><?php esc_html_e('Order Totals','marketking-multivendor-marketplace-for-woocommerce');?></h4>
	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] );  ?></td>
					</tr>
					<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>
	</table>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<?php
function order_details_vendor_table($suborder, $order, $order_items, $show_purchase_note, $show_customer_details, $downloads, $show_downloads){
	// get vendor id
	$suborder_id = $suborder->get_id();
	$vendor_id = marketking()->get_order_vendor($suborder_id);
	$store_name = marketking()->get_store_name_display($vendor_id);
	?>
	<div class="marketking_order_details_vendor_table_header">
		<div class="marketking_order_details_vendor_table_title">
			<?php echo '<h4>'.esc_html__('Products sold by ','marketking-multivendor-marketplace-for-woocommerce').$store_name.'</h4>'; ?>
		</div>
		<div class="marketking_order_details_vendor_table_view_order">
			<a href="<?php echo esc_attr($suborder->get_view_order_url());?>"><button class="woocommerce-button button view"><?php echo esc_html__( 'View Order', 'marketking-multivendor-marketplace-for-woocommerce' ).' #'.esc_html($suborder_id); ?></button></a>
		</div>
	</div>


	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );
			$vendor_subtotal = 0;

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				// if vendor is not vendor id, skip.
				$product_vendor_id = marketking()->get_product_vendor( $product->get_id() );
				if ($vendor_id !== $product_vendor_id){
					continue;
				}

				// add to vendor subtotal
				if (!isset($tax_display)){
					$tax_display = get_option( 'woocommerce_tax_display_cart' );
				}

				if ( 'excl' === $tax_display ) {
				  $vendor_subtotal+=$order->get_line_subtotal( $item );
				} else {
				  $vendor_subtotal+=$order->get_line_subtotal( $item, true );
				}


				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>

		<tfoot>
			
			<?php
			foreach ( $suborder->get_order_item_totals() as $key => $total ) {
				?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] );  ?></td>
					</tr>
					<?php
			}
			?>
			<?php if ( $suborder->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $suborder->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
				
		</tfoot>
	</table><br>
	<?php
}


/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
