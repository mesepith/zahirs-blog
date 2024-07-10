<?php
/**
 * The sidebar containing the small sidebar widget area.
 *
 * @package Gambit
 */

?>
	<section id="secondary" class="small-sidebar widget-area clearfix" role="complementary">

		<?php // Check if Sidebar has widgets.
		if ( is_active_sidebar( 'sidebar-small' ) ) :

			dynamic_sidebar( 'sidebar-small' );

		endif; ?>

	</section><!-- #secondary -->
