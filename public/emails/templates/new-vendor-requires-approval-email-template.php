<?php
	
defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
$user = get_user_by('login', $user_login);
$user_email = $user->user_email;

 ?>

<p>
	<?php esc_html_e( 'You have a new vendor registration that requires approval.', 'marketking-multivendor-marketplace-for-woocommerce');	?>
	<br /><br />
 	<?php esc_html_e( 'Username: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($user_login); ?>
 	<br />
 	<?php esc_html_e( 'Email: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($user_email); ?>
 	<br /><br />
 	<a href="<?php echo esc_attr(admin_url('/user-edit.php?user_id='.$user->ID)); ?> "><?php esc_html_e( 'Click to Review User', 'marketking-multivendor-marketplace-for-woocommerce' ); ?> </a>
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
