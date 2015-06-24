<?php

  /* Create the slides custom post type
  */
  add_action('init', 'pzsp_create_slides_post_type');

  if (is_admin()) {
    // add_action('add_meta_boxes', 'pzsp_slides_meta');
    add_action('admin_init', 'pzsp_slides_meta');
    add_filter('manage_pzsp-slides_posts_columns', 'pzsp_add_slide_columns');
    add_action('manage_pzsp-slides_posts_custom_column', 'pzsp_add_slide_column_content', 10, 2);
  }
// Later this will xtend a pizazz content class


  function pzsp_create_slides_post_type()
  {


    $labels = array(
        'name'               => _x('Slides', 'post type general name'),
        'singular_name'      => _x('Slide', 'post type singular name'),
        'add_new'            => __('Add New Slide'),
        'add_new_item'       => __('Add New Slide'),
        'edit_item'          => __('Edit Slide'),
        'new_item'           => __('New Slide'),
        'view_item'          => __('View Slide'),
        'search_items'       => __('Search Slides'),
        'not_found'          => __('No slides found'),
        'not_found_in_trash' => __('No slides found in Trash'),
        'parent_item_colon'  => '',
        'menu_name'          => _x('S+ Slides', 'pzsp-slides'),
    );

    $args = array(
        'labels'               => $labels,
        'description'          => __('Slides provides a method for you to manually create slides for the PizazzWP SliderPlus plugin to use'),
        'public'               => true,
        'publicly_queryable'   => false,
        'show_ui'              => true,
        'show_in_menu'         => 'pizazzwp',
        'show_in_nav_menus'    => false,
        'query_var'            => true,
        'rewrite'              => true,
        'capability_type'      => 'post',
        'has_archive'          => false,
        'hierarchical'         => false,
        'menu_position'        => 45,
        'taxonomies'           => array('slide_set', 'category'),
        'supports'             => array('title', 'editor', 'page-attributes', 'thumbnail', 'revisions'),
        'exclude_from_search'  => true,
        'register_meta_box_cb' => 'pzsp_slides_meta'
    );

    register_post_type('pzsp-slides', $args);

    // Create taxonomy for slide sets
    $labels = array(
        'name'              => _x('Slide categories', 'taxonomy general name'),
        'singular_name'     => _x('Slide category', 'taxonomy singular name'),
        'search_items'      => __('Search Slide categories'),
        'all_items'         => __('All Slide categories'),
        'parent_item'       => __('Parent Slide category'),
        'parent_item_colon' => __('Parent Slide category:'),
        'edit_item'         => __('Edit Slide category'),
        'update_item'       => __('Update Slide category'),
        'add_new_item'      => __('Add New Slide category'),
        'new_item_name'     => __('New Slide category name'),
        'menu_name'         => __(' Slide Categories'),
    );

    register_taxonomy('slide_set',
                      array('pzsp-slides', 'post', 'page'),
                      array(
                          'hierarchical' => true,
                          'labels'       => $labels,
                          'show_ui'      => true,
                          'query_var'    => true,
                          'rewrite'      => array('slug' => 'slideset'),
                      )
    );

  }


  function pzsp_add_slide_columns($columns)
  {
    $pzsp_front  = array_slice($columns, 0, 2);
    $pzsp_back   = array_slice($columns, 2);
    $pzsp_insert = array(
        'pzsp_slidecats' => __('Slide Categories', 'pzsp'),
        'pzsp_links'     => __('Links', 'pzsp'),
        'pzsp_pageorder' => __('Page Order', 'pzsp')
    );

    return array_merge($pzsp_front, $pzsp_insert, $pzsp_back);
  }

  function pzsp_add_slide_column_content($column, $post_id)
  {
    switch ($column) {
      case 'pzsp_links':
        $pzsp_source = get_post_meta($post_id, 'pzsp_video_url', true);
        if ($pzsp_source) {
          echo '<a href="' . $pzsp_source . '">Embed URL</a><br/>';
        }
        $pzsp_destination = get_post_meta($post_id, 'pzsp_destination_url', true);
        if ($pzsp_destination) {
          echo '<a href="' . $pzsp_destination . '">Destination URL</a><br/>';
        }
        break;
      case 'pzsp_slidecats':
        echo get_the_term_list($post_id, 'slide_set', '', ', ', '');
        break;
      case 'pzsp_pageorder':
        $pzsp_menu_order = get_page($post_id);
        echo $pzsp_menu_order->menu_order;
        break;
    }
  }


  function pzsp_slides_meta()
  {
    global $pzsp_cpt_slides_meta_boxes;

    // Fill any defaults if necessary
    $pzsp_cpt_slides_meta_boxes = pzsp_slide_defaults();

    pzsp_populate_slide_options();

    add_meta_box($pzsp_cpt_slides_meta_boxes[ 'id' ],
                 $pzsp_cpt_slides_meta_boxes[ 'title' ],
                 'pzsp_show_box',
                 $pzsp_cpt_slides_meta_boxes[ 'page' ],
                 $pzsp_cpt_slides_meta_boxes[ 'context' ],
                 $pzsp_cpt_slides_meta_boxes[ 'priority' ],
                 $pzsp_cpt_slides_meta_boxes
    );

  } // End slides_meta


  function pzsp_populate_slide_options()
  {
    global $pzsp_cpt_slides_meta_boxes;

    $prefix = 'pzsp_';
    /*
     *
     * Setup Slides extra fields
     *
     */
    $pzsp_cpt_slides_meta_boxes                            = array(
        'id'       => 'pzsp-slides-video',
        'title'    => 'Extras',
        'page'     => 'pzsp-slides',
        'context'  => 'normal',
        'priority' => 'high',
        'tabs'     => array(
            0 => array(

                'icon'  => '<img src="' . PZSP_PLUGIN_URL . '/libs/images/icons/world-65grey.png" width="16px"/>',
                'label' => __('Sources', 'pzsp'),
                'id'    => $prefix . 'tab_slides_source',
                'type'  => 'tab',
            )
        )
    );
    $pzsp_cpt_slides_meta_boxes[ 'tabs' ][ 0 ][ 'fields' ] = array(
//			 array(
//				'label' => __('Embed Slider','pzsp'),
//				'id' => $prefix . 'embed_slider',
//				'type' => 'text',
//				'desc' => __('Enter a Slider shortname here if you want to embed another slider in this slide!','pzsp'),
//				'default' => ''
//			),
array('label'   => __('Sources', 'pzsp'),
      'desc'    => __('', 'pzsp'),
      'id'      => $prefix . 'sources_settings',
      'type'    => 'heading',
      'default' => '',
),
array(
    'label'   => __('Embed URL (video etc)', 'pzsp'),
    'id'      => $prefix . 'video_url',
    'type'    => 'text',
    'desc'    => __('To embed content from an external source, such as a YouTube video, enter its URL here. Entering a URL will override the displaying of the featured image for this slide.
					<br/><br/> <strong>Supported sources include:</strong>
					<br/>&nbsp;&bull;&nbsp;YouTube (only public videos and playlists - "unlisted" and "private" videos will not embed)
					<br/>&nbsp;&bull;&nbsp;Vimeo
					<br/>&nbsp;&bull;&nbsp;DailyMotion
					<br/>&nbsp;&bull;&nbsp;blip.tv
					<br/>&nbsp;&bull;&nbsp;Flickr (both videos and images)
					<br/>&nbsp;&bull;&nbsp;Viddler
					<br/>&nbsp;&bull;&nbsp;Hulu
					<br/>&nbsp;&bull;&nbsp;Qik
					<br/>&nbsp;&bull;&nbsp;Revision3
					<br/>&nbsp;&bull;&nbsp;Scribd
					<br/>&nbsp;&bull;&nbsp;Photobucket
					<br/>&nbsp;&bull;&nbsp;WordPress.tv (only VideoPress-type videos for the time being)
					<br/>&nbsp;&bull;&nbsp;SmugMug (WordPress 3.0+)
					<br/>&nbsp;&bull;&nbsp;FunnyOrDie.com (WordPress 3.0+)
					<br/>&nbsp;&bull;&nbsp;Twitter (WordPress 3.4+)					
					<br/><br/>
					A full list can be seen here: <a href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target=_blank>WordPress supported oEmbed Providers</a><br/>
					<br/><strong>Note:</strong>
					<br/>&nbsp;&bull;&nbsp;Testing has produced mixed results with these. It seems to be a case of finding the exact URL that WordPress will like, and in some cases, the provider has changed their specs and it no longer matches with WordPress\'s coding.<br/>
					<br/>&nbsp;&bull;&nbsp;The content will automatically resize to fit the container; however, sometimes it may not fully fit the height. There is an option in the Slideshows to vertically centre when this happens.', 'pzsp'),
    'default' => ''
),
array(
    'label'   => __('Embed code', 'pzsp'),
    'id'      => $prefix . 'embed_code',
    'type'    => 'textarea-small',
    'desc'    => __('Enter full code, such as iframe, if you\'d rather embed using this method. It has one advantage too - it makes page loads faster.<br/><br/><strong>Note: </strong>If you enter an Embed URL as well, it will load, not this embed code', 'pzsp'),
    'default' => '',
    'help'    => 'If you want to embed a local video, enter the WordPress [video] shortcode format here. Read more at: <a href="http://codex.wordpress.org/Video_Shortcode" target=_blank>Video shortcode</a>'
),
array(
    'label'   => __('Destination URL', 'pzsp'),
    'id'      => $prefix . 'destination_url',
    'type'    => 'text',
    'desc'    => __('Enter a URL to goto when this slide is clicked on. If no URL is entered here, the slide will not link anywhere.', 'pzsp'),
    'default' => ''
),
array(
    'label'   => __('Destination window', 'pzsp'),
    'id'      => $prefix . 'destination_window',
    'type'    => 'select',
    'options' => array(
        array('value' => '_self', 'text' => 'Same'),
        array('value' => '_blank', 'text' => 'New'),
    ),
    'desc'    => __('Select whether to open this link in the same or a new window.', 'pzsp'),
    'default' => '_self'
),

    );


  }

  add_action('admin_head', 'pzsp_slides_add_help_tab');
  function pzsp_slides_add_help_tab()
  {
    $screen = get_current_screen();
    global $current_user;
    $user_id = $current_user->ID;
//	pzdebug($_SERVER);
    if (get_user_meta($user_id, 'pzsp_closed_help')) {
      $pz_help_button = '<p><a class="button-help-on" href="' . $_SERVER[ 'REQUEST_URI' ] . '&pzsp_yes_help">Turn on automatic display of help window</a></p>';
    } else {
      $pz_help_button = '<p><a class="button-help-off" href="' . $_SERVER[ 'REQUEST_URI' ] . '&pzsp_no_help">Turn off automatic display of help window</a></p>';
    }

    $prefix = 'pzsp_';
    switch ($screen->id) {
      case 'edit-pzsp-slides':
        $screen->add_help_tab(array(
                                  'id'      => $prefix . 'view_help_about',
                                  'title'   => __('About Slides'),
                                  'content' => '<h3>About</h3><p>' . __('PizazzWP SliderPlus Slides are a custom content type that lets you setup text, images and/or videos to display as slide sets. Note: They are not the only way to create Slideshows though, you can also make Slideshows out of posts and pages (and more in the future!).') . '</p>' . $pz_help_button
                              ));
        $screen->add_help_tab(array(
                                  'title'   => __('Duplicating Slides', 'pzsp'),
                                  'id'      => $prefix . 'view_help_duplicating',
                                  'content' => '<h3>Duplicating slides</h3><p>' . __('If you wish to duplicate slides, install the plugin <em>Duplicate Post</em> by Enrico Battochi and look for the Clone option when you hover over the Slide title in the Slide listing.', 'pzsp') . '</p>',
                              ));
        $screen->add_help_tab(array(
                                  'title'   => __('Support', 'pzsp'),
                                  'id'      => $prefix . 'view_help_support',
                                  'content' => '<h3>Support</h3><p>' . __('Headway users can get support for SliderPlus on the <a href="http://support.headwaythemes.com/" target=_blank>Headway forums</a>. Other users can get support at ', 'pzsp') . '<a href="https://pizazzwp.freshdesk.com" target=_blank>PizazzWP Support</a></p>',
                              )
        );


        break;
      case 'pzsp-slides':

        $screen->add_help_tab(array(
                                  'title'   => __('Designing a slide', 'pzsp'),
                                  'id'      => $prefix . 'edit_help_designing',
                                  'content' => '<h3>Tips for designing a slide</h3><p>' . __('A slide is made up of content and image. The image is set use the Featured Image. The content is entered as per a normal.....
            	<br>
            	<img src="' . PZSP_PLUGIN_URL . '/images/help/anatomy-of-slide.jpg"/>

            	', 'pzsp') . '</p>' . $pz_help_button,
                              )
        );

        break;

      default:
        return;
        break;
    }
  }

// Make this only load once - probably loads all the time at the moment

  function pzsp_slide_defaults()
  {
    global $pzsp_cpt_slides_meta_boxes;
    $pzsp_slide_defaults = array();
    pzsp_populate_slide_options();
    foreach ($pzsp_cpt_slides_meta_boxes[ 'tabs' ] as $pzsp_meta_box) {
      foreach ($pzsp_meta_box[ 'fields' ] as $pzsp_field) {
        if (!isset($pzsp_field[ 'id' ])) {
          $pzsp_slide_defaults[ $pzsp_field[ 'id' ] ] = (isset($pzsp_field[ 'default' ]) ? $pzsp_field[ 'default' ] : null);
        }
      }
    }

    return $pzsp_slide_defaults;
  }