<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Marketking_New_Vendor_Requires_Approval_Email extends WC_Email {

    public function __construct() {

        // set ID, this simply needs to be a unique name
        $this->id = 'marketking_new_vendor_requires_approval_email';

        // this is the title in WooCommerce Email settings
        $this->title = esc_html__('New vendor requires approval', 'marketking-multivendor-marketplace-for-woocommerce');

        // this is the description in WooCommerce email settings
        $this->description = esc_html__('This email is sent to admin when a new vendor registers and requires manual approval', 'marketking-multivendor-marketplace-for-woocommerce');

        // these are the default heading and subject lines that can be overridden using the settings
        $this->heading = esc_html__('New vendor requires approval', 'marketking-multivendor-marketplace-for-woocommerce');
        $this->subject = esc_html__('New vendor requires approval', 'marketking-multivendor-marketplace-for-woocommerce');

        $this->template_base  = MARKETKINGCORE_DIR . 'public/emails/templates/';
        $this->template_html  = 'new-vendor-requires-approval-email-template.php';
        $this->template_plain =  'plain-new-vendor-requires-approval-email-template.php';
        
        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct();

        // this sets the recipient to the settings defined below in init_form_fields()
        $this->recipient = $this->get_option( 'recipient' );

        // if none was entered, just use the WP admin email as a fallback
        if ( ! $this->recipient ){
            $this->recipient = get_option( 'admin_email' );
        }

        add_action( 'woocommerce_created_customer_notification', array( $this, 'trigger'), 10, 3 );
        add_action( 'marketking_new_user_requires_approval_notification', array( $this, 'trigger'), 10, 3 );

    }

    public function trigger($customer_id, $data, $password) {
        if ( ! $this->is_enabled() || ! $this->get_recipient() ){
           return;
        }
        
        $this->object = new WP_User( $customer_id );
        $this->user_login         = stripslashes( $this->object->user_login );
		$this->user_email         = stripslashes( $this->object->user_email );

        $this->subject = apply_filters('marketking_requires_approval_email_heading', $this->subject, $this->user_login);
        // check if customer requires manual approval
        $account_approved = get_user_meta($customer_id, 'marketking_account_approved', true);
        if ($account_approved === 'no'){
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }
     
    }

    public function get_content_html() {
        ob_start();
        if (method_exists($this, 'get_additional_content')){
            $additional_content_checked = $this->get_additional_content();
        } else {
            $additional_content_checked = false;
        }
        wc_get_template( $this->template_html, array(
            'email_heading'      => apply_filters('marketking_requires_approval_email_heading', $this->get_heading(), $this->user_login),
            'additional_content' => $additional_content_checked,
            'user_login'         => $this->user_login,
            'email'              => $this,
        ), $this->template_base, $this->template_base  );
        return ob_get_clean();
    }


    public function get_content_plain() {
        ob_start();
        if (method_exists($this, 'get_additional_content')){
            $additional_content_checked = $this->get_additional_content();
        } else {
            $additional_content_checked = false;
        }
        wc_get_template( $this->template_plain, array(
            'email_heading'      => $this->get_heading(),
			'additional_content' => $additional_content_checked,
			'user_login'         => $this->user_login,
			'email'              => $this,
        ), $this->template_base, $this->template_base );
        return ob_get_clean();
    }

    public function init_form_fields() {

        $this->form_fields = array(
            'enabled'    => array(
                'title'   => esc_html__( 'Enable/Disable', 'marketking-multivendor-marketplace-for-woocommerce' ),
                'type'    => 'checkbox',
                'label'   => esc_html__( 'Enable this email notification', 'marketking-multivendor-marketplace-for-woocommerce' ),
                'default' => 'yes',
            ),
            'recipient'  => array(
                'title'       => esc_html__('Recipient(s)','marketking-multivendor-marketplace-for-woocommerce'),
                'type'        => 'text',
                'description' => esc_html__('Enter recipients (comma separated) for this email. Defaults to','marketking-multivendor-marketplace-for-woocommerce').sprintf( '<code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
                'placeholder' => '',
                'default'     => ''
            ),
            'subject'    => array(
                'title'       => 'Subject',
                'type'        => 'text',
                'description' => esc_html__('This controls the email subject line. Leave blank to use the default subject: ','marketking-multivendor-marketplace-for-woocommerce').sprintf( '<code>%s</code>.', $this->subject ),
                'placeholder' => '',
                'default'     => ''
            ),
            'heading'    => array(
                'title'       => esc_html__('Email Heading','marketking-multivendor-marketplace-for-woocommerce'),
                'type'        => 'text',
                'description' => esc_html__('This controls the main heading contained within the email notification. Leave blank to use the default heading: ','marketking-multivendor-marketplace-for-woocommerce').sprintf( '<code>%s</code>.', $this->heading ),
                'placeholder' => '',
                'default'     => ''
            ),
            'email_type' => array(
                'title'       => esc_html__('Email type','marketking-multivendor-marketplace-for-woocommerce'),
                'type'        => 'select',
                'description' => esc_html__('Choose which format of email to send.','marketking-multivendor-marketplace-for-woocommerce'),
                'default'     => 'html',
                'class'       => 'email_type',
                'options'     => array(
                    'plain'     => 'Plain text',
                    'html'      => 'HTML', 'woocommerce',
                    'multipart' => 'Multipart', 'woocommerce',
                )
            ),
            'additional_content' => array(
                'title'       => esc_html__( 'Additional content', 'woocommerce' ),
                'description' => esc_html__( 'Text to appear below the main email content.', 'woocommerce' ),
                'css'         => 'width:400px; height: 75px;',
                'placeholder' => esc_html__( 'N/A', 'woocommerce' ),
                'type'        => 'textarea',
                'default'     => '',
                'desc_tip'    => true,
            ),
        );
    }

}
return new Marketking_New_Vendor_Requires_Approval_Email();