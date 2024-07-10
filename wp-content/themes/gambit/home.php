<?php
/**
 * The template for displaying the blog index (latest posts)
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Gambit
 */

get_header();

// Get Theme Options from Database.
$theme_options = gambit_theme_options();
?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		// Display Slider.
		gambit_slider();

		// Display Magazine Homepage Widgets.
		gambit_magazine_widgets();

		do_action( 'gambit_before_blog' );

		if ( have_posts() ) :

			// Display Latest Posts Title.
			if ( '' !== $theme_options['blog_title'] ) : ?>

				<header class="page-header">

					<h1 class="archive-title"><?php echo wp_kses_post( $theme_options['blog_title'] ); ?></h1>

				</header><!-- .page-header -->

			<?php endif;

			while ( have_posts() ) : the_post();

				get_template_part( 'template-parts/content', $theme_options['post_layout'] );

			endwhile;

			// Display Pagination.
			gambit_pagination();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
