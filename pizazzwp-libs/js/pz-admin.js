jQuery( document ).ready( function ()
{

    //http://jsfiddle.net/g4AXp/

//// Focal point script
//    draw_targeter();
//
//    var dst = "tr.compat-field-pzgp-focal-point input[type=\"text\"]";
//
//    //Set up bindings
//    jQuery( document ).on( "dblclick", ".media-modal .attachment-details .thumbnail", function ( e )
//    {
//        process_coordinate_set( e, this, dst );
//        document.getSelection().removeAllRanges();
//    } );
//
//    jQuery( document ).find( "div.wp_attachment_holder img.thumbnail" ).dblclick( function ( e )
//    {
//        process_coordinate_set( e, this, dst );
//    } );
//
//    jQuery( document ).find( "div.wp_attachment_holder .imgedit-crop-wrap img" ).dblclick( function ( e )
//    {
//        process_coordinate_set( e, this, dst );
//    } );
//
//    function process_coordinate_set( e, src, dst_field )
//    {
//        // Get mouse click coordinates
//        if ( e.offsetX == undefined ) // this works for Firefox
//        {
//            var relX = e.pageX - jQuery( src ).offset().left;
//            var relY = e.pageY - jQuery( src ).offset().top;
//        }
//        else                     // works in Google Chrome
//        {
//            var relX = e.offsetX;
//            var relY = e.offsetY;
//        }
//
//        // get width and height of image
//        var imgW = jQuery( src ).width();
//        var imgH = jQuery( src ).height();
//        // Calculate percentage coordinates
//        var percX = Math.floor( relX / imgW * 100 );
//        var percY = Math.floor( relY / imgH * 100 );
//        var coords = percX + ',' + percY;
//
//        jQuery( dst_field ).val( coords ).change();
//
//    }
//
//    function draw_targeter( e, src, dst_field )
//    {
//        // Draw a traget indicator which shows when hovering over the image.
//        jQuery( ".media-modal .attachment-details .thumbnail:hover, .wp_attachment_holder img.thumbnail:hover, .wp_attachment_holder .imgedit-crop-wrap img:hover" ).css( "cursor", "crosshair" );
//
//    }

} );
