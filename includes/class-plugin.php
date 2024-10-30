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
 * HelpDesk Plugin container class.
 */
final class Plugin {
	/**
	 * Plugin name.
	 *
	 *  @var string $plugin_name Plugin name.
	 */
	public $plugin_name;
	/**
	 * Plugin constructor.
	 *
	 * @param string $plugin_name Plugin name.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;

		add_action( 'activated_plugin', array( $this, 'redirect_to_settings' ) );
		add_filter( 'plugin_action_links_' . $plugin_name, array( $this, 'add_action_links' ) );
	}

	/**
	 * Redirect to settings page after activation.
	 *
	 * @param string $plugin Plugin file.
	 */
	public function redirect_to_settings( $plugin ) {
		if ( $this->plugin_name !== $plugin ) {
			return;
		}

		$licence_id = get_option( Config::get( 'licence_id_option_name' ) );
		if ( $licence_id ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=helpdesk' ) );
		exit;
	}

	/**
	 * Add action links to plugin page.
	 *
	 * @param array $actions Plugin actions.
	 *
	 * @return array
	 */
	public function add_action_links( $actions ) {
		$plugin_links = array(
			'<a href="' . esc_attr( admin_url( 'admin.php?page=helpdesk' ) ) . '">' . __( 'Settings', 'helpdesk-contact-form' ) . '</a>',
		);

		return array_merge( $actions, $plugin_links );
	}
}
