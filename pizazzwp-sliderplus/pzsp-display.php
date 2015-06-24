<?php

  /**
   * Function: pzsplus
   * Description: Template tag to execute a slideplus
   * @param  [string] $pzsp_shortname [Shortname to be displayed]
   * @return none
   */


  function pzsplus($pzsp_shortname)
  {
    ob_start();
    echo do_shortcode('[sliderplus ' . $pzsp_shortname . ']');
    $contents = ob_get_contents();
    ob_end_clean();
    echo $contents;
  }

  /* This Class is all about creating and laying out the pzsp
   What we want to achieve…
   … create an object which is the pzsp
   … methods for each functionality - eg navigation,

  */

  class PZSP
  {

    var $PZSP;
    var $image_content;
    var $text_content;
    var $navigation_array;
    var $content_lefttop;
    var $content_rightbottom;

    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: Construct PZSP object
     *
     * *************************** */

    function __construct($pzsp_settings)
    {
      $frame_styling    = '';
      $contents_styling = '';
      if ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterleft%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%') {
        $frame_styling    = 'max-height:' . $pzsp_settings[ 'pzsp_contents_height' ] . 'px;';
        $contents_styling = 'width:' . (100 - $pzsp_settings[ 'pzsp_nav_width' ]) . '%;';
      }
      $this->PZSP = '
			<div class="pzsp-outer-wrapper" style="' . $frame_styling . ';">
				%pageprev%
				%navoutertop%
				%navouterleft%
				<div class="pzsp-inner-wrapper" style="' . $contents_styling . ';float:left;">
					%navinnertop%
					<div class="cycle-slideshow pzsp-contents" %slideconfig%>%contents%</div><!-- end pzsp contents -->
					%navinnerbottom%
				</div><!-- end pzsp inner wrapper -->
				%navouterright%
				%shadow%
				%navouterbottom%
				%pagenext%
			</div><!-- end pzsp outer wrapper -->
			';
    }

    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: Get the contents
     *
     * *************************** */

    function get_contents($pzsp_settings)
    {
// Next we get the data for each post. Thus setting up another loop
      $pzsp_number_show = (empty($pzsp_settings[ 'pzsp_number_show' ]) || !isset($pzsp_settings[ 'pzsp_number_show' ]) || intval($pzsp_settings[ 'pzsp_number_show' ]) == 0) ? -1 : intval($pzsp_settings[ 'pzsp_number_show' ]);

      $filtering = ($pzsp_settings[ 'pzsp_content_type' ] == 'page' ? 'slide_set' : $pzsp_settings[ 'pzsp_filtering' ]);
      switch ($filtering) {
        case 'recent' :
          $pzsp_query_options = array(
              'posts_per_page'      => $pzsp_number_show,
              'post_type'           => $pzsp_settings[ 'pzsp_content_type' ],
              'offset'              => 0,
              'orderby'             => $pzsp_settings[ 'pzsp_order_by' ],
              'order'               => $pzsp_settings[ 'pzsp_order_az' ],
              'ignore_sticky_posts' => true
          );
          break;
        case 'category' :
          $pzsp_show_ids      = maybe_unserialize($pzsp_settings[ 'pzsp_category' ]);
          $pzsp_query_options = array(
              'posts_per_page' => $pzsp_number_show,
              'category__in'   => $pzsp_show_ids,
              'post_type'      => $pzsp_settings[ 'pzsp_content_type' ],
              'orderby'        => $pzsp_settings[ 'pzsp_order_by' ],
              'order'          => $pzsp_settings[ 'pzsp_order_az' ],
          );
          break;
        case 'tags' :
          $pzsp_show_ids      = maybe_unserialize($pzsp_settings[ 'pzsp_tags' ]);
          $pzsp_query_options = array(
              'posts_per_page' => $pzsp_number_show,
              'tag__in'        => $pzsp_show_ids,
              'post_type'      => $pzsp_settings[ 'pzsp_content_type' ],
              'orderby'        => $pzsp_settings[ 'pzsp_order_by' ],
              'order'          => $pzsp_settings[ 'pzsp_order_az' ],
          );
          break;
        case 'slide_set' :
          $pzsp_show_ids      = maybe_unserialize($pzsp_settings[ 'pzsp_slide_set' ]);
          $pzsp_query_options = array(
              'post_type'      => $pzsp_settings[ 'pzsp_content_type' ],
              'tax_query'      => array(
                  array(
                      'taxonomy' => 'slide_set',
                      'field'    => 'slug',
                      'terms'    => $pzsp_show_ids,
                      'operator' => 'IN'
                  ),
              ),
              'posts_per_page' => $pzsp_number_show,
              'orderby'        => $pzsp_settings[ 'pzsp_order_by' ],
              'order'          => $pzsp_settings[ 'pzsp_order_az' ],
          );
          break;
        case 'taxonomy' :
          $pzsp_show_ids = maybe_unserialize($pzsp_settings[ 'pzsp_taxonomy' ]);
//				pzdebug($pzsp_show_ids);
          $taxonomies = get_taxonomies();
// WTF is this doing?????????
          foreach ($taxonomies as $taxonomy) {
            if (term_exists($pzsp_show_ids[ 0 ], $taxonomy)) {
              $pzsp_in_taxonomy = $taxonomy;
              break;
            }
          }
          $pzsp_query_options = array(
              'post_type'      => $pzsp_settings[ 'pzsp_content_type' ],
              'tax_query'      => array(
                  array(
                      'taxonomy' => $pzsp_in_taxonomy,
                      'field'    => 'slug',
                      'terms'    => $pzsp_show_ids,
                  ),
              ),
              'orderby'        => $pzsp_settings[ 'pzsp_order_by' ],
              'order'          => $pzsp_settings[ 'pzsp_order_az' ],
              'posts_per_page' => $pzsp_number_show,
          );
          break;
        case 'specific_ids' :
          $pzsp_show_ids      = explode(',', $pzsp_settings[ 'pzsp_specific_ids' ]);
          $pzsp_query_options = array(
              'post__in'  => $pzsp_show_ids,
              'orderby'   => $pzsp_settings[ 'pzsp_order_by' ],
              'order'     => $pzsp_settings[ 'pzsp_order_az' ],
              'post_type' => $pzsp_settings[ 'pzsp_content_type' ]
          );
          break;
      }
//pzdebug();
      $this->text_content     = array();
      $this->image_content    = array();
      $this->navigation_array = array();
      $pzsp_content_data      = array();
//pzdebug($pzsp_query_options);
      if ($pzsp_settings[ 'pzsp_content_type' ] == 'rss') {
// todo
// 		} elseif ($pzsp_settings['pzsp_content_type'] == 'gplus_gallery' && class_exists('GPFunctions')  && substr($pzsp_settings['pzsp_gplus_gallery'],0,2)=='G+'){
// 			// GALLERYPLUS GALLERY
// 			$order_by = (!$pzsp_settings['pzsp_order_by']) ? 'title' : $pzsp_settings['pzsp_order_by'];
// 			$order_az = (!$pzsp_settings['pzsp_order_az']) ? 'ASC' : $pzsp_settings['pzsp_order_az'];
// 			$album = $pzsp_settings['pzsp_gplus_gallery'];
// 			$args = array( 'post_type' => 'attachment', 'numberposts' => (($pzsp_settings['pzsp_number_show']>0)?$pzsp_settings['pzsp_number_show']:-1), 'post_status' => null, 'post_parent' => $album, 'orderby' => $order_by, 'order' => $order_az); 
// 			$attachments = get_posts( $args );
// 			$pzsp_content_data = array();
// 			if ($attachments) {
// 				$i = 1;
// 				foreach ( $attachments as $post ) {
// 						setup_postdata($post);
// 						$image_src = wp_get_attachment_image_src( $post->ID, 'full');
// 						$resized_image = self::resize_image($pzsp_settings,$image_src[0],$post->ID,stripslashes(get_the_title($post->ID)));
// 						$link_url = ($post->post_content) ? $post->post_content : 'javascript:void()';
// 						$pzsp_content_data[] = array(
// 							'title'=>stripslashes(get_the_title($post->ID)),
// 							'content'=>stripslashes($post->post_excerpt),
// 							'image'=>$resized_image,
// 							'link'=>$link_url,
// 							'meta' => ''
// 							);
// //					if ($pzsp_settings['pzsp_number_show']>0 && $i++>=$pzsp_settings['pzsp_number_show']) {break;}
// 				}
// 			}
      } elseif ($pzsp_settings[ 'pzsp_content_type' ] == 'ngg_gallery' && class_exists('nggdb')) {
// NEXTGEN GALLERY
        $pzsp_content_data = array();
//			var_dump($pzsp_sort_by,);
        if ($pzsp_settings[ 'pzsp_ngg_order_by' ] == 'rand') {
          $images  = nggdb::get_gallery($pzsp_settings[ 'pzsp_ngg_gallery' ], 'pid', 'ASC');
          $imgsort = shuffle($images);
          $keys    = array_keys($images);
          shuffle($keys);
          $images_rand = array();
          foreach ($keys as $key) {
            $images_rand[ ] = $images[ $key ];
          }
          $images = $images_rand;
        } else {
          $images = nggdb::get_gallery($pzsp_settings[ 'pzsp_ngg_gallery' ], $pzsp_settings[ 'pzsp_ngg_order_by' ], $pzsp_settings[ 'pzsp_order_az' ]);
        }
// pid, alltext, imagedate
        $i = 1;
        foreach ($images as $image) {
          if (empty($pzsp_settings[ 'pzsp_do_not_show' ])) {
            $resized_image = $image->imageURL;
          } else {
            $resized_image = self::resize_image($pzsp_settings, $image->imageURL, $image->pid, stripslashes($image->alttext));
          }
          $pzsp_content_data[ ] = array(
              'title'   => stripslashes($image->alttext),
              'content' => stripslashes($image->description),
              'image'   => $resized_image[ 0 ],
              'alttext' => $resized_image[ 1 ],
              'link'    => 'javascript:void()',
              'meta'    => ''
          );
          if ($pzsp_settings[ 'pzsp_number_show' ] > 0 && $i++ >= $pzsp_settings[ 'pzsp_number_show' ]) {
            break;
          }
        };
//			switch ($pzsp_settings['pzsp_order_by') {
//				case 'title':
//
//
//					break;
//
//				default:
//					break;
//			}
      } elseif ($pzsp_settings[ 'pzsp_content_type' ] == 'gplus_gallery') {
        // TODO: FIX THIS FOR SINCE WP35!
        if (SPDEBUG) {
          echo pzdebug(microtime(true));
        }
        /*
         * IF WORDPRESS OR GALLERYPLUS GALLERY
         */
        //			var_dump(pzsp_get_wp_gallery($pzsp_settings['pzsp_wp_gallery'],$pzsp_settings));

        $order_by = (!$pzsp_settings[ 'pzsp_order_by' ]) ? 'title' : $pzsp_settings[ 'pzsp_order_by' ];
        $order_az = (!$pzsp_settings[ 'pzsp_order_az' ]) ? 'ASC' : $pzsp_settings[ 'pzsp_order_az' ];
        $album    = $pzsp_settings[ 'pzsp_gplus_gallery' ];
        if ($album == '99999999') {
          global $posts;
          $album = $posts[ 0 ]->ID;
        }

        $attachments       = pzsp_get_post_images($album,$order_by,$order_az,$pzsp_settings);
        $pzsp_content_data = array();
        $example           = array(
            'post_id'     => 757,
            'title'       => 'Boardwalk',
            'caption'     => 'Public domain via http://www.burningwell.org/gallery2/v/Landscapes/ocean/DCP_2082.jpg.html',
            'thumb_url'   => 'http://local.wordpress.dev/wp-content/uploads/2011/07/dcp_20821-150x150.jpg',
            'image_url'   => 'http://local.wordpress.dev/wp-content/uploads/2011/07/dcp_20821.jpg',
            'link_url'    => 'http://local.wordpress.dev/wp-content/uploads/2011/07/dcp_20821.jpg',
            'focal_point' => '',
        );

        if ($attachments) {
          $i = 1;
          foreach ($attachments as $post) {
            $image_src     = wp_get_attachment_image_src($post['post_id'], 'full');
            $resized_image = self::resize_image($pzsp_settings, $post[ 'image_url' ], $post[ 'post_id' ], stripslashes($post[ 'title' ]));
// This needs to be fixed up to use the right link urls.
            $link_url             = ($post[ 'link_url' ]) ? $post[ 'link_url' ] : 'javascript:void()';
            $pzsp_content_data[ ] = array(
                'title'   => stripslashes($post[ 'title' ]),
                'content' => stripslashes($post[ 'caption' ]),
                'image'   => $resized_image[ 0 ],
                'alttext' => $resized_image[ 1 ],
                'link'    => $link_url,
                'meta'    => ''
            );
//					if ($pzsp_settings['pzsp_number_show']>0 && $i++>=$pzsp_settings['pzsp_number_show']) {break;}
          }
        }
      } else {
        if (SPDEBUG) {
          echo pzdebug(microtime(true));
        }
        /*			 * ******************
         * POST/PAGE IMAGES
         *
         * ***************** */

        $pzsp_wp_query = new WP_Query($pzsp_query_options);
        if ($pzsp_wp_query->have_posts()) {
//					$i = 0;
          $pzsp_content_data = array();
          while ($pzsp_wp_query->have_posts()) {
            $pzsp_wp_query->the_post();
//pzdebug($pzsp_wp_query->post->ID);
// resize the image
            $resized_image = self::resize_image(
                $pzsp_settings, null, get_the_ID(), get_the_title()
            );
//						$pzsp_the_content = ($pzsp_settings['pzsp_text_prune'])?get_the_excerpt():get_the_content();
            ob_start();
            if (has_excerpt() && !empty($pzsp_settings[ 'pzsp_text_prune' ])) {
              echo self::strip_columns_shortcodes(get_the_excerpt());
              $the_contents = ob_get_contents() . '%real_excerpt%';
            } else {
              if (!empty($pzsp_settings[ 'pzsp_strip_iframes' ])) {
                echo self::strip_columns_shortcodes(self::strip_iframes(wpautop(get_the_content())));
              } else {
                echo self::strip_columns_shortcodes(wpautop(get_the_content()));
              }
              $the_contents = ob_get_contents();
            }
            ob_end_clean();

            $pzsp_content_data[ ] = array(
                'link'    => get_permalink(),
                'title'   => get_the_title(),
                'content' => $the_contents,
                'image'   => $resized_image[ 0 ],
                'alttext' => $resized_image[ 1 ],
                'meta'    => get_post_custom()
            );
//						$pzsp_slide_meta = get_post_custom();
//						$pzsp_the_content = get_the_content();
//						$pzsp_link_to = get_permalink();
//						$pzsp_post_title = get_the_title();
//						$i++;
          }
        } // End while loop

        // Tell WP to use the main query again
        wp_reset_postdata();
      }


      if (!empty($pzsp_settings[ 'pzsp_randomise_slides' ])) {
        shuffle($pzsp_content_data);
      }

      $i = 0;
      foreach ($pzsp_content_data as $pzsp_content_datum) {
        self::process_content_data($pzsp_settings, $i, $pzsp_content_datum);
        $i++;
      }


      $this->content_lefttop     = ($pzsp_settings[ 'pzsp_layout' ] == 'ImageLeft' || $pzsp_settings[ 'pzsp_layout' ] == 'ImageTop' || $pzsp_settings[ 'pzsp_layout' ] == 'ImageOnly') ? $this->image_content : $this->text_content;
      $this->content_rightbottom = ($pzsp_settings[ 'pzsp_layout' ] == 'ImageRight' || $pzsp_settings[ 'pzsp_layout' ] == 'ImageBottom') ? $this->image_content : $this->text_content;
      $this->navigation_location = $pzsp_settings[ 'pzsp_nav_location' ];
    }

    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: resize_image
     *
     * *************************** */

    private function resize_image($pzsp_settings, $pzsp_image_maybe = null, $uid, $pz_title)
    {
      $pzsp_image_size = ($pzsp_settings[ 'pzsp_layout' ] == 'ImageOnly') ? 100 : $pzsp_settings[ 'pzsp_image_size' ];
      if ($pzsp_settings[ 'pzsp_layout' ] == 'ImageLeft' || $pzsp_settings[ 'pzsp_layout' ] == 'ImageRight') {
        $image_dimensions = array(($pzsp_image_size / 100), 1);
      } else {
        $image_dimensions = array(1, ($pzsp_image_size / 100));
      }
      $pzsp_respect_focal_point = (isset($pzsp_settings[ 'pzsp_respect_focal_point' ])) ? $pzsp_settings[ 'pzsp_respect_focal_point' ] : false;
      $pzsp_less_nav            = ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterleft%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%') ? (100 - $pzsp_settings[ 'pzsp_nav_width' ]) / 100 : 1;
      $resized_image            = pzsp_process_image(
          $pzsp_settings[ 'pzsp_post_id' ], PZSP_CACHE, $image_dimensions[ 0 ] * $pzsp_settings[ 'pzsp_contents_width' ] * $pzsp_less_nav, $image_dimensions[ 1 ] * $pzsp_settings[ 'pzsp_contents_height' ], $pzsp_settings[ 'pzsp_sizing_type' ], $pzsp_settings[ 'pzsp_horz_crop_align' ], $pzsp_settings[ 'pzsp_vert_crop_align' ], $pzsp_settings[ 'pzsp_image_fill' ], null, $pzsp_settings[ 'pzsp_quality' ], (!empty($pzsp_settings[ 'pzsp_greyscale' ]) && $pzsp_settings[ 'pzsp_greyscale' ] == 'on'), $pzsp_image_maybe, $uid, $pz_title, $pzsp_respect_focal_point, (isset($pzsp_settings[ 'pzsp_do_not_resize' ]) ? $pzsp_settings[ 'pzsp_do_not_resize' ] : false)
      );

//		var_dump($image_dimensions[0] * $pzsp_settings['pzsp_contents_width'] * $pzsp_less_nav, $image_dimensions[1] * $pzsp_settings['pzsp_contents_height']);
      return $resized_image;
    }

    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: process_content_data
     *
     * *************************** */

    private function process_content_data($pzsp_settings, $i, $pzsp_content_datum)
    {
      $pzsp_link_to = $pzsp_content_datum[ 'link' ];

      if ($pzsp_settings[ 'pzsp_content_type' ] == 'pzsp-slides') {
        if (!empty($pzsp_content_datum[ 'meta' ][ 'pzsp_destination_url' ][ 0 ])) {
          $pzsp_link_to = $pzsp_content_datum[ 'meta' ][ 'pzsp_destination_url' ][ 0 ];
        } else {
          $pzsp_link_to = 'javascript:void()';
        }
      }

      $this->navigation_array[ $i ][ 'link' ] = $pzsp_link_to;

      switch ($pzsp_settings[ 'pzsp_nav_type' ]) {
        case 'text':
          $this->navigation_array[ $i ][ 'title' ] = '<span class="pzsp-nav-is-titles">' . $pzsp_content_datum[ 'title' ] . '</span>';
          break;
        case 'bullets':
          $this->navigation_array[ $i ][ 'title' ] = '<span class="pzsp-nav-is-bullets">&bull;</span>';
          break;
        case 'squares':
          // TODO: can use &#9724
          $this->navigation_array[ $i ][ 'title' ] = '<span class="pzsp-nav-is-bullets"><span class="draw-square-bullet"></span></span></span>';
          break;
        case 'asterisks':
          $this->navigation_array[ $i ][ 'title' ] = '<span class="pzsp-nav-is-asterisks">*</span>';
          break;
        case 'numbers':
          $this->navigation_array[ $i ][ 'title' ] = '<span class="pzsp-nav-is-numbers">' . ($i + 1) . '</span>';
          break;
      }
      $target_window            = (!empty($pzsp_content_datum[ 'meta' ][ 'pzsp_destination_window' ]) ? $pzsp_content_datum[ 'meta' ][ 'pzsp_destination_window' ][ 0 ] : '_self');
      $this->text_content[ $i ] = '';
      $this->text_content[ $i ] .= '<div class="pzsp-text-content" style="padding:' . $pzsp_settings[ 'pzsp_text_padding' ] . 'px;' . (!empty($pzsp_settings[ 'pzsp_full_width' ]) ? 'margin:0 auto;max-width:' . $pzsp_settings[ 'pzsp_text_size' ] . 'px' : null) . '">';
      if (!empty($pzsp_settings[ 'pzsp_delink_title' ])) {
        $this->text_content[ $i ] .= ((!empty($pzsp_settings[ 'pzsp_hide_title' ]) && $pzsp_settings[ 'pzsp_hide_title' ] == 'on')) ? null : '<h2 class="pzsp-entry-title"><a>' . $pzsp_content_datum[ 'title' ] . '</a></h2>';
      } else {
        $this->text_content[ $i ] .= ((!empty($pzsp_settings[ 'pzsp_hide_title' ]) && $pzsp_settings[ 'pzsp_hide_title' ] == 'on')) ? null : '<h2 class="pzsp-entry-title"><a href="' . $pzsp_link_to . '" title="' . $pzsp_content_datum[ 'title' ] . '" target=' . $target_window . '>' . $pzsp_content_datum[ 'title' ] . '</a></h2>';
      }
      $pzsp_settings[ 'pzsp_read_more' ] = (!isset($pzsp_settings[ 'pzsp_read_more' ])) ? '[Read more]' : $pzsp_settings[ 'pzsp_read_more' ];


      if (!empty($pzsp_settings[ 'pzsp_text_prune' ]) && strlen(strip_tags(strip_shortcodes($pzsp_content_datum[ 'content' ]))) > $pzsp_settings[ 'pzsp_text_prune' ] && strpos($pzsp_content_datum[ 'content' ], '%real_excerpt%') == 0
      ) {

        $pzsp_settings[ 'pzsp_trunc-char' ] = (!isset($pzsp_settings[ 'pzsp_trunc-char' ])) ? 'ellipses' : $pzsp_settings[ 'pzsp_trunc-char' ];

        if ($pzsp_settings[ 'pzsp_trunc-char' ] == 'arrows') {
          $truncchar = '<span class="pzsp-more-indicator">&raquo; </span>';
        } elseif ($pzsp_settings[ 'pzsp_trunc-char' ] == 'ellipses') {
          $truncchar = '<span class="pzsp-more-indicator">&#8230; </span>';
        }

        //, '<b><i><sup><sub><em><strong><u><br><a><p><br><hr><ul><li><img>'
        // need to leave in line breaks maybe...
        $pzsp_the_content = substr(strip_tags(strip_shortcodes($pzsp_content_datum[ 'content' ])), 0, $pzsp_settings[ 'pzsp_text_prune' ]) . $truncchar . '<a href="' . $pzsp_link_to . '"title="' . $pzsp_content_datum[ 'title' ] . '" target=' . (!empty($pzsp_content_datum[ 'meta' ][ 'pzsp_destination_window' ][ 0 ]) ? $pzsp_content_datum[ 'meta' ][ 'pzsp_destination_window' ][ 0 ] : null) . '>' . $pzsp_settings[ 'pzsp_read_more' ] . '</a>';
      } else {
        $pzsp_the_content = str_replace('%real_excerpt%', null, $pzsp_content_datum[ 'content' ]) . (isset($pzsp_settings[ 'pzsp_force_readmore' ]) && !empty($pzsp_settings[ 'pzsp_text_prune' ]) ? $pzsp_settings[ 'pzsp_read_more' ] : null);
      }
      $this->text_content[ $i ] .= ((!empty($pzsp_settings[ 'pzsp_hide_body' ]) && $pzsp_settings[ 'pzsp_hide_body' ] == 'on')) ? null : '<div class="pzsp-entry-body">' . $pzsp_the_content . '</div>';
      $this->text_content[ $i ] .= '</div> <!-- end pzsp-text-content -->';

// Check for embeds
      if (isset($pzsp_content_datum[ 'meta' ][ 'pzsp_video_url' ]) && !!($pzsp_content_datum[ 'meta' ][ 'pzsp_video_url' ][ 0 ])) {

        // Embed oembed
        $pzsp_image_size = ($pzsp_settings[ 'pzsp_layout' ] == 'ImageOnly') ? 100 : $pzsp_settings[ 'pzsp_image_size' ];
        $pzsp_dimensions = array(
            'width'  => $pzsp_settings[ 'pzsp_contents_width' ] * $pzsp_image_size / 100,
            'height' => $pzsp_settings[ 'pzsp_contents_height' ]
        );
//      var_dump(esc_html($pzsp_content_datum['meta']['pzsp_video_url'][0]));
        $pzsp_source = wp_oembed_get(($pzsp_content_datum[ 'meta' ][ 'pzsp_video_url' ][ 0 ]), $pzsp_dimensions);
        // Enable youTube and Vimeo API
        if (strpos($pzsp_source, 'youtube')) {
          $pzsp_source = preg_replace("/src=\".*?(?=\")/u", "$0&enablejsapi=1", $pzsp_source);
        } else {
          $pzsp_source = preg_replace("/src=\".*?(?=\")/u", "$0&api=1", $pzsp_source);
        }

        $pzsp_source                 = (!$pzsp_source) ? '<span style="color:#fff;font-size:11px;font-family:sans-serif;font-weight:bold;">Source not available</span>' : $pzsp_source;
        $this->image_content[ $i++ ] = '<table class="pzsp-video" style="max-height:' . $pzsp_settings[ 'pzsp_contents_height' ] . 'px;">
							<tr><td class="' . (($pzsp_settings[ 'pzsp_centre_video' ] == 'on') ? 'pzsp-centre-video' : null) . '">
								<div class="pzsp-image-content is-video">' . $pzsp_source . '</div>
							</td></tr>
							</table>';
      } elseif (isset($pzsp_content_datum[ 'meta' ][ 'pzsp_embed_slider' ]) && !!($pzsp_content_datum[ 'meta' ][ 'pzsp_embed_slider' ][ 0 ])) {
// Embed a slideer shortcode (not working yet)
        $this->image_content[ $i++ ] = '<div class="pzsp-embedded-content pzsp-slider-embed-' . $pzsp_content_datum[ 'meta' ][ 'pzsp_embed_slider' ][ 0 ] . '">[sliderplus ' . $pzsp_content_datum[ 'meta' ][ 'pzsp_embed_slider' ][ 0 ] . ' embedded]</div>';
      } elseif (isset($pzsp_content_datum[ 'meta' ][ 'pzsp_embed_code' ]) && !!($pzsp_content_datum[ 'meta' ][ 'pzsp_embed_code' ][ 0 ])) {
// Embedd code
        // Enable YouTube and Vimeo APIs
        if (substr($pzsp_content_datum[ 'meta' ][ 'pzsp_embed_code' ][ 0 ], 0, 6) == '[video' || substr($pzsp_content_datum[ 'meta' ][ 'pzsp_embed_code' ][ 0 ], 0, 6) == '[audio') {
          $pzsp_source = do_shortcode(($pzsp_content_datum[ 'meta' ][ 'pzsp_embed_code' ][ 0 ]));
        } else {
          $pzsp_source = $pzsp_content_datum[ 'meta' ][ 'pzsp_embed_code' ][ 0 ];
        }
        if (strpos($pzsp_source, 'youtube')) {
          $pzsp_source = preg_replace("/src=\".*?(?=\")/u", "$0?enablejsapi=1", $pzsp_source);
        } elseif ((substr($pzsp_content_datum[ 'meta' ][ 'pzsp_embed_code' ][ 0 ], 0, 6) != '[video' || substr($pzsp_content_datum[ 'meta' ][ 'pzsp_embed_code' ][ 0 ], 0, 6) != '[audio')) {
          $pzsp_source = preg_replace("/src=\".*?(?=\")/u", "$0?api=1", $pzsp_source);
        }

        $this->image_content[ $i++ ] = '<div class="pzsp-embedded-content is-code">' . $pzsp_source . '</div>';
      } else {


        if (isset($pzsp_settings[ 'pzsp_text_fill' ]) && !$pzsp_content_datum[ 'image' ]) {
          $pzsp_img_src = 'No image';
        } else {
          $pzsp_img_src = '<img src="' . $pzsp_content_datum[ 'image' ] . '" class="pzsp-slide-image" alt="' . $pzsp_content_datum[ 'alttext' ] . '"/>';
        }
        if (isset($pzsp_settings[ 'pzsp_link_image' ])) {
          $this->image_content[ $i ] = '<div class="pzsp-image-content"><a href="' . $pzsp_link_to . '" title="' . $pzsp_content_datum[ 'title' ] . '"  target=' . (!empty($pzsp_content_datum[ 'meta' ][ 'pzsp_destination_window' ][ 0 ]) ? $pzsp_content_datum[ 'meta' ][ 'pzsp_destination_window' ][ 0 ] : null) . '>' . $pzsp_img_src . '</a></div>';
        } else {
          $this->image_content[ $i ] = '<div class="pzsp-image-content">' . $pzsp_img_src . '</div>';
        }
      }
    }

    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: Add the contents
     *
     * *************************** */

    function contents($pzsp_settings)
    {
//pzdebug((array)$this);

      $final_content = null;
      if (count($this->content_lefttop)) {

        for ($i = 0; $i < count($this->content_lefttop); $i++) {

// Resize text area if no image
          if (strip_tags($this->content_lefttop[ $i ]) == 'No image' || strip_tags($this->content_rightbottom[ $i ]) == 'No image') {
            $pzsp_text_size                  = 100;
            $pzsp_text_nudge                 = null;
            $this->content_lefttop[ $i ]     = (strip_tags($this->content_lefttop[ $i ]) == 'No image') ? '' : $this->content_lefttop[ $i ];
            $this->content_rightbottom[ $i ] = (strip_tags($this->content_rightbottom[ $i ]) == 'No image') ? '' : $this->content_rightbottom[ $i ];
          } else {
            $pzsp_text_size  = $pzsp_settings[ 'pzsp_text_size' ];
            $pzsp_text_nudge = intval((isset($pzsp_settings[ 'pzsp_text_nudge' ])) ? $pzsp_settings[ 'pzsp_text_nudge' ] : 0);
          }
          switch ($pzsp_settings[ 'pzsp_layout' ]) {
            case 'ImageLeft' :
              $lt_is  = 'is-image';
              $rb_is  = 'is-text';
              $rb_css = 'width:' . (!empty($pzsp_settings[ 'pzsp_full_width' ]) ? '100' : $pzsp_text_size) . '%; right:' . $pzsp_text_nudge . 'px;height:100%;';
              $lt_css = 'width:' . ($pzsp_settings[ 'pzsp_image_size' ]) . '%; left:0;height:100%;';
              break;
            case 'ImageRight' :
              $lt_is  = 'is-text';
              $rb_is  = 'is-image';
              $lt_css = 'width:' . (!empty($pzsp_settings[ 'pzsp_full_width' ]) ? '100' : $pzsp_text_size) . '%; left:' . $pzsp_text_nudge . 'px;height:100%;';
              $rb_css = 'width:' . ($pzsp_settings[ 'pzsp_image_size' ]) . '%; right:0;height:100%;';
              break;
            case 'ImageTop' :
              $rb_is  = 'is-text';
              $lt_is  = 'is-image';
              $rb_css = 'max-height:' . ($pzsp_text_size) . '%; bottom:' . $pzsp_text_nudge . 'px;width:100%;';
              $lt_css = 'height:' . ($pzsp_settings[ 'pzsp_image_size' ]) . '%; top:0;width:100%;';
              break;
            case 'ImageBottom' :
              $lt_is  = 'is-text';
              $rb_is  = 'is-image';
              $lt_css = 'max-height:' . ($pzsp_text_size) . '%; top:' . $pzsp_text_nudge . 'px;width:100%;';
              $rb_css = 'height:' . ($pzsp_settings[ 'pzsp_image_size' ]) . '%;bottom:0;width:100%;';
              break;
            case 'ImageOnly' :
              $lt_is  = 'is-image';
              $rb_is  = 'is-text';
              $lt_css = 'height:100%;width:100%;';
              break;
            case 'TextOnly' :
              $lt_is  = 'is-text';
              $rb_is  = 'is-image';
              $lt_css = 'height:100%;width:100%;';
              break;
          }
          $final_content .= '<div class="pzsp-content-container  pzsp-slide-id-' . ($i + 1) . '" style="width:100%;height:100%;max-height:' . $pzsp_settings[ 'pzsp_contents_height' ] . 'px;">';
// if no image content then expand text if so it fills

          $final_content .= '<div class="pzsp-content-leftortop ' . $lt_is . '" style="' . $lt_css . '">' . $this->content_lefttop[ $i ] . '</div><!-- end pzsp content left or top or whole-->';
          if ($pzsp_settings[ 'pzsp_layout' ] != 'ImageOnly' && $pzsp_settings[ 'pzsp_layout' ] != 'TextOnly') {
            $final_content .= '<div class="pzsp-content-rightorbottom ' . $rb_is . '" style="' . $rb_css . '">' . $this->content_rightbottom[ $i ] . '</div><!-- end pzsp content right or bottom -->';
          }
          $final_content .= '</div>';
        }
      } else {
        $lt_is  = 'is-text';
        $rb_is  = 'is-image';
        $lt_css = 'height:100%;width:100%;';
        $rb_css = '';
        $final_content .= '<div class="pzsp-content-container  pzsp-slide-id-no-content" style="width:100%;max-height:' . $pzsp_settings[ 'pzsp_contents_height' ] . 'px;">';
        $final_content .= '<div class="pzsp-content-leftortop ' . $lt_is . '" style="' . $lt_css . '"><br/><br/><p>&nbsp;&nbsp;Your criteria for this slideshow returned no results!</p></div><!-- end pzsp content left or top or whole-->';
        if ($pzsp_settings[ 'pzsp_layout' ] != 'ImageOnly' && $pzsp_settings[ 'pzsp_layout' ] != 'TextOnly') {
          $final_content .= '<div class="pzsp-content-rightorbottom ' . $rb_is . '" style="' . $rb_css . '"><br/><br/><p>&nbsp;&nbsp;Your criteria for this slideshow returned no results!</p></div><!-- end pzsp content right or bottom -->';
        }
        $final_content .= '</div>';
      }
      $this->PZSP = str_replace('%contents%', $final_content, $this->PZSP);
    }

    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: Add the navigation
     *
     * *************************** */

    function navigation($pzsp_settings)
    {
      $pzsp_nav_height          = '';
      $nav_css                  = '';
      $pzsp_nav_items_alignment = '';
      $navtype                  = 'navtype-' . $pzsp_settings[ 'pzsp_nav_type' ];
      $pzsp_bna_padding         = null;
      $styling                  = ($navtype == 'navtype-text') ? 'style="width:' . (100 / max(count($this->navigation_array), 1)) . '%;"' : null;

      $final_nav = null;
      for ($i = 0; $i < count($this->navigation_array); $i++) {
        if ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterleft%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%') {
          if ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%') {
            $nav_css = 'width:' . $pzsp_settings[ 'pzsp_nav_width' ] . '%;float:right;';
          } else {
            $nav_css = 'width:' . $pzsp_settings[ 'pzsp_nav_width' ] . '%;float:left;';
          }
          if (!empty($pzsp_settings[ 'pzsp_nav_mouseover' ])) {
            $final_nav .= '<tr class="pzsp-nav-item pzsp-nav-item-' . ($i + 1) . ' " ' . $styling . ' ><td class="' . $navtype . (!empty($pzsp_settings[ 'pzsp_nav_type_phone_bullets' ]) ? ' phone-bullet-nav ' : null) . '"><a href="' . $this->navigation_array[ $i ][ 'link' ] . '">' . $this->navigation_array[ $i ][ 'title' ] . '</a></td></tr>';
          } else {
            $final_nav .= '<tr class="pzsp-nav-item pzsp-nav-item-' . ($i + 1) . ' " ' . $styling . ' ><td class="' . $navtype . (!empty($pzsp_settings[ 'pzsp_nav_type_phone_bullets' ]) ? ' phone-bullet-nav ' : null) . '"><a href="#">' . $this->navigation_array[ $i ][ 'title' ] . '</a></td></tr>';
          }
          $pzsp_nav_items_alignment = '';
          $pzsp_nav_height          = 'max-height:' . $pzsp_settings[ 'pzsp_contents_height' ] . 'px;';
//				$pzsp_nav_height = 'height:' . $pzsp_settings['pzsp_contents_height'] . 'px;';
          $pzsp_bna_padding = (($pzsp_settings[ 'pzsp_nav_type' ]) != 'text') ? 'padding-top:10px;' : null;
        } else {
// Nav is top or bottom
          switch ($pzsp_settings[ 'pzsp_nav_align' ]) {
// Centre
            case 'centre':
              $pzsp_nav_alignment       = 'float:left;';
              $pzsp_nav_items_alignment = 'margin:0 auto;';
              break;
// Left or top
            case 'lefttop':
              $pzsp_nav_alignment       = 'float:left;';
              $pzsp_nav_items_alignment = 'float:left;';
              break;
// Right or bottom
            case 'rightbottom':
              $pzsp_nav_alignment       = 'float:right;';
              $pzsp_nav_items_alignment = 'float:right;';
              break;
          }
          $pzsp_nav_height = '';
          $nav_css         = 'width:100%;' . $pzsp_nav_alignment;
          if (!empty($pzsp_settings[ 'pzsp_nav_mouseover' ])) {
            $final_nav .= '<td class="pzsp-nav-item pzsp-nav-item-' . ($i + 1) . ' ' . $navtype . (!empty($pzsp_settings[ 'pzsp_nav_type_phone_bullets' ]) ? ' phone-bullet-nav ' : null) . '" ' . $styling . ' ><a href="' . $this->navigation_array[ $i ][ 'link' ] . '">' . $this->navigation_array[ $i ][ 'title' ] . '</a></td>';
          } else {
            $final_nav .= '<td class="pzsp-nav-item pzsp-nav-item-' . ($i + 1) . ' ' . $navtype . (!empty($pzsp_settings[ 'pzsp_nav_type_phone_bullets' ]) ? ' phone-bullet-nav ' : null) . '" ' . $styling . ' ><a href="#">' . $this->navigation_array[ $i ][ 'title' ] . '</a></td>';
          }
        }
      }
      if ($pzsp_settings[ 'pzsp_nav_location' ] == '%navoutertop%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navouterbottom%') {
        $final_nav = '<tr>' . $final_nav . '</tr>';
      }
