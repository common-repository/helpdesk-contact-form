<?php
/**
 * HelpDesk product integration with WordPress.
 *
 * @package HelpDesk
 * @subpackage HelpDesk/includes
 */

namespace HelpDeskContactForm;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

/**
 * HelpDesk API service integration.
 */
final class HelpDesk_API {
	/**
	 * Get endpoint for HelpDesk API to install WordPress plugin.
	 */
	public static function get_wordpress_install_endpoint() {
		$url = Config::get( 'helpdesk_api_url' ) . '/v1/integrations/wordpress/install';

		function generateUriEncodedJson($location) {
			// Function to generate a random string for nonce
			function generateNonce($length = 16) {
				return bin2hex(random_bytes($length / 2));
			}
		
			// Generate the nonce
			$nonce = generateNonce();
		
			// Create an associative array with the nonce and location
			$data = array(
				"nonce" => $nonce,
				"location" => $location
			);
		
			// Encode the array to JSON
			$json = wp_json_encode($data);
		
			// URI encode the JSON
			$uriEncodedJson = urlencode($json);
		
			// Return the URI encoded JSON
			return $json;
		}
		
		$state = generateUriEncodedJson(admin_url( 'admin.php?page=helpdesk' ));

		
			$url .= '&state=' . urlencode($state);
	
		//$url = add_query_arg( 'state', $state , $url );
        

		return $url;
	}
}
