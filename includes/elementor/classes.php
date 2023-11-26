<?php



class Elementor_Store_Title_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'marketking_store_title_widget';
	}

	public function get_title() {
		return esc_html__( 'Store Title', 'marketking-multivendor-marketplace-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-post-title';
	}

	public function get_categories() {
		return [ 'marketking' ];
	}

	public function get_keywords() {
		return [ 'marketking', 'store', 'multivendor', 'marketplace' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'marketking-multivendor-plugin-for-woocommerce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color',
			[
				'label' => esc_html__( 'Text Color', 'marketking-multivendor-plugin-for-woocommerce' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#222',
				'selectors' => [
					'{{WRAPPER}} h3' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .marketking_elementor_store_title',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'label' => esc_html__( 'Text Shadow', 'marketking-multivendor-plugin-for-woocommerce' ),
				'selector' => '{{WRAPPER}} .marketking_elementor_store_title',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .marketking_elementor_store_title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}



	protected function render() {
		$vendor_id = marketking()->get_vendor_id_in_store_url();

		$title = marketking()->get_store_name_display($vendor_id);


		$settings = $this->get_settings_for_display();
		
		?>
		<div class="marketking_elementor_store_title">
		<h3><?php echo esc_html($title); ?> </h3>
		</div>
		<?php
	}
}

class Elementor_Vendor_Badges_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'marketking_vendor_badges_widget';
	}

	public function get_title() {
		return esc_html__( 'Vendor Badges', 'marketking-multivendor-marketplace-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-icon-box';
	}

	public function get_categories() {
		return [ 'marketking' ];
	}

	public function get_keywords() {
		return [ 'marketking', 'store', 'multivendor', 'marketplace' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'marketking-multivendor-plugin-for-woocommerce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .marketking_elementor_store_badges_container' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'width',
			[
				'label' => esc_html__( 'Width', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .marketking_elementor_store_badges' => 'width: {{SIZE}}{{UNIT}}; display:inline-block;',
				],
			]
		);

		$this->end_controls_section();

	}



	protected function render() {
		$vendor_id = marketking()->get_vendor_id_in_store_url();

		$title = marketking()->get_store_name_display($vendor_id);

		$settings = $this->get_settings_for_display();
		
		?>
		<div class="marketking_elementor_store_badges_container">
		<div class="marketking_elementor_store_badges">
		<?php
		if (defined('MARKETKINGPRO_DIR')){
			if (intval(get_option('marketking_enable_badges_setting', 1)) === 1){
				marketkingpro()->display_vendor_badges($vendor_id, 4, 200);
			}
		}
		?>
		</div>
		</div>
		<?php
	}
}


class Elementor_Store_Profile_Image_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'marketking_store_profile_image_widget';
	}

	public function get_title() {
		return esc_html__( 'Profile Image', 'marketking-multivendor-marketplace-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return [ 'marketking' ];
	}

	public function get_keywords() {
		return [ 'marketking', 'store', 'multivendor', 'marketplace' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Style', 'marketking-multivendor-plugin-for-woocommerce' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'custom_dimension',
			[
				'label' => esc_html__( 'Image Dimension', 'marketking-multivendor-plugin-for-woocommerce' ),
				'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'Crop the original image size to any custom size. Set custom width or height to keep the original size ratio.', 'marketking-multivendor-plugin-for-woocommerce' ),
				'default' => [
					'width' => '',
					'height' => '',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .marketking_elementor_store_profile_image' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => esc_html__( 'Border', 'marketking-multivendor-plugin-for-woocommerce' ),
				'selector' => '{{WRAPPER}} .marketking_elementor_store_profile_image',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$vendor_id = marketking()->get_vendor_id_in_store_url();
		$img = marketking()->get_store_profile_image_link($vendor_id);
		if (empty($img)){
			// show default image
			$img = MARKETKINGCORE_URL. 'includes/assets/images/store-profile.png';

		} else {
			$img = marketking()->get_resized_image($img, 'thumbnail');
		}

		$this->print_render_attribute_string( 'wrapper' );

		$width = (isset($settings['custom_dimension']['width'])) ? 'width:'.esc_html($settings['custom_dimension']['width']).'px !important;' : '';
		$height = (isset($settings['custom_dimension']['height'])) ? 'height:'.esc_html($settings['custom_dimension']['height']).'px !important;' : '';

		?>
		<div class="marketking_elementor_store_profile_image"><img style="display:inline; <?php echo esc_html($width).' '.esc_html($height); ?>" src="<?php echo esc_url($img);?>"></div>

		<?php
	}

}

class Elementor_Store_Banner_Image_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'marketking_store_banner_image_widget';
	}

	public function get_title() {
		return esc_html__( 'Banner Image', 'marketking-multivendor-marketplace-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-image';
	}

	public function get_categories() {
		return [ 'marketking' ];
	}

	public function get_keywords() {
		return [ 'marketking', 'store', 'multivendor', 'marketplace' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Style', 'marketking-multivendor-plugin-for-woocommerce' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'custom_dimension',
			[
				'label' => esc_html__( 'Image Dimension', 'marketking-multivendor-plugin-for-woocommerce' ),
				'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
				'description' => esc_html__( 'Crop the original image size to any custom size. Set custom width or height to keep the original size ratio.', 'marketking-multivendor-plugin-for-woocommerce' ),
				'default' => [
					'width' => '',
					'height' => '',
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .marketking_elementor_store_banner_image' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'border',
				'label' => esc_html__( 'Border', 'marketking-multivendor-plugin-for-woocommerce' ),
				'selector' => '{{WRAPPER}} .marketking_elementor_store_banner_image',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$vendor_id = marketking()->get_vendor_id_in_store_url();
		$img = marketking()->get_store_banner_image_link($vendor_id);
		if (empty($img)){
			$img = MARKETKINGCORE_URL. 'includes/assets/images/store-banner.png';

		} else {
			$img = marketking()->get_resized_image($img, 'large');
		}

		$this->print_render_attribute_string( 'wrapper' );

		$width = (isset($settings['custom_dimension']['width'])) ? 'width:'.esc_html($settings['custom_dimension']['width']).'px !important;' : '';
		$height = (isset($settings['custom_dimension']['height'])) ? 'height:'.esc_html($settings['custom_dimension']['height']).'px !important;' : '';

		?>
		<div class="marketking_elementor_store_banner_image"><img style="display:inline; <?php echo esc_html($width).' '.esc_html($height); ?>" src="<?php echo esc_url($img);?>"></div>
		<?php
	}

}

class Elementor_Store_Tabs_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'marketking_store_tabs_widget';
	}

	public function get_title() {
		return esc_html__( 'Store Tabs', 'marketking-multivendor-marketplace-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-tabs';
	}

	public function get_categories() {
		return [ 'marketking' ];
	}

	public function get_keywords() {
		return [ 'marketking', 'store', 'multivendor', 'marketplace' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'marketking-multivendor-plugin-for-woocommerce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .marketking_elementor_store_tabs' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}



	protected function render() {
		$vendor_id = marketking()->get_vendor_id_in_store_url();

		$settings = $this->get_settings_for_display();
		
		?>
		<div class="marketking_elementor_store_tabs">
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
		  
		</div>
		</div>
		<?php
	}
}

class Elementor_Store_Tabs_Follow_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'marketking_store_tabs_follow_widget';
	}

	public function get_title() {
		return esc_html__( 'Follow Button', 'marketking-multivendor-marketplace-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-button';
	}

	public function get_categories() {
		return [ 'marketking' ];
	}

	public function get_keywords() {
		return [ 'marketking', 'store', 'multivendor', 'marketplace' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'marketking-multivendor-plugin-for-woocommerce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .marketking_elementor_store_tabs_follow' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}



	protected function render() {
		$vendor_id = marketking()->get_vendor_id_in_store_url();

		$settings = $this->get_settings_for_display();
		
		?>
		<div class="marketking_elementor_store_tabs_follow">
		<div class="marketking_tabclass" style="display:inline-block !important;">
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

				  			do_action('marketking_store_page_tabright', $vendor_id);

				  		}
				  	}
				}
		  	?>
		</div>
		</div>
		<?php
	}
}

class Elementor_Store_Tabs_Content_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'marketking_store_tabs_content_widget';
	}

	public function get_title() {
		return esc_html__( 'Store Tabs Content', 'marketking-multivendor-marketplace-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-table-of-contents';
	}

	public function get_categories() {
		return [ 'marketking' ];
	}

	public function get_keywords() {
		return [ 'marketking', 'store', 'multivendor', 'marketplace' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'marketking-multivendor-plugin-for-woocommerce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .marketking_elementor_store_tabs_content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}



	protected function render() {
		$vendor_id = marketking()->get_vendor_id_in_store_url();

		$settings = $this->get_settings_for_display();
		
		?>
		<div class="marketking_elementor_store_tabs_content">
			<!-- Tab content -->
			<div id="marketking_vendor_tab_products" class="marketking_tab <?php echo marketking()->marketking_tab_active('products');?>">
			  <?php

			  $content = get_transient('marketking_display_products_shortcode_elementor_content'.get_current_user_id());
			  if ($content){
			  	echo $content;
			  }
			  	
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
						$args = array ('post_type' => 'product', 'post_author' => $vendor_id, 'number' => $items_per_page, 'paged' => $pagenr);
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
					    $args = array ('post_type' => 'product', 'post_author' => $vendor_id, 'fields' => 'ids');
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
			  	// if email or phone, show contact info
			  	$showphone = get_user_meta($vendor_id,'marketking_show_store_phone', true);
			  	$showemail = get_user_meta($vendor_id,'marketking_show_store_email', true);
			  	$company = get_user_meta($vendor_id,'billing_company', true);

			  	$phone = get_user_meta($vendor_id,'billing_phone', true);
			  	$email = get_userdata($vendor_id)->user_email;
			  	?>
			  	<h3><?php esc_html_e('Vendor Information', 'marketking-multivendor-marketplace-for-woocommerce'); ?></h3>

			  	<?php

			  	if (apply_filters('marketking_vendor_details_show_vendor', true)){

				  	echo '<strong>'.esc_html__('Vendor: ','marketking-multivendor-marketplace-for-woocommerce').'</strong>';
				  	$store_name = marketking()->get_store_name_display($vendor_id);
				  	echo esc_html($store_name).'<br>';
				  }

			  	// display badges if applicable
				if (apply_filters('marketking_vendor_details_show_badges', true)){

				  	if (defined('MARKETKINGPRO_DIR')){
				  		if (intval(get_option('marketking_enable_badges_setting', 1)) === 1){
				  			marketkingpro()->display_vendor_badges($vendor_id);
				  		}
				  	}
				}
			  	marketking()->display_about_us($vendor_id);

			  	  	
			  	?>
			  	<?php

			  	if (apply_filters('marketking_vendor_details_show_rating', true)){

				  	// rating
				  	$rating = marketking()->get_vendor_rating($vendor_id);
				  	// if there's any rating
				  	if (intval($rating['count'])!==0){
				  		// show rating
				  		if (intval($rating['count']) === 1){
				  			$review = esc_html__('review','marketking-multivendor-marketplace-for-woocommerce');
				  		} else {
				  			$review = esc_html__('reviews','marketking-multivendor-marketplace-for-woocommerce');
				  		}
				  		echo '<strong>'.esc_html__('Rating:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.esc_html($rating['rating']).' '.esc_html__('rating from','marketking-multivendor-marketplace-for-woocommerce').' '.esc_html($rating['count']).' '.esc_html($review);
				  		echo '<br>';
				  	}
				  }
			  	
			  	?>
			  	<?php

			  	if (apply_filters('marketking_vendor_details_show_company', true)){

				  	if (!empty($company)){
				  		echo '<br><strong>'.esc_html__('Company:','marketking-multivendor-marketplace-for-woocommerce').'</strong> ';

				  		echo apply_filters('marketking_vendor_company_name', $company, $vendor_id);

				  		echo '<br>';
				  	}
				  }

			  	$customer = new WC_Customer($vendor_id);
			  	if (is_a($customer,'WC_Customer')){
			  		$address = $customer->get_billing();

			  		if (apply_filters('marketking_vendor_details_show_address', true)){

				  		if (is_array($address)){
					  		if (!empty($address['address_1']) || !empty($address['address_2'])){
					  			echo '<strong>'.esc_html__('Address:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.esc_html($address['address_1']).' '.esc_html($address['address_2']).', '.esc_html($address['city']).', '.esc_html($address['postcode']);

					  			if (!empty($address['country'])){
					  				if (isset($address['state']) && isset($address['country'])){
					  					$countrystates = WC()->countries->get_states( $address['country'] );
					  					$countrycountry = WC()->countries->countries;
					  					if (isset($countrystates[$address['state']]) && isset($countrycountry[ $address['country'] ])){
					  						echo ', '.$countrystates[$address['state']].', '.$countrycountry[ $address['country'] ].'<br>';
					  					}
					  				}
					  			}
					  		}
					  	}
					}
			  	}

			  	  	// Store Cat
			  	  	if (defined('MARKETKINGPRO_DIR')){
			  	  		if (apply_filters('marketking_vendor_details_show_categories', true)){


				  		  	if (intval(get_option('marketking_enable_storecategories_setting', 1)) === 1){
				  		  		$selectedarr = get_user_meta($vendor_id,'marketking_store_categories', true);
				  		  		if (empty($selectedarr)){
				  		  			$selectedarr = array();
				  		  		}
				  		  		
				  		  		if (count($selectedarr) == 1){
				  		  			$text = esc_html__('Store Category','marketking-multivendor-marketplace-for-woocommerce');
				  		  		} else {
				  		  			$text = esc_html__('Store Categories','marketking-multivendor-marketplace-for-woocommerce');
				  		  		}

				  		  		foreach ($selectedarr as $index => $catid){
				  		  			$catname = get_term($catid)->name;
				  		  			$selectedarr[$index] = $catname;
				  		  		}

				  		  		$cats = implode(', ',$selectedarr);
				  		  		echo '<strong>'.$text.':</strong> '.$cats.'<br>';

				  		  	}
				  		  }
			  		  }
			  		
			  		if (apply_filters('marketking_vendor_details_show_phone', true)){

						if ($showphone === 'yes'){
							echo '<strong>'.esc_html__('Phone:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.esc_html($phone).'<br>';
						}
					}
					if (apply_filters('marketking_vendor_details_show_email', true)){

						if ($showemail === 'yes'){
							echo '<strong>'.esc_html__('Email:','marketking-multivendor-marketplace-for-woocommerce').'</strong> '.esc_html($email).'<br>';
						}
				  	}
			  	echo '<br>';

			  	
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
		?>
		</div>
		<?php
	}
}

class Elementor_Store_Notice_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'marketking_store_notice_widget';
	}

	public function get_title() {
		return esc_html__( 'Store Notice', 'marketking-multivendor-marketplace-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-banner';
	}

	public function get_categories() {
		return [ 'marketking' ];
	}

	public function get_keywords() {
		return [ 'marketking', 'store', 'multivendor', 'marketplace' ];
	}


	protected function render() {
		$vendor_id = marketking()->get_vendor_id_in_store_url();

		$settings = $this->get_settings_for_display();
		
	  	// Store Notice
	  	if (defined('MARKETKINGPRO_DIR')){
		  	if (intval(get_option('marketking_enable_storenotice_setting', 1)) === 1){
				// get current vendor
				$notice_enabled = get_user_meta($vendor_id,'marketking_notice_enabled', true);
				if ($notice_enabled === 'yes'){
					$notice_message = get_user_meta($vendor_id,'marketking_notice_message', true);
					if (!empty($notice_message)){
						if (function_exists('wc_print_notice')){
							wc_print_notice($notice_message,'notice');
						}
					}
				}
			}
	  	}
	}
}


class Elementor_Social_Icons_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'marketking_social_icons_widget';
	}

	public function get_title() {
		return esc_html__( 'Social Icons', 'marketking-multivendor-marketplace-for-woocommerce' );
	}

	public function get_icon() {
		return 'eicon-social-icons';
	}

	public function get_categories() {
		return [ 'marketking' ];
	}

	public function get_keywords() {
		return [ 'marketking', 'store', 'multivendor', 'marketplace' ];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Style', 'marketking-multivendor-plugin-for-woocommerce' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .marketking_elementor_social_container' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'width',
			[
				'label' => esc_html__( 'Width', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'.marketking_vendor_social_display' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}



	protected function render() {
		$vendor_id = marketking()->get_vendor_id_in_store_url();

		$title = marketking()->get_store_name_display($vendor_id);

		$settings = $this->get_settings_for_display();
		
		?>
		<div class="marketking_elementor_social_container">
		<?php
		marketkingpro()->display_social_links($vendor_id, 10, 20);
		?>
		</div>
		<?php
	}
}

?>