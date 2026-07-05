<?php
/**
 * Plugin Name: HeatMapX – Heatmaps & A/B Testing
 * Plugin URI: https://github.com/xtv-llc/heatmapx-wordpress
 * Description: Heatmaps, session analytics and A/B testing by HeatMapX. Paste your site key — no theme editing required.
 * Version: 1.0.0
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author: XTV LLC
 * Author URI: https://heatmapx.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: heatmapx
 *
 * @package HeatMapX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/functions.php';

if ( is_admin() ) {
	require_once __DIR__ . '/includes/settings-page.php';
}

/**
 * Add a Settings shortcut on the Plugins list row.
 *
 * @param array $links Existing action links.
 * @return array
 */
function heatmapx_plugin_action_links( $links ) {
	$url = admin_url( 'options-general.php?page=heatmapx' );
	array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'heatmapx' ) . '</a>' );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'heatmapx_plugin_action_links' );

/**
 * Current settings merged over defaults.
 *
 * @return array
 */
function heatmapx_get_settings() {
	$saved = get_option( 'heatmapx_settings', array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}
	return array_merge( heatmapx_default_settings(), $saved );
}

/**
 * Enqueue the tracker script on front-end pages.
 */
function heatmapx_enqueue_tracker() {
	$settings      = heatmapx_get_settings();
	$user_is_admin = is_user_logged_in() && current_user_can( 'manage_options' );
	if ( ! heatmapx_should_output_tag( $settings['site_key'], $settings['exclude_admins'], $user_is_admin ) ) {
		return;
	}
	wp_enqueue_script(
		'heatmapx',
		heatmapx_tracker_src( $settings['site_key'] ),
		array(),
		null, // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Intentional: this is a remote, self-hosted CDN script (heatmapx.com), not a bundled plugin asset, so a local ?ver= cache-buster does not apply; the tracker host manages its own script versioning/cache headers.
		array(
			'in_footer' => false,
			'strategy'  => 'async',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'heatmapx_enqueue_tracker' );

add_filter( 'script_loader_tag', 'heatmapx_filter_script_tag', 10, 2 );
