<?php
/**
 * Splash page document. Loaded by Veila_Render::output().
 *
 * This template does not call wp_head() or wp_footer(), so theme and plugin
 * assets (including remote fonts or trackers) are never added to the page.
 *
 * @package Veila
 *
 * @var array  $veila_settings Settings.
 * @var string $veila_mode     Mode.
 * @var bool   $veila_preview  Preview flag.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$veila_css_file = VEILA_DIR . 'assets/css/splash.css';
wp_register_style( 'veila-splash', VEILA_URL . 'assets/css/splash.css', array(), file_exists( $veila_css_file ) ? (string) filemtime( $veila_css_file ) : VEILA_VERSION );
if ( '' !== trim( (string) $veila_settings['custom_css'] ) ) {
	wp_add_inline_style( 'veila-splash', $veila_settings['custom_css'] );
}
$veila_noindex = 'maintenance' === $veila_mode || ! empty( $veila_settings['noindex'] ) || $veila_preview;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo esc_html( Veila_Render::document_title( $veila_settings, $veila_mode ) ); ?></title>
<?php if ( '' !== $veila_settings['seo_description'] ) : ?>
<meta name="description" content="<?php echo esc_attr( $veila_settings['seo_description'] ); ?>" />
<meta property="og:description" content="<?php echo esc_attr( $veila_settings['seo_description'] ); ?>" />
<?php endif; ?>
<meta property="og:title" content="<?php echo esc_attr( Veila_Render::document_title( $veila_settings, $veila_mode ) ); ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo esc_url( home_url( '/' ) ); ?>" />
<?php if ( $veila_noindex ) : ?>
<meta name="robots" content="noindex, nofollow" />
<?php endif; ?>
<?php
if ( function_exists( 'wp_site_icon' ) ) {
	wp_site_icon();
}
wp_print_styles( array( 'veila-splash' ) );

/**
 * Fires inside <head> on the splash page (Pro prints fonts and its script here).
 *
 * @param array  $veila_settings Settings.
 * @param string $veila_mode     Mode.
 */
do_action( 'veila_splash_head', $veila_settings, $veila_mode );
?>
</head>
<body class="veila-body">
<?php if ( $veila_preview ) : ?>
<div class="veila-preview-flag" role="status"><?php esc_html_e( 'Preview — only administrators can see this page right now.', 'veila' ); ?></div>
<?php endif; ?>
<?php Veila_Render::markup( $veila_settings, $veila_mode, 'front' ); ?>
<?php
/**
 * Fires before </body> on the splash page.
 *
 * @param array  $veila_settings Settings.
 * @param string $veila_mode     Mode.
 */
do_action( 'veila_splash_footer', $veila_settings, $veila_mode );
?>
</body>
</html>
