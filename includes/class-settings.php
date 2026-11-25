<?php
/**
 * Settings storage, sanitization, starter templates, and color helpers.
 *
 * @package Veila
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings helper.
 */
class Veila_Settings {

	const OPTION = 'veila_settings';

	/**
	 * Register the option.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
	}

	/**
	 * Register setting.
	 */
	public static function register() {
		register_setting(
			'veila',
			self::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => self::defaults(),
				'show_in_rest'      => false,
			)
		);
	}

	/**
	 * Default settings. The mode is always "off" until the site owner turns it on.
	 *
	 * @return array
	 */
	public static function defaults() {
		$defaults = array(
			'mode'               => 'off',
			'noindex'            => 0,
			'retry_after'        => 60,
			'bypass_cap'         => 'manage_options',
			'template'           => 'veila_ivory',
			'layout'             => 'centered',
			'logo_id'            => 0,
			'logo_id_dark'       => 0,
			'logo_width'         => 160,
			'show_badge'         => 1,
			'badge'              => '',
			'headline'           => __( 'Something new is on the way', 'sanctoria-veila' ),
			'message'            => __( 'We are putting the finishing touches on our new site. Please check back soon.', 'sanctoria-veila' ),
			'bg_type'            => 'color',
			'bg_color'           => '#FBF8F1',
			'bg_color_2'         => '#F5EFE2',
			'bg_gradient_angle'  => 160,
			'bg_image_id'        => 0,
			'overlay_opacity'    => 40,
			'text_color'         => '#2B2B2B',
			'heading_color'      => '#1B2A4A',
			'accent_color'       => '#9C7A2E',
			'card_bg'            => '#FFFFFF',
			'font_stack'         => 'serif',
			'heading_size'       => 44,
			'text_align'         => 'center',
			'seo_title'          => '',
			'seo_description'    => '',
			'social_facebook'    => '',
			'social_x'           => '',
			'social_instagram'   => '',
			'social_linkedin'    => '',
			'social_youtube'     => '',
			'social_tiktok'      => '',
			'social_github'      => '',
			'social_email'       => '',
			'show_login_link'    => 0,
			'show_credit'        => 0,
			'footer_text'        => '',
		);

		/**
		 * Filter default Veila settings.
		 *
		 * @param array $defaults Defaults.
		 */
		return apply_filters( 'veila_default_settings', $defaults );
	}

	/**
	 * Get merged settings.
	 *
	 * @return array
	 */
	public static function get() {
		$stored = get_option( self::OPTION, array() );
		if ( ! is_array( $stored ) ) {
			$stored = array();
		}
		$settings = array_merge( self::defaults(), $stored );
		// Custom CSS was removed for the WordPress.org guideline on arbitrary code insertion.
		// Ignore a leftover value from 1.0.1; uninstall still deletes the whole option.
		unset( $settings['custom_css'] );

		/**
		 * Filter saved Veila settings.
		 *
		 * @param array $settings Settings.
		 */
		return apply_filters( 'veila_settings', $settings );
	}

	/**
	 * Get one setting.
	 *
	 * @param string $key      Key.
	 * @param mixed  $fallback Fallback.
	 * @return mixed
	 */
	public static function get_value( $key, $fallback = '' ) {
		$settings = self::get();
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $fallback;
	}

	/**
	 * Allowed modes.
	 *
	 * @return array
	 */
	public static function modes() {
		return array(
			'off'         => __( 'Off — visitors see the normal site', 'sanctoria-veila' ),
			'coming_soon' => __( 'Coming soon — HTTP 200, can be indexed', 'sanctoria-veila' ),
			'maintenance' => __( 'Maintenance — HTTP 503 with Retry-After', 'sanctoria-veila' ),
		);
	}

	/**
	 * Capabilities that can bypass the veil.
	 *
	 * @return array
	 */
	public static function bypass_caps() {
		return array(
			'manage_options'    => __( 'Administrators (manage_options)', 'sanctoria-veila' ),
			'edit_others_posts' => __( 'Editors and above (edit_others_posts)', 'sanctoria-veila' ),
			'publish_posts'     => __( 'Authors and above (publish_posts)', 'sanctoria-veila' ),
			'edit_posts'        => __( 'Contributors and above (edit_posts)', 'sanctoria-veila' ),
			'read'              => __( 'Any logged-in user (read)', 'sanctoria-veila' ),
		);
	}

	/**
	 * Layouts.
	 *
	 * @return array
	 */
	public static function layouts() {
		return array(
			'centered' => __( 'Centered', 'sanctoria-veila' ),
			'card'     => __( 'Card', 'sanctoria-veila' ),
			'split'    => __( 'Split (image beside text)', 'sanctoria-veila' ),
			'minimal'  => __( 'Minimal (left aligned)', 'sanctoria-veila' ),
		);
	}

	/**
	 * System font stacks. Nothing is loaded from another website.
	 *
	 * @return array slug => array( label, stack ).
	 */
	public static function font_stacks() {
		$stacks = array(
			'serif'    => array(
				'label' => __( 'Classic serif', 'sanctoria-veila' ),
				'stack' => '"Iowan Old Style","Palatino Linotype",Palatino,"Book Antiqua",Georgia,serif',
			),
			'sans'     => array(
				'label' => __( 'System sans-serif', 'sanctoria-veila' ),
				'stack' => 'system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif',
			),
			'humanist' => array(
				'label' => __( 'Humanist sans-serif', 'sanctoria-veila' ),
				'stack' => 'Seravek,"Gill Sans Nova",Ubuntu,Calibri,"DejaVu Sans",source-sans-pro,sans-serif',
			),
			'mono'     => array(
				'label' => __( 'Monospace', 'sanctoria-veila' ),
				'stack' => 'ui-monospace,"Cascadia Code","Source Code Pro",Menlo,Consolas,"DejaVu Sans Mono",monospace',
			),
		);

		/**
		 * Filter the available font stacks (Pro adds local fonts).
		 *
		 * @param array $stacks Stacks.
		 */
		return apply_filters( 'veila_font_stacks', $stacks );
	}

	/**
	 * Social networks Veila can link to.
	 *
	 * @return array key => label.
	 */
	public static function socials() {
		return array(
			'facebook'  => 'Facebook',
			'x'         => 'X',
			'instagram' => 'Instagram',
			'linkedin'  => 'LinkedIn',
			'youtube'   => 'YouTube',
			'tiktok'    => 'TikTok',
			'github'    => 'GitHub',
			'email'     => __( 'Email', 'sanctoria-veila' ),
		);
	}

	/**
	 * Sanitize the settings array.
	 *
	 * @param mixed $input Raw input.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$defaults = self::defaults();
		if ( ! is_array( $input ) ) {
			return $defaults;
		}

		$out = $defaults;

		$mode                = (string) ( $input['mode'] ?? 'off' );
		$out['mode']         = array_key_exists( $mode, self::modes() ) ? $mode : 'off';
		$out['noindex']      = empty( $input['noindex'] ) ? 0 : 1;
		$out['retry_after']  = self::int_range( $input['retry_after'] ?? 60, 1, 10080, 60 );
		$cap                 = (string) ( $input['bypass_cap'] ?? 'manage_options' );
		$out['bypass_cap']   = array_key_exists( $cap, self::bypass_caps() ) ? $cap : 'manage_options';
		$out['template']     = sanitize_key( $input['template'] ?? '' );
		$layout              = (string) ( $input['layout'] ?? 'centered' );
		$out['layout']       = array_key_exists( $layout, self::layouts() ) ? $layout : 'centered';
		$out['logo_id']      = absint( $input['logo_id'] ?? 0 );
		$out['logo_id_dark'] = absint( $input['logo_id_dark'] ?? 0 );
		$out['logo_width']   = self::int_range( $input['logo_width'] ?? 160, 32, 480, 160 );
		$out['show_badge']   = empty( $input['show_badge'] ) ? 0 : 1;
		$out['badge']        = sanitize_text_field( $input['badge'] ?? '' );
		$out['headline']     = sanitize_text_field( $input['headline'] ?? '' );
		$out['message']      = wp_kses( (string) ( $input['message'] ?? '' ), self::message_kses() );

		$bg_type                  = (string) ( $input['bg_type'] ?? 'color' );
		$out['bg_type']           = in_array( $bg_type, array( 'color', 'gradient', 'image' ), true ) ? $bg_type : 'color';
		$out['bg_color']          = self::sanitize_hex( $input['bg_color'] ?? '' );
		$out['bg_color_2']        = self::sanitize_hex( $input['bg_color_2'] ?? '' );
		$out['bg_gradient_angle'] = self::int_range( $input['bg_gradient_angle'] ?? 160, 0, 360, 160 );
		$out['bg_image_id']       = absint( $input['bg_image_id'] ?? 0 );
		$out['overlay_opacity']   = self::int_range( $input['overlay_opacity'] ?? 40, 0, 90, 40 );
		$out['text_color']        = self::sanitize_hex( $input['text_color'] ?? '' );
		$out['heading_color']     = self::sanitize_hex( $input['heading_color'] ?? '' );
		$out['accent_color']      = self::sanitize_hex( $input['accent_color'] ?? '' );
		$out['card_bg']           = self::sanitize_hex( $input['card_bg'] ?? '' );
		$font                     = (string) ( $input['font_stack'] ?? 'serif' );
		$out['font_stack']        = array_key_exists( $font, self::font_stacks() ) ? $font : 'serif';
		$out['heading_size']      = self::int_range( $input['heading_size'] ?? 44, 20, 96, 44 );
		$align                    = (string) ( $input['text_align'] ?? 'center' );
		$out['text_align']        = in_array( $align, array( 'center', 'left' ), true ) ? $align : 'center';

		$out['seo_title']       = sanitize_text_field( $input['seo_title'] ?? '' );
		$out['seo_description'] = sanitize_textarea_field( $input['seo_description'] ?? '' );
		foreach ( array_keys( self::socials() ) as $net ) {
			$raw = trim( (string) ( $input[ 'social_' . $net ] ?? '' ) );
			if ( 'email' === $net ) {
				$out['social_email'] = is_email( $raw ) ? sanitize_email( $raw ) : '';
			} else {
				$out[ 'social_' . $net ] = esc_url_raw( $raw, array( 'http', 'https' ) );
			}
		}
		$out['show_login_link'] = empty( $input['show_login_link'] ) ? 0 : 1;
		$out['show_credit']     = empty( $input['show_credit'] ) ? 0 : 1;
		$out['footer_text']     = sanitize_text_field( $input['footer_text'] ?? '' );

		/**
		 * Filter sanitized settings (Pro may add keys).
		 *
		 * @param array $out   Sanitized.
		 * @param array $input Raw input.
		 */
		return apply_filters( 'veila_sanitize_settings', $out, $input );
	}

	/**
	 * Tags allowed in the message.
	 *
	 * @return array
	 */
	public static function message_kses() {
		return array(
			'a'      => array(
				'href'   => true,
				'title'  => true,
				'target' => true,
				'rel'    => true,
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'p'      => array(),
		);
	}

	/**
	 * Clamp an integer.
	 *
	 * @param mixed $value    Value.
	 * @param int   $min      Min.
	 * @param int   $max      Max.
	 * @param int   $fallback Default.
	 * @return int
	 */
	public static function int_range( $value, $min, $max, $fallback ) {
		if ( ! is_numeric( $value ) ) {
			return $fallback;
		}
		$value = (int) $value;
		if ( $value < $min ) {
			return $min;
		}
		if ( $value > $max ) {
			return $max;
		}
		return $value;
	}

	/**
	 * Sanitize a #RRGGBB or #RGB color, or empty.
	 *
	 * @param string $color Color.
	 * @return string
	 */
	public static function sanitize_hex( $color ) {
		$color = trim( (string) $color );
		if ( '' === $color ) {
			return '';
		}
		$sanitized = sanitize_hex_color( $color );
		return $sanitized ? $sanitized : '';
	}

	/**
	 * Starter templates (applied only when the user clicks them).
	 *
	 * @return array
	 */
	public static function starters() {
		$starters = array(
			'veila_ivory' => array(
				'label'           => __( 'Veila Ivory', 'sanctoria-veila' ),
				'layout'          => 'centered',
				'bg_type'         => 'color',
				'bg_color'        => '#FBF8F1',
				'bg_color_2'      => '#F5EFE2',
				'text_color'      => '#2B2B2B',
				'heading_color'   => '#1B2A4A',
				'accent_color'    => '#9C7A2E',
				'card_bg'         => '#FFFFFF',
				'font_stack'      => 'serif',
				'heading_size'    => 44,
				'text_align'      => 'center',
				'overlay_opacity' => 0,
			),
			'veila_night' => array(
				'label'           => __( 'Veila Night', 'sanctoria-veila' ),
				'layout'          => 'centered',
				'bg_type'         => 'gradient',
				'bg_color'        => '#0F1A30',
				'bg_color_2'      => '#1B2A4A',
				'bg_gradient_angle' => 165,
				'text_color'      => '#F5EFE2',
				'heading_color'   => '#E3C77A',
				'accent_color'    => '#C9A24A',
				'card_bg'         => '#1B2A4A',
				'font_stack'      => 'serif',
				'heading_size'    => 46,
				'text_align'      => 'center',
				'overlay_opacity' => 0,
			),
			'clean_light' => array(
				'label'           => __( 'Clean Light', 'sanctoria-veila' ),
				'layout'          => 'card',
				'bg_type'         => 'color',
				'bg_color'        => '#F3F1EC',
				'text_color'      => '#44403C',
				'heading_color'   => '#1C1917',
				'accent_color'    => '#1D4E4A',
				'card_bg'         => '#FFFFFF',
				'font_stack'      => 'sans',
				'heading_size'    => 38,
				'text_align'      => 'center',
				'overlay_opacity' => 0,
			),
			'clean_dark' => array(
				'label'           => __( 'Clean Dark', 'sanctoria-veila' ),
				'layout'          => 'card',
				'bg_type'         => 'gradient',
				'bg_color'        => '#0F1720',
				'bg_color_2'      => '#1D4E4A',
				'bg_gradient_angle' => 160,
				'text_color'      => '#E7E5E4',
				'heading_color'   => '#F5F5F4',
				'accent_color'    => '#5EEAD4',
				'card_bg'         => '#18202A',
				'font_stack'      => 'sans',
				'heading_size'    => 38,
				'text_align'      => 'center',
				'overlay_opacity' => 0,
			),
			'minimal'    => array(
				'label'           => __( 'Minimal', 'sanctoria-veila' ),
				'layout'          => 'minimal',
				'bg_type'         => 'color',
				'bg_color'        => '#FFFFFF',
				'text_color'      => '#3F3F46',
				'heading_color'   => '#111111',
				'accent_color'    => '#111111',
				'card_bg'         => '#FFFFFF',
				'font_stack'      => 'humanist',
				'heading_size'    => 56,
				'text_align'      => 'left',
				'overlay_opacity' => 0,
			),
		);

		/**
		 * Filter starter templates shown above the tabs.
		 *
		 * @param array $starters Starters.
		 */
		return apply_filters( 'veila_starters', $starters );
	}

	/**
	 * Relative luminance of a #RRGGBB color (0–1), or null if invalid.
	 *
	 * @param string $hex Hex color.
	 * @return float|null
	 */
	public static function relative_luminance( $hex ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( ! preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
			return null;
		}
		$chan = array();
		foreach ( array( 0, 2, 4 ) as $i ) {
			$c      = hexdec( substr( $hex, $i, 2 ) ) / 255;
			$chan[] = ( $c <= 0.04045 ) ? ( $c / 12.92 ) : pow( ( $c + 0.055 ) / 1.055, 2.4 );
		}
		return 0.2126 * $chan[0] + 0.7152 * $chan[1] + 0.0722 * $chan[2];
	}

	/**
	 * WCAG contrast ratio between two hex colors, or null if either is invalid.
	 *
	 * @param string $a Color A.
	 * @param string $b Color B.
	 * @return float|null
	 */
	public static function contrast_ratio( $a, $b ) {
		$la = self::relative_luminance( $a );
		$lb = self::relative_luminance( $b );
		if ( null === $la || null === $lb ) {
			return null;
		}
		$hi = max( $la, $lb );
		$lo = min( $la, $lb );
		return ( $hi + 0.05 ) / ( $lo + 0.05 );
	}

	/**
	 * Representative background color behind the text (card color for card layouts).
	 *
	 * @param array|null $s Settings.
	 * @return string
	 */
	public static function surface_color( $s = null ) {
		if ( ! is_array( $s ) ) {
			$s = self::get();
		}
		if ( 'card' === ( $s['layout'] ?? '' ) && ! empty( $s['card_bg'] ) ) {
			return $s['card_bg'];
		}
		if ( 'image' === ( $s['bg_type'] ?? 'color' ) && ! empty( $s['bg_image_id'] ) && 'split' !== ( $s['layout'] ?? '' ) ) {
			return '#000000';
		}
		return ! empty( $s['bg_color'] ) ? $s['bg_color'] : '#FFFFFF';
	}

	/**
	 * Whether the surface behind the text is dark.
	 *
	 * @param array|null $s Settings.
	 * @return bool
	 */
	public static function background_is_dark( $s = null ) {
		if ( ! is_array( $s ) ) {
			$s = self::get();
		}
		$layout = $s['layout'] ?? 'centered';
		if ( 'card' === $layout && ! empty( $s['card_bg'] ) ) {
			$l = self::relative_luminance( $s['card_bg'] );
			return null !== $l && $l < 0.45;
		}
		$type = $s['bg_type'] ?? 'color';
		if ( 'image' === $type && ! empty( $s['bg_image_id'] ) && 'split' !== $layout ) {
			return true;
		}
		$l1 = self::relative_luminance( $s['bg_color'] ?? '' );
		if ( null === $l1 ) {
			return false;
		}
		if ( 'gradient' === $type && ! empty( $s['bg_color_2'] ) ) {
			$l2 = self::relative_luminance( $s['bg_color_2'] );
			if ( null !== $l2 ) {
				return ( ( $l1 + $l2 ) / 2 ) < 0.45;
			}
		}
		return $l1 < 0.45;
	}

	/**
	 * A readable text color for the current surface: the chosen color when it
	 * meets WCAG AA (4.5:1), otherwise near-black or near-white.
	 *
	 * @param string     $chosen Chosen color.
	 * @param array|null $s      Settings.
	 * @param float      $min    Minimum ratio.
	 * @return string
	 */
	public static function readable_color( $chosen, $s = null, $min = 4.5 ) {
		$surface = self::surface_color( $s );
		$ratio   = self::contrast_ratio( $chosen, $surface );
		if ( $chosen && null !== $ratio && $ratio >= $min ) {
			return $chosen;
		}
		if ( $chosen && null === self::relative_luminance( $surface ) ) {
			return $chosen;
		}
		return self::background_is_dark( $s ) ? '#F8F7F4' : '#1A1A1A';
	}

	/**
	 * Text on the accent color (buttons): white or near-black, whichever reads better.
	 *
	 * @param string $accent Accent hex.
	 * @return string
	 */
	public static function on_color( $accent ) {
		$l = self::relative_luminance( $accent );
		if ( null === $l ) {
			return '#FFFFFF';
		}
		$white = self::contrast_ratio( $accent, '#FFFFFF' );
		$black = self::contrast_ratio( $accent, '#111111' );
		return ( $white >= $black ) ? '#FFFFFF' : '#111111';
	}

	/**
	 * Attachment ID of the logo that suits the current background.
	 *
	 * @param array|null $s Settings.
	 * @return int
	 */
	public static function logo_attachment_id( $s = null ) {
		if ( ! is_array( $s ) ) {
			$s = self::get();
		}
		$light = (int) ( $s['logo_id'] ?? 0 );
		$dark  = (int) ( $s['logo_id_dark'] ?? 0 );
		if ( $dark && self::background_is_dark( $s ) ) {
			return $dark;
		}
		return $light ? $light : $dark;
	}

	/**
	 * Full-size URL of the logo that suits the current background.
	 *
	 * @param array|null $s Settings.
	 * @return string
	 */
	public static function logo_url( $s = null ) {
		$id = self::logo_attachment_id( $s );
		if ( ! $id ) {
			return '';
		}
		$url = wp_get_attachment_image_url( $id, 'full' );
		return $url ? $url : '';
	}
}
