<?php
/**
 * The template for displaying posts in the Magazine Sidebar widget
 *
 * @package Gambit
 */

// Get widget settings.
$post_excerpt = get_query_var( 'gambit_post_excerpt', false );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'large-post clearfix' ); ?>>

	<?php gambit_post_image( 'gambit-thumbnail-large' ); ?>

	<header class="entry-header">

		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php gambit_magazine_entry_date(); ?>

	</header><!-- .entry-header -->

	<?php // Display post excerpt if enabled.
	if ( $post_excerpt ) : ?>

		<div class="entry-content">

			<?php the_excerpt(); ?>
			<?php gambit_more_link(); ?>

		</div><!-- .entry-content -->

	<?php endif; ?>

</article>
