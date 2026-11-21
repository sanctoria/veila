<?php
/**
 * Admin-bar indicator so logged-in users always know the veil is on.
 *
 * @package Veila
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin bar indicator.
 */
class Veila_Admin_Bar {

	/**
	 * Hooks.
	 */
	public static function init() {
		add_action( 'admin_bar_menu', array( __CLASS__, 'node' ), 100 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'css' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'css' ) );
	}

	/**
	 * Whether the indicator should show.
	 *
	 * @return bool
	 */
	private static function visible() {
		return is_admin_bar_showing() && current_user_can( 'manage_options' ) && 'off' !== Veila_Gate::effective_mode();
	}

	/**
	 * Add the node.
	 *
	 * @param WP_Admin_Bar $bar Admin bar.
	 */
	public static function node( $bar ) {
		if ( ! self::visible() ) {
			return;
		}
		$mode  = Veila_Gate::effective_mode();
		$label = 'maintenance' === $mode ? __( 'Maintenance mode is on', 'sanctoria-veila' ) : __( 'Coming soon mode is on', 'sanctoria-veila' );

		/**
		 * Filter the admin-bar label (white label can rename it).
		 *
		 * @param string $label Label.
		 * @param string $mode  Mode.
		 */
		$label = apply_filters( 'veila_admin_bar_label', $label, $mode );
		$bar->add_node(
			array(
				'id'    => 'veila-status',
				'title' => '<span class="ab-icon" aria-hidden="true"></span><span class="ab-label">' . esc_html( $label ) . '</span>',
				'href'  => Veila_Admin::settings_url(),
				'meta'  => array(
					'class' => 'veila-status veila-status-' . sanitize_html_class( $mode ),
					'title' => __( 'Visitors see the splash page. Click to change.', 'sanctoria-veila' ),
				),
			)
		);
	}

	/**
	 * Small inline style for the indicator.
	 */
	public static function css() {
		if ( ! self::visible() ) {
			return;
		}
		wp_add_inline_style( 'admin-bar', '#wpadminbar .veila-status>.ab-item{background:#9C7A2E;color:#fff}#wpadminbar .veila-status-maintenance>.ab-item{background:#b32d2e}#wpadminbar .veila-status .ab-icon:before{content:"\f160";top:2px;color:#fff}#wpadminbar .veila-status>.ab-item:hover{color:#fff!important;filter:brightness(1.1)}' );
	}
}
