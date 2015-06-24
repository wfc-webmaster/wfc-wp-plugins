<?php

  /*

    Create the SliderPlus custom post type
   */

// Add scaling menu:
// - Hide point (width when block disappears)
// - height behaviour - lock, scale
// - Featured images behaviour - lock, scale
// - Headings scaling
// - body text scaling
// Add a config menu
// - Full width
//

  if (is_admin())
  {
    include(PZSP_PLUGIN_PATH . '/pzsp-cpt-wphelp.php');

    add_filter('manage_pizazzsliders_posts_columns', 'pzsp_add_columns');
    add_action('manage_pizazzsliders_posts_custom_column', 'pzsp_add_column_content', 10, 2);
    add_action('current_screen', 'pzsp_meta');
//add_action('add_meta_boxes', 'pzsp_meta');
  }

  add_action('init', 'pzsp_create_post_type');

  function pzsp_create_post_type()
  {
    $labels = array(
        'name'               => _x('Slideshows', 'post type general name'),
        'singular_name'      => _x('Slideshow Layout', 'post type singular name'),
        'add_new'            => __('Add New Slideshow Layout'),
        'add_new_item'       => __('Add New Slideshow Layout'),
        'edit_item'          => __('Edit Slideshow Layout'),
        'new_item'           => __('New Slideshow Layout'),
        'view_item'          => __('View Slideshow Layout'),
        'search_items'       => __('Search Slideshow Layouts'),
        'not_found'          => __('No Slideshow layouts found'),
        'not_found_in_trash' => __('No Slideshow layouts found in Trash'),
        'parent_item_colon'  => _x('Parent Slideshow:', 'pizazzsliders'),
        'menu_name'          => _x('S+ Slideshows', 'pizazzsliders'),
    );

    $args = array(
        'labels'               => $labels,
        'description'          => __('SliderPlus provides a method to display your posts or pages in sliders, tabbed view, galleries and more.'),
        'public'               => false,
        'publicly_queryable'   => false,
        'show_ui'              => true,
        'show_in_menu'         => 'pizazzwp',
        'show_in_nav_menus'    => false,
        'query_var'            => true,
        'rewrite'              => true,
        'capability_type'      => 'page',
        //			'capabilities' => pzsp_access(),
        'has_archive'          => false,
        'hierarchical'         => false,
        'menu_position'        => 45,
        'supports'             => array('title', 'revisions'),
        'exclude_from_search'  => true,
        'can_export'           => true,
        'register_meta_box_cb' => 'pzsp_meta'
    );

    $result = register_post_type('pizazzsliders', $args);
  }

  function pzsp_sliderplus_description($post)
  {

    ?>
    <div class="after-title-help postbox">
      <div class="inside">
        <p class="howto"><?php echo __('SliderPlus Slideshows are where you select pre-created content to display in a slideshow. This can be from Posts, Pages, Pizazz Slides, or galleries.<br/> If you want to know more, click the dropdown <strong>help button</strong> in the top right, or visit the website: ', 'pzsp'); ?>
          <a href="http://guides.pizazzwp.com/sliderplus/about-sliderplus/" target="_blank">SliderPlus Guides</a></p>

      </div>
      <!-- .inside -->
    </div><!-- .postbox -->
  <?php

  }

  add_action('views_edit-pizazzsliders', 'pzsp_sliderplus_description');

  function pzsp_doc_description($post)
  {
    if ($post->post_type != 'pizazzsliders')
    {
      return;
    }
    ?>
    <div class="after-title-help postbox">
      <div class="inside">
        <p class="howto"><?php echo __('For help, click the tooltips (the white question mark in the grey circle); or open the dropdown help in the top right of the screen; or visit the website: ', 'pzsp'); ?>
          <a href="http://guides.pizazzwp.com/sliderplus/about-sliderplus/" target="_blank">SliderPlus Online Guide</a>
        </p>
      </div>
      <!-- .inside -->
    </div><!-- .postbox -->
  <?php
  }

  add_action('edit_form_after_title', 'pzsp_doc_description');


  function pzsp_add_columns($columns)
  {
    unset($columns[ 'thumbnail' ]);
    $pzsp_front  = array_slice($columns, 0, 2);
    $pzsp_back   = array_slice($columns, 2);
    $pzsp_insert = array(
        'pzsp_short_name'   => __('Short Name', 'pzsp') . '&nbsp;<span class="pz-help"><span class="pz-help-button">?<span class="pz-help-text">Use short name in shortcode.<br/> e.g. [sliderplus myslider]</span></span></span>',
        'pzsp_content_type' => __('Content Source', 'pzsp'),
        'pzsp_filtering'    => __('Criteria', 'pzsp'),
        'pzsp_layout'       => __('Layout', 'pzsp'),
        'pzsp_nav_type'     => __('Navigation', 'pzsp'),
        'pzsp_theme'        => __('Theme', 'pzsp')
    );

    return array_merge($pzsp_front, $pzsp_insert, $pzsp_back);
  }

  function pzsp_add_column_content($column, $post_id)
  {
    switch ($column)
    {
      case 'pzsp_short_name':
        echo get_post_meta($post_id, 'pzsp_short_name', true);
        break;
      case 'pzsp_content_type':
      case 'pzsp_filtering':
      case 'pzsp_layout':
      case 'pzsp_nav_type':
        echo ucfirst(get_post_meta($post_id, $column, true));
        break;
      case 'pzsp_theme':
        echo ucfirst(get_post_meta($post_id, 'pzsp_theme', true));
        break;
    }
  }

  function pzsp_meta($screen)
  {
    // This saves a bazillion seconds in queries. Need to determine why!
    if ($screen->id != 'pizazzsliders')
    {
      return;
    }

    global $pzsp_cpt_meta_boxes;
    //$pzsp_cpt_meta_boxes = pzsp_slider_defaults();
    if (!is_admin())
    {
      return;
    }
    $screen = get_current_screen();
    if (!empty($screen->id) && $screen->id != 'pizazzsliders')
    {
      return;
    }
    pzsp_populate_slider_options();

    add_meta_box(
        $pzsp_cpt_meta_boxes[ 'id' ], $pzsp_cpt_meta_boxes[ 'title' ], 'pzsp_show_box', $pzsp_cpt_meta_boxes[ 'page' ], $pzsp_cpt_meta_boxes[ 'context' ], $pzsp_cpt_meta_boxes[ 'priority' ], $pzsp_cpt_meta_boxes
    );
//	add_meta_box( 'pzsp-slider-help', 'SliderPlus Help', 'pzsp_draw_help', 'pizazzsliders', 'side', 'default', '' );
  }

  function pzsp_draw_help()
  {

    echo '<div class="pzsp_slider_help">SliderPlus context aware help</div>';

    return;
  }

  /*
   * function pzsp_populate_slider_options()
   */

  function pzsp_populate_slider_options($just_defaults = false)
  {
    global $pzsp_cpt_meta_boxes;
    $prefix = 'pzsp_';
    $pzsp_content_types = array();
    $category_list      = array();
    $tags_list          = array();
    $tax_list           = array();
    $slide_list         = array();
    $slideset_list      = array();
    $pzsp_galleries     = array();
    $pzsp_ngg           = array();

    if (!$just_defaults && is_admin())
    { // debugging
      // this was exiting here if not admin. but that caused an warning down in pzsp_slider_defaults about $pzsp_cpt_meta_boxes being empty. bugger.
      if (is_admin())
      {
        $screen = get_current_screen();
        if (!empty($screen) && $screen->id != 'pizazzsliders' && $screen->id != 'pzsp-slides')
        {
          return;
        }
      }
      if (!$just_defaults)
      {
        // Generate taxonomies
        $taxonomies = pzsp_get_taxonomies('slide_set', 'exclude');
        $tax_list   = array();
        if ($taxonomies)
        {
          $i = 0;
          foreach ($taxonomies as $key => $tax)
          {
            $tax_list[ $i++ ] = array('value' => $key, 'text' => $tax);
          }
        }
//pzdebug($tax_list);
        // Get slide sets only
        $slidesets     = pzsp_get_taxonomies('slide_set', 'only');
        $slideset_list = array();
        if ($slidesets)
        {
          $i = 0;
          foreach ($slidesets as $key => $slideset)
          {
            $slideset_list[ $i++ ] = array('value' => $key, 'text' => $slideset);
          }
        }

// Generate slides list
        $slides     = get_posts(array('post_type' => 'pzsp-slides'));
        $slide_list = array();
        if ($slides)
        {
          $i = 0;
          foreach ($slides as $slide)
          {
            $slide_list[ $i++ ] = array('value' => $slide->ID, 'text' => $slide->post_title);
          }
        }

        // Generate pages list
        $pages     = get_pages(array('post_type' => 'page'));
        $page_list = array();
        if ($pages)
        {
          $i = 0;
          foreach ($pages as $page)
          {
            $page_list[ $i++ ] = array('value' => $page->ID, 'text' => $page->post_title);
          }
        }

        // G+ Gallery list
          $pzsp_galleries = array();
          $galleries      = pzsp_get_galleries('pzspgp');
          foreach ($galleries as $gallery)
          {
            if ($gallery[ 'source' ] == 'Gallery+: ')
            {
              $pzsp_galleries[ ] = array('value' => $gallery[ 'post_id' ], 'text' => ('G+: ' . $gallery[ 'title' ]));
            }
          }
// WP Gallery list
        // WordPress
        $wp_results             = array();
        $wp_results             = pzsp_get_wp_galleries('pzspwp');
        $wp_single              = array();
        $wp_single[ 0 ]         = array('post_id' => 99999999,
                                        'title'   => 'Use images in viewed post/page',
                                        'source'  => 'Content: ');
        $pzsp_wp_galleries_list = array_merge($wp_single, $wp_results);
        foreach ($pzsp_wp_galleries_list as $gallery)
        {
          $pzsp_galleries[ ] = array('value' => $gallery[ 'post_id' ], 'text' => ('WP: ' . $gallery[ 'title' ]));
        }

        // NGG Gallery list
        $pzsp_ngg[ 0 ] = array('value' => '0', 'text' => 'No galleries available');

        if (class_exists('nggdb'))
        {
          global $ngg, $nggdb;
          $pzsp_ngg     = array();
          $ng_galleries = $nggdb->find_all_galleries('gid', 'asc', true, 0, 0, false);
          if ($ng_galleries)
          {
            foreach ($ng_galleries as $gallery)
            {
              $pzsp_ngg[ ] = array('value' => $gallery->gid,
                                   'text'  => ('NG: ' . $gallery->title . ' (' . $gallery->counter . ')'));
            }
          }
        }


        // Generate tags list
        $tags_list = pzsp_get_tags();

        // Generate category list
        $category_list = pzsp_get_categories();

        // Generate content types
        $pzsp_content_types = pzsp_get_post_types();
//	$pzsp_content_types[] = array('value'=>'rss','text'=>'RSS Feed');
        $pzsp_content_types[ ] = array('value' => 'gplus_gallery', 'text' => 'WordPress/GalleryPlus Galleries');
        $pzsp_content_types[ ] = array('value' => 'ngg_gallery', 'text' => 'NextGen Galleries');
      }
    }
    $i                   = 0;
    $pzsp_cpt_meta_boxes = array(
        'id'       => 'pzsp-slider-settings',
        'title'    => 'Slideshow Settings',
        'page'     => 'pizazzsliders',
        'context'  => 'normal',
        'priority' => 'high',
        /* Labels CANNOT contain SPACES - tho might have fixed this */
        'tabs'     => array(
            $i++ => array(
                'icon'  => '<img src="' . PIZAZZ_PLUGIN_URL . '/images/icons/general-65grey.png" width="20px"/>',
                'label' => __('General', 'pzsp'),
                'id'    => $prefix . 'tab_general',
                'type'  => 'tab',
            ),
            $i++ => array(
                'icon'  => '<img src="' . PIZAZZ_PLUGIN_URL . '/images/icons/filter-65grey.png" width="20px"/>',
                'label' => __('Criteria', 'pzsp'),
                'id'    => $prefix . 'tab_filter',
                'type'  => 'tab',
            ),
            $i++ => array(
                'icon'  => '<img src="' . PIZAZZ_PLUGIN_URL . '/images/icons/layout1-65grey.png" width="20px"/>',
                'label' => __('Layout', 'pzsp'),
                'id'    => $prefix . 'tab_layout',
                'type'  => 'tab',
            ),
            $i++ => array(
                'icon'  => '<img src="' . PIZAZZ_PLUGIN_URL . '/images/icons/text1-65grey.png" width="20px"/>',
                'label' => __('Content', 'pzsp'),
                'id'    => $prefix . 'tab_contents',
                'type'  => 'tab',
            ),
            $i++ => array(
                'icon'  => '<img src="' . PIZAZZ_PLUGIN_URL . '/images/icons/images-65grey.png" width="20px"/>',
                'label' => __('Feature', 'pzsp'),
                'id'    => $prefix . 'tab_features',
                'type'  => 'tab',
            ),
            $i++ => array(
                'icon'  => '<img src="' . PIZAZZ_PLUGIN_URL . '/images/icons/navigation-65grey.png" width="20px"/>',
                'label' => __('Navigation', 'pzsp'),
                'id'    => $prefix . 'tab_navigation',
                'type'  => 'tab',
            ),
            $i++ => array(
                'icon'  => '<img src="' . PIZAZZ_PLUGIN_URL . '/images/icons/transitions-65grey.png" width="20px"/>',
                'label' => __('Transition', 'pzsp'),
                'id'    => $prefix . 'tab_transition',
                'type'  => 'tab',
            ),
            $i++ => array(
                'icon'  => '<img src="' . PIZAZZ_PLUGIN_URL . '/images/icons/styling-65grey.png" width="20px"/>',
                'label' => __('Styling', 'pzsp'),
                'id'    => $prefix . 'tab_styles',
                'type'  => 'tab',
            ),
            $i++ => array(
                'icon'  => '<img src="' . PIZAZZ_PLUGIN_URL . '/images/icons/phone-65grey.png" width="20px"/>',
                'label' => __('Responsive', 'pzsp'),
                'id'    => $prefix . 'tab_responsive',
                'type'  => 'tab',
            ),
            $i++ => array(
                'icon'  => '<img src="' . PIZAZZ_PLUGIN_URL . '/images/icons/braces-65grey.png" width="20px"/>',
                'label' => __('CSS', 'pzsp'),
                'id'    => $prefix . 'tab_custom_css',
                'type'  => 'tab',
            ),
        )
    );


    $i = 0;
    /*	 * **********************************************************************
     * ************************************************************************
     * Create General Settings Meta Box
     * ************************************************************************
     * ********************************************************************** */
    $pzsp_cpt_meta_boxes[ 'tabs' ][ $i++ ][ 'fields' ] = array(
        array('label'   => __('General Settings', 'pzsp'),
              'desc'    => __('', 'pzsp'),
              'id'      => $prefix . 'general_settings',
              'type'    => 'heading',
              'default' => '',
        ),
        array(
            'label'   => __('Slideshow ID', 'pzsp'),
            'desc'    => __('Automatically enters the Slideshow ID.', 'pzsp'),
            'id'      => $prefix . 'post_id',
            'type'    => 'readonly',
            'default' => null
        ),
        array(
            'label'   => __('Show slideshow title.', 'pzsp'),
            'desc'    => __('If checked, the Title (entered above) will appear above the slideshow.', 'pzsp'),
            'id'      => $prefix . 'show_title',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Short Name', 'pzsp'),
            'desc'    => __('Enter a short name for this Slideshow. <strong>No spaces or punctuation and MUST begin with an alphabetic character - i.e. not a number.</strong> This will be the name you use in the shortcode. <strong>e.g. [sliderplus myfaves]</strong><p>If you only enter the shortcode [sliderplus] it will display a list of available Slideshows</p>', 'pzsp'),
            'id'      => $prefix . 'short_name',
            'type'    => 'text',
            'default' => '',
            'help'    => 'MUST begin with an alphabetic character - i.e. not a number. Also, no spaces or punctuation.'
        ),
        // array(
        //     'label' => __('Lower range (px)','pzsp'),
        //     'desc' => __('','pzsp'),
        //     'id' => $prefix . 'lower_range',
        //     'type' => 'text',
        //     'default' => 0,
        //     'help' => 'Lower range for responsive design'
        // ),
        // array(
        //     'label' => __('Upper range (px)','pzsp'),
        //     'desc' => __('','pzsp'),
        //     'id' => $prefix . 'upper_range',
        //     'type' => 'text',
        //     'default' => 9999,
        //     'help' => 'Upper range for responsive design'
        // ),
    );

    /********************************************
     *
     * CRITERIA
     *
     ********************************************/
    $pzsp_cpt_meta_boxes[ 'tabs' ][ $i++ ][ 'fields' ] = array(
        array('label'   => __('Criteria', 'pzsp'),
              'desc'    => __('', 'pzsp'),
              'id'      => $prefix . 'criteria_heading',
              'type'    => 'heading',
              'default' => '',
        ),
        array(
            'label'   => __('Content Type Source', 'pzsp'),
            'id'      => $prefix . 'content_type',
            'type'    => 'select',
            'options' => $pzsp_content_types,
            'default' => 'post',
            'help'    => 'Slides are automatically constructed using the Featured Image and the content in the selected source',
            'desc'    => __('Choose a content type source for your slideshow. This can be posts, pages, slides, galleries or custom content types<br/><br/>Slides will then be automatically constructed from your selected source.', 'pzsp')
        ),
        array(
            'label'   => __('Filtering method', 'pzsp'),
            'id'      => $prefix . 'filtering',
            'type'    => 'select',
            'options' => array(
                array('value' => 'recent', 'text' => __('Recent Posts', 'pzsp')),
                array('value' => 'slide_set', 'text' => __('Slide Set', 'pzsp')),
                array('value' => 'category', 'text' => __('Categories', 'pzsp')),
                array('value' => 'tags', 'text' => __('Tags', 'pzsp')),
                array('value' => 'taxonomy', 'text' => __('Taxonomy', 'pzsp')),
                array('value' => 'specific_ids', 'text' => __('Specific IDs', 'pzsp')),
            ),
            'default' => 'recent',
            'desc'    => __('Choose a filtering method for your content type.', 'pzsp')
        ),
        array(
            'label'   => __('Select content in category', 'pzsp'),
            'id'      => $prefix . 'category',
            'type'    => 'multiselect',
            'options' => $category_list,
            'default' => null,
            'desc'    => __('Select the content category to use. (Generally, you should only use one!)<br/><br/><strong>Note:</strong> This will only show categories that have posts assigned to them.', 'pzsp')
        ),
        array(
            'label'   => __('Select content with tags', 'pzsp'),
            'id'      => $prefix . 'tags',
            'type'    => 'multiselect',
            'options' => $tags_list,
            'default' => null,
            'desc'    => __('Select the content tags to use.', 'pzsp')
        ),
        array(
            'label'   => __('Select content in custom taxonomy', 'pzsp'),
            'id'      => $prefix . 'taxonomy',
            'type'    => 'multiselect',
            'options' => $tax_list,
            'default' => null,
            'desc'    => __('Select the content custom taxonomies to use. (Generally, you should only use one!)', 'pzsp')
        ),
        array(
            'label'   => __('Select content in slide set', 'pzsp'),
            'id'      => $prefix . 'slide_set',
            'type'    => 'multiselect',
            'options' => $slideset_list,
            'default' => null,
            'desc'    => __('Select the content slide set to use. (Generally, you should only use one!)', 'pzsp')
        ),
        array(
            'label'   => __('Select WordPress/GalleryPlus gallery', 'pzsp'),
            'id'      => $prefix . 'gplus_gallery',
            'type'    => 'select',
            'options' => $pzsp_galleries,
            'default' => null,
            'desc'    => __('Select the WordPress or GalleryPlus gallery to use.', 'pzsp')
        ),
        array(
            'label'   => __('Select NextGen gallery', 'pzsp'),
            'id'      => $prefix . 'ngg_gallery',
            'type'    => 'select',
            'options' => $pzsp_ngg,
            'default' => null,
            'desc'    => __('Select the NextGen gallery to use.', 'pzsp')
        ),
        array(
            'label'   => __('Specific IDs', 'pzsp'),
            'desc'    => __('If you\'d rather, you can select content by specify the IDs of each as a comma separated list. e.g. 23,78,45', 'pzsp'),
            'id'      => $prefix . 'specific_ids',
            'type'    => 'text',
            'default' => ''
        ),
        array(
            'label'   => __('Number to show', 'pzsp'),
            'desc'    => __('Enter the number of posts to show. Set to 0 or blank for all', 'pzsp'),
            'id'      => $prefix . 'number_show',
            'type'    => 'numeric',
            'default' => null
        ),
        array(
            'label'   => __('Selection order', 'pzsp'),
            'desc'    => __('Select the order the slides will be selected. Note: Choosing random will present a random selection of slides, not a random order of slides. Use Randomise below idf you want the latter to occur.', 'pzsp'),
            'id'      => $prefix . 'order_by',
            'type'    => 'select',
            'options' => array(
                array('value' => 'date', 'text' => __('Date published', 'pzsp')),
                array('value' => 'modified', 'text' => __('Date last modified', 'pzsp')),
                array('value' => 'title', 'text' => __('Title', 'pzsp')),
                array('value' => 'rand', 'text' => __('Random', 'pzsp')),
                array('value' => 'menu_order', 'text' => __('Page order', 'pzsp')),
            ),
            'default' => 'date'
        ),
        array(
            'label'   => __('Order by', 'pzsp'),
            'desc'    => __('Select the order to display the slides from NextGen', 'pzsp'),
            'id'      => $prefix . 'ngg_order_by',
            'type'    => 'select',
            'options' => array(
                array('value' => 'imagedate', 'text' => __('Image Date', 'pzsp')),
                array('value' => 'alttext', 'text' => __('Alt Text', 'pzsp')),
                array('value' => 'pid', 'text' => __('ID', 'pzsp')),
                array('value' => 'rand', 'text' => __('Random', 'pzsp')),
            ),
            'default' => 'title'
        ),
        array(
            'label'   => __('Order direction', 'pzsp'),
            'desc'    => __('Select the order direction to display the slides', 'pzsp'),
            'id'      => $prefix . 'order_az',
            'type'    => 'select',
            'options' => array(
                array('value' => 'ASC', 'text' => __('Ascending', 'pzsp')),
                array('value' => 'DESC', 'text' => __('Descending', 'pzsp')),
            ),
            'default' => 'DESC'
        ),
        array(
            'label'   => __('Randomise slide order', 'pzsp'),
            'desc'    => __('Display the slides in a random order', 'pzsp'),
            'id'      => $prefix . 'randomise_slides',
            'type'    => 'checkbox',
            'default' => false
        ),
        // How do you process RSS feeds??? As text only - no featured image? Then may as well just use Tabs+
        // If featured image, then how to trim text. what rules.
        //	        array(
        //	            'label' => __('RSS feed URL','pzsp'),
        //	            'desc' => __('Enter the URL of the RSS feed to display. <strong>Note:</strong> Not all feeds are created equal! So, cannot guarantee all feeds will work. Test before going to live!','pzsp'),
        //	            'id' => $prefix . 'rss',
        //	            'type' => 'text',
        //	            'default' => ''
        //	        ),
    );


    /*	 * ***********************************************************************
     * ************************************************************************
     * Create layout Settings Meta Box
     * ***********************************************************************
     * ********************************************************************** */
    $pzsp_cpt_meta_boxes[ 'tabs' ][ $i++ ][ 'fields' ] = array(
        array('label'   => __('Layout', 'pzsp'),
              'desc'    => __('.', 'pzsp'),
              'id'      => $prefix . 'desktop_layout',
              'type'    => 'heading',
              'default' => '',
        ),
        array(
            'label'   => __('Slide layout', 'pzsp'),
            'id'      => $prefix . 'layout',
            'type'    => 'select',
            'options' => array(
                array('value' => 'ImageLeft', 'text' => 'Feature left, Content right'),
                array('value' => 'ImageRight', 'text' => 'Feature right, Content left'),
                array('value' => 'ImageTop', 'text' => 'Feature top, Content bottom'),
                array('value' => 'ImageBottom', 'text' => 'Feature bottom, Content top'),
                array('value' => 'ImageOnly', 'text' => 'Feature only'),
                array('value' => 'TextOnly', 'text' => 'Content only'),
            ),
            'default' => 'ImageRight',
            'desc'    => __('</p>Slides can be arranged in one of four general configurations. If you are using Slides as the source, then if you\'ve set a Embed URL or Embded Code, it will be shown in the feature area. Otherwise, if the posts/page/slide has a featured image, it will show in the feature area:</p>
								<div style="width:50%;float:left;"><h4>Feature left, Content right</h4><img class="pzsp-layout-images" src="' . PZSP_PLUGIN_URL . '/images/pzsplayouts-img-left.jpg" width=64 height=64/></div>
								<div style="width:50%;float:left;"><h4>Feature right, Content left</h4><img class="pzsp-layout-images" src="' . PZSP_PLUGIN_URL . '/images/pzsplayouts-img-right.jpg" width=64 height=64/></div>
								<div style="width:50%;float:left;"><h4>Feature top, Content bottom</h4><img class="pzsp-layout-images" src="' . PZSP_PLUGIN_URL . '/images/pzsplayouts-img-top.jpg" width=64 height=64/></div>
								<div style="width:50%;float:left;"><h4>Feature bottom, Content top</h4><img class="pzsp-layout-images" src="' . PZSP_PLUGIN_URL . '/images/pzsplayouts-img-bottom.jpg" width=64 height=64/></div>'
                , 'pzsp'),
            'help'    => 'Content area is where the post/page content is displayed<br/>Feature area is where the Featured Image or embedded video is displayed.'
        ),
        array(
            'label'   => __('Navigation Location', 'pzsp'),
            'id'      => $prefix . 'nav_location',
            'type'    => 'select',
            'options' => array(
                array('value' => '%navoutertop%', 'text' => __('Top', 'pzsp')),
                array('value' => '%navouterleft%', 'text' => __('Left', 'pzsp')),
                array('value' => '%navouterright%', 'text' => __('Right', 'pzsp')),
                array('value' => '%navouterbottom%', 'text' => __('Bottom', 'pzsp')),
                array('value' => 'navnone', 'text' => __('No Navigation', 'pzsp')),
            ),
            'default' => '%navouterbottom%',
            'desc'    => __('Select the location of navigation.', 'pzsp'),
        ),
        array(
            'label'   => __('Full width', 'pzsp'),
            'desc'    => __('Make this a full width slider. This does require some special treatments by you:<ul>
				<li>The width and height below should match your images</li>
				<li>Your images should be wider than a desktop screen, e.g. 3000px</li>
				<li>Set the Content area relative size (below) as the desktop width in pixels of your main content section the page. Usually around 960.</li>
				<li>Set the Content area opacity if you want the image to show through.</li>
				<li>Be sure to set the Feature area relative width to 100%</li>
				<li><strong>Headway users:</strong> Display this in it own wrapper of type Fluid Width</li>
				<li><strong>Headway users:</strong> when you draw the SliderPlus block, make it the full width of the wrapper</li>
				<li><strong>Headway users:</strong> you will want to set the wrapper margins to zero</li>
				</ul>', 'pzsp'),
            'id'      => $prefix . 'full_width',
            'help'    => 'IMPORTANT: Click and read the tooltip for this setting!',
            'type'    => 'checkbox',
            'default' => false

        ),
        array(
            'label'   => __('Slideshow width (px)', 'pzsp'),
            'desc'    => __('Enter a width for the Slideshow - i.e. navigation, image, text, border and padding  - in pixels.
								<p>When selecting the Slideshow in a Headway block, it will show this width so you know how wide the block needs to be.</p>
							<strong>Note:</strong> Because of the responsive design built in, the Slideshow will automatically shrink if there is insufficent width for it. For example, you set it to 900px wide, insert it into a post, but the post can only display 600px wide,the slider will shrink accordingly.	
							', 'pzsp'),
            'id'      => $prefix . 'contents_width',
            'type'    => 'numeric',
            'default' => 500,
            'help'    => 'Includes navigation width if left or right navigation'
        ),
        array(
            'label'   => __('Slide height (px)<br/>', 'pzsp'),
            'desc'    => __('Enter a height for the slide - i.e. image and text only - in pixels. The full height of the Slideshow, inlcuding navigation, padding and borders, is automatically calculated.', 'pzsp'),
            'id'      => $prefix . 'contents_height',
            'type'    => 'numeric',
            'default' => '200',
            'help'    => 'Excludes navigation height when top or bottom navigation'
        ),
        array(
            'label'   => __('Content area relative size<br>(% or px when full width)', 'pzsp'),
            'desc'    => __('Enter what percentage of the slide the Content should occupy. When the text area is horizontal, this represents a maximum height.<br/>
								<strong>Note:</strong> Do not enter the % symbol.
								<h4>Examples</h4>
								In these examples, the chosen layout is Feature Left, Content Right.
								<h5>Content 25%, Feature 75%</h5>
								<img src="' . PZSP_PLUGIN_URL . '/images/help/text25-image75-nudge0-opacity0.jpg"/>
								<h5>Content 75%, Feature 25%</h5>
								<img src="' . PZSP_PLUGIN_URL . '/images/help/text75-image25-nudge0-opacity0.jpg"/>
								<h5>Content 25%, Feature 100%, Nudge 50px, Opacity 80%</h5>
								<img src="' . PZSP_PLUGIN_URL . '/images/help/text25-image100-nudge50-opacity80.jpg"/>

							', 'pzsp'),
            'id'      => $prefix . 'text_size',
            'type'    => 'numeric',
            'default' => '33',
            'help'    => 'Content area is where the post/page content is displayed'
        ),
        array(
            'label'   => __('Feature area relative size (%)', 'pzsp'),
            'desc'    => __('Enter what percentage of the frame the Feature area should occupy.<br/>
								If 100, the whole Feature will show behind the text.<br/> 
								<strong>Note:</strong> Do not enter the % symbol.<br/>
								If you are using SliderPlus Slides as the content source, and you have set a Embed URL or Embded Code, the video or image from that source will be sized by this option.
								<h4>Examples</h4>
								In these examples, the chosen layout is Feature Left, Content Right.
								<h5>Content 25%, Feature 75%</h5>
								<img src="' . PZSP_PLUGIN_URL . '/images/help/text25-image75-nudge0-opacity0.jpg"/>
								<h5>Content 75%, Feature 25%</h5>
								<img src="' . PZSP_PLUGIN_URL . '/images/help/text75-image25-nudge0-opacity0.jpg"/>
								<h5>Content 25%, Feature 100%, Nudge 50px, Opacity 80%</h5>
								<img src="' . PZSP_PLUGIN_URL . '/images/help/text25-image100-nudge50-opacity80.jpg"/>
								', 'pzsp'),
            'id'      => $prefix . 'image_size',
            'type'    => 'numeric',
            'default' => '67',
            'help'    => 'Feature area is where the Featured Image or embedded video is displayed.'
        ),
    );


    /*	 * **********************************************************************
     * ************************************************************************
     * Text
     * ************************************************************************
     * ********************************************************************** */
    $pzsp_cpt_meta_boxes[ 'tabs' ][ $i++ ][ 'fields' ] = array(
        array('label'   => __('Content', 'pzsp'),
              'desc'    => __('', 'pzsp'),
              'id'      => $prefix . 'text_settings',
              'type'    => 'heading',
              'default' => '',
        ),
//        array(
//            'label'   => __('Use actual excerpt if available.', 'pzsp'),
//            'desc'    => __('If the user has entered text in the excerpt field, use it instead.', 'pzsp'),
//            'id'      => $prefix . 'use_excerpt',
//            'type'    => 'checkbox',
//            'default' => false
//        ),
        array(
            'label'   => __('Content prune (chars)', 'pzsp'),
            'desc'    => __('Enter a value for maximum number characters to show in the content. Enter 0 to show all of the text. This will not trim if the actual excerpt is displayed.', 'pzsp'),
            'id'      => $prefix . 'text_prune',
            'type'    => 'numeric',
            'default' => 0
        ),
        array(
            'label'   => __('Content area padding (px)', 'pzsp'),
            'desc'    => __('Enter padding around content', 'pzsp'),
            'id'      => $prefix . 'text_padding',
            'type'    => 'numeric',
            'default' => '15'
        ),
        array(
            'label'   => __('Nudge Content area (px)', 'pzsp'),
            'desc'    => __('If you don\'t want the content area hard up against the edge, enter a distance to nudge it in from the edge. Now, obviously, you only want to use this if feature size is 100%!', 'pzsp'),
            'id'      => $prefix . 'text_nudge',
            'type'    => 'numeric',
            'default' => 0
        ),
        array(
            'label'   => __('Fill content area to whole slide if no Feature.', 'pzsp'),
            'desc'    => __('If the slide doesn\'t have a Feature, check this box to make the Content area fill the entire slide.', 'pzsp'),
            'id'      => $prefix . 'text_fill',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Content area opacity (%)', 'pzsp'),
            'desc'    => __('Enter a percentage for the opacity of the content area.', 'pzsp'),
            'id'      => $prefix . 'text_area_opacity',
            'type'    => 'numeric',
            'default' => '100'
        ),
        array(
            'label'   => __('Disable title link', 'pzsp'),
            'desc'    => __('Prevent the title from linking to its page.', 'pzsp'),
            'id'      => $prefix . 'delink_title',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Hide title', 'pzsp'),
            'desc'    => __('Hide the content title.', 'pzsp'),
            'id'      => $prefix . 'hide_title',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Hide body', 'pzsp'),
            'desc'    => __('Hide the content body text.<br/><br/>However, if you actually want to hide a body...', 'pzsp'),
            'id'      => $prefix . 'hide_body',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Strip out iframes', 'pzsp'),
            'desc'    => __('Enable to remove iframes from content body', 'pzsp'),
            'id'      => $prefix . 'strip_iframes',
            'type'    => 'checkbox',
            'default' => true
        ),
        array(
            'label'   => __('[Read More] text', 'pzsp'),
            'desc'    => __('Enter the text you want to display to read more.', 'pzsp'),
            'id'      => $prefix . 'read_more',
            'type'    => 'text',
            'default' => '[Read more]'
        ),
        array(
            'label'   => __('Always show excerpts Read More', 'pzsp'),
            'desc'    => __('Enable if you want to always show the Read More link for excerpts, no matter what the prine length. You can style it with CSS to be a nice button. Selector is <em>.span.pzsp-more-indicator a</em>', 'pzsp'),
            'id'      => $prefix . 'force_readmore',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'type'    => 'select',
            'options' => array(
                array('value' => 'ellipses', 'text' => 'Ellipses'),
                array('value' => 'arrows', 'text' => 'Arrows'),
                array('value' => 'none', 'text' => 'None')
            ),
            'label'   => 'Truncation character',
            'desc'    => 'Choose character to show when the content is truncated',
            'default' => 'ellipses',
            'id'      => $prefix . 'trunc-char'
        ),
    );


    /*	 * **********************************************************************
     * ************************************************************************
     * Feature
     * ************************************************************************
     * ********************************************************************** */
    $pzsp_cpt_meta_boxes[ 'tabs' ][ $i++ ][ 'fields' ] = array(
        array('label'   => __('Feature', 'pzsp'),
              'desc'    => __('', 'pzsp'),
              'id'      => $prefix . 'feature_settings',
              'type'    => 'heading',
              'default' => '',
        ),
        // 'gp-focal-point-align' => array(
        // 	'type' 			=> 'checkbox',
        // 	'label' 		=> 'Respect Focal Point',
        // 	'tooltip' 	=> 'If selected and gallery used is a WordPress or GalleryPlus gallery, the focal point co-ordinates entered for the original image will determine crop alignment of the display images. Focal point is entered in the WP media page for the image.',
        // 	'default' 	=> true,
        // 	'name' 			=> 'gp-focal-point-align',
        // ),
        array(
            'label'   => __('Display original images', 'pzsp'),
            'id'      => $prefix . 'do_not_resize',
            'type'    => 'checkbox',
            'desc'    => __('The standard graphics software on web servers used for resizing images doesn\'t give perfectly sharp images when the quality is at 100%. If you need the highest quality images, resize them in Photoshop to fit the Slideshow and then check this option so they don\'t get resampled.', 'pzsp'),
            'default' => false,
        ),
        array(
            'label'   => __('Respect focal point', 'pzsp'),
            'id'      => $prefix . 'respect_focal_point',
            'type'    => 'checkbox',
            'desc'    => __('If selected and images used are in the WordPress Media Library, the focal point co-ordinates entered for the original image will determine crop alignment of the display images. Focal point is entered in the WP media page for the image.', 'pzsp'),
            'default' => true,
        ),
        array(
            'label'   => __('Image sizing type', 'pzsp'),
            'id'      => $prefix . 'sizing_type',
            'type'    => 'select',
            'options' => array(
                array('value' => 'auto', 'text' => 'Fit within width and height'),
                array('value' => 'crop', 'text' => 'Crop width and height to fit'),
                array('value' => 'exact', 'text' => 'Stretch to width and height (Warning: Can distort image)'),
                array('value' => 'landscape', 'text' => 'Crop width, match height'),
                array('value' => 'portrait', 'text' => 'Match width, crop height'),
            ),
            'default' => 'crop',
            'desc'    => __('When Feature is an image, choose how you want the image resized in respect of its width and height to the Display Image Height and Width.', 'pzsp'),
        ),
        array(
            'type'    => 'select',
            'options' => array(
                array('value' => 'centre', 'text' => 'Centre'),
                array('value' => 'top', 'text' => 'Top'),
                array('value' => 'topquarter', 'text' => 'Top quarter'),
                array('value' => 'bottomquarter', 'text' => 'Bottom quarter'),
                array('value' => 'bottom', 'text' => 'Bottom')
            ),
            'label'   => 'Vertical Crop Alignment',
            'desc'    => __('When Feature is an image, if the resized image is cropped, do you want to crop from the centre out, top down, top quarter down, bottom quarter up, or bottom up? <br/><strong><strong>Note:</strong></strong>Due to the shape of some images, and settings, changes between different vertical cropping settings may not be noticable.', 'pzsp'),
            'default' => 'centre',
            'id'      => $prefix . 'vert_crop_align'
        ),
        array(
            'type'    => 'select',
            'options' => array(
                array('value' => 'centre', 'text' => 'Centre'),
                array('value' => 'left', 'text' => 'Left'),
                array('value' => 'leftquarter', 'text' => 'Left quarter'),
                array('value' => 'rightquarter', 'text' => 'Right quarter'),
                array('value' => 'right', 'text' => 'Right')
            ),
            'label'   => 'Horizontal Crop Alignment',
            'desc'    => __('When Feature is an image, choose how you want to horizontally align the main image when it crops', 'pzsp'),
            'default' => 'centre',
            'id'      => $prefix . 'horz_crop_align'
        ),
        array(
            'label'   => __('Image background colour', 'pzsp'),
            'desc'    => __('When Feature is an image, choose a fill colour for when an image doesn\'t fully fill the frame.', 'pzsp'),
            'id'      => $prefix . 'image_fill',
            'type'    => 'colorpicker',
            'default' => '#ffffff'
        ),
        array(
            'label'   => __('Image quality', 'pzsp'),
            'desc'    => __('When Feature is an image, lower values will reduce the size of the file but also the clarity of the image. Higher values will make images slower loading.<br/>Value range is 0 to 100. If zero it will default to 70.', 'pzsp'),
            'id'      => $prefix . 'quality',
            'type'    => 'numeric',
            'default' => 70
        ),
        array(
            'label'   => __('Convert Featured Images to greyscale.', 'pzsp'),
            'desc'    => __('When Feature is an image, check this if you want to display the images as greyscale/black&white. This is a nice effect if you have a coloured theme.', 'pzsp'),
            'id'      => $prefix . 'greyscale',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Link Featured Image', 'pzsp'),
            'desc'    => __('When Feature is an image, if checked, clicking the image will take the reader to the post or page.<br/><br/><strong>Note:</strong> This does not apply to Slides. To link a slide, you must specify a destination URL in its settings.', 'pzsp'),
            'id'      => $prefix . 'link_image',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Centre embedded feature', 'pzsp'),
            'id'      => $prefix . 'centre_video',
            'type'    => 'checkbox',
            'desc'    => __('If the Feature is using an Embed URL, and per chance the embedded content (e.g. video, Flickr image) does not fully fill the area, check this box to make it centre.', 'pzsp'),
            'default' => true
        ),
    );

    /*	 * ***********************************************************************
     * ************************************************************************
     * Create Navigation settings meta box
     * ***********************************************************************
     * ********************************************************************** */
    $pzsp_nav_icons = array(
        array('value' => '1',
              'text'  => 'One
		<span aria-hidden="true" class="icon-arrow-left"></span>
		<span aria-hidden="true" class="icon-arrow-right"></span>
		'),
        array('value' => '2',
              'text'  => 'Two
		<span aria-hidden="true" class="icon-arrow-left-2"></span>
		<span aria-hidden="true" class="icon-arrow-right-2"></span>
		'),
        array('value' => '3',
              'text'  => 'Three
		<span aria-hidden="true" class="icon-arrow-left-3"></span>
		<span aria-hidden="true" class="icon-arrow-right-3"></span>
		'),
        array('value' => '4',
              'text'  => 'Four
		<span aria-hidden="true" class="icon-caret-left"></span>
		<span aria-hidden="true" class="icon-caret-right"></span>
		'),
        array('value' => '5',
              'text'  => 'Five
		<span aria-hidden="true" class="icon-arrow-left-5"></span>
		<span aria-hidden="true" class="icon-arrow-right-5"></span>
		'),
        array('value' => '6',
              'text'  => 'Six
		<span aria-hidden="true" class="icon-arrow-left-7"></span>
		<span aria-hidden="true" class="icon-arrow-right-7"></span>
		'),
        array('value' => '7',
              'text'  => 'Seven
		<span aria-hidden="true" class="icon-arrow-left-8"></span>
		<span aria-hidden="true" class="icon-arrow-right-8"></span>
		'),
        array('value' => '8',
              'text'  => 'Eight
		<span aria-hidden="true" class="icon-left"></span>
		<span aria-hidden="true" class="icon-right"></span>
		'),
        array('value' => '9',
              'text'  => 'Nine
		<span aria-hidden="true" class="icon-arrow-left-9"></span>
		<span aria-hidden="true" class="icon-arrow-right-9"></span>
		'),
        array('value' => '10',
              'text'  => 'Ten
		<span aria-hidden="true" class="icon-arrow-left-10"></span>
		<span aria-hidden="true" class="icon-arrow-right-10"></span>
		'),
        array('value' => '11',
              'text'  => 'Eleven
		<span aria-hidden="true" class="icon-arrow"></span>
		<span aria-hidden="true" class="icon-arrow-2"></span>
		'),
    );
    $pzsp_icon_list = '<div class="pzsp_icon_list">
		One: <span aria-hidden="true" class="icon-arrow-left"></span>
		<span aria-hidden="true" class="icon-arrow-right"></span>
		<br/>		
		Two: 
		<span aria-hidden="true" class="icon-arrow-left-2"></span>
		<span aria-hidden="true" class="icon-arrow-right-2"></span>
		<br/>		
		Three: 
		<span aria-hidden="true" class="icon-arrow-left-3"></span>
		<span aria-hidden="true" class="icon-arrow-right-3"></span>
		<br/>		
		Four: 
		<span aria-hidden="true" class="icon-caret-left"></span>
		<span aria-hidden="true" class="icon-caret-right"></span>
		<br/>		
		Five:
		<span aria-hidden="true" class="icon-arrow-left-5"></span>
		<span aria-hidden="true" class="icon-arrow-right-5"></span>
		<br/>		
		Six: 
		<span aria-hidden="true" class="icon-arrow-left-7"></span>
		<span aria-hidden="true" class="icon-arrow-right-7"></span>
		<br/>		
		Seven: 
		<span aria-hidden="true" class="icon-arrow-left-8"></span>
		<span aria-hidden="true" class="icon-arrow-right-8"></span>
		<br/>		
		Eight: 
		<span aria-hidden="true" class="icon-left"></span>
		<span aria-hidden="true" class="icon-right"></span>
		<br/>		
		Nine: 
		<span aria-hidden="true" class="icon-arrow-left-9"></span>
		<span aria-hidden="true" class="icon-arrow-right-9"></span>
		<br/>		
		Ten: <span aria-hidden="true" class="icon-arrow-left-10"></span>
		<span aria-hidden="true" class="icon-arrow-right-10"></span>
		<br/>		
		Eleven: <span aria-hidden="true" class="icon-arrow"></span>
		<span aria-hidden="true" class="icon-arrow-2"></span>
		<br/></div>';

    $pzsp_cpt_meta_boxes[ 'tabs' ][ $i++ ][ 'fields' ] = array(
        array('label'   => __('Navigation', 'pzsp'),
              'desc'    => __('', 'pzsp'),
              'id'      => $prefix . 'navigation_settings',
              'type'    => 'heading',
              'default' => '',
        ),
        array(
            'label'   => __('Navigation Type', 'pzsp'),
            'id'      => $prefix . 'nav_type',
            'type'    => 'select',
            'options' => array(
                array('value' => 'bullets', 'text' => __('Bullets', 'pzsp')),
                array('value' => 'squares', 'text' => __('Squares', 'pzsp')),
                array('value' => 'asterisks', 'text' => __('Asterisks', 'pzsp')),
                array('value' => 'text', 'text' => __('Titles', 'pzsp')),
                array('value' => 'numbers', 'text' => __('Numbers', 'pzsp')),
            ),
            'default' => 'text',
            'desc'    => __('Select the type of navigation you want to use.', 'pzsp'),
        ),
        array(
            'label'   => __('Navigation Inside', 'pzsp'),
            'desc'    => __('If enabled and navigation location is set to top or bottom, the navigation will appear over the top of the slide rather than above or below. For this to be effective, the navigation background is automatically set to transparent.', 'pzsp'),
            'id'      => $prefix . 'nav_inside',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Mouseover navigation', 'pzsp'),
            'desc'    => __('If enabled, the the slides will automatically change when the viewer places their mouse over each navigation item. If disabled, they will need to click to navigate.', 'pzsp'),
            'id'      => $prefix . 'nav_mouseover',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Hover navigation', 'pzsp'),
            'id'      => $prefix . 'hide_hover_nav',
            'type'    => 'select',
            'options' => array(
                array('value' => 'onhover', 'text' => 'On hover'),
                array('value' => 'never', 'text' => 'Never show'),
                array('value' => 'always', 'text' => 'Always show')
            ),
            'desc'    => __('When to show the forward and back navigation arrows over a slide.', 'pzsp'),
            'default' => 'onhover'
        ),
        array(
            'label'   => __('Hover nav icons', 'pzsp'),
            'id'      => $prefix . 'nav_icons',
            'type'    => 'select',
            'options' => $pzsp_nav_icons,
            'default' => '1',
            'desc'    => __('Select the navigation icons to use for the hover navigation.' . $pzsp_icon_list, 'pzsp'),
        ),
        array(
            'label'   => __('Custom hover navigation image: previous', 'pzsp'),
            'id'      => $prefix . 'custom_hover_nav_prev',
            'type'    => 'text',
            'desc'    => __('If you wish to use custom hover navigation images, enter the URLs. Note: Maximum size is 64px x 64px', 'pzsp'),
            'default' => ''
        ),
        array(
            'label'   => __('Custom hover navigation image: next', 'pzsp'),
            'id'      => $prefix . 'custom_hover_nav_next',
            'type'    => 'text',
            'desc'    => __('If you wish to use custom hover navigation images, enter the URLs. Note: Maximum size is 64px x 64px', 'pzsp'),
            'default' => ''
        ),
        array(
            'label'   => __('Horizontal Navigation Alignment', 'pzsp'),
            'id'      => $prefix . 'nav_align',
            'type'    => 'select',
            'options' => array(
                array('value' => 'lefttop', 'text' => __('Left', 'pzsp')),
                array('value' => 'centre', 'text' => __('Centre', 'pzsp')),
                array('value' => 'rightbottom', 'text' => __('Right', 'pzsp')),
            ),
            'default' => 'centre',
            'desc'    => __('Select the alignment for the navigation when location is top or bottom.', 'pzsp'),
        ),
        array(
            'label'   => __('Vertical Navigation Width (%)', 'pzsp'),
            'id'      => $prefix . 'nav_width',
            'type'    => 'numeric',
            'default' => '20',
            'desc'    => __('Enter the width of the navigation as a percentage of the width of the Slideshow when the navigation location is left or right.
								<p>With vertical navigation you would never want this to be 100!</p>', 'pzsp'),
        ),
        array(
            'label'   => __('Navigation item colour override', 'pzsp'),
            'desc'    => __('Choose a colour for the navigation items. This will override the theme\'s colour. Set to "none" to use theme colour', 'pzsp'),
            'id'      => $prefix . 'nav_item_colour_over',
            'type'    => 'colorpicker',
            'default' => 'none'
        ),
        array(
            'label'   => __('Navigation selected item colour override', 'pzsp'),
            'desc'    => __('Choose a colour for the selected navigation item. This will override the theme\'s colour. Set to "none" to use theme colour', 'pzsp'),
            'id'      => $prefix . 'nav_selected_item_colour_over',
            'type'    => 'colorpicker',
            'default' => 'none'
        ),
        array(
            'label'   => __('Navigation item hover colour override', 'pzsp'),
            'desc'    => __('Choose a colour for the navigation items when they are hovered over. This will override the theme\'s colour. Set to "none" to use theme colour', 'pzsp'),
            'id'      => $prefix . 'nav_hover_item_colour_over',
            'type'    => 'colorpicker',
            'default' => 'none'
        ),
        array(
            'label'   => __('Hover-nav colour', 'pzsp'),
            'desc'    => __('Choose a colour for the hover navigation arrows. This will override the theme\'s colour. Set to "none" to use theme colour', 'pzsp'),
            'id'      => $prefix . 'hover_nav_colour',
            'type'    => 'colorpicker',
            'default' => '#fff'
        ),
        array(
            'label'   => __('Hover-nav secondary colour', 'pzsp'),
            'desc'    => __('Choose a secondary colour for the hover navigation arrows. This will override the theme\'s colour. Set to "none" to use theme colour', 'pzsp'),
            'id'      => $prefix . 'hover_nav_colour_secondary',
            'type'    => 'colorpicker',
            'default' => '#555'
        ),
        // Thought about having navigationbuttons, but then it starts to become a gallery
    );
    /*	 * ***********************************************************************
     * ************************************************************************
     * Transitions
     * ************************************************************************
     * *********************************************************************** */
    $pzsp_cpt_meta_boxes[ 'tabs' ][ $i++ ][ 'fields' ] = array(
        array('label'   => __('Transitions', 'pzsp'),
              'desc'    => __('', 'pzsp'),
              'id'      => $prefix . 'transitions_Settings',
              'type'    => 'heading',
              'default' => '',
        ),
        array(
            'label'   => __('Transition Effect', 'pzsp'),
            'id'      => $prefix . 'trans_type',
            'type'    => 'select',
            'default' => 'fade',
            'options' => array(
                array('value' => 'scrollHorz', 'text' => __('Scroll left/right', 'pzsp')),
                array('value' => 'scrollVert', 'text' => __('Scroll up/down', 'pzsp')),
                array('value' => 'fade', 'text' => __('Fade', 'pzsp')),
                array('value' => 'fadeout', 'text' => __('Fade Out', 'pzsp')),
                array('value' => 'tileSlide', 'text' => __('Tile Slide Vertical', 'pzsp')),
                array('value' => 'tileBlind', 'text' => __('Tile Blind Vertical', 'pzsp')),
                array('value' => 'none', 'text' => __('None', 'pzsp')),
            ),
            'desc'    => __('Select the transition(s) you want to use.<br/> <strong>The transitions and easing demos will return in a future update</strong>', 'pzsp'),
            'default' => 'scrollHorz'
        ),
        array(
            'label'   => __('Transition Easing In', 'pzsp'),
            'id'      => $prefix . 'trans_ease_in',
            'type'    => 'select',
            'options' => array(
                array('value' => '', 'text' => __('Normal', 'pzsp')),
                array('value' => 'easeInBounce', 'text' => __('Bounce', 'pzsp')),
                array('value' => 'easeInElastic', 'text' => __('Elastic', 'pzsp')),
                array('value' => 'easeInCirc', 'text' => __('Circ', 'pzsp')),
                array('value' => 'easeInCubit', 'text' => __('Cubit', 'pzsp')),
                array('value' => 'easeInExpo', 'text' => __('Expo', 'pzsp')),
                array('value' => 'easeInQuad', 'text' => __('Quad', 'pzsp')),
                array('value' => 'easeInQuart', 'text' => __('Quart', 'pzsp')),
                array('value' => 'easeInQuint', 'text' => __('Quint', 'pzsp')),
            ),
            'desc'    => __('Select the transition easing you want to use, that is, when the slide is being shown.<br/> <strong>The transitions and easing demos will return in a future update</strong><br/>
	            	<br/>Note: we\'re not responsible for these unusual names!', 'pzsp'),
            'default' => ''
        ),
        // 'help' => '
        // 			<div style="border:#ddd solid 1px;border-radius:5px;background:#fafafa;padding:10px;margin:10px;" >
        // 				<p>Select from these dropdowns to see demonstrations. Note, some of the easing differences may appear quite subtle.</p>
        // 				<form class="pzsp_trans_demo_nav">
        // 				<strong>Effects:</strong>
        // 					<select name="pzsp_trans_demo_options" id="pzsp_trans_demo_options">
        // 						<option value="none" selected="selected">none</option>
        // 						<option value="fade">fade</option>
        // 						<option value="fadeOut">fadeOut</option>
        // 						<option value="scrollHorz">Scroll horizontal</option>
        // 						<option value="scrollVert">Scroll vertical</option>
        // 						<option value="tileSlide">Tile Slide</option>
        // 						<option value="titleBlind">Title Blind</option>
        // 					</select>
        // 					&nbsp;<strong>Easing:</strong>
        // 					<select name="pzsp_trans_demo_easing" id="pzsp_trans_demo_easing">
        // 						<option value="" selected="selected">Normal</option>
        // 						<option value="easeInOutBounce">Bounce</option>
        // 						<option value="easeInOutElastic">Elastic</option>
        // 						<option value="easeInOutCirc">Circ</option>
        // 						<option value="easeInOutCubic">Cubic</option>
        // 						<option value="easeInOutExpo">Expo</option>
        // 						<option value="easeInOutQuad">Quad</option>
        // 						<option value="easeInOutQuart">Quart</option>
        // 						<option value="easeInOutQuint">Quint</option>
        // 					</select>
        // 				</form>
        // 				<div id="pzsp_trans_demo" class="cycle-slideshow" style="overflow:hidden!important;background:#333;margin-top:5px">
        // 					<img src="'.PZSP_PLUGIN_URL.'/images/promos/excerptsplus-small.jpg" width="255" height="148"/>
        // 					<img src="'.PZSP_PLUGIN_URL.'/images/promos/galleryplus-small.jpg" width="255" height="148"/>
        // 					<img src="'.PZSP_PLUGIN_URL.'/images/promos/headerplus-small.jpg" width="255" height="148"/>
        // 					<img src="'.PZSP_PLUGIN_URL.'/images/promos/tabsplus-small.jpg" width="255" height="148"/>
        // 					<img src="'.PZSP_PLUGIN_URL.'/images/promos/sliderplus-small.jpg" width="255" height="148"/>
        // 				</div>
        // 			</div>',
        //			'default' => ''
        //	),
        // array(
        //     'label' => __('Randomize','pzsp'),
        //     'desc' => __('If enabled, the selected transition effect will be used in a random order.','pzsp'),
        //     'id' => $prefix . 'trans_rand',
        //     'type' => 'checkbox',
        //     'default' => true
        // ),
        array(
            'label'   => __('Synchronize transitions', 'pzsp'),
            'desc'    => __('If enabled, both the current and next slide will transition at the same time.', 'pzsp'),
            'id'      => $prefix . 'trans_sync',
            'type'    => 'checkbox',
            'default' => true
        ),
        // array(
        //     'label' => __('Loop slideshow','pzsp'),
        //     'desc' => __('If enabled, the slideshow will automatically go back to the first slide after the last.','pzsp'),
        //     'id' => $prefix . 'loop_slideshow',
        //     'type' => 'checkbox',
        //     'default' => false
        // ),
        array(
            'label'   => __('Allow transition interrupt', 'pzsp'),
            'desc'    => __('If enabled, clicking another navigation item will immediately trigger a switch to that slide.<br/><br/> <strong>Note:</strong>Tranistions that resize, such as BlindX, FadeZoom etc, can get scrambled if interrupted. It\'s not recommended to have this setting enabled if you are using any of the resizing transition effects.', 'pzsp'),
            'id'      => $prefix . 'trans_interrupt',
            'type'    => 'checkbox',
            'default' => true
        ),
        array(
            'label'   => __('Transition duration', 'pzsp'),
            'desc'    => __('Enter a time in seconds for the duration of the transition ', 'pzsp'),
            'id'      => $prefix . 'trans_duration',
            'type'    => 'numeric',
            'default' => 2
        ),
        array(
            'label'   => __('Slide display duration', 'pzsp'),
            'desc'    => __('Enter a time in seconds for the duration of the display of each slide.<br/><br/>
	            	Set this to zero to prevent auto running Slideshows or greater than zero for automatically running Slideshows. ', 'pzsp'),
            'id'      => $prefix . 'trans_display',
            'type'    => 'numeric',
            'help'    => 'Set to greater than zero for an automatic slideshow.',
            'default' => 0
        ),
        array(
            'label'   => __('Disable pause on hover', 'pzsp'),
            'desc'    => __('Check this to prevent hovering the mouse over the slideshow pausing it if it is auto-running.', 'pzsp'),
            'id'      => $prefix . 'pause_on_hover',
            'type'    => 'checkbox',
            'default' => false
        ),
    );

    /*	 * ***********************************************************************
     * ************************************************************************
     * Styling
     * ************************************************************************
     * *********************************************************************** */

    $pzsp_cpt_meta_boxes[ 'tabs' ][ $i++ ][ 'fields' ] = array(
        array('label'   => __('Styling', 'pzsp'),
              'desc'    => __('', 'pzsp'),
              'id'      => $prefix . 'styling_settings',
              'type'    => 'heading',
              'default' => '',
        ),
        array(
            'label'   => __('Theme', 'pzsp'),
            'id'      => $prefix . 'theme',
            'type'    => 'select',
            'options' => array(
                array('value' => 'light', 'text' => 'Light'),
                array('value' => 'grey', 'text' => 'Grey'),
                array('value' => 'dark', 'text' => 'Dark'),
                array('value' => 'skyblue', 'text' => 'Sky Blue'),
                array('value' => 'glossyblue', 'text' => 'Glossy Blue'),
                array('value' => 'rainbow', 'text' => 'Rainbow'),
                array('value' => 'none', 'text' => 'None'),
            ),
            'default' => 'dark',
            'desc'    => __('Choose a styling theme for this Slideshow. You can further customize the styles with standard CSS in the CSS tab.', 'pzsp'),
        ),
        array(
            'label'   => __('Padding size (px)', 'pzsp'),
            'desc'    => __('Set the size of the padding you want around the Slideshow. 0 for none. <br/><br/><strong>Note:</strong> This will be <em>subtracted</em> from the height and width of the Slideshow as necessary. That is, the contents of your Slideshow will be this much smaller', 'pzsp'),
            'id'      => $prefix . 'padding_size',
            'type'    => 'numeric',
            'default' => 0
        ),
        array(
            'label'   => __('Padding colour', 'pzsp'),
            'desc'    => __('Choose a colour for padded area. Use "transparent" or "none" for no colour.', 'pzsp'),
            'id'      => $prefix . 'padding_colour',
            'type'    => 'colorpicker',
            'default' => '#ffffff'
        ),
        array(
            'label'   => __('Border size (px)', 'pzsp'),
            'desc'    => __('Set the size in pixels of the border edge of the entire Slideshow.<br/>This is most useful for creating a photo-like frame. <br/>E.g. Set the padding to 5px, its colour to #ffff, the border to 1px and it\'s colour to a very pale grey, such as #f7f7f7. <br/><br/> <strong>Note:</strong> This will be <em>subtracted</em> from the height and width of the Slideshow as necessary.  That is, the contents of your Slideshow will be this much smaller', 'pzsp'),
            'id'      => $prefix . 'border_size',
            'type'    => 'numeric',
            'default' => 0
        ),
        array(
            'label'   => __('Border colour', 'pzsp'),
            'desc'    => __('Choose a colour for border. Use none for no border.', 'pzsp'),
            'id'      => $prefix . 'border_colour',
            'type'    => 'colorpicker',
            'default' => 'none'
        ),
        array(
            'label'   => __('Drop shadow', 'pzsp'),
            'id'      => $prefix . 'shadows',
            'type'    => 'select',
            'options' => array(
                array('value' => 'none', 'text' => 'No drop shadow'),
                array('value' => 'shadows_01.png', 'text' => 'Shadow 01'),
                array('value' => 'shadows_02.png', 'text' => 'Shadow 02'),
                array('value' => 'shadows_03.png', 'text' => 'Shadow 03'),
                array('value' => 'shadows_04.png', 'text' => 'Shadow 04'),
                array('value' => 'shadows_05.png', 'text' => 'Shadow 05'),
                array('value' => 'shadows_06.png', 'text' => 'Shadow 06'),
                array('value' => 'shadows_07.png', 'text' => 'Shadow 07'),
                array('value' => 'shadows_08.png', 'text' => 'Shadow 08'),
                array('value' => 'shadows_09.png', 'text' => 'Shadow 09'),
                array('value' => 'shadows_10.png', 'text' => 'Shadow 10'),
                array('value' => 'shadows_11.png', 'text' => 'Shadow 11'),
                array('value' => 'shadows_12.png', 'text' => 'Shadow 12'),
                array('value' => 'shadows_13.png', 'text' => 'Shadow 13'),
                array('value' => 'shadows_14.png', 'text' => 'Shadow 14'),
                array('value' => 'shadows_15.png', 'text' => 'Shadow 15'),
                array('value' => 'shadows_16.png', 'text' => 'Shadow 16'),
                array('value' => 'shadows_17.png', 'text' => 'Shadow 17'),
                array('value' => 'shadows_18.png', 'text' => 'Shadow 18'),
            ),
            'default' => 'shadows_12.png',
            'desc'    => __('Choose a shadow for this Slideshow.', 'pzsp') . '<img src="' . PZSP_PLUGIN_URL . '/css/images/Slider_Shadows.jpg"/>',
        ),
        array(
            'label'   => __('Drop shadow location', 'pzsp'),
            'id'      => $prefix . 'shadow_location',
            'type'    => 'select',
            'options' => array(
                array('value' => 'slider', 'text' => 'Below Slideshow'),
                array('value' => 'contents', 'text' => 'Below contents'),
            ),
            'default' => 'slider',
            'desc'    => __('Select where you want the shadow to appear. This only really affects when the navigation is at the bottom, as then, below the Slideshow will put the shadow below the navigation.', 'pzsp')
        ),
        array(
            'label'   => __('Drop shadow background colour', 'pzsp'),
            'desc'    => __('Choose a colour for shadow background. Use "transparent" or "none" for no colour.', 'pzsp'),
            'id'      => $prefix . 'shadow_bgcolour',
            'type'    => 'colorpicker',
            'default' => 'transparent'
        ),
        array(
            'label'   => __('Override navigation background', 'pzsp'),
            'desc'    => __('Override the navigation background colour with the drop shadow background colour.', 'pzsp'),
            'id'      => $prefix . 'nav_override_bgcolour',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Border shadow', 'pzsp'),
            'desc'    => __('Add a border shadow to the entire Slideshow. Enabling this will prevent the display of the drop shadow.', 'pzsp'),
            'id'      => $prefix . 'border_shadow',
            'type'    => 'select',
            'default' => 'none',
            'options' => array(
                array('value' => 'none', 'text' => 'None'),
                array('value' => 'slider', 'text' => 'Yes'),
            )
        ),
        array(
            'label'   => __('Bottom margin (px)', 'pzsp'),
            'desc'    => __('Enter a height in pixels for the margin at the bottom of the Slideshow', 'pzsp'),
            'id'      => $prefix . 'bottom_margin',
            'type'    => 'numeric',
            'default' => '20'
        ),
    );
    /*	 * **********************************************************************
     * ************************************************************************
     * Responsive
     * ************************************************************************
     * ********************************************************************** */
    $pzsp_cpt_meta_boxes[ 'tabs' ][ $i++ ][ 'fields' ] = array(
        array('label'   => __('Responsive Overrides', 'pzsp'),
              'desc'    => __('', 'pzsp'),
              'id'      => $prefix . 'responsive_settings',
              'type'    => 'heading',
              'default' => '',
        ),
        array(
            'label'   => __('Phone breakpoint', 'pzsp'),
            'desc'    => __('Enter a width in pixels for the maximum width of phone displays. Default: 720', 'pzsp'),
            'id'      => $prefix . 'phone-breakpoint',
            'type'    => 'numeric',
            'default' => 720
        ),
        array(
            'label'   => __('Tablet breakpoint', 'pzsp'),
            'desc'    => __('Enter a width in pixels for the maximum width of tablet displays. Desktops  will be set to anything above this. Default: 1024', 'pzsp'),
            'id'      => $prefix . 'tablet-breakpoint',
            'type'    => 'numeric',
            'default' => 1024
        ),
        array(
            'label'   => __('Use bullets for navigation instead of titles on phones', 'pzsp'),
            'id'      => $prefix . 'nav_type_phone_bullets',
            'type'    => 'checkbox',
            'default' => true,
            'desc'    => __('If enabled, if navigation type is titles, they will be replaced with bullets when browsed from phone sized devices.', 'pzsp'),
        ),
        array(
            'label'   => __('Enable responsive stylings', 'pzsp'),
            'desc'    => __('Enable responsive settings.', 'pzsp'),
            'id'      => $prefix . 'enable-responsive-text',
            'type'    => 'checkbox',
            'default' => false
        ),
        array(
            'label'   => __('Title CSS - desktop', 'pzsp'),
            'desc'    => __('Enter CSS declarations for titles when viewed on a desktop and landscape tablet.', 'pzsp'),
            'id'      => $prefix . 'desktop-title-css',
            'type'    => 'text',
            'default' => 'font-size: 20px'
        ),
        array(
            'label'   => __('Title CSS  - tablet', 'pzsp'),
            'desc'    => __('Enter CSS declarations for titles when viewed on a tablet.', 'pzsp'),
            'id'      => $prefix . 'tablet-title-css',
            'type'    => 'text',
            'default' => 'font-size: 18px'
        ),
        array(
            'label'   => __('Title CSS - phone', 'pzsp'),
            'desc'    => __('Enter CSS declarations for titles when viewed on a phone.', 'pzsp'),
            'id'      => $prefix . 'phone-title-css',
            'type'    => 'text',
            'default' => 'font-size: 16px'
        ),
        array(
            'label'   => __('Body CSS - desktop', 'pzsp'),
            'desc'    => __('Enter CSS declarations for body text when viewed on a desktop', 'pzsp'),
            'id'      => $prefix . 'desktop-body-css',
            'type'    => 'text',
            'default' => 'font-size: 14px'
        ),
        array(
            'label'   => __('Body CSS - tablet', 'pzsp'),
            'desc'    => __('Enter CSS declarations for body text when viewed on a tablet', 'pzsp'),
            'id'      => $prefix . 'tablet-body-css',
            'type'    => 'text',
            'default' => 'font-size: 13px'
        ),
        array(
            'label'   => __('Body CSS - phone', 'pzsp'),
            'desc'    => __('Enter CSS declarations for body text when viewed on a phone', 'pzsp'),
            'id'      => $prefix . 'phone-body-css',
            'type'    => 'text',
            'default' => 'font-size:12px;'
        ),

    );
    /*	 * ***********************************************************************
     * ***********************************************************************
     * Custom CSS
     * ***********************************************************************
     * ********************************************************************** */
    $pzsp_cpt_meta_boxes[ 'tabs' ][ $i++ ][ 'fields' ] = array(
        array('label'   => __('Custom CSS', 'pzsp'),
              'desc'    => __('', 'pzsp'),
              'id'      => $prefix . 'customcss_settings',
              'type'    => 'heading',
              'default' => '',
        ),
        array(
            'label'   => __('Slideshow title declarations', 'pzsp'),
            'desc'    => __('Any CSS declarations you enter here will style the selector ', 'pzsp') . '<em>.pzsp-slideshow-title</em> <br/><br/><strong>Note:</strong> These are just the declarations part of the CSS. E,g, background-color:#f00;',
            'id'      => $prefix . 'slideshow_title_css',
            'type'    => 'textarea-small',
            'default' => ''
        ),
        array(
            'label'   => __('Content background declarations', 'pzsp'),
            'desc'    => __('Any CSS declarations you enter here will style the selector ', 'pzsp') . '<em>.pzsp-content-container .is-text</em> <br/><br/><strong>Note:</strong> These are just the declarations part of the CSS. E,g, background-color:#f00;<br/><br/>Also, the only ones that will have an effect on this selector are things like background and borders, and you will need to add <strong>!important</strong> to the background color declarations to override the inbuilt code.<br/><br/>Setting a background colour will also override the background transparency set on the text options panel, so you will need to use rgba if you want transparency.',
            'id'      => $prefix . 'content_background',
            'type'    => 'textarea-small',
            'default' => ''
        ),
        array(
            'label'   => __('Content title declarations', 'pzsp'),
            'desc'    => __('Any CSS declarations you enter here will style the selector ', 'pzsp') . '<em>.pzsp-text-content h2.pzsp-entry-title</em> and <em>.pzsp-text-content h2.pzsp-entry-title a</em><br/><br/><strong>Note:</strong> These are just the declarations part of the CSS. E,g, background-color:#f00;border:1px solid #555',
            'id'      => $prefix . 'content_h2_css',
            'type'    => 'textarea-small',
            'default' => ''
        ),
        array(
            'label'   => __('Content body declarations', 'pzsp'),
            'desc'    => __('Any CSS declarations you enter here will style the selector ', 'pzsp') . '<em>.pzsp-text-content .pzsp-entry-body </em> <br/><br/><strong>Note:</strong> These are just the declarations part of the CSS. E,g, background-color:#f00;border:1px solid #555',
            'id'      => $prefix . 'content_css',
            'type'    => 'textarea-small',
            'default' => ''
        ),
        array(
            'label'   => __('Content body links declarations', 'pzsp'),
            'desc'    => __('Any CSS declarations you enter here will style the selector ', 'pzsp') . '<em>.pzsp-text-content .pzsp-entry-body a</em> <br/><br/><strong>Note:</strong> These are just the declarations part of the CSS. E,g, background-color:#f00;border:1px solid #555',
            'id'      => $prefix . 'content_link_css',
            'type'    => 'textarea-small',
            'default' => ''
        ),
        array(
            'label'   => __('Content body H3 declarations', 'pzsp'),
            'desc'    => __('Any CSS declarations you enter here will style the selector ', 'pzsp') . '<em>.pzsp-text-content .pzsp-entry-body h3</em> and <em>.pzsp-text-content .pzsp-entry-body h3 a</em><br/><br/><strong>Note:</strong> These are just the declarations part of the CSS. E,g, background-color:#f00;border:1px solid #555',
            'id'      => $prefix . 'content_h3_css',
            'type'    => 'textarea-small',
            'default' => ''
        ),
        /* 				array(
          'label' => __('Navigation declarations','pzsp'),
          'desc' => __('Any CSS declarations you enter here will style the selector ','pzsp').'<em>.pzsp-</em><br/><br/><strong>Note:</strong> These are just the declarations part of the CSS. E,g, background-color:#f00;border:1px solid #555',
          'id' => $prefix . 'navigation_css',
          'type' => 'textarea-small',
          'default' => ''
          ),
          array(
          'label' => __('Navigation button declarations','pzsp'),
          'desc' => __('Any CSS declarations you enter here will style the selector ','pzsp').'<em>.pzsp-</em><br/><br/><strong>Note:</strong> These are just the declarations part of the CSS. E,g, background-color:#f00;border:1px solid #555',
          'id' => $prefix . 'navigation_button_css',
          'type' => 'textarea-small',
          'default' => ''
          ),
          array(
          'label' => __('Navigation button hover declarations','pzsp'),
          'desc' => __('Any CSS declarations you enter here will style the selector ','pzsp').'<em>.pzsp-</em><br/><br/><strong>Note:</strong> These are just the declarations part of the CSS. E,g, background-color:#f00;border:1px solid #555',
          'id' => $prefix . 'navigation_button_hover_css',
          'type' => 'textarea-small',
          'default' => ''
          ),
         */
        array(
            'label'   => __('Custom CSS file URL', 'pzsp'),
            'desc'    => __('Alternatively, you can create a custom css theme file. Enter its URL here.<br><br/>
						<strong>Note:</strong> It is loaded after other CSS on this page, so its settings may override anything entered here.<br>
						However, avoid using generic selectors as other CSS may override them or you may inadvertently style selectors in other Slideshows. 
						Instead, target the specific Slideshow. e.g. #pzsp-myfirstslider .h2 {color:green;} '),
            'id'      => $prefix . 'custom_theme_url',
            'type'    => 'textarea-small',
            'default' => ''
        ),
        array(
            'label'   => __('Custom CSS', 'pzsp'),
            'desc'    => __('You also may enter custom CSS directly here.<br/><br/><strong>Note:</strong> Unlike the above which are just declarations, you need to include the full selectors here. Always uniquely identify the Slideshow by its short name so styling doesn\'t apply to all Slideshows.
	eg. div#pzsp-myfirstslider ...', 'pzsp'),
            'id'      => $prefix . 'custom_css',
            'type'    => 'textarea-large',
            'default' => ''
        ),
    );
  }

// Make this only load once - probably loads all the time at the moment
// Hmmm. This doesn't seem to even bbe being used.
  function pzsp_slider_defaults($just_defaults = false)
  {
    if ($just_defaults)
    {
      return false;
    }
    global $pzsp_cpt_meta_boxes;
    $pzsp_slider_defaults = array();
    pzsp_populate_slider_options($just_defaults);
    foreach ($pzsp_cpt_meta_boxes[ 'tabs' ] as $pzsp_meta_box)
    {
      foreach ($pzsp_meta_box[ 'fields' ] as $pzsp_field)
      {
        if (!isset($pzsp_field[ 'id' ]))
        {
          $pzsp_slider_defaults[ $pzsp_field[ 'id' ] ] = (isset($pzsp_field[ 'default' ]) ? $pzsp_field[ 'default' ] : null);
        }
      }
    }

    return $pzsp_slider_defaults;
  }

