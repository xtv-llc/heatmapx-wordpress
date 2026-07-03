<?php
/**
 * Plugin Name: HeatMapX – Heatmaps & A/B Testing
 * Plugin URI: https://heatmapx.com
 * Description: Heatmaps, session analytics and A/B testing by HeatMapX. Paste your site key — no theme editing required.
 * Version: 1.0.0
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author: XTV LLC
 * Author URI: https://heatmapx.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: heatmapx
 * Domain Path: /languages
 *
 * @package HeatMapX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/functions.php';

/**
 * Current settings merged over defaults.
 *
 * @return array
 */
function hmx_get_settings() {
	$saved = get_option( 'heatmapx_settings', array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	return array_merge( hmx_default_settings(), $saved );
}

/**
 * Load bundled translations (WP also auto-loads from wordpress.org once listed).
 */
function hmx_load_textdomain() {
	load_plugin_textdomain( 'heatmapx', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'hmx_load_textdomain' );

/**
 * Print the tracker tag on front-end pages.
 */
function hmx_output_tracker_tag() {
	$settings      = hmx_get_settings();
	$user_is_admin = is_user_logged_in() && current_user_can( 'manage_options' );
	if ( ! hmx_should_output_tag( $settings['site_key'], $settings['exclude_admins'], $user_is_admin ) ) {
		return;
	}
	echo hmx_build_tracker_tag( $settings['site_key'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside hmx_build_tracker_tag().
}
add_action( 'wp_head', 'hmx_output_tracker_tag', 20 );
