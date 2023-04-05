<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Marketking_New_Verification_Email extends WC_Email {

    public function __construct() {

        // set ID, this simply needs to be a unique name
        $this->id = 'marketking_new_verification_email';

        // this is the title in WooCommerce Email settings
        $this->title = esc_html__('New Verification Status', 'marketking-multivendor-marketplace-for-woocommerce');

        // this is the description in WooCommerce email settings
        $this->description = esc_html__('This email is sent when a vendor verification request is approved or rejected (marketking)', 'marketking-multivendor-marketplace-for-woocommerce');

        // these are the default heading and subject lines that can be overridden using the settings
        $this->heading = esc_html__('New Verification', 'marketking-multivendor-marketplace-for-woocommerce');
        $this->subject = esc_html__('New Verification', 'marketking-multivendor-marketplace-for-woocommerce');

        $this->template_base  = MARKETKINGCORE_DIR . 'public/emails/templates/';
        $this->template_html  = 'new-verification-email-template.php';
        $this->template_plain =  'plain-new-verification-email-template.php';
        
        // Call parent constructor to load any other defaults not explicity defined here
        parent::__construct();

        add_action( 'marketking_new_verification_notification', array( $this, 'trigger'), 10, 4 );

    }

    public function trigger($email_address, $status, $comment, $vitem) {

        $this->recipient = $email_address;
        $this->status = $status;
        $this->comment = $comment;
        $this->vitem = $vitem;

        if ( ! $this->is_enabled() || ! $this->get_recipient() ){
           return;
        }

        marketking()->switch_to_user_locale($email_address);
        
        do_action('wpml_switch_language_for_email', $email_address);
        $this->heading = esc_html__('New Verification Status', 'marketking-multivendor-marketplace-for-woocommerce');
        $this->subject = esc_html__('New Verification Status', 'marketking-multivendor-marketplace-for-woocommerce');

        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        do_action('wpml_restore_language_from_email');

        marketking()->restore_locale();
     
    }

    public function get_content_html() {
        ob_start();
        if (method_exists($this, 'get_additional_content')){
            $additional_content_checked = $this->get_additional_content();
        } else {
            $additional_content_checked = false;
        }
        wc_get_template( $this->template_html, array(
            'email_heading'      => $this->get_heading(),
            'additional_content' => $additional_content_checked,
            'status'       => $this->status,
            'comment'             => $this->comment,
            'vitem'     => $this->vitem,
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
            'status'       => $this->status,
            'comment'             => $this->comment,
            'vitem'     => $this->vitem,
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
            )
        );
    }

}
return new Marketking_New_Verification_Email();