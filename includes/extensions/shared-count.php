<?php

/**
 *  Comment extention
 *
 *  Adds schema Comment for Article types
 *
 *  @since 1.5.3
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function mashsbGetShareObj( $url ) {
    global $mashsb_options;
    $mashengine = true;
    if( $mashengine ) {
        if( !class_exists( 'RollingCurlX' ) )
            require_once MASHSB_PLUGIN_DIR . 'includes/libraries/RolingCurlX.php';
        if( !class_exists( 'mashengine' ) )
            require_once(MASHSB_PLUGIN_DIR . 'includes/mashengine.php');
        mashdebug()->error( 'mashsbGetShareObj() url: ' . $url );
        $mashsbSharesObj = new mashengine( $url );
        return $mashsbSharesObj;
    }
    require_once(MASHSB_PLUGIN_DIR . 'includes/sharedcount.class.php');
    $apikey = isset( $mashsb_options['mashsharer_apikey'] ) ? $mashsb_options['mashsharer_apikey'] : '';
    $mashsbSharesObj = new mashsbSharedcount( $url, 10, $apikey );
    return $mashsbSharesObj;
}


/**
 * Sharecount functions
 * Get the share count from the service sharedcount.com
 *
 * @package     MASHSB
 * @subpackage  Functions/sharedcount
 * @copyright   Copyright (c) 2014, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0.9
 */

class schema_Sharedcount {
    private $url,$timeout;

    function __construct($url,$timeout=10, $apikey = '') {
        global $mashsb_options;
        $this->url = rawurlencode($url);
        $this->timeout= $timeout;
        $this->apikey = trim($apikey);
        }

function getFBTWCounts(){
        global $mashsb_options;
        
        isset($mashsb_options['facebook_count_mode']) ? $fb_mode = $mashsb_options['facebook_count_mode'] : $fb_mode = '';
        
        $sharecounts = $this->get_sharedcount();

        if(!$sharecounts){
            $this->sharecount  = new stdClass;
            $this->sharecount->total = 0;
			return $this->sharecount; 
        }
            $counts = array('shares'=>array(),'total'=>0);
            switch ($fb_mode){
                case $fb_mode === 'likes':
                $counts['shares']['fb'] = $sharecounts['Facebook']['like_count'];
                break;
            case $fb_mode === 'total':
                $counts['shares']['fb'] = $sharecounts['Facebook']['total_count']; 
                break;
            default:
                $counts['shares']['fb'] = $sharecounts['Facebook']['share_count']; 
            }
            $counts['shares']['tw'] = $sharecounts['Twitter'];

	foreach ($counts['shares'] as $mashsbcounts => $sharecount) $counts['total'] += (int)$sharecount;
        mashdebug()->error("sharedcount.com getFBTWCounts: " . $counts['total']);

        $totalArr = array ('total' => $counts['total']);
        $objMerged = (object)array_merge((array)$sharecounts, (array)$totalArr);
	return $objMerged;

}
/* Only used when mashshare-networks is enabled */
function getAllCounts(){
        global $mashsb_options;
        
        isset($mashsb_options['facebook_count_mode']) ? $fb_mode = $mashsb_options['facebook_count_mode'] : $fb_mode = '';
        
        $sharecounts = $this->get_sharedcount();
        if(!$sharecounts){
	    $this->sharecount  = new stdClass;
            $this->sharecount->total = 0;
	    return $this->sharecount; 
        }
        
        $counts = array('shares'=>array(),'total'=>0);
            $counts = array('shares'=>array(),'total'=>0);
            switch ($fb_mode){
                case $fb_mode === 'likes':
                $counts['shares']['fb'] = $sharecounts['Facebook']['like_count'];
                break;
            case $fb_mode === 'total':
                $counts['shares']['fb'] = $sharecounts['Facebook']['total_count']; 
                break;
            default:
                $counts['shares']['fb'] = $sharecounts['Facebook']['share_count']; 
            }
            isset($sharecounts['Twitter']) ? $counts['shares']['tw'] = $sharecounts['Twitter'] : $sharecounts['Twitter'] = 0;
            isset($sharecounts['GooglePlusOne']) ? $counts['shares']['gp'] = $sharecounts['GooglePlusOne'] : $counts['shares']['gp'] = 0 ;
            isset($sharecounts['LinkedIn']) ? $counts['shares']['li'] = $sharecounts['LinkedIn'] : $counts['shares']['li'] = 0;
            isset($sharecounts['StumbleUpon']) ? $counts['shares']['st'] = $sharecounts['StumbleUpon'] : $counts['shares']['st'] = 0 ;
            isset($sharecounts['Pinterest']) ? $counts['shares']['pin'] = $sharecounts['Pinterest'] : $counts['shares']['pin'] = 0;
        
        $total = 0;
	foreach ($counts['shares'] as $totalcount) $total += (int)$totalcount;
        $totalArr = array ('total' => $total);
        $objMerged = (object)array_merge((array)$sharecounts, (array)$totalArr);
        mashdebug()->info("sharedcount.com getAllCounts: " . $counts['total']);
        return $objMerged;
}

function update_sharedcount_domain($domain = false){ 
    global $mashsb_options;
	if(!$domain){
		try{
			$domain_obj =  $this->_curl('http://'. $mashsb_options["mashsharer_sharecount_domain"] . "/account?apikey=" . $this->apikey);
			$domain = $domain_obj["domain"];
		}
		catch (Exception $e){
	        mashdebug()->error("error: " . $domain_obj);
			return 0;
		}
	}
	$mashsb_options["mashsharer_sharecount_domain"] = $domain;
	update_option( 'mashsb_settings', $mashsb_options);
	return 1;
}

private function _curl($url){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,5); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 5); //timeout in seconds
	$curl_results = curl_exec ($curl);
	curl_close ($curl);
	return json_decode($curl_results, true);
}

