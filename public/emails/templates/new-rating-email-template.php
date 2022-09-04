<?php
	
defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
?>

<p>
	<?php esc_html_e( 'You have received a new rating!', 'marketking-multivendor-marketplace-for-woocommerce');	?>
	<br /><br />
	<?php esc_html_e( 'Rating: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($rating); ?>
	<br /><br />
 	<?php esc_html_e( 'Comment: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($comment); ?>
	<br /><br />
 	<?php esc_html_e( 'Product: ','marketking-multivendor-marketplace-for-woocommerce'); 

 	echo wp_kses( $product, array( 'a' => array( 'href' => array() ) ) ) ; ?>

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
