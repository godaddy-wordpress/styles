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
		$this->styles_loader = new StylesLoaderStub();
    }

	public function tear_down() {
		parent::tear_down();
		$this->styles_loader = null;
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
		$this->assertEmpty( wp_styles()->registered[ $this->styles_loader::HANDLE ] );
		$this->assertFalse( $this->styles_loader->hasRegistered() );
    }

	public function test_has_registered_returns_true_if_registered() {
		wp_enqueue_style( $this->styles_loader::HANDLE, 'styles.css', array(), '1.0.0' );

		$this->assertNotEmpty( wp_styles()->registered[ $this->styles_loader::HANDLE ] );
		$this->assertTrue( $this->styles_loader->hasRegistered() );
    }

	public function test_get_registered_returns_array_or_false() {
		$this->assertFalse( $this->styles_loader->getRegistered() );

		wp_enqueue_style( $this->styles_loader::HANDLE, 'styles.css', array(), '1.0.0' );
		$this->assertInstanceOf( _WP_Dependency::class, $this->styles_loader->getRegistered() );
	}

	public function test_is_must_use_returns_true_for_mu_plugin_path() {
		wp_enqueue_style( $this->styles_loader::HANDLE, 'styles.css', array(), '1.0.0' );

		$this->assertFalse( $this->styles_loader->isMustUse() );

		wp_deregister_style( $this->styles_loader::HANDLE );
		wp_enqueue_style( $this->styles_loader::HANDLE, 'mu-plugins/styles.css', array(), '1.0.0' );

		$this->assertTrue( $this->styles_loader->isMustUse() );
	}

	public function test_boot_hooks_exist() {
		$callback = array( $this->styles_loader, 'enqueue' );

		$this->assertFalse( has_action( 'admin_enqueue_scripts', $callback ) );
		$this->assertFalse( has_action( 'wp_enqueue_scripts', $callback ) );

		$this->styles_loader->boot();

		// Assert "not false" because has_action returns the priority of the hook if the callback exists.
		$this->assertNotFalse( has_action( 'admin_enqueue_scripts', $callback ) );
		$this->assertNotFalse( has_action( 'wp_enqueue_scripts', $callback ) );
	}

	public function test_skips_enqueue_when_older_version() {
		$this->styles_loader->boot();
		do_action( 'wp_enqueue_scripts' );

		$this->assertEquals(
			StylesLoaderStub::VERSION,
			$this->styles_loader->getRegisteredVersion()
		);

		$older_styles_loader = new OlderStylesLoaderStub;
		$older_styles_loader->boot();
		do_action( 'wp_enqueue_scripts' );

		$this->assertEquals(
			StylesLoaderStub::VERSION,
			$older_styles_loader->getRegisteredVersion()
		);
	}

	public function test_enqueue_when_newer_version() {
		$this->styles_loader->boot();
		do_action( 'wp_enqueue_scripts' );

		$this->assertEquals(
			StylesLoaderStub::VERSION,
			$this->styles_loader->getRegisteredVersion()
		);

		$newer_styles_loader = new NewerStylesLoaderStub;
		$newer_styles_loader->boot();
		do_action( 'wp_enqueue_scripts' );

		$this->assertEquals(
			NewerStylesLoaderStub::VERSION,
			$newer_styles_loader->getRegisteredVersion()
		);
    }

	public function test_skips_enqueue_when_mu_plugin_registered() {
		$must_use_styles_loader = new MustUseStylesLoaderStub;
		$must_use_styles_loader->boot();
		do_action( 'wp_enqueue_scripts' );

		$this->assertEquals(
			MustUseStylesLoaderStub::VERSION,
			$must_use_styles_loader->getRegisteredVersion()
		);

		$newer_styles_loader = new NewerStylesLoaderStub;
		$newer_styles_loader->boot();
		do_action( 'wp_enqueue_scripts' );

		$this->assertEquals(
			MustUseStylesLoaderStub::VERSION,
			$newer_styles_loader->getRegisteredVersion()
		);
    }
}

class StylesLoaderStub extends StylesLoader {
	const VERSION = '1.0.0';
	const HANDLE = 'godaddy-styles-testing';
}
class OlderStylesLoaderStub extends StylesLoader {
	const VERSION = '0.1.0';
	const HANDLE = 'godaddy-styles-testing';
}
class NewerStylesLoaderStub extends StylesLoader {
	const VERSION = '2.0.0';
	const HANDLE = 'godaddy-styles-testing';
}
class MustUseStylesLoaderStub extends StylesLoader {
	const VERSION = '1.0.0';
	const HANDLE = 'godaddy-styles-testing';

	public function enqueue() {
		add_filter( 'plugins_url', array( $this, 'plugins_url_callback' ) );
		parent::enqueue();
		remove_filter( 'plugins_url', array( $this, 'plugins_url_callback' ) );
	}

	public function plugins_url_callback( $url ) {
		return str_replace( 'plugins/', 'mu-plugins/', $url );
	}
}