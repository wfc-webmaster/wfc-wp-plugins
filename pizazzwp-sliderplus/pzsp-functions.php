<?php

  /* This is a place to keep global functions. They can be either in the class or out of it.
    If in, they will need to be called with pzspFuncs:function_name($param)

    PHP 5.3 supports namespace which will make this even easier
   */

  function pzsp_get_categories() {
// Grabs all WP categories to an array, and adds a first option of All
// You will need to wrangle your own code to make use of the All
    $categories_select_query = get_categories();
//	$categories_array = array('all'=>'All');
    $categories_array = array();
    foreach ( $categories_select_query as $category ) {
      $categories_array[ ] = array( 'value' => $category->cat_ID, 'text' => $category->cat_name );
    }

    return $categories_array;
  }

  function pzsp_get_tags() {
// Grabs all WP categories to an array, and adds a first option of All
// You will need to wrangle your own code to make use of the All
    $tags_select_query = get_tags();
//	$tags_array = array('all'=>'All');
//	var_dump($tags_select_query);
    $tags_array = array();
    foreach ( $tags_select_query as $tag ) {
      $tags_array[ ] = array( 'value' => $tag->term_id, 'text' => $tag->name );
    }

//	var_dump($tags_array);
    return $tags_array;
  }

  function pzsp_get_taxonomies( $filter = null, $limitto = 'exclude' ) {
    $custom_tax        = get_taxonomies();
    $pzsp_exclude_list = array( 'nav_menu', 'link_category', 'post_format', 'category', 'post_tag' );

    if ( $filter && $limitto == 'exclude' ) {
      $pzsp_exclude_list[ ] = $filter;
    }
    $tax_array = array();
    foreach ( $custom_tax as $tax ) {
      switch ( $limitto ) {
        case 'only':
          if ( $tax == $filter ) {
            $pz_terms = get_terms( $tax );
            foreach ( $pz_terms as $pz_term ) {
              $tax_array[ $pz_term->slug ] = $pz_term->name;
            }
            break 2;
          }
          break;
        case 'exclude':
          if ( ! in_array( $tax, $pzsp_exclude_list ) ) {
            $tax_name                        = ( $tax == 'post_tag' ) ? 'Tags' : ucwords( $tax );
            $tax_array[ 'tax-' . $tax_name ] = '>' . $tax_name;
            $pz_terms                        = get_terms( $tax );
            foreach ( $pz_terms as $pz_term ) {
              $tax_array[ $pz_term->slug ] = $pz_term->name;
            }
          }
          break;
      }
    }

    return $tax_array;
  }

  function pzsp_get_sliders( $pzsp_inc_width ) {
    $query_options = array(
      'post_type'      => 'pizazzsliders',
      'meta_key'       => 'pzsp_short_name',
      'posts_per_page' => - 1
    );

    $pz_wp_query = new WP_Query( $query_options );
    $pzsp_return = array();
    while ( $pz_wp_query->have_posts() ) {
      $pz_wp_query->the_post();
      $pzsp_settings                                           = get_post_custom();
      $pzsp_width                                              = ( ( $pzsp_inc_width ) ? ' (Width: ' . $pzsp_settings[ 'pzsp_contents_width' ][ 0 ] . ' px)' : null );
      $pzsp_return[ $pzsp_settings[ 'pzsp_short_name' ][ 0 ] ] = $pzsp_settings[ 'pzsp_short_name' ][ 0 ] . $pzsp_width;
    };

    // tell WP to use the main query again
    wp_reset_postdata();

    return $pzsp_return;
  }

// Functions ourside of the class
  function pzsp_clear_post_cache() {
// Clears cache of specified post's images
    global $post_ID;
    pzsp_clear_cache( PIZAZZWP_CACHE_PATH . PZSP_CACHE, 'sp-' . $post_ID );
  }

  function pzsp_clear_cache( $path = false, $match = false ) {
    $path  = ( ! $path ) ? PIZAZZWP_CACHE_PATH . PZSP_CACHE : $path;
    $match = ( ! $match ) ? 'sp-' : $match;
    if ( ! is_dir( $path ) ) {
      pzsp_check_cache( PZSP_CACHE );
    } else {
      $cache_files = scandir( $path );
      foreach ( $cache_files as $cache_file ) {
        if ( strpos( $cache_file, $match ) !== false ) {
          unlink( $path . '/' . $cache_file );
        }
      }
    }
  }

  /* * ***************************
   *
   * Function: Load the meta values from the Slider post
   *
   * *************************** */

  function pzsp_get_slider_meta( $short_name, $just_defaults = false ) {
    $pzsp_settings = '';
    if ( intval( $short_name ) > 0 ) :
      $pzsp_settings = get_post_custom( $short_name );
    else:
      $query_options = array(
        'post_type'  => 'pizazzsliders',
        'meta_key'   => 'pzsp_short_name',
        'meta_value' => $short_name
      );

      $pzm_wp_query = new WP_Query( $query_options );
      if ( $pzm_wp_query->have_posts() ) :
        $pzm_wp_query->the_post();
        $pzsp_settings = get_post_custom();
      endif;

      // tell WP to use the main query again
      wp_reset_postdata();
    endif;
    if ( ! $pzsp_settings ) {
      return array();
    }

    $pzsp_return = pzsp_slider_defaults( $just_defaults );
    $pzsp_return = array();
    foreach ( $pzsp_settings as $key => $value ) :
      $pzsp_return[ $key ] = $value[ 0 ];
    endforeach;

    return $pzsp_return;
  }

  function pzsp_get_post_types() {
// Post types
    $all_post_types = array(
      array( 'value' => 'post', 'text' => __( 'Post', 'pzsp' ) ),
      array( 'value' => 'page', 'text' => __( 'Page', 'pzsp' ) ),
    );
    $args           = array(
      '_builtin' => false
    );
    $output         = 'names'; // names or objects
    $operator       = 'and'; // 'and' or 'or'
    $post_types     = get_post_types( $args, $output, $operator );
    foreach ( $post_types as $post_type ) {
      switch ( true ) {
        case $post_type == 'pizazzsliders':
        case $post_type == 'displayed_gallery':
        case $post_type == 'display_type';
        case $post_type == 'gal_display_source';
        case $post_type == 'lightbox_library':
        case strpos( $post_type, 'wp-types-' ) === 0:
        case strpos( $post_type, 'arc-' ) === 0:
        case strpos( $post_type, 'ngg_' ) === 0:
        case $post_type == 'gp_gallery':
        case $post_type == 'deprecated_log':
          continue 2;
        case $post_type == 'pzsp-slides':
          $post_type_text = 'Pizazz Slides';
          break;

        default:
          $post_type_text = $post_type;
          break;
      }
      $all_post_types[ ] = array( 'value' => $post_type, 'text' => $post_type_text );
    }

//		$all_post_types[] = array('value' => 'rss','text'=> __('RSS feed (N/A)','pzsp'));
    return $all_post_types;
  }

