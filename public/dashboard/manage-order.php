<?php

/*

Dashboard Order Page
* @version 1.0.2

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/


?>
<?php
if(marketking()->vendor_has_panel('orders')){

    $checkedval = 0;
    if (marketking()->is_vendor_team_member()){
        $checkedval = intval(get_user_meta(get_current_user_id(),'marketking_teammember_available_panel_editorders', true));
    }

    ?>
    <div class="nk-content marketking_manage_order_page">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <form id="marketking_manage_order_form">
                    <?php
                    $order_id = sanitize_text_field(marketking()->get_pagenr_query_var());
                    // check that current vendor is assigned to the order
                    $order_vendor = get_post_field( 'post_author', $order_id );
                    if (intval($order_vendor) === intval($user_id)){
                        $order = wc_get_order($order_id);
                    	// has permission, continue

                        $post = get_post($order_id);
                        if (!is_a($post, 'WP_Post')){
                            $post = 0;
                        }
                    	?>
     
                        <div class="nk-block-head nk-block-head-sm">
                            <div class="nk-block-between g-3">
                                <div class="nk-block-head-content">
                                    <h3 class="nk-block-title page-title"><?php esc_html_e('Order Details','marketking-multivendor-marketplace-for-woocommerce');?> / <strong class="text-primary small">#<?php 

                                    // sequential
                                    $order_nr_sequential = get_post_meta($order_id,'_order_number', true);
                                    if (!empty($order_nr_sequential)){
                                        echo $order_nr_sequential;
                                    } else {
                                        echo esc_html($order_id);
                                    }

                                    ?></strong></h3> 
                                    <?php do_action('marketking_after_order_details_text'); ?>

                                    <div class="nk-block-des text-soft">
                                        <ul class="list-inline">
                                            <li><?php esc_html_e('Customer:','marketking-multivendor-marketplace-for-woocommerce');?> <span class="text-base"><?php echo apply_filters('marketking_order_page_customer_name', esc_html($order->get_formatted_billing_full_name()), $order);

                                            ?></span></li>
                                            <li><?php esc_html_e('Date:','marketking-multivendor-marketplace-for-woocommerce');?> <span class="text-base"><?php 
                                            $date_created = $order->get_date_created();
                                            echo $date_created->date_i18n( get_option('date_format'). ' ' . get_option('time_format'), $date_created->getTimestamp()+(get_option('gmt_offset')*3600) );
                                             ?></span></li>
                                        </ul>
                                        <?php do_action('marketking_order_before_order_information'); ?>
                                    </div>
                                </div>
                                <div class="nk-block-head-content">
                                    <ul class="nk-block-tools g-3">
                                        
                                    <input type="hidden" id="marketking_save_order_button_id" value="<?php echo esc_attr($order_id);?>">
                                    <?php
                                    // either not team member, or team member with permission to add
                                    if (!marketking()->is_vendor_team_member() || $checkedval === 1){
                                        ?>
                                        <div id="marketking_save_order_button">
                                            <a href="#" class="toggle btn btn-icon btn-primary d-md-none"><em class="icon ni ni-edit-fill"></em></a>
                                            <a href="#" class="toggle btn btn-primary d-none d-md-inline-flex"><em class="icon ni ni-edit-fill"></em><span><?php esc_html_e('Update Order','marketking-multivendor-marketplace-for-woocommerce') ?></span></a>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'orders';?>" class="marketking-order-back-button btn btn-icon btn-gray ml-2 text-white pl-2 pr-3"><em class="icon ni ni-arrow-left"></em><?php esc_html_e('Back','marketking-multivendor-marketplace-for-woocommerce'); ?></a>

                                    </ul>
                                </div>
                            </div>
                        </div><!-- .nk-block-head -->

                        <?php
                        if (isset($_GET['update'])){
                            $add = sanitize_text_field($_GET['update']);;
                            if ($add === 'success'){
                                ?>                                    
                                <div class="alert alert-primary alert-icon"><em class="icon ni ni-check-circle"></em> <strong><?php esc_html_e('The order has been updated successfully','marketking-multivendor-marketplace-for-woocommerce');?></strong>.</div>
                                <?php
                            }
                        }
                        ?>
                        <div class="nk-block">
                            <div class="card">
                                <div class="card-aside-wrap">
                                    <div class="card-content">
                                        <div class="card-inner">
                                            <div class="nk-block">
                                                <div class="nk-block-head">
                                                    <h5 class="title"><?php esc_html_e('Order Information','marketking-multivendor-marketplace-for-woocommerce');?></h5>
                                                    <?php do_action('marketking_after_order_information_text'); ?>

                                                </div><!-- .nk-block-head -->
                                                <div class="card card-preview">
                                                        <div class="row g-gs">
                                                            <div class="col-lg-4">
                                                                <div class="card">
                                                                    <h6 class="overline-title title marketking_order_item_title"><?php esc_html_e('General','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                                    <div class="card-body">
                                                                        <?php echo esc_html__('Payment via:','marketking-multivendor-marketplace-for-woocommerce').' '.$order->get_payment_method_title();?><br><br>
                                                                        <?php echo esc_html__('Date:','marketking-multivendor-marketplace-for-woocommerce').' '.$date_created->date_i18n( get_option('date_format'), $date_created->getTimestamp()+(get_option('gmt_offset')*3600) );?><br><br>
                                                                        <div class="form-group">
                                                                            <label class="form-label" for="marketking_order_status"><?php esc_html_e('Status','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                            <?php do_action('marketking_manage_order_after_status'); ?>
                                                                            <div class="form-control-wrap">
                                                                                <div class="form-control-select">
                                                                                    <select class="form-control" name="marketking_order_status" id="marketking_order_status" <?php
                                                                                    // disabled or not
                                                                                    if(!marketking()->vendor_can_change_order_status($user_id)){
                                                                                        echo 'disabled="disabled"';
                                                                                    }
                                                                                    $status = $order->get_status();
                                                                                    $modifiable_statuses = apply_filters('marketking_modifiable_statuses', array('processing','completed','on-hold'));
                                                                                    if (!in_array($status, $modifiable_statuses) && !apply_filters('marketking_all_order_statuses_modifiable', false)){
                                                                                        echo 'disabled="disabled"';
                                                                                    }

                                                                                    $removed_statuses = apply_filters('marketking_removed_statuses', array());

                                                                                    // either not team member, or team member with permission to add
                                                                                    if (!marketking()->is_vendor_team_member() || $checkedval === 1){

                                                                                    } else {
                                                                                        echo 'disabled="disabled"';
                                                                                    }

                                                                                    ?>>

                                                                                        <?php
                                                                                            if (!in_array('processing', $removed_statuses)){
                                                                                                ?>
                                                                                                <option value="processing" <?php selected($status, 'processing', true);?>><?php esc_html_e('Processing','marketking-multivendor-marketplace-for-woocommerce');?></option>

                                                                                                <?php
                                                                                            }
                                                                                        ?>

                                                                                        <?php
                                                                                            if (!in_array('on-hold', $removed_statuses)){
                                                                                                ?>
                                                                                                <option value="on-hold" <?php selected($status, 'on-hold', true);?>><?php esc_html_e('On hold','marketking-multivendor-marketplace-for-woocommerce');?></option>
                                                                                                <?php
                                                                                            }
                                                                                        ?>
                                                                                        <?php
                                                                                        // allow completed or not based on shipping tracking setting
                                                                                        $showcompleted = 'yes';
                                                                                        if ($status !== 'completed'){
                                                                                            if(defined('MARKETKINGPRO_DIR')){
                                                                                                if (intval(get_option('marketking_enable_shippingtracking_setting', 1)) === 1){
                                                                                                    if (intval(get_option( 'marketking_require_shipment_order_completed_setting', 0 )) === 1){

                                                                                                    $has_shipment = get_post_meta($order_id,'has_shipment', true);
                                                                                                    if ($has_shipment !== 'yes'){
                                                                                                        $showcompleted = 'no';
                                                                                                    }

                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                        if (!apply_filters('marketking_vendors_can_completed_status', true)){
                                                                                            $showcompleted = 'no';
                                                                                        }
                                                                                        if ($showcompleted === 'yes'){
                                                                                            ?>
                                                                                            <?php
                                                                                                if (!in_array('completed', $removed_statuses)){
                                                                                                    ?>
                                                                                                    <option value="completed" <?php selected($status, 'completed', true);?>><?php esc_html_e('Completed','marketking-multivendor-marketplace-for-woocommerce');?></option>
                                                                                                    <?php
                                                                                                }
                                                                                            ?>
                                                                                            <?php
                                                                                        }
                                                                                        // if non modifiable, show rest of statuses as well
                                                                                         if (!in_array($status, $modifiable_statuses) || apply_filters('marketking_vendor_can_all_order_statuses', false)){
                                                                                            ?>
                                                                                            <?php
                                                                                                if (!in_array('pending', $removed_statuses)){
                                                                                                    ?>
                                                                                                    <option value="pending" <?php selected($status, 'pending', true);?>><?php esc_html_e('Pending payment','marketking-multivendor-marketplace-for-woocommerce');?></option>
                                                                                                    <?php
                                                                                                }
                                                                                            ?>
                                                                                            <?php
                                                                                                if (!in_array('cancelled', $removed_statuses)){
                                                                                                    ?>
                                                                                                    <option value="cancelled" <?php selected($status, 'cancelled', true);?>><?php esc_html_e('Cancelled','marketking-multivendor-marketplace-for-woocommerce');?></option>
                                                                                                    <?php
                                                                                                }
                                                                                            ?>
                                                                                            <?php
                                                                                                if (!in_array('refunded', $removed_statuses)){
                                                                                                    ?>
                                                                                                    <option value="refunded" <?php selected($status, 'refunded', true);?>><?php esc_html_e('Refunded','marketking-multivendor-marketplace-for-woocommerce');?></option>
                                                                                                    <?php
                                                                                                }
                                                                                            ?>
                                                                                            <?php
                                                                                                if (!in_array('failed', $removed_statuses)){
                                                                                                    ?>
                                                                                                    <option value="failed" <?php selected($status, 'failed', true);?>><?php esc_html_e('Failed','marketking-multivendor-marketplace-for-woocommerce');?></option>
                                                                                                    <?php
                                                                                                }
                                                                                            ?>
                                                                                            <?php
                                                                                         }  

                                                                                         do_action('marketking_order_statuses_custom', $user_id, $status);


                                                                                        ?>
                                                                                    </select>
                                                                                </div>
                                                                                <?php do_action('marketking_order_after_order_status'); ?>
                                                                                <?php
                                                                                $received = get_post_meta($order_id,'marked_received', true);
                                                                                if ($received === 'yes'){
                                                                                    ?>
                                                                                    <p class="form-field form-field-wide">
                                                                                        <?php
                                                                                        echo '<div class="marketking_order_mark_received">'.esc_html__('The customer has marked this order as received.','marketking-multivendor-marketplace-for-woocommerce').'</div>';
                                                                                        ?>
                                                                                    </p>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <h6 class="overline-title title marketking_order_item_title"><?php esc_html_e('Billing','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <p class="card-text"><?php echo apply_filters('marketking_order_page_billing_address', $order->get_formatted_billing_address(), $order);?></p>
                                                                        <?php 

                                                                        if (apply_filters('marketking_vendors_see_customer_contact_info', true, $order)){
                                                                            if (!empty($order->get_billing_email())){
                                                                                echo esc_html__('Email:','marketking-multivendor-marketplace-for-woocommerce').' '.$order->get_billing_email().'<br>';

                                                                            }

                                                                            if (!empty($order->get_billing_phone())){
                                                                                echo esc_html__('Phone:','marketking-multivendor-marketplace-for-woocommerce').' '.$order->get_billing_phone();
                                                                            }
                                                                        }

                                                                        do_action('marketking_order_after_phone', $order);

                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <h6 class="overline-title title marketking_order_item_title"><?php esc_html_e('Shipping','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <p class="card-text"><?php echo apply_filters('marketking_order_page_shipping_address', $order->get_formatted_shipping_address(), $order);?></p>
                                                                        <?php
                                                                        $note = $order->get_customer_note();
                                                                        if (!empty($note)){
                                                                            ?>
                                                                            <p class="card-text"><strong><?php esc_html_e('Customer provided note:','marketking-multivendor-marketplace-for-woocommerce');?></strong><br><?php echo ' '.esc_html($note); ?></p>

                                                                            <?php
                                                                        }

                                                                        do_action('marketking_order_after_shipping', $order);
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </div>
                                                </div>
                                                
                                            </div><!-- .nk-block -->
                                            <?php do_action('marketking_order_after_order_information'); ?>
                                            <div class="nk-divider divider md"></div><div class="nk-block">
                                                <?php do_action('marketking_order_before_order_items'); ?>

                                                <div class="nk-block-head">
                                                    <h5 class="title"><?php esc_html_e('Order Items','marketking-multivendor-marketplace-for-woocommerce');?></h5>
                                                </div><!-- .nk-block-head -->
                                            </div><!-- .nk-block -->
                                            <br>
                                            <div id="woocommerce-order-items">
                                                <?php WC_Meta_Box_Order_Items::output($post);  ?>
                                            </div>

                                            <?php
                                            if (defined('MARKETKINGPRO_DIR')){
                                                if (apply_filters('marketking_enable_downloadable_product_permissions', true)){
                                                    ?>
                                                    <div class="nk-divider divider md"></div><div class="nk-block">
                                                        <div class="nk-block-head">
                                                            <h5 class="title"><?php esc_html_e('Downloadable Product Permissions','marketking-multivendor-marketplace-for-woocommerce');?></h5>
                                                        </div><!-- .nk-block-head -->
                                                    </div><!-- .nk-block -->
                                                    <br>
                                                    <div id="woocommerce-order-downloads">
                                                        <?php WC_Meta_Box_Order_Downloads::output($post);  ?>
                                                    </div>
                                                    <?php

                                                    do_action('marketking_after_downloadable_product_permissions', $order_id);

                                                    

                                                    
                                                    if (class_exists('WC_Admin_Custom_Order_Fields') && apply_filters('marketking_admin_custom_order_fields', true)){

                                                        ?>
                                                        <div class="nk-divider divider md"></div><div class="nk-block">
                                                            <div class="nk-block-head">
                                                                <h5 class="title"><?php esc_html_e('Custom Order Fields','marketking-multivendor-marketplace-for-woocommerce');?></h5>
                                                            </div><!-- .nk-block-head -->
                                                        </div><!-- .nk-block -->
                                                        <br>
                                                        <?php

                                                        $admin_order_fields = new WC_Admin_Custom_Order_Fields;
                                                        $version = $admin_order_fields::VERSION;

                                                        if (version_compare($version, '1.16.0', '>=')) {
                                                            $admin_order_fields->load_class( '/src/admin/class-wc-admin-custom-order-fields-admin.php', 'WC_Admin_Custom_Order_Fields_Admin' );
                                                        } else {
                                                            $admin_order_fields->load_class( '/includes/admin/class-wc-admin-custom-order-fields-admin.php', 'WC_Admin_Custom_Order_Fields_Admin' );
                                                        }
                                                        $admin_order_CPT = new WC_Admin_Custom_Order_Fields_Shop_Order_CPT;
                                                        $admin_order_CPT ->load_meta_box();
                                                        $admin_order_CPT->meta_box->render();
                                                        
                                                    }

                                                    if (class_exists('WC_Subscriptions')){
                                                        if (intval(get_option( 'marketking_enable_subscriptions_setting', 0 )) === 1){
                                                            if(marketking()->vendor_has_panel('subscriptions')){

                                                                ?>
                                                                <div class="nk-divider divider md"></div><div class="nk-block">
                                                                    <div class="nk-block-head">
                                                                        <h5 class="title"><?php esc_html_e('Related Orders','marketking-multivendor-marketplace-for-woocommerce');?></h5>
                                                                    </div><!-- .nk-block-head -->
                                                                </div><!-- .nk-block -->
                                                                <br>
                                                                <?php

                                                                function removeLink($str){
                                                                $regex = '/<a (.*)<\/a>/isU';
                                                                preg_match_all($regex,$str,$result);
                                                                foreach($result[0] as $rs)
                                                                {
                                                                    $regex = '/<a (.*)>(.*)<\/a>/isU';
                                                                    $text = preg_replace($regex,'$2',$rs);
                                                                    $str = str_replace($rs,$text,$str);
                                                                }
                                                                return $str;}

                                                                ob_start();
                                                                WCS_Meta_Box_Related_Orders::output($post);
                                                                $related_orders_content = ob_get_clean();
                                                                $related_orders_content = removeLink($related_orders_content);

                                                                echo $related_orders_content;

                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                           ?>
                                            
                                        </div><!-- .card-inner -->
                                    </div><!-- .card-content -->
                                    <div id="marketking_order_notes_container" class="card-aside card-aside-right" data-content="userAside" data-toggle-screen="xxl" data-toggle-overlay="true" data-toggle-body="true">
                                        <div class="card-inner-group">
                                            
                                            <div class="card-inner">
                                                <?php do_action('marketking_order_before_order_total'); ?>
                                                <div class="overline-title-alt mb-2 marketking_order_totals_title"><?php esc_html_e('Order Totals','marketking-multivendor-marketplace-for-woocommerce');?></div>
                                                <div class="profile-balance">
                                                    <div class="profile-balance-group gx-4">
                                                        <div class="profile-balance-sub">
                                                            <div class="profile-balance-amount">
                                                                <div class="number"><?php echo wc_price($order->get_total(), array('currency' => $order->get_currency()));?></div>
                                                            </div>
                                                            <div class="profile-balance-subtitle"><?php esc_html_e('Order Value','marketking-multivendor-marketplace-for-woocommerce');?></div>
                                                        </div>
                                                        <div class="profile-balance-sub">
                                                            <span class="profile-balance-plus text-soft marketking_icon_order_profit"><em class="icon ni ni-reports"></em></span>
                                                            <div class="profile-balance-amount">
                                                                <div class="number"><?php 
                                                                $earnings = marketking()->get_order_earnings($order_id);
                                                                if ($earnings !== 0){
                                                                    echo wc_price($earnings);
                                                                } else {
                                                                    echo '—';
                                                                }
                                                                
                                                                ?></div>
                                                            </div>
                                                            <div class="profile-balance-subtitle"><?php esc_html_e('Your Earnings','marketking-multivendor-marketplace-for-woocommerce');?></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php do_action('marketking_order_after_order_total'); ?>
                                            </div><!-- .card-inner -->

                                            <?php

                                            // SHIPPING TRACKING

                                            if (defined('MARKETKINGPRO_DIR')){
                                                if (intval(get_option('marketking_enable_shippingtracking_setting', 1)) === 1){
                                                    ?>
                                                    <div class="card-inner">
                                                        <h6 class="overline-title-alt mb-3"><?php 
                                                        esc_html_e('Shipping Tracking','marketking-multivendor-marketplace-for-woocommerce');
                                                        ?></h6>
                                                        <?php
                                                        // if order already has shipment, show shipping history
                                                        $shipping_history = get_post_meta($order_id,'marketking_shipment_history', true);
                                                        $providers = marketkingpro()->get_tracking_providers();
                                                        $selectedproviders = get_option('marketking_shipping_providers_setting',array('sp-other'));

                                                        if (empty($providers)){
                                                            $providers = array();
                                                        }
                                                        if (empty($selectedproviders)){
                                                            $selectedproviders = array();
                                                        }

                                                        if (!empty($shipping_history)){
                                                            ?>
                                                                <?php
                                                            // show packages
                                                            foreach ($shipping_history as $shipment){
                                                                esc_html_e('Shipment via ','marketking-multivendor-marketplace-for-woocommerce');

                                                                $providername = $providers[$shipment['provider']]['label'];
                                                                if ($shipment['provider'] === 'sp-other'){
                                                                    $providername = $shipment['providername'];
                                                                }
                                                                echo $providername.': <a href="'.esc_url($shipment['trackingurl']).'">'.esc_html($shipment['trackingnr']).'</a><br>';

                                                            }

                                                            // show button with ' add new shipment '
                                                            ?>
                                                            <br><button class="btn btn-sm btn-gray" id="marketking_add_another_shipment_button" value="<?php echo esc_attr($order_id);?>"><?php esc_html_e('Add another','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                                            <?php
                                                        }

                                                        ?>
                                                        <div class="row gy-3 <?php if (!empty($shipping_history)){ echo 'marketking_new_shipment_hidden'; }?>">
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="default-06"><?php esc_html_e('Create Shipment','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <div class="form-control-wrap ">
                                                                        <div class="form-control-select">
                                                                            <select class="form-control" id="marketking_create_shipment_provider">
                                                                                <?php
                                                                                foreach ($providers as $slug => $provider){
                                                                                    if (in_array($slug,$selectedproviders)){
                                                                                        ?>
                                                                                        <option value="<?php echo esc_attr($slug); ?>"><?php echo esc_html($provider['label']); ?></option>
                                                                                        <?php
                                                                                        }
                                                                                } 
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 marketking_create_shipment_other">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="default-01"><?php esc_html_e('Provider Name','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="text" class="form-control" id="marketking_create_shipment_provider_name" placeholder="<?php esc_html_e('Enter the shipping provider name','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="default-01"><?php esc_html_e('Tracking Number','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="text" class="form-control" id="marketking_create_shipment_tracking_number" placeholder="<?php esc_html_e('Enter the tracking number','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 marketking_create_shipment_other">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="default-01"><?php esc_html_e('Tracking URL','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="text" class="form-control" id="marketking_create_shipment_tracking_url" placeholder="<?php esc_html_e('Enter the tracking URL','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12">
                                                                <button class="btn btn-sm btn-secondary" type="button" id="marketking_create_shipment_button" value="<?php echo esc_attr($order_id);?>"><?php esc_html_e('Create shipment','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }

                                            // INVOICE PACKING DELIVERY
                                            if(defined('MARKETKINGPRO_DIR')){
                                                if (intval(get_option( 'marketking_enable_vendorinvoices_setting', 1 )) === 1 && (defined('WPO_WCPDF_VERSION') || defined('WF_PKLIST_VERSION'))){
                                                    ?>
                                                    <div class="card-inner">

                                                        <?php do_action('marketking_order_before_invoice_packing'); ?>

                                                        <h6 class="overline-title-alt mb-3"><?php 

                                                        if (apply_filters('marketking_enable_packing_slip_invoices_vendors', true)){
                                                            esc_html_e('Invoice & Packing','marketking-multivendor-marketplace-for-woocommerce');
                                                        } else {
                                                            esc_html_e('Invoice','marketking-multivendor-marketplace-for-woocommerce');
                                                        }

                                                        ?></h6>
                                                        <div class="marketking_vendor_invoice_download_container_main">
                                                            <?php
                                                            if (defined('WPO_WCPDF_VERSION')){
                                                                ?>
                                                                <div class="marketking_vendor_invoice_download_container">
                                                                    <?php
                                                                    $pdfdownload = do_shortcode('[wcpdf_download_invoice order_id='.$order_id.']');
                                                                    $pdfdownload = str_replace('my-account=1', '', $pdfdownload);
                                                                    echo $pdfdownload;


                                                                    // commission invoice
                                                                    if(intval(get_option( 'marketking_enable_commission_invoices_setting', 0 )) === 1){
                                                                       ?>
                                                                       <br><br>
                                                                       <button type="button" class="btn btn-sm btn-gray" name="marketking_vendor_download_cominvoice" id="marketking_vendor_download_cominvoice" value="<?php echo esc_attr($order_id);?>"><?php esc_html_e('Commission invoice','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
                                                                       <?php
                                                                    }

                                                                    ?>
                                                                </div>
                                                                <div class="marketking_vendor_invoice_download_container_second">

                                                                <?php

                                                                if (apply_filters('marketking_enable_packing_slip_invoices_vendors', true)){
                                                                    ?>
                                                                    <div class="marketking_packing_slip_container">
                                                                        <?php

                                                                        //do_action( 'woocommerce_admin_order_actions_end', $order );

                                                                        if (defined('WPO_WCPDF_VERSION')){
                                                                            global $wp;

                                                                            $invoice = wcpdf_get_packing_slip( $order );
                                                                            if ( ! $invoice || ! $invoice->is_allowed() ) {
                                                                                
                                                                            } else {
                                                                                $link_text = esc_html__( 'Packing slip (PDF)', 'marketking-multivendor-marketplace-for-woocommerce' );


                                                                                $pdf_url = WPO_WCPDF()->endpoint->get_document_link( $order, 'packing-slip', array( 'shortcode' => 'true' ) );

                                                                                $text = sprintf( '<p><a href="%s" target="_blank">%s</a></p>', esc_url( $pdf_url ), esc_html( $link_text ) );

                                                                                echo $text; 

                                                                            }
                                                                        }

                                                                        ?>
                                                                    </div>
                                                                    <?php
                                                                }



                                                            } else if (defined('WF_PKLIST_VERSION')){
                                                                
                                                                ?>
                                                                <table class="wf_invoice_metabox" style="width:100%;">          
                                                                    <?php
                                                                    $data_arr=array();
                                                                    $data_arr=apply_filters('wt_print_docdata_metabox',$data_arr, $order, $order_id);
                                                                    if(count($data_arr)>0){
                                                                        ?>
                                                                        <tr>
                                                                            <td style="font-weight:bold;">
                                                                                <h4 style="margin:0px; padding-top:5px; padding-bottom:3px; border-bottom:dashed 1px #ccc;"><?php _e('Document details','print-invoices-packing-slip-labels-for-woocommerce'); ?></h4>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td style="padding-bottom:10px;">
                                                                                <?php
                                                                                
                                                                                foreach($data_arr as $datav)
                                                                                {
                                                                                    echo '<span style="font-weight:500;">';
                                                                                    echo ("" !== $datav['label'] ? $datav['label'].': ' : '');
                                                                                    echo '</span>';
                                                                                    echo $datav['value'].'<br />';
                                                                                }
                                                                                ?>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                        }
                                                                    ?>
                                                                    <tr>
                                                                        <td>
                                                                            <h4 style="margin:0px; padding-top:5px; padding-bottom:3px; border-bottom:dashed 1px #ccc;"><?php _e('Print/Download','print-invoices-packing-slip-labels-for-woocommerce'); ?></h4>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td style="height:3px; font-size:0px; line-height:0px;"></td>
                                                                    </tr>
                                                                    <?php
                                                                    $btn_arr=array();
                                                                    $btn_arr=apply_filters('wt_print_actions', $btn_arr, $order, $order_id, 'detail_page');

                                                                    foreach($btn_arr as $btn_key=>$args){
                                                                        $btn_arr[$btn_key]['is_show_prompt'] = 1;
                                                                    }
                                                                    
                                                                    Wf_Woocommerce_Packing_List_Admin::generate_print_button_html($btn_arr, $order, $order_id, 'detail_page'); //generate buttons

                                                                    add_action('wp_footer', function(){
                                                                        // remove wp-admin from download links
                                                                        ?>
                                                                        <script>
                                                                            jQuery(document).ready(function(){
                                                                                jQuery('.wf_invoice_metabox a').each(function(){
                                                                                    let link = jQuery(this).prop('href');
                                                                                    link = link.replace('/wp-admin', '');
                                                                                    jQuery(this).prop('href', link);
                                                                                });
                                                                            });
                                                                        </script>
                                                                        <?php
                                                                    });


                                                                    ?>
                                                                </table>
                                                                <?php
                                                            }
                                                            ?>
                                                            
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <?php

                                                    do_action('marketking_order_after_invoice_packing');

                                                    
                                                }
                                            }
                                            ?>
                                            <div id="woocommerce-order-notes" class="card-inner">
                                                <?php do_action('marketking_order_before_order_notes'); ?>

                                                <h6 class="overline-title-alt mb-3"><?php esc_html_e('Order Notes','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                <?php WC_Meta_Box_Order_Notes::output($post);  ?>

                                            </div><!-- .card-inner -->

                                            <?php do_action('marketking_order_after_order_notes'); ?>


                                            <div id="marketking_custom_boxes_area">
                                            <?php
                                                do_action('marketking_custom_boxes_area');
                                            ?>
                                            </div>
                                        </div><!-- .card-inner -->
                                    </div><!-- .card-aside -->
                                </div><!-- .card-aside-wrap -->
                            </div><!-- .card -->
                        </div><!-- .nk-block -->
                        <?php
                    }

                    ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>