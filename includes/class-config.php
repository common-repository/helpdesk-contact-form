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
 * HelpDesk plugin static configuration.
 */
final class Config
{
	/**
	 * Configuration.
	 *
	 * @var array
	 */
	private static $config = null;

	/**
	 * Get configuration value.
	 *
	 * @param string $key Configuration key.
	 *
	 * @return string|null Configuration value.
	 */
	public static function get($key)
	{
		if (null === self::$config) {
			$config_file_path = __DIR__ . '/config.json';
			if (file_exists($config_file_path)) {
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- File is read from plugin directory, not a remote source.
				self::$config = json_decode(file_get_contents($config_file_path), true);
			} else {
				self::$config = array(
					'licence_id_option_name' => 'licenseID',
					'contact_form_id_option_name' => 'contactFormID',
					'client_id' => '960c42d226c9eb61e7ac83aaf54c1df6',
					'helpdesk_app_url' => 'https://app.helpdesk.com',
					'helpdesk_api_url' => 'https://api.helpdesk.com',
					'livechat_accounts_url' => 'https://accounts.helpdesk.com/signup',
					'forms_url' => 'https://forms.helpdesk.com',
				);
			}
		}

		return self::$config[$key] ?? null;
	}
}
?>