<?php
/**
 * The template for displaying large posts in the Magazine List widget.
 *
 * @package Gambit
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>

	<?php gambit_post_image( 'gambit-thumbnail-archive' ); ?>

	<header class="entry-header">

		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php gambit_magazine_entry_meta(); ?>

	</header><!-- .entry-header -->

	<div class="entry-content">

		<?php the_excerpt(); ?>
		<?php gambit_more_link(); ?>

	</div><!-- .entry-content -->

</article>
