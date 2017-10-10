<?php
/**
 * Genesis Theme 
 *
 *
 * Remove Geiesis schema output
 *
 * plugin url: https://www.studiopress.com/
 * @since 1.5.4
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

//add_filter( 'genesis_attr_head',					'schema_wp_genesis_attributes_removal_function', 20 );

//add_filter( 'genesis_attr_site-header',				'schema_wp_genesis_attributes_removal_function', 20 );
//add_filter( 'genesis_attr_site-title',				'schema_wp_genesis_attributes_removal_function', 20 );
//add_filter( 'genesis_attr_site-description',		'schema_wp_genesis_attributes_removal_function', 20 );

add_filter( 'genesis_attr_search-form',				'schema_wp_genesis_attributes_removal_function', 20 );

//add_filter( 'genesis_attr_nav-primary',				'schema_wp_genesis_attributes_removal_function', 20 );
//add_filter( 'genesis_attr_nav-secondary',			'schema_wp_genesis_attributes_removal_function', 20 );

add_filter( 'genesis_attr_body',					'schema_wp_genesis_attributes_removal_function', 20 );

add_filter( 'genesis_attr_content',					'schema_wp_genesis_attributes_removal_function', 20 );

add_filter( 'genesis_attr_entry',					'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_entry-author',			'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_entry-author-name',		'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_entry-author-link',		'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_entry-image',				'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_entry-image-widget',		'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_entry-image-grid-loop',	'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_entry-time',				'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_entry-title',				'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_entry-content',			'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_entry-comments',			'schema_wp_genesis_attributes_removal_function', 20 );

add_filter( 'genesis_attr_author',					'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_author-box',				'schema_wp_genesis_attributes_removal_function', 20 );

add_filter( 'genesis_attr_comment',					'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_comment-content',			'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_comment-author',			'schema_wp_genesis_attributes_removal_function', 20 );
add_filter( 'genesis_attr_comment-time-link',		'schema_wp_genesis_attributes_removal_function', 20 );

//add_filter( 'genesis_attr_sidebar-primary',			'schema_wp_genesis_attributes_removal_function', 20 );
//add_filter( 'genesis_attr_sidebar-secondary',		'schema_wp_genesis_attributes_removal_function', 20 );

//add_filter( 'genesis_attr_site-footer',				'schema_wp_genesis_attributes_removal_function', 20 );

/*
* Remove Genesis schema markups
*
* @since 1.5.4
*/
function schema_wp_genesis_attributes_removal_function( $attributes ) {
 	
	$attributes['role']			= '';
	$attributes['itemprop']		= '';
	$attributes['itemscope']	= '';
    $attributes['itemtype'] 	= ''; 
	
  return $attributes;
}

add_action( 'init', 'schema_wp_wprs_remove_genesis_search_form' );
/*
* Remove Genesis search form filter
*
* @since 1.5.4
*/
function schema_wp_wprs_remove_genesis_search_form() {
	
	remove_filter( 'get_search_form', 'genesis_search_form' );	
}

/*
* Add Genesis search form without markup
*
* @since 1.5.4
*/
add_filter( 'get_search_form', 'wp_schema_genesis_search_form' );
/**
 * Replace the default search form with a Genesis-specific form.
 *
 * The exact output depends on whether the child theme supports HTML5 or not.
 *
 * Applies the `genesis_search_text`, `genesis_search_button_text`, `genesis_search_form_label` and
 * `genesis_search_form` filters.
 *
 * @since 0.2.0
 *
 * @uses genesis_html5() Check for HTML5 support.
 *
 * @return string HTML markup.
 */
