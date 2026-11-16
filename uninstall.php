<?php
/**
 * Uninstall Veila: remove its settings and per-user notes.
 *
 * @package Veila
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'veila_settings' );
delete_metadata( 'user', 0, 'veila_upsell_dismissed', '', true );

if ( is_multisite() ) {
	delete_site_option( 'veila_settings' );
}