// Callback function to show fields in meta box
  /**
   * [pzsp_show_box description]
   *
   * @param  [type] $postobj              [description]
   * @param  [type] $pizazz_callback_args [description]
   *
   * @return [type]                       [description]
   */
  function pzsp_show_box( $postobj, $pizazz_callback_args ) {
    global $post, $post_ID;
    $pzsp_is_new       = ( get_post_status() == 'auto-draft' );
    $pizazz_meta_boxes = $pizazz_callback_args[ 'args' ];
// Use nonce for verification
    echo '<input type="hidden" name="pizazz_meta_box_nonce" value="', wp_create_nonce( basename( __FILE__ ) ), '" />';
    echo '<div id="pzwp_' . $pizazz_meta_boxes[ 'id' ] . '" class="pizazz-meta-boxes" >';

    echo '<ul id="pizazz-meta-nav" class="pizazz-meta-nav">';
    foreach ( $pizazz_meta_boxes[ 'tabs' ] as $pizazz_meta_box_tab ) {
      if ( isset( $pizazz_meta_box_tab[ 'icon' ] ) ) {
        $pzwp_showhide_labels = 'hide';
        $pzwp_label_icon      = $pizazz_meta_box_tab[ 'icon' ];
      } else {
        $pzwp_showhide_labels = 'show';
        $pzwp_label_icon      = null;
      }
      echo '<li class="pizazz-meta-tab-title"><a href="#pizazz-form-table-' . str_replace( ' ', '-', $pizazz_meta_box_tab[ 'label' ] ) . '">' . $pzwp_label_icon . '<span class="pzwp_' . $pzwp_showhide_labels . '_labels"><div class="pzsp-arrow-left"></div>' . $pizazz_meta_box_tab[ 'label' ] . '</span></a></li>';
    }
    echo '</ul>
		<div class="pzwp_the_tables" style="min-height:' . ( count( $pizazz_meta_boxes[ 'tabs' ] ) * 52 + 10 ) . 'px">';
    foreach ( $pizazz_meta_boxes[ 'tabs' ] as $pizazz_meta_box_tab ) {
      echo '<table id="pizazz-form-table-' . str_replace( ' ', '-', $pizazz_meta_box_tab[ 'label' ] ) . '" class="form-table pizazz-form-table">';
      foreach ( $pizazz_meta_box_tab[ 'fields' ] as $field ) {
// get current post meta data
        $pizazz_value = get_post_meta( $post->ID, $field[ 'id' ], true );


/////
// WORK ON THIS!!
// $pizazz_value = ($force_default && $pizazz_value === '')?$field['default']:$pizazz_value;
/////
//	if $pizazz_value is null it chucks a warning in in_array as it wants an array
        echo '<tr id="pizazz-form-table-row-' . str_replace( ' ', '-', $pizazz_meta_box_tab[ 'label' ] ) . '-field-' . $field[ 'id' ] . '" class="row-' . $field[ 'id' ] . '">';
        if ( $field[ 'type' ] != 'heading' ) {
          echo '<th><label class="title-' . $field[ 'id' ] . '" for="', $field[ 'id' ], '">', $field[ 'label' ], '</label></th>';
        } else {
          echo '<th class="pz-field-heading"><h4 class="pz-field-heading">' . $field[ 'label' ] . '</h4></th>';
        }
        if ( $field[ 'type' ] != 'infobox' && $field[ 'type' ] != 'heading' ) {
          echo '<td class="pz-help"><span class="pz-help-button">?<span class="pz-help-text" id="pz-help-text-' . $field[ 'id' ] . '">' . $field[ 'desc' ] . '</span></span></td>';
        } else {
          echo '<td class="pz-help"></td>';
        }
        echo '<td class="cell-' . $field[ 'id' ] . '" data-fieldid="' . $field[ 'id' ] . '">';
// This is simply to stop PHP debugging notice about missing index 'help' when help isn't specified
        $field[ 'help' ] = ( ( ! array_key_exists( 'help', $field ) ) ? null : $field[ 'help' ] );
        switch ( $field[ 'type' ] ) {
          case 'heading':
            echo '';
            break;
          case 'infobox':
            echo '<span class="pzwp-infobox">' . $field[ 'desc' ] . '</span>';
            break;
          case 'readonly':
            echo '<input type="text" name="', $field[ 'id' ], '" id="', $field[ 'id' ], '" readonly="readonly" value="' . $post_ID . '" />';
            break;
          case 'text':
            echo '<input type="text" name="', $field[ 'id' ], '" id="', $field[ 'id' ], '" value="', ( ! $pzsp_is_new && ( $pizazz_value || $pizazz_value === '' || $pizazz_value === '0' ) ) ? $pizazz_value : $field[ 'default' ], '" size="30" style="width:97%" />', '<br />';
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;
          case 'colorpicker':
            echo '<input type="text" name="', $field[ 'id' ], '" id="', $field[ 'id' ], '" value="', ( ! $pzsp_is_new && $pizazz_value ) ? $pizazz_value : $field[ 'default' ], '" size="30" style="width:100px" />', '<span class="pzwp_colour_swatch pzwp_colour_' . $field[ 'id' ] . '">&nbsp;</span><br />';
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;
          case 'numeric':
            echo '<input type="numeric" name="', $field[ 'id' ], '" id="', $field[ 'id' ], '" value="', ( ! $pzsp_is_new && ( $pizazz_value || $pizazz_value === '0' || $pizazz_value === '' ) ) ? $pizazz_value : $field[ 'default' ], '" size="30" style="width:100px" />', '<br />';
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;
          case 'textarea':
            echo '<textarea name="', $field[ 'id' ], '" id="', $field[ 'id' ], '" cols="60" rows="4" style="width:97%">', ( ! $pzsp_is_new && ( $pizazz_value || $pizazz_value === '' ) ) ? $pizazz_value : $field[ 'default' ], '</textarea>', '<br />';
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;
          case 'textarea-small':
            echo '<textarea name="', $field[ 'id' ], '" id="', $field[ 'id' ], '" cols="60" rows="2" style="width:97%">', ( ! $pzsp_is_new && ( $pizazz_value || $pizazz_value === '' ) ) ? $pizazz_value : $field[ 'default' ], '</textarea>', '<br />';
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;
          case 'textarea-large':
            echo '<textarea name="', $field[ 'id' ], '" id="', $field[ 'id' ], '" cols="60" rows="16" style="width:97%">', ( ! $pzsp_is_new && ( $pizazz_value || $pizazz_value === '' ) ) ? $pizazz_value : $field[ 'default' ], '</textarea>', '<br />';
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;
          case 'select':
            $pizazz_value = ( $pzsp_is_new ) ? $field[ 'default' ] : $pizazz_value;
            echo '<select  name="', $field[ 'id' ], '" id="', $field[ 'id' ], '">';
            foreach ( $field[ 'options' ] as $option ) {
              $pizazz_value = ( ! $pizazz_value ) ? $field[ 'default' ] : $pizazz_value;
              echo '<option' . ( ( $pizazz_value == $option[ 'value' ] ) ? ' selected="selected"' : '' ) . ' value="' . $option[ 'value' ] . '">' . $option[ 'text' ] . '</option>';
            }
            echo '</select>';
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;
          case 'ddslick-select':
            $pizazz_value = ( $pzsp_is_new ) ? $field[ 'default' ] : $pizazz_value;
            echo '<select  name="', $field[ 'id' ], '" id="', $field[ 'id' ], '" class="pzwp_ddslick">';
            foreach ( $field[ 'options' ] as $option ) {
              $pizazz_value = ( ! $pizazz_value ) ? $field[ 'default' ] : $pizazz_value;
              echo '<option' . ( ( $pizazz_value == $option[ 'value' ] ) ? ' selected="selected"' : '' ) . ' value="' . $option[ 'value' ] . '">' . $option[ 'text' ] . '</option>';
            }
            echo '</select>';
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;
          case 'multiselect':
            $pizazz_value = ( $pzsp_is_new ) ? $field[ 'default' ] : $pizazz_value;
            if ( ! $field[ 'options' ] ) {
              echo '<div id="', $field[ 'id' ], '">';
              echo '<span class="pzwp-infobox">No options available</span>';
              echo '</div>';
              echo '<span class="howto">' . $field[ 'help' ] . '</span>';
              break;
            }
            echo '<div id="', $field[ 'id' ], '" style="overflow-x:auto;max-height:100px;background:white;border:1px #eee solid">';
            foreach ( $field[ 'options' ] as $option ) {
              if ( substr( $option[ 'text' ], 0, 1 ) == '>' ) {
                echo '<strong>&nbsp;=== ' . substr( $option[ 'text' ], 1 ) . ' ==========</strong><br/>';
              } else {
                $pizazz_array_value = ( is_string( $pizazz_value ) ) ? array( $pizazz_value ) : $pizazz_value;
                $pzwp_in_array      = ( $pizazz_value ) ? ( in_array( $option[ 'value' ], $pizazz_array_value ) || $pizazz_value == $option[ 'value' ] ) : false;
                echo '&nbsp;<input type="checkbox" name="' . $field[ 'id' ] . '[]" id="' . $field[ 'id' ] . '" value="' . $option[ 'value' ] . '"' . ( ( $pzwp_in_array ) ? ' checked="checked"' : '' ) . ' />&nbsp;' . $option[ 'text' ] . '<br/>';
              }
            }
            echo '</div>';
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;

          case 'radio':
            foreach ( $field[ 'options' ] as $option ) {
              $pizazz_value = ( $pzsp_is_new ) ? $field[ 'default' ] : $pizazz_value;
              echo '<input type="radio" name="', $field[ 'id' ], '" value="', $option[ 'value' ], '"', $pizazz_value == $option[ 'value' ] ? ' checked="checked"' : '', ' />&nbsp;', $option[ 'text' ], '&nbsp;&nbsp;&nbsp;';
            }
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;
          case 'checkbox':
            $pizazz_value = ( $pzsp_is_new ) ? $field[ 'default' ] : $pizazz_value;
            echo '<input type="checkbox" name="', $field[ 'id' ], '" id="', $field[ 'id' ], '"', $pizazz_value ? ' checked="checked"' : '', ' />';
            echo '<span class="howto">' . $field[ 'help' ] . '</span>';
            break;
        }
        echo '</div><td>',
        '</tr>';
      }

      echo '</table>';
    }
    echo '</div>';
    echo '</div>';
  }

  /**
   * Save data from custom meta boxes
   *
   * @param  [type] $post_id [Custom post type Post ID]
   *
   * @return [type]          [no return value]
   */
  function pzsp_save_data( $post_id ) {
//var_dump($post_id,$_POST['post_type']);
// Will need to manually add each case as new types created.
    if ( ! isset( $_POST[ 'post_type' ] ) ) {
      return false;
    }
    switch ( $_POST[ 'post_type' ] ) {
      case 'pizazzsliders' :
        global $pzsp_cpt_meta_boxes;
        $pizazz_meta_boxes = $pzsp_cpt_meta_boxes;
        break;
      case 'pzsp-slides' :
        global $pzsp_cpt_slides_meta_boxes;
        $pizazz_meta_boxes = $pzsp_cpt_slides_meta_boxes;
        break;
      case 'pzip-maps' :
        global $pzip_cpt_meta_boxes;
        $pzip_number_points = get_post_meta( $post_id, 'pzip_number_points', true );
        for ( $j = 1; $j <= $pzip_number_points; $j ++ ) {
          $pzip_cpt_meta_boxes[ 'tabs' ][ ] =
            array(
              'label' => __( 'Point-', 'pzip' ) . $j,
              'id'    => 'pzip_tab_point-' . $j,
              'type'  => 'tab',
            );
          pzip_options_map_image_points( $j, 'pzip_', $j, null );
        }
        $pizazz_meta_boxes = $pzip_cpt_meta_boxes;
        break;
      default:
        return false;
    }
//pzdebug($pizazz_meta_boxes);
// verify nonce
    if ( ! wp_verify_nonce( $_POST[ 'pizazz_meta_box_nonce' ], basename( __FILE__ ) ) ) {
      return $post_id;
    }

// check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return $post_id;
    }

