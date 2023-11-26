<?php

/*
Individual Store Page
* @version 1.2.0

This template file can be edited and overwritten with your own custom template. To do this, simply copy this file under your theme (or child theme) folder, in a folder named 'marketking', and then edit it there. 

For example, if your theme is storefront, you can copy this file under wp-content/themes/storefront/marketking/ and then edit it with your own custom content and changes.

*/


?>
<h3><?php 
echo apply_filters('marketking_store_header_text_string', esc_html__('Store: ', 'marketking-multivendor-marketplace-for-woocommerce')); 
echo marketking()->get_store_name_display($vendor_id); 
?></h3>

<?php
$store_style = intval(get_option( 'marketking_store_style_setting', 1 ));

// if page is set to elementor for example, set back to 1 - if we reached here it should be 1, 2, or 3
if (!in_array($store_style, array(1, 2, 3))){
	$store_style = 3;
}

if ($store_style === 1){
	?>
	<div id="marketking_vendor_store_page_header">
		<div id="marketking_vendor_store_page_profile">
			<div id="marketking_vendor_store_page_profile_pic">
				<?php
				$img = marketking()->get_store_profile_image_link($vendor_id);
				if (empty($img)){
					// show default image
					$img = MARKETKINGCORE_URL.'includes/assets/images/store-profile.png';
				} else {
					$img = marketking()->get_resized_image($img, 'thumbnail');
				}
				?>
				<img class="marketking_vendor_store_page_profile_image" src="<?php echo esc_url($img);?>">
		
			</div>
			<div id="marketking_vendor_store_page_profile_name">
				<?php echo marketking()->get_store_name_display($vendor_id); ?>
			</div>
			<?php
			// display badges if applicable
			if (defined('MARKETKINGPRO_DIR')){
				if (intval(get_option('marketking_enable_badges_setting', 1)) === 1){
					?>
					<div id="marketking_vendor_page_badges_container">
					<?php
					marketkingpro()->display_vendor_badges($vendor_id, 4, 20);
					?>
					</div>
					<?php
				}
			}
			?>
		</div>			
		<?php
		$img = marketking()->get_store_banner_image_link($vendor_id);
		if (empty($img)){
			$img = MARKETKINGCORE_URL.'includes/assets/images/store-banner.png';
		} else {
			$img = marketking()->get_resized_image($img, 'large');
		}
		?>
		<div id="marketking_vendor_store_page_banner" style="background-image: url('<?php echo esc_url($img);?>');">
			<?php
			// display socials if applicable
			if (defined('MARKETKINGPRO_DIR')){
				if (intval(get_option('marketking_enable_social_setting', 1)) === 1){
					?>
					<div id="marketking_vendor_page_social_container">
					<?php
					marketkingpro()->display_social_links($vendor_id, 10, 20);
					?>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<?php
}

if ($store_style === 2){
	?>
	<div id="marketking_vendor_store_page_header" class="marketking_store_style_2">
		<div id="marketking_vendor_store_page_profile" class="marketking_store_style_2">
			<div id="marketking_vendor_store_page_profile_pic" class="marketking_store_style_2">
				<?php
				$img = marketking()->get_store_profile_image_link($vendor_id);
				if (empty($img)){
					// show default image
					$img = MARKETKINGCORE_URL. 'includes/assets/images/store-profile.png';
				} else {
					$img = marketking()->get_resized_image($img, 'thumbnail');
				}
				?>
				<img class="marketking_vendor_store_page_profile_image" src="<?php echo esc_url($img);?>">
		
			</div>
			<div id="marketking_vendor_store_page_profile_name" class="marketking_store_style_2">
				<?php echo marketking()->get_store_name_display($vendor_id); ?>
			</div>
			<?php
			// display badges if applicable
			if (defined('MARKETKINGPRO_DIR')){
				if (intval(get_option('marketking_enable_badges_setting', 1)) === 1){
					?>
					<div id="marketking_vendor_page_badges_container">
					<?php
					marketkingpro()->display_vendor_badges($vendor_id, 4, 20);
					?>
					</div>
					<?php
				}
			}
			?>
		</div>			
		<?php
		$img = marketking()->get_store_banner_image_link($vendor_id);
		if (empty($img)){
			$img = MARKETKINGCORE_URL. 'includes/assets/images/store-banner.png';
		} else {
			$img = marketking()->get_resized_image($img, 'large');
		}
		?>
		<div id="marketking_vendor_store_page_banner" class="marketking_store_style_2" style="background-image: url('<?php echo esc_url($img);?>');">
			<?php
			// display socials if applicable
			if (defined('MARKETKINGPRO_DIR')){
				if (intval(get_option('marketking_enable_social_setting', 1)) === 1){
					?>
					<div id="marketking_vendor_page_social_container">
					<?php
					marketkingpro()->display_social_links($vendor_id, 10, 20);
					?>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<?php
}

if ($store_style === 3){
	?>
	<div id="marketking_vendor_store_page_header" class="marketking_store_style_3">
		<div id="marketking_vendor_store_page_profile" class="marketking_store_style_3">
			<div id="marketking_vendor_store_page_profile_pic" class="marketking_store_style_3">
				<?php
				$img = marketking()->get_store_profile_image_link($vendor_id);
				if (empty($img)){
					// show default image
					$img = MARKETKINGCORE_URL.'includes/assets/images/store-profile.png';
				} else {
					$img = marketking()->get_resized_image($img, 'thumbnail');
				}
				?>
				<img class="marketking_vendor_store_page_profile_image" src="<?php echo esc_url($img);?>">
		
			</div>
			<div id="marketking_vendor_store_page_profile_name" class="marketking_store_style_3">
				<?php echo marketking()->get_store_name_display($vendor_id); ?>
			</div>
			<?php
			// display badges if applicable
			if (defined('MARKETKINGPRO_DIR')){
				if (intval(get_option('marketking_enable_badges_setting', 1)) === 1){
					?>
					<div id="marketking_vendor_page_badges_container">
					<?php
					marketkingpro()->display_vendor_badges($vendor_id, 4, 20);
					?>
					</div>
					<?php
				}
			}
			?>
		</div>			
		<?php
		$img = marketking()->get_store_banner_image_link($vendor_id);
		if (empty($img)){
			$img = MARKETKINGCORE_URL.'includes/assets/images/store-banner.png';
		} else {
			$img = marketking()->get_resized_image($img, 'large');
		}
		?>
		<div id="marketking_vendor_store_page_banner" class="marketking_store_style_3" style="background-image: url('<?php echo esc_url($img);?>');">
			<?php
			// display socials if applicable
			if (defined('MARKETKINGPRO_DIR')){
				if (intval(get_option('marketking_enable_social_setting', 1)) === 1){
					?>
					<div id="marketking_vendor_page_social_container">
					<?php
					marketkingpro()->display_social_links($vendor_id, 10, 20);
					?>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<?php
}
?>

<!-- TABS -->
<div class="marketking_tabclass">
	<div class="marketking_tabclass_left">
	  <button class="marketking_tablinks" value="marketking_vendor_tab_products" type="button"><?php esc_html_e('Products','marketking-multivendor-marketplace-for-woocommerce');?></button>
	  <?php
	  if (apply_filters('marketking_show_vendor_details_tab_product_page', true)){
	  	?>
		<button class="marketking_tablinks" value="marketking_vendor_tab_info" type="button"><?php esc_html_e('Vendor Details','marketking-multivendor-marketplace-for-woocommerce');?></button>
	  	<?php
	  }
	  if (defined('MARKETKINGPRO_DIR')){
	  	if (intval(get_option('marketking_enable_reviews_setting', 1)) === 1){
  			?>
  			<button class="marketking_tablinks" value="marketking_vendor_tab_reviews" type="button"><?php echo apply_filters('marketking_feedback_tab_name',esc_html__('Feedback','marketking-multivendor-marketplace-for-woocommerce'));?></button>
  			<?php
	  	}
	  }
	  if (defined('MARKETKINGPRO_DIR')){
	  	if (intval(get_option('marketking_enable_storepolicy_setting', 1)) === 1){
	  		// get current vendor
			$policy_enabled = get_user_meta($vendor_id,'marketking_policy_enabled', true);
			if ($policy_enabled === 'yes'){
				$policy_message = get_user_meta($vendor_id,'marketking_policy_message', true);
				// show policies tab
				?>
				<button class="marketking_tablinks" value="marketking_vendor_tab_policies" type="button"><?php echo apply_filters('marketking_policies_tab_name',esc_html__('Policies','marketking-multivendor-marketplace-for-woocommerce'));?></button>

				<?php
			}
  			?>
  			<?php
	  	}
	  }
	  if (defined('MARKETKINGPRO_DIR')){
	  	if (intval(get_option('marketking_enable_inquiries_setting', 1)) === 1){
	  		if (intval(get_option('marketking_enable_vendor_page_inquiries_setting', 1)) === 1){
	  			?>
	  			<button class="marketking_tablinks" value="marketking_vendor_tab_inquiries" type="button"><?php echo apply_filters('marketking_contact_tab_name',esc_html__('Contact','marketking-multivendor-marketplace-for-woocommerce'));?></button>
	  			<?php
	  		}
	  	}
	  }
	  ?>
	</div>
  <div class="marketking_tabclass_right">
  	<?php
	  	if (defined('MARKETKINGPRO_DIR')){
		  	if (intval(get_option('marketking_enable_favorite_setting', 1)) === 1){
		  		// cannot follow self
		  		$user_id = get_current_user_id();
		  		if ($vendor_id !== $user_id && is_user_logged_in()){
		  			$follows = get_user_meta($user_id,'marketking_follows_vendor_'.$vendor_id, true);

		  			?>
		  			<button class="marketking_follow_button" value="<?php echo esc_attr($vendor_id);?>"><?php
		  				if ($follows !== 'yes'){
		  					esc_html_e('Follow','marketking-multivendor-marketplace-for-woocommerce');
		  				} else if ($follows === 'yes'){
		  					esc_html_e('Following','marketking-multivendor-marketplace-for-woocommerce');
		  				}
		  				
		  			?></button>
		  			<?php
		  		}
		  	}

		  	do_action('marketking_store_page_tabright', $vendor_id);

		}
  	?>
  </div>
</div>

<!-- Tab content -->
<div id="marketking_vendor_tab_products" class="marketking_tab <?php echo marketking()->marketking_tab_active('products');?>">
  <?php
  	// Store Notice
  	if (defined('MARKETKINGPRO_DIR')){
	  	if (intval(get_option('marketking_enable_storenotice_setting', 1)) === 1){
			// get current vendor
			$notice_enabled = get_user_meta($vendor_id,'marketking_notice_enabled', true);
			if ($notice_enabled === 'yes'){
				$notice_message = get_user_meta($vendor_id,'marketking_notice_message', true);
				if (!empty($notice_message)){
					wc_print_notice($notice_message,'notice');
				}
			}
		}
  	}
   	
  	echo do_shortcode(apply_filters('marketking_products_shortcode','[products limit="'.apply_filters('marketking_default_products_number',12).'" paginate="true" visibility="visible "cache="false"]'));	



  ?>
</div>

<div id="marketking_vendor_tab_policies" class="marketking_tab <?php echo marketking()->marketking_tab_active('policies');?>">
  <?php
  	// Store Policies
  	if (defined('MARKETKINGPRO_DIR')){
	  	if (intval(get_option('marketking_enable_storepolicy_setting', 1)) === 1){
			// get current vendor
			$policy_enabled = get_user_meta($vendor_id,'marketking_policy_enabled', true);
			if ($policy_enabled === 'yes'){
				$policy_message = get_user_meta($vendor_id,'marketking_policy_message', true);
				if (!empty($policy_message)){
					$policy_message = nl2br(esc_html($policy_message));
					$allowed = array('***h3***','***h4***','***i***','***strong***','***/h3***','***/h4***','***/i***','***/strong***');
					$replaced = array('<h3>','<h4>','<i>','<strong>','</h3>','</h4>','</i>','</strong>');

					$policy_message = str_replace($allowed, $replaced, $policy_message);
					echo $policy_message;
				}
			}
		}
  	}
  ?>
</div>

<div id="marketking_vendor_tab_reviews" class="marketking_tab <?php echo marketking()->marketking_tab_active('reviews');?>">
  <?php
  	// Reviews
  	if (defined('MARKETKINGPRO_DIR')){
	  	if (intval(get_option('marketking_enable_reviews_setting', 1)) === 1){
	  		$items_per_page = apply_filters('marketking_vendor_reviews_per_page', 5);

	  		$pagenr = get_query_var('pagenr2');
	  		if (empty($pagenr)){
	  			$pagenr = 1;
	  		}
			// last 10 reviews here
			$args = array ('post_type' => 'product', 'post_author' => $vendor_id, 'number' => $items_per_page, 'paged' => $pagenr,'type' => 'review');
		    $comments = get_comments( $args );

		    if (empty($comments)){
		    	esc_html_e('There are no reviews yet...','marketking-multivendor-marketplace-for-woocommerce');
		    } else {
		    	// show review average
		    	$rating = marketking()->get_vendor_rating($vendor_id);
		    	// if there's any rating
		    	if (intval($rating['count'])!==0){
		    		?>
		    		<div class="marketking_rating_header">
			    		<?php
			    		// show rating
			    		if (intval($rating['count']) === 1){
			    			$review = esc_html__('review','marketking-multivendor-marketplace-for-woocommerce');
			    		} else {
			    			$review = esc_html__('reviews','marketking-multivendor-marketplace-for-woocommerce');
			    		}
			    		echo '<strong>'.esc_html__('Rating:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.esc_html($rating['rating']).' '.esc_html__('rating from','marketking-multivendor-marketplace-for-woocommerce').' '.esc_html($rating['count']).' '.esc_html($review);
			    		echo '<br>';
			    	?>
			   		</div>
			    	<?php
		    	}
		    }
		    wp_list_comments( array( 'callback' => 'woocommerce_comments' ), $comments);

		    // display pagination

		    // get total nr
		    $args = array ('post_type' => 'product', 'post_author' => $vendor_id, 'fields' => 'ids','type' => 'review');
		    $comments = get_comments( $args );
		    $totalnr = count($comments); //total nr of reviews
		    $nrofpages = ceil($totalnr/$items_per_page);
		    $store_link = marketking()->get_store_link($vendor_id);


		    $i = 1;
		    while($i <= $nrofpages){
		    	$pagelink = $store_link.'/reviews/'.$i;
		    	$active = '';
		    	if ($i === intval($pagenr)){
		    		$active = 'marketking_review_active_page';
		    	}
		    	// display page
		    	?>
		    	<a href="<?php echo esc_attr($pagelink);?>" class="marketking_review_pagination_page <?php echo esc_html($active);?>"><?php echo esc_html($i); ?></a>
		    	<?php
		    	$i++;
		    }


			?>
			<?php
		}
  	}
  
  ?>
</div>

<div id="marketking_vendor_tab_info" class="marketking_tab <?php echo marketking()->marketking_tab_active('info');?>">
  <?php
  	
  	marketking()->get_vendor_details_tab($vendor_id);
  	
	?>
</div>
<?php

// Inquiry tab
if (defined('MARKETKINGPRO_DIR')){
	if (intval(get_option('marketking_enable_inquiries_setting', 1)) === 1){
		if (intval(get_option('marketking_enable_vendor_page_inquiries_setting', 1)) === 1){
			?>
				<div id="marketking_vendor_tab_inquiries" class="marketking_tab <?php echo marketking()->marketking_tab_active('inquiries');?>">
				  <?php
				  	marketkingpro()->get_vendor_inquiries_tab($vendor_id);
				  	
					?>
				</div>
				<?php
		}
	}
}