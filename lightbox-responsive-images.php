<?php
/*
Plugin Name: Lightbox for Responsive Images
Plugin URI: http://www.meow.fr
Description: Simple but efficient lightbox designed for Responsive Images and WP 4.4+.
Version: 0.1.2
Author: Thomas KIM, Jordy Meow
Author URI: http://www.meow.fr
Text Domain: lightbox-responsive-images
Domain Path: /languages
*/

class MeowApps_LRI_Core {

  public function __construct() {
    add_action( 'init', array( $this, 'lri_init' ) );
  }

  function lri_init() {
    add_action( 'wp_enqueue_scripts', array( $this, 'lri_scripts' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'lri_style' ) );
    add_action( 'wp_head', array( $this, 'create_plugin_url_var' ) );
    add_action( 'wp_ajax_get_attachment_meta', array( $this, 'lri_get_attachment_meta' ) );
    add_action( 'wp_ajax_nopriv_get_attachment_meta', array( $this, 'lri_get_attachment_meta' ) );
  }

  function lri_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'lri-js', plugin_dir_url( __FILE__ ) . 'js/lri.js',
      array( 'jquery', 'imagesLoaded' ), '0.1.2', true );
    wp_localize_script( 'lri-js', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_script( 'imagesLoaded', 'https://npmcdn.com/imagesloaded@4.1/imagesloaded.pkgd.min.js',
      array( 'jquery' ), '0.1.2', true);
    wp_enqueue_script( 'touchswipe-js', plugin_dir_url( __FILE__ ) . 'js/jquery.touchSwipe.min.js',
      array( 'jquery' ), '0.1.2', true );
  }

  function lri_style() {
    wp_enqueue_style( 'lri-css', plugin_dir_url( __FILE__ ) . 'css/lri.css' );
    wp_enqueue_style( 'animate-css', plugin_dir_url( __FILE__ ) . 'css/animate.css' );
    wp_enqueue_style( 'lri-fontawesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css' );
    wp_enqueue_style( 'lri-ionicons', 'http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css' );
  }

  function create_plugin_url_var() {
    $params = json_encode( array(
      'plugin_url' => plugin_dir_url( __FILE__ ),
      'css_selector' => $this->getoption( 'css_selector', 'lri_basics', '.status-draft, .status-pending, .status-publish' ),
      'image_effect' => $this->getoption( 'animate_effect', 'lri_basics', 'none' ),
      'slide_effect' => $this->getoption( 'swipe_effect', 'lri_basics', false ),
      'force_resize' => $this->getoption( 'force_resize', 'lri_basics', true ),
      'theme' => $this->getoption( 'theme', 'lri_basics', 'plain' )
    ) );
    echo '<script type="text/javascript">';
    echo 'var meowapps_lri = ' . $params . ';';
    echo '</script>';
  }

  function getoption( $option, $section, $default = '' ) {
    $options = get_option( $section );
    if ( isset( $options[$option] ) ) {
      if ( $options[$option] == "off" )
          return false;
      if ( $options[$option] == "on" )
          return true;
      return $options[$option];
    }
    return $default;
  }

  function setoption( $option, $section, $value ) {
    $options = get_option( $section );
    if ( empty( $options ) )
      $options = array();
    $options[$option] = $value;
    update_option( $section, $options );
  }

  function lri_get_attachment_meta() {
  	echo $this->lri_get_attachment( $_POST['img_id'] );
      wp_die();
  }

  // Retrieve all useful informations of an attachment
  function lri_get_attachment( $attachment_id ) {
    $attachment = get_post( $attachment_id );
    // Here we get all the attachment metadata
    $img_datas = wp_get_attachment_metadata( $attachment_id );
    // Aperture
    $aperture = $img_datas['image_meta']['aperture'];
    // Camera
    $camera = $img_datas['image_meta']['camera'];
    // Date as a timestamp
    $date_timestamp = $img_datas['image_meta']['created_timestamp'];
    // We convert the previous date as a real date, in the wordpress format used for this site.
    $date =  date_i18n( get_option( 'date_format' ), $date_timestamp );
    // Focal length
    $focal_length = $img_datas['image_meta']['focal_length'];
    // Shutter speed, as a float number.
    $shutter_speed = $img_datas['image_meta']['shutter_speed'];
    // If it's not equal to 0, we convert it as a 1/xxxx format
    if ( $shutter_speed != 0 ) {
        if ( ( 1 / $img_datas['image_meta']['shutter_speed']) > 1 ) {
            if ( number_format( ( 1 / $img_datas['image_meta']['shutter_speed']), 1)
              ==  number_format( ( 1 / $img_datas['image_meta']['shutter_speed'] ), 0 ) ) {
                $shutter_speed =  "1/" .
                  number_format( ( 1 / $img_datas['image_meta']['shutter_speed'] ), 0, '.', '');
            }
            else {
                $shutter_speed =  "1/" .
                  number_format( ( 1 / $img_datas['image_meta']['shutter_speed'] ), 1, '.', '');
            }
        } else {
        $shutter_speed = $img_datas['image_meta']['shutter_speed'];
        }
    }
    // ISO
    $iso = $img_datas['image_meta']['iso'];
    return json_encode(array(
        'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
        'aperture' => $aperture,
        'camera' => $camera,
        'caption' => $attachment->post_excerpt,
        'date' => $date,
        'description' => $attachment->post_content,
        'focal_length' => $focal_length,
        'href' => get_permalink( $attachment->ID ),
        'iso' => $iso,
        'shutter_speed' => $shutter_speed,
        'src' => $attachment->guid,
        'title' => $attachment->post_title
    ));
  }
}

if ( class_exists( 'MeowApps_LRI_Core' ) ) {
  global $lri_core;
  $lri_core = new MeowApps_LRI_Core;
  if ( is_admin() ) {
    include( 'lri_admin.php' );
    new MeowApps_LRI_Admin;
  }
}

?>
