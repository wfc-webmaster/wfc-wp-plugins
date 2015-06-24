=== PizazzWP SliderPlus ===
Author: Chris Howard
Contributors: chrishoward
Tags: slider,slideshow,images,videos,content
Requires at least: 3.5.1
Tested up to: 4.2
Stable tag: 1.4.0
Version: 1.4.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ===
SliderPlus is a full content slider for creating slideshows of text, images and videos, and is useful for making featured content sliders, showcases, advertising banners, video slideshows, post and page sliders and much more.

==SliderPlus Installation

If you have a pre-existing install of SliderPlus, you will need to deactivate it before activating this one.

1. Locate the SliderPlus zip file
2. Go to your WordPress dashboard. Plugins, Add New, Upload and select the SliderPlus zip file.
3. Once uploaded, click activate


== SUPPORT & DOCUMENTATION:
If you require support, please sen an email to support@pizazzwp.com or access the support form in WP Admin> PizazzWP > About & Support
For documentation: http://guides.pizazzwp.com/sliderplus/about-sliderplus/

Please review the changes listed below.

== CHANGELOG ==

= 1.4.0 =
* ADDED: Option for different slideshow per device!

= 1.3.17 :: 11 April 2015 =
* FIXED: Missing jQuery dependency in unhide-block.js enqueue

= 1.3.16 =
* FIXED: Cache check calling wrong function

= 1.3.15 : 16-Dec-2014 =
* FIXED: Not displaying GalleryPlus images

= 1.3.14 =
* FIXED: Stopped using Google CDN version of jQuery. Back to standard WP built in. This fixes some jQuery conflicts that it introduced

= 1.3.13 =
* FIXED: Rejigged CSS that was stopping responsive titles CSS from being set.

= 1.3.12 : 2-Oct-2014 =
* FIXED: Help "?" button not working. This was also causing Featured image in Slides to fail, and the pull down Help.
* FIXED: Wrong support information

= 1.3.11 =
* FIXED: Alt text not being added to images

= 1.3.10 : =
* FIXED: Problem where SliderPlus was not restoring the main query after running itself.
* CHANGED: Slides post type now accessible by other plugins, e.g. Architect

= 1.3.9  : 1-June-2014 =
* FIXED: Debugging code left in that would cause slow performance on sites with a lot of posts
* FIXED: Warning notice on PizazzWP constant
* FIXED: Headway 3.7 compatibility

= 1.3.8  : 9-April-2014 =
* FIXED: Content type selection not displaying correct filtering options

= 1.3.7 : 6 April 2014 =
* CHANGED: PizazzWP libraries is now a separate plugin

= 1.3.6 =
* ADDED: Option to strip iframes from body content
* CHANGED: Made title line height 1.1
* ADDED: Responsive options for content
* ADDED: Option to enable or not changing of nav titles to bullets on phones
* UPDATED: jQuery Cycle2 to v2.1.2
* FIXED: Bug when no transition duration set (slideshow would screw up and go blank)
* CHANGED: Rejigged how scripts are loaded
* CHANGED: Code structure so shortcode isn't called by block
* FIXED: Sometimes theme was coming up null coz of typo in variable name.
* FIXED: Paragraph breaks going missing in content
* CHANGED: Renamed to Slider Plus (i.e. added a space. This simply is to improve search hits for it)

= 1.3.5 =
* UPDATE: Updated libs
* FIXED: Various minor bug fixes
* FIXED: Loading of metabox code to only happen on SliderPlus screens, vastly improving speed on other admin screens for sites with many posts.

= 1.3.4 : 3-Dec-2013 =
* FIXED: Problem with saving slideshows and slides

= 1.3.3 : 27-Nov-2013
* ADDED: Tip about centreing full width slideshows from Alan Hutchinson. http://www.photoproseo.com
* ADDED: Tip about responsive heights being limited by min-height set by Visual Editor.

* CHANGED: Made the code for hiding the block until rendered tidy up after itself.
* CHANGED: Removed some html comments
* CHANGED: the z-index position of Headway nav submenus so they don't get hidden behind SliderPlus navs.

* FIXED: Rare occurrence of line breaks being inserted into data settings. Possibly caused by a third party plugin.
* FIXED: Meta box loading slowing admin

1.3.2: 15-Nov-13
ADDED: Info message to Slideshow list page.

CHANGED: Removed update notice
CHANGED: SliderPlus block will stay hidden until page fully drawn. This prevents the ugly look before the block turns into a slider.
CHANGED: Using global resizer

FIXED: Heaps of notices in PHP 5.4 strict mode
FIXED: Small js error when navigating slides with videos.

