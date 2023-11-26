<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php

$vendor_id = marketking()->get_order_vendor($order_id);

$have_vat = apply_filters('marketking_commission_invoice_have_vat', false, $vendor_id);
$vat_rate = apply_filters('marketking_commission_invoice_vat_rate', 16, $vendor_id);
$earnings = marketking()->get_order_earnings($order_id);

do_action( 'wpo_wcpdf_before_document', 'invoice', $document->order ); ?>

<table class="head container">
	<tr>
		<td class="header">
		<?php
		if ( $document->has_header_logo() ) {
			do_action( 'wpo_wcpdf_before_shop_logo', 'invoice', $document->order );
			$document->header_logo();
			do_action( 'wpo_wcpdf_after_shop_logo', 'invoice', $document->order );
		} else {
			$document->title();
		}
		?>
		</td>
		<td class="shop-info">
			<?php do_action( 'wpo_wcpdf_before_shop_name', 'invoice', $document->order ); ?>
			<div class="shop-name"><h3><?php $document->shop_name(); ?></h3></div>
			<?php do_action( 'wpo_wcpdf_after_shop_name', 'invoice', $document->order ); ?>
			<?php do_action( 'wpo_wcpdf_before_shop_address', 'invoice', $document->order ); ?>
			<div class="shop-address"><?php $document->shop_address(); ?></div>
			<?php do_action( 'wpo_wcpdf_after_shop_address', 'invoice', $document->order ); ?>
		</td>
	</tr>
</table>

<?php 

do_action( 'wpo_wcpdf_before_document_label', 'invoice', $document->order ); ?>

<h1 class="document-type-label">
	<?php if ( $document->has_header_logo() ) $document->title(); ?>
</h1>

<?php do_action( 'wpo_wcpdf_after_document_label', 'invoice', $document->order ); ?>

<table class="order-data-addresses">
	<tr>
		<td class="address billing-address">
			<?php
			$shop_name = WPO_WCPDF()->settings->general_settings['shop_name']['default'];
			$store_address = WPO_WCPDF()->settings->general_settings['shop_address']['default'];
		
			// Country and state separated:
			echo apply_filters('marketking_commission_invoice_admin_address', $shop_name.'<br>'.$store_address);
			?>
		</td>

		<td class="order-data">
			<table>
				<?php do_action( 'wpo_wcpdf_before_order_data', 'invoice', $document->order ); ?>
				<?php if ( isset( $document->settings['display_number'] ) ) : ?>
					<tr class="invoice-number">
						<th><?php echo $document->get_number_title(); ?></th>
						<td><?php $document->invoice_number(); ?></td>
					</tr>
				<?php endif; ?>
				<?php if ( isset( $document->settings['display_date'] ) ) : ?>
					<tr class="invoice-date">
						<th><?php echo $document->get_date_title(); ?></th>
						<td><?php $document->invoice_date(); ?></td>
					</tr>
				<?php endif; ?>
				<tr class="order-number">
					<th><?php _e( 'Order Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $document->order_number(); ?></td>
				</tr>
				<tr class="order-date">
					<th><?php _e( 'Date:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
					<td><?php $document->order_date(); ?></td>
				</tr>
				<?php do_action( 'wpo_wcpdf_after_order_data', 'invoice', $document->order ); ?>
			</table>			
		</td>
	</tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', 'invoice', $document->order ); ?>

<table class="order-details">
	<thead>
		<tr>
			<th class="product"><?php _e( 'Product', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="quantity"><?php _e( 'Quantity', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="price"><?php _e( 'Price', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$i = 1;
			foreach ( $document->get_order_items() as $item_id => $item ) { 
				if ($i === 1) {
					?>
					<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', 'item-'.$item_id, esc_attr( 'invoice' ), $document->order, $item_id ); ?>">
						<td class="product">
							<?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
							<span class="item-name"><?php echo esc_html__('Commission - Order','marketking-multivendor-marketplace-for-woocommerce').' #'.$order_id; ?></span>
						</td>
						<td class="quantity">1</td>
						<td class="price"><?php 

						if (!$have_vat){
							$subtotal = wc_price($earnings);
						} else {
							// remove VAT from earnings
							$percentage = 100+$vat_rate; // e.g. 115
							$earnings_without_vat = $earnings*100/$percentage;
							$vat = $earnings-$earnings_without_vat;
							$subtotal = wc_price($earnings_without_vat);
						}

						// COD REVERSE, then the invoice should be what the vendor pays the admin
						$order = wc_get_order($order_id);
						$method = $order->get_payment_method();

						if(get_option( 'marketking_cod_behaviour_setting', 'default' ) === 'reverse' && $method === 'cod' && apply_filters('marketking_apply_cod_reverse', true, $order)){
							$subtotal = wc_price($order->get_total()-marketking()->get_order_earnings($order_id));
						}

						echo $subtotal;

					?></td>
			</tr>
			<?php } 
			$i++;
		}
		?>
	</tbody>
	<tfoot>
		<tr class="no-borders">
			<td class="no-borders">
				<div class="document-notes">
					<?php do_action( 'wpo_wcpdf_before_document_notes', 'invoice', $document->order ); ?>
					<?php if ( $document->get_document_notes() ) : ?>
						<h3><?php _e( 'Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						<?php $document->document_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_document_notes', 'invoice', $document->order ); ?>
				</div>
				<div class="customer-notes">
					<?php do_action( 'wpo_wcpdf_before_customer_notes', 'invoice', $document->order ); ?>
					<?php if ( $document->get_shipping_notes() ) : ?>
						<h3><?php _e( 'Customer Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						<?php $document->shipping_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_customer_notes', 'invoice', $document->order ); ?>
				</div>				
			</td>
			<td class="no-borders" colspan="2">
				<table class="totals">
					<tfoot>
						<?php
						// subtotal, tva, total
						?>
						<tr class="subtotal">
							<th class="description"><?php echo esc_html__('Subtotal','marketking-multivendor-marketplace-for-woocommerce') ?></th>
							<td class="price"><span class="totals-price"><?php echo $subtotal; ?></span></td>
						</tr>

						<?php
						if ($have_vat){
							?>
							<tr class="vat">
								<th class="description"><?php echo apply_filters('marketking_commission_invoice_tax_name', esc_html__('VAT','marketking-multivendor-marketplace-for-woocommerce'), $vendor_id); ?></th>
								<td class="price"><span class="totals-price"><?php echo wc_price($vat); ?></span></td>
							</tr>
							<tr class="total">
								<th class="description"><?php echo esc_html__('Total','marketking-multivendor-marketplace-for-woocommerce') ?></th>
								<td class="price"><span class="totals-price"><?php echo wc_price($earnings); ?></span></td>
							</tr>
							<?php
							// show total as well
						}
						?>

					</tfoot>
				</table>
			</td>
		</tr>
	</tfoot>
</table>

<div class="bottom-spacer"></div>

<?php do_action( 'wpo_wcpdf_after_order_details', 'invoice', $document->order ); ?>

<?php if ( $document->get_footer() ) : ?>
	<div id="footer">
		<!-- hook available: wpo_wcpdf_before_footer -->
		<?php $document->footer(); ?>
		<!-- hook available: wpo_wcpdf_after_footer -->
	</div><!-- #letter-footer -->
<?php endif; ?>

<?php do_action( 'wpo_wcpdf_after_document', 'invoice', $document->order ); ?>

