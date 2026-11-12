<?php
/**
 * Shared helpers and reusable Elementor control groups for ProSlide.
 *
 * @package ProSlide
 */

namespace ProSlide;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin as Elementor_Plugin;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper utilities for templates, Swiper configuration, and shared controls.
 */
class Helpers {

	/**
	 * Fetch all saved Elementor templates for dropdown selection.
	 *
	 * @return array<string|int, string>
	 */
	public static function get_elementor_templates() {
		$options = [
			'' => esc_html__( '— Select Elementor Template —', 'proslide-elementor' ),
		];

		$templates = get_posts(
			[
				'post_type'      => 'elementor_library',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			]
		);

		if ( empty( $templates ) || is_wp_error( $templates ) ) {
			return $options;
		}

		foreach ( $templates as $template ) {
			$type = get_post_meta( $template->ID, '_elementor_template_type', true );
			if ( 'kit' === $type ) {
				continue;
			}
			$label = $template->post_title;
			if ( $type ) {
				$label .= sprintf( ' (%s)', ucfirst( $type ) );
			}
			$options[ $template->ID ] = $label;
		}

		return $options;
	}

	/**
	 * Render an Elementor template by ID.
	 *
	 * @param int $template_id Template post ID.
	 * @return string
	 */
	public static function render_elementor_template( $template_id ) {
		$template_id = absint( $template_id );
		if ( ! $template_id ) {
			return '';
		}

		$post = get_post( $template_id );
		if ( ! $post || 'elementor_library' !== $post->post_type || 'publish' !== $post->post_status ) {
			return '';
		}

		// Prevent infinite recursion if a template includes itself.
		static $rendering_stack = [];
		if ( isset( $rendering_stack[ $template_id ] ) ) {
			return '';
		}

		$rendering_stack[ $template_id ] = true;
		$content                         = Elementor_Plugin::instance()->frontend->get_builder_content_for_display( $template_id, true );
		unset( $rendering_stack[ $template_id ] );

		return $content;
	}

