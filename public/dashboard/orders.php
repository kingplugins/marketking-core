<?php

/*

Orders View Dashboard Page
* @version 1.0.2

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/


?><?php
if (intval(get_option( 'marketking_agents_can_manage_orders_setting', 1 )) === 1){
    if(marketking()->vendor_has_panel('orders')){
        ?>
        <div class="nk-content marketking_orders_page" style="margin-top:65px">
            <div class="container-fluid">
                <div class="nk-content-inner">
                    <div class="nk-content-body">
                        <div class="nk-block-head nk-block-head-sm">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h3 class="nk-block-title page-title"><?php esc_html_e('Orders','marketking-multivendor-marketplace-for-woocommerce');?></h3>
                                    <div class="nk-block-des text-soft">
                                        <p><?php esc_html_e('Here you can view and manage all orders assigned to you.', 'marketking-multivendor-marketplace-for-woocommerce');?></p>
                                    </div>
                                </div><!-- .nk-block-head-content -->
                                <div class="nk-block-head-content">
                                    <div class="toggle-wrap nk-block-tools-toggle">
                                        <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="more-options"><em class="icon ni ni-more-v"></em></a>
                                        <div class="toggle-expand-content" data-content="more-options">
                                            <ul class="nk-block-tools g-3">

                                                <li class="marketking_status_dropdown_menu_wrapper"><div class="drodown">
                                                    <a href="#" class="dropdown-toggle dropdown-indicator btn btn-outline-light btn-white" data-toggle="dropdown"><?php esc_html_e('Status','marketking-multivendor-marketplace-for-woocommerce');?>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end marketking_status_dropdown_menu">
                                                        <ul class="link-list-opt no-bdr">
                                                            <li class="marketking_status_option"><a href="#">
                                                                <input type="hidden" class="status_value" value="<?php esc_html_e('Pending Payment','marketking-multivendor-marketplace-for-woocommerce');?>"><em class="icon ni ni-update"></em><span><?php esc_html_e('Pending Payment','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>

                                                            <?php do_action('marketking_orders_page_status_dropdown'); ?>

                                                            <li class="marketking_status_option"><a href="#"><input type="hidden" class="status_value" value="<?php esc_html_e('Processing','marketking-multivendor-marketplace-for-woocommerce');?>"><em class="icon ni ni-setting"></em><span><?php esc_html_e('Processing','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                                            <li class="marketking_status_option"><a href="#"><input type="hidden" class="status_value" value="<?php esc_html_e('On Hold','marketking-multivendor-marketplace-for-woocommerce');?>"><em class="icon ni ni-pause-circle"></em><span><?php esc_html_e('On Hold','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                                            <li class="marketking_status_option"><a href="#"><input type="hidden" class="status_value" value="<?php esc_html_e('Completed','marketking-multivendor-marketplace-for-woocommerce');?>"><em class="icon ni ni-check-round-cut"></em><span><?php esc_html_e('Completed','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                                            <li class="marketking_status_option"><a href="#"><input type="hidden" class="status_value" value="<?php esc_html_e('Refunded','marketking-multivendor-marketplace-for-woocommerce');?>"><em class="icon ni ni-invest"></em><span><?php esc_html_e('Refunded','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                                            <li class="marketking_status_option"><a href="#"><input type="hidden" class="status_value" value="<?php esc_html_e('Cancelled','marketking-multivendor-marketplace-for-woocommerce');?>"><em class="icon ni ni-na"></em><span><?php esc_html_e('Cancelled','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                                            <li class="marketking_status_option"><a href="#"><input type="hidden" class="status_value" value="<?php esc_html_e('Failed','marketking-multivendor-marketplace-for-woocommerce');?>"><em class="icon ni ni-cross-circle"></em><span><?php esc_html_e('Failed','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                                        </ul>
                                                    </div>
                                                </div></li>

                                                <li>
                                                    <div class="form-control-wrap">
                                                        <div class="form-icon form-icon-right">
                                                            <em class="icon ni ni-search"></em>
                                                        </div>
                                                        <input type="text" class="form-control" id="marketking_orders_search" placeholder="<?php esc_html_e('Search orders...','marketking-multivendor-marketplace-for-woocommerce');?>">
                                                    </div>
                                                </li>
                                               
                                            </ul>
                                        </div>
                                    </div>
                                </div><!-- .nk-block-head-content -->
                            </div><!-- .nk-block-between -->
                        </div><!-- .nk-block-head -->
                        <table id="marketking_dashboard_orders_table" class="nk-tb-list is-separate mb-3">
                            <thead>
                                <tr class="nk-tb-item nk-tb-head">
                                    <th class="nk-tb-col"><span class="sub-text"><?php esc_html_e('Order','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                    <th class="nk-tb-col tb-col-md"><span class="sub-text"><?php esc_html_e('Date','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                    <th class="nk-tb-col tb-col-md"><span class="sub-text d-none d-mb-block"><?php esc_html_e('Status','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                    <?php do_action('marketking_orders_page_column_header'); ?>
                                    <th class="nk-tb-col tb-col-sm"><span class="sub-text"><?php esc_html_e('Customer','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                    <th class="nk-tb-col tb-col-md"><span class="sub-text"><?php esc_html_e('Purchased','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                    <th class="nk-tb-col"><span class="sub-text"><?php esc_html_e('Order Total','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                    <th class="nk-tb-col tb-col-md"><span class="sub-text"><?php esc_html_e('Earnings','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                    <?php 
                                        if (apply_filters('marketking_show_actions_my_orders_page', true)){
                                            ?>
                                                <th class="nk-tb-col"><span class="sub-text"><?php esc_html_e('Actions','marketking-multivendor-marketplace-for-woocommerce'); ?></span></th>
                                            <?php
                                        }
                                    ?>
                                    

                                </tr>
                            </thead>
                            <?php
                            if (!marketking()->load_tables_with_ajax(get_current_user_id())){
                                ?>
                                <tfoot>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col tb-col-md"><?php esc_html_e('order','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                        <th class="nk-tb-col tb-col-md"><?php esc_html_e('date','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                        <th class="nk-tb-col tb-col-md"><?php esc_html_e('status','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                        <?php do_action('marketking_orders_page_column_footer'); ?>
                                        <th class="nk-tb-col tb-col-md"><?php esc_html_e('customer','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                        <th class="nk-tb-col tb-col-md"><?php esc_html_e('purchased','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                        <th class="nk-tb-col tb-col-md"><?php esc_html_e('order total','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                        <th class="nk-tb-col tb-col-md"><?php esc_html_e('earnings','marketking-multivendor-marketplace-for-woocommerce'); ?></th>

                                        <?php 
                                            if (apply_filters('marketking_show_actions_my_orders_page', true)){
                                                ?>
                                                    <th class="nk-tb-col tb-col-md"><?php esc_html_e('actions','marketking-multivendor-marketplace-for-woocommerce'); ?></th>
                                                <?php
                                            }
                                        ?>

                                    </tr>
                                </tfoot>
                                <?php
                            }
                            ?>
                            <tbody>
                                <?php

                                if (!marketking()->load_tables_with_ajax(get_current_user_id())){

                                    $vendor_orders = get_posts( array( 'post_type' => 'shop_order','post_status'=>'any','numberposts' => -1, 'author'   => $user_id, 'fields' =>'ids') );

                                    foreach ($vendor_orders as $order){
                                        $orderobj = wc_get_order($order);
                                        if ($orderobj !== false){
                                            ?>
                                            <tr class="nk-tb-item">
                                                <td class="nk-tb-col" data-order="<?php
                                                    echo esc_attr($order);
                                                ?>">
                                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'manage-order/'.$order);?>">

                                                        <div>
                                                            <span class="tb-lead">#<?php 

                                                            // sequential
                                                            $order_nr_sequential = get_post_meta($order,'_order_number', true);
                                                            if (!empty($order_nr_sequential)){
                                                                echo $order_nr_sequential;
                                                            } else {
                                                                echo esc_html($order);
                                                            }
                                                            echo ' ';

                                                            $name = $orderobj->get_formatted_billing_full_name();

                                                            $name = apply_filters('marketking_customers_page_name_display', $name, $orderobj);
                                                            
                                                            echo esc_html($name);

                                                            // subscription renewal
                                                            $renewal = get_post_meta($order, '_subscription_renewal', true);
                                                            if (!empty($renewal)){
                                                                echo ' ('.esc_html__('susbcription renewal', 'marketking-multivendor-marketplace-for-woocommerce').')';
                                                            }

                                                        ?></span>
                                                        </div>
                                                    </a>
                                                </td>
                                                <td class="nk-tb-col tb-col-md" data-order="<?php
                                                    $date = $orderobj->get_date_created();
                                                    echo $date->getTimestamp();
                                                ?>">
                                                    <div>
                                                        <span class="tb-sub"><?php 
                                                        
                                                        echo $date->date_i18n( get_option('date_format'), $date->getTimestamp()+(get_option('gmt_offset')*3600) );

                                                        
                                                        ?></span>
                                                    </div>
                                                </td>
                                                <td class="nk-tb-col tb-col-md"> 
                                                    <div >
                                                        <span class="dot bg-warning d-mb-none"></span>
                                                        <?php
                                                        $status = $orderobj->get_status();
                                                        $statustext = $badge = '';
                                                        if ($status === 'processing'){
                                                            $badge = 'badge-success';
                                                            $statustext = esc_html__('Processing','marketking-multivendor-marketplace-for-woocommerce');
                                                        } else if ($status === 'on-hold'){
                                                            $badge = 'badge-warning';
                                                            $statustext = esc_html__('On Hold','marketking-multivendor-marketplace-for-woocommerce');
                                                        } else if (in_array($status,apply_filters('marketking_earning_completed_statuses', array('completed')))){
                                                            $badge = 'badge-info';
                                                            $statustext = esc_html__('Completed','marketking-multivendor-marketplace-for-woocommerce');
                                                        } else if ($status === 'refunded'){
                                                            $badge = 'badge-gray';
                                                            $statustext = esc_html__('Refunded','marketking-multivendor-marketplace-for-woocommerce');
                                                        } else if ($status === 'cancelled'){
                                                            $badge = 'badge-gray';
                                                            $statustext = esc_html__('Cancelled','marketking-multivendor-marketplace-for-woocommerce');
                                                        } else if ($status === 'pending'){
                                                            $badge = 'badge-dark';
                                                            $statustext = esc_html__('Pending Payment','marketking-multivendor-marketplace-for-woocommerce');
                                                        } else if ($status === 'failed'){
                                                            $badge = 'badge-danger';
                                                            $statustext = esc_html__('Failed','marketking-multivendor-marketplace-for-woocommerce');
                                                        } else {
                                                            // custom status
                                                            $badge = apply_filters('marketking_custom_status_badge', 'badge-gray', $status);
                                                            $wcstatuses = wc_get_order_statuses();
                                                            if (isset($wcstatuses['wc-'.$status])){
                                                                $statustext = $wcstatuses['wc-'.$status];
                                                            } else {
                                                                $statustext = '';
                                                            }
                                                            $statustext = apply_filters('marketking_custom_status_text', $statustext, $status);
                                                        }

                                                        ?>
                                                        <span class="badge badge-sm badge-dot has-bg <?php echo esc_attr($badge);?> d-mb-inline-flex"><?php
                                                        echo esc_html($statustext);
                                                        ?></span>
                                                    </div>
                                                </td>
                                                <?php do_action('marketking_orders_page_column_content', $orderobj); ?>
                                                <td class="nk-tb-col tb-col-sm">
                                                    <div>
                                                         <span class="tb-sub"><?php
                                                         $customer_id = $orderobj -> get_customer_id();
                                                         $data = get_userdata($customer_id);
                                                         if (isset($data->first_name)){
                                                            $name = $data->first_name.' '.$data->last_name;
                                                         }

                                                         // if guest user, show name by order
                                                         if ($data === false){
                                                            $name = $orderobj -> get_formatted_billing_full_name() . ' '.esc_html__('(guest user)','marketking-multivendor-marketplace-for-woocommerce');
                                                         }
                                                         $name = apply_filters('marketking_customers_page_name_display', $name, $customer_id);

                                                         echo esc_html($name);
                                                         ?></span>
                                                    </div>
                                                </td>
                                                <td class="nk-tb-col tb-col-md"> 
                                                    <div>
                                                        <span class="tb-sub text-primary"><?php
                                                        $items = $orderobj->get_items();
                                                        $items_count = count( $items );
                                                        if ($items_count > apply_filters('marketking_dashboard_item_count_limit', 4)){
                                                            echo esc_html($items_count).' '.esc_html__('Items', 'marketking-multivendor-marketplace-for-woocommerce');
                                                        } else {
                                                            // show the items
                                                            foreach ($items as $item){
                                                                echo apply_filters('marketking_item_display_dashboard', $item->get_name().' x '.$item->get_quantity().'<br>', $item);
                                                            }
                                                        }
                                                        ?></span>
                                                    </div>
                                                </td>
                                                <td class="nk-tb-col" data-order="<?php echo esc_attr($orderobj->get_total());?>"> 
                                                    <div>
                                                        <span class="tb-lead"><?php echo wc_price($orderobj->get_total(), array('currency' => $orderobj->get_currency()));?></span>
                                                    </div>
                                                </td>
                                                <td class="nk-tb-col tb-col-md" data-order="<?php echo esc_attr(marketking()->get_order_earnings($orderobj->get_id()));?>"> 
                                                    <div>
                                                        <span class="tb-lead"><?php 
                                                        $earnings = marketking()->get_order_earnings($orderobj->get_id());
                                                        if ($earnings === 0){
                                                            echo '—';
                                                        } else {
                                                            echo wc_price($earnings);
                                                        }

                                                        $tax_fee_recipient = get_post_meta($orderobj->get_id(),'tax_fee_recipient', true);
                                                        if (empty($tax_fee_recipient)){
                                                            $tax_fee_recipient = get_option('marketking_tax_fee_recipient_setting', 'vendor');
                                                        }
                                                        if ($tax_fee_recipient === 'vendor'){
                                                            $tax = $orderobj->get_total_tax();
                                                            if (floatval($tax) > 0){
                                                                echo ' ('.esc_html__('tax','marketking-multivendor-marketplace-for-woocommerce').' '.wc_price($tax).')';
                                                            }
                                                        }

                                                        ?></span>
                                                    </div>
                                                </td>
                                                <?php 
                                                    if (apply_filters('marketking_show_actions_my_orders_page', true)){
                                                        ?>
                                                            <td class="nk-tb-col">
                                                                <div class="marketking_manage_order_container"> 

                                                                    <a href="<?php echo esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'manage-order/'.$orderobj->get_id());?>"><button class="btn btn-sm btn-dim btn-secondary marketking_manage_order" value="<?php echo esc_attr($orderobj->get_id());?>"><em class="icon ni ni-bag-fill"></em><span><?php esc_html_e('View Order','marketking-multivendor-marketplace-for-woocommerce');?></span></button></a>
                                                                </div>
                                                            </td>
                                                        <?php
                                                    }
                                                ?>
                                            </tr>
                                            <?php
                                        }
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
    }
}
?>