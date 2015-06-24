/**
 * Created by chrishoward on 21/06/2014.
 */
jQuery( document ).ready( function ()
{

  //http://jsfiddle.net/g4AXp/


//  jQuery( document ).on( "click", ".js--select-attachment", function ( e )
//  {
//    jQuery( ".media-modal .attachment-details .thumbnail img, .wp_attachment_holder img.thumbnail, .wp_attachment_holder .imgedit-crop-wrap img" ).attr( "title", "Click to set focal point" );
////        draw_targeter(null,null,null,50,50);
//  } );

  var dst = "tr.compat-field-pzgp-focal-point input[type=\"text\"]";

  //Set up bindings

  jQuery( document ).on( "click", ".media-modal .attachment-media-view img.details-image", function ( e )
  {
    process_coordinate_set( e, this, dst );
    document.getSelection().removeAllRanges();
  } );

  // Feature image selector

  jQuery(document ).on('click', '.attachment-info .thumbnail-image' , function ( e )
  {
    process_coordinate_set( e, this, dst );
  } );


  // Edit Media screen
  jQuery( document ).on( 'click',"div.wp_attachment_holder img.thumbnail" , function ( e )
  {
    process_coordinate_set( e, this, dst );
  } );

  //
  jQuery( document ).on('click', "div.wp_attachment_holder .imgedit-crop-wrap img", function ( e )
  {
    process_coordinate_set( e, this, dst );
  } );

  function process_coordinate_set( e, src, dst_field )
  {
    var relX, relY;
    // Get mouse click coordinates
    if ( e.offsetX == undefined ) // this works for Firefox
    {
      relX = e.pageX - jQuery( src ).offset().left;
      relY = e.pageY - jQuery( src ).offset().top;
    }
    else                     // works in Google Chrome
    {
      relX = e.offsetX;
      relY = e.offsetY;
      clickX = e.offsetX;
      clickY = e.offsetY;
    }

    // get width and height of image
    var imgW = jQuery( src ).width();
    var imgH = jQuery( src ).height();
    // Calculate percentage coordinates
    var percX = Math.floor( relX / imgW * 100 );
    var percY = Math.floor( relY / imgH * 100 );
    var coords = percX + ',' + percY;

    jQuery( dst_field ).val( coords ).change();

    ///      draw_targeter(e,src, dst_field,clickX,clickY);

  }

  // Draw a traget indicator which shows when hovering over the image.

//    function draw_targeter( e, src, dst_field, clickX,clickY )
//    {
//        // Draw a traget indicator which shows when hovering over the image.
//        var one = jQuery( ".media-modal .attachment-details .thumbnail img" );
//        var two = jQuery( ".wp_attachment_holder img.thumbnail img" );
//        var three = jQuery( ".wp_attachment_holder .imgedit-crop-wrap img" );
////        console.log(one.length,two.length,three.length);
//
//        var style = "position:absolute;color:red;z-index:1;padding:0 5px;border-radius:50px;background: rgba(255,255,255,0.5);top:"+percY+"%;left:"+percX+"%;";
//
//        one.mouseenter(function(){
//            jQuery(this).css("cursor","crosshair");
//            jQuery(this ).parent().append('<span class="hotspot" style="'+style+'">+</span>');
//        });
//        one.mouseleave(function(){
//            jQuery(this).css("cursor","default");
////            jQuery(this ).parent().find('span.hotspot').remove();
//        });
//    }
//    draw_targeter();

} );
