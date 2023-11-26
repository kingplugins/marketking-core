<?php
	
defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

// get refund link
$refund = esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'refunds/?conversation='.$refund;
?>

<p>
	<?php esc_html_e( 'You have received a new refund request.', 'marketking-multivendor-marketplace-for-woocommerce');	?>
	<br /><br />
	<?php esc_html_e( 'Go to request: ','marketking-multivendor-marketplace-for-woocommerce'); echo '<a href="'.esc_attr($refund).'">'.esc_html__('View Request','marketking-multivendor-marketplace-for-woocommerce').'</a>'; ?>
	<br /><br />
 	<?php esc_html_e( 'Reason: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($reason); ?>
	<br /><br />
 	<?php esc_html_e( 'User: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($user); ?>

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
