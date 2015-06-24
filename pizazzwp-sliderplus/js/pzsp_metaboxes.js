jQuery(document).ready(function () {

  jQuery('.pizazz-meta-boxes').tabs({});

// Need to add a class to the rows to detect when focus or click

  //jQuery('#pzsp-slider-settings').find('');

  // Sources
  // have to do this individually because the functions is (currently) generic
  hide_sources();
  hide_filters();
  init();
  function init() {
    var cval = jQuery('#pzsp_content_type').val();
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_' + cval).show();
    show_filters(cval);
  }



  //Source changes
  jQuery('#pzsp_content_type').change(function () {
    hide_filters();
    var cval = jQuery(this).val();
    show_filters(cval);

  });
  //
  // filtering
  // have to do this individually because the functions is (currently) generic
  //	var fval = jQuery('#pzsp_filtering').val();
  //	jQuery('#pizazz-form-table-row-Criteria-field-pzsp_'+fval).show();
  //Filter changes
  jQuery('#pzsp_filtering').change(function () {
    var fval = jQuery(this).val();
    hide_filters();
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_' + fval).show();
  });


  function hide_filters() {
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_taxonomy').hide();
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_tags').hide();
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_category').hide();
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_specific_ids').hide();
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_slide_set').hide();
  }

  function hide_sources() {
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_gplus_gallery').hide();
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_ngg_gallery').hide();
  }

  function show_filters(cval) {
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_gplus_gallery').hide();
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_ngg_gallery').hide();
    jQuery('#pizazz-form-table-row-Criteria-field-pzsp_' + cval).show();


    // best to setup a switch here.
    // TODO FINISH THIS!!

    if (cval != 'ngg_gallery' && cval != 'gplus_gallery') {
      jQuery('#pizazz-form-table-row-Criteria-field-pzsp_filtering').show();
      var fval = jQuery('#pzsp_filtering').val();
      jQuery('#pizazz-form-table-row-Criteria-field-pzsp_' + fval).show();
      if (cval == 'page') {
        jQuery('#pizazz-form-table-row-Criteria-field-pzsp_filtering').hide();
        jQuery('#pizazz-form-table-row-Criteria-field-pzsp_slide_set').show();
// Is this meant to be an option?
//          jQuery('#pizazz-form-table-row-Criteria-field-pzsp_specific_ids').show();

      }
    } else {
      jQuery('#pizazz-form-table-row-Criteria-field-pzsp_filtering').hide();
      hide_filters();
    }

    if (cval == 'ngg_gallery') {
      jQuery('#pizazz-form-table-row-Criteria-field-pzsp_order_by').hide();
      jQuery('#pizazz-form-table-row-Criteria-field-pzsp_ngg_order_by').show();

    } else {
      jQuery('#pizazz-form-table-row-Criteria-field-pzsp_order_by').show();
      jQuery('#pizazz-form-table-row-Criteria-field-pzsp_ngg_order_by').hide();

    }

  }

  // Color popups
  jQuery('#pzsp_image_fill').ColorPicker({
    color: jQuery('#pzsp_image_fill').val(),
    onChange: function (hsb, hex, rgb) {
      jQuery('#pzsp_image_fill').val('#' + hex);
      jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_image_fill')
          .css('background-color', jQuery('#pzsp_image_fill').val());
    }
  });

  jQuery('#pzsp_border_colour').ColorPicker({
    color: jQuery('#pzsp_border_colour').val(),
    onChange: function (hsb, hex, rgb) {
      jQuery('#pzsp_border_colour').val('#' + hex);
      jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_border_colour')
          .css('background-color', jQuery('#pzsp_border_colour').val());
    }
  });

  jQuery('#pzsp_padding_colour').ColorPicker({
    color: jQuery('#pzsp_padding_colour').val(),
    onChange: function (hsb, hex, rgb) {
      jQuery('#pzsp_padding_colour').val('#' + hex);
      jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_padding_colour')
          .css('background-color', jQuery('#pzsp_padding_colour').val());

    }
  });
  jQuery('#pzsp_shadow_bgcolour').ColorPicker({
    color: jQuery('#pzsp_shadow_bgcolour').val(),
    onChange: function (hsb, hex, rgb) {
      jQuery('#pzsp_shadow_bgcolour').val('#' + hex);
      jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_shadow_bgcolour')
          .css('background-color', jQuery('#pzsp_shadow_bgcolour').val());

    }
  });
  jQuery('#pzsp_nav_item_colour_over').ColorPicker({
    color: jQuery('#pzsp_nav_item_colour_over').val(),
    onChange: function (hsb, hex, rgb) {
      jQuery('#pzsp_nav_item_colour_over').val('#' + hex);
      jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_nav_item_colour_over')
          .css('background-color', jQuery('#pzsp_nav_item_colour_over').val());

    }
  });
  jQuery('#pzsp_nav_selected_item_colour_over').ColorPicker({
    color: jQuery('#pzsp_nav_selected_item_colour_over').val(),
    onChange: function (hsb, hex, rgb) {
      jQuery('#pzsp_nav_selected_item_colour_over').val('#' + hex);
      jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_nav_selected_item_colour_over')
          .css('background-color', jQuery('#pzsp_nav_selected_item_colour_over').val());

    }
  });
  jQuery('#pzsp_nav_hover_item_colour_over').ColorPicker({
    color: jQuery('#pzsp_nav_hover_item_colour_over').val(),
    onChange: function (hsb, hex, rgb) {
      jQuery('#pzsp_nav_hover_item_colour_over').val('#' + hex);
      jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_nav_hover_item_colour_over')
          .css('background-color', jQuery('#pzsp_nav_hover_item_colour_over').val());

    }
  });

  jQuery('#pzsp_hover_nav_colour').ColorPicker({
    color: jQuery('#pzsp_hover_nav_colour').val(),
    onChange: function (hsb, hex, rgb) {
      jQuery('#pzsp_hover_nav_colour').val('#' + hex);
      jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_hover_nav_colour')
          .css('background-color', jQuery('#pzsp_hover_nav_colour').val());

    }
  });


  jQuery('#pzsp_hover_nav_colour_secondary').ColorPicker({
    color: jQuery('#pzsp_hover_nav_colour_secondary').val(),
    onChange: function (hsb, hex, rgb) {
      jQuery('#pzsp_hover_nav_colour_secondary').val('#' + hex);
      jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_hover_nav_colour_secondary')
          .css('background-color', jQuery('#pzsp_hover_nav_colour_secondary').val());

    }
  });

  jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_image_fill').css('background-color', jQuery('#pzsp_image_fill').val());
  jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_padding_colour').css('background-color', jQuery('#pzsp_padding_colour').val());
  jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_border_colour').css('background-color', jQuery('#pzsp_border_colour').val());
  jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_shadow_bgcolour').css('background-color', jQuery('#pzsp_shadow_bgcolour').val());
  jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_nav_item_colour_over').css('background-color', jQuery('#pzsp_nav_item_colour_over').val());
  jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_nav_selected_item_colour_over').css('background-color', jQuery('#pzsp_nav_selected_item_colour_over').val());
  jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_nav_hover_item_colour_over').css('background-color', jQuery('#pzsp_nav_hover_item_colour_over').val());
  jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_hover_nav_colour').css('background-color', jQuery('#pzsp_hover_nav_colour').val());
  jQuery('.pzwp_colour_swatch.pzwp_colour_pzsp_hover_nav_colour_secondary').css('background-color', jQuery('#pzsp_hover_nav_colour_secondary').val());

  fx = '';
  fxe = '';
// We don't need these since not showing transitions in backend		

  // Do the effects demo
//	jQuery('#pzsp_trans_demo_options').change(function() {
//		fx = jQuery.trim(jQuery(this).val());
//		start();
//	});
//	jQuery('#pzsp_trans_demo_easing').change(function() {
//		fxe = jQuery.trim(jQuery(this).val());
//		start();
//	});
//	function start() {
//		jQuery('div#pzsp_trans_demo').cycle2('stop');
//		jQuery('div#pzsp_trans_demo').cycle2({
//			fx: fx,
//			easing: fxe,
//			speed: 2000,
//			sync: 1,
//			timeout: 100,
//			delay: -1000,
//			continuous: 1,
//			autostop: 1,
//			autostopCount: 4,
//			pause: 1,
//			random: 1,
//			manualTrump: 0
//		});
//	}
//	jQuery('div#pzsp_trans_demo').cycle2({timeout: 0});


});