function wp_schema_genesis_search_form( $form) {
	
	// Added extra checks for older versions of Genesis to prevent errors
	// @since 1.6.2
	if ( ! function_exists('genesis_html5') ) return $form;
	if ( ! function_exists('genesis_a11y') ) return $form;
	if ( ! function_exists('genesis_attr') ) return $form;
	
	
	$search_text = get_search_query() ? apply_filters( 'the_search_query', get_search_query() ) : apply_filters( 'genesis_search_text', __( 'Search this website', 'genesis' ) . ' &#x02026;' );

	$button_text = apply_filters( 'genesis_search_button_text', esc_attr__( 'Search', 'genesis' ) );

	$onfocus = "if ('" . esc_js( $search_text ) . "' === this.value) {this.value = '';}";
	$onblur  = "if ('' === this.value) {this.value = '" . esc_js( $search_text ) . "';}";

	//* Empty label, by default. Filterable.
	$label = apply_filters( 'genesis_search_form_label', '' );

	$value_or_placeholder = ( get_search_query() == '' ) ? 'placeholder' : 'value';
	
	if ( genesis_html5() ) {

		$form  = sprintf( '<form %s>', genesis_attr( 'search-form' ) );

		if ( genesis_a11y( 'search-form' ) ) {

			if ( '' == $label )  {
				$label = apply_filters( 'genesis_search_text', __( 'Search this website', 'genesis' ) );
			}

			$form_id = uniqid( 'searchform-' );

			$form .= sprintf(
				'<label class="search-form-label screen-reader-text" for="%s">%s</label><input type="search" name="s" id="%s" %s="%s" /><input type="submit" value="%s" /></form>',
				//home_url( '/?s={s}' ),
				esc_attr( $form_id ),
				esc_html( $label ),
				esc_attr( $form_id ),
				$value_or_placeholder,
				esc_attr( $search_text ),
				esc_attr( $button_text )
			);

		} else {

			$form .= sprintf(
				'%s<input type="search" name="s" %s="%s" /><input type="submit" value="%s"  /></form>',
				esc_html( $label ),
				//home_url( '/?s={s}' ),
				$value_or_placeholder,
				esc_attr( $search_text ),
				esc_attr( $button_text )
			);
		}

	} else {

		$form = sprintf(
			'<form method="get" class="searchform search-form" action="%s" role="search" >%s<input type="text" value="%s" name="s" class="s search-input" onfocus="%s" onblur="%s" /><input type="submit" class="searchsubmit search-submit" value="%s" /></form>',
			home_url( '/' ),
			esc_html( $label ),
			esc_attr( $search_text ),
			esc_attr( $onfocus ),
			esc_attr( $onblur ),
			esc_attr( $button_text )
		);

	}

	return apply_filters( 'genesis_search_form', $form, $search_text, $button_text, $label );
}

add_action( 'init', 'schema_wp_remove_genesis_breadcrumbs_attr_markup' );
/*
* Remove Genesis Breadcrumbs attributes 
*
* @since 1.6.9.4
*/
function schema_wp_remove_genesis_breadcrumbs_attr_markup() {
    
	$breadcrumbs_enable = schema_wp_get_option( 'breadcrumbs_enable' );
	
	if ( $breadcrumbs_enable ) {
		
		add_filter( 'genesis_attr_breadcrumb',				'schema_wp_genesis_attributes_removal_function', 20 );
		add_filter( 'genesis_attr_breadcrumb-link-wrap',	'schema_wp_genesis_attributes_removal_function', 20 );

     }
}

add_action( 'genesis_breadcrumb_link', 'schema_wp_remove_genesis_breadcrumbs_link_markup' );
/*
* Remove Genesis Breadcrumbs itemprop markup
*
* @since 1.6.9.4
*/
function schema_wp_remove_genesis_breadcrumbs_link_markup( $output ) {
	
	$breadcrumbs_enable = schema_wp_get_option( 'breadcrumbs_enable' );
	
	if ( $breadcrumbs_enable ) {
		
		$output = str_replace('itemprop="name"', '', $output);
		$output = str_replace('itemprop="item"', '', $output);
	
	}
	
    return $output;
}
