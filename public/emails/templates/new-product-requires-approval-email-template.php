<?php
	
defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
$product = wc_get_product($product_id);
$vendor_id = marketking()->get_product_vendor($product_id);
$vendor_name = marketking()->get_store_name_display($vendor_id);
$product_name = $product->get_title();
?>

<p>
	<?php esc_html_e( 'A vendor has submitted the following product for approval.', 'marketking-multivendor-marketplace-for-woocommerce');	?>
	<br /><br />
 	<?php esc_html_e( 'Vendor: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($vendor_name); ?>
 	<br />
 	<?php esc_html_e( 'Product: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($product_name); ?>
 	<br /><br />
 	<a href="<?php echo esc_attr(admin_url('/post.php?post='.$product_id.'&action=edit')); ?>"><?php esc_html_e( 'Click to Review Product', 'marketking-multivendor-marketplace-for-woocommerce' ); ?> </a>
</p>
<?php

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
