<?php
/**
 * Widget Elementor: LU3G Sponsor Carousel.
 *
 * Step 1: widget registrato e vuoto. Loghi, stili e controlli arrivano negli step successivi.
 *
 * @package LU3G_Sponsor_Carousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Carosello dei loghi degli sponsor.
 */
class LU3G_SC_Sponsor_Carousel_Widget extends Widget_Base {

	/**
	 * Nome interno del widget (usato anche dall'hook JS frontend/element_ready/<nome>.default).
	 *
	 * @return string
	 */
	public function get_name() {
		return 'lu3g-sponsor-carousel';
	}

	/**
	 * Titolo nel pannello.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Sponsor Carousel', 'lu3g-sponsor-carousel' );
	}

	/**
	 * Icona nel pannello.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slider-push';
	}

	/**
	 * Categoria "LU3G".
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'lu3g' );
	}

	/**
	 * Parole per la ricerca nel pannello.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'sponsor', 'loghi', 'logo', 'partner', 'carosello', 'marquee', 'lu3g' );
	}

	/**
	 * CSS caricato solo dove c'è il widget.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'lu3g-sc-sponsor-carousel' );
	}

	/**
	 * JS caricato solo dove c'è il widget.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'lu3g-sc-sponsor-carousel' );
	}

	/**
	 * Controlli. Per ora solo un avviso: il repeater dei loghi arriva nello step 2.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_logos',
			array(
				'label' => esc_html__( 'Loghi', 'lu3g-sponsor-carousel' ),
			)
		);

		$this->add_control(
			'logos_placeholder',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Il widget è attivo. I loghi e gli stili si aggiungono nelle prossime versioni.', 'lu3g-sponsor-carousel' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Markup. Nell'editor mostra un riquadro segnaposto, sul sito non stampa nulla.
	 */
	protected function render() {
		if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return;
		}

		printf(
			'<div class="lu3g-sc lu3g-sc--placeholder">%s</div>',
			esc_html__( 'LU3G Sponsor Carousel', 'lu3g-sponsor-carousel' )
		);
	}
}
