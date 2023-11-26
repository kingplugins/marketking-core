<?php

/*

Products Dashboard Page
* @version 1.0.2

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/


?><?php
if(marketking()->vendor_has_panel('products')){
    $checkedval = 0;
    if (marketking()->is_vendor_team_member()){
        $checkedval = intval(get_user_meta(get_current_user_id(),'marketking_teammember_available_panel_editproducts', true));
    }
    
    ?>
    <div class="nk-content marketking_products_page">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title"><?php esc_html_e('Products','marketking-multivendor-marketplace-for-woocommerce'); ?></h3>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <div>
                                        <ul class="nk-block-tools g-3">
                                            <?php

                                            ?>
                                            <li>
                                                <div class="form-control-wrap">
                                                    <div class="form-icon form-icon-right">
                                                        <em class="icon ni ni-search"></em>
                                                    </div>
                                                    <input type="text" class="form-control" id="marketking_products_search" placeholder="<?php esc_html_e('Search products...','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                </div>
                                            </li>
                                            <li class="marketking_status_dropdown_menu_wrapper"><div class="dropdown">
                                                <a href="#" class="dropdown-toggle dropdown-indicator btn btn-outline-light btn-white" data-toggle="dropdown"><span class="marketking_status_text_desktop"><?php esc_html_e('Status','marketking-multivendor-marketplace-for-woocommerce');?></span><span class="marketking_status_text_mobile"><em class="icon ni ni-check-round-cut"></em></span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end marketking_status_dropdown_menu">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li class="marketking_status_option"><a href="#"><input type="hidden" class="status_value" value="<?php esc_html_e('Published','marketking-multivendor-marketplace-for-woocommerce');?>"><em class="icon ni ni-check-round-cut"></em><span><?php esc_html_e('Published','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                                        <li class="marketking_status_option"><a href="#"><input type="hidden" class="status_value" value="<?php esc_html_e('Draft','marketking-multivendor-marketplace-for-woocommerce');?>"><em class="icon ni ni-pen-alt-fill"></em><span><?php esc_html_e('Draft','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                                        <li class="marketking_status_option"><a href="#"><input type="hidden" class="status_value" value="<?php esc_html_e('Pending','marketking-multivendor-marketplace-for-woocommerce');?>"><em class="icon ni ni-update"></em><span><?php esc_html_e('Pending Review','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                                    </ul>
                                                </div>
                                            </div></li>
                                            <?php

                                            if(intval(get_option( 'marketking_vendors_can_newproducts_setting',1 )) === 1){
                                                if (apply_filters('marketking_vendors_can_add_products', true)){
                                                    // either not team member, or team member with permission to add
                                                    if (!marketking()->is_vendor_team_member() || $checkedval === 1){
                                                        if (apply_filters('marketking_vendors_can_add_products', true)){

                                                            if(marketking()->vendor_can_add_more_products($user_id)){
                                                                ?>
                                                                <li class="nk-block-tools-opt">
                                                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'edit-product/add');?>" class="btn btn-primary d-md-inline-flex"><em class="icon ni ni-plus"></em><span><?php esc_html_e('Add Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a>
                                                                </li>
                                                                <?php
                                                            } else {
                                                                // show some error message that they reached the max nr of products
                                                                ?>
                                                                <button type="button" class="btn btn-gray d-none d-md-inline-flex" disabled="disabled"><em class="icon ni ni-plus"></em>&nbsp;&nbsp;<?php esc_html_e('Add Product (Max Limit Reached)','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            ?>
                                        </ul>
                                        <?php
                                        if (defined('MARKETKINGPRO_DIR')){
                                            if (intval(get_option('marketking_enable_importexport_setting', 1)) === 1){
                                                ?>
                                                <div class="marketking_importexport_buttons_container">
                                                    <?php
                                                    if(intval(get_option( 'marketking_vendors_can_newproducts_setting',1 )) === 1){
                                                        if (apply_filters('marketking_vendors_can_add_products', true)){
                                                            if(marketking()->vendor_can_add_more_products($user_id)){
                                                                // import option = only if vendor can add new products
                                                                if (!marketking()->is_vendor_team_member()){
                                                                    ?>
                                                                    
                                                                        <a href="<?php echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'import-products');?>" class="btn btn-sm btn-secondary d-none d-md-inline-flex"><em class="icon ni ni-upload"></em><span><?php esc_html_e('Import','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a>
                                                                    
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    }

                                                    // export option
                                                    ?>
                                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'export-products');?>" class="btn btn-sm btn-secondary d-none d-md-inline-flex"><em class="icon ni ni-download"></em><span><?php esc_html_e('Export','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a>
                                                    <?php
                                                ?>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
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
                    ?>

                    <table id="marketking_dashboard_products_table" class="nk-tb-list is-separate mb-3">
                        <thead>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col"><span class="sub-text"><?php esc_html_e('Name','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                <th class="nk-tb-col tb-col-md marketking-column-mid"><span class="sub-text"><?php esc_html_e('SKU','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                <?php
                                // product advertisement
                                if (intval(get_option( 'marketking_enable_advertising_setting', 0 )) === 1){
                                    if(marketking()->vendor_has_panel('advertising')){
                                        ?>
                                        <th class="nk-tb-col tb-col-md marketking-column-mid"><span class="sub-text"><?php esc_html_e('Advertisement','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                        <?php
                                    }
                                }
                                ?>
                                <th class="nk-tb-col marketking-column-small"><span class="sub-text"><?php esc_html_e('Price','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                <th class="nk-tb-col tb-col-md marketking-column-mid"><span class="sub-text"><?php esc_html_e('Stock','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                <th class="nk-tb-col tb-col-md marketking-column-mid"><span class="sub-text"><?php esc_html_e('Categories','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                <th class="nk-tb-col tb-col-md marketking-column-small"><span class="sub-text"><?php esc_html_e('Type','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                <th class="nk-tb-col tb-col-md marketking-column-small"><span class="sub-text"><?php esc_html_e('Status','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                <th class="nk-tb-col tb-col-md marketking-column-mid"><span class="sub-text"><?php esc_html_e('Tags','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                <th class="nk-tb-col tb-col-md marketking-column-mid"><span class="sub-text"><?php esc_html_e('Date Published','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                <th class="nk-tb-col marketking-column-min"><span class="sub-text"><?php esc_html_e('Actions','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>                           

                            </tr>
                        </thead>
                        <?php
                        if (!marketking()->load_tables_with_ajax(get_current_user_id())){
                            ?>
                            <tfoot>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col tb-non-tools"><?php esc_html_e('name','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                    <th class="nk-tb-col tb-col-md tb-non-tools"><?php esc_html_e('SKU','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                    <?php
                                    // product advertisement
                                    if (intval(get_option( 'marketking_enable_advertising_setting', 0 )) === 1){
                                        if(marketking()->vendor_has_panel('advertising')){
                                            ?>
                                            <th class="nk-tb-col tb-col-md tb-non-tools"><?php esc_html_e('Advertisement','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <th class="nk-tb-col tb-non-tools"><?php esc_html_e('price','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                    <th class="nk-tb-col tb-col-md tb-non-tools"><?php esc_html_e('stock','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                    <th class="nk-tb-col tb-col-md tb-non-tools"><?php esc_html_e('categories','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                    <th class="nk-tb-col tb-col-md tb-non-tools"><?php esc_html_e('type','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                    <th class="nk-tb-col tb-col-md tb-non-tools"><?php esc_html_e('status','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                    <th class="nk-tb-col tb-col-md tb-non-tools"><?php esc_html_e('tags','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                    <th class="nk-tb-col tb-col-md tb-non-tools"><?php esc_html_e('date','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                    <th class="nk-tb-col tb-non-tools marketking-column-min"></th>
                                </tr>
                            </tfoot>
                            <?php
                        }
                        ?>
                        <tbody>
                            <?php

                            if (!marketking()->load_tables_with_ajax(get_current_user_id())){


                                $vendor_products = wc_get_products( array( 
                                    'numberposts' => -1,
                                    'post_status'    => array( 'draft', 'pending', 'private', 'publish' ),
                                    'author'   => $user_id,
                                    'orderby' => 'date',
                                    'order' => 'DESC',
                                ));


                                if (intval(get_option( 'marketking_enable_bookings_setting', 0 )) === 1){
                                    if(marketking()->vendor_has_panel('bookings')){

                                        if ( class_exists( 'WC_Bookings' ) ) {
                                            $vendor_booking_products = wc_get_products( array( 
                                                'numberposts' => -1,
                                                'post_status'    => array( 'draft', 'pending', 'private', 'publish' ),
                                                'author'   => $user_id,
                                                'orderby' => 'date',
                                                'order' => 'DESC',
                                                'type' =>'booking'
                                            ));

                                            $vendor_products = array_merge($vendor_products, $vendor_booking_products);
                                        }
                                    }
                                }

                                foreach ($vendor_products as $product){

                                    // add custom filter to remove products
                                    $allowed_product = apply_filters('marketking_allowed_vendor_edit_product', true, $product);
                                    if (!$allowed_product){
                                        continue;
                                    }
                                    ?>
                                    <tr class="nk-tb-item">
                                        <td class="nk-tb-col marketking-column-small">
                                            <a href="<?php 

                                            // either not team member, or team member with permission to add
                                            if (!marketking()->is_vendor_team_member() || $checkedval === 1){
                                                if (apply_filters('marketking_vendors_can_edit_products', true)){

                                                    echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'edit-product/'.$product->get_id());
                                                } else {
                                                    echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'products');
                                                }
                                            } else {
                                                echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'products';
                                            }

                                                ?>">
                                                <span class="tb-product">
                                                <?php
                                                $src = wp_get_attachment_url( $product->get_image_id() );
                                                if (empty($src)){
                                                    $src = wc_placeholder_img_src();
                                                } else {
                                                    $src = marketking()->get_resized_image($src, 'thumbnail');
                                                }
                                                $title = $product->get_title();
                                                if (empty($title)){
                                                    $title = '—';
                                                }
                                                $sku = $product->get_sku();
                                                if (empty($sku)){
                                                    $sku = '—';
                                                }
                                                $price = apply_filters('marketking_products_page_price', $product->get_price(), $product);
                                                
                                                $categories = $product->get_category_ids();
                                                $categoriestext = '';
                                                foreach ($categories as $cat){
                                                    if( $term = get_term_by( 'id', $cat, 'product_cat' ) ){
                                                        $categoriestext .= $term->name.', ';
                                                    }
                                                }
                                                $categoriestext = substr($categoriestext, 0, -2);
                                                if (empty($categoriestext)){
                                                    $categoriestext = '—';
                                                }

                                                $tags = $product->get_tag_ids();
                                                $tagstext = '';
                                                foreach ($tags as $tag){
                                                    if( $term = get_term_by( 'id', $tag, 'product_tag' ) ){
                                                        $tagstext .= $term->name.', ';
                                                    }
                                                }
                                                $tagstext = substr($tagstext, 0, -2);
                                                if (empty($tagstext)){
                                                    $tagstext = '—';
                                                }
                                                $type = ucfirst($product->get_type());
                                                $time = $product->get_date_created();
                                                if ($time === null){
                                                    $time = $product->get_date_modified();
                                                }

                                                if ($time){
                                                    $timestamp = $time->getTimestamp();
                                                    $date = $time->date_i18n( get_option('date_format'), $timestamp+(get_option('gmt_offset')*3600) );
                                                } else {
                                                    $date = '-';
                                                }
                                                
                                                

                                                ?>
                                                <img src="<?php echo esc_attr($src);?>" alt="" class="thumb">
                                                <span class="title"><?php echo esc_html($title);?></span>
                                                </span>
                                            </a>

                                        </td>
                                        <td class="nk-tb-col tb-col-md marketking-column-mid">
                                            <span class="tb-sub marketking-column-small"><?php echo esc_html($sku);?></span>
                                        </td>
                                        <?php
                                        // product advertisement
                                        if (intval(get_option( 'marketking_enable_advertising_setting', 0 )) === 1){
                                            if(marketking()->vendor_has_panel('advertising')){
                                                ?>
                                                <td class="nk-tb-col tb-col-md marketking-column-mid" data-order="<?php 

                                                    if (marketking()->is_advertised($product->get_id())){
                                                        echo marketking()->get_ad_days_left_val($product->get_id());
                                                    } else {
                                                        echo '-1';
                                                    }

                                                ?>">
                                                    <span class="tb-sub marketking-column-small">
                                                        <?php 
                                                        if (marketking()->is_advertised($product->get_id())){
                                                            $daysleft = marketking()->get_ad_days_left($product->get_id());
                                                            echo '<div class="marketking_advertised_column">'.$daysleft.' '.esc_html__('days left','marketking-multivendor-marketplace-for-woocommerce').'</div>';
                                                            
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?></span>
                                                </td>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <td class="nk-tb-col marketking-column-small" data-order="<?php echo esc_attr($price);?>">
                                            <span class="tb-lead"><?php 
                                            if (!empty($price)){
                                                echo wc_price($price);
                                            } else {
                                                echo '—';
                                            }
                                            ?></span>
                                        </td>
                                        <td class="nk-tb-col tb-col-md marketking-column-mid">
                                            <?php
                                            $stock = $product->get_stock_status();
                                            $stocktext = $badge = '';
                                            if ($stock === 'instock'){
                                                $badge = 'badge-gray';
                                                $stocktext = esc_html__('In stock', 'marketking-multivendor-marketplace-for-woocommerce');
                                            } else if ($stock === 'outofstock'){
                                                $badge = 'badge-warning';
                                                $stocktext = esc_html__('Out of stock', 'marketking-multivendor-marketplace-for-woocommerce');
                                            } else if ($stock === 'onbackorder'){
                                                $badge = 'badge-gray';
                                                $stocktext = esc_html__('On backorder', 'marketking-multivendor-marketplace-for-woocommerce');
                                            }

                                            if (apply_filters('marketking_dashboard_products_show_stock_qty', false)){
                                                $qtystock = $product->get_stock_quantity();
                                                if (!empty($qtystock) || $qtystock === 0){
                                                    $stocktext.= ' ('.$qtystock.')';
                                                }
                                            }
                                            ?>
                                            <span class="badge badge-sm badge-dot has-bg <?php echo esc_attr($badge);?> d-none d-mb-inline-flex"><?php
                                            echo esc_html(ucfirst($stocktext));
                                            ?></span>
                                        </td>
                                        <td class="nk-tb-col tb-col-md marketking-column-mid">
                                            <span class="tb-sub"><?php echo esc_html($categoriestext);?></span>
                                        </td>
                                        <td class="nk-tb-col tb-col-md marketking-column-small">
                                            <span class="tb-sub"><?php echo esc_html($type);?></span>
                                        </td>
                                        <td class="nk-tb-col tb-col-md marketking-column-small">
                                            <?php
                                            $status = get_post_status($product->get_id());
                                            $statustext = $badge = '';
                                            if ($status === 'publish'){
                                                $badge = 'badge-success';
                                                $statustext = esc_html__('Published','marketking-multivendor-marketplace-for-woocommerce');
                                            } else if ($status === 'draft'){
                                                $badge = 'badge-gray';
                                                $statustext = esc_html__('Draft','marketking-multivendor-marketplace-for-woocommerce');
                                            } else if ($status === 'pending'){
                                                 $badge = 'badge-info';
                                                 $statustext = esc_html__('Pending','marketking-multivendor-marketplace-for-woocommerce');
                                            } else if ($status === 'hidden'){
                                                 $badge = 'badge-gray';
                                                 $statustext = esc_html__('Hidden','marketking-multivendor-marketplace-for-woocommerce');
                                            } else if ($status === 'on backorder'){
                                                 $badge = 'badge-gray';
                                                 $statustext = esc_html__('On Backorder','marketking-multivendor-marketplace-for-woocommerce');
                                            } else if ($status === 'on-backorder'){
                                                 $badge = 'badge-gray';
                                                 $statustext = esc_html__('On Backorder','marketking-multivendor-marketplace-for-woocommerce');
                                            } else {
                                                $badge = 'badge-gray';
                                                $statustext = ucfirst($status);
                                            }
                                            ?>
                                            <span class="badge badge-sm badge-dot has-bg <?php echo esc_attr($badge);?> d-none d-mb-inline-flex"><?php
                                            echo esc_html(ucfirst($statustext));
                                            ?></span>
                                        </td>
                                        <td class="nk-tb-col tb-col-md marketking-column-mid">
                                            <span class="tb-sub"><?php echo esc_html($tagstext);?></span>
                                        </td>
                                        <td class="nk-tb-col tb-col-md marketking-column-mid" data-order="<?php echo esc_attr($timestamp);?>">
                                            <span class="tb-sub"><?php echo esc_html($date);?></span>
                                        </td>
                                        <td class="nk-tb-col marketking-column-min">
                                            <ul class="nk-tb-actions gx-1 my-n1">
                                                <li class="mr-n1">
                                                    <div class="dropdown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <ul class="link-list-opt no-bdr">
                                                                <?php
                                                                // either not team member, or team member with permission to add
                                                                if (apply_filters('marketking_vendors_can_edit_products', true)){

                                                                    if (!marketking()->is_vendor_team_member() || $checkedval === 1){
                                                                        ?>
                                                                        <li><a href="<?php echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'edit-product/'.$product->get_id());?>"><em class="icon ni ni-edit"></em><span><?php esc_html_e('Edit Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
                                                                        <?php
                                                                    }
                                                                }

                                                                ?>
                                                                <li><a target="_blank" href="<?php 
                                                                $permalink = $product->get_permalink();
                                                                echo esc_attr($permalink);
                                                                ?>
                                                                "><em class="icon ni ni-eye"></em><span><?php esc_html_e('View Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>


                                                                <li><input type="hidden" class="marketking_product_url" value="<?php echo esc_attr($product->get_permalink());?>"><a href="#" class="marketking_copy_url"><em class="icon ni ni-copy"></em><span><?php esc_html_e('Copy URL','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>

                                                                <?php


                                                                if(intval(get_option( 'marketking_vendors_can_newproducts_setting',1 )) === 1){
                                                                    if (apply_filters('marketking_vendors_can_add_products', true)){
                                                                        // either not team member, or team member with permission to add
                                                                        if (!marketking()->is_vendor_team_member() || $checkedval === 1){
                                                                            if(marketking()->vendor_can_add_more_products($user_id)){
                                                                                ?>
                                                                                <li><input type="hidden" class="marketking_input_id" value="<?php echo esc_attr($product->get_id());?>"><a href="#" class="marketking_clone_product"><em class="icon ni ni-copy-fill"></em><span><?php esc_html_e('Clone Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
                                                                                <?php
                                                                            }
                                                                        }
                                                                    }
                                                                }

                                                                // either not team member, or team member with permission to add
                                                                if (!marketking()->is_vendor_team_member() || $checkedval === 1){
                                                                    if (apply_filters('marketking_vendors_can_edit_products', true)){

                                                                        ?>
                                                                        <li><a href="#" class="toggle marketking_delete_button" value="<?php echo esc_attr($product->get_id());?>"><em class="icon ni ni-trash"></em><span><?php esc_html_e('Delete Product','marketking-multivendor-marketplace-for-woocommerce'); ?></span></a></li>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </td>
                                        
                                    </tr>
                                    <?php
                                }
                            }
                            
                            ?>
                            
                        </tbody>
                        
                    </table>

                </div>
            </div>
        </div>
    </div>
    <?php

    marketking()->set_product_standby();
}
?>