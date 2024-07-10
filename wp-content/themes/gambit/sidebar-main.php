<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Gambit
 */

?>
	<section id="secondary" class="main-sidebar widget-area clearfix" role="complementary">

		<?php // Check if Sidebar has widgets.
		if ( is_active_sidebar( 'sidebar' ) ) :

			dynamic_sidebar( 'sidebar' );

		endif; ?>

	</section><!-- #secondary -->
