<?php
/*

Sidebar Page
* @version 1.0.1

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/
?>

<?php
if (!isset($user_id)){
    $user_id = get_current_user_id();
    if (marketking()->is_vendor_team_member()){
        $user_id = marketking()->get_team_member_parent();
    }
}

?>

<div class="nk-sidebar nk-sidebar-fixed is-light " data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-sidebar-brand">
            <a href="<?php echo esc_attr(marketking()->get_store_link($user_id));?>" class="logo-link nk-sidebar-logo">
                <img class="logo-small logo-img logo-img-small" src="<?php echo esc_url($logo_src); ?>" alt="logo-small">
            </a>
        </div>
        <div class="nk-menu-trigger mr-n2">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
        </div>
    </div><!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <?php
                    $show_announ = 'no';
                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option( 'marketking_enable_announcements_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('announcements')){
                                $show_announ = 'yes';
                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'announcements';?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-bell"></em></span>
                                        <span class="nk-menu-text"><?php esc_html_e('Announcements', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                        <?php if ($unread_ann !== 0){ ?>
                                            <span class="nk-menu-badge badge-danger"><?php echo esc_html($unread_ann).esc_html__(' New', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                        <?php } ?>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php
                            }
                        }
                    }

                    if ($show_announ === 'no'){
                        // show hidden item to make menu work correctly
                        ?>
                        <li class="nk-menu-item marketking_menu_hidden_item">
                                <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'announcements';?>" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-bell"></em></span>
                                    <span class="nk-menu-text"><?php esc_html_e('Announcements', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    <?php if ($unread_ann !== 0){ ?>
                                        <span class="nk-menu-badge badge-danger"><?php echo esc_html($unread_ann).esc_html__(' New', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    <?php } ?>
                                </a>
                            </li><!-- .nk-menu-item -->
                        <?php
                    }
                    ?>
                    <li class="nk-menu-heading">
                    </li><!-- .nk-menu-item -->

                    <?php
                    if(marketking()->vendor_has_panel('dashboard')){
                        ?>
                        <li class="nk-menu-item">
                           <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))));?>" class="nk-menu-link">
                               <span class="nk-menu-icon"><em class="icon ni ni-dashboard-fill"></em></span>
                               <span class="nk-menu-text"><?php esc_html_e('Dashboard', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                           </a>
                       </li><!-- .nk-menu-item -->
                   
                        <?php
                    }
                                          
                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option( 'marketking_enable_messages_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('messages')){
                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'messages';?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-chat-fill"></em></span>
                                        <span class="nk-menu-text"><?php esc_html_e('Messages', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                        <?php if ($unread_msg !== 0){ ?>
                                            <span class="nk-menu-badge badge-danger"><?php echo esc_html($unread_msg).esc_html__(' New', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                        <?php } ?>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php
                            }
                        }
                    }
                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option( 'marketking_enable_coupons_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('coupons')){
                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'coupons';?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-wallet-saving"></em></span>
                                        <span class="nk-menu-text"><?php esc_html_e('Coupons', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php
                            }
                        }
                    }

                    if(marketking()->vendor_has_panel('products')){
                        ?>
                        <li class="nk-menu-item">
                            <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'products';?>" class="nk-menu-link" data-original-title="" title="">
                                <span class="nk-menu-icon"><em class="icon ni ni-package-fill"></em></span>
                                <span class="nk-menu-text"><?php esc_html_e('Products', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                            </a>
                        </li>
                        <?php
                    }

                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option( 'marketking_enable_subscriptions_setting', 0 )) === 1){
                            if(class_exists('WC_Subscriptions')){
                                if(marketking()->vendor_has_panel('subscriptions')){
                                        ?>
                                        <li class="nk-menu-item">
                                            <a href="<?php echo esc_attr( trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))) ) . 'subscriptions'; ?>"
                                               class="nk-menu-link" data-original-title="" title="">
                                                <span class="nk-menu-icon"><em class="icon ni ni-repeat-fill"></em></span>
                                                <span class="nk-menu-text"><?php esc_html_e( 'Subscriptions', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></span>
                                            </a>
                                        </li>
                                        <?php
                                }
                            }
                        }
                    }


                    if (intval(get_option( 'marketking_agents_can_manage_orders_setting', 1 )) === 1){
                        if(marketking()->vendor_has_panel('orders')){
                            ?>
                            <li class="nk-menu-item">
                                <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'orders';?>" class="nk-menu-link" data-original-title="" title="">
                                    <span class="nk-menu-icon"><em class="icon ni ni-bag-fill"></em></span>
                                    <span class="nk-menu-text"><?php esc_html_e('Orders', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    <?php if ($vendor_orders_nr !== 0){ ?>
                                        <span class="nk-menu-badge badge-danger"><?php echo esc_html($vendor_orders_nr).esc_html__(' New', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    <?php } ?>
                                </a>
                            </li>
                            <?php
                        }
                    }

                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option( 'marketking_enable_bookings_setting', 0 )) === 1){
                            if(class_exists('WC_Bookings')){
                                if(marketking()->vendor_has_panel('bookings')){

                                    if ( marketking()->vendor_has_panel( 'products' ) ) {

                                        $vendor_bookings_nr = count( get_posts( array(
                                            'post_type'   => array( 'wc_booking', 'accommodation-booking' ),
                                            'post_status' => array( 'pending-confirmation' ),
                                            'numberposts' => - 1,
                                            'author'      => $user_id,
                                            'fields'      => 'ids'
                                        ) ) );

                                        ?>
                                        <li class="nk-menu-item">
                                            <a href="<?php echo esc_attr( trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))) ) . 'bookings'; ?>"
                                               class="nk-menu-link" data-original-title="" title="">
                                                <span class="nk-menu-icon"><em class="icon ni ni-calender-date-fill"></em></span>
                                                <span class="nk-menu-text"><?php esc_html_e( 'Bookings', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></span>
                                                <?php if ( $vendor_bookings_nr !== 0 ) { ?>
                                                    <span class="nk-menu-badge badge-danger"><?php echo esc_html($vendor_bookings_nr ) . esc_html__( ' New', 'marketking-multivendor-marketplace-for-woocommerce' ); ?></span>
                                                <?php } ?>
                                            </a>
                                        </li>
                                        <?php
                                    }
                                }
                            }
                        }
                    }

                    // B2BKING INTEGRATION START
                    if (defined('B2BKING_DIR') && defined('MARKETKINGPRO_DIR') && intval(get_option('marketking_enable_b2bkingintegration_setting', 1)) === 1){

                        if (intval(get_option('b2bking_enable_offers_setting', 1)) === 1){
                            if(marketking()->vendor_has_panel('b2bkingoffers')){
                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'offers';?>" class="nk-menu-link" data-original-title="" title="">
                                        <span class="nk-menu-icon"><em class="icon ni ni-tags-fill"></em></span>
                                        <span class="nk-menu-text"><?php esc_html_e('Offers', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    </a>
                                </li>
                                <?php
                            }
                        }

                        if (intval(get_option('b2bking_enable_conversations_setting', 1)) === 1){
                            if(marketking()->vendor_has_panel('b2bkingconversations')){
                               ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'b2bmessaging';?>" class="nk-menu-link" data-original-title="" title="">
                                        <span class="nk-menu-icon"><em class="icon ni ni-briefcase"></em></span>
                                        <span class="nk-menu-text"><?php esc_html_e('B2B Messages', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    </a>
                                </li>
                                <?php
                            }
                        }

                        if (intval(get_option('b2bking_show_dynamic_rules_vendors_setting_marketking', 1)) === 1){
                            if(marketking()->vendor_has_panel('b2bkingrules')){
                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'rules';?>" class="nk-menu-link" data-original-title="" title="">
                                        <span class="nk-menu-icon"><em class="icon ni ni-layers-fill"></em></span>
                                        <span class="nk-menu-text"><?php esc_html_e('Dynamic Rules', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    </a>
                                </li>
                                <?php
                            }
                        }   
                    }

                    // B2BKING INTEGRATION END

                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option( 'marketking_enable_teams_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('teams')){
                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'team';?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-network"></em></span>
                                        <span class="nk-menu-text"><?php esc_html_e('My Team', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php
                            }
                        }
                    }
                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option( 'marketking_enable_earnings_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('earnings')){

                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'earnings';?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                                        <span class="nk-menu-text"><?php esc_html_e('Earnings', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php
                            }
                        }
                    }
                    if (intval(get_option( 'marketking_enable_payouts_setting', 1 )) === 1){
                        if(marketking()->vendor_has_panel('payouts')){
                            ?>
                            <li class="nk-menu-item">
                                <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'payouts';?>" class="nk-menu-link">
                                    <span class="nk-menu-icon"><em class="icon ni ni-wallet-out"></em></span>
                                    <span class="nk-menu-text"><?php esc_html_e('Payouts', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                </a>
                            </li><!-- .nk-menu-item -->
                            <?php
                        }
                    }
                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option( 'marketking_enable_reviews_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('reviews')){
                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'reviews';?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-reports-alt"></em></span>
                                        <span class="nk-menu-text"><?php esc_html_e('Reviews', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php
                            }
                        }
                        if (intval(get_option( 'marketking_enable_refunds_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('refunds')){
                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'refunds';?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-reload"></em></span>
                                        <span class="nk-menu-text"><?php esc_html_e('Refunds', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                        <?php 
                                        // get number of open refunds
                                        $refund_requests = count(get_posts( array( 
                                            'post_type' => 'marketking_refund',
                                            'numberposts' => -1,
                                            'post_status'    => 'any',
                                            'fields'    => 'ids',
                                            'meta_query'=> array(
                                                'relation' => 'AND',
                                                array(
                                                    'key' => 'vendor_id',
                                                    'value' => $user_id
                                                ),
                                                array(
                                                    'key' => 'request_status',
                                                    'value' => 'open'
                                                ),
                                            )
                                        )));

                                        if ($refund_requests !== 0){ 
                                            ?>
                                            <span class="nk-menu-badge badge-danger"><?php echo esc_html($refund_requests).esc_html__(' Open', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                        <?php } ?>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php

                                if (defined('WOOCOMMERCE_WARRANTY_VERSION')){
                                    ?>
                                    <li class="nk-menu-item">
                                        <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'rma';?>" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-shield-check-fill"></em></span>
                                            <span class="nk-menu-text"><?php echo apply_filters('marketking_menu_refunds_title',esc_html__('Warranty', 'marketking-multivendor-marketplace-for-woocommerce'));?></span>
                                        </a>
                                    </li><!-- .nk-menu-item -->
                                    <?php
                                }
                            }
                        }
                        if (intval(get_option( 'marketking_enable_vendordocs_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('vendordocs')){
                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'docs';?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-book-fill"></em></span>
                                        <span class="nk-menu-text"><?php echo apply_filters('marketking_menu_vendordocs_title',esc_html__('Docs', 'marketking-multivendor-marketplace-for-woocommerce'));?></span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php
                            }
                        }

                        if (intval(get_option( 'marketking_enable_memberships_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('memberships')){
                                ?>
                                <li class="nk-menu-item">
                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'membership';?>" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-navigate-fill"></em></span>
                                        <span class="nk-menu-text"><?php echo apply_filters('marketking_menu_memberships_title',get_option('marketking_memberships_page_name_setting', esc_html__('Member','marketking-multivendor-marketplace-for-woocommerce')));?></span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php
                            }
                        }
                    }

                    // if vendor or team has access to any of these, they should be able to see settings
                    $profile_like_panels = array('shipping','vacation','storenotice','storepolicy','storeseo','verification','storecategories','profile-settings');
                    $has_vendor_like_panel = false;
                    $first_vendor_like_panel = '';
                    foreach ($profile_like_panels as $panel){
                        if(marketking()->vendor_has_panel($panel)){
                            $has_vendor_like_panel = true;
                            $first_vendor_like_panel = $panel;
                            break;
                        }
                    }
                    if(marketking()->vendor_has_panel('profile') || $has_vendor_like_panel){

                        ?>
                        <li class="nk-menu-item">
                            <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))));
                            // show profile if available, else show first available settings panel in profile page
                            if(marketking()->vendor_has_panel('profile')){
                                echo 'profile';
                            } else {
                                echo $first_vendor_like_panel;
                            }

                        ?>" class="nk-menu-link">
                                <span class="nk-menu-icon"><em class="icon ni ni-account-setting-fill"></em></span>
                                <span class="nk-menu-text"><?php esc_html_e('Settings', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                            </a>
                        </li><!-- .nk-menu-item -->

                    <?php 
                    }
                   
                    do_action('marketking_extend_menu'); ?>

                    <div class="marketking_mobile_menu_end"> <!-- spacing for mobile scrolling -->
                        <br><br><br><br>
                    </div>

                </ul><!-- .nk-menu -->
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>