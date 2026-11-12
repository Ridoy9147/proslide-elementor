( function ( $ ) {
	'use strict';

	/**
	 * Prepare nested slider child containers in Elementor editor preview.
	 */
	function prepareNestedEditorSlides( $wrapper ) {
		if ( ! $wrapper.hasClass( 'proslide-is-nested' ) ) {
			return;
		}

		var $nestedWrap = $wrapper.find( '.proslide-nested-wrapper' ).first();
		var rawBgs = $wrapper.attr( 'data-nested-bgs' );
		var bgs = [];

		if ( rawBgs ) {
			try {
				bgs = JSON.parse( rawBgs );
			} catch ( e ) {
				bgs = [];
			}
		}

		$nestedWrap.children( '.elementor-element, .e-con' ).each( function ( idx ) {
			var $child = $( this );
			$child.addClass( 'swiper-slide proslide-slide' );

			if ( bgs[ idx ] ) {
				var bg = bgs[ idx ];
				$child.css( {
					'background-color': bg.color || '',
					'background-image': bg.image ? 'url("' + bg.image + '")' : '',
					'background-size': bg.size || 'cover',
					'background-position': bg.position || 'center center',
					'background-repeat': 'no-repeat'
				} );
			}
		} );
	}

	/**
	 * Build Swiper options object from widget data-proslide-config.
	 */
	function buildSwiperOptions( $wrapper, config ) {
		var isEditMode = window.elementorFrontend && elementorFrontend.isEditMode();
		var prevEl = $wrapper.find( '.proslide-arrow-prev' )[ 0 ];
		var nextEl = $wrapper.find( '.proslide-arrow-next' )[ 0 ];
		var pagEl  = $wrapper.find( '.proslide-pagination' )[ 0 ];

		var mobileSpv  = ( config.slidesPerView && config.slidesPerView.mobile ) ? config.slidesPerView.mobile : 1;
		var tabletSpv  = ( config.slidesPerView && config.slidesPerView.tablet ) ? config.slidesPerView.tablet : mobileSpv;
		var desktopSpv = ( config.slidesPerView && config.slidesPerView.desktop ) ? config.slidesPerView.desktop : tabletSpv;

		var mobileScroll  = ( config.slidesToScroll && config.slidesToScroll.mobile ) ? config.slidesToScroll.mobile : 1;
		var tabletScroll  = ( config.slidesToScroll && config.slidesToScroll.tablet ) ? config.slidesToScroll.tablet : mobileScroll;
		var desktopScroll = ( config.slidesToScroll && config.slidesToScroll.desktop ) ? config.slidesToScroll.desktop : tabletScroll;

		var mobileSpace  = ( config.spaceBetween && typeof config.spaceBetween.mobile !== 'undefined' ) ? config.spaceBetween.mobile : 0;
		var tabletSpace  = ( config.spaceBetween && typeof config.spaceBetween.tablet !== 'undefined' ) ? config.spaceBetween.tablet : mobileSpace;
		var desktopSpace = ( config.spaceBetween && typeof config.spaceBetween.desktop !== 'undefined' ) ? config.spaceBetween.desktop : tabletSpace;

		var options = {
			effect: config.effect || 'slide',
			speed: config.speed || 700,
			loop: isEditMode ? false : Boolean( config.loop ),
			centeredSlides: Boolean( config.centeredSlides ),
			autoHeight: Boolean( config.autoHeight ),
			grabCursor: Boolean( config.grabCursor ),
			freeMode: Boolean( config.freeMode ),
			watchOverflow: true,
			slidesPerView: mobileSpv,
			slidesPerGroup: mobileScroll,
			spaceBetween: mobileSpace,
			breakpoints: {
				768: {
					slidesPerView: tabletSpv,
					slidesPerGroup: tabletScroll,
					spaceBetween: tabletSpace
				},
				1025: {
					slidesPerView: desktopSpv,
					slidesPerGroup: desktopScroll,
					spaceBetween: desktopSpace
				}
			}
		};

		if ( 'fade' === options.effect ) {
			options.fadeEffect = { crossFade: true };
		}

		if ( config.autoplay && ! isEditMode ) {
			options.autoplay = {
				delay: config.autoplaySpeed || 5000,
				disableOnInteraction: Boolean( config.pauseOnInteraction ),
				pauseOnMouseEnter: Boolean( config.pauseOnHover )
			};
		}

		if ( config.keyboard ) {
			options.keyboard = { enabled: true, onlyInViewport: true };
		}

		if ( config.mousewheel ) {
			options.mousewheel = { forceToAxis: true };
		}

		if ( config.showArrows && prevEl && nextEl ) {
			options.navigation = {
				prevEl: prevEl,
				nextEl: nextEl
			};
		}

		if ( config.showDots && pagEl ) {
			options.pagination = {
				el: pagEl,
				type: config.paginationType || 'bullets',
				clickable: true
			};
		}

		return options;
	}

	/**
	 * Initialize a ProSlide widget instance.
	 */
	function initProSlide( $scope ) {
		var $wrapper = $scope.find( '.proslide-wrapper' ).first();
		if ( ! $wrapper.length ) {
			return;
		}

		var $swiperEl = $wrapper.find( '.proslide-swiper' ).first();
		if ( ! $swiperEl.length ) {
			return;
		}

		// Destroy previous instance if re-initializing in editor.
		var existing = $swiperEl.data( 'proslideInstance' );
		if ( existing && typeof existing.destroy === 'function' ) {
			existing.destroy( true, true );
			$swiperEl.removeData( 'proslideInstance' );
		}

		prepareNestedEditorSlides( $wrapper );

		var rawConfig = $wrapper.attr( 'data-proslide-config' );
		var config    = {};
		if ( rawConfig ) {
			try {
				config = JSON.parse( rawConfig );
			} catch ( e ) {
				config = {};
			}
		}

		var swiperOptions = buildSwiperOptions( $wrapper, config );

		if ( window.elementorFrontend && elementorFrontend.utils && elementorFrontend.utils.swiper ) {
			var AsyncSwiper = elementorFrontend.utils.swiper;
			new AsyncSwiper( $swiperEl, swiperOptions ).then( function ( swiperInstance ) {
				$swiperEl.data( 'proslideInstance', swiperInstance );
			} );
		} else if ( typeof window.Swiper === 'function' ) {
			var swiperInstance = new window.Swiper( $swiperEl[ 0 ], swiperOptions );
			$swiperEl.data( 'proslideInstance', swiperInstance );
		}
	}

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/proslide-content-slider.default', initProSlide );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/proslide-nested-slider.default', initProSlide );
	} );
}( jQuery ) );
