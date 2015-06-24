<?php
  /*
    Plugin Name: PizazzWP Slider Plus
    Plugin URI: http://pizazzwp.com/sliderplus
    Description: Slider Plus is a full content slider for creating slideshows of text, images and videos, and is useful for making featured content sliders, showcases, advertising banners, video slideshows, post and page sliders and much more. <p><strong>For more information see the <a href="http://guides.pizazzwp.com/sliderplus/about-sliderplus" target=_blank>Getting started</a> page</strong>. <a href="https://s3.amazonaws.com/341public/LATEST/versioninfo/sp-changelog.html" target=_blank>View changelog</a></p>
    Version: 1.4.0
    Author: Chris Howard
    Author URI: http://pizazzwp.com
    License: GNU GPL v2
   */


  define('PZSP_VERSION', '1.4.0');

  define('PZSP_PLUGIN_URL', substr(WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), '', plugin_basename(__FILE__)), 0, -1));
  define('PZSP_PLUGIN_PATH', substr(WP_PLUGIN_DIR . '/' . str_replace(basename(__FILE__), '', plugin_basename(__FILE__)), 0, -1));
  define('PZSP_CACHE', '/splus/');
  define('PZSP_CACHE_URL', WP_CONTENT_URL . '/uploads/cache/pizazzwp/splus');
  define('PZSP_CACHE_PATH', WP_CONTENT_DIR . '/uploads/cache/pizazzwp/splus');
  define('SPDEBUG', 0);

  $pzsp_cpt_meta_boxes = array();

  global $capabilities;

  $capabilities[ 'administrator' ] = array(
      'delete_others_posts' => 'delete_others_posts',
      'edit_others_posts'   => 'edit_others_posts',
      'edit_post'           => 'edit_post',
      'read_post'           => 'read_post',
      'delete_post'         => 'delete_post',
      'edit_posts'          => 'edit_posts',
      'publish_posts'       => 'publish_posts',
      'read_private_posts'  => 'read_private_posts',
  );

  $capabilities[ 'editor' ] = array(
      'delete_others_posts' => false,
      'edit_others_posts'   => 'edit_others_posts',
      'edit_post'           => 'edit_post',
      'read_post'           => 'read_post',
      'delete_post'         => 'delete_post',
      'edit_posts'          => 'edit_posts',
      'edit_others_posts'   => 'edit_others_posts',
      'publish_posts'       => 'publish_posts',
      'read_private_posts'  => 'read_private_posts',
  );

  $capabilities[ 'author' ] = array(
      'delete_others_posts' => false,
      'edit_post'           => 'edit_post',
      'read_post'           => 'read_post',
      'delete_post'         => 'delete_post',
      'edit_posts'          => 'edit_posts',
      'edit_others_posts'   => false,
      'publish_posts'       => 'publish_posts',
      'read_private_posts'  => false,
  );

  $capabilities[ 'contributor' ] = array(
      'delete_others_posts' => false,
      'edit_post'           => 'edit_post',
      'read_post'           => 'read_post',
      'delete_post'         => 'delete_post',
      'edit_posts'          => 'edit_posts',
      'edit_others_posts'   => false,
      'publish_posts'       => false,
      'read_private_posts'  => false,
  );

  $capabilities[ 'subscriber' ] = array(
      'delete_others_posts' => false,
      'edit_post'           => false,
      'read_post'           => 'read_post',
      'delete_post'         => false,
      'edit_posts'          => false,
      'edit_others_posts'   => false,
      'publish_posts'       => false,
      'read_private_posts'  => false,
  );
  require_once PZSP_PLUGIN_PATH . '/includes/dependency-check/sp-check-dependencies.php';


  if (!function_exists('pizazzwp_head')) {
//	include_once PZSP_PLUGIN_PATH . '/libs/PizazzWP.php';
  }

  add_action('wp_enqueue_scripts', 'pzsp_register_scripts');
  function pzsp_register_scripts()
  {
    // Deregister cycle2 from other pizazz plugins
    wp_dequeue_script('jquery-cycle2-mod');
    wp_deregister_script('jquery-cycle2-mod');
    wp_register_script('jquery-cycle2-mod-pack', PZSP_PLUGIN_URL . '/js/cycle2/jquery.cycle2.min.mod.2.1.2.js', array('jquery'), '', true);
    wp_register_script('jquery-easing', PZSP_PLUGIN_URL . '/js/cycle2/jquery.easing.1.3.js', array('jquery'), '', true);
    wp_register_script('pzsp-scripts-standard', PZSP_PLUGIN_URL . '/js/pzsp_scripts.js', array('jquery'), '', true);
    wp_register_script('jquery-dotdotdot', PZSP_PLUGIN_URL . '/js/jquery.dotdotdot.min.js', array('jquery'), '', true);

  }


  if (is_admin()) {
    add_action('admin_init', 'pzsp_initiate_updater');

    function pzsp_initiate_updater()
    {

      $opt_val = get_option('pizazz_options');
      if (class_exists('HeadwayUpdaterAPI') && empty($opt_val[ 'val_update_method' ])) {

        $updater = new HeadwayUpdaterAPI(array(
                                             'slug'            => 'sliderplus',
                                             'path'            => plugin_basename(__FILE__),
                                             'name'            => 'SliderPlus',
                                             'type'            => 'block',
                                             'current_version' => PZSP_VERSION
                                         ));
      } else {

        require_once('wp-updates-plugin.php');
//			// Load WP auto updater
//			require PZSP_PLUGIN_PATH . '/libs/plugin-update-checker.php';
//			$SliderPlusUpdateChecker = new PluginUpdateChecker(
//					'https://s3.amazonaws.com/341public/LATEST/versioninfo/sliderplusmetadata.json', __FILE__, 'sliderplus'
//			);
      }
    }

  }
