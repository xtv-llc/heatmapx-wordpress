<?php
/**
 * Pure helper functions for the HeatMapX plugin.
 * No WordPress hooks here — keep everything unit-testable.
 *
 * @package HeatMapX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'HEATMAPX_TRACKER_HOST' ) ) {
	define( 'HEATMAPX_TRACKER_HOST', 'https://heatmapx.com' );
}

/**
 * Validate a HeatMapX site key. Returns '' when invalid.
 * Production keys are 32-char hex; accept 16–64 alphanumerics for forward compatibility.
 *
 * @param mixed $key Raw user input.
 * @return string
 */
function heatmapx_sanitize_site_key( $key ) {
	if ( ! is_string( $key ) ) {
		return '';
	}
	$key = trim( $key );
	return preg_match( '/^[A-Za-z0-9]{16,64}$/', $key ) ? $key : '';
}

/**
 * Build the tracker script URL (?key=... query arg), for use with wp_enqueue_script().
 *
 * @param string $site_key Site key.
 * @return string Empty string when the key is invalid.
 */
function heatmapx_tracker_src( $site_key ) {
	$site_key = heatmapx_sanitize_site_key( $site_key );
	if ( '' === $site_key ) {
		return '';
	}
	return HEATMAPX_TRACKER_HOST . '/tracker.js?key=' . rawurlencode( $site_key );
}

/**
 * Build the tracker <script> tag in the dual-key format
 * (?key= in src AND data-site-key), so the key survives GTM-like rewrites.
 *
 * Used only for the settings-page preview; the actual front-end tag is
 * produced by wp_enqueue_script() + the script_loader_tag filter (see
 * heatmapx_enqueue_tracker() / heatmapx_filter_script_tag() in heatmapx.php).
 *
 * @param string $site_key Site key.
 * @return string Empty string when the key is invalid.
 */
function heatmapx_build_tracker_tag( $site_key ) {
	$src = heatmapx_tracker_src( $site_key );
	if ( '' === $src ) {
		return '';
	}
	$site_key = heatmapx_sanitize_site_key( $site_key );
	// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- Preview string for the settings-page <code> block only; not printed on the front end (front-end output uses wp_enqueue_script(), see heatmapx.php).
	return '<script async src="' . esc_url( $src ) . '" data-site-key="'
		. esc_attr( $site_key ) . '"></script>' . "\n";
}

/**
 * Add the data-site-key attribute to the enqueued tracker <script> tag
 * (dual-key format: ?key= in src AND data-site-key attribute), so the key
 * survives GTM-like tag-manager rewrites. Pure string operation: the site
 * key is read back out of the already-built src="...?key=..." rather than
 * looked up again, so this only ever touches the tag WordPress generated
 * for our own enqueued handle.
 *
 * @param string $tag    The `<script>` tag markup WordPress generated.
 * @param string $handle Registered script handle for the tag.
 * @return string
 */
function heatmapx_filter_script_tag( $tag, $handle ) {
	if ( 'heatmapx' !== $handle ) {
		return $tag;
	}
	if ( ! preg_match( '/[?&]key=([^"&]+)/', $tag, $matches ) ) {
		return $tag;
	}
	$site_key = rawurldecode( $matches[1] );
	$attr     = 'data-site-key="' . esc_attr( $site_key ) . '" ';
	return str_replace( ' src=', ' ' . $attr . 'src=', $tag );
}

/**
 * Decide whether the tracker tag should be printed for this request.
 *
 * @param string $site_key       Configured site key.
 * @param bool   $exclude_admins Option: skip logged-in administrators.
 * @param bool   $user_is_admin  Whether the current user is a logged-in administrator.
 * @return bool
 */
function heatmapx_should_output_tag( $site_key, $exclude_admins, $user_is_admin ) {
	if ( '' === heatmapx_sanitize_site_key( $site_key ) ) {
		return false;
	}
	if ( $exclude_admins && $user_is_admin ) {
		return false;
	}
	return true;
}

/**
 * Default plugin settings.
 *
 * @return array
 */
function heatmapx_default_settings() {
	return array(
		'site_key'       => '',
		'exclude_admins' => true,
	);
}

/**
 * Sanitize callback for the Settings API.
 *
 * @param mixed $input Raw submitted value.
 * @return array
 */
function heatmapx_sanitize_settings( $input ) {
	$out = heatmapx_default_settings();
	if ( ! is_array( $input ) ) {
		return $out;
	}
	$out['site_key']       = isset( $input['site_key'] ) ? heatmapx_sanitize_site_key( $input['site_key'] ) : '';
	$out['exclude_admins'] = ! empty( $input['exclude_admins'] );
	return $out;
}
