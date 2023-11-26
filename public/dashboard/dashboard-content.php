<?php

/*

Main Dashboard Page
* @version 1.0.0

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/


?>
<?php
    if(marketking()->vendor_has_panel('dashboard')){
    ?>
    <div class="nk-content marketking_dashboard_page">
        <div class="container-fluid">
            <?php
            // vacation notice
            if (marketking()->is_on_vacation($user_id)){
                ?>
                <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'vacation';?>">
                    <div class="alert alert-fill alert-danger alert-icon"><em class="icon ni ni-sun-fill"></em> <strong><?php esc_html_e('Your store is in vacation mode and products cannot be purchased. You can modify this via Settings -> Vacation.','marketking-multivendor-marketplace-for-woocommerce');?></strong></div>
                </a><br>
                <?php 
            }
            ?>
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h4 class="nk-block-title page-title"><?php esc_html_e('Dashboard', 'marketking-multivendor-marketplace-for-woocommerce');?></h4>
                                <div class="nk-block-des text-soft fs-15px">
                                    <?php
                                    $name = wp_get_current_user()->first_name.' '.wp_get_current_user()->last_name;
                                    ?>
                                    <p><?php esc_html_e('Welcome to your vendor dashboard', 'marketking-multivendor-marketplace-for-woocommerce');?><?php 
                                    if (!empty($name)){
                                        echo ', '.esc_html($name).'!';
                                    } else{
                                        echo '!';
                                    }
                                    esc_html_e(' Here\'s everything at a glance...', 'marketking-multivendor-marketplace-for-woocommerce');
                                    ?></p>
                                </div>
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->
                    <div class="nk-block">
                        <div class="row g-gs">
                        
                            <div class="<?php echo esc_attr(apply_filters('marketking_dashboard_card_classes', 'col-xxl-4 col-md-6'));?>">
                                <div class="card is-dark h-100">
                                    <div class="nk-ecwg nk-ecwg1">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title"><?php esc_html_e('Balance available', 'marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                </div>
                                                <?php
                                                if (defined('MARKETKINGPRO_DIR')){
                                                    if (intval(get_option( 'marketking_enable_earnings_setting', 1 )) === 1){
                                                        ?>
                                                        <div class="card-tools">
                                                            <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'earnings';?>" class="link"><?php esc_html_e('View earnings', 'marketking-multivendor-marketplace-for-woocommerce');?></a>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <div class="data">
                                                <div class="amount"><?php
                                                        $outstanding_balance = get_user_meta($user_id,'marketking_outstanding_earnings', true);
                                                        if (empty($outstanding_balance)){
                                                            $outstanding_balance = 0;
                                                        }
                                                        echo wc_price($outstanding_balance);
                                                        ?></div>
                                                <div class="info"><strong><?php
                                                        $site_time = time()+(get_option('gmt_offset')*3600);
                                                        $current_day = date_i18n( 'd', $site_time );

                                                        $earnings_number = marketking()->get_earnings($user_id,'last_days', 30);
                                                        echo strip_tags(wc_price($earnings_number));

                                                        ?></strong> <?php esc_html_e('earnings in the last 30 days','marketking-multivendor-marketplace-for-woocommerce');?></div>
                                            </div>
                                            <div class="data">
                                                <h6 class="sub-title"><?php esc_html_e('Earnings this month so far', 'marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                <div class="data-group">
                                                    <div class="amount"><?php
                                                        $current_day = date_i18n( 'd', $site_time );

                                                        $earnings_number = marketking()->get_earnings($user_id,'current_month');
                                                        echo wc_price($earnings_number);

                                                        ?></div>
                                                  
                                                </div>
                                            </div>
                                        </div><!-- .card-inner -->
                                        <div class="nk-ecwg1-ck">
                                            <canvas class="ecommerce-line-chart-s1" id="totalSales"></canvas>
                                        </div>
                                    </div><!-- .nk-ecwg -->
                                </div><!-- .card -->
                            </div><!-- .col -->
                                
                            <div class="<?php echo esc_attr(apply_filters('marketking_dashboard_card_classes', 'col-xxl-4 col-md-6'));?>">
                                <div class="card card-full overflow-hidden">
                                    <div class="nk-ecwg nk-ecwg7 h-100">
                                        <div class="card-inner flex-grow-1">
                                            <div class="card-title-group mb-4">
                                                <div class="card-title">
                                                    <h6 class="title"><?php esc_html_e('Order Statistics (last 30 days)', 'marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                </div>
                                            </div>
                                            <div class="nk-ecwg7-ck">
                                                <?php

                                                $vendor_orders = get_posts( array( 
                                                    'post_type' => 'shop_order',
                                                    'post_status'=>'any',
                                                    'date_query' => array(
                                                            'after' => date('Y-m-d', strtotime('-30 days')) 
                                                        ),
                                                    'numberposts' => -1,
                                                    'author'   => get_current_user_id(),
                                                    'fields' =>'ids'
                                                ));

                                                if (empty($vendor_orders)){
                                                    ?>
                                                    <p class="marketking_no_orders"><?php esc_html_e('There are no orders yet...','marketking-multivendor-marketplace-for-woocommerce'); ?></p>

                                                    <?php
                                                } else {
                                                    ?>
                                                    <canvas class="ecommerce-doughnut-s1" id="orderStatistics"></canvas>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <ul class="nk-ecwg7-legends">
                                                <li>
                                                    <div class="title">
                                                        <span class="dot dot-lg sq" data-bg="#816bff"></span>
                                                        <span><?php esc_html_e('Completed', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="title">
                                                        <span class="dot dot-lg sq" data-bg="#13c9f2"></span>
                                                        <span><?php esc_html_e('Pending', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="title">
                                                        <span class="dot dot-lg sq" data-bg="#ff82b7"></span>
                                                        <span><?php esc_html_e('Cancelled', 'marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div><!-- .card-inner -->
                                    </div>
                                </div><!-- .card -->
                            </div><!-- .col -->
                            <div class="<?php echo esc_attr(apply_filters('marketking_dashboard_card_classes', 'col-xxl-4 col-md-6'));?>">
                                <div class="card h-100">
                                    <div class="card-inner">
                                        <div class="card-title-group mb-2">
                                            <div class="card-title">
                                                <h6 class="title"><?php esc_html_e('Store Statistics (last 30 days)', 'marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                            </div>
                                        </div>
                                        <ul class="nk-store-statistics">
                                            <li class="item">
                                                <div class="info">
                                                    <div class="title"><?php esc_html_e('Orders', 'marketking-multivendor-marketplace-for-woocommerce');?></div>
                                                    <div class="count"><?php
                                                    // if earnings enabled,
                                                    $vendor_orders = get_posts( array( 
                                                        'post_type' => 'shop_order',
                                                        'post_status'=> 'any',
                                                        'date_query' => array(
                                                            'after' => date('Y-m-d', strtotime('-31 days')) 
                                                        ),
                                                        'numberposts' => -1,
                                                        'author'   => $user_id,
                                                        'fields'   => 'ids'
                                                    ));

                                                    echo count($vendor_orders);

                                                    // get nr of unique customers as well.
                                                    $nr_of_customers = 0;
                                                    $customers = array();

                                                    // get item count as well
                                                    $item_count = 0;

                                                    // get average order value
                                                    $order_totals = array();

                                                    foreach ($vendor_orders as $order_id){
                                                        $order = wc_get_order($order_id);
                                                        if ($order!== false){
                                                            $item_count += $order->get_item_count();
                                                            array_push($order_totals, ($order->get_total() - $order->get_shipping_total() ));

                                                            $customer_id = $order->get_customer_id();
                                                            if ($customer_id === false || intval($customer_id) === 0){
                                                                // guest user, add as plus 1
                                                                $nr_of_customers++;
                                                            } else {
                                                                array_push($customers, $customer_id);
                                                            }
                                                        }
                                                    }

                                                    $nr_of_customers += count(array_filter(array_unique($customers)));

                                                    $order_totals = array_filter($order_totals);
                                                    $ordersnr = count($order_totals);
                                                    if ($ordersnr !== 0){
                                                        $average_order = array_sum($order_totals)/$ordersnr;
                                                    } else {
                                                        $average_order = 0;
                                                    }


                                                    ?></div>
                                                </div>
                                                <em class="icon bg-primary-dim ni ni-bag"></em>
                                            </li>
                                            <li class="item">
                                                <div class="info">
                                                    <div class="title"><?php esc_html_e('Customers (Unique)', 'marketking-multivendor-marketplace-for-woocommerce');?></div>
                                                    <div class="count"><?php
                                                    echo esc_html($nr_of_customers);                                                
                                                    ?></div>
                                                </div>
                                                <em class="icon bg-info-dim ni ni-users"></em>
                                            </li>
                                            <li class="item">
                                                <div class="info">
                                                    <div class="title"><?php esc_html_e('Items Sold','marketking-multivendor-marketplace-for-woocommerce');?></div>
                                                    <div class="count"><?php

                                                        echo esc_html($item_count);
                                                    ?></div>
                                                </div>
                                                <em class="icon bg-pink-dim ni ni-box"></em>
                                            </li>
                                            <li class="item">
                                                <div class="info">
                                                    <div class="title"><?php esc_html_e('Average Order','marketking-multivendor-marketplace-for-woocommerce');?><em class="icon ni ni-help marketking_info_icon nk-tooltip" data-toggle="tooltip" data-bs-placement="right" title="<?php esc_html_e('Average order value, excluding shipping cost.','marketking-multivendor-marketplace-for-woocommerce');?>"></em></div>
                                                    <div class="count"><?php
                                                        echo wc_price($average_order);
                                                    ?></div>
                                                </div>
                                                <em class="bg-purple-dim icon ni ni-activity-round-fill"></em>
                                            </li>
                                        </ul>
                                    </div><!-- .card-inner -->
                                </div><!-- .card -->
                            </div><!-- .col -->

                            <?php

                            $vendor_orders = get_posts( array( 
                                'post_type' => 'shop_order',
                                'numberposts' => apply_filters('marketking_recent_orders_number', 5),
                                'fields'    => 'ids',
                                'post_status'    => 'any',
                                'author'   => $user_id
                            ));

                            if (count($vendor_orders) > 0){
                            ?>

                                <div class="col-xxl-12">
                                    <div class="card card-full">
                                        <div class="card-inner">
                                            <div class="card-title-group">
                                                <div class="card-title">
                                                    <h6 class="title"><?php esc_html_e('Recent Orders', 'marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="nk-tb-list mt-n2">
                                            <div class="nk-tb-item nk-tb-head">
                                                <div class="nk-tb-col"><span><?php esc_html_e('Order No.', 'marketking-multivendor-marketplace-for-woocommerce');?></span></div>
                                                <div class="nk-tb-col tb-col-sm"><span><?php esc_html_e('Customer', 'marketking-multivendor-marketplace-for-woocommerce');?></span></div>
                                                <div class="nk-tb-col tb-col-md"><span><?php esc_html_e('Date', 'marketking-multivendor-marketplace-for-woocommerce');?></span></div>
                                                <div class="nk-tb-col"><span><?php esc_html_e('Amount', 'marketking-multivendor-marketplace-for-woocommerce');?></span></div>
                                                <div class="nk-tb-col"><span class="d-none d-sm-inline"><?php esc_html_e('Status', 'marketking-multivendor-marketplace-for-woocommerce');?></span></div>
                                            </div>

                                                <?php

                                                foreach ($vendor_orders as $order_id){
                                                    $orderobj = wc_get_order($order_id);
                                                    if ($orderobj !== false){
                                                        $date = $orderobj->get_date_created();

                                                        $order_link = esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'manage-order/'.$orderobj->get_id());
                                                        if(!marketking()->vendor_has_panel('orders')){
                                                            $order_link = trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )));
                                                        }

                                                        ?>
                                                            <div class="nk-tb-item">
                                                                    <div class="nk-tb-col">
                                                                        <a href="<?php 

                                                                        echo $order_link;?>">

                                                                            <span class="tb-lead">#<?php 

                                                                            $order_id = $orderobj->get_id();

                                                                            // sequential
                                                                            $order_nr_sequential = get_post_meta($order_id,'_order_number', true);
                                                                            if (!empty($order_nr_sequential)){
                                                                                echo $order_nr_sequential;
                                                                            } else {
                                                                                echo esc_html($order_id);
                                                                            }

                                                                            ?></span>
                                                                        </a>
                                                                    </div>
                                                                <div class="nk-tb-col tb-col-sm">
                                                                    <a href="<?php 

                                                                        echo $order_link;
                                                                        ?>">

                                                                        <div class="user-card">
                                                                            <div class="user-avatar sm bg-purple-dim">
                                                                                <span><?php
                                                                         $customer_id = $orderobj -> get_customer_id();
                                                                         $data = get_userdata($customer_id);

                                                                         // if guest user, show name by order
                                                                         if ($data === false){
                                                                            $name = $orderobj -> get_formatted_billing_full_name() . ' '.esc_html__('(guest user)','marketking-multivendor-marketplace-for-woocommerce');
                                                                         } else {
                                                                            $name = $data->first_name.' '.$data->last_name;

                                                                         }
                                                                         $name = apply_filters('marketking_customers_page_name_display', $name, $customer_id);

                                                                         echo strtoupper(substr($name,0, 2));

                                                                         ?></span>
                                                                            </div>
                                                                            <div class="user-name">
                                                                                <span class="tb-lead"><?php echo esc_html($name);?></span>
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                                <div class="nk-tb-col tb-col-md">
                                                                    <a href="<?php 
                                                                        
                                                                        echo $order_link;
                                                                        ?>">
                                                                        <span class="tb-sub"><?php 
                                                                        echo $date->date_i18n( get_option('date_format'), $date->getTimestamp()+(get_option('gmt_offset')*3600) );
                                                                        ?></span>
                                                                    </a>
                                                                </div>
                                                                <div class="nk-tb-col">
                                                                    <a href="<?php 

                                                                        echo $order_link;
                                                                        ?>">

                                                                        <span class="tb-sub tb-amount"><?php echo wc_price($orderobj->get_total());?></span>
                                                                    </a>
                                                                </div>
                                                                <div class="nk-tb-col">
                                                                    <?php
                                                                    $status = $orderobj->get_status();
                                                                    $statustext = $badge = '';
                                                                    if ($status === 'processing'){
                                                                        $badge = 'badge-warning';
                                                                        $statustext = esc_html__('Pending Order Completion','marketking-multivendor-marketplace-for-woocommerce');
                                                                    } else if ($status === 'on-hold'){
                                                                        $badge = 'badge-warning';
                                                                        $statustext = esc_html__('Pending Order Completion','marketking-multivendor-marketplace-for-woocommerce');
                                                                    } else if (in_array($status,apply_filters('marketking_earning_completed_statuses', array('completed')))){
                                                                        $badge = 'badge-success';
                                                                        $statustext = esc_html__('Completed','marketking-multivendor-marketplace-for-woocommerce');
                                                                    } else if ($status === 'refunded'){
                                                                        $badge = 'badge-danger';
                                                                        $statustext = esc_html__('Order Refunded','marketking-multivendor-marketplace-for-woocommerce');
                                                                    } else if ($status === 'cancelled'){
                                                                        $badge = 'badge-danger';
                                                                        $statustext = esc_html__('Order Cancelled','marketking-multivendor-marketplace-for-woocommerce');
                                                                    } else if ($status === 'pending'){
                                                                        $badge = 'badge-warning';
                                                                        $statustext = esc_html__('Pending Order Payment','marketking-multivendor-marketplace-for-woocommerce');
                                                                    } else if ($status === 'failed'){
                                                                        $badge = 'badge-danger';
                                                                        $statustext = esc_html__('Order Failed','marketking-multivendor-marketplace-for-woocommerce');
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
                                                                    <a href="<?php 

                                                                        $order_link = esc_attr(trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' ))).'manage-order/'.$orderobj->get_id());
                                                                        if(!marketking()->vendor_has_panel('orders')){
                                                                            $order_link = trailingslashit(get_page_link(get_option( 'marketking_vendordash_page_setting', 'disabled' )));
                                                                        }

                                                                        echo $order_link;

                                                                        ?>">

                                                                        <span class="badge badge-dot badge-dot-xs <?php echo esc_attr($badge);?>"><?php
                                                                            echo esc_html($statustext);
                                                                        ?></span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        <?php
                                                    }

                                                }
                                                ?>
                                            
                                            
                                        </div>
                                    </div><!-- .card -->
                                </div>
                            <?php

                            }

                            ?>
                        </div><!-- .row -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>