// Do a version check in Pizazz
  if (is_admin() && $_SERVER[ 'QUERY_STRING' ] === 'page=pizazz-help') {
    add_action('pizazzwp_updates_sliderplus', 'pzsp_check_version');
  }


  add_action('wp_enqueue_scripts', 'pzsp_unhide');
  function pzsp_unhide()
  {
    // This unhides S+ blocks after being drawn
    wp_enqueue_script('pzsp-unhide-block-script', PZSP_PLUGIN_URL . '/js/pzsp-unhide-block.js',array('jquery'), null, true);
  }

//require(PZSP_PLUGIN_PATH.'/pzsp-admin.php');
  require(PZSP_PLUGIN_PATH . '/pzsp-cpt.php');
  require(PZSP_PLUGIN_PATH . '/pzsp-cpt-slides.php');
  require(PZSP_PLUGIN_PATH . '/pzsp-imageresizer.php');
  require(PZSP_PLUGIN_PATH . '/pzsp-functions.php');
  require(PZSP_PLUGIN_PATH . '/pzsp-display.php');
  require(PZSP_PLUGIN_PATH . '/pzsp-widget.php');
  if (is_admin()) {
    require(PZSP_PLUGIN_PATH . '/pzsp-admin.php');
  }

//include(PZSP_PLUGIN_PATH.'/extensions/rilwis-meta-box/demo/demo.php');

  function pzsp_check_cache()
  {
    if (!is_dir(PZSP_CACHE_PATH)) {
      @mkdir(WP_CONTENT_DIR . '/uploads');
      @mkdir(WP_CONTENT_DIR . '/uploads/cache');
      @mkdir(WP_CONTENT_DIR . '/uploads/cache/pizazzwp');
      @mkdir(WP_CONTENT_DIR . '/uploads/cache/pizazzwp/splus');
    }
    if (!is_dir(PZSP_CACHE_PATH)) {
      echo '<div id="message" class="updated"><p>Unable to create SliderPlus Image Cache folders. You will have to manually create the following folders as necessary:</p>
					&nbsp;&nbsp;&nbsp;&nbsp;wp-content/uploads<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;wp-content/uploads/cache<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;wp-content/uploads/cache/pizazzwp<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;wp-content/uploads/cache/pizazzwp/splus<br/>
					<p>using FTP and set their permissions to 777<br/><br/></p>
				</div>';

      return false;
    }

    return true;
  }

  if (is_admin()) {
    add_action('admin_notices', 'pzsp_check_cache');

    function pzsp_admin_options_page()
    {
      if (version_compare(PIZAZZ_VERSION, '1.2.08', '>')) {
        ?>
        <h3>Refresh SliderPlus Cache</h3>
        <p>If you update or change images in any posts, pages, Slides or Slideshows, sometimes the SliderPlus cache may
          get out-of-sync, and things may not appear right. In that case, you can refresh the SliderPlus cache to ensure
          your site visitors are seeing it right.</p> <p>Please note: Refreshing the cache causes no problems other than
          the next person who visits your site may have to wait a little longer as the cache get recreated. <strong>No
            content in any post, page, Slide or Slideshow will be affected</strong>. </p><p>Click the button to refresh
          the SliderPlus cache.</p>
        <form action="admin.php?page=pizazz-tools" method="post">
          <input class="button-primary" type="submit" name="emptyspcache" value="Refresh SliderPlus Cache">
        </form>
        <hr style="margin-top:20px;border-color:#eee;border-style:solid;"/>

      <?php
      }
    }

    add_action('admin_notices', 'pzsp_check_gd');

    function pzsp_check_gd()
    {
      if (!function_exists('gd_info')) {
        echo '<div id="message" class="error"><p>The PHP GD Image Processing Library is not active on this server. 99% of servers have this already; unfortunately yours doesn\'t. SliderPlus (and other plugins) will be unable to generate images and may not function correctly. Please ask your host to activate the GD Library.</p></div>';
      }
    }

  }


