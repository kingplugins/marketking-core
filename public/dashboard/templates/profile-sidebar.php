<div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
    <div class="card-inner-group" data-simplebar>
        <div class="card-inner">
            <div class="user-card">
                <div class="user-avatar">
                    <span><?php 
                    $profile_pic = get_user_meta($user_id,'marketking_profile_logo_image', true);
                    if (empty($profile_pic)){
                        echo esc_html(strtoupper(substr($currentuser->user_login, 0, 2)));
                    }
                    ?></span>
                </div>
                <div class="user-info">
                    <span class="lead-text"><?php 
                    $storename = marketking()->get_store_name_display($user_id);
                    $firstlastname = $currentuser->first_name.' '.$currentuser->last_name;
                    if(empty($storename)){
                        echo esc_html($firstlastname);
                    } else {
                        echo esc_html($storename);
                    }
                    ?></span>
                    <span class="sub-text"><?php 
                    if(!empty($storename)){
                        echo esc_html($firstlastname);
                    } ?></span>
                </div>
                
            </div><!-- .user-card -->
        </div><!-- .card-inner -->
       
        <div class="card-inner p-0">
            <ul class="link-list-menu">
                <?php
                if(marketking()->vendor_has_panel('profile')){
                    ?>
                    <li><a class="<?php if ($page ==='profile'){echo 'active';}?>" href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'profile';?>"><em class="icon ni ni-user-list-fill"></em><span><?php esc_html_e('Store Information','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                    <?php
                }
                
                if (defined('MARKETKINGPRO_DIR')){
                    if (intval(get_option('marketking_enable_shipping_setting', 1)) === 1){
                        if(marketking()->vendor_has_panel('shipping')){
                            ?>
                            <li><a class="<?php if ($page ==='shipping' || $page === 'shippingzone'){echo 'active';}?>" href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'shipping';?>"><em class="icon ni ni-truck"></em><span><?php esc_html_e('Shipping','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                            <?php
                        }
                    }
                }
                ?>
                <?php

                if(defined('MARKETKINGPRO_DIR')){
                    if (intval(get_option( 'marketking_enable_vendorinvoices_setting', 1 )) === 1 && (defined('WPO_WCPDF_VERSION') || defined('WF_PKLIST_VERSION'))){
                        if(marketking()->vendor_has_panel('vendorinvoices')){
                            ?>
                            <li><a class="<?php if ($page ==='vendorinvoices'){echo 'active';}?>" href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'vendorinvoices';?>"><em class="icon ni ni-file-check"></em><span><?php esc_html_e('Invoicing','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                            <?php
                        }
                    }
                }

                if (defined('MARKETKINGPRO_DIR')){
                    if (intval(get_option('marketking_enable_support_setting', 1)) === 1){
                        if(marketking()->vendor_has_panel('support')){
                            ?>
                            <li><a class="<?php if ($page ==='support'){echo 'active';}?>" href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'support';?>"><em class="icon ni ni-ticket-plus"></em><span><?php esc_html_e('Support','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                            <?php
                        }
                    }
                }

                if (defined('MARKETKINGPRO_DIR')){
                    if (intval(get_option('marketking_enable_vacation_setting', 1)) === 1){
                        if(marketking()->vendor_has_panel('vacation')){
                            ?>
                            <li><a class="<?php if ($page ==='vacation'){echo 'active';}?>" href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'vacation';?>"><em class="icon ni ni-sun-fill"></em><span><?php esc_html_e('Vacation','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                            <?php
                        }
                    }
                }
                if (defined('MARKETKINGPRO_DIR')){
                    if (intval(get_option('marketking_enable_storenotice_setting', 1)) === 1){
                        if(marketking()->vendor_has_panel('storenotice')){
                            ?>
                            <li><a class="<?php if ($page ==='storenotice'){echo 'active';}?>" href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'storenotice';?>"><em class="icon ni ni-notice"></em><span><?php esc_html_e('Store Notice','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                            <?php
                        }
                    }
                }

                if (defined('MARKETKINGPRO_DIR')){
                    if (intval(get_option('marketking_enable_storepolicy_setting', 1)) === 1){
                        if(marketking()->vendor_has_panel('storepolicy')){
                            ?>
                            <li><a class="<?php if ($page ==='storepolicy'){echo 'active';}?>" href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'storepolicy';?>"><em class="icon ni ni-files-fill"></em><span><?php esc_html_e('Store Policies','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                            <?php
                        }
                    }
                }

                if (defined('MARKETKINGPRO_DIR')){
                    if (intval(get_option('marketking_enable_storeseo_setting', 1)) === 1){
                        if(marketking()->vendor_has_panel('storeseo')){
                            ?>
                            <li><a class="<?php if ($page ==='storeseo'){echo 'active';}?>" href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'storeseo';?>"><em class="icon ni ni-search"></em><span><?php esc_html_e('SEO Settings','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                            <?php
                        }
                    }
                }

                if (defined('MARKETKINGPRO_DIR')){
                    if (intval(get_option('marketking_enable_verification_setting', 1)) === 1){
                        if(marketking()->vendor_has_panel('verification')){
                            ?>
                            <li><a class="<?php if ($page ==='verification'){echo 'active';}?>" href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'verification';?>"><em class="icon ni ni-shield-check-fill"></em><span><?php esc_html_e('Verification','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                            <?php
                        }
                    }
                }
                if(marketking()->vendor_has_panel('profile-settings')){

                    ?>
                    <li><a class="<?php if ($page ==='profile-settings'){echo 'active';}?>" href="<?php echo esc_attr(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'profile-settings';?>"><em class="icon ni ni-opt-alt-fill"></em><span><?php esc_html_e('Settings','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                    <?php
                }
                ?>
            </ul>
        </div><!-- .card-inner -->
    </div><!-- .card-inner-group -->
</div><!-- card-aside -->