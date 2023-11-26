<?php
/*

Header Bar
* @version 1.0.2

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/
?>

<!-- main header @s -->
<div class="nk-header nk-header-fixed is-light">
    <div class="container-fluid">
        
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ml-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="<?php echo esc_attr(get_home_url());?>" class="logo-link">
                    <img class="logo-dark logo-img" src="<?php echo esc_url($logo_src); ?>" alt="logo-dark">
                </a>
            </div><!-- .nk-header-brand -->
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">
                    <!-- HIDDEN COMMENTS FOR SCRIPTS PURPOSES -->
                    <em class="icon ni ni-comments ni-comments-hidden"></em>
                    <?php
                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option( 'marketking_enable_messages_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('messages')){

                                if (!isset($messages)){
                                    $user_id = get_current_user_id();
                                    $currentuser = new WP_User($user_id);
                                    $user = $currentuser->user_login;
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
                                }
                                if (!isset($unread_msg)){
                                    $unread_msg = 0;
                                    $user_id = get_current_user_id();
                                    $currentuser = new WP_User($user_id);
                                    $user = $currentuser->user_login;
                                    $currentuserlogin = $currentuser -> user_login;

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
                                }
                                if (!isset($announcements)){
                                    $user_id = get_current_user_id();
                                    $currentuser = new WP_User($user_id);
                                    $user = $currentuser->user_login;
                                    $currentuserlogin = $currentuser -> user_login;
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

                                    $unread_ann = 0;
                                    foreach ($announcements as $announcement){
                                        $read_status = get_user_meta($user_id,'marketking_announce_read_'.$announcement->ID, true);
                                        if (!$read_status || empty($read_status)){
                                            $unread_ann++;
                                        }
                                    }
                                }
                                ?>
                                <li class="dropdown chats-dropdown hide-mb-xs">
                                    <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-toggle="dropdown">
                                        <div class="icon-status <?php if ($unread_msg !== 0) {echo 'icon-status-info';}?>"><em class="icon ni ni-comments"></em></div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right">
                                        <div class="dropdown-head">
                                            <span class="sub-title nk-dropdown-title"><?php echo apply_filters('marketking_unread_announcements_text',esc_html__('Recent Messages', 'marketking-multivendor-marketplace-for-woocommerce')); ?></span>
                                        </div>
                                        <div class="dropdown-body">
                                            <ul class="chat-list">
                                                <?php
                                                // remove closed messages
                                                $closedmsg = array();
                                                foreach ($messages as $message){
                                                    $nr_messages = get_post_meta ($message, 'marketking_message_messages_number', true);
                                                    $last_closed_time = get_user_meta($user_id,'marketking_message_last_closed_'.$message, true);
                                                    if (!empty($last_closed_time)){
                                                        $last_message_time = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_time', true);
                                                        if (floatval($last_closed_time) > floatval($last_message_time)){
                                                            array_push($closedmsg, $message);
                                                        }
                                                    }
                                                }

                                                $messagesarr = array_diff($messages,$closedmsg);
                                                // show last 6 messages that are active (not closed)
                                                $messagesarr = array_slice($messagesarr, 0, 6);
                                                foreach ($messagesarr as $message){ // message is a message thread e.g. conversation

                                                    $title = substr(get_the_title($message), 0, 65);
                                                    if (strlen($title) === 65){
                                                        $title .= '...';
                                                    }
                                                    $nr_messages = get_post_meta ($message, 'marketking_message_messages_number', true);

                                                    $last_message_time = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_time', true);
                                                    // build time string
                                                    // if today
                                                    if((time()-$last_message_time) < 86400){
                                                        // show time
                                                        $timestring = date_i18n( 'h:i A', $last_message_time+(get_option('gmt_offset')*3600) );
                                                    } else if ((time()-$last_message_time) < 172800){
                                                    // if yesterday
                                                        $timestring = 'Yesterday at '.date_i18n( 'h:i A', $last_message_time+(get_option('gmt_offset')*3600) );
                                                    } else {
                                                    // date
                                                        $timestring = date_i18n( get_option('date_format'), $last_message_time+(get_option('gmt_offset')*3600) ); 
                                                    }

                                                    $last_message = get_post_meta ($message, 'marketking_message_message_'.$nr_messages, true);
                                                    // first 100 chars
                                                    $last_message = substr($last_message, 0, 100);

                                                    // check if message is unread
                                                    $is_unread = '';
                                                    $last_message_author = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_author', true);
                                                    if ($last_message_author !== $currentuserlogin){
                                                        $last_read_time = get_user_meta($user_id,'marketking_message_last_read_'.$message, true);
                                                        if (!empty($last_read_time)){
                                                            $last_message_time = get_post_meta ($message, 'marketking_message_message_'.$nr_messages.'_time', true);
                                                            if (floatval($last_read_time) < floatval($last_message_time)){
                                                                $is_unread = 'is-unread';
                                                            }
                                                        } else {
                                                            $is_unread = 'is-unread';
                                                        }
                                                    } 
                                              
                                                    ?>
                                                    <li class="chat-item <?php echo esc_attr($is_unread);?>">
                                                        <a class="chat-link" href="<?php echo trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'messages?id='.esc_attr($message);?>">

                                                            <?php

                                                            $otherparty = marketking()->get_other_chat_party($message);
                                                            $icon = marketking()->get_display_icon_image($otherparty);
                                                          
                                                            ?>
                                                            <div class="chat-media user-avatar" style="<?php
                                                            if (strlen($icon) != 2){
                                                                echo 'background-image: url('.$icon.') !important;';
                                                            }
                                                            ?>">
                                                                <span><?php 
                                                                if (strlen($icon) == 2){
                                                                    echo esc_html($icon);
                                                                }
                                                                ?></span>
                                                            </div>
                                                            <div class="chat-info">
                                                                <div class="chat-from">
                                                                    <div class="name"><?php echo esc_html($title);?></div>
                                                                    <span class="time"><?php echo esc_html($timestring);?></span>
                                                                </div>
                                                                <div class="chat-context">
                                                                    <div class="text"><?php echo esc_html(strip_tags($last_message));?></div>

                                                                </div>
                                                            </div>
                                                        </a>
                                                    </li><!-- .chat-item -->
                                                    <?php

                                                }
                                                ?>
                                            </ul><!-- .chat-list -->
                                        </div><!-- .nk-dropdown-body -->
                                        <div class="dropdown-foot center">
                                            <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'messages'); ?>"><?php esc_html_e('View All', 'marketking-multivendor-marketplace-for-woocommerce'); ?></a>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                    }
                    ?>
                    <?php
                    if (defined('MARKETKINGPRO_DIR')){
                        if (intval(get_option( 'marketking_enable_announcements_setting', 1 )) === 1){
                            if(marketking()->vendor_has_panel('announcements')){
                                ?>
                                <li class="dropdown notification-dropdown">
                                    <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-toggle="dropdown">
                                        <div class="icon-status <?php if ($unread_ann !== 0) {echo 'icon-status-info';}?>"><em class="icon ni ni-bell"></em></div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right">
                                        <div class="dropdown-head">
                                            <span class="sub-title nk-dropdown-title"><?php echo apply_filters('marketking_unread_announcements_text',esc_html__('Unread Announcements', 'marketking-multivendor-marketplace-for-woocommerce')); ?></span>
                                        </div>
                                        <div class="dropdown-body">
                                            <?php
                                            // show all announcements
                                            $i=1;
                                            foreach ($announcements as $announcement){
                                                $read_status = get_user_meta($user_id,'marketking_announce_read_'.$announcement->ID, true);
                                                if (!$read_status || empty($read_status)){
                                                    // is unread, so let's display it
                                                    $i++;
                                                } else {
                                                    continue;
                                                }

                                                if ($i>6){
                                                    continue;
                                                }

                                                ?>
                                                <div class="nk-notification">
                                                    <div class="nk-notification-item dropdown-inner">
                                                        <div class="nk-notification-icon">
                                                            <em class="icon icon-circle bg-warning-dim ni ni-curve-down-right"></em>
                                                        </div>
                                                        <div class="nk-notification-content">
                                                            <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'announcement/?id='.esc_attr($announcement->ID)); ?>"><div class="nk-notification-text"><?php echo esc_html($announcement->post_title);?></div></a>
                                                            <div class="nk-notification-time"><?php echo esc_html(get_the_date(get_option( 'date_format' ), $announcement));?></div>
                                                        </div>
                                                    </div>
                                                </div><!-- .nk-notification -->
                                                <?php
                                            }
                                            ?>
                                        </div><!-- .nk-dropdown-body -->
                                        <div class="dropdown-foot center">
                                            <a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true))).'announcements'); ?>"><?php esc_html_e('View All', 'marketking-multivendor-marketplace-for-woocommerce'); ?></a>
                                        </div>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                    }
                    ?>
                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle mr-n1" data-toggle="dropdown">
                            <div class="user-toggle">
                                <?php
                                $icon = marketking()->get_display_icon_image($user_id);
                                ?>
                                <div class="user-avatar sm" <?php if (strlen($icon)!=2){ echo 'style="background-image: url(\''.$icon.'\');background-size: contain!important;"';} ?>>
                                    <?php 
                                        if (strlen($icon)==2){
                                            echo $icon;
                                        }
                                    ?>
                                    
                                </div>
                                <div class="user-info d-none d-xl-block">
                                    <div class="user-status user-status-active"><?php esc_html_e('Vendor','marketking-multivendor-marketplace-for-woocommerce');?></div>
                                    <div class="user-name dropdown-indicator"><?php 
                                        $storename = marketking()->get_store_name_display($user_id);
                                        $firstlastname = $currentuser->first_name.' '.$currentuser->last_name;
                                        if(empty($storename)){
                                            echo esc_html($firstlastname);
                                        } else {
                                            echo esc_html($storename);
                                        }
                                        ?></div>
                                        
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar" <?php if (strlen($icon)!=2){ echo 'style="background-image: url(\''.$icon.'\');background-size: contain!important;"';} ?>>
                                        <span>
                                            <?php 
                                                if (strlen($icon)==2){
                                                    echo $icon;
                                                }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text"><?php 
                                        if(empty($storename)){
                                            echo esc_html($firstlastname);
                                        } else {
                                            echo esc_html($storename);
                                        }
                                        ?></span>
                                        <span class="sub-text"><?php 
                                        if(!empty($storename)){
                                            echo esc_html($firstlastname);
                                        }

                                         ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a href="<?php echo esc_attr(marketking()->get_store_link($user_id));?>"><em class="icon ni ni-home"></em><span><?php esc_html_e('Go to My Store','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                </ul>

                            </div>
                            <?php
                            if (!marketking()->is_vendor_team_member()){
                                ?>
                                <div class="dropdown-inner">
                                    <ul class="link-list">
                                        <li><a href="<?php echo esc_attr(trailingslashit(get_page_link(apply_filters( 'wpml_object_id', get_option( 'marketking_vendordash_page_setting', 'disabled' ), 'post' , true)))).'profile';?>"><em class="icon ni ni-account-setting-fill"></em><span><?php esc_html_e('Store Settings','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                    </ul>

                                </div>
                                <?php
                            }
                            ?>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li><a href="<?php echo esc_url(wp_logout_url()); ?>"><em class="icon ni ni-signout"></em><span><?php esc_html_e('Sign out','marketking-multivendor-marketplace-for-woocommerce');?></span></a></li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div><!-- .nk-header-wrap -->
    </div><!-- .container-fliud -->
</div>
<!-- main header @e -->
<!-- content @s -->