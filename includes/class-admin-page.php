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
require_once plugin_dir_path(__FILE__) . "class-livechat-accounts.php";
require_once plugin_dir_path(__FILE__) . "class-helpdesk-api.php";

/**
 * HelpDesk admin page.
 */
final class Admin_Page
{
	/**
	 * Plugin name.
	 *
	 *  @var string $plugin_name Plugin name.
	 */
	public $plugin_name;
	/**
	 * Admin_Page constructor.
	 *
	 * @param string $plugin_name Plugin name.
	 */
	public function __construct($plugin_name)
	{
		$this->plugin_name = $plugin_name;

		add_action("admin_menu", [$this, "add_menu_item"]);
		add_action("admin_init", [$this, "handle_query_args"]);
		add_action("admin_enqueue_scripts", [$this, "enqueue_scripts"]);
		add_action("admin_init", [$this, "helpdesk_create_contact_page"]);
	}

	/**
	 * Add menu item.
	 */
	public function add_menu_item()
	{
		add_menu_page(
			"HelpDesk",
			"HelpDesk",
			"administrator",
			"helpdesk",
			[$this, "render"],
			plugin_dir_url(__FILE__) . "../assets/img/icon.svg"
		);
	}

	/**
	 * Handle query args.
	 */
	public function handle_query_args()
	{
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if (!isset($_GET["licenseID"])) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$licenseID = sanitize_text_field($_GET["licenseID"]);
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$contactFormID = sanitize_text_field($_GET["contactFormID"]);

		// Nonce verification not required there - no sensitive data is being processed and non-admins can't access the page
		if (!$licenseID) {
			return;
		}

		if (!current_user_can("administrator")) {
			wp_safe_redirect(admin_url());
			return;
		}

		update_option(Config::get("licence_id_option_name"), $licenseID);
		update_option(
			Config::get("contact_form_id_option_name"),
			$contactFormID
		);

		wp_safe_redirect(admin_url("admin.php?page=helpdesk"));
	}

	/**
	 * Enqueue scripts.
	 *
	 * @param string $hook_suffix Hook name.
	 */
	public function enqueue_scripts($hook_suffix)
	{
		wp_enqueue_style(
			"helpdesk-admin-menu",
			plugin_dir_url(__FILE__) . "../assets/css/admin-menu.css",
			[],
			"1.0.0",
			"all"
		);

		if ("toplevel_page_helpdesk" !== $hook_suffix) {
			return;
		}

		wp_enqueue_style(
			"helpdesk-admin-page",
			plugin_dir_url(__FILE__) . "../assets/css/admin-page.css",
			[],
			"1.0.0",
			"all"
		);

		wp_enqueue_script(
			"helpdesk-admin-page",
			plugin_dir_url(__FILE__) . "../assets/js/admin-page.js",
			[],
			"1.0.0",
			true
		);
	}

	/**
	 * Returns allowed HTML attrs for the contact form iframe.
	 * Only the ones provided in the code are allowed.
	 *
	 * @return array Allowed HTML.
	 */
	function helpdesk_allowed_html_in_iframe()
	{
		return [
			"iframe" => [
				"src" => true,
				"width" => true,
				"height" => true,
				"style" => true,
				"border" => true,
			],
		];
	}

