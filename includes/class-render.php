<?php
/**
 * Splash page markup. The same markup is used on the front end and in the
 * live preview on the settings page, so they always match.
 *
 * @package Veila
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renderer.
 */
class Veila_Render {

	/**
	 * Social icon paths (24×24). Brand glyphs from Simple Icons (CC0).
	 *
	 * @return array
	 */
	public static function icon_paths() {
		return array(
			'facebook' => 'M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z',
			'x' => 'M14.234 10.162 22.977 0h-2.072l-7.591 8.824L7.251 0H.258l9.168 13.343L.258 24H2.33l8.016-9.318L16.749 24h6.993zm-2.837 3.299-.929-1.329L3.076 1.56h3.182l5.965 8.532.929 1.329 7.754 11.09h-3.182z',
			'instagram' => 'M7.0301.084c-1.2768.0602-2.1487.264-2.911.5634-.7888.3075-1.4575.72-2.1228 1.3877-.6652.6677-1.075 1.3368-1.3802 2.127-.2954.7638-.4956 1.6365-.552 2.914-.0564 1.2775-.0689 1.6882-.0626 4.947.0062 3.2586.0206 3.6671.0825 4.9473.061 1.2765.264 2.1482.5635 2.9107.308.7889.72 1.4573 1.388 2.1228.6679.6655 1.3365 1.0743 2.1285 1.38.7632.295 1.6361.4961 2.9134.552 1.2773.056 1.6884.069 4.9462.0627 3.2578-.0062 3.668-.0207 4.9478-.0814 1.28-.0607 2.147-.2652 2.9098-.5633.7889-.3086 1.4578-.72 2.1228-1.3881.665-.6682 1.0745-1.3378 1.3795-2.1284.2957-.7632.4966-1.636.552-2.9124.056-1.2809.0692-1.6898.063-4.948-.0063-3.2583-.021-3.6668-.0817-4.9465-.0607-1.2797-.264-2.1487-.5633-2.9117-.3084-.7889-.72-1.4568-1.3876-2.1228C21.2982 1.33 20.628.9208 19.8378.6165 19.074.321 18.2017.1197 16.9244.0645 15.6471.0093 15.236-.005 11.977.0014 8.718.0076 8.31.0215 7.0301.0839m.1402 21.6932c-1.17-.0509-1.8053-.2453-2.2287-.408-.5606-.216-.96-.4771-1.3819-.895-.422-.4178-.6811-.8186-.9-1.378-.1644-.4234-.3624-1.058-.4171-2.228-.0595-1.2645-.072-1.6442-.079-4.848-.007-3.2037.0053-3.583.0607-4.848.05-1.169.2456-1.805.408-2.2282.216-.5613.4762-.96.895-1.3816.4188-.4217.8184-.6814 1.3783-.9003.423-.1651 1.0575-.3614 2.227-.4171 1.2655-.06 1.6447-.072 4.848-.079 3.2033-.007 3.5835.005 4.8495.0608 1.169.0508 1.8053.2445 2.228.408.5608.216.96.4754 1.3816.895.4217.4194.6816.8176.9005 1.3787.1653.4217.3617 1.056.4169 2.2263.0602 1.2655.0739 1.645.0796 4.848.0058 3.203-.0055 3.5834-.061 4.848-.051 1.17-.245 1.8055-.408 2.2294-.216.5604-.4763.96-.8954 1.3814-.419.4215-.8181.6811-1.3783.9-.4224.1649-1.0577.3617-2.2262.4174-1.2656.0595-1.6448.072-4.8493.079-3.2045.007-3.5825-.006-4.848-.0608M16.953 5.5864A1.44 1.44 0 1 0 18.39 4.144a1.44 1.44 0 0 0-1.437 1.4424M5.8385 12.012c.0067 3.4032 2.7706 6.1557 6.173 6.1493 3.4026-.0065 6.157-2.7701 6.1506-6.1733-.0065-3.4032-2.771-6.1565-6.174-6.1498-3.403.0067-6.156 2.771-6.1496 6.1738M8 12.0077a4 4 0 1 1 4.008 3.9921A3.9996 3.9996 0 0 1 8 12.0077',
			'linkedin' => 'M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13zM7.12 20.45H3.56V9h3.56v11.45z',
			'youtube' => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
			'tiktok' => 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z',
			'github' => 'M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12',
			'email' => 'M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm1 2.2V17h16V7.2l-8 5.3-8-5.3zM5.6 7l6.4 4.2L18.4 7H5.6z',
		);
	}