//add_action('publish_post','pzsp_clear_post_cache');
  add_action('post_updated', 'pzsp_clear_post_cache');

// Set-up Action and Filter Hooks
// register_activation_hook(__FILE__, 'pzsp_add_defaults');
// register_uninstall_hook(__FILE__, 'pzsp_delete_plugin_options');
// add_action('admin_init', 'pzsp_init' );

  add_action('admin_enqueue_scripts', 'pzsp_admin_enqueue');

  function pzsp_admin_enqueue()
  {
    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('jquery-ui-button');

    $screen = get_current_screen();
    //var_dump($screen->id);
    if (!empty($screen) && $screen->id != 'pizazzsliders' && $screen->id != 'edit-pizazzsliders' && $screen->id != 'pzsp-slides' && $screen->id != 'edit-pzsp-slides') {
      return;
    }
    // Load up the JS to play with the sliders
    // TODO: Make this page aware in the future
    wp_enqueue_style('pzsp-icomoon-css', PZSP_PLUGIN_URL . '/css/icomoon/style.css');
    wp_enqueue_style('pzsp-admin-css', PZSP_PLUGIN_URL . '/css/pzsp_admin.css');
    wp_enqueue_style('pzsp-chosen-css', PZSP_PLUGIN_URL . '/js/chosen/chosen.min.css');

    wp_enqueue_script('jquery-pizazz-sliderp-admin', PZSP_PLUGIN_URL . '/js/sliderplus-admin.js', array('jquery'), true);
    wp_enqueue_script('jquery-chosen', PZSP_PLUGIN_URL . '/js/chosen/chosen.jquery.min.js', array('jquery'), true);
  }

