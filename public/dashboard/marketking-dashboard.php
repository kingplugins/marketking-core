<body class="nk-body bg-lighter npc-general has-sidebar <?php
    $page = get_query_var('dashpage');
    if ($page === 'edit-product' || 'edit-booking-product'){
        echo 'post-type-product wc-wp-version-gte-55';
    }

    if (apply_filters('marketking_dashboard_rtl', false)){ 
        echo ' has-rtl';
    }

?>" <?php if (apply_filters('marketking_dashboard_rtl', false)){ echo 'dir="rtl"'; }?>>
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <?php

            // set locale
            $locale = get_locale();
            setlocale(LC_ALL,$locale);


            // get announcements here as the unread number has to be shown in sidebar
            // get all announcements that the user has access (visibility) to
            $user_id = get_current_user_id();

            if (marketking()->is_vendor_team_member()){
                $user_id = marketking()->get_team_member_parent();
            }


            $user = get_user_by('id', $user_id) -> user_login;
            $agent_group = get_user_meta($user_id, 'marketking_group', true);
            $announcements = get_posts(array( 'post_type' => 'marketking_announce',
                      'post_status'=>'publish',
                      'numberposts' => -1,
                      'meta_query'=> array(
                            'relation' => 'OR',
                            array(
                                'key' => 'marketking_group_'.$agent_group,
                                'value' => '1',
                            ),
                            array(
                                'key' => 'marketking_user_'.$user, 
                                'value' => '1',
                            ),
                        )));

            // Get nr of orders
            $vendor_orders_nr = count(get_posts( array( 'post_type' => 'shop_order', 'post_status'=>'wc-processing','numberposts' => -1, 'author'   => $user_id, 'fields' =>'ids') ) );


            // check how many are unread
            $unread_ann = 0;
            foreach ($announcements as $announcement){
                $read_status = get_user_meta($user_id,'marketking_announce_read_'.$announcement->ID, true);
                if (!$read_status || empty($read_status)){
                    $unread_ann++;
                }
            }

            marketking()->set_data('unread_ann', $unread_ann);
            marketking()->set_data('user_id', $user_id);
            marketking()->set_data('announcements', $announcements);


            // get all messages that are unread (unread = user is different than msg author + read time is lower than last marked time)
            // get and display messages
            $currentuser = new WP_User($user_id);
            $currentuserlogin = $currentuser -> user_login;
            $messages = get_posts(
                        array( 
                            'post_type' => 'marketking_message', // only conversations
                            'post_status' => 'publish',
                            'numberposts' => -1,
                            'fields' => 'ids',
                            'meta_query'=> array(   // only the specific user's conversations
                                'relation' => 'OR',
                                array(
                                    'key' => 'marketking_message_user',
                                    'value' => $currentuserlogin, 
                                ),
                                array(
                                    'key' => 'marketking_message_message_1_author',
                                    'value' => $currentuserlogin, 
                                )


                            )
                        )
                    );
            if (current_user_can('activate_plugins')){
                // include shop messages
                $messages2 = get_posts(
                    array( 
                        'post_type' => 'marketking_message', // only conversations
                        'post_status' => 'publish',
                        'numberposts' => -1,
                        'fields' => 'ids',
                        'meta_query'=> array(   // only the specific user's conversations
                            'relation' => 'OR',
                            array(
                                'key' => 'marketking_message_user',
                                'value' => 'shop'
                            ),
                            array(
                                'key' => 'marketking_message_message_1_author',
                                'value' => 'shop'
                            )
                        )
                    )
                );
                $messages = array_merge($messages, $messages2);
            }
            // check how many are unread
            $unread_msg = 0;
            foreach ($messages as $message){
                // check that last msg is not current user
                $nr_messages = get_post_meta ($message, 'marketking_message_messages_number', true);
                $last_message_author = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_author', true);
                if ($last_message_author !== $currentuserlogin){
                    // chek if last read time is lower than last msg time
                    $last_read_time = get_user_meta($user_id,'marketking_message_last_read_'.$message, true);
                    if (!empty($last_read_time)){
                        $last_message_time = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_time', true);
                        if (floatval($last_read_time) < floatval($last_message_time)){
                            $unread_msg++;
                        }
                    } else {
                        $unread_msg++;
                    }
                }
                
            }

            marketking()->set_data('unread_msg', $unread_msg);
            marketking()->set_data('messages', $messages);



            ?>
            <?php 

            include(apply_filters('marketking_dashboard_template','templates/sidebar.php'));

            ?>

            <div class="nk-wrap ">
                <?php

                include(apply_filters('marketking_dashboard_template','templates/header-bar.php'));

                // get page
                $page = get_query_var('dashpage');

                if (empty($page)){
                    // Agent dashboard here
                    include(apply_filters('marketking_dashboard_template','dashboard-content.php'));
                }
                
                if ($page === 'announcements'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('announcements');
                    }
                } else if ($page === 'announcement'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('announcement');
                    }
                } else if ($page === 'messages'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('messages');
                    }
                }  else if ($page === 'subscriptions'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('subscriptions');
                    }
                } else if ($page === 'customers'){
                    include(apply_filters('marketking_dashboard_template','customers.php'));
                } else if ($page === 'profile'){
                    include(apply_filters('marketking_dashboard_template','profile.php'));
                } else if ($page === 'products'){
                    include(apply_filters('marketking_dashboard_template','products.php'));
                } else if ($page === 'edit-product'){
                    include(apply_filters('marketking_dashboard_template','edit-product.php'));
                } else if ($page === 'profile-settings'){
                    include(apply_filters('marketking_dashboard_template','profile-settings.php'));
                } else if ($page === 'vacation'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('vacation');
                    }
                } else if ($page === 'storenotice'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('storenotice');
                    }
                } else if ($page === 'storepolicy'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('storepolicy');
                    }
                } else if ($page === 'storecategories'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('storecategories');
                    }
                } else if ($page === 'vendorinvoices'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('vendorinvoices');
                    }
                } else if ($page === 'storeseo'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('storeseo');
                    }
                } else if ($page === 'social'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('social');
                    }
                } else if ($page === 'verification'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('verification');
                    }
                } else if ($page === 'orders'){
                    include(apply_filters('marketking_dashboard_template','orders.php'));
                } else if ($page === 'earnings'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('earnings');
                    }
                } else if ($page === 'support'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('support');
                    }
                } else if ($page === 'shipping'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('shipping');
                    }
                } else if ($page === 'shippingzone'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('shippingzone');
                    }
                } else if ($page === 'payouts'){
                    include(apply_filters('marketking_dashboard_template','payouts.php'));
                } else if ($page === 'team'){
                   if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('team');
                    }
                } else if ($page === 'membership'){
                   if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('memberships');
                    }
                } else if ($page === 'rma'){
                   if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('rma');
                    }
                } else if ($page === 'affiliate-links'){
                    include(apply_filters('marketking_dashboard_template','affiliate-links.php'));
                } else if ($page === 'cart-sharing'){
                    include(apply_filters('marketking_dashboard_template','cart-sharing.php'));
                } else if ($page === 'coupons'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('coupons');
                    }
                } else if ($page === 'edit-coupon'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('edit-coupon');
                    }
                } else if ($page === 'edit-team'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('edit-team');
                    }
                } else if ($page === 'rules'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('rules');
                    }
                } else if ($page === 'offers'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('offers');
                    }
                } else if ($page === 'b2bmessaging'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('b2bmessaging');
                    }
                } else if ($page === 'reviews'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('reviews');
                    }
                } else if ($page === 'refunds'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('refunds');
                    }
                } else if ($page === 'docs'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('docs');
                    }
                } else if ($page === 'docssingle'){
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('docssingle');
                    }
                } else if ($page === 'manage-order'){
                    include(apply_filters('marketking_dashboard_template','manage-order.php'));
                } else if ( $page === 'import-products') {
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('import-products');
                    }
                } else if ( $page === 'export-products') {
                    if (defined('MARKETKINGPRO_DIR')){
                        echo marketkingpro()->get_page('export-products');
                    }
                } else if ( $page === 'expressconnect'){

                    if( !class_exists("Stripe\Stripe") ) {
                      require_once( MARKETKINGPRO_DIR . 'includes/assets/lib/Stripe/init.php' );
                    }

                    try {
                        ?>

                        <div class="nk-content p-0">
                            <div class="nk-content-inner">
                                <div class="nk-content-body">
                                    <div class="connecting_to_stripe">
                                    <?php
                                    esc_html_e('Connecting to Stripe, please wait...','marketking-multivendor-marketplace-for-woocommerce');
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                        

                        $settings = get_option('woocommerce_marketking_stripe_gateway_settings');

                        $testmode = false;

                        if (isset( $settings['test_mode'] )){
                            if ($settings['test_mode'] === 'yes'){
                                $testmode = true;
                            }
                        }
                        if (!isset($settings['test_secret_key'])){
                            $settings['test_secret_key'] = '';
                        }
                        if (!isset($settings['secret_key'])){
                            $settings['secret_key'] = '';
                        }

                        $secret_key = $testmode ? $settings['test_secret_key'] : $settings['secret_key'];
                          
                        $stripe = new \Stripe\StripeClient($secret_key);
                        $account = $stripe->accounts->create([
                        'type' => 'express',
                        'capabilities' => [
                          'card_payments' => ['requested' => true],
                          'transfers' => ['requested' => true],
                        ],
                        ]);
                        $account_id = $account->id;

                        $refresh_url = $return_url = esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'payouts';

                        $link = $stripe->accountLinks->create([
                        'account' => $account_id,
                        'refresh_url' => $refresh_url,
                        'return_url' => $return_url,
                        'type' => 'account_onboarding',
                        ]);

                        $url = $link->url;
                        // redirect to url

                        ?>
                        <script>window.location = "<?php echo $url;?>";</script>
                        <?php

                    } catch ( Throwable $ex ) {

                      ?>

                      <div class="nk-content p-0">
                          <div class="nk-content-inner">
                              <div class="nk-content-body">
                                  <div class="connecting_to_stripe">
                                  <?php
                                  echo $ex->getMessage();
                                  ?>
                              </div>
                          </div>
                      </div>
                      <?php

                    }
                }

                do_action('marketking_extend_page', $page);

                // on edit product page, display hidden footer, in order to have all scripts necessary for editors e.g. tinymce
                if ($page === 'edit-booking-order' || $page === 'edit-booking-product' || $page === 'edit-product' || $page === 'manage-order' || $page === 'edit-coupon' || $page === 1){ // page 1 fix for flatsome and or other themes
                    ?>
                    <div id="marketking_footer_hidden">
                        <?php
                        if (apply_filters('marketking_display_footer_scripts', true)){
                            wp_footer();
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
</body>