	/**
	 * Render admin page.
	 */
	public function render()
	{
		$licenseID = get_option(Config::get("licence_id_option_name"));
		$helpdesk_app_link = Config::get("helpdesk_app_url");
		$wordpress_install_endpoint = HelpDesk_API::get_wordpress_install_endpoint();
		$connect_link = LiveChat_Accounts::get_sign_in_url(
			$wordpress_install_endpoint
		);
		$forms_url = Config::get("forms_url");

		$contact_form_id = get_option(
			Config::get("contact_form_id_option_name")
		);
		$form_url =
			$forms_url .
			"?licenseID=" .
			esc_attr($licenseID) .
			"&contactFormID=" .
			esc_attr($contact_form_id);

		$plug_url = plugin_dir_url(__FILE__) . "../assets/img/plug.svg";
		$check_url = plugin_dir_url(__FILE__) . "../assets/img/check.svg";
		$logo_url = plugin_dir_url(__FILE__) . "../assets/img/logo-full.svg";
		$edit_icon_url =
			plugin_dir_url(__FILE__) . "../assets/img/edit-icon.svg";
		$refresh_icon_url =
			plugin_dir_url(__FILE__) . "../assets/img/refresh.svg";

		$status_icon_alt_text = __("Status icon", "helpdesk-contact-form");
		$refresh_icon_alt_text = __("Refresh icon", "helpdesk-contact-form");
		$heading = $licenseID
			? __("HelpDesk is connected", "helpdesk-contact-form")
			: __("Connect HelpDesk to your website", "helpdesk-contact-form");
		$description = $licenseID
			? __(
				"Customize your HelpDesk and boost your customer engagement.",
				"helpdesk-contact-form"
			)
			: __(
				"Log in or create an account to connect HelpDesk to your website.",
				"helpdesk-contact-form"
			);
		$button_label = $licenseID
			? __("Customize your HelpDesk", "helpdesk-contact-form")
			: __("Continue", "helpdesk-contact-form");
		$button_link = $licenseID ? $helpdesk_app_link : $connect_link;
		$button_target = $licenseID ? "_blank" : "_self";
		$hd_connected = $licenseID ? "connected" : "not-connected";

		if ($licenseID) {
			// HD is connected
			$status_icon = $check_url;
			$hd_connected_class = "hd-connected";
			$heading = __("HelpDesk is connected", "helpdesk-contact-form");
			$description = __(
				"Customize your helpdesk and boost your customer engagement.",
				"helpdesk-contact-form"
			);
			$button_label = __(
				"Customize your HelpDesk",
				"helpdesk-contact-form"
			);
			$button_link = $helpdesk_app_link;
			$button_target = "_blank";
			$escaped_form_url = esc_url($form_url);
			$iframe = '<iframe sandbox="allow-scripts allow-popups allow-forms allow-same-origin" width="100%" height="700px" style="border: 0; overflow: hidden; overflow-x: auto; margin:auto; display:flex;" src="' . $escaped_form_url . '"> Your browser does not allow embedded content.</iframe>';
		} else {
			// HD is not yet connected
			$status_icon = $plug_url;
			$hd_connected_class = "hd-no-connection";
			$heading = __(
				"Connect HelpDesk to your website",
				"helpdesk-contact-form"
			);
			$description = __(
				"Log in or create an account to connect HelpDesk to your website.",
				"helpdesk-contact-form"
			);
			$button_label = __("Continue", "helpdesk-contact-form");
			$button_link = $connect_link;
			$button_target = "_self";
		}
		?>


		<main class="wrap wpbody-content helpdesk_admin_page--content <?php echo esc_html(
			$hd_connected_class
		); ?>">
			<div class="helpdesk_admin_page--logo-full" style="background-image: url('<?php echo esc_html(
				$logo_url
			); ?>');">
			</div>

			<div class="helpdesk_admin_page--flex-row-container hd-connected-container">
				<div class="settings-column">
					<div class="admin-page-box box-green">
						<h2><?php esc_html_e(
							"Add a contact form to your website",
							"helpdesk-contact-form"
						); ?></h2>

						<h3><?php esc_html_e("Shortcode", "helpdesk-contact-form"); ?></h3>
						<p>
							<?php esc_html_e(
								"Use this shortcode to display the contact form on any WordPress post or page:",
								"helpdesk-contact-form"
							); ?>
						</p>
						<p><code class="mm-code">[helpdesk_contact_form]</code></p>

						<h3><?php esc_html_e("Template tag", "helpdesk-contact-form"); ?></h3>
						<p>
							<?php esc_html_e(
								"Use this template tag to display the contact form anywhere in your theme template:",
								"helpdesk-contact-form"
							); ?>
						</p>
						<p><code
								class="mm-code">&lt;?php if (function_exists('helpdesk_contact_form')) helpdesk_contact_form(); ?&gt;</code>
						</p>

						<h3><?php esc_html_e("Create a new page", "helpdesk-contact-form"); ?></h3>
						<p style="margin-bottom:10px;">
							<?php esc_html_e(
								"Automatically create a new page with a contact form.",
								"helpdesk-contact-form"
							); ?>
						</p>
						<hr class="wp-header-end">


						<?php
						$args = [
							"title" => "Contact Us",
							"post_type" => "page",
							"post_status" => "publish",
							"posts_per_page" => 1,
						];
						$page_check_query = new \WP_Query($args);

						if ($page_check_query->have_posts()) {
							$page_link = get_permalink($page_check_query->posts[0]->ID); ?>
							<div class="notice notice-success is-dismissible">
								<p>
									<?php
									echo sprintf(
										/* translators: %s: link to the page */
										esc_html__(
											'The "Contact us" page has been successfully created. %s.',
											"helpdesk-contact-form"
										),
										'<a href="' . esc_url($page_link) . '" target="_blank">View it here</a>',
									); ?>
								</p>
							</div>
							<?php
						} else {
							?>
							<div class="wrap">
								<form method="post" action="">
									<?php wp_nonce_field("helpdesk_create_page_action", "helpdesk_create_page_nonce"); ?>
									<input type="hidden" name="helpdesk_create_page" value="1">
									<button type="submit" style="margin-left:0;"
										class="helpdesk_admin_page--button helpdesk_admin_page--button-secondary">Create a new page with contact
										form</button>
								</form>
							</div>
							<?php
						}
						?>
					</div>
					<div class="admin-page-box box-green">
						<h3><?php esc_html_e(
							"Manage contact form messages",
							"helpdesk-contact-form"
						); ?></h3>
						<p style="margin-bottom:10px;"><?php esc_html_e(
							"HelpDesk status: Connected",
							"helpdesk-contact-form"
						); ?> 🟢
						</p>
						<a style="margin-left:0;" class="helpdesk_admin_page--button "
							href="<?php echo esc_attr($helpdesk_app_link); ?>" target="<?php echo esc_attr($button_target); ?>"><?php esc_html_e(
											"Browse tickets in HelpDesk",
											"helpdesk-contact-form"
										); ?></a>

					</div>
				</div>
				<div class="preview-column">
					<div class="admin-page-box box-green box-green">
						<div class="flex-row-space-between">
							<div>
								<h2 style="display:inline-block" class="preview-h2">Preview</h2> <a class="" id="refresh-button"
									href="#"><img style="display:inline-block; margin-left:4px; height:18px;"
										class="helpdesk_admin_page--refresh-icon" src="<?php echo esc_html(
											$refresh_icon_url
										); ?>" alt="<?php echo esc_attr($refresh_icon_alt_text); ?>"> </img> </a>
							</div>
							<a class="blue-button" href="<?php echo esc_html(
								$helpdesk_app_link
							); ?>/settings/contact-forms/<?php echo esc_html($contact_form_id); ?>" target="_blank">
								<span>Customize form</span>
								<img style="display:inline-block; margin-left:4px; height:15px;" class="helpdesk_admin_page--edit-icon"
									src="<?php echo esc_html($edit_icon_url); ?>" alt="<?php echo esc_attr(
										 	$status_icon_alt_text
										 ); ?>">
								</img>
							</a>
						</div>
						<div class="helpdesk_admin_page--section">
							<?php if (isset($iframe)) {
								echo wp_kses($iframe, $this->helpdesk_allowed_html_in_iframe());
							} ?>

						</div>


					</div>
				</div>
			</div>

			<div class="helpdesk_admin_page--container helpdesk-connection-box box-green">
				<div class="helpdesk_admin_page--section">
					<img class="helpdesk_admin_page--status-icon" src="<?php echo esc_html(
						$status_icon
					); ?>" alt="<?php echo esc_attr($status_icon_alt_text); ?>">
				</div>
				<div class="helpdesk_admin_page--section">
					<h1><?php echo esc_html($heading); ?></h1>
					<p><?php echo esc_html($description); ?></p>
				</div>
				<div class="helpdesk_admin_page--section">
					<a class="helpdesk_admin_page--button" href="<?php echo esc_attr(
						$button_link
					); ?>" target="<?php echo esc_attr($button_target); ?>"><?php echo esc_html(
								$button_label
							); ?></a>
				</div>
			</div>


		</main>
		<?php
	}

	/**
	 * Form handler for creating a new page with a contact form.
	 * @return void
	 */
	public function helpdesk_create_contact_page()
	{
		if (
			isset($_POST["helpdesk_create_page"]) &&
			$_POST["helpdesk_create_page"] == "1"
		) {
			if (!isset($_POST['helpdesk_create_page_nonce']) || !wp_verify_nonce($_POST['helpdesk_create_page_nonce'], 'helpdesk_create_page_action')) {
				wp_die(esc_html(__('Nonce verification failed', 'helpdesk-contact-form')));
			}
			$this->create_page_with_hd_form();
		}
	}

	/**
	 * Creates a new page with a contact form if it doesn't exist yet.
	 * @return void
	 */
	public function create_page_with_hd_form()
	{
		$page = [
			"post_title" => "Contact Us",
			"post_content" => "[helpdesk_contact_form]",
			"post_status" => "publish",
			"post_type" => "page",
		];
		// Check for the page's existence
		$args = [
			"title" => "Contact Us",
			"post_type" => "page",
			"post_status" => "publish",
			"posts_per_page" => 1,
		];
		$page_check_query = new \WP_Query($args);

		if (!$page_check_query->have_posts()) {
			$page_id = wp_insert_post($page);
			$this->page_id = $page_id;
			$this->notice_message = sprintf(
				/* translators: %s: link to the page */
				esc_html__(
					"Contact Us page created successfully. You can view it %s.",
					"helpdesk-contact-form"
				),
				'<a href="' . esc_url(get_permalink($page_id)) . '">here</a>',
			);
			$this->notice_class = "notice-success";
		}

		// Reset the global $post variable
		wp_reset_postdata();
	}
}
?>