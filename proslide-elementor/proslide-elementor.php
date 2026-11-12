<?php
/**
 * Plugin Name:       ProSlide for Elementor
 * Plugin URI:        https://github.com/Ridoy9147/proslide-elementor
 * Description:       Advanced responsive Elementor slider plugin with full content design, background images & Ken Burns effects, Elementor template slides, and nested Elementor widget slides.
 * Version:           1.0.1
 * Author:            Developer Hridoy
 * Author URI:        https://github.com/Ridoy9147
 * Text Domain:       proslide-elementor
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Elementor tested up to: 3.26
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'PROSLIDE_VERSION', '1.0.1' );
define( 'PROSLIDE_FILE', __FILE__ );
define( 'PROSLIDE_PATH', plugin_dir_path( __FILE__ ) );
define( 'PROSLIDE_URL', plugin_dir_url( __FILE__ ) );
define( 'PROSLIDE_MIN_ELEMENTOR_VERSION', '3.10.0' );
define( 'PROSLIDE_MIN_PHP_VERSION', '7.4' );

/**
 * Main ProSlide Bootstrap Check.
 */
final class ProSlide_Bootstrap {

	/**
	 * Instance.
	 *
	 * @var ProSlide_Bootstrap|null
	 */
	private static $instance = null;

	/**
	 * Singleton instance.
	 *
	 * @return ProSlide_Bootstrap
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
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Initialize the plugin after plugins are loaded.
	 */
	public function init() {
		load_plugin_textdomain( 'proslide-elementor', false, dirname( plugin_basename( PROSLIDE_FILE ) ) . '/languages' );

		// Check if Elementor is installed and activated.
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check for required Elementor version.
		if ( defined( 'ELEMENTOR_VERSION' ) && ! version_compare( ELEMENTOR_VERSION, PROSLIDE_MIN_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version.
		if ( version_compare( PHP_VERSION, PROSLIDE_MIN_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}

		require_once PROSLIDE_PATH . 'includes/class-proslide-helpers.php';
		require_once PROSLIDE_PATH . 'includes/class-proslide-plugin.php';

		\ProSlide\Plugin::instance();
	}

	/**
	 * Admin notice when Elementor is not active.
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'proslide-elementor' ),
			'<strong>' . esc_html__( 'ProSlide for Elementor', 'proslide-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'proslide-elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice when Elementor version is below minimum.
	 */
	public function admin_notice_minimum_elementor_version() {
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'proslide-elementor' ),
			'<strong>' . esc_html__( 'ProSlide for Elementor', 'proslide-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'proslide-elementor' ) . '</strong>',
			esc_html( PROSLIDE_MIN_ELEMENTOR_VERSION )
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice when PHP version is below minimum.
	 */
	public function admin_notice_minimum_php_version() {
		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'proslide-elementor' ),
			'<strong>' . esc_html__( 'ProSlide for Elementor', 'proslide-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'proslide-elementor' ) . '</strong>',
			esc_html( PROSLIDE_MIN_PHP_VERSION )
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
	}
}

ProSlide_Bootstrap::instance();
