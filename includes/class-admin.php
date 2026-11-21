<?php
/**
 * Settings screen (Appearance > Veila) with a live preview.
 *
 * @package Veila
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin UI.
 */
class Veila_Admin {

	/**
	 * Hook the menu and assets.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_filter( 'wp_redirect', array( __CLASS__, 'preserve_tab_on_redirect' ) );
		add_action( 'admin_post_veila_apply_starter', array( __CLASS__, 'apply_starter' ) );
		add_action( 'wp_ajax_veila_dismiss_upsell', array( __CLASS__, 'dismiss_upsell' ) );
		add_action( 'admin_notices', array( __CLASS__, 'notices' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( VEILA_FILE ), array( __CLASS__, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'row_meta' ), 10, 2 );
	}

	/**
	 * Menu label (white label can rename it).
	 *
	 * @return string
	 */
	public static function menu_label() {
		/**
		 * Filter the Appearance submenu label and page title.
		 *
		 * @param string $label Label.
		 */
		return (string) apply_filters( 'veila_menu_label', __( 'Sanctoria Veila', 'sanctoria-veila' ) );
	}

	/**
	 * Add Appearance > Veila.
	 */
	public static function menu() {
		add_theme_page(
			self::menu_label(),
			self::menu_label(),
			'manage_options',
			'veila',
			array( __CLASS__, 'render' )
		);
	}

	/**
	 * Settings URL for Appearance > Veila.
	 *
	 * @return string
	 */
	public static function settings_url() {
		return admin_url( 'themes.php?page=veila' );
	}

