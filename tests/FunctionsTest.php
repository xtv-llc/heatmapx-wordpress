<?php
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase {

	private const KEY = 'a1b2c3d4e5f60718293a4b5c6d7e8f90'; // 32-hex like production keys

	public function test_sanitize_accepts_production_format_key(): void {
		$this->assertSame( self::KEY, hmx_sanitize_site_key( self::KEY ) );
	}

	public function test_sanitize_trims_whitespace(): void {
		$this->assertSame( self::KEY, hmx_sanitize_site_key( '  ' . self::KEY . "\n" ) );
	}

	public function test_sanitize_rejects_html_injection(): void {
		$this->assertSame( '', hmx_sanitize_site_key( '"><script>alert(1)</script>' ) );
	}

	public function test_sanitize_rejects_too_short_key(): void {
		$this->assertSame( '', hmx_sanitize_site_key( 'abc123' ) );
	}

	public function test_sanitize_rejects_non_string(): void {
		$this->assertSame( '', hmx_sanitize_site_key( null ) );
		$this->assertSame( '', hmx_sanitize_site_key( array( self::KEY ) ) );
	}

	public function test_build_tag_matches_dual_format_exactly(): void {
		$expected = '<script async src="https://heatmapx.com/tracker.js?key=' . self::KEY
			. '" data-site-key="' . self::KEY . '"></script>' . "\n";
		$this->assertSame( $expected, hmx_build_tracker_tag( self::KEY ) );
	}

	public function test_build_tag_returns_empty_for_invalid_key(): void {
		$this->assertSame( '', hmx_build_tracker_tag( 'bad key!' ) );
		$this->assertSame( '', hmx_build_tracker_tag( '' ) );
	}

	public function test_should_output_false_without_key(): void {
		$this->assertFalse( hmx_should_output_tag( '', true, false ) );
	}

	public function test_should_output_false_for_admin_when_excluded(): void {
		$this->assertFalse( hmx_should_output_tag( self::KEY, true, true ) );
	}

	public function test_should_output_true_for_admin_when_not_excluded(): void {
		$this->assertTrue( hmx_should_output_tag( self::KEY, false, true ) );
	}

	public function test_should_output_true_for_visitor(): void {
		$this->assertTrue( hmx_should_output_tag( self::KEY, true, false ) );
	}

	public function test_default_settings_shape(): void {
		$this->assertSame(
			array( 'site_key' => '', 'exclude_admins' => true ),
			hmx_default_settings()
		);
	}

	public function test_sanitize_settings_falls_back_to_defaults_on_garbage(): void {
		$this->assertSame( hmx_default_settings(), hmx_sanitize_settings( 'not-an-array' ) );
	}

	public function test_sanitize_settings_unchecked_checkbox_means_false(): void {
		$out = hmx_sanitize_settings( array( 'site_key' => self::KEY ) );
		$this->assertSame( self::KEY, $out['site_key'] );
		$this->assertFalse( $out['exclude_admins'] );
	}

	public function test_tracker_src_builds_url_with_key(): void {
		$this->assertSame(
			'https://heatmapx.com/tracker.js?key=' . self::KEY,
			hmx_tracker_src( self::KEY )
		);
	}

	public function test_tracker_src_returns_empty_for_invalid_key(): void {
		$this->assertSame( '', hmx_tracker_src( 'bad key!' ) );
		$this->assertSame( '', hmx_tracker_src( '' ) );
	}

	public function test_filter_script_tag_leaves_other_handles_unchanged(): void {
		$tag = '<script src="https://example.com/other.js" id="other-js"></script>' . "\n";
		$this->assertSame( $tag, hmx_filter_script_tag( $tag, 'other-handle' ) );
	}

	public function test_filter_script_tag_adds_data_site_key_for_heatmapx_handle(): void {
		$tag      = '<script src="' . HMX_TRACKER_HOST . '/tracker.js?key=' . self::KEY
			. '" id="heatmapx-js" async></script>' . "\n";
		$expected = '<script data-site-key="' . self::KEY . '" src="' . HMX_TRACKER_HOST
			. '/tracker.js?key=' . self::KEY . '" id="heatmapx-js" async></script>' . "\n";
		$this->assertSame( $expected, hmx_filter_script_tag( $tag, 'heatmapx' ) );
	}

	public function test_filter_script_tag_escapes_extracted_key(): void {
		$tag      = '<script src="' . HMX_TRACKER_HOST . '/tracker.js?key=%22%3E%3Cb%3E"'
			. ' id="heatmapx-js"></script>' . "\n";
		$result   = hmx_filter_script_tag( $tag, 'heatmapx' );
		$this->assertStringContainsString( 'data-site-key="&quot;&gt;&lt;b&gt;"', $result );
	}

	public function test_filter_script_tag_leaves_tag_unchanged_when_no_key_found(): void {
		$tag = '<script src="' . HMX_TRACKER_HOST . '/tracker.js" id="heatmapx-js"></script>' . "\n";
		$this->assertSame( $tag, hmx_filter_script_tag( $tag, 'heatmapx' ) );
	}
}
