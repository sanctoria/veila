<?php
/**
 * Core plugin bootstrap.
 *
 * @package Veila
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class Veila_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Veila_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get the instance.
	 *
	 * @return Veila_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Hook everything up.
	 */
	private function __construct() {
		Veila_Settings::init();
		Veila_Cache::init();
		Veila_Gate::init();
		Veila_Admin_Bar::init();

		if ( is_admin() ) {
			Veila_Admin::init();
		}
	}
}
