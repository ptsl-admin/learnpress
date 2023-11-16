<?php
/**
 * Elementor Controls for widget filter course settings.
 *
 * @since 4.2.5
 * @version 1.0.0
 */

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use LearnPress\ExternalPlugin\Elementor\LPElementorControls;

$content_fields = array_merge( 
    LPElementorControls::add_fields_in_section(
        'layout_filter',
        esc_html__( 'Layout', 'learnpress' ),
        Controls_Manager::TAB_CONTENT,
        [
            LPElementorControls::add_control_type_select(
                'layout',
                esc_html__( 'Layout', 'learnpress' ),
                [
                    'base'  => esc_html__( 'Default', 'learnpress' ),
                    'popup' => esc_html__( 'Popup', 'learnpress' ),
                ],
                'base' 
            ),
            LPElementorControls::add_control_type(
                'button_popup',
                esc_html__( 'Text Popup', 'learnpress' ),
                esc_html__( 'Filter', 'learnpress' ),
                Controls_Manager::TEXT,
                [
                    'label_block' => true,
                    'condition'   => [
                        'layout' => 'popup',
                    ]
                ]
            ),
            LPElementorControls::add_control_type(
                'icon_popup',
                esc_html__( 'Icon Popup', 'learnpress' ),
                [],
                Controls_Manager::ICONS,
                [
                    'skin'        => 'inline',
                    'label_block' => false,
                    'condition'   => [
                        'layout' => 'popup',
                    ]
                ]

            ),
            LPElementorControls::add_control_type_select(
                'icon_align',
                esc_html__( 'Icon Position', 'learnpress' ),
                [
                    'left'  => esc_html__( 'Before', 'learnpress' ),
					'right' => esc_html__( 'After', 'learnpress' ),
                ],
                'left',
                [
                    'condition' => [
                        'layout' => 'popup',
                        'icon_popup[value]!' => ''
                    ] 
                ] 
            ),
            LPElementorControls::add_control_type_select(
                'selected_style_show',
                esc_html__( 'Selected Style', 'learnpress' ),
                [
                    'none'   => esc_html__( 'None', 'learnpress' ),
					'number' => esc_html__( 'Number', 'learnpress' ),
                    'list'   => esc_html__( 'List', 'learnpress' ),
                ],
                'number',
                [
                    'condition'   => [
                        'layout' => 'popup',
                    ]
                ] 
            ),
        ]
    ),
    LPElementorControls::add_fields_in_section(
        'content_filter',
        esc_html__( 'Content', 'learnpress' ),
        Controls_Manager::TAB_CONTENT,
        [
            LPElementorControls::add_control_type(
				'show_in_rest',
                esc_html__( 'Load widget via REST', 'learnpress' ),
				'yes',
				Controls_Manager::SWITCHER,
				[
					'label_on'     => esc_html__( 'Yes', 'learnpress' ),
					'label_off'    => esc_html__( 'No', 'learnpress' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			),
            LPElementorControls::add_control_type(
				'search_suggestion',
                esc_html__( 'Enable Keyword Search Suggestion', 'learnpress' ),
				'yes',
				Controls_Manager::SWITCHER,
				[
					'label_on'     => esc_html__( 'Yes', 'learnpress' ),
					'label_off'    => esc_html__( 'No', 'learnpress' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			),
            LPElementorControls::add_control_type(
                'item_filter',
                esc_html__( 'Fields', 'learnpress' ),
                [
					[
						'item_fields'  => 'category',
                    ],
                    [
                        'item_fields'  => 'btn_submit',
					]
				],
                Controls_Manager::REPEATER,
                [
                    'fields'        => [
                        [
                            'name'        => 'item_fields',
                            'label'       => esc_html__( 'Filter By', 'learnpress' ),
                            'type'        => Controls_Manager::SELECT,
                            'options'     => array(
                                'search'     =>  esc_html__( 'Keyword', 'learnpress' ),
                                'price'      =>  esc_html__( 'Price', 'learnpress' ),
                                'category'   =>  esc_html__( 'Course Category', 'learnpress' ),
                                'tag'        =>  esc_html__( 'Course Tag', 'learnpress' ),
                                'author'     =>  esc_html__( 'Author', 'learnpress' ),
                                'level'      =>  esc_html__( 'Level', 'learnpress' ),
                                'btn_submit' =>  esc_html__( 'Button Submit', 'learnpress' ),
                                'btn_reset'  =>  esc_html__( 'Button Reset', 'learnpress' ),
                            ),
                        ]
                    ],
                    'prevent_empty' => false,
                    'title_field'   => '{{{ item_fields }}}',
                ]
            ),
        ]
    ),
    []
);

$style_fields   = array_merge(
    LPElementorControls::add_fields_in_section(
		'filter_layout',
		esc_html__( 'Layout', 'learnpress' ),
		Controls_Manager::TAB_STYLE,
        [
            "filter_layout_margin"           => LPElementorControls::add_responsive_control_type(
                "filter_layout_margin",
                esc_html__( 'Margin', 'learnpress' ),
                [],
                Controls_Manager::DIMENSIONS,
                [
                    'size_units' => [ 'px', '%', 'custom' ],
                    'selectors'  => array(
                        "{{WRAPPER}} .lp-form-course-filter" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ),
                ]
            ),
            "filter_layout_padding"          => LPElementorControls::add_responsive_control_type(
                "filter_layout_padding",
                esc_html__( 'Padding', 'learnpress' ),
                [],
                Controls_Manager::DIMENSIONS,
                [
                    'size_units' => [ 'px', '%', 'custom' ],
                    'selectors'  => array(
                        "{{WRAPPER}} .lp-form-course-filter" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ),
                ]
            ),
            "filter_layout_border" => LPElementorControls::add_group_control_type(
                "filter_layout_border",
                Group_Control_Border::get_type(),
                '{{WRAPPER}} .lp-form-course-filter'
            ),
            "filter_layout_background" => LPElementorControls::add_control_type_color(
                "filter_layout_background",
                esc_html__( 'Background', 'learnpress' ),
                ['{{WRAPPER}} .lp-form-course-filter' =>  'background: {{VALUE}}' ]
            ),
            "filter_layout_radius"          => LPElementorControls::add_responsive_control_type(
                "filter_layout_radius",
                esc_html__( 'Radius', 'learnpress' ),
                [],
                Controls_Manager::DIMENSIONS,
                [
                    'size_units' => [ 'px', '%', 'custom' ],
                    'selectors'  => array(
                        "{{WRAPPER}} .lp-form-course-filter" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ),
                ]
            ),
            "filter_layout_Shadow" => LPElementorControls::add_group_control_type(
                "filter_layout_Shadow",
                Group_Control_Box_Shadow::get_type(),
                '{{WRAPPER}} .lp-form-course-filter'
            ),
        ]
	),
    LPElementorControls::add_fields_in_section(
		'filter_item',
		esc_html__( 'Item', 'learnpress' ),
		Controls_Manager::TAB_STYLE,
        [
            "item_margin"           => LPElementorControls::add_responsive_control_type(
                "item_margin",
                esc_html__( 'Margin', 'learnpress' ),
                [],
                Controls_Manager::DIMENSIONS,
                [
                    'size_units' => [ 'px', '%', 'custom' ],
                    'selectors'  => array(
                        "{{WRAPPER}} .lp-form-course-filter__item" => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ),
                ]
            ),
            "item_padding"          => LPElementorControls::add_responsive_control_type(
                "item_padding",
                esc_html__( 'Padding', 'learnpress' ),
                [],
                Controls_Manager::DIMENSIONS,
                [
                    'size_units' => [ 'px', '%', 'custom' ],
                    'selectors'  => array(
                        "{{WRAPPER}} .lp-form-course-filter__item" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ),
                ]
            ),
            "item_border" => LPElementorControls::add_group_control_type(
                "item_border",
                Group_Control_Border::get_type(),
                '{{WRAPPER}} .lp-form-course-filter__item'
            ),
            "item_background" => LPElementorControls::add_control_type_color(
                "layout_background",
                esc_html__( 'Background', 'learnpress' ),
                ['{{WRAPPER}} .lp-form-course-filter__item' =>  'background: {{VALUE}}' ]
            ),
            "item_radius"          => LPElementorControls::add_responsive_control_type(
                "item_radius",
                esc_html__( 'Radius', 'learnpress' ),
                [],
                Controls_Manager::DIMENSIONS,
                [
                    'size_units' => [ 'px', '%', 'custom' ],
                    'selectors'  => array(
                        "{{WRAPPER}} .lp-form-course-filter__item" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ),
                ]
            )
        ]
	),
    LPElementorControls::add_fields_in_section(
		'filter_title',
		esc_html__( 'Title', 'learnpress' ),
		Controls_Manager::TAB_STYLE,
		LPElementorControls::add_controls_style_text(
			'filter_title',
			'.lp-form-course-filter .lp-form-course-filter__title',
            [],
            [ 'text_display','text_background', 'text_background_hover' ]
		)
	),
    LPElementorControls::add_fields_in_section(
		'filter_content',
		esc_html__( 'Label', 'learnpress' ),
		Controls_Manager::TAB_STYLE,
		LPElementorControls::add_controls_style_text(
			'filter_content',
			'.lp-form-course-filter .lp-form-course-filter__item .lp-form-course-filter__content label',
            [],
            [ 'text_display','text_shadow', 'text_background', 'text_background_hover' ]
		)
	),
    LPElementorControls::add_fields_in_section(
		'input_search',
		esc_html__( 'Search', 'learnpress' ),
		Controls_Manager::TAB_STYLE,
		LPElementorControls::add_controls_style_button(
			'input_search',
			'.lp-form-course-filter__content .lp-course-filter-search-field input',
            [],
            [ 'text_display','text_shadow', 'text_color_hover', 'text_background', 'text_background_hover' ]
		)
    ),
    LPElementorControls::add_fields_in_section(
		'btn_submit',
		esc_html__( 'Button Submit', 'learnpress' ),
		Controls_Manager::TAB_STYLE,
		LPElementorControls::add_controls_style_button(
			'btn_submit',
			'.course-filter-submit',
            [
                'btn_submit_align'         => LPElementorControls::add_responsive_control_type(
					'btn_submit_align',
					esc_html__( 'Alignment', 'learnpress' ),
					'',
					Controls_Manager::CHOOSE,
					[
						'options'   => [
							'left'   => [
								'title' => esc_html__( 'Left', 'learnpress' ),
								'icon'  => 'eicon-text-align-left',
							],
							'center' => [
								'title' => esc_html__( 'Center', 'learnpress' ),
								'icon'  => 'eicon-text-align-center',
							],
							'right'  => [
								'title' => esc_html__( 'Right', 'learnpress' ),
								'icon'  => 'eicon-text-align-right',
							],
						],
						'selectors' => [
							'{{WRAPPER}} .course-filter-submit' => 'text-align: {{VALUE}};',
						],
					]
				),
                "btn_submit_width"          => LPElementorControls::add_responsive_control_type(
                    "btn_submit_width",
                    esc_html__( 'Width', 'learnpress' ),
                    [],
                    Controls_Manager::SLIDER,
                    [
                        'size_units' => array( 'px', '%', 'custom' ),
                        'range'      => array(
                            'px' => array(
                                'min' => 1,
                                'max' => 500,
                                'step'=> 5
                            ),
                        ),
                        'selectors'  => array(
                            '{{WRAPPER}} .course-filter-submit' => 'width: {{SIZE}}{{UNIT}};',
                        ),
                    ]
                )
            ],
            [ 'text_display' ]
		)
    ),
    LPElementorControls::add_fields_in_section(
		'btn_reset',
		esc_html__( 'Button Reset', 'learnpress' ),
		Controls_Manager::TAB_STYLE,
        LPElementorControls::add_controls_style_button(
            'btn_reset',
            '.course-filter-reset',
            [
                'btn_reset_align'         => LPElementorControls::add_responsive_control_type(
					'btn_reset_align',
					esc_html__( 'Alignment', 'learnpress' ),
					'',
					Controls_Manager::CHOOSE,
					[
						'options'   => [
							'left'   => [
								'title' => esc_html__( 'Left', 'learnpress' ),
								'icon'  => 'eicon-text-align-left',
							],
							'center' => [
								'title' => esc_html__( 'Center', 'learnpress' ),
								'icon'  => 'eicon-text-align-center',
							],
							'right'  => [
								'title' => esc_html__( 'Right', 'learnpress' ),
								'icon'  => 'eicon-text-align-right',
							],
						],
						'selectors' => [
							'{{WRAPPER}} .course-filter-reset' => 'text-align: {{VALUE}};',
						],
					]
				),
                "btn_reset_width"          => LPElementorControls::add_responsive_control_type(
                    "btn_reset_width",
                    esc_html__( 'Width', 'learnpress' ),
                    [],
                    Controls_Manager::SLIDER,
                    [
                        'size_units' => array( 'px', '%', 'custom' ),
                        'range'      => array(
                            'px' => array(
                                'min' => 1,
                                'max' => 500,
                                'step'=> 5
                            ),
                        ),
                        'selectors'  => array(
                            '{{WRAPPER}} .course-filter-reset' => 'width: {{SIZE}}{{UNIT}};',
                        ),
                    ]
                )
            ],
            [ 'text_display' ]
        )
    ),
    LPElementorControls::add_fields_in_section(
		'btn_popup',
		esc_html__( 'Button Popup', 'learnpress' ),
		Controls_Manager::TAB_STYLE,
        LPElementorControls::add_controls_style_button(
            'btn_popup',
            '.lp-button-popup',
            [
                'btn_popup_align'         => LPElementorControls::add_responsive_control_type(
					'btn_popup_align',
					esc_html__( 'Alignment', 'learnpress' ),
					'',
					Controls_Manager::CHOOSE,
					[
						'options'   => [
							'left'   => [
								'title' => esc_html__( 'Left', 'learnpress' ),
								'icon'  => 'eicon-text-align-left',
							],
							'center' => [
								'title' => esc_html__( 'Center', 'learnpress' ),
								'icon'  => 'eicon-text-align-center',
							],
							'right'  => [
								'title' => esc_html__( 'Right', 'learnpress' ),
								'icon'  => 'eicon-text-align-right',
							],
						],
						'selectors' => [
							'{{WRAPPER}} .lp-button-popup' => 'text-align: {{VALUE}};',
						],
					]
				),
                "btn_popup_width"          => LPElementorControls::add_responsive_control_type(
                    "btn_popup_width",
                    esc_html__( 'Width', 'learnpress' ),
                    [],
                    Controls_Manager::SLIDER,
                    [
                        'size_units' => array( 'px', '%', 'custom' ),
                        'range'      => array(
                            'px' => array(
                                'min' => 1,
                                'max' => 500,
                                'step'=> 5
                            ),
                        ),
                        'selectors'  => array(
                            '{{WRAPPER}} .lp-button-popup' => 'width: {{SIZE}}{{UNIT}};',
                        ),
                    ]
                )
            ],
            [ 'text_display' ]
        )
    )
);


return apply_filters(
	'learn-press/elementor/course/filter-course-el',
    array_merge(
        apply_filters(
            'learn-press/elementor/course/filter-course-el/tab-content',
            $content_fields
        ),
        apply_filters(
            'learn-press/elementor/course/filter-course-el/tab-styles',
            $style_fields
        )
    )
);