<?php 

defined( 'ABSPATH' ) || exit; 

?>
<html>
    <head>
        <base href="../../">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php
        // favicon url
        $favicon_setting = get_option('marketking_logo_favicon_setting','');
        if (empty($favicon_setting)){
            $favicon_setting = MARKETKINGCORE_URL. 'includes/assets/images/marketking-icon5.svg';
        }

        global $marketking_is_dashboard;
        $marketking_is_dashboard = true;

        ?>
        <link rel="shortcut icon" href="<?php echo apply_filters('marketking_favicon_url', $favicon_setting);?>"/>
        <title><?php 

        // esc_html_e('Vendor Dashboard','marketking-multivendor-marketplace-for-woocommerce');
        $dashboardid = intval(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true));

        echo esc_html(get_the_title($dashboardid));

        ?></title>
        <?php

        global $marketking_public;

        // fix action scheduler bug
        add_filter('option_woocommerce_attribute_lookup_direct_updates', function($val){
            return 'yes';
        }, 10, 1);

        add_action('wp_print_styles', array($marketking_public, 'enqueue_dashboard_resources'));
        add_action('wp_print_scripts', array($marketking_public, 'enqueue_dashboard_resources'));

        // enqueue media uploader
        require_once ABSPATH . WPINC . '/media-template.php';
        add_action( 'wp_print_scripts', 'wp_print_media_templates' );
        add_action( 'marketking_dashboard_footer', 'wp_print_media_templates' );

        add_action('wp_print_styles', function(){
            global $wp_styles;
            $wp_styles->queue = apply_filters('marketking_css_queue', array('marketking_dashboard','media-views','imgareaselect', 'wc-country-select', 'select2', 'selectWoo', 'simplebar'));

            $currentp = get_query_var('dashpage');
            $b2bkingpages = array('rules','offers','b2bmessaging');

            if (defined('B2BKING_DIR') && defined('MARKETKINGPRO_DIR') && in_array($currentp, $b2bkingpages) && intval(get_option('marketking_enable_b2bkingintegration_setting', 1)) === 1){
                $b2bkingarray = array('marketkingpro_b2bkingintegrationcss','dataTables','jquerymodalzz','select2');
                $wp_styles->queue = array_merge($wp_styles->queue, $b2bkingarray);
            }

            /// OR if current page is refunds, then add jquery modals
            if ($currentp === 'refunds'){
                $b2bkingarray = array('marketkingpro_b2bkingintegrationcss','jquerymodalzz','select2');
                $wp_styles->queue = array_merge($wp_styles->queue, $b2bkingarray);
            }
        });
        add_action('wp_print_scripts', function(){

            global $wp_scripts;
            $wp_scripts->queue = apply_filters('marketking_js_queue',array('marketking_dashboard_bundle','marketking_dashboard_scripts','marketking_dashboard_chart','marketking_public_script', 'marketking_dashboard_messages', 'marketking_dashboard_chart_sales', 'dataTablesButtons', 'jszip', 'pdfmake', 'dataTablesButtonsHTML', 'dataTablesButtonsPrint', 'dataTablesButtonsColvis', 'vfsfonts', 'media-editor','media-audiovideo', 'wc-country-select', 'select2', 'selectWoo', 'simplebar'));

            $currentp = get_query_var('dashpage');
            $b2bkingpages = array('rules','offers','b2bmessaging');
            if (defined('B2BKING_DIR') && defined('MARKETKINGPRO_DIR') && in_array($currentp, $b2bkingpages) && intval(get_option('marketking_enable_b2bkingintegration_setting', 1)) === 1){
                $b2bkingarray = array('marketkingpro_b2bkingintegrationjs','dataTables','jquerymodalzz','select2');
                $wp_scripts->queue = array_merge($wp_scripts->queue, $b2bkingarray);
            }

            /// OR if current page is refunds, then add jquery modals
            if ($currentp === 'refunds'){
                $b2bkingarray = array('marketkingpro_b2bkingintegrationjs','jquerymodalzz','select2');
                $wp_scripts->queue = array_merge($wp_scripts->queue, $b2bkingarray);
            }
        });

        // ON EDIT PRODUCT PAGE + MANAGE ORDER PAGE, LOAD WOOCOMMERCE JS
        if ( get_query_var( 'dashpage' ) === 'edit-resource' || get_query_var( 'dashpage' ) === 'calendar-google-integration' || get_query_var( 'dashpage' ) === 'edit-booking-order' || get_query_var( 'dashpage' ) === 'add-booking-order' || get_query_var( 'dashpage' ) === 'booking-calendar' || get_query_var( 'dashpage' ) === 'edit-booking-product' || get_query_var( 'dashpage' ) === 'edit-product' || get_query_var( 'dashpage' ) === 'manage-order' || get_query_var( 'dashpage' ) === 'edit-coupon' || get_query_var( 'dashpage' ) === 'import-products' || get_query_var( 'dashpage' ) === 'export-products' ) {
            add_action('wp_print_styles', array($marketking_public, 'enqueue_dashboard_woocommerce_resources'));
            add_action('wp_print_scripts', array($marketking_public, 'enqueue_dashboard_woocommerce_resources'));                 
        }

        // Integrations
        // FooEvents // incomplete
        if (apply_filters('marketking_enable_fooevents_integration', true)){
            
            if (class_exists('FooEvents')){

                include ( MARKETKINGCORE_DIR . 'public/dashboard/integrations/fooevents.php' );

                add_action('wp_print_styles', function(){
                    $fooevents = new Marketking_Foo;
                    $fooevents->foo_styles();
                });

                add_action('wp_print_scripts', function(){
                    wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
                    wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.js' ), array( 'iris' ), false, 1 );

                    $fooevents = new Marketking_Foo;
                    $fooevents->foo_scripts();
                });

            } 
            
        }

        //ireca themes car rental 
        if (defined('OVACRS_PLUGIN_FILE')){
            add_action('wp_print_styles', function(){
               // Admin Css
               wp_enqueue_style('datetimepicker', OVACRS_PLUGIN_URI.'assets/plugins/datetimepicker/jquery.datetimepicker.css', array(), null);
               wp_enqueue_style('ovacrs-default', OVACRS_PLUGIN_URI.'assets/ovacrs_admin.css', array(), null);

               wp_enqueue_style( 'ovacrs_woo_admin', plugins_url().'/woocommerce/assets/css/admin.css');
               wp_enqueue_style( 'ovacrs_booking_admin', OVACRS_PLUGIN_URI.'admin/admin-style.css');

            });

            add_action('wp_print_scripts', function(){
                // Date Time Picker
                wp_enqueue_script('datetimepicker', OVACRS_PLUGIN_URI.'assets/plugins/datetimepicker/jquery.datetimepicker.js', array('jquery'), null, true );
                wp_enqueue_script('ova_crs', OVACRS_PLUGIN_URI.'assets/ova-crs.js', array('jquery'), null, true );

               wp_enqueue_script('admin_script', OVACRS_PLUGIN_URI.'admin/admin_script.js', array('jquery'),false,true);
               wp_localize_script( 'admin_script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

               add_action('wp_footer', function(){
                   ?>
                   <script type="text/javascript">
                       jQuery(document).ready(function(){
                           <?php 
                           $bundle_script = file_get_contents(OVACRS_PLUGIN_URI.'assets/ova-crs.js');
                           echo $bundle_script; 
                           ?>
                       });
                   </script>
                   <?php
               }, 1000);

               wp_enqueue_script('moment', OVACRS_PLUGIN_URI.'admin/fullcalendar/moment.min.js', array('jquery'),null,true);
               wp_enqueue_script('fullcalendar', OVACRS_PLUGIN_URI.'admin/fullcalendar/main.js', array('jquery'),null,true);
               wp_enqueue_script('locale-all', OVACRS_PLUGIN_URI.'admin/fullcalendar/locales-all.js', array('jquery'),null,true);
               wp_enqueue_style('fullcalendar', OVACRS_PLUGIN_URI.'admin/fullcalendar/main.min.css', array(), null);
               wp_enqueue_script('calendar_booking', OVACRS_PLUGIN_URI.'admin/calendar.js', array('jquery'), false, true );
               
            });
        }

        // pdf invoices webtoffee

        if (defined('WF_PKLIST_VERSION')){
            add_action('wp_print_scripts', function(){
                wp_enqueue_script( WF_PKLIST_PLUGIN_NAME, WF_PKLIST_PLUGIN_URL . 'admin/js/wf-woocommerce-packing-list-admin.js', array( 'jquery','jquery-ui-autocomplete','wp-color-picker','jquery-tiptip'), WF_PKLIST_VERSION, false );
                //order list page bulk action filter
                $order_meta_autocomplete = Wf_Woocommerce_Packing_List_Admin::order_meta_dropdown_list();
                $wf_admin_img_path= WF_PKLIST_PLUGIN_URL . 'admin/images/uploader_sample_img.png';
                $is_rtl = is_rtl() ? 'rtl' : 'ltr';
                $params=array(
                    'nonces' => array(
                            'wf_packlist' => wp_create_nonce(WF_PKLIST_PLUGIN_NAME),
                     ),
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'no_image'=>$wf_admin_img_path,
                    'bulk_actions'=>array(),
                    'print_action_url'=>admin_url('?print_packinglist=true'),
                    'order_meta_autocomplete' => json_encode($order_meta_autocomplete),
                    'is_rtl' => $is_rtl,
                    'msgs'=>array(
                        'settings_success'=>__('Settings updated.','print-invoices-packing-slip-labels-for-woocommerce'),
                        'all_fields_mandatory'=>__('All fields are mandatory','print-invoices-packing-slip-labels-for-woocommerce'),
                        'settings_error'=>sprintf(__('Unable to update settings due to an internal error. %s To troubleshoot please click %s here. %s', 'print-invoices-packing-slip-labels-for-woocommerce'), '<br />', '<a href="https://www.webtoffee.com/how-to-fix-the-unable-to-save-settings-issue/" target="_blank">', '</a>'),
                        'select_orders_first'=>__('You have to select order(s) first!','print-invoices-packing-slip-labels-for-woocommerce'),
                        'invoice_not_gen_bulk'=>__('One or more order do not have invoice generated. Generate manually?','print-invoices-packing-slip-labels-for-woocommerce'),
                        'error'=>__('Error','print-invoices-packing-slip-labels-for-woocommerce'),
                        'please_wait'=>__('Please wait','print-invoices-packing-slip-labels-for-woocommerce'),
                        'is_required'=>__("is required",'print-invoices-packing-slip-labels-for-woocommerce'),
                        'invoice_title_prompt' => __("Invoice",'print-invoices-packing-slip-labels-for-woocommerce'),
                        'invoice_number_prompt' => __("number has not been generated yet. Do you want to manually generate one ?",'print-invoices-packing-slip-labels-for-woocommerce'),
                        'invoice_number_prompt_free_order' => __("‘Generate invoice for free orders’ is disabled in Invoice settings > Advanced. You are attempting to generate invoice for this free order. Proceed?",'print-invoices-packing-slip-labels-for-woocommerce'),
                        'creditnote_number_prompt' => __("Refund in this order seems not having credit number yet. Do you want to manually generate one ?",'print-invoices-packing-slip-labels-for-woocommerce'),
                        'invoice_number_prompt_no_from_addr' => __("Please fill the `from address` in the plugin's general settings.",'print-invoices-packing-slip-labels-for-woocommerce'),
                        'fitler_code_copied' => __("Code Copied","print-invoices-packing-slip-labels-for-woocommerce"),
                        'close'=>__("Close",'print-invoices-packing-slip-labels-for-woocommerce'),
                        'save'=>__("Save",'print-invoices-packing-slip-labels-for-woocommerce'),
                        'enter_mandatory_fields'=>__('Please enter mandatory fields','print-invoices-packing-slip-labels-for-woocommerce'),
                        'buy_pro_prompt_order_meta' => __('You can add more than 1 order meta in','print-invoices-packing-slip-labels-for-woocommerce'),
                        'buy_pro_prompt_edit_order_meta' => __('Edit','print-invoices-packing-slip-labels-for-woocommerce'),
                        'buy_pro_prompt_edit_order_meta_desc' => __('You can edit an existing item by using its key.','print-invoices-packing-slip-labels-for-woocommerce'),
                    )
                );
                wp_localize_script(WF_PKLIST_PLUGIN_NAME, 'wf_pklist_params', $params);
            });


        }
       

        /* Crowdfunding for WooCommerce */
        if (defined('WC_CF_URL')){
            require_once WC_CF_DIR . 'include/class-admin.php';
            require_once WC_CF_DIR . 'include/class-product-settings.php';
        }

        /* Product Bundles */
        if (intval(get_option('marketking_enable_bundles_setting', 1)) === 1){

            if(defined('MARKETKINGPRO_DIR')){
                // yith bundle as well
                if (defined('YITH_WCPB_VERSION')){
                    $admin = YITH_WCPB_Admin();

                    add_action('wp_print_styles', function(){
                       wp_enqueue_style( 'yith-wcpb-admin-styles', YITH_WCPB_ASSETS_URL . '/css/admin.css', array(), YITH_WCPB_VERSION );
                       wp_register_style( 'yith-wcpb-popup', YITH_WCPB_ASSETS_URL . '/css/yith-wcpb-popup.css', array(), YITH_WCPB_VERSION );
                       wp_enqueue_style( 'yith-wcpb-popup' );
                       wp_enqueue_style( 'yith-plugin-fw-fields' );
                    });
                    add_action('wp_print_scripts', function(){
                       $metabox_js = defined( 'YITH_WCPB_PREMIUM' ) ? 'bundle_options_metabox_premium.js' : 'bundle_options_metabox.js';
                       wp_register_script( 'yith-wcpb-popup', yit_load_js_file( YITH_WCPB_ASSETS_URL . '/js/yith-wcpb-popup.js' ), array( 'jquery' ), YITH_WCPB_VERSION, true );
                       wp_register_script( 'yith_wcpb_bundle_options_metabox', yit_load_js_file( YITH_WCPB_ASSETS_URL . '/js/' . $metabox_js ), array( 'jquery', 'jquery-ui-sortable', 'yith-wcpb-popup' ), YITH_WCPB_VERSION, true );

                       wp_enqueue_script( 'yith-plugin-fw-fields' );
                       wp_enqueue_script( 'yith_wcpb_bundle_options_metabox' );

                       wp_localize_script(
                           'yith_wcpb_bundle_options_metabox',
                           'ajax_object',
                           array(
                               'free_not_simple'     => __( 'You can add only simple products with the FREE version of YITH WooCommerce Product Bundles', 'yith-woocommerce-product-bundles' ),
                               'yith_bundle_product' => __( 'You cannot add a bundle product', 'yith-woocommerce-product-bundles' ),
                               'minimum_characters'  => apply_filters( 'yith_wcpb_minimum_characters_ajax_search', 3 ),
                           )
                       );

                       wp_localize_script(
                           'yith_wcpb_bundle_options_metabox',
                           'yith_bundle_opts',
                           array(
                               'i18n'               => array(
                                   'addedLabelSingular' => _n( '1 item added', '%s items added', 1, 'yith-woocommerce-product-bundles' ),
                                   'addedLabelPlural'   => _n( '1 item added', '%s items added', 2, 'yith-woocommerce-product-bundles' ),
                               ),
                               'minimum_characters' => apply_filters( 'yith_wcpb_minimum_characters_ajax_search', 3 ),
                           )
                       );
                    });

                }
                if (class_exists('WC_Bundles')){

                    // Admin notices handling.
                    require_once( WC_PB_ABSPATH . 'includes/admin/class-wc-pb-admin-notices.php' );

                    // Admin functions and hooks.
                    require_once( WC_PB_ABSPATH . 'includes/admin/class-wc-pb-admin.php' );

                    // Product Import/Export.
                    require_once( WC_PB_ABSPATH . 'includes/admin/export/class-wc-pb-product-export.php' );
                    require_once( WC_PB_ABSPATH . 'includes/admin/import/class-wc-pb-product-import.php' );

                    // Product Metaboxes.
                    require_once( WC_PB_ABSPATH . 'includes/admin/meta-boxes/class-wc-pb-meta-box-product-data.php' );

                    // Post type stuff.
                    require_once( WC_PB_ABSPATH . 'includes/admin/class-wc-pb-admin-post-types.php' );

                    // Admin AJAX.
                    require_once( WC_PB_ABSPATH . 'includes/admin/class-wc-pb-admin-ajax.php' );

                    // Admin edit-order screen.
                    require_once( WC_PB_ABSPATH . 'includes/admin/class-wc-pb-admin-order.php' );

                    require_once( WC_PB_ABSPATH . 'includes/modules/min-max-items/includes/admin/class-wc-pb-mmi-admin.php' );

                    require_once( WC_PB_ABSPATH . 'includes/modules/min-max-items/includes/class-wc-pb-mmi-product.php' );


                    add_action('wp_print_styles', function(){
                       $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                       wp_register_style( 'wc-pb-admin-css', WC_PB()->plugin_url() . '/assets/css/admin/admin.css', array(), WC_PB()->version );
                       wp_style_add_data( 'wc-pb-admin-css', 'rtl', 'replace' );

                       wp_register_style( 'wc-pb-admin-product-css', WC_PB()->plugin_url() . '/assets/css/admin/meta-boxes-product.css', array( 'woocommerce_admin_styles' ), WC_PB()->version );
                       wp_style_add_data( 'wc-pb-admin-product-css', 'rtl', 'replace' );

                       wp_register_style( 'wc-pb-admin-edit-order-css', WC_PB()->plugin_url() . '/assets/css/admin/meta-boxes-order.css', array( 'woocommerce_admin_styles' ), WC_PB()->version );
                       wp_style_add_data( 'wc-pb-admin-edit-order-css', 'rtl', 'replace' );

                       wp_enqueue_style( 'wc-pb-admin-css' );

                       wp_enqueue_style( 'wc-pb-admin-product-css', 'sw-admin-css-select' );

                       wp_enqueue_style( 'wc-pb-admin-edit-order-css' );


                    });
                    add_action('wp_print_scripts', function(){
                        
                        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                        wp_register_script( 'wc-pb-admin-product-panel', WC_PB()->plugin_url() . '/assets/js/admin/meta-boxes-product' . $suffix . '.js', array( 'wc-admin-product-meta-boxes', 'sw-admin-select-init' ), WC_PB()->version );
                        wp_register_script( 'wc-pb-admin-order-panel', WC_PB()->plugin_url() . '/assets/js/admin/meta-boxes-order' . $suffix . '.js', array( 'wc-admin-order-meta-boxes' ), WC_PB()->version );

                        wp_enqueue_script( 'wc-pb-admin-product-panel' );

                        // Find group modes with a parent item.
                        $group_mode_options      = WC_Product_Bundle::get_group_mode_options();
                        $group_modes_with_parent = array();

                        foreach ( $group_mode_options as $group_mode_key => $group_mode_title ) {
                            if ( WC_Product_Bundle::group_mode_has( $group_mode_key, 'parent_item' ) || WC_Product_Bundle::group_mode_has( $group_mode_key, 'faked_parent_item' ) ) {
                                $group_modes_with_parent[] = $group_mode_key;
                            }
                        }

                        $params = array(
                            'add_bundled_product_nonce' => wp_create_nonce( 'wc_bundles_add_bundled_product' ),
                            'group_modes_with_parent'   => $group_modes_with_parent,
                            'is_first_bundle'           => isset( $_GET[ 'wc_pb_first_bundle' ] ) ? 'yes' : 'no',
                            /* translators: %s: Lowest required qty value. */
                            'i18n_qty_low_error'        => __( 'Please enter an integer higher than %s.', 'woocommerce-product-bundles' ),
                            /* translators: %s: Highest allowed qty value. */
                            'i18n_qty_high_error'       => __( 'Please enter an integer lower than or equal to %s.', 'woocommerce-product-bundles' ),
                            /* translators: %s: Required step qty value. */
                            'i18n_qty_step_error'       => __( 'Please enter an integer that is a multiple of %s.', 'woocommerce-product-bundles' )
                        );

                        wp_localize_script( 'wc-pb-admin-product-panel', 'wc_bundles_admin_params', $params );

                        wc_enqueue_js( "
                            jQuery( function( $ ) {
                                jQuery( '.show_insufficient_stock_items' ).on( 'click', function() {
                                    var anchor = jQuery( this ),
                                        panel  = jQuery( this ).parent().find( '.insufficient_stock_items' );

                                    if ( anchor.hasClass( 'closed' ) ) {
                                        anchor.removeClass( 'closed' );
                                        panel.slideDown( 200 );
                                    } else {
                                        anchor.addClass( 'closed' );
                                        panel.slideUp( 200 );
                                    }
                                    return false;
                                } );
                            } );
                        " );

                        wp_enqueue_script( 'wc-pb-admin-order-panel' );

                        $params = array(
                            'edit_bundle_nonce'     => wp_create_nonce( 'wc_bundles_edit_bundle' ),
                            'i18n_configure'        => __( 'Configure', 'woocommerce-product-bundles' ),
                            'i18n_edit'             => __( 'Edit', 'woocommerce-product-bundles' ),
                            'i18n_form_error'       => __( 'Failed to initialize form. If this issue persists, please reload the page and try again.', 'woocommerce-product-bundles' ),
                            'i18n_validation_error' => __( 'Failed to validate configuration. If this issue persists, please reload the page and try again.', 'woocommerce-product-bundles' )
                        );

                        wp_localize_script( 'wc-pb-admin-order-panel', 'wc_bundles_admin_order_params', $params );
                        $bundled_selectsw_version = '1.1.7';

                        $is_registered      = wp_script_is( 'sw-admin-select-init', $list = 'registered' );
                        $registered_version = $is_registered ? wp_scripts()->registered[ 'sw-admin-select-init' ]->ver : '';
                        $register           = ! $is_registered || version_compare( $bundled_selectsw_version, $registered_version, '>' );


                        if ( $register ) {

                            if ( $is_registered ) {
                                wp_deregister_script( 'sw-admin-select-init' );
                            }

                            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                            // Register own select2 initialization library.
                            wp_register_script( 'sw-admin-select-init', WC_PB()->plugin_url() . '/assets/js/admin/select2-init' . $suffix . '.js', array( 'jquery', 'sw-admin-select' ), $bundled_selectsw_version );
                        }

                        if(!function_exists('load_selectsw2')){ 

                            function load_selectsw2() {

                                $load_selectsw_from = wp_scripts()->registered[ 'sw-admin-select-init' ]->src;

                                return strpos( $load_selectsw_from, WC_PB()->plugin_url() ) === 0;
                            }
                        }

                        if ( load_selectsw2() ) {

                            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                            // Register selectSW library.
                            wp_register_script( 'sw-admin-select', WC_PB()->plugin_url() . '/assets/js/admin/select2' . $suffix . '.js', array( 'jquery' ), $bundled_selectsw_version );

                            // Register selectSW styles.
                            wp_register_style( 'sw-admin-css-select', WC_PB()->plugin_url() . '/assets/css/admin/select2.css', array(), $bundled_selectsw_version );
                            wp_style_add_data( 'sw-admin-css-select', 'rtl', 'replace' );
                        }
                    });

                    
                }
            }
        }
       

        /* WooCommerce Box Office */
        if (apply_filters('marketking_enable_boxoffice_integration', true)){

            if (defined('WOOCOMMERCE_BOX_OFFICE_VERSION')){

                add_action('wp_print_styles', function(){
                   wp_register_style( 'woocommerce-box-office-admin-post-type-product', WCBO()->assets_url . 'css/admin-post-type-product.css', array(), WCBO()->_version );
                   wp_register_style( 'woocommerce-box-office-admin-post-type-event-ticket', WCBO()->assets_url . 'css/admin-post-type-event-ticket.css', array(), WCBO()->_version );
                   wp_register_style( 'woocommerce-box-office-admin-post-type-event-ticket-email', WCBO()->assets_url . 'css/admin-post-type-event-ticket-email.css', array(), WCBO()->_version );
                   wp_register_style( 'woocommerce-box-office-admin-tools', WCBO()->assets_url . 'css/admin-tools.css', array(), WCBO()->_version );
                   wp_register_style( 'woocommerce-box-office-multiple-tickets', WCBO()->assets_url . 'css/multiple-tickets.css', array(), WCBO()->_version );

                   wp_enqueue_style( 'woocommerce-box-office-admin-post-type-product' );
                   wp_enqueue_style( 'woocommerce-box-office-admin-post-type-event-ticket' );
                   wp_enqueue_style( 'woocommerce-box-office-admin-post-type-event-ticket-email' );
                   wp_enqueue_style( 'woocommerce-box-office-admin-tools' );
                   wp_enqueue_style( 'woocommerce-box-office-multiple-tickets' );
                 
                });

                add_action('wp_print_scripts', function(){

                    $exported_js = array(
                        'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    );

                    $exported_js['editPostUrl'] = admin_url( 'post.php?action=edit' );
                    wp_register_script( 'woocommerce-box-office-admin-order', WCBO()->assets_url . 'js/admin-order' . WCBO()->script_suffix . '.js', array( 'jquery' ), WCBO()->_version );
                    wp_enqueue_script( 'woocommerce-box-office-admin-order' );
                    wp_localize_script( 'woocommerce-box-office-admin-order', 'wcBoxOfficeParams', $exported_js );

                    // Export default contents for print and email to JS var.
                    $exported_js['defaultPrintContent'] = wc_get_template_html( 'ticket/default-print-content.php', array(), 'woocommerce-box-office', WCBO()->dir . 'templates/' );
                    $exported_js['defaultEmailContent'] = wc_get_template_html( 'ticket/default-email-content.php', array(), 'woocommerce-box-office', WCBO()->dir . 'templates/' );

                    wp_register_script( 'woocommerce-box-office-admin-product', WCBO()->assets_url . 'js/admin-product' . WCBO()->script_suffix . '.js', array( 'jquery' ), WCBO()->_version );
                    wp_enqueue_script( 'woocommerce-box-office-admin-product' );
                    wp_localize_script( 'woocommerce-box-office-admin-product', 'wcBoxOfficeParams', $exported_js );

                    $exported_js['previewEmailAction']    = 'show_test_email';
                    $exported_js['previewEmailNonce']     = wp_create_nonce( 'test-email' );

                    $exported_js['i18n_previewEmptyProductOrBody']    = __( 'Product is not selected or email body is empty. Please fill it.', 'woocommerce-box-office' );

                    wp_register_script( 'woocommerce-box-office-admin-tools', WCBO()->assets_url . 'js/admin-tools' . WCBO()->script_suffix . '.js', array( 'jquery' ), WCBO()->_version );
                    wp_enqueue_script( 'woocommerce-box-office-admin-tools' );
                    wp_localize_script( 'woocommerce-box-office-admin-tools', 'wcBoxOfficeParams', $exported_js );
                });
            }
        }

        // WooCommerce Min / Max Quantities
        if (apply_filters('marketking_enable_minmaxqty_integration', true)){

            if (class_exists('WC_Min_Max_Quantities')){
                include_once WC_MMQ_ABSPATH . '/includes/class-wc-min-max-quantities-admin.php';

                add_action('wp_print_styles', function(){
                    $instance = WC_Min_Max_Quantities::get_instance();
                    $suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                    wp_enqueue_style( 'wc-mmq-admin', $instance->plugin_url() . '/assets/css/admin/admin.css', '', WC_MIN_MAX_QUANTITIES );

                });
                add_action('wp_print_scripts', function(){
                    $instance = WC_Min_Max_Quantities::get_instance();
                    $suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                    wp_register_script( 'wc-mmq-admin-product-panel', $instance->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery' ), WC_MIN_MAX_QUANTITIES );
                    wp_enqueue_script( 'wc-mmq-admin-product-panel' );
                });
            }
        }

        // Enhancer for WooCommerce Susbcription

        if (defined('ENR_FILE')){

            include_once(ENR_DIR.'includes/admin/class-enr-admin.php');
            include_once(ENR_DIR.'includes/admin/class-enr-admin-post-types.php');

            ENR_Admin::init();

            add_action('wp_print_styles', function(){

                wp_register_style( 'enr-admin', ENR_URL . '/assets/css/admin.css', array( 'woocommerce_admin_styles' ), _enr()->get_version() );
                wp_enqueue_style( 'enr-admin' );

            });
            add_action('wp_print_scripts', function(){
               wp_register_script( 'enr-admin', ENR_URL . '/assets/js/admin.js', array( 'jquery', 'wc-backbone-modal', 'wc-enhanced-select' ), _enr()->get_version() );

               $billing_period_strings = WC_Subscriptions_Synchroniser::get_billing_period_ranges();
               wp_localize_script( 'enr-admin', 'enr_admin_params', array(
                   'period'                                       => wcs_get_subscription_period_strings(),
                   'preview_email_inputs_nonce'                   => wp_create_nonce( 'enr-collect-preview-email-inputs' ),
                   'preview_email_nonce'                          => wp_create_nonce( 'enr-preview-email' ),
                   'email_default_data'                           => ENR_Meta_Box_Subscription_Email_Template_Data::get_default_data(),
                   'email_placeholders'                           => ENR_Meta_Box_Subscription_Email_Template_Data::get_placeholders(),
                   'subscription_lengths'                         => wcs_get_subscription_ranges(),
                   'sync_options'                                 => array(
                       'week'  => $billing_period_strings[ 'week' ],
                       'month' => $billing_period_strings[ 'month' ],
                       'year'  => WC_Subscriptions_Synchroniser::get_year_sync_options(),
                   ),
                   'back_to_all_subscription_plans_url'           => esc_url( admin_url( 'edit.php?post_type=enr_subsc_plan' ) ),
                   'back_to_all_subscription_email_templates_url' => esc_url( admin_url( 'edit.php?post_type=enr_email_template' ) ),
                   'back_to_all_label'                            => esc_attr__( 'Back to all', 'enhancer-for-woocommerce-subscriptions' ),
               ) );

               wp_enqueue_script( 'enr-admin' );

               wp_enqueue_script( 'enr-post-ordering', ENR_URL . '/assets/js/post-ordering.js', array( 'jquery-ui-sortable' ), _enr()->get_version() );


            });
        }
        

        

        // Advanced Product Labels
        if (apply_filters('marketking_enable_productlabels_integration', true)){

            if (class_exists('Woocommerce_Advanced_Product_Labels')){

                // remove custom colors, issue in dashboard
                add_filter('wapl_label_styles', function($options){
                    unset($options['custom']);
                    return $options;
                }, 10, 1);

                add_action('wp_print_styles', function(){
                    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                    wp_enqueue_style('wp-color-picker' );
                    wp_enqueue_style( 'woocommerce-advanced-product-labels-front-end', plugins_url( '/assets/front-end/css/woocommerce-advanced-product-labels.min.css', WooCommerce_Advanced_Product_Labels()->file ), array(), WooCommerce_Advanced_Product_Labels()->version );
                    wp_enqueue_style( 'woocommerce-advanced-product-labels', plugins_url( '/assets/admin/css/woocommerce-advanced-product-labels.min.css', WooCommerce_Advanced_Product_Labels()->file ), array( 'wp-color-picker' ), WooCommerce_Advanced_Product_Labels()->version );


                });
                add_action('wp_print_scripts', function(){
                    wp_enqueue_style('wp-color-picker' );
                    wp_enqueue_script('wp-color-picker' );

                    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                    wp_enqueue_script( 'woocommerce-advanced-product-labels', plugins_url( '/assets/admin/js/woocommerce-advanced-product-labels' . $suffix . '.js', WooCommerce_Advanced_Product_Labels()->file ), array(  ), WooCommerce_Advanced_Product_Labels()->version );
                    wp_enqueue_script( 'wp-conditions' );

                    wp_localize_script( 'woocommerce-advanced-product-labels', 'wapl', array(
                        'types' => array_keys( wapl_get_label_types() ),
                        'colors' => array_keys( wapl_get_label_styles() ),
                    ) );

                    wp_localize_script( 'wp-conditions', 'wpc2', array(
                        'action_prefix' => 'wapl_',
                    ) );

                });
            }
        }

        // Per Product Shipping
        if (apply_filters('marketking_enable_perproductshipping_integration', true)){

            if (defined('PER_PRODUCT_SHIPPING_FILE')){
                $dir = plugin_dir_path(PER_PRODUCT_SHIPPING_FILE);
                include_once $dir.'includes/class-wc-shipping-per-product-admin.php';
                new WC_Shipping_Per_Product_Admin( new WC_Shipping_Per_Product_Init );

                add_action('wp_print_styles', function(){
                    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                    wp_enqueue_style( 'wc-shipping-per-product-styles', plugins_url( 'assets/css/admin.css', PER_PRODUCT_SHIPPING_FILE ) );
                });
                add_action('wp_print_scripts', function(){
                  $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                  wp_register_script( 'wc-shipping-per-product', plugins_url( 'assets/js/shipping-per-product' . $suffix . '.js', PER_PRODUCT_SHIPPING_FILE ), array( 'jquery' ), PER_PRODUCT_SHIPPING_VERSION, true );

                  wp_localize_script( 'wc-shipping-per-product', 'wc_shipping_per_product_params', array(
                      'i18n_no_row_selected' => __( 'No row selected', 'woocommerce-shipping-per-product' ),
                      'i18n_product_id'      => __( 'Product ID', 'woocommerce-shipping-per-product' ),
                      'i18n_country_code'    => __( 'Country Code', 'woocommerce-shipping-per-product' ),
                      'i18n_state'           => __( 'State/County Code', 'woocommerce-shipping-per-product' ),
                      'i18n_postcode'        => __( 'Zip/Postal Code', 'woocommerce-shipping-per-product' ),
                      'i18n_cost'            => __( 'Cost', 'woocommerce-shipping-per-product' ),
                      'i18n_item_cost'       => __( 'Item Cost', 'woocommerce-shipping-per-product' ),
                  ) );
                });
            }
        }

        /* WooCommerce Simple Auctions */
        if (class_exists('WooCommerce_simple_auction') && get_query_var('dashpage') === 'edit-product' && (intval(get_option('marketking_enable_auctions_setting', 1)) === 1) && defined('MARKETKINGPRO_DIR')){

            add_action('wp_print_styles', function(){
                $auctions = new WooCommerce_simple_auction;

                wp_enqueue_style( 'jquery-ui-datepicker' );
                wp_enqueue_style( 'DataTables', $auctions->plugin_url . 'js/DataTables/datatables.min.css', array() );
                wp_enqueue_style( 'DataTables-buttons', $auctions->plugin_url . 'js/DataTables/buttons.dataTables.min.css', array() );
                wp_enqueue_style( 'simple-auction-admin', $auctions->plugin_url . 'css/admin.css', array( 'woocommerce_admin_styles', 'jquery-ui-style' ) );
            });


            add_action('wp_print_scripts', function(){
                $auctions = new WooCommerce_simple_auction;

                $params = array(
                    'ajaxurl'        => admin_url( 'admin-ajax.php' ),
                    'SA_nonce'       => wp_create_nonce( 'SAajax-nonce' ),
                    'calendar_image' => WC()->plugin_url() . '/assets/images/calendar.png',
                    'datatable_language' => array(
                               "sEmptyTable"=>     esc_html__("No data available in table", 'wc_simple_auctions' ),
                               "sInfo"=>           esc_html__("Showing _START_ to _END_ of _TOTAL_ entries", 'wc_simple_auctions' ),
                               "sInfoEmpty"=>      esc_html__("Showing 0 to 0 of 0 entries", 'wc_simple_auctions' ),
                               "sInfoFiltered"=>   esc_html__("(filtered from _MAX_ total entries)", 'wc_simple_auctions' ),
                               "sLengthMenu"=>     esc_html__("Show _MENU_ entries", 'wc_simple_auctions' ),
                               "sLoadingRecords"=> esc_html__("Loading...", 'wc_simple_auctions' ),
                               "sProcessing"=>     esc_html__("Processing...", 'wc_simple_auctions' ),
                               "sSearch"=>         esc_html__("Search:", 'wc_simple_auctions' ),
                               "sZeroRecords"=>    esc_html__("No matching records found", 'wc_simple_auctions' ),
                               "oPaginate"=> array(
                                   "sFirst"=>    esc_html__("First", 'wc_simple_auctions' ),
                                   "sLast"=>     esc_html__("Last", 'wc_simple_auctions' ),
                                   "sNext"=>     esc_html__("Next", 'wc_simple_auctions' ),
                                   "sPrevious"=> esc_html__("Previous", 'wc_simple_auctions' )
                               ),
                               "oAria"=> array(
                                   "sSortAscending"=>  esc_html__(": activate to sort column ascending", 'wc_simple_auctions' ),
                                   "sSortDescending"=> esc_html__(": activate to sort column descending", 'wc_simple_auctions' )
                               )
                            )
                );
                wp_enqueue_script( 'DataTables', $auctions->plugin_url . 'js/DataTables/datatables.min.js', array( 'jquery' ), false );
                wp_enqueue_script( 'DataTables-buttons', $auctions->plugin_url . 'js/DataTables/dataTables.buttons.min.js', array( 'jquery', 'DataTables' ), false );
                wp_enqueue_script( 'jszip', $auctions->plugin_url . 'js/DataTables/jszip.min.js', array( 'jquery', 'DataTables', 'DataTables-buttons' ), false );
                wp_enqueue_script( 'buttons.html5', $auctions->plugin_url . 'js/DataTables/buttons.html5.min.js', array( 'jquery', 'DataTables', 'DataTables-buttons' ), false );
                wp_enqueue_script( 'buttons.colVis', $auctions->plugin_url . 'js/DataTables/buttons.colVis.min.js', array( 'jquery', 'DataTables', 'DataTables-buttons' ), false );

                
                wp_register_script(
                    'simple-auction-admin',
                    $auctions->plugin_url . '/js/simple-auction-admin.js',
                    array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'timepicker-addon', 'wc-admin-meta-boxes'),
                    '1',
                    true
                );

                wp_localize_script(
                    'simple-auction-admin',
                    'SA_Ajax',
                    $params
                );

                wp_enqueue_script( 'simple-auction-admin' );

                wp_enqueue_script(
                    'timepicker-addon',
                    $auctions->plugin_url . '/js/jquery-ui-timepicker-addon.js',
                    array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'DataTables' ),
                    $auctions->version,
                    true
                );
            });

        }

        // WooCommerce Bookings Integration - REF RR
        if (intval(get_option( 'marketking_enable_bookings_setting', 0 )) === 1){

            if(marketking()->vendor_has_panel('bookings')){


                if (defined('MARKETKINGPRO_DIR')){

                    if ( class_exists( 'WC_Bookings' ) ) {

                        add_action('marketking_dashboard_head', function(){
                            if (!defined('WF_PKLIST_VERSION')){
                                ?>
                                <style type="text/css">
                                    .nk-app-root:nth-of-type(2) {
                                        display: none;
                                    }
                                </style>
                                <?php
                            } else {
                               ?>
                               <style type="text/css">
                                   .nk-app-root:nth-of-type(3) {
                                       display: none;
                                   }
                               </style>
                               <?php 
                            }
                        });

                        include( MARKETKINGPRO_DIR. "includes/wcbookings/integrations/wc-bookings/class-marketking-wc-bookings.php" );

                        if (defined('WC_ACCOMMODATION_BOOKINGS_INCLUDES_PATH')){
                            // accomodation
                            include( MARKETKINGPRO_DIR . "includes/wcbookings/integrations/wc-bookings/includes/class-marketking-wc-accomodation-booking.php" );
                        }
                        
                        //booking-calendar
                        include( MARKETKINGPRO_DIR. "includes/wcbookings/integrations/wc-bookings/includes/class-marketking-wc-bookings-calendar.php" );
                        include( MARKETKINGPRO_DIR. "includes/wcbookings/integrations/wc-bookings/includes/class-marketking-wc-bookings-google-calendar-integration.php" );
                        //booking-products
                        include( MARKETKINGPRO_DIR. "includes/wcbookings/integrations/wc-bookings/includes/class-marketking-wc-bookings-metabox.php" );
                        //booking-resources
                        include( MARKETKINGPRO_DIR. "includes/wcbookings/integrations/wc-bookings/includes/class-marketking-wc-bookings-resources.php" );
                        //booking-orders
                        include( MARKETKINGPRO_DIR. "includes/wcbookings/integrations/wc-bookings/includes/class-marketking-wc-bookings-order-edit.php" );
                        include( MARKETKINGPRO_DIR. "includes/wcbookings/integrations/wc-bookings/includes/class-marketking-wc-bookings-order-create.php" );
                        include( MARKETKINGPRO_DIR. "includes/wcbookings/integrations/wc-bookings/includes/class-marketking-wc-bookings-cost-calculation.php" );


                        add_action( 'marketking_wc_bookings_nav', function () {
                            ?>
                            <div class="marketking-wc-booking-nav">
                            <?php
                            if ( get_query_var( 'dashpage' ) !== 'bookings' ):
                                ?>
                                <a href="<?php echo esc_attr( get_page_link( get_option( 'marketking_vendordash_page_setting', 'disabled' ) ) . 'bookings' ); ?>"
                                   class="btn btn-sm btn-secondary  d-md-inline-flex"><em
                                            class="icon ni ni-calendar"></em>
                                    <span><?php esc_html_e( 'Bookings', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></span>
                                </a>
                            <?php endif; ?>
                            <?php
                            if ( get_query_var( 'dashpage' ) !== 'booking-orders' ):
                                ?>
                                <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'products?type=booking'; ?>"
                                   class="btn btn-sm btn-secondary  d-md-inline-flex"><em
                                            class="icon ni ni-package-fill"></em>
                                    <span><?php esc_html_e( 'Products', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></span>
                                </a>
                            <?php endif; ?>
                            <?php
                            if ( get_query_var( 'dashpage' ) !== 'bookable-resources' ):
                                ?>
                                <a href="<?php echo esc_attr( get_page_link( get_option( 'marketking_vendordash_page_setting', 'disabled' ) ) . 'bookable-resources' ); ?>"
                                   class="btn btn-sm btn-secondary  d-md-inline-flex"><em
                                            class="icon ni ni-box"></em>
                                    <span><?php esc_html_e( 'Resources', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></span>
                                </a>
                            <?php endif; ?>
                            <?php
                            if ( get_query_var( 'dashpage' ) !== 'booking-calendar' ):
                                ?>
                                <a href="<?php echo esc_attr( get_page_link( get_option( 'marketking_vendordash_page_setting', 'disabled' ) ) . 'booking-calendar/?view=month' ); ?>"
                                   class="btn btn-sm btn-secondary  d-md-inline-flex"><em
                                            class="icon ni ni-calendar-alt-fill"></em>
                                    <span><?php esc_html_e( 'Calendar', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></span>
                                </a>
                            <?php endif; ?>
                            <?php
                            // temp disabled
                            if (false === true) {

                                if ( get_query_var( 'dashpage' ) === 'booking-calendar' ):
                                    ?>
                                    <a href="<?php echo esc_attr( get_page_link( get_option( 'marketking_vendordash_page_setting', 'disabled' ) ) . 'calendar-google-integration' ); ?>"
                                       class="btn btn-sm btn-secondary  d-md-inline-flex"><em
                                                class="icon ni ni-calendar-alt-fill"></em>
                                        <span><?php esc_html_e( 'Google Calendar Integration', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></span>
                                    </a>

                                <?php endif; 
                            }?>
                            </div>
                            <?php
                        } );


                        // remove EXPORT tab
                        add_filter('woocommerce_product_data_tabs', function($tabs){
                            unset($tabs['bookings_export']);
                            return $tabs;
                        }, 100, 1);

                        add_action( 'marketking_extend_page', function ( $page ) {
                            global $post;
                            if ( get_query_var( 'dashpage' ) === 'edit-booking-product' ) {
                                $prod = sanitize_text_field( Marketking_WC_Bookings::get_pagenr_query_var() );
                                if ( $prod === 'add' ) {
                                    global $marketking_product_add_id;
                                    $prod = $marketking_product_add_id;
                                }

                                $post = 0;
                                if ( ! empty( $prod ) ) {
                                    $post    = get_post( $prod );
                                    $product = wc_get_product( $post );
                                    if ( ! is_a( $post, 'WP_Post' ) ) {
                                        $post = 0;
                                    }
                                }
                            }
                            $wc_bookings = new Marketking_WC_Bookings();
                            $wc_bookings->create_page( $page );
                        } );

                        add_action( 'wp_print_styles', function () {
                            $wc_bookings = new Marketking_WC_Bookings();
                            $wc_bookings->styles();

                        } );


                        add_action( 'wp_print_scripts', function () {

                            $wc_bookings = new Marketking_WC_Bookings();
                            $wc_bookings->scripts();

                            if ( get_query_var( 'dashpage' ) === 'edit-booking-order' ) {
                                $wc_bookings = new Marketking_WC_Bookings_Order_Metabox();
                                $wc_bookings->scripts();
                            }

                            if ( get_query_var( 'dashpage' ) === 'booking-calendar' ) {
                                $wc_bookings = new Marketking_WC_Bookings_Calendar();
                                $wc_bookings->output();

                                $wc_bookings = new Marketking_WC_Bookings_Calendar();
                                $wc_bookings->scripts();

                            }

                            if ( get_query_var( 'dashpage' ) === 'add-booking-order' ) {
                                if ( isset( $_POST['bookable_product_id'] ) ) {
                                    require_once WC_BOOKINGS_ABSPATH . 'includes/admin/class-wc-bookings-admin.php';
                                    require_once WC_BOOKINGS_ABSPATH . 'includes/booking-form/class-wc-booking-form.php';

                                    $product_id = sanitize_text_field( $_POST['bookable_product_id'] );
                                    $product    = get_wc_product_booking( $product_id );

                                    $wc_bookings = new WC_Booking_Form( $product );
                                    $wc_bookings->scripts();
                                }
                            }

                        } );
                    }
                }
            }
        }

        // WooCommerce Bookings Orig Attempt
        /*
        if (class_exists( 'WC_Bookings' )){
            new WC_Bookings_Menus();
            new WC_Bookings_Report_Dashboard();
            $bookings_admin = new WC_Bookings_Admin();
            new WC_Bookings_Ajax();
            new WC_Bookings_Admin_Add_Ons();
            new WC_Booking_Products_Export();
            new WC_Booking_Products_Import();
            new WC_Bookings_Tracks();

            $bookings_admin->init_tabs();
            // remove EXPORT tab
            add_filter('woocommerce_product_data_tabs', function($tabs){
                unset($tabs['bookings_export']);
                return $tabs;
            }, 100, 1);

            add_action('wp_print_styles', function(){
               global $post, $wp_scripts;

               $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';

               wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css' );
               wp_enqueue_style( 'wc_bookings_admin_styles', WC_BOOKINGS_PLUGIN_URL . '/dist/css/admin.css', null, WC_BOOKINGS_VERSION );
               wp_enqueue_style( 'wc_bookings_admin_calendar_css', WC_BOOKINGS_PLUGIN_URL . '/dist/css/admin-calendar-gutenberg.css', null, WC_BOOKINGS_VERSION );
               wp_enqueue_style( 'wc_bookings_admin_store_availability_css', WC_BOOKINGS_PLUGIN_URL . '/dist/css/admin-store-availability.css', null, WC_BOOKINGS_VERSION );

            });

            add_action('wp_print_scripts', function(){
               global $post, $wp_scripts;

               $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';
               
                wp_enqueue_script( 'wc_bookings_admin_js', WC_BOOKINGS_PLUGIN_URL . '/dist/admin.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' ), WC_BOOKINGS_VERSION, true );
                wp_enqueue_script( 'wc-bookings-moment', WC_BOOKINGS_PLUGIN_URL . '/dist/js/lib/moment-with-locales.js', array(), WC_BOOKINGS_VERSION, true );
                wp_enqueue_script( 'wc-bookings-moment-timezone', WC_BOOKINGS_PLUGIN_URL . '/dist/js/lib/moment-timezone-with-data.js', array(), WC_BOOKINGS_VERSION, true );
                wp_register_script( 'wc_bookings_admin_time_picker_js', WC_BOOKINGS_PLUGIN_URL . '/dist/admin-time-picker.js', null, WC_BOOKINGS_VERSION, true );
                wp_register_script( 'wc_bookings_admin_calendar_gutenberg_js', WC_BOOKINGS_PLUGIN_URL . '/dist/admin-calendar-gutenberg.js', array( 'wc_bookings_admin_js', 'wp-components', 'wp-element' ), WC_BOOKINGS_VERSION, true );
                wp_register_script( 'wc_bookings_admin_calendar_js', WC_BOOKINGS_PLUGIN_URL . '/dist/admin-calendar.js', array(), WC_BOOKINGS_VERSION, true );
                wp_register_script( 'wc_bookings_admin_store_availability_js', WC_BOOKINGS_PLUGIN_URL . '/dist/admin-store-availability.js', array( 'wc_bookings_admin_js', 'wp-components', 'wp-element' ), WC_BOOKINGS_VERSION, true );
                $params = array(
                    'i18n_remove_person'     => esc_js( __( 'Are you sure you want to remove this person type?', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
                    'nonce_unlink_person'    => wp_create_nonce( 'unlink-bookable-person' ),
                    'nonce_add_person'       => wp_create_nonce( 'add-bookable-person' ),
                    'i18n_remove_resource'   => esc_js( __( 'Are you sure you want to remove this resource?', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
                    'nonce_delete_resource'  => wp_create_nonce( 'delete-bookable-resource' ),
                    'nonce_add_resource'     => wp_create_nonce( 'add-bookable-resource' ),
                    'i18n_minutes'           => esc_js( __( 'minutes', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
                    'i18n_hours'             => esc_js( __( 'hours', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
                    'i18n_days'              => esc_js( __( 'days', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
                    'i18n_new_resource_name' => esc_js( __( 'Enter a name for the new resource', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
                    'post'                   => isset( $post->ID ) ? $post->ID : '',
                    'plugin_url'             => WC()->plugin_url(),
                    'ajax_url'               => admin_url( 'admin-ajax.php' ),
                    'calendar_image'         => WC_BOOKINGS_PLUGIN_URL . '/dist/images/calendar.png',
                    'i18n_view_details'      => esc_js( __( 'View details', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
                    'i18n_customer'          => esc_js( __( 'Customer', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
                    'i18n_resource'          => esc_js( __( 'Resource', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
                    'i18n_persons'           => esc_js( __( 'Persons', 'marketking-multivendor-marketplace-for-woocommerce' ) ),
                    'bookings_version'       => WC_BOOKINGS_VERSION,
                    'bookings_db_version'    => WC_BOOKINGS_DB_VERSION,
                );

                wp_localize_script( 'wc_bookings_admin_js', 'wc_bookings_admin_js_params', $params );

                $params = array(
                    'nonce_add_store_availability_rule'     => wp_create_nonce( 'add-store-availability-rule' ),
                    'nonce_get_store_availability_rules'    => wp_create_nonce( 'get-store-availability-rules' ),
                    'nonce_update_store_availability_rule'  => wp_create_nonce( 'update-store-availability-rule' ),
                    'nonce_delete_store_availability_rules' => wp_create_nonce( 'delete-store-availability-rules' ),
                    'ajax_url'                              => WC()->ajax_url(),
                );

                wp_localize_script( 'wc_bookings_admin_store_availability_js', 'wc_bookings_admin_store_availability_js_params', $params );
            });

    
    
            
        }
        */

        // All Products for WooCommerce Subscriptions
        if (intval(get_option( 'marketking_enable_subscriptions_setting', 0 )) === 1){
            if (class_exists('WC_Subscriptions')){
                if (defined('WCS_ATT_VERSION')){

                    $attplugin = WCS_ATT::instance();
                    $attplugin->includes();
                    $attplugin->admin_includes();
                    require_once( WCS_ATT_ABSPATH . 'includes/admin/class-wcs-att-admin-notices.php' );
                    require_once( WCS_ATT_ABSPATH . 'includes/admin/class-wcs-att-admin.php' );
                    WCS_ATT_Admin::admin_init();

                    add_action('wp_print_styles', function(){
                        $add_scripts             = true;
                        $writepanel_dependencies = array( 'jquery', 'jquery-ui-datepicker', 'wc-admin-meta-boxes', 'wc-admin-product-meta-boxes' );

                        wp_register_style( 'wcsatt-writepanel-css', WCS_ATT()->plugin_url() . '/assets/css/admin/meta-boxes.css', array( 'woocommerce_admin_styles' ), WCS_ATT::VERSION );
                        wp_style_add_data( 'wcsatt-writepanel-css', 'rtl', 'replace' );
                        wp_enqueue_style( 'wcsatt-writepanel-css' );

                        wp_register_style( 'wcsatt-admin-css', WCS_ATT()->plugin_url() . '/assets/css/admin/admin.css', array(), WCS_ATT::VERSION );
                        wp_enqueue_style( 'wcsatt-admin-css' );

                    });

                    add_action('wp_print_scripts', function(){

                        $post = '';
                        $add_scripts             = true;
                        $suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                        $writepanel_dependencies = array( 'jquery', 'jquery-ui-datepicker', 'wc-admin-meta-boxes', 'wc-admin-product-meta-boxes' );

                        wp_register_script( 'wcsatt-writepanel', WCS_ATT()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', $writepanel_dependencies, WCS_ATT::VERSION );

                        wp_enqueue_script( 'wcsatt-writepanel' );

                        $params = array(
                            'add_subscription_scheme_nonce'      => wp_create_nonce( 'wcsatt_add_subscription_scheme' ),
                            'subscription_lengths'               => wcs_get_subscription_ranges(),
                            'i18n_do_no_sync'                    => __( 'Disabled', 'woocommerce-all-products-for-subscriptions' ),
                            'i18n_inherit_option'                => __( 'Inherit from product', 'woocommerce-all-products-for-subscriptions' ),
                            'i18n_inherit_option_variable'       => __( 'Inherit from chosen variation', 'woocommerce-all-products-for-subscriptions' ),
                            'i18n_override_option'               => __( 'Override product', 'woocommerce-all-products-for-subscriptions' ),
                            'i18n_override_option_variable'      => __( 'Override all variations', 'woocommerce-all-products-for-subscriptions' ),
                            'i18n_discount_description'          => __( 'Discount to apply to the product when this plan is selected.', 'woocommerce-all-products-for-subscriptions' ),
                            'i18n_discount_description_variable' => __( 'Discount to apply to the chosen variation when this plan is selected.', 'woocommerce-all-products-for-subscriptions' ),
                            'is_onboarding'                      => isset( $_GET[ 'wcsatt_onboarding' ] ) ? 'yes' : 'no',
                            'wc_ajax_url'                        => admin_url( 'admin-ajax.php' ),
                            'post_id'                            => is_object( $post ) ? $post->ID : '',
                        );

                        wp_localize_script( 'wcsatt-writepanel', 'wcsatt_admin_params', $params );
                        
                    });


                }
            }
        }


        // WooCommerce Subscription Downloads
        /* NOT FULLY WORKING YET because the subscriptions search on products page works via WPDB and shows all subs not just the vendor's own
        if (intval(get_option( 'marketking_enable_subscriptions_setting', 0 )) === 1){
            if (class_exists('WC_Subscriptions')){
                if (defined('WC_SUBSCRIPTION_DOWNLOADS_VERSION')){

                    // we use woocommerce-subscription-downloads directly for the folder name, won't work with other folder names
                    include( MARKETKINGCORE_DIR . '../woocommerce-subscription-downloads/includes/class-wc-subscription-downloads-products.php');
                    include( MARKETKINGCORE_DIR . '../woocommerce-subscription-downloads/includes/class-wc-subscription-downloads-ajax.php');

                    add_action('wp_print_scripts', function(){
                        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                        $pluginurl = plugins_url('../../../woocommerce-subscription-downloads/assets/js/admin/writepanel'. $suffix . '.js', __FILE__);

                        wp_enqueue_script( 'wc_subscription_downloads_writepanel', $pluginurl, array( 'ajax-chosen', 'chosen' ), WC_SUBSCRIPTION_DOWNLOADS_VERSION, true );

                        wp_localize_script(
                            'wc_subscription_downloads_writepanel',
                            'wc_subscription_downloads_product',
                            array(
                                'ajax_url' => admin_url( 'admin-ajax.php' ),
                                'security' => wp_create_nonce( 'search-products' ),
                            )
                        );
                    });
                }
            }
        }
        */

        // WooCommerce Subscriptions
        if (intval(get_option( 'marketking_enable_subscriptions_setting', 0 )) === 1){

            if (class_exists('WC_Subscriptions')){

                if(!function_exists('admin_script_parameters2')){ 

                    function admin_script_parameters2( $script_parameters ) {

                        $billing_period_strings = WC_Subscriptions_Synchroniser::get_billing_period_ranges();

                        $script_parameters['syncOptions'] = array(
                            'week'  => $billing_period_strings['week'],
                            'month' => $billing_period_strings['month'],
                            'year'  => WC_Subscriptions_Synchroniser::get_year_sync_options(),
                        );

                        return $script_parameters;
                    } 
                }

                WC_Subscriptions_Admin::init();


                add_action('wp_print_styles', function(){

                    wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_Subscriptions_Core_Plugin::instance()->get_plugin_version() );
                    wp_enqueue_style( 'woocommerce_subscriptions_admin', WC_Subscriptions_Core_Plugin::instance()->get_subscriptions_core_directory_url( 'assets/css/admin.css' ), array( 'woocommerce_admin_styles' ), WC_Subscriptions_Core_Plugin::instance()->get_plugin_version() );
                    wp_enqueue_style( 'wc_subscriptions_statuses_admin', WC_Subscriptions_Core_Plugin::instance()->get_subscriptions_core_directory_url( 'assets/css/admin-order-statuses.css' ), array( 'woocommerce_admin_styles' ), WC_Subscriptions_Core_Plugin::instance()->get_plugin_version() );
                });

                add_action('wp_print_scripts', function(){
                    global $post;

                    $dependencies = array( 'jquery' );

                    $woocommerce_admin_script_handle     = 'wc-admin-meta-boxes';
                    $trashing_subscription_order_warning = __( 'Trashing this order will also trash the subscriptions purchased with the order.', 'woocommerce-subscriptions' );

                    $dependencies[] = $woocommerce_admin_script_handle;
                    $dependencies[] = 'wc-admin-product-meta-boxes';
                    $dependencies[] = 'wc-admin-variation-meta-boxes';

                    $script_params = array(
                        'productType'                 => WC_Subscriptions_Core_Plugin::instance()->get_product_type_name(),
                        'trialPeriodSingular'         => wcs_get_available_time_periods(),
                        'trialPeriodPlurals'          => wcs_get_available_time_periods( 'plural' ),
                        'subscriptionLengths'         => wcs_get_subscription_ranges(),
                        'trialTooLongMessages'        => esc_html__('The trial period cannot exceed the maximum','marketking-multivendor-marketplace-for-woocommerce'),
                        'bulkEditPeriodMessage'       => __( 'Enter the new period, either day, week, month or year:', 'woocommerce-subscriptions' ),
                        'bulkEditLengthMessage'       => __( 'Enter a new length (e.g. 5):', 'woocommerce-subscriptions' ),
                        'bulkEditIntervalhMessage'    => __( 'Enter a new interval as a single number (e.g. to charge every 2nd month, enter 2):', 'woocommerce-subscriptions' ),
                        'bulkDeleteOptionLabel'       => __( 'Delete all variations without a subscription', 'woocommerce-subscriptions' ),
                        'oneTimeShippingCheckNonce'   => wp_create_nonce( 'one_time_shipping' ),
                        'productHasSubscriptions'     => ! wcs_is_large_site() && wcs_get_subscriptions_for_product( isset($post->ID) ? $post->ID : '', 'ids', array( 'limit' => 1 ) ) ? 'yes' : 'no',
                        'productTypeWarning'          => __( 'The product type can not be changed because this product is associated with subscriptions.', 'woocommerce-subscriptions' ),
                        'isLargeSite'                 => wcs_is_large_site(),
                        'nonce'                       => wp_create_nonce( 'wc_subscriptions_admin' ),
                        'variationDeleteErrorMessage' => __( 'An error occurred determining if that variation can be deleted. Please try again.', 'woocommerce-subscriptions' ),
                        'variationDeleteFailMessage'  => __( 'That variation can not be removed because it is associated with active subscriptions. To remove this variation, please cancel and delete the subscriptions for it.', 'woocommerce-subscriptions' ),
                        'bulkTrashWarning' => __( "You are about to trash one or more orders which contain a subscription.\n\nTrashing the orders will also trash the subscriptions purchased with these orders.", 'woocommerce-subscriptions' ),
                        'trashWarning'      => $trashing_subscription_order_warning,
                        'changeMetaWarning' => __( "WARNING: Bad things are about to happen!\n\nThe payment gateway used to purchase this subscription does not support modifying a subscription's details.\n\nChanges to the billing period, recurring discount, recurring tax or recurring total may not be reflected in the amount charged by the payment gateway.", 'woocommerce-subscriptions' ),
                        'removeItemWarning' => __( 'You are deleting a subscription item. You will also need to manually cancel and trash the subscription on the Manage Subscriptions screen.', 'woocommerce-subscriptions' ),
                        'roundAtSubtotal'   => esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
                        'EditOrderNonce'    => wp_create_nonce( 'woocommerce-subscriptions' ),
                        'postId'            => isset($post->ID) ? $post->ID : '',
                    );

                    $dependencies[] = $woocommerce_admin_script_handle;
                    $dependencies[] = 'wc-admin-order-meta-boxes';

                    if ( wcs_is_woocommerce_pre( '2.6' ) ) {
                        $dependencies[] = 'wc-admin-order-meta-boxes-modal';
                    }

                    $script_params['ajaxLoaderImage'] = WC()->plugin_url() . '/assets/images/ajax-loader.gif';
                    $script_params['ajaxUrl']         = admin_url( 'admin-ajax.php' );
                    $script_params['isWCPre24']       = var_export( wcs_is_woocommerce_pre( '2.4' ), true );

                    wp_enqueue_script( 'woocommerce_subscriptions_admin', WC_Subscriptions_Core_Plugin::instance()->get_subscriptions_core_directory_url( 'assets/js/admin/admin.js' ), $dependencies, filemtime( WC_Subscriptions_Core_Plugin::instance()->get_subscriptions_core_directory( 'assets/js/admin/admin.js' ) ) );
                    wp_localize_script( 'woocommerce_subscriptions_admin', 'WCSubscriptions', admin_script_parameters2($script_params ) );  


                    // if subscription or shop_order page
                    $order_id = sanitize_text_field(marketking()->get_pagenr_query_var());
                    $order = wc_get_order($order_id);
                    if ($order){

                        $subscr = wcs_get_subscription( get_post($order_id) );

                        wp_register_script( 'jstz', WC_Subscriptions_Core_Plugin::instance()->get_subscriptions_core_directory_url( 'assets/js/admin/jstz.min.js' ) );

                        wp_register_script( 'momentjs', WC_Subscriptions_Core_Plugin::instance()->get_subscriptions_core_directory_url( 'assets/js/admin/moment.min.js' ) );

                        wp_enqueue_script( 'wcs-admin-meta-boxes-subscription', WC_Subscriptions_Core_Plugin::instance()->get_subscriptions_core_directory_url( 'assets/js/admin/meta-boxes-subscription.js' ), array( 'wc-admin-meta-boxes', 'jstz', 'momentjs' ), WC_VERSION );

                        wp_localize_script( 'wcs-admin-meta-boxes-subscription', 'wcs_admin_meta_boxes',  array(
                            'i18n_start_date_notice'         => __( 'Please enter a start date in the past.', 'woocommerce-subscriptions' ),
                            'i18n_past_date_notice'          => WCS_Staging::is_duplicate_site() ? __( 'Please enter a date at least 2 minutes into the future.', 'woocommerce-subscriptions' ) : __( 'Please enter a date at least one hour into the future.', 'woocommerce-subscriptions' ),
                            'i18n_next_payment_start_notice' => __( 'Please enter a date after the trial end.', 'woocommerce-subscriptions' ),
                            'i18n_next_payment_trial_notice' => __( 'Please enter a date after the start date.', 'woocommerce-subscriptions' ),
                            'i18n_trial_end_start_notice'    => __( 'Please enter a date after the start date.', 'woocommerce-subscriptions' ),
                            'i18n_trial_end_next_notice'     => __( 'Please enter a date before the next payment.', 'woocommerce-subscriptions' ),
                            'i18n_end_date_notice'           => __( 'Please enter a date after the next payment.', 'woocommerce-subscriptions' ),
                            'process_renewal_action_warning' => __( "Are you sure you want to process a renewal?\n\nThis will charge the customer and email them the renewal order (if emails are enabled).", 'woocommerce-subscriptions' ),
                            'payment_method'                 => is_object($subscr) ? $subscr->get_payment_method() : '',
                            'search_customers_nonce'         => wp_create_nonce( 'search-customers' ),
                            'get_customer_orders_nonce'      => wp_create_nonce( 'get-customer-orders' ),
                            'is_duplicate_site'              => WCS_Staging::is_duplicate_site(),
                        ) );

                        wp_enqueue_script( 'wcs-admin-meta-boxes-order', WC_Subscriptions_Core_Plugin::instance()->get_subscriptions_core_directory_url( 'assets/js/admin/wcs-meta-boxes-order.js' ) );

                        wp_localize_script(
                            'wcs-admin-meta-boxes-order',
                            'wcs_admin_order_meta_boxes',
                            array(
                                'retry_renewal_payment_action_warning' => __( "Are you sure you want to retry payment for this renewal order?\n\nThis will attempt to charge the customer and send renewal order emails (if emails are enabled).", 'woocommerce-subscriptions' ),
                            )
                        );
                    }
                    wp_enqueue_script(
                        'wcs-admin-coupon-meta-boxes',
                        WC_Subscriptions_Core_Plugin::instance()->get_subscriptions_core_directory_url( 'assets/js/admin/meta-boxes-coupon.js' ),
                        array( 'jquery', 'wc-admin-meta-boxes' ),
                        WC_Subscriptions_Core_Plugin::instance()->get_plugin_version()
                    );
                    
                
                });
                
            }
        }

        // Price Based On Country
        if (apply_filters('marketking_enable_pricebasedcountry_integration', true)){

            if ( defined('WCPBC_PLUGIN_FILE') ) {
                include_once plugin_dir_path( WCPBC_PLUGIN_FILE ) . 'includes/class-wcpbc-install.php';
                include_once plugin_dir_path( WCPBC_PLUGIN_FILE ) . 'includes/admin/class-wcpbc-admin-notices.php';
                include_once plugin_dir_path( WCPBC_PLUGIN_FILE ) . 'includes/admin/class-wcpbc-admin.php';
                include_once plugin_dir_path( WCPBC_PLUGIN_FILE ) . 'includes/admin/class-wcpbc-admin-meta-boxes.php';
                include_once plugin_dir_path( WCPBC_PLUGIN_FILE ) . 'includes/admin/class-wcpbc-admin-ads.php';
                WCPBC_Install::init();
                WCPBC_Admin::init();
                WCPBC_Admin_Meta_Boxes::init();
                WCPBC_Admin_Notices::init();
                WCPBC_Admin_Ads::init();

                add_action('wp_print_styles', function(){
                    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                    wp_enqueue_style( 'wwc_price_based_country_admin_styles', WCPBC()->plugin_url() . 'assets/css/admin' . $suffix . '.css', array(), WCPBC()->version );

                });

                add_action('wp_print_scripts', function(){
                    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                    // JS.
                    wp_register_script( 'wc_price_based_country_admin', WCPBC()->plugin_url() . 'assets/js/admin' . $suffix . '.js', array( 'jquery', 'woocommerce_admin', 'accounting' ), WCPBC()->version, true );
                    wp_register_script( 'wc_price_based_country_admin_notices', WCPBC()->plugin_url() . 'assets/js/admin-notices' . $suffix . '.js', array( 'jquery' ), WCPBC()->version, true );
                    wp_register_script( 'wc_price_based_country_admin_system_report', WCPBC()->plugin_url() . 'assets/js/admin-system-report' . $suffix . '.js', array( 'jquery' ), WCPBC()->version, false );

                    wp_localize_script(
                       'wc_price_based_country_admin',
                       'wc_price_based_country_admin_params',
                       array(
                           'ajax_url'                 => admin_url( 'admin-ajax.php' ),
                           'product_type_supported'   => array_keys( wcpbc_product_types_supported() ),
                           'product_type_third_party' => array_keys( wcpbc_product_types_supported( 'third-party' ) ),
                           'is_pro'                   => wcpbc_is_pro() ? '1' : '',
                           'i18n_delete_zone_alert'   => __( 'Are you sure you want to delete this zone? This action cannot be undone', 'woocommerce-product-price-based-on-countries' ),
                           'i18n_default_zone_name'   => __( 'Zone', 'woocommerce-product-price-based-on-countries' ),
                       )
                    );
                    wp_localize_script(
                       'wc_price_based_country_admin_notices',
                       'wc_price_based_country_admin_notices_params',
                       array(
                           'ajax_url' => admin_url( 'admin-ajax.php' ),
                       )
                    );
                    wp_localize_script(
                       'wc_price_based_country_admin_system_report',
                       'wc_price_based_country_admin_system_report_params',
                       array(
                           'ajax_url'                => admin_url( 'admin-ajax.php' ),
                           'remote_addr_check_nonce' => wp_create_nonce( 'remote-addr-check' ),
                           // Translators: PHP Code.
                           'define_constant_alert'   => sprintf( esc_html__( 'Your server does not include the customer IP in HTTP_X_FORWARDED_FOR. Fix it by adding %s to your config.php.', 'woocommerce-product-price-based-on-countries' ), "<code>define( 'WCPBC_USE_REMOTE_ADDR', true );</code>" ),
                           'ip_no_match'             => esc_html__( 'The first IP not empty of your server variables does not match with your real IP.', 'woocommerce-product-price-based-on-countries' ),
                           'geoipdb_required'        => esc_html__( 'The MaxMind GeoIP database is required.', 'woocommerce-product-price-based-on-countries' ),
                       )
                    );

                       wp_enqueue_script( 'wc_price_based_country_admin' );
                       wp_enqueue_script( 'wc_price_based_country_admin_notices' );
                       
       
                });
                
            }
        }



        // WooCommerce Deposits
        /*
        Everything on product page is already working on vendor's side
        What needs to be done is to make the subsequent deposit payments also go to the vendor. Right now tried and it's assigned to the admin by default
        So remaining issues have to do with subsequent deposits
        Payment plan option has been removed via public.js
        /*
        /*
        if (defined('WC_DEPOSITS_VERSION')){
            $dir = plugin_dir_path(WC_DEPOSITS_FILE);

            require_once $dir . 'includes/class-wc-deposits-settings.php';
            require_once $dir . 'includes/class-wc-deposits-plans-admin.php';
            require_once $dir . 'includes/class-wc-deposits-product-admin.php';

            add_action('wp_print_scripts', function(){
                $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                wp_enqueue_script( 'woocommerce-deposits-admin', WC_DEPOSITS_PLUGIN_URL . '/assets/js/admin' . $suffix . '.js', array( 'jquery' ), WC_DEPOSITS_VERSION, true );
            });
        }
        */

        // WooCommerce PDF Vouchers
        if (apply_filters('marketking_enable_woovou_integration', true)){

            if (defined('WOO_VOU_PLUGIN_VERSION')){

                include ( MARKETKINGCORE_DIR . 'public/dashboard/integrations/woo_vou_pdf_vouchers.php' );

                add_action('wp_print_styles', function(){
                    $woo_vou_pdf_vouchers = new Marketking_Woo_Vou;
                    $woo_vou_pdf_vouchers->woo_vou_styles();
                });

                add_action('wp_print_scripts', function(){
                    $woo_vou_pdf_vouchers = new Marketking_Woo_Vou;
                    $woo_vou_pdf_vouchers->woo_vou_scripts();
                });

            }
        }

        // woo 3d viewer
        if(defined('WOO3DV_VERSION')){
            add_filter('marketking_vendor_upload_file_size', function($val){
                return 104857600;
            }, 10, 1);
            add_action('wp_print_styles', function(){
                woo3dv_enqueue_scripts_backend();
            });
            add_action('wp_print_scripts', function(){
                wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
                wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.js' ), array( 'iris' ), false, 1 );
                woo3dv_enqueue_scripts_backend();

                $woo3dv_current_version = get_option('woo3dv_version');
                $settings = get_option( 'woo3dv_settings' );
                $upload_dir = wp_upload_dir();

                wp_enqueue_script( 'woo3dv-backend-model.js',  WP_CONTENT_DIR . '/plugins/woo-3d-viewer/includes/js/woo3dv-backend-model.js', array( 'jquery' ), $woo3dv_current_version );

                wp_localize_script( 'woo3dv-backend-model.js', 'woo3dv',
                    array(
                        'url' => admin_url( 'admin-ajax.php' ),
                        'plugin_url' => plugin_dir_url( dirname( __FILE__ ) ),
                        'upload_dir' => $upload_dir['baseurl'].'/woo3dv/',
                        'shading' => $settings['shading'],
                        'display_mode' => isset($settings['display_mode']) ? $settings['display_mode'] : '3d_model',
                        'display_mode_mobile' => isset($settings['display_mode_mobile']) ? $settings['display_mode_mobile'] : '3d_model',
                        'show_shadow' => $settings['show_shadow'],
                        'shadow_softness' => $settings['shadow_softness'],
                        'show_light_source1' => $settings['show_light_source1'],
                        'show_light_source2' => $settings['show_light_source2'],
                        'show_light_source3' => $settings['show_light_source3'],
                        'show_light_source4' => $settings['show_light_source4'],
                        'show_light_source5' => $settings['show_light_source5'],
                        'show_light_source6' => $settings['show_light_source6'],
                        'show_light_source7' => $settings['show_light_source7'],
                        'show_light_source8' => $settings['show_light_source8'],
                        'show_light_source9' => $settings['show_light_source9'],
                        'show_light_source10' => $settings['show_light_source10'],
                        'show_light_source20' => $settings['show_light_source20'],
                        'show_light_source30' => $settings['show_light_source30'],
                        'show_light_source40' => $settings['show_light_source40'],
                        'show_light_source50' => $settings['show_light_source50'],
                        'show_light_source60' => $settings['show_light_source60'],
                        'show_light_source70' => $settings['show_light_source70'],
                        'show_light_source80' => $settings['show_light_source80'],
                        'show_light_source90' => $settings['show_light_source90'],
                        'show_ground' => $settings['show_ground'],
                        'show_fog' => $settings['show_fog'],
                        'ground_mirror' => $settings['ground_mirror'],
                        'model_default_color' => str_replace( '#', '0x', $settings['model_default_color'] ),
                        'model_default_shininess' => $settings['model_default_shininess'],
                        'model_default_transparency' => $settings['model_default_transparency'],
                        'background1' => str_replace( '#', '0x', $settings['background1']),
                        'grid_color' => str_replace( '#', '0x', $settings['grid_color'] ),
                        'fog_color' => str_replace( '#', '0x', $settings['fog_color'] ),
                        'ground_color' => str_replace( '#', '0x', $settings['ground_color'] ),
                        'auto_rotation' => $settings['auto_rotation'],
                        'auto_rotation_speed' => $settings['auto_rotation_speed'],
                        'auto_rotation_direction' => $settings['auto_rotation_direction'],
                        'default_rotation_x' => $settings['default_rotation_x'],
                        'default_rotation_y' => $settings['default_rotation_y'],
                        'show_grid' => $settings['show_grid'],
                        'file_chunk_size' => $settings['file_chunk_size'],
                        'post_max_size' => ini_get('post_max_size'),
                        'text_not_available' => __('Not available in your browser', 'woo3dv'),
                        'text_model_not_found' => __('Model not found!', 'woo3dv'),
                        'text_enable_preview' => __('Please enable Preview Model in the settings of the plugin', 'woo3dv'),
                        'text_upload_model' => __('Please upload the model first', 'woo3dv'),
                        'text_webm_chrome' => __('WEBM rendering works only in Chrome browser', 'woo3dv'),
                        'text_switch_tabs' => __("Please don't switch to other tabs while rendering", 'woo3dv'),
                        'text_post_max_size' => __('The amount of data we are going to submit is larger than post_max_size in php.ini ('.ini_get('post_max_size').'). Either increase post_max_size value or decrease resolution or quality of gif/video', 'woo3dv'),
                        'text_repairing_model' => __( "Repairing..", 'woo3dv' ),
                        'text_model_repaired' => __( "Repairing.. done!", 'woo3dv' ),
                        'text_model_repair_report' => __( 'Error report:', 'woo3dv' ),
                        'text_model_repair_failed' => __( "Repairing.. fail!", 'woo3dv' ),
                        'text_model_no_repair_needed' => __( 'No errors found.', 'woo3dv' ),
                        'text_model_repair_degenerate_facets' => __( 'Degenerate facets', 'woo3dv' ),
                        'text_model_repair_edges_fixed' => __( 'Edges fixed', 'woo3dv' ),
                        'text_model_repair_facets_removed' => __( 'Facets removed', 'woo3dv' ),
                        'text_model_repair_facets_added' => __( 'Facets added', 'woo3dv' ),
                        'text_model_repair_facets_reversed' => __( 'Facets reversed', 'woo3dv' ),
                        'text_model_repair_backwards_edges' => __( 'Backwards edges', 'woo3dv' ),
        //              'text_upload_model' => __( "Please upload the model first!", 'woo3dv' ),
                        'text_repairing_mtl' => __( 'Can not repair textured models yet!', 'woo3dv' ),
                        'text_repairing_only' => __( 'Can repair only STL and OBJ models', 'woo3dv' ),
                        'text_repairing_alert' => __( "The model will be sent to our server for repair.\nRepairing some models with very faulty geometries may result in broken models.\nClick OK if you agree.", 'woo3dv' ),
                        'text_reducing_model' => __( "Reducing..", 'woo3dv' ),
                        'text_model_reduced' => __( "Reducing.. done!", 'woo3dv' ),
                        'text_model_no_reduction_needed' => __( "No reduction needed", 'woo3dv' ),
                        'text_enter_polygon_cap' => __( "% of triangles to reduce", 'woo3dv' ),
                        'text_reducing_mtl' => __( 'Can not reduce textured models yet!', 'woo3dv' ),
                        'text_reducing_only' => __( 'Can reduce only STL and OBJ models', 'woo3dv' ),
                        'text_reducing_alert' => __( "The model will be sent to our server for polygon reduction.\n Click OK if you agree.", 'woo3dv' ),
                        'upload_file_nonce' => wp_create_nonce( 'woo3dv-file-upload' )
                    )
                );
            });
            add_action( 'admin_enqueue_scripts', 'woo3dv_enqueue_scripts_backend' );
            add_action( 'wp_ajax_woo3dv_handle_upload', 'woo3dv_handle_upload' );
            add_action( 'wp_ajax_woo3dv_handle_process', 'woo3dv_handle_process' );
            add_action( 'wp_ajax_woo3dv_handle_process_check', 'woo3dv_handle_process_check' );
            add_action( 'wp_ajax_woo3dv_handle_zip', 'woo3dv_handle_zip' );
            add_action( 'wp_ajax_woo3dv_save_thumbnail_ajax', 'woo3dv_save_thumbnail_ajax' );
            add_action( 'wp_ajax_nopriv_woo3dv_save_thumbnail_ajax', 'woo3dv_save_thumbnail_ajax' );

            require_once WP_CONTENT_DIR.'/plugins/woo-3d-viewer/includes/woo3dv-admin.php';

        }

                        

        // QR Codes
        if (apply_filters('marketking_enable_qrcodes_integration', true)){
            if (class_exists('WooCommerceQrCodes')){
              
              add_action('wp_print_styles', function(){
                global $WooCommerceQrCodes;
                $wooqr_options = array(
                    'qr_options' => get_option('wooqr_option_name')
                );
                wp_enqueue_style('wcqrc-product', $WooCommerceQrCodes->plugin_url . 'assets/css/wooqr-code.css', array(), $WooCommerceQrCodes->version);
                
                wp_enqueue_style('qrcode-style', $WooCommerceQrCodes->plugin_url . 'assets/admin/css/style.css', array('jquery'),
                    $WooCommerceQrCodes->version);
                  
              });
              add_action('wp_print_scripts', function(){
                global $WooCommerceQrCodes;
                $wooqr_options = array(
                    'qr_options' => get_option('wooqr_option_name')
                );
                  wp_enqueue_script('qrcode-qrcode', $WooCommerceQrCodes->plugin_url . 'assets/common/js/kjua.js', array('jquery'),
                      $WooCommerceQrCodes->version);
                  wp_enqueue_script('qrcode-createqr', $WooCommerceQrCodes->plugin_url . 'assets/common/js/createqr.js', array('jquery'),$WooCommerceQrCodes->version);
                  wp_localize_script( 'qrcode-createqr', 'wooqr_options', $wooqr_options );
              });
            }
        }
        

        // name your price
        if (apply_filters('marketking_enable_nyp_integration', true)){
           if ( function_exists( 'wc_nyp_init' ) ) {
               include_once WC_Name_Your_Price()->plugin_path() . '/includes/admin/class-wc-nyp-admin-system-status.php';
               include_once WC_Name_Your_Price()->plugin_path() . '/includes/admin/meta-boxes/class-wc-nyp-meta-box-product-data.php';
               WC_NYP_Meta_Box_Product_Data::init();


               add_action('wp_print_styles', function(){
                   
               });
               add_action('wp_print_scripts', function(){

                   add_action('wp_footer', function(){
                       ?>
                       <script type="text/javascript">
                           jQuery(document).ready(function(){
                               var woocommerce_nyp_metabox = {
                                 enter_value: "<?php echo __( 'Enter a value', 'wc_name_your_price' ); ?>",
                                 price_adjust: "<?php echo __( 'Enter a value (fixed or %)', 'wc_name_your_price' ); ?>",
                                 simple_types: ['simple', 'subscription', 'bundle', 'composite', 'variation', 'subscription_variation', 'deposit', 'mix-and-match'],
                                 variable_types: ['variable', 'variable-subscription']
                               };
                               <?php 
                               $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
                               $bundle_script = file_get_contents(WC_Name_Your_Price()->plugin_url() . '/assets/js/admin/nyp-metabox' . $suffix . '.js');
                               echo $bundle_script; 
                               ?>
                           });
                       </script>
                       <?php
                   }, 1000);


                   wp_enqueue_script( 'accounting' );
                   wp_enqueue_script( 'woocommerce-nyp' );
               });
           } 
        }
       
        


        // Addons integration only if the product exists (not being added)
        $prod = sanitize_text_field(marketking()->get_pagenr_query_var());

        if ($prod === 'add'){
          global $marketking_product_add_id;
          $prod = $marketking_product_add_id;
        }

        $post = 0;
        if (!empty($prod)){
            $post = get_post($prod);
            $product = wc_get_product($post);
            if (!is_a($post, 'WP_Post')){
                $post = 0;
            }
        }

        // Addons integration only if the product exists (not currently being added)
        if ($post!==0){
           // WooCommerce TM Extra Addons - Incomplete
           if (defined('THEMECOMPLETE_EPO_PLUGIN_FILE')){
              // Globals Admin Interface
              THEMECOMPLETE_EPO_ADMIN_GLOBAL();

              // Admin Interface
              THEMECOMPLETE_EPO_ADMIN(); 

              add_action('wp_print_styles', function(){
               $pluginurl = THEMECOMPLETE_EPO_PLUGIN_URL;
               $ext = ".min";

               if ( THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode == "dev" ) {
                   $ext = "";
               }

               wp_enqueue_style( 'themecomplete-epo-admin', $pluginurl . '/assets/css/admin/tm-epo-admin' . $ext . '.css' );
               THEMECOMPLETE_EPO_ADMIN_GLOBAL()->register_admin_styles( 1 );
                   
              });

              add_action('wp_print_scripts', function(){
                   $pluginurl = THEMECOMPLETE_EPO_PLUGIN_URL;
                   $ext = ".min";

                   if ( THEMECOMPLETE_EPO()->tm_epo_global_js_css_mode == "dev" ) {
                       $ext = "";
                   }
                   $prod = sanitize_text_field(marketking()->get_pagenr_query_var());

                   if ($prod === 'add'){
                     global $marketking_product_add_id;
                     $prod = $marketking_product_add_id;
                   }

                   $post = 0;
                   if (!empty($prod)){
                       $post = get_post($prod);
                       $product = wc_get_product($post);
                       if (!is_a($post, 'WP_Post')){
                           $post = 0;
                       }
                   }

                  wp_register_script( 'themecomplete-epo-admin-metaboxes', $pluginurl . '/assets/js/admin/tm-epo-admin' . $ext . '.js', array( 'jquery' ), THEMECOMPLETE_EPO_VERSION );
                 $params = array(
                  'post_id'                => isset( $post ) ? $post : '',
                  'plugin_url'             => $pluginurl,
                  'ajax_url'               => strtok( admin_url( 'admin-ajax' . '.php' ), '?' ),//WPML 3.3.x fix
                  'add_tm_epo_nonce'       => wp_create_nonce( "add-tm-epo" ),
                  'delete_tm_epo_nonce'    => wp_create_nonce( "delete-tm-epo" ),
                  'check_attributes_nonce' => wp_create_nonce( "check_attributes" ),
                  'load_tm_epo_nonce'      => wp_create_nonce( "load-tm-epo" ),
                  'i18n_no_variations'     => esc_html__( 'There are no saved variations yet.', 'woocommerce-tm-extra-product-options' ),
                  'i18n_max_tmcp'          => esc_html__( 'You cannot add any more extra options.', 'woocommerce-tm-extra-product-options' ),
                  'i18n_remove_tmcp'       => esc_html__( 'Are you sure you want to remove this option?', 'woocommerce-tm-extra-product-options' ),
                  'i18n_missing_tmcp'      => esc_html__( 'Before adding Extra Product Options, add and save some attributes on the Attributes tab.', 'woocommerce-tm-extra-product-options' ),
                  'i18n_fixed_type'        => esc_html__( 'Fixed amount', 'woocommerce-tm-extra-product-options' ),
                  'i18n_percent_type'      => esc_html__( 'Percent of the original price', 'woocommerce-tm-extra-product-options' ),
                  'i18n_error_title'       => esc_html__( 'Error', 'woocommerce-tm-extra-product-options' ),
                 );
                 wp_localize_script( 'themecomplete-epo-admin-metaboxes', 'TMEPOADMINJS', $params );
                 wp_enqueue_script( 'themecomplete-epo-admin-metaboxes' );

                 // integration incomplete
                // THEMECOMPLETE_EPO_ADMIN_GLOBAL()->register_admin_scripts( 1 );
              });

           }


           if (intval(get_option('marketking_enable_addons_setting', 1)) === 1){

             // Plugin Republic Product Addons
             if (class_exists( 'PEWC_Product_Extra_Post_Type' )){

                 add_action('wp_print_styles', function(){

                     $version = defined( 'PEWC_SCRIPT_DEBUG' ) && PEWC_SCRIPT_DEBUG ? time() : PEWC_PLUGIN_VERSION;

                     wp_enqueue_style( 'pewc-admin-style', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/admin-style.css', array(), $version );

                     wp_enqueue_style( 'pewc-dropzone-basic', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/basic.min.css', array(), $version );
                     wp_enqueue_style( 'pewc-dropzone', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/dropzone.min.css', array(), $version );

                 });
                 add_action('wp_print_scripts', function(){
                     $version = defined( 'PEWC_SCRIPT_DEBUG' ) && PEWC_SCRIPT_DEBUG ? time() : PEWC_PLUGIN_VERSION;

                     wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
                     wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.js' ), array( 'iris' ), false, 1 );
                     if( version_compare( $GLOBALS['wp_version'], '5.5', '<' ) ) {
                         wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n',
                             array(
                                 'clear' => __( 'Clear', 'pewc' ),
                                 'defaultString' => __( 'Default', 'pewc' ),
                                 'pick' => __( 'Select Color', 'pewc' ),
                                 'current' => __( 'Current Color', 'pewc' ),
                             )
                         );
                     } else {
                         wp_set_script_translations( 'wp-color-picker' );
                     }

                     $has_migrated = pewc_has_migrated();
                     if( ! $has_migrated ) {
                         $admin_js_file = 'admin-pewc.js';
                     } else {
                         $admin_js_file = 'admin-fields.js';
                     }

                     wp_register_script( 'pewc-admin-script', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/' . $admin_js_file, array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker', 'select2', 'jquery-tiptip', 'wc-enhanced-select' ), $version, true );
                     $params = array(
                         'delete_group'              => __( 'Delete this group?', 'pewc' ),
                         'delete_field'              => __( 'Delete this field? Deleting this field will also delete any conditions associated with it.', 'pewc' ),
                         'delete_option'             => __( 'Delete this option?', 'pewc' ),
                         'checked_label'             => __( 'Checked', 'pewc' ),
                         'condition_continue'    => __( 'This field is used in a condition. Changing its field type may affect the condition. Continue?', 'pewc' ),
                         'copy_label'                    => __( 'copy', 'pewc' ),
                         'select_text'                   => __( ' -- Select a field -- ', 'pewc' ),
                         'load_addons_ajax'      => pewc_enable_ajax_load_addons(),
                         'enable_numeric_options'        => apply_filters( 'pewc_enable_numeric_options', false )
                     );
                     if( class_exists( 'WC' ) ) {
                         $params['placeholder_src'] = wc_placeholder_img_src();
                     }

                     wp_localize_script(
                         'pewc-admin-script',
                         'pewc_obj',
                         $params
                     );

                     wp_enqueue_script( 'pewc-admin-script' );

                     wp_enqueue_media();

                     wp_enqueue_script( 'pewc-dropzone', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/dropzone.js', array( 'jquery' ), $version, false );


                 });

               
             }

             // WooCommerce Product Addons
             if ( defined('WC_PRODUCT_ADDONS_VERSION') ) {
                 include_once (WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/admin/class-wc-product-addons-privacy.php');
                 include_once (WC_PRODUCT_ADDONS_PLUGIN_PATH . '/includes/admin/class-wc-product-addons-admin.php');
                 $GLOBALS['Product_Addon_Admin'] = new WC_Product_Addons_Admin();

                 add_action('wp_print_scripts', function(){
                    wp_enqueue_media();

                     $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

                     wp_register_script( 'woocommerce_product_addons', plugins_url( 'assets/js/admin/admin' . $suffix . '.js', WC_PRODUCT_ADDONS_MAIN_FILE ), array( 'jquery' ), WC_PRODUCT_ADDONS_VERSION, true );

                     $params = array(
                        'ajax_url' => admin_url( 'admin-ajax.php' ),
                        'nonce'    => array(
                            'get_addon_options' => wp_create_nonce( 'wc-pao-get-addon-options' ),
                            'get_addon_field'   => wp_create_nonce( 'wc-pao-get-addon-field' ),
                        ),
                        'i18n'     => array(
                            'required_fields'       => __( 'All fields must have a title and/or option name. Please review the settings highlighted in red border.', 'woocommerce-product-addons' ),
                            'limit_price_range'         => __( 'Limit price range', 'woocommerce-product-addons' ),
                            'limit_quantity_range'      => __( 'Limit quantity range', 'woocommerce-product-addons' ),
                            'limit_character_length'    => __( 'Limit character length', 'woocommerce-product-addons' ),
                            'restrictions'              => __( 'Restrictions', 'woocommerce-product-addons' ),
                            'confirm_remove_addon'      => __( 'Are you sure you want remove this add-on field?', 'woocommerce-product-addons' ),
                            'confirm_remove_option'     => __( 'Are you sure you want delete this option?', 'woocommerce-product-addons' ),
                            'add_image_swatch'          => __( 'Add Image Swatch', 'woocommerce-product-addons' ),
                            'add_image'                 => __( 'Add Image', 'woocommerce-product-addons' ),
                        ),
                     );

                     wp_localize_script( 'woocommerce_product_addons', 'wc_pao_params', apply_filters( 'wc_pao_params', $params ) );

                     wp_enqueue_script( 'woocommerce_product_addons' );
                 });

                 add_action('wp_print_styles', function(){
                    wp_enqueue_style( 'woocommerce_product_addons_css', WC_PRODUCT_ADDONS_PLUGIN_URL . '/assets/css/admin/admin.css', array(), WC_PRODUCT_ADDONS_VERSION );
                 });


                 
             } 
           }


           // WooCommerce Measurement Price Calculator
           if (apply_filters('marketking_enable_measurementprice_integration', true)){

               if (class_exists('WC_Measurement_Price_Calculator_Loader')){
                   include_once( wc_measurement_price_calculator()->get_plugin_path() . '/src/admin/post-types/writepanels/writepanels-init.php' );

                   add_action('wp_print_scripts', function(){

                        $prod = sanitize_text_field(marketking()->get_pagenr_query_var());

                        if ($prod === 'add'){
                          global $marketking_product_add_id;
                          $prod = $marketking_product_add_id;
                        }

                        $post = 0;
                        if (!empty($prod)){
                            $post = get_post($prod);
                            $product = wc_get_product($post);
                        }

                        if(!function_exists('wc_measurement_price_calculator_is_variable_product_with_stock_managed2')){ 

                           function wc_measurement_price_calculator_is_variable_product_with_stock_managed2( $product ) {
                               if ( ! $product instanceof \WC_Product || ! $product->is_type( 'variable' ) ) {
                                   return false;
                               }
                               foreach ( $product->get_children() as $variation_id ) {
                                   $variation = wc_get_product( $variation_id );
                                   if ( $variation && $variation->get_manage_stock() ) {
                                       return true;
                                   }
                               }
                               return false;
                           }
                       }
                       wp_enqueue_script( 'wc-price-calculator-admin', wc_measurement_price_calculator()->get_plugin_url() . '/assets/js/admin/wc-measurement-price-calculator.min.js', array(), \WC_Measurement_Price_Calculator::VERSION );

                       // Variables for JS scripts
                       $wc_price_calculator_admin_params = [
                          'woocommerce_currency_symbol'            => get_woocommerce_currency_symbol(),
                          'woocommerce_weight_unit'                => 'no' !== get_option( 'woocommerce_enable_weight', true ) ? get_option( 'woocommerce_weight_unit' ) : '',
                          'pricing_rules_enabled_notice'           => __( 'Cannot edit price while a pricing table is active', 'woocommerce-measurement-price-calculator' ),
                          'is_variable_product_with_stock_managed' => wc_measurement_price_calculator_is_variable_product_with_stock_managed2( $product ),
                       ];

                       wp_localize_script( 'wc-price-calculator-admin', 'wc_price_calculator_admin_params', $wc_price_calculator_admin_params );
                   });

               }
           }

           // Waitlists
           if (apply_filters('marketking_enable_waitlist_integration', true)){

               if (class_exists('WooCommerce_Waitlist_Plugin')){
                 
                 add_action('wp_print_styles', function(){

                   wp_enqueue_style( 'wcwl_admin', WCWL_ENQUEUE_PATH . '/includes/css/src/wcwl_admin.min.css', array(), WCWL_VERSION );
                     
                 });
                 add_action('wp_print_scripts', function(){

                   wp_enqueue_script( 'wcwl_admin_custom_tab', WCWL_ENQUEUE_PATH . '/includes/js/src/wcwl_admin_custom_tab.min.js', array(), WCWL_VERSION, true );
                   $data = array(
                     'admin_email'            => get_option( 'woocommerce_email_from_address' ),
                     'invalid_email'          => __( 'One or more emails entered appear to be invalid', 'woocommerce-waitlist' ),
                     'add_text'               => __( 'Add', 'woocommerce-waitlist' ),
                     'no_users_text'          => __( 'No users selected', 'woocommerce-waitlist' ),
                     'no_action_text'         => __( 'No action selected', 'woocommerce-waitlist' ),
                     'view_profile_text'      => __( 'View User Profile', 'woocommerce-waitlist' ),
                     'go_text'                => __( 'Go', 'woocommerce-waitlist' ),
                     'update_button_text'     => __( 'Update Options', 'woocommerce-waitlist' ),
                     'update_waitlist_notice' => __( 'Waitlists may be appear inaccurate due to an update to variations. Please update the product or refresh the page to update waitlists', 'woocommerce-waitlist' ),
                     'current_user'           => get_current_user_id(),
                   );
                   wp_localize_script( 'wcwl_admin_custom_tab', 'wcwl_tab', $data );
                 });

                 // could use              require_once WCWL_ENQUEUE_PATH.'/classes/admin/product-tab/class-pie-wcwl-custom-admin-tab.php';
                 // but it is dependent on http/
                 
                 // woocommerce waitlist integration
                 require_once WP_CONTENT_DIR.'/plugins/woocommerce-waitlist/classes/admin/product-tab/class-pie-wcwl-custom-admin-tab.php';
                 $tab = new Pie_WCWL_Custom_Tab( $product );
                 $tab->init();
               }
           }

        }

        // WooCommerce Warranty https://woocommerce.com/products/warranty-requests/
        if (apply_filters('marketking_enable_warranty_integration', true)){

            if (defined('WOOCOMMERCE_WARRANTY_VERSION')){

                $warranty = new WooCommerce_Warranty();

                require_once $warranty::$includes_path . '/class-warranty-coupons.php';
                require_once $warranty::$includes_path . '/class-warranty-settings.php';
                require_once $warranty::$includes_path . '/class-warranty-admin.php';

                $admin = new Warranty_Admin;
                $admin->init();


                add_action('wp_print_styles', function(){

                    global $woocommerce;

                    wp_enqueue_style( 'select2' );
                    wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), WOOCOMMERCE_WARRANTY_VERSION );

                    wp_enqueue_style( 'warranty_admin_css', plugins_url( 'assets/css/admin.css', WooCommerce_Warranty::$plugin_file ), array(), WOOCOMMERCE_WARRANTY_VERSION );

                });
                add_action('wp_print_scripts', function(){

                    global $woocommerce;

                    wp_enqueue_script( 'selectWoo' );
                    wp_enqueue_script( 'wc-enhanced-select' );

                    wp_enqueue_script( 'user-email-search', plugins_url( 'assets/js/user-email-search.js', WooCommerce_Warranty::$plugin_file ), array( 'wc-enhanced-select' ), WOOCOMMERCE_WARRANTY_VERSION );

                    add_thickbox();
                    wp_enqueue_media();

                    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                    wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC()->version, true );


                    wp_enqueue_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI.min.js', array( 'jquery' ), '2.70', true );
                    wp_enqueue_script( 'jquery-tiptip' );
                    wp_enqueue_script( 'jquery-ui-sortable' );
                    wp_enqueue_script( 'jquery-ui-core', null, array( 'jquery' ) );

                    $js = '
                            jQuery(".warranty-delete").click(function(e) {
                                return confirm("' . __( 'Do you really want to delete this request?', 'wc_warranty' ) . '");
                            });
                            var tiptip_args = {
                                "attribute" : "data-tip",
                                "fadeIn" : 50,
                                "fadeOut" : 50,
                                "delay" : 200
                            };
                            $(".tips, .help_tip").tipTip( tiptip_args );
                        ';
                    ?>
                    <script>
                        var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>"
                    </script>
                    <?php

                    if ( function_exists( 'wc_enqueue_js' ) ) {
                        wc_enqueue_js( $js );
                    } else {
                        $woocommerce->add_inline_js( $js );
                    }

                    wp_enqueue_script( 'warranties_list', plugins_url( 'assets/js/list.js', WooCommerce_Warranty::$plugin_file ), array( 'jquery' ), WOOCOMMERCE_WARRANTY_VERSION, true );

                    wp_enqueue_script( 'jquery-ui' );
                    wp_enqueue_script( 'jquery-ui-sortable' );
                    wp_enqueue_script( 'warranty_form_builder', plugins_url( 'assets/js/form-builder.js', WooCommerce_Warranty::$plugin_file ), array(), WOOCOMMERCE_WARRANTY_VERSION, true );

                    $data = array(
                        'help_img_url' => plugins_url() . '/woocommerce/assets/images/help.png',
                        'tips'         => array_map( 'wc_sanitize_tooltip', WooCommerce_Warranty::$tips ),
                    );

                    wp_localize_script( 'warranty_form_builder', 'WFB', $data );
                    wp_enqueue_script( 'warranty_shop_order', plugins_url( 'assets/js/orders.js', WooCommerce_Warranty::$plugin_file ), array( 'jquery' ), WOOCOMMERCE_WARRANTY_VERSION, true );

                    wp_enqueue_style( 'wc-form-builder', plugins_url( 'assets/css/form-builder.css', WooCommerce_Warranty::$plugin_file ), array(), WOOCOMMERCE_WARRANTY_VERSION );

                    $js = '
                            if ( jQuery( \'select.multi-select2\' ).length ) {
                                jQuery( \'select.multi-select2\' ).selectWoo();
                            }
                    ';

                    if ( function_exists( 'wc_enqueue_js' ) ) {
                        wc_enqueue_js( $js );
                    } else {
                        $woocommerce->add_inline_js( $js );
                    }


                });

               
            } 
        } 

        do_action('marketking_dashboard_before_scripts_styles');

        wp_print_styles(); 
        
        wp_print_scripts(); 


        if (defined('MARKETKINGPRO_DIR')){
            if (intval(get_option('marketking_enable_colorscheme_setting', 1)) === 1) { 
                if (intval(get_option('marketking_change_color_scheme_setting', 0)) === 1){

                    echo marketkingpro()->get_page('colorscheme');

                }
            }
        }

        $user_id = get_current_user_id();
        if (marketking()->is_vendor_team_member()){
            $user_id = marketking()->get_team_member_parent();
        }
        
        // load profile pic in user avatar if it is set
        $profile_pic = get_user_meta($user_id,'marketking_profile_logo_image', true);

        if (!empty($profile_pic)){
          $profile_pic = marketking()->get_resized_image($profile_pic,'thumbnail');
            ?>
            <style type="text/css">
                .nk-header-tools .user-avatar, .simplebar-content .user-avatar{
                    background-size: contain !important;
                }

                
            </style>
            <?php
        }

        // hide virtual / downloadable if enabled force default
        if ( is_user_logged_in() ) {
            $vendor_id = get_current_user_id();
            if (marketking()->is_vendor_team_member()){
                $vendor_id = marketking()->get_team_member_parent();
            }

            $all_virtual = marketking()->vendor_all_products_virtual($vendor_id);
            $all_downloadable = marketking()->vendor_all_products_downloadable($vendor_id);      
            $all_individually = marketking()->vendor_all_products_individually($vendor_id);      

            ?>
            <style type="text/css">
              <?php
              if (intval($all_virtual ) === 1){
                ?>
                label[for="_virtual"]{
                  display:none !important;
                }
                <?php
              }

              if (intval($all_downloadable ) === 1){
                ?>
                label[for="_downloadable"]{
                  display:none !important;
                }
                <?php
              }

              if (intval($all_individually ) === 1){
                ?>
                .inventory_sold_individually{
                  display:none !important;
                }
                <?php
              }
              ?>

            </style>
            <?php
        }

        // WOOCS set currency in vendor dashboard
        if (class_exists('WOOCS')){
          global $WOOCS;
          $default = $WOOCS->default_currency;
          $WOOCS->set_currency($default);
        }

        // woodmart error fix frequently bought together
        // fix not working well, creating other issues
        /*
        if (!function_exists('convert_to_screen')){
            function convert_to_screen(){
            }
        }
        */ 
        add_filter( 'woocommerce_product_data_tabs', function($tabs){
            if (isset($tabs['woodmart_bought_together'])){
                unset($tabs['woodmart_bought_together']);
            }
            return $tabs;
        }, 10, 1);

        

        do_action('marketking_dashboard_head');

        ?>
    </head>
    
    <?php
    // get logo
    $logo_src = get_option('marketking_logo_setting','');
    // if no logo configured, set default marketking logo
    if ($logo_src === ''){
        $logo_src = MARKETKINGCORE_URL. 'includes/assets/images/marketkinglogoblack.png';
    }

    // User is logged in, but not a vendor -> show logout button

    if ( is_user_logged_in() ) {
        $vendor_id = get_current_user_id();
        if (marketking()->is_vendor_team_member()){
            $vendor_id = marketking()->get_team_member_parent();
        }

        // check if user is vendor
        $is_vendor = get_user_meta($vendor_id,'marketking_group', true);
        $is_approved = get_user_meta($vendor_id,'marketking_account_approved', true);
        if ($is_vendor === 'none' || empty($is_vendor) || ($is_approved === 'no')){
            ?>
                <body class="nk-body npc-default pg-auth no-touch nk-nio-theme">

                    <div class="nk-app-root">
                        <!-- main @s -->
                        <div class="nk-main ">
                            <!-- wrap @s -->
                            <div class="nk-wrap nk-wrap-nosidebar">
                                <!-- content @s -->
                                <div class="nk-content ">
                                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                                        <div class="brand-logo pb-4 text-center brand-logo-padding">
                                            <a href="<?php echo esc_attr(get_home_url());?>"><img class="logo-dark logo-img logo-img-lg" src="<?php echo esc_url($logo_src); ?>" alt="logo-dark"></a>
                                        </div>
                                        <div class="card">
                                            <div class="card-inner card-inner-lg">
                                                    <div class="example-alert">
                                                        <div class="alert alert-danger alert-icon alert-dismissible">
                                                            <em class="icon ni ni-cross-circle"></em> <strong><?php esc_html_e('Invalid Account','marketking-multivendor-marketplace-for-woocommerce');?></strong>! <?php         echo '<span class="marketking_already_logged_in_message">';
                esc_html_e('Your current account is not a vendor or has been deactivated. To login as a vendor, please logout first. ','marketking-multivendor-marketplace-for-woocommerce');

                                                            ?> <button class="close" data-dismiss="alert"></button></div>
                                                    </div><br />
                                                <?php
                                                if (apply_filters('marketking_show_dashboard_logout_button', true)){
                                                    ?>
                                                    <a href="<?php echo esc_url(wp_logout_url()); ?>">
                                                        <button id="wp-submit" type="submit" value="Login" name="wp-submit" class="btn btn-lg btn-primary btn-block"><?php esc_html_e('Log out','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                                    </a>
                                                    <?php
                                                }

                                                do_action('marketking_dashboard_after_logout_button');
                                                ?>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- wrap @e -->
                            </div>
                            <!-- content @e -->
                        </div>
                        <!-- main @e -->
                    </div>


                </body>

            <?php

        } else {

            // User is logged in, and a vendor -> Show dashboard
            include(apply_filters('marketking_dashboard_template', MARKETKINGCORE_DIR . 'public/dashboard/marketking-dashboard.php'));

        }

    } else {
        
            // User is not logged in -> Show login page

            ?>
            <body class="nk-body npc-default pg-auth no-touch nk-nio-theme">

            <div class="nk-app-root">
                <!-- main @s -->
                <div class="nk-main ">
                    <!-- wrap @s -->
                    <div class="nk-wrap nk-wrap-nosidebar">
                        <!-- content @s -->
                        <div class="nk-content ">
                            <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                                <div class="brand-logo pb-4 text-center brand-logo-padding">
                                    <a href="<?php echo esc_attr(get_home_url());?>"><img class="logo-dark logo-img logo-img-lg" src="<?php echo esc_url($logo_src); ?>" alt="logo-dark"></a>
                                </div>
                                <div class="card">
                                    <div class="card-inner card-inner-lg">
                                        <?php 
                                        if (isset($_GET['reason'])){
                                            $reason = sanitize_text_field($_GET['reason']);
                                            if ($reason === 'invalid_username'){
                                                $reason = esc_html__('Username is invalid','marketking-multivendor-marketplace-for-woocommerce');
                                            }
                                            if ($reason === 'empty_username'){
                                                $reason = esc_html__('Username is empty','marketking-multivendor-marketplace-for-woocommerce');
                                            }
                                            if ($reason === 'incorrect_password'){
                                                $reason = esc_html__('Password is incorrect','marketking-multivendor-marketplace-for-woocommerce');
                                            }
                                            if ($reason === 'empty_password'){
                                                $reason = esc_html__('Password is empty','marketking-multivendor-marketplace-for-woocommerce');
                                            }

                                            ?>                                        
                                            <div class="example-alert">
                                                <div class="alert alert-danger alert-icon alert-dismissible">
                                                    <em class="icon ni ni-cross-circle"></em> <strong><?php esc_html_e('Login failed','marketking-multivendor-marketplace-for-woocommerce');?></strong>! <?php echo esc_html($reason);?> <button class="close" data-dismiss="alert"></button></div>
                                            </div><br />
                                            <?php
                                        }
                                        ?>
                                        <div class="nk-block-head">
                                            <div class="nk-block-head-content">
                                                <h4 class="nk-block-title"><em class="icon ni ni-bag-fill"></em>&nbsp;<?php esc_html_e('Sign-In','marketking-multivendor-marketplace-for-woocommerce');?></h4>
                                                <div class="nk-block-des">
                                                    <p><?php esc_html_e('Access your vendor dashboard and data.','marketking-multivendor-marketplace-for-woocommerce');?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <form name="loginform" id="loginform" action="<?php echo site_url( '/wp-login.php' ); ?>" method="post">
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="default-01"><?php esc_html_e('Email or Username','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                </div>
                                                <input type="text" class="form-control form-control-lg" id="user_login" placeholder="<?php esc_attr_e('Enter your email address or username','marketking-multivendor-marketplace-for-woocommerce');?>" name="log" value="<?php echo apply_filters('marketking_default_vendor_username', '');?>">
                                            </div>
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="password"><?php esc_html_e('Password','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                    <a class="link link-primary link-sm" href="<?php echo wp_lostpassword_url(); ?>"><?php esc_html_e('Forgot password?','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                                </div>
                                                <div class="form-control-wrap">
                                                    <a href="#" class="form-icon form-icon-right passcode-switch" data-target="user_pass">
                                                        <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                        <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                                    </a>
                                                    <input type="password" class="form-control form-control-lg" id="user_pass" placeholder="<?php esc_attr_e('Enter your password','marketking-multivendor-marketplace-for-woocommerce');?>" name="pwd" value="<?php echo apply_filters('marketking_default_vendor_password', '');?>">
                                                    <input type="hidden" name="marketking_dashboard_login" value="1">
                                                    <input type="hidden" value="<?php echo esc_attr( trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))) ); ?>" name="redirect_to">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button id="wp-submit" type="submit" value="Login" name="wp-submit" class="btn btn-lg btn-primary btn-block"><?php esc_html_e('Sign in','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                            </div>
                                        </form>
                                        <div class="form-note-s2 text-center pt-4"> <?php esc_html_e('New on our platform?','marketking-multivendor-marketplace-for-woocommerce');?> <a href="<?php 
                                            if (get_option( 'marketking_vendor_registration_setting', 'myaccount' ) === 'separate'){
                                                $page = get_option('marketking_vendor_registration_page_setting');
                                                $link = esc_attr(get_permalink($page)).'?redir=1';
                                            } else {
                                                $link = esc_attr(get_permalink( wc_get_page_id( 'myaccount' ) ).'?redir=1');
                                            }
                                            echo esc_attr(apply_filters('marketking_become_a_vendor_dashboard_link', $link));

                                         ?>"><?php esc_html_e('Become a Vendor','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- wrap @e -->
                    </div>
                    <!-- content @e -->
                </div>
                <!-- main @e -->
            </div>

            <?php
            do_action('marketking_dashboard_footer');

            }
            ?>
        </body>

    
</html>
    