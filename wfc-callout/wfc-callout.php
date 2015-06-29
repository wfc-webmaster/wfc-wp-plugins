<?php
/*
Plugin Name: WFC-Call Out
Description: This plugin creates the WFC Call Out Widget
Author: David Elden
Version: 1.0
*/
/* Start Adding Functions Below this Line */

class WFC_CallOut extends WP_Widget {
	function WFC_CallOut() {

		$widget_options = array( 
			'classname' => 'wfc-callout',
			'description' => 'A widget to display callout text with WFC Cards.'
			);

		parent::WP_Widget('wfc_callout', 'WFC Call Out', $widget_options);

		add_action('admin_enqueue_scripts', array($this, 'upload_scripts'));
    }

    /*
    =============================================
    Upload the Javascripts for the media uploader
    =============================================
    */

    function widget($args, $instance) {
		extract( $args, EXTR_SKIP );

		$title = ( $instance['title'] ) ? $instance['title'] : 'Display image and text cards.';
		$headline = ( $instance['headline'] ) ? $instance['headline'] : 'Main Headline Goes Here';
		$subhead = ( $instance['subhead'] ) ? $instance['subhead'] : 'Nothing to display. Please add something to the card';
		$linktext = ( $instance['linktext'] ) ? $instance['linktext'] : 'Link text here';
		$link = ( $instance['link'] ) ? $instance['link'] : '';
		$link_compiled = '<a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($linktext) . '</a>';

		$image = ( $instance['image'] ) ? $instance['image'] : 'Upload an image.';
		?>

		<?php echo $before_widget; ?>
		<?php // echo $before_title . $title . $after_title ?>
		<div class="wfc-callout-container">
			<h1><?php echo htmlspecialchars($headline) ?></h1>
			<h3><?php echo htmlspecialchars($subhead) ?></h3>
			<p><?php echo $link_compiled ?></p>
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

		<p><label for="<?php echo $this->get_field_id('headline'); ?>">Headline:</label><br />
			<input id="<?php echo $this->get_field_id('headline'); ?>" 
				name="<?php echo $this->get_field_name('headline'); ?>" 
				value="<?php echo esc_attr($instance['headline']); ?>"	
				type="text" class="widefat" /></p>		

		<p style="text-align:left;"><label for="<?php echo $this->get_field_id('subhead'); ?>">
			Subhead:</label><br />
			<textarea id="<?php echo $this->get_field_id('subhead'); ?>" name="<?php echo $this->get_field_name('subhead'); ?>" type="text" class="widefat"><?php echo esc_attr($instance['subhead']); ?></textarea>
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

function wfc_callout_init() {
	register_widget("WFC_CallOut");
}

add_action('widgets_init', 'wfc_callout_init');

/* Stop Adding Functions Below this Line */
?>