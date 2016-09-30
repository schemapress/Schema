<?php
/**
 * Admin Functions
 *
 * @package     Schema
 * @subpackage  Admin Functions/Formatting
 * @copyright   Copyright (c) 2016, Hesham Zebida
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


add_action( 'schema_wp_do_after_settings_updated', 'schema_wp_after_update_settings' );
/**
 * Delete Schema KSON-LD cached data in post meta  on plugin settings update
 *
 *
 * @since  1.6.1
 */
function schema_wp_after_update_settings() {
    
	// Delete cached data in post meta
	schema_wp_json_delete_cache();
}


/**
 * Delete Schema KSON-LD cached data in post meta 
 *
 *
 * @since  1.6.1
 */
function schema_wp_json_delete_cache() {
    
	// Delete cached data in post meta
	delete_post_meta_by_key( '_schema_json' );
	delete_post_meta_by_key( '_schema_json_timestamp' );
}


/**
 * Sanitizes a string key for Schema Settings
 *
 * Keys are used as internal identifiers. Alphanumeric characters, dashes, underscores, stops, colons and slashes are allowed
 *
 * @since  1.5.9.3
 * @param  string $key String key
 * @return string Sanitized key
 */
function schema_wp_sanitize_key( $key ) {
	$raw_key = $key;
	$key = preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );

	/**
	 * Filter a sanitized key string.
	 *
	 * @since 2.5.8
	 * @param string $key     Sanitized key.
	 * @param string $raw_key The key prior to sanitization.
	 */
	return apply_filters( 'schema_wp_sanitize_key', $key, $raw_key );
}


/**
 * Convert an object to an associative array.
 *
 * Can handle multidimensional arrays
 *
 * @since 1.0
 *
 * @param unknown $data
 * @return array
 */
function schema_wp_object_to_array( $data ) {
	if ( is_array( $data ) || is_object( $data ) ) {
		$result = array();
		foreach ( $data as $key => $value ) {
			$result[ $key ] = schema_wp_object_to_array( $value );
		}
		return $result;
	}
	return $data;
}


/**
 * Flatten an array
 * 
 * @since 1.4.7
 * @return flat array
 */
function schema_wp_array_flatten($array) {

	$return = array();
	foreach ($array as $key => $value) {
	if (is_array($value)){ $return = array_merge($return, array_flatten($value));}
		else {$return[$key] = $value;}
	}
	
	return $return;
}


/**
* Retrieve a post given its title.
*
* @link http://wordpress.stackexchange.com/questions/11292/how-do-i-get-a-post-page-or-cpt-id-from-a-title-or-slug/11296#11296
*
* @since 1.6
*
* @uses $wpdb
*
* @param string $post_title Page title
* @param string $post_type post type ('post','page','any custom type')
* @param string $output Optional. Output type. OBJECT, ARRAY_N, or ARRAY_A.
* @return mixed
*/
function schema_wp_get_post_by_title($page_title, $post_type = 'post' , $output = OBJECT) {
    global $wpdb;
        $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type= %s", $page_title, $post_type));
        if ( $post )
            return get_post($post, $output);

    return null;
}


/**
 * Recursive array search
 *
 * #link http://php.net/manual/en/function.array-search.php
 *
 * @since 1.6
 * @return Returns the key for needle if it is found in the array, FALSE otherwise. 
 */
function schema_wp_recursive_array_search( $needle, $haystack ) {
    foreach($haystack as $key=>$value) {
        $current_key=$key;
        if($needle===$value OR (is_array($value) && schema_wp_recursive_array_search($needle,$value) !== false)) {
            return $current_key;
        }
    }
    return false;
}


/**
 * Get Currencies
 *
 * @since 1.0
 * @return array $currencies A list of the available currencies
 */
