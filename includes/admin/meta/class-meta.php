<?php
/**
 * Post Meta Boxes 
 *
 * Used to create custom meta boxes
 * @link: https://github.com/tammyhart/Reusable-Custom-WordPress-Meta-Boxes
 * @since       1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// metaboxes directory constant
//define( 'SCHEMA_CUSTOM_METABOXES_DIR', get_template_directory_uri() . '/metaboxes' );
define( 'SCHEMA_CUSTOM_METABOXES_DIR', plugin_dir_url( __FILE__ ) );

/**
 * recives data about a form field and spits out the proper html
 *
 * @param	array					$field			array with various bits of information about the field
 * @param	string|int|bool|array	$meta			the saved data for this field
 * @param	array					$repeatable		if is this for a repeatable field, contains parant id and the current integar
 *
 * @return	string									html for the field
 */
function schema_wp_custom_meta_box_field( $field, $meta = null, $repeatable = null ) {
	if ( ! ( $field || is_array( $field ) ) )
		return;
	
	// get field data
	$type = isset( $field['type'] ) ? $field['type'] : null;
	$label = isset( $field['label'] ) ? $field['label'] : null;
	$desc = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : null;
	$tip = isset( $field['tip'] ) ? '<span data-tooltip="'.$field['tip'].'"><span class="dashicons dashicons-info"></span></span>' : null;
	$placeholder = isset( $field['placeholder'] ) ? $field['placeholder'] : null;
	$place = isset( $field['place'] ) ? $field['place'] : null;
	$size = isset( $field['size'] ) ? $field['size'] : 'regular';
	$class = isset( $field['class'] ) ? $field['class'] : '';
	$post_type = isset( $field['post_type'] ) ? $field['post_type'] : null;
	$options = isset( $field['options'] ) ? $field['options'] : null;
	$settings = isset( $field['settings'] ) ? $field['settings'] : null;
	$repeatable_fields = isset( $field['repeatable_fields'] ) ? $field['repeatable_fields'] : null;
	$selectone = isset( $field['selectone'] ) ? $field['selectone'] : __('Select One', 'schema-wp');
	$sanitizer = isset( $field['sanitizer'] ) ? $field['sanitizer'] : null;
	$default = isset( $field['default'] ) ? $field['default'] : null;
	$required = isset( $field['required'] ) ? $field['required'] : null;
	$min = isset( $field['min'] ) ? $field['min'] : null;
	$max = isset( $field['max'] ) ? $field['max'] : null;
	$step = isset( $field['step'] ) ? $field['step'] : null;
	$multiple = isset( $field['multiple'] ) ? $field['multiple'] : null;
	
	// the id and name for each field
	$id = $name = isset( $field['id'] ) ? $field['id'] : null;
	if ( $repeatable ) {
		$name = $repeatable[0] . '[' . $repeatable[1] . '][' . $id .']';
		$id = $repeatable[0] . '_' . $repeatable[1] . '_' . $id;
	}
	switch( $type ) {
		// separator 
		case 'separator':
			echo '<div style="height:2px; border-bottom: 1px dashed #ccc;"></div>';
		break;
		// opening div
		case 'div_open':
			echo '<span id="' . esc_attr( $id ) . '" class="toggle">'.__('Advanced', 'schema-wp').' <span class="dashicons dashicons-arrow-down-alt2"></span></span>';
			echo '</li></ul><span style="clear:both;display:block;"></span><div id="' . esc_attr( $id ) . '_wrap" class="toggle_div"><ul>';
		break;
		// closing div
		case 'div_close':
			echo '</li></ul></div>';
		break;
		// headline
		case 'desc':
			echo '<p>'.$desc.'</p>';
		break;
		// basic
		case 'text':
		case 'tel':
		case 'email':
		default:
			if ( is_array($meta) ) break; // if $meta has an array, continue to the next case, @since 1.6.9.4 
			echo '<input type="' . $type . '" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . esc_attr( $meta ) . '" class="'.$size.'-text '.$class.'" size="30" placeholder="' . $placeholder . '" />
					<br />' . $desc;
		break;
		case 'url':
			echo '<input type="' . $type . '" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" placeholder="' . $placeholder . '" value="' . esc_url( $meta ) . '" class="regular-text '.$class.'" size="30" />
					<br />' . $desc;
		break;
		case 'number':
			echo '<input type="' . $type . '" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . intval( $meta ) . '" class="'.$size.'-text '.$class.'" size="30" />
					<br />' . $desc;
		break;
		// textarea
		case 'textarea':
			echo '<textarea name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" placeholder="' . $placeholder . '" cols="60" rows="4" class="'.$class.'">' . esc_textarea( $meta ) . '</textarea>
					<br />' . $desc;
		break;
		// editor
		case 'editor':
			echo wp_editor( $meta, $id, $settings ) . '<br />' . $desc;
		break;
		// checkbox
		case 'checkbox':
			echo '<input type="checkbox" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" ' . checked( $meta, true, false ) . ' value="1" />
					<label for="' . esc_attr( $id ) . '">' . $desc . '</label>';
		break;
		// select, chosen
		case 'select':
		case 'chosen':
		
		if ( '' === $meta || array() === $meta ) {
				$meta = isset($field['default']) ? $field['default'] : '';
			}
			
			echo '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '"' , $type == 'chosen' ? ' class="'.$class.' chosen"' : ' class="'.$class.'"' , isset( $multiple ) && $multiple == true ? ' multiple="multiple"' : '' , '>
					<option value="">' . $selectone . '</option>'; // Select One
			foreach ( $options as $option )
				echo '<option' . selected( $meta, $option['value'], false ) . ' value="' . $option['value'] . '">' . $option['label'] . '</option>';
			echo '</select><br />' . $desc;
		break;
		// radio
		case 'radio':
			echo '<ul class="meta_box_items">';
			foreach ( $options as $option )
				echo '<li><input type="radio" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '-' . $option['value'] . '" value="' . $option['value'] . '" ' . checked( $meta, $option['value'], false ) . ' />
						<label for="' . esc_attr( $id ) . '-' . $option['value'] . '">' . $option['label'] . '</label></li>';
			echo '</ul><p>' . $desc . '</p>';
		break;
		// checkbox_group
		case 'checkbox_group':
			echo '<ul class="meta_box_items">';
			foreach ( $options as $option )
				echo '<li><input type="checkbox" value="' . $option['value'] . '" name="' . esc_attr( $name ) . '[]" id="' . esc_attr( $id ) . '-' . $option['value'] . '"' , is_array( $meta ) && in_array( $option['value'], $meta ) ? ' checked="checked"' : '' , ' /> 
						<label for="' . esc_attr( $id ) . '-' . $option['value'] . '">' . $option['label'] . '</label></li>';
			echo '</ul><p>' . $desc . '</p>';
		break;
		// checkbox_group
		case 'checkbox_group_inline':
			echo '<ul class="meta_box_items">';
			foreach ( $options as $option )
				echo '<input class="'.esc_attr( $option['value'] ).'" type="checkbox" value="' . $option['value'] . '" name="' . esc_attr( $name ) . '[]" id="' . esc_attr( $id ) . '-' . $option['value'] . '"' , is_array( $meta ) && in_array( $option['value'], $meta ) ? ' checked="checked"' : '' , ' /> 
						<label for="' . esc_attr( $id ) . '-' . $option['value'] . '">' . $option['label'] . '</label><br>';
			echo '<p>' . $desc . '</p>';
		break;
		// color
		case 'color':
			$meta = $meta ? $meta : '#';
			echo '<input type="text" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . $meta . '" size="10" />
				<br />' . $desc;
			echo '<div id="colorpicker-' . esc_attr( $id ) . '"></div>
				<script type="text/javascript">
				jQuery(function(jQuery) {
					jQuery("#colorpicker-' . esc_attr( $id ) . '").hide();
					jQuery("#colorpicker-' . esc_attr( $id ) . '").farbtastic("#' . esc_attr( $id ) . '");
					jQuery("#' . esc_attr( $id ) . '").bind("blur", function() { jQuery("#colorpicker-' . esc_attr( $id ) . '").slideToggle(); } );
					jQuery("#' . esc_attr( $id ) . '").bind("focus", function() { jQuery("#colorpicker-' . esc_attr( $id ) . '").slideToggle(); } );
				});
				</script>';
		break;
		// post_select, post_chosen
		case 'post_select':
		case 'post_list':
		case 'post_chosen':
			echo '<select data-placeholder="Select One" name="' . esc_attr( $name ) . '[]" id="' . esc_attr( $id ) . '"' , $type == 'post_chosen' ? ' class="chosen"' : '' , isset( $multiple ) && $multiple == true ? ' multiple="multiple"' : '' , '>
					<option value=""></option>'; // Select One
			$posts = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => -1, 'orderby' => 'name', 'order' => 'ASC' ) );
			foreach ( $posts as $item )
				echo '<option value="' . $item->ID . '"' . selected( is_array( $meta ) && in_array( $item->ID, $meta ), true, false ) . '>' . $item->post_title . '</option>';
			$post_type_object = get_post_type_object( $post_type );
			if(isset($post_type_object->label))
				echo '</select> &nbsp;<span class="description"><a href="' . admin_url( 'edit.php?post_type=' . $post_type . '">Manage ' . $post_type_object->label ) . '</a></span><br />' . $desc;
		break;
		// post_checkboxes
		case 'post_checkboxes':
			$posts = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => -1 ) );
			echo '<ul class="meta_box_items">';
			foreach ( $posts as $item ) 
				echo '<li><input type="checkbox" value="' . $item->ID . '" name="' . esc_attr( $name ) . '[]" id="' . esc_attr( $id ) . '-' . $item->ID . '"' , is_array( $meta ) && in_array( $item->ID, $meta ) ? ' checked="checked"' : '' , ' />
						<label for="' . esc_attr( $id ) . '-' . $item->ID . '">' . $item->post_title . '</label></li>';
			$post_type_object = get_post_type_object( $post_type );
			echo '</ul> ' . $desc , ' &nbsp;<span class="description"><a href="' . admin_url( 'edit.php?post_type=' . $post_type . '">Manage ' . $post_type_object->label ) . '</a></span>';
		break;
		// post_drop_sort
		case 'post_drop_sort':
			//areas
			$post_type_object = get_post_type_object( $post_type );
			echo '<p>' . $desc . ' &nbsp;<span class="description"><a href="' . admin_url( 'edit.php?post_type=' . $post_type . '">Manage ' . $post_type_object->label ) . '</a></span></p><div class="post_drop_sort_areas">';
			foreach ( $areas as $area ) {
				echo '<ul id="area-' . $area['id']  . '" class="sort_list">
						<li class="post_drop_sort_area_name">' . $area['label'] . '</li>';
						if ( is_array( $meta ) ) {
							$items = explode( ',', $meta[$area['id']] );
							foreach ( $items as $item ) {
								$output = $display == 'thumbnail' ? get_the_post_thumbnail( $item, array( 204, 30 ) ) : wp_filter_nohtml_kses(get_the_title( $item )); 
								echo '<li id="' . $item . '">' . $output . '</li>';
							}
						}
				echo '</ul>
					<input type="hidden" name="' . esc_attr( $name ) . '[' . $area['id'] . ']" 
					class="store-area-' . $area['id'] . '" 
					value="' , $meta ? $meta[$area['id']] : '' , '" />';
			}
			echo '</div>';
			// source
			$exclude = null;
			if ( !empty( $meta ) ) {
				$exclude = implode( ',', $meta ); // because each ID is in a unique key
				$exclude = explode( ',', $exclude ); // put all the ID's back into a single array
			}
			$posts = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => -1, 'post__not_in' => $exclude ) );
			echo '<ul class="post_drop_sort_source sort_list">
					<li class="post_drop_sort_area_name">Available ' . $label . '</li>';
			foreach ( $posts as $item ) {
				$output = $display == 'thumbnail' ? get_the_post_thumbnail( $item->ID, array( 204, 30 ) ) : wp_filter_nohtml_kses(get_the_title( $item->ID )); 
				echo '<li id="' . $item->ID . '">' . $output . '</li>';
			}
			echo '</ul>';
		break;
		// tax_select
		case 'tax_select':
			echo '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '">
					<option value="">Select One</option>'; // Select One
			$terms = get_terms( $id, 'get=all' );
			$post_terms = wp_get_object_terms( get_the_ID(), $id );
			$taxonomy = get_taxonomy( $id );
			$selected = $post_terms ? $taxonomy->hierarchical ? $post_terms[0]->term_id : $post_terms[0]->slug : null;
			foreach ( $terms as $term ) {
				$term_value = $taxonomy->hierarchical ? $term->term_id : $term->slug;
				echo '<option value="' . $term_value . '"' . selected( $selected, $term_value, false ) . '>' . $term->name . '</option>'; 
			}
			echo '</select> &nbsp;<span class="description"><a href="'.get_bloginfo( 'url' ) . '/wp-admin/edit-tags.php?taxonomy=' . $id . '">Manage ' . $taxonomy->label . '</a></span>
				<br />' . $desc;
		break;
		// CPT, custom post types
		case 'cpt':
			$options = apply_filters('schema_wp_get_post_types_settings', schema_wp_get_post_types() );
			echo $desc;
			if (!empty($options)) {
				echo '<ul class="meta_box_cpt_items">';
				foreach ( $options as $option => $info )
					echo '<li><input type="checkbox" value="' . $option . '" name="' . esc_attr( $name ) . '['.$option.']" id="' . esc_attr( $id ) . '-' . $option . '"' , is_array( $meta ) && in_array( $option, $meta, true ) ? ' checked="checked"' : '' , ' /> 
						<label for="' . esc_attr( $id ) . '-' . $option. '">' . $info['label'] . '</label></li>';
				echo '</ul>';
			} else {
				echo '';
			}
		break;
		// tax_checkboxes
		case 'tax_checkboxes':
			$terms = get_terms( $id, 'get=all' );
			$post_terms = wp_get_object_terms( get_the_ID(), $id );
			$taxonomy = get_taxonomy( $id );
			$checked = $post_terms ? $taxonomy->hierarchical ? $post_terms[0]->term_id : $post_terms[0]->slug : null;
			foreach ( $terms as $term ) {
				$term_value = $taxonomy->hierarchical ? $term->term_id : $term->slug;
				echo '<input type="checkbox" value="' . $term_value . '" name="' . $id . '[]" id="term-' . $term_value . '"' . checked( $checked, $term_value, false ) . ' /> <label for="term-' . $term_value . '">' . $term->name . '</label><br />';
			}
			echo '<span class="description">' . $field['desc'] . ' <a href="'.get_bloginfo( 'url' ) . '/wp-admin/edit-tags.php?taxonomy=' . $id . '&post_type=' . $page . '">Manage ' . $taxonomy->label . '</a></span>';
		break;
		// date
		case 'date':
			echo '<input type="text" class="datepicker" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . $meta . '" size="30" />
					<br />' . $desc;
		break;
		// slider
		case 'slider':
			$value = $meta != '' ? intval( $meta ) : '0';
			echo '<div id="' . esc_attr( $id ) . '-slider"></div>
				<br />
				<input type="text" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" value="' . $value . '" size="5" /> ' . $desc;
		break;
		// rating slider
		case 'sliderrating':
			$post_id 		= isset($_GET['post']) ? $_GET['post'] : null;
			$rating_type  	= schema_wp_review_get_rating_type( $post_id ); 
			$rating_scale 	= schema_wp_review_get_rating_scale( $rating_type ); 
			$value 			= $meta != '' ? intval( $meta ) : '0';
			$value        	= schema_wp_review_adjust_rating( $value, $rating_scale );
		
			//$value = $meta != '' ? intval( $meta ) : '0';
			echo '<div id="' . esc_attr( $id ) . '-sliderrating"></div>
				<br />
				<input type="text" readonly="readonly" name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '" class="' . esc_attr( $id ) . '" value="' . $value . '" size="5" />' . $desc;
		break;
		// image
		case 'image':
			$image = SCHEMA_CUSTOM_METABOXES_DIR . '/images/image.png';	
			echo '<div class="meta_box_image"><span class="meta_box_default_image" style="display:none">' . $image . '</span>';
			if ( $meta ) {
				$image = wp_get_attachment_image_src( intval( $meta ), 'medium' );
				$image = $image[0];
			}				
			echo	'<input name="' . esc_attr( $name ) . '" type="hidden" class="meta_box_upload_image" value="' . intval( $meta ) . '" />
						<img src="' . esc_attr( $image ) . '" class="meta_box_preview_image" alt="" />
							<a href="#" class="meta_box_upload_image_button button" rel="' . get_the_ID() . '">Choose Image</a>
							<small>&nbsp;<a href="#" class="meta_box_clear_image_button">Remove Image</a></small></div>
							<br clear="all" />' . $desc;
		break;
		// file
		case 'file':		
			$iconClass = 'meta_box_file';
			if ( $meta ) $iconClass .= ' checked';
			echo	'<div class="meta_box_file_stuff"><input name="' . esc_attr( $name ) . '" type="hidden" class="meta_box_upload_file" value="' . esc_url( $meta ) . '" />
						<span class="' . $iconClass . '"></span>
						<span class="meta_box_filename">' . esc_url( $meta ) . '</span>
							<a href="#" class="meta_box_upload_image_button button" rel="' . get_the_ID() . '">Choose File</a>
							<small>&nbsp;<a href="#" class="meta_box_clear_file_button">Remove File</a></small></div>
							<br clear="all" />' . $desc;
		break;
		// repeatable
		case 'repeatable':
			echo '<table id="' . esc_attr( $id ) . '-repeatable" class="meta_box_repeatable" cellspacing="0">
				<thead>
					<tr>
						<th><span class="sort_label"><span class="dashicons dashicons-menu"></span></span></th>
						<th>Fields</th>
						<th><a class="meta_box_repeatable_add" href="#"><span class="dashicons dashicons-plus-alt"></span></a></th>
					</tr>
				</thead>
				<tbody>';
			$i = 0;
			// create an empty array
			if ( $meta == '' || $meta == array() ) {
				$keys = wp_list_pluck( $repeatable_fields, 'id' );
				$meta = array ( array_fill_keys( $keys, null ) );
			}
			$meta = array_values( $meta );
			foreach( $meta as $row ) {
				echo '<tr>
						<td><span class="sort hndle"><span class="dashicons dashicons-menu"></span></span></td><td>';
				foreach ( $repeatable_fields as $repeatable_field ) {
					if ( ! array_key_exists( $repeatable_field['id'], $meta[$i] ) )
						$meta[$i][$repeatable_field['id']] = null;
					echo '<label>' . $repeatable_field['label']  . '</label><p>';
					echo schema_wp_custom_meta_box_field( $repeatable_field, $meta[$i][$repeatable_field['id']], array( $id, $i ) );
					echo '</p>';
				} // end each field
				echo '</td><td><a class="meta_box_repeatable_remove repeatable-remove" href="#"><span class="dashicons dashicons-dismiss"></span></a></td></tr>';
				$i++;
			} // end each row
			echo '</tbody>';
			echo '
				<tfoot>
					<tr>
						<th><span class="sort_label"><span class="dashicons dashicons-menu"></span></span></th>
						<th>' . __('Fields', 'schema-wp') . '</th>
						<th><a class="meta_box_repeatable_add" href="#"><span class="dashicons dashicons-plus-alt"></span></a></th>
					</tr>
				</tfoot>';
			echo '</table>
				' . $desc;
		break;
		
		// repeatable row
		case 'repeatable_row':
			echo '<table id="' . esc_attr( $id ) . '-repeatable" class="meta_box_repeatable meta_box_repeatable_row" cellspacing="0">
				<thead>
					<tr>
						<th><span class="sort_label"><span class="dashicons dashicons-menu"></span></span></th>
						<th>Fields</th>
						<th><a class="meta_box_repeatable_add" href="#"><span class="dashicons dashicons-plus-alt"></span></a></th>
					</tr>
				</thead>
				<tbody>';
			$i = 0;
			// create an empty array
			if ( $meta == '' || $meta == array() ) {
				$keys = wp_list_pluck( $repeatable_fields, 'id' );
				$meta = array ( array_fill_keys( $keys, null ) );
			}
			$meta = array_values( $meta );
			//echo '<pre>'; print_r($meta); echo '</pre>';// exit;
			foreach( $meta as $row ) {
				echo '<tr>
						<td><span class="sort hndle"><span class="dashicons dashicons-menu"></span></span></td><td><ul>';
				foreach ( $repeatable_fields as $repeatable_field ) {
					if ( ! array_key_exists( $repeatable_field['id'], $meta[$i] ) )
						$meta[$i][$repeatable_field['id']] = null;
					//echo '<li><label>' . $repeatable_field['label']  . '</label>';
					echo '<li><label></label>';
					//echo '<li>';
					echo schema_wp_custom_meta_box_field( $repeatable_field, $meta[$i][$repeatable_field['id']], array( $id, $i ) );
					echo '</li>';
				} // end each field
				echo '</td><td><a class="meta_box_repeatable_remove" href="#"><span class="dashicons dashicons-dismiss"></span></a></td></tr>';
				$i++;
			} // end each row
			echo '</tbody>';
			echo '
				<tfoot>
					<tr>
						<th><span class="sort_label"><span class="dashicons dashicons-menu"></span></span></th>
						<th>Fields</th>
						<th><a class="meta_box_repeatable_add" href="#"><span class="dashicons dashicons-plus-alt"></span></a></th>
					</tr>
				</tfoot>';
			echo '</table>
				  <p><br />' . $desc . '</p>';
		break;
		
	} //end switch
		
}


