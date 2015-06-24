/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


// closure to avoid namespace collision
(function(){
    // creates the plugin
    tinymce.create('tinymce.plugins.mygallery', {
        // creates control instances based on the control's id.
        // our button's id is &quot;mygallery_button&quot;
        createControl : function(id, controlManager) {
            if (id == 'pzsp_sliders_button') {
                // creates the button
                var button = controlManager.createButton('pzsp_sliders_button', {
                    title : 'Add SliderPlus Shortcode', // title of the button
                    image : '../wp-content/plugins/PzSliderPlus/icon.png',  // path to the button's image
                    onclick : function() {
                        // do something when the button is clicked <img src="http://www.garyc40.com/wordpress/wp-includes/images/smilies/icon_smile.gif" alt=":)" class="wp-smiley"> 
                    }
                });
                return button;
            }
            return null;
        }
    });
 
    // registers the plugin. DON'T MISS THIS STEP!!!
    tinymce.PluginManager.add('mygallery', tinymce.plugins.mygallery);
})()