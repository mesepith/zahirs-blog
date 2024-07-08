<?php
/**
 * Icon Block
 *
 * @package Copy the Code
 * @since 3.7.0
 */

namespace CopyTheCode\Gutenberg\Blocks;

use CopyTheCode\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Icon class.
 */
class Icon {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'init' ] );
		add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_assets' ] );
	}

	/**
	 * Enqueue block assets.
	 */
	public function enqueue_block_assets() {
		if ( ! has_block( 'copy-the-code/icon' ) ) {
			return;
		}

		wp_enqueue_script(
			'ctc-gb-icon',
			COPY_THE_CODE_URI . 'classes/gutenberg/blocks/icon/js/icon.js',
			[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-server-side-render' ],
			COPY_THE_CODE_VER
		);

		wp_enqueue_style(
			'ctc-gb-icon',
			COPY_THE_CODE_URI . 'classes/gutenberg/blocks/icon/css/style.css',
			[],
			COPY_THE_CODE_VER,
			'all'
		);

		// Core.
		wp_enqueue_script( 'ctc-core', COPY_THE_CODE_URI . 'classes/blocks/assets/js/core.js', [ 'jquery' ], COPY_THE_CODE_VER, true );
		wp_enqueue_script( 'ctc-clipboard', COPY_THE_CODE_URI . 'assets/js/clipboard.js', [ 'jquery' ], COPY_THE_CODE_VER, true );
	}

	/**
	 * Initialize.
	 */
	public function init() {
		register_block_type(
			COPY_THE_CODE_GUTENBERG_BLOCKS . 'blocks/icon/block.json',
			[
				'render_callback' => [ $this, 'render' ],
			]
		);
	}

	/**
	 * Render.
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content Block content.
	 *
	 * @return string
	 */
	public function render( $attributes, $content ) {
		$alignment    = isset( $attributes['alignment'] ) ? $attributes['alignment'] : 'left';
		$copy_content = isset( $attributes['content'] ) ? $attributes['content'] : '';
		ob_start();
		?>
		<div class="ctc-block ctc-copy-icon" style="text-align: <?php echo esc_attr( $alignment ); ?>">
			<span copy-as-raw="yes" class="ctc-block-copy ctc-block-copy-icon" role="button" aria-label="Copied">
				<?php echo Helpers::get_svg_copy_icon(); ?>
				<?php echo Helpers::get_svg_checked_icon(); ?>
			</span>
			<textarea class="ctc-copy-content" style="display: none;"><?php echo wp_kses_post( apply_shortcodes( $copy_content ) ); ?></textarea>
		</div>
		<?php
		return ob_get_clean();
	}
}
