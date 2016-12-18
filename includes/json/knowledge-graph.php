<?php
/**
 * Knowledge Graph
 *
 * @since 1.0
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_head', 'schema_wp_output_knowledge_graph');
/**
 * The main function responsible for output schema json-ld 
 *
 * @since 1.0
 * @return schema json-ld final output
 */
function schema_wp_output_knowledge_graph() {
	
	// Run only on front page
	if ( is_front_page() ) {
		
		$json = schema_wp_get_knowledge_graph_json();
		
		$knowledge_graph = '';
		
		if ($json) {
			$knowledge_graph .= "\n\n";
			$knowledge_graph .= '<!-- This site is optimized with the Schema plugin v'.SCHEMAWP_VERSION.' - http://schema.press -->';
			$knowledge_graph .= "\n";
			$knowledge_graph .= '<script type="application/ld+json">' . json_encode($json) . '</script>';
			$knowledge_graph .= "\n\n";
		}
		
		$knowledge_graph = apply_filters( 'schema_wp_output_knowledge_graph', $knowledge_graph );
		
		echo $knowledge_graph;
	}
}


/**
 * The main function responsible for putting shema array all together
 *
 * @param string $type for schema type (example: Organization)
 * @since 1.0
 * @return schema output
 */
function schema_wp_get_knowledge_graph_json() {
	
	//if ( ! isset($type) ) return;
	
	$organization_or_person = schema_wp_get_option( 'organization_or_person' );
	
	if ( empty($organization_or_person) ) return;
	
	
	switch ( $organization_or_person ) {
		case "organization":
		$type = 'Organization';
			break;
		case "person":
		$type = 'Person';
			break;
	}
	
	$schema = array();
	
	$name = schema_wp_get_option( 'name' );
	$url = esc_attr( stripslashes( schema_wp_get_option( 'url' ) ) );
	
	if ( empty($name) || empty($url) ) return;
	
	// Set logo only when type = Organization
	if ( $type == 'Organization' ) {
		$logo = esc_attr( stripslashes( schema_wp_get_option( 'logo' ) ) );
	} else {
		$logo = '';
	}
	
	$schema['@context'] = "http://schema.org";
	$schema['@type'] = $type;
	$schema['@id'] = '#' . $organization_or_person; // @todo this needs to be dynamic so we can add #person as well!
	
	
	if ( !empty($name) ) $schema['name'] = $name;
	if ( !empty($url) ) $schema['url'] = $url;
	if ( !empty($logo) ) $schema['logo'] = $logo;
	
	// Get corporate contacts types array
	$corporate_contacts_types = schema_wp_get_corporate_contacts_types_array();
	// Add contact
	if ( ! empty($corporate_contacts_types) ) {
		$schema["contactPoint"] = $corporate_contacts_types;
	}
	
	// Get social links array
	$social = schema_wp_get_social_array();
	// Add sameAs
	if ( ! empty($social) ) {
		$schema["sameAs"] = $social;
	}
	
	return apply_filters( 'schema_wp_knowledge_graph_json', $schema );
}


/**
 * Get Get corporate contacts types array
 *
 * @since 1.0
 * @return array
 */
function schema_wp_get_corporate_contacts_types_array() {
	
	$contact_type = array();
	
	$corporate_contacts_telephone		= schema_wp_get_option( 'corporate_contacts_telephone' );
	$corporate_contacts_contact_type	= schema_wp_get_option( 'corporate_contacts_contact_type' );
	
	// Remove dashes and replace it with a space
	$corporate_contacts_telephone = str_replace("_", " ", $corporate_contacts_telephone);
	$corporate_contacts_contact_type = str_replace("_", " ", $corporate_contacts_contact_type);
	
	$corporate_contacts_types = array(
		'@type'			=> 'ContactPoint',	// default required value
		'telephone'		=> $corporate_contacts_telephone,
		'contactType'	=> $corporate_contacts_contact_type
		);
	
	// If phone is provided
	if ( $corporate_contacts_telephone != '' )  return $corporate_contacts_types;
	
	// Return an empty array
	return array();
}


/**
 * Get social links array
 *
 * @since 1.0
 * @return array
 */
function schema_wp_get_social_array() {
	
	$social = array();
	
	$google 	= esc_attr( stripslashes( schema_wp_get_option( 'google' ) ) );
	$facebook 	= esc_attr( stripslashes( schema_wp_get_option( 'facebook') ) );
	$twitter 	= esc_attr( stripslashes( schema_wp_get_option( 'twitter' ) ) );
	$instagram 	= esc_attr( stripslashes( schema_wp_get_option( 'instagram' ) ) );
	$youtube 	= esc_attr( stripslashes( schema_wp_get_option( 'youtube' ) ) );
	$linkedin 	= esc_attr( stripslashes( schema_wp_get_option( 'linkedin' ) ) );
	$myspace 	= esc_attr( stripslashes( schema_wp_get_option( 'myspace' ) ) );
	$pinterest 	= esc_attr( stripslashes( schema_wp_get_option( 'pinterest' ) ) );
	$soundcloud = esc_attr( stripslashes( schema_wp_get_option( 'soundcloud' ) ) );
	$tumblr 	= esc_attr( stripslashes( schema_wp_get_option( 'tumblr' ) ) );
	
	$social_links = array( $google, $facebook, $twitter, $instagram, $youtube, $linkedin, $myspace, $pinterest, $soundcloud, $tumblr);
	
	// Remove empty fields
	foreach( $social_links as $profile ) {
		if ( $profile != '' ) $social[] = $profile;
	}
	
	return $social;
}
