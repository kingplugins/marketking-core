<?php

/*

Profile Dashboard Page
* @version 1.0.0

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/


?><?php
if(marketking()->vendor_has_panel('profile')){
    ?>
    <div class="nk-content marketking_profile_page">
        <?php
        $userdata = get_userdata($user_id);
        $company = get_user_meta($user_id,'billing_company', true);
        $phone = get_user_meta($user_id,'billing_phone', true);
        $store_name = get_user_meta($user_id,'marketking_store_name', true);
        $address = get_user_meta($user_id,'billing_address_1', true);
        ?>
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
                                                <h4 class="nk-block-title"><?php esc_html_e('Store Information','marketking-multivendor-marketplace-for-woocommerce');?></h4>
                                                <div class="nk-block-des">
                                                    <p><?php esc_html_e('Your store information & data.','marketking-multivendor-marketplace-for-woocommerce');?></p>
                                                </div>
                                            </div>
                                            <div class="nk-block-head-content align-self-start d-lg-none">
                                                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1" data-target="userAside"><?php esc_html_e('Menu','marketking-multivendor-marketplace-for-woocommerce');?><em class="icon ni ni-menu-alt-r"></em></a>
                                            </div>
                                        </div>
                                    </div><!-- .nk-block-head -->
                                    <div class="nk-block">
                                        <div class="nk-data data-list">
                                            <div class="data-head">
                                                <h6 class="overline-title"><?php esc_html_e('Store Info','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                            </div>
                                            <div class="data-item data-item-profile" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label"><?php esc_html_e('First Name','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <span class="data-value"><?php echo esc_html($userdata->first_name);?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item data-item-profile" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label"><?php esc_html_e('Last Name','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <span class="data-value"><?php echo esc_html($userdata->last_name);?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item data-item-profile" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label"><?php esc_html_e('Company Name','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <span class="data-value"><?php echo esc_html($company);?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item data-item-address" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label"><?php esc_html_e('Address','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <span class="data-value"><?php echo esc_html($address);?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item data-item-profile" data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label"><?php esc_html_e('Store Name','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <span class="data-value"><?php echo esc_html($store_name);?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item data-item-profile"  data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col" >
                                                    <span class="data-label"><?php esc_html_e('Email','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <span class="data-value"><?php echo esc_html($userdata->user_email);?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item data-item-profile"  data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col" >
                                                    <span class="data-label"><?php esc_html_e('Phone','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <span class="data-value"><?php echo esc_html($phone);?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item data-item-aboutus"  data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col" >
                                                    <span class="data-label"><?php esc_html_e('About Us','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <span class="data-value"><?php echo substr(esc_html(get_user_meta($user_id,'marketking_store_aboutus', true)), 0, 40).'...';?></span>
                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item data-item-image"  data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col" >
                                                    <span class="data-label"><?php esc_html_e('Profile Image','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <span class="data-value">
                                                        <img class="marketking_profile_images_preview" src="<?php 

                                                            $imageprof = get_user_meta($user_id,'marketking_profile_logo_image', true);
                                                            if (empty($imageprof)){
                                                                $imageprof = MARKETKINGCORE_URL.'includes/assets/images/store-profile.png';

                                                            }
                                                            echo esc_attr($imageprof);

                                                        ?>">
                                                    </span>

                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->
                                            <div class="data-item data-item-image"  data-toggle="modal" data-target="#profile-edit">
                                                <div class="data-col" >
                                                    <span class="data-label"><?php esc_html_e('Banner Image','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <span class="data-value">
                                                        <img class="marketking_profile_images_preview" src="<?php 

                                                            $imageprof = get_user_meta($user_id,'marketking_profile_logo_image_banner', true);

                                                            if (!empty($imageprof)){
                                                                echo esc_attr($imageprof);
                                                            }

                                                        ?>">
                                                    </span>


                                                </div>
                                                <div class="data-col data-col-end"><span class="data-more"><em class="icon ni ni-forward-ios"></em></span></div>
                                            </div><!-- data-item -->

                                        </div><!-- data-list -->
                                    </div><!-- .nk-block -->
                                </div>
                                <?php 

                                include(apply_filters('marketking_dashboard_template','templates/profile-sidebar.php'));

                                ?>
                                <div class="modal fade" tabindex="-1" role="dialog" id="profile-edit">
                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                        <div class="modal-content">
                                            <a href="#" class="close" data-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
                                            <div class="modal-body modal-body-lg">
                                                <h5 class="title"><?php esc_html_e('Update Settings','marketking-multivendor-marketplace-for-woocommerce');?></h5>
                                                <ul class="nk-nav nav nav-tabs">
                                                    <li class="nav-item">
                                                        <a class="nav-link active nav-tab-personal" data-toggle="tab" href="#personal"><?php esc_html_e('Store Info','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                                        <a class="nav-link active nav-tab-address" data-toggle="tab" href="#address"><?php esc_html_e('Address','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                                        <a class="nav-link active nav-tab-aboutus" data-toggle="tab" href="#aboutus"><?php esc_html_e('About Us','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                                        <a class="nav-link nav-tab-images" data-toggle="tab" href="#images"><?php esc_html_e('Images','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                                    </li>
                                                </ul><!-- .nav-tabs -->
                                                <div class="tab-content">
                                                    <div class="tab-pane active" id="personal">
                                                        <div class="row gy-4">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="first-name"><?php esc_html_e('First Name','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <input type="text" class="form-control form-control-lg" id="first-name" value="<?php echo esc_attr($userdata->first_name);?>" placeholder="<?php esc_html_e('Enter your first name...','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="last-name"><?php esc_html_e('Last Name','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <input type="text" class="form-control form-control-lg" id="last-name" value="<?php echo esc_attr($userdata->last_name);?>" placeholder="<?php esc_html_e('Enter your last name...','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="company-name"><?php esc_html_e('Company Name','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <input type="text" class="form-control form-control-lg" id="company-name"  value="<?php echo esc_attr($company);?>" placeholder="<?php esc_html_e('Enter your company name...','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="store-name"><?php esc_html_e('Store Name','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <input type="text" maxlength="<?php echo esc_attr(apply_filters('marketking_store_name_max_length', 25)); ?>" class="form-control form-control-lg" id="store-name"  value="<?php echo esc_attr($store_name);?>" placeholder="<?php esc_html_e('Enter your store name...','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                                </div>
                                                            </div>

                                                           
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="email"><?php esc_html_e('Email','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <input type="email" class="form-control form-control-lg" value="<?php echo esc_attr($userdata->user_email);?>" id="email" placeholder="<?php esc_html_e('Enter your email...','marketking-multivendor-marketplace-for-woocommerce');?>"><br>
                                                                    <?php
                                                                    if (apply_filters('marketking_show_email_phone_vendor_profile', true)){
                                                                        ?>
                                                                        <div class="custom-control custom-switch mr-n2">
                                                                            <input type="checkbox" class="custom-control-input" <?php
                                                                            $check = get_user_meta($user_id,'marketking_show_store_email', true);
                                                                            checked($check,'yes',true);
                                                                            ?> name="showemail" id="showemail">
                                                                            <label class="custom-control-label" for="showemail"><?php esc_html_e('Show email on store page','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="phone"><?php esc_html_e('Phone','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <input type="tel" class="form-control form-control-lg" value="<?php echo esc_attr($phone);?>" id="phone" placeholder="<?php esc_html_e('Enter your phone number...','marketking-multivendor-marketplace-for-woocommerce');?>"><br>
                                                                    <?php
                                                                    if (apply_filters('marketking_show_email_phone_vendor_profile', true)){
                                                                        ?>
                                                                        <div class="custom-control custom-switch mr-n2">
                                                                            <input type="checkbox" class="custom-control-input" <?php
                                                                            $check = get_user_meta($user_id,'marketking_show_store_phone', true);
                                                                            checked($check,'yes',true);
                                                                            ?> name="showphone" id="showphone">
                                                                            <label class="custom-control-label" for="showphone"><?php esc_html_e('Show phone on store page','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-12">
                                                                <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                                                    <li>
                                                                        <button class="marketking_update_profile btn btn-lg btn-primary"><?php esc_html_e('Update Profile','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#" data-dismiss="modal" class="link link-light"><?php esc_html_e('Cancel','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div><!-- .tab-pane -->
                                                    <div class="tab-pane" id="images">
                                                        <div class="row gy-4">
                                                            
                                                            <div class="col-12">
                                                                <ul class="flex-wrap flex-sm-nowrap gx-4 gy-2">
                                                                    <li>
                                                                        <input type="hidden" id="marketking_profile_logo_image" name="marketking_profile_logo_image" value="<?php echo esc_attr(get_user_meta($user_id,'marketking_profile_logo_image', true));?>">
                                                                        <input type="hidden" id="marketking_profile_logo_image_banner" name="marketking_profile_logo_image_banner" value="<?php echo esc_attr(get_user_meta($user_id,'marketking_profile_logo_image_banner', true));?>">
                                                                        <div class="marketking-vendor">
                                                                        <div class="marketking-tab-contents">
                                                                        <div class="marketking-content-body">
                                                                        <div class="marketking-vendor-image"><div class="picture"><div class="marketking_clear_image" id="marketking_clear_image_profile"><?php esc_html_e('clear', 'marketking-multivendor-marketplace-for-woocommerce');?></div><p class="marketking-picture-header"><?php esc_html_e('Vendor Picture', 'marketking-multivendor-marketplace-for-woocommerce');?></p> <div class="marketking-profile-image"><div class="marketking-upload-image"><img src="<?php 

                                                                        $imageprof = get_user_meta($user_id,'marketking_profile_logo_image', true);
                                                                        if (empty($imageprof)){
                                                                            $imageprof = MARKETKINGCORE_URL.'includes/assets/images/store-profile.png';
                                                                        }
                                                                        echo esc_attr($imageprof);

                                                                        ?>"> <!----></div></div> <p class="marketking-picture-footer"><?php esc_html_e('Click to select a profile picture.', 'marketking-multivendor-marketplace-for-woocommerce');?></p></div> <div class="picture banner" style="<?php
                                                                        $imageprof = get_user_meta($user_id,'marketking_profile_logo_image_banner', true);
                                                                        if (!empty($imageprof)){
                                                                            echo 'background-image:url('.esc_attr($imageprof).')';
                                                                        }
                                                                    
                                                                        ?>"><div class="marketking_clear_image" id="marketking_clear_image_profile_banner"><?php esc_html_e('clear', 'marketking-multivendor-marketplace-for-woocommerce');?></div><div class="marketking-banner-image"><div class="marketking-upload-image"><!----> <button type="button">
                                                                        <?php esc_html_e('Upload Banner', 'marketking-multivendor-marketplace-for-woocommerce');?>
                                                                    </button></div></div> <p class="marketking-picture-footer"><?php esc_html_e('Click to select / upload a banner for the store.', 'marketking-multivendor-marketplace-for-woocommerce');?></p></div></div> </div></div></div>


                                                                    </li>
                                                                   
                                                                </ul>
                                                            </div>

                                                            <div class="col-12">
                                                                <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                                                    <li>
                                                                        <button class="marketking_update_profile btn btn-lg btn-primary"><?php esc_html_e('Save Images','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#" data-dismiss="modal" class="link link-light"><?php esc_html_e('Cancel','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div><!-- .tab-pane -->
                                                    <div class="tab-pane" id="address">
                                                        <div class="row gy-4">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="address1"><?php esc_html_e('Address 1','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <input type="text" class="form-control form-control-lg" id="address1" value="<?php echo esc_attr($address);?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="address2"><?php esc_html_e('Address 2','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <input type="text" class="form-control form-control-lg" id="address2" value="<?php echo esc_attr(get_user_meta($user_id,'billing_address_2', true));?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="city"><?php esc_html_e('City','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <input type="text" class="form-control form-control-lg" id="city"  value="<?php echo esc_attr(get_user_meta($user_id,'billing_city', true));?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="postcode"><?php esc_html_e('Postcode / ZIP','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                                    <input type="text" maxlength="25" class="form-control form-control-lg" id="postcode"  value="<?php echo esc_attr(get_user_meta($user_id,'billing_postcode', true));?>" >
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="billing_country"><?php esc_html_e('Country and State','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                                                    <div class="form-control-wrap country_state_wrap">
                                                                        <?php
                                                                        woocommerce_form_field( 'billing_country', array( 'type' => 'country', 'default' =>get_user_meta($user_id,'billing_country', true), 'input_class' => array('form-control')));

                                                                        woocommerce_form_field( 'billing_state', array( 'type' => 'state', 'default' =>get_user_meta($user_id,'billing_state', true), 'input_class' => array('form-control')));
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="col-12">
                                                                <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                                                    <li>
                                                                        <button class="marketking_update_profile btn btn-lg btn-primary"><?php esc_html_e('Update Profile','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#" data-dismiss="modal" class="link link-light"><?php esc_html_e('Cancel','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div><!-- .tab-pane -->
                                                    <div class="tab-pane" id="aboutus">
                                                        <div class="row gy-4">
                                                            
                                                            <div class="col-12">
                                                                <ul class="flex-wrap flex-sm-nowrap gx-4 gy-2">
                                                                    <li>
                                                                        <div class="form-group"><label class="form-label" for="default-textarea"><?php esc_html_e('About Us','marketking-multivendor-marketplace-for-woocommerce');?></label><div class="form-control-wrap"><textarea class="form-control form-control-simple no-resize" id="aboutusdescription" placeholder="<?php esc_html_e('Enter a description / about us here. Selected HTML tags can be used to format the above text: h2, h3, h4, i, strong','marketking-multivendor-marketplace-for-woocommerce');

                                                                        if (apply_filters('marketking_aboutus_allow_youtube', true)){
                                                                            echo ', <youtube>SUKZqnuiwMI</youtube>';
                                                                        }
                                                                        ?>


                                                                        "><?php 

                                                                        $aboutus = get_user_meta($user_id,'marketking_store_aboutus', true);

                                                                        $aboutus = esc_html($aboutus);

                                                                        $allowed = array('<h2>','</h2>','<h3>','<h4>','<i>','<strong>','</h3>','</h4>','</i>','</strong>');
                                                                        $replaced = array('***h2***','***/h2***','***h3***','***h4***','***i***','***strong***','***/h3***','***/h4***','***/i***','***/strong***');

                                                                        if (apply_filters('marketking_aboutus_allow_youtube', true)){
                                                                            array_push($replaced,'***youtube***');
                                                                            array_push($replaced,'***/youtube***');
                                                                            array_push($allowed,'<youtube>');
                                                                            array_push($allowed,'</youtube>');
                                                                        }

                                                                        $aboutus = str_replace($replaced, $allowed, $aboutus);

                                                                        echo $aboutus;

                                                                        ?></textarea></div></div>
                                                                    </li>
                                                                   
                                                                </ul>
                                                            </div>

                                                            <div class="col-12">
                                                                <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                                                    <li>
                                                                        <button class="marketking_update_profile btn btn-lg btn-primary"><?php esc_html_e('Update Profile','marketking-multivendor-marketplace-for-woocommerce');?></button>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#" data-dismiss="modal" class="link link-light"><?php esc_html_e('Cancel','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div><!-- .tab-pane -->
                                                </div><!-- .tab-content -->
                                            </div><!-- .modal-body -->
                                        </div><!-- .modal-content -->
                                    </div><!-- .modal-dialog -->
                                </div><!-- .modal -->
                            </div><!-- .card-aside-wrap -->
                        </div><!-- .card -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>