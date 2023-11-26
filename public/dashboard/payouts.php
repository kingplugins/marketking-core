<?php

/*

Payouts Dashboard Page
* @version 1.0.0

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/


?><?php
if (intval(get_option( 'marketking_enable_payouts_setting', 1 )) === 1){
    if(marketking()->vendor_has_panel('payouts')){
        ?>
        <div class="nk-content marketking_payouts_page">
            <div class="container-fluid">
                <div class="nk-content-inner">
                    <div class="nk-content-body">
                        <div class="components-preview wide-md mx-auto">

                            <?php

                            $settings = get_option('woocommerce_marketking_stripe_gateway_settings');

                            $testmode = false;

                            if (isset( $settings['test_mode'] )){
                                if ($settings['test_mode'] === 'yes'){
                                    $testmode = true;
                                }
                            }

                            if (!isset($settings['test_client_id'])){
                                $settings['test_client_id'] = '';
                            }
                            if (!isset($settings['test_secret_key'])){
                                $settings['test_secret_key'] = '';
                            }
                            if (!isset($settings['client_id'])){
                                $settings['client_id'] = '';
                            }
                            if (!isset($settings['secret_key'])){
                                $settings['secret_key'] = '';
                            }

                            $client_id = $testmode ? $settings['test_client_id'] : $settings['client_id'];
                            $secret_key = $testmode ? $settings['test_secret_key'] : $settings['secret_key'];
                            if (isset($client_id) && isset($secret_key)) {
                                
                                $is_stripe_connected = false;
                                $stripe_user_id = get_user_meta( $user_id, 'stripe_user_id', true );
                                $vendor_connected = get_user_meta( $user_id, 'vendor_connected', true );
                                if( $stripe_user_id && $vendor_connected ) {
                                    $is_stripe_connected = true;
                                }

                                if (isset($_GET['code'])) {
                                    $code = sanitize_text_field($_GET['code']);
                                    
                                    $token_request_body = array(
                                        'grant_type' => 'authorization_code',
                                        'client_id' => $client_id,
                                        'code' => $code,
                                        'client_secret' => $secret_key
                                    );

                                    $req = curl_init('https://connect.stripe.com/oauth/token');
                                    curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($req, CURLOPT_POST, true);
                                    curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                                    curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
                                    curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2);
                                    curl_setopt($req, CURLOPT_VERBOSE, true);
                                    // TODO: Additional error handling
                                    $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
                                    $resp = json_decode(curl_exec($req), true);
                                    curl_close($req);
                                    if (!isset($resp['error'])) {
                                        update_user_meta( $user_id, 'vendor_connected', 1);
                                        update_user_meta( $user_id, 'admin_client_id', $client_id);
                                        if (isset($resp['access_token'])){
                                            update_user_meta( $user_id, 'access_token', $resp['access_token']);
                                        }
                                        if (isset($resp['refresh_token'])){
                                            update_user_meta( $user_id, 'refresh_token', $resp['refresh_token']);
                                        }
                                        if (isset($resp['stripe_publishable_key'])){
                                            update_user_meta( $user_id, 'stripe_publishable_key', $resp['stripe_publishable_key']);
                                        }
                                        if (isset($resp['stripe_user_id'])){
                                            update_user_meta( $user_id, 'stripe_user_id', $resp['stripe_user_id']);
                                        }

                                        ?>                                    
                                        <div class="alert alert-primary alert-icon"><em class="icon ni ni-check-circle"></em> <strong><?php esc_html_e('You have successfully connected your Stripe account.','marketking-multivendor-marketplace-for-woocommerce');?></strong></div>
                                        <?php
                                    } else {
                                        ?>                                    
                                        <div class="alert alert-fill alert-danger alert-iconn"><em class="icon ni ni-cross-circle"></em> <strong><?php

                                        echo 'Error: '.sanitize_text_field($resp['error']);
                                        if (isset($resp['error_description'])){
                                            echo sanitize_text_field($resp['error_description']);
                                        }

                                        update_option('marketking_stripe_connect_error', $resp);

                                        ?></strong></div>
                                        <?php
                                    }
                                }
                            }

                            ?>
                            <div class="nk-block-head nk-block-head-sm">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h3 class="nk-block-title page-title"><?php esc_html_e('Payouts','marketking-multivendor-marketplace-for-woocommerce');?></h3>
                                        <div class="nk-block-des text-soft">
                                            <p><?php esc_html_e('View and keep track of your payouts.','marketking-multivendor-marketplace-for-woocommerce');?></p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                   
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <div class="row g-gs">
                                    <div class="col-xxl-6 col-sm-6">
                                        <div class="card text-white bg-primary">
                                            <div class="card-header marketking_available_payout_header"><?php esc_html_e('Available for Payout','marketking-multivendor-marketplace-for-woocommerce');?></div>
                                            <div class="card-inner marketking_available_payout_card">
                                                <h5 class="card-title"><?php 

                                                $outstanding_balance = get_user_meta($user_id,'marketking_outstanding_earnings', true);
                                                if (empty($outstanding_balance)){
                                                    $outstanding_balance = 0;
                                                }
                                                echo wc_price($outstanding_balance);

                                                ?></h5>
                                                <p class="card-text"><?php esc_html_e('This is the amount you currently have in earnings, available for your next payout.','marketking-multivendor-marketplace-for-woocommerce');?></p>
                                            </div>
                                        </div>
                                    </div><!-- .col -->
                                    <div class="col-xxl-6 col-sm-6">
                                        <div class="card bg-lighten h-100">
                                            <div class="card-header" style="display: flex; align-items: center;"><?php esc_html_e('Payout Account','marketking-multivendor-marketplace-for-woocommerce');?>
                                                
                                                <?php
                                                $active_withdrawal = get_user_meta($user_id,'marketking_active_withdrawal', true);
                                                if ($active_withdrawal === 'yes'){
                                                    ?>
                                                    <span class="badge rounded-pill bg-warning withdrawal-pending"><?php esc_html_e('Withdrawal request pending','marketking-multivendor-marketplace-for-woocommerce');?></span>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="card-inner">
                                                <?php
                                                // get method set if any
                                                $method = get_user_meta($user_id,'marketking_agent_selected_payout_method', true);
                                                if ($method === 'paypal'){
                                                    $method = 'PayPal';
                                                } else if ($method === 'bank'){
                                                    $method = 'Bank';
                                                } else if ($method === 'stripe'){
                                                    $method = 'Stripe';
                                                } else if ($method === 'custom'){
                                                    $method = get_option( 'marketking_enable_custom_payouts_title_setting', '' );
                                                }
                                                ?>
                                                <h6 class="card-title mb-4"><?php esc_html_e('Set payout account','marketking-multivendor-marketplace-for-woocommerce');?> <?php if (!empty($method)){echo '('.esc_html($method).' '.esc_html__('currently selected', 'marketking-multivendor-marketplace-for-woocommerce').')';}?></h6>


                                                <a href="#" class="btn btn-gray btn-sm marketking_set_payout_button" data-toggle="modal" data-target="#modal_set_payout_method"><em class="icon ni ni-setting"></em><span><?php esc_html_e('Configure','marketking-multivendor-marketplace-for-woocommerce');?></span> </a>

                                                <?php
                                                // withdrawal requests module

                                                if (defined('MARKETKINGPRO_DIR')){
                                                    if (intval(get_option('marketking_enable_withdrawals_setting', 1)) === 1){
                                                        ?>
                                                        <a href="#" class="btn btn-gray btn-sm marketking_withdrawal_button" data-toggle="modal" data-target="#modal_make_withdrawal"><em class="icon ni ni-wallet-in"></em><span><?php esc_html_e('Withdraw','marketking-multivendor-marketplace-for-woocommerce');

                                                    ?></span> </a>

                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div><!-- .col -->
                                    <div class="col-xxl-12">
                                        <div class="card card-full">
                                            <div class="card-inner">
                                                <div class="card-title-group">
                                                    <div class="card-title">
                                                        <h6 class="title"><?php esc_html_e('Recent Payouts','marketking-multivendor-marketplace-for-woocommerce');?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="nk-tb-list mt-n2">
                                                <div class="nk-tb-item nk-tb-head">
                                                    <div class="nk-tb-col"><span><?php esc_html_e('Amount','marketking-multivendor-marketplace-for-woocommerce');?></span></div>
                                                    <div class="nk-tb-col tb-col-sm"><span><?php esc_html_e('Payment Method','marketking-multivendor-marketplace-for-woocommerce');?></span></div>
                                                    <div class="nk-tb-col tb-col-md"><span><?php esc_html_e('Date Processed','marketking-multivendor-marketplace-for-woocommerce');?></span></div>
                                                    <div class="nk-tb-col"><span class="d-none d-sm-inline"><?php esc_html_e('Notes','marketking-multivendor-marketplace-for-woocommerce');?></span></div>
                                                </div>
                                                <?php
                                                $user_payout_history = sanitize_text_field(get_user_meta($user_id,'marketking_user_payout_history', true));

                                                if ($user_payout_history){
                                                    $transactions = explode(';', $user_payout_history);
                                                    $transactions = array_filter($transactions);
                                                } else {
                                                    // empty, no transactions
                                                    $transactions = array();
                                                }
                                                $transactions = array_reverse($transactions);
                                                foreach ($transactions as $transaction){
                                                    $elements = explode(':', $transaction);
                                                    $date = $elements[0];
                                                    $amount = $elements[1];
                                                    $oustanding_balance = $elements[2];
                                                    $note = $elements[3];
                                                    $method = $elements[4];
                                                    if (isset($elements[5])){
                                                        $bonus = $elements[5];
                                                    } else {
                                                        $bonus = 'no';
                                                    }
                                                    ?>
                                                    <div class="nk-tb-item">
                                                        <div class="nk-tb-col">
                                                            <span class="tb-sub tb-amount"><?php echo wc_price($amount);
                                                            if ($bonus === 'yes'){
                                                                echo ' '.esc_html__('(bonus)','marketking-multivendor-marketplace-for-woocommerce');
                                                            }
                                                            ?></span>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-sm">
                                                            <div class="user-card">
                                                                <div class="user-name">
                                                                    <span class="tb-lead"><?php echo $method;?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-md">
                                                            <span class="tb-sub"><?php echo esc_html($date);?></span>
                                                        </div>

                                                        <div class="nk-tb-col marketking_column_limited_width">
                                                            <span class="tb-sub"><?php echo esc_html($note);?></span>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div><!-- .card -->
                                    </div>
                                </div>
                            </div><!-- .row -->
                        </div><!-- .nk-block -->
                    </div>
                </div>
            </div>

            
            <div class="modal fade" tabindex="-1" id="modal_make_withdrawal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php esc_html_e('Make Withdrawal Request','marketking-multivendor-marketplace-for-woocommerce'); ?></h5>
                            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                <em class="icon ni ni-cross"></em>
                            </a>
                        </div>
                        <div class="modal-body">
                            <form action="#" class="form-validate is-alter" id="marketking_set_payout_form">
                                <?php
                                $method = get_user_meta($user_id,'marketking_agent_selected_payout_method', true);
                                $withdrawal_limit = get_option('marketking_withdrawal_limit_setting', 500);
                                if (empty($method)){
                                    esc_html_e('You must configure a payment method before making a withdrawal.','marketking-multivendor-marketplace-for-woocommerce');
                                } else if (floatval($outstanding_balance) < floatval($withdrawal_limit)){
                                    esc_html_e('Your balance is below the minimum withdrawal threshold: ','marketking-multivendor-marketplace-for-woocommerce');
                                    echo wc_price($withdrawal_limit);
                                } else {
                                    $active_withdrawal = get_user_meta($user_id,'marketking_active_withdrawal', true);
                                    if ($active_withdrawal === 'yes'){
                                        $amount = get_user_meta($user_id,'marketking_withdrawal_amount', true);
                                        esc_html_e('You already have an active request for ','marketking-multivendor-marketplace-for-woocommerce'); echo wc_price($amount).'.';
                                        echo '<br><br>';
                                    }

                                    echo '<input type="hidden" id="marketking_max_withdraw" value="'.esc_attr(round(floatval($outstanding_balance), 2)).'">';
                                    echo '<input type="hidden" id="marketking_min_withdraw" value="'.esc_attr(round(floatval($withdrawal_limit), 2)).'">';

                                    if ($active_withdrawal !== 'yes'){

                                        ?>
                                        <div class="form-group">
                                            <label class="form-label" for="withdrawal-amount"><?php esc_html_e('Withdrawal Amount','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                            <div class="form-control-wrap">
                                                <span class="marketking_withdrawal_amount_available"><?php esc_html_e('Available for withdrawal:','marketking-multivendor-marketplace-for-woocommerce'); ?><?php echo ' '.wc_price(round(floatval($outstanding_balance), 2));?></span>
                                                <input type="number" class="form-control" id="withdrawal-amount" min="<?php echo esc_attr(floatval($withdrawal_limit));?>" <?php
                                                    echo 'max="'.esc_attr(round(floatval($outstanding_balance), 2)).'"';
                                                ?> name="withdrawal-amount" placeholder="<?php esc_html_e('Enter your withdrawal amount here...','marketking-multivendor-marketplace-for-woocommerce');?>">
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                     <div class="form-group">
                                         <button type="button" id="marketking_make_withdrawal" class="btn btn-primary"><?php 

                                         if ($active_withdrawal !== 'yes'){
                                            esc_html_e('Make Request','marketking-multivendor-marketplace-for-woocommerce'); 
                                         } else {
                                            esc_html_e('Cancel Current Request','marketking-multivendor-marketplace-for-woocommerce'); 
                                            echo '<input type="hidden" id="cancel_request" value="1">';
                                         }

                                     ?></button>

                                        <?php 
                                        if ($active_withdrawal !== 'yes'){
                                            ?>
                                             <button type="button" id="marketking_withdrawal_max" class="btn btn-secondary"><?php esc_html_e('Select Max','marketking-multivendor-marketplace-for-woocommerce');?></a>
                                            <?php 
                                        }
                                        ?>
                                     </div>
                                    <?php
                                }
                                ?>
                                

                            </form>
                        </div>
                        <div class="modal-footer bg-light">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" tabindex="-1" id="modal_set_payout_method">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php esc_html_e('Set Payout Method','marketking-multivendor-marketplace-for-woocommerce'); ?></h5>
                            <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                <em class="icon ni ni-cross"></em>
                            </a>
                        </div>
                        <div class="modal-body">
                            <form action="#" class="form-validate is-alter" id="marketking_set_payout_form">
                                <h6><?php esc_html_e('Select a payment method:','marketking-multivendor-marketplace-for-woocommerce');?></h6><br>
                                <?php
                                // get all configured methods: paypal, bank, custom
                                $paypal = intval(get_option( 'marketking_enable_paypal_payouts_setting', 1 ));
                                $stripe = intval(get_option( 'marketking_enable_stripe_payouts_setting', 1 ));
                                $bank = intval(get_option( 'marketking_enable_bank_payouts_setting', 0 ));
                                $custom = intval(get_option( 'marketking_enable_custom_payouts_setting', 0 ));
                                $title = get_option( 'marketking_enable_custom_payouts_title_setting', '' );
                                $description = get_option( 'marketking_enable_custom_payouts_description_setting', '' );

                                // get currently selected method if any
                                $selected = get_user_meta($user_id,'marketking_agent_selected_payout_method', true);
                                if ($paypal === 1){
                                    ?>
                                     <div class="g mb-1">
                                        <div class="custom-control custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="marketkingpayoutMethod" id="paypalMethod" value="paypal" <?php checked('paypal', $selected, true);?>>
                                            <label class="custom-control-label" for="paypalMethod"><?php esc_html_e('PayPal','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                        </div>
                                    </div>
                                    <?php
                                }
                                if (defined('MARKETKINGPRO_DIR')){
                                    if (intval(get_option( 'marketking_enable_stripe_setting', 1 )) === 1){
                                        if ($stripe === 1){
                                            ?>
                                             <div class="g mb-1">
                                                <div class="custom-control custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="marketkingpayoutMethod" id="stripeMethod" value="stripe" <?php checked('stripe', $selected, true);?>>
                                                    <label class="custom-control-label" for="stripeMethod"><?php esc_html_e('Stripe','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                                if ($bank === 1){
                                    ?>
                                   <div class="g mb-1">
                                       <div class="custom-control custom-radio">
                                           <input type="radio" class="custom-control-input" name="marketkingpayoutMethod" id="bankMethod" value="bank" <?php checked('bank', $selected, true);?>>
                                           <label class="custom-control-label" for="bankMethod"><?php esc_html_e('Bank Transfer','marketking-multivendor-marketplace-for-woocommerce');?></label>
                                       </div>
                                   </div>
                                   <?php
                                }
                                if ($custom === 1){
                                    ?>
                                   <div class="g mb-1">
                                       <div class="custom-control custom-control custom-radio">
                                           <input type="radio" class="custom-control-input" name="marketkingpayoutMethod" id="customMethod" value="custom" <?php checked('custom', $selected, true);?>>
                                           <label class="custom-control-label" for="customMethod"><?php echo esc_html($title); ?></label>
                                       </div>
                                   </div>
                                   <?php
                               }
                               ?>
                               <br>
                               <?php
                               $info = base64_decode(get_user_meta($user_id,'marketking_payout_info', true));
                               $info = explode('**&&', $info);

                               $i = 0;
                               while ($i < 19){
                                if (!isset($info[$i])){
                                    $info[$i] = '';
                                }
                                $i++;
                               }

                                if ($paypal === 1){
                                    ?>

                                    <div class="form-group marketking_paypal_info">
                                        <label class="form-label" for="paypal-email"><?php esc_html_e('PayPal Email Address','marketking-multivendor-marketplace-for-woocommerce'); ?></label> (*)
                                        <div class="form-control-wrap">
                                            <input type="email" class="form-control" id="paypal-email" name="paypal-email" placeholder="<?php esc_html_e('Enter your PayPal email address here...','marketking-multivendor-marketplace-for-woocommerce');?>" value="<?php echo esc_attr($info[0]);?>">
                                        </div>
                                    </div>
                                    <?php
                                }

                                if ($bank === 1){
                                    ?>
                                    <h6 class="marketking_bank_info"><?php esc_html_e('Personal / Business Details','marketking-multivendor-marketplace-for-woocommerce'); ?></h6><br class="marketking_bank_info">
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="full-name"><?php esc_html_e('Full Name','marketking-multivendor-marketplace-for-woocommerce'); ?></label> (*)
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="full-name" name="full-name" value="<?php echo esc_attr($info[2]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="billing-address-1"><?php esc_html_e('Billing Address Line 1','marketking-multivendor-marketplace-for-woocommerce'); ?></label> (*)
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="billing-address-1" name="billing-address-1" value="<?php echo esc_attr($info[3]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="billing-address-2"><?php esc_html_e('Billing Address Line 2','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="billing-address-2" name="billing-address-2" value="<?php echo esc_attr($info[4]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="city"><?php esc_html_e('City','marketking-multivendor-marketplace-for-woocommerce'); ?></label> (*)
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="city" name="city" value="<?php echo esc_attr($info[5]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="state"><?php esc_html_e('State','marketking-multivendor-marketplace-for-woocommerce'); ?></label> (*)
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="state" name="state" value="<?php echo esc_attr($info[6]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="postcode"><?php esc_html_e('Postcode','marketking-multivendor-marketplace-for-woocommerce'); ?></label> (*)
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="postcode" name="postcode" value="<?php echo esc_attr($info[7]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="country"><?php esc_html_e('Country','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="country" name="country" value="<?php echo esc_attr($info[8]);?>">
                                        </div>
                                    </div>
                                    <hr class="marketking_bank_info">
                                    <h6 class="marketking_bank_info"><?php esc_html_e('Bank / Wire Transfer Details','marketking-multivendor-marketplace-for-woocommerce'); ?></h6><br>
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="bank-account-holder-name"><?php esc_html_e('Account Holder Name','marketking-multivendor-marketplace-for-woocommerce'); ?></label> (*)
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="bank-account-holder-name" name="bank-account-holder-name" value="<?php echo esc_attr($info[9]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="bank-account-holder-name"><?php esc_html_e('Bank Name','marketking-multivendor-marketplace-for-woocommerce'); ?></label> (*)
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="bankname" name="bankname" value="<?php echo esc_attr($info[17]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="bank-account-holder-name"><?php esc_html_e('BIC / SWIFT','marketking-multivendor-marketplace-for-woocommerce'); ?></label> (*)
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="bankswift" name="bankswift" value="<?php echo esc_attr($info[18]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info">
                                        <label class="form-label" for="bank-account-number"><?php esc_html_e('Bank Account Number/IBAN','marketking-multivendor-marketplace-for-woocommerce'); ?></label> (*)
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="bank-account-number" name="bank-account-number" required value="<?php echo esc_attr($info[10]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info marketking_bank_branch_city_field">
                                        <label class="form-label" for="bank-branch-city"><?php esc_html_e('Bank Branch City','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="bank-branch-city" name="bank-branch-city" value="<?php echo esc_attr($info[11]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info marketking_bank_branch_country_field">
                                        <label class="form-label" for="bank-branch-country"><?php esc_html_e('Bank Branch Country','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="bank-branch-country" name="bank-branch-country" value="<?php echo esc_attr($info[12]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info marketking_intermediary_bank_field">
                                        <label class="form-label" for="intermediary-bank-bank-code"><?php esc_html_e('Intermediary Bank - Bank Code','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="intermediary-bank-bank-code" name="intermediary-bank-bank-code" value="<?php echo esc_attr($info[13]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info marketking_intermediary_bank_field">
                                        <label class="form-label" for="intermediary-bank-name"><?php esc_html_e('Intermediary Bank - Name','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="intermediary-bank-name" name="intermediary-bank-name" value="<?php echo esc_attr($info[14]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info marketking_intermediary_bank_field">
                                        <label class="form-label" for="intermediary-bank-city"><?php esc_html_e('Intermediary Bank - City','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="intermediary-bank-city" name="intermediary-bank-city" value="<?php echo esc_attr($info[15]);?>">
                                        </div>
                                    </div>
                                    <div class="form-group marketking_bank_info marketking_intermediary_bank_field">
                                        <label class="form-label" for="intermediary-bank-country"><?php esc_html_e('Intermediary Bank - Country','marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="intermediary-bank-country" name="intermediary-bank-country" value="<?php echo esc_attr($info[16]);?>">
                                        </div>
                                    </div>


                                    <?php
                                }

                                if ($custom === 1){
                                    
                                    ?>
                                    <div class="form-group marketking_custom_info">
                                        <label class="form-label" for="paypal-email"><?php echo esc_html($title); ?></label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="custom-method" name="custom-method" placeholder="<?php echo esc_attr($description);?>" value="<?php echo esc_attr($info[1]);?>">
                                        </div>
                                    </div>
                                    <?php
                                }


                                if ($stripe === 1){
                                    if (defined('MARKETKINGPRO_DIR')){
                                        if (intval(get_option( 'marketking_enable_stripe_setting', 1 )) === 1){

                                            ?>

                                            <div class="form-group marketking_stripe_info">
                                                <?php

                                                if (isset($client_id) && isset($secret_key)) {
                                                                                                 
                                                    if ( get_user_meta($user_id, 'vendor_connected', true) == 1 ) {
                                                        ?>
                                                        <div class="clear"></div>
                                                        <div class="marketking_stripe_connect">
                                                            <table class="form-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th style="width: 35%;">
                                                                            <label><?php _e('Stripe', 'marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                                                        </th>
                                                                        <td>
                                                                            <label><?php _e('You are connected with Stripe', 'marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th></th>
                                                                        <td>
                                                                            <input type="button" id="disconnect_stripe" class="button btn-secondary btn-sm btn" name="disconnect_stripe" value="<?php _e('Disconnect Stripe Account', 'marketking-multivendor-marketplace-for-woocommerce'); ?>" />
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <?php 
                                                        $is_stripe_connected = true;
                                                    }
                                                    
                                                    if( !$is_stripe_connected ) {
                                                        
                                                        $the_user = new WP_User($user_id);
                                                        $user_email = $the_user->user_email;
                                                        
                                                        // Show OAuth link
                                                        $authorize_request_body = apply_filters( 'marketking_stripe_authorize_request_params', array(
                                                        'response_type' => 'code',
                                                        'scope' => 'read_write',
                                                        'client_id' => $client_id,
                                                        'redirect_uri' => esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'payouts',
                                                        'state' => $user_id,
                                                        'stripe_user' => array( 
                                                            'email'         => $user_email,
                                                            'url'           => marketking()->get_store_link( $user_id ),
                                                            'business_name' => marketking()->get_store_name_display($user_id),
                                                            'first_name'    => $the_user->first_name,
                                                            'last_name'     => $the_user->last_name
                                                            )
                                                        ), $user_id );
                                                        if( apply_filters( 'marketking_is_allow_stripe_express_api', false ) ) { // set it to STANDARD accounts by default
                                                            //$url = esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'expressconnect';

                                                            $authorize_request_body['suggested_capabilities'] = array( 'transfers', 'card_payments' );
                                                            
                                                            $url = 'https://connect.stripe.com/express/oauth/authorize?' . http_build_query($authorize_request_body);

                                                        } else {
                                                            $url = 'https://connect.stripe.com/oauth/authorize?' . http_build_query($authorize_request_body);
                                                        }
                                                        $stripe_connect_url = MARKETKINGCORE_URL. 'includes/assets/images/stripeconnect.png';

                                                        ?>
                                                        <div class="clear"></div>
                                                        <div class="marketking_stripe_connect">
                                                            <table class="form-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <th style="width: 35%;">
                                                                            <label><?php _e('Stripe', 'marketking-multivendor-marketplace-for-woocommerce'); ?></label>
                                                                        </th>
                                                                        <td><?php _e('You are not connected with Stripe.', 'marketking-multivendor-marketplace-for-woocommerce'); ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th></th>
                                                                        <td>
                                                                            <a href=<?php echo $url; ?> target="_self"><img src="<?php echo $stripe_connect_url; ?>" /></a>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                            <?php

                                        }
                                    }
                                }
                                ?>
                                <div class="form-group">
                                    <button type="button" id="marketking_save_payout" class="btn btn-lg btn-primary"><?php esc_html_e('Save Info','marketking-multivendor-marketplace-for-woocommerce'); ?></button>
                                </div>

                            </form>
                        </div>
                        <div class="modal-footer bg-light">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
?>
