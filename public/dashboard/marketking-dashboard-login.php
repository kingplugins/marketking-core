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
          $favicon_setting = plugins_url('../../includes/assets/images/marketking-icon5.svg', __FILE__);
        }

        ?>
        <link rel="shortcut icon" href="<?php echo apply_filters('marketking_favicon_url', $favicon_setting);?>"/>
        <title><?php 

        // esc_html_e('Vendor Dashboard','marketking-multivendor-marketplace-for-woocommerce');
        $dashboardid = intval(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true));

        echo esc_html(get_the_title($dashboardid));

        ?></title>
        <?php

        global $marketking_public;

        add_action('wp_print_styles', array($marketking_public, 'enqueue_dashboard_resources'));
        add_action('wp_print_scripts', array($marketking_public, 'enqueue_dashboard_resources'));

        // enqueue media uploader
        require_once ABSPATH . WPINC . '/media-template.php';
        add_action( 'wp_print_scripts', 'wp_print_media_templates' );
        add_action( 'marketking_dashboard_footer', 'wp_print_media_templates' );


        add_action('wp_print_styles', function(){
            global $wp_styles;
            $wp_styles->queue = array('marketking_dashboard','media-views','imgareaselect', 'wc-country-select', 'select2', 'selectWoo');

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
            $wp_scripts->queue = array('marketking_dashboard_bundle','marketking_dashboard_scripts','marketking_dashboard_chart','marketking_public_script', 'marketking_dashboard_messages', 'marketking_dashboard_chart_sales', 'dataTablesButtons', 'jszip', 'pdfmake', 'dataTablesButtonsHTML', 'dataTablesButtonsPrint', 'dataTablesButtonsColvis', 'vfsfonts', 'media-editor','media-audiovideo', 'wc-country-select', 'select2', 'selectWoo');

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
        if (get_query_var('dashpage') === 'edit-product' || get_query_var('dashpage') === 'manage-order' || get_query_var('dashpage') === 'edit-coupon'|| get_query_var('dashpage') === 'import-products'|| get_query_var('dashpage') === 'export-products'){
            add_action('wp_print_styles', array($marketking_public, 'enqueue_dashboard_woocommerce_resources'));
            add_action('wp_print_scripts', array($marketking_public, 'enqueue_dashboard_woocommerce_resources'));                 
        }

        // Integrations
        // FooEvents
        if (class_exists('FooEvents')){
            add_action('wp_print_styles', function(){
                $foo = new FooEvents;
                $foo->plugin_init();
                $foo->register_styles();
                $foo->register_scripts();
            });
            add_action('wp_print_scripts', function(){
                $foo = new FooEvents;
                $foo->plugin_init();
                $foo->register_styles();
                $foo->register_scripts();
            });
        } 

        // WooCommerce PDF Vouchers
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

        // QR Codes
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

                     wp_register_script( 'woocommerce_product_addons', plugins_url( 'assets/js/admin' . $suffix . '.js', WC_PRODUCT_ADDONS_MAIN_FILE ), array( 'jquery' ), WC_PRODUCT_ADDONS_VERSION, true );

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
                     wp_enqueue_style( 'woocommerce_product_addons_css', WC_PRODUCT_ADDONS_PLUGIN_URL . '/assets/css/admin.css', array(), WC_PRODUCT_ADDONS_VERSION );
                 });


                 
             } 
           }
        }

        


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
            ?>
            <style type="text/css">
                .nk-header-tools .user-avatar, .simplebar-content .user-avatar{
                    background-image: url("<?php echo esc_html( $profile_pic ); ?> ") !important;
                    background-size: contain !important;
                }

                .marketking_messages_page .simplebar-content .user-avatar{
                    background-image: none !important;
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

        do_action('marketking_dashboard_head');

        ?>
    </head>
    
    <?php
    // get logo
    $logo_src = get_option('marketking_logo_setting','');
    // if no logo configured, set default marketking logo
    if ($logo_src === ''){
        $logo_src = plugins_url('../../includes/assets/images/marketkinglogoblack.png', __FILE__);
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
                esc_html_e('Your current account is not a vendor. To login as a vendor, please logout first. ','marketking-multivendor-marketplace-for-woocommerce');

                                                            ?> <button class="close" data-dismiss="alert"></button></div>
                                                    </div><br />
                                                <a href="<?php echo esc_url(wc_logout_url()); ?>">
                                                    <button id="wp-submit" type="submit" value="Login" name="wp-submit" class="btn btn-lg btn-primary btn-block"><?php esc_html_e('Log out','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                                </a>
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
            include('marketking-dashboard.php');

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
                                                    <input type="hidden" value="<?php echo esc_attr( get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)) ); ?>" name="redirect_to">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <button id="wp-submit" type="submit" value="Login" name="wp-submit" class="btn btn-lg btn-primary btn-block"><?php esc_html_e('Sign in','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                            </div>
                                        </form>
                                        <div class="form-note-s2 text-center pt-4"> <?php esc_html_e('New on our platform?','marketking-multivendor-marketplace-for-woocommerce');?> <a href="<?php 
                                            if (get_option( 'marketking_vendor_registration_setting', 'myaccount' ) === 'separate'){
                                                $page = get_option('marketking_vendor_registration_page_setting');
                                                echo esc_attr(get_permalink($page)).'?redir=1';
                                            } else {
                                                echo esc_attr(get_permalink( wc_get_page_id( 'myaccount' ) ).'?redir=1');
                                            }
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
    