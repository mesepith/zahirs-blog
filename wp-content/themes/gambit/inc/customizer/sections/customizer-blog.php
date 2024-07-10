<?php
/**
 * Blog Settings
 *
 * Register Blog Settings section, settings and controls for Theme Customizer
 *
 * @package Gambit
 */

/**
 * Adds blog settings in the Customizer
 *
 * @param object $wp_customize / Customizer Object.
 */
function gambit_customize_register_blog_settings( $wp_customize ) {

	// Add Sections for Post Settings.
	$wp_customize->add_section(
		'gambit_section_blog',
		array(
			'title'    => esc_html__( 'Blog Settings', 'gambit' ),
			'priority' => 25,
			'panel'    => 'gambit_options_panel',
		)
	);

	// Add Blog Title setting and control.
	$wp_customize->add_setting(
		'gambit_theme_options[blog_title]',
		array(
			'default'           => '',
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_kses_post',
		)
	);

	$wp_customize->add_control(
		'gambit_theme_options[blog_title]',
		array(
			'label'    => esc_html__( 'Blog Title', 'gambit' ),
			'section'  => 'gambit_section_blog',
			'settings' => 'gambit_theme_options[blog_title]',
			'type'     => 'text',
			'priority' => 10,
		)
	);

	$wp_customize->selective_refresh->add_partial(
		'gambit_theme_options[blog_title]',
		array(
			'selector'        => '.blog-header .blog-title',
			'render_callback' => 'gambit_customize_partial_blog_title',
		)
	);

	// Add Settings and Controls for blog layout.
	$wp_customize->add_setting(
		'gambit_theme_options[post_layout]',
		array(
			'default'           => 'small-image',
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'gambit_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'gambit_theme_options[post_layout]',
		array(
			'label'    => esc_html__( 'Blog Layout', 'gambit' ),
			'section'  => 'gambit_section_blog',
			'settings' => 'gambit_theme_options[post_layout]',
			'type'     => 'select',
			'priority' => 30,
			'choices'  => array(
				'small-image' => esc_html__( 'Show featured image beside content', 'gambit' ),
				'index'       => esc_html__( 'Show featured image below title', 'gambit' ),
			),
		)
	);

	// Add Settings and Controls for blog content.
	$wp_customize->add_setting(
		'gambit_theme_options[post_content]',
		array(
			'default'           => 'excerpt',
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'gambit_sanitize_select',
		)
	);

	$wp_customize->add_control(
		'gambit_theme_options[post_content]',
		array(
			'label'    => esc_html__( 'Blog Display', 'gambit' ),
			'section'  => 'gambit_section_blog',
			'settings' => 'gambit_theme_options[post_content]',
			'type'     => 'radio',
			'priority' => 40,
			'choices'  => array(
				'full'    => esc_html__( 'Full post', 'gambit' ),
				'excerpt' => esc_html__( 'Post excerpt', 'gambit' ),
			),
		)
	);

	// Add Setting and Control for Excerpt Length.
	$wp_customize->add_setting(
		'gambit_theme_options[excerpt_length]',
		array(
			'default'           => 35,
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'gambit_theme_options[excerpt_length]',
		array(
			'label'           => esc_html__( 'Number of words in excerpt', 'gambit' ),
			'section'         => 'gambit_section_blog',
			'settings'        => 'gambit_theme_options[excerpt_length]',
			'type'            => 'text',
			'active_callback' => 'gambit_control_post_content_callback',
			'priority'        => 50,
		)
	);

	// Add Magazine Widgets Headline.
	$wp_customize->add_control(
		new Gambit_Customize_Header_Control(
			$wp_customize,
			'gambit_theme_options[blog_magazine_widgets_title]',
			array(
				'label'    => esc_html__( 'Magazine Widgets', 'gambit' ),
				'section'  => 'gambit_section_blog',
				'settings' => array(),
				'priority' => 60,
			)
		)
	);

	// Add Setting and Control for showing post date.
	$wp_customize->add_setting(
		'gambit_theme_options[blog_magazine_widgets]',
		array(
			'default'           => true,
			'type'              => 'option',
			'transport'         => 'refresh',
			'sanitize_callback' => 'gambit_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'gambit_theme_options[blog_magazine_widgets]',
		array(
			'label'    => esc_html__( 'Display Magazine widgets on blog index', 'gambit' ),
			'section'  => 'gambit_section_blog',
			'settings' => 'gambit_theme_options[blog_magazine_widgets]',
			'type'     => 'checkbox',
			'priority' => 70,
		)
	);
}
add_action( 'customize_register', 'gambit_customize_register_blog_settings' );