/**
 * Finds any item in any level of an array
 *
 * @param	string	$needle 	field type to look for
 * @param	array	$haystack	an array to search the type in
 *
 * @return	bool				whether or not the type is in the provided array
 * @since 1.0
 */
function schema_wp_meta_box_find_field_type($keySearch, $array) {
	
	if ( ! is_array( $array) ) return;
	
    foreach ($array as $key => $item) {
        
		if ( isset( $key['type'] ) && $key['type'] == 'repeatable' )
			return schema_wp_meta_box_find_field_type( $keySearch, $key['repeatable_fields'] );
		elseif ( isset( $key['type'] ) && $key['type'] == 'repeatable_row' )
			return schema_wp_meta_box_find_field_type( $keySearch, $h['repeatable_fields'] );
		elseif ( ( isset( $key['type'] ) && $key['type'] == $keySearch ) || ( isset( $key['repeatable_type'] ) && $key['repeatable_type'] == $keySearch ) )
			return true;
		
		// additional check
		// @since 1.6.9.1
		if ($key == $keySearch) {
            return true;
        } else {
            if (is_array($item) && schema_wp_meta_box_find_field_type($item, $keySearch)) {
               return true;
            }
        }
    }

    return false;
}


/**
 * Find repeatable
 *
 * This function does almost the same exact thing that the above function 
 * does, except we're exclusively looking for the repeatable field. The 
 * reason is that we need a way to look for other fields nested within a 
 * repeatable, but also need a way to stop at repeatable being true. 
 * Hopefully I'll find a better way to do this later.
 *
 * @param	string	$needle 	field type to look for
 * @param	array	$haystack	an array to search the type in
 *
 * @return	bool				whether or not the type is in the provided array
 */
