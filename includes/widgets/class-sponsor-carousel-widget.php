<?php
/**
 * Widget Elementor: LU3G Sponsor Carousel.
 *
 * Stili pronti: griglia, marquee, doppia riga. Gli altri sono già nel selettore e, finché non sono
 * pronti, vengono mostrati come griglia con un avviso nell'editor.
 *
 * Solo render(), senza content_template(): il markup usa wp_get_attachment_image (srcset, width e
 * height per evitare il layout shift) e le misure lette dagli SVG, che in JavaScript andrebbero
 * riscritte a parte. Senza template Elementor ridisegna il widget dal server a ogni modifica:
 * l'anteprima nell'editor resta dal vivo, con un breve caricamento.
 *
 * @package LU3G_Sponsor_Carousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

/**
 * Carosello dei loghi degli sponsor.
 */
class LU3G_SC_Sponsor_Carousel_Widget extends Widget_Base {

	/**
	 * Stili già pronti; gli altri, per ora, si vedono come griglia.
	 */
	const READY_STYLES = array( 'griglia', 'marquee', 'doppia_riga' );

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
	 * Stili disponibili.
	 *
	 * @return array
	 */
	private function lu3g_sc_styles() {
		return array(
			'marquee'     => esc_html__( 'Marquee (scorrimento continuo)', 'lu3g-sponsor-carousel' ),
			'doppia_riga' => esc_html__( 'Doppia riga', 'lu3g-sponsor-carousel' ),
			'slider'      => esc_html__( 'Slider', 'lu3g-sponsor-carousel' ),
			'logo_wall'   => esc_html__( 'Logo wall', 'lu3g-sponsor-carousel' ),
			'griglia'     => esc_html__( 'Griglia', 'lu3g-sponsor-carousel' ),
		);
	}

	/**
	 * Controlli.
	 */
	protected function register_controls() {
		$this->lu3g_sc_register_logos_controls();
		$this->lu3g_sc_register_layout_controls();
		$this->lu3g_sc_register_logo_style_controls();
	}

