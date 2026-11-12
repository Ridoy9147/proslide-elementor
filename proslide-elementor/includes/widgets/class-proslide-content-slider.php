<?php
/**
 * ProSlide Content & Template Slider Widget.
 *
 * Supports full content design per slide, background images with Ken Burns & overlays,
 * Elementor templates as slides, and comprehensive global + per-slide styling overrides.
 *
 * @package ProSlide
 */

namespace ProSlide\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;
use ProSlide\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Content & Template Slider Widget.
 */
class Content_Slider extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'proslide-content-slider';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'ProSlide Content & Template Slider', 'proslide-elementor' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-slides';
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
		return [ 'slider', 'carousel', 'hero', 'template', 'banner', 'proslide', 'swiper' ];
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
	 * Register widget controls.
	 */
	protected function register_controls() {
		$this->register_slides_section();
		$this->register_animation_section();
		Helpers::register_slider_settings_controls( $this );
		Helpers::register_navigation_pagination_controls( $this );

		// Style Tab — Global styles (applies across all slides by default).
		$this->register_style_container_section();
		$this->register_style_subtitle_section();
		$this->register_style_title_section();
		$this->register_style_description_section();
		$this->register_style_button_section( 'btn_1', esc_html__( 'Primary Button (Global)', 'proslide-elementor' ), '.proslide-btn-primary' );
		$this->register_style_button_section( 'btn_2', esc_html__( 'Secondary Button (Global)', 'proslide-elementor' ), '.proslide-btn-secondary' );
		Helpers::register_navigation_pagination_style_controls( $this );
	}

	/**
	 * Slides Repeater Section.
	 */
	private function register_slides_section() {
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Slides', 'proslide-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'slide_item_tabs' );

		// Tab 1: Background.
		$repeater->start_controls_tab(
			'tab_slide_background',
			[
				'label' => esc_html__( 'Background', 'proslide-elementor' ),
			]
		);

		$repeater->add_control(
			'slide_bg_type',
			[
				'label'   => esc_html__( 'Background Type', 'proslide-elementor' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'classic',
				'options' => [
					'classic'  => [
						'title' => esc_html__( 'Image / Color', 'proslide-elementor' ),
						'icon'  => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'proslide-elementor' ),
						'icon'  => 'eicon-barcode',
					],
				],
			]
		);

		$repeater->add_control(
			'slide_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1e293b',
				'condition' => [
					'slide_bg_type' => 'classic',
				],
			]
		);

		$repeater->add_control(
			'slide_bg_image',
			[
				'label'     => esc_html__( 'Background Image', 'proslide-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'slide_bg_type' => 'classic',
				],
			]
		);

		$repeater->add_control(
			'slide_bg_size',
			[
				'label'     => esc_html__( 'Image Size', 'proslide-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => [
					'cover'   => esc_html__( 'Cover', 'proslide-elementor' ),
					'contain' => esc_html__( 'Contain', 'proslide-elementor' ),
					'auto'    => esc_html__( 'Auto', 'proslide-elementor' ),
				],
				'condition' => [
					'slide_bg_type' => 'classic',
				],
			]
		);

		$repeater->add_control(
			'slide_bg_position',
			[
				'label'     => esc_html__( 'Image Position', 'proslide-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => [
					'center center' => esc_html__( 'Center Center', 'proslide-elementor' ),
					'top center'    => esc_html__( 'Top Center', 'proslide-elementor' ),
					'bottom center' => esc_html__( 'Bottom Center', 'proslide-elementor' ),
					'center left'   => esc_html__( 'Center Left', 'proslide-elementor' ),
					'center right'  => esc_html__( 'Center Right', 'proslide-elementor' ),
				],
				'condition' => [
					'slide_bg_type' => 'classic',
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
				'condition'    => [
					'slide_bg_type' => 'classic',
				],
			]
		);

		$repeater->add_control(
			'slide_bg_ken_burns_dir',
			[
				'label'     => esc_html__( 'Ken Burns Direction', 'proslide-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'in',
				'options'   => [
					'in'  => esc_html__( 'Zoom In', 'proslide-elementor' ),
					'out' => esc_html__( 'Zoom Out', 'proslide-elementor' ),
				],
				'condition' => [
					'slide_bg_type'      => 'classic',
					'slide_bg_ken_burns' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'slide_bg_gradient_a',
			[
				'label'     => esc_html__( 'Gradient Color 1', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4f46e5',
				'condition' => [
					'slide_bg_type' => 'gradient',
				],
			]
		);

		$repeater->add_control(
			'slide_bg_gradient_b',
			[
				'label'     => esc_html__( 'Gradient Color 2', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#06b6d4',
				'condition' => [
					'slide_bg_type' => 'gradient',
				],
			]
		);

		$repeater->add_control(
			'slide_bg_gradient_angle',
			[
				'label'     => esc_html__( 'Gradient Angle (deg)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 135,
				'min'       => 0,
				'max'       => 360,
				'condition' => [
					'slide_bg_type' => 'gradient',
				],
			]
		);

		$repeater->add_control(
			'slide_overlay_color',
			[
				'label'     => esc_html__( 'Background Overlay Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(15, 23, 42, 0.45)',
				'separator' => 'before',
			]
		);

		$repeater->end_controls_tab();

		// Tab 2: Content & Template.
		$repeater->start_controls_tab(
			'tab_slide_content',
			[
				'label' => esc_html__( 'Content', 'proslide-elementor' ),
			]
		);

		$repeater->add_control(
			'slide_type',
			[
				'label'   => esc_html__( 'Slide Content Source', 'proslide-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'content',
				'options' => [
					'content'  => esc_html__( 'Custom Content', 'proslide-elementor' ),
					'template' => esc_html__( 'Elementor Template', 'proslide-elementor' ),
					'both'     => esc_html__( 'Elementor Template + Content Overlay', 'proslide-elementor' ),
				],
			]
		);

		$repeater->add_control(
			'elementor_template',
			[
				'label'       => esc_html__( 'Select Elementor Template', 'proslide-elementor' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => Helpers::get_elementor_templates(),
				'label_block' => true,
				'condition'   => [
					'slide_type' => [ 'template', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_subtitle',
			[
				'label'       => esc_html__( 'Subtitle / Badge', 'proslide-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'FEATURED COLLECTION', 'proslide-elementor' ),
				'label_block' => true,
				'condition'   => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_subtitle_icon',
			[
				'label'     => esc_html__( 'Subtitle Icon', 'proslide-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_title',
			[
				'label'       => esc_html__( 'Main Title', 'proslide-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'default'     => esc_html__( 'Design Stunning Responsive Sliders', 'proslide-elementor' ),
				'label_block' => true,
				'condition'   => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_title_tag',
			[
				'label'     => esc_html__( 'Title HTML Tag', 'proslide-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => [
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'div',
					'p'   => 'p',
				],
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_description',
			[
				'label'     => esc_html__( 'Description / Rich Content', 'proslide-elementor' ),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => esc_html__( 'Customize every detail — typography, overlays, animations, buttons, and responsive layouts right inside Elementor.', 'proslide-elementor' ),
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_btn_1_text',
			[
				'label'     => esc_html__( 'Primary Button Text', 'proslide-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Get Started', 'proslide-elementor' ),
				'separator' => 'before',
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_btn_1_link',
			[
				'label'     => esc_html__( 'Primary Button Link', 'proslide-elementor' ),
				'type'      => Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_btn_1_icon',
			[
				'label'     => esc_html__( 'Primary Button Icon', 'proslide-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_btn_1_icon_pos',
			[
				'label'     => esc_html__( 'Icon Position', 'proslide-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => [
					'left'  => esc_html__( 'Before Text', 'proslide-elementor' ),
					'right' => esc_html__( 'After Text', 'proslide-elementor' ),
				],
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_btn_2_text',
			[
				'label'     => esc_html__( 'Secondary Button Text', 'proslide-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'separator' => 'before',
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_btn_2_link',
			[
				'label'     => esc_html__( 'Secondary Button Link', 'proslide-elementor' ),
				'type'      => Controls_Manager::URL,
				'default'   => [ 'url' => '#' ],
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_btn_2_icon',
			[
				'label'     => esc_html__( 'Secondary Button Icon', 'proslide-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->add_control(
			'slide_btn_2_icon_pos',
			[
				'label'     => esc_html__( 'Secondary Icon Position', 'proslide-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'right',
				'options'   => [
					'left'  => esc_html__( 'Before Text', 'proslide-elementor' ),
					'right' => esc_html__( 'After Text', 'proslide-elementor' ),
				],
				'condition' => [
					'slide_type' => [ 'content', 'both' ],
				],
			]
		);

		$repeater->end_controls_tab();

		// Tab 3: Per-Slide Layout & Custom Style Overrides.
		$repeater->start_controls_tab(
			'tab_slide_layout',
			[
				'label' => esc_html__( 'Layout & Style', 'proslide-elementor' ),
			]
		);

		$repeater->add_control(
			'slide_h_pos',
			[
				'label'   => esc_html__( 'Horizontal Position', 'proslide-elementor' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'proslide-elementor' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'proslide-elementor' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'proslide-elementor' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
			]
		);

		$repeater->add_control(
			'slide_v_pos',
			[
				'label'   => esc_html__( 'Vertical Position', 'proslide-elementor' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'top'    => [
						'title' => esc_html__( 'Top', 'proslide-elementor' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'proslide-elementor' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'proslide-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
			]
		);

		$repeater->add_control(
			'slide_text_align',
			[
				'label'   => esc_html__( 'Text Alignment', 'proslide-elementor' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
			]
		);

		$repeater->add_control(
			'slide_whole_link',
			[
				'label'       => esc_html__( 'Whole Slide Link (Optional)', 'proslide-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com',
				'separator'   => 'before',
			]
		);

		$repeater->add_control(
			'slide_custom_style_heading',
			[
				'label'     => esc_html__( 'Custom Style (This Slide Only)', 'proslide-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'slide_override_colors',
			[
				'label'        => esc_html__( 'Custom Style for This Slide', 'proslide-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		// Per-slide Subtitle styling.
		$repeater->add_control(
			'slide_custom_subtitle_heading',
			[
				'label'     => esc_html__( 'Subtitle / Badge', 'proslide-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_subtitle_color',
			[
				'label'     => esc_html__( 'Subtitle Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_subtitle_bg',
			[
				'label'     => esc_html__( 'Subtitle Background', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_subtitle_size',
			[
				'label'     => esc_html__( 'Subtitle Font Size (px)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 10,
				'max'       => 50,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		// Per-slide Title styling.
		$repeater->add_control(
			'slide_custom_title_heading',
			[
				'label'     => esc_html__( 'Title', 'proslide-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_title_size',
			[
				'label'     => esc_html__( 'Title Font Size (px)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 16,
				'max'       => 120,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_title_weight',
			[
				'label'     => esc_html__( 'Title Font Weight', 'proslide-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => [
					''    => esc_html__( 'Default', 'proslide-elementor' ),
					'400' => '400',
					'500' => '500',
					'600' => '600',
					'700' => '700',
					'800' => '800',
					'900' => '900',
				],
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		// Per-slide Description styling.
		$repeater->add_control(
			'slide_custom_desc_heading',
			[
				'label'     => esc_html__( 'Description', 'proslide-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_desc_color',
			[
				'label'     => esc_html__( 'Description Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_desc_size',
			[
				'label'     => esc_html__( 'Description Font Size (px)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 12,
				'max'       => 40,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		// Per-slide Primary Button styling.
		$repeater->add_control(
			'slide_custom_btn1_heading',
			[
				'label'     => esc_html__( 'Primary Button', 'proslide-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_btn1_color',
			[
				'label'     => esc_html__( 'Text Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_btn1_bg',
			[
				'label'     => esc_html__( 'Background Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_btn1_padding_v',
			[
				'label'     => esc_html__( 'Vertical Padding (px)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 60,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_btn1_padding_h',
			[
				'label'     => esc_html__( 'Horizontal Padding (px)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 80,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_btn1_radius',
			[
				'label'     => esc_html__( 'Border Radius (px)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 60,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		// Per-slide Secondary Button styling.
		$repeater->add_control(
			'slide_custom_btn2_heading',
			[
				'label'     => esc_html__( 'Secondary Button', 'proslide-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_btn2_color',
			[
				'label'     => esc_html__( 'Text Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_btn2_bg',
			[
				'label'     => esc_html__( 'Background Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_btn2_padding_v',
			[
				'label'     => esc_html__( 'Vertical Padding (px)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 60,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_btn2_padding_h',
			[
				'label'     => esc_html__( 'Horizontal Padding (px)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 80,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->add_control(
			'slide_custom_btn2_radius',
			[
				'label'     => esc_html__( 'Border Radius (px)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 60,
				'condition' => [ 'slide_override_colors' => 'yes' ],
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'slides',
			[
				'label'       => esc_html__( 'Slides List', 'proslide-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ slide_title || (slide_type === "template" ? "Elementor Template Slide" : "Slide") }}}',
				'default'     => [
					[
						'slide_subtitle'    => esc_html__( 'WELCOME TO PROSLIDE', 'proslide-elementor' ),
						'slide_title'       => esc_html__( 'Build Next-Level Hero Sliders', 'proslide-elementor' ),
						'slide_description' => esc_html__( 'Full content design, background images with Ken Burns zoom, and seamless Elementor template support.', 'proslide-elementor' ),
						'slide_btn_1_text'  => esc_html__( 'Explore Features', 'proslide-elementor' ),
						'slide_btn_2_text'  => esc_html__( 'Learn More', 'proslide-elementor' ),
					],
					[
						'slide_subtitle'    => esc_html__( 'RESPONSIVE & FLEXIBLE', 'proslide-elementor' ),
						'slide_title'       => esc_html__( 'Tailored For Every Screen Size', 'proslide-elementor' ),
						'slide_description' => esc_html__( 'Fine-tune slides per view, spacing, typography, and navigation independently for Desktop, Tablet, and Mobile.', 'proslide-elementor' ),
						'slide_btn_1_text'  => esc_html__( 'Get Started', 'proslide-elementor' ),
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Content Entrance Animation Section.
	 */
	private function register_animation_section() {
		$this->start_controls_section(
			'section_content_animation',
			[
				'label' => esc_html__( 'Content Entrance Animation', 'proslide-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'content_animation',
			[
				'label'   => esc_html__( 'Active Slide Content Animation', 'proslide-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fadeInUp',
				'options' => [
					'none'        => esc_html__( 'None', 'proslide-elementor' ),
					'fadeInUp'    => esc_html__( 'Fade In Up', 'proslide-elementor' ),
					'fadeInDown'  => esc_html__( 'Fade In Down', 'proslide-elementor' ),
					'fadeInLeft'  => esc_html__( 'Fade In Left', 'proslide-elementor' ),
					'fadeInRight' => esc_html__( 'Fade In Right', 'proslide-elementor' ),
					'zoomIn'      => esc_html__( 'Zoom In', 'proslide-elementor' ),
				],
			]
		);

		$this->add_control(
			'content_animation_duration',
			[
				'label'     => esc_html__( 'Animation Duration (ms)', 'proslide-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 650,
				'min'       => 150,
				'max'       => 2500,
				'step'      => 50,
				'selectors' => [
					'{{WRAPPER}} .proslide-wrapper' => '--proslide-anim-duration: {{VALUE}}ms;',
				],
				'condition' => [
					'content_animation!' => 'none',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Container & Content Box.
	 */
	private function register_style_container_section() {
		$this->start_controls_section(
			'section_style_container',
			[
				'label' => esc_html__( 'Slider Container & Content Box', 'proslide-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'slider_border_radius',
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
				'name'     => 'slider_border',
				'selector' => '{{WRAPPER}} .proslide-swiper',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'slider_box_shadow',
				'selector' => '{{WRAPPER}} .proslide-swiper',
			]
		);

		$this->add_responsive_control(
			'slide_inner_padding',
			[
				'label'      => esc_html__( 'Slide Inner Padding', 'proslide-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'top'    => 40,
					'right'  => 40,
					'bottom' => 40,
					'left'   => 40,
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .proslide-slide-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'content_box_max_width',
			[
				'label'      => esc_html__( 'Content Box Max Width', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 200, 'max' => 1400 ],
					'%'  => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => [ 'size' => 760, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .proslide-content-box' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_box_padding',
			[
				'label'      => esc_html__( 'Content Box Padding', 'proslide-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .proslide-content-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'content_box_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .proslide-content-box',
			]
		);

		$this->add_control(
			'content_box_blur',
			[
				'label'      => esc_html__( 'Content Box Backdrop Blur (Glass)', 'proslide-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 30 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .proslide-content-box' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);',
				],
			]
		);

		$this->add_control(
			'content_box_border_radius',
			[
				'label'      => esc_html__( 'Content Box Border Radius', 'proslide-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .proslide-content-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'content_box_border',
				'selector' => '{{WRAPPER}} .proslide-content-box',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Subtitle / Badge (Global).
	 */
	private function register_style_subtitle_section() {
		$this->start_controls_section(
			'section_style_subtitle',
			[
				'label' => esc_html__( 'Subtitle / Badge (Global)', 'proslide-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'subtitle_align',
			[
				'label'     => esc_html__( 'Alignment', 'proslide-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Right', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .proslide-subtitle' => 'align-self: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .proslide-subtitle',
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => esc_html__( 'Text Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2e8f0',
				'selectors' => [
					'{{WRAPPER}} .proslide-subtitle'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .proslide-subtitle svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'subtitle_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.14)',
				'selectors' => [
					'{{WRAPPER}} .proslide-subtitle' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'subtitle_padding',
			[
				'label'      => esc_html__( 'Padding', 'proslide-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'    => 6,
					'right'  => 14,
					'bottom' => 6,
					'left'   => 14,
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .proslide-subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'subtitle_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'proslide-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'    => 50,
					'right'  => 50,
					'bottom' => 50,
					'left'   => 50,
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .proslide-subtitle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'subtitle_border',
				'selector' => '{{WRAPPER}} .proslide-subtitle',
			]
		);

		$this->add_responsive_control(
			'subtitle_margin_bottom',
			[
				'label'     => esc_html__( 'Spacing Below', 'proslide-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'default'   => [ 'size' => 16, 'unit' => 'px' ],
				'selectors' => [
					'{{WRAPPER}} .proslide-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Title (Global).
	 */
	private function register_style_title_section() {
		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title (Global)', 'proslide-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'title_align',
			[
				'label'     => esc_html__( 'Alignment', 'proslide-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .proslide-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .proslide-title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .proslide-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .proslide-title',
			]
		);

		$this->add_responsive_control(
			'title_margin_bottom',
			[
				'label'     => esc_html__( 'Spacing Below', 'proslide-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'default'   => [ 'size' => 16, 'unit' => 'px' ],
				'selectors' => [
					'{{WRAPPER}} .proslide-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Description (Global).
	 */
	private function register_style_description_section() {
		$this->start_controls_section(
			'section_style_description',
			[
				'label' => esc_html__( 'Description (Global)', 'proslide-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'description_align',
			[
				'label'     => esc_html__( 'Alignment', 'proslide-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'proslide-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .proslide-description' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .proslide-description',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2e8f0',
				'selectors' => [
					'{{WRAPPER}} .proslide-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'description_margin_bottom',
			[
				'label'     => esc_html__( 'Spacing Below', 'proslide-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 80 ] ],
				'default'   => [ 'size' => 26, 'unit' => 'px' ],
				'selectors' => [
					'{{WRAPPER}} .proslide-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Reusable Button Style Section (Global).
	 *
	 * @param string $prefix   Control ID prefix.
	 * @param string $label    Section label.
	 * @param string $selector CSS selector.
	 */
	private function register_style_button_section( $prefix, $label, $selector ) {
		$this->start_controls_section(
			'section_style_' . $prefix,
			[
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		if ( 'btn_1' === $prefix ) {
			$this->add_responsive_control(
				'buttons_align',
				[
					'label'     => esc_html__( 'Buttons Alignment', 'proslide-elementor' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => [
						'flex-start' => [
							'title' => esc_html__( 'Left', 'proslide-elementor' ),
							'icon'  => 'eicon-h-align-left',
						],
						'center'     => [
							'title' => esc_html__( 'Center', 'proslide-elementor' ),
							'icon'  => 'eicon-h-align-center',
						],
						'flex-end'   => [
							'title' => esc_html__( 'Right', 'proslide-elementor' ),
							'icon'  => 'eicon-h-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .proslide-buttons' => 'justify-content: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
				'buttons_gap',
				[
					'label'     => esc_html__( 'Gap Between Buttons', 'proslide-elementor' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
					'default'   => [ 'size' => 12, 'unit' => 'px' ],
					'selectors' => [
						'{{WRAPPER}} .proslide-buttons' => 'gap: {{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $prefix . '_typography',
				'selector' => '{{WRAPPER}} ' . $selector,
			]
		);

		$this->add_responsive_control(
			$prefix . '_padding',
			[
				'label'      => esc_html__( 'Padding', 'proslide-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'    => 13,
					'right'  => 28,
					'bottom' => 13,
					'left'   => 28,
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			$prefix . '_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'proslide-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'    => 8,
					'right'  => 8,
					'bottom' => 8,
					'left'   => 8,
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} ' . $selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_' . $prefix . '_style' );

		$this->start_controls_tab(
			'tab_' . $prefix . '_normal',
			[
				'label' => esc_html__( 'Normal', 'proslide-elementor' ),
			]
		);

		$this->add_control(
			$prefix . '_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} ' . $selector         => 'color: {{VALUE}};',
					'{{WRAPPER}} ' . $selector . ' svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . '_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => ( 'btn_1' === $prefix ) ? '#6366f1' : 'rgba(255, 255, 255, 0.12)',
				'selectors' => [
					'{{WRAPPER}} ' . $selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => $prefix . '_border',
				'selector' => '{{WRAPPER}} ' . $selector,
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_' . $prefix . '_hover',
			[
				'label' => esc_html__( 'Hover', 'proslide-elementor' ),
			]
		);

		$this->add_control(
			$prefix . '_hover_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} ' . $selector . ':hover'     => 'color: {{VALUE}};',
					'{{WRAPPER}} ' . $selector . ':hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . '_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => ( 'btn_1' === $prefix ) ? '#4f46e5' : 'rgba(255, 255, 255, 0.25)',
				'selectors' => [
					'{{WRAPPER}} ' . $selector . ':hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . '_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'proslide-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $selector . ':hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => $prefix . '_box_shadow',
				'selector'  => '{{WRAPPER}} ' . $selector,
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$slides   = ! empty( $settings['slides'] ) ? $settings['slides'] : [];

		if ( empty( $slides ) ) {
			return;
		}

		$swiper_config   = Helpers::build_swiper_config( $settings );
		$arrows_pos      = ! empty( $settings['arrows_position'] ) ? $settings['arrows_position'] : 'inside';
		$dots_pos        = ! empty( $settings['dots_position'] ) ? $settings['dots_position'] : 'inside-bottom';
		$content_anim    = ! empty( $settings['content_animation'] ) ? $settings['content_animation'] : 'none';

		$wrapper_classes = [
			'proslide-wrapper',
			'proslide-arrows-' . sanitize_html_class( $arrows_pos ),
			'proslide-dots-' . sanitize_html_class( $dots_pos ),
		];

		if ( 'none' !== $content_anim ) {
			$wrapper_classes[] = 'proslide-anim-' . sanitize_html_class( $content_anim );
		}
		?>
		<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" data-proslide-config="<?php echo esc_attr( wp_json_encode( $swiper_config ) ); ?>">
			<div class="swiper proslide-swiper">
				<div class="swiper-wrapper">
					<?php foreach ( $slides as $index => $slide ) : ?>
						<?php $this->render_single_slide( $slide, $index ); ?>
					<?php endforeach; ?>
				</div>
			</div>
			<?php Helpers::render_navigation_and_pagination( $settings ); ?>
		</div>
		<?php
	}

	/**
	 * Render a single slide item.
	 *
	 * @param array $slide Slide repeater item.
	 * @param int   $index Slide index.
	 */
	private function render_single_slide( array $slide, $index ) {
		$slide_type    = ! empty( $slide['slide_type'] ) ? $slide['slide_type'] : 'content';
		$bg_type       = ! empty( $slide['slide_bg_type'] ) ? $slide['slide_bg_type'] : 'classic';
		$ken_burns     = ( 'yes' === ( $slide['slide_bg_ken_burns'] ?? '' ) );
		$ken_burns_dir = ! empty( $slide['slide_bg_ken_burns_dir'] ) ? $slide['slide_bg_ken_burns_dir'] : 'in';

		$bg_styles = [];
		if ( 'gradient' === $bg_type ) {
			$c1    = ! empty( $slide['slide_bg_gradient_a'] ) ? $slide['slide_bg_gradient_a'] : '#4f46e5';
			$c2    = ! empty( $slide['slide_bg_gradient_b'] ) ? $slide['slide_bg_gradient_b'] : '#06b6d4';
			$angle = isset( $slide['slide_bg_gradient_angle'] ) ? (int) $slide['slide_bg_gradient_angle'] : 135;
			$bg_styles[] = sprintf( 'background: linear-gradient(%ddeg, %s, %s);', $angle, esc_attr( $c1 ), esc_attr( $c2 ) );
		} else {
			if ( ! empty( $slide['slide_bg_color'] ) ) {
				$bg_styles[] = 'background-color:' . esc_attr( $slide['slide_bg_color'] ) . ';';
			}
			if ( ! empty( $slide['slide_bg_image']['url'] ) ) {
				$bg_styles[] = "background-image:url('" . esc_url( $slide['slide_bg_image']['url'] ) . "');";
				$bg_styles[] = 'background-size:' . esc_attr( $slide['slide_bg_size'] ?? 'cover' ) . ';';
				$bg_styles[] = 'background-position:' . esc_attr( $slide['slide_bg_position'] ?? 'center center' ) . ';';
			}
		}

		$bg_classes = [ 'proslide-bg-layer' ];
		if ( $ken_burns && 'classic' === $bg_type ) {
			$bg_classes[] = 'proslide-ken-burns';
			$bg_classes[] = 'proslide-ken-burns-' . sanitize_html_class( $ken_burns_dir );
		}

		// Horizontal and Vertical position mapping.
		$h_pos_val = ! empty( $slide['slide_h_pos'] ) ? $slide['slide_h_pos'] : ( ! empty( $slide['slide_h_align'] ) ? $slide['slide_h_align'] : 'center' );
		$v_pos_val = ! empty( $slide['slide_v_pos'] ) ? $slide['slide_v_pos'] : ( ! empty( $slide['slide_v_align'] ) ? $slide['slide_v_align'] : 'center' );

		// Normalize horizontal position class (left, center, right).
		$h_pos_clean = in_array( $h_pos_val, [ 'left', 'flex-start' ], true ) ? 'left' : ( in_array( $h_pos_val, [ 'right', 'flex-end' ], true ) ? 'right' : 'center' );

		// Normalize vertical justify-content (top -> flex-start, center -> center, bottom -> flex-end).
		$v_justify = in_array( $v_pos_val, [ 'top', 'flex-start' ], true ) ? 'flex-start' : ( in_array( $v_pos_val, [ 'bottom', 'flex-end' ], true ) ? 'flex-end' : 'center' );

		$text_align = ! empty( $slide['slide_text_align'] ) ? $slide['slide_text_align'] : 'center';

		$inner_classes = [
			'proslide-slide-inner',
			'proslide-h-' . $h_pos_clean,
		];

		$inner_style = sprintf(
			'justify-content:%s; text-align:%s;',
			esc_attr( $v_justify ),
			esc_attr( $text_align )
		);

		$overlay_style = '';
		if ( ! empty( $slide['slide_overlay_color'] ) ) {
			$overlay_style = 'background-color:' . esc_attr( $slide['slide_overlay_color'] ) . ';';
		}

		$whole_link = ! empty( $slide['slide_whole_link']['url'] ) ? $slide['slide_whole_link'] : null;
		?>
		<div class="swiper-slide proslide-slide elementor-repeater-item-<?php echo esc_attr( $slide['_id'] ?? $index ); ?>">
			<div class="<?php echo esc_attr( implode( ' ', $bg_classes ) ); ?>" style="<?php echo esc_attr( implode( ' ', $bg_styles ) ); ?>"></div>
			<?php if ( $overlay_style ) : ?>
				<div class="proslide-bg-overlay" style="<?php echo esc_attr( $overlay_style ); ?>"></div>
			<?php endif; ?>

			<?php if ( $whole_link ) : ?>
				<a class="proslide-whole-link" href="<?php echo esc_url( $whole_link['url'] ); ?>"<?php echo ! empty( $whole_link['is_external'] ) ? ' target="_blank"' : ''; ?><?php echo ! empty( $whole_link['nofollow'] ) ? ' rel="nofollow"' : ''; ?> aria-label="<?php echo esc_attr( wp_strip_all_tags( $slide['slide_title'] ?? '' ) ); ?>"></a>
			<?php endif; ?>

			<?php if ( in_array( $slide_type, [ 'template', 'both' ], true ) && ! empty( $slide['elementor_template'] ) ) : ?>
				<div class="proslide-template-layer">
					<?php echo Helpers::render_elementor_template( $slide['elementor_template'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>

			<?php if ( in_array( $slide_type, [ 'content', 'both' ], true ) ) : ?>
				<div class="<?php echo esc_attr( implode( ' ', $inner_classes ) ); ?>" style="<?php echo esc_attr( $inner_style ); ?>">
					<div class="proslide-content-box">
						<?php $this->render_slide_content( $slide ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render slide inner text & buttons with per-slide overrides.
	 *
	 * @param array $slide Slide data.
	 */
	private function render_slide_content( array $slide ) {
		$override = ( 'yes' === ( $slide['slide_override_colors'] ?? '' ) );

		if ( ! empty( $slide['slide_subtitle'] ) ) :
			$sub_styles = [];
			if ( $override ) {
				if ( ! empty( $slide['slide_custom_subtitle_color'] ) ) {
					$sub_styles[] = 'color:' . esc_attr( $slide['slide_custom_subtitle_color'] ) . ';';
				}
				if ( ! empty( $slide['slide_custom_subtitle_bg'] ) ) {
					$sub_styles[] = 'background-color:' . esc_attr( $slide['slide_custom_subtitle_bg'] ) . ';';
				}
				if ( ! empty( $slide['slide_custom_subtitle_size'] ) ) {
					$sub_styles[] = 'font-size:' . absint( $slide['slide_custom_subtitle_size'] ) . 'px;';
				}
			}
			$sub_attr = ! empty( $sub_styles ) ? ' style="' . esc_attr( implode( ' ', $sub_styles ) ) . '"' : '';
			?>
			<div class="proslide-subtitle"<?php echo $sub_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php if ( ! empty( $slide['slide_subtitle_icon']['value'] ) ) : ?>
					<span class="proslide-subtitle-icon">
						<?php Icons_Manager::render_icon( $slide['slide_subtitle_icon'], [ 'aria-hidden' => 'true' ] ); ?>
					</span>
				<?php endif; ?>
				<span><?php echo esc_html( $slide['slide_subtitle'] ); ?></span>
			</div>
			<?php
		endif;

		if ( ! empty( $slide['slide_title'] ) ) :
			$tag          = Utils::validate_html_tag( $slide['slide_title_tag'] ?? 'h2' );
			$title_styles = [];
			if ( $override ) {
				if ( ! empty( $slide['slide_custom_title_color'] ) ) {
					$title_styles[] = 'color:' . esc_attr( $slide['slide_custom_title_color'] ) . ';';
				}
				if ( ! empty( $slide['slide_custom_title_size'] ) ) {
					$title_styles[] = 'font-size:' . absint( $slide['slide_custom_title_size'] ) . 'px;';
				}
				if ( ! empty( $slide['slide_custom_title_weight'] ) ) {
					$title_styles[] = 'font-weight:' . esc_attr( $slide['slide_custom_title_weight'] ) . ';';
				}
			}
			$title_attr = ! empty( $title_styles ) ? ' style="' . esc_attr( implode( ' ', $title_styles ) ) . '"' : '';
			printf(
				'<%1$s class="proslide-title"%2$s>%3$s</%1$s>',
				esc_html( $tag ),
				$title_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				wp_kses_post( nl2br( $slide['slide_title'] ) )
			);
		endif;

		if ( ! empty( $slide['slide_description'] ) ) :
			$desc_styles = [];
			if ( $override ) {
				if ( ! empty( $slide['slide_custom_desc_color'] ) ) {
					$desc_styles[] = 'color:' . esc_attr( $slide['slide_custom_desc_color'] ) . ';';
				}
				if ( ! empty( $slide['slide_custom_desc_size'] ) ) {
					$desc_styles[] = 'font-size:' . absint( $slide['slide_custom_desc_size'] ) . 'px;';
				}
			}
			$desc_attr = ! empty( $desc_styles ) ? ' style="' . esc_attr( implode( ' ', $desc_styles ) ) . '"' : '';
			?>
			<div class="proslide-description"<?php echo $desc_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php echo wp_kses_post( $slide['slide_description'] ); ?>
			</div>
			<?php
		endif;

		$has_btn_1 = ! empty( $slide['slide_btn_1_text'] );
		$has_btn_2 = ! empty( $slide['slide_btn_2_text'] );

		if ( $has_btn_1 || $has_btn_2 ) :
			$btn1_styles = [];
			$btn2_styles = [];

			if ( $override ) {
				if ( ! empty( $slide['slide_custom_btn1_color'] ) ) {
					$btn1_styles[] = 'color:' . esc_attr( $slide['slide_custom_btn1_color'] ) . ';';
				}
				if ( ! empty( $slide['slide_custom_btn1_bg'] ) ) {
					$btn1_styles[] = 'background-color:' . esc_attr( $slide['slide_custom_btn1_bg'] ) . ';';
				}
				if ( isset( $slide['slide_custom_btn1_padding_v'] ) && '' !== $slide['slide_custom_btn1_padding_v'] ) {
					$btn1_styles[] = 'padding-top:' . absint( $slide['slide_custom_btn1_padding_v'] ) . 'px; padding-bottom:' . absint( $slide['slide_custom_btn1_padding_v'] ) . 'px;';
				}
				if ( isset( $slide['slide_custom_btn1_padding_h'] ) && '' !== $slide['slide_custom_btn1_padding_h'] ) {
					$btn1_styles[] = 'padding-left:' . absint( $slide['slide_custom_btn1_padding_h'] ) . 'px; padding-right:' . absint( $slide['slide_custom_btn1_padding_h'] ) . 'px;';
				}
				if ( isset( $slide['slide_custom_btn1_radius'] ) && '' !== $slide['slide_custom_btn1_radius'] ) {
					$btn1_styles[] = 'border-radius:' . absint( $slide['slide_custom_btn1_radius'] ) . 'px;';
				}

				if ( ! empty( $slide['slide_custom_btn2_color'] ) ) {
					$btn2_styles[] = 'color:' . esc_attr( $slide['slide_custom_btn2_color'] ) . ';';
				}
				if ( ! empty( $slide['slide_custom_btn2_bg'] ) ) {
					$btn2_styles[] = 'background-color:' . esc_attr( $slide['slide_custom_btn2_bg'] ) . ';';
				}
				if ( isset( $slide['slide_custom_btn2_padding_v'] ) && '' !== $slide['slide_custom_btn2_padding_v'] ) {
					$btn2_styles[] = 'padding-top:' . absint( $slide['slide_custom_btn2_padding_v'] ) . 'px; padding-bottom:' . absint( $slide['slide_custom_btn2_padding_v'] ) . 'px;';
				}
				if ( isset( $slide['slide_custom_btn2_padding_h'] ) && '' !== $slide['slide_custom_btn2_padding_h'] ) {
					$btn2_styles[] = 'padding-left:' . absint( $slide['slide_custom_btn2_padding_h'] ) . 'px; padding-right:' . absint( $slide['slide_custom_btn2_padding_h'] ) . 'px;';
				}
				if ( isset( $slide['slide_custom_btn2_radius'] ) && '' !== $slide['slide_custom_btn2_radius'] ) {
					$btn2_styles[] = 'border-radius:' . absint( $slide['slide_custom_btn2_radius'] ) . 'px;';
				}
			}

			$btn1_inline = ! empty( $btn1_styles ) ? implode( ' ', $btn1_styles ) : '';
			$btn2_inline = ! empty( $btn2_styles ) ? implode( ' ', $btn2_styles ) : '';
			?>
			<div class="proslide-buttons">
				<?php
				if ( $has_btn_1 ) {
					$this->render_button(
						$slide['slide_btn_1_text'],
						$slide['slide_btn_1_link'] ?? [],
						$slide['slide_btn_1_icon'] ?? [],
						$slide['slide_btn_1_icon_pos'] ?? 'right',
						'proslide-btn proslide-btn-primary',
						$btn1_inline
					);
				}
				if ( $has_btn_2 ) {
					$this->render_button(
						$slide['slide_btn_2_text'],
						$slide['slide_btn_2_link'] ?? [],
						$slide['slide_btn_2_icon'] ?? [],
						$slide['slide_btn_2_icon_pos'] ?? 'right',
						'proslide-btn proslide-btn-secondary',
						$btn2_inline
					);
				}
				?>
			</div>
			<?php
		endif;
	}

	/**
	 * Render a CTA button.
	 *
	 * @param string $text     Button label.
	 * @param array  $link     URL control array.
	 * @param array  $icon     Icons control array.
	 * @param string $icon_pos Icon position (left|right).
	 * @param string $classes  CSS classes.
	 * @param string $inline   Inline style string.
	 */
	private function render_button( $text, array $link, array $icon, $icon_pos, $classes, $inline = '' ) {
		$url         = ! empty( $link['url'] ) ? $link['url'] : '#';
		$external    = ! empty( $link['is_external'] ) ? ' target="_blank"' : '';
		$nofollow    = ! empty( $link['nofollow'] ) ? ' rel="nofollow"' : '';
		$has_icon    = ! empty( $icon['value'] );
		$style_attr  = $inline ? ' style="' . esc_attr( $inline ) . '"' : '';
		?>
		<a class="<?php echo esc_attr( $classes ); ?>" href="<?php echo esc_url( $url ); ?>"<?php echo $external . $nofollow . $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php if ( $has_icon && 'left' === $icon_pos ) : ?>
				<span class="proslide-btn-icon proslide-btn-icon-left">
					<?php Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?>
				</span>
			<?php endif; ?>
			<span><?php echo esc_html( $text ); ?></span>
			<?php if ( $has_icon && 'right' === $icon_pos ) : ?>
				<span class="proslide-btn-icon proslide-btn-icon-right">
					<?php Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] ); ?>
				</span>
			<?php endif; ?>
		</a>
		<?php
	}
}
