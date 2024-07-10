<?php
/**
 * Theme Links Control for the Customizer
 *
 * @package Gambit
 */

/**
 * Make sure that custom controls are only defined in the Customizer
 */
if ( class_exists( 'WP_Customize_Control' ) ) :

	/**
	 * Displays the theme links in the Customizer.
	 */
	class Gambit_Customize_Links_Control extends WP_Customize_Control {
		/**
		 * Render Control
		 */
		public function render_content() {
			?>

			<div class="theme-links">

				<span class="customize-control-title"><?php esc_html_e( 'Theme Links', 'gambit' ); ?></span>

				<p>
					<a href="<?php echo esc_url( __( 'https://themezee.com/themes/gambit/', 'gambit' ) ); ?>?utm_source=customizer&utm_medium=textlink&utm_campaign=gambit&utm_content=theme-page" target="_blank">
						<?php esc_html_e( 'Theme Page', 'gambit' ); ?>
					</a>
				</p>

				<p>
					<a href="http://preview.themezee.com/?demo=gambit&utm_source=customizer&utm_campaign=gambit" target="_blank">
						<?php esc_html_e( 'Theme Demo', 'gambit' ); ?>
					</a>
				</p>

				<p>
					<a href="<?php echo esc_url( __( 'https://themezee.com/docs/gambit-documentation/', 'gambit' ) ); ?>?utm_source=customizer&utm_medium=textlink&utm_campaign=gambit&utm_content=documentation" target="_blank">
						<?php esc_html_e( 'Theme Documentation', 'gambit' ); ?>
					</a>
				</p>

				<p>
					<a href="<?php echo esc_url( __( 'https://themezee.com/changelogs/?action=themezee-changelog&type=theme&slug=gambit/', 'gambit' ) ); ?>" target="_blank">
						<?php esc_html_e( 'Theme Changelog', 'gambit' ); ?>
					</a>
				</p>

				<p>
					<a href="<?php echo esc_url( __( 'https://wordpress.org/support/theme/gambit/reviews/', 'gambit' ) ); ?>" target="_blank">
						<?php esc_html_e( 'Rate this theme', 'gambit' ); ?>
					</a>
				</p>

			</div>

			<?php
		}
	}

endif;
