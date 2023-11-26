<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */


defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'left';

$order_id = $order->get_id();
$suborders = marketking()->get_suborders_of_order($order_id);

?>
<p style="margin: 0 0 16px;"><?php echo apply_filters('marketking_order_split_notice',esc_html__('Since your order contains products sold by different vendors, it has been split into multiple sub-orders. Each sub-order will be handled by their respective vendor independently.','marketking-multivendor-marketplace-for-woocommerce'));?></p>
<?php

foreach ($suborders as $suborder){
	output_suborder_table($suborder, $sent_to_admin, $plain_text, $email, $text_align);
}


do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<h2>
	<?php
	if ( $sent_to_admin ) {
		$before = '<a class="link" href="' . esc_url( $order->get_edit_order_url() ) . '">';
		$after  = '</a>';
	} else {
		$before = '';
		$after  = '';
	}
	/* translators: %s: Order ID. */
	echo wp_kses_post( $before . sprintf( esc_html__( '[Order Totals #%s]', 'marketking-multivendor-marketplace-for-woocommerce' ) . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ) );
	?>
</h2>
<h2><?php esc_html_e('Order Totals', 'marketking-multivendor-marketplace-for-woocommerce'); ?></h2>
<div style="margin-bottom: 40px;">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<tfoot>
			<?php
			$item_totals = $order->get_order_item_totals();

			if ( $item_totals ) {
				$i = 0;
				foreach ( $item_totals as $total ) {
					$i++;
					?>
					<tr>
						<th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
						<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
					</tr>
					<?php
				}
			}
			if ( $order->get_customer_note() ) {
				?>
				<tr>
					<th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
					<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
				<?php
			}
			?>
		</tfoot>
	</table>
</div>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php
function output_suborder_table($order, $sent_to_admin, $plain_text, $email, $text_align){
	$order_id = $order->get_id();
	$vendor_id = marketking()->get_order_vendor($order_id);
	$store_name = marketking()->get_store_name_display($vendor_id);

	?>
	<h2><?php echo esc_html__('Products sold by', 'marketking-multivendor-marketplace-for-woocommerce').' '.esc_html($store_name); ?></h2>
	<div style="margin-bottom: 40px;">
		<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
			<thead>
				<tr>
					<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
					<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Quantity', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
					<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Price', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				echo wc_get_email_order_items( 
					$order,
					array(
						'show_sku'      => $sent_to_admin,
						'show_image'    => false,
						'image_size'    => array( 32, 32 ),
						'plain_text'    => $plain_text,
						'sent_to_admin' => $sent_to_admin,
					)
				);
				?>
			</tbody>
			<tfoot>
				<?php
				$item_totals = $order->get_order_item_totals();

				if ( $item_totals ) {
					$i = 0;
					foreach ( $item_totals as $total ) {
						$i++;
						?>
						<tr>
							<th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
							<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
						</tr>
						<?php
					}
				}
				if ( $order->get_customer_note() ) {
					?>
					<tr>
						<th class="td" scope="row" colspan="2" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></th>
						<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
					</tr>
					<?php
				}
				?>
			</tfoot>
		</table>
	</div>

	<?php
}

?>