function schema_wp_meta_box_find_repeatable( $needle = 'repeatable', $haystack ) {
	foreach ( $haystack as $h )
		if ( isset( $h['type'] ) && $h['type'] == $needle )
			return true;
	return false;
}

/**
 * Find repeatable_row
 *
 * This function does almost the same exact thing that the above function 
 * does, except we're exclusively looking for the repeatable field. The 
 * reason is that we need a way to look for other fields nested within a 
 * repeatable, but also need a way to stop at repeatable being true. 
 * Hopefully I'll find a better way to do this later.
 *
 * @param	string	$needle 	field type to look for
 * @param	array	$haystack	an array to search the type in
 *
 * @return	bool				whether or not the type is in the provided array
 */
function schema_wp_meta_box_find_repeatable_row( $needle = 'repeatable_row', $haystack ) {
	foreach ( $haystack as $h )
		if ( isset( $h['type'] ) && $h['type'] == $needle )
			return true;
	return false;
}

/**
 * sanitize boolean inputs
 */
function schema_wp_meta_box_santitize_boolean( $string ) {
	if ( ! isset( $string ) || $string != 1 || $string != true )
		return false;
	else
		return true;
}

/**
 * outputs properly sanitized data
 *
 * @param	string	$string		the string to run through a validation function
 * @param	string	$function	the validation function
 *
 * @return						a validated string
 */
