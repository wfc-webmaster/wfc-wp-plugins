<?php
/**
 * Adds widget.
 */
class pzsp_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'pzsp_widget', // Base ID
			'SliderPlus Slideshow', // Name
			array( 'description' => __( 'Display a SliderPlus slideshow', 'pzsp' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )	echo $before_title . $title . $after_title;
		echo do_shortcode('[sliderplus '.$instance['shortname'].']');
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['shortname'] = strip_tags( $new_instance['shortname'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$pzsp_sliders = pzsp_get_sliders(true);
		$pzsp_sliders = array_merge(array('none'=>'None selected'),$pzsp_sliders);

		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance[ 'title' ] ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'shortname' ); ?>"><?php _e( 'Slideshow Short name:' ); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id( 'shortname' ); ?>" name="<?php echo $this->get_field_name( 'shortname' ); ?>">
			<?php
			foreach ($pzsp_sliders as $key => $value) {
				if ($key == $instance[ 'shortname' ] ) {
					echo '<option value="'.$key.'" selected="selected">'.$value.'</option>';
				} else {
					echo '<option value="'.$key.'">'.$value.'</option>';
				}
			}
			?>
		</select>
	</p>
		<?php 
	}

} // class Foo_Widget

// register Foo_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "pzsp_widget" );' ) );