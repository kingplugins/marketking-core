<?php
	
defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

?>
<p>
	<?php esc_html_e( 'You\'re all set and ready to start selling! Your account has been approved.', 'marketking-multivendor-marketplace-for-woocommerce');	?>
	<br /><br />
	<?php esc_html_e( 'You can access your vendor dashboard to manage products, orders, earnings and more at: ', 'marketking-multivendor-marketplace-for-woocommerce'); 
		echo make_clickable( esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))) ) ;	?>
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
