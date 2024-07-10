<?php
/**
 * The template for displaying the footer.
 *
 * Contains all content after the main content area and sidebar
 *
 * @package Gambit
 */

?>

		</div><!-- #content -->

	</div><!-- #content-wrap -->

	<?php do_action( 'gambit_before_footer' ); ?>

	<div id="footer" class="site-footer-wrap">

		<footer id="colophon" class="site-footer container clearfix" role="contentinfo">

			<div id="footer-text" class="site-info">
				<?php do_action( 'gambit_footer_text' ); ?>
			</div><!-- .site-info -->

			<?php do_action( 'gambit_footer_menu' ); ?>

		</footer><!-- #colophon -->

	</div>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
