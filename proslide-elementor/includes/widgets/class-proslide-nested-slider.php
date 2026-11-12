<?php
/**
 * ProSlide Nested Elementor Widget Slider.
 *
 * Allows dragging and dropping any Elementor widget directly inside each slide
 * using Elementor's Nested Elements architecture, with per-slide background images,
 * overlays, and full responsive Swiper customization.
 *
 * @package ProSlide
 */

namespace ProSlide\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;
use ProSlide\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nested Widget Slider.
 */
class Nested_Slider extends Widget_Nested_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'proslide-nested-slider';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'ProSlide Nested Widget Slider', 'proslide-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-nested-carousel';
	}

	/**
	 * Widget categories.
	 *
	 * @return array
	 */
	public function get_categories() {
		return [ 'proslide-elements' ];
	}

	/**
	 * Search keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return [ 'nested', 'slider', 'carousel', 'widgets', 'container', 'proslide' ];
	}

	/**
	 * Style dependencies.
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return [ 'proslide-css' ];
	}

	/**
	 * Script dependencies.
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return [ 'proslide-js' ];
	}

	/**
	 * Check if widget should show in Elementor panel.
	 *
	 * @return bool
	 */
	public function show_in_panel(): bool {
		if ( isset( Plugin::$instance->experiments ) && method_exists( Plugin::$instance->experiments, 'is_feature_active' ) ) {
			return Plugin::$instance->experiments->is_feature_active( 'nested-elements' );
		}
		return true;
	}

	/**
	 * HTML wrapper class.
	 *
	 * @return string
	 */
	protected function get_html_wrapper_class() {
		return 'elementor-widget-proslide-nested-slider';
	}

	/**
	 * Default child containers for new widget instances.
	 *
	 * @return array
	 */
	protected function get_default_children_elements() {
		return [
			[
				'elType'   => 'container',
				'settings' => [
					'_title' => esc_html__( 'Slide #1', 'proslide-elementor' ),
				],
			],
			[
				'elType'   => 'container',
				'settings' => [
					'_title' => esc_html__( 'Slide #2', 'proslide-elementor' ),
				],
			],
			[
				'elType'   => 'container',
				'settings' => [
					'_title' => esc_html__( 'Slide #3', 'proslide-elementor' ),
				],
			],
		];
	}

	/**
	 * Repeater title setting key.
	 *
	 * @return string
	 */
	protected function get_default_repeater_title_setting_key() {
		return 'slide_title';
	}

	/**
	 * Default child title pattern.
	 *
	 * @return string
	 */
	protected function get_default_children_title() {
		return esc_html__( 'Slide #%d', 'proslide-elementor' );
	}

	/**
	 * Selector where Elementor places nested child containers.
	 *
	 * @return string
	 */
	protected function get_default_children_placeholder_selector() {
		return '.proslide-nested-wrapper';
	}

	/**
	 * Register controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_nested_slides',
			[
				'label' => esc_html__( 'Slides (Drag Widgets Inside)', 'proslide-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'slide_title',
			[
				'label'       => esc_html__( 'Slide Label', 'proslide-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Slide', 'proslide-elementor' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'slide_bg_color',
			[
				'label'   => esc_html__( 'Background Color', 'proslide-elementor' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#0f172a',
			]
		);

		$repeater->add_control(
			'slide_bg_image',
			[
				'label'   => esc_html__( 'Background Image', 'proslide-elementor' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'slide_bg_size',
			[
				'label'   => esc_html__( 'Background Size', 'proslide-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => [
					'cover'   => esc_html__( 'Cover', 'proslide-elementor' ),
					'contain' => esc_html__( 'Contain', 'proslide-elementor' ),
					'auto'    => esc_html__( 'Auto', 'proslide-elementor' ),
				],
			]
		);

		$repeater->add_control(
			'slide_bg_position',
			[
				'label'   => esc_html__( 'Background Position', 'proslide-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => [
					'center center' => esc_html__( 'Center Center', 'proslide-elementor' ),
					'top center'    => esc_html__( 'Top Center', 'proslide-elementor' ),
					'bottom center' => esc_html__( 'Bottom Center', 'proslide-elementor' ),
					'center left'   => esc_html__( 'Center Left', 'proslide-elementor' ),
					'center right'  => esc_html__( 'Center Right', 'proslide-elementor' ),
				],
			]
		);

		$repeater->add_control(
			'slide_bg_ken_burns',
			[
				'label'        => esc_html__( 'Ken Burns Zoom Effect', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$repeater->add_control(
			'slide_overlay_color',
			[
				'label'   => esc_html__( 'Overlay Color', 'proslide-elementor' ),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(15, 23, 42, 0.35)',
			]
		);

		$this->add_control(
			'slides',
			[
				'label'   => esc_html__( 'Slides', 'proslide-elementor' ),
				'type'    => Control_Nested_Repeater::CONTROL_TYPE,
				'fields'  => $repeater->get_controls(),
				'default' => [
					[ 'slide_title' => esc_html__( 'Slide #1', 'proslide-elementor' ) ],
					[ 'slide_title' => esc_html__( 'Slide #2', 'proslide-elementor' ) ],
					[ 'slide_title' => esc_html__( 'Slide #3', 'proslide-elementor' ) ],
				],
			]
		);

		$this->end_controls_section();

		Helpers::register_slider_settings_controls( $this );
		Helpers::register_navigation_pagination_controls( $this );

		// Style: Container.
		$this->start_controls_section(
			'section_nested_style_container',
			[
				'label' => esc_html__( 'Slider Container', 'proslide-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'nested_slide_padding',
			[
				'label'      => esc_html__( 'Slide Inner Padding', 'proslide-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'top'    => 30,
					'right'  => 30,
					'bottom' => 30,
					'left'   => 30,
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .proslide-nested-slide > .proslide-slide-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .proslide-nested-wrapper > .elementor-element'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'nested_border_radius',
			[
				'label'      => esc_html__( 'Slider Border Radius', 'proslide-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .proslide-swiper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'nested_border',
				'selector' => '{{WRAPPER}} .proslide-swiper',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'nested_box_shadow',
				'selector' => '{{WRAPPER}} .proslide-swiper',
			]
		);

		$this->end_controls_section();

		Helpers::register_navigation_pagination_style_controls( $this );
	}

	/**
	 * Frontend render.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$slides   = ! empty( $settings['slides'] ) ? $settings['slides'] : [];

		if ( empty( $slides ) ) {
			return;
		}

		$swiper_config = Helpers::build_swiper_config( $settings );
		$arrows_pos    = ! empty( $settings['arrows_position'] ) ? $settings['arrows_position'] : 'inside';
		$dots_pos      = ! empty( $settings['dots_position'] ) ? $settings['dots_position'] : 'inside-bottom';

		$wrapper_classes = [
			'proslide-wrapper',
			'proslide-is-nested',
			'proslide-arrows-' . sanitize_html_class( $arrows_pos ),
			'proslide-dots-' . sanitize_html_class( $dots_pos ),
		];
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-proslide-config="<?php echo esc_attr( wp_json_encode( $swiper_config ) ); ?>">
			<div class="swiper proslide-swiper">
				<div class="swiper-wrapper proslide-nested-wrapper">
					<?php foreach ( $slides as $index => $slide ) : ?>
						<?php
						$bg_styles = [];
						if ( ! empty( $slide['slide_bg_color'] ) ) {
							$bg_styles[] = 'background-color:' . esc_attr( $slide['slide_bg_color'] ) . ';';
						}
						if ( ! empty( $slide['slide_bg_image']['url'] ) ) {
							$bg_styles[] = "background-image:url('" . esc_url( $slide['slide_bg_image']['url'] ) . "');";
							$bg_styles[] = 'background-size:' . esc_attr( $slide['slide_bg_size'] ?? 'cover' ) . ';';
							$bg_styles[] = 'background-position:' . esc_attr( $slide['slide_bg_position'] ?? 'center center' ) . ';';
						}
						$bg_classes = [ 'proslide-bg-layer' ];
						if ( 'yes' === ( $slide['slide_bg_ken_burns'] ?? '' ) ) {
							$bg_classes[] = 'proslide-ken-burns';
							$bg_classes[] = 'proslide-ken-burns-in';
						}
						$overlay = ! empty( $slide['slide_overlay_color'] ) ? 'background-color:' . esc_attr( $slide['slide_overlay_color'] ) . ';' : '';
						?>
						<div class="swiper-slide proslide-slide proslide-nested-slide elementor-repeater-item-<?php echo esc_attr( $slide['_id'] ?? $index ); ?>">
							<div class="<?php echo esc_attr( implode( ' ', $bg_classes ) ); ?>" style="<?php echo esc_attr( implode( ' ', $bg_styles ) ); ?>"></div>
							<?php if ( $overlay ) : ?>
								<div class="proslide-bg-overlay" style="<?php echo esc_attr( $overlay ); ?>"></div>
							<?php endif; ?>
							<div class="proslide-slide-inner">
								<?php $this->print_child( $index ); ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php Helpers::render_navigation_and_pagination( $settings ); ?>
		</div>
		<?php
	}
}
