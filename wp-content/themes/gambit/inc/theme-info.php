<?php
/**
 * Theme Info
 *
 * Adds a simple Theme Info page to the Appearance section of the WordPress Dashboard.
 *
 * @package Gambit
 */

/**
 * Add Theme Info page to admin menu
 */
function gambit_theme_info_menu_link() {

	// Get theme details.
	$theme = wp_get_theme();

	add_theme_page(
		sprintf( esc_html__( 'Welcome to %1$s %2$s', 'gambit' ), $theme->display( 'Name' ), $theme->display( 'Version' ) ),
		esc_html__( 'Theme Info', 'gambit' ),
		'edit_theme_options',
		'gambit',
		'gambit_theme_info_page'
	);

}
add_action( 'admin_menu', 'gambit_theme_info_menu_link' );

/**
 * Display Theme Info page
 */
function gambit_theme_info_page() {

	// Get theme details.
	$theme = wp_get_theme();
	?>

	<div class="wrap theme-info-wrap">

		<h1><?php printf( esc_html__( 'Welcome to %1$s %2$s', 'gambit' ), $theme->display( 'Name' ), $theme->display( 'Version' ) ); ?></h1>

		<div class="theme-description"><?php echo $theme->display( 'Description' ); ?></div>

		<hr>
		<div class="important-links clearfix">
			<p><strong><?php esc_html_e( 'Theme Links', 'gambit' ); ?>:</strong>
				<a href="<?php echo esc_url( __( 'https://themezee.com/themes/gambit/', 'gambit' ) . '?utm_source=theme-info&utm_medium=textlink&utm_campaign=gambit&utm_content=theme-page' ); ?>" target="_blank"><?php esc_html_e( 'Theme Page', 'gambit' ); ?></a>
				<a href="http://preview.themezee.com/?demo=gambit&utm_source=theme-info&utm_campaign=gambit" target="_blank"><?php esc_html_e( 'Theme Demo', 'gambit' ); ?></a>
				<a href="<?php echo esc_url( __( 'https://themezee.com/docs/gambit-documentation/', 'gambit' ) . '?utm_source=theme-info&utm_medium=textlink&utm_campaign=gambit&utm_content=documentation' ); ?>" target="_blank"><?php esc_html_e( 'Theme Documentation', 'gambit' ); ?></a>
				<a href="<?php echo esc_url( __( 'https://themezee.com/changelogs/?action=themezee-changelog&type=theme&slug=gambit', 'gambit' ) ); ?>" target="_blank"><?php esc_html_e( 'Theme Changelog', 'gambit' ); ?></a>
				<a href="<?php echo esc_url( __( 'https://wordpress.org/support/theme/gambit/reviews/', 'gambit' ) ); ?>" target="_blank"><?php esc_html_e( 'Rate this theme', 'gambit' ); ?></a>
			</p>
		</div>
		<hr>

		<div id="getting-started">

			<h3><?php printf( esc_html__( 'Getting Started with %s', 'gambit' ), $theme->display( 'Name' ) ); ?></h3>

			<div class="columns-wrapper clearfix">

				<div class="column column-half clearfix">

					<div class="section">
						<h4><?php esc_html_e( 'Theme Documentation', 'gambit' ); ?></h4>

						<p class="about">
							<?php esc_html_e( 'You need help to setup and configure this theme? We got you covered with an extensive theme documentation on our website.', 'gambit' ); ?>
						</p>
						<p>
							<a href="<?php echo esc_url( __( 'https://themezee.com/docs/gambit-documentation/', 'gambit' ) . '?utm_source=theme-info&utm_medium=button&utm_campaign=gambit&utm_content=documentation' ); ?>" target="_blank" class="button button-secondary">
								<?php printf( esc_html__( 'View %s Documentation', 'gambit' ), 'Gambit' ); ?>
							</a>
						</p>
					</div>

					<div class="section">
						<h4><?php esc_html_e( 'Theme Options', 'gambit' ); ?></h4>

						<p class="about">
							<?php printf( esc_html__( '%s makes use of the Customizer for all theme settings. Click on "Customize Theme" to open the Customizer now.', 'gambit' ), $theme->display( 'Name' ) ); ?>
						</p>
						<p>
							<a href="<?php echo wp_customize_url(); ?>" class="button button-primary"><?php esc_html_e( 'Customize Theme', 'gambit' ); ?></a>
						</p>
					</div>

				</div>

				<div class="column column-half clearfix">

					<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/screenshot.jpg" />

				</div>

			</div>

		</div>

		<hr>

		<div id="more-features">

			<h3><?php esc_html_e( 'Get more features', 'gambit' ); ?></h3>

			<div class="columns-wrapper clearfix">

				<div class="column column-half clearfix">

					<div class="section">
						<h4><?php esc_html_e( 'Pro Version Add-on', 'gambit' ); ?></h4>

						<p class="about">
							<?php printf( esc_html__( 'Purchase the %s Pro Add-on and get additional features and advanced customization options.', 'gambit' ), 'Gambit' ); ?>
						</p>
						<p>
							<a href="<?php echo esc_url( __( 'https://themezee.com/addons/gambit-pro/', 'gambit' ) . '?utm_source=theme-info&utm_medium=button&utm_campaign=gambit&utm_content=pro-version' ); ?>" target="_blank" class="button button-secondary">
								<?php printf( esc_html__( 'Learn more about %s Pro', 'gambit' ), 'Gambit' ); ?>
							</a>
						</p>
					</div>

				</div>

				<div class="column column-half clearfix">

					<div class="section">
						<h4><?php esc_html_e( 'Recommended Plugins', 'gambit' ); ?></h4>

						<p class="about">
							<?php esc_html_e( 'Extend the functionality of your WordPress website with our free and easy to use plugins.', 'gambit' ); ?>
						</p>
						<p>
							<a href="<?php echo esc_url( admin_url( 'plugin-install.php?tab=search&type=tag&s=themezee' ) ); ?>" class="button button-secondary">
								<?php esc_html_e( 'Install Plugins', 'gambit' ); ?>
							</a>
						</p>
					</div>

				</div>

			</div>

		</div>

		<hr>

		<div id="theme-author">

			<p>
			<?php
			printf(
				esc_html__( '%1$s is proudly brought to you by %2$s. If you like this theme, %3$s :)', 'gambit' ),
				$theme->display( 'Name' ),
				'<a target="_blank" href="' . __( 'https://themezee.com/', 'gambit' ) . '?utm_source=theme-info&utm_medium=footer&utm_campaign=gambit" title="ThemeZee">ThemeZee</a>',
				'<a target="_blank" href="' . __( 'https://wordpress.org/support/theme/gambit/reviews/', 'gambit' ) . '" title="' . esc_attr__( 'Review Gambit', 'gambit' ) . '">' . esc_html__( 'rate it', 'gambit' ) . '</a>'
			);
			?>
			</p>

		</div>

	</div>

	<?php
}

/**
 * Enqueues CSS for Theme Info page
 *
 * @param int $hook Hook suffix for the current admin page.
 */
function gambit_theme_info_page_css( $hook ) {

	// Load styles and scripts only on theme info page.
	if ( 'appearance_page_gambit' != $hook ) {
		return;
	}

	// Embed theme info css style.
	wp_enqueue_style( 'gambit-theme-info-css', get_template_directory_uri() . '/assets/css/theme-info.css' );

}
add_action( 'admin_enqueue_scripts', 'gambit_theme_info_page_css' );