// You're probably wondering why the hell this uses tables to show what is essentially a menu. Why not ul/li? Well... because of CSS! 
// Tables where the only way I could get equal height cells for when nav is titles and one wraps lines.

      if ($navtype == 'navtype-text' || ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterleft%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%')) {
        $nav_width = '100%;';
      } else {
        $nav_width = (10 * count($this->navigation_array)) . 'px;';
      }
      if (!empty($pzsp_settings[ 'pzsp_nav_override_bgcolour' ]) && $pzsp_settings[ 'pzsp_nav_override_bgcolour' ] == 'on') {
        $pzsp_nav_bgcolour = (!isset($pzsp_settings[ 'pzsp_shadow_bgcolour' ]) || $pzsp_settings[ 'pzsp_shadow_bgcolour' ] == 'none') ? 'transparent' : $pzsp_settings[ 'pzsp_shadow_bgcolour' ];
        $pzsp_nav_bgcolour = 'background:' . $pzsp_nav_bgcolour . ';';
      } else {
        $pzsp_nav_bgcolour = null;
      }
      $pzsp_nav_dir = ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterleft%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%') ? 'vertical' : 'horizontal';

      $pzsp_nav_inside = null;
      if (!empty($pzsp_settings[ 'pzsp_nav_inside' ])) {
        switch ($pzsp_settings[ 'pzsp_nav_location' ]) {
          case '%navoutertop%':
            $pzsp_nav_inside = 'innertop';
            break;
          case '%navouterbottom%':
            $pzsp_nav_inside = 'innerbottom';
            break;
        }
      }
      $nav_html   = '<div class="pzsp-nav-container ' . $pzsp_nav_dir . ' ' . $pzsp_nav_inside . '" style="' . $nav_css . $pzsp_nav_bgcolour . $pzsp_nav_height . '">
							%shadow-in-nav%
				<table class="pzsp-navigation" style="width:' . $nav_width . $pzsp_nav_items_alignment . $pzsp_bna_padding . '">' . $final_nav . '</table>
			</div><!-- end pzsp nav -->';
      $this->PZSP = str_replace($pzsp_settings[ 'pzsp_nav_location' ], $nav_html, $this->PZSP);
    }

    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: Add next/prev nav
     *
     * *************************** */

    function page_flip($pzsp_settings)
    {

      switch ($pzsp_settings[ 'pzsp_hide_hover_nav' ]) {
        case 'always':
          break;
        case 'on':
        case 'never' :
          return;
        case 'onhover' :
        case '':
        default:
          break;
      }

//		if (isset($pzsp_settings['pzsp_hide_hover_nav']) && $pzsp_settings['pzsp_hide_hover_nav'] == 'on')
//		{
//			return;
//		}

      $pzsp_nudge_for_nav_left  = null;
      $pzsp_nudge_for_nav_right = null;
      if ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterleft%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%') {
        if ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%') {
          $pzsp_nudge_for_nav_right = 'right:' . $pzsp_settings[ 'pzsp_nav_width' ] . '%;';
        } else {
          $pzsp_nudge_for_nav_left = 'left:' . $pzsp_settings[ 'pzsp_nav_width' ] . '%;';
        }
      }

      $pzsp_nav_icons = array(
          array(
              'left'  => '<span aria-hidden="true" class="icon-arrow-left"></span>',
              'right' => '<span aria-hidden="true" class="icon-arrow-right"></span>
				'),
          array(
              'left'  => '<span aria-hidden="true" class="icon-arrow-left-2"></span>',
              'right' => '<span aria-hidden="true" class="icon-arrow-right-2"></span>
				'),
          array(
              'left'  => '<span aria-hidden="true" class="icon-arrow-left-3"></span>',
              'right' => '<span aria-hidden="true" class="icon-arrow-right-3"></span>
				'),
          array(
              'left'  => '<span aria-hidden="true" class="icon-caret-left"></span>',
              'right' => '<span aria-hidden="true" class="icon-caret-right"></span>
				'),
          array(
              'left'  => '<span aria-hidden="true" class="icon-arrow-left-5"></span>',
              'right' => '<span aria-hidden="true" class="icon-arrow-right-5"></span>
				'),
          array(
              'left'  => '<span aria-hidden="true" class="icon-arrow-left-7"></span>',
              'right' => '<span aria-hidden="true" class="icon-arrow-right-7"></span>
				'),
          array(
              'left'  => '<span aria-hidden="true" class="icon-arrow-left-8"></span>',
              'right' => '<span aria-hidden="true" class="icon-arrow-right-8"></span>
				'),
          array(
              'left'  => '<span aria-hidden="true" class="icon-left"></span>',
              'right' => '<span aria-hidden="true" class="icon-right"></span>
				'),
          array(
              'left'  => '<span aria-hidden="true" class="icon-arrow-left-9"></span>',
              'right' => '<span aria-hidden="true" class="icon-arrow-right-9"></span>
				'),
          array(
              'left'  => '<span aria-hidden="true" class="icon-arrow-left-10"></span>',
              'right' => '<span aria-hidden="true" class="icon-arrow-right-10"></span>
				'),
          array(
              'left'  => '<span aria-hidden="true" class="icon-arrow"></span>',
              'right' => '<span aria-hidden="true" class="icon-arrow-2"></span>
				'),
      );
//		$this->PZSP	 = str_replace( '%pageprev%', '<a href="#" id="pzsp-prev-' . $pzsp_settings[ 'pzsp_short_name' ] . '" class="pzsp-prev"  style="' . $pzsp_nudge_for_nav_left . 'height:' . ($pzsp_settings[ 'pzsp_contents_height' ]) . 'px;' . (!empty( $pzsp_settings[ 'pzsp_custom_hover_nav_prev' ] ) ? 'background-image:url(' . $pzsp_settings[ 'pzsp_custom_hover_nav_prev' ] . ')' : null) . '"></a>', $this->PZSP );
//		$this->PZSP	 = str_replace( '%pagenext%', '<a href="#" id="pzsp-next-' . $pzsp_settings[ 'pzsp_short_name' ] . '" class="pzsp-next"  style="' . $pzsp_nudge_for_nav_right . 'height:' . ($pzsp_settings[ 'pzsp_contents_height' ]) . 'px;' . (!empty( $pzsp_settings[ 'pzsp_custom_hover_nav_next' ] ) ? 'background-image:url(' . $pzsp_settings[ 'pzsp_custom_hover_nav_next' ] . ')' : null) . '"></a>', $this->PZSP );
      if (!isset($pzsp_settings[ 'pzsp_nav_icons' ])) {
//			$pzsp_settings[ 'pzsp_custom_hover_nav_prev' ] = PZSP_PLUGIN_URL . '/libs/images/icons/prev.png';
//			$pzsp_settings[ 'pzsp_custom_hover_nav_next' ] = PZSP_PLUGIN_URL . '/libs/images/icons/next.png';
        $pzsp_settings[ 'pzsp_nav_icons' ] = 1;
      }
      $pzsp_prev_icon = $pzsp_nav_icons[ ($pzsp_settings[ 'pzsp_nav_icons' ] - 1) ][ 'left' ];
      $pzsp_prev_bg   = null;
      $pzsp_next_icon = $pzsp_nav_icons[ ($pzsp_settings[ 'pzsp_nav_icons' ] - 1) ][ 'right' ];
      $pzsp_next_bg   = null;

      if (!empty($pzsp_settings[ 'pzsp_custom_hover_nav_prev' ])) {
        $pzsp_prev_icon = ' ';
        $pzsp_prev_bg   = 'background-image:url(' . $pzsp_settings[ 'pzsp_custom_hover_nav_prev' ] . ')';
      }
      if (!empty($pzsp_settings[ 'pzsp_custom_hover_nav_next' ])) {
        $pzsp_next_icon = ' ';
        $pzsp_next_bg   = 'background-image:url(' . $pzsp_settings[ 'pzsp_custom_hover_nav_next' ] . ')';
      }
      $this->PZSP = str_replace('%pageprev%', '<a href="#" id="pzsp-prev-' . $pzsp_settings[ 'pzsp_short_name' ] . '" class="pzsp-prev ' . $pzsp_settings[ 'pzsp_hide_hover_nav' ] . '"  style="' . $pzsp_nudge_for_nav_left . 'max-height:' . ($pzsp_settings[ 'pzsp_contents_height' ]) . 'px;' . $pzsp_prev_bg . '">' . $pzsp_prev_icon . '</a>', $this->PZSP);
      $this->PZSP = str_replace('%pagenext%', '<a href="#" id="pzsp-next-' . $pzsp_settings[ 'pzsp_short_name' ] . '" class="pzsp-next ' . $pzsp_settings[ 'pzsp_hide_hover_nav' ] . '"  style="' . $pzsp_nudge_for_nav_right . 'max-height:' . ($pzsp_settings[ 'pzsp_contents_height' ]) . 'px;' . $pzsp_next_bg . '">' . $pzsp_next_icon . '</a>', $this->PZSP);
    }

    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: Add shadow
     *
     * *************************** */

    function shadow($pzsp_settings)
    {
      if ($pzsp_settings[ 'pzsp_shadow_location' ] == 'contents' && $pzsp_settings[ 'pzsp_shadows' ] != 'none') {
        $pzsp_shadow_bgcolour = (!isset($pzsp_settings[ 'pzsp_shadow_bgcolour' ]) || $pzsp_settings[ 'pzsp_shadow_bgcolour' ] == 'none') ? 'transparent' : $pzsp_settings[ 'pzsp_shadow_bgcolour' ];
// Note: This is not perfect, and some shadows may cut off...
        if ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterleft%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navoutertop%') {
          $pzsp_shadow_height = 'max-height:' . (80 * ($pzsp_settings[ 'pzsp_contents_width' ] / 961)) . 'px;';
          $this->PZSP         = str_replace(
              '%shadow%', '<div class="pzsp_slider_shadow" style="background-color:' . $pzsp_shadow_bgcolour . '; width:' . ($pzsp_settings[ 'pzsp_contents_width' ]) . 'px;' . $pzsp_shadow_height . '"><img src="' . PZSP_PLUGIN_URL . '/css/images/' . $pzsp_settings[ 'pzsp_shadows' ] . '"  width="' . ($pzsp_settings[ 'pzsp_contents_width' ]) . 'px"/></div>', $this->PZSP);
        } else {
          $this->PZSP = str_replace(
              '%shadow-in-nav%', '<div class="pzsp_slider_shadow" style="background-color:' . $pzsp_shadow_bgcolour . '; width:' . ($pzsp_settings[ 'pzsp_contents_width' ]) . 'px;"><img src="' . PZSP_PLUGIN_URL . '/css/images/' . $pzsp_settings[ 'pzsp_shadows' ] . '"  width="' . ($pzsp_settings[ 'pzsp_contents_width' ]) . 'px"/></div>', $this->PZSP);
        }
      }
    }

    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: Configure slides
     *
     * *************************** */

    function slide_config($pzsp_settings)
    {
      $pzsp_trans_type = maybe_unserialize($pzsp_settings[ 'pzsp_trans_type' ]);
      if (is_array($pzsp_trans_type)) {
        $pzsp_trans_type = $pzsp_trans_type[ 0 ];
      }
      switch ($pzsp_trans_type) {
        case 'tileBlindHorz':
        case 'tileSlideHorz':
          $pzsp_effects = 'data-cycle-fx ="' . str_replace('Horz', '', $pzsp_trans_type) . '" data-cycle-tile-vertical=false ';
          break;
        default:
          $pzsp_effects = 'data-cycle-fx ="' . $pzsp_trans_type . '"';
          break;
      }

      if ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterleft%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%') {
        $pzsp_page_selector = '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-navigation tbody';
      } else {
        $pzsp_page_selector = '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-navigation tbody tr';
      }
      $pzsp_less_nav = ($pzsp_settings[ 'pzsp_nav_location' ] == '%navouterleft%' || $pzsp_settings[ 'pzsp_nav_location' ] == '%navouterright%') ? (100 - $pzsp_settings[ 'pzsp_nav_width' ]) / 100 : 1;

      $the_data = '';
      $the_data .= '  data-cycle-pager="' . $pzsp_page_selector . '"';
      $the_data .= '	data-cycle-pager-template=""';
      $the_data .= '	data-cycle-auto-height = "' . round($pzsp_settings[ 'pzsp_contents_width' ] * $pzsp_less_nav) . ':' . $pzsp_settings[ 'pzsp_contents_height' ] . '"';
      $the_data .= '	data-cycle-speed="' . ((empty($pzsp_settings[ 'pzsp_trans_duration' ]) ? 1 : $pzsp_settings[ 'pzsp_trans_duration' ]) * 1000) . '"';
      $the_data .= '	data-cycle-timeout="' . ($pzsp_settings[ 'pzsp_trans_display' ] * 1000) . '"';
      $the_data .= '	data-cycle-manual-trump="' . (!empty($pzsp_settings[ 'pzsp_trans_interrupt' ]) ? 1 : 0) . '"';
      $the_data .= '	data-cycle-sync="' . ((!empty($pzsp_settings[ 'pzsp_trans_sync' ])) * 1) . '"';
      $the_data .= '	data-cycle-pager-event="' . (!empty($pzsp_settings[ 'pzsp_nav_mouseover' ]) && ($pzsp_settings[ 'pzsp_nav_mouseover' ] == 'on') ? 'mouseover' : 'click') . '"';
      $the_data .= '	data-cycle-next = "a#pzsp-next-' . $pzsp_settings[ 'pzsp_short_name' ] . '"';
      $the_data .= '	data-cycle-prev = "a#pzsp-prev-' . $pzsp_settings[ 'pzsp_short_name' ] . '"';
      $the_data .= '	data-cycle-easing ="' . (!empty($pzsp_settings[ 'pzsp_trans_ease_in' ]) ? $pzsp_settings[ 'pzsp_trans_ease_in' ] : '') . '"';
      $the_data .= '	' . $pzsp_effects;
      $the_data .= '	data-cycle-swipe="true"';
      $the_data .= '	data-cycle-pause-on-hover="' . (!empty($pzsp_settings[ 'pzsp_pause_on_hover' ]) ? 'false' : 'true') . '"';
      $the_data .= '	data-cycle-log="false"';
      $the_data .= '	data-cycle-slides=">div" ';
      $this->PZSP = str_replace('%slideconfig%', $the_data, $this->PZSP);
//    var_dump(esc_html($this->PZSP));
    }

// data-cycle-slides = ">a.pzsp_video_links"
    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: Cleanup
     *
     * *************************** */

    function cleanup()
    {
// cleans out any left over codes
      $this->PZSP = str_replace('%navoutertop%', null, $this->PZSP);
      $this->PZSP = str_replace('%navouterleft%', null, $this->PZSP);
      $this->PZSP = str_replace('%navouterright%', null, $this->PZSP);
      $this->PZSP = str_replace('%navouterbottom%', null, $this->PZSP);
      $this->PZSP = str_replace('%navinnertop%', null, $this->PZSP);
      $this->PZSP = str_replace('%navinnerbottom%', null, $this->PZSP);
      $this->PZSP = str_replace('%pageprev%', null, $this->PZSP);
      $this->PZSP = str_replace('%pagenext%', null, $this->PZSP);
      $this->PZSP = str_replace('%shadow%', null, $this->PZSP);
      $this->PZSP = str_replace('%shadow-in-nav%', null, $this->PZSP);
      $this->PZSP = str_replace('%slideconfig%', null, $this->PZSP);
    }

    /*	 * ***************************
     *
     * 	Class: PZSP
     * Method: Destruct
     *
     * *************************** */

    function __destruct()
    {

    }

    static function strip_columns_shortcodes($the_content)
    {
      $result = preg_replace("/\\[(.)*\\]/uim", "", $the_content);

      return $result;
    }

    static function strip_iframes($the_content)
    {
      $result = preg_replace("/\\<iframe(.)*iframe\\>/ui", "", $the_content);

      return $result;
    }


  }


  class pzsp_Shortcode
  {

    static $add_script = false;

    static function init()
    {
      // TODO: What's this for? Is it still needed?
      add_shortcode('myshortcode', array(__CLASS__, 'handle_shortcode'));
    }

    static function handle_shortcode($atts, $content = null, $code = "")
    {
      self::$add_script = true;
//    wp_enqueue_style('pzsp-styles', PZSP_PLUGIN_URL . '/css/pzsp.css');
//    wp_enqueue_script('jquery-cycle2-mod-pack');
//    wp_enqueue_script('jquery-easing', PZSP_PLUGIN_URL . '/js/cycle2/jquery.easing.1.3.js', array('jquery'), '', true);
//    wp_enqueue_script('pzsp-scripts-standard', PZSP_PLUGIN_URL . '/js/pzsp_scripts.js', array('jquery'), '', true);
//    wp_enqueue_script('jquery-dotdotdot', PZSP_PLUGIN_URL . '/js/jquery.dotdotdot.min.js', array('jquery'), '', true);

      return 'My short code. Check if js loaded';
    }

  }

  pzsp_Shortcode::init();


  /* * ***************************
   *
   * Function: Create the shortcode, which actually does a lot of work.
   *
   * *************************** */

// This shouldn't be a shortcode here, it should be a pure function that is then called by the shortcode. That way we can make it a template tag.
// And it should be part of the class so we can simply say:
// $x = new PZSP($short_name)
// echo $x
//

  add_shortcode('sliderplus', 'pzsp_shortcode');

  function pzsp_shortcode($atts, $content = null, $code = "")
  {
    $return_html = '';


// create new PZSP from $atts id
// do a loop to get the posts
// 
// need to generate a unique ID. Easy for blocks. How to for non blocks :/ maybe time
    if (!isset($atts[ 0 ]) || empty($atts[ 0 ]) || $atts[ 0 ] == 'none') {
      $pzsp_sliders = pzsp_get_sliders(false);
      $return_html .= '<div class= "pzsp-noslideshow">Oops! You did not enter a Slideshow short name to display<br/>Try one of these: <br/>';
      foreach ($pzsp_sliders as $pzsp_slider) {
        $return_html .= '[sliderplus ' . $pzsp_slider . ']<br/>';
      }
      $return_html .= '</div>';
    } else {
      $pzsp_is_embedded = (in_array('embedded',$atts)) ? 'pzsp_is_embedded' : 'pzsp_is_not_embedded';

      $slideshow= pzsp_get_slideshow($atts,'shortcode');

      $pzsp_settings    = pzsp_get_slider_meta(strtolower($slideshow), true); // true = onl get default vlaues - i.e. don't load all the arrays off taxonomies etc
      $pzsp_uid         = '#pzsp-' . $slideshow;

      wp_enqueue_style('pzsp-styles', PZSP_PLUGIN_URL . '/css/pzsp.css');
      wp_enqueue_style('pzsp-icomoon-css-', PZSP_PLUGIN_URL . '/css/icomoon/style.css');
      wp_enqueue_script('jquery-cycle2-mod-pack');
      wp_enqueue_script('jquery-easing');
      wp_enqueue_script('pzsp-scripts-standard');
      wp_enqueue_script('jquery-dotdotdot');


      $pzsp_enqueues = pzsp_create_js_css($pzsp_settings, $pzsp_uid, $pzsp_is_embedded);
      foreach ($pzsp_enqueues as $enqueue) {
        if ($enqueue[ 0 ] == 'style') {
          wp_enqueue_style($enqueue[ 1 ], $enqueue[ 2 ]);
        } elseif ($enqueue[ 0 ] == 'script') {
          wp_enqueue_script($enqueue[ 1 ], $enqueue[ 2 ], $enqueue[ 3 ], $enqueue[ 4 ], $enqueue[ 5 ]);
        }
      }

      $return_html .= pzsp_render($pzsp_settings, $pzsp_uid, $pzsp_is_embedded);
    }

    return $return_html;
  }

  function pzsp_render($pzsp_settings, $pzsp_uid, $pzsp_is_embedded)
  {
//var_dump($pzsp_settings,$pzsp_is_embedded);
    // How do we get this to be somewhere else?!
//    do_action('action_pzsp_create_js_css',$pzsp_settings, $pzsp_uid, $pzsp_is_embedded);

/// okay, lay it out
    $return_html  = '';
    $pzsp_display = new PZSP($pzsp_settings);

    $pzsp_display->get_contents($pzsp_settings);

    $pzsp_display->contents($pzsp_settings);
    $pzsp_display->slide_config($pzsp_settings);
    $pzsp_display->navigation($pzsp_settings);
    $pzsp_display->page_flip($pzsp_settings);
    if (!isset($pzsp_settings[ 'pzsp_border_shadow' ]) || $pzsp_settings[ 'pzsp_border_shadow' ] == 'none') {
      $pzsp_display->shadow($pzsp_settings);
    }
    $pzsp_display->cleanup();

// Need to allow for a bit of Headway :/ when slider is wider than container or so
    $pzsp_container_width = ($pzsp_settings[ 'pzsp_contents_width' ] - 2 * ($pzsp_settings[ 'pzsp_border_size' ] + $pzsp_settings[ 'pzsp_padding_size' ]));

// This is for future if slider in slider works :/
// This is madness! It can send it into an infinite loop in some circumstances. Give up on it!
// $pzsp_final_output = do_shortcode($pzsp_display->PZSP);

    $pzsp_frame_css = 'padding:' . $pzsp_settings[ 'pzsp_padding_size' ]
        . 'px;background-color:' . $pzsp_settings[ 'pzsp_padding_colour' ]
        . ';border:solid ' . $pzsp_settings[ 'pzsp_border_size' ] . 'px ' . $pzsp_settings[ 'pzsp_border_colour' ];

// Add the block title if set
//var_dump();
    if (isset($pzsp_settings[ 'pzsp_show_title' ]) && $pzsp_settings[ 'pzsp_show_title' ]) {
      $return_html .= '<div class="pzsp-slider-' . $pzsp_settings[ 'pzsp_short_name' ] . ' pzsp-slideshow-title">';
      $return_html .= get_the_title($pzsp_settings[ 'pzsp_post_id' ]);
      $return_html .= '</div>';
    }
//		Changing the overflow:hidden to visible may screw things. 27-07-13
//		$return_html .= '<div id="pzsp-slider-' . $pzsp_settings['pzsp_short_name'] . '" class="pzsp-slider pzsp-' . $pzsp_settings['pzsp_theme'] . 'theme ' . (($pzsp_settings['pzsp_border_shadow'] == 'slider') ? 'bordershadow' : null) . '" style="' . $pzsp_frame_css . ';overflow:hidden;width:' . $pzsp_container_width . 'px;max-width:100%;">
//											<!-- SliderPlus v' . PZSP_VERSION . ' -->';
    $use_theme = (!empty($pzsp_settings[ 'pzsp_theme' ]) ? $pzsp_settings[ 'pzsp_theme' ] : 'light');
// attempts at centering
//    $return_html .= '<div id="pzsp-slider-' . $pzsp_settings['pzsp_short_name'] . '" class="pzsp-slider pzsp-' . $use_theme . 'theme ' . (($pzsp_settings['pzsp_border_shadow'] == 'slider') ? 'bordershadow' : null) . '" style="' . $pzsp_frame_css . ';margin-left:'.($pzsp_container_width/2).'px;width:' . $pzsp_container_width . 'px;max-width:100%;position:relative;left:50%;overflow:visible;">';
    $return_html .= '<div id="pzsp-slider-' . $pzsp_settings[ 'pzsp_short_name' ] . '" class="pzsp-slider pzsp-' . $use_theme . 'theme ' . (($pzsp_settings[ 'pzsp_border_shadow' ] == 'slider') ? 'bordershadow' : null) . '" style="' . $pzsp_frame_css . ';width:' . $pzsp_container_width . 'px;max-width:100%;overflow:visible;">';
    $return_html .= '	 <!-- SliderPlus v' . PZSP_VERSION . ' -->';


    $return_html .= '<div id="pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . '" class="pzsp-container ' . $pzsp_is_embedded . '" style="float:left;clear:right;width:' . $pzsp_container_width . 'px;max-width:100%;">'
        . $pzsp_display->PZSP
        . '</div><!end id pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' --></div>';
    if ($pzsp_settings[ 'pzsp_border_shadow' ] == 'none' && substr($pzsp_settings[ 'pzsp_shadows' ], 0, 8) == 'shadows_' && $pzsp_settings[ 'pzsp_shadow_location' ] == 'slider') {
      $return_html .= '<div class="pzsp_slider_shadow"><img src="' . PZSP_PLUGIN_URL . '/css/images/' . $pzsp_settings[ 'pzsp_shadows' ] . '"  width="' . ($pzsp_settings[ 'pzsp_contents_width' ]) . 'px"/></div>';
    }
//	}
// With a little margin to space the sliders when more than one.
    $return_html .= '<div class="cleardiv" style="margin-bottom:' . ((isset($pzsp_settings[ 'pzsp_bottom_margin' ])) ? $pzsp_settings[ 'pzsp_bottom_margin' ] : 0) . 'px;"></div>';

    return $return_html;
  }


  /* * ***************************
   *
   * Function: Load the meta values from the UCD post
   *
   * *************************** */

  function get_pzsp_meta($meta_value)
  {
    $query_options = array(
        'post_type'  => 'pizazzsliders',
        'meta_key'   => 'pzsp_short_name',
        'meta_value' => $meta_value
    );


    $pzsp_meta_wp_query = new WP_Query($query_options);

    $pzsp_settings = '';
// First off, we get the pzsp data
    if ($pzsp_meta_wp_query->have_posts()) {
      $pzsp_meta_wp_query->the_post();
      $pzsp_settings = get_post_custom();
    }

    // tell WP to use the main query again
    wp_reset_postdata();

    return $pzsp_settings;
  }


  /**
   * [pzsp_create_js_css description]
   * @param  [type] $pzsp_settings    [description]
   * @param  [type] $uid              [description]
   * @param  [type] $pzsp_is_embedded [description]
   * @return [type]                   [description]
   */
  function pzsp_create_js_css($pzsp_settings, $pzsp_uid, $pzsp_is_embedded)
  {

    $pzsp_enqueue = array();

    $pzsp_effects = maybe_unserialize($pzsp_settings[ 'pzsp_trans_type' ]);
    if (!isset($pzsp_settings[ 'pzsp_trans_type' ])) {
      $pzsp_effects = 'none';
    } elseif (is_array($pzsp_effects)) {
      $pzsp_effects = (implode(',', $pzsp_effects));
    }

    $js = "	jQuery(document).ready(function() {\n";


    // Add auto pause for any video
    // Need to bind a pause method to each video when the users clicks a nav item... Sounds so easy!
    // Probably could add autoplay somehow too...
    $js .= '
				var pauseVideos = function() {
			 jQuery("iframe").each(function() {
					var source = jQuery(this).attr(\'src\');
          if (typeof source === "undefined") {return;}

					// Pause Vimeo
					// Need to check video
					if (source.indexOf("vimeo")) {
						this.contentWindow.postMessage(\'{ "method": "pause" }\', \'*\');
					}
					// Pause YouTube
					if (source.indexOf("youtube")) {
						this.contentWindow.postMessage(\'{"event":"command","func":"pauseVideo","args":""}\',\'*\');
					}
					// Pause Wistia
					if (source.indexOf("wistia")) {
						this.contentWindow.postMessage(\'{ "method":"pause" }\', \'*\');
					}

			});
			};


	jQuery(function()
					{
						jQuery(".pzsp-navigation a, a.pzsp-next, a.pzsp-prev") . on( "click", pauseVideos );
					});
		';

    if ($pzsp_settings[ 'pzsp_text_area_opacity' ] < 100 && ($pzsp_settings[ 'pzsp_image_size' ] == 100 || $pzsp_settings[ 'pzsp_layout' ] == 'ImageOnly')) {
      $js .= "	// Make transparent if necessary.
					// Need to do this individually for themes with multicolored tabs
					jQuery('#pzsp-" . $pzsp_settings[ 'pzsp_short_name' ] . " .pzsp-content-container .is-text').each(function(index) {
						var x = jQuery(this).css('backgroundColor');
						var a = " . ($pzsp_settings[ 'pzsp_text_area_opacity' ] / 100) . ";
						var rgba=x.replace(')',', '+a+')');
						var rgba=rgba.replace('rgb','rgba');
						jQuery(this).css('backgroundColor',rgba);
					});
				";
    }

// The inspector shows div widths
    if (isset($pzsp_settings[ 'pzsp_enable_inspector' ]) && is_user_logged_in()) {
      $js .= "	jQuery('html').append('<span class=\"pzdimensions\"></span>');
		jQuery('.pzdimensions').css({'z-index':'9999999' ,'width':'200px;','height':'50px;','position':'fixed','background-color':'black','color':'yellow','top':'0','border':'#fff solid 2px','margin':'5px','padding':'5px'});

		jQuery('div, p, li, article, header, footer, sidebar, aside').mouseenter(function(e){
			var div_width = jQuery(this).width();
			var div_height = jQuery(this).height();
			jQuery('.pzdimensions').text('['+e.target.nodeName.toLowerCase()+'] width:'+div_width+'px');
		}).mouseleave(function(){		jQuery('.pzdimensions').text(' ');});";
    }

    $js .= "});";


//create js cache
    if (pzsp_check_cache()) {
//	var_dump($js);
      $js_file_path = PZSP_CACHE_PATH . '/sp-' . $pzsp_settings[ 'pzsp_post_id' ] . '_scripts.js';
      $js_file_url  = PZSP_CACHE_URL . '/sp-' . $pzsp_settings[ 'pzsp_post_id' ] . '_scripts.js';
// NOTE: This will always overwrite the existing js
// Therefore we need a way to check if it should be appended or overwritten
// Maybe do same as mage cache - i.e. wipe on save, create if missing
// or do we just create one for each :/
      if (!file_exists($js_file_path)) {
        $handle = @fopen($js_file_path, 'w');
        $result = fwrite($handle, $js);
        fclose($handle);
      }
      //Place them in the head to speed up rendering

      $pzsp_enqueue[ ] = array('script',
                               'pzsp-scripts-' . $pzsp_settings[ 'pzsp_post_id' ],
                               $js_file_url,
                               array('jquery'),
                               '',
                               false);
    }


// Load appropriate CSS
//  $pzsp_theme = (empty($pzsp_settings['pszp_theme'])?'light':$pzsp_settings['pzsp_theme']);

    $pzsp_enqueue[ ]          = array('style',
                                      'pzsp-theme-' . $pzsp_settings[ 'pzsp_theme' ],
                                      PZSP_PLUGIN_URL . '/css/theme_' . $pzsp_settings[ 'pzsp_theme' ] . '.css');
    $pzsp_custom_declarations = '';

    if (!empty($pzsp_settings[ 'pzsp_enable-responsive-text' ]) && !empty($pzsp_settings[ 'pzsp_tablet-breakpoint' ]) && !empty($pzsp_settings[ 'pzsp_phone-breakpoint' ])) {
      // Desktop
      $pzsp_custom_declarations .= "\n" . '@media only screen and (min-width : ' . ($pzsp_settings[ 'pzsp_tablet-breakpoint' ] + 1) . 'px) {';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' h2.pzsp-entry-title {' . ((!empty($pzsp_settings[ 'pzsp_desktop-title-css' ])) ? $pzsp_settings[ 'pzsp_desktop-title-css' ] : null) . '}';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-entry-body {' . ((!empty($pzsp_settings[ 'pzsp_desktop-body-css' ])) ? $pzsp_settings[ 'pzsp_desktop-body-css' ] : null) . '}';
      $pzsp_custom_declarations .= '}' . "\n";
      // Tablet
      $pzsp_custom_declarations .= '@media only screen and (min-width : ' . ($pzsp_settings[ 'pzsp_phone-breakpoint' ] + 1) . 'px) and (max-width : ' . ($pzsp_settings[ 'pzsp_tablet-breakpoint' ]) . 'px) {';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' h2.pzsp-entry-title {' . ((!empty($pzsp_settings[ 'pzsp_tablet-title-css' ])) ? $pzsp_settings[ 'pzsp_tablet-title-css' ] : null) . '}';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-entry-body {' . ((!empty($pzsp_settings[ 'pzsp_tablet-body-css' ])) ? $pzsp_settings[ 'pzsp_tablet-body-css' ] : null) . '}';
      $pzsp_custom_declarations .= '}' . "\n";
      // Phone
      $pzsp_custom_declarations .= '@media only screen and (max-width : ' . ($pzsp_settings[ 'pzsp_phone-breakpoint' ] - 1) . 'px) {';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' h2.pzsp-entry-title {' . ((!empty($pzsp_settings[ 'pzsp_phone-title-css' ])) ? $pzsp_settings[ 'pzsp_phone-title-css' ] : null) . '}';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-entry-body {' . ((!empty($pzsp_settings[ 'pzsp_phone-body-css' ])) ? $pzsp_settings[ 'pzsp_phone-body-css' ] : null) . '}';
      $pzsp_custom_declarations .= '}' . "\n";

    }
    if ($pzsp_settings[ 'pzsp_nav_type' ] == 'text' && !empty($pzsp_settings[ 'pzsp_nav_type_phone_bullets' ]) && !empty($pzsp_settings[ 'pzsp_phone-breakpoint' ])) {
      $pzsp_custom_declarations .= '
      @media all and (max-width: ' . ($pzsp_settings[ 'pzsp_phone-breakpoint' ] - 1) . 'px) {
        #pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item.navtype-text.phone-bullet-nav a span { display: none }
        #pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item.navtype-text.phone-bullet-nav a:after { content: "*"; color: #808080; background: #808080; text-shadow:none; padding: 0px 4px; border: 1px solid #bbb; border-radius: 20px; opacity: 0.8;font-size:10px; }
        #pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item.navtype-text.phone-bullet-nav:hover a:after { color: #fff; background: #fff; border: 1px solid #bbb; opacity: 0.7 }
        #pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item.navtype-text.cycle-pager-active.phone-bullet-nav a:after { color: #fff; background: #fff; border: 1px solid #999; opacity: 1 }

        #pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item .navtype-text.phone-bullet-nav a span { display: none }
        #pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item .navtype-text.phone-bullet-nav a:after { content: "*"; color: #808080; background: #808080; padding: 0px 4px; border: 1px solid #bbb; border-radius: 20px; opacity: 0.8;font-size:10px; }
        #pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item .navtype-text.phone-bullet-nav:hover a:after { color: #fff; background: #fff; border: 1px solid #bbb; opacity: 0.7 }
        #pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item.cycle-pager-active .navtype-text.phone-bullet-nav a:after { color: #fff; background: #fff; border: 1px solid #999; opacity: 1 }
      }
    ';
    }

    if (isset($pzsp_settings[ 'pzsp_slideshow_title_css' ])) {
      $pzsp_custom_declarations .= '.pzsp-slider-' . $pzsp_settings[ 'pzsp_short_name' ] . '.pzsp-slideshow-title   {' . $pzsp_settings[ 'pzsp_slideshow_title_css' ] . ';}';
    }
    if (isset($pzsp_settings[ 'pzsp_content_background' ])) {
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-content-container .is-text   {' . $pzsp_settings[ 'pzsp_content_background' ] . ';}';
    }
    if (isset($pzsp_settings[ 'pzsp_content_css' ])) {
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-entry-body  {' . $pzsp_settings[ 'pzsp_content_css' ] . ';}';
    }
    if (isset($pzsp_settings[ 'pzsp_content_link_css' ])) {
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-entry-body  a {' . $pzsp_settings[ 'pzsp_content_link_css' ] . ';}';
    }
    if (isset($pzsp_settings[ 'pzsp_content_h2_css' ])) {
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . '  h2.pzsp-entry-title  {' . $pzsp_settings[ 'pzsp_content_h2_css' ] . ';}';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . '  h2.pzsp-entry-title a {' . $pzsp_settings[ 'pzsp_content_h2_css' ] . ';}';
    }
    if (isset($pzsp_settings[ 'pzsp_content_h3_css' ])) {
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-text-content .pzsp-entry-body  h3 {' . $pzsp_settings[ 'pzsp_content_h3_css' ] . ';}';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-text-content .pzsp-entry-body  h3 a {' . $pzsp_settings[ 'pzsp_content_h3_css' ] . ';}';
    }

    if ((!empty($pzsp_settings[ 'pzsp_nav_item_colour_over' ]) && 'none' != $pzsp_settings[ 'pzsp_nav_item_colour_over' ])) {
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item span {color:' . $pzsp_settings[ 'pzsp_nav_item_colour_over' ] . ';}';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item span.draw-square-bullet {background-color:' . $pzsp_settings[ 'pzsp_nav_item_colour_over' ] . ';}';
    }
    if ((!empty($pzsp_settings[ 'pzsp_nav_selected_item_colour_over' ]) && 'none' != $pzsp_settings[ 'pzsp_nav_selected_item_colour_over' ])) {
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item.cycle-pager-active span {color:' . $pzsp_settings[ 'pzsp_nav_selected_item_colour_over' ] . ';}';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item.cycle-pager-active span.draw-square-bullet {background-color:' . $pzsp_settings[ 'pzsp_nav_selected_item_colour_over' ] . ';}';
    }
    if ((!empty($pzsp_settings[ 'pzsp_nav_hover_item_colour_over' ]) && 'none' != $pzsp_settings[ 'pzsp_nav_hover_item_colour_over' ])) {
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item:hover span {color:' . $pzsp_settings[ 'pzsp_nav_hover_item_colour_over' ] . ';}';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-nav-item:hover span.draw-square-bullet {background-color:' . $pzsp_settings[ 'pzsp_nav_hover_item_colour_over' ] . ';}';
    }

    if ((!empty($pzsp_settings[ 'pzsp_hover_nav_colour' ]) && 'none' != $pzsp_settings[ 'pzsp_hover_nav_colour' ])) {
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-next span {color:' . $pzsp_settings[ 'pzsp_hover_nav_colour' ] . ';}';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-prev span {color:' . $pzsp_settings[ 'pzsp_hover_nav_colour' ] . ';}';
    }
    if ((!empty($pzsp_settings[ 'pzsp_hover_nav_colour_secondary' ]) && 'none' != $pzsp_settings[ 'pzsp_hover_nav_colour_secondary' ])) {
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-next span {background-color:' . $pzsp_settings[ 'pzsp_hover_nav_colour_secondary' ] . ';}';
      $pzsp_custom_declarations .= '#pzsp-' . $pzsp_settings[ 'pzsp_short_name' ] . ' .pzsp-prev span {background-color:' . $pzsp_settings[ 'pzsp_hover_nav_colour_secondary' ] . ';}';
    }


    if (!empty($pzsp_settings[ 'pzsp_custom_css' ]) || $pzsp_custom_declarations) {
      $customcss_file_path = PZSP_CACHE_PATH . '/sp-' . $pzsp_settings[ 'pzsp_post_id' ] . '_custom.css';
      $customcss_file_url  = PZSP_CACHE_URL . '/sp-' . $pzsp_settings[ 'pzsp_post_id' ] . '_custom.css';
// NOTE: This will always overwrite the existing js
// Therefore we need a way to check if it should be appended or overwritten
// Maybe do same as mage cache - i.e. wipe on save, create if missing
// or do we just create one for each :/
      if (!file_exists($customcss_file_path)) {
        $pzsp_custom_css = (!empty($pzsp_settings[ 'pzsp_custom_css' ])) ? $pzsp_settings[ 'pzsp_custom_css' ] : '';
        $handle          = @fopen($customcss_file_path, 'w');
        $result          = fwrite($handle, $pzsp_custom_declarations . (!empty($pzsp_settings[ 'pzsp_custom_css' ]) ? $pzsp_settings[ 'pzsp_custom_css' ] : null));
        fclose($handle);
      }
      $pzsp_enqueue[ ] = array('style', 'pzsp-css-' . $pzsp_settings[ 'pzsp_post_id' ], $customcss_file_url);
    }
    if (isset($pzsp_settings[ 'pzsp_custom_theme_url' ])) {
// CURRENTLY RELIES ON URL BEING CORRECT AND FILE VALID. File existis only works on paths
      $pzsp_enqueue[ ] = array('style',
                               'pzsp-custom-theme-css-' . $pzsp_settings[ 'pzsp_post_id' ],
                               $pzsp_settings[ 'pzsp_custom_theme_url' ]);
    }

    return $pzsp_enqueue;
  }
