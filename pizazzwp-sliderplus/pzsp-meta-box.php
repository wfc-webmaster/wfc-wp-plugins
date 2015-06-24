<?php


add_action('save_post', 'pizazz_save_data');
 
// Save data from meta box
function pizazz_save_data($post_id) {
die('you are here');
	// Will need to manually add each case as new types created.
	switch ($_POST['post_type']) {
		case 'pizazzsliders' :
	    global $pzsp_cpt_meta_boxes;
			$pzsp_meta_boxes = $pzsp_cpt_meta_boxes;
			break;
		case 'pzsp-slides' :
	    global $pzsp_cpt_slides_meta_boxes;
			$pzsp_meta_boxes = $pzsp_cpt_slides_meta_boxes;
			break;
		default:
			return false;
	}
 
    // verify nonce
    if (!wp_verify_nonce($_POST['pzsp_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }
 
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

	pzdebug(current_user_can('edit_page', $post_id),current_user_can('edit_post', $post_id));
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    foreach ($pzsp_meta_boxes['tabs'] as $pzsp_meta_box) {
	    foreach ($pzsp_meta_box['fields'] as $field) {
	        $old = get_post_meta($post_id, $field['id'], true);
	        $new = $_POST[$field['id']];
	        if ($new && $new != $old) {
	            update_post_meta($post_id, $field['id'], $new);
	        } elseif ('' == $new && $old) {
	            delete_post_meta($post_id, $field['id'], $old);
	        }
	    }
	}
}
