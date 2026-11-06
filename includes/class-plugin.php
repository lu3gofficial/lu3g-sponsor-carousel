<?php
/**
 * Integrazione con Elementor: categoria "LU3G", asset e registrazione del widget.
 *
 * @package LU3G_Sponsor_Carousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin (istanza unica).
 */
final class LU3G_SC_Plugin {

	/**
	 * Istanza unica.
	 *
	 * @var LU3G_SC_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Restituisce l'istanza unica.
	 *
	 * @return LU3G_SC_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Aggancia gli hook di Elementor.
	 */
	private function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		// Asset solo registrati: il widget li dichiara in get_style_depends / get_script_depends,
		// così Elementor li carica soltanto nelle pagine in cui il widget è presente.
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_styles' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ) );
	}

	/**
	 * Categoria "LU3G" nel pannello dei widget, condivisa con gli altri plugin LU3G:
	 * si aggiunge solo se nessuno l'ha già registrata.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Gestore degli elementi.
	 */
	public function register_category( $elements_manager ) {
		$categories = method_exists( $elements_manager, 'get_categories' ) ? $elements_manager->get_categories() : array();

		if ( ! isset( $categories['lu3g'] ) ) {
			$elements_manager->add_category(
				'lu3g',
				array(
					'title' => 'LU3G',
					'icon'  => 'fa fa-plug',
				)
			);
		}
	}

	/**
	 * Registra il widget.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Gestore dei widget.
	 */
	public function register_widgets( $widgets_manager ) {
		require_once LU3G_SC_PATH . 'includes/widgets/class-sponsor-carousel-widget.php';
		$widgets_manager->register( new LU3G_SC_Sponsor_Carousel_Widget() );
	}

	/**
	 * Foglio di stile del widget.
	 */
	public function register_styles() {
		wp_register_style(
			'lu3g-sc-sponsor-carousel',
			LU3G_SC_URL . 'assets/css/sponsor-carousel.css',
			array(),
			LU3G_SC_VERSION
		);
	}

	/**
	 * Script del widget: dipende dal frontend di Elementor, che fornisce
	 * elementorModules e gli hook frontend/element_ready.
	 */
	public function register_scripts() {
		wp_register_script(
			'lu3g-sc-sponsor-carousel',
			LU3G_SC_URL . 'assets/js/sponsor-carousel.js',
			array( 'elementor-frontend' ),
			LU3G_SC_VERSION,
			true
		);
	}
}
