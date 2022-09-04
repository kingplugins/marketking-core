<?php
	
defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

$item_name = get_the_title($vitem);
?>

<p>
	<?php esc_html_e( 'There is an update regarding your recent verification request.', 'marketking-multivendor-marketplace-for-woocommerce');	?>
	<br /><br />
 	<?php esc_html_e( 'Verification for: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($item_name); ?>
	<br /><br />
	<?php esc_html_e( 'New Status: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html(ucfirst($status)); ?>
	<?php
		if (!empty($comment)){
			?>
			<br /><br />
		 	<?php esc_html_e( 'Comment: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($comment); ?>
			<?php
		}
	?>
	
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
