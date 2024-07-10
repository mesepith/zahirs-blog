<?php
/**
 * The template for displaying search results pages.
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
		if ( have_posts() ) : ?>

			<header class="page-header">

				<h1 class="archive-title"><?php printf( esc_html__( 'Search Results for: %s', 'gambit' ), '<span>' . esc_html( get_search_query() ) . '</span>' ); ?></h1>

			</header><!-- .page-header -->

			<?php
			while ( have_posts() ) : the_post();

				if ( 'post' === get_post_type() ) :

					get_template_part( 'template-parts/content', $theme_options['post_layout'] );

				else :

					get_template_part( 'template-parts/content', 'search' );

				endif;

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
