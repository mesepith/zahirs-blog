<?php
/**
 * The template for displaying full image posts in Magazine Post widgets
 *
 * @package Gambit
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php gambit_post_image(); ?>

	<header class="entry-header">

		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php gambit_entry_meta(); ?>

	</header><!-- .entry-header -->

	<div class="entry-content">

		<?php the_excerpt(); ?>
		<?php gambit_more_link(); ?>

	</div><!-- .entry-content -->

</article>