	/**
	 * Inline SVG for a social network.
	 *
	 * @param string $net Network key.
	 * @return string
	 */
	public static function icon( $net ) {
		$paths = self::icon_paths();
		if ( empty( $paths[ $net ] ) ) {
			return '';
		}
		return '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="' . esc_attr( $paths[ $net ] ) . '"/></svg>';
	}

	/**
	 * Default badge text per mode.
	 *
	 * @param string $mode Mode.
	 * @return string
	 */
	public static function default_badge( $mode ) {
		return 'maintenance' === $mode ? __( 'Down for maintenance', 'veila' ) : __( 'Coming soon', 'veila' );
	}

	/**
	 * CSS custom properties for the current settings.
	 *
	 * @param array $s Settings.
	 * @return array name => value.
	 */
	public static function css_vars( $s ) {
		$stacks  = Veila_Settings::font_stacks();
		$stack   = isset( $stacks[ $s['font_stack'] ] ) ? $stacks[ $s['font_stack'] ]['stack'] : $stacks['serif']['stack'];
		$bg1     = $s['bg_color'] ? $s['bg_color'] : '#FFFFFF';
		$bg2     = $s['bg_color_2'] ? $s['bg_color_2'] : $bg1;
		$image   = ( 'image' === $s['bg_type'] && $s['bg_image_id'] ) ? wp_get_attachment_image_url( (int) $s['bg_image_id'], 'full' ) : '';
		$accent  = $s['accent_color'] ? $s['accent_color'] : '#1B2A4A';
		$surface = Veila_Settings::surface_color( $s );

		if ( 'gradient' === $s['bg_type'] ) {
			$bg = 'linear-gradient(' . (int) $s['bg_gradient_angle'] . 'deg,' . $bg1 . ',' . $bg2 . ')';
		} elseif ( $image && 'split' !== $s['layout'] ) {
			$bg = $bg1 . ' url("' . esc_url_raw( $image ) . '") center/cover no-repeat';
		} else {
			$bg = $bg1;
		}

		// The footer sits on the page background, outside the card.
		$page = $s;
		if ( 'card' === $s['layout'] ) {
			$page['layout'] = 'centered';
		}

		$vars = array(
			'--veila-bg'         => $bg,
			'--veila-media'      => $image ? 'url("' . esc_url_raw( $image ) . '")' : 'none',
			'--veila-overlay'    => ( $image && 'split' !== $s['layout'] ) ? (string) ( (int) $s['overlay_opacity'] / 100 ) : '0',
			'--veila-surface'    => $surface,
			'--veila-text'       => Veila_Settings::readable_color( $s['text_color'], $s ),
			'--veila-heading'    => Veila_Settings::readable_color( $s['heading_color'], $s, 3 ),
			'--veila-accent'     => $accent,
			'--veila-on-accent'  => Veila_Settings::on_color( $accent ),
			'--veila-link'       => Veila_Settings::readable_color( $accent, $s, 3 ),
			'--veila-foot'       => Veila_Settings::readable_color( $s['text_color'], $page ),
			'--veila-foot-link'  => Veila_Settings::readable_color( $accent, $page, 3 ),
			'--veila-card'       => $s['card_bg'] ? $s['card_bg'] : '#FFFFFF',
			'--veila-font'       => $stack,
			'--veila-head-font'  => $stack,
			'--veila-h-size'     => (int) $s['heading_size'] . 'px',
			'--veila-logo-w'     => (int) $s['logo_width'] . 'px',
		);

		/**
		 * Filter splash page CSS variables (Pro adds fonts).
		 *
		 * @param array $vars Vars.
		 * @param array $s    Settings.
		 */
		return apply_filters( 'veila_css_vars', $vars, $s );
	}

