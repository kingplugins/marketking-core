<?php

/*

Profile Settings Page
* @version 1.0.0

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/


?><?php
if(marketking()->vendor_has_panel('profile-settings')){
    ?>
    <div class="nk-content marketking_profile_settings_page">
        <div class="container-fluid">
            <?php
            if (isset($_GET['update'])){
                $add = sanitize_text_field($_GET['update']);;
                if ($add === 'success'){
                    ?>                                    
                    <div class="alert alert-primary alert-icon"><em class="icon ni ni-check-circle"></em> <strong><?php esc_html_e('Your settings have been saved successfully','marketking-multivendor-marketplace-for-woocommerce');?></strong>.</div>
                    <?php
                }
            }
            ?>
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block">
                        <div class="card">
                            <div class="card-aside-wrap">
                                <div class="card-inner card-inner-lg">
                                    <div class="nk-block-head nk-block-head-lg">
                                        <div class="nk-block-between">
                                            <div class="nk-block-head-content">
                                                <h4 class="nk-block-title"><?php esc_html_e('Profile Settings','marketking-multivendor-marketplace-for-woocommerce');?></h4>

                                            </div>
                                            <div class="nk-block-head-content align-self-start d-lg-none">
                                                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1" data-target="userAside"><?php esc_html_e('Menu','marketking-multivendor-marketplace-for-woocommerce');?><em class="icon ni ni-menu-alt-r"></em></a>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    if (defined('MARKETKINGPRO_DIR')){ 
                                        if (!marketking()->is_vendor_team_member()){
                                            ?>

                                            <!-- .nk-block-head -->
                                            <div class="nk-block-head nk-block-head-sm">
                                                <div class="nk-block-head-content">
                                                    <h6><?php esc_html_e('Email Settings','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                    <p><?php esc_html_e('Choose which email notifications you would like to receive.','marketking-multivendor-marketplace-for-woocommerce');?></p>
                                                </div>
                                            </div><!-- .nk-block-head -->

                                            <?php
                                        }
                                    }
                                    ?>
                                    <?php
                                    $new_announcements_email = get_user_meta($user_id,'marketking_receive_new_announcements_emails', true);
                                    if (empty($new_announcements_email)){
                                        $new_announcements_email = 'yes';
                                    }

                                    $new_messages_email = get_user_meta($user_id,'marketking_receive_new_messages_emails', true);
                                    if (empty($new_messages_email)){
                                        $new_messages_email = 'yes';
                                    }

                                    $new_refund_email = get_user_meta($user_id,'marketking_receive_new_refund_emails', true);
                                    if (empty($new_refund_email)){
                                        $new_refund_email = 'yes';
                                    }

                                    $new_review_email = get_user_meta($user_id,'marketking_receive_new_review_emails', true);
                                    if (empty($new_review_email)){
                                        $new_review_email = 'yes';
                                    }

                                    $vendor_setting = get_user_meta(get_current_user_id(), 'marketking_vendor_load_tables_ajax', true);
                                    if (empty($vendor_setting)){
                                        $vendor_setting = 'no';
                                    }

                                    if (defined('MARKETKINGPRO_DIR')){
                                        if (!marketking()->is_vendor_team_member()){
                                        ?>
                                        <div class="nk-block-content">
                                            <div class="gy-3">
                                                <div class="g-item">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" <?php checked('yes',$new_announcements_email, true); ?> id="new-announcements">
                                                        <label class="custom-control-label" for="new-announcements"><?php esc_html_e('Email me when new announcements are published.','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                    </div>
                                                </div>
                                                <div class="g-item">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" <?php checked('yes',$new_messages_email, true); ?> id="new-messages">
                                                        <label class="custom-control-label" for="new-messages"><?php esc_html_e('Email me when I receive a new message.','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                    </div>
                                                </div>
                                                <?php
                                                if (intval(get_option('marketking_enable_refunds_setting', 1)) === 1){
                                                    ?>
                                                    <div class="g-item">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" <?php checked('yes',$new_refund_email, true); ?> id="new-refund">
                                                            <label class="custom-control-label" for="new-refund"><?php esc_html_e('Email me when I receive a new refund request.','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                if (intval(get_option('marketking_enable_reviews_setting', 1)) === 1){
                                                    ?>
                                                    <div class="g-item">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input" <?php checked('yes',$new_review_email, true); ?> id="new-review">
                                                            <label class="custom-control-label" for="new-review"><?php esc_html_e('Email me when I receive a new rating (review).','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }

                                                ?>
                                            </div>
                                        </div><!-- .nk-block-content -->
                                        <br><br><br>

                                        <?php
                                        }
                                    }
                                    ?>
                                    <div class="nk-block-head nk-block-head-sm">
                                        <div class="nk-block-head-content">
                                            <h6><?php esc_html_e('Dashboard Settings','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                            <p><?php esc_html_e('Control how your vendor dashboard works.','marketking-multivendor-marketplace-for-woocommerce');?></p>
                                        </div>
                                    </div><!-- .nk-block-head -->
                                    <div class="nk-block-content">
                                        <div class="gy-3">
                                            <div class="g-item">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" <?php checked('yes',$vendor_setting, true); ?> id="dashajax">
                                                    <label class="custom-control-label" for="dashajax"><?php esc_html_e('Load dashboard tables with AJAX (only enable this if you have a large nr. of products / orders).','marketking-multivendor-marketplace-for-woocommerce');

                                                      ?></label>
                                                </div>
                                            </div>
                                           
                                        </div>
                                    </div><!-- .nk-block-content -->
                                    <br><br>
                                    <button class="btn btn-primary" type="submit" id="marketking_save_settings" value="<?php echo esc_attr($user_id);?>"><?php esc_html_e('Save Settings','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                </div>
                                <?php 

                                include(apply_filters('marketking_dashboard_template','templates/profile-sidebar.php'));

                                ?>
                            </div><!-- .card-inner -->
                        </div><!-- .card-aside-wrap -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>