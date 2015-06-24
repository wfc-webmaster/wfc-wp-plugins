<?php

  /* This class must be included in another file and included later so we don't get an error about HeadwayBlockAPI class not existing. */

  class SliderPlusBlock extends HeadwayBlockAPI {


    public $id = 'sliderplus';

    public $name = 'Slider Plus';

    public $options_class = 'SliderPlusBlockOptions';

    public $description = "SliderPlus is a full content slider for creating slideshows of text, images and videos, and is useful for making featured content sliders, showcases, advertising banners, video slideshows, post and page sliders and much more.";

    public $slideshow ='';

    /**
     * Use this to enqueue styles or scripts for your block.  This method will be execute when the block type is on
     * the current page you are viewing.  Also, not only is it page-specific, the method will execute for every instance
     * of that block type on the current page.
     *
     * This method will be executed at the WordPress 'wp' hook
     **/
    static function enqueue_action( $block_id, $block, $original_block = null ) {

      if ( method_exists( 'HeadwayBlocksData', 'get_legacy_id' ) ) {
        $block[ 'id' ] = HeadwayBlocksData::get_legacy_id( $block );
      }

//		wp_enqueue_style('sliderplus-block-css', PZSP_PLUGIN_URL.'/css/sliderplus-block.css');
      // Enqueue scripts
      wp_enqueue_script( 'jquery' );
      //wp_enqueue_script('jquery-visualize', DPB_BLOCK_URL.'/js/jQuery-Visualize/js/visualize-jQuery.js');
      wp_dequeue_script( 'jquery-cycle2-mod' );
      wp_deregister_script( 'jquery-cycle2-mod' );
      wp_register_script( 'jquery-cycle2-mod-pack', PZSP_PLUGIN_URL . '/js/cycle2/jquery.cycle2.min.mod.2.1.2.js', array( 'jquery' ), '', true );
      wp_register_script( 'jquery-easing', PZSP_PLUGIN_URL . '/js/cycle2/jquery.easing.1.3.js', array( 'jquery' ), '', true );
      wp_register_script( 'pzsp-scripts-standard', PZSP_PLUGIN_URL . '/js/pzsp_scripts.js', array( 'jquery' ), '', true );
      wp_register_script( 'jquery-dotdotdot', PZSP_PLUGIN_URL . '/js/jquery.dotdotdot.min.js', array( 'jquery' ), '', true );

      wp_enqueue_style( 'pzsp-styles', PZSP_PLUGIN_URL . '/css/pzsp.css' );
      wp_enqueue_style( 'pzsp-icomoon-css-', PZSP_PLUGIN_URL . '/css/icomoon/style.css' );
      wp_enqueue_script( 'jquery-cycle2-mod-pack' );
      wp_enqueue_script( 'jquery-easing' );
      wp_enqueue_script( 'pzsp-scripts-standard' );
      wp_enqueue_script( 'jquery-dotdotdot' );


//      var_dump(wp_script_is('jquery-cycle2-mod-pack','enqueued'));
//      var_dump(wp_script_is('jquery-easing','enqueued'));
//      var_dump(wp_script_is('pzsp-scripts-standard','enqueued'));
//      var_dump(wp_script_is('jquery-dotdotdot','enqueued'));

      $slideshow= pzsp_get_slideshow($block,'block');

      $enqueue_arr = pzsp_create_js_css( pzsp_get_slider_meta( strtolower( $slideshow ) ), '#block-' . $block[ 'id' ], false );
      foreach ( $enqueue_arr as $enqueue ) {
        if ( $enqueue[ 0 ] == 'style' ) {
          wp_enqueue_style( $enqueue[ 1 ], $enqueue[ 2 ] );
        } elseif ( $enqueue[ 0 ] == 'script' ) {

          wp_enqueue_script( $enqueue[ 1 ], $enqueue[ 2 ], $enqueue[ 3 ], $enqueue[ 4 ], $enqueue[ 5 ] );
        }
      }

      return;

    }


    /**
     * Use this method to register sidebars, menus, or anything to that nature.  This method executes for every single block that
     * has this method defined.
     *
     * The method will execute for every single block on every single layout.
     **/
    static function init_action( $block_id, $block, $original_block = null ) {
      if ( method_exists( 'HeadwayBlocksData', 'get_legacy_id' ) ) {
        $block[ 'id' ] = HeadwayBlocksData::get_legacy_id( $block );
      }


    }


    /**
     * Use this to insert dynamic JS into the page needed.  This is perfect for initializing instances of jQuery Cycle, jQuery Tabs, etc.
     **/
    static function js_content( $block_id, $block, $original_block = null ) {
      if ( method_exists( 'HeadwayBlocksData', 'get_legacy_id' ) ) {
        $block[ 'id' ] = HeadwayBlocksData::get_legacy_id( $block );
      }
      $settings = SliderPlusBlockOptions::get_settings( $block );

      // Pass the necessary JS to a string
      // This example is for the custom input function in the VE
      // Use  "" for enclosing JS
      $return = "";

      return $return;

    }


    /**
     * Anything in here will be displayed when the block is being displayed.
     **/
    function content( $block ) {

      $slideshow= pzsp_get_slideshow($block,'block');

//      var_dump( $slideshow, $device );

      //$settings = SliderPlusBlockOptions::get_settings($block);
      //pzdebug($settings);
      // In normal situation youwould convert this back to the category names.
//		$categories = (is_array($settings['categories']) ? implode(',',$settings['categories']) : $settings['categories']);
      if ( $slideshow === 'none' ) {
        echo '<div class="pzsp-slider pzsp-noslideshow">You will need to choose a SliderPlus Slideshow to display in this block</div>';
      } else {
        // How can we stop this displaying until we want??
        echo '<span class="pzsp-hider-script"><script type="text/javascript">jQuery(".block-type-sliderplus").hide();</script></span>';
        echo pzsp_render( pzsp_get_slider_meta( strtolower( $slideshow ) ), '#block-' . $block[ 'id' ], false );
      }
    }

    function setup_elements() {
      /*
      Slideshow title: .pzsp-slideshow-title
      Content background: .pzsp-content-container .is-text
      Content title: .pzsp-text-content h2.pzsp-entry-title
      Content body: .pzsp-text-content .pzsp-entry-body
      Content body links: .pzsp-text-content .pzsp-entry-body a
      Content body H3: .pzsp-text-content .pzsp-entry-body h3


       */
      $this->register_block_element( array(
                                       'id'               => 'slideshow-title',
                                       'name'             => 'Slideshow title',
                                       'selector'         => '.pzsp-slideshow-title',
                                       'properties'       => array(
                                         'fonts',
                                         'text-shadow',
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'inherit-location' => 'text'
                                     ) );

      $this->register_block_element( array(
                                       'id'               => 'content-background',
                                       'name'             => 'Content background',
                                       'selector'         => '.pzsp-content-container .is-text',
                                       'properties'       => array(
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'inherit-location' => 'text'
                                     ) );
      $this->register_block_element( array(
                                       'id'               => 'content-title',
                                       'name'             => 'Content title',
                                       'selector'         => '.pzsp-text-content h2.pzsp-entry-title, .pzsp-text-content h2.pzsp-entry-title a',
                                       'properties'       => array(
                                         'fonts',
                                         'text-shadow',
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'inherit-location' => 'text'
                                     ) );

      $this->register_block_element( array(
                                       'id'               => 'content-body',
                                       'name'             => 'Content body text',
                                       'selector'         => '.pzsp-text-content .pzsp-entry-body',
                                       'properties'       => array(
                                         'fonts',
                                         'text-shadow',
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'inherit-location' => 'text'
                                     ) );
      $this->register_block_element( array(
                                       'id'               => 'content-body-links',
                                       'name'             => 'Content body links',
                                       'selector'         => '.pzsp-text-content .pzsp-entry-body a',
                                       'properties'       => array(
                                         'fonts',
                                         'text-shadow',
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'states'           => array(
                                         'Hover'   => '.pzsp-text-content .pzsp-entry-body a:hover',
                                         'Clicked' => '.pzsp-text-content .pzsp-entry-body a:active',
                                         'Visited' => '.pzsp-text-content .pzsp-entry-body a:visited'
                                       ),
                                       'inherit-location' => 'text'
                                     ) );
      $this->register_block_element( array(
                                       'id'               => 'content-h3',
                                       'name'             => 'Content sub-headings (H3 tags)',
                                       'selector'         => '.pzsp-text-content .pzsp-entry-body h3',
                                       'properties'       => array(
                                         'fonts',
                                         'text-shadow',
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'inherit-location' => 'text'
                                     ) );

      $this->register_block_element( array(
                                       'id'               => 'nav-items',
                                       'name'             => 'Navigation items',
                                       'selector'         => '.pzsp-nav-item, .pzsp-nav-container',
                                       'properties'       => array(
                                         'fonts',
                                         'text-shadow',
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'states'           => array(
                                         'Hover'   => '.pzsp-nav-item:hover',
                                         'Current' => '.pzsp-nav-item.cycle-pager-active',
                                       ),
                                       'inherit-location' => 'text'
                                     ) );

      $this->register_block_element( array(
                                       'id'               => 'nav-text',
                                       'name'             => 'Navigation text, bullets, numbers',
                                       'selector'         => '.pzsp-nav-item a',
                                       'properties'       => array(
                                         'fonts',
                                         'text-shadow',
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'states'           => array(
                                         'Hover'   => '.pzsp-nav-item a:hover',
                                         'Current' => '.pzsp-nav-item.cycle-pager-active a',
                                       ),
                                       'inherit-location' => 'text'
                                     ) );
      $this->register_block_element( array(
                                       'id'               => 'nav-squares',
                                       'name'             => 'Navigation square bullets',
                                       'selector'         => '.pzsp-nav-item a .draw-square-bullet',
                                       'properties'       => array(
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'states'           => array(
                                         'Hover'   => '.pzsp-nav-item:hover .draw-square-bullet',
                                         'Current' => '.pzsp-nav-item.cycle-pager-active .draw-square-bullet',
                                       ),
                                       'inherit-location' => 'text'
                                     ) );
      $this->register_block_element( array(
                                       'id'               => '',
                                       'name'             => '',
                                       'selector'         => '',
                                       'properties'       => array(
                                         'fonts',
                                         'text-shadow',
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'states'           => array(
                                         'Hover'   => '',
                                         'Clicked' => '',
                                         'Visited' => ''
                                       ),
                                       'inherit-location' => 'text'
                                     ) );
      $this->register_block_element( array(
                                       'id'               => 'slideshow-panel',
                                       'name'             => 'Panel',
                                       'selector'         => '.pzsp-outer-wrapper',
                                       'properties'       => array(
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'padding'
                                       ),
                                       'inherit-location' => 'text'
                                     ) );
      $this->register_block_element( array(
                                       'id'               => '',
                                       'name'             => '',
                                       'selector'         => '',
                                       'properties'       => array(
                                         'fonts',
                                         'text-shadow',
                                         'background',
                                         'borders',
                                         'rounded-corners',
                                         'box-shadow',
                                         'padding'
                                       ),
                                       'states'           => array(
                                         'Hover'   => '',
                                         'Clicked' => '',
                                         'Visited' => ''
                                       ),
                                       'inherit-location' => 'text'
                                     ) );

    }

  }