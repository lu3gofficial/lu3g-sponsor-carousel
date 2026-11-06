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
 *
 * Slider: Swiper di Elementor (elementorFrontend.utils.swiper, che carica Swiper 5 o 8 secondo la
 * versione), con le opzioni dei controlli lette da getElementSettings() (controlli frontend_available).
 *
 * Logo wall: il markup ha tutti i loghi in griglia (così resta anche senza JS e con movimento ridotto).
 * Qui restano visibili i primi N elementi (slot, per dispositivo); gli altri sono messi da parte. A ogni
 * intervallo il logo di uno slot a caso svanisce e al suo posto entra un logo messo da parte: ogni logo
 * esiste una volta sola, quindi a schermo non ce ne sono mai due uguali.
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

	/**
	 * Valore di una variabile CSS in pixel (px o em), per lo spazio tra le slide.
	 *
	 * @param {HTMLElement} el   Elemento da cui leggere.
	 * @param {string}      name Nome della variabile.
	 * @param {number}      def  Valore predefinito.
	 * @return {number}
	 */
	function cssPixels( el, name, def ) {
		var style = window.getComputedStyle( el );
		var value = style.getPropertyValue( name ).trim();
		var num = parseFloat( value );

		if ( isNaN( num ) ) {
			return def;
		}

		if ( /em$/.test( value ) ) {
			return num * parseFloat( style.fontSize );
		}

		return num;
	}

	/**
	 * Opzioni di Swiper dai controlli del widget. Le "slide visibili" seguono i punti di passaggio di
	 * Elementor: Swiper conta da mobile in su, quindi tablet parte da mobile+1 e desktop da tablet+1.
	 *
	 * @param {Object}      settings Impostazioni del widget (getElementSettings).
	 * @param {HTMLElement} root     Contenitore del widget.
	 * @param {number}      count    Numero di loghi.
	 * @param {boolean}     reduced  Movimento ridotto.
	 * @return {Object}
	 */
	function sliderConfig( settings, root, count, reduced ) {
		var bp = ( window.elementorFrontend && window.elementorFrontend.config && window.elementorFrontend.config.responsive && window.elementorFrontend.config.responsive.activeBreakpoints ) || {};
		var mobile = bp.mobile ? bp.mobile.value : 767;
		var tablet = bp.tablet ? bp.tablet.value : 1024;
		var num = function ( value, def ) {
			var n = parseInt( value, 10 );
			return isNaN( n ) || n < 1 ? def : n;
		};
		var visibleDesktop = num( settings.slider_visible, 4 );
		var visibleTablet = num( settings.slider_visible_tablet, visibleDesktop );
		var visibleMobile = num( settings.slider_visible_mobile, visibleTablet );
		var gap = cssPixels( root, '--lu3g-sc-gap', 32 );
		var config = {
			slidesPerView: Math.min( visibleMobile, count ),
			spaceBetween: gap,
			speed: reduced ? 0 : num( settings.slider_speed, 600 ),
			// Il ciclo serve solo se ci sono più loghi di quelli visibili insieme.
			loop: 'yes' === settings.slider_loop && count > Math.max( visibleDesktop, visibleTablet, visibleMobile ),
			watchOverflow: true,
			a11y: { enabled: true },
			breakpoints: {},
		};

		config.breakpoints[ mobile + 1 ] = { slidesPerView: Math.min( visibleTablet, count ) };
		config.breakpoints[ tablet + 1 ] = { slidesPerView: Math.min( visibleDesktop, count ) };

		if ( 'yes' === settings.slider_autoplay && ! reduced ) {
			config.autoplay = { delay: num( settings.slider_interval, 3000 ), disableOnInteraction: false };
		}

		if ( 'yes' === settings.slider_arrows ) {
			config.navigation = {
				prevEl: root.querySelector( '.lu3g-sc__arrow--prev' ),
				nextEl: root.querySelector( '.lu3g-sc__arrow--next' ),
			};
		}

		if ( 'yes' === settings.slider_dots ) {
			config.pagination = { el: root.querySelector( '.lu3g-sc__dots' ), clickable: true };
		}

		return config;
	}

	/**
	 * Valore di un controllo responsive per la larghezza attuale (punti di passaggio di Elementor).
	 *
	 * @param {Object} settings Impostazioni del widget.
	 * @param {string} key      Nome del controllo.
	 * @param {number} def      Valore predefinito.
	 * @return {number}
	 */
	function responsiveNumber( settings, key, def ) {
		var bp = ( window.elementorFrontend && window.elementorFrontend.config && window.elementorFrontend.config.responsive && window.elementorFrontend.config.responsive.activeBreakpoints ) || {};
		var width = window.innerWidth;
		var num = function ( value ) {
			var n = parseInt( value, 10 );
			return isNaN( n ) || n < 1 ? null : n;
		};
		var desktop = num( settings[ key ] ) || def;
		var tablet = num( settings[ key + '_tablet' ] ) || desktop;
		var mobile = num( settings[ key + '_mobile' ] ) || tablet;

		if ( width <= ( bp.mobile ? bp.mobile.value : 767 ) ) {
			return mobile;
		}

		if ( width <= ( bp.tablet ? bp.tablet.value : 1024 ) ) {
			return tablet;
		}

		return desktop;
	}

	/**
	 * Prepara il logo in arrivo: se l'immagine non è ancora caricata (loading="lazy" su un elemento
	 * nascosto) la carica prima, così entra già intera.
	 *
	 * @param {HTMLElement} logo Logo (.lu3g-sc__logo).
	 * @param {Function}    done Da chiamare quando è pronto.
	 */
	function whenLoaded( logo, done ) {
		var img = logo.querySelector( 'img' );

		if ( ! img || img.complete ) {
			done();
			return;
		}

		var called = false;
		var finish = function () {
			if ( ! called ) {
				called = true;
				done();
			}
		};

		img.setAttribute( 'loading', 'eager' );
		img.addEventListener( 'load', finish, { once: true } );
		img.addEventListener( 'error', finish, { once: true } );
		window.setTimeout( finish, 4000 );
	}

	/**
	 * Tooltip col nome dello sponsor: un solo elemento (.lu3g-sc__tip) nel widget, fuori dalle righe
	 * che scorrono, posizionato sopra il logo al passaggio del mouse e col focus da tastiera.
	 *
	 * @param {HTMLElement} root Contenitore del widget.
	 */
	function setupTooltip( root ) {
		var tip = root.querySelector( '.lu3g-sc__tip' );

		if ( ! tip ) {
			return;
		}

		var current = null;

		var show = function ( logo ) {
			var box = root.getBoundingClientRect();
			var rect = logo.getBoundingClientRect();

			current = logo;
			tip.textContent = logo.getAttribute( 'data-lu3g-sc-tip' );
			tip.style.left = ( rect.left - box.left + rect.width / 2 ) + 'px';
			tip.style.top = ( rect.top - box.top ) + 'px';
			tip.classList.add( 'is-visible' );
		};

		var hide = function () {
			current = null;
			tip.classList.remove( 'is-visible' );
		};

		root.addEventListener( 'mouseover', function ( e ) {
			var logo = e.target.closest( '[data-lu3g-sc-tip]' );

			if ( logo && logo !== current ) {
				show( logo );
			}
		} );

		root.addEventListener( 'mouseout', function ( e ) {
			if ( current && ! current.contains( e.relatedTarget ) ) {
				hide();
			}
		} );

		root.addEventListener( 'focusin', function ( e ) {
			var logo = e.target.closest( '[data-lu3g-sc-tip]' );

			if ( logo ) {
				show( logo );
			}
		} );

		root.addEventListener( 'focusout', hide );
	}

	/**
	 * Pulsante pausa: aggiunge o toglie lu3g-sc--paused sul widget e avvisa chi deve fermarsi.
	 *
	 * @param {HTMLElement} root     Contenitore del widget.
	 * @param {Function}    onChange Chiamata con true (in pausa) o false.
	 */
	function setupPauseButton( root, onChange ) {
		var button = root.querySelector( '.lu3g-sc__pause' );

		if ( ! button ) {
			return;
		}

		button.addEventListener( 'click', function () {
			var paused = ! root.classList.contains( 'lu3g-sc--paused' );

			root.classList.toggle( 'lu3g-sc--paused', paused );
			button.setAttribute( 'aria-pressed', paused ? 'true' : 'false' );
			button.setAttribute( 'aria-label', button.getAttribute( paused ? 'data-label-play' : 'data-label-pause' ) );

			if ( onChange ) {
				onChange( paused );
			}
		} );
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

				this.timers = [];

				var widget = this.$element[ 0 ].querySelector( '.lu3g-sc' );

				if ( widget ) {
					setupTooltip( widget );
				}

				var slider = this.$element[ 0 ].querySelector( '.lu3g-sc--slider' );

				if ( slider ) {
					this.lu3gInitSlider( slider );
					return;
				}

				var wall = this.$element[ 0 ].querySelector( '.lu3g-sc--logo_wall' );

				if ( wall ) {
					this.lu3gInitWall( wall );
					return;
				}

				var root = this.$element[ 0 ].querySelector( '.lu3g-sc--moving' );

				if ( ! root ) {
					return;
				}

				setupPauseButton( root );

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
			 * Logo wall.
			 *
			 * @param {HTMLElement} root Contenitore del widget.
			 */
			lu3gInitWall: function ( root ) {
				var self = this;
				var settings = this.getElementSettings();
				var items = Array.prototype.slice.call( root.querySelectorAll( '.lu3g-sc__item' ) );
				var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
				var interval = Math.max( 500, parseInt( settings.wall_interval, 10 ) || 2500 );
				var fade = Math.max( 0, parseInt( settings.wall_fade, 10 ) );
				var paused = false;
				var busy = false;
				var lastSlot = -1;
				var slots = 0;

				if ( isNaN( fade ) ) {
					fade = 600;
				}

				// Movimento ridotto: resta la griglia con tutti i loghi.
				if ( reduced || items.length < 2 ) {
					return;
				}

				// Primi N elementi visibili, gli altri da parte.
				var layout = function () {
					slots = Math.min( responsiveNumber( settings, 'wall_slots', 6 ), items.length );
					items.forEach( function ( item, n ) {
						item.classList.toggle( 'is-pooled', n >= slots );
					} );
				};

				var swap = function () {
					if ( paused || busy || document.hidden || slots >= items.length || root.classList.contains( 'lu3g-sc--paused' ) ) {
						return;
					}

					var pool = items.slice( slots );
					var slot = Math.floor( Math.random() * slots );

					// Mai lo stesso slot due volte di fila, se ce n'è più di uno.
					if ( slots > 1 && slot === lastSlot ) {
						slot = ( slot + 1 + Math.floor( Math.random() * ( slots - 1 ) ) ) % slots;
					}

					var target = items[ slot ];
					var source = pool[ Math.floor( Math.random() * pool.length ) ];
					var outgoing = target.querySelector( '.lu3g-sc__logo' );
					var incoming = source.querySelector( '.lu3g-sc__logo' );

					if ( ! outgoing || ! incoming ) {
						return;
					}

					busy = true;
					lastSlot = slot;

					whenLoaded( incoming, function () {
						outgoing.classList.add( 'is-leaving' );

						self.timers.push( window.setTimeout( function () {
							// Scambio dei loghi: quello uscito va da parte, quello nuovo entra trasparente e compare.
							incoming.classList.add( 'is-leaving' );
							source.appendChild( outgoing );
							target.appendChild( incoming );
							outgoing.classList.remove( 'is-leaving' );
							void incoming.offsetWidth; // nuovo calcolo: la dissolvenza in entrata parte da 0
							incoming.classList.remove( 'is-leaving' );
							busy = false;
						}, fade ) );
					} );
				};

				layout();
				this.timers.push( window.setInterval( swap, interval ) );
				setupPauseButton( root );

				// Pausa al passaggio del mouse e col focus da tastiera.
				root.addEventListener( 'mouseenter', function () {
					paused = true;
				} );
				root.addEventListener( 'mouseleave', function () {
					paused = root.contains( document.activeElement );
				} );
				root.addEventListener( 'focusin', function () {
					paused = true;
				} );
				root.addEventListener( 'focusout', function () {
					window.setTimeout( function () {
						paused = root.contains( document.activeElement ) || root.matches( ':hover' );
					}, 0 );
				} );

				// Il numero di slot cambia col dispositivo.
				if ( window.ResizeObserver ) {
					var lastWidth = 0;

					this.lu3gObserver = new window.ResizeObserver( function () {
						if ( window.innerWidth !== lastWidth ) {
							lastWidth = window.innerWidth;
							layout();
						}
					} );
					this.lu3gObserver.observe( root );
				}

				// Per le prove: la sostituzione si può chiamare a mano.
				this.lu3gSwap = swap;
			},

			/**
			 * Slider con lo Swiper di Elementor.
			 *
			 * @param {HTMLElement} root Contenitore del widget.
			 */
			lu3gInitSlider: function ( root ) {
				var self = this;
				var container = root.querySelector( '.lu3g-sc__swiper' );
				var SwiperClass = ( window.elementorFrontend.utils && window.elementorFrontend.utils.swiper ) || window.Swiper;
				var reduced = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
				var swiperClass = window.elementorFrontend.config && window.elementorFrontend.config.swiperClass;

				if ( ! container || ! SwiperClass ) {
					return;
				}

				// Classe del contenitore che la versione di Swiper caricata si aspetta.
				if ( swiperClass && ! container.classList.contains( swiperClass ) ) {
					container.classList.remove( 'swiper', 'swiper-container' );
					container.classList.add( swiperClass );
				}

				var count = container.querySelectorAll( '.swiper-slide' ).length;
				var config = sliderConfig( this.getElementSettings(), root, count, reduced );

				// Con Elementor il costruttore restituisce una promessa (Swiper viene caricato quando serve).
				Promise.resolve( new SwiperClass( container, config ) ).then( function ( swiper ) {
					if ( self.lu3gDestroyed ) {
						swiper.destroy( true, true );
						return;
					}

					self.lu3gSwiper = swiper;

					if ( ! config.autoplay ) {
						return;
					}

					// Pausa al passaggio del mouse e col focus da tastiera (vale per Swiper 5 e 8).
					var stop = function () {
						if ( swiper.autoplay ) {
							swiper.autoplay.stop();
						}
					};
					var start = function () {
						if ( swiper.autoplay && ! root.classList.contains( 'lu3g-sc--paused' ) && ! root.contains( document.activeElement ) && ! root.matches( ':hover' ) ) {
							swiper.autoplay.start();
						}
					};

					setupPauseButton( root, function ( paused ) {
						if ( paused ) {
							stop();
						} else if ( swiper.autoplay ) {
							swiper.autoplay.start();
						}
					} );

					root.addEventListener( 'mouseenter', stop );
					root.addEventListener( 'mouseleave', start );
					root.addEventListener( 'focusin', stop );
					root.addEventListener( 'focusout', function () {
						window.setTimeout( start, 0 );
					} );
				} );

				// Lo spazio tra i loghi può cambiare col dispositivo: si rilegge al cambio di larghezza.
				if ( window.ResizeObserver ) {
					this.lu3gObserver = new window.ResizeObserver( function () {
						var swiper = self.lu3gSwiper;
						var gap = cssPixels( root, '--lu3g-sc-gap', 32 );

						if ( swiper && swiper.params && swiper.params.spaceBetween !== gap ) {
							swiper.params.spaceBetween = gap;
							swiper.update();
						}
					} );
					this.lu3gObserver.observe( root );
				}
			},

			/**
			 * Il widget viene tolto o ridisegnato: si ferma tutto ciò che è partito.
			 */
			onDestroy: function () {
				this.lu3gDestroyed = true;

				( this.timers || [] ).forEach( function ( id ) {
					window.clearInterval( id );
					window.clearTimeout( id );
				} );
				this.timers = [];

				if ( this.lu3gSwiper ) {
					this.lu3gSwiper.destroy( true, true );
					this.lu3gSwiper = null;
				}

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
