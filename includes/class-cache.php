<?php
/**
 * Cache handling: never let a page cache store the splash page, and purge
 * common page caches whenever the veil is switched on or off.
 *
 * @package Veila
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cache helper.
 */
class Veila_Cache {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'update_option_' . Veila_Settings::OPTION, array( __CLASS__, 'maybe_purge_on_change' ), 10, 2 );
	}

	/**
	 * Purge caches when the mode (or anything visible on the splash page) changes.
	 *
	 * @param mixed $old_value Old.
	 * @param mixed $value     New.
	 */
	public static function maybe_purge_on_change( $old_value, $value ) {
		if ( $old_value === $value ) {
			return;
		}
		self::purge_all();
	}

	/**
	 * Tell page caches, proxies, and browsers not to store this response.
	 */
	public static function send_no_cache_headers() {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Constant read by cache plugins.
		}
		if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
			define( 'DONOTCACHEOBJECT', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Constant read by cache plugins.
		}
		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( 'DONOTCACHEDB', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Constant read by cache plugins.
		}
		if ( ! defined( 'DONOTROCKETOPTIMIZE' ) ) {
			define( 'DONOTROCKETOPTIMIZE', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Constant read by cache plugins.
		}
		if ( headers_sent() ) {
			return;
		}
		nocache_headers();
		header( 'Cache-Control: no-cache, no-store, must-revalidate, max-age=0, private' );
		header( 'Pragma: no-cache' );
		header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
		header( 'X-LiteSpeed-Cache-Control: no-cache' );
		header( 'X-Accel-Expires: 0' );
		header( 'CDN-Cache-Control: no-store' );
		header( 'Surrogate-Control: no-store' );

		/**
		 * Fires when Veila sends no-cache headers for the splash page.
		 * LiteSpeed Cache listens to its own action below.
		 */
		do_action( 'litespeed_control_set_nocache', 'veila splash page' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Third-party hook.
		do_action( 'veila_no_cache_headers_sent' );
	}

	/**
	 * Purge common page caches if their plugins are active. Each call is
	 * guarded, so nothing happens when a cache plugin is not installed.
	 */
	public static function purge_all() {
		// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Third-party cache plugin hooks.
		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache(); // WP Super Cache.
		}
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain(); // WP Rocket.
		}
		if ( function_exists( 'w3tc_flush_all' ) ) {
			w3tc_flush_all(); // W3 Total Cache.
		}
		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache(); // SiteGround Optimizer.
		}
		do_action( 'litespeed_purge_all' ); // LiteSpeed Cache (Hostinger and others).
		do_action( 'breeze_clear_all_cache' ); // Breeze.
		do_action( 'cache_enabler_clear_complete_cache' ); // Cache Enabler.
		do_action( 'rt_nginx_helper_purge_all' ); // Nginx Helper.
		if ( isset( $GLOBALS['wp_fastest_cache'] ) && is_object( $GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ) {
			$GLOBALS['wp_fastest_cache']->deleteCache( true ); // WP Fastest Cache.
		}
		// phpcs:enable
		wp_cache_flush();

		/**
		 * Fires after Veila asked cache plugins to purge. Hook in to clear a CDN.
		 */
		do_action( 'veila_purge_caches' );
	}
}