function get_sharedcount()  {
    mashdebug()->info("Share URL: " . $this->url);
    global $mashsb_options;
    if( empty($this->apikey) ){
        return 0; //quit early if there's no API key.
    }
    //$apikey = trim($mashsb_options['mashsharer_apikey']);
    $domain = isset($mashsb_options['mashsharer_sharecount_domain']) ? trim($mashsb_options['mashsharer_sharecount_domain']) : '';	
	if(!isset($domain) || empty($domain)){
		$domain = "free.sharedcount.com";
		$this->update_sharedcount_domain($domain);
	}

	try {
        $counts = $this->_curl('http://'.$domain . "/?url=" . $this->url . "&apikey=" . $this->apikey);
        //mashdebug()->error('check ' . $domain . $this->apikey . $this->url);
        if(isset($counts["Error"]) && isset($counts['Domain']) && $counts["Type"] === "domain_apikey_mismatch"){
	         $this->update_sharedcount_domain($counts['Domain']);
             return 0;
        }
        else if(isset($counts["Error"]) && isset($counts['Type']) && $counts['Type'] === 'invalid_api_key'  ){
             $this->update_sharedcount_domain();
             return 0;
        }

        mashdebug()->error("Facebook total count: " . $counts['Facebook']['total_count']);
        MASHSB()->logger->info("URL: " . urldecode($this->url) . " API Key:" . $this->apikey . " sharedcount.com FB total_count: " . $counts['Facebook']['total_count'] . " FB share_count:" . $counts['Facebook']['share_count'] . " TW: " . $counts['Twitter'] . " G+:" . $counts['GooglePlusOne'] . " Linkedin:" . $counts['LinkedIn'] . " Stumble: " . $counts['StumbleUpon'] . " Pinterest: " . $counts['Pinterest']);

        return $counts;
	} catch (Exception $e){
                mashdebug()->error("error: " . $counts);
                MASHSB()->logger->info('ERROR: Curl()' . $counts);
		return 0;
	}
        mashdebug()->error("error2: " . $counts);
        MASHSB()->logger->info('ERROR 2: Curl()' . $counts);
	return 0;
}

}