function schema_wp_meta_box_sanitize( $string, $function = 'sanitize_text_field' ) {
	switch ( $function ) {
		case 'intval':
			return intval( $string );
		case 'absint':
			return absint( $string );
		case 'wp_kses_post':
			return wp_kses_post( $string );
		case 'wp_kses_data':
			return wp_kses_data( $string );
		case 'esc_url_raw':
			return esc_url_raw( $string );
		case 'is_email':
			return is_email( $string );
		case 'sanitize_title':
			return sanitize_title( $string );
		case 'santitize_title_with_dashes':
			return sanitize_title_with_dashes( $string );
		case 'santitize_boolean':
			return santitize_boolean( $string );
		case 'sanitize_html_class':
			return sanitize_html_class( $string );
		case 'no_santitize':
			return $string;
		case 'sanitize_text_field':
		default:
			return sanitize_text_field( $string );
	}
}

/**
 * Map a multideminsional array
 *
 * @param	string	$func		the function to map
 * @param	array	$meta		a multidimensional array
 * @param	array	$sanitizer	a matching multidimensional array of sanitizers
 *
 * @return	array				new array, fully mapped with the provided arrays
 */
function schema_wp_meta_box_array_map_r( $func, $meta, $sanitizer ) {
		
	$newMeta = array();
	$meta = array_values( $meta );
	
	foreach( $meta as $key => $array ) {
		if ( $array == '' )
			continue;
		/**
		 * some values are stored as array, we only want multidimensional ones
		 */
		if ( ! is_array( $array ) ) {
			return array_map( $func, $meta, (array)$sanitizer );
			break;
		}
		/**
		 * the sanitizer will have all of the fields, but the item may only 
		 * have valeus for a few, remove the ones we don't have from the santizer
		 */
		$keys = array_keys( $array );
		$newSanitizer = $sanitizer;
		if ( is_array( $sanitizer ) ) {
			foreach( $newSanitizer as $sanitizerKey => $value )
				if ( ! in_array( $sanitizerKey, $keys ) )
					unset( $newSanitizer[$sanitizerKey] );
		}
		/**
		 * run the function as deep as the array goes
		 */
		foreach( $array as $arrayKey => $arrayValue )
			if ( is_array( $arrayValue ) )
				$array[$arrayKey] = schema_wp_meta_box_array_map_r( $func, $arrayValue, $newSanitizer[$arrayKey] );
		
		$array = array_map( $func, $array, $newSanitizer );
		$newMeta[$key] = array_combine( $keys, array_values( $array ) );
	}
	return $newMeta;
}

