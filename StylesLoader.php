<?php
/**
 * The StylesLoader class.
 *
 * @package GoDaddy
 */

namespace GoDaddy\Styles;

/**
 * The StylesLoader class.
 */
class StylesLoader {
	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	const VERSION = '0.2.1';

	public static function boot() {
		add_action( 'admin_enqueue_scripts', array( self::class, 'loadStyles' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'loadStyles' ) );
	}

	public static function loadStyles() {
		$build_file_path = plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

		$asset_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $build_file_path )
			? include $build_file_path
			: array(
				'dependencies' => array( 'wp-components' ),
				'version'      => self::VERSION,
			);

		wp_enqueue_style(
			'godaddy-styles',
			plugin_dir_url( __FILE__ ) . 'build/index.css',
			$asset_file['dependencies'],
			$asset_file['version'],
		);
	}
}