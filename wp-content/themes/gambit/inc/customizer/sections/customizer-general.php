<?php
/**
 * General Settings
 *
 * Register General section, settings and controls for Theme Customizer
 *
 * @package Gambit
 */

/**
 * Adds all general settings to the Customizer
 *
 * @param object $wp_customize / Customizer Object.
 */
function gambit_customize_register_general_settings( $wp_customize ) {

	// Add Section for Theme Options.
	$wp_customize->add_section(
		'gambit_section_general',
		array(
			'title'    => esc_html__( 'General Settings', 'gambit' ),
			'priority' => 10,
			'panel'    => 'gambit_options_panel',
		)
	);

	// Add Settings and Controls for Theme Width.
	$wp_customize->add_setting(
		'gambit_theme_options[theme_width]',
		array(
			'default'           => 'boxed-layout',
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'gambit_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'gambit_theme_options[theme_width]',
		array(
			'label'    => esc_html__( 'Theme Width', 'gambit' ),
			'section'  => 'gambit_section_general',
			'settings' => 'gambit_theme_options[theme_width]',
			'type'     => 'radio',
			'priority' => 1,
			'choices'  => array(
				'boxed-layout' => esc_html__( 'Boxed Layout', 'gambit' ),
				'wide-layout'  => esc_html__( 'Wide Layout', 'gambit' ),
			),
		)
	);

	// Add Settings and Controls for Theme Layout.
	$wp_customize->add_setting(
		'gambit_theme_options[theme_layout]',
		array(
			'default'           => 'content-center',
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'gambit_sanitize_select',
		)
	);
	$wp_customize->add_control(
		'gambit_theme_options[theme_layout]',
		array(
			'label'    => esc_html__( 'Theme Layout', 'gambit' ),
			'section'  => 'gambit_section_general',
			'settings' => 'gambit_theme_options[theme_layout]',
			'type'     => 'radio',
			'priority' => 2,
			'choices'  => array(
				'content-left'   => esc_html__( 'Content Left', 'gambit' ),
				'content-center' => esc_html__( 'Content Center', 'gambit' ),
				'content-right'  => esc_html__( 'Content Right', 'gambit' ),
			),
		)
	);
}
add_action( 'customize_register', 'gambit_customize_register_general_settings' );
