<?php
  /*
    Plugin Name: PizazzWP Libraries
    Plugin URI: http://pizazzwp.com
    Description: Pizazz is a framework for the Pizazz family of add-ons for WordPress and Headway. It ALSO includes a function to display floating buttons on your site. Note: This plugin is required to use any of the Pizazz add-ons
    Author: Chris Howard
    Version: 1.6.3
    Author URI: http://pizazzwp.com
    License: GNU GPL v2
   */


  if (!function_exists('pizazzwp_head')) {
    define('PIZAZZ_VERSION', '1.6.3');

    define('PIZAZZ_ICON_URL', trailingslashit(plugin_dir_url(__FILE__)) . '/wp-icon.png');
    define('PIZAZZ_ICON_PATH', trailingslashit(plugin_dir_path(__FILE__)) . '/wp-icon.png');
    define('PIZAZZ_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
    define('PIZAZZ_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)));


    define('PIZAZZWP_CACHE_PATH', WP_CONTENT_DIR . '/uploads/cache/pizazzwp/');
    define('PIZAZZWP_CACHE_URL', WP_CONTENT_URL . '/uploads/cache/pizazzwp/');

    require_once('wp-updates-plugin.php');
    new WPUpdatesPluginUpdater_426('http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));
    //
    //
    //
    // Keep this up to date!!
    //
    //
    //
    //
    define('PIZAZZ_SCREENS', 'pizazzwp_page_pizazz-help:edit-gp_gallery:gp_gallery:edit-pizazzsliders:pizazzsliders:edit-pzsp-slides:pzsp-slides:pizazzwp_page_pizazz-tools:pizazzwp_page_pizazz-news:pizazzwp_page_pizazz-floats'
    );

    // Need to do this for HTTPS and images to work
    define('PIZAZZ_IMAGE_URL', str_replace('http:', '', PIZAZZ_PLUGIN_URL));

    require_once(PIZAZZ_PLUGIN_PATH . '/pzwp_functions.php');
    require_once(PIZAZZ_PLUGIN_PATH . '/pz-post-types.php');
    require_once PIZAZZ_PLUGIN_PATH . '/pzwp-focal-point/pzwp-focal-point.php';


    add_action('init', 'pizazz_init');

    function pizazz_init()
    {
      if (is_admin()) {
        add_action('admin_head', 'pizazzwp_admin_head');
        if ($_SERVER[ 'QUERY_STRING' ] === 'page=pizazz-help') {
          add_action('admin_footer', 'pzwp_admin_footer_scripts', 12);
        }
        add_action('admin_head', 'pizazzwp_head');
        add_action('admin_menu', 'pz_check_globals');
        if (current_user_can('manage_options')) {
          $opt_val = get_option('pizazz_options');
          if ($opt_val[ 'val_hide_debug_message' ] != 'hidedebugmsg') {
            add_action('activity_box_end', 'pz_dash_debug_message');
          }
          if ($opt_val[ 'val_hide_dash_message' ] != 'hidedashmsg') {
            add_action('activity_box_end', 'pz_dash_update_message');
          }
        }
      }
      if (!is_admin()) {
        add_action('wp_head', 'pizazzwp_head');
        add_action('wp_footer', 'pzwp_add_floaties');
      }
    }

    function pz_dash_update_message()
    {
      echo '<div id="update-nag" >Note: If the Headway/WordPress updater for PizazzWP plugins is not working, enable the WP Updates server in PizazzWP > Options.</div>';
    }

    function pz_dash_debug_message()
    {
      if (WP_DEBUG) {
        echo '<div id="message" class="updated" style="padding:10px;">Note: You have WP debug mode enabled. This could slow your site down noticeably.</div>';
      }
    }

    add_action('admin_enqueue_scripts', 'pizazz_admin_scripts');

    function pizazz_admin_scripts()
    {

//$screen_info = get_current_screen();
//var_dump(PIZAZZ_VERSION);
//var_dump($_SERVER['QUERY_STRING'],$_SERVER);
//var_dump(explode(':',PIZAZZ_SCREENS));
//var_dump(array_search($current_screen->id,$pizazz_screens));
// Pizazz admin pages ID
// PizazzWP: 									pizazzwp_page_pizazz-help
// Galleries: 								edit-gp_gallery
// Galleries > Add/Edit:			gp_gallery
// S+ Slideshows: 						edit-pizazzsliders
// S+ Slideshows > Add/Edit:	pizazzsliders
// S+ Slides: 								edit-pzsp-slides
// S+ Slides > Add/Edit:			pzsp-slides
// Tools:											pizazzwp_page_pizazz-tools
// News:											pizazzwp_page_pizazz-news
// Floating buttons:					pizazzwp_page_pizazz-floats


      global $current_screen;
      $pizazz_screens = explode(':', PIZAZZ_SCREENS);

      if (array_search($current_screen->id, $pizazz_screens)) {

        function pzwp_float_scripts()
        {
          if (!wp_script_is('jquery-ui-tabs')) {
            wp_enqueue_script('jquery-ui-tabs', false, array('jquery'));
          }
        }

      }
      wp_enqueue_style('pzwp-admin-styles', PIZAZZ_PLUGIN_URL . 'css/admin-styles.css');
      wp_enqueue_style('pzwp-styles', PIZAZZ_PLUGIN_URL . 'css/pzwp-styles.css');

      if (!wp_script_is('jquery-qtip') && array_search($current_screen->id, $pizazz_screens)
      ) {
        wp_enqueue_style('jquery-qtip-css', PIZAZZ_PLUGIN_URL . 'js/jquery.qtip/jquery.qtip.min.css');
        wp_register_script('jquery-qtip', PIZAZZ_PLUGIN_URL . 'js/jquery.qtip/jquery.qtip.min.mod.js', array('jquery'));
//        wp_register_script('jquery-qtip', PIZAZZ_PLUGIN_URL . 'js/jquery.qtip/jquery.qtip.min.js', array('jquery'));
        wp_enqueue_script('jquery-qtip');
      }
    }

    add_action('wp_enqueue_scripts', 'pizazz_frontend_scripts');

    function pizazz_frontend_scripts()
    {

// TODO: Code options for fittext				
//				wp_enqueue_script('jquery-fittext', PIZAZZ_PLUGIN_URL.'js/jquery.fittext.js');
      wp_enqueue_style('pzwp-styles', PIZAZZ_PLUGIN_URL . 'css/pzwp-styles.css');
    }

    function pizazzwp_head()
    {
      $opt_val = get_option('pzwp_opts');

      // TODO: Only create floater script if needed

      echo "<script type='text/javascript'>";
      echo "

		jQuery(document).ready(function() {
			jQuery.fx.speeds._default = 500;
			var loading = jQuery('<img src=\"" . PIZAZZ_IMAGE_URL . "images/loading.gif\" alt=\"loading\" class=\"loading\">');\n";

      for ($i = 0; $i < 3; $i++) {
        if ($opt_val[ $i ][ 'val_page' ] != 'none' && $opt_val[ $i ][ 'val_page' ] != 'url') {
          echo "jQuery('.pzwp-floater.floater-" . $i . " a').each(function() {
							var dialog = jQuery('<div class=\"pzwp-dialog-popup-" . $i . "\"></div>')
								.append(loading.clone());
							var link = jQuery(this).one('click', function() {
								dialog
									.load(link.attr('href') + ' " . $opt_val[ $i ][ 'val_contentcss' ] . "')
									.dialog({
										title: link.attr('title'),
							            modal: true,
							            show: 'drop',
				            			position:['center',50],
							           	width: 800,
										height: 600,
										buttons: {'Close': function() {dialog.dialog('close');}}
									});
				
								link.click(function() {
									dialog.dialog('open')
					                .dialog( 	                );
									
									return false;
								});
				
								return false;
							});
						});
					";
        }
      }

      echo "});";

      echo "</script>";
    }

    function pizazzwp_admin_head()
    {
      $pzwp_current_admin_screen = get_current_screen();
      //	var_dump($pzwp_current_admin_screen,$pzwp_current_admin_screen->id);
      if ($pzwp_current_admin_screen->id == 'pizazzwp_page_pizazz-floats') {
        wp_enqueue_style('colorpicker-styles', PIZAZZ_PLUGIN_URL . 'js/colorpicker/css/colorpicker.css');
        wp_enqueue_script('jquery-colorpicker-', PIZAZZ_PLUGIN_URL . 'js/colorpicker/js/colorpicker.js', array('jquery'), '', true);
        wp_enqueue_script('jquery-colorpicker-eye', PIZAZZ_PLUGIN_URL . 'js/colorpicker/js/eye.js', array('jquery'), '', true);
        wp_enqueue_script('jquery-colorpicker-utils', PIZAZZ_PLUGIN_URL . 'js/colorpicker/js/utils.js', array('jquery'), '', true);
        wp_enqueue_script('jquery-colorpicker-layout', PIZAZZ_PLUGIN_URL . 'js/colorpicker/js/layout.js?ver=1.0.2', array('jquery'), '', true);
        wp_enqueue_script('jquery-pz-floaties', PIZAZZ_PLUGIN_URL . 'js/pz-floatie.js', array('jquery'), '', true);
      }

      $pizazz_screens = explode(':', PIZAZZ_SCREENS);
      if (array_search($pzwp_current_admin_screen->id, $pizazz_screens)) {
        wp_enqueue_script('jquery-pz-help', PIZAZZ_PLUGIN_URL . 'js/pz-help.js', array('jquery'), '', true);
      }
      wp_enqueue_script('jquery-pz-admin', PIZAZZ_PLUGIN_URL . 'js/pz-admin.js', array('jquery'), '', true);


    }

    function pzwp_admin_footer_scripts()
    {
      $screen = get_current_screen();
      if ($screen->id == 'pizazzwp_page_pizazz-help') {
        echo '
<script type="text/javascript" src="http://assets.freshdesk.com/widget/freshwidget.js"></script>
<script type="text/javascript">
	FreshWidget.init("", {"queryString": "&widgetType=popup&formTitle=PizazzWP+Help+Request&screenshot=no", "widgetType": "popup", "buttonType": "text", "buttonText": "Support", "buttonColor": "white", "buttonBg": "#e0183f", "alignment": "2", "offset": "235px", "formHeight": "500px", "screenshot": "no", "url": "https://pizazzwp.freshdesk.com"} );
</script>
';
      }
    }

    function pz_check_globals()
    {
      // Provide access to $pizzz_menu global and check if it's set. Run pizazz_menu if it's not.
      global $pizazz_menu, $pizazzwp_updates;
      if (!$pizazz_menu) {
        $pizazz_menu = add_menu_page('About PizazzWP', 'PizazzWP', 'edit_posts', 'pizazzwp', 'pizazz_admin', PIZAZZ_ICON_URL, 92);
        //Maybe revisit this on later
        // add_submenu_page(
        // 'pizazzwp',
        // 'Styling',
        // 'Styling',
        // 'manage_options',
        // 'pizazz-styling',
        // 'pizazz_styling'
        //
        // );

        $opt_val = get_option('pizazz_options');
        if ($opt_val[ 'val_hide_pzwp_floaties' ] != 'hidepzwpfloaties') {

          add_submenu_page(
              'pizazzwp', 'Pizazz Floating Buttons', 'Floating Buttons', 'manage_options', 'pizazz-floats', 'pizazz_floats'
          );
        }
        if ($opt_val[ 'val_hide_pzwp_news' ] != 'hidepzwpnews') {
          add_submenu_page(
              'pizazzwp', 'Pizazz News', 'News', 'publish_pages', 'pizazz-news', 'pizazz_news'
          );
        }
        add_submenu_page(
            'pizazzwp', 'Pizazz Tools', 'Tools', 'publish_pages', 'pizazz-tools', 'pizazz_tools'
        );
        add_submenu_page(
            'pizazzwp', 'Pizazz Options', 'Options', 'manage_options', 'pizazz-options', 'pizazz_options'
        );
      }
      add_submenu_page(
          'pizazzwp', 'About PizazzWP', 'About & Support', 'publish_pages', 'pizazz-help', 'pizazz_admin'
      );
      // Make sure Pizazz About is the first menu
      global $submenu;
      if ($submenu[ 'pizazzwp' ][ 0 ][ 0 ] != 'About & Support' && current_user_can('publish_pages')) {
        // This is reliant on About being the last menu item
        array_unshift($submenu[ 'pizazzwp' ], array_pop($submenu[ 'pizazzwp' ]));
      } elseif ($submenu[ 'pizazzwp' ][ 0 ][ 0 ] != 'About & Support' && !current_user_can('publish_pages')) {
        // This is reliant on About being the last menu item
        if (!empty($submenu[ 'pizazzwp' ])) {
          array_pop($submenu[ 'pizazzwp' ]);
        }
      }

      // Surely there's a better way!
      //
//		if ($GLOBALS[ 'submenu' ][ 'pizazzwp' ][ 0 ][ 0 ] != 'PizazzWP' && current_user_can('publish_pages'))
//		{
//			add_submenu_page(
//				'pizazzwp', 'About PizazzWP', 'PizazzWP', 'publish_pages', 'pizazz-help', 'pizazz_admin'
//			);
//
//
//			$nm         = count($GLOBALS[ 'submenu' ][ 'pizazzwp' ]);
//			$temp_array = $GLOBALS[ 'submenu' ][ 'pizazzwp' ];
//
//			$GLOBALS[ 'submenu' ][ 'pizazzwp' ][ 0 ] = $temp_array[ $nm - 1 ];
//			for ($i = 1; $i < ($nm); $i++)
//			{
//				$GLOBALS[ 'submenu' ][ 'pizazzwp' ][ $i ] = $temp_array[ $i - 1 ];
//			}
//		}
//		if ($GLOBALS[ 'submenu' ][ 'pizazzwp' ][ 0 ][ 0 ] == 'PizazzWP')
//		{
//			$GLOBALS[ 'submenu' ][ 'pizazzwp' ][ 0 ][ 0 ] = 'About & Support';
//		};
    }

    // Admin main page
    function pizazz_admin()
    {
      global $title;
      ?>
      <div class="wrap">

        <!-- Display Plugin Icon, Header, and Description -->
        <div class="icon32" id="icon-users"><br></div>

        <h2><?php echo $title ?></h2>
        <?php
          $opt_val = get_option('pizazz_options');
          if ($opt_val[ 'val_hide_debug_message' ] != 'hidedebugmsg' && WP_DEBUG) {
            echo '<div id="message" class="updated" style="padding:10px;">Note: You have WP debug mode enabled. This will slow your site down noticeably.</div>';
          }
        ?>

        <?php echo '<br/><div class="pzwp-info-block pzwp_cell wide"><div class="pzwp-product-icons"><img src="' . PIZAZZ_IMAGE_URL . '/images/pizazzwp-logo-small.png"/></div><div class="pzwp-product-info">'; ?>
        <h2>PizazzWP</h2>

        <p>    <?php _e("PizazzWP is a series of add-ons for Headway and WordPress that will help give your site some pizazz!", 'sp34'); ?></p>
        <?php
          echo '
					<p>The PizazzWP library adds the Floating buttons and the ability to set a Focal Point on images, which is then used by PizazzWP blocks and plugins.</p>
			<!-- Begin MailChimp Signup Form -->
			<div id="mc_embed_signup_pizazz_news">
			<form action="http://pizazzwp.us5.list-manage.com/subscribe/post?u=585497f48b7a32d3561af998a&amp;id=17f540fdc0" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
				<label for="mce-EMAIL">To receive news and information about PizazzWP plugins directly, please subscribe to our mailing list.</label>
				<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
				<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
			</form>
			</div>

			<!--End mc_embed_signup_pizazz_news-->

</div>';
          //			echo '
          //				<div class="pzwp_cell wide dark">
          //				<h2><a href="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=headway-extend#tab-blocks" class="pzwp_head_links">Blocks on Headway Extend</a></h2>
          //				<p>These five plugins are specifically for the Headway 3 theme framework, although SliderPlus will also work with other themes by using shortcodes.</p>
          //			</div>
          //					';
          /** Architect */
          echo '<div class="pzwp_cell wide" style="border:#124a6e solid 2px;background:#eee">';
          echo '<h2 style="">Architect beta available!</h2>';
          echo '<div class="pzwp-info-block"><div class="pzwp-product-icons"><img src="' . PIZAZZ_IMAGE_URL . '/images/pizazzwp-family-thumbs-arc.png"/></div><div class="pzwp-product-info">';
          echo '<p>' . __('Architect is now in beta! If you want to get in on beta testing or have any questions, send us an email at <a href="mailto:beta@pizazzwp.com">beta@pizazzwp.com</a>.', 'pzwp') . '</p>';
          echo '<p>' . __("<strong>Architect is an all-in-one content layout framework.</strong> <br><br>It lets you arrange your content in any way from just the one plugin. Whether that's layout out a magazine-style grid of excerpts, a featured posts slider, displaying testimonials, listing product features, a photo gallery or tabbed posts, Architect will let you build it. And it works with custom posts, custom taxonomies and custom fields. And even  if your site doesn't yet have content, it can automatically display dummy content.", 'pzwp') . '</p>';
          echo '<p>' . __("And one more cool feature... It lets you replace the WordPress Gallery with one of your own design, yet still using the WP Gallery shortcode!", 'pzwp') . '</p>';
          echo '<p>' . __('If you want more info, check out the <a href="http://architect4wp.com" target=_blank>Architect</a> website.', 'pzwp') . '</p>';
          if (class_exists('pz_Architect')) {
            echo '<p class="pzlibs-sw-version"><strong>v' . PZARC_VERSION . ' installed</strong></p>';
            do_action('pizazzwp_updates_arc');
          }
          //    echo '<p><a title="View TabsPlus info page" href="http://pizazzwp.com/tabs/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help2-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View TabsPlus info page</a></p>';
          //    echo '<p><a title="View TabsPlus Online User Guide" href="http://guides.pizazzwp.com/excerptsplus/about-tabsplus/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help1-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View TabsPlus Online User Guide</a></p>';
          echo '</div></div>';
          echo '</div>';
          echo '<div style="clear:both"></div>';

          /** ExcerptsPlus */
          echo '<div class="pzwp_table">';
          echo '<div class="pzwp_cell narrow">';
          echo '<h2>ExcerptsPlus</h2>';
          echo '<div class="pzwp-info-block"><div class="pzwp-product-icons"><img src="' . PIZAZZ_IMAGE_URL . '/images/pizazzwp-family-thumbs-eplus.jpg"/></div><div class="pzwp-product-info">';
          echo '<p>' . __("ExcerptsPlus is the Swiss Army Knife of content display, providing flexible and advanced content display. Adds a block that provides many more excerpt and content display options. Can be used to setup magazine layouts, featured post sliders, and even simple image galleries. In conjunction with custom posts types can create almost anything!", 'pzwp') . '</p>				';
          if (function_exists('ep_activate')) {
            echo '<p class="pzlibs-sw-version"><strong>v' . EPVERSION . ' installed</strong></p>';
            do_action('pizazzwp_updates_excerptsplus');
          }
          echo '<p><a title="View ExcerptsPlus info page" href="http://pizazzwp.com/excerpts/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help2-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View ExcerptsPlus info page</a></p>';
          echo '<p><a title="View ExcerptsPlus Online User Guide" href="http://guides.pizazzwp.com/excerptsplus/about-excerpts/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help1-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View ExcerptsPlus Online User Guide</a></p>';
          echo '</div></div>';
          echo '</div>';

          /** GalleryPlus */
          echo '<div class="pzwp_cell narrow">';
          echo '<h2>GalleryPlus</h2>';
          echo '<div class="pzwp-info-block"><div class="pzwp-product-icons"><img src="' . PIZAZZ_IMAGE_URL . '/images/pizazzwp-family-thumbs-gplus.jpg"/></div><div class="pzwp-product-info">';
          echo '<p>' . __("GalleryPlus lets you create galleries with a photo style, book style, portfolio and more. So, however you use your website, whether home blogger, travel blogger, professional photographer, pro blogger, amateur photographer etc, if you want to display galleries of photos, GalleryPlus provides an easy, powerful and flexible way to do so.", 'pzwp') . '</p>				';
          if (function_exists('gp_activate')) {
            echo '<p class="pzlibs-sw-version"><strong>v' . GP_VERSION . ' installed</strong></p>';
            do_action('pizazzwp_updates_galleryplus');
          }
          echo '<p><a title="View GalleryPlus info page" href="http://pizazzwp.com/gallery/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help2-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View GalleryPlus info page</a></p>';
          echo '<p><a title="View GalleryPlus Online User Guide" href="http://guides.pizazzwp.com/galleryplus/welcome/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help1-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View GalleryPlus Online User Guide</a></p>';
          echo '</div></div>';
          echo '</div>';

          /** Slider Plus  */
          echo '<div class="pzwp_cell narrow">';
          echo '<h2>SliderPlus</h2>';
          echo '<div class="pzwp-info-block"><div class="pzwp-product-icons"><img src="' . PIZAZZ_IMAGE_URL . '/images/pizazzwp-family-thumbs-splus.jpg"/></div><div class="pzwp-product-info">';
          echo '<p>' . __("PizazzWP SliderPlus is a fully featured, full-content slider and is used to display your content in a navigable slideshow. It can display from posts, page, slides and can even display videos. Sliders can be shown in a Headway block or with a standard WordPress shortcode.", 'pzwp') . '</p>';
          if (function_exists('pzsp_activate')) {
            echo '<p class="pzlibs-sw-version"><strong>v' . PZSP_VERSION . ' installed</strong></p>';
            do_action('pizazzwp_updates_sliderplus');
          }
          echo '<p><a title="View SliderPlus info page" href="http://pizazzwp.com/slider/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help2-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View SliderPlus info page</a></p>';
          echo '<p><a title="View SliderPlus Online User Guide" href="http://guides.pizazzwp.com/excerptsplus/about-sliderplus/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help1-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View SliderPlus Online User Guide</a></p>';
          echo '</div></div>';
          echo '</div>';
          echo '<div style="clear:both"></div>';

          /** SWISS ARMY BLOCK */
          echo '<div class="pzwp_cell narrow">';
          echo '<h2>Swiss Army Block</h2>';
          echo '<div class="pzwp-info-block"><div class="pzwp-product-icons"><img src="' . PIZAZZ_IMAGE_URL . '/images/pizazzwp-family-thumbs-sab.jpg"/></div><div class="pzwp-product-info">';
          echo '<p>' . __("Nine blocks in one and growing! Swiss Army Block is a collection of small blocks that each do one simple task in a Headway block form to make your life easier.<br/><br/>Currently, it consists of nine blocks: Author, Related Posts, Titles, HeaderPlus, Documents, Sets (for creating reusable block groups), SearchPlus, Icons and Spacer plus two extensions, Outer Space, and Widows, with more to be added.", 'pzwp') . '</p>
				';
          if (function_exists('sab_activate')) {
            echo '<p class="pzlibs-sw-version"><strong>v' . SABVERSION . ' installed</strong></p>';
            do_action('pizazzwp_updates_sab');
          }
          echo '<p><a title="View Swiss Army Block info page" href="http://pizazzwp.com/swissarmyblock/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help2-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View SwissArmy Block info page</a></p>';
          echo '<p><a title="View Swiss Army Block Online User Guide" href="http://guides.pizazzwp.com/excerptsplus/about-swiss-army-block/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help1-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View Swiss Army Block Online User Guide</a></p>';
          echo '</div></div>';
          echo '</div>';

          /**  TabsPlus Block */
          echo '<div class="pzwp_cell narrow">';
          echo '<h2>TabsPlus</h2>';
          echo '<div class="pzwp-info-block"><div class="pzwp-product-icons"><img src="' . PIZAZZ_IMAGE_URL . '/images/pizazzwp-family-thumbs-tplus.jpg"/></div><div class="pzwp-product-info">';
          echo '<p>' . __("TabsPlus is one of the most useful blocks out of the Headway workshop. It greatly increase the power and flexibility of your page layouts and allow you to shrink your pages to minimize scrolling.", 'pzwp') . '</p>
				';
          if (function_exists('tp_activate')) {
            echo '<p class="pzlibs-sw-version"><strong>v' . TP_VERSION . ' installed</strong></p>';
            do_action('pizazzwp_updates_tabsplus');
          }
          echo '<p><a title="View TabsPlus info page" href="http://pizazzwp.com/tabs/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help2-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View TabsPlus info page</a></p>';
          echo '<p><a title="View TabsPlus Online User Guide" href="http://guides.pizazzwp.com/excerptsplus/about-tabsplus/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help1-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View TabsPlus Online User Guide</a></p>';
          echo '</div></div>';
          echo '</div>';

          /**  Codex */
          echo '<div class="pzwp_cell narrow">';
          echo '<h2>Codex</h2>';
          echo '<div class="pzwp-info-block"><div class="pzwp-product-icons"><img src="' . PIZAZZ_IMAGE_URL . '/images/pizazzwp-family-thumbs-codex.jpg"/></div><div class="pzwp-product-info">';
          echo '<p>' . __("Easily build and display beautiful step-based documentation such as tutorials, guides and manuals, or even feature list, and all with a choice of multiple layout options. Inspired by ScreenSteps http://www.screensteps.com/ and FlatDoc http://ricostacruz.com/flatdoc/.", 'pzwp') . '</p>
				';
          if (class_exists('pzCodex')) {
            echo '<p class="pzlibs-sw-version"><strong>v' . PZCODEX_VERSION . ' installed</strong></p>';
            do_action('pizazzwp_updates_codex');
          }
          //    echo '<p><a title="View TabsPlus info page" href="http://pizazzwp.com/tabs/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help2-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View TabsPlus info page</a></p>';
          //    echo '<p><a title="View TabsPlus Online User Guide" href="http://guides.pizazzwp.com/excerptsplus/about-tabsplus/" target=_blank><img src= "' . PIZAZZ_IMAGE_URL . '/images/icons/help1-65grey.png" width="16" style="vertical-align:top"/>&nbsp;View TabsPlus Online User Guide</a></p>';
          echo '</div></div>';
          echo '</div>';
          echo '<div style="clear:both"></div>';

          echo '
            <script type="text/javascript" src="http://assets.freshdesk.com/widget/freshwidget.js"></script>
            <style type="text/css" media="screen, projection">
              @import url(http://assets.freshdesk.com/widget/freshwidget.css);
            </style>
            <iframe class="freshwidget-embedded-form" id="freshwidget-embedded-form" src="https://pizazzwp.freshdesk.com/widgets/feedback_widget/new?&widgetType=embedded&formTitle=Submit+a+help+request&screenshot=no" scrolling="no" height="850px" width="90%" frameborder="0"  style="margin:20px 10px 10px 40px;background:#eee;overflow-y: auto;">
            </iframe>
            ';
        ?>
      </div>
<?php

//			echo '<div class="pzwp_cell narrow">';
      // echo '<h2>InteractivePro (in development)</h2>';
      // echo '<div class="pzwp-info-block"><div class="pzwp-product-icons"><img src="'.PIZAZZ_IMAGE_URL.'/images/pizazzwp-family-thumbs-ipro.jpg"/></div><div class="pzwp-product-info">';
      // echo '<p>'.__("InteractivePro provides interactive hotspots that pop up content and will be awesome for interactive user guides, infographics, product catalogs or just plain making sites more user friendly. Popups can trigger on mouse over, mouse click, on page load and even by the scroll position on a page.",'pzwp').'</p>';
      // if (function_exists('int_activate')) {
      // 	echo '<p><strong>v'.INTVERSION.' installed</strong></p>';
      // 	do_action('pizazzwp_updates_interactivepro');
      // }
      // echo '<p><a title="View InteractivePro info page" href="http://pizazzwp.com/interactive-pro/" target=_blank>View InteractivePro info page</a></p>';
      // echo '</div></div>';
//			echo '</div>';
//			echo '<div class="pzwp_cell narrow"></div>';
////			echo '<div style="clear:both"></div>';
//			echo '
//				<div class="pzwp_cell wide dark">
//			<h2><a href="http://pizazzwp.com" target=_blank class="pzwp_head_links">Blocks and plugins on PizazzWP</a></h2>
//			<p>These two plugins and blocks are only available at pizazzwp.com and supported via support.pizazzwp.com
//			</div>';
//			echo '</div>';
//			echo '<div class="pzwp_cell narrow">';
//			echo '<h2>InteractivePro (in development)</h2>';
//			echo '<div class="pzwp-info-block"><div class="pzwp-product-icons"><img src="'.PIZAZZ_IMAGE_URL.'/images/pizazzwp-family-thumbs-ipro.jpg"/></div><div class="pzwp-product-info">';
//			echo '<p>'.__("InteractivePro provides interactive hotspots that pop up content and will be awesome for interactive user guides, infographics, product catalogs or just plain making sites more user friendly. Popups can trigger on mouse over, mouse click, on page load and even by the scroll position on a page.",'pzwp').'</p>';
//			if (function_exists('int_activate')) {
//				echo '<p><strong>v'.INTVERSION.' installed</strong></p>';
//				do_action('pizazzwp_updates_interactivepro');
//			}
//			echo '<p><a title="View InteractivePro info page" href="http://pizazzwp.com/interactive-pro/" target=_blank>View InteractivePro info page</a></p>';
//			echo '</div></div>';
//			echo '</div>';
//			echo '<div class="pzwp_cell narrow"></div>';
      echo '<div style="clear:both"></div>';
      if (!class_exists('HeadwayBlockAPI')) {
        echo '<div class="pzwp_cell wide">';
        echo '<h2>Headway Themes Framework for WordPress</h2>';
        echo '<div class="pzwp-info-block"><div class="pzwp-product-headway"><a target="_blank" href="http://zfer.us/byuBg"><img src="' . PIZAZZ_PLUGIN_URL . '/images/Headway-125x200_wb.png" alt="Complete Theme Control For Beginners To Experts With No Need For Code — Headway For WordPress" border="0"></a></div><div class="pzwp-product-info">';
        echo '<p><a href="http://www.shareasale.com/r.cfm?b=233381&u=439265&m=27477&urllink=&afftrack=" target=_blank>Headway Themes Framework for WordPress</a>' . __("(affiliate link) is a stunning theme generator with ueasy to use Visual Editor that lets you build a wireframe of your pages in its Grid Mode and then paint the design on them in the Design Mode.<br/> Great for all users to greatly speed up development of your site! However, power users aren't forgotten either, with Headway providing custom CSS and full support of child theming. Plus Headway is widely recognized as having superb SEO.", 'pzwp') . '</p>';
        echo '</div></div>';
        echo '</div>';
      }
      echo '</div><!-- end table -->';
//		echo '<p> You are running PizazzWP library version: ' . PIZAZZ_VERSION . '</p>';
      echo '</div>';
      echo '<div style="clear:both"></div>';
    }

    function pizazz_news()
    {
      global $title;
      ?>

      <div class="wrap">
        <!-- Display Plugin Icon, Header, and Description -->
        <div class="icon32" id="icon-tools"><br></div>
        <h2><?php echo $title ?></h2>

        <h2>Pizazz News</h2>

        <p><?php _e('All the latest news direct from the Pizazz blog and twitter feed.', 'pzwp'); ?></p>
        <?php
          $rss = fetch_feed('http://pizazzwp.com/feed');
          if (!is_wp_error($rss)) : // Checks that the object is created correctly
            // Figure out how many total items there are, but limit it to 5.
          {
            $maxitems = $rss->get_item_quantity(5);

            // Build an array of all the items, starting with element 0 (first element).
            $rss_items = $rss->get_items(0, $maxitems);
          }
          endif;
        ?>

        <div class="postbox pzwp_blog" style="width:68%;float:left;">
          <h3 class="handle" style="line-height:30px;padding-left:10px;">Recent Pizazz News</h3>
          <ul class="inside">
            <?php
              if ($maxitems == 0) {
                echo '<li>No items.</li>';
              } else // Loop through each feed item and display each item as a hyperlink.
              {
                foreach ($rss_items as $item) :
                  ?>
                  <li>
                    <h4 style="font-size:15px;"><a href='<?php echo esc_url($item->get_permalink()); ?>'
                                                   title='<?php echo esc_html($item->get_title()); ?>'
                                                   target=_blank>
                        <?php echo esc_html($item->get_title()); ?></a></h4>

                    <p style="line-height:0;font-style:italic"><?php echo $item->get_date('j F Y'); ?></p>

                    <p><?php echo $item->get_description(); ?><a
                          href="<?php echo esc_url($item->get_permalink()); ?>" target=_blank>
                        Continue reading</a></p>
                  </li>
                <?php endforeach;
              } ?>
          </ul>
        </div>

        <div class="postbox pzwp_twitter" style="width:30%;float:right;">
          <h3 class="handle" style="line-height:30px;padding-left:10px;">Pizazz on Twitter</h3>
          <ul class="inside">
            <?php pzwp_display_latest_tweets('pizazzwp') ?>
          </ul>
        </div>


      </div> <?php
    }

    // Init plugin options to white list our options
    // function pzwp_init(){
    // register_setting( 'pzwp_opts', 'pzwp_sliders');
    // }

    function pizazz_floats()
    {
      global $title;
      ?>

      <div class="wrap">
      <!-- Display Plugin Icon, Header, and Description -->
      <div class="icon32" id="icon-tools"><br></div>
      <h2><?php echo $title ?></h2>

      <p><?php _e('Here you can create up to three floating buttons that lock to the side of the user\'s browser. Commonly used for Feedback buttons, Follow buttons and special announcements. (Limited to three because you don\'t want to overwhelm your readers with buttons.)', 'pzwp'); ?></p>

      <?php
        // 3x text, color, location, type (fixed/scrolling), WP page (popup with jQuery dialog),position from top, width, height
        $opt_name = array();
        // Read in existing option value from database
        $defaults =
            array(
                'val_label'         => '',
                'val_colour'        => '#ffffff',
                'val_bgcolour'      => '#0044aa',
                'val_corner_radius' => '3',
                'val_loc'           => 'right',
                'val_type'          => 'fixed',
                'val_page'          => 'none',
                'val_url'           => '',
                'val_contentcss'    => '.entry-content',
                'val_offset'        => '0',
                'val_width'         => '150',
                'val_fontsize'      => '18',
                'val_enabled'       => '',
                'val_preview'       => 'preview',
            );
        $opt_val  = get_option('pzwp_opts'
        );
        for ($i = 0; $i < 3; $i++) {
          if (!$opt_val[ $i ]) {
            $opt_val[ $i ]                   = $defaults;
            $opt_val[ $i ][ 'val_label' ]    = 'Button ' . ($i + 1);
            $opt_val[ $i ][ 'val_offset' ]   = (170 * $i);
            $opt_val[ $i ][ 'val_bgcolour' ] = '#' . (112233 * ($i + 1));
          }
        }
        update_option('pzwp_opts', $opt_val);
        for ($i = 0; $i < 3; $i++) {
          $opt_name[ $i ]    = array(
              'name_label'         => 'pzwp_label_' . $i,
              'name_colour'        => 'pzwp_colour_' . $i,
              'name_bgcolour'      => 'pzwp_bgcolour_' . $i,
              'name_corner_radius' => 'pzwp_corner_radius_' . $i,
              'name_loc'           => 'pzwp_loc_' . $i,
              'name_type'          => 'pzwp_type_' . $i,
              'name_page'          => 'pzwp_page_' . $i,
              'name_url'           => 'pzwp_url_' . $i,
              'name_contentcss'    => 'pzwp_contentcss_' . $i,
              'name_offset'        => 'pzwp_offset_' . $i,
              'name_width'         => 'pzwp_width_' . $i,
              'name_fontsize'      => 'pzwp_fontsize_' . $i,
              'name_enabled'       => 'pzwp_enabled_' . $i,
              'name_preview'       => 'pzwp_preview_' . $i
          );
          $hidden_field_name = 'pzwp_submit_hidden';
          if (isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y') {
            // Read their posted value
            $opt_val[ $i ] = array(
                'val_label'         => $_POST[ $opt_name[ $i ][ 'name_label' ] ],
                'val_colour'        => $_POST[ $opt_name[ $i ][ 'name_colour' ] ],
                'val_bgcolour'      => $_POST[ $opt_name[ $i ][ 'name_bgcolour' ] ],
                'val_corner_radius' => $_POST[ $opt_name[ $i ][ 'name_corner_radius' ] ],
                'val_loc'           => $_POST[ $opt_name[ $i ][ 'name_loc' ] ],
                'val_type'          => $_POST[ $opt_name[ $i ][ 'name_type' ] ],
                'val_page'          => $_POST[ $opt_name[ $i ][ 'name_page' ] ],
                'val_url'           => $_POST[ $opt_name[ $i ][ 'name_url' ] ],
                'val_contentcss'    => $_POST[ $opt_name[ $i ][ 'name_contentcss' ] ],
                'val_offset'        => $_POST[ $opt_name[ $i ][ 'name_offset' ] ],
                'val_width'         => $_POST[ $opt_name[ $i ][ 'name_width' ] ],
                'val_fontsize'      => $_POST[ $opt_name[ $i ][ 'name_fontsize' ] ],
                'val_enabled'       => $_POST[ $opt_name[ $i ][ 'name_enabled' ] ],
                'val_preview'       => $_POST[ $opt_name[ $i ][ 'name_preview' ] ],
            );
            // Save the posted values in the database
            update_option('pzwp_opts', $opt_val);
            if ($i == 0) {
              echo '<div id="message" class="updated fade" style="padding:10px;">Button settings saved.</div>';
            }
          }
        }
        //		pzwp_add_floaties();
        $page_list       = get_pages();
        $pages[ 'none' ] = 'None';
        $pages[ 'url' ]  = 'URL';
        foreach ($page_list as $page) {
          $pages[ $page->post_name ] = $page->post_title;
        }
      ?>
      <form method="post" action="admin.php?page=pizazz-floats">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

        <div id="pzwp-floaters-form">
          <ul>
            <li><a href="#pzwp-fieldset-0">Button One</a></li>
            <li><a href="#pzwp-fieldset-1">Button Two</a></li>
            <li><a href="#pzwp-fieldset-2">Button Three</a></li>
          </ul>

          <?php
            for ($i = 0; $i < 3; $i++) {
              ?>
              <fieldset id="pzwp-fieldset-<?php echo $i; ?>" class="pzwp-float-fields">
                <h3>&nbsp;Button <?php echo substr('One  Two  Three', ($i * 5), 5); ?></h3>
                <table class="form-table">
                  <tr>
                    <th scope="row"><?php _e("Button Label", 'pzwp') ?><span class="pzwp-floater-tooltip"
                                                                             title="<?php _e("This is the label that will display on the button", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <input type="text" size="20" name="<?php echo $opt_name[ $i ][ 'name_label' ]; ?>"
                             value="<?php echo $opt_val[ $i ][ 'val_label' ]; ?>"/>
                    </td>
                    <th scope="row"><?php _e("CSS content element", 'pzwp') ?><span
                          class="pzwp-floater-tooltip"
                          title="<?php _e("Many themes use '.entry_content' as the element that contains the main content on the page. If your theme is different you will need to enter it's content css element name here. Precede it with a dot if it's a class and a hash if it's an id.", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <input type="text" size="20"
                             name="<?php echo $opt_name[ $i ][ 'name_contentcss' ]; ?>"
                             value="<?php echo $opt_val[ $i ][ 'val_contentcss' ]; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><?php _e("Page to show", 'pzwp') ?><span class="pzwp-floater-tooltip"
                                                                             title="<?php _e("Select the page you want to display in the popup window when the user clicks this button, or None for no action, or URL to goto a URL.", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <select name="<?php echo $opt_name[ $i ][ 'name_page' ]; ?>">
                        <?php
                          foreach ($pages as $page_id => $page_name) {
                            echo '<option value="' . $page_id . '"' . (($opt_val[ $i ][ 'val_page' ] == $page_id) ? 'selected' : null) . '>' . $page_name . '</option>';
                          }
                        ?>
                      </select>
                    </td>
                    <th scope="row"><?php _e("Full URL to open", 'pzwp') ?><span
                          class="pzwp-floater-tooltip"
                          title="<?php _e("If you'd rather link the button to a URL, enter it here. This will open the page. Make sure URL is selected for the 'Page to show'.", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <input type="text" size="20" name="<?php echo $opt_name[ $i ][ 'name_url' ]; ?>"
                             value="<?php echo $opt_val[ $i ][ 'val_url' ]; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><?php _e("Type", 'pzwp') ?><span class="pzwp-floater-tooltip"
                                                                     title="<?php _e("The buttons can be either fixed on what spot in the browser window, or scroll with the content.", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <input type="radio" name="<?php echo $opt_name[ $i ][ 'name_type' ]; ?>"
                             value="fixed" <?php echo(($opt_val[ $i ][ 'val_type' ] == 'fixed') ? 'checked=\"yes\"' : null); ?>/>&nbsp;Fixed&nbsp;&nbsp;&nbsp;
                      <input type="radio" name="<?php echo $opt_name[ $i ][ 'name_type' ]; ?>"
                             value="absolute" <?php echo(($opt_val[ $i ][ 'val_type' ] == 'absolute') ? 'checked=\"yes\"' : null); ?>/>&nbsp;Scrolling
                    </td>
                    <th scope="row"><?php _e("Location", 'pzwp') ?><span class="pzwp-floater-tooltip"
                                                                         title="<?php _e("Choose the location you want this button to appear.", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <select name="<?php echo $opt_name[ $i ][ 'name_loc' ]; ?>">
                        <option
                            value="left" <?php echo(($opt_val[ $i ][ 'val_loc' ] == 'left') ? 'selected' : null); ?>>
                          Left side
                        </option>
                        <option
                            value="lefttop" <?php echo(($opt_val[ $i ][ 'val_loc' ] == 'lefttop') ? 'selected' : null); ?>>
                          Top left (diagonal)
                        </option>
                        <option value="top" <?php echo(($opt_val[ $i ][ 'val_loc' ] == 'top') ? 'selected' : null); ?>>
                          Top edge
                        </option>
                        <option
                            value="righttop" <?php echo(($opt_val[ $i ][ 'val_loc' ] == 'righttop') ? 'selected' : null); ?>>
                          Top right (diagonal)
                        </option>
                        <option
                            value="right" <?php echo(($opt_val[ $i ][ 'val_loc' ] == 'right') ? 'selected' : null); ?>>
                          Right side
                        </option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><?php _e("Offset", 'pzwp') ?><span class="pzwp-floater-tooltip"
                                                                       title="<?php _e("Set the distance from the top (for location left and right) or the sides when location is top. Note: When location is top, setting a negative number will offset from the right of the browser window. This setting has no effect if either of the diagonal corner locations are selected.", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <input type="text" size="5" name="<?php echo $opt_name[ $i ][ 'name_offset' ]; ?>"
                             value="<?php echo $opt_val[ $i ][ 'val_offset' ]; ?>"/>px
                    </td>
                    <th scope="row"><?php _e("Width", 'pzwp') ?><span class="pzwp-floater-tooltip"
                                                                      title="<?php _e("Choose a width for your button", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <input type="text" size="5" name="<?php echo $opt_name[ $i ][ 'name_width' ]; ?>"
                             value="<?php echo $opt_val[ $i ][ 'val_width' ]; ?>"/>px
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><?php _e("Background Colour", 'pzwp') ?><span
                          class="pzwp-floater-tooltip"
                          title="<?php _e("Set a background colour for your button", 'pzwp') ?>">?</span>
                    </th>
                    <td style="position:relative;">
                      <input type="text" size="20" id="<?php echo $opt_name[ $i ][ 'name_bgcolour' ]; ?>"
                             name="<?php echo $opt_name[ $i ][ 'name_bgcolour' ]; ?>"
                             value="<?php echo $opt_val[ $i ][ 'val_bgcolour' ]; ?>"/>
                    </td>
                    <th scope="row"><?php _e("Corner radius", 'pzwp') ?><span class="pzwp-floater-tooltip"
                                                                              title="<?php _e("Choose the corner radius if you want rounded corners on the button", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <input type="number" min="0" max="10"
                             name="<?php echo $opt_name[ $i ][ 'name_corner_radius' ]; ?>"
                             value="<?php echo $opt_val[ $i ][ 'val_corner_radius' ]; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><?php _e("Font Size", 'pzwp') ?><span class="pzwp-floater-tooltip"
                                                                          title="<?php _e("Select a font size for the label", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <input type="number" min="10" max="50"
                             name="<?php echo $opt_name[ $i ][ 'name_fontsize' ]; ?>"
                             value="<?php echo $opt_val[ $i ][ 'val_fontsize' ]; ?>"/>px
                    </td>
                    <th scope="row"><?php _e("Label and border colour", 'pzwp') ?><span
                          class="pzwp-floater-tooltip"
                          title="<?php _e("Choose a colour for the label and border", 'pzwp') ?>">?</span>
                    </th>
                    <td style="position:relative;">
                      <input type="text" size="20" id="<?php echo $opt_name[ $i ][ 'name_colour' ]; ?>"
                             name="<?php echo $opt_name[ $i ][ 'name_colour' ]; ?>"
                             value="<?php echo $opt_val[ $i ][ 'val_colour' ]; ?>"/>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><?php _e("Enable on site", 'pzwp') ?><span class="pzwp-floater-tooltip"
                                                                               title="<?php _e("Select this to display this button on your site", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <input type="checkbox" name="<?php echo $opt_name[ $i ][ 'name_enabled' ]; ?>"
                             value="live" <?php echo(($opt_val[ $i ][ 'val_enabled' ] == 'live') ? 'checked=\"yes\"' : null); ?>/>&nbsp;Live
                    </td>
                    <th scope="row"><?php _e("Show preview in admin", 'pzwp') ?><span
                          class="pzwp-floater-tooltip"
                          title="<?php _e("Select this to preview your button on this admin page. Note: This does not preview the popup.", 'pzwp') ?>">?</span>
                    </th>
                    <td>
                      <input type="checkbox" name="<?php echo $opt_name[ $i ][ 'name_preview' ]; ?>"
                             value="preview" <?php echo(($opt_val[ $i ][ 'val_preview' ] == 'preview') ? 'checked=\"yes\"' : null); ?>/>&nbsp;Preview
                    </td>
                  </tr>
                </table>
              </fieldset>

            <?php } ?>
        </div>
        <p class="submit">
          <input type="submit" class="button-primary" name="save-changes"
                 value="<?php _e('Save Changes', 'pzwp') ?>"/>
        </p>
      </form>

      </div>


      <!-- // 3x text, color, location, type (fixed/scrolling), WP page (popup with jQuery dialog),position from top, width, height -->

      <!-- </div> -->
      <div style="clear:both"></div>
    <?php
    }

    function pizazz_tools()
    {
      global $title;
      ?>

      <div class="wrap">
        <!-- Display Plugin Icon, Header, and Description -->
        <div class="icon32" id="icon-tools"><br></div>
        <h2><?php echo $title ?></h2>

        <h2>Pizazz Tools</h2>

        <p><?php _e('On this page are tools installed with other plugins in the Pizazz family. If you don\'t have any others installed, you won\'t see anything here.', 'pzwp'); ?></p>


        <?php
          if (function_exists('gp_admin_options_page')) {
            echo '<h2>GalleryPlus Tools</h2>';
            gp_admin_options_page();
            if (isset($_POST[ 'emptycache' ])) {
              gp_clear_image_cache();
              echo '<div id="message" class="updated"><p>GalleryPlus Image Cache cleared. It will be recreated next time someone vists your site.</p></div>';
            }
          }

          if (function_exists('pzsp_admin_options_page')) {
            echo '<h2>SliderPlus Tools</h2>';
            pzsp_admin_options_page();
            if (isset($_POST[ 'emptyspcache' ])) {
              pzsp_clear_post_cache();
              echo '<div id="message" class="updated"><p>SliderPlus cache cleared. It will be recreated next time someone vists your site.</p></div>';
            }
          }

          if (function_exists('ep_admin_options_page')) {
            echo '<h2>ExcerptsPlus Tools</h2>';
            ep_admin_options_page();
            if (isset($_POST[ 'emptyepcache' ])) {
              ep_clear_image_cache();
              echo '<div id="message" class="updated"><p>ExcerptsPlus Image Cache cleared. It will be recreated next time someone vists your site.</p></div>';
            }
          }

          if (function_exists('tp_admin_options_page')) {
            echo '<h2>TabsPlus Tools</h2>';
            tp_admin_options_page();
            if (isset($_POST[ 'emptytpcache' ])) {
              tp_clear_image_cache();
              echo '<div id="message" class="updated"><p>TabsPlus Image Cache cleared. It will be recreated next time someone vists your site.</p></div>';
            }
          }
        ?></div> <?php
      //delete_option('pzwp_opts');
    }

    function pizazz_options()
    {
      global $title;
      $opt_name = array();
      // Read in existing option value from database
      $defaults =
          array(
              'val_show_thumbs'               => 'showthumbs',
              'val_hide_dash_message'         => false,
              'val_hide_debug_message'        => false,
              'val_hide_pzwp_news'            => false,
              'val_hide_pzwp_floaties'        => false,
              'val_hide_pzwp_documents'       => false,
              'val_show_pzwp_snippets'        => false,
              'val_hide_pzwp_photographer'    => false,
              'val_hide_pzwp_focal_point'     => false,
              'val_hide_pzwp_destination_url' => false,
              'val_cache_path'                => 'need to work this out based on wp site path',
              'val_cache_url'                 => 'need to work this out based on wp site url',
              'val_update_method'             => false,
          );

      $opt_val = get_option('pizazz_options');
      if (!$opt_val) {
        $opt_val = $defaults;
      }
      //$opt_val = $defaults;
      update_option('pizazz_options', $opt_val);
      $opt_name          = array(
          'name_show_thumbs'               => 'pzwp_show_thumbs',
          'name_hide_dash_message'         => 'pzwp_hide_dash_message',
          'name_hide_debug_message'        => 'pzwp_hide_debug_message',
          'name_hide_pzwp_news'            => 'pzwp_hide_news',
          'name_hide_pzwp_floaties'        => 'pzwp_hide_floaties',
          'name_hide_pzwp_documents'       => 'pzwp_hide_documents',
          'name_show_pzwp_snippets'        => 'pzwp_show_snippets',
          'name_hide_pzwp_photographer'    => 'pzwp_hide_photographer',
          'name_hide_pzwp_focal_point'     => 'pzwp_hide_focal_point',
          'name_hide_pzwp_destination_url' => 'pzwp_hide_destination_url',
          'name_cache_path'                => 'pzwp_cache_path',
          'name_cache_url'                 => 'pzwp_cache_url',
          'name_update_method'             => 'pzwp_update_method'
      );
      $hidden_field_name = 'pzwp_options_submit_hidden';
      if (isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y') {
        // Read their posted value

        $opt_val = array(
            'val_show_thumbs'               => (isset($_POST[ $opt_name[ 'name_show_thumbs' ] ]) ? $_POST[ $opt_name[ 'name_show_thumbs' ] ] : false),
            'val_hide_dash_message'         => (isset($_POST[ $opt_name[ 'name_hide_dash_message' ] ]) ? $_POST[ $opt_name[ 'name_hide_dash_message' ] ] : false),
            'val_hide_debug_message'        => (isset($_POST[ $opt_name[ 'name_hide_debug_message' ] ]) ? $_POST[ $opt_name[ 'name_hide_debug_message' ] ] : false),
            'val_hide_pzwp_news'            => (isset($_POST[ $opt_name[ 'name_hide_pzwp_news' ] ]) ? $_POST[ $opt_name[ 'name_hide_pzwp_news' ] ] : false),
            'val_hide_pzwp_floaties'        => (isset($_POST[ $opt_name[ 'name_hide_pzwp_floaties' ] ]) ? $_POST[ $opt_name[ 'name_hide_pzwp_floaties' ] ] : false),
            'val_hide_pzwp_documents'       => (isset($_POST[ $opt_name[ 'name_hide_pzwp_documents' ] ]) ? $_POST[ $opt_name[ 'name_hide_pzwp_documents' ] ] : false),
            'val_show_pzwp_snippets'        => (isset($_POST[ $opt_name[ 'name_show_pzwp_snippets' ] ]) ? $_POST[ $opt_name[ 'name_show_pzwp_snippets' ] ] : false),
            'val_hide_pzwp_photographer'    => (isset($_POST[ $opt_name[ 'name_hide_pzwp_photographer' ] ]) ? $_POST[ $opt_name[ 'name_hide_pzwp_photographer' ] ] : false),
            'val_hide_pzwp_focal_point'     => (isset($_POST[ $opt_name[ 'name_hide_pzwp_focal_point' ] ]) ? $_POST[ $opt_name[ 'name_hide_pzwp_focal_point' ] ] : false),
            'val_hide_pzwp_destination_url' => (isset($_POST[ $opt_name[ 'name_hide_pzwp_destination_url' ] ]) ? $_POST[ $opt_name[ 'name_hide_pzwp_destination_url' ] ] : false),
            'val_cache_path'                => (isset($_POST[ $opt_name[ 'name_cache_path' ] ]) ? $_POST[ $opt_name[ 'name_cache_path' ] ] : false),
            'val_cache_url'                 => (isset($_POST[ $opt_name[ 'name_cache_url' ] ]) ? $_POST[ $opt_name[ 'name_cache_url' ] ] : false),
            'val_update_method'             => (isset($_POST[ $opt_name[ 'name_update_method' ] ]) ? $_POST[ $opt_name[ 'name_update_method' ] ] : false),
        );
        // Save the posted values in the database
        update_option('pizazz_options', $opt_val);
        echo '<div id="message" class="updated fade" style="padding:10px;">PizazzWP options saved.</div>';
      }
      ?>
      <div class="wrap">
        <!-- Display Plugin Icon, Header, and Description -->
        <div class="icon32" id="icon-tools"><br></div>
        <h2><?php echo $title ?></h2>

        <p><?php _e('On this page you can set various options for the PizazzWP.', 'pzwp'); ?></p>

        <form method="post" action="admin.php?page=pizazz-options">
          <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

          <div id="pzwp-options">
            <h3>General settings</h3>
            <table class="form-table">
              <tr>
                <th scope="row"><?php _e("Show thumbnails in posts listings", 'pzwp') ?>
                  <span class="pzwp-options-tooltip"
                        title="<?php _e("Display thumbnails in post or page listing screens", 'pzwp') ?>">?</span>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo $opt_name[ 'name_show_thumbs' ]; ?>"
                         value="showthumbs" <?php echo ($opt_val[ 'val_show_thumbs' ] == 'showthumbs') ? 'checked' : null; ?> />
                </td>
              </tr>
              <tr>
                <th scope="row"><?php _e("Hide update message on dashboard", 'pzwp') ?>
                  <span class="pzwp-options-tooltip"
                        title="<?php _e("Hide the message on the dashboard about manual updating", 'pzwp') ?>">?</span>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo $opt_name[ 'name_hide_dash_message' ]; ?>"
                         value="hidedashmsg" <?php echo ($opt_val[ 'val_hide_dash_message' ] == 'hidedashmsg') ? 'checked' : null; ?> />
                </td>
              </tr>
              <tr>
                <th scope="row"><?php _e("Hide Debug mode message", 'pzwp') ?>
                  <span class="pzwp-options-tooltip"
                        title="<?php _e("Hide the message on the dashboard and PizazzWP menu that warns when WP Debug mode is active.", 'pzwp') ?>">?</span>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo $opt_name[ 'name_hide_debug_message' ]; ?>"
                         value="hidedebugmsg" <?php echo ($opt_val[ 'val_hide_debug_message' ] == 'hidedebugmsg') ? 'checked' : null; ?> />
                </td>
              </tr>
              <tr>
                <th scope="row"><?php _e("Hide PizazzWP News menu", 'pzwp') ?>
                  <span class="pzwp-options-tooltip"
                        title="<?php _e("Hide the PizazzWP menu page that shows the latest PizazzWP news.", 'pzwp') ?>">?</span>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo $opt_name[ 'name_hide_pzwp_news' ]; ?>"
                         value="hidepzwpnews" <?php echo ($opt_val[ 'val_hide_pzwp_news' ] == 'hidepzwpnews') ? 'checked' : null; ?> />
                </td>
              </tr>
              <tr>
                <th scope="row"><?php _e("Hide PizazzWP Floating Buttons menu", 'pzwp') ?>
                  <span class="pzwp-options-tooltip"
                        title="<?php _e("Hide the PizazzWP menu page that lets you setup the Floating Buttons.", 'pzwp') ?>">?</span>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo $opt_name[ 'name_hide_pzwp_floaties' ]; ?>"
                         value="hidepzwpfloaties" <?php echo ($opt_val[ 'val_hide_pzwp_floaties' ] == 'hidepzwpfloaties') ? 'checked' : null; ?> />
                </td>
              </tr>
              <tr>
                <th scope="row"><?php _e("Show DocLists content type", 'pzwp') ?>
                  <span class="pzwp-options-tooltip"
                        title="<?php _e("Show the menu item for entering DocLists content types as used by the Swiss Army Block Document Lists block.", 'pzwp') ?>">?</span>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo $opt_name[ 'name_hide_pzwp_documents' ]; ?>"
                         value="showdocuments" <?php echo ($opt_val[ 'val_hide_pzwp_documents' ] == 'showdocuments') ? 'checked' : null; ?> />
                </td>
              </tr>
              <tr>
                <th scope="row"><?php _e("Show Snippets content type", 'pzwp') ?>
                  <span class="pzwp-options-tooltip"
                        title="<?php _e("Snippets are simply another post type you can use for any content you like and with plugins like ExcerptsPlus, SliderPlus, and Architect, that support custom post types. They were created with small snippets of content in mind, like features and testimonials.", 'pzwp') ?>">?</span>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo $opt_name[ 'name_show_pzwp_snippets' ]; ?>"
                         value="showsnippets" <?php echo ($opt_val[ 'val_show_pzwp_snippets' ] == 'showsnippets') ? 'checked' : null; ?> />
                </td>
              </tr>
              <tr>
                <th scope="row"><?php _e("Use PizazzWP updates server", 'pzwp') ?>
                  <span class="pzwp-options-tooltip"
                        title="<?php _e("Enable this setting to use the PizazzWP server to check for updates instead of the Headway server. If you aren't using the Headway theme, it will automatically use PizazzWP server.", 'pzwp') ?>">?</span>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo $opt_name[ 'name_update_method' ]; ?>"
                         value="usewpupdates" <?php echo ($opt_val[ 'val_update_method' ] == 'usewpupdates') ? 'checked' : null; ?> />
                </td>
              </tr>
            </table>
            <p class="submit">
              <input type="submit" class="button-primary" name="save-changes"
                     value="<?php _e('Save Changes', 'pzwp') ?>"/>
            </p>
          </div>
        </form>
        <?php
        ?></div> <?php
      //delete_option('pzwp_opts');
    }

    /*
     * Functions
     */

    function pzwp_add_floaties()
    {
      // These are the Floaties options, not the pizazz ones.
      $opt_val = get_option('pzwp_opts');

      if (!$opt_val) {
        return;
      }

      // Not even sure the dialog popup works!!

      if (($opt_val[ 0 ][ 'val_page' ] != 'none' && $opt_val[ 0 ][ 'val_page' ] != 'url' && $opt_val[ 0 ][ 'val_enabled' ]) ||
          ($opt_val[ 1 ][ 'val_page' ] != 'none' && $opt_val[ 1 ][ 'val_page' ] != 'url' && $opt_val[ 1 ][ 'val_enabled' ]) ||
          ($opt_val[ 2 ][ 'val_page' ] != 'none' && $opt_val[ 2 ][ 'val_page' ] != 'url' && $opt_val[ 2 ][ 'val_enabled' ])
      ) {
        wp_enqueue_style('jquery-ui-custom-css', PIZAZZ_PLUGIN_URL . '/css/jquery-ui.custom.css');
        if (!wp_script_is('jquery-ui-dialog')) {
          wp_enqueue_script('jquery-ui-dialog', false, array('jquery'));
        }
      }
      $page_list = get_pages();
      for ($i = 0; $i < 3; $i++) {
        $pagename[ $i ] = 'No page selected';
        $rotation[ $i ] = '';
        // $pagename[0]= 'None';
        // $pagename[9999999]= 'URL';
        if ($opt_val[ $i ][ 'val_page' ] != 'None' && $opt_val[ $i ][ 'val_page' ] != 'URL') {
          foreach ($page_list as $page) {

            if ($opt_val[ $i ][ 'val_page' ] == $page->post_name) {
              $pagename[ $i ] = $page->post_title;
              break;
            }
          }
        }
        $nudge_fudge = $opt_val[ $i ][ 'val_width' ] / 2 - 8;
        $height      = $opt_val[ $i ][ 'val_fontsize' ] * 1.5;
        $width       = $opt_val[ $i ][ 'val_width' ];
        $nudge[ $i ] = '';
        switch ($opt_val[ $i ][ 'val_loc' ]) {
          case 'left' :
            $nudge[ $i ] = 'left:' . (-$nudge_fudge - 3) . 'px;';
            $nudge[ $i ] .= 'top:' . ($nudge_fudge + $opt_val[ $i ][ 'val_offset' ]) . 'px;';
            $rotation[ $i ] = 'rotate-90';
            break;
          case 'lefttop' :
            $calcl = -($width / pi() * sin(deg2rad(45)) + $height / 2);
            $calcr = ($width / 2 - $width / pi() * sin(deg2rad(45)) - $height);
            // $calcl= -(($width-sin(deg2rad(45)+atan($height/$width))*sqrt($height^2+$width^2))/2);
            //				$calcr = (sin(deg2rad(45)+atan($height/$width))*sqrt($height^2+$width^2)-$height)/2;
            $nudge[ $i ] = 'left:' . $calcl . 'px;';
            $nudge[ $i ] .= 'top:' . $calcr . 'px;';
            $rotation[ $i ] = 'rotate-45';
            break;
          case 'top' :
            if ($opt_val[ $i ][ 'val_offset' ] >= 0) {
              $nudge[ $i ] = 'left:' . $opt_val[ $i ][ 'val_offset' ] . 'px;';
            } else {
              $nudge[ $i ] = 'right:' . (-$opt_val[ $i ][ 'val_offset' ]) . 'px;';
            }
            $nudge[ $i ] .= 'top:-3px;';
            $rotation[ $i ] = 'rotate-0';
            break;
          case 'righttop' :
            $nudge[ $i ] = 'right:' . (-($width / pi() * sin(deg2rad(45)) + $height / 2)) . 'px;';
            $nudge[ $i ] .= 'top:' . ($width / 2 - $width / pi() * sin(deg2rad(45)) - $height) . 'px;';
            $rotation[ $i ] = 'rotate45';
            break;
          case 'right' :
            $nudge[ $i ] = 'right:' . (-$nudge_fudge - 3) . 'px;';
            $nudge[ $i ] .= 'top:' . ($nudge_fudge + $opt_val[ $i ][ 'val_offset' ]) . 'px;';
            $rotation[ $i ] = 'rotate90';
            break;
        }
        $style[ $i ] = 'style="
							background-color:' . $opt_val[ $i ][ 'val_bgcolour' ] . ';
							color:' . $opt_val[ $i ][ 'val_colour' ] . ';
							position:' . $opt_val[ $i ][ 'val_type' ] . ';
							border-color:' . $opt_val[ $i ][ 'val_colour' ] . ';
							border-radius:' . $opt_val[ $i ][ 'val_corner_radius' ] . 'px;
							width:' . $opt_val[ $i ][ 'val_width' ] . 'px;
							font-size:' . $opt_val[ $i ][ 'val_fontsize' ] . 'px;
							' . $nudge[ $i ] . '
						"';

        switch ($opt_val[ $i ][ 'val_page' ]) {
          case 'none':
            $page_url = '<span style="color:' . $opt_val[ $i ][ 'val_colour' ] . '" >' . $opt_val[ $i ][ 'val_label' ] . '</span>';
            break;
          case 'url':
            $page_url = '<a href="' . $opt_val[ $i ][ 'val_url' ] . '" style="color:' . $opt_val[ $i ][ 'val_colour' ] . '" target=_blank>' . $opt_val[ $i ][ 'val_label' ] . '</a>';
            break;
          default:
            $page_url = '<a href="' . get_bloginfo('url') . '/' . $opt_val[ $i ][ 'val_page' ] . '" style="color:' . $opt_val[ $i ][ 'val_colour' ] . '" title="' . $pagename[ $i ] . '" >' . $opt_val[ $i ][ 'val_label' ] . '</a>';
            break;
        }
        if ($opt_val[ $i ][ 'val_enabled' ] === 'live' && $opt_val[ $i ][ 'val_label' ] && !is_admin()) {
          echo '<div class="pzwp-floater floater-' . $i . ' ' . $rotation[ $i ] . '" ' . $style[ $i ] . '>' . $page_url . '</div>';
        }
        if ($opt_val[ $i ][ 'val_preview' ] === 'preview' && $opt_val[ $i ][ 'val_label' ] && is_admin()) {
          echo '<div class="pzwp-floater floater-' . $i . ' ' . $rotation[ $i ] . '" ' . $style[ $i ] . '>' . $page_url . '</div>';
        }
      }
    }


//    if (!function_exists('pzwp_add_media_fields')) {
//
//      function pzwp_add_media_fields($form_fields, $post)
//      {
//        // $form_fields['be-photographer-name'] = array(
//        // 	'label' => 'Photographer Name',
//        // 	'input' => 'text',
//        // 	'value' => get_post_meta( $post->ID, 'be_photographer_name', true ),
//        // 	'helps' => 'If provided, photo credit will be displayed',
//        // );
//
//        $form_fields[ 'pzgp-focal-point' ] = array(
//            'label' => 'Focal Point',
//            'input' => 'text',
//            'value' => get_post_meta($post->ID, 'pzgp_focal_point', true),
//            'helps' => 'This is used by PizazzWP blocks and plugins for focal point cropping. Format is X%,Y%. e.g. 33,66 would be 33% across, 66% down.
//		<br/><strong>If you are in the media editor, you can double click on the image to automatically fill the focal point field.</strong>',
//        );
//
//        return $form_fields;
//      }
//
//      add_filter('attachment_fields_to_edit', 'pzwp_add_media_fields', 10, 2);
//
//      /**
//       * Save values of Photographer Name and URL in media uploader
//       *
//       * @param $post array, the post data for database
//       * @param $attachment array, attachment fields from $_POST form
//       * @return $post array, modified post data
//       */
//      function pzwp_add_media_fields_save($post, $attachment)
//      {
//        // if( isset( $attachment['be-photographer-name'] ) )
//        // 	update_post_meta( $post['ID'], 'be_photographer_name', $attachment['be-photographer-name'] );
//
//        if (isset($attachment[ 'pzgp-focal-point' ])) {
//          update_post_meta($post[ 'ID' ], 'pzgp_focal_point', $attachment[ 'pzgp-focal-point' ]);
//        }
//
//        return $post;
//      }
//
//      add_filter('attachment_fields_to_save', 'pzwp_add_media_fields_save', 10, 2);
//    }
  } // (!function_exists('pizazzwp_head'))


// TODO: Support page
// TODO: Check WPMS
// TODO: Add fittext options page
// TODO: FIX dialog not popping up
// TODO: Add double-check for upgrades. i.e. if HW fails, check Pz
// Add Options screen. E.g Show thumbs and IDs in lists
// Fix buttons not right in IE8 http://florizelmedia.com/
