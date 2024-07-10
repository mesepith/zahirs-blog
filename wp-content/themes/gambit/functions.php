<?php
/**
 * Gambit functions and definitions
 *
 * @package Gambit
 */

/**
 * Gambit only works in WordPress 4.7 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}


if ( ! function_exists( 'gambit_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function gambit_setup() {

		// Make theme available for translation. Translations can be filed in the /languages/ directory.
		load_theme_textdomain( 'gambit', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		// Set detfault Post Thumbnail size.
		set_post_thumbnail_size( 750, 450, true );

		// Register Navigation Menu.
		register_nav_menu( 'primary', esc_html__( 'Main Navigation', 'gambit' ) );

		// Switch default core markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'gambit_custom_background_args', array( 'default-color' => 'e5e5e5' ) ) );

		// Set up the WordPress core custom logo feature.
		add_theme_support(
			'custom-logo',
			apply_filters(
				'gambit_custom_logo_args',
				array(
					'height'      => 40,
					'width'       => 250,
					'flex-height' => true,
					'flex-width'  => true,
				)
			)
		);

		// Set up the WordPress core custom header feature.
		add_theme_support(
			'custom-header',
			apply_filters(
				'gambit_custom_header_args',
				array(
					'header-text' => false,
					'width'       => 1340,
					'height'      => 420,
					'flex-height' => true,
				)
			)
		);

		// Add Theme Support for wooCommerce.
		add_theme_support( 'woocommerce' );

		// Add extra theme styling to the visual editor.
		add_editor_style( array( 'assets/css/editor-style.css' ) );

		// Add Theme Support for Selective Refresh in Customizer.
		if ( ! get_option( 'link_manager_enabled' ) ) {
			add_theme_support( 'customize-selective-refresh-widgets' );
		}

		// Add support for responsive embed blocks.
		add_theme_support( 'responsive-embeds' );
	}
endif;
add_action( 'after_setup_theme', 'gambit_setup' );


/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function gambit_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'gambit_content_width', 750 );
}
add_action( 'after_setup_theme', 'gambit_content_width', 0 );


/**
 * Register widget areas and custom widgets.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function gambit_widgets_init() {

	register_sidebar(
		array(
			'name'          => esc_html__( 'Main Sidebar', 'gambit' ),
			'id'            => 'sidebar',
			'description'   => esc_html__( 'Appears on posts and pages except the full width template.', 'gambit' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s clearfix">',
			'after_widget'  => '</aside>',
			'before_title'  => '<div class="widget-header"><h3 class="widget-title">',
			'after_title'   => '</h3></div>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Small Sidebar', 'gambit' ),
			'id'            => 'sidebar-small',
			'description'   => esc_html__( 'Appears on posts and pages except the full width template.', 'gambit' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s clearfix">',
			'after_widget'  => '</aside>',
			'before_title'  => '<div class="widget-header"><h3 class="widget-title">',
			'after_title'   => '</h3></div>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Header', 'gambit' ),
			'id'            => 'header',
			'description'   => esc_html__( 'Appears on header area. You can use a search or ad widget here.', 'gambit' ),
			'before_widget' => '<aside id="%1$s" class="header-widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h4 class="header-widget-title">',
			'after_title'   => '</h4>',
		)
	);

	register_sidebar(
		array(
			'name'          => esc_html__( 'Magazine Homepage', 'gambit' ),
			'id'            => 'magazine-homepage',
			'description'   => esc_html__( 'Appears on blog index and Magazine Homepage template. You can use the Magazine widgets here.', 'gambit' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<div class="widget-header"><h3 class="widget-title">',
			'after_title'   => '</h3></div>',
		)
	);
}
add_action( 'widgets_init', 'gambit_widgets_init' );


/**
 * Enqueue scripts and styles.
 */
