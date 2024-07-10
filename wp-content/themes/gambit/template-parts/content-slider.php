<?php
/**
 * The template for displaying articles in the slideshow loop
 *
 * @package Gambit
 */

?>

<li id="slide-<?php the_ID(); ?>" class="zeeslide clearfix">

	<?php gambit_slider_image( 'post-thumbnail', array( 'class' => 'slide-image', 'loading' => false ) ); ?>

	<div class="slide-post clearfix">

		<header class="entry-header">

			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		</header><!-- .entry-header -->

		<div class="entry-content clearfix">

			<a href="<?php the_permalink(); ?>" rel="bookmark">
				<?php the_excerpt(); ?>
			</a>

		</div><!-- .entry-content -->

	</div>

</li>
