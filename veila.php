<?php
/**
 * Plugin Name:       Veila
 * Plugin URI:        https://wordpress.org/plugins/veila/
 * Description:       A coming-soon and maintenance page for WordPress with a live preview, starter templates, and the right HTTP status codes. Off until you turn it on. No ads, no accounts, and no external services. Settings: Appearance > Veila.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Sanctoria
 * Author URI:        https://sanctoria.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       veila
 * Domain Path:       /languages
 *
 * @package Veila
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VEILA_VERSION', '1.0.0' );
define( 'VEILA_FILE', __FILE__ );
define( 'VEILA_DIR', plugin_dir_path( __FILE__ ) );
define( 'VEILA_URL', plugin_dir_url( __FILE__ ) );

require_once VEILA_DIR . 'includes/class-settings.php';
require_once VEILA_DIR . 'includes/class-cache.php';
require_once VEILA_DIR . 'includes/class-render.php';
require_once VEILA_DIR . 'includes/class-gate.php';
require_once VEILA_DIR . 'includes/class-admin-bar.php';
require_once VEILA_DIR . 'includes/class-admin.php';
require_once VEILA_DIR . 'includes/class-plugin.php';

/**
 * Boot the plugin.
 *
 * @return Veila_Plugin
 */
function veila() {
	return Veila_Plugin::instance();
}

veila();

/**
 * Veila never turns itself on. Activation only makes sure the stored mode is
 * "off" when no settings exist yet.
 */
function veila_activate() {
	if ( false === get_option( 'veila_settings', false ) ) {
		add_option( 'veila_settings', array( 'mode' => 'off' ) );
	}
}
register_activation_hook( __FILE__, 'veila_activate' );

/**
 * Clear page caches on deactivation so a cached splash page cannot linger.
 */
function veila_deactivate() {
	Veila_Cache::purge_all();
}
register_deactivation_hook( __FILE__, 'veila_deactivate' );
