<?php

defined( 'ABSPATH' ) || exit;

$product = wc_get_product($product_id);
$vendor_id = marketking()->get_product_vendor($product_id);
$vendor_name = marketking()->get_store_name_display($vendor_id);
$product_name = $product->get_title();

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

esc_html_e( 'You have a new vendor registration that requires approval.', 'marketking-multivendor-marketplace-for-woocommerce');	
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
esc_html_e( 'Vendor: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($vendor_name); 
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
esc_html_e( 'Product: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($product_name);

echo "\n\n----------------------------------------\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
