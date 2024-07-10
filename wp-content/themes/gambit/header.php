<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Gambit
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php do_action( 'wp_body_open' ); ?>

	<?php do_action( 'gambit_before_site' ); ?>

	<div id="page" class="hfeed site">

		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'gambit' ); ?></a>

		<?php do_action( 'gambit_header_bar' ); ?>
		<?php do_action( 'gambit_before_header' ); ?>

		<header id="masthead" class="site-header clearfix" role="banner">

			<div class="header-main container clearfix">

				<div id="logo" class="site-branding clearfix">

					<?php gambit_site_logo(); ?>
					<?php gambit_site_title(); ?>
					<?php gambit_site_description(); ?>

				</div><!-- .site-branding -->

				<div class="header-widgets clearfix">

					<?php
					if ( is_active_sidebar( 'header' ) ) :

						dynamic_sidebar( 'header' );

					endif;
					?>

				</div><!-- .header-widgets -->

			</div><!-- .header-main -->

			<?php get_template_part( 'template-parts/header/site', 'navigation' ); ?>

		</header><!-- #masthead -->

		<?php do_action( 'gambit_after_header' ); ?>

		<div id="content-wrap" class="site-content-wrap clearfix">

			<?php gambit_header_image(); ?>

			<?php gambit_breadcrumbs(); ?>

			<div id="content" class="site-content container clearfix">
