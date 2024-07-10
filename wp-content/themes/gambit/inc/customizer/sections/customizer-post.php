<?php
/**
 * Post Settings
 *
 * Register Post Settings section, settings and controls for Theme Customizer
 *
 * @package Gambit
 */

/**
 * Adds post settings in the Customizer
 *
 * @param object $wp_customize / Customizer Object.
 */
function gambit_customize_register_post_settings( $wp_customize ) {

	// Add Sections for Post Settings.
	$wp_customize->add_section(
		'gambit_section_post',
		array(
			'title'    => esc_html__( 'Post Settings', 'gambit' ),
			'priority' => 30,
			'panel'    => 'gambit_options_panel',
		)
	);

	// Add Post Meta Settings.
	$wp_customize->add_setting(
		'gambit_theme_options[postmeta_headline]',
		array(
			'default'           => '',
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_attr',
		)
	);
	$wp_customize->add_control(
		new Gambit_Customize_Header_Control(
			$wp_customize,
			'gambit_theme_options[postmeta_headline]',
			array(
				'label'    => esc_html__( 'Post Details', 'gambit' ),
				'section'  => 'gambit_section_post',
				'settings' => 'gambit_theme_options[postmeta_headline]',
				'priority' => 40,
			)
		)
	);

	$wp_customize->add_setting(
		'gambit_theme_options[meta_date]',
		array(
			'default'           => true,
			'type'              => 'option',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'gambit_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'gambit_theme_options[meta_date]',
		array(
			'label'    => esc_html__( 'Display date', 'gambit' ),
			'section'  => 'gambit_section_post',
			'settings' => 'gambit_theme_options[meta_date]',
			'type'     => 'checkbox',
			'priority' => 50,
		)
	);

	$wp_customize->add_setting(
		'gambit_theme_options[meta_author]',
		array(
			'default'           => true,
			'type'              => 'option',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'gambit_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'gambit_theme_options[meta_author]',
		array(
			'label'    => esc_html__( 'Display author', 'gambit' ),
			'section'  => 'gambit_section_post',
			'settings' => 'gambit_theme_options[meta_author]',
			'type'     => 'checkbox',
			'priority' => 60,
		)
	);

	$wp_customize->add_setting(
		'gambit_theme_options[meta_category]',
		array(
			'default'           => true,
			'type'              => 'option',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'gambit_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'gambit_theme_options[meta_category]',
		array(
			'label'    => esc_html__( 'Display categories', 'gambit' ),
			'section'  => 'gambit_section_post',
			'settings' => 'gambit_theme_options[meta_category]',
			'type'     => 'checkbox',
			'priority' => 70,
		)
	);

	// Add Post Footer Settings.
	$wp_customize->add_setting(
		'gambit_theme_options[single_posts_headline]',
		array(
			'default'           => '',
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_attr',
		)
	);
	$wp_customize->add_control(
		new Gambit_Customize_Header_Control(
			$wp_customize,
			'gambit_theme_options[single_posts_headline]',
			array(
				'label'    => esc_html__( 'Single Posts', 'gambit' ),
				'section'  => 'gambit_section_post',
				'settings' => 'gambit_theme_options[single_posts_headline]',
				'priority' => 80,
			)
		)
	);

	$wp_customize->add_setting(
		'gambit_theme_options[post_image]',
		array(
			'default'           => true,
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'gambit_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'gambit_theme_options[post_image]',
		array(
			'label'    => esc_html__( 'Display featured image', 'gambit' ),
			'section'  => 'gambit_section_post',
			'settings' => 'gambit_theme_options[post_image]',
			'type'     => 'checkbox',
			'priority' => 90,
		)
	);

	$wp_customize->add_setting(
		'gambit_theme_options[meta_tags]',
		array(
			'default'           => true,
			'type'              => 'option',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'gambit_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'gambit_theme_options[meta_tags]',
		array(
			'label'    => esc_html__( 'Display tags', 'gambit' ),
			'section'  => 'gambit_section_post',
			'settings' => 'gambit_theme_options[meta_tags]',
			'type'     => 'checkbox',
			'priority' => 100,
		)
	);
	$wp_customize->add_setting(
		'gambit_theme_options[post_navigation]',
		array(
			'default'           => true,
			'type'              => 'option',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'gambit_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'gambit_theme_options[post_navigation]',
		array(
			'label'    => esc_html__( 'Display previous/next post navigation', 'gambit' ),
			'section'  => 'gambit_section_post',
			'settings' => 'gambit_theme_options[post_navigation]',
			'type'     => 'checkbox',
			'priority' => 110,
		)
	);
}
add_action( 'customize_register', 'gambit_customize_register_post_settings' );
