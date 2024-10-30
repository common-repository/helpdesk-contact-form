<?php
/**
 * HelpDesk product integration with WordPress.
 *
 * @link https://openwidget.com
 * @package HelpDesk
 * @since 1.0.0
 */

namespace HelpDeskContactForm;

require_once plugin_dir_path( __FILE__ ) . 'includes/class-config.php';

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( Config::get( 'licence_id_option_name' ) );
delete_option( Config::get( 'contact_form_id_option_name' ) );