// Setup cache clearing as required
  add_action('save_post', 'pzsp_clear_post_cache');
  add_action('headway_visual_editor_save', 'pzsp_clear_cache');

  add_action('admin_enqueue_scripts', 'pzsp_admin_scripts');

  function pzsp_admin_scripts()
  {
// Add this in future version. Near future! :P
//	// Include a check if is post or page only
//	if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
//		add_filter( 'mce_buttons', 'filter_mce_button' );
//		add_filter( 'mce_external_plugins', 'filter_mce_plugin' );
//		function filter_mce_button( $buttons ) {
//				// add a separation before our button, here our button's id is &quot;mygallery_button&quot;
//				array_push( $buttons, '|', 'pzsp_sliders_button' );
//				return $buttons;
//		}
//
//		function filter_mce_plugin( $plugins ) {
//				// this plugin file will work the magic of our button
//				$plugins['mygallery'] = PZSP_PLUGIN_URL . '/js/pzsp_sliders_editor_button.js';
//				return $plugins;
//		}
//	}


    $screen = get_current_Screen();
    if ($screen->id == 'pizazzsliders' || $screen->id == 'pzsp-slides') {

      wp_enqueue_script('jquery-pzspmetaboxes', PZSP_PLUGIN_URL . '/js/pzsp_metaboxes.js', array('jquery'));

      wp_enqueue_script('jquery-colorpicker-', PZSP_PLUGIN_URL . '/js/colorpicker/js/colorpicker.js', array('jquery'));
      wp_enqueue_script('jquery-colorpicker-eye', PZSP_PLUGIN_URL . '/js/colorpicker/js/eye.js', array('jquery'));
      wp_enqueue_script('jquery-colorpicker-utils', PZSP_PLUGIN_URL . '/js/colorpicker/js/utils.js', array('jquery'));
      wp_enqueue_script('jquery-colorpicker-layout', PZSP_PLUGIN_URL . '/js/colorpicker/js/layout.js?ver=1.0.2', array('jquery'));

// We don't need these since not showing transitions in backend		
//		wp_enqueue_script('jquery-cycle2-mod', PZSP_PLUGIN_URL . '/js/cycle2/jquery.cycle2.min.mod.js', array('jquery'));
//		wp_enqueue_script('jquery-cycle2-tile-mod', PZSP_PLUGIN_URL . '/js/cycle2/jquery.cycle2.tile.min.mod.js', array('jquery'));
//		wp_enqueue_script('jquery-cycle2-scrollvert-mod', PZSP_PLUGIN_URL . '/js/cycle2/jquery.cycle2.scrollVert.min.mod.js', array('jquery'));
//		wp_enqueue_script('jquery-cycle2-shuffle-mod', PZSP_PLUGIN_URL . '/js/cycle2/jquery.cycle2.shuffle.min.mod.js', array('jquery'));
//		wp_enqueue_script('jquery-cycle2-swipe-mod', PZSP_PLUGIN_URL . '/js/cycle2/jquery.cycle2.swipe.min.mod.js', array('jquery'));
//
//		wp_enqueue_script('jquery-easing', PZSP_PLUGIN_URL . '/js/cycle2/jquery.easing.1.3.js', array('jquery'));

      wp_enqueue_script('js-validator', PZSP_PLUGIN_URL . '/js/javascript_form/gen_validatorv4.js');
      wp_enqueue_script('js-validation', PZSP_PLUGIN_URL . '/js/validation.js', array(), false, true);

      wp_enqueue_style('colorpicker-styles', PZSP_PLUGIN_URL . '/js/colorpicker/css/colorpicker.css');
      // Load this until other plugins are updated with the new admin styles
      wp_enqueue_style('pzsp-styles', PZSP_PLUGIN_URL . '/css/pzsp.css');
    }
//	if ($screen->id == 'edit-pizazzsliders' 
//					|| $screen->id == 'edit-pzsp-slides'
//					|| $screen->id == 'pizazzsliders' 
//					|| $screen->id == 'pzsp-slides') 
//		{
//			if (!version_compare(PIZAZZ_VERSION,'1.2.07','>')) {
//				function pzsp_need_libs1208() {
//					echo '<div id="message" class="error"><p>SliderPlus requires all Pizazz plugins to be using at least libs 1.2.08 to function fully and correctly. Go to the <a href="'.get_site_url().'/wp-admin/plugins.php">Plugins page</a> and update any that require it. If none appear to, check the <a href="'.get_site_url().'/wp-admin/admin.php?page=pizazz-help">PizazzWP menu page</a> for manual updates.</p></div>';
//				}
//				add_action( 'admin_notices', 'pzsp_need_libs1208');
//			}
//	}
  }

  register_activation_hook(__FILE__, 'pzsp_activate');

  function pzsp_activate()
  {
    // Add any code you want when activating the plugin
    // Add a popup window that introduces S+ esp help
//	echo '<div id="message" class="updated" style="padding:10px;">You beauty! SliderPlus is installed and ready to go! You can view the <a href="http://guides.pizazzwp.com/sliderplus/about-sliderplus/" target=_blank>SlidePlus online guide</a> for getting started information. Otherwise, there is aplethora of built in help.</div>';
  }

  register_deactivation_hook(__FILE__, 'pzsp_deactivate');

  function pzsp_deactivate()
  {
    // Add any code you want when deactivating the plugin
    // Delete the user notice so redisplays on activation.
    // This will only delete it for the current user, but more often than not that will be the same user who installed it.
    global $current_user;
    $user_id = $current_user->ID;
    delete_user_meta($user_id, 'pzsp_ignore_notice');
  }

