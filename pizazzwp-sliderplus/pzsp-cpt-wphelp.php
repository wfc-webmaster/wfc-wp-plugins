<?php

/*
  WP pulldown help for SliderPlus slideshow listing and editing
 */

add_action('admin_head', 'pzsp_sliders_add_help_tab');

function pzsp_sliders_add_help_tab() {
	$screen = get_current_screen();
	$prefix = 'pzsp_';
	/*
	 * Check if current screen is My Admin Page
	 * Don't add help tab if it's not
	 */
	global $current_user;
	$user_id = $current_user->ID;
//	pzdebug($_SERVER);
//	if (get_user_meta($user_id, 'pzsp_closed_help')) {
//		$pz_help_button = '<p><a class="button-help-on" href="'.$_SERVER['REQUEST_URI'].'&pzsp_yes_help">Turn on automatic display of help window</a></p>';
//	} else {
//		$pz_help_button = '<p><a class="button-help-off" href="'.$_SERVER['REQUEST_URI'].'&pzsp_no_help">Turn off automatic display of help window</a></p>';
//	}
$pz_help_button = null;

	switch ($screen->id) {
		case 'edit-pizazzsliders':
			$screen->add_help_tab(array(
					'title' => __('What is a Slideshow', 'pzsp'),
					'id' => $prefix . 'view_help_slideshow',
					'content' => '<h3>What is a SliderPlus Slideshow</h3><p>' . __('
					<p>Slideshows are made of a series of slides whose content can be sources of posts, pages, GalleryPlus and NextGen image galleries, or SliderPlus\' own content type, Slides, managed under the menu <em>PizazzWP</em> > <em>S+ Slides</em>.</p>
					'.$pz_help_button.'
			')));
			$screen->add_help_tab(array(
					'title' => __('Usage', 'pzsp'),
					'id' => $prefix . 'view_help_usage',
					'content' => '<h3>SliderPlus Usage</h3><p>' . __('
					Slideshows can be displayed using any of the following methods
				<h4>Shortcode</h4>
					To display a Slideshow in a post or page, insert the shortcode plus short name of your Slideshow into a WordPress page or post<br/>
					e.g.: <strong>[sliderplus myfirstslider]</strong>
				<h4>Widget</h4>
					Slideshows can also be displayed in sideabrs with the SliderPlus Slideshow widget. You can set a title and choose Slideshow from the dropdown.					
				<h4>Headway SliderPlus block</h4>
					Headway users can also display Slideshows with the included block.
				<h4>Template tag</h4>
					Use the template tag <em>pzsplus($shortname)</em> in your WP page templates, where $shortname is the Shortname of the Slideshow to display.'
			)));
			$screen->add_help_tab(array(
					'title' => __('Duplicating Slideshows', 'pzsp'),
					'id' => $prefix . 'view_help_duplicating',
					'content' => '<h3>Duplicating Slideshows</h3><p>' . __('If you wish to duplicate Slideshow, install the plugin <em>Duplicate Post</em> by Enrico Battochi and look for the Clone option when you hover over the Slideshow title in the Slideshow listing.', 'pzsp') . '</p>',
			));
			$screen->add_help_tab(array(
					'id' => $prefix . 'view_help_about',
					'title' => __('About SliderPlus'),
					'content' => '<h3>About</h3><p>' . __('PizazzWP SliderPlus is a fully featured, full-content slider and is used to display your content in a fully navigable slideshow.') . '</p>' .
					'<h4>Features</h4><ul>
							<li>Full content Slideshow, not just image galleries</li>
							<li>Enhanced contextual help that stays open as long as you need it</li>
							<li>Uses WordPress help system also</li>
							<li>Many drop shadow options</li>
							<li>Multiple styling themes</li>
							<li>Custom CSS</li>
							<li>Display Slideshows using shortcode, template tag, Headway block, or in widget.
							<li>Multiple content sources - posts, page, slides and image galleries</li>
							<li>Supports display of videos</li>
							<li>Responsive design for multi-screen size support</li>
						</ul>'
					. '<p>' . 'SliderPlus v' . PZSP_VERSION . '</p>'
			));
			$screen->add_help_tab(array(
					'title' => __('Terminology', 'pzsp'),
					'id' => $prefix . 'view_help_terminology',
					'content' => '<h3>Terminology</h3><p>' . __('One of the challenging aspects of producing slideshows from multiple content sources is the terms can get confusing. Here are some clarifications:') . '</p>'
					. '<h4>Slides</h4>
							Each Slideshow is made up of a series of slides. So, a post or page can be a slide, but so can the SliderPlus Slides content type. Whenever possible, the use of the term Slides (capitalized) will refer to the SliderPlus Slides content type, and the lowercase "slides" will refer to the individual slides in a slideshow.
						'
							,
							)
			);
			$screen->add_help_tab(array(
					'title' => __('Support', 'pzsp'),
					'id' => $prefix . 'view_help_support',
					'content' => '<h3>Support</h3><p>' . __('Please send report requests to support@pizazzwp.com<br/>', 'pzsp') . '</p>
					<p>There is also <a href="http://guides.pizazzwp.com/sliderplus/about-sliderplus/" target=_blank>more help online</a>.',
								  )
			);


			break;
    case 'pizazzsliders':

      $screen->add_help_tab(array(
                                 'title'   => __('Designing a Slideshow', 'pzsp'),
                                 'id'      => $prefix . 'edit_help_designing',
                                 'content' => '<h3>Tips for designing a Slideshow</h3><p>' . __('
            	The most important thing to remember when designing a Slideshow is to ensure the content of each slide can be viewed in the chosen dimensions. Sliders and slideshows are always a fixed height and width emthod of displaying content. If you need display variable height content, then consider TabsPlus.<br/><br/>
							For example: If you mix images and videos (i.e. some slides with featured image, some with videos), you will have to tailor your images to match you videos\' dimensions, and video from diffrent sources (e.g. Vimeo and YouTube), may have different dimensions.', 'pzsp') . '</p>
							<p>It\s always best to design your space first. Know the size of the area you intend to put your Slideshow. If you design the Slideshow with specific dimensions, and then put tem in a smaller space, e.g. a smaller Headway block, it can throw out of whack all your calculations for image and video sizes.</p>
						           	<br>
            	<img src="' . PZSP_PLUGIN_URL . '/images/help/anatomy-of-slide.jpg"/>
					' . $pz_help_button . '
						',
                            )
      );
      $screen->add_help_tab(array(
                                 'title'   => __('Sizing Slideshows for videos', 'pzsp'),
                                 'id'      => $prefix . 'edit_help_sizing',
                                 'content' => '<h3>Sizing Slideshows for videos and other fixed dimension content sources</h3><p>' . __('
							Using featured images in Slides, SliderPlus will crop them to fit. But sizing Slideshows to match video soures can be fiddly, as you have no control over relative the dimensions of the video. So here\'s some formulas:<br>
							<h4 class="pzsp_help_heading">Calculating the contents height to match a required image area relative size</h4>
							<div class="pzsp_help_callout"><p>video_height/video_width*image_relative_size/100*slideshow_width</p></div>
							
							<p>e.g video is 500w by 300h and you want to fit it in an area that is 60% of a 900px wide Slideshow:</p>
							<p>300/500*60/100*900 = 324px</p>
							
							<h4 class="pzsp_help_heading">Calculating the image relative size to match a required contents height</h4>
							<div class="pzsp_help_callout"><p>video_width/video_height*contents_height*100/slidershow_width</p></div>
							
							<p>e.g video is 500w by 300h and you want to fit it in height that is 324px of a 900px wide Slideshow:</p>
							<p>500/300*324*100/900 = 60%</p>
							
							<p>To get the video width and height, look at the embed code for your video source</p>
							', 'pzsp') . '</p>',
                            ));
      $screen->add_help_tab(array(
                                 'title'   => __('This page appears as a long list', 'pzsp'),
                                 'id'      => $prefix . 'edit_help_jserror',
                                 'content' => '<h3>All the settings on this page are in a single VERY long list</h3><p>' . __('This screen should look something like this:<p><img src="' . PZSP_PLUGIN_URL . '/images/help/example-screen.jpg"/></p>If not, you are encountering a javascript problem.
<ul><li>Is the page stuck loading? Try reloading the page</li>
<li>Try deactivating all other plugins and see if it displays as a nice tabbed panel.</li>
<li>Are you running any external services, such as Google Pagespeed, that might scramble how the javascript files load?</li>
</ul>
') . '</p>',
                            )
      );

      $screen->add_help_tab(array(
                                 'title'   => __('Using Custom CSS', 'pzsp'),
                                 'id'      => $prefix . 'edit_help_custom_css',
                                 'content' => '<h3>Using Custom CSS</h3>
SliderPlus provides several solutions for applying custom css to your Slideshows.<br/>
<h4>Using a custom CSS file</h4>
You can duplicate a SliderPlus theme file and edit it willy-nilly, and then in the field for an external Custom CSS file URL, enter its path.
<h4>Custom CSS field</h4>
Or you can just copy and paste into the Custom CSS field the selectors you want to modify, and then edit them there.
<br/>br/>
CSS being CSS if any property doesn\'t seem to be taking affect, try suffixing it with the !important rule.            ')
      );

      $screen->add_help_tab(array(
                                 'title'   => __('Centering fullwidth slideshows', 'pzsp'),
                                 'id'      => $prefix . 'edit_help_custom_css',
                                 'content' => '<h3>Centering Full Width Slideshows</h3>
         <p>In CSS fluid width divs do not allow their content to be centred. Therefore, if your using a SliderPlus slideshow at full width in a fluid width wrapper, it won\'t centre. Why does this even matter? In most cases it won\'t. But on the rare occasions the user\'s screen width is wider than your images, they would be left aligned.</p>
         <p>This tip comes from <a href="http://www.photoproseo.com" target="_blank">Alan Hutchinson</a> and provides an alternative:</p>
         <p><em>I changed the grid to be fluid with with a fixed grid width, set to the same width as my image - then when my screen is larger than my image it is showing the slider in the centre of the screen with the wrapper padding each side. When it’s smaller than the image it’s giving me the full width effect.</em>

      '));
      $screen->add_help_tab(array(
                                 'title'   => __('Responsive heights', 'pzsp'),
                                 'id'      => $prefix . 'edit_help_custom_css',
                                 'content' => '<h3>Responsive heights -  or why is there a big gap under slideshows on small screens?</h3>
    <p>Headway sets minimum heights on blocks based on how you draw them in the visual editor.</p>
    <p>Two things you can do to override that:</p>
    <p>1) In the visual editor, reduce the height of your block to the minimum tou want it to display, like 100px</p>
    <p>2) Alternatively, in custom CSS, add the following:</p>
       <p> <pre>.block-type-sliderplus {min-height:100px!important;}</pre></p>
'));

      break;
    default:
      return;
      break;
  }

}

