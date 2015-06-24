

jQuery(document).ready(function() {
  var PZSP = {};
//  jQuery('.pzsp-entry-body').dotdotdot({
//  });
  jQuery(window).resize(function() {

    jQuery('.pzsp-nav-container.vertical').each(function(i, e) {

      PZSP.height = jQuery(this).parent().find('.pzsp-inner-wrapper').height();
      jQuery('.pzsp-nav-container.vertical').height(PZSP.height);
      jQuery(this).parent().find('.pzsp-next').height(PZSP.height);
      jQuery(this).parent().find('.pzsp-prev').height(PZSP.height);

    });

// Work on this. Needs to preserve do it when slide visiblizes
//		jQuery('.pzsp-container').each(function(i, e) {
//			jQuery('.pzsp-content-leftortop.is-image').each(function(i, e) {
//				console.log(i, jQuery(e).find('.pzsp-image-content.is-video iframe').length);
//				PZSP.found = jQuery(e).find('.pzsp-image-content.is-video iframe');
//				if (PZSP.found.length) {
//					jQuery(PZSP.found).height(jQuery(e).height());
//				}
//			});
//		});
  });
	jQuery('.pzsp-slider,	.pzsp_slider_shadow').css('visibility','visible');
});