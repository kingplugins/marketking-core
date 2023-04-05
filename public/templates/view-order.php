<?php
/**
 * Cart Page
 *
 * This template is based on the WooCommerce Cart Template templates/myaccount/view-order.php
 * @version 3.0.0
 *
 * The purpose of this template is to improve the display for multiple vendors + composite orders
 * The template will be updated if any changes occur to the original WC cart template
 * 
 *
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();
$order_id = $order->get_id();

if (!marketking()->is_multivendor_order($order_id) && !marketking()->is_suborder($order_id)){
	?>
	<p>
	<?php
	printf(
		/* translators: 1: order number 2: order date 3: order status */
		esc_html__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'marketking-multivendor-marketplace-for-woocommerce' ),
		'<mark class="order-number">' . $order->get_order_number() . '</mark>', 
		'<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>', 
		'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>' 
	);
	?>
	</p>
	<?php
} else if (marketking()->is_suborder($order_id)){

	$order_vendor = marketking()->get_order_vendor($order_id);
	$store_name = marketking()->get_store_name_display($order_vendor);
	$store_link = marketking()->get_store_link($order_vendor);
	$parent_order_id = marketking()->get_parent_order($order_id);
	$parent_order = wc_get_order($parent_order_id);
	?>
	<p>
	<?php
	printf(
		/* translators: 1: order number 2: order date 3: order status */
		esc_html__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'marketking-multivendor-marketplace-for-woocommerce' ),
		'<mark class="order-number">' . $order->get_order_number() . '</mark>', 
		'<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>', 
		'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>' 
	);
	echo '<br>'.esc_html__('This is a sub-order of order','marketking-multivendor-marketplace-for-woocommerce').' ';
	?><a href="<?php echo esc_attr($parent_order->get_view_order_url());?>"><?php echo '#'.esc_html($parent_order_id);?></a>.
	<?php echo esc_html__('Products are sold by ','marketking-multivendor-marketplace-for-woocommerce').'<a href="'.esc_attr($store_link).'">'.esc_html($store_name).'</a>.'; ?>
	
	</p>
	<?php
}
?>


<?php if ( $notes ) : ?>
	<h2><?php esc_html_e( 'Order updates', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></h2>
	<ol class="woocommerce-OrderUpdates commentlist notes">
		<?php foreach ( $notes as $note ) : ?>
		<li class="woocommerce-OrderUpdate comment note">
			<div class="woocommerce-OrderUpdate-inner comment_container">
				<div class="woocommerce-OrderUpdate-text comment-text">
					<p class="woocommerce-OrderUpdate-meta meta"><?php echo date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'marketking-multivendor-marketplace-for-woocommerce' ), strtotime( $note->comment_date ) );  ?></p>
					<div class="woocommerce-OrderUpdate-description description">
						<?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) );  ?>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
		</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>