function schema_wp_get_currencies() {

	$currencies = array(
		'USD' => __( 'US Dollars', 'schema-wp' ),
		'EUR' => __( 'Euros', 'schema-wp' ),
		'AUD' => __( 'Australian Dollars', 'schema-wp' ),
		'BDT' => __( 'Bangladeshi Taka', 'schema-wp' ),
		'BRL' => __( 'Brazilian Real', 'schema-wp' ),
		'BGN' => __( 'Bulgarian Lev', 'schema-wp' ),
		'CAD' => __( 'Canadian Dollars', 'schema-wp' ),
		'CLP' => __( 'Chilean Peso', 'schema-wp' ),
		'CNY' => __( 'Chinese Yuan', 'schema-wp' ),
		'COP' => __( 'Colombian Peso', 'schema-wp' ),
		'HRK' => __( 'Croatia Kuna', 'schema-wp' ),
		'CZK' => __( 'Czech Koruna', 'schema-wp' ),
		'DKK' => __( 'Danish Krone', 'schema-wp' ),
		'DOP' => __( 'Dominican Peso', 'schema-wp' ),
		'EGP' => __( 'Egyptian Pound', 'schema-wp' ),
		'HKD' => __( 'Hong Kong Dollar', 'schema-wp' ),
		'HUF' => __( 'Hungarian Forint', 'schema-wp' ),
		'ISK' => __( 'Icelandic Krona', 'schema-wp' ),
		'IDR' => __( 'Indonesia Rupiah', 'schema-wp' ),
		'INR' => __( 'Indian Rupee', 'schema-wp' ),
		'ILS' => __( 'Israeli Shekel', 'schema-wp' ),
		'JPY' => __( 'Japanese Yen', 'schema-wp' ),
		'KIP' => __( 'Lao Kip', 'schema-wp' ),
		'MYR' => __( 'Malaysian Ringgits', 'schema-wp' ),
		'MXN' => __( 'Mexican Peso', 'schema-wp' ),
		'NPR' => __( 'Nepali Rupee', 'schema-wp' ),
		'NGN' => __( 'Nigerian Naira', 'schema-wp' ),
		'NOK' => __( 'Norwegian Krone', 'schema-wp' ),
		'NZD' => __( 'New Zealand Dollar', 'schema-wp' ),
		'PYG' => __( 'Paraguayan Guaraní', 'schema-wp' ),
		'PHP' => __( 'Philippine Pesos', 'schema-wp' ),
		'PLN' => __( 'Polish Zloty', 'schema-wp' ),
		'GBP' => __( 'Pounds Sterling', 'schema-wp' ),
		'RON' => __( 'Romanian Leu', 'schema-wp' ),
		'RUB' => __( 'Russian Ruble', 'schema-wp' ),
		'SGD' => __( 'Singapore Dollar', 'schema-wp' ),
		'ZAR' => __( 'South African Rand', 'schema-wp' ),
		'KRW' => __( 'South Korean Won', 'schema-wp' ),
		'SEK' => __( 'Swedish Krona', 'schema-wp' ),
		'CHF' => __( 'Swiss Franc', 'schema-wp' ),
		'TWD' => __( 'Taiwan New Dollars', 'schema-wp' ),
		'THB' => __( 'Thai Baht', 'schema-wp' ),
		'TRY' => __( 'Turkish Lira', 'schema-wp' ),
		'AED' => __( 'United Arab Emirates Dirham', 'schema-wp' ),
		'VND' => __( 'Vietnamese Dong', 'schema-wp' ),
	);

	return apply_filters( 'schema_wp_currencies', $currencies );
}


/**
 * Get the store's set currency
 *
 * @since 1.0
 * @return string The currency code
 */
function schema_wp_get_currency() {
	$currency = schema_wp()->settings->get( 'currency', 'USD' );
	return apply_filters( 'schema_wp_currency', $currency );
}


/**
 * Get corporate contacts types
 *
 * @since 1.0
 * @return array $corporate_contacts_types A list of the available types
 */
function schema_wp_get_corporate_contacts_types() {

	$corporate_contacts_types = array(
		'customer_support'		=> __( 'Customer Support', 'schema-wp' ),
		'technical_support'		=> __( 'Technical Support', 'schema-wp' ),
		'billing_support'		=> __( 'Billing Support', 'schema-wp' ),
		'bill_payment'			=> __( 'Bill Payment', 'schema-wp' ),
		'sales'					=> __( 'Sales', 'schema-wp' ),
		'reservations'			=> __( 'Reservations', 'schema-wp' ),
		'credit_card_support'	=> __( 'Credit Card Support', 'schema-wp' ),
		'emergency'				=> __( 'Emergency', 'schema-wp' ),
		'baggage_tracking'		=> __( 'Baggage Tracking', 'schema-wp' ),
		'roadside_assistance'	=> __( 'Roadside Assistance', 'schema-wp' ),
		'package_tracking'		=> __( 'Package Tracking', 'schema-wp' ),
	);

	return apply_filters( 'schema_wp_corporate_contacts_types', $corporate_contacts_types );
}