// runs only in admin and user can manage
// checks online version info
// Presents download link to latest version
// Provide link to changelog
  function pzsp_check_version()
  {
    if (!current_user_can('manage_options')) {
      return false;
    }
    $latest_version_array = wp_remote_get('https://s3.amazonaws.com/341public/LATEST/versioninfo/pzsp-version.txt', array('timeout' => 2));
    if (is_wp_error($latest_version_array)) {
      echo 'Could not contact updates server. Try again later.';

      return false;
    }
    $latest_version  = $latest_version_array[ 'body' ];
    $is_beta         = strpos(PZSP_VERSION, 'b');
    $current_version = ($is_beta) ? substr(PZSP_VERSION, $is_beta) : PZSP_VERSION;
    $is_new_version  = version_compare($latest_version, $current_version, '>');
    $pz_version_id   = str_replace('.', '', $latest_version);
    if ($is_new_version) {

      echo '<div id="update-nag" class="pizazzwp-updates-available pzwp-show-update-sliderplus">SliderPlus ' . $latest_version . ' is available, you\'re running ' . PZSP_VERSION . '!  Go to the <a href="' . get_site_url() . '/wp-admin/plugins.php">Plugins page</a> and update it.<br/>
				Or download and manually install it from here:
				<a href="https://s3.amazonaws.com/341public/LATEST/headway-sliderplus-' . $pz_version_id . '.zip">headway-sliderplus-' . $pz_version_id . '</a>
				</div>';
    } else {
      echo '<div style="font-weight:bold;margin-bottom:5px;">You have the latest version</div>';
      echo '<div class="pzwp-show-update-sliderplus">You can re-download it at anytime from here:<br/>
				<a href="https://s3.amazonaws.com/341public/LATEST/headway-sliderplus-' . $pz_version_id . '.zip">headway-sliderplus-' . $pz_version_id . '</a>
				</div>';
    }
    // var_dump($current_version,intval(substr($current_version,-4,4)))	;
    // if (1208 > intval(substr($current_version,-4,4))) {
    // 	echo '<div id="update-nag">All your Pizzaz blocks need to be using library version at least 1208 (the last four digits of the version numbers shown on this page). Please update any that are not on library 1208.</div>';
    // }
  }

  /* * ==================================================================================================================

    Headway Themes stuff

    ================================================================================================================== */

  add_action('after_setup_theme', 'register_pzsp_block');

  function register_pzsp_block()
  {

    // If using Headway, then enable the block that accompanies the plugin
    if (!class_exists('HeadwayBlockAPI')) {
      return false;
    }

    require_once(PZSP_PLUGIN_PATH . '/block-display.php');
    require_once(PZSP_PLUGIN_PATH . '/block-options.php');

//	require_once 'block-functions.php';

    return headway_register_block('SliderPlusBlock', substr(WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), '', plugin_basename(__FILE__)), 0, -1));
  }

  /* Display a notice that can be dismissed */

//add_action('admin_notices', 'pzsp_admin_notice');
//add_action('admin_init', 'pzsp_nag_ignore');

  function pzsp_admin_notice()
  {
    global $current_user;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    if (current_user_can('install_plugins')) {
      if (!get_user_meta($user_id, 'pzsp_ignore_notice')) {


        // ADD A LINK TO USER GUIDE AND TUTES

        echo '<div class="updated">';
        printf(__('<h3>SliderPlus v' . PZSP_VERSION . '</h3>
		        	<a href="https://s3.amazonaws.com/341public/LATEST/versioninfo/sp-changelog.html" target=_blank>View changelog</a><br/>
		        	<a href="%1$s" style="float:right;"><strong>Hide Notice</strong></a><br/>'), '?pzsp_nag_ignore=0');
        echo "</div>";
      }
    }
  }

  function pzsp_nag_ignore()
  {
    global $current_user;
    $user_id = $current_user->ID;
    /* If user clicks to ignore the notice, add that to their user meta */
    if (isset($_GET[ 'pzsp_nag_ignore' ]) && '0' == $_GET[ 'pzsp_nag_ignore' ]) {
      add_user_meta($user_id, 'pzsp_ignore_notice', 'true', true);
    }
//	if (isset($_REQUEST['pzsp_no_help'])) {
//		add_user_meta($user_id, 'pzsp_closed_help', 'true', true);
//	}
//	if (isset($_REQUEST['pzsp_yes_help'])) {
//		delete_user_meta($user_id, 'pzsp_closed_help');
//	}

  }

  //Replace the default WordPress jQuery script with Google Libraries jQuery script
//  function modify_jquery() {
//      wp_deregister_script('jquery');
//      wp_register_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.js', false, '1.10.2');
//      wp_enqueue_script('jquery');
//  }
  // Removed 1.3.14. Not sure why I did this in the first place.
 //add_action('init', 'modify_jquery');

//add_action('admin_footer','pz_open_help');
//function pz_open_help() {
//	$screen = get_current_screen();
//	global $current_user;
//	$user_id = $current_user->ID;
////	pzdebug($_REQUEST);
//	if (get_user_meta($user_id, 'pzsp_closed_help')) {	return;}
//
//	if ($screen->id != 'pizazzsliders' && $screen->id != 'pzsp-slides' && $screen->id != 'edit-pzsp-slides' && $screen->id != 'edit-pizazzsliders' )		{			return;		}
////	var_dump($screen->id);
//	print "<script type=\"text/javascript\">
//		jQuery(document).ready(function() {
//
//			jQuery('#screen-meta').show();
//			jQuery('#contextual-help-wrap').show();
//			jQuery('#contextual-help-link-wrap a').addClass('screen-meta-active');
//		});
//	</script>\n";
//
//
//}
