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

	/**
	 * The style handle.
	 *
	 * @var string
	 */
	const HANDLE = 'godaddy-styles';

	/**
     * The current instance.
     *
     * @var static
     */
    protected static $instance;

	/**
     * Get the instance.
     *
     * @return static
     */
    public static function getInstance() {
        if ( is_null( static::$instance ) ) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Set the instance.
     *
     * @param  StylesLoader|null  $instance
     * @return StylesLoader|static
     */
    public static function setInstance( StylesLoader $instance = null ) {
        return static::$instance = $instance;
    }

	public function boot() {
		add_action( 'admin_enqueue_scripts', array( $this, 'loadStyles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'loadStyles' ) );
	}

	public function loadStyles() {
		$build_file_path = plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

		$asset_file = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $build_file_path )
			? include $build_file_path
			: array(
				'dependencies' => array( 'wp-components' ),
				'version'      => self::VERSION,
			);

		wp_enqueue_style(
			self::HANDLE,
			plugin_dir_url( __FILE__ ) . 'build/index.css',
			$asset_file['dependencies'],
			$asset_file['version'],
		);
	}

	public function hasRegistered() {
		return wp_styles()->query( self::HANDLE ) !== false;
	}

	public function getRegistered() {
		return wp_styles()->query( self::HANDLE );
	}

	public function isMustUse() {
		$src = $this->getRegistered()->src;
		return ! empty( $src ) && strpos( $src, 'mu-plugins' ) !== false;
	}
}
