<?php
/**
 * Admin settings page (Settings → HeatMapX).
 *
 * @package HeatMapX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register option, section and fields.
 */
function heatmapx_register_settings() {
	register_setting(
		'heatmapx',
		'heatmapx_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'heatmapx_sanitize_settings',
			'default'           => heatmapx_default_settings(),
		)
	);
	add_settings_section( 'heatmapx_main', __( 'Tracking', 'heatmapx' ), '__return_false', 'heatmapx' );
	add_settings_field( 'heatmapx_site_key', __( 'Site key', 'heatmapx' ), 'heatmapx_render_site_key_field', 'heatmapx', 'heatmapx_main' );
	add_settings_field( 'heatmapx_exclude_admins', __( 'Administrators', 'heatmapx' ), 'heatmapx_render_exclude_admins_field', 'heatmapx', 'heatmapx_main' );
}
add_action( 'admin_init', 'heatmapx_register_settings' );

/**
 * Add the options page.
 */
function heatmapx_add_settings_page() {
	add_options_page( 'HeatMapX', 'HeatMapX', 'manage_options', 'heatmapx', 'heatmapx_render_settings_page' );
}
add_action( 'admin_menu', 'heatmapx_add_settings_page' );

/**
 * Site key text field.
 */
function heatmapx_render_site_key_field() {
	$settings = heatmapx_get_settings();
	printf(
		'<input type="text" name="heatmapx_settings[site_key]" value="%s" class="regular-text code" autocomplete="off" />',
		esc_attr( $settings['site_key'] )
	);
	echo '<p class="description">'
		. esc_html__( 'Find your site key in the HeatMapX dashboard (Sites → Install).', 'heatmapx' )
		. '</p>';
}

/**
 * Exclude-admins checkbox.
 */
function heatmapx_render_exclude_admins_field() {
	$settings = heatmapx_get_settings();
	printf(
		'<label><input type="checkbox" name="heatmapx_settings[exclude_admins]" value="1" %s /> %s</label>',
		checked( $settings['exclude_admins'], true, false ),
		esc_html__( 'Do not track logged-in administrators (recommended)', 'heatmapx' )
	);
}

/**
 * Render the page.
 */
function heatmapx_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$settings = heatmapx_get_settings();
	?>
	<div class="wrap">
		<h1>HeatMapX</h1>
		<?php if ( '' === $settings['site_key'] ) : ?>
			<p>
				<?php esc_html_e( 'No HeatMapX account yet? Create one for free and copy your site key.', 'heatmapx' ); ?>
				<a href="https://heatmapx.com/login?utm_source=wordpress-plugin&utm_medium=referral&utm_campaign=settings" target="_blank" rel="noopener">
					<?php esc_html_e( 'Get your free site key →', 'heatmapx' ); ?>
				</a>
			</p>
		<?php endif; ?>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'heatmapx' );
			do_settings_sections( 'heatmapx' );
			submit_button();
			?>
		</form>
		<?php if ( '' !== $settings['site_key'] ) : ?>
			<h2><?php esc_html_e( 'Tag preview', 'heatmapx' ); ?></h2>
			<p class="description"><?php esc_html_e( 'This tag is inserted automatically on every public page:', 'heatmapx' ); ?></p>
			<code><?php echo esc_html( trim( heatmapx_build_tracker_tag( $settings['site_key'] ) ) ); ?></code>
		<?php endif; ?>
	</div>
	<?php
}
