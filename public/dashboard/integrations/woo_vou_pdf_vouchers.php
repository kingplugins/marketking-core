<?php

class Marketking_Woo_Vou{

    function __construct() {
        include_once ( WOO_VOU_META_DIR . '/woo-vou-meta-box-functions.php' );
        include_once( WOO_VOU_DIR . '/includes/class-woo-vou-scripts.php' );
        include_once ( WOO_VOU_DIR . '/includes/admin/woo-vou-admin-functions.php' );
    }

    // Woo_Vou PDF Vouchers
    function woo_vou_scripts(){
        //woo_vou_metabox_scripts()
        global $wp_version, $woocommerce, $post;
        global  $wp_scripts;
        $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';

        // Enqueu built-in script for color picker.
        wp_enqueue_script(
                'iris',
                admin_url( 'js/iris.min.js' ),
                array( 
                    'jquery-ui-draggable', 
                    'jquery-ui-slider', 
                    'jquery-touch-punch'
                ),
                false,
                1
            );

            // Now we can enqueue the color-picker script itself, 
            //    naming iris.js as its dependency
            wp_enqueue_script(
                'wp-color-picker',
                admin_url( 'js/color-picker.min.js' ),
                array( 'iris' ),
                false,
                1
            );
        wp_enqueue_script( 'farbtastic' );

        // Enqueue Meta Box Scripts
        wp_enqueue_script( 'woo-vou-meta-box', WOO_VOU_META_URL . '/js/meta-box.js', array( 'jquery' ), WOO_VOU_PLUGIN_VERSION, true );

        //localize script
        $newui = $wp_version >= '3.5' ? '1' : '0'; //check wp version for showing media uploader
        wp_localize_script( 'woo-vou-meta-box','WooVou',array(      
        'new_media_ui'      =>  $newui,
        'one_file_min'      =>  esc_html__('You must have at least one file.','woovoucher' ),
        ));

        // Enqueue for  image or file uploader
        wp_enqueue_script( 'media-upload' );
        add_thickbox();
        wp_enqueue_script( 'jquery-ui-sortable' );

        // Enqueue for datepicker
        wp_enqueue_script(array('jquery','jquery-ui-core','jquery-ui-datepicker','jquery-ui-slider'));

        wp_deregister_script( 'datepicker-slider' );
        wp_register_script('datepicker-slider', WOO_VOU_META_URL.'/js/datetimepicker/jquery-ui-slider-Access.js', array('jquery'), WOO_VOU_PLUGIN_VERSION );
        wp_enqueue_script('datepicker-slider');

        wp_deregister_script( 'timepicker-addon' );
        wp_register_script('timepicker-addon', WOO_VOU_META_URL.'/js/datetimepicker/jquery-date-timepicker-addon.js', array('datepicker-slider'), WOO_VOU_PLUGIN_VERSION, true);
        wp_enqueue_script('timepicker-addon');


        $prod = sanitize_text_field(marketking()->get_pagenr_query_var());
        if ($prod === 'add'){
          global $marketking_product_add_id;
          $prod = $marketking_product_add_id;
        }

   
        //woo_vou_popup_scripts()
        global $wp_version;
        $wc_screen_id       = woo_vou_get_wc_screen_id();
        $woo_vou_screen_id  = woo_vou_get_voucher_screen_id();
        $prefix             = WOO_VOU_META_PREFIX; // get prefix
        $coupon_type        = '';
        $newui              = $wp_version >= '3.5' ? '1' : '0'; //check wp version for showing media uploader
        $post_id            = $prod;
        $wc_vou_vendor_screen   = 'toplevel_page_woo-vou-codes'; // screen id of voucher code page when vendors role

        $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ) );
        wp_register_script( 'woo-vou-admin-script', WOO_VOU_URL . 'includes/js/woo-vou-admin.js', array(), WOO_VOU_PLUGIN_VERSION, true);

        $is_partial_redeem = get_option('vou_enable_partial_redeem');

        wp_localize_script( 'woo-vou-admin-script' , 'WooVouAdminSetOpt' , array( 
        'is_partial_option' => $is_partial_redeem ) );
        wp_enqueue_script( 'woo-vou-admin-script' );

        // check if pdf fonts plugin is active or not
        $is_pdf_fonts_plugin_active = false;
        if( defined( 'WOO_VOU_PF_DIR') ) {
        $is_pdf_fonts_plugin_active = true;
        }

        $coupon_type = get_post_meta( $post_id, $prefix . 'coupon_type', true );

        $is_addon = "";
        if(isset($_GET['section']) && $_GET['section'] == 'vou_addon'){
        $is_addon = "vou_addon";
        }
        wp_localize_script( 'woo-vou-admin-script' , 'WooVouAdminSettings' , array( 
        'ajaxurl'                    => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
        'new_media_ui'               => $newui, 
        'is_pdf_fonts_plugin_active' => $is_pdf_fonts_plugin_active,
        'coupon_type'                => $coupon_type,
        'upload_base_url'            => $upload_dir['baseurl'],
        'code_used_success' => esc_html__( 'Thank you for your business, voucher code submitted successfully.', 'woovoucher' ),
        'redeem_amount_empty_error' => esc_html__( 'Please enter redeem amount.', 'woovoucher' ),
        'redeem_amount_greaterthen_redeemable_amount' => esc_html__( 'Redeem amount should not be greater than redeemable amount.', 'woovoucher' ),
        'is_addon' => $is_addon
        ) );

        $vou_change_expiry_date = get_option( 'vou_change_expiry_date' );

        // add js for code details in admin
        wp_register_script( 'woo-vou-code-detail-script', WOO_VOU_URL . 'includes/js/woo-vou-code-details.js', array( 'jquery' ), WOO_VOU_PLUGIN_VERSION );
        wp_enqueue_script( 'woo-vou-code-detail-script' );

        wp_localize_script( 'woo-vou-code-detail-script' , 'WooVouCode' , array( 
            'ajaxurl'           => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
            'new_media_ui'      => $newui,
            'invalid_url'       => esc_html__( 'Please enter valid url (i.e. http://www.example.com).', 'woovoucher' ),
            'invalid_email'     => esc_html__('Please enter valid Email ID', 'woovoucher'),
            'mail_sent'         => esc_html__('Mail sent successfully', 'woovoucher'),
            'vou_change_expiry_date' => $vou_change_expiry_date
        ) );
        wp_enqueue_media();

        $is_variable = $is_translated = $is_enable_coupon = ''; // Declare variables
        $is_translated      = apply_filters('woo_vou_is_translation_product', false, $post_id); // Filter added to add compatibility with WPML
        $product = wc_get_product($post_id);
        $is_variable = (is_object($product) && ($product->is_type('variable') || $product->is_type('variation'))) ? 1 : 0;

        // Get coupon code option
        $vou_enable_coupon = get_option( 'vou_enable_coupon_code' );

        wp_register_script( 'woo-vou-script-metabox', WOO_VOU_URL.'includes/js/woo-vou-metabox.js', array( 'jquery', 'jquery-form' ), WOO_VOU_PLUGIN_VERSION, true ); 
        wp_enqueue_script( 'woo-vou-script-metabox' );
        wp_localize_script( 'woo-vou-script-metabox', 'WooVouMeta', array(  
            'invalid_url'               => esc_html__( 'Please enter valid url (i.e. http://www.example.com).', 'woovoucher' ),
            'noofvouchererror'          => '<div>' . esc_html__( 'Please enter Number of Voucher Codes.', 'woovoucher' ) . '</div>',
            'patternemptyerror'         => '<div>' . esc_html__( 'Please enter Pattern to import voucher code(s).', 'woovoucher' ) . '</div>',
            'onlydigitserror'           => '<div>' . esc_html__( 'Please enter only Numeric values in Number of Voucher Codes.', 'woovoucher' ) . '</div>',
            'generateerror'             => '<div>' . esc_html__( 'Please enter Valid Pattern to import voucher code(s).', 'woovoucher' ) . '</div>',
            'filetypeerror'             => '<div>' . esc_html__( 'Please upload csv file.', 'woovoucher' ) . '</div>',
            'fileerror'                 => '<div>' . esc_html__( 'File can not be empty, please upload valid file.', 'woovoucher' ) . '</div>',
            'new_media_ui'              => $newui,
            'enable_voucher'            => get_option( 'vou_enable_voucher' ), //Localize "Auto Enable Voucher" setting to use in JS 
            'price_options'             => get_option( 'vou_voucher_price_options' ), //Localize "Voucher Price Options" setting to use in JS 
            'invalid_price'             => esc_html__( 'You can\'t leave this empty.', 'woovoucher' ),
            'woo_vou_nonce'             => wp_create_nonce( 'woo_vou_pre_publish_validation' ),
            'ajaxurl'                   => admin_url( 'admin-ajax.php', ( is_ssl() ? 'https' : 'http' ) ),
            'prefix_placeholder'        => esc_html__('WPWeb', 'woovoucher'),
            'seperator_placeholder'     => '-',
            'pattern_placeholder'       => 'LLDD',
            'global_vou_pdf_usability'  => get_option('vou_pdf_usability'),
            'is_variable'               => $is_variable,
            'is_translated'             => $is_translated,
            'stock_qty_err'             => '<span id="woo_vou_stock_error" class="woo-vou-stocks-error">'.esc_html__('Please either enter quantity for "Stock quantity" or untick "Manage stock?" option.', 'woovoucher').'</span>',
            'deliverymetherror'         => esc_html__( 'Please select atleast one delivery method.', 'woovoucher' ),
            'enable_coupon_code'        => $vou_enable_coupon
        ) );

        wp_enqueue_script( array( 'jquery', 'jquery-ui-tabs', 'media-upload', 'thickbox', 'tinymce','jquery-ui-accordion' ) );

        wp_register_script( 'woo-vou-admin-voucher-script', WOO_VOU_URL . 'includes/js/woo-vou-admin-voucher.js', array(), WOO_VOU_PLUGIN_VERSION );
        wp_enqueue_script( 'woo-vou-admin-voucher-script' );

        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouSettings' , array( 'new_media_ui' => $newui ) );
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouTranObj' , array( 
            'onbuttontxt' => esc_html__('Voucher Builder is On','woovoucher'),
            'offbuttontxt' => esc_html__('Voucher Builder is Off','woovoucher'),
            'switchanswer' => esc_html__('Default WordPress editor has some content, switching to the Voucher will remove it.','woovoucher'),
            'btnsave' => esc_html__('Save','woovoucher'),
            'btncancel' => esc_html__('Cancel','woovoucher'),
            'btndelete' => esc_html__('Delete','woovoucher'),
            'btnaddmore' => esc_html__('Add More','woovoucher'),
            'wp_version' => $wp_version
        ));
        /* this is used for text block section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouTextBlock' , array( 
            'textblocktitle' => esc_html__('Voucher Code','woovoucher'),
            'textblockdesc' => esc_html__('Voucher Code','woovoucher'),
            'textblockdesccodes' => '{codes}'
        ));
        /* this is used for message box section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouMsgBox' , array( 
            'msgboxtitle' => esc_html__('Redeem Instruction','woovoucher'),
            'msgboxdesc' => '<p>' . '{redeem}' . '</p>'
        ));
        /* this is used for logo box section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouSiteLogoBox' , array( 
                'sitelogoboxtitle' => esc_html__('Voucher Site Logo','woovoucher'),
                'sitelogoboxdesc'  => '{sitelogo}'
            ));
        /* this is used for logo box section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouLogoBox' , array( 
            'logoboxtitle' => esc_html__('Voucher Logo','woovoucher'),
            'logoboxdesc' => '{vendorlogo}'
        ));
        /* this is used for expire date block section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouExpireBlock' , array( 
            'expireblocktitle' => esc_html__('Expire Date','woovoucher'),
            'expireblockdesc' => esc_html__('Expire:','woovoucher') . ' {expiredatetime}'
        ));
        /* this is used for vendor's address block section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouVenAddrBlock' , array( 
            'venaddrblocktitle' => esc_html__('Vendor\'s Address','woovoucher'),
            'venaddrblockdesc' => '{vendoraddress}'
        ));
        /* this is used for website URL block section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouSiteURLBlock' , array( 
            'siteurlblocktitle' => esc_html__('Website URL','woovoucher'),
            'siteurlblockdesc' => '{siteurl}'
        ));
        /* this is used for voucher location block section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouLocBlock' , array( 
            'locblocktitle' => esc_html__('Voucher Locations','woovoucher'),
            'locblockdesc' => '<p><span style="font-size: 9pt;">{location}</span></p>'
        ));
        /* this is used for blank box section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouBlankBox' , array( 
            'blankboxtitle' => esc_html__('Blank Block','woovoucher'),
            'blankboxdesc' => esc_html__('Blank Block','woovoucher')
        ));
        /* this is used for custom box section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouCustomBlock' , array( 
            'customblocktitle' => esc_html__('Custom Block','woovoucher'),
            'customblockdesc' => esc_html__('Custom Block','woovoucher')
        ));
                                                                                        
        /* this is used for custom box section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouQrcodeBlock' , array( 
            'qrcodeblocktitle' => esc_html__( 'QR Code','woovoucher' ),
            'qrcodeblockdesc' => '{qrcode}'
        ));

        /* this is used for custom box section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouQrcodesBlock' , array( 
            'qrcodesblocktitle' => esc_html__( 'QR Codes','woovoucher' ),
            'qrcodesblockdesc' => '{qrcodes}'
        ));
        
        /* this is used for custom box section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouBarcodeBlock' , array( 
            'barcodeblocktitle' => esc_html__( 'Barcode','woovoucher' ),
            'barcodeblockdesc' => '{barcode}'
        ));

        /* this is used for custom box section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouBarcodesBlock' , array( 
            'barcodesblocktitle' => esc_html__( 'Barcodes','woovoucher' ),
            'barcodesblockdesc' => '{barcodes}'
        ));
                                                                                        
        /* this is used for Messages section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouMessage' , array( 
            'invalid_number' => esc_html__('Please enter valid number.','woovoucher'),
        ));

        /* this is used for feature image section */
        wp_localize_script( 'woo-vou-admin-voucher-script' , 'WooVouProductImageBlock' , array( 
            'productimageblocktitle' => esc_html__( 'Product Image','woovoucher' ),
            'productimageblockdesc' => '{productimage}'
        ));
    }

    function woo_vou_styles(){
        global  $wp_scripts;
        $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';

        // Register admin styles
        wp_register_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css', array(), $jquery_version );
        wp_enqueue_style( 'jquery-ui-style' );
        
        wp_register_style( 'woo-vou-font-awesome-style', WOO_VOU_URL.'includes/css/font-awesome.min.css', array(), WOO_VOU_PLUGIN_VERSION );
        wp_enqueue_style( 'woo-vou-font-awesome-style' );
        
        wp_register_style( 'woo-vou-admin-style', WOO_VOU_URL.'includes/css/woo-vou-admin.css', array(), WOO_VOU_PLUGIN_VERSION );
        wp_enqueue_style( 'woo-vou-admin-style' );

        wp_register_style( 'woo-vou-style-metabox', WOO_VOU_URL.'includes/css/woo-vou-metabox.css', array(), WOO_VOU_PLUGIN_VERSION );
        wp_enqueue_style( 'woo-vou-style-metabox' );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'farbtastic' );
        wp_register_style( 'woo-vou-admin-style',  WOO_VOU_URL . 'includes/css/woo-vou-admin-voucher.css', array(), WOO_VOU_PLUGIN_VERSION );
        wp_enqueue_style( 'woo-vou-admin-style' );
        wp_register_style( 'woo_vou_dashboard_style', WOO_VOU_URL.'includes/css/woo-vou-dashboard-widget.css' );
        wp_enqueue_style( 'woo_vou_dashboard_style' );
        wp_register_style( 'woo-vou-common-admin-style',  WOO_VOU_URL . 'includes/css/woo-vou-admin-common.css', array(), WOO_VOU_PLUGIN_VERSION );
        
        wp_enqueue_style( 'woo-vou-common-admin-style' );
    }
}




