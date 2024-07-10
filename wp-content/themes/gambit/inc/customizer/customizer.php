<?php
/**
 * Implement theme options in the Customizer
 *
 * @package Gambit
 */

// Load Customizer Helper Functions.
require get_template_directory() . '/inc/customizer/functions/sanitize-functions.php';
require get_template_directory() . '/inc/customizer/functions/callback-functions.php';

// Load Custom Controls.
require get_template_directory() . '/inc/customizer/controls/category-dropdown-control.php';
require get_template_directory() . '/inc/customizer/controls/header-control.php';
require get_template_directory() . '/inc/customizer/controls/links-control.php';
require get_template_directory() . '/inc/customizer/controls/plugin-control.php';
require get_template_directory() . '/inc/customizer/controls/upgrade-control.php';

// Load Customizer Section Files.
require get_template_directory() . '/inc/customizer/sections/customizer-general.php';
require get_template_directory() . '/inc/customizer/sections/customizer-blog.php';
require get_template_directory() . '/inc/customizer/sections/customizer-post.php';
require get_template_directory() . '/inc/customizer/sections/customizer-slider.php';
require get_template_directory() . '/inc/customizer/sections/customizer-info.php';
require get_template_directory() . '/inc/customizer/sections/customizer-website.php';

/**
 * Registers Theme Options panel and sets up some WordPress core settings
 *
 * @param object $wp_customize / Customizer Object.
 */
function gambit_customize_register_options( $wp_customize ) {

	// Add Theme Options Panel.
	$wp_customize->add_panel(
		'gambit_options_panel',
		array(
			'priority'       => 180,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => esc_html__( 'Theme Options', 'gambit' ),
		)
	);

	// Change default background section.
	$wp_customize->get_control( 'background_color' )->section = 'background_image';
	$wp_customize->get_section( 'background_image' )->title   = esc_html__( 'Background', 'gambit' );
}
add_action( 'customize_register', 'gambit_customize_register_options' );


/**
 * Embed JS file to make Theme Customizer preview reload changes asynchronously.
 */
function gambit_customize_preview_js() {
	wp_enqueue_script( 'gambit-customize-preview', get_template_directory_uri() . '/assets/js/customize-preview.js', array( 'customize-preview' ), '20210115', true );
}
add_action( 'customize_preview_init', 'gambit_customize_preview_js' );


/**
 * Embed JS for Customizer Controls.
 */
function gambit_customizer_controls_js() {
	wp_enqueue_script( 'gambit-customizer-controls', get_template_directory_uri() . '/assets/js/customizer-controls.js', array(), '20210115', true );
}
add_action( 'customize_controls_enqueue_scripts', 'gambit_customizer_controls_js' );


/**
 * Embed CSS styles for the theme options in the Customizer
 */
function gambit_customize_preview_css() {
	wp_enqueue_style( 'gambit-customizer-css', get_template_directory_uri() . '/assets/css/customizer.css', array(), '20210115' );
}
add_action( 'customize_controls_print_styles', 'gambit_customize_preview_css' );