REMOVED: Help page automatic opening

1.3.1
ADDED: WP-Updates support for non-Headway sites

1.3.0
ADDED: Support for local videos using WP's new video shortcode format http://codex.wordpress.org/Video_Shortcode
ADDED: Option to always show hover navigation. This was done by changing the option that to hide it
ADDED: Option for full width slideshows. Please read the help text with this option
ADDED: Option for top or bottom navigation to appear floated over the slideshow rather than above or below
ADDED: Option to disable linking of content titles

1.2.4
ADDED: Option to randomise the display order of the slides, irrespective of what "Order by" is selected.

CHANGED: WP Context Help now is automatically opened. Can be set not not open too.
CHANGED: When "Mouseover" navigation selected, clicking nav will link to post/page or slide destination

FIXED: Sliders not showing in Visual Editor
FIXED: Design mode styling of content titles not working
FIXED: Borders and shadows not wrapping around the slider
FIXED: Titles and content missing sometimes
FIXED: Only 10 Slideshows showing in block selector

1.2.3: 28-July-2013
CHANGED: Removed some transitions that weren't working

FIXED: Slide height was wrong when navigation position set to left or right
FIXED: Both arrows showing when using custom images for hover arrows
FIXED: "Add Media" button no longer working in Slides since 1.2.1
FIXED: Extra space showing at bottom of slideshow
FIXED: Vertical navs busting layout if borders applied

ADDED: Stylings for Headway users in Visual Editor Design Mode

1.2.2
FIXED: An extremely rare situation where sometimes image filenames have some junk on the end put there by unknown plugin. S+ now removes the junk.
ADDED: Option to not resize images and display the original image. Useful when the highest quality image is required as server resizing isn't as good as Photoshop's
FIXED: Destination URL in Slides not working.

1.2.1
FIXED: Missing hover nav buttons


1.2.0 : 04-May-2013
FIXED: Greatly improved responsive scaling
UPDATED: Pizazz Libs v1.3.5 which, among other fixes and updates, provides a Support button on the PizazzWP menu screen

1.1.5

ADDED: Automatic video pause when switching slides with YouTube or Vimeo videos set as the feature
ADDED: Option to override Respect Focal Point
ADDED: Option for custom nav colours
ADDED: Option to always show Read More if excerpts
ADDED: Options to color hover nav

CHANGED: Added link for current version on PizazzWP menu screen
CHANGED: Added message in PizazzWP menu screen when Pizazz updates server not available
CHANGED: Split filters into its own tab.
CHANGED: Content height is now a maximum height, so content area will adjust fluildly
CHANGED: Using Icon font for hover nav instad of PNGs.
CHANGED: Navigation elements to be more larger when small screen

FIXED: Respect Focal Point not working
FIXED: Overlapping text while slideshow is initially loading
FIXED: Full content not preserving line breaks
FIXED: Criteria filters not hiding on change
FIXED: Main navigation was behind hover navigation  meaning end elements weren't clickable

1.1.4 : 22-Mar-2013
FIXED: Vertical navigation not working since switch to Cycle2
FIXED: Removed debug message left in code!

1.1.3 : 17-Mar-2013
ADDED: Option for custom hover nav images
ADDED: Changelog link to plugin description on plugins page

1.1.2 : 13-Mar-2013
ADDED: Message about changes to the transition options
FIXED: Bug when no easing selected slideshow didn't work
FIXED: Horizontal text disappeared

1.1.1 : 13-Mar-2013
REMOVED: Transitions preview. Will reinstate in the future
REMOVED: Easing out as no longer supported by Cycle.

1.1.0 : 12-Mar-2013
UPDATED: To jQuery Cycle2 library.(Modded so no conflict with Cycle1 in other plugins). Also, they have reduced the number of transitions, plus only one transition per slideshow.
ADDED: Option to set the browser window slide destination URLs open into
CHANGED: Improved responsive design
CHANGED: Moved Resizer into core
CHANGED: Titles nav switches to bullets on small devices
CHANGED: Now uses manual excerpt if available.
CHANGED: Horizontal content area is now fluid up to the percentage entered
FIXED: Message not showing in block when no slider selected
FIXED: General tidy up of PHP notices
FIXED: Sometimes slideshow didn't tell you when it was empty
FIXED: Critical error that would crash WP or event the server when "ideal" circumstances.
FIXED: Centre image alignment not necessarily working correctly
FIXED: Square bullets not displaying correct colours per status (active, hover, inactive) in all themes

1.0.12 :29-Jan-2013
FIXED: Compatibility with updated GalleryPlus v.1.6