	/**
	 * Add a Settings link under the plugin name, before Deactivate.
	 *
	 * @param array $links Action links.
	 * @return array
	 */
	public static function action_links( $links ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $links;
		}
		$settings = '<a href="' . esc_url( self::settings_url() ) . '">' . esc_html__( 'Settings', 'sanctoria-veila' ) . '</a>';
		array_unshift( $links, $settings );
		return $links;
	}

	/**
	 * Add a Settings link on the plugin row meta line.
	 *
	 * @param array  $links Row meta links.
	 * @param string $file  Plugin basename.
	 * @return array
	 */
	public static function row_meta( $links, $file ) {
		if ( plugin_basename( VEILA_FILE ) !== $file ) {
			return $links;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return $links;
		}
		$links[] = '<a href="' . esc_url( self::settings_url() ) . '">' . esc_html__( 'Settings', 'sanctoria-veila' ) . '</a>';
		return $links;
	}

	/**
	 * Enqueue settings assets.
	 *
	 * @param string $hook Hook.
	 */
	public static function assets( $hook ) {
		if ( 'appearance_page_veila' !== $hook ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		$splash = VEILA_DIR . 'assets/css/splash.css';
		$css    = VEILA_DIR . 'admin/css/admin.css';
		$js     = VEILA_DIR . 'admin/js/admin.js';
		wp_enqueue_style( 'veila-splash', VEILA_URL . 'assets/css/splash.css', array(), file_exists( $splash ) ? (string) filemtime( $splash ) : VEILA_VERSION );
		wp_enqueue_style( 'veila-admin', VEILA_URL . 'admin/css/admin.css', array( 'wp-color-picker', 'veila-splash' ), file_exists( $css ) ? (string) filemtime( $css ) : VEILA_VERSION );
		wp_enqueue_script( 'veila-admin', VEILA_URL . 'admin/js/admin.js', array( 'jquery', 'wp-color-picker' ), file_exists( $js ) ? (string) filemtime( $js ) : VEILA_VERSION, true );

		$settings = Veila_Settings::get();
		$bg       = $settings['bg_image_id'] ? wp_get_attachment_image_url( (int) $settings['bg_image_id'], 'full' ) : '';
		$stacks   = array();
		foreach ( Veila_Settings::font_stacks() as $slug => $font ) {
			$stacks[ $slug ] = array(
				'stack' => $font['stack'],
				'head'  => isset( $font['head'] ) ? $font['head'] : $font['stack'],
			);
		}
		wp_localize_script(
			'veila-admin',
			'veilaAdmin',
			array(
				'bgUrl'       => $bg ? $bg : '',
				'stacks'      => $stacks,
				'badges'      => array(
					'coming_soon' => Veila_Render::default_badge( 'coming_soon' ),
					'maintenance' => Veila_Render::default_badge( 'maintenance' ),
				),
				'mediaTitle'  => __( 'Select image', 'sanctoria-veila' ),
				'mediaButton' => __( 'Use this image', 'sanctoria-veila' ),
				'contrastLow' => __( 'Low contrast: Sanctoria Veila will use a more readable text color on the live page.', 'sanctoria-veila' ),
				'contrastOk'  => __( 'Text contrast passes WCAG AA.', 'sanctoria-veila' ),
				'freeTabs'    => array_keys( self::free_tabs() ),
			)
		);
	}

	/**
	 * Free tabs.
	 *
	 * @return array
	 */
	public static function free_tabs() {
		return array(
			'status'   => __( 'Status', 'sanctoria-veila' ),
			'design'   => __( 'Design', 'sanctoria-veila' ),
			'content'  => __( 'Content', 'sanctoria-veila' ),
			'seo'      => __( 'SEO & social', 'sanctoria-veila' ),
			'advanced' => __( 'Advanced', 'sanctoria-veila' ),
		);
	}

	/**
	 * Apply a built-in starter template.
	 */
	public static function apply_starter() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'sanctoria-veila' ) );
		}
		check_admin_referer( 'veila_apply_starter' );
		$key      = isset( $_GET['starter'] ) ? sanitize_key( wp_unslash( $_GET['starter'] ) ) : '';
		$starters = Veila_Settings::starters();
		if ( ! isset( $starters[ $key ] ) ) {
			wp_safe_redirect( self::settings_url() );
			exit;
		}
		$current = Veila_Settings::get();
		$next    = array_merge( $current, $starters[ $key ] );
		unset( $next['label'] );
		$next['template'] = $key;
		update_option( Veila_Settings::OPTION, Veila_Settings::sanitize( $next ) );
		wp_safe_redirect( add_query_arg( 'veila_msg', 'starter', self::settings_url() ) . '#veila-tab-design' );
		exit;
	}

	/**
	 * Dismiss the Pro note (per user).
	 */
	public static function dismiss_upsell() {
		check_ajax_referer( 'veila_dismiss_upsell', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( null, 403 );
		}
		update_user_meta( get_current_user_id(), 'veila_upsell_dismissed', 1 );
		wp_send_json_success();
	}

	/**
	 * Notices: kill switch on.
	 */
	public static function notices() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( Veila_Gate::kill_switch() ) {
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'Sanctoria Veila is switched off by VEILA_DISABLE in wp-config.php. Visitors see the normal site until that line is removed.', 'sanctoria-veila' ) . '</p></div>';
		}
	}

	/**
	 * Render the settings page.
	 */
	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$s        = Veila_Settings::get();
		$starters = Veila_Settings::starters();
		$tabs     = self::free_tabs();

		/**
		 * Filter settings tabs. Pro adds more.
		 *
		 * @param array $tabs Tabs.
		 */
		$tabs   = apply_filters( 'veila_settings_tabs', $tabs );
		$active = 'status';
		$mode   = Veila_Gate::effective_mode();
		$msg    = isset( $_GET['veila_msg'] ) ? sanitize_key( wp_unslash( $_GET['veila_msg'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		?>
		<div class="wrap veila-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php if ( 'starter' === $msg ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Template applied. You can keep editing below.', 'sanctoria-veila' ); ?></p></div>
			<?php endif; ?>
			<?php
			/**
			 * Fires after the page title (Pro prints its own notices here).
			 *
			 * @param string $msg Message slug from the URL.
			 */
			do_action( 'veila_settings_notices', $msg );
			?>
			<p class="veila-lead"><?php esc_html_e( 'Show visitors a coming-soon or maintenance page while you work. Logged-in administrators keep seeing the real site. Nothing is sent to an external service.', 'sanctoria-veila' ); ?></p>

			<div class="veila-status-strip veila-status-strip-<?php echo esc_attr( $mode ); ?>">
				<strong><?php esc_html_e( 'Right now:', 'sanctoria-veila' ); ?></strong>
				<?php
				if ( 'maintenance' === $mode ) {
					esc_html_e( 'Maintenance mode is on. Visitors get HTTP 503 and the splash page.', 'sanctoria-veila' );
				} elseif ( 'coming_soon' === $mode ) {
					esc_html_e( 'Coming soon mode is on. Visitors see the splash page.', 'sanctoria-veila' );
				} else {
					esc_html_e( 'Sanctoria Veila is off. Visitors see the normal site.', 'sanctoria-veila' );
				}
				?>
			</div>

			<div class="veila-starters">
				<span><?php esc_html_e( 'Start from a template:', 'sanctoria-veila' ); ?></span>
				<?php foreach ( $starters as $key => $starter ) : ?>
					<a class="button<?php echo $s['template'] === $key ? ' veila-current' : ''; ?>" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'veila_apply_starter', 'starter' => $key ), admin_url( 'admin-post.php' ) ), 'veila_apply_starter' ) ); ?>"><?php echo esc_html( $starter['label'] ); ?></a>
				<?php endforeach; ?>
				<?php do_action( 'veila_after_starters' ); ?>
			</div>

			<div class="veila-layout">
				<div class="veila-main-col">
					<nav class="veila-tabs" aria-label="<?php esc_attr_e( 'Settings sections', 'sanctoria-veila' ); ?>">
						<?php foreach ( $tabs as $id => $label ) : ?>
							<a href="#veila-tab-<?php echo esc_attr( $id ); ?>" class="veila-tab<?php echo $active === $id ? ' is-active' : ''; ?>" data-tab="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></a>
						<?php endforeach; ?>
					</nav>
					<form method="post" action="options.php" class="veila-form">
						<?php settings_fields( 'veila' ); ?>
						<input type="hidden" name="veila_active_tab" class="veila-active-tab" value="<?php echo esc_attr( $active ); ?>" />
						<input type="hidden" name="<?php echo esc_attr( Veila_Settings::OPTION ); ?>[template]" value="<?php echo esc_attr( $s['template'] ); ?>" />

						<div id="veila-tab-status" class="veila-panel is-active">
							<?php self::field_radio( 'mode', __( 'Mode', 'sanctoria-veila' ), $s['mode'], Veila_Settings::modes() ); ?>
							<p class="description veila-mode-help"><?php esc_html_e( 'Coming soon is for a site that has not launched yet. Maintenance is for short planned work on a live site: it tells search engines to come back later instead of dropping your pages.', 'sanctoria-veila' ); ?></p>
							<?php self::field_checkbox( 'noindex', __( 'Ask search engines not to index the coming-soon page (noindex)', 'sanctoria-veila' ), $s['noindex'] ); ?>
							<?php self::field_number( 'retry_after', __( 'Maintenance Retry-After (minutes)', 'sanctoria-veila' ), $s['retry_after'], 1, 10080, __( 'Sent with HTTP 503 so search engines know when to try again.', 'sanctoria-veila' ) ); ?>
							<?php self::field_select( 'bypass_cap', __( 'Who sees the real site', 'sanctoria-veila' ), $s['bypass_cap'], Veila_Settings::bypass_caps() ); ?>
							<div class="veila-info">
								<p><strong><?php esc_html_e( 'Always reachable:', 'sanctoria-veila' ); ?></strong> <?php esc_html_e( 'the dashboard, wp-login.php, the REST API, wp-cron.php, admin-ajax.php, XML-RPC, robots.txt, and WP-CLI. Feeds and sitemaps get a short plain-text reply (503 in maintenance, 404 while coming soon).', 'sanctoria-veila' ); ?></p>
								<p><strong><?php esc_html_e( 'Emergency off switch:', 'sanctoria-veila' ); ?></strong> <?php esc_html_e( 'add this line to wp-config.php to turn Veila off without logging in:', 'sanctoria-veila' ); ?> <code>define( 'VEILA_DISABLE', true );</code></p>
							</div>
						</div>

						<div id="veila-tab-design" class="veila-panel">
							<?php self::field_select( 'layout', __( 'Layout', 'sanctoria-veila' ), $s['layout'], Veila_Settings::layouts() ); ?>
							<?php self::field_media( 'logo_id', __( 'Logo (for light backgrounds)', 'sanctoria-veila' ), $s['logo_id'] ); ?>
							<?php self::field_media( 'logo_id_dark', __( 'Logo (for dark backgrounds)', 'sanctoria-veila' ), $s['logo_id_dark'] ); ?>
							<p class="description veila-under"><?php esc_html_e( 'Use a transparent PNG or SVG. Veila picks the dark-background logo automatically when the page behind the text is dark.', 'sanctoria-veila' ); ?></p>
							<?php self::field_number( 'logo_width', __( 'Logo width (px)', 'sanctoria-veila' ), $s['logo_width'], 32, 480 ); ?>
							<?php self::field_select( 'bg_type', __( 'Background type', 'sanctoria-veila' ), $s['bg_type'], array( 'color' => __( 'Solid color', 'sanctoria-veila' ), 'gradient' => __( 'Gradient', 'sanctoria-veila' ), 'image' => __( 'Image', 'sanctoria-veila' ) ) ); ?>
							<?php self::field_color( 'bg_color', __( 'Background color', 'sanctoria-veila' ), $s['bg_color'] ); ?>
							<?php self::field_color( 'bg_color_2', __( 'Gradient second color', 'sanctoria-veila' ), $s['bg_color_2'] ); ?>
							<?php self::field_number( 'bg_gradient_angle', __( 'Gradient angle', 'sanctoria-veila' ), $s['bg_gradient_angle'], 0, 360 ); ?>
							<?php self::field_media( 'bg_image_id', __( 'Background image', 'sanctoria-veila' ), $s['bg_image_id'] ); ?>
							<?php self::field_number( 'overlay_opacity', __( 'Image darkening (%)', 'sanctoria-veila' ), $s['overlay_opacity'], 0, 90, __( 'Darkens a full-screen background image so text stays readable.', 'sanctoria-veila' ) ); ?>
							<?php self::field_color( 'heading_color', __( 'Headline color', 'sanctoria-veila' ), $s['heading_color'] ); ?>
							<?php self::field_color( 'text_color', __( 'Text color', 'sanctoria-veila' ), $s['text_color'] ); ?>
							<?php self::field_color( 'accent_color', __( 'Accent color', 'sanctoria-veila' ), $s['accent_color'] ); ?>
							<?php self::field_color( 'card_bg', __( 'Card background (Card layout)', 'sanctoria-veila' ), $s['card_bg'] ); ?>
							<?php self::field_select( 'font_stack', __( 'Typeface', 'sanctoria-veila' ), $s['font_stack'], wp_list_pluck( Veila_Settings::font_stacks(), 'label' ) ); ?>
							<?php self::field_number( 'heading_size', __( 'Headline size (px)', 'sanctoria-veila' ), $s['heading_size'], 20, 96 ); ?>
							<?php self::field_select( 'text_align', __( 'Text alignment', 'sanctoria-veila' ), $s['text_align'], array( 'center' => __( 'Centered', 'sanctoria-veila' ), 'left' => __( 'Left', 'sanctoria-veila' ) ) ); ?>
							<p class="description"><?php esc_html_e( 'Typefaces use fonts already on the visitor’s device. Nothing is loaded from Google or any other website.', 'sanctoria-veila' ); ?></p>
						</div>

						<div id="veila-tab-content" class="veila-panel">
							<?php self::field_checkbox( 'show_badge', __( 'Show the small label above the headline', 'sanctoria-veila' ), $s['show_badge'] ); ?>
							<?php self::field_text( 'badge', __( 'Label text', 'sanctoria-veila' ), $s['badge'], __( 'Leave empty for “Coming soon” or “Down for maintenance”', 'sanctoria-veila' ) ); ?>
							<?php self::field_text( 'headline', __( 'Headline', 'sanctoria-veila' ), $s['headline'] ); ?>
							<?php self::field_textarea( 'message', __( 'Message', 'sanctoria-veila' ), $s['message'], __( 'Plain text. Links, bold, and italics are allowed.', 'sanctoria-veila' ), 'large-text', 5 ); ?>
							<?php self::field_text( 'footer_text', __( 'Footer text', 'sanctoria-veila' ), $s['footer_text'], '© ' . gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?>
							<?php self::field_checkbox( 'show_login_link', __( 'Show a “Log in” link in the footer', 'sanctoria-veila' ), $s['show_login_link'] ); ?>
						</div>

						<div id="veila-tab-seo" class="veila-panel">
							<?php self::field_text( 'seo_title', __( 'Page title', 'sanctoria-veila' ), $s['seo_title'], get_bloginfo( 'name' ) . ' — ' . __( 'Coming soon', 'sanctoria-veila' ) ); ?>
							<?php self::field_textarea( 'seo_description', __( 'Meta description', 'sanctoria-veila' ), $s['seo_description'], __( 'Shown by search engines and link previews. About 150 characters.', 'sanctoria-veila' ), 'large-text', 3 ); ?>
							<p class="description veila-under"><?php esc_html_e( 'The favicon comes from your Site Icon (Settings > General).', 'sanctoria-veila' ); ?></p>
							<h2 class="veila-subhead"><?php esc_html_e( 'Social links', 'sanctoria-veila' ); ?></h2>
							<?php
							foreach ( Veila_Settings::socials() as $net => $label ) {
								self::field_text( 'social_' . $net, $label, $s[ 'social_' . $net ], 'email' === $net ? 'hello@example.com' : 'https://', 'email' === $net ? 'email' : 'url' );
							}
							?>
						</div>

						<div id="veila-tab-advanced" class="veila-panel">
							<?php self::field_textarea( 'custom_css', __( 'Custom CSS', 'sanctoria-veila' ), $s['custom_css'], __( 'Added only on the splash page. Do not paste scripts here.', 'sanctoria-veila' ), 'large-text code', 8 ); ?>
							<?php self::field_checkbox( 'show_credit', __( 'Show a small “Made with Veila” note in the footer (off by default)', 'sanctoria-veila' ), $s['show_credit'] ); ?>
							<div class="veila-info">
								<p><strong><?php esc_html_e( 'Caching:', 'sanctoria-veila' ); ?></strong> <?php esc_html_e( 'The splash page is sent with no-cache headers, and Veila asks common cache plugins (LiteSpeed, WP Rocket, W3 Total Cache, WP Super Cache, SiteGround, Breeze, Cache Enabler, WP Fastest Cache, Nginx Helper) to purge whenever you save. If you use a CDN, purge it after switching modes.', 'sanctoria-veila' ); ?></p>
								<p><a class="button" href="<?php echo esc_url( Veila_Gate::preview_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open the real splash page (preview)', 'sanctoria-veila' ); ?></a></p>
							</div>
						</div>

						<?php submit_button( __( 'Save changes', 'sanctoria-veila' ) ); ?>
					</form>

					<?php
					// Pro panels render after this form so they can include their own <form> tags.
					do_action( 'veila_settings_panels', $s );
					?>
				</div>

				<div class="veila-preview-col">
					<div class="veila-preview-toolbar">
						<strong><?php esc_html_e( 'Live preview', 'sanctoria-veila' ); ?></strong>
						<a href="<?php echo esc_url( Veila_Gate::preview_url() ); ?>" target="_blank" rel="noopener noreferrer" id="veila-open-preview"><?php esc_html_e( 'Open full page', 'sanctoria-veila' ); ?></a>
					</div>
					<div class="veila-preview" id="veila-preview">
						<?php Veila_Render::markup( $s, 'maintenance' === $s['mode'] ? 'maintenance' : 'coming_soon', 'preview' ); ?>
					</div>
					<p class="veila-contrast" id="veila-contrast" aria-live="polite"></p>
				</div>
			</div>

			<?php
			$dismissed = get_user_meta( get_current_user_id(), 'veila_upsell_dismissed', true );
			if ( ! $dismissed && ! defined( 'VEILA_PRO_VERSION' ) ) :
				?>
				<div class="veila-upsell" id="veila-upsell">
					<p><?php esc_html_e( 'Veila Pro adds a template library, a countdown that can launch your site on time, an on-site email signup list with CSV export, secret preview links for clients, role and IP access, page rules, scheduling, video backgrounds, local fonts, and white-label tools. It is a one-time purchase: Veila Pro ($20, one site, three years of updates), Veila Pro Trinity ($50, three sites, lifetime updates), or Veila Pro Infinity ($99, unlimited sites, lifetime updates). The free plugin stays fully usable on its own.', 'sanctoria-veila' ); ?></p>
					<button type="button" class="button-link" id="veila-dismiss-upsell" data-nonce="<?php echo esc_attr( wp_create_nonce( 'veila_dismiss_upsell' ) ); ?>"><?php esc_html_e( 'Dismiss', 'sanctoria-veila' ); ?></button>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Keep the open settings tab in the URL after the free form is saved.
	 *
	 * @param string $location Redirect location.
	 * @return string
	 */
	public static function preserve_tab_on_redirect( $location ) {
		if ( ! is_string( $location ) || false === strpos( $location, 'page=veila' ) ) {
			return $location;
		}
		if ( empty( $_POST['veila_active_tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return $location;
		}
		$tab = sanitize_key( wp_unslash( $_POST['veila_active_tab'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $tab && false === strpos( $location, '#veila-tab-' ) ) {
			$location .= '#veila-tab-' . $tab;
		}
		return $location;
	}

	/**
	 * Field name attribute.
	 *
	 * @param string $name Key.
	 * @return string
	 */
	private static function name( $name ) {
		return Veila_Settings::OPTION . '[' . $name . ']';
	}

	/**
	 * Color field.
	 *
	 * @param string $name  Name.
	 * @param string $label Label.
	 * @param string $value Value.
	 */
	private static function field_color( $name, $label, $value ) {
		self::row(
			$label,
			sprintf(
				'<input type="text" class="veila-color" name="%1$s" id="%2$s" value="%3$s" data-default-color="" />',
				esc_attr( self::name( $name ) ),
				esc_attr( $name ),
				esc_attr( $value )
			),
			$name
		);
	}

	/**
	 * Text field.
	 *
	 * @param string $name        Name.
	 * @param string $label       Label.
	 * @param string $value       Value.
	 * @param string $placeholder Placeholder.
	 * @param string $type        Input type.
	 */
	private static function field_text( $name, $label, $value, $placeholder = '', $type = 'text' ) {
		self::row(
			$label,
			sprintf(
				'<input type="%5$s" class="regular-text" name="%1$s" id="%2$s" value="%3$s" placeholder="%4$s" />',
				esc_attr( self::name( $name ) ),
				esc_attr( $name ),
				esc_attr( $value ),
				esc_attr( $placeholder ),
				esc_attr( $type )
			),
			$name
		);
	}

	/**
	 * Number field.
	 *
	 * @param string $name  Name.
	 * @param string $label Label.
	 * @param mixed  $value Value.
	 * @param int    $min   Min.
	 * @param int    $max   Max.
	 * @param string $help  Help.
	 */
	private static function field_number( $name, $label, $value, $min, $max, $help = '' ) {
		$html = sprintf(
			'<input type="number" class="small-text" name="%1$s" id="%2$s" value="%3$s" min="%4$d" max="%5$d" />',
			esc_attr( self::name( $name ) ),
			esc_attr( $name ),
			esc_attr( $value ),
			(int) $min,
			(int) $max
		);
		if ( $help ) {
			$html .= '<p class="description">' . esc_html( $help ) . '</p>';
		}
		self::row( $label, $html, $name );
	}

	/**
	 * Checkbox field.
	 *
	 * @param string $name  Name.
	 * @param string $label Label.
	 * @param mixed  $value Value.
	 */
	private static function field_checkbox( $name, $label, $value ) {
		printf(
			'<p class="veila-check"><label><input type="checkbox" name="%1$s" id="%2$s" value="1" %3$s /> %4$s</label></p>',
			esc_attr( self::name( $name ) ),
			esc_attr( $name ),
			checked( 1, (int) $value, false ),
			esc_html( $label )
		);
	}

	/**
	 * Select field.
	 *
	 * @param string $name    Name.
	 * @param string $label   Label.
	 * @param string $value   Value.
	 * @param array  $options Options.
	 */
	private static function field_select( $name, $label, $value, $options ) {
		$html = sprintf( '<select name="%1$s" id="%2$s">', esc_attr( self::name( $name ) ), esc_attr( $name ) );
		foreach ( $options as $k => $lab ) {
			$html .= '<option value="' . esc_attr( $k ) . '" ' . selected( $value, $k, false ) . '>' . esc_html( $lab ) . '</option>';
		}
		$html .= '</select>';
		self::row( $label, $html, $name );
	}

	/**
	 * Radio group.
	 *
	 * @param string $name    Name.
	 * @param string $label   Label.
	 * @param string $value   Value.
	 * @param array  $options Options.
	 */
	private static function field_radio( $name, $label, $value, $options ) {
		$html = '<fieldset class="veila-radios" id="' . esc_attr( $name ) . '"><legend class="screen-reader-text">' . esc_html( $label ) . '</legend>';
		foreach ( $options as $k => $lab ) {
			$html .= sprintf(
				'<label class="veila-radio veila-radio-%4$s"><input type="radio" name="%1$s" value="%2$s" %3$s /> <span>%5$s</span></label>',
				esc_attr( self::name( $name ) ),
				esc_attr( $k ),
				checked( $value, $k, false ),
				esc_attr( $k ),
				esc_html( $lab )
			);
		}
		$html .= '</fieldset>';
		self::row( $label, $html );
	}

	/**
	 * Textarea.
	 *
	 * @param string $name  Name.
	 * @param string $label Label.
	 * @param string $value Value.
	 * @param string $help  Help.
	 * @param string $class CSS class.
	 * @param int    $rows  Rows.
	 */
	private static function field_textarea( $name, $label, $value, $help = '', $class = 'large-text code', $rows = 6 ) {
		$html = sprintf(
			'<textarea name="%1$s" id="%2$s" class="%4$s" rows="%5$d">%3$s</textarea>',
			esc_attr( self::name( $name ) ),
			esc_attr( $name ),
			esc_textarea( $value ),
			esc_attr( $class ),
			(int) $rows
		);
		if ( $help ) {
			$html .= '<p class="description">' . esc_html( $help ) . '</p>';
		}
		self::row( $label, $html, $name );
	}

	/**
	 * Media picker.
	 *
	 * @param string $name  Name.
	 * @param string $label Label.
	 * @param int    $value Attachment ID.
	 */
	private static function field_media( $name, $label, $value ) {
		$url  = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
		$full = $value ? wp_get_attachment_image_url( (int) $value, 'full' ) : '';
		$html = sprintf(
			'<div class="veila-media-field" data-target="%1$s"%4$s><input type="hidden" name="%2$s" id="%1$s" value="%3$s" />',
			esc_attr( $name ),
			esc_attr( self::name( $name ) ),
			esc_attr( $value ? $value : '' ),
			$full ? ' data-url="' . esc_url( $full ) . '"' : ''
		);
		$html .= '<div class="veila-media-preview">';
		if ( $url ) {
			$html .= '<img src="' . esc_url( $url ) . '" alt="" />';
		}
		$html .= '</div>';
		$html .= '<button type="button" class="button veila-media-select">' . esc_html__( 'Choose image', 'sanctoria-veila' ) . '</button> ';
		$html .= '<button type="button" class="button-link veila-media-clear">' . esc_html__( 'Remove', 'sanctoria-veila' ) . '</button></div>';
		self::row( $label, $html, $name );
	}

	/**
	 * Settings row.
	 *
	 * @param string $label Label.
	 * @param string $field Field HTML (already escaped by callers).
	 * @param string $for   Field id for the label.
	 */
	private static function row( $label, $field, $for = '' ) {
		echo '<div class="veila-row"><div class="veila-row-label"><label' . ( $for ? ' for="' . esc_attr( $for ) . '"' : '' ) . '>' . esc_html( $label ) . '</label></div><div class="veila-row-field">' . $field . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Field HTML is escaped by each caller.
	}
}