	/**
	 * Contenuto → Loghi: un elemento del repeater per sponsor.
	 */
	private function lu3g_sc_register_logos_controls() {
		$this->start_controls_section(
			'section_logos',
			array(
				'label' => esc_html__( 'Loghi', 'lu3g-sponsor-carousel' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'logo',
			array(
				'label'       => esc_html__( 'Logo', 'lu3g-sponsor-carousel' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'image', 'svg' ),
				'default'     => array( 'url' => Utils::get_placeholder_image_src() ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'logo_alt',
			array(
				'label'       => esc_html__( 'Logo alternativo (facoltativo)', 'lu3g-sponsor-carousel' ),
				'type'        => Controls_Manager::MEDIA,
				'media_types' => array( 'image', 'svg' ),
				'description' => esc_html__( 'Per esempio la versione bianca per gli sfondi scuri. Si usa con "Usa il logo alternativo" in Layout.', 'lu3g-sponsor-carousel' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'name',
			array(
				'label'       => esc_html__( 'Nome dello sponsor', 'lu3g-sponsor-carousel' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => esc_html__( 'Testo alternativo dell\'immagine e, più avanti, tooltip.', 'lu3g-sponsor-carousel' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Link', 'lu3g-sponsor-carousel' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'description' => esc_html__( 'Il link riceve sempre rel="sponsored noopener". "Apri in una nuova scheda" è nelle opzioni del link (ingranaggio).', 'lu3g-sponsor-carousel' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$placeholder = array( 'url' => Utils::get_placeholder_image_src() );

		$this->add_control(
			'logos',
			array(
				'label'       => esc_html__( 'Sponsor', 'lu3g-sponsor-carousel' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ name || "' . esc_html__( 'Sponsor', 'lu3g-sponsor-carousel' ) . '" }}}',
				'default'     => array(
					array( 'logo' => $placeholder, 'name' => esc_html__( 'Sponsor 1', 'lu3g-sponsor-carousel' ) ),
					array( 'logo' => $placeholder, 'name' => esc_html__( 'Sponsor 2', 'lu3g-sponsor-carousel' ) ),
					array( 'logo' => $placeholder, 'name' => esc_html__( 'Sponsor 3', 'lu3g-sponsor-carousel' ) ),
					array( 'logo' => $placeholder, 'name' => esc_html__( 'Sponsor 4', 'lu3g-sponsor-carousel' ) ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'logo_image',
				'default' => 'medium',
				'exclude' => array( 'custom' ),
			)
		);

		$this->add_control(
			'list_label',
			array(
				'label'       => esc_html__( 'Nome per gli screen reader', 'lu3g-sponsor-carousel' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'I nostri sponsor', 'lu3g-sponsor-carousel' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Contenuto → Layout: stile e opzioni che dipendono dallo stile.
	 */
	private function lu3g_sc_register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'lu3g-sponsor-carousel' ),
			)
		);

		$this->add_control(
			'style',
			array(
				'label'   => esc_html__( 'Stile', 'lu3g-sponsor-carousel' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'marquee',
				'options' => $this->lu3g_sc_styles(),
			)
		);

		$this->add_control(
			'style_not_ready',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Questo stile arriva nei prossimi aggiornamenti: per ora i loghi si vedono come griglia.', 'lu3g-sponsor-carousel' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => array( 'style!' => self::READY_STYLES ),
			)
		);

		$this->add_control(
			'use_alt_logo',
			array(
				'label'        => esc_html__( 'Usa il logo alternativo', 'lu3g-sponsor-carousel' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Dove c\'è un logo alternativo, si mostra quello. Gli sponsor senza restano col logo principale.', 'lu3g-sponsor-carousel' ),
			)
		);

		$this->add_responsive_control(
			'grid_columns',
			array(
				'label'          => esc_html__( 'Colonne', 'lu3g-sponsor-carousel' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 1,
				'max'            => 12,
				'default'        => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'selectors'      => array( '{{WRAPPER}}' => '--lu3g-sc-columns: {{VALUE}};' ),
				'condition'      => array( 'style' => 'griglia' ),
			)
		);

		$marquee = array( 'style' => array( 'marquee', 'doppia_riga' ) );

		$this->add_control(
			'marquee_speed',
			array(
				'label'       => esc_html__( 'Durata di un giro (secondi)', 'lu3g-sponsor-carousel' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 's' ),
				'range'       => array( 's' => array( 'min' => 3, 'max' => 180, 'step' => 1 ) ),
				'default'     => array( 'unit' => 's', 'size' => 30 ),
				'description' => esc_html__( 'Tempo in cui scorrono tutti i loghi una volta: più alto = più lento.', 'lu3g-sponsor-carousel' ),
				'selectors'   => array( '{{WRAPPER}}' => '--lu3g-sc-duration: {{SIZE}}s;' ),
				'condition'   => $marquee,
			)
		);

		$this->add_control(
			'marquee_direction',
			array(
				'label'       => esc_html__( 'Direzione', 'lu3g-sponsor-carousel' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'left'  => array(
						'title' => esc_html__( 'Verso sinistra', 'lu3g-sponsor-carousel' ),
						'icon'  => 'eicon-arrow-left',
					),
					'right' => array(
						'title' => esc_html__( 'Verso destra', 'lu3g-sponsor-carousel' ),
						'icon'  => 'eicon-arrow-right',
					),
				),
				'default'     => 'left',
				'toggle'      => false,
				'description' => esc_html__( 'Con la doppia riga, la seconda riga va nella direzione opposta.', 'lu3g-sponsor-carousel' ),
				'condition'   => $marquee,
			)
		);

		$this->add_control(
			'marquee_pause',
			array(
				'label'        => esc_html__( 'Pausa al passaggio del mouse', 'lu3g-sponsor-carousel' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Vale anche quando un logo riceve il focus da tastiera.', 'lu3g-sponsor-carousel' ),
				'condition'    => $marquee,
			)
		);

		$this->add_control(
			'marquee_fade',
			array(
				'label'        => esc_html__( 'Sfumatura ai bordi', 'lu3g-sponsor-carousel' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => $marquee,
			)
		);

		$this->add_responsive_control(
			'marquee_fade_width',
			array(
				'label'      => esc_html__( 'Larghezza della sfumatura', 'lu3g-sponsor-carousel' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 400 ),
					'%'  => array( 'min' => 0, 'max' => 40 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 80 ),
				'selectors'  => array( '{{WRAPPER}}' => '--lu3g-sc-fade: {{SIZE}}{{UNIT}};' ),
				'condition'  => $marquee + array( 'marquee_fade' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Stile → Loghi: misure, spaziatura e allineamento (il resto della tab Stile arriva nello step 6).
	 */
	private function lu3g_sc_register_logo_style_controls() {
		$this->start_controls_section(
			'section_logo_style',
			array(
				'label' => esc_html__( 'Loghi', 'lu3g-sponsor-carousel' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'logo_max_height',
			array(
				'label'      => esc_html__( 'Altezza massima', 'lu3g-sponsor-carousel' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 10, 'max' => 400 ),
					'vh' => array( 'min' => 1, 'max' => 50 ),
				),
				'selectors'  => array( '{{WRAPPER}}' => '--lu3g-sc-logo-height: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'logo_max_width',
			array(
				'label'      => esc_html__( 'Larghezza massima', 'lu3g-sponsor-carousel' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array( 'min' => 20, 'max' => 600 ),
					'%'  => array( 'min' => 10, 'max' => 100 ),
				),
				'selectors'  => array( '{{WRAPPER}}' => '--lu3g-sc-logo-width: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_responsive_control(
			'logo_gap',
			array(
				'label'      => esc_html__( 'Spaziatura tra i loghi', 'lu3g-sponsor-carousel' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 200 ),
					'em' => array( 'min' => 0, 'max' => 10 ),
				),
				'selectors'  => array( '{{WRAPPER}}' => '--lu3g-sc-gap: {{SIZE}}{{UNIT}};' ),
			)
		);

		$this->add_control(
			'logo_valign',
			array(
				'label'     => esc_html__( 'Allineamento verticale', 'lu3g-sponsor-carousel' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'In alto', 'lu3g-sponsor-carousel' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Al centro', 'lu3g-sponsor-carousel' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'In basso', 'lu3g-sponsor-carousel' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors' => array( '{{WRAPPER}}' => '--lu3g-sc-align: {{VALUE}};' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Markup.
	 */
	protected function render() {
		$settings  = $this->get_settings_for_display();
		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode();
		$logos     = isset( $settings['logos'] ) && is_array( $settings['logos'] ) ? $settings['logos'] : array();
		$items     = $this->lu3g_sc_items( $logos, $settings );

		if ( empty( $items ) ) {
			if ( $is_editor ) {
				printf(
					'<div class="lu3g-sc lu3g-sc--placeholder">%s</div>',
					esc_html__( 'Aggiungi almeno un logo nella sezione Loghi.', 'lu3g-sponsor-carousel' )
				);
			}
			return;
		}

		$style = isset( $this->lu3g_sc_styles()[ $settings['style'] ] ) ? $settings['style'] : 'marquee';

		// Stili non ancora pronti: per ora griglia (l'avviso è nel pannello).
		$layout = in_array( $style, self::READY_STYLES, true ) ? $style : 'griglia';

		// Doppia riga con un solo logo: una riga sola.
		if ( 'doppia_riga' === $layout && count( $items ) < 2 ) {
			$layout = 'marquee';
		}

		$classes = array( 'lu3g-sc', 'lu3g-sc--' . $layout );

		if ( 'griglia' !== $layout ) {
			$classes[] = 'lu3g-sc--moving';

			if ( 'yes' === $settings['marquee_pause'] ) {
				$classes[] = 'lu3g-sc--pause';
			}

			if ( 'yes' === $settings['marquee_fade'] ) {
				$classes[] = 'lu3g-sc--fade';
			}
		}

		$this->add_render_attribute(
			'wrapper',
			array(
				'class'      => $classes,
				'data-style' => $style,
				'role'       => 'region',
				'aria-label' => $settings['list_label'] ? $settings['list_label'] : esc_html__( 'I nostri sponsor', 'lu3g-sponsor-carousel' ),
			)
		);

		echo '<div ' . $this->get_render_attribute_string( 'wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput

		if ( 'griglia' === $layout ) {
			echo $this->lu3g_sc_list( $items ); // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			$reverse = 'right' === $settings['marquee_direction'];

			if ( 'doppia_riga' === $layout ) {
				// Loghi alternati tra le due righe: 1, 3, 5... sopra e 2, 4, 6... sotto.
				$rows = array( array(), array() );

				foreach ( $items as $n => $item ) {
					$rows[ $n % 2 ][] = $item;
				}

				echo $this->lu3g_sc_marquee_row( $rows[0], $reverse );  // phpcs:ignore WordPress.Security.EscapeOutput
				echo $this->lu3g_sc_marquee_row( $rows[1], ! $reverse ); // phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				echo $this->lu3g_sc_marquee_row( $items, $reverse ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}

		echo '</div>';
	}

	/**
	 * Lista dei loghi.
	 *
	 * @param array $items Markup dei loghi.
	 * @param bool  $copy  Copia per il marquee: nascosta agli screen reader e fuori dal Tab.
	 * @return string
	 */
	private function lu3g_sc_list( array $items, $copy = false ) {
		$out = $copy ? '<ul class="lu3g-sc__list" aria-hidden="true">' : '<ul class="lu3g-sc__list">';

		foreach ( $items as $item ) {
			if ( $copy ) {
				$item = str_replace( '<a ', '<a tabindex="-1" ', $item );
			}

			$out .= '<li class="lu3g-sc__item">' . $item . '</li>';
		}

		return $out . '</ul>';
	}

	/**
	 * Una riga del marquee: la lista e la sua copia una dopo l'altra. L'animazione sposta la
	 * traccia di metà della sua larghezza (cioè di una lista), poi riparte: il giro non ha stacchi.
	 *
	 * @param array $items   Markup dei loghi.
	 * @param bool  $reverse Verso destra invece che verso sinistra.
	 * @return string
	 */
	private function lu3g_sc_marquee_row( array $items, $reverse ) {
		return '<div class="lu3g-sc__row' . ( $reverse ? ' lu3g-sc__row--reverse' : '' ) . '">'
			. '<div class="lu3g-sc__track">'
			. $this->lu3g_sc_list( $items )
			. $this->lu3g_sc_list( $items, true )
			. '</div></div>';
	}

	/**
	 * Markup di tutti i loghi; quelli senza immagine vengono saltati.
	 *
	 * @param array $logos    Righe del repeater.
	 * @param array $settings Impostazioni del widget.
	 * @return array
	 */
	private function lu3g_sc_items( array $logos, array $settings ) {
		$items = array();

		foreach ( $logos as $index => $logo ) {
			$markup = $this->lu3g_sc_item_markup( $logo, $settings, $index );

			if ( '' !== $markup ) {
				$items[] = $markup;
			}
		}

		return $items;
	}

	/**
	 * Logo (con link, se c'è).
	 *
	 * @param array $logo     Riga del repeater.
	 * @param array $settings Impostazioni del widget.
	 * @param int   $index    Posizione, per le chiavi degli attributi.
	 * @return string
	 */
	private function lu3g_sc_item_markup( array $logo, array $settings, $index ) {
		$media = isset( $logo['logo'] ) && is_array( $logo['logo'] ) ? $logo['logo'] : array();

		// Logo alternativo, se richiesto e caricato.
		if ( 'yes' === $settings['use_alt_logo'] && ! empty( $logo['logo_alt']['url'] ) ) {
			$media = $logo['logo_alt'];
		}

		$name = isset( $logo['name'] ) ? trim( wp_strip_all_tags( (string) $logo['name'] ) ) : '';
		$img  = $this->lu3g_sc_image_markup( $media, $name, $settings );

		// Nel marquee i loghi fuori dal bordo entrano in vista scorrendo: con loading="lazy" si
		// caricherebbero solo in quel momento e comparirebbero di colpo. Lì si caricano subito.
		if ( in_array( $settings['style'], array( 'marquee', 'doppia_riga' ), true ) ) {
			$img = str_replace( 'loading="lazy"', 'loading="eager"', $img );
		}

		if ( '' === $img ) {
			return '';
		}

		if ( empty( $logo['link']['url'] ) ) {
			return '<span class="lu3g-sc__logo">' . $img . '</span>';
		}

		$key = 'link_' . $index;

		// href, nuova scheda, nofollow e attributi personalizzati come li imposta Elementor;
		// "sponsored noopener" sempre, come chiedono le linee guida sui link a pagamento.
		$this->add_link_attributes( $key, $logo['link'] );
		$this->add_render_attribute(
			$key,
			array(
				'class' => 'lu3g-sc__logo',
				'rel'   => array( 'sponsored', 'noopener' ),
			)
		);

		if ( '' !== $name ) {
			$this->add_render_attribute( $key, 'aria-label', $name );
		}

		return '<a ' . $this->get_render_attribute_string( $key ) . '>' . $img . '</a>';
	}

	/**
	 * Tag img del logo: wp_get_attachment_image per i file della libreria (srcset, width e height),
	 * misure lette dal file per gli SVG, tag semplice per un URL esterno o il segnaposto.
	 *
	 * @param array  $media    Valore del controllo MEDIA.
	 * @param string $name     Nome dello sponsor (alt).
	 * @param array  $settings Impostazioni del widget.
	 * @return string
	 */
	private function lu3g_sc_image_markup( array $media, $name, array $settings ) {
		$id  = isset( $media['id'] ) ? (int) $media['id'] : 0;
		$url = isset( $media['url'] ) ? (string) $media['url'] : '';

		if ( ! $id && '' === $url ) {
			return '';
		}

		$attrs = array(
			'class'    => 'lu3g-sc__img',
			'loading'  => 'lazy',
			'decoding' => 'async',
		);

		if ( $id && 'image/svg+xml' === get_post_mime_type( $id ) ) {
			list( $width, $height ) = $this->lu3g_sc_svg_size( $id );

			return sprintf(
				'<img src="%s" alt="%s" class="lu3g-sc__img" loading="lazy" decoding="async"%s>',
				esc_url( wp_get_attachment_url( $id ) ),
				esc_attr( '' !== $name ? $name : $this->lu3g_sc_attachment_alt( $id ) ),
				$width ? sprintf( ' width="%d" height="%d"', $width, $height ) : ''
			);
		}

		if ( $id && wp_attachment_is_image( $id ) ) {
			$size = isset( $settings['logo_image_size'] ) && $settings['logo_image_size'] ? $settings['logo_image_size'] : 'medium';

			$attrs['alt'] = '' !== $name ? $name : $this->lu3g_sc_attachment_alt( $id );

			return (string) wp_get_attachment_image( $id, $size, false, $attrs );
		}

		return sprintf(
			'<img src="%s" alt="%s" class="lu3g-sc__img" loading="lazy" decoding="async">',
			esc_url( $url ),
			esc_attr( $name )
		);
	}

	/**
	 * Testo alternativo salvato nella libreria, se il nome dello sponsor è vuoto.
	 *
	 * @param int $id ID dell'allegato.
	 * @return string
	 */
	private function lu3g_sc_attachment_alt( $id ) {
		return trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	}

	/**
	 * Larghezza e altezza di un SVG (attributi width/height o viewBox), per evitare il layout shift.
	 *
	 * @param int $id ID dell'allegato.
	 * @return array [ larghezza, altezza ], [ 0, 0 ] se non si ricavano.
	 */
	private function lu3g_sc_svg_size( $id ) {
		$file = get_attached_file( $id );

		if ( ! $file || ! is_readable( $file ) || filesize( $file ) > 512000 ) {
			return array( 0, 0 );
		}

		// Basta l'inizio del file: lì c'è il tag <svg>.
		$head = (string) file_get_contents( $file, false, null, 0, 4096 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( ! preg_match( '/<svg\b[^>]*>/i', $head, $tag ) ) {
			return array( 0, 0 );
		}

		$tag = $tag[0];

		if ( preg_match( '/\swidth=["\']\s*([\d.]+)(px)?\s*["\']/i', $tag, $w ) && preg_match( '/\sheight=["\']\s*([\d.]+)(px)?\s*["\']/i', $tag, $h ) ) {
			return array( (int) round( (float) $w[1] ), (int) round( (float) $h[1] ) );
		}

		if ( preg_match( '/viewBox=["\']\s*[-\d.]+[\s,]+[-\d.]+[\s,]+([\d.]+)[\s,]+([\d.]+)\s*["\']/i', $tag, $box ) ) {
			return array( (int) round( (float) $box[1] ), (int) round( (float) $box[2] ) );
		}

		return array( 0, 0 );
	}
}