	/**
	 * Inline style attribute value from CSS variables.
	 *
	 * @param array $vars Vars.
	 * @return string
	 */
	public static function style_attr( $vars ) {
		$out = '';
		foreach ( $vars as $name => $value ) {
			$value = str_replace( array( '<', '>', ';', '{', '}' ), '', (string) $value );
			$out  .= $name . ':' . $value . ';';
		}
		return $out;
	}

	/**
	 * CSS classes for the root element.
	 *
	 * @param array  $s    Settings.
	 * @param string $mode Mode.
	 * @return string
	 */
	public static function root_classes( $s, $mode ) {
		$classes = array(
			'veila-root',
			'veila-layout-' . $s['layout'],
			'veila-align-' . $s['text_align'],
			'veila-mode-' . $mode,
			Veila_Settings::background_is_dark( $s ) ? 'veila-is-dark' : 'veila-is-light',
		);
		if ( 'image' === $s['bg_type'] && $s['bg_image_id'] ) {
			$classes[] = 'veila-has-image';
		}

		/**
		 * Filter root classes.
		 *
		 * @param array  $classes Classes.
		 * @param array  $s       Settings.
		 * @param string $mode    Mode.
		 */
		$classes = apply_filters( 'veila_root_classes', $classes, $s, $mode );
		return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
	}

