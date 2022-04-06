<?php
/**
 * Tests the StylesLoader class.
 *
 * @package GoDaddy_Styles
 */

use GoDaddy\Styles\StylesLoader;

/**
 * Tests the StylesLoader class.
 */
class StylesLoaderTest extends WP_UnitTestCase {
	/**
	 * The StylesLoader instance.
	 *
	 * @var \GoDaddy\Styles\StylesLoader;
	 */
	private $styles_loader;

	public function set_up() {
        parent::set_up();
		$this->styles_loader = StylesLoader::getInstance();
    }

	public function tear_down() {
		parent::tear_down();
		$this->styles_loader->setInstance( null );
		unset( $GLOBALS['wp_styles'] );
    }

	/**
	 * TESTS:
	 * - If style handle has not already been enqueued, enqueue it.
	 * - If the current plugin is a mu-plugin, override any enqueued handle.
	 * - If style handle has been enqueued:
	 * 		- do nothing if the src is from the mu-plugin dir.
	 * 		- enqueue only if we're enqueuing a more recent version.
	 */

    public function test_has_registered_returns_false_if_missing() {
		$this->assertEmpty( wp_styles()->registered[ StylesLoader::HANDLE ] );
		$this->assertFalse( $this->styles_loader->hasRegistered() );
    }

	public function test_has_registered_returns_true_if_registered() {
		wp_enqueue_style( StylesLoader::HANDLE, 'styles.css', array(), '1.0.0' );

		$this->assertNotEmpty( wp_styles()->registered[ StylesLoader::HANDLE ] );
		$this->assertTrue( $this->styles_loader->hasRegistered() );
    }

	public function test_get_registered_returns_array_or_false() {
		$this->assertFalse( $this->styles_loader->getRegistered() );

		wp_enqueue_style( StylesLoader::HANDLE, 'styles.css', array(), '1.0.0' );
		$this->assertInstanceOf( _WP_Dependency::class, $this->styles_loader->getRegistered() );
	}

	public function test_is_must_use_returns_true_for_mu_plugin_path() {
		wp_enqueue_style( StylesLoader::HANDLE, 'styles.css', array(), '1.0.0' );
		$this->assertFalse( $this->styles_loader->isMustUse() );

		wp_deregister_style( StylesLoader::HANDLE );
		wp_enqueue_style( StylesLoader::HANDLE, 'mu-plugins/styles.css', array(), '1.0.0' );

		$this->assertTrue( $this->styles_loader->isMustUse() );
	}

	// public function test_should_enqueue() {
	// 	$this->assertEmpty( wp_styles()->registered['godaddy-styles'] );
	// 	$this->assertTrue( StylesLoader::shouldEnqueue() );
    // }
}