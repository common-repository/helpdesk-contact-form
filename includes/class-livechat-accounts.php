<?php
/**
 * HelpDesk product integration with WordPress.
 *
 * @package HelpDesk
 * @subpackage HelpDesk/includes
 */

namespace HelpDeskContactForm;

if (!defined('WPINC')) {
	exit;
}

/**
 * LiveChat Accounts service integration.
 */
final class LiveChat_Accounts
{
	/**
	 * Get sign in URL for LiveChat accounts service.
	 *
	 * @param string $redirect_uri Redirect URI.
	 */
	public static function get_sign_in_url($redirect_uri)
	{
		$url = Config::get('livechat_accounts_url');
		$url = add_query_arg('response_type', 'code', $url);
		$url = add_query_arg('client_id', Config::get('client_id'), $url);
		$url = add_query_arg('redirect_uri', $redirect_uri, $url);
		$url = add_query_arg('utm_source', 'wordpress', $url);
		$url = add_query_arg('utm_medium', 'integration', $url);

		return $url;
	}
}
