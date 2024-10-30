<?php

class MeowApps_LRI_Admin {

  public function __construct() {
    add_action( 'admin_init', array( $this, 'admin_init' ) );
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );
  }

  function admin_menu() {
    global $lri_core;
    add_options_page( 'Lightbox Responsive Images', 'Lightbox', 'manage_options',
      'lri_settings', array( $this, 'settings_page' ) );
		$css_selector = $lri_core->getoption( 'css_selector', 'lri_basics', null );
		if ( empty( $css_selector ) )
			$lri_core->setoption( 'css_selector', 'lri_basics', '.status-draft, .status-pending, .status-publish' );
    $animate_effect = $lri_core->getoption( 'animate_effect', 'lri_basics', null );
		if ( empty( $animate_effect ) )
			$lri_core->setoption( 'animate_effect', 'lri_basics', 'none' );
    $swipe_effect = $lri_core->getoption( 'swipe_effect', 'lri_basics', null );
		if ( empty( $swipe_effect ) )
			$lri_core->setoption( 'swipe_effect', 'lri_basics', true );
    $theme = $lri_core->getoption( 'theme', 'lri_basics', null );
		if ( empty( $theme ) )
			$lri_core->setoption( 'theme', 'lri_basics', 'plain' );
    $force_resize = $lri_core->getoption( 'force_resize', 'lri_basics', null );
		if ( empty( $force_resize ) )
			$lri_core->setoption( 'force_resize', 'lri_basics', true );
  }

  function admin_init() {
    require( 'lri_class.settings-api.php' );
    $sections = array(
			array(
				'id' => 'lri_basics',
				'title' => __( '&nbsp;&nbsp;Basics', 'meowapps_lri' )
			),
			// array(
			// 	'id' => 'lri_pro',
			// 	'title' => __( 'Pro', 'meowapps_lri' )
			// )
		);

    $effects = array( 'none', 'bounce', 'flash', 'pulse', 'rubberBand', 'shake', 'headShake', 'swing', 'tada', 'wobble', 'jello', 'bounceIn', 'bounceInDown', 'bounceInLeft', 'bounceInRight', 'bounceInUp', 'bounceOut', 'bounceOutDown', 'bounceOutLeft', 'bounceOutRight', 'bounceOutUp', 'fadeIn', 'fadeInDown', 'fadeInDownBig', 'fadeInLeft', 'fadeInLeftBig', 'fadeInRight', 'fadeInRightBig', 'fadeInUp', 'fadeInUpBig', 'fadeOut', 'fadeOutDown', 'fadeOutDownBig', 'fadeOutLeft', 'fadeOutLeftBig', 'fadeOutRight', 'fadeOutRightBig', 'fadeOutUp', 'fadeOutUpBig', 'flipInX', 'flipInY', 'flipOutX', 'flipOutY', 'lightSpeedIn', 'lightSpeedOut', 'rotateIn', 'rotateInDownLeft', 'rotateInDownRight', 'rotateInUpLeft', 'rotateInUpRight', 'rotateOut', 'rotateOutDownLeft', 'rotateOutDownRight', 'rotateOutUpLeft', 'rotateOutUpRight', 'hinge', 'rollIn', 'rollOut', 'zoomIn', 'zoomInDown', 'zoomInLeft', 'zoomInRight', 'zoomInUp', 'zoomOut', 'zoomOutDown', 'zoomOutLeft', 'zoomOutRight', 'zoomOutUp', 'slideInDown', 'slideInLeft', 'slideInRight', 'slideInUp', 'slideOutDown', 'slideOutLeft', 'slideOutRight', 'slideOutUp' );
    $effects_fields = array();
    foreach ( $effects as $effect )
      $effects_fields["$effect"] = $effect;

    $fields = array(
			'lri_basics' => array(
        array(
					'name' => 'theme',
					'label' => __( 'Theme', 'meowapps_lri' ),
          'type' => 'radio',
					'options' => array(
            'plain' => __( 'Plain <br /><small>Very simple and light lightbox.</small><br />', 'meowapps_lri' ),
            'shibuya' => __( 'Shibuya <br /><small>Cool lighbox that displays information about the image.</small>', 'meowapps_lri' ),
          )
        ),
				array(
					'name' => 'css_selector',
					'label' => __( 'CSS Selector', 'meowapps_lri' ),
					'desc' => __( '<br />This CSS selector should match with the container of your post content.', 'meowapps_lri' ),
					'type' => 'text',
					'default' => '.status-draft, .status-pending, .status-publish',
        ),
        array(
					'name' => 'animate_effect',
					'label' => __( 'Animation Effect', 'meowapps_lri' ),
					'desc' => __( '<br />This option adds an animation effect to the lightbox. You can see them in action on the <a target="_blank" href="https://daneden.github.io/animate.css/">Animate.css</a> page.<br />We recommend you to try pulse, zoomIn, or rollIn :)', 'meowapps_lri' ),
					'type' => 'select',
					'options' => $effects_fields
        ),
        array(
					'name' => 'swipe_effect',
					'label' => __( 'Swipe Effect', 'meowapps_lri' ),
					'desc' => __( 'This enables the swipe effect.', 'meowapps_lri' ),
					'type' => 'checkbox',
					'default' => true
        ),
        array(
					'name' => 'force_resize',
					'label' => __( 'Force Resize', 'meowapps_lri' ),
					'desc' => __( 'Even if the image is too small, its size will be adapted to the screen.', 'meowapps_lri' ),
					'type' => 'checkbox',
					'default' => true
        )
      )
    );
    global $lri_settings_api;
		$lri_settings_api = new WeDevs_Settings_API;
		$lri_settings_api->set_sections( $sections );
		$lri_settings_api->set_fields( $fields );
		$lri_settings_api->admin_init();
  }

  function settings_page() {
    require( 'lri_class.settings-api.php' );
    global $lri_settings_api;
    echo '<div class="wrap">';
    echo "<h1>Lightbox Responsive Images</h1>";
    $lri_settings_api->show_navigation();
    $lri_settings_api->show_forms();
    echo '</div>';
  }

}

?>