/**
 * takes in a few peices of data and creates a custom meta box
 *
 * @param	string			$id			meta box id
 * @param	string			$title		title
 * @param	array			$fields		array of each field the box should include
 * @param	string|array	$page		post type to add meta box to
 */
class Schema_Custom_Add_Meta_Box {
	
	var $id;
	var $title;
	var $fields;
	var $page;
	var $context;
	var $priority;
	
    public function __construct( $id, $title, $fields, $page, $context = 'advanced', $priority = 'high' ) {
		$this->id = $id;
		$this->title = $title;
		$this->fields = $fields;
		$this->page = $page;
		$this->context = $context;
		$this->priority = $priority;
		
		if( ! is_array( $this->page ) )
			$this->page = array( $this->page );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_head',  array( $this, 'admin_head' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_box' ) );
		add_action( 'save_post',  array( $this, 'save_box' ));
    }
	
	/**
	 * enqueue necessary scripts and styles
	 */
	function admin_enqueue_scripts() {
		global $pagenow;
		if ( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) && in_array( get_post_type(), $this->page ) ) {
			
			// debug
			//echo'<pre>';print_r($this->fields);echo'</pre>';
			
			// js
			$deps = array( 'jquery' ); 
			if ( schema_wp_meta_box_find_field_type( 'date', $this->fields ) )
				$deps[] = 'jquery-ui-datepicker';
			if (  in_array( true, array(
				schema_wp_meta_box_find_field_type( 'slider', $this->fields ),
				schema_wp_meta_box_find_field_type( 'sliderrating', $this->fields )
			) ) ) {
				$deps[] = 'jquery-ui-slider';
			}
			if ( schema_wp_meta_box_find_field_type( 'color', $this->fields ) )
				$deps[] = 'farbtastic';
			if ( in_array( true, array(
				schema_wp_meta_box_find_field_type( 'chosen', $this->fields ),
				schema_wp_meta_box_find_field_type( 'post_chosen', $this->fields )
			) ) ) {
				wp_register_script( 'chosen', SCHEMA_CUSTOM_METABOXES_DIR . 'js/chosen.js', array( 'jquery' ) );
				$deps[] = 'chosen';
				wp_enqueue_style( 'chosen', SCHEMA_CUSTOM_METABOXES_DIR . 'css/chosen.css' );
			}
			if ( in_array( true, array( 
				schema_wp_meta_box_find_field_type( 'date', $this->fields ), 
				schema_wp_meta_box_find_field_type( 'slider', $this->fields ),
				schema_wp_meta_box_find_field_type( 'sliderrating', $this->fields ),
				schema_wp_meta_box_find_field_type( 'color', $this->fields ),
				schema_wp_meta_box_find_field_type( 'chosen', $this->fields ),
				schema_wp_meta_box_find_field_type( 'post_chosen', $this->fields ),
				schema_wp_meta_box_find_repeatable( 'repeatable', $this->fields ),
				schema_wp_meta_box_find_repeatable( 'repeatable_row', $this->fields ),
				schema_wp_meta_box_find_field_type( 'image', $this->fields ),
				schema_wp_meta_box_find_field_type( 'file', $this->fields )
			) ) )
				wp_enqueue_script( 'meta_box', SCHEMA_CUSTOM_METABOXES_DIR . 'js/scripts.js', $deps );
				
			if ( in_array( true, array( 
				schema_wp_meta_box_find_field_type( 'select', $this->fields )
			) ) )
				wp_enqueue_script( 'schema_meta_box', SCHEMA_CUSTOM_METABOXES_DIR . 'js/schema.js', $deps );
			
			// Load media uploader required scripts
			if ( in_array( true, array( 
				schema_wp_meta_box_find_field_type( 'image', $this->fields )
				) ) ) {
				if(function_exists('wp_enqueue_media')) {
            		wp_enqueue_media();
        		} else {
            		wp_enqueue_script('media-upload');
            		wp_enqueue_script('thickbox');
            		wp_enqueue_style('thickbox');
        		}
			}
			
			// css
			$deps = array();
			wp_register_style( 'jqueryui', SCHEMA_CUSTOM_METABOXES_DIR . 'css/jqueryui.css' ); 
			if ( schema_wp_meta_box_find_field_type( 'date', $this->fields ) || schema_wp_meta_box_find_field_type( 'slider', $this->fields ) || schema_wp_meta_box_find_field_type( 'sliderrating', $this->fields ) )
				$deps[] = 'jqueryui';
			if ( schema_wp_meta_box_find_field_type( 'color', $this->fields ) )
				$deps[] = 'farbtastic';
			wp_enqueue_style( 'meta_box', SCHEMA_CUSTOM_METABOXES_DIR . 'css/meta_box.css', $deps );
		}
	}
	
	/**
	 * adds scripts to the head for special fields with extra js requirements
	 */
	function admin_head() {
		
		if ( in_array( get_post_type(), $this->page ) && ( schema_wp_meta_box_find_field_type( 'date', $this->fields ) 
															|| schema_wp_meta_box_find_field_type( 'slider', $this->fields ) 
															|| schema_wp_meta_box_find_field_type( 'sliderrating', $this->fields ) ) ) {
		
			echo '<script type="text/javascript">
						jQuery(function($) {';
			
			foreach ( $this->fields as $field ) {
				switch( $field['type'] ) {
					// date
					case 'date' :
						echo '$("#' . $field['id'] . '").datepicker({
								dateFormat: \'yy-mm-dd\'
							});';
					break;
					// slider
					case 'slider' :
						$value = get_post_meta( get_the_ID(), $field['id'], true );
						if ( $value == '' )
							$value = $field['min'];
						echo '
							$( "#' . $field['id'] . '-slider" ).slider({
								range : "min",
								value: ' . $value . ',
								min: ' . $field['min'] . ',
								max: ' . $field['max'] . ',
								step: ' . $field['step'] . ',
								slide: function( event, ui ) {
									$( "#' . $field['id'] . '" ).val( ui.value );
								}
							});';
					break;
					// slider rating: Used by the Schema Review extension
					case 'sliderrating' :
						$post_id = get_the_ID();
						$value = get_post_meta( $post_id, $field['id'], true );
						if ( $value == '' )
							$value = $field['min'];
						
						$_scale 		= schema_wp_get_option( 'schema_review_rating_scale', false );
						$rating_type  	= schema_wp_review_get_rating_type( $post_id ); 
						$rating_scale 	= schema_wp_review_get_rating_scale( $rating_type ); 
						$value			= schema_wp_review_adjust_rating( $value, $rating_scale );
							
						echo '
							$( "#' . $field['id'] . '-sliderrating" ).slider({
								range : "min",
								value: ' . $value . ',
								min: ' . $rating_scale['min'] . ',
								max: ' . $rating_scale['max'] . ',
								step: ' . $rating_scale['step'] . ',
								slide: function( event, ui ) {
									$( "#' . $field['id'] . '" ).val( ui.value );
								}
							});
							
							var RatingType = "";
							var RatingScale = ' . json_encode($_scale) . ';
							var newBegin = 0;
							var newEnd   = 0;
							var newStep  = 0;
	
							$("#_schema_review_rating_type").on("change", function() {
								
        						var currentVal = parseInt($("#' . $field['id'] . '-sliderrating" ).slider("value")),
        						RatingType = $("#_schema_review_rating_type").val();
								
								var newBegin = parseInt(RatingScale[RatingType]["min"]);
								var newEnd 	 = parseInt(RatingScale[RatingType]["max"]);
								var newStep  = parseFloat(RatingScale[RatingType]["step"]);
    							
								if ( currentVal > newEnd ) var currentVal = newEnd;
								
								
								var $slide = $("#' . $field['id'] . '-sliderrating" ).slider({
        							range : "min",
									value: currentVal,
        							min: newBegin,
        							max: newEnd,
									step: newStep,
									slide: function( event, ui ) {
										$( "#' . $field['id'] . '" ).val( ui.value );
									}
        						});
								$slide.slider("value", $slide.slider("value"));
      						});
						';
					break;
				}
			}
			
			echo '});
			</script>';
		}
	}
	
	/**
	 * adds the meta box for every post type in $page
	 */
	function add_box() {
		foreach ( $this->page as $page ) {
			add_meta_box( $this->id, $this->title, array( $this, 'meta_box_callback' ), $page, $this->context, $this->priority );
		}
	}
	
	/**
	 * outputs the meta box
	 */
	function meta_box_callback() {
		// Use nonce for verification
		wp_nonce_field( 'custom_meta_box_nonce_action', 'custom_meta_box_nonce_field' );
		
		// Begin the field table and loop
		echo '<table class="form-table meta_box">';
		foreach ( $this->fields as $field) {
			
			if ( !isset($field['label']) ) 
				$field['label'] = null;
			
			// set tooltip
			$tip = isset( $field['tip'] ) ? '<span data-tooltip="'.$field['tip'].'"><span class="dashicons dashicons-info"></span></span>' : null;
			
			if ( $field['type'] == 'section' ) {
				echo '<tr>
						<td colspan="2">
							<h2>' . $field['label']  . ' ' . $tip . '</h2>
						</td>
					</tr>';
			}
			else {
				$label = isset($field['required']) ? $field['label'] . ' <span class="required">*</span>' : $field['label'];
				echo '<tr>
						<th style="width:20%"><label for="' . $field['id'] . '">' . $label . ' ' . $tip . '</label></th>
						<td>';
						
						$meta = get_post_meta( get_the_ID(), $field['id'], true);
						echo schema_wp_custom_meta_box_field( $field, $meta );
						
				echo     '<td>
					</tr>';
			}
		} // end foreach
		echo '</table>'; // end table
	}
	
	/**
	 * saves the captured data
	 */
	function save_box( $post_id ) {
		
		$post_type = get_post_type();
		
		// verify nonce
		if ( ! isset( $_POST['custom_meta_box_nonce_field'] ) )
			return $post_id;
		if ( ! ( in_array( $post_type, $this->page ) || wp_verify_nonce( $_POST['custom_meta_box_nonce_field'],  'custom_meta_box_nonce_action' ) ) ) 
			return $post_id;
		// check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
		// check permissions
		//if ( ! current_user_can( 'edit_page', $post_id ) )
			//return $post_id;
		
		// debug
		//if ( isset( $_POST['_schema_review_rating_type'] ) ) echo $_POST['_schema_review_rating_type']; 
		//echo '<pre>'; print_r($_POST);echo'</pre>'; 
		
		// loop through fields and save the data
		foreach ( $this->fields as $field ) {
			if( $field['type'] == 'section' ) {
				$sanitizer = null;
				continue;
			}
			if( in_array( $field['type'], array( 'tax_select', 'tax_checkboxes' ) ) ) {
				// save taxonomies
				if ( isset( $_POST[$field['id']] ) ) {
					$term = $_POST[$field['id']];
					wp_set_object_terms( $post_id, $term, $field['id'] );
				}
			}
			/*else {
				// save the rest
				$new = false;
				$old = get_post_meta( $post_id, $field['id'], true );
				if ( isset( $_POST[$field['id']] ) )
					$new = $_POST[$field['id']];
				if ( isset( $new ) && '' == $new && $old ) {
					delete_post_meta( $post_id, $field['id'], $old );
				} elseif ( isset( $new ) && $new != $old ) {
					$sanitizer = isset( $field['sanitizer'] ) ? $field['sanitizer'] : 'sanitize_text_field';
					if ( is_array( $new ) )
						$new = schema_wp_meta_box_array_map_r( 'schema_wp_meta_box_sanitize', $new, $sanitizer );
					else
						$new = schema_wp_meta_box_sanitize( $new, $sanitizer );
					update_post_meta( $post_id, $field['id'], $new );
				}
			}*/
			
			else {
				// save the rest
				$new = false;
				$old = get_post_meta( $post_id, $field['id'], true );
				if ( isset( $_POST[$field['id']] ) )
					$new = $_POST[$field['id']];
				if($field['type'] == 'repeatable' || $field['type'] == 'repeatable_row' ) {
					if (is_array($new)) $new = array_values($new);
				}
				if ( isset( $new ) && '' == $new && $old ) {
					delete_post_meta( $post_id, $field['id'], $old );
				} elseif ( isset( $new ) && $new != $old ) {
					$sanitizer = isset( $field['sanitizer'] ) ? $field['sanitizer'] : 'sanitize_text_field';
					if ( is_array( $new ) ) 
						$new = schema_wp_meta_box_array_map_r( 'schema_wp_meta_box_sanitize', $new, $sanitizer );
					else
						$new = schema_wp_meta_box_sanitize( $new, $sanitizer );
					
					if( $field['type'] == 'date') {
						$new = strtotime($new);
					}
					if( $field['type'] == 'sliderrating') {
						// adjust rating before saving values
						if (function_exists('schema_wp_review_adjust_rating')) {
							$rating_type	= schema_wp_review_get_rating_type( $post_id );
							$scale 			= schema_wp_review_get_rating_scale( $rating_type );
							$new        	= schema_wp_review_adjust_rating( $new, $scale, true );
						}
					}

					update_post_meta( $post_id, $field['id'], $new );
				}
			}
		} // end foreach
	}
	
}
