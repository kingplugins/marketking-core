<?php

/*

Edit Product Page
* @version 1.1.0

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/

?>
<?php
    if(marketking()->vendor_has_panel('products')){
        $checkedval = 0;
        if (marketking()->is_vendor_team_member()){
            $checkedval = intval(get_user_meta(get_current_user_id(),'marketking_teammember_available_panel_editproducts', true));
        }

        if (!apply_filters('marketking_vendors_can_edit_products', true)){
            return;
        }   

        $productid = sanitize_text_field(marketking()->get_pagenr_query_var());
        $canadd = marketking()->vendor_can_add_more_products($user_id);

        // if product exists
        $post = get_post($productid);
        $product = wc_get_product($productid);
        $exists = 'existing';

        // Bookings Ref RR
        if (intval(get_option( 'marketking_enable_bookings_setting', 0 )) === 1){
             if(defined('MARKETKINGPRO_DIR')){
                if(marketking()->vendor_has_panel('bookings')){
                    if(class_exists('WC_Bookings')){
                        Marketking_WC_Bookings_Metabox::output_metabox();
                    }
                }
            }
        }

        // get original query var
        if (get_query_var('pagenr') === 'add'){
            $exists = 'new'; 
        }

        $allowed_product = apply_filters('marketking_allowed_vendor_edit_product', true, $product);
        if (!$allowed_product){
            exit();
        }

        // save post and retake it later - this helps compatibility with elementor, which changes the post ID for some reason
        $retake = 'no';
        if (is_object($post)){
            $originalpost = $post;
            $originalproduct = $product;
            $retake = 'yes';
        }

        if ( (int) marketking()->get_product_vendor( $productid ) !== (int) $user_id ) {
            // check that we're not on add page
            if (get_query_var('pagenr') !== 'add'){
                return;
            } else {
                // clear standby product and reload
                marketking()->clear_product_standby($productid);
                marketking()->set_product_standby();

                ?>
                <script>
                    location.reload();
                </script>
                <?php
            }
        }

        // either not team member, or team member with permission to add
        if (!marketking()->is_vendor_team_member() || $checkedval === 1){

            if($canadd || $exists === 'existing'){

            ?>
                <div class="nk-content marketking_edit_product_page">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                <form id="marketking_save_product_form">

                                <?php

                                if (!marketking()->vendor_can_publish_products($user_id)){
                                    ?>
                                    <input type="hidden" id="marketking_can_publish_products" value="no">
                                    <?php
                                } else {
                                    ?>
                                    <input type="hidden" id="marketking_can_publish_products" value="yes">
                                    <?php
                                }                              

                                if ($exists === 'new'){
                                    if (!marketking()->vendor_can_publish_products($user_id)){
                                        $text = esc_html__('Send New Product for Review','marketking-multivendor-marketplace-for-woocommerce');
                                        $icon = 'ni-plus';
                                        $actionedit = 'add';
                                    } else {
                                        $text = esc_html__('Save New Product','marketking-multivendor-marketplace-for-woocommerce');
                                        $icon = 'ni-plus';
                                        $actionedit = 'add';
                                    }
                                } else {
                                    if (!marketking()->vendor_can_publish_products($user_id)){
                                        $text = esc_html__('Send for Review','marketking-multivendor-marketplace-for-woocommerce');
                                        $icon = 'ni-edit-fill';
                                        $actionedit = 'edit';
                                    } else {
                                        $text = esc_html__('Publish Product','marketking-multivendor-marketplace-for-woocommerce');
                                        $icon = 'ni-edit-fill';
                                        $actionedit = 'edit';
                                    }
                                    
                                }

                                ?>
                                <input id="marketking_edit_product_action_edit" name="marketking_edit_product_action_edit" type="hidden" value="<?php echo esc_attr($actionedit);?>">
                                <div class="nk-block-head nk-block-head-sm">
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content marketking_status_text_title">
                                            <h3 class="nk-block-title page-title "><?php esc_html_e('Edit Product','marketking-multivendor-marketplace-for-woocommerce'); ?></h3>
                                            <div class="marketking_edit_status_container">
                                                <?php
                                                    if ($exists === 'existing'){
                                                        $status = get_post_status($prod);
                                                    } else {
                                                        $status = 'publish';
                                                    }

                                                    if ($actionedit === 'add'){
                                                        $status = 'new';
                                                    }

                                                ?>
                                                <p class="marketking_status_text">- &nbsp;</p>
                                                <select name="marketking_edit_product_status" id="marketking_edit_product_status" class="marketking_status_<?php echo esc_attr($status);?>" disabled>
                                                    <option value="publish" <?php selected($status, 'publish', true);?>><?php esc_html_e('Published Product','marketking-multivendor-marketplace-for-woocommerce');?></option>
                                                    <option value="pending" <?php selected($status, 'pending', true);?>><?php esc_html_e('Product Pending Review','marketking-multivendor-marketplace-for-woocommerce');?></option>
                                                    <option value="draft" <?php selected($status, 'draft', true);?>><?php esc_html_e('Draft Product','marketking-multivendor-marketplace-for-woocommerce');?></option>
                                                    <option value="new" <?php selected($status, 'new', true);?>><?php esc_html_e('New Product','marketking-multivendor-marketplace-for-woocommerce');?></option>
                                                </select>&nbsp;
                                               
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <div data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <input type="hidden" id="marketking_save_product_button_id" value="<?php echo esc_attr($productid);?>">
                                                        <input type="hidden" id="post_ID" value="<?php echo esc_attr($productid);?>">
                                                        <li class="nk-block-tools-opt">
                                                            <?php
                                                            if (!marketking()->vendor_can_publish_products($user_id)){
                                                                $tip = esc_html__('When a product is ready to be published, let us know by clicking on "Send for Review". If you are still working on the product, save it as a draft instead.','marketking-multivendor-marketplace-for-woocommerce');
                                                                echo '<div class="marketking_product_help_tip">'.wc_help_tip($tip, false).'</div>';
                                                            }
                                                            ?>
                                                            <div id="marketking_save_product_button">
                                                                <a href="#" class="toggle btn btn-primary d-md-inline-flex"><em class="icon ni <?php echo esc_attr($icon);?>"></em><span><?php echo esc_html($text); ?></span></a>
                                                            </div>
                                                            <div id="marketking_save_as_draft_button">
                                                                <a href="#" class="toggle btn btn-gray d-md-inline-flex ml-2"><em class="icon ni ni-file-text"></em><span><?php esc_html_e('Save as Draft','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a>
                                                            </div>
                                                            <?php
                                                            if ($exists === 'existing'){
                                                                // additional buttons for View Product and Remove Product
                                                                ?>
                                                                <div class="dropdown">
                                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-gray btn-trigger ml-2 text-white pl-2 pr-3" data-toggle="dropdown"><em class="icon ni ni-more-h"></em><?php esc_html_e('More','marketking-multivendor-marketplace-for-woocommerce'); ?></a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <ul class="link-list-opt no-bdr">
                                                                            <li><a target="_blank" href="<?php 
                                                                            $permalink = $product->get_permalink();
                                                                            echo esc_attr($permalink);
                                                                            ?>
                                                                            "><em class="icon ni ni-eye"></em><span><?php esc_html_e('View Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
                                                                            <?php

                                                                            if(intval(get_option( 'marketking_vendors_can_newproducts_setting',1 )) === 1){
                                                                                if (apply_filters('marketking_vendors_can_add_products', true)){
                                                                                    // either not team member, or team member with permission to add
                                                                                    if (!marketking()->is_vendor_team_member() || $checkedval === 1){
                                                                                        if($canadd){
                                                                                            ?>
                                                                                            <li><input type="hidden" class="marketking_input_id" value="<?php echo esc_attr($product->get_id());?>"><a href="#" class="marketking_clone_product"><em class="icon ni ni-copy-fill"></em><span><?php esc_html_e('Clone Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
                                                                                            <?php
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                            ?>
                                                                            <li><a href="#" class="toggle marketking_delete_button" value="<?php echo esc_attr($productid);?>"><em class="icon ni ni-trash"></em><span><?php esc_html_e('Delete Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                            ?>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                    </div><!-- .nk-block-between -->
                                </div><!-- .nk-block-head -->
                                <?php

                                // view button html
                                if ($status === 'pending' || $status === 'draft'){
                                    $button_text = esc_html__('Preview Product', 'marketking-multivendor-marketplace-for-woocommerce');
                                } else {
                                    $button_text = esc_html__('View Product', 'marketking-multivendor-marketplace-for-woocommerce');
                                }

                                ob_start();
                                ?>
                                <a target="_blank" href="<?php 
                                $permalink = $product->get_permalink();
                                echo esc_attr($permalink);
                                ?>
                                "><button type="button" class="btn btn-sm btn-gray view_product_button">
                                <em class="icon ni ni-eye-fill button-icon"></em><?php echo esc_html($button_text); ?></button></a>
                                <?php
                                $view_button_html = ob_get_clean();


                                if (isset($_GET['add'])){
                                    $add = sanitize_text_field($_GET['add']);;
                                    if ($add === 'success'){
                                        ?>                                    
                                        <div class="alert alert-primary alert-icon"><em class="icon ni ni-check-circle"></em> <strong><?php esc_html_e('Your product has been created successfully','marketking-multivendor-marketplace-for-woocommerce');?></strong>. <?php esc_html_e('You can now continue to edit it','marketking-multivendor-marketplace-for-woocommerce');?>.<?php echo $view_button_html; ?></div>
                                        <?php
                                    }
                                }
                                if (isset($_GET['update'])){
                                    $add = sanitize_text_field($_GET['update']);;
                                    if ($add === 'success'){
                                        ?>                                    
                                        <div class="alert alert-primary alert-icon"><em class="icon ni ni-check-circle"></em> <strong><?php esc_html_e('Your product has been updated successfully','marketking-multivendor-marketplace-for-woocommerce');?></strong>.<?php echo $view_button_html;?></div>
                                        <?php
                                    }
                                }
                                
                                ?>

                                <!-- PRODUCT TITLE -->
                                <?php
                                if($exists === 'existing'){
                                    $title = $product->get_title();
                                    if ($title === 'Product Name'){
                                        $title = '';
                                    }
                                } else {
                                    $title = '';
                                }


                                ?>
                                <div><div class="form-group"><div class="form-control-wrap"><input type="text" class="form-control form-control-lg form-control-outlined" id="marketking_product_title" value="<?php echo esc_attr($title);?>" required><label class="form-label-outlined" for="outlined-lg"><?php esc_html_e('Product Name','marketking-multivendor-marketplace-for-woocommerce');?></label></div></div></div>

                                <!-- PRODUCT DATA -->
                                <div id="marketking_edit_product_data_container" class="postbox-container wp-core-ui">
                                    <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                                        <div id="woocommerce-product-data" class="postbox ">
                                            <div class="postbox-header">
                                                <h2 class="hndle ui-sortable-handle">
                                                    <?php esc_html_e("Product data",'marketking-multivendor-marketplace-for-woocommerce');?>
                                                </h2>
                                            </div>
                                            <div class="inside">
                                                <?php

                                                WC_Meta_Box_Product_Data::output($post); 

                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br><br>
                                <div class="row">
                                    <!-- GALLERY -->
                                    <br><br>
                                    <div class="col-xxl-3 col-md-6 marketking_card_gal_cat_tags marketking_main_product_image_block">
                                        <div class="code-block"><h6 class="overline-title title"><?php esc_html_e('Main Product Image','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                            <img id="marketking_edit_product_main_image" src="<?php
                                            // if edit product
                                            $imageval = 0;

                                            if ($retake === 'yes'){
                                                $post = $originalpost;
                                                $product = $originalproduct;
                                            }

                                            if ($exists === 'existing'){
                                                $src = wp_get_attachment_url( $product->get_image_id() );
                                                if (empty($src)){
                                                    $src = wc_placeholder_img_src();
                                                } else {
                                                    $imageval = $product->get_image_id();
                                                    $src = marketking()->get_resized_image($src, 'medium');
                                                }
                                            
                                            } else {
                                                // new product
                                                $src = wc_placeholder_img_src();
                                            }
                                            echo esc_attr($src);
                                            ?>">

                                            <div class="marketking_edit_product_image_explainer"><?php esc_html_e('Click the image to edit or update','marketking-multivendor-marketplace-for-woocommerce');?></div>
                                            <input type="hidden" name="marketking_edit_product_main_image_value" id="marketking_edit_product_main_image_value" value="<?php echo esc_attr($imageval);?>">

                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-md-6 marketking_card_gal_cat_tags marketking_image_gallery_block">
                                        <div class="code-block"><h6 class="overline-title title"><?php esc_html_e('Image Gallery','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                            <?php

                                                // compatibility fix for elementor
                                                if ($retake === 'yes'){
                                                    $post = $originalpost;
                                                }

                                                WC_Meta_Box_Product_Images::output($post); 
                                            ?>
                                        </div>
                                    </div>
                                    <!-- CATEGORIES AND TAGS -->
                                    <div class="col-xxl-3 col-md-6 marketking_card_gal_cat_tags marketking_categories_block">
                                        <div class="code-block marketking_cattag_card"><h6 class="overline-title title"><?php esc_html_e('Categories','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                            <div class="form-group">
                                                <div class="form-control-wrap">
                                                    <?php
                                                    $selectedarr = array();

                                                    if($exists === 'existing'){
                                                        if ($post->ID !== false && $post->ID !== 0){
                                                            $selectedarr = $product->get_category_ids();
                                                        }
                                                    }

                                                    $excluded_categories = marketking()->get_excluded_category_ids($user_id);
                                                    $args =  array(
                                                        'hierarchical'     => 1,
                                                        'hide_empty'       => 0,
                                                        'class'            => 'form_select',
                                                        'name'             => 'marketking_select_categories',
                                                        'id'               => 'marketking_select_categories',
                                                        'taxonomy'         => 'product_cat',
                                                        'orderby'          => 'name',
                                                        'title_li'         => '',
                                                        'exclude'          => implode(',',$excluded_categories),
                                                        'selected'         => implode(',',$selectedarr)
                                                    );

                                                    // Mutiple categories in pro

                                                    if(defined('MARKETKINGPRO_DIR')){
                                                        $current_id = get_current_user_id();
                                                        if (marketking()->is_vendor_team_member()){
                                                            $current_id = marketking()->get_team_member_parent();
                                                        }
                                                        if (marketking()->vendor_can_multiple_categories($current_id)){
                                                            $args['multiple'] = true;
                                                        }
                                                    }

                                                    wp_dropdown_categories( $args );
                                                    ?>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- CATEGORIES AND TAGS -->
                                    <?php
                                    if(intval(get_option( 'marketking_vendors_can_tags_setting',1 )) === 1){
                                        ?>
                                        <div class="col-xxl-3 col-md-6 marketking_card_gal_cat_tags marketking_tags_block">
                                            <div class="code-block marketking_cattag_card"><h6 class="overline-title title"><?php esc_html_e('Tags','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                <div class="form-group">
                                                    <div class="form-control-wrap">
                                                        <select class="form-select" multiple id="marketking_select_tags" name="marketking_select_tags[]">
                                                            <?php
                                                            $terms = get_terms( array('taxonomy' => 'product_tag', 'hide_empty' => false) );

                                                            foreach ( $terms as $term ){
                                                                if (marketking()->vendor_can_product_tag($user_id,$term->term_id)){
                                                                    if( has_term( $term->term_id, 'product_tag', $prod ) ) {
                                                                        $selected = 'selected="selected"';
                                                                    } else {
                                                                        $selected = '';
                                                                    }

                                                                    echo '<option value="'.esc_attr($term->term_id).'" '.esc_attr($selected).'>'.esc_html($term->name).'</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    
                                </div>
                                <?php do_action('marketking_edit_product_after_tags', $post); ?>

                                <?php
                                // debug test woo3d viewer
                                if(defined('WOO3DV_VERSION')){
                                    ?>
                                    <div class="row">
                                        <div class="col-xxl-6 col-md-6 marketking_card_gal_cat_tags">
                                            <div class="code-block marketking_cattag_card"><h6 class="overline-title title"><?php esc_html_e('Product 3D Model','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                <?php
                                                $_GET['post'] = $productid;
                                                $_GET['action'] = 'edit';
                                                $_GET['page'] = 'woo3dv';
                                                woo3dv_meta_box_output();
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>

                                <?php
                                // QR CODE INTEGRATION // https://wordpress.org/plugins/qr-code-woocommerce/
                                if(class_exists('WooCommerceQrCodes')){
                                    ?>
                                    <div class="row">
                                        <div class="col-xxl-3 col-md-6 marketking_card_gal_cat_tags">
                                            <div class="code-block marketking_cattag_card"><h6 class="overline-title title"><?php esc_html_e('QR Code','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                <?php echo do_shortcode('[wooqr id="'.esc_attr($productid).'" title="1" price="1"]'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }

                                // Simple Auctions Integration
                                if(class_exists('WooCommerce_simple_auction') && intval(get_option('marketking_enable_auctions_setting', 1)) === 1 && defined('MARKETKINGPRO_DIR')){
                                    if (is_object($product)){
                                        if ($product->get_type() === 'auction'){
                                            ?>
                                            <div class="row postbox" id="Auction">
                                                <div class="col-xxl-12 col-md-12 marketking_card_gal_cat_tags">
                                                    <div class="code-block marketking_cattag_card"><h6 class="overline-title title"><?php esc_html_e('Auction','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                        <?php 
                                                        $auctions = new WooCommerce_simple_auction;

                                                        $auctions->woocommerce_simple_auctions_meta_callback();

                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row postbox" id="Automatic_relist_auction">
                                                <div class="col-xxl-12 col-md-12 marketking_card_gal_cat_tags">
                                                    <div class="code-block marketking_cattag_card"><h6 class="overline-title title"><?php esc_html_e('Automatic relist auction','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                        <?php 
                                                        $auctions = new WooCommerce_simple_auction;

                                                        $auctions->woocommerce_simple_auctions_automatic_relist_callback();
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    
                                }

                                ?>

                                <?php
                                if (defined('B2BKING_DIR') && defined('MARKETKINGPRO_DIR') && intval(get_option('marketking_enable_b2bkingintegration_setting', 1)) === 1){
                                    if (intval(get_option('b2bking_show_visibility_vendors_setting_marketking', 1)) === 1){
                                        if(marketking()->vendor_has_panel('b2bkingvisibility')){
                                            ?>
                                            <div class="row">
                                                <br><br>
                                                <div class="col-xxl-9 col-md-12 marketking_card_gal_cat_tags">
                                                    <div class="code-block"><h6 class="overline-title title"><?php esc_html_e('User & Group Visibility (B2B & Wholesale)','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                    <?php

                                                    require_once ( B2BKING_DIR . 'admin/class-b2bking-admin.php' );
                                                    if (!isset($b2bking_admin)){
                                                        $b2bking_admin = new B2bking_Admin;
                                                    }
                                                    $b2bking_admin->b2bking_product_visibility_metabox_content();

                                                    ?>

                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                ?>

                                <!-- ADVERTISING -->
                                <?php
                                if (intval(get_option( 'marketking_enable_advertising_setting', 0 )) === 1){
                                    if(marketking()->vendor_has_panel('advertising')){
                                        ?>
                                        <div class="row">
                                            <div class="col-xxl-12 marketking_card_gal_cat_tags marketking_advertising_block">
                                                <div class="code-block marketking_cattag_card marketking_advertising_card">
                                                    <h6 class="overline-title title"><?php esc_html_e('Product Advertising','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                    <div class="form-group">
                                                        <div class="form-control-wrap">
                                                            <?php
                                                            if ($exists === 'new'){
                                                                esc_html_e('You must save the product first, before being able to advertise it.','marketking-multivendor-marketplace-for-woocommerce');
                                                            } else {

                                                                if (marketking()->is_advertised($productid)){
                                                                    $days_left = marketking()->get_ad_days_left($productid);
                                                                    ?>
                                                                    <div class="marketking_product_advertised"><em class="icon ni ni-star-round"></em><?php echo esc_html__('This product is already advertised:','marketking-multivendor-marketplace-for-woocommerce').' '.$days_left.' '.esc_html__('days left','marketking-multivendor-marketplace-for-woocommerce'); ?></div>

                                                                    <?php
                                                                } else {
                                                                    // not advertised
                                                                }
                                                                
                                                                // first get and display the number of credits the vendor has
                                                                $advertising_credits = marketking()->get_advertising_credits($user_id);

                                                                ?>
                                                                <div class="nk-block">
                                                                    <h6 class="lead-text"><?php esc_html_e('Advertising Credits','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                                    <div class="card">
                                                                        <div class="card-inner card-inner-credits">
                                                                            <div class="between-center flex-wrap flex-md-nowrap g-3 col-xl-12 col-xxl-6">
                                                                                <div class="media media-center gx-3 wide-xs">
                                                                                    <div class="media-object">
                                                                                        <em class="icon icon-circle icon-circle-lg ni ni-coins marketking-icon-gray"></em>
                                                                                    </div>
                                                                                    <div class="media-content">
                                                                                        <p><?php echo esc_html__('Available credits:','marketking-multivendor-marketplace-for-woocommerce').' <strong class="marketking_credits_number">'.esc_html($advertising_credits);?></strong></p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="nk-block-actions flex-shrink-0 buy_credits_initial">
                                                                                    <button type="button" class="btn btn-sm btn-secondary buy_credits_initial_button">
                                                                                        <em class="icon ni ni-cart button-icon"></em><?php esc_html_e('Buy Credits','marketking-multivendor-marketplace-for-woocommerce');?>

                                                                                    <button type="button" class="btn btn-sm btn-gray download_credit_history" value="<?php echo esc_attr($user_id);?>">
                                                                                        <em class="icon ni ni-todo button-icon"></em><?php esc_html_e('View Log','marketking-multivendor-marketplace-for-woocommerce');?>

                                                                                </div>
                                                                                <div class="nk-block-actions flex-shrink-0 buy_credits_second" style="display:none">
                                                                                    <?php
                                                                                    $credit_cost = get_option('marketking_credit_price_setting',1);
                                                                                    echo '<div class="cost_per_credit">'.esc_html__('Cost per credit','marketking-multivendor-marketplace-for-woocommerce').': '.wc_price($credit_cost).'</div>';

                                                                                    ?>
                                                                                    <input type="number" class="add_credits_cart_input form-control form-control-sm" placeholder="<?php esc_attr_e('Number of Credits','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                                                    <a href="#" class="btn btn-primary add_credits_cart_button">
                                                                                        <em class="icon ni ni-cart-fill"></em>
                                                                                        <?php esc_html_e('Add to Cart','marketking-multivendor-marketplace-for-woocommerce');?></a>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <?php

                                                            }

                                                            ?>
                                                            <div class="nk-block">
                                                                <h6 class="lead-text"><?php esc_html_e('Advertise Product','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                                <div class="card">
                                                                    <div class="card-inner card-inner-credits">
                                                                        <div class="between-center flex-wrap flex-md-nowrap g-3 col-xl-12 col-xxl-6">
                                                                            <div class="media media-center gx-3 wide-xs">
                                                                                <div class="media-object">
                                                                                    <em class="icon icon-circle icon-circle-lg ni ni-star-fill marketking-icon-main"></em>
                                                                                </div>
                                                                                <div class="media-content">
                                                                                    <p><?php 

                                                                                    $credit_cost =get_option('marketking_credit_cost_per_day_setting',1);

                                                                                    echo esc_html__('Cost per day:','marketking-multivendor-marketplace-for-woocommerce').' <strong class="marketking_credits_number">'.esc_html($credit_cost).' '.esc_html__('credits','marketking-multivendor-marketplace-for-woocommerce');?></strong></p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="nk-block-actions flex-shrink-0 advertise_initial">
                                                                                <button type="button" class="btn btn-sm btn-primary advertise_initial_button">
                                                                                    <em class="icon ni ni-star button-icon"></em><?php esc_html_e('Advertise Now','marketking-multivendor-marketplace-for-woocommerce');?>

                                                                            </div>
                                                                            <div class="nk-block-actions flex-shrink-0 advertise_second" style="display:none">
                                                                                <?php
                                                                                $credit_cost = get_option('marketking_credit_price_setting',1);
                                                                                echo '<div class="cost_per_credit">'.esc_html__('How many days to advertise?','marketking-multivendor-marketplace-for-woocommerce').'</div>';

                                                                                ?>
                                                                                <input type="number" min="1" step="1" class="advertising_days_input form-control form-control-sm" placeholder="<?php esc_attr_e('Number of advertising days','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                                                <a href="#" class="btn btn-primary purchase_ads_button">
                                                                                    <em class="icon ni ni-star-fill"></em>
                                                                                    <?php esc_html_e('Purchase Ad','marketking-multivendor-marketplace-for-woocommerce');?></a>

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            


                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                                <!-- DESCRIPTION -->
                                <br><br>
                                <div class="row">
                                    <div id="postexcerpt" class="postbox col-xxl-6">
                                        <h6><?php esc_html_e("Description",'marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                        <?php
                                        if ($exists === 'new'){
                                            $content = '';
                                        } else {
                                            $content = $post->post_content;
                                        }
                                        $settings = array(
                                            'textarea_name' => 'longexcerpt',
                                            'quicktags'     => array( 'buttons' => 'em,strong,link' ),
                                            'tinymce'       => array(
                                                'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                                                'theme_advanced_buttons2' => '',
                                            ),
                                            'editor_css'    => '<style>#wp-longexcerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
                                        );

                                        wp_editor( htmlspecialchars_decode( $content, ENT_QUOTES ), 'longexcerpt', apply_filters( 'woocommerce_product_short_description_editor_settings', $settings ) );
                                        ?>
                                    </div>

                                    <!-- SHORT DESCRIPTION -->
                                    <br><br>
                                    <div id="shortpostexcerpt" class="postbox col-xxl-6">
                                        <h6><?php esc_html_e("Short Description",'marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                        <?php
                                        WC_Meta_Box_Product_Short_Description::output($post); 
                                        ?>
                                    </div>
                                </div>

                                <!-- TEST -->

                                <?php
                                
                                ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }

        marketking()->set_product_standby();

    }
?>