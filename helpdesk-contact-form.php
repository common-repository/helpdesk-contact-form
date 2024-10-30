<?php
/**
 * HelpDesk product integration with WordPress.
 *
 * @package HelpDesk
 *
 * Plugin Name:        HelpDesk Contact Form
 * Plugin URI:         https://www.helpdesk.com/integrations/wordpress
 * Description:        Make communication effortless with the WordPress contact form plugin provided by HelpDesk. Create your contact form without any coding and manage all website messages in one spot.
 * Version:            1.1.2
 * Requires at least:  4.6
 * Requires PHP:       6.1
 * Author:             text.com
 * Author URI:         https://helpdesk.com
 * Text Domain:        helpdesk-contact-form
 * Domain Path:        /languages
 * License:            GPLv3 or later
 */

namespace HelpDeskContactForm;

if (!defined('WPINC')) {
	exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/class-plugin.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-contact-form.php';

new Plugin(plugin_basename(__FILE__));
new Admin_Page(plugin_basename(__FILE__));
new Contact_Form(plugin_basename(__FILE__));

