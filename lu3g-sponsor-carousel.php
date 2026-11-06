<?php
/**
 * Plugin Name:       LU3G Sponsor Carousel
 * Description:       Widget Elementor per i loghi degli sponsor: marquee, doppia riga, slider, logo wall e griglia.
 * Version:           0.3.0
 * Plugin URI:        https://github.com/lu3gofficial/lu3g-sponsor-carousel
 * Update URI:        https://github.com/lu3gofficial/lu3g-sponsor-carousel
 * Author:            LU3G Agenzia Web
 * Author URI:        https://lu3g.it
 * Text Domain:       lu3g-sponsor-carousel
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 *
 * @package LU3G_Sponsor_Carousel
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LU3G_SC_VERSION', '0.3.0' );
define( 'LU3G_SC_FILE', __FILE__ );
define( 'LU3G_SC_PATH', plugin_dir_path( __FILE__ ) );
define( 'LU3G_SC_URL', plugin_dir_url( __FILE__ ) );

/** Versione minima di Elementor: da qui esiste l'hook elementor/widgets/register. */
define( 'LU3G_SC_MIN_ELEMENTOR', '3.5.0' );

/**
 * Traduzioni del plugin (cartella languages/).
 */
function lu3g_sc_load_textdomain() {
	load_plugin_textdomain( 'lu3g-sponsor-carousel', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'lu3g_sc_load_textdomain' );

/**
 * Avvio dopo il caricamento dei plugin: senza Elementor, o con una versione troppo vecchia,
 * il widget non viene registrato e in bacheca compare un avviso.
 */
function lu3g_sc_boot() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'lu3g_sc_notice_missing_elementor' );
		return;
	}

	if ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, LU3G_SC_MIN_ELEMENTOR, '<' ) ) {
		add_action( 'admin_notices', 'lu3g_sc_notice_old_elementor' );
		return;
	}

	require_once LU3G_SC_PATH . 'includes/class-plugin.php';
	LU3G_SC_Plugin::instance();
}
add_action( 'plugins_loaded', 'lu3g_sc_boot' );

/**
 * Avviso: Elementor non è attivo.
 */
function lu3g_sc_notice_missing_elementor() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%s</p></div>',
		esc_html__( 'LU3G Sponsor Carousel richiede Elementor attivo.', 'lu3g-sponsor-carousel' )
	);
}

/**
 * Avviso: Elementor è troppo vecchio.
 */
function lu3g_sc_notice_old_elementor() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%s</p></div>',
		esc_html(
			sprintf(
				/* translators: %s: versione minima di Elementor. */
				__( 'LU3G Sponsor Carousel richiede Elementor %s o successivo: aggiorna Elementor.', 'lu3g-sponsor-carousel' ),
				LU3G_SC_MIN_ELEMENTOR
			)
		)
	);
}

/**
 * Aggiornamenti automatici dalle release GitHub di https://github.com/lu3gofficial/lu3g-sponsor-carousel (vedi includes/class-updater.php).
 * Fuori da ogni controllo su Elementor: il plugin si aggiorna anche dove Elementor è disattivato.
 */
require_once __DIR__ . '/includes/class-updater.php';
LU3G_SC_Updater::init( __FILE__ );
