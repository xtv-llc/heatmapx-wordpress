<?php
/**
 * Pure helper functions for the HeatMapX plugin.
 * No WordPress hooks here — keep everything unit-testable.
 *
 * @package HeatMapX
 */

if ( ! defined( 'HMX_TRACKER_HOST' ) ) {
	define( 'HMX_TRACKER_HOST', 'https://heatmapx.com' );
}

/**
 * Validate a HeatMapX site key. Returns '' when invalid.
 * Production keys are 32-char hex; accept 16–64 alphanumerics for forward compatibility.
 *
 * @param mixed $key Raw user input.
 * @return string
 */
function hmx_sanitize_site_key( $key ) {
	if ( ! is_string( $key ) ) {
		return '';
	}
	$key = trim( $key );
	return preg_match( '/^[A-Za-z0-9]{16,64}$/', $key ) ? $key : '';
}

/**
 * Build the tracker <script> tag in the dual-key format
 * (?key= in src AND data-site-key), so the key survives GTM-like rewrites.
 *
 * @param string $site_key Site key.
 * @return string Empty string when the key is invalid.
 */
function hmx_build_tracker_tag( $site_key ) {
	$site_key = hmx_sanitize_site_key( $site_key );
	if ( '' === $site_key ) {
		return '';
	}
	$src = HMX_TRACKER_HOST . '/tracker.js?key=' . rawurlencode( $site_key );
	// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- Intentional inline async tracker tag (GTM-style dual-key format); not a themeable asset, so wp_enqueue_script() does not apply.
	return '<script async src="' . esc_url( $src ) . '" data-site-key="'
		. esc_attr( $site_key ) . '"></script>' . "\n";
}

/**
 * Decide whether the tracker tag should be printed for this request.
 *
 * @param string $site_key       Configured site key.
 * @param bool   $exclude_admins Option: skip logged-in administrators.
 * @param bool   $user_is_admin  Whether the current user is a logged-in administrator.
 * @return bool
 */
function hmx_should_output_tag( $site_key, $exclude_admins, $user_is_admin ) {
	if ( '' === hmx_sanitize_site_key( $site_key ) ) {
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
function hmx_default_settings() {
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
function hmx_sanitize_settings( $input ) {
	$out = hmx_default_settings();
	if ( ! is_array( $input ) ) {
		return $out;
	}
	$out['site_key']       = isset( $input['site_key'] ) ? hmx_sanitize_site_key( $input['site_key'] ) : '';
	$out['exclude_admins'] = ! empty( $input['exclude_admins'] );
	return $out;
}
