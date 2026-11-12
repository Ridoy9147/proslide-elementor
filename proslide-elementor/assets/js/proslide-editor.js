( function () {
	'use strict';

	/**
	 * Register ProSlide Nested Slider with Elementor's nested elements editor system.
	 */
	function registerProSlideNestedElement() {
		if ( ! window.elementor || ! elementor.modules || ! elementor.modules.elements || ! elementor.modules.elements.types || ! elementor.modules.elements.types.NestedElementBase ) {
			return;
		}

		if ( ! window.$e || ! $e.components || ! $e.components.get( 'nested-elements' ) ) {
			return;
		}

		var NestedView = $e.components.get( 'nested-elements' ).exports.NestedView;
		if ( ! NestedView ) {
			return;
		}

		// Avoid duplicate class registration.
		if ( elementor.elementsManager && elementor.elementsManager.getElementType( 'proslide-nested-slider' ) ) {
			return;
		}

		class ProSlideNestedView extends NestedView {
			filter( child, index ) {
				child.attributes.dataIndex = index + 1;
				return true;
			}

			onAddChild( childView ) {
				var index = childView.model.attributes.dataIndex;
				childView.$el.addClass( 'swiper-slide proslide-slide proslide-nested-slide' );
				childView.$el.attr( 'data-slide-index', index );
			}
		}

		class ProSlideNestedElement extends elementor.modules.elements.types.NestedElementBase {
			getType() {
				return 'proslide-nested-slider';
			}

			getView() {
				return ProSlideNestedView;
			}
		}

		elementor.elementsManager.registerElementType( new ProSlideNestedElement() );
	}

	if ( window.elementorCommon && elementorCommon.elements && elementorCommon.elements.$window ) {
		elementorCommon.elements.$window.on( 'elementor/nested-element-type-loaded', registerProSlideNestedElement );
	}

	// Fallback init.
	if ( window.elementor ) {
		registerProSlideNestedElement();
	}
}() );
