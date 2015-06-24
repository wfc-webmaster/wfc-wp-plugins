<?php
  if (!post_type_exists('gp_gallery') && !function_exists('pz_create_galleries_post_type')) {
    add_action('init', 'pz_create_galleries_post_type');
    function pz_create_galleries_post_type()
    {
      $labels = array(
          'name'               => _x('Galleries', 'post type general name'),
          'singular_name'      => _x('Gallery', 'post type singular name'),
          'add_new'            => _x('Add New Gallery', 'gallery'),
          'add_new_item'       => __('Add New Gallery'),
          'edit_item'          => __('Edit Gallery'),
          'new_item'           => __('New Gallery'),
          'view_item'          => __('View Gallery'),
          'search_items'       => __('Search Gallerys'),
          'not_found'          => __('No gallerys found'),
          'not_found_in_trash' => __('No gallerys found in Trash'),
          'parent_item_colon'  => '',
          'menu_name'          => _x('Galleries', 'pzarc-galleries'),
      );
      $args   = array(
          'labels'             => $labels,
          'public'             => false,
          'publicly_queryable' => false,
          'show_ui'            => true,
          'show_in_menu'       => 'pizazzwp',
          'query_var'          => true,
          'rewrite'            => true,
          'capability_type'    => 'post',
          'has_archive'        => true,
          'hierarchical'       => false,
          'menu_position'      => 999,
          'supports'           => array('title', 'editor', 'excerpt')
      );


      register_post_type('gp_gallery', $args);

    }
  }

  if (!post_type_exists('pzsp-slides') && !function_exists('pz_create_slides_post_type')) {
    add_action('init', 'pz_create_slides_post_type');
    function pz_create_slides_post_type()
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
          'public'               => false,
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


    }
  }


  $opt_val = get_option('pizazz_options');
  if (!empty($opt_val[ 'val_show_pzwp_snippets' ]) && !post_type_exists('pz_snippets') && !function_exists('pz_create_snippets_post_type')) {
    add_action('init', 'pz_create_snippets_post_type');
    function pz_create_snippets_post_type()
    {
      $labels = array(
          'name'               => _x('Snippets', 'post type general name'),
          'singular_name'      => _x('Snippet', 'post type singular name'),
          'add_new'            => _x('Add New Snippet', 'gallery'),
          'add_new_item'       => __('Add New Snippet'),
          'edit_item'          => __('Edit Snippet'),
          'new_item'           => __('New Snippet'),
          'view_item'          => __('View Snippet'),
          'search_items'       => __('Search Snippets'),
          'not_found'          => __('No snippets found'),
          'not_found_in_trash' => __('No snippets found in Trash'),
          'parent_item_colon'  => '',
          'menu_name'          => _x('Snippets', 'pzarchitect'),
      );
      $args   = array(
          'labels'             => $labels,
          'public'             => true,
          'publicly_queryable' => true,
          'show_ui'            => true,
          //          'show_in_menu'       => 'pzarc',
          'menu_icon'          => 'dashicons-format-aside',
          'query_var'          => true,
          'rewrite'            => true,
          'capability_type'    => 'page',
          'has_archive'        => true,
          'hierarchical'       => true,
          'taxonomies'         => array('category', 'post_tag'),
          //          'menu_position'      => 999,
          'supports'           => array('title',
                                        'editor',
                                        'author',
                                        'thumbnail',
                                        'excerpt',
                                        'comments',
                                        'revisions',
                                        'post-formats',
                                        'page-attributes')
      );


      register_post_type('pz_snippets', $args);

      // Create custom category taxonomy for Snippets
      $labels = array(
          'name' => _x( 'Snippet categories', 'taxonomy general name' ),
          'singular_name' => _x( 'Snippet category', 'taxonomy singular name' ),
          'search_items' =>  __( 'Search Snippet categories' ),
          'all_items' => __( 'All Snippet categories' ),
          'parent_item' => __( 'Parent Snippet category' ),
          'parent_item_colon' => __( 'Parent Snippet category:' ),
          'edit_item' => __( 'Edit Snippet category' ),
          'update_item' => __( 'Update Snippet category' ),
          'add_new_item' => __( 'Add New Snippet category' ),
          'new_item_name' => __( 'New Snippet category name' ),
          'menu_name' => __( 'Snippet Categories' ),
      );

      register_taxonomy('pz_snippet_cat',
                        array('pz_snippets'),
                        array(
                            'hierarchical' => true,
                            'labels' => $labels,
                            'show_ui' => true,
                            'query_var' => true,
                            'rewrite' => array( 'slug' => 'pzsnippetscat' ),
                        )
      );

      // Create custom category taxonomy for Snippets
      $labels = array(
          'name' => _x( 'Snippet tags', 'taxonomy general name' ),
          'singular_name' => _x( 'Snippet tag', 'taxonomy singular name' ),
          'search_items' =>  __( 'Search Snippet tags' ),
          'all_items' => __( 'All Snippet tags' ),
          'parent_item' => __( 'Parent Snippet tag' ),
          'parent_item_colon' => __( 'Parent Snippet tag:' ),
          'edit_item' => __( 'Edit Snippet tag' ),
          'update_item' => __( 'Update Snippet tag' ),
          'add_new_item' => __( 'Add New Snippet tag' ),
          'new_item_name' => __( 'New Snippet tag name' ),
          'menu_name' => __( 'Snippet Tags' ),
      );

      register_taxonomy('pz_snippet_tag',
                        array('pz_snippets'),
                        array(
                            'hierarchical' => false,
                            'labels' => $labels,
                            'show_ui' => true,
                            'query_var' => true,
                            'rewrite' => array( 'slug' => 'pzsnippetstag' ),
                        )
      );

    }
  }