// check permissions
    if ( 'page' == $_POST[ 'post_type' ] ) {
      if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return $post_id;
      }
    } elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
      return $post_id;
    }
//  if (empty($pizazz_meta_boxes['tabs'])) {return;};
    foreach ( $pizazz_meta_boxes[ 'tabs' ] as $pzsp_meta_box ) {
      foreach ( $pzsp_meta_box[ 'fields' ] as $field ) {
        $old = get_post_meta( $post_id, $field[ 'id' ], true );
        $new = ( isset( $_POST[ $field[ 'id' ] ] ) ) ? $_POST[ $field[ 'id' ] ] : null;
        if ( $new != $old ) {
          update_post_meta( $post_id, $field[ 'id' ], $new );
        } elseif ( '' == $new && $old ) {
          delete_post_meta( $post_id, $field[ 'id' ], $old );
        }
      }
    }
  }

  add_action( 'save_post', 'pzsp_save_data' );

//add_action('post_updated', 'pizazz_save_data');

  function pzsp_process_image(
    $pz_slider_id,
    $cache_path,
    $pz_image_width,
    $pz_image_height,
    $pz_sizing_type = 'crop',
    $pz_horz_crop_align = 'centre',
    $pz_vert_crop_align = 'centre',
    $pz_image_fill = '#ffffff',
    $pz_max_image_dim = '1024',
    $pz_quality = '70',
    $pz_greyscale,
    $pz_image,
    $uid,
    $pz_title,
    $pzsp_respect_focal_point = false,
    $pzsp_do_not_resize = false
  ) {
    $alt_text         = $pz_title;
    $pzsp_focal_point = '';
    $new_image_url    = '';
    if ( ! $pz_image ) {
      $image_src          = '';
      $new_image_url      = '';
      $has_post_thumbnail = has_post_thumbnail();
      $is_image_attached  = false;
      if ( ! $has_post_thumbnail ) {
        $args        = array(
          'post_type'   => 'attachment',
          'numberposts' => - 1,
          'post_status' => null,
          'post_parent' => $uid
        );
        $attachments = get_posts( $args );
        if ( isset( $attachments[ 0 ] ) ) {
          $is_image_attached = wp_attachment_is_image( $attachments[ 0 ]->ID );
          if ( $pzsp_respect_focal_point ) {
            $pzsp_focal_point = get_post_meta( $attachments[ 0 ]->ID, 'pzgp-focal-point', true );
          }
        }
      }
    }
    //---------------------------------------------------------------------------------------------------------------------
    // Process if Image available
    //---------------------------------------------------------------------------------------------------------------------
    $has_usable_image = false;
    if ( function_exists( 'pzwp_image_cache' ) ) {
      pzwp_image_cache( PIZAZZWP_CACHE_PATH . $cache_path );
      if ( ( ! empty( $has_post_thumbnail ) || ! empty( $is_image_attached ) || ! empty( $pz_image ) ) ) {
        $has_usable_image = true;
        if ( ! empty( $has_post_thumbnail ) ) {
          // Changed to use get_the_post_thumbnail instead of wp_get_attachment_image_src coz nextgen didn't use it
          $thumb_id   = get_post_thumbnail_id( $uid );
          $image_info = pzgetlinks( get_the_post_thumbnail( $uid, 'full' ) );
          $image      = $image_info[ 0 ];
          if ( $pzsp_respect_focal_point ) {
            $pzsp_focal_point = get_post_meta( get_post_thumbnail_id(), 'pzgp_focal_point', true );
          }
        } elseif ( ! empty( $pz_image ) ) {
          $image = $pz_image;
        }
        if ( ! empty( $image ) ) {
          //---------------------------------------------------------------------------------------------------------------------
          // New image processing routine
          //---------------------------------------------------------------------------------------------------------------------

          $try_to_create_again = false;
          $extension           = strtolower( strrchr( $image, '.' ) );
          // Need to make sure no junk after filename
          if
          ( strpos( $image, '.jpg' ) > 0
          ) {
            preg_match( "/^(.)*.jpg/u", $image, $matches );
            $image     = $matches[ 0 ];
            $extension = '.jpg';
          } elseif
          ( strpos( $image, '.jpeg' ) > 0
          ) {
            preg_match( "/^(.)*.jpeg/u", $image, $matches );
            $image     = $matches[ 0 ];
            $extension = '.jpeg';
          } elseif
          ( strpos( $image, '.png' ) > 0
          ) {
            preg_match( "/^(.)*.png/u", $image, $matches );
            $image     = $matches[ 0 ];
            $extension = '.png';
          } elseif
          ( strpos( $image, '.gif' ) > 0
          ) {
            preg_match( "/^(.)*.gif/u", $image, $matches );
            $image     = $matches[ 0 ];
            $extension = '.gif';
          }

          $ext_types = '.jpg .jpeg .png .gif';

          // verify extension is acceptable
          if ( $image && strpos( $ext_types, $extension ) !== false ) {
            // This line accomodates nextgen too, making sure it uses the full image
            $image_url = str_replace( 'thumbs/thumbs_', '', $image );

            // Not all servers support looking for URL, so we need the path.
            $image_shortname = str_replace( ( home_url() . '/wp-content' ), '', $image_url );
            $image_home_path = WP_CONTENT_DIR;
            $image_path      = $image_home_path . $image_shortname;

            $new_image_url  = PIZAZZWP_CACHE_URL . $cache_path . 'sp-' . $pz_slider_id . '-post-' . $uid . $extension;
            $new_image_path = PIZAZZWP_CACHE_PATH . $cache_path . 'sp-' . $pz_slider_id . '-post-' . $uid . $extension;
            if ( ! empty( $thumb_id ) ) {
              $alt_text = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
              if ( empty( $alt_text ) ) {
                $thumb_info = get_post( $thumb_id );
                $alt_text   = $thumb_info->post_title;
              }
            }

            $image_src = '<img src="' . $new_image_url . '" alt="' . $alt_text . '"/>';
            if ( ! file_exists( $new_image_path ) && ! file_exists( $new_image_url ) ) {
              $try_to_create_again = true;
            }

//---------------------------------------------------------------------------------------------------------------------
//
            // Create images if required
//
            //---------------------------------------------------------------------------------------------------------------------

            if ( $try_to_create_again && ! $pzsp_do_not_resize ) {
              if ( $image && $pz_image_width > 0 && $pz_image_height > 0 ) {

                // the @ hides the error message
                if ( function_exists( 'exif_imagetype' ) ) {
                  $image_exists = @exif_imagetype( $image_path );
                } else {
                  $image_exists = @getimagesize( $image_path );
                }
                $useloc = 'path';
                if ( ! $image_exists ) {
                  $useloc = 'url';
                  if ( function_exists( 'exif_imagetype' ) ) {
                    $image_exists = @exif_imagetype( $image_url );
                  } else {
                    $image_exists = @getimagesize( $image_url );
                  }
                  if ( ! $image_exists ) {
                    $useloc = 'noimage';
                  }
                }

                if ( ! $image_exists ) {
                  $image_src = 'Cannot find or access the specified image. Please check you have set a featured image, or post has an imgae. Otherwise check the file permissions on your images folders';
                } else {
                  //---------------------------------------------------------------------------------------------------------------------
                  // Run the image processing from Oberto
                  //---------------------------------------------------------------------------------------------------------------------

                  if ( $useloc == 'url' ) {
                    $resizeObj = new pzsp_resize( $image_url );
                  } else {
                    $resizeObj = new pzsp_resize( $image_path );
                  }
                  $pz_sizing_type = ( ! $pz_sizing_type ) ? 'crop' : $pz_sizing_type;


                  $resizeObj->resizeImage(
                    $pz_image_width, $pz_image_height, $pz_sizing_type, $pz_vert_crop_align, $pz_horz_crop_align, $pz_image_fill, true, $pzsp_focal_point,
//																	  (($settings['gp-thumb-focal-point-align'] && $settings['gp-thumb-sizing-type'] == 'crop')? $pz_focal_point: null),
                    'sliderplus:562'
                  );
                  /*
                    $newWidth,
                    $newHeight,
                    $resizingType="auto",
                    $vcrop_align="center",
                    $hcrop_align="center",
                    $img_bg_color,
                    $centre_image=true,
                    $focal_point,
                    $call_info='')	{

                   */
//var_dump($resizeObj);			

                  if ( $pz_greyscale ) {
                    $resizeObj->greyscale();
                  }
                  /* Files should alsways exist... so only really need to be created when in Visual Editor... */
                  if ( ! file_exists( $new_image_path ) || $try_to_create_again ) {
// Need to delete all cached images that match the block id
                    $pzwp_quality = ( ! $pz_quality ) ? 70 : (int) $pz_quality;
                    $resizeObj->saveImage( $new_image_path, $pzwp_quality );
                  }


                  if ( ! file_exists( $new_image_path ) && ! file_exists( $new_image_url ) ) {
                    // Image creation error
                    $pzwp_error[ 'cache' ] .= '<div class="pzwp-errors"><strong>Image cache problem:</strong>.Image ' . $new_image_url . ' not created. <span class="ep_rtfm">First, reload this page. If that doesn\'t help re-publish in the Visual Editor</span> and/or check Headway cache folder permissions if that doesn\'t resolve it.</div>';
//								$pzwp_error['cache'] .= '<div class="pzwp-errors"><strong>Image cache problem:</strong>.Image '.$new_image_url.' not created. Please check Headway cache folder permissions.</div>';
                  }
                  if ( ! empty( $thumb_id ) ) {
                    $alt_text = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
                    if ( empty( $alt_text ) ) {
                      $thumb_info = get_post( $thumb_id );
                      $alt_text   = $thumb_info->post_title;
                    }
                  }
                  $image_src = '<img src="' . $new_image_url . '" alt="' . $pz_title . '"/>';
                }
              } elseif ( ! ( $pz_image_width > 0 && $pz_image_height > 0 ) ) {
                // Publish again
                // This aint much use outside of headway :/
                //								$pzwp_error['publish-again'] = '<div class="pzwp-errors"><strong>Block creation problem:</strong> <strong>Block ID '.$block['id'].'</strong> not fully created. <span class="ep_rtfm">First, reload this page. If that doesn\'t help, then re-publish it in the Visual Editor.</span> If that fails, contact Headway support.</div>';
              } elseif ( ! $image ) {
                // NextGen error
                $pzwp_error[ 'nextgen' ] .= '<div class="pzwp-errors"><strong>Featured Image problem:</strong> WordPress indicates the post <strong><em>' . get_the_title() . '</em></strong> has an issue with its Featured Image. This could happen if you were using an alternative image plugin to supply your Featured Image, and have since deactivated that plugin.</div>';
              }
            } elseif ( $pzsp_do_not_resize ) {
              $new_image_url = $image;
            }
          } // Emd image processsing
        } else {
          $new_image_url = '';
        }
// TODO: Find out if this is needed
//					if (($has_post_thumbnail || $attachments) && !$epv['images_correctly_configured']) {
//						// Image dimensions error
//						$pzwp_error['dimensions'] = '<div class="pzwp-errors"><strong>Image dimensions problem:</strong> You have not correctly entered the image height and/or width for this block, <strong>ID '.$block['id'].'</strong>.</div>';
//					}
      }
    } else {
      $new_image_url = '';
    } // End if pzwp_image_cache() exists

    return array( $new_image_url, $alt_text );
  }

  function pzsp_get_wp_galleries() {

    $args    = array( 'post_type' => 'post', 'numberposts' => - 1, 'post_status' => null, 'post_parent' => null );
    $albums  = get_posts( $args );
    $results = array();
    $inc     = 0;
    if ( $albums ) {
      foreach ( $albums as $post ) {
        setup_postdata( $post );
        $pzsp_has_images = pzsp_wp_attachment_count( $post->ID, 'image' );
        if ( $pzsp_has_images >= 3 ) {
          $results[ $inc ++ ] = array(
            'post_id' => $post->ID,
            'title'   => substr( get_the_title( $post->ID ), 0, 60 ) . ' (' . $pzsp_has_images . ')',
            'source'  => 'WP Post: '
          );
        }
      }
    }


    $args   = array( 'post_type' => 'page', 'numberposts' => - 1, 'post_status' => null, 'post_parent' => null );
    $albums = get_posts( $args );
    if ( $albums ) {
      foreach ( $albums as $post ) {
        setup_postdata( $post );
        $pzsp_has_images = pzsp_wp_attachment_count( $post->ID, 'image' );
        if ( $pzsp_has_images >= 3 ) {
          $results[ $inc ++ ] = array(
            'post_id' => $post->ID,
            'title'   => substr( get_the_title( $post->ID ), 0, 60 ) . ' (' . $pzsp_has_images . ')',
            'source'  => 'WP Page: '
          );
        }
      }
    }


    return $results;
  }

  /* -----------------------------------------------------------------------------------

    Function to get the image data for selected album

    ----------------------------------------------------------------------------------- */

  function pzsp_get_wp_gallery( $album, $settings ) {
    $order_by = ( ! $settings[ 'pzsp-order-by' ] ) ? 'title' : $settings[ 'pzsp-order-by' ];
    $order_az = ( ! $settings[ 'pzsp-order-az' ] ) ? 'ASC' : $settings[ 'pzsp-order-az' ];

// $args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $album, 'orderby' => $order_by, 'order' => $order_az); 
    $args        = array(
      'post_type'   => 'attachment',
      'numberposts' => - 1,
      'post_status' => null,
      'post_parent' => $album,
      'orderby'     => 'title',
      'order'       => 'ASC'
    );
    $attachments = get_posts( $args );
    $results     = array();
    $inc         = 0;
    if ( $attachments ) {
      foreach ( $attachments as $post ) {
        setup_postdata( $post );
        $height          = $settings[ 'pzsp-image-height' ];
        $width           = $settings[ 'pzsp-image-width' ];
        $image_size      = max( $settings[ 'pzsp-image-height' ], $settings[ 'pzsp-image-width' ] );
        $image_src       = wp_get_attachment_image_src( $post->ID, 'full' );
        $results[ $inc ] = array(
          'post_id'   => $post->ID,
          'title'     => get_the_title( $post->ID ),
          'caption'   => $attachments[ $inc ]->post_excerpt,
          'thumb_url' => wp_get_attachment_thumb_url( $post->ID ),
          'image_url' => $image_src[ 0 ]
        );
        $inc ++;
      }
    }

    return $results;
  }

  function pzsp_wp_attachment_count( $post_id, $type = "image" ) {
    $args        = array(
      'post_type'      => 'attachment',
      'numberposts'    => - 1,
      'post_status'    => null,
      'post_parent'    => $post_id,
      'post_mime_type' => $type
    );
    $attachments = get_posts( $args );

    return sizeof( $attachments );
  }

  function pzsp_access() {
    global $capabilities;
    $pzsp_access             = null;
    $pzsp_settings           = (array) get_option( 'pizazzwp_sliderplus_settings' );
    $pzsp_admin_access_value = esc_attr( $pzsp_settings[ 'admin_access' ] );

//	if ($user_role is greater than min_access then capabilities -> user role. else capabilites = null)
    // very important top down so access levels trickle down
    if ( current_user_can( 'install_plugins' ) ) {
      $pzsp_access = $capabilities[ 'administrator' ];

    } elseif ( current_user_can( 'edit_others_posts' ) ) { // Editor
      if ( $pzsp_admin_access_value == 'editor'
           || $pzsp_admin_access_value == 'author'
           || $pzsp_admin_access_value == 'contributor'
           || $pzsp_admin_access_value == 'subscriber'
      ) {
        $pzsp_access = $capabilities[ 'editor' ];
      }

    } elseif ( current_user_can( 'upload_files' ) ) { // Author
      if ( $pzsp_admin_access_value == 'author'
           || $pzsp_admin_access_value == 'contributor'
           || $pzsp_admin_access_value == 'subscriber'
      ) {
        $pzsp_access = $capabilities[ 'author' ];
      }

    } elseif ( current_user_can( 'edit_posts' ) ) { // Contributor
      if ( $pzsp_admin_access_value == 'contributor'
           || $pzsp_admin_access_value == 'subscriber'
      ) {
        $pzsp_access = $capabilities[ 'contributor' ];
      }

    } elseif ( current_user_can( 'read' ) ) { // Subscriber
      if ( $pzsp_admin_access_value == 'subscriber'
      ) {
        $pzsp_access = $capabilities[ 'subscriber' ];
      } else {
        $pzsp_access = null;
      }
    }

//pzdebug($pzsp_access);
    return $pzsp_access;
  }

  function pzsp_get_post_images( $album, $order_by, $order_az, $pzsp_settings ) {
    $gp_list = array();
    $results = null;
    // Then get galleried images
    $gp_query = array( 'p' => $album, 'post_type' => 'gp_gallery' );
    $gp_posts = new WP_Query( $gp_query );

    if ( $gp_posts->have_posts() ) {
      $gp_posts->the_post();
      $gp_content = get_the_content();
      $is_matches = preg_match_all( "/(\\[gallery)(.*?)(ids=\")([\\d,\\,])*\\\"\\]/u", $gp_content, $gp_gallery_scs );
      foreach ( $gp_gallery_scs[ 0 ] as $gp_gallery_sc ) {
        $is_matches = preg_match_all( "/(?<=ids=\\\")([\\d,\\,])*/u", $gp_gallery_sc, $matches );
        foreach ( $matches[ 0 ] as $keys => $match ) {
          if ( ! empty( $match ) ) {
            $gp_list       = array_merge( $gp_list, explode( ',', $match ) );
            $gp_sort_order = ( is_array( $match ) ) ? explode( ',', $match ) : $match;
          }
        }
      }

    }
    // We don't have to worryabout duplicates because WP filters them
    if ( count( $gp_list ) > 0 ) {
      $args    = array(
        'post_type'   => 'attachment',
        'numberposts' => - 1,
        'post_status' => null,
        'post_parent' => null,
        'orderby'     => $order_by,
        'order'       => $order_az,
        'include'     => $gp_list
      );
      $results = pzsp_get_attachments( $args, $pzsp_settings, $results );
    }

    $results = pzsp_limit_images( $results, ( ! empty( $pzsp_settings[ 'pzsp_number_show ' ] ) ? $pzsp_settings[ 'pzsp_number_show ' ] : 99999999 ) );

    // Had to add manual sorting by sort order as WP seems to have dropped that since 3.5!
    if ( $order_by == 'menu_order' ) {
      $sorted_results = pzsp_sortGalleryBySortOrder( $results, $gp_sort_order );

      return $sorted_results;
    } else {
      return $results;
    }
  }

  function pzsp_get_attachments( $args, $settings, $results = array() ) {
//    var_dump($settings);
    $attachments = get_posts( $args );
    if ( $attachments ) {
      $inc = 0;
      foreach ( $attachments as $key => $post ) {
        setup_postdata( $post );
        $height     = ( $settings[ 'pzsp_image_size' ] === 'ImageTop' || $settings[ 'pzsp_image_size' ] === 'ImageBottom' ) ? $settings[ 'pzsp_contents_height' ] * $settings[ 'pzsp_image_size' ] / 100 : $settings[ 'pzsp_contents_height' ];
        $width      = ( $settings[ 'pzsp_image_size' ] === 'ImageLeft' || $settings[ 'pzsp_image_size' ] === 'ImageRight' ) ? $settings[ 'pzsp_contents_width' ] * $settings[ 'pzsp_image_size' ] / 100 : $settings[ 'pzsp_contents_width' ];
        $image_size = max( $height, $width );
        $image_src  = wp_get_attachment_image_src( $post->ID, 'full' );
        if ( ! empty( $settings[ 'pzsp_respect_focal_point' ] ) ) {
          $thumb_info = wp_get_attachment_image_src( $post->ID, 'medium' );
          $thumb_src  = $thumb_info[ 0 ];
        } else {
          $thumb_src = wp_get_attachment_thumb_url( $post->ID );
        }
        $link_url   = ( get_post_meta( $post->ID, 'pzgp_link_url', true ) ) ? get_post_meta( $post->ID, 'pzgp_link_url', true ) : $image_src[ 0 ];
        $results[ ] = array(
          'post_id'     => $post->ID,
          'title'       => stripslashes( get_the_title( $post->ID ) ),
          'caption'     => stripslashes( $attachments[ $inc ++ ]->post_content ),
          'thumb_url'   => $thumb_src,
          'image_url'   => $image_src[ 0 ],
          'link_url'    => $link_url,
          'focal_point' => get_post_meta( $post->ID, 'pzgp_focal_point', true )
        );
      }
    }

    return $results;
  }

  function pzsp_limit_images( $results, $limit ) {
    if ( count( $results ) > ( ! empty( $limit ) ? $limit : 9999 ) ) {
      $chunk = array_chunk( $results, $limit );

      return $chunk[ 0 ];
    } else {
      return $results;
    }

  }

  function pzsp_sortGalleryBySortOrder( $sourceArray, $orderArray = null ) {
    $re_sourced = array();
//	if (empty($sourceArray)) {
//		return $sourceArray;
//	}
    if ( empty( $orderArray ) ) {
      return $sourceArray;
    }
    $orderArray = ( ! is_array( $orderArray ) ? explode( ',', $orderArray ) : $orderArray );
    foreach ( $sourceArray as $source_line ) {
      $re_sourced[ $source_line[ 'post_id' ] ] = $source_line;
    }
    $ordered = array();
    foreach ( $orderArray as $key ) {
      if ( array_key_exists( $key, $re_sourced ) ) {
        $ordered[ ] = $re_sourced[ $key ];
        unset( $re_sourced[ $key ] );
      }
    }

    return $ordered;
  }

  function pzsp_get_galleries( $caller ) {

    // If this is loaded without a valid caller, reject it.
    if ( ! $caller ) {
      return false;
    }

    // We need to look for each source and load any galleries in it
    // Gallery+
    $gp_results = array();
    $gp_results = pzsp_get_gp_galleries();

    // WordPress
    $wp_results     = array();
    $wp_results     = pzsp_get_wp_galleries( true );
    $wp_single      = array();
    $wp_single[ 0 ] = array(
      'post_id' => 99999999,
      'title'   => 'Use images in viewed post/page',
      'source'  => 'Content: '
    );


    $return_array = array_merge( $gp_results, $wp_single, $wp_results );

    return $return_array;
  }

  function pzsp_get_gp_galleries() {

    $args    = array( 'post_type' => 'gp_gallery', 'numberposts' => - 1, 'post_status' => null, 'post_parent' => null );
    $albums  = get_posts( $args );
    $results = array();
    $inc     = 0;
    if ( $albums ) {
      foreach ( $albums as $post ) {
        setup_postdata( $post );
        $results[ $inc ++ ] = array(
          'post_id' => $post->ID,
          'title'   => get_the_title( $post->ID ),
          'source'  => 'Gallery+: '
        );
      }
    }

    return $results;
  }

  function pzsp_get_slideshow( $pzsp_stuff, $type = '' ) {
    require_once( PZSP_PLUGIN_PATH . '/includes/Mobile-Detect/Mobile_Detect.php' );
    $detect = new Mobile_Detect;
    $device = 'not set';

    $slideshows['default']='none';

    switch ( $type ) {
      case 'block':
        $settings = SliderPlusBlockOptions::get_settings( $pzsp_stuff );
        $slideshows = array(
          'default' => $settings[ 'pzsp_opt_slidername' ],
          'tablet'  => $settings[ 'pzsp_opt_slidername_tablet' ],
          'phone'   => $settings[ 'pzsp_opt_slidername_phone' ]
        );
        break;
      case 'shortcode':
        $slideshows = array(
          'default'=> $pzsp_stuff[0],
          'tablet'=>(isset($pzsp_stuff['tablet'])?$pzsp_stuff['tablet']:$pzsp_stuff[0]),
          'phone'=>(isset($pzsp_stuff['phone'])?$pzsp_stuff['phone']:$pzsp_stuff[0]),
        );
        break;
    }
    if ( $type ) {
      switch ( true ) {
        case ( $detect->isMobile() && ! $detect->isTablet() ):
          // Phone
          $slideshow = empty( $slideshows[ 'phone' ] ) || 'none' === $slideshows[ 'phone' ] ? $slideshows[ 'default' ] : $slideshows[ 'phone' ];
          $device    = 'phone';
          break;
        case ( $detect->isTablet() ):
          // Tablet
          $slideshow = empty( $slideshows[ 'tablet' ] ) || 'none' === $slideshows[ 'tablet' ] ? $slideshows[ 'default' ] : $slideshows[ 'tablet' ];
          $device    = 'tablet';
          break;
        default:
          // Desktop or other weird thing
          $slideshow = $slideshows[ 'default' ];
          $device    = 'desktop';
          break;
      }
    } else {
      $slideshow = '';
    }

    return $slideshow;
  }