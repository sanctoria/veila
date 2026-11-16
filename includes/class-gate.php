<?php
/**
 * Decides when visitors see the splash page, and with which HTTP status.
 *
 * Only front-end page views are covered. The dashboard, wp-login.php,
 * wp-cron.php, admin-ajax.php, the REST API, XML-RPC, WP-CLI, robots.txt,
 * and the favicon never reach this code or are skipped explicitly.
 *
 * @package Veila
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end gate.
 */
class Veila_Gate {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'maybe_show' ), 0 );
	}

	/**
	 * Whether the wp-config.php kill switch is set.
	 *
	 * @return bool
	 */
	public static function kill_switch() {
		return defined( 'VEILA_DISABLE' ) && VEILA_DISABLE;
	}

	/**
	 * Mode in effect right now: off, coming_soon, or maintenance.
	 *
	 * @return string
	 */
	public static function effective_mode() {
		if ( self::kill_switch() ) {
			return 'off';
		}
		$s    = Veila_Settings::get();
		$mode = $s['mode'];

		/**
		 * Filter the active mode (Pro uses this for schedules and auto-launch).
		 *
		 * @param string $mode Mode.
		 * @param array  $s    Settings.
		 */
		$mode = (string) apply_filters( 'veila_active_mode', $mode, $s );
		return in_array( $mode, array( 'coming_soon', 'maintenance' ), true ) ? $mode : 'off';
	}

	/**
	 * Whether the current visitor sees the real site because of their capability.
	 *
	 * @param array $s Settings.
	 * @return bool
	 */
	public static function should_bypass( $s ) {
		$bypass = is_user_logged_in() && current_user_can( $s['bypass_cap'] );

		/**
		 * Filter whether the current visitor bypasses the splash page
		 * (Pro adds roles, IP allowlists, secret links, and a password).
		 *
		 * @param bool  $bypass Bypass.
		 * @param array $s      Settings.
		 */
		return (bool) apply_filters( 'veila_bypass', $bypass, $s );
	}

	/**
	 * Requests that must always work, whatever the mode.
	 *
	 * @return bool
	 */
	public static function is_exempt_request() {
		$exempt = false;
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			$exempt = true;
		} elseif ( ( defined( 'WP_CLI' ) && WP_CLI ) || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
			$exempt = true;
		} elseif ( is_robots() || is_favicon() || is_customize_preview() ) {
			$exempt = true;
		} elseif ( isset( $GLOBALS['pagenow'] ) && in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php', 'wp-activate.php', 'wp-signup.php' ), true ) ) {
			$exempt = true;
		}

		/**
		 * Filter whether this request is exempt from the splash page.
		 *
		 * @param bool $exempt Exempt.
		 */
		return (bool) apply_filters( 'veila_is_exempt_request', $exempt );
	}

	/**
	 * Admin preview of the splash page (always HTTP 200, never cached).
	 *
	 * @return bool
	 */
	public static function is_preview_request() {
		if ( empty( $_GET['veila_preview'] ) || ! current_user_can( 'manage_options' ) ) {
			return false;
		}
		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		return (bool) wp_verify_nonce( $nonce, 'veila_preview' );
	}

	/**
	 * Preview URL for the settings page.
	 *
	 * @param string $mode Mode to preview.
	 * @return string
	 */
	public static function preview_url( $mode = '' ) {
		$args = array( 'veila_preview' => 1 );
		if ( $mode ) {
			$args['veila_mode'] = $mode;
		}
		return wp_nonce_url( add_query_arg( $args, home_url( '/' ) ), 'veila_preview' );
	}

	/**
	 * Retry-After in seconds for maintenance mode.
	 *
	 * @param array $s Settings.
	 * @return int
	 */
	public static function retry_after( $s ) {
		$seconds = max( 60, (int) $s['retry_after'] * MINUTE_IN_SECONDS );

		/**
		 * Filter the Retry-After value in seconds.
		 *
		 * @param int   $seconds Seconds.
		 * @param array $s       Settings.
		 */
		return max( 1, (int) apply_filters( 'veila_retry_after', $seconds, $s ) );
	}

	/**
	 * Show the splash page when the veil is on.
	 */
	public static function maybe_show() {
		$s = Veila_Settings::get();

		if ( self::is_preview_request() ) {
			$mode = isset( $_GET['veila_mode'] ) ? sanitize_key( wp_unslash( $_GET['veila_mode'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce checked in is_preview_request().
			if ( ! in_array( $mode, array( 'coming_soon', 'maintenance' ), true ) ) {
				$mode = 'off' === $s['mode'] ? 'coming_soon' : $s['mode'];
			}
			Veila_Cache::send_no_cache_headers();
			Veila_Render::output( $s, $mode, true );
			exit;
		}

		$mode = self::effective_mode();
		if ( 'off' === $mode || self::is_exempt_request() || self::should_bypass( $s ) ) {
			return;
		}

		/**
		 * Filter whether the veil covers this URL (Pro page rules).
		 *
		 * @param bool   $applies Applies.
		 * @param string $mode    Mode.
		 * @param array  $s       Settings.
		 */
		if ( ! apply_filters( 'veila_applies_to_request', true, $mode, $s ) ) {
			return;
		}

		Veila_Cache::send_no_cache_headers();

		if ( is_feed() || is_trackback() || get_query_var( 'sitemap' ) || get_query_var( 'sitemap-stylesheet' ) ) {
			self::machine_response( $mode, $s );
			exit;
		}

		self::send_status( $mode, $s );
		Veila_Render::output( $s, $mode, false );
		exit;
	}

	/**
	 * Status line and robots headers for the splash page.
	 *
	 * @param string $mode Mode.
	 * @param array  $s    Settings.
	 */
	public static function send_status( $mode, $s ) {
		if ( headers_sent() ) {
			return;
		}
		if ( 'maintenance' === $mode ) {
			status_header( 503 );
			header( 'Retry-After: ' . self::retry_after( $s ) );
			header( 'X-Robots-Tag: noindex, nofollow', true );
		} else {
			status_header( 200 );
			if ( ! empty( $s['noindex'] ) ) {
				header( 'X-Robots-Tag: noindex, nofollow', true );
			}
		}
		header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
	}

	/**
	 * Feeds, trackbacks, and sitemaps get a short plain-text reply instead of
	 * the HTML splash page, so feed readers and crawlers never cache it.
	 * Maintenance returns 503 with Retry-After; coming soon returns 404 so no
	 * unpublished content leaks through a feed.
	 *
	 * @param string $mode Mode.
	 * @param array  $s    Settings.
	 */
	public static function machine_response( $mode, $s ) {
		if ( ! headers_sent() ) {
			if ( 'maintenance' === $mode ) {
				status_header( 503 );
				header( 'Retry-After: ' . self::retry_after( $s ) );
			} else {
				status_header( 404 );
			}
			header( 'X-Robots-Tag: noindex, nofollow', true );
			header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ) );
		}
		if ( 'maintenance' === $mode ) {
			echo esc_html__( 'This site is down for maintenance. Please try again soon.', 'veila' );
		} else {
			echo esc_html__( 'This site is not public yet.', 'veila' );
		}
	}
}
