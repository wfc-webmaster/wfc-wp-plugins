<?php

/* This class must be included in another file and included later so we don't get an error about HeadwayBlockOptionsAPI class not existing. */

class SliderPlusBlockOptions extends HeadwayBlockOptionsAPI
{

	public $tabs				 = array( );
	public $inputs			 = array( );
	public $tab_notices = array( );

	function modify_arguments( $args = false )
	{
		$block = $args[ 'block' ];

		// Set up the array of tabs
		$this->tabs =
						array(
								'help-tab'		 => 'Help',
								'general-tab'	 => 'General'
		);

		// Setup the tab options		
		$this->inputs =
						array(
								'help-tab'		 => self::pzsp_helptab( $block ),
								'general-tab'	 => self::pzsp_generaltab( $block, 'no' ),
		);

		// Setup any optional messages you want displayed on each tabs' panel			
		$this->tab_notices =
						array(
								'help-tab'		 => 'SliderPlus version ' . PZSP_VERSION . '<br/></br/><strong>Note: Create the Slideshows in WP admin under the <a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/edit.php?post_type=pizazzsliders" target=_blank>PizazzWP menu</a>. In this block, you can then choose the Slideshow to display.</strong><br/>
			<strong>Support:</strong> Please send report requests to support@pizazzwp.com<br/>
',
								'general-tab'	 => '<strong>Note: Create the Slideshows in WP admin under the <a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/edit.php?post_type=pizazzsliders" target=_blank>PizazzWP menu</a>. In this block, you can then choose the Slideshow to display.</strong>'
		);
	}

	// I set up each tab in its own function, then I can use conditionals whether to display the tab or not.
	// And it makes navigating the code heaps easier when you have a lot of options.

  static function pzsp_helptab( $block )
	{
		$settings = array(
		);
		return $settings;
	}

  static function pzsp_generaltab( $block, $just_defaults )
	{
		$pzsp_sliders = array( );
		if ( $just_defaults == 'no' )
		{
			$pzsp_sliders	 = pzsp_get_sliders( true );
			$pzsp_sliders	 = array_merge( array( 'none' => 'None selected' ), $pzsp_sliders );
		}
		//make it show the slider width - padding+width+shadow+border
		$settings = array(
				'pzsp_opt_slidername' => array(
						'type'		 => 'select',
						'name'		 => 'pzsp_opt_slidername', //This will be the setting you retrieve from the database.
						'label'		 => 'Slideshow name',
						'default'	 => '',
						'options'	 => $pzsp_sliders,
						'tooltip'	 => 'Select the short name of the Slideshow to display in this block. The width reminds you how wide you\'ve made the Slideshow, and therefore at least how wide the block needs to be.'
				),
        'pzsp_opt_slidername_phone' => array(
          'type'		 => 'select',
          'name'		 => 'pzsp_opt_slidername_phone', //This will be the setting you retrieve from the database.
          'label'		 => 'Slideshow name (phone)',
          'default'	 => '',
          'options'	 => $pzsp_sliders,
          'tooltip'	 => 'Select the short name of the Slideshow to display in this block on phones.'
        ),
        'pzsp_opt_slidername_tablet' => array(
          'type'		 => 'select',
          'name'		 => 'pzsp_opt_slidername_tablet', //This will be the setting you retrieve from the database.
          'label'		 => 'Slideshow name (tablet)',
          'default'	 => '',
          'options'	 => $pzsp_sliders,
          'tooltip'	 => 'Select the short name of the Slideshow to display in this block on tablets.'
        ),
//			'pzsp_opt_customize' => array(
//				'type' => 'select',
//				'name' => 'pzsp_opt_customize', //This will be the setting you retrieve from the database.
//				'label' => 'Settings',
//				'default' => 'default',
//				'options' => array('default'=>'Defaults','custom'=>'Custom'),
//				'tooltip' => 'To customise the settings for this block, choose Custom. Otherwise it will use the settings configured in the Sliders in WP Admin.'
//			)
		);

		return $settings;
	}

  static function grab_categories()
	{
		// Grabs all WP categories to an array, and adds a first option of All
		// You will need to wrangle your own code to make use of the All
		$categories_select_query = get_categories();
		$categories_array				 = array( 'all' => 'All' );
		foreach ( $categories_select_query as $category )
		{
			$categories_array[ $category->cat_ID ] = $category->cat_name;
		}

		return $categories_array;
	}

  static function get_settings( $block )
	{
		// use this function to retrieve block settings to an array to use in the content area of your block
		//
		// usage: $settings = HeadwayExampleBlockOptions::get_settings($block)
		//  or    $settings = HeadwayExampleBlockOptions::get_settings($block['id'])
		//
		// The $settings array will then contain all your block options 
		// eg $settings['dob'], $settings['height'] etc

		if ( is_integer( $block ) )
		{
			$block = HeadwayBlocksData::get_block( $block );
		}
		$settings	 = array( );
		$options	 = array_merge( self::pzsp_helptab( $block ), self::pzsp_generaltab( $block, 'yes' )
		);
		foreach ( $options as $option )
		{
			$settings[ $option[ 'name' ] ] = HeadwayBlockAPI::get_setting( $block, $option[ 'name' ], $option[ 'default' ] );
		}

		return $settings;
	}

}

// End of class