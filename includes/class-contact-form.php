<?php
/**
 * HelpDesk product integration with WordPress.
 *
 * @package HelpDesk
 * @subpackage HelpDesk/includes
 */

namespace HelpDeskContactForm;

if (!defined("WPINC")) {
    exit();
}

require_once plugin_dir_path(__FILE__) . "class-config.php";

/**
 * HelpDesk Contact Form Shortcode ini
 */
final class Contact_Form
{
    /**
     * Contact_Form constructor.
     *
     * @param string $plugin_name Plugin name.
     */
    public function __construct($plugin_name)
    {
        add_shortcode("helpdesk_contact_form", [$this, "render"]);
    }

    public function render()
    {
        $helpdesk_id = get_option(Config::get("licence_id_option_name"));
        $contact_form_id = get_option(
            Config::get("contact_form_id_option_name")
        );
        $forms_url = Config::get("forms_url");
        $form_url =
            $forms_url .
            "?licenseID=" .
            esc_attr($helpdesk_id) .
            "&contactFormID=" .
            esc_attr($contact_form_id);

        // Contact form iframe
        $iframe =
            '<iframe sandbox="allow-scripts allow-popups allow-forms allow-same-origin" width="100%" height="700px" style="border: 0; overflow: hidden; overflow-x: auto; margin:auto; display:flex;" src="' .
            $form_url .
            '"> Your browser does not allow embedded content.</iframe>';

        return apply_filters("helpdesk_filter_contact_form", $iframe);
    }
}
