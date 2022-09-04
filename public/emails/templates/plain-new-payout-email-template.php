<?php

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

esc_html_e( 'Your payout has been processed! Happy Spending!', 'marketking-multivendor-marketplace-for-woocommerce');	
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
esc_html_e( 'Amount: ','marketking-multivendor-marketplace-for-woocommerce'); echo wc_price($amount);
echo "\n\n----------------------------------------\n\n";
esc_html_e( 'Method: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($method);
echo "\n\n----------------------------------------\n\n";
esc_html_e( 'Notes: ','marketking-multivendor-marketplace-for-woocommerce'); echo $note;
echo "\n\n----------------------------------------\n\n";
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