	/**
	 * Print the splash body markup.
	 *
	 * @param array  $s       Settings.
	 * @param string $mode    Mode.
	 * @param string $context "front" or "preview".
	 */
	public static function markup( $s, $mode, $context = 'front' ) {
		$logo_light = $s['logo_id'] ? wp_get_attachment_image_url( (int) $s['logo_id'], 'full' ) : '';
		$logo_dark  = $s['logo_id_dark'] ? wp_get_attachment_image_url( (int) $s['logo_id_dark'], 'full' ) : '';
		$logo       = Veila_Settings::logo_url( $s );
		$badge      = '' !== $s['badge'] ? $s['badge'] : self::default_badge( $mode );
		$tag        = 'preview' === $context ? 'div' : 'main';
		?>
		<div class="<?php echo esc_attr( self::root_classes( $s, $mode ) ); ?>" id="<?php echo 'preview' === $context ? 'veila-preview-root' : 'veila-root'; ?>" style="<?php echo esc_attr( self::style_attr( self::css_vars( $s ) ) ); ?>">
			<div class="veila-bg" aria-hidden="true"><?php do_action( 'veila_splash_background', $s, $context ); ?></div>
			<<?php echo esc_html( $tag ); ?> class="veila-main">
				<div class="veila-media" aria-hidden="true"></div>
				<div class="veila-content">
					<div class="veila-card">
						<div class="veila-logo" data-light="<?php echo esc_url( $logo_light ? $logo_light : '' ); ?>" data-dark="<?php echo esc_url( $logo_dark ? $logo_dark : '' ); ?>">
							<?php if ( $logo || 'preview' === $context ) : ?>
								<img src="<?php echo esc_url( $logo ? $logo : '' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"<?php echo $logo ? '' : ' hidden'; ?> />
							<?php endif; ?>
							<span class="veila-wordmark"<?php echo $logo ? ' hidden' : ''; ?>><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
						</div>
						<p class="veila-badge"<?php echo empty( $s['show_badge'] ) ? ' hidden' : ''; ?> data-coming="<?php echo esc_attr( self::default_badge( 'coming_soon' ) ); ?>" data-maint="<?php echo esc_attr( self::default_badge( 'maintenance' ) ); ?>"><?php echo esc_html( $badge ); ?></p>
						<h1 class="veila-headline"><?php echo esc_html( $s['headline'] ); ?></h1>
						<div class="veila-message"><?php echo wp_kses( wpautop( $s['message'] ), Veila_Settings::message_kses() ); ?></div>
						<?php
						/**
						 * Fires after the message (Pro adds the countdown and signup form).
						 *
						 * @param array  $s       Settings.
						 * @param string $mode    Mode.
						 * @param string $context front or preview.
						 */
						do_action( 'veila_splash_after_message', $s, $mode, $context );
						?>
						<ul class="veila-social">
							<?php
							foreach ( Veila_Settings::socials() as $net => $label ) :
								$value = $s[ 'social_' . $net ];
								if ( ! $value && 'preview' !== $context ) {
									continue;
								}
								$href = 'email' === $net ? 'mailto:' . antispambot( $value ) : $value;
								?>
								<li class="veila-social-<?php echo esc_attr( $net ); ?>"<?php echo $value ? '' : ' hidden'; ?>><a href="<?php echo esc_url( $href ? $href : '#', array( 'http', 'https', 'mailto' ) ); ?>" rel="noopener noreferrer"<?php echo 'email' === $net ? '' : ' target="_blank"'; ?>><?php echo self::icon( $net ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG with escaped path. ?><span class="veila-sr"><?php echo esc_html( $label ); ?></span></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</<?php echo esc_html( $tag ); ?>>
			<?php self::footer( $s, $context ); ?>
		</div>
		<?php
	}

	/**
	 * Footer line: custom text, optional login link, and an opt-in credit.
	 *
	 * @param array  $s       Settings.
	 * @param string $context Context.
	 */
	public static function footer( $s, $context ) {
		$parts = array();
		if ( '' !== $s['footer_text'] ) {
			$parts[] = '<span class="veila-footer-text">' . esc_html( $s['footer_text'] ) . '</span>';
		}
		if ( ! empty( $s['show_login_link'] ) || 'preview' === $context ) {
			$parts[] = '<a class="veila-login-link" href="' . esc_url( wp_login_url() ) . '"' . ( empty( $s['show_login_link'] ) ? ' hidden' : '' ) . '>' . esc_html__( 'Log in', 'veila' ) . '</a>';
		}

		/**
		 * Filter whether the opt-in "Made with Veila" credit may show (white label hides it).
		 *
		 * @param bool  $show Show.
		 * @param array $s    Settings.
		 */
		$credit_allowed = apply_filters( 'veila_show_credit', ! empty( $s['show_credit'] ), $s );
		if ( $credit_allowed || ( 'preview' === $context && apply_filters( 'veila_show_credit', true, $s ) ) ) {
			$parts[] = '<span class="veila-credit"' . ( $credit_allowed ? '' : ' hidden' ) . '>' . esc_html__( 'Made with Veila', 'veila' ) . '</span>';
		}
		echo '<footer class="veila-footer">' . implode( '', $parts ) . '</footer>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Parts escaped above.
	}

	/**
	 * Document title.
	 *
	 * @param array  $s    Settings.
	 * @param string $mode Mode.
	 * @return string
	 */
	public static function document_title( $s, $mode ) {
		if ( '' !== $s['seo_title'] ) {
			return $s['seo_title'];
		}
		$badge = '' !== $s['badge'] ? $s['badge'] : self::default_badge( $mode );
		return get_bloginfo( 'name' ) . ' — ' . $badge;
	}

	/**
	 * Output the complete splash document.
	 *
	 * @param array  $s       Settings.
	 * @param string $mode    Mode.
	 * @param bool   $preview Whether this is an admin preview.
	 */
	public static function output( $s, $mode, $preview = false ) {
		$template = VEILA_DIR . 'templates/splash.php';

		/**
		 * Filter the splash template file path.
		 *
		 * @param string $template Path.
		 * @param array  $s        Settings.
		 * @param string $mode     Mode.
		 */
		$template = apply_filters( 'veila_template_file', $template, $s, $mode );
		if ( ! is_readable( $template ) ) {
			$template = VEILA_DIR . 'templates/splash.php';
		}
		$veila_settings = $s;
		$veila_mode     = $mode;
		$veila_preview  = $preview;
		include $template;
	}
}
