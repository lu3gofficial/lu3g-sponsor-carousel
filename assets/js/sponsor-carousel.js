/**
 * LU3G Sponsor Carousel – handler frontend di Elementor.
 *
 * La classe estende elementorModules.frontend.handlers.Base: Elementor la crea per ogni
 * widget nella pagina e la ricrea nell'anteprima dell'editor a ogni modifica dei controlli,
 * chiamando onDestroy sulla vecchia istanza. Lì si fermano osservatori e timer.
 *
 * Marquee e doppia riga: lo scorrimento è solo CSS (traccia = lista + copia, spostata di -50%).
 * Qui si riempie la riga quando i loghi sono pochi: se una lista è più stretta della riga, i loghi
 * vengono ripetuti dentro entrambe le liste (le ripetizioni sono nascoste agli screen reader) e
 * --lu3g-sc-repeat allunga la durata nella stessa misura, così la velocità resta quella scelta.
 */
( function () {
	'use strict';

	/**
	 * Riempie una riga del marquee.
	 *
	 * @param {HTMLElement} row Riga (.lu3g-sc__row).
	 */
	function fillRow( row ) {
		var lists = row.querySelectorAll( '.lu3g-sc__list' );

		if ( lists.length < 2 ) {
			return;
		}

		// Si riparte sempre dai loghi originali.
		Array.prototype.forEach.call( row.querySelectorAll( '.lu3g-sc__item--clone' ), function ( clone ) {
			clone.remove();
		} );
		row.style.removeProperty( '--lu3g-sc-repeat' );

		var originals = Array.prototype.slice.call( lists[ 0 ].children );
		var setWidth = lists[ 0 ].getBoundingClientRect().width;
		var rowWidth = row.getBoundingClientRect().width;

		if ( ! originals.length || ! setWidth || ! rowWidth ) {
			return;
		}

		var repeat = Math.max( 1, Math.ceil( rowWidth / setWidth ) );

		for ( var i = 1; i < repeat; i++ ) {
			Array.prototype.forEach.call( lists, function ( list ) {
				originals.forEach( function ( item ) {
					var clone = item.cloneNode( true );

					clone.classList.add( 'lu3g-sc__item--clone' );
					clone.setAttribute( 'aria-hidden', 'true' );
					Array.prototype.forEach.call( clone.querySelectorAll( 'a' ), function ( link ) {
						link.setAttribute( 'tabindex', '-1' );
					} );
					list.appendChild( clone );
				} );
			} );
		}

		row.style.setProperty( '--lu3g-sc-repeat', String( repeat ) );
	}

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

				var root = this.$element[ 0 ].querySelector( '.lu3g-sc--moving' );

				if ( ! root ) {
					return;
				}

				var rows = Array.prototype.slice.call( root.querySelectorAll( '.lu3g-sc__row' ) );
				var fill = function () {
					rows.forEach( fillRow );
				};

				fill();

				// Si ricalcola quando cambia la larghezza (finestra, dispositivo nell'editor) e quando
				// le immagini senza misure finiscono di caricarsi.
				if ( window.ResizeObserver ) {
					var lastWidth = 0;

					this.lu3gObserver = new window.ResizeObserver( function ( entries ) {
						var width = Math.round( entries[ 0 ].contentRect.width );

						if ( width !== lastWidth ) {
							lastWidth = width;
							fill();
						}
					} );
					this.lu3gObserver.observe( root );
				}

				Array.prototype.forEach.call( root.querySelectorAll( 'img' ), function ( img ) {
					if ( ! img.complete ) {
						img.addEventListener( 'load', fill, { once: true } );
					}
				} );
			},

			/**
			 * Il widget viene tolto o ridisegnato: si ferma tutto ciò che è partito.
			 */
			onDestroy: function () {
				if ( this.lu3gObserver ) {
					this.lu3gObserver.disconnect();
					this.lu3gObserver = null;
				}

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
