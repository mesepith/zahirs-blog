<?php
/**
 * Main Navigation
 *
 * @version 1.0
 * @package Gambit
 */
?>

<?php if ( has_nav_menu( 'primary' ) ) : ?>

	<div id="main-navigation-wrap" class="primary-navigation-wrap">

		<button class="primary-menu-toggle menu-toggle" aria-controls="primary-menu" aria-expanded="false" <?php gambit_amp_menu_toggle(); ?>>
			<?php
			echo gambit_get_svg( 'menu' );
			echo gambit_get_svg( 'close' );
			?>
			<span class="menu-toggle-text"><?php esc_html_e( 'Navigation', 'gambit' ); ?></span>
		</button>

		<div class="primary-navigation">

			<nav id="site-navigation" class="main-navigation" role="navigation" <?php gambit_amp_menu_is_toggled(); ?> aria-label="<?php esc_attr_e( 'Primary Menu', 'gambit' ); ?>">

				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'container'      => false,
					)
				);
				?>
			</nav><!-- #site-navigation -->

		</div><!-- .primary-navigation -->

	</div>

<?php endif; ?>

<?php do_action( 'gambit_after_navigation' ); ?>
