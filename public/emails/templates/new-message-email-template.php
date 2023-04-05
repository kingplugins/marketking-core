<?php
	
defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );
$user_info = get_userdata($userid);

if (marketking()->is_vendor($userid)){
	$name = marketking()->get_store_name_display($userid);
} else {
	$name = $user_info->first_name.' '.$user_info->last_name.' ('.$user_info->user_login.')';
}


if ($messageid !== 'inquiry' && $messageid !== 'support' && $messageid !== 'withdrawal'){
	?>
	<p>
		<?php esc_html_e( 'You have a new message.', 'marketking-multivendor-marketplace-for-woocommerce');	?>
		<br /><br />
		<?php esc_html_e( 'Sender: ','marketking-multivendor-marketplace-for-woocommerce'); echo esc_html($name); ?>
		<br /><br />
	 	<?php esc_html_e( 'Message: ','marketking-multivendor-marketplace-for-woocommerce'); echo apply_filters('the_content',$message); ?>
	</p>
	<?php
} else if ($messageid === 'inquiry'){
	?>
	<p>
		<?php 

		if (is_user_logged_in()){
			esc_html_e( 'You have a new inquiry.', 'marketking-multivendor-marketplace-for-woocommerce' ); 
		} else {
			esc_html_e( 'You have a new inquiry from a guest (logged out) user.', 'marketking-multivendor-marketplace-for-woocommerce' ); 
		}

		?>
		<br /><br />
		<?php
		echo nl2br(wp_kses(
			$message,
			array(
				'br' => true,
				'b'  => true,
			)
		));
		?>
	</p>
	<?php
} else if ($messageid === 'support'){
	?>
	<p>
		<?php 
		esc_html_e( 'You have a new support request.', 'marketking-multivendor-marketplace-for-woocommerce' ); 

		?>
		<br /><br />
		<?php
		echo nl2br(wp_kses(
			$message,
			array(
				'br' => true,
				'b'  => true,
			)
		));
		?>
	</p>
	<?php
} else if ($messageid === 'withdrawal'){
	?>
	<p>
		<?php 
		esc_html_e( 'You have a new withdrawal request.', 'marketking-multivendor-marketplace-for-woocommerce' ); 

		?>
		<br /><br />
		<?php
		echo nl2br(wp_kses(
			$message,
			array(
				'br' => true,
				'b'  => true,
			)
		));
		?>
	</p>
	<?php
}


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
