<?php

// Various global functions


if (!function_exists('pzwp_check_cache')) {

	function pzwp_check_cache($cache_path) {
		if (!is_dir(PIZAZZWP_CACHE_PATH . $cache_path)) {
			@mkdir(WP_CONTENT_DIR . '/uploads/cache');
			@mkdir(PIZAZZWP_CACHE_PATH);
			@mkdir(PIZAZZWP_CACHE_PATH . $cache_path);
		}
		if (!is_dir(PIZAZZWP_CACHE_PATH . $cache_path)) {
			echo '<div id="message" class="updated"><p>Unable to create cache folder <strong>' . $cache_path . '</strong>. You will have to manually create the following folders:</p>
					&nbsp;&nbsp;&nbsp;&nbsp;wp-content/uploads/cache<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;wp-content/uploads/cache/pizazzwp<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;wp-content/uploads/cache/pizazzwp/' . $cache_path . '<br/>
					<p>using FTP and set their permissions to 777<br/><br/></p>
				</div>';
			return false;
		}
		return true;
	}

}

if (!function_exists('pzdebug')) {

	//---------------------------------------------------------------------------------------------------
	// Debug
	//---------------------------------------------------------------------------------------------------
	/**
	 * [pzdebug description]
	 * @param  string $value='' [description]
	 * @return [type]           [description]
	 */
	function pzdebug($value = '') {
		if (current_user_can('manage_options')) {
			$btr = debug_backtrace();
			$line = $btr[0]['line'];
			$file = basename($btr[0]['file']);
			print"<pre>$file:$line</pre>\n";
			if (is_array($value)) {
				print"<pre>";
				print_r($value);
				print"</pre>\n";
			} elseif (is_object($value)) {
				var_dump($value);
			} else {
				print("<p>&gt;${value}&lt;</p>");
			}
		}
	}

}

if (!function_exists('pzgetlinks')) {

	/**
	 * [pzgetlinks description]
	 * @param  [type] $str [description]
	 * @return [type]      [description]
	 */
	function pzgetlinks($str) {
		preg_match_all('/(href|src)\=(\"|\')[^\"\'\>]+/i', $str, $media);
		unset($str);
		$str = preg_replace('/(href|src)(\"|\'|\=\"|\=\')(.*)/i', "$3", $media[0]);
		return $str;
	}

}

if (!function_exists('fb_AddThumbColumn') && function_exists('add_theme_support')) {
	// for post and page
	$opt_val = get_option('pizazz_options');
	if ($opt_val['val_show_thumbs'] == 'showthumbs') {
		add_theme_support('post-thumbnails', array('post', 'page'));

		function fb_AddThumbColumn($cols) {
			$cols['thumbnail'] = __('Thumbnail');
			return $cols;
		}

		// Function to add thumbs to column views. Need to make this an option.
		/**
		 * [fb_AddThumbValue description]
		 * @param  [type] $column_name [description]
		 * @param  [type] $post_id     [description]
		 * @return [type]              [description]
		 */
		function fb_AddThumbValue($column_name, $post_id) {
			$width = (int) 35;
			$height = (int) 35;
			if ('thumbnail' == $column_name) {
				// thumbnail of WP 2.9
				$thumbnail_id = get_post_meta($post_id, '_thumbnail_id', true);
				// image from gallery
				$attachments = get_children(array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image'));
				if ($thumbnail_id)
					$thumb = wp_get_attachment_image($thumbnail_id, array($width, $height), true);
				elseif ($attachments) {
					foreach ($attachments as $attachment_id => $attachment) {
						$thumb = wp_get_attachment_image($attachment_id, array($width, $height), true);
					}
				}
				if (isset($thumb) && $thumb) {
					echo $thumb;
				} else {
					echo __('None');
				}
			}
		}

		// for posts
		add_filter('manage_posts_columns', 'fb_AddThumbColumn');
		add_action('manage_posts_custom_column', 'fb_AddThumbValue', 10, 2);
		// for pages
		add_filter('manage_pages_columns', 'fb_AddThumbColumn');
		add_action('manage_pages_custom_column', 'fb_AddThumbValue', 10, 2);
	}
}

if (!function_exists('pzwp_get_post_types')) {

	function pzwp_get_post_types() {
		// Post types
		$all_post_types = array('post');
		$args = array(
				'public' => true,
				'_builtin' => false
		);
		$output = 'names'; // names or objects
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args, $output, $operator);
		foreach ($post_types as $post_type) {
			$all_post_types[$post_type] = $post_type;
		}
		return $all_post_types;
	}

}