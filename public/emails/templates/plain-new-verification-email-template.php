<?php

defined( 'ABSPATH' ) || exit;
$item_name = get_the_title($vitem);
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

esc_html_e( 'There is an update regarding your recent verification request.', 'marketking-multivendor-marketplace-for-woocommerce');	
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
esc_html_e( 'Verification for: ','marketking-multivendor-marketplace-for-woocommerce'); echo wc_price($item_name);
echo "\n\n----------------------------------------\n\n";
esc_html_e( 'New Status: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html(ucfirst($status));
echo "\n\n----------------------------------------\n\n";
if (!empty($comment)){
esc_html_e( 'Comment: ','marketking-multivendor-marketplace-for-woocommerce'); echo $comment;
echo "\n\n----------------------------------------\n\n";
}
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
