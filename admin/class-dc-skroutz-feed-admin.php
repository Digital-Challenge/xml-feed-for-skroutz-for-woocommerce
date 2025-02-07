<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @noinspection PhpUnused
 * @noinspection HtmlUnknownTarget
 */
class Dicha_Skroutz_Feed_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @var string $plugin_name
	 */
	private string $plugin_name;

	/**
	 * The current version of this plugin.
	 *
	 * @var string $version
	 */
	private string $version;

	/**
	 * A list of attribute slug/labels, ready for display in inputs.
	 *
	 * @var array $attributes_list
	 */
	private array $attributes_list;

	/**
	 * The installed version of WooCommerce plugin.
	 *
	 * @var string $woo_version
	 */
	private string $woo_version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( string $plugin_name, string $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->woo_version = defined( 'WC_VERSION' ) ? WC_VERSION : '6.2.0';
	}


	/**
	 *****************************
	 ***** MENU AND SETTINGS *****
	 *****************************
	 */

	/**
	 * Register top-level menu page if not created already by other DC plugin.
	 */
	public function create_dc_toplevel_menu(): void {

		if ( empty ( $GLOBALS['admin_page_hooks']['digital_challenge_plugins'] ) ) {
			add_menu_page(
				'Digital Challenge',
				'Digital Challenge',
				'manage_options',
				'digital_challenge_plugins',
				[ $this, 'digital_challenge_plugin_settings' ],
				'data:image/svg+xml;base64,PHN2ZyBpZD0iZGNfbG9nb19zdmciIGRhdGEtbmFtZT0iZGNfbG9nb19zdmciIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjQwMCIgaGVpZ2h0PSI0MDAiIGZpbGwtcnVsZT0iZXZlbm9kZCI+PHBhdGggZmlsbD0iI2E3YWFhZCIgZD0iTTIwNy40IDQuNjQ5Yy0zOS41MDMgNC4wMTMtNzAuNTI5IDE3LjQyMy0xMDQuMjk1IDQ1LjA3Ni0xLjcwMiAxLjM5NS0xMS4xNTIgOC4yMjktMjEgMTUuMTg3TDQ4LjYgODguNjA1bC0yNS4zNzUgMTcuOTE4QzkuNzE3IDExNi4wMjYgOS4wOTQgMTE2LjY4IDEwLjY1OSAxMTkuNzJjLjM2Mi43MDQgNy4wODggMTAuMzcgMTQuOTQ2IDIxLjQ4QzQzLjA0NyAxNjUuODU5IDQxLjQ0OSAxNjMuMDEyIDQxLjIgMTY5Yy0uMzk0IDkuNDU5LS4yIDMwLjMzMi4zMjEgMzQuNkM1MC4yNzcgMjc1LjI1MyA5OS4wNSAzMzEuOTgxIDE2Ny40IDM1MC4wMDdjMi42NC42OTYgNS41MiAxLjY4MyA2LjQgMi4xOTQgMS45MjMgMS4xMTUgMS4xMjQuMDUxIDE4LjQzMyAyNC41NTEgMTQuMTU4IDIwLjA0MSAxNS4wMjkgMjEuMTIgMTYuNyAyMC43MDEuNTE2LS4xMyAxMy43MDItOS4yNjYgMjkuMzAyLTIwLjMwM2w2Ni4xNjUtNDYuMzM5YzU3LjkzOC0zNS4wMiA4OS4yODEtODcuNDI3IDg5LjM1Ny0xNDkuNDExLjA0Ni0zNy44NTYtOC44NjUtNjguNTk0LTI4LjI5Ny05Ny42LTMxLjMzNC00Ni43NzMtNjguNzY2LTcwLjMxNS0xMjQuNDM4LTc4LjI2Mi01LjM5NS0uNzctMjguNzIxLTEuMzg3LTMzLjYyMi0uODg5bTIxIDIyLjE2MWMxNy41MjkgMS4yMTMgNDMuNjk4IDcuNjgyIDQ4LjEgMTEuODg4IDEuODEgMS43MyAxLjYyMiAyLjQwOS03LjE1MSAyNS44NTMtMTEuMTI4IDI5LjczNC05LjU5NSAyNy40NTYtMTYuOTc5IDI1LjIzNS0zMy4wMzItOS45MzktNjAuMzQ3LTYuNTctODYuMDgyIDEwLjYxNy00MC42MjkgMjcuMTM0LTUzLjYzOCA3My45NzEtMzIuNTE4IDExNy4wODEgMjguMTI3IDU3LjQxMyA4OC41MzcgNzUuMTA2IDEzOC42MyA0MC42MDEgMjYuNzA2LTE4LjM5NSAzOS42My00Mi4zMyA0Mi4xNDItNzguMDQ1LjY0My05LjEzNC41NzctOS4wMzcgNi4zMzUtOS4zOSA4Ljc2Mi0uNTM4IDQ3LjMzNy0uNzE4IDQ4LjQxNy0uMjI2IDIuMDA4LjkxNiAyLjMwNiAyLjMyIDIuMzA2IDEwLjg4IDAgNTIuODY3LTI3Ljk4NCA5OS41MjQtNzcuNzEgMTI5LjU2My0zNy4wNTMgMjIuMzgzLTg0Ljc5MiAyOC41MDktMTI1LjY5IDE2LjEyOS0zNC4wNzktMTAuMzE2LTYwLjAyMS0yOS44ODUtODAuODYzLTYwLjk5Ni0yOC41NzEtNDIuNjQ4LTMyLjIzMi05Ni44NDMtOS44NDktMTQ1LjggMjYuMjY4LTU3LjQ1NCA5MS4wMzktOTcuNTM2IDE1MC45MTItOTMuMzltLTEuNDU0IDgxLjAyOWMzMy45NTQgNS4zNzEgNjUuMDg0IDQ2LjMxOSA2MS44ODMgODEuNC0yLjE1IDIzLjU1OS0xMy4zMTkgNDEuMDczLTM0LjEzMiA1My41MjMtNi45NTMgNC4xNTktMTYuNTIgOC4wMzgtMTkuODI1IDguMDM4LTIuODA0IDAtLjM4NCAzLjE1NS0zMy4yODctNDMuNGwtMzcuOTA4LTUzLjZjLTguNDk5LTEyLjAwOS04LjQwNC0xMS4zODktMi44MjItMTguNDU1IDExLjMxNS0xNC4zMjUgMjkuNDMzLTI1LjIwNSA0NS44MzktMjcuNTI1IDguMDAxLTEuMTMxIDEzLjAxLTEuMTI2IDIwLjI1Mi4wMTkiLz48L3N2Zz4=',
				20
			);
		}

		add_submenu_page( 'digital_challenge_plugins', esc_html__( 'Skroutz/BestPrice XML', 'xml-feed-for-skroutz-for-woocommerce' ), esc_html__( 'Skroutz/BestPrice XML', 'xml-feed-for-skroutz-for-woocommerce' ), 'manage_options', DICHA_SKROUTZ_FEED_SLUG, [
			$this,
			'digital_challenge_plugin_settings'
		] );
	}


	/**
	 * Creates main page and tab menu <ul>. Also provides a hook for other plugins to hook their own tab <li>.
	 */
	public function digital_challenge_plugin_settings(): void {

		if ( ! current_user_can( 'manage_options' ) ) return;

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php $this->print_notices(); ?>
		</div>
		<div class="settings-tabs">
			<ul class="subsubsub">
				<li class="dc-plugin-tab">
					<?php if ( isset( $_GET['page'] ) && $_GET['page'] === 'digital_challenge_plugins' ) : ?>
						<span class="active"><?php esc_html_e( 'Plugin\'s Homepage', 'xml-feed-for-skroutz-for-woocommerce' ); ?></span>
					<?php else : ?>
						<a href="admin.php?page=digital_challenge_plugins"><?php esc_html_e( 'Plugin\'s Homepage', 'xml-feed-for-skroutz-for-woocommerce' ); ?></a>
					<?php endif; ?>
				</li>
				<?php do_action( 'digital_challenge_plugin_settings_tabs' ); ?>
			</ul>
		</div>
		<div class="clear"></div>
		<div class="render-settings">
			<?php do_action( 'digital_challenge_plugin_settings' ); ?>
		</div>
		<?php
	}


	/**
	 * Creates the tab menu item which contains the plugin's settings.
	 */
	public function create_plugin_settings_tab(): void {
		?>
		<li class="dc-plugin-tab">
			<?php if ( isset( $_GET['page'] ) && $_GET['page'] === DICHA_SKROUTZ_FEED_SLUG ) : ?>
				| <span class="active">Skroutz/BestPrice XML</span>
			<?php else : ?>
				| <a href="admin.php?page=<?php echo esc_attr( $this->plugin_name ); ?>">Skroutz/BestPrice XML</a>
			<?php endif; ?>
		</li>
		<?php
	}


	/**
	 * Displays an external link to see all Digital Challenge plugins, if no plugin tab is selected.
	 */
	function dc_render_settings_homepage(): void {
		?>
		<a href="https://www.dicha.gr/plugins/" target="_blank" title="Digital Challenge Plugins"><?php esc_html_e( 'Δείτε όλα τα plugins της Digital Challenge', 'xml-feed-for-skroutz-for-woocommerce' ); ?></a>
		<?php
	}


	/**
	 * Creates settings sections and fields.
	 * Registers settings.
	 */
	function register_plugin_settings(): void {

		add_settings_section(
			'dicha_skroutz_feed_cron_section',
			__( 'Feed generation schedule', 'xml-feed-for-skroutz-for-woocommerce' ),
			[ $this, 'print_cron_settings_info' ],
			'dicha_skroutz_feed_settings'
		);

		add_settings_section(
			'dicha_skroutz_feed_settings_section',
			__( 'Settings for Plugin Skroutz/Best Price XML', 'xml-feed-for-skroutz-for-woocommerce' ),
			[ $this, 'print_skroutz_settings_info' ],
			'dicha_skroutz_feed_settings'
		);

		add_settings_section(
			'dicha_skroutz_feed_logs_section',
			__( 'Log settings', 'xml-feed-for-skroutz-for-woocommerce' ),
			[ $this, 'print_logs_settings_info' ],
			'dicha_skroutz_feed_settings'
		);

		$settings = [
			[
				'dicha_skroutz_feed_cron' => [
					__( 'WP Cron Schedule', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_cron_section'
				]
			],
			[
				'dicha_skroutz_feed_manufacturer' => [
					__( 'Manufacturer', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_color' => [
					__( 'Color', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_size' => [
					__( 'Size', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_availability' => [
					__( 'Default Availability', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_include_backorders' => [
					__( 'Include products on backorder', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_title_attributes' => [
					__( 'Attributes in Product name', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_description' => [
					__( 'Description to display', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_enable_ean_field' => [
					__( 'EAN/Barcode field', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_incl_excl_mode_categories' => [
					'',
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_incl_excl_mode_tags' => [
					'',
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_filter_categories' => [
					__( 'Product categories filter', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_filter_tags' => [
					__( 'Product tags filter', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_shipping_cost' => [
					__( 'Fixed Shipping Cost', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_free_shipping' => [
					__( 'Free Shipping over', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_settings_section'
				]
			],
			[
				'dicha_skroutz_feed_log_level' => [
					__( 'Log level', 'xml-feed-for-skroutz-for-woocommerce' ),
					'dicha_skroutz_feed_logs_section'
				]
			],
		];

		// don't add labels for these, they will be added manually
		$no_input_label_for_these = [
			'dicha_skroutz_feed_cron',
			'dicha_skroutz_feed_enable_ean_field',
			'dicha_skroutz_feed_description'
		];

		foreach ( $settings as $setting ) {

			foreach ( $setting as $key => $item ) {

				if ( ! empty( $item[0] ) ) {
					add_settings_field(
						$key,
						$item[0],
						[ $this, $key . '_callback' ],
						'dicha_skroutz_feed_settings',
						$item[1],
						[
							'class' => $key,
							'label_for' => !in_array( $key, $no_input_label_for_these ) ? $key : ''
						]
					);
				}

				register_setting(
					'dicha_skroutz_feed_option_group',
					$key,
					[
						'sanitize_callback' => [ $this, 'sanitize_own_settings' ]
					]
				);
			}
		}
	}


	/**
	 * Sanitizes our own settings.
	 *
	 * @param mixed $value The sanitized option value.
	 */
	function sanitize_own_settings( $value ) {

		if ( is_array( $value ) ) {
			$value = array_map( 'sanitize_text_field', $value );
		}
		else {
			$value = sanitize_text_field( $value );
		}

		return $value;
	}


	/**
	 * Prints the Settings form and XML Feed Tools.
	 */
	function render_plugin_settings(): void {

		// todo maybe create an options migrator

		$last_run = get_option( 'dicha_skroutz_feed_last_run' );
		?>
		<div class="wrap <?php echo esc_attr( $this->plugin_name ); ?>-settings">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="dicha-skroutz-feed-tools-wrapper">
				<h2><?php esc_html_e( 'XML Feed Tools', 'xml-feed-for-skroutz-for-woocommerce' ); ?></h2>
				<p><?php esc_html_e( 'Last XML feed generated at:', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
					<strong><?php echo esc_html( $last_run ?: __( 'Never', 'xml-feed-for-skroutz-for-woocommerce' ) ); ?></strong>
				</p>
				<p><?php esc_html_e( 'Submit the following URL to Skroutz or BestPrice:', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
					<br>
					<code><?php echo esc_url( $this->get_default_xml_file_url() ); ?></code>
				</p>
				<p class="submit">
					<?php if ( $last_run ) : ?>
						<a href="<?php echo esc_url( $this->get_default_xml_file_url() ); ?>" target="_blank"
						   class="button button-primary">
							<?php esc_html_e( 'View XML feed', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
						</a>
					<?php endif; ?>
					<input type="submit" name="submit" id="submit" class="button button-primary"
					       value="<?php echo esc_html( $last_run ? __( 'Update XML feed', 'xml-feed-for-skroutz-for-woocommerce' ) : __( 'Create XML feed', 'xml-feed-for-skroutz-for-woocommerce' ) ); ?>">
				</p>
				<input type="hidden" name="action" value="dicha_skroutz_feed_create_feed">
			</form>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'dicha_skroutz_feed_option_group' );
				do_settings_sections( 'dicha_skroutz_feed_settings' );
				submit_button();
				?>
				<input type="hidden" name="action" value="save_settings">
			</form>
		</div>
		<?php
	}


	/**
	 * Saves the plugin's Settings.
	 */
	function save_settings(): void {

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ), 'dicha_skroutz_feed_option_group-options' ) ) return;
		if ( ! current_user_can( 'manage_options' ) ) return;

		update_option( 'dicha_skroutz_feed_manufacturer', ! empty( $_POST['dicha_skroutz_feed_manufacturer'] ) ? wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_manufacturer'] ) ) : [], false );
		update_option( 'dicha_skroutz_feed_color', ! empty( $_POST['dicha_skroutz_feed_color'] ) ? wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_color'] ) ) : [], false );
		update_option( 'dicha_skroutz_feed_size', ! empty( $_POST['dicha_skroutz_feed_size'] ) ? wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_size'] ) ) : [], false );
		update_option( 'dicha_skroutz_feed_availability', wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_availability'] ?? '' ) ), false );
		update_option( 'dicha_skroutz_feed_include_backorders', wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_include_backorders'] ?? '' ) ), false );
		update_option( 'dicha_skroutz_feed_title_attributes', ! empty( $_POST['dicha_skroutz_feed_title_attributes'] ) ? wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_title_attributes'] ) ) : [], false );
		update_option( 'dicha_skroutz_feed_description', wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_description'] ?? '' ) ), false );
		update_option( 'dicha_skroutz_feed_enable_ean_field', wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_enable_ean_field'] ?? '' ) ), false );
		update_option( 'dicha_skroutz_feed_filter_categories', ! empty( $_POST['dicha_skroutz_feed_filter_categories'] ) ? wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_filter_categories'] ) ) : [], false );
		update_option( 'dicha_skroutz_feed_filter_tags', ! empty( $_POST['dicha_skroutz_feed_filter_tags'] ) ? wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_filter_tags'] ) ) : [], false );
		update_option( 'dicha_skroutz_incl_excl_mode_categories', wc_clean( wp_unslash( $_POST['dicha_skroutz_incl_excl_mode_categories'] ?? '' ) ), false );
		update_option( 'dicha_skroutz_incl_excl_mode_tags', wc_clean( wp_unslash( $_POST['dicha_skroutz_incl_excl_mode_tags'] ?? '' ) ), false );
		update_option( 'dicha_skroutz_feed_shipping_cost', wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_shipping_cost'] ?? '' ) ), false );
		update_option( 'dicha_skroutz_feed_free_shipping', wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_free_shipping'] ?? '' ) ), false );
		update_option( 'dicha_skroutz_feed_log_level', wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_log_level'] ?? '' ) ), false );


		// Cron options and handling
		$cron_hour = wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_cron_hour'] ?? '' ) );
		$cron_hour = is_numeric( $cron_hour ) && $cron_hour > 0 && $cron_hour <= 24 ? (int) $cron_hour : '';

		$cron_min  = wc_clean( wp_unslash( $_POST['dicha_skroutz_feed_cron_minute'] ?? '' ) );
		$cron_min  = is_numeric( $cron_min ) && $cron_min >= 0 && $cron_min < 60 ? (int) $cron_min : 0;

		// Maybe set cron and then update setting
		$new_cron_options = [
			'h' => $cron_hour,
			'm' => $cron_min
		];

		$this->maybe_set_cron( $new_cron_options );
		update_option( 'dicha_skroutz_feed_cron', $new_cron_options, false );

		// redirect with success update notice
		wp_redirect( admin_url( 'admin.php?page=' . DICHA_SKROUTZ_FEED_SLUG . '&updated=1' ) );
		exit;
	}


	/**
	 * Updates the scheduled cron actions for XML feed generation.
	 *
	 * @param array $new_cron_options
	 *
	 * @return void
	 */
	function maybe_set_cron( array $new_cron_options ): void {

		$hook  = 'dicha_skroutz_feed_generation';
		$args  = [];
		$group = 'dicha_feeds_generation';

		as_unschedule_all_actions( $hook, $args, $group );

		if ( $new_cron_options['h'] !== '' && $new_cron_options['m'] !== '' ) {

			$cron_schedule = sprintf( '%1$d %2$s * * *', $new_cron_options['m'], $new_cron_options['h'] > 1 ? "*/{$new_cron_options['h']}" : "*" );

			as_schedule_cron_action( time(), $cron_schedule, $hook, $args, $group, true, 9 );
		}
	}


	/**
	 * Prints a notice in the settings page about the outcome of the manual XML generation process.
	 *
	 * @return void
	 */
	function print_notices(): void {

		if ( isset( $_GET['updated'] ) && '1' === $_GET['updated'] ) : ?>
			<div class="updated">
				<p><?php esc_html_e( 'Changes have been saved.', 'xml-feed-for-skroutz-for-woocommerce' ); ?></p>
			</div>
		<?php endif; ?>
		<?php if ( isset( $_GET['feed_success'] ) ) : ?>
			<?php if ( '1' === $_GET['feed_success'] ) : ?>
				<div class="notice notice-success">
					<p><?php esc_html_e( 'XML feed was generated successfully!', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
						<a href="<?php echo esc_url( $this->get_default_xml_file_url() ); ?>"
						   target="_blank"><?php esc_html_e( 'View XML', 'xml-feed-for-skroutz-for-woocommerce' ); ?></a>
					</p>
				</div>
			<?php else: ?>
				<div class="notice notice-error">
					<p><?php esc_html_e( 'XML feed generation failed. Enable and then check plugin logs for more details.', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
					</p>
				</div>
			<?php endif; ?>
		<?php endif;
	}



	/**
	 ***************************
	 ***** SETTINGS INPUTS *****
	 ***************************
	 */

	/**
	 * Prints the Feed generation schedule section description.
	 */
	function print_cron_settings_info(): void {
		esc_html_e( 'Settings about the scheduling of Skroutz/BestPrice XML feed generation.', 'xml-feed-for-skroutz-for-woocommerce' );
	}


	/**
	 * Prints the XML Settings section description.
	 */
	function print_skroutz_settings_info(): void {
		esc_html_e( 'Settings to control the product data for the XML feed.', 'xml-feed-for-skroutz-for-woocommerce' );
		echo '<br>';
		esc_html_e( 'Do NOT use the same attribute in multiple fields, for example in both Color and Size.', 'xml-feed-for-skroutz-for-woocommerce' );
	}


	/**
	 * Prints the logs settings section description.
	 */
	function print_logs_settings_info(): void {
		esc_html_e( 'Settings about logging during the feed generation.', 'xml-feed-for-skroutz-for-woocommerce' );
	}


	/**
	 * Prints the HTML for the cron schedule field in the settings.
	 */
	function dicha_skroutz_feed_cron_callback(): void {

		$current_cron_options = get_option( 'dicha_skroutz_feed_cron', [ 'h' => '', 'm' => '50' ] );

		// compatibility with old option
		if ( is_string( $current_cron_options ) ) {

			if ( 'hourly' === $current_cron_options ) {
				$current_cron_options = [
					'h' => 1,
					'm' => 50
				];
			}
			elseif ( 'twicedaily' === $current_cron_options ) {
				$current_cron_options = [
					'h' => 12,
					'm' => 50
				];
			}
			elseif ( 'daily' === $current_cron_options ) {
				$current_cron_options = [
					'h' => 24,
					'm' => 50
				];
			}
		}

		$allowed_html_for_inputs = $this->get_allowed_html_tags_for_inputs();

		printf(
			/* translators: 1: A select input for cron schedule. 2: the CSS class for schedule description. 3: CSS styles. 4: Number representing the minute */
			wp_kses_post( __( '%1$s<span class="%2$s" style="%3$s">, starting at 00:XX, where XX is the %4$s minute.</span>', 'xml-feed-for-skroutz-for-woocommerce' ) ),
			wp_kses( $this->get_html_for_cron_hour_field( $current_cron_options ), $allowed_html_for_inputs ),
			'dicha-skroutz-cron-minute-input-wrapper',
			empty( $current_cron_options['h'] ) ? 'display:none' : 'display:inline',
			wp_kses( $this->get_html_for_cron_minute_field( $current_cron_options ), $allowed_html_for_inputs ),
		);

		?>
		<p class="desc">
			<?php esc_html_e( 'Select how frequently the XML feed will be updated.', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
		</p>
		<ol>
			<li><?php echo wp_kses_post( __( 'Select the frequency of the XML feed generation, or disable automatic generation using WP Cron scheduling.', 'xml-feed-for-skroutz-for-woocommerce' ) ); ?></li>
			<li><?php echo wp_kses_post( __( '<strong>Fill the "minute" field with a number between 0-59.</strong> If you insert the value "45", then the import will start running at 00:45 at night (UTC time), and then every X hours, depending on the "hour" field. Suggested values: 45-55 so that the feed is ready when Skroutz fetches your XML.', 'xml-feed-for-skroutz-for-woocommerce' ) ); ?></li>
			<li><?php esc_html_e( 'If "No WP Cron" is selected, then the XML generation will not run automatically using WP Cron, so you need to setup server cron jobs manually. This option is best for large eshops that need frequent updates on specific time, and need more server resources.', 'xml-feed-for-skroutz-for-woocommerce' ); ?></li>
		</ol>
		<?php
	}


	/**
	 * Retrieves a list of allowed HTML tags and their permitted attributes for form inputs.
	 *
	 * @return array The allowed HTML tags and their attributes.
	 */
	private function get_allowed_html_tags_for_inputs(): array {
		return [
			'select' => [
				'id'         => [],
				'name'       => [],
				'value'      => [],
				'aria-label' => []
			],
			'option' => [
				'value'    => [],
				'selected' => []
			],
			'input'  => [
				'id'         => [],
				'type'       => [],
				'min'        => [],
				'max'        => [],
				'step'       => [],
				'name'       => [],
				'value'      => [],
				'selected'   => [],
				'aria-label' => []
			],
		];
	}


	/**
	 * Prints the HTML for the cron schedule hour field in the settings.
	 *
	 * @param $current_cron_options array
	 *
	 * @return string
	 */
	private function get_html_for_cron_hour_field( array $current_cron_options ): string {

		$current_cron_hour = ! empty( $current_cron_options['h'] ) ? (int) $current_cron_options['h'] : '';
		$cron_hour_options = [ 1, 2, 3, 4, 6, 8, 12, 24 ];

		ob_start();
		?>
		<select id="dicha_skroutz_feed_cron_hour" name="dicha_skroutz_feed_cron_hour" aria-label="<?php esc_html_e( 'Frequency of feed generation in hours.', 'xml-feed-for-skroutz-for-woocommerce' ); ?>">
			<option value=""><?php esc_html_e( 'No WP Cron', 'xml-feed-for-skroutz-for-woocommerce' ); ?></option>
			<?php foreach ( $cron_hour_options as $cron_hour_option ) : ?>
				<option value="<?php echo esc_attr( $cron_hour_option ); ?>"<?php selected( $current_cron_hour === $cron_hour_option ); ?>>
					<?php
					/* translators: %d: A number representing the hour interval. */
					printf( esc_html( _n( 'Every %d hour', 'Every %d hours', $cron_hour_option , 'xml-feed-for-skroutz-for-woocommerce' ) ), esc_attr( $cron_hour_option ) );
					?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php

		return ob_get_clean();
	}


	/**
	 * Prints the HTML for the cron schedule minute field in the settings.
	 *
	 * @param $current_cron_options array
	 *
	 * @return string
	 */
	private function get_html_for_cron_minute_field( array $current_cron_options ): string {

		$current_cron_minute = ! empty( $current_cron_options['m'] ) ? (int) $current_cron_options['m'] : 0;

		ob_start();
		?>
		<input id="dicha_skroutz_feed_cron_minute" name="dicha_skroutz_feed_cron_minute" type="number"
		       min="0" max="59" step="1" value="<?php echo esc_attr( $current_cron_minute ); ?>"
		       aria-label="<?php esc_html_e( 'Exact minute (0-59) of the hour to start the feed generation.', 'xml-feed-for-skroutz-for-woocommerce' ); ?>" />
		<?php

		return ob_get_clean();
	}


	/**
	 * Prints the HTML for the manufacturer field in the settings.
	 */
	function dicha_skroutz_feed_manufacturer_callback(): void {

		$selected_values      = get_option( 'dicha_skroutz_feed_manufacturer', [] );
		$options              = $this->prepare_attributes_list();
		$custom_brand_options = [];

		if ( ! empty( $options['custom_taxonomies'] ) ) {
			if ( isset( $options['custom_taxonomies']['product_brand'] ) ) {
				$custom_brand_options['woo__product_brand'] = __( 'WooCommerce Brands', 'xml-feed-for-skroutz-for-woocommerce' );
			}
			// maybe add support for other brands plugins here...
		}

		?>
		<!--suppress HtmlFormInputWithoutLabel -->
		<select id="dicha_skroutz_feed_manufacturer" name="dicha_skroutz_feed_manufacturer[]" class="select-woo-input" multiple="multiple">
			<optgroup label="<?php esc_html_e( 'Available product attributes:', 'xml-feed-for-skroutz-for-woocommerce' ); ?>">
				<?php foreach ( $options['attribute_taxonomies'] as $attr_slug => $attr_name ) : ?>
					<option value="<?php echo esc_attr( $attr_slug ); ?>"<?php selected( in_array( $attr_slug, $selected_values ) ); ?>>
						<?php echo esc_html( $attr_name ); ?>
					</option>
				<?php endforeach; ?>
			</optgroup>
			<?php if ( ! empty( $custom_brand_options ) ) : ?>
				<optgroup label="<?php esc_html_e( 'Other brand taxonomies:', 'xml-feed-for-skroutz-for-woocommerce' ); ?>">
					<?php foreach ( $custom_brand_options as $tax_slug => $tax_name ) : ?>
						<option value="<?php echo esc_attr( $tax_slug ); ?>"<?php selected( in_array( $tax_slug, $selected_values ) ); ?>>
							<?php echo esc_html( $tax_name ); ?>
						</option>
					<?php endforeach; ?>
				</optgroup>
			<?php endif; ?>
		</select>
		<?php
	}


	/**
	 * Prints the HTML for the color field in the settings.
	 */
	function dicha_skroutz_feed_color_callback(): void {

		$options         = $this->prepare_attributes_list();
		$selected_values = get_option( 'dicha_skroutz_feed_color', [] );
		?>
		<!--suppress HtmlFormInputWithoutLabel -->
		<select id="dicha_skroutz_feed_color" name="dicha_skroutz_feed_color[]" class="select-woo-input" multiple="multiple">
			<optgroup label="<?php esc_html_e( 'Available product attributes:', 'xml-feed-for-skroutz-for-woocommerce' ); ?>">
				<?php foreach ( $options['attribute_taxonomies'] as $attr_slug => $attr_name ) : ?>
					<option value="<?php echo esc_attr( $attr_slug ); ?>"<?php selected( in_array( $attr_slug, $selected_values ) ); ?>>
						<?php echo esc_html( $attr_name ); ?>
					</option>
				<?php endforeach; ?>
			</optgroup>
		</select>
		<?php
	}


	/**
	 * Prints the HTML for the size field in the settings.
	 */
	function dicha_skroutz_feed_size_callback(): void {

		$options         = $this->prepare_attributes_list();
		$selected_values = get_option( 'dicha_skroutz_feed_size', [] );
		?>
		<!--suppress HtmlFormInputWithoutLabel -->
		<select id="dicha_skroutz_feed_size" name="dicha_skroutz_feed_size[]" class="select-woo-input" multiple="multiple">
			<optgroup label="<?php esc_html_e( 'Available product attributes:', 'xml-feed-for-skroutz-for-woocommerce' ); ?>">
				<?php foreach ( $options['attribute_taxonomies'] as $attr_slug => $attr_name ) : ?>
					<option value="<?php echo esc_attr( $attr_slug ); ?>"<?php selected( in_array( $attr_slug, $selected_values ) ); ?>>
						<?php echo esc_html( $attr_name ); ?>
					</option>
				<?php endforeach; ?>
			</optgroup>
		</select>
		<?php
	}


	/**
	 * Prints the HTML for the availability field in the settings.
	 */
	function dicha_skroutz_feed_availability_callback(): void {

		$selected_availability  = get_option( 'dicha_skroutz_feed_availability' );
		$skroutz_availabilities = self::skroutz_get_availability_options( false, true );
		?>
		<!--suppress HtmlFormInputWithoutLabel -->
		<select id="dicha_skroutz_feed_availability" name="dicha_skroutz_feed_availability">
			<?php foreach ( $skroutz_availabilities as $key => $availability_label ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $selected_availability, $key ); ?>>
					<?php echo esc_html( $availability_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p class="desc">
			<?php echo wp_kses_post( __( 'If you are not sure what to choose, consult <a href="https://developer.skroutz.gr/el/feedspec/#availability" target="_blank">Skroutz instructions</a>.', 'xml-feed-for-skroutz-for-woocommerce' ) ); ?>
		</p>
		<?php
	}


	/**
	 * Prints the HTML for the backorders inclusion field in the settings.
	 */
	function dicha_skroutz_feed_include_backorders_callback(): void {

		$dicha_skroutz_feed_include_backorders = get_option( 'dicha_skroutz_feed_include_backorders', 'no' );
		?>
		<label for="dicha_skroutz_feed_include_backorders">
			<input type="checkbox" id="dicha_skroutz_feed_include_backorders" name="dicha_skroutz_feed_include_backorders" value="yes"<?php checked( 'yes', $dicha_skroutz_feed_include_backorders ); ?>>
			<?php esc_html_e( 'Include products on backorder in XML', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
		</label>
		<?php
	}


	/**
	 * Prints the HTML for the attributes in product name field in the settings.
	 */
	function dicha_skroutz_feed_title_attributes_callback(): void {

		$selected_values         = get_option( 'dicha_skroutz_feed_title_attributes', [] );
		$options                 = $this->prepare_attributes_list();
		$custom_taxonomy_options = [];

		if ( ! empty( $options['custom_taxonomies'] ) ) {
			if ( isset( $options['custom_taxonomies']['product_brand'] ) ) {
				$custom_taxonomy_options['woo__product_brand'] = __( 'WooCommerce Brands', 'xml-feed-for-skroutz-for-woocommerce' );
			}
			// maybe add support for other custom taxonomies here...
		}

		?>
		<!--suppress HtmlFormInputWithoutLabel -->
		<select id="dicha_skroutz_feed_title_attributes" name="dicha_skroutz_feed_title_attributes[]"
		        class="select-woo-input" multiple="multiple">
			<optgroup label="<?php esc_html_e( 'Available product attributes:', 'xml-feed-for-skroutz-for-woocommerce' ); ?>">
				<?php foreach ( $options['attribute_taxonomies'] as $attr_slug => $attr_name ) : ?>
					<option
						value="<?php echo esc_attr( $attr_slug ); ?>"<?php selected( in_array( $attr_slug, $selected_values ) ); ?>>
						<?php echo esc_html( $attr_name ); ?>
					</option>
				<?php endforeach; ?>
			</optgroup>
			<?php if ( ! empty( $custom_taxonomy_options ) ) : ?>
				<optgroup label="<?php esc_html_e( 'Other taxonomies:', 'xml-feed-for-skroutz-for-woocommerce' ); ?>">
					<?php foreach ( $custom_taxonomy_options as $tax_slug => $tax_name ) : ?>
						<option value="<?php echo esc_attr( $tax_slug ); ?>"<?php selected( in_array( $tax_slug, $selected_values ) ); ?>>
							<?php echo esc_html( $tax_name ); ?>
						</option>
					<?php endforeach; ?>
				</optgroup>
			<?php endif; ?>
		</select>
		<p class="desc">
			<?php echo wp_kses_post( __( 'These attributes will be added to XML product name (if not included already).', 'xml-feed-for-skroutz-for-woocommerce' ) ); ?>
		</p>
		<?php
	}


	/**
	 * Prints the HTML for the description field in the settings.
	 */
	function dicha_skroutz_feed_description_callback(): void {

		$dicha_skroutz_feed_description = get_option( 'dicha_skroutz_feed_description', 'short' );
		?>
		<input type="radio" name="dicha_skroutz_feed_description"
		       id="dicha_skroutz_feed_description_short"
		       value="short"<?php checked( $dicha_skroutz_feed_description, 'short' ); ?>>
		<label for="dicha_skroutz_feed_description_short">
			<?php esc_html_e( 'Short description', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
		</label>
		<br>
		<input type="radio" name="dicha_skroutz_feed_description" id="dicha_skroutz_feed_description_long"
		       value="long"<?php checked( $dicha_skroutz_feed_description, 'long' ); ?>>
		<label for="dicha_skroutz_feed_description_long">
			<?php esc_html_e( 'Description', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
		</label>
		<?php
	}


	/**
	 * Prints the HTML for the ean field in the settings.
	 */
	function dicha_skroutz_feed_enable_ean_field_callback(): void {

		$default_ean_enabled = version_compare( $this->woo_version, '9.2', '>=' ) ? 'no' : 'yes';
		$dicha_skroutz_feed_enable_ean_field = get_option( 'dicha_skroutz_feed_enable_ean_field', $default_ean_enabled );
		?>
		<label for="dicha_skroutz_feed_enable_ean_field">
			<input type="checkbox" id="dicha_skroutz_feed_enable_ean_field" name="dicha_skroutz_feed_enable_ean_field" value="yes"<?php checked( 'yes', $dicha_skroutz_feed_enable_ean_field ); ?>>
			<?php esc_html_e( 'Add new field for inserting the EAN/Barcode info. The field will appear under WooCommerce SKU field.', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
			<?php if ( ! wc_string_to_bool( $dicha_skroutz_feed_enable_ean_field ) && version_compare( $this->woo_version, '9.2', '>=' ) ) : ?>
				<br><?php esc_html_e( 'We suggest keeping this unchecked. Use the new native WooCommerce field called "GTIN, UPC, EAN, or ISBN" which is located under the "SKU" field.', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
			<?php endif; ?>
		</label>
		<?php
	}


	/**
	 * Prints the HTML for the categories filter field in the settings.
	 */
	function dicha_skroutz_feed_filter_categories_callback(): void {

		$terms                = $this->get_taxonomy_list_tree( 'product_cat' );
		$include_exclude_mode = get_option( 'dicha_skroutz_incl_excl_mode_categories' );
		$selected_terms       = get_option( 'dicha_skroutz_feed_filter_categories', [] );
		$selected_terms       = is_array( $selected_terms ) ? $selected_terms : [];

		?>
		<select name="dicha_skroutz_incl_excl_mode_categories" style="vertical-align:top;"
		        aria-label="<?php esc_html_e( 'Include/Exclude mode', 'xml-feed-for-skroutz-for-woocommerce' ); ?>">
			<option value="0"<?php selected( $include_exclude_mode, '0' ); ?>>
				<?php esc_html_e( 'Exclude', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
			</option>
			<option value="1"<?php selected( $include_exclude_mode, '1' ); ?>>
				<?php esc_html_e( 'Include', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
			</option>
		</select>
		<!--suppress HtmlFormInputWithoutLabel -->
		<select id="dicha_skroutz_feed_filter_categories"
		        name="dicha_skroutz_feed_filter_categories[]" class="select-woo-input" multiple="multiple">
			<?php foreach ( $terms as $term_id => $term_name ) : ?>
				<option value="<?php echo (int) $term_id; ?>"<?php selected( in_array( $term_id, $selected_terms ) ); ?>>
					<?php echo esc_html( $term_name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}


	/**
	 * Prints the HTML for the tags filter field in the settings.
	 */
	function dicha_skroutz_feed_filter_tags_callback(): void {

		$terms                = $this->get_taxonomy_list_tree( 'product_tag' );
		$include_exclude_mode = get_option( 'dicha_skroutz_incl_excl_mode_tags' );
		$selected_terms       = get_option( 'dicha_skroutz_feed_filter_tags', [] );
		$selected_terms       = is_array( $selected_terms ) ? $selected_terms : [];

		?>
		<select name="dicha_skroutz_incl_excl_mode_tags" style="vertical-align:top;"
		        aria-label="<?php esc_html_e( 'Include/Exclude mode', 'xml-feed-for-skroutz-for-woocommerce' ); ?>">
			<option value="0"<?php selected( $include_exclude_mode, '0' ); ?>>
				<?php esc_html_e( 'Exclude', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
			</option>
			<option value="1"<?php selected( $include_exclude_mode, '1' ); ?>>
				<?php esc_html_e( 'Include', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
			</option>
		</select>
		<!--suppress HtmlFormInputWithoutLabel -->
		<select id="dicha_skroutz_feed_filter_tags"
		        name="dicha_skroutz_feed_filter_tags[]" class="select-woo-input" multiple="multiple">
			<?php foreach ( $terms as $term_id => $term_name ) : ?>
				<option value="<?php echo (int) $term_id; ?>"<?php selected( in_array( $term_id, $selected_terms ) ); ?>>
					<?php echo esc_html( $term_name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}


	/**
	 * Prints the HTML for the shipping cost field in the settings.
	 */
	function dicha_skroutz_feed_shipping_cost_callback(): void {
		printf(
			'<input type="text" id="dicha_skroutz_feed_shipping_cost" name="dicha_skroutz_feed_shipping_cost" value="%s" />',
			esc_attr( get_option( 'dicha_skroutz_feed_shipping_cost' ) )
		);
	}


	/**
	 * Prints the HTML for the free shipping field in the settings.
	 */
	function dicha_skroutz_feed_free_shipping_callback(): void {
		printf(
			'<input type="text" id="dicha_skroutz_feed_free_shipping" name="dicha_skroutz_feed_free_shipping" value="%s" />',
			esc_attr( get_option( 'dicha_skroutz_feed_free_shipping' ) )
		);
	}


	/**
	 * Prints the HTML for the logs level field in the settings.
	 *
	 * @noinspection HtmlUnknownTarget
	 */
	function dicha_skroutz_feed_log_level_callback(): void {

		$current_val = get_option( 'dicha_skroutz_feed_log_level', 'minimal' );
		$options     = [
			'disabled' => __( 'No logging', 'xml-feed-for-skroutz-for-woocommerce' ),
			'minimal'  => __( 'Basic logging', 'xml-feed-for-skroutz-for-woocommerce' ),
			'full'     => __( 'Full logging', 'xml-feed-for-skroutz-for-woocommerce' ),
		];

		?>
		<!--suppress HtmlFormInputWithoutLabel -->
		<select id="dicha_skroutz_feed_log_level" name="dicha_skroutz_feed_log_level">
			<?php foreach ( $options as $key => $option ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $current_val === $key ); ?>>
					<?php echo esc_html( $option ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p class="desc">
			<?php /* translators: %s: Link to WooCommerce Logs page */ ?>
			<?php echo wp_kses_post( sprintf( __( 'You can find the logs in the <a href="%s" target="_blank">WooCommerce Logs</a> page.', 'xml-feed-for-skroutz-for-woocommerce' ), admin_url( 'admin.php?page=wc-status&tab=logs' ) ) ); ?>
			<br>
			<?php esc_html_e( 'Basic logging only shows info about feed generation.', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
			<br>
			<?php esc_html_e( 'Full logging also shows problematic products and the reasons for skipping them.', 'xml-feed-for-skroutz-for-woocommerce' ); ?>
		</p>
		<?php
	}


	
	/**
	 *************************************
	 ***** SETTINGS HELPER FUNCTIONS *****
	 *************************************
	 */

	/**
	 * Prepare attribute slug/labels for display in settings inputs.
	 *
	 * @return array
	 */
	function prepare_attributes_list(): array {

		if ( ! empty( $this->attributes_list ) ) return $this->attributes_list;

		$all_product_taxonomies = wp_list_pluck( get_object_taxonomies( 'product', 'objects' ), 'label', 'name' );
		$attribute_taxonomies   = wp_list_pluck( wc_get_attribute_taxonomies(), 'attribute_label', 'attribute_name' );
		$woo_native_taxonomies  = [
			'product_type',
			'product_visibility',
			'product_cat',
			'product_tag',
			'product_shipping_class'
		];

		$attribute_taxonomies_prefixed = array_map( function( $v ) { return 'pa_' . $v; }, array_keys( $attribute_taxonomies ) );
		$custom_taxonomies             = array_filter( $all_product_taxonomies, function( $tax_slug ) use ( $attribute_taxonomies_prefixed, $woo_native_taxonomies ) {
			return ! in_array( $tax_slug, array_merge( $attribute_taxonomies_prefixed, $woo_native_taxonomies ) );
		}, ARRAY_FILTER_USE_KEY );

		$this->attributes_list = [
			'attribute_taxonomies' => $attribute_taxonomies,
			'custom_taxonomies'    => $custom_taxonomies
		];

		return $this->attributes_list;
	}


	/**
	 * Returns an hierarchical list of the selected taxonomy.
	 *
	 * @param string $taxonomy The taxonomy slug.
	 *
	 * @return array
	 */
	function get_taxonomy_list_tree( string $taxonomy ): array {

		$taxonomy_terms_list = [];

		$parent_terms = get_terms( [
			'taxonomy'   => $taxonomy,
			'parent'     => 0,
			'hide_empty' => false,
			'orderby'    => 'name',
		] );

		$this->build_taxonomy_tree( $taxonomy_terms_list, $parent_terms, $taxonomy );

		return $taxonomy_terms_list;
	}


	/**
	 * Adds recursively taxonomy terms to the hierarchical list.
	 *
	 * @param array          $taxonomy_terms_list
	 * @param array|WP_Error $parent_terms
	 * @param string         $taxonomy
	 * @param int            $depth
	 * @param string         $separator
	 */
	function build_taxonomy_tree( array &$taxonomy_terms_list, $parent_terms, string $taxonomy, int $depth = 0, string $separator = '— ' ): void {

		if ( ! empty( $parent_terms ) && is_array( $parent_terms ) ) {

			foreach ( $parent_terms as $term ) {

				$term_id   = $term->term_id;
				$term_name = str_repeat( $separator, $depth ) . $term->name;

				$taxonomy_terms_list[ $term_id ] = $term_name;

				$child_terms = get_terms( [
					'taxonomy'   => $taxonomy,
					'parent'     => $term_id,
					'hide_empty' => false,
					'orderby'    => 'name',
				] );

				if ( ! empty( $child_terms ) && is_array( $child_terms ) ) {
					$this->build_taxonomy_tree( $taxonomy_terms_list, $child_terms, $taxonomy, $depth + 1, $separator );
				}
			}
		}
	}


	/**
	 * Returns an array with the availability options.
	 *
	 * @param $include_default bool If a default option is included in the beggining of the returned array.
	 * @param $for_admin       bool If true, a localized version of the options is returned for display inside admin UI, depending on admin lanuguage.
	 *                         If false, greek strings will be returned, ready for display in the XML.
	 *
	 * @return array
	 */
	public static function skroutz_get_availability_options( bool $include_default = false, bool $for_admin = false ): array {

		/*
		 * Do NOT ever change keys for backward compatibility reasons.
		 * Change only display labels if you need to (because Skroutz changed its XML requirements).
		 * Texts for admin interface are translatable, BUT texts for XML are NOT. XML texts are always in greek, just like Skroutz requires.
		 */
		$options = [
			'1' => $for_admin ? __( 'Available', 'xml-feed-for-skroutz-for-woocommerce' ) : 'Άμεσα διαθέσιμο',
			'2' => $for_admin ? __( 'Available from 1 to 3 days', 'xml-feed-for-skroutz-for-woocommerce' ) : 'Διαθέσιμο από 1 έως 3 ημέρες',
			'3' => $for_admin ? __( 'Available from 4 to 10 days', 'xml-feed-for-skroutz-for-woocommerce' ) : 'Διαθέσιμο από 4 έως 10 ημέρες',
			'4' => $for_admin ? __( 'Available up to 30 days', 'xml-feed-for-skroutz-for-woocommerce' ) : 'Διαθέσιμο από 10 έως 30 ημέρες',
			'5' => $for_admin ? __( 'Hide from XML', 'xml-feed-for-skroutz-for-woocommerce' ) : 'Απόκρυψη από το XML'
		];

		if ( $include_default ) {
			$default_option = [ '' => __( 'Default availability', 'xml-feed-for-skroutz-for-woocommerce' ) ];
			$options        = $default_option + $options;
		}

		return $options;
	}


	/**
	 * Get the default name for the feed upload folder, optionally customized by a filter.
	 *
	 * @return string The default folder name with any leading or trailing slashes removed.
	 */
	public static function get_default_feed_upload_folder_name(): string {

		$default_folder = 'dc-export-feeds';

		// remove slashes from start or end, if added by mistake
		return trim( trim( apply_filters( 'dicha_skroutz_feed_upload_folder', $default_folder ) ), '/\\' );
	}


	/**
	 * Generates and returns the filename for a feed, optionally customized by a filter.
	 *
	 * @param string $feed_type The type of feed, default is 'skroutz'.
	 *
	 * @return string The final feed filename without the .xml extension.
	 */
	public static function get_feed_filename( string $feed_type = 'skroutz' ): string {

		$filename_final = trim( apply_filters( 'dicha_skroutz_feed_custom_xml_filename', $feed_type ) );

		// remove .xml extension, if added by mistake
		return preg_replace( '/\.xml$/i', '', $filename_final );
	}


	/**
	 * Get the default file path for the feed.
	 *
	 * @return string The full path to the default feed directory.
	 */
	public static function get_default_feed_filepath(): string {

		$uploads_path = wp_upload_dir()['basedir'];
		$feed_folder  = self::get_default_feed_upload_folder_name();

		return trailingslashit( $uploads_path . '/' . $feed_folder );
	}


	/**
	 * Returns the default XML feed URL.
	 * This is used only in the manual XML update via the XML Feed Tools section.
	 *
	 * @return string
	 */
	private function get_default_xml_file_url(): string {

		$uploads_baseurl = wp_upload_dir()['baseurl'];
		$feed_folder     = self::get_default_feed_upload_folder_name();
		$feed_filename   = self::get_feed_filename();

		return $uploads_baseurl . '/' . $feed_folder . '/' . $feed_filename . '.xml';
	}



	/**
	 ****************************
	 ***** PRODUCT DATA TAB *****
	 ****************************
	 */

	/**
	 * Add a new product tab for Export Feeds settings.
	 *
	 * @param array $product_data_tabs
	 *
	 * @return array
	 */
	public function register_new_exports_tab( array $product_data_tabs ): array {

		$product_data_tabs['dicha_export_feeds_settings'] = [
			'label'  => __( 'Export Feeds', 'xml-feed-for-skroutz-for-woocommerce' ),
			'target' => 'dicha_export_feeds_settings_product_data',
			'class'    => [ 'hide_if_grouped', 'hide_if_external' ], // maybe add 'hide_if_virtual'
			'priority' => 75, // after advanced tab (70)
		];

		return $product_data_tabs;
	}


	/**
	 * Create Export Feeds settings tab content.
	 */
	public function print_exports_tab_content(): void {
		global $post;
		?>
		<div id='dicha_export_feeds_settings_product_data' class='panel woocommerce_options_panel'>
			<div class='options_group'>
				<?php

				woocommerce_wp_select( [
					'id'          => 'dicha_skroutz_feed_custom_availability',
					'label'       => __( 'Skroutz/BestPrice Availability', 'xml-feed-for-skroutz-for-woocommerce' ),
					'class'       => 'form-row-full',
					'desc_tip'    => true,
					'description' => __( 'The availability to show in XML feed for Skroutz/BestPrice. If you select the "Default availability" option, the default availability from the plugin\'s settings will be used.', 'xml-feed-for-skroutz-for-woocommerce' ),
					'options'     => self::skroutz_get_availability_options( true, true ),
					'value'       => get_post_meta( $post->ID, 'dicha_skroutz_feed_custom_availability', true ),
				] );

				do_action( 'dicha_export_feeds_settings_tab_fields_inside' );

				wp_nonce_field( 'dicha_skroutz_save_product_fields', 'dicha_skroutz_save_product_fields_nonce' );
				?>
			</div>
			<?php do_action( 'dicha_export_feeds_settings_tab_fields_after' ); ?>
		</div>
		<?php
	}


	/**
	 * Add a new custom field for EAN/Barcode under SKU in Inventory tab.
	 * Adds EAN field only if enabled from global settings.
	 *
	 */
	function add_ean_field_under_sku(): void {

		$default_ean_enabled = version_compare( $this->woo_version, '9.2', '>=' ) ? 'no' : 'yes';
		$dicha_skroutz_feed_enable_ean_field = get_option( 'dicha_skroutz_feed_enable_ean_field', $default_ean_enabled );

		if ( ! wc_string_to_bool( $dicha_skroutz_feed_enable_ean_field ) ) return;

		woocommerce_wp_text_input( [
			'id'          => 'dicha_skroutz_feed_ean_barcode',
			'class'       => 'form-row-full',
			'label'       => __( 'EAN/Barcode', 'xml-feed-for-skroutz-for-woocommerce' ),
			'value'       => get_post_meta( get_the_ID(), 'dicha_skroutz_feed_ean_barcode', true ),
			'desc_tip'    => true,
			'description' => __( 'The product\'s official EAN code or Barcode. A unique EAN/Barcode is allocated to each separate retail product.', 'xml-feed-for-skroutz-for-woocommerce' )
		] );
	}


	/**
	 * Save product custom fields
	 *
	 * @param int $post_id WP post id.
	 */
	function save_product_custom_fields( $post_id ): void {

		if ( empty( $_POST['dicha_skroutz_save_product_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dicha_skroutz_save_product_fields_nonce'] ) ), 'dicha_skroutz_save_product_fields' ) ) {
			return;
		}

		// Save EAN field only if enabled from global settings
		$default_ean_enabled = version_compare( $this->woo_version, '9.2', '>=' ) ? 'no' : 'yes';
		$dicha_skroutz_feed_enable_ean_field = get_option( 'dicha_skroutz_feed_enable_ean_field', $default_ean_enabled );

		if ( wc_string_to_bool( $dicha_skroutz_feed_enable_ean_field ) ) {

			$dicha_skroutz_feed_ean_barcode = sanitize_text_field( wp_unslash( $_POST['dicha_skroutz_feed_ean_barcode'] ?? '' ) );

			if ( ! empty( $dicha_skroutz_feed_ean_barcode ) ) {
				update_post_meta( $post_id, 'dicha_skroutz_feed_ean_barcode', wc_clean( $dicha_skroutz_feed_ean_barcode ) );
			}
			else {
				delete_post_meta( $post_id, 'dicha_skroutz_feed_ean_barcode' );
			}
		}

		$dicha_skroutz_feed_custom_availability = sanitize_text_field( wp_unslash( $_POST['dicha_skroutz_feed_custom_availability'] ?? '' ) );

		if ( ! empty( $dicha_skroutz_feed_custom_availability ) ) {
			update_post_meta( $post_id, 'dicha_skroutz_feed_custom_availability', wc_clean( $dicha_skroutz_feed_custom_availability ) );
		}
		else {
			delete_post_meta( $post_id, 'dicha_skroutz_feed_custom_availability' );
		}
	}


	/**
	 * Setup custom fields in product variations
	 *
	 * @param int     $loop           Position in the loop.
	 * @param array   $variation_data Variation data.
	 * @param WP_Post $variation      Post data.
	 */
	function print_variation_custom_fields( int $loop, array $variation_data, WP_Post $variation ): void {

		$default_ean_enabled = version_compare( $this->woo_version, '9.2', '>=' ) ? 'no' : 'yes';
		$dicha_skroutz_feed_enable_ean_field = get_option( 'dicha_skroutz_feed_enable_ean_field', $default_ean_enabled );

		if ( wc_string_to_bool( $dicha_skroutz_feed_enable_ean_field ) ) {

			echo '<div class="form-row form-row-full">';
			woocommerce_wp_text_input( [
				'id'          => "variable_dicha_skroutz_feed_ean_barcode_$loop",
				'name'        => "variable_dicha_skroutz_feed_ean_barcode[$loop]",
				'label'       => __( 'EAN/Barcode', 'xml-feed-for-skroutz-for-woocommerce' ),
				'desc_tip'    => true,
				'value'       => get_post_meta( $variation->ID, 'dicha_skroutz_feed_ean_barcode', true ),
				'description' => __( 'The variation\'s official EAN code or Barcode. A unique EAN/Barcode is allocated to each separate retail product.', 'xml-feed-for-skroutz-for-woocommerce' )
			] );
			echo '</div>';
		}

		echo '<div class="form-row form-row-full">';
		woocommerce_wp_select( [
			'id'          => "variable_dicha_skroutz_feed_custom_availability_$loop",
			'name'        => "variable_dicha_skroutz_feed_custom_availability[$loop]",
			'label'       => __( 'Skroutz/BestPrice Availability', 'xml-feed-for-skroutz-for-woocommerce' ),
			'desc_tip'    => true,
			'description' => __( 'The availability to show in XML feed for Skroutz/BestPrice. If you select the "Default availability" option, the default availability from the plugin\'s settings will be used.', 'xml-feed-for-skroutz-for-woocommerce' ),
			'options'     => self::skroutz_get_availability_options( true, true ),
			'value'       => get_post_meta( $variation->ID, 'dicha_skroutz_feed_custom_availability', true ),
		] );

		wp_nonce_field( 'dicha_skroutz_save_variation_fields', 'dicha_skroutz_save_variation_fields_nonce' );

		echo '</div>';
	}


	/**
	 * Save custom fields in variations.
	 *
	 * @param int $variation_id
	 * @param int $i
	 */
	function save_variation_custom_fields( int $variation_id, int $i ): void {

		if ( empty( $_POST['dicha_skroutz_save_variation_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dicha_skroutz_save_variation_fields_nonce'] ) ), 'dicha_skroutz_save_variation_fields' ) ) {
			return;
		}

		// Save EAN field only if enabled from global settings
		$default_ean_enabled = version_compare( $this->woo_version, '9.2', '>=' ) ? 'no' : 'yes';
		$dicha_skroutz_feed_enable_ean_field = get_option( 'dicha_skroutz_feed_enable_ean_field', $default_ean_enabled );

		if ( wc_string_to_bool( $dicha_skroutz_feed_enable_ean_field ) ) {

			$dicha_skroutz_feed_ean_barcode = sanitize_text_field( wp_unslash( $_POST['variable_dicha_skroutz_feed_ean_barcode'][ $i ] ?? '' ) );

			if ( ! empty( $dicha_skroutz_feed_ean_barcode ) ) {
				update_post_meta( $variation_id, 'dicha_skroutz_feed_ean_barcode', $dicha_skroutz_feed_ean_barcode );
			}
			else {
				delete_post_meta( $variation_id, 'dicha_skroutz_feed_ean_barcode' );
			}
		}

		$custom_availability = sanitize_text_field( wp_unslash( $_POST['variable_dicha_skroutz_feed_custom_availability'][ $i ] ?? '' ) );

		if ( ! empty( $custom_availability ) ) {
			update_post_meta( $variation_id, 'dicha_skroutz_feed_custom_availability', $custom_availability );
		}
		else {
			delete_post_meta( $variation_id, 'dicha_skroutz_feed_custom_availability' );
		}
	}



	/**
	 *******************************
	 ***** ADMIN PRODUCTS LIST *****
	 *******************************
	 */

	/**
	 * New custom column in admin products list for Skroutz Availability.
	 *
	 * @param string[] $columns An associative array of column headings.
	 *
	 * @return string[]
	 */
	public function register_availability_column( array $columns ): array {

		$columns['dicha_skroutz_feed_custom_availability'] = __( 'Skroutz availability', 'xml-feed-for-skroutz-for-woocommerce' );

		return $columns;
	}


	/**
	 * Fill Skroutz Availability column with content.
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int    $post_id     The current post ID.
	 */
	public function fill_availability_column( string $column_name, int $post_id ): void {

		if ( 'dicha_skroutz_feed_custom_availability' === $column_name ) {

			$availability_value   = get_post_meta( $post_id, 'dicha_skroutz_feed_custom_availability', true );
			$availability_options = self::skroutz_get_availability_options( true, true );
			$availability_text    = $availability_options[ $availability_value ] ?? array_shift( $availability_options );

			// echo display text
			echo esc_html( $availability_text );

			// echo hidden value for getting quick edit
			echo '<span class="hidden">' . esc_html( $availability_value ) . '</span>';
		}
	}

	
	/**
	 * Add a filter for Skroutz Availability in admin products list.
	 *
	 */
	public function print_custom_fields_filter_in_admin_list(): void {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		// nonce verification is handled by WordPress in post filters, so it's not needed when using the `restrict_manage_posts` hook
		global $typenow;

		// only add filter to products list
		if ( 'product' === $typenow ) {

			$availability_options = self::skroutz_get_availability_options( false, true );
			?>
			<select name="dicha_skroutz_availability" id="dropdown_dicha_skroutz_availability" aria-label="<?php esc_html_e( 'Skroutz availability filter', 'xml-feed-for-skroutz-for-woocommerce' ); ?>">
				<option value=""><?php esc_html_e( 'Filter by Skroutz Availability', 'xml-feed-for-skroutz-for-woocommerce' ); ?></option>
				<?php
				$current_v = isset( $_GET['dicha_skroutz_availability'] ) ? sanitize_text_field( wp_unslash( $_GET['dicha_skroutz_availability'] ) ) : '';

				foreach ( $availability_options as $avail_key => $avail_label ) :
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_html( $avail_key ),
						selected( $avail_key, $current_v ),
						esc_html( $avail_label )
					);
				endforeach; ?>
			</select>
			<?php
		}

		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}


	/**
	 * Filter Query for Product List for Skroutz Availability.
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 */
	public function filter_by_custom_fields_query_mod( WP_Query $query ): void {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		// nonce verification is handled by WordPress in post filters, so it's not needed when using the `restrict_manage_posts` hook
		global $pagenow;
		global $typenow;

		if ( 'product' === $typenow && is_admin() && 'edit.php' === $pagenow && isset( $_GET['dicha_skroutz_availability'] ) && $_GET['dicha_skroutz_availability'] != '' ) {

			// keep existing meta queries
			$meta_query = (array) $query->get( 'meta_query' );

			// add our meta query
			$meta_query[] = [
				'key'   => 'dicha_skroutz_feed_custom_availability',
				'value' => sanitize_text_field( wp_unslash( $_GET['dicha_skroutz_availability'] ) ),
			];

			$query->set( 'meta_query', $meta_query );
		}

		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}
	

	/**
	 * Add skroutz availability select field to quick edit box, along with other WooCommerce fields.
	 */
	function add_availability_field_to_quick_edit_box(): void {

		$availability_options = self::skroutz_get_availability_options( true, true );

		?>
		<br class="clear">
		<label class="alignleft dicha-skroutz-availability-field">
			<span class="title"><?php esc_html_e( 'Skroutz availability', 'xml-feed-for-skroutz-for-woocommerce' ); ?></span>
			<span class="input-text-wrap">
				<select class="dicha-skroutz-availability" name="dicha_skroutz_feed_custom_availability">
					<?php foreach ( $availability_options as $avail_key => $avail_label ) :
						printf(
							'<option value="%1$s">%2$s</option>',
							esc_html( $avail_key ),
							esc_html( $avail_label )
						);
					endforeach; ?>
				</select>
			</span>
		</label>
		<?php
	}


	/**
	 * Add skroutz availability select field to Bulk Edit box, along with other WooCommerce fields.
	 */
	function add_availability_field_to_bulk_edit_box(): void {

		$availability_options = self::skroutz_get_availability_options( true, true );
		?>
		<label class="alignleft dicha-skroutz-availability-field">
			<span class="title"><?php esc_html_e( 'Skroutz availability', 'xml-feed-for-skroutz-for-woocommerce' ); ?></span>
			<span class="input-text-wrap">
				<select class="dicha-skroutz-availability" name="change_dicha_skroutz_feed_custom_availability">
					<option value=""><?php esc_html_e( '— No Change —', 'xml-feed-for-skroutz-for-woocommerce' ) ?></option>
					<?php foreach ( $availability_options as $avail_key => $avail_label ) :
						printf(
							'<option value="%1$s">%2$s</option>',
							esc_html( $avail_key ?: 'restore_default' ), // temp value for default option because empty value is taken already
							esc_html( $avail_label )
						);
					endforeach; ?>
				</select>
			</span>
		</label>
		<?php
	}


	/**
	 * Save skroutz availability field for quick edit and bulk edit actions.
	 *
	 * @param int $post_id
	 */
	function quick_and_bulk_edit_save_availability( int $post_id ): void {

		// check user capabilities
		if ( ! current_user_can( 'manage_woocommerce', $post_id ) ) return;

		// if quick edit save
		if ( isset( $_REQUEST['_inline_edit'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_inline_edit'] ) ), 'inlineeditnonce' ) ) {

			if ( isset( $_REQUEST['dicha_skroutz_feed_custom_availability'] ) ) {
				update_post_meta( $post_id, 'dicha_skroutz_feed_custom_availability', sanitize_text_field( wp_unslash( $_REQUEST['dicha_skroutz_feed_custom_availability'] ) ) );
			}
		}
		// if bulk edit save
		elseif ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'bulk-posts' ) ) {

			if ( isset( $_REQUEST['change_dicha_skroutz_feed_custom_availability'] ) ) {

				if ( $_REQUEST['change_dicha_skroutz_feed_custom_availability'] === 'restore_default' ) {
					delete_post_meta( $post_id, 'dicha_skroutz_feed_custom_availability' );
				}
				elseif ( ! empty( $_REQUEST['change_dicha_skroutz_feed_custom_availability'] ) ) { // empty value means "No change" was selected
					update_post_meta( $post_id, 'dicha_skroutz_feed_custom_availability', sanitize_text_field( wp_unslash( $_REQUEST['change_dicha_skroutz_feed_custom_availability'] ) ) );
				}
			}
		}
	}



	/**
	 ****************
	 ***** MISC *****
	 ****************
	 */

	/**
	 * Add Settings link in plugin page.
	 *
	 * @param array  $actions     The actions array.
	 * @param string $plugin_file Path to the plugin file relative to the 'plugins' directory.
	 *
	 * @return array The actions array.
	 */
	public function add_plugin_action_links( array $actions, string $plugin_file ): array {

		if ( $plugin_file === DICHA_SKROUTZ_FEED_BASE_FILE ) {

			$settings_link = [
				'settings' => sprintf( '<a href="%1$s">%2$s</a>',
					esc_url( admin_url( 'admin.php?page=' . DICHA_SKROUTZ_FEED_SLUG ) ),
					esc_html__( 'Settings', 'xml-feed-for-skroutz-for-woocommerce' )
				)
			];

			$actions = array_merge( $settings_link, $actions );
		}

		return $actions;
	}



	/**
	 ******************************
	 ***** WC COMPATIBILITIES *****
	 ******************************
	 */

	/**
	 * Declare compatibility with WooCommerce Features (HPOS, Cart & Checkout Blocks)
	 *
	 * @return void
	 * @noinspection PhpFullyQualifiedNameUsageInspection
	 */
	function declare_compatibility_with_wc_features(): void {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', DICHA_SKROUTZ_FEED_FILE );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', DICHA_SKROUTZ_FEED_FILE );
		}
	}



	/**
	 ***************************
	 ***** ASSETS ENQUEUES *****
	 ***************************
	 */

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param string $hook The current admin page
	 */
	public function enqueue_styles( string $hook ): void {
		global $typenow;

		$env_type               = wp_get_environment_type();
		$assets_version         = in_array( $env_type, [ 'local', 'development' ] ) ? $this->version . time() : $this->version;
		$is_settings_page       = 'digital-challenge__page_' . DICHA_SKROUTZ_FEED_SLUG === $hook || 'digital-challenge_page_' . DICHA_SKROUTZ_FEED_SLUG === $hook;
		$is_products_admin_list = 'edit.php' === $hook && 'product' === $typenow;
		$is_product_edit_page   = ( 'post-new.php' === $hook || 'post.php' === $hook ) && 'product' === $typenow;

		if ( $is_products_admin_list ) {

			wp_enqueue_style( 'dc-skroutz-product-list', DICHA_SKROUTZ_FEED_PLUGIN_DIR_URL . 'admin/css/dc-skroutz-feed-product-list.css', [], $assets_version );
		}
		else if ( $is_settings_page ) {

			wp_enqueue_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css', [], WC()->version );
			wp_enqueue_style( 'dc-skroutz-admin-settings', DICHA_SKROUTZ_FEED_PLUGIN_DIR_URL . 'admin/css/dc-skroutz-feed-admin.css', [], $assets_version );
		}
		elseif( $is_product_edit_page ) {

			wp_enqueue_style( 'dc-skroutz-product-edit', DICHA_SKROUTZ_FEED_PLUGIN_DIR_URL . 'admin/css/dc-skroutz-feed-product-edit.css', [], $assets_version );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook The current admin page
	 */
	public function enqueue_scripts( string $hook ): void {
		global $typenow;

		$env_type               = wp_get_environment_type();
		$assets_version         = in_array( $env_type, [ 'local', 'development' ] ) ? $this->version . time() : $this->version;
		$is_settings_page       = 'digital-challenge__page_' . DICHA_SKROUTZ_FEED_SLUG === $hook || 'digital-challenge_page_' . DICHA_SKROUTZ_FEED_SLUG === $hook;
		$is_products_admin_list = 'edit.php' === $hook && 'product' === $typenow;

		if ( $is_settings_page ) {

			wp_enqueue_script( 'dc-skroutz-admin-settings', DICHA_SKROUTZ_FEED_PLUGIN_DIR_URL . 'admin/js/dc-skroutz-feed-admin.js', [ 'jquery', 'selectWoo' ], $assets_version, [ 'strategy' => 'defer' ] );
		}
		elseif ( $is_products_admin_list ) {

			wp_enqueue_script( 'dc-skroutz-quick-edit', DICHA_SKROUTZ_FEED_PLUGIN_DIR_URL . 'admin/js/dc-skroutz-feed-quick-bulk-edit.js', [ 'jquery' ], $assets_version, [ 'strategy' => 'defer' ] );
		}
	}

}