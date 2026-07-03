<?php
/**
 * Delete plugin data on uninstall.
 *
 * @package HeatMapX
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'heatmapx_settings' );
