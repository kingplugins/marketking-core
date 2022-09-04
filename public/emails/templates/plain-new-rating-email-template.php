<?php

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

esc_html_e( 'Your have received a new rating!', 'marketking-multivendor-marketplace-for-woocommerce');	
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
esc_html_e( 'Rating: ','marketking-multivendor-marketplace-for-woocommerce'); echo wc_price($rating);
echo "\n\n----------------------------------------\n\n";
esc_html_e( 'Comment: ','marketking-multivendor-marketplace-for-woocommerce'); echo $comment;
echo "\n\n----------------------------------------\n\n";
esc_html_e( 'Product: ','marketking-multivendor-marketplace-for-woocommerce'); echo $product;
echo "\n\n----------------------------------------\n\n";
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
