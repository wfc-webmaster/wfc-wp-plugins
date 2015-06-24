<?php

/*
Create the G+ Albums using custom post types
*/
if (!post_type_exists('gp_gallery')) {
	add_action('init', 'pzsp_create_gallery_post_type');
}
function pzsp_create_gallery_post_type() 
{
  $labels = array(
    'name' => _x('Galleries', 'post type general name'),
    'singular_name' => _x('Gallery', 'post type singular name'),
    'add_new' => _x('Add New Gallery', 'gallery'),
    'add_new_item' => __('Add New Gallery'),
    'edit_item' => __('Edit Gallery'),
    'new_item' => __('New Gallery'),
    'view_item' => __('View Gallery'),
    'search_items' => __('Search Gallerys'),
    'not_found' =>  __('No gallerys found'),
    'not_found_in_trash' => __('No gallerys found in Trash'), 
    'parent_item_colon' => ''

  );
  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => true, 
    'show_in_menu' => 'pizazzwp', 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => 999,
    'supports' => array('title','editor')
  ); 


  register_post_type('gp_gallery',$args);
	
}