	/**
	 * Register detailed responsive Slider Settings controls.
	 *
	 * @param Widget_Base $widget Target widget instance.
	 */
	public static function register_slider_settings_controls( Widget_Base $widget ) {
		$widget->start_controls_section(
			'section_slider_settings',
			[
				'label' => esc_html__( 'Slider Settings & Responsive', 'proslide-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$widget->add_control(
			'slider_effect',
			[
				'label'   => esc_html__( 'Transition Effect', 'proslide-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide'     => esc_html__( 'Slide', 'proslide-elementor' ),
					'fade'      => esc_html__( 'Fade', 'proslide-elementor' ),
					'coverflow' => esc_html__( 'Coverflow (3D)', 'proslide-elementor' ),
					'flip'      => esc_html__( 'Flip (3D)', 'proslide-elementor' ),
					'cube'      => esc_html__( 'Cube (3D)', 'proslide-elementor' ),
					'cards'     => esc_html__( 'Cards (Stacked)', 'proslide-elementor' ),
				],
			]
		);

		$widget->add_responsive_control(
			'slides_per_view',
			[
				'label'          => esc_html__( 'Slides Per View', 'proslide-elementor' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '1',
				'tablet_default' => '1',
				'mobile_default' => '1',
				'options'        => [
					'1'    => '1',
					'2'    => '2',
					'3'    => '3',
					'4'    => '4',
					'5'    => '5',
					'6'    => '6',
					'auto' => esc_html__( 'Auto', 'proslide-elementor' ),
				],
				'condition'      => [
					'slider_effect' => [ 'slide', 'coverflow' ],
				],
			]
		);

		$widget->add_responsive_control(
			'slides_to_scroll',
			[
				'label'          => esc_html__( 'Slides to Scroll', 'proslide-elementor' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '1',
				'tablet_default' => '1',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'condition'      => [
					'slider_effect' => [ 'slide', 'coverflow' ],
				],
			]
		);

		$widget->add_responsive_control(
			'space_between',
			[
				'label'          => esc_html__( 'Space Between Slides (px)', 'proslide-elementor' ),
				'type'           => Controls_Manager::SLIDER,
				'range'          => [
					'px' => [
						'min' => 0,
						'max' => 120,
					],
				],
				'default'        => [ 'size' => 0, 'unit' => 'px' ],
				'tablet_default' => [ 'size' => 0, 'unit' => 'px' ],
				'mobile_default' => [ 'size' => 0, 'unit' => 'px' ],
				'condition'      => [
					'slider_effect' => [ 'slide', 'coverflow' ],
				],
			]
		);

		$widget->add_responsive_control(
			'slider_height',
			[
				'label'      => esc_html__( 'Slider Height', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh', 'em' ],
				'range'      => [
					'px' => [
						'min' => 150,
						'max' => 1200,
					],
					'vh' => [
						'min' => 15,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 550,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .proslide-slide-inner' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_control(
			'centered_slides',
			[
				'label'        => esc_html__( 'Centered Slides', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'slider_effect' => [ 'slide', 'coverflow' ],
				],
			]
		);

		$widget->add_control(
			'auto_height',
			[
				'label'        => esc_html__( 'Auto Height', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$widget->add_control(
			'loop',
			[
				'label'        => esc_html__( 'Infinite Loop', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$widget->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Autoplay', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$widget->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Autoplay Delay (ms)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'min'       => 500,
				'max'       => 30000,
				'step'      => 100,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$widget->add_control(
			'pause_on_hover',
			[
				'label'        => esc_html__( 'Pause on Hover', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'autoplay' => 'yes',
				],
			]
		);

		$widget->add_control(
			'pause_on_interaction',
			[
				'label'        => esc_html__( 'Pause on Interaction', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'autoplay' => 'yes',
				],
			]
		);

		$widget->add_control(
			'transition_speed',
			[
				'label'   => esc_html__( 'Transition Speed (ms)', 'proslide-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 700,
				'min'     => 100,
				'max'     => 5000,
				'step'    => 50,
			]
		);

		$widget->add_control(
			'grab_cursor',
			[
				'label'        => esc_html__( 'Grab Cursor', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$widget->add_control(
			'keyboard_control',
			[
				'label'        => esc_html__( 'Keyboard Navigation', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$widget->add_control(
			'mousewheel_control',
			[
				'label'        => esc_html__( 'Mousewheel Navigation', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$widget->add_control(
			'free_mode',
			[
				'label'        => esc_html__( 'Free Mode (Momentum)', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$widget->end_controls_section();
	}

	/**
	 * Register Navigation Arrows and Pagination controls.
	 *
	 * @param Widget_Base $widget Target widget instance.
	 */
	public static function register_navigation_pagination_controls( Widget_Base $widget ) {
		$widget->start_controls_section(
			'section_navigation',
			[
				'label' => esc_html__( 'Navigation & Pagination', 'proslide-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$widget->add_control(
			'show_arrows',
			[
				'label'        => esc_html__( 'Show Navigation Arrows', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$widget->add_control(
			'arrows_position',
			[
				'label'     => esc_html__( 'Arrows Position', 'proslide-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inside',
				'options'   => [
					'inside'       => esc_html__( 'Inside Sides', 'proslide-elementor' ),
					'outside'      => esc_html__( 'Outside Sides', 'proslide-elementor' ),
					'bottom-right' => esc_html__( 'Bottom Right', 'proslide-elementor' ),
					'bottom-left'  => esc_html__( 'Bottom Left', 'proslide-elementor' ),
					'top-right'    => esc_html__( 'Top Right', 'proslide-elementor' ),
				],
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$widget->add_control(
			'arrow_prev_icon',
			[
				'label'     => esc_html__( 'Previous Icon', 'proslide-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-chevron-left',
					'library' => 'fa-solid',
				],
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$widget->add_control(
			'arrow_next_icon',
			[
				'label'     => esc_html__( 'Next Icon', 'proslide-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => [
					'value'   => 'fas fa-chevron-right',
					'library' => 'fa-solid',
				],
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$widget->add_control(
			'show_dots',
			[
				'label'        => esc_html__( 'Show Pagination', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$widget->add_control(
			'pagination_type',
			[
				'label'     => esc_html__( 'Pagination Type', 'proslide-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bullets',
				'options'   => [
					'bullets'     => esc_html__( 'Dots / Bullets', 'proslide-elementor' ),
					'fraction'    => esc_html__( 'Fraction (1 / 5)', 'proslide-elementor' ),
					'progressbar' => esc_html__( 'Progress Bar', 'proslide-elementor' ),
				],
				'condition' => [
					'show_dots' => 'yes',
				],
			]
		);

		$widget->add_control(
			'dots_position',
			[
				'label'     => esc_html__( 'Pagination Position', 'proslide-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inside-bottom',
				'options'   => [
					'inside-bottom'  => esc_html__( 'Inside Bottom', 'proslide-elementor' ),
					'outside-bottom' => esc_html__( 'Outside Bottom', 'proslide-elementor' ),
					'inside-top'     => esc_html__( 'Inside Top', 'proslide-elementor' ),
				],
				'condition' => [
					'show_dots' => 'yes',
				],
			]
		);

		$widget->end_controls_section();
	}

	/**
	 * Register Style Tab controls for Arrows & Pagination.
	 *
	 * @param Widget_Base $widget Target widget instance.
	 */
	public static function register_navigation_pagination_style_controls( Widget_Base $widget ) {
		// Arrows Style Section.
		$widget->start_controls_section(
			'section_style_arrows',
			[
				'label'     => esc_html__( 'Navigation Arrows', 'proslide-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_arrows' => 'yes',
				],
			]
		);

		$widget->add_responsive_control(
			'arrows_hide_responsive',
			[
				'label'        => esc_html__( 'Hide on Device', 'proslide-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'flex',
				'options'      => [
					'flex' => esc_html__( 'Show', 'proslide-elementor' ),
					'none' => esc_html__( 'Hide', 'proslide-elementor' ),
				],
				'selectors'    => [
					'{{WRAPPER}} .proslide-arrow' => 'display: {{VALUE}};',
				],
			]
		);

		$widget->add_responsive_control(
			'arrow_box_size',
			[
				'label'      => esc_html__( 'Button Size', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 24,
						'max' => 100,
					],
				],
				'default'    => [ 'size' => 46, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .proslide-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_responsive_control(
			'arrow_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 50,
					],
				],
				'default'    => [ 'size' => 18, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .proslide-arrow'     => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .proslide-arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_responsive_control(
			'arrow_horizontal_offset',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => -100,
						'max' => 120,
					],
					'%'  => [
						'min' => -20,
						'max' => 25,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .proslide-arrows-inside .proslide-arrow-prev'  => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .proslide-arrows-inside .proslide-arrow-next'  => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .proslide-arrows-outside .proslide-arrow-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .proslide-arrows-outside .proslide-arrow-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_control(
			'arrow_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'proslide-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .proslide-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$widget->start_controls_tabs( 'tabs_arrow_style' );

		$widget->start_controls_tab(
			'tab_arrow_normal',
			[
				'label' => esc_html__( 'Normal', 'proslide-elementor' ),
			]
		);

		$widget->add_control(
			'arrow_color',
			[
				'label'     => esc_html__( 'Icon Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .proslide-arrow'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .proslide-arrow svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$widget->add_control(
			'arrow_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.45)',
				'selectors' => [
					'{{WRAPPER}} .proslide-arrow' => 'background-color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'arrow_border',
				'selector' => '{{WRAPPER}} .proslide-arrow',
			]
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'tab_arrow_hover',
			[
				'label' => esc_html__( 'Hover', 'proslide-elementor' ),
			]
		);

		$widget->add_control(
			'arrow_hover_color',
			[
				'label'     => esc_html__( 'Icon Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .proslide-arrow:hover'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .proslide-arrow:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$widget->add_control(
			'arrow_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.8)',
				'selectors' => [
					'{{WRAPPER}} .proslide-arrow:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$widget->add_control(
			'arrow_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .proslide-arrow:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$widget->end_controls_tab();
		$widget->end_controls_tabs();

		$widget->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'arrow_box_shadow',
				'selector'  => '{{WRAPPER}} .proslide-arrow',
				'separator' => 'before',
			]
		);

		$widget->end_controls_section();

		// Pagination Style Section.
		$widget->start_controls_section(
			'section_style_pagination',
			[
				'label'     => esc_html__( 'Pagination / Dots', 'proslide-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_dots' => 'yes',
				],
			]
		);

		$widget->add_responsive_control(
			'dots_bottom_offset',
			[
				'label'      => esc_html__( 'Vertical Offset', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .proslide-dots-inside-bottom .swiper-pagination'  => 'bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .proslide-dots-outside-bottom .swiper-pagination' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .proslide-dots-inside-top .swiper-pagination'     => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_responsive_control(
			'dot_width',
			[
				'label'      => esc_html__( 'Bullet Width', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 4,
						'max' => 40,
					],
				],
				'default'    => [ 'size' => 10, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'pagination_type' => 'bullets',
				],
			]
		);

		$widget->add_responsive_control(
			'dot_height',
			[
				'label'      => esc_html__( 'Bullet Height', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 4,
						'max' => 40,
					],
				],
				'default'    => [ 'size' => 10, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'pagination_type' => 'bullets',
				],
			]
		);

		$widget->add_responsive_control(
			'dot_active_width',
			[
				'label'      => esc_html__( 'Active Bullet Width', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 4,
						'max' => 60,
					],
				],
				'default'    => [ 'size' => 26, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'pagination_type' => 'bullets',
				],
			]
		);

		$widget->add_responsive_control(
			'dot_spacing',
			[
				'label'      => esc_html__( 'Bullet Spacing', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 2,
						'max' => 30,
					],
				],
				'default'    => [ 'size' => 6, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'pagination_type' => 'bullets',
				],
			]
		);

		$widget->add_control(
			'dot_border_radius',
			[
				'label'      => esc_html__( 'Bullet Border Radius', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'    => [ 'size' => 10, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'pagination_type' => 'bullets',
				],
			]
		);

		$widget->add_control(
			'dot_inactive_color',
			[
				'label'     => esc_html__( 'Inactive Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.45)',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet'      => 'background-color: {{VALUE}}; opacity: 1;',
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-fraction'    => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_control(
			'dot_active_color',
			[
				'label'     => esc_html__( 'Active Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active'    => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-progressbar-fill' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .swiper-pagination-current'          => 'color: {{VALUE}};',
				],
			]
		);

		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'fraction_typography',
				'selector'  => '{{WRAPPER}} .swiper-pagination-fraction',
				'condition' => [
					'pagination_type' => 'fraction',
				],
			]
		);

		$widget->end_controls_section();
	}

	/**
	 * Build Swiper configuration array from widget settings.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	public static function build_swiper_config( array $settings ) {
		$effect = ! empty( $settings['slider_effect'] ) ? $settings['slider_effect'] : 'slide';

		$slides_desktop = ! empty( $settings['slides_per_view'] ) ? $settings['slides_per_view'] : '1';
		$slides_tablet  = ! empty( $settings['slides_per_view_tablet'] ) ? $settings['slides_per_view_tablet'] : $slides_desktop;
		$slides_mobile  = ! empty( $settings['slides_per_view_mobile'] ) ? $settings['slides_per_view_mobile'] : $slides_tablet;

		$scroll_desktop = ! empty( $settings['slides_to_scroll'] ) ? (int) $settings['slides_to_scroll'] : 1;
		$scroll_tablet  = ! empty( $settings['slides_to_scroll_tablet'] ) ? (int) $settings['slides_to_scroll_tablet'] : $scroll_desktop;
		$scroll_mobile  = ! empty( $settings['slides_to_scroll_mobile'] ) ? (int) $settings['slides_to_scroll_mobile'] : $scroll_tablet;

		$space_desktop = isset( $settings['space_between']['size'] ) ? (int) $settings['space_between']['size'] : 0;
		$space_tablet  = isset( $settings['space_between_tablet']['size'] ) ? (int) $settings['space_between_tablet']['size'] : $space_desktop;
		$space_mobile  = isset( $settings['space_between_mobile']['size'] ) ? (int) $settings['space_between_mobile']['size'] : $space_tablet;

		if ( ! in_array( $effect, [ 'slide', 'coverflow' ], true ) ) {
			$slides_desktop = 1;
			$slides_tablet  = 1;
			$slides_mobile  = 1;
		}

		return [
			'effect'             => $effect,
			'loop'               => ( 'yes' === ( $settings['loop'] ?? '' ) ),
			'speed'              => ! empty( $settings['transition_speed'] ) ? (int) $settings['transition_speed'] : 700,
			'autoplay'           => ( 'yes' === ( $settings['autoplay'] ?? '' ) ),
			'autoplaySpeed'      => ! empty( $settings['autoplay_speed'] ) ? (int) $settings['autoplay_speed'] : 5000,
			'pauseOnHover'       => ( 'yes' === ( $settings['pause_on_hover'] ?? '' ) ),
			'pauseOnInteraction' => ( 'yes' === ( $settings['pause_on_interaction'] ?? '' ) ),
			'centeredSlides'     => ( 'yes' === ( $settings['centered_slides'] ?? '' ) ),
			'autoHeight'         => ( 'yes' === ( $settings['auto_height'] ?? '' ) ),
			'grabCursor'         => ( 'yes' === ( $settings['grab_cursor'] ?? '' ) ),
			'keyboard'           => ( 'yes' === ( $settings['keyboard_control'] ?? '' ) ),
			'mousewheel'         => ( 'yes' === ( $settings['mousewheel_control'] ?? '' ) ),
			'freeMode'           => ( 'yes' === ( $settings['free_mode'] ?? '' ) ),
			'showArrows'         => ( 'yes' === ( $settings['show_arrows'] ?? '' ) ),
			'showDots'           => ( 'yes' === ( $settings['show_dots'] ?? '' ) ),
			'paginationType'     => ! empty( $settings['pagination_type'] ) ? $settings['pagination_type'] : 'bullets',
			'slidesPerView'      => [
				'desktop' => 'auto' === $slides_desktop ? 'auto' : (int) $slides_desktop,
				'tablet'  => 'auto' === $slides_tablet ? 'auto' : (int) $slides_tablet,
				'mobile'  => 'auto' === $slides_mobile ? 'auto' : (int) $slides_mobile,
			],
			'slidesToScroll'     => [
				'desktop' => $scroll_desktop,
				'tablet'  => $scroll_tablet,
				'mobile'  => $scroll_mobile,
			],
			'spaceBetween'       => [
				'desktop' => $space_desktop,
				'tablet'  => $space_tablet,
				'mobile'  => $space_mobile,
			],
		];
	}

	/**
	 * Render navigation arrows and pagination markup.
	 *
	 * @param array $settings Widget settings.
	 */
	public static function render_navigation_and_pagination( array $settings ) {
		if ( 'yes' === ( $settings['show_arrows'] ?? '' ) ) :
			?>
			<div class="proslide-arrows">
				<button type="button" class="proslide-arrow proslide-arrow-prev" aria-label="<?php esc_attr_e( 'Previous slide', 'proslide-elementor' ); ?>">
					<?php
					if ( ! empty( $settings['arrow_prev_icon']['value'] ) ) {
						Icons_Manager::render_icon( $settings['arrow_prev_icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						echo '<span aria-hidden="true">&#8249;</span>';
					}
					?>
				</button>
				<button type="button" class="proslide-arrow proslide-arrow-next" aria-label="<?php esc_attr_e( 'Next slide', 'proslide-elementor' ); ?>">
					<?php
					if ( ! empty( $settings['arrow_next_icon']['value'] ) ) {
						Icons_Manager::render_icon( $settings['arrow_next_icon'], [ 'aria-hidden' => 'true' ] );
					} else {
						echo '<span aria-hidden="true">&#8250;</span>';
					}
					?>
				</button>
			</div>
			<?php
		endif;

		if ( 'yes' === ( $settings['show_dots'] ?? '' ) ) :
			?>
			<div class="swiper-pagination proslide-pagination"></div>
			<?php
		endif;
	}
}