1.0.11 : 20-Nov-2012
ADDED: Nav type square bullets
ADDED: Slideshow title and CSS styling
ADDED: Horizontal and vertical scrolling transition

CHANGED: Menu tabs now only show icon. Text shows when hovered. This allows longer labels without messing up the visuals
CHANGED: Default transition to Scroll left/right (horizontal)
CHANGED: Option labels to icons only as future titles will be longer and cause wrapping issues.
CHANGED: Options hover to show option name
CHANGED: Layouts of option panels to include panel title

FIXED: Missing hover navs when more than 10 images
FIXED: Custom taxonomy display to filter correctly (on slug)
FIXED: jQuery error when transition override off

1.0.10 : 5-Sep-2012
ADDED: Method to use Pizazz update checker if required

1.0.9 : 4-Sep-2012
FIXED: Warnings that were occuring on some sites

v1.0.8
FIXED: Extra dot after pzsp-container in jquery causing jquery error on some sites
FIXED: Added theme_none.css file to stop file not found error in console
UPDATED: Pizazz libs v1.3.0

v1.0.7 : 15-Aug-2012
FIXED: Hover nav not reshowing after disabling and re-enabling.
FIXED: The sometimes overly large bullets
ADDED: Sort random to NextGen gallery source
ADDED: Check for GD Library
ADDED: WordPress gallery option including using images in current post/page
CHANGED: Transition Interrupt now defaults to enabled

v1.0.6 - 3-Aug-2012
FIXED: When pruning text, was not allowing for shortcodes, so could cause broken html with extra end div

v1.0.5 - 1-Aug-2012
HELP: Added Usage section in SliderPlus Slideshow listing screen
HELP: Various tweaks and typo fixes
ADDED: SliderPlus widget
ADDED: SliderPlus WP template tag pzsplus($shortname)
ADDED: Glossy Blue theme
ADDED: Sky Blue theme
CHANGED: Put Sldieshow WP help into its own php file

v1.0.4 - 31-Jul-2012
HELP: Added "Anatomy of a slide" image to "Designing a Sldeshow" help
FIXED: Manual updater using wrong file name
CHANGED: Changing naming of Text Area to Content, and Image Area to Feature. Updated help to reflect

v1.0.3
UPDATED: Pizazz libs v1.2.11

v1.0.2
UPDATED: jQuery Cycle to v2.9999.5
CHANGED: Simple validation on some fields now, such as shortname.
ADDED: Image only and Text only options to layout selector

v1.0.1 : 24-Jul-2012
ADDED: Loading indicator when using an embedded URL
ADDED: Option to use Gallery+ or NextGen galleries as the source
ADDED: Option to use embed code in slides
CHANGED: Moved some Pizazz libs into dedicated SliderPlus libs
UPDATED: Help texts
CHANGED: Bullet size from 32px to 45px
FIXED: Filters not showing on load, only on change
FIXED: Warnings in transition effects listing
FIXED: Slide destination link not working
FIXED: Bottom border in wrong place

v1.0.00.1209

FIRST PUBLIC RELEASE
Features:
=========
- Can create many different slideshow types, such as featured content sliders, product showcases, image sliders, video slideshows, post and page sliders, advertising banners and much more
- Sliders can be embedded into posts and pages using a very simple shortcode, or, for Headway users, using the SliderPlus block.
CONTENT
- Multiple content sources (pages,posts,custom content types, slides) - and more to come, including galleries and RSS feeds.
- Special slide content type that supports videos as the slide and various common sources, such as YouTube, Vimeo, Flickr, Hulu and more
- Content filtering for Recent posts, Categories, Tags, Custom taxonomies and Slide Sets.
- Multiple slide ordering options
LAYOUT
- Full control over slide image/video size and textual content size and position
- Cropping control of images
TRANSITIONS
- Twenty transition effects to choose from with optional easing such as bounce and elastic.
- Use multiple transitions in one slider
- Options transition duration, interval, randomization and synchronization
- Uses the venerable jQuery Cycle slideshow plugin
NAVIGATION
- Navigation can be positioned left, right, top, bottom or hidden
- Navigation types of Titles, bullets and numbers
- Optional slide switcher navigation when mouseover
STYLING
- Four built-in colour themes: Light; Grey; Dark and Rainbow.
- Twenty built-in shadow variations
- Extensive custom CSS support
HELP
- Comprehensive popup contextual help on each option
- Popup help stays open while you need it to configure an option
- Generalized help built-in using WordPress' drop down help system
- Very clean and clear options panels
RESPONSIVE DESIGN
- Sliders resize on smaller screens

