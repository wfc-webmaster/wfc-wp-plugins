jQuery(document).ready(function() {


	jQuery('#pzwp_colour_0').ColorPicker({
		color: jQuery('#pzwp_colour_0').val(),
		onChange: function(hsb, hex, rgb) {
			jQuery('#pzwp_colour_0').val('#' + hex);
		}
	})

	jQuery('#pzwp_bgcolour_0').ColorPicker({
		color: jQuery('#pzwp_bgcolour_0').val(),
		onChange: function(hsb, hex, rgb) {
			jQuery('#pzwp_bgcolour_0').val('#' + hex);
		}
	})

	jQuery('#pzwp_colour_1').ColorPicker({
		color: jQuery('#pzwp_colour_1').val(),
		onChange: function(hsb, hex, rgb) {
			jQuery('#pzwp_colour_1').val('#' + hex);
		}
	})

	jQuery('#pzwp_bgcolour_1').ColorPicker({
		color: jQuery('#pzwp_bgcolour_1').val(),
		onChange: function(hsb, hex, rgb) {
			jQuery('#pzwp_bgcolour_1').val('#' + hex);
		}
	})

	jQuery('#pzwp_colour_2').ColorPicker({
		color: jQuery('#pzwp_colour_2').val(),
		onChange: function(hsb, hex, rgb) {
			jQuery('#pzwp_colour_2').val('#' + hex);
		}
	})

	jQuery('#pzwp_bgcolour_2').ColorPicker({
		color: jQuery('#pzwp_bgcolour_2').val(),
		onChange: function(hsb, hex, rgb) {
			jQuery('#pzwp_bgcolour_2').val('#' + hex);
		}
	})

	jQuery('#pzwp-floaters-form').tabs({});
});