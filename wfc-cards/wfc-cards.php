<?php
/*
Plugin Name: WFC-Cards
Description: This plugin creates the WFC-Cards Widget
Author: David Elden
Version: 1.0
*/
/* Start Adding Functions Below this Line */

class WFC_Cards extends WP_Widget {
	function WFC_Cards() {

		$widget_options = array( 
			'classname' => 'wfc-cards',
			'description' => 'A widget to display image and text cards'
			);

		parent::WP_Widget('wfc_cards', 'WFC Cards', $widget_options);

		add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));
    }

    /*
    =============================================
    Upload the Javascripts for the media uploader
    =============================================
    */

    public function upload_scripts() {
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('upload_media_widget', plugin_dir_url(__FILE__) . 'upload-media.js', array('jquery'));
        wp_enqueue_style('thickbox');
    }

	function widget($args, $instance) {
		extract( $args, EXTR_SKIP );

		$title = ( $instance['title'] ) ? $instance['title'] : 'Display image and text cards.';
		$imagelink = ( $instance['imagelink'] ) ? $instance['imagelink'] : '';
		$alt_tag = ( $instance['alt_tag'] ) ? $instance['alt_tag'] : '';
		$department = ( $instance['department'] ) ? $instance['department'] : 'WFC Department';
		$headline = ( $instance['headline'] ) ? $instance['headline'] : 'Main Headline Goes Here';
		$body = ( $instance['body'] ) ? $instance['body'] : 'Nothing to display. Please add something to the card';
		$linktext = ( $instance['linktext'] ) ? $instance['linktext'] : 'Link text here';
		$link = ( $instance['link'] ) ? $instance['link'] : '';
		$link_compiled = '<a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($linktext) . '</a>';

		$image = ( $instance['image'] ) ? $instance['image'] : 'Upload an image.';
		?>

		<?php echo $before_widget; ?>
		<?php // echo $before_title . $title . $after_title ?>
		<div class="wfc-cards-container">
			<img src="<?php echo $image ?>" alt="<?php echo $alt_tag ?>" />
			<div class="wfc-cards-text">
				<h3><?php echo htmlspecialchars($department) ?></h3>
				<h1><?php echo htmlspecialchars($headline) ?></h1>
				<p><?php echo htmlspecialchars($body) ?></p>
				<p><?php echo $link_compiled ?></p>
			</div>
		</div>

		<?php

	}

	// function update() {

	// }

	function form($instance) {

		?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>">Card Title:</label><br />
			<input id="<?php echo $this->get_field_id('title'); ?>" 
				name="<?php echo $this->get_field_name('title'); ?>" 
				value="<?php echo esc_attr($instance['title']); ?>"	
				type="text" class="widefat" /></p>

		<p><img id="previewPic" src="<?php echo esc_url($instance['image']); ?>" />
			<input name="<?php echo $this->get_field_name('image'); ?>" 
					id="<?php echo $this->get_field_id('image'); ?>" 
					class="widefat" 
					type="text" 
					size="36"  
					value="<?php echo esc_url($instance['image']); ?>" />

			<input class="upload_image_button" type="button" value="Select Image" />
		</p>

		<p><label for="<?php echo $this->get_field_id('alt_tag'); ?>">Image Alt Tag:</label><br />
			<input id="<?php echo $this->get_field_id('alt_tag'); ?>" 
				name="<?php echo $this->get_field_name('alt_tag'); ?>" 
				value="<?php echo esc_attr($instance['alt_tag']); ?>" 
				type="text" class="widefat" /></p>

		<p><label for="<?php echo $this->get_field_id('department'); ?>">Department:</label><br />
			<input id="<?php echo $this->get_field_id('department'); ?>" 
				name="<?php echo $this->get_field_name('department'); ?>" 
				value="<?php echo esc_attr($instance['department']); ?>"
				type="text" class="widefat" /></p>

		<p><label for="<?php echo $this->get_field_id('headline'); ?>">Headline:</label><br />
			<input id="<?php echo $this->get_field_id('headline'); ?>" 
				name="<?php echo $this->get_field_name('headline'); ?>" 
				value="<?php echo esc_attr($instance['headline']); ?>"	
				type="text" class="widefat" /></p>		

		<p><label for="<?php echo $this->get_field_id('body'); ?>">
			Body:</label><br />
			<textarea id="<?php echo $this->get_field_id('body'); ?>" name="<?php echo $this->get_field_name('body'); ?>" type="text" class="widefat"><?php echo esc_attr($instance['body']); ?></textarea>
		</p>

		<p><label for="<?php echo $this->get_field_id('link'); ?>">Link:</label><br />
			<input id="<?php echo $this->get_field_id('link'); ?>" 
				name="<?php echo $this->get_field_name('link'); ?>" 
				value="<?php echo esc_attr($instance['link']); ?>"	
				type="text" class="widefat" /></p>

		<p><label for="<?php echo $this->get_field_id('linktext'); ?>">Link Text:</label><br />
			<input id="<?php echo $this->get_field_id('linktext'); ?>" 
				name="<?php echo $this->get_field_name('linktext'); ?>" 
				value="<?php echo esc_attr($instance['linktext']); ?>"
				type="text" class="widefat"	/></p>
		<?php

	}
}

function wfc_cards_init() {
	register_widget("WFC_Cards");
}

add_action('widgets_init', 'wfc_cards_init');

/* Stop Adding Functions Below this Line */
?>