function gambit_scripts() {

	// Get Theme Version.
	$theme_version = wp_get_theme()->get( 'Version' );

	// Register and Enqueue Stylesheet.
	wp_enqueue_style( 'gambit-stylesheet', get_stylesheet_uri(), array(), $theme_version );

	// Register and Enqueue Safari Flexbox CSS fixes.
	wp_enqueue_style( 'gambit-safari-flexbox-fixes', get_template_directory_uri() . '/assets/css/safari-flexbox-fixes.css', array(), '20210115' );

	// Register and Enqueue HTML5shiv to support HTML5 elements in older IE versions.
	wp_enqueue_script( 'html5shiv', get_template_directory_uri() . '/assets/js/html5shiv.min.js', array(), '3.7.3' );
	wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );

	// Register and enqueue navigation.min.js.
	if ( ( has_nav_menu( 'primary' ) || has_nav_menu( 'secondary' ) ) && ! gambit_is_amp() ) {
		wp_enqueue_script( 'gambit-navigation', get_theme_file_uri( '/assets/js/navigation.min.js' ), array(), '20220224', true );
		$gambit_l10n = array(
			'expand'   => esc_html__( 'Expand child menu', 'gambit' ),
			'collapse' => esc_html__( 'Collapse child menu', 'gambit' ),
			'icon'     => gambit_get_svg( 'expand' ),
		);
		wp_localize_script( 'gambit-navigation', 'gambitScreenReaderText', $gambit_l10n );
	}

	// Enqueue svgxuse to support external SVG Sprites in Internet Explorer.
	if ( ! gambit_is_amp() ) {
		wp_enqueue_script( 'svgxuse', get_theme_file_uri( '/assets/js/svgxuse.min.js' ), array(), '1.2.6' );
	}

	// Register Comment Reply Script for Threaded Comments.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'gambit_scripts' );


/**
 * Enqueue theme fonts.
 */
function gambit_theme_fonts() {
	$fonts_url = gambit_get_fonts_url();

	// Load Fonts if necessary.
	if ( $fonts_url ) {
		require_once get_theme_file_path( 'inc/wptt-webfont-loader.php' );
		wp_enqueue_style( 'gambit-theme-fonts', wptt_get_webfont_url( $fonts_url ), array(), '20201110' );
	}
}
add_action( 'wp_enqueue_scripts', 'gambit_theme_fonts', 1 );
add_action( 'enqueue_block_editor_assets', 'gambit_theme_fonts', 1 );


/**
 * Retrieve webfont URL to load fonts locally.
 */
function gambit_get_fonts_url() {
	$font_families = array(
		'Oxygen:400,400italic,700,700italic',
	);

	$query_args = array(
		'family'  => urlencode( implode( '|', $font_families ) ),
		'subset'  => urlencode( 'latin,latin-ext' ),
		'display' => urlencode( 'swap' ),
	);

	return apply_filters( 'gambit_get_fonts_url', add_query_arg( $query_args, 'https://fonts.googleapis.com/css' ) );
}


/**
 * Add custom sizes for featured images
 */
function gambit_add_image_sizes() {

	// Add Custom Header Image Size.
	add_image_size( 'gambit-header-image', 1340, 420, true );

	// Add Image Size for Archives.
	add_image_size( 'gambit-thumbnail-archive', 300, 240, true );

	// Add different thumbnail sizes for widgets and post layouts.
	add_image_size( 'gambit-thumbnail-small', 100, 75, true );
	add_image_size( 'gambit-thumbnail-medium', 300, 200, true );
	add_image_size( 'gambit-thumbnail-large', 420, 280, true );
}
add_action( 'after_setup_theme', 'gambit_add_image_sizes' );


/**
 * Make custom image sizes available in Gutenberg.
 */
function gambit_add_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'post-thumbnail'         => esc_html__( 'Gambit Single Post', 'gambit' ),
			'gambit-thumbnail-large' => esc_html__( 'Gambit Magazine Post', 'gambit' ),
			'gambit-thumbnail-small' => esc_html__( 'Gambit Thumbnail', 'gambit' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'gambit_add_image_size_names' );


/**
 * Include Files
 */

// Include Theme Info page.
require get_template_directory() . '/inc/theme-info.php';

// Include Theme Customizer Options.
require get_template_directory() . '/inc/customizer/customizer.php';
require get_template_directory() . '/inc/customizer/default-options.php';

// Include SVG Icon Functions.
require get_template_directory() . '/inc/icons.php';

// Include Extra Functions.
require get_template_directory() . '/inc/extras.php';

// Include Template Functions.
require get_template_directory() . '/inc/template-tags.php';

// Include Gutenberg Features.
require get_template_directory() . '/inc/gutenberg.php';

// Include support functions for Theme Addons.
require get_template_directory() . '/inc/addons.php';

// Include Post Slider Setup.
require get_template_directory() . '/inc/slider.php';

// Include Magazine Functions.
require get_template_directory() . '/inc/magazine.php';

// Include Widget Files.
require get_template_directory() . '/inc/widgets/widget-magazine-posts-columns.php';
require get_template_directory() . '/inc/widgets/widget-magazine-posts-grid.php';
require get_template_directory() . '/inc/widgets/widget-magazine-posts-sidebar.php';
