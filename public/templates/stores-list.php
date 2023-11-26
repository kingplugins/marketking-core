<?php

/*
Stores List Page
* @version 1.0.2


This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/


?>
<div id="marketking_stores_vendors_table_container">
    <?php
    // store cat serach
    if (defined('MARKETKINGPRO_DIR')){
        if (intval(get_option('marketking_enable_storecategories_setting', 1)) === 1){

            $hidecat = 'no';
            if (isset($showcat)){
                if ($showcat === 'no'){
                    $hidecat = 'yes';
                }
            }
            $args =  array(
                'hierarchical'     => 1,
                'hide_empty'       => 0,
                'class'            => 'form_select',
                'name'             => 'marketking_select_storecategories',
                'id'               => 'marketking_select_storecategories',
                'taxonomy'         => 'storecat',
                'orderby'          => 'name',
                'title_li'         => '',
                'show_option_all' => esc_html__('All Categories','marketking-multivendor-marketplace-for-woocommerce'),
            );

            if ($hidecat === 'no'){
                ?>
                <div class="marketking_frontend_store_categories_select_container">
                    <div class="marketking-select-content-header">
                        <?php // esc_html_e('Store Categories', 'marketking-multivendor-marketplace-for-woocommerce');?>
                    </div> 
                    <?php
                    wp_dropdown_categories( $args );
                    ?>
                </div>
                <?php
            }

        }
    }
    ?>
    <table id="marketking_stores_vendors_table">
        <thead>
            <tr id="marketking_stores_table_header">
                <th><?php esc_html_e('Vendor Pic','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Vendor Name','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                <th><?php esc_html_e('Vendor Rating','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                <?php

                    // Hidden column store categories
                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option('marketking_enable_storecategories_setting', 1)) === 1){
                            ?>
                            <th class="marketking_hidden_column"><?php esc_html_e('Store Categories','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                            <?php
                        }
                    }

                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option('marketking_enable_favorite_setting', 1)) === 1){
                            ?>
                            <th><?php esc_html_e('Follow','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                            <?php
                        }
                    }
                ?>
                <th><?php esc_html_e('Actions','marketking-multivendor-marketplace-for-woocommerce'); ?></th>

            </tr>
        </thead>
        <tbody id="marketking_stores_table_tbody">
            <?php

            foreach ( $vendors as $user ) {

                $user_id = $user->ID;

                if (marketking()->vendor_is_inactive($user_id)){
                    continue;
                    //skip
                }
                
                $original_user_id = $user_id;
                $username = $user->user_login;
                $store_name = marketking()->get_store_name_display($user_id);
                $store_link = marketking()->get_store_link($user_id);

                $profile_pic = marketking()->get_store_profile_image_link($user_id);
                if (empty($profile_pic)){
                    $profile_pic = MARKETKINGCORE_URL. 'includes/assets/images/store-profile.png';

                } else {
                    $profile_pic = marketking()->get_resized_image( $profile_pic, 'thumbnail' );
                }

                echo
                '<tr>
                    <td class="marketking_vendor_stores_left_column"><a class="marketking_vendor_link" href="'.esc_attr($store_link).'"><img class="marketking_vendor_profile" src='.esc_attr($profile_pic).'></a></td>
                    <td class="marketking_vendor_name"><a class="marketking_vendor_link" href="'.esc_attr($store_link).'">'.esc_html( $store_name ).'</a>';
                    do_action('marketking_vendor_list_after_name', $user_id);
                    echo '</td>';

                    // rating
                    $rating = marketking()->get_vendor_rating($user_id);
                    ?>
                    <td class="marketking_vendor_rating">
                        <?php 
                            if (intval($rating['count'])!==0){

                                // show rating
                                if (intval($rating['count']) === 1){
                                    $review = esc_html__('review','marketking-multivendor-marketplace-for-woocommerce');
                                } else {
                                    $review = esc_html__('reviews','marketking-multivendor-marketplace-for-woocommerce');
                                }
                                echo '<strong>'.esc_html__('Rating:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.esc_html($rating['rating']).' '.esc_html__('out of 5','marketking-multivendor-marketplace-for-woocommerce');
                                echo '<br>';

                            }
                        ?>
                    </td>
                    <?php

                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option('marketking_enable_storecategories_setting', 1)) === 1){
                            
                            // Store Cat
                            $selectedarr = get_user_meta($user_id,'marketking_store_categories', true);

                            $cats = '';
                            if (!empty($selectedarr)){
                                foreach ($selectedarr as $index => $catid){
                                    $catname = get_term($catid)->name;
                                    $selectedarr[$index] = $catname;
                                }

                                $cats = implode(', ',$selectedarr);
                            }
                            
                            

                            ?>
                            <td class="marketking_hidden_column"><?php echo $cats; ?></td>
                            <?php
                        }
                    }


                    // if follow stores, show follow button
                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option('marketking_enable_favorite_setting', 1)) === 1){
                            $vendor_id = $user_id;
                            $current_user_id = get_current_user_id();
                            if ($vendor_id !== $current_user_id){
                                if (is_user_logged_in()){
                                    $follows = get_user_meta($current_user_id,'marketking_follows_vendor_'.$vendor_id, true);

                                    ?>
                                    <td class="marketking_vendor_follow">
                                        <?php do_action('marketking_vendor_follow_stores_list_start', $vendor_id); ?>

                                        <button class="marketking_follow_button" value="<?php echo esc_attr($vendor_id);?>"><?php
                                        if ($follows !== 'yes'){
                                            esc_html_e('Follow','marketking-multivendor-marketplace-for-woocommerce');
                                        } else if ($follows === 'yes'){
                                            esc_html_e('Following','marketking-multivendor-marketplace-for-woocommerce');
                                        }
                                        
                                        ?></button>
                                        <?php do_action('marketking_vendor_follow_stores_list_end', $vendor_id); ?>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td class="marketking_vendor_follow">
                                        <?php do_action('marketking_vendor_follow_stores_list_start', $vendor_id); ?>

                                        <?php do_action('marketking_vendor_follow_stores_list_end', $vendor_id); ?>
                                    </td>
                                    <?php
                                }
                            } else {
                                ?>
                                <td class="marketking_vendor_follow">
                                    <?php do_action('marketking_vendor_follow_stores_list_start', $vendor_id); ?>

                                    <?php esc_html_e('This is your store.','marketking-multivendor-marketplace-for-woocommerce');?>

                                    <?php do_action('marketking_vendor_follow_stores_list_end', $vendor_id); ?>
                                </td>
                                <?php
                            }

                        }
                    }



                    ?>
                    <td class="marketking_vendor_shop">                                 
                        <a class="marketking_vendor_link" href="<?php echo esc_attr($store_link);?>"> <span class="dashicons dashicons-arrow-right-alt2 marketking_arrow_icon_view_shop"></span></a>
                    </td>
                    <?php
                echo '</tr>';
            }

            ?>
           
        </tbody>
        <tfoot class="marketking_hidden_column">
            <tr class="nk-tb-item nk-tb-head">
               <th><?php esc_html_e('Vendor Pic','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
               <th><?php esc_html_e('Vendor Name','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
               <th><?php esc_html_e('Vendor Rating','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
               <?php

                   // Hidden column store categories
                   if (defined('MARKETKINGPRO_DIR')){
                       if (intval(get_option('marketking_enable_storecategories_setting', 1)) === 1){
                           ?>
                           <th class="marketking_hidden_column marketking_store_categories_search"><?php esc_html_e('Store Categories','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                           <?php
                       }
                   }

                   if (defined('MARKETKINGPRO_DIR')){
                       if (intval(get_option('marketking_enable_favorite_setting', 1)) === 1){
                           ?>
                           <th><?php esc_html_e('Follow','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                           <?php
                       }
                   }
               ?>
               <th><?php esc_html_e('Actions','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
            </tr>
        </tfoot>
        
    </table>
</div>
