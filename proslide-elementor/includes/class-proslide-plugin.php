<?php
/**
 * Core ProSlide Plugin Loader.
 *
 * @package ProSlide
 */

namespace ProSlide;

use Elementor\Plugin as Elementor_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_widget_category' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_styles' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
	}

	/**
	 * Register custom ProSlide widget category in Elementor.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
	 */
	public function register_widget_category( $elements_manager ) {
		$elements_manager->add_category(
			'proslide-elements',
			[
				'title' => esc_html__( 'ProSlide Elements', 'proslide-elementor' ),
				'icon'  => 'eicon-slides',
			]
		);
	}

	/**
	 * Register Elementor widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		require_once PROSLIDE_PATH . 'includes/widgets/class-proslide-content-slider.php';
		$widgets_manager->register( new Widgets\Content_Slider() );

		// Register Nested Elementor Widget Slider if Elementor Nested Elements module is active.
		if ( $this->is_nested_elements_active() ) {
			require_once PROSLIDE_PATH . 'includes/widgets/class-proslide-nested-slider.php';
			$widgets_manager->register( new Widgets\Nested_Slider() );
		}
	}

	/**
	 * Check if Elementor's Nested Elements feature is available and active.
	 *
	 * @return bool
	 */
	private function is_nested_elements_active() {
		if ( ! class_exists( '\Elementor\Modules\NestedElements\Base\Widget_Nested_Base' ) ) {
			return false;
		}

		if ( isset( Elementor_Plugin::$instance->experiments ) && method_exists( Elementor_Plugin::$instance->experiments, 'is_feature_active' ) ) {
			return Elementor_Plugin::$instance->experiments->is_feature_active( 'nested-elements' );
		}

		return true;
	}

	/**
	 * Register frontend styles.
	 */
	public function register_styles() {
		$deps = [];
		if ( wp_style_is( 'e-swiper', 'registered' ) ) {
			$deps[] = 'e-swiper';
		} elseif ( wp_style_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		wp_register_style(
			'proslide-css',
			PROSLIDE_URL . 'assets/css/proslide.css',
			$deps,
			PROSLIDE_VERSION
		);
	}

	/**
	 * Register frontend scripts.
	 */
	public function register_scripts() {
		$deps = [ 'jquery', 'elementor-frontend' ];
		if ( wp_script_is( 'swiper', 'registered' ) ) {
			$deps[] = 'swiper';
		}

		wp_register_script(
			'proslide-js',
			PROSLIDE_URL . 'assets/js/proslide.js',
			$deps,
			PROSLIDE_VERSION,
			true
		);
	}

	/**
	 * Enqueue styles inside Elementor editor if needed.
	 */
	public function enqueue_editor_styles() {
		wp_enqueue_style( 'proslide-css' );
	}

	/**
	 * Enqueue scripts inside Elementor editor for nested elements support.
	 */
	public function enqueue_editor_scripts() {
		if ( $this->is_nested_elements_active() ) {
			wp_enqueue_script(
				'proslide-editor-js',
				PROSLIDE_URL . 'assets/js/proslide-editor.js',
				[ 'elementor-editor' ],
				PROSLIDE_VERSION,
				true
			);
		}
	}
}
