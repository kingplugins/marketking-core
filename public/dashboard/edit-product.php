<?php

/*

Edit Product Page
* @version 1.0.1

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file directly under your theme (or child theme) folder and then edit it there. 

For example, if your theme is storefront, you can copy this file directly under wp-content/themes/storefront/ and then edit it with your own custom content and changes.

*/


?>
<?php
    if(marketking()->vendor_has_panel('products')){
        $checkedval = 0;
        if (marketking()->is_vendor_team_member()){
            $checkedval = intval(get_user_meta(get_current_user_id(),'marketking_teammember_available_panel_editproducts', true));
        }

        $productid = sanitize_text_field(marketking()->get_pagenr_query_var());
        $canadd = marketking()->vendor_can_add_more_products($user_id);

        // if product exists
        $post = get_post($productid);
        $product = wc_get_product($productid);
        $exists = 'existing';

        // get original query var
        if (get_query_var('pagenr') === 'add'){
            $exists = 'new'; 
        }

        // save post and retake it later - this helps compatibility with elementor, which changes the post ID for some reason
        $retake = 'no';
        if (is_object($post)){
            $originalpost = $post;
            $originalproduct = $product;
            $retake = 'yes';
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

                                if ($exists === 'new'){
                                    $text = esc_html__('Save New Product','marketking-multivendor-marketplace-for-woocommerce');
                                    $icon = 'ni-plus';
                                    $actionedit = 'add';
                                } else {
                                    $text = esc_html__('Update Product','marketking-multivendor-marketplace-for-woocommerce');
                                    $icon = 'ni-edit-fill';
                                    $actionedit = 'edit';
                                }

                                ?>
                                <input id="marketking_edit_product_action_edit" type="hidden" value="<?php echo esc_attr($actionedit);?>">
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

                                                ?>
                                                <p class="marketking_status_text">- &nbsp;</p>
                                                <select name="marketking_edit_product_status" id="marketking_edit_product_status" class="marketking_status_<?php echo esc_attr($status);?>">
                                                    <?php
                                                        // check if user is allowed to publish directly
                                                        if (marketking()->vendor_can_publish_products($user_id)){
                                                            ?>
                                                                <option value="publish" <?php selected($status, 'publish', true);?>><?php esc_html_e('Published');?></option>
                                                            <?php
                                                        } else {
                                                            ?>
                                                                <option value="pending" <?php selected($status, 'pending', true);?>><?php esc_html_e('Ready for Review');?></option>
                                                            <?php
                                                        }
                                                    ?>
                                                    <option value="draft" <?php selected($status, 'draft', true);?>><?php esc_html_e('Draft');?></option>
                                                </select>&nbsp;
                                                <?php
                                                if (!marketking()->vendor_can_publish_products($user_id)){
                                                    $tip = esc_html__('When a product is ready to be published, save it as "Ready for Review", to let the admins it is ready. If you are still working on, and making changes to the product, save it as "Draft".','marketking-multivendor-marketplace-for-woocommerce');
                                                    echo ' '.wc_help_tip($tip, false);
                                                }
                                                ?>
                                            </div>
                                        </div><!-- .nk-block-head-content -->
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <input type="hidden" id="marketking_save_product_button_id" value="<?php echo esc_attr($productid);?>">
                                                        <input type="hidden" id="post_ID" value="<?php echo esc_attr($productid);?>">
                                                        <li class="nk-block-tools-opt">
                                                            <div id="marketking_save_product_button">
                                                                <a href="#" class="toggle btn btn-icon btn-primary d-md-none"><em class="icon ni <?php echo esc_attr($icon);?>"></em></a>
                                                                <a href="#" class="toggle btn btn-primary d-none d-md-inline-flex"><em class="icon ni <?php echo esc_attr($icon);?>"></em><span><?php echo esc_html($text); ?></span></a>
                                                            </div>
                                                            <?php
                                                            if ($exists === 'existing'){
                                                                // additional buttons for View Product and Remove Product
                                                                ?>
                                                                <div class="dropdown">
                                                                    <a href="#" class="dropdown-toggle btn btn-icon btn-gray btn-trigger ml-2 text-white pl-2 pr-3" data-toggle="dropdown"><em class="icon ni ni-more-h"></em><?php esc_html_e('More','marketking-multivendor-marketplace-for-woocommerce'); ?></a>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <ul class="link-list-opt no-bdr">
                                                                            <li><a href="<?php 
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
                                if (isset($_GET['add'])){
                                    $add = sanitize_text_field($_GET['add']);;
                                    if ($add === 'success'){
                                        ?>                                    
                                        <div class="alert alert-primary alert-icon"><em class="icon ni ni-check-circle"></em> <strong><?php esc_html_e('Your product has been created successfully','marketking-multivendor-marketplace-for-woocommerce');?></strong>. <?php esc_html_e('You can now continue to edit it','marketking-multivendor-marketplace-for-woocommerce');?>.</div>
                                        <?php
                                    }
                                }
                                if (isset($_GET['update'])){
                                    $add = sanitize_text_field($_GET['update']);;
                                    if ($add === 'success'){
                                        ?>                                    
                                        <div class="alert alert-primary alert-icon"><em class="icon ni ni-check-circle"></em> <strong><?php esc_html_e('Your product has been updated successfully','marketking-multivendor-marketplace-for-woocommerce');?></strong>.</div>
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
                                    <div class="col-xxl-3 col-md-6 marketking_card_gal_cat_tags">
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
                                                }
                                            
                                            } else {
                                                // new product
                                                $src = wc_placeholder_img_src();
                                            }
                                            echo esc_attr($src);
                                            ?>">
                                            <input type="hidden" name="marketking_edit_product_main_image_value" id="marketking_edit_product_main_image_value" value="<?php echo esc_attr($imageval);?>">

                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-md-6 marketking_card_gal_cat_tags">
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
                                    <div class="col-xxl-3 col-md-6 marketking_card_gal_cat_tags">
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
                                        <div class="col-xxl-3 col-md-6 marketking_card_gal_cat_tags">
                                            <div class="code-block marketking_cattag_card"><h6 class="overline-title title"><?php esc_html_e('Tags','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                <div class="form-group">
                                                    <div class="form-control-wrap">
                                                        <select class="form-select " multiple id="marketking_select_tags" name="marketking_select_tags[]">
                                                            <?php
                                                            $terms = get_terms( array('taxonomy' => 'product_tag', 'hide_empty' => false) );

                                                            foreach ( $terms as $term ){
                                                                if( has_term( $term->term_id, 'product_tag', $prod ) ) {
                                                                    $selected = 'selected="selected"';
                                                                } else {
                                                                    $selected = '';
                                                                }

                                                                echo '<option value="'.esc_attr($term->term_id).'" '.esc_attr($selected).'>'.esc_html($term->name).'</option>';
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
                                ?>

                                <?php
                                if (defined('B2BKING_DIR') && defined('MARKETKINGPRO_DIR') && intval(get_option('marketking_enable_b2bkingintegration_setting', 1)) === 1){
                                    if (intval(get_option('b2bking_show_visibility_vendors_setting_marketking', 1)) === 1){
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