/**
 * LU3G Sponsor Carousel – handler frontend di Elementor.
 *
 * La classe estende elementorModules.frontend.handlers.Base: Elementor la crea per ogni
 * widget nella pagina e la ricrea nell'anteprima dell'editor a ogni modifica dei controlli,
 * chiamando onDestroy sulla vecchia istanza. Lì vanno fermati interval e istanze (Swiper,
 * logo wall) che arrivano negli step successivi.
 */
( function () {
	'use strict';

	function register() {
		var Base = window.elementorModules && window.elementorModules.frontend.handlers.Base;

		if ( ! Base ) {
			return;
		}

		var SponsorCarousel = Base.extend( {
			/**
			 * Avvio del widget.
			 */
			onInit: function () {
				Base.prototype.onInit.apply( this, arguments );
				this.timers = [];
			},

			/**
			 * Il widget viene tolto o ridisegnato: si ferma tutto ciò che è partito.
			 */
			onDestroy: function () {
				( this.timers || [] ).forEach( function ( id ) {
					window.clearInterval( id );
					window.clearTimeout( id );
				} );
				this.timers = [];

				if ( Base.prototype.onDestroy ) {
					Base.prototype.onDestroy.apply( this, arguments );
				}
			},
		} );

		window.elementorFrontend.hooks.addAction( 'frontend/element_ready/lu3g-sponsor-carousel.default', function ( $element ) {
			window.elementorFrontend.elementsHandler.addHandler( SponsorCarousel, { $element: $element } );
		} );
	}

	// Il frontend di Elementor può essere già pronto oppure no. L'evento di avvio è un evento
	// jQuery (Elementor lo lancia con jQuery( window ).trigger): addEventListener non lo riceve,
	// quindi qui jQuery è necessario.
	if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
		register();
	} else if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', register );
	}
} )